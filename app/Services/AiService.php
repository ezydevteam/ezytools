<?php

namespace App\Services;

use App\Models\AiModel;
use App\Models\AiProvider;
use App\Models\AiSetting;
use App\Models\AiToolConfig;
use App\Models\AiUsage;
use App\Models\Tool;
use App\Models\User;
use App\Services\Ai\AiDriverInterface;
use App\Services\Ai\AiResponse;
use App\Services\Ai\GeminiDriver;
use App\Services\Ai\OpenAiDriver;
use Illuminate\Support\Facades\Log;

class AiService
{
    /**
     * Map provider names to their driver classes.
     */
    protected array $drivers = [
        'openai' => OpenAiDriver::class,
        'grok'   => OpenAiDriver::class, // Grok uses OpenAI-compatible API
        'gemini' => GeminiDriver::class,
    ];

    /**
     * Generate an AI response for a given tool.
     */
    public function generate(
        Tool $tool,
        string $userMessage,
        ?User $user,
        ?string $ip = null,
    ): AiResponse {
        $config = $tool->aiConfig;

        if (!$config) {
            return AiResponse::failed('AI not configured for this tool.');
        }

        $isPro = $user?->isPro() ?? false;

        // Check daily limit
        $limitCheck = $this->checkDailyLimit($user, $ip);
        if ($limitCheck !== true) {
            return AiResponse::failed($limitCheck);
        }

        // Check AI credits (only for authenticated users when system is enabled)
        $creditCheck = $this->checkCredits($user, $config);
        if ($creditCheck !== true) {
            return $creditCheck;
        }

        // Check budget limit
        $budgetCheck = $this->checkBudgetLimit();
        if ($budgetCheck !== true) {
            return AiResponse::failed('AI service temporarily unavailable. Please try again later.');
        }

        // Select provider & model for tier
        $provider = $config->getProviderForTier($isPro);
        $model = $config->getModelForTier($isPro);
        $maxTokens = $config->getMaxTokensForTier($isPro);

        if (!$provider || !$model) {
            return AiResponse::failed('No AI provider configured.');
        }

        // Try primary provider
        $response = $this->callProvider($provider, $model, $config, $userMessage, $maxTokens);

        // If primary fails, try fallback
        if (!$response->success && $config->fallbackProvider && $config->fallbackModel) {
            Log::warning("AI primary failed for tool [{$tool->slug}], trying fallback", [
                'primary_provider' => $provider->name,
                'error' => $response->error,
            ]);

            $response = $this->callProvider(
                $config->fallbackProvider,
                $config->fallbackModel,
                $config,
                $userMessage,
                $maxTokens,
            );
        }

        // Log usage
        $this->logUsage($tool, $user, $ip, $response);

        // Deduct credits on success
        if ($response->success) {
            $this->deductCredits($user, $config);
        }

        return $response;
    }

    /**
     * Call an AI provider using the appropriate driver.
     */
    protected function callProvider(
        AiProvider $provider,
        AiModel $model,
        AiToolConfig $config,
        string $userMessage,
        int $maxTokens,
    ): AiResponse {
        if (!$provider->is_active) {
            return AiResponse::failed("Provider '{$provider->label}' is disabled.", $provider->name, $model->name);
        }

        $driverClass = $this->drivers[$provider->name] ?? null;

        if (!$driverClass) {
            return AiResponse::failed("Unknown provider: {$provider->name}");
        }

        /** @var AiDriverInterface $driver */
        $driver = new $driverClass();

        return $driver->chat(
            provider: $provider,
            model: $model,
            systemPrompt: $config->system_prompt ?? 'You are a helpful assistant.',
            userMessage: $userMessage,
            maxTokens: $maxTokens,
            temperature: (float) ($config->temperature ?? 0.7),
        );
    }

    /**
     * Generate a raw AI response without tool context.
     * Used by detection/humanizer services for direct API calls.
     */
    public function generateRaw(
        string $systemPrompt,
        string $userMessage,
        int $maxTokens = 1000,
        float $temperature = 0.7,
        ?string $providerName = null,
        ?string $modelName = null,
    ): AiResponse {
        // Use specified or find default provider/model
        $provider = $providerName
            ? AiProvider::where('name', $providerName)->where('is_active', true)->first()
            : AiProvider::where('is_active', true)->first();

        if (!$provider) {
            return AiResponse::failed('No active AI provider available.');
        }

        $model = $modelName
            ? AiModel::where('name', $modelName)->where('provider_id', $provider->id)->first()
            : AiModel::where('provider_id', $provider->id)->first();

        if (!$model) {
            return AiResponse::failed('No AI model available.');
        }

        $driverClass = $this->drivers[$provider->name] ?? null;
        if (!$driverClass) {
            return AiResponse::failed("Unknown provider: {$provider->name}");
        }

        $driver = new $driverClass();

        return $driver->chat(
            provider: $provider,
            model: $model,
            systemPrompt: $systemPrompt,
            userMessage: $userMessage,
            maxTokens: $maxTokens,
            temperature: $temperature,
        );
    }
    /**
     * Check if the user has exceeded their daily AI usage limit.
     */
    protected function checkDailyLimit(?User $user, ?string $ip): true|string
    {
        $todayCount = AiUsage::todayCountFor($user?->id, $ip);

        if ($user?->isPro()) {
            $limit = (int) AiSetting::getValue('daily_limit_pro', -1);
            if ($limit === -1) {
                return true; // Unlimited
            }
            if ($todayCount >= $limit) {
                return 'Daily request limit reached. Please try again tomorrow.';
            }
            return true;
        }

        if ($user) {
            $limit = (int) AiSetting::getValue('daily_limit_registered', 10);
            if ($todayCount >= $limit) {
                return 'Daily limit reached. Upgrade to Pro for more AI requests.';
            }
        } else {
            $limit = (int) AiSetting::getValue('daily_limit_guest', 3);
            if ($todayCount >= $limit) {
                return 'Daily limit reached. Sign in or upgrade to Pro for more.';
            }
        }

        return true;
    }

    /**
     * Check if the daily budget has been exceeded.
     */
    protected function checkBudgetLimit(): true|string
    {
        $maxDailySpend = (float) AiSetting::getValue('max_daily_spend_usd', 5.00);
        $autoDisable = AiSetting::getValue('auto_disable_on_budget_exceed', 'true');

        if ($autoDisable === 'true' && AiUsage::todayTotalCost() >= $maxDailySpend) {
            Log::critical('AI daily budget exceeded, auto-disabling AI requests.', [
                'budget' => $maxDailySpend,
                'spent' => AiUsage::todayTotalCost(),
            ]);

            return 'Daily budget exceeded';
        }

        return true;
    }

    /**
     * Log an AI usage record.
     */
    protected function logUsage(Tool $tool, ?User $user, ?string $ip, AiResponse $response): void
    {
        try {
            $provider = AiProvider::where('name', $response->provider)->first();
            $model = AiModel::where('name', $response->model)->first();

            $cost = $model
                ? $model->calculateCost($response->inputTokens, $response->outputTokens)
                : 0;

            AiUsage::create([
                'tool_id' => $tool->id,
                'user_id' => $user?->id,
                'ip_address' => $ip,
                'provider_id' => $provider?->id,
                'model_id' => $model?->id,
                'input_tokens' => $response->inputTokens,
                'output_tokens' => $response->outputTokens,
                'cost_usd' => $cost,
                'status' => $response->success ? 'success' : 'failed',
                'error_message' => $response->error,
                'created_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to log AI usage', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Get remaining requests for a user/IP today.
     */
    public function remainingRequests(?User $user, ?string $ip): int|string
    {
        $todayCount = AiUsage::todayCountFor($user?->id, $ip);

        if ($user?->isPro()) {
            $limit = (int) AiSetting::getValue('daily_limit_pro', -1);
            if ($limit === -1) {
                return 'unlimited';
            }
            return max(0, $limit - $todayCount);
        }

        $limit = $user
            ? (int) AiSetting::getValue('daily_limit_registered', 10)
            : (int) AiSetting::getValue('daily_limit_guest', 3);

        return max(0, $limit - $todayCount);
    }

    /**
     * Check if the credit system is enabled.
     */
    public function isCreditSystemEnabled(): bool
    {
        return AiSetting::getValue('credit_system_enabled', 'true') === 'true';
    }

    /**
     * Check if the user has enough credits for this tool.
     */
    protected function checkCredits(?User $user, AiToolConfig $config): true|AiResponse
    {
        if (!$this->isCreditSystemEnabled()) {
            return true;
        }

        // Guests are not credit-gated (use daily IP limits instead)
        if (!$user) {
            return true;
        }

        $userCredits = (int) $user->ai_credit;

        // -1 means unlimited credits
        if ($userCredits === -1) {
            return true;
        }

        $cost = (int) ($config->credit_cost ?? AiSetting::getValue('credit_cost_default', 1));

        if ($userCredits < $cost) {
            return AiResponse::failed(
                "Insufficient AI credits. You need {$cost} credit(s) but have {$userCredits}. Please upgrade to Pro for more credits."
            );
        }

        return true;
    }

    /**
     * Deduct credits from the user after a successful generation.
     */
    protected function deductCredits(?User $user, AiToolConfig $config): void
    {
        if (!$this->isCreditSystemEnabled() || !$user) {
            return;
        }

        $userCredits = (int) $user->ai_credit;

        // -1 means unlimited — don't deduct
        if ($userCredits === -1) {
            return;
        }

        $cost = (int) ($config->credit_cost ?? AiSetting::getValue('credit_cost_default', 1));
        $newBalance = max(0, $userCredits - $cost);

        $user->update(['ai_credit' => $newBalance]);
    }

    /**
     * Get remaining credits for a user.
     * Returns: int (credits), -1 (unlimited), or null (system disabled / guest).
     */
    public function getRemainingCredits(?User $user): ?int
    {
        if (!$this->isCreditSystemEnabled() || !$user) {
            return null;
        }

        return (int) $user->ai_credit;
    }
}
