<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tool;
use App\Services\AiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AiToolController extends Controller
{
    public function __construct(
        protected AiService $aiService,
    ) {}

    /**
     * Handle an AI tool request.
     * POST /api/ai/{toolSlug}
     */
    public function handle(Request $request, string $toolSlug): JsonResponse
    {
        $tool = Tool::where('slug', $toolSlug)
            ->where('is_active', true)
            ->with('aiConfig')
            ->firstOrFail();

        if (!$tool->aiConfig) {
            return response()->json([
                'error' => 'ai_not_configured',
                'message' => 'This tool does not have AI configured.',
            ], 422);
        }

        // Block premium tools for non-pro users
        if ($tool->is_premium && !($request->user()?->isPro())) {
            return response()->json([
                'error' => 'pro_required',
                'message' => 'This is a Pro tool. Please upgrade to use it.',
            ], 403);
        }

        $isPro = $request->user()?->isPro() ?? false;
        $maxInputLength = $isPro 
            ? ($tool->aiConfig->max_input_length_pro ?? 10000)
            : ($tool->aiConfig->max_input_length_free ?? 1000);

        $request->validate([
            'message' => "required|string|max:{$maxInputLength}",
            'options' => 'nullable|array',
        ]);

        // Build the user message with options context
        $userMessage = $this->buildUserMessage(
            $request->input('message'),
            $request->input('options', []),
            $tool->slug,
        );

        $response = $this->aiService->generate(
            tool: $tool,
            userMessage: $userMessage,
            user: $request->user(),
            ip: $request->ip(),
        );

        if (!$response->success) {
            $isLimitError = str_contains($response->error ?? '', 'সীমা') || str_contains($response->error ?? '', 'limit');
            $isCreditError = str_contains($response->error ?? '', 'credit') || str_contains($response->error ?? '', 'Credit');
            $statusCode = $isCreditError ? 402 : ($isLimitError ? 429 : 500);

            return response()->json([
                'error' => $isCreditError ? 'insufficient_credits' : 'ai_error',
                'message' => $response->error,
                'remaining' => $this->aiService->remainingRequests(
                    $request->user(),
                    $request->ip(),
                ),
                'credits' => $this->aiService->getRemainingCredits($request->user()),
            ], $statusCode);
        }

        return response()->json([
            'content' => $response->content,
            'tokens' => [
                'input' => $response->inputTokens,
                'output' => $response->outputTokens,
            ],
            'remaining' => $this->aiService->remainingRequests(
                $request->user(),
                $request->ip(),
            ),
            'credits' => $this->aiService->getRemainingCredits($request->user()),
        ]);
    }

    /**
     * Build the final user message, injecting options as context.
     */
    protected function buildUserMessage(string $message, array $options, string $toolSlug): string
    {
        if (empty($options)) {
            return $message;
        }

        $optionLines = [];
        foreach ($options as $key => $value) {
            if ($value !== null && $value !== '') {
                $label = str_replace('_', ' ', ucfirst($key));
                $optionLines[] = "{$label}: {$value}";
            }
        }

        if (empty($optionLines)) {
            return $message;
        }

        $optionsStr = implode("\n", $optionLines);

        return "Settings:\n{$optionsStr}\n\n---\n\n{$message}";
    }

    /**
     * AI Content Detector
     * POST /api/ai/content-detector
     */
    public function contentDetector(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'text' => 'required|string|min:50|max:50000',
        ]);

        $isPro = $request->user()?->isPro() ?? false;

        // Get limits from AI Config
        $tool = \App\Models\Tool::where('slug', 'ai-content-detector')->first();
        $config = $tool?->aiConfig;
        $maxChars = $isPro
            ? ($config->max_input_length_pro ?? 5000)
            : ($config->max_input_length_free ?? 1000);

        if (mb_strlen($validated['text']) > $maxChars) {
            $proLimit = $config->max_input_length_pro ?? 5000;
            return response()->json([
                'error' => 'too_long',
                'message' => "Character limit: " . number_format($maxChars) . ". Your text: " . number_format(mb_strlen($validated['text'])) . " characters." . (!$isPro ? " Upgrade to Pro for up to " . number_format($proLimit) . "." : ''),
                'upgrade' => !$isPro,
            ], 422);
        }

        // Rate limit
        $limitCheck = $this->aiService->remainingRequests($request->user(), $request->ip());
        if ($limitCheck !== null && $limitCheck <= 0) {
            return response()->json([
                'error' => 'rate_limited',
                'message' => 'You have reached your daily usage limit. Please try again tomorrow or upgrade to Pro.',
            ], 429);
        }

        $result = app(\App\Services\AiDetectionService::class)->detect($validated['text']);

        return response()->json([
            'overall_score' => $result->overallScore,
            'verdict' => $result->verdict,
            'burstiness' => $result->burstinessScore,
            'perplexity' => $result->perplexityScore,
            'sentences' => $result->sentences,
            'language' => $result->language,
            'char_count' => mb_strlen($validated['text']),
            'word_count' => count(preg_split('/\s+/u', trim($validated['text']))),
        ]);
    }

    /**
     * AI Detector & Humanizer (Combined tool)
     * POST /api/ai/detector-humanizer
     */
    public function detectorHumanizer(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'action' => 'required|in:detect,humanize',
            'text' => 'required|string|min:20|max:10000',
            'level' => 'sometimes|in:light,medium,heavy',
            'style' => 'sometimes|in:conversational,academic,professional',
            'language' => 'sometimes|in:bangla,english,auto',
        ]);

        $isPro = $request->user()?->isPro() ?? false;

        if ($validated['action'] === 'detect') {
            $result = app(\App\Services\AiDetectionService::class)->detect($validated['text']);

            return response()->json([
                'overall_score' => $result->overallScore,
                'verdict' => $result->verdict,
                'sentences' => $result->sentences,
                'language' => $result->language,
                'burstiness' => $result->burstinessScore,
            ]);
        }

        // Humanize action — get limits from AI Config
        $tool = \App\Models\Tool::where('slug', 'ai-detector-humanizer')->first();
        $config = $tool?->aiConfig;
        $maxChars = $isPro
            ? ($config->max_input_length_pro ?? 5000)
            : ($config->max_input_length_free ?? 1000);

        if (mb_strlen($validated['text']) > $maxChars) {
            $proLimit = $config->max_input_length_pro ?? 5000;
            return response()->json([
                'error' => 'too_long',
                'message' => "Character limit: " . number_format($maxChars) . ". Your text: " . number_format(mb_strlen($validated['text'])) . " characters." . (!$isPro ? " Upgrade to Pro for " . number_format($proLimit) . "." : ''),
                'upgrade' => !$isPro,
            ], 422);
        }

        // Heavy mode = Pro only
        $level = $validated['level'] ?? 'medium';
        if ($level === 'heavy' && !$isPro) {
            return response()->json([
                'error' => 'pro_required',
                'message' => 'Heavy humanization is a Pro feature. Upgrade to access it.',
                'upgrade' => true,
            ], 403);
        }

        $result = app(\App\Services\AiHumanizerService::class)->humanize(
            text: $validated['text'],
            level: $level,
            language: $validated['language'] ?? 'auto',
            style: $validated['style'] ?? 'conversational',
            isPro: $isPro,
        );

        return response()->json([
            'humanized_text' => $result->humanizedText,
            'original_score' => $result->originalScore,
            'humanized_score' => $result->humanizedScore,
            'improvement' => $result->improvement,
            'passes' => $result->passesCompleted,
        ]);
    }

    /**
     * AI Voice Generator
     * POST /api/ai/voice-generator
     */
    public function voiceGenerator(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'text' => 'required|string|min:5|max:5000',
            'voice_id' => 'required|exists:ai_voices,id',
            'speed' => 'sometimes|numeric|min:0.5|max:2.0',
            'pitch' => 'sometimes|numeric|min:0.5|max:2.0',
        ]);

        $isPro = $request->user()?->isPro() ?? false;

        // Get limits from AI Config
        $tool = \App\Models\Tool::where('slug', 'ai-voice-generator')->first();
        $config = $tool?->aiConfig;
        $maxLen = $isPro
            ? ($config->max_input_length_pro ?? 2000)
            : ($config->max_input_length_free ?? 200);

        if (mb_strlen($validated['text']) > $maxLen) {
            $proLimit = $config->max_input_length_pro ?? 2000;
            return response()->json([
                'error' => 'too_long',
                'message' => "Character limit: " . number_format($maxLen) . "." . (!$isPro ? " Upgrade to Pro for " . number_format($proLimit) . " characters." : ''),
                'upgrade' => !$isPro,
            ], 422);
        }

        $voice = \App\Models\AiVoice::findOrFail($validated['voice_id']);

        // Pro-only voice check
        if ($voice->is_pro_only && !$isPro) {
            return response()->json([
                'error' => 'pro_required',
                'message' => 'This voice is available for Pro users only.',
                'upgrade' => true,
            ], 403);
        }

        try {
            $result = app(\App\Services\AiVoiceService::class)->generate(
                text: $validated['text'],
                voiceId: $validated['voice_id'],
                language: $voice->language,
                speed: $validated['speed'] ?? 1.0,
                pitch: $validated['pitch'] ?? 1.0,
            );

            return response()->json([
                'download_url' => $result->downloadUrl,
                'duration' => $result->duration,
                'expires_at' => $result->expiresAt->toIso8601String(),
                'uuid' => $result->uuid,
            ]);
        } catch (\Exception $e) {
            \Log::error('Voice generation failed', ['error' => $e->getMessage()]);
            return response()->json([
                'error' => 'generation_failed',
                'message' => 'Voice generation failed. Please try again.',
            ], 500);
        }
    }

    /**
     * Download a generated voice file.
     * GET /api/voice/download/{uuid}
     */
    public function voiceDownload(string $uuid): \Symfony\Component\HttpFoundation\Response
    {
        $job = \App\Models\AiVoiceJob::where('output_path', 'LIKE', "voice-jobs/{$uuid}/%")
            ->where('status', 'done')
            ->firstOrFail();

        if ($job->expires_at && $job->expires_at->isPast()) {
            abort(410, 'This audio file has expired.');
        }

        $path = \Illuminate\Support\Facades\Storage::disk('local')->path($job->output_path);

        if (!file_exists($path)) {
            abort(404, 'Audio file not found.');
        }

        return response()->download($path, 'ezytools-voice.mp3', [
            'Content-Type' => 'audio/mpeg',
        ]);
    }

    /**
     * Run the full SEO audit for a URL.
     * POST /api/ai/seo-auditor
     */
    public function seoAuditor(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'url' => 'required|url|max:2048',
            'target_keyword' => 'nullable|string|max:255',
        ]);

        $user = $request->user();
        $ip = $request->ip();

        // Guest / Free limit check
        if (!$user || !$user->isPro()) {
            $todayCount = \App\Models\SeoAuditReport::where(function ($query) use ($user, $ip) {
                if ($user) {
                    $query->where('user_id', $user->id);
                } else {
                    $query->where('ip_address', $ip);
                }
            })
            ->whereDate('created_at', now()->toDateString())
            ->count();

            $limit = $user ? 5 : 2;
            if ($todayCount >= $limit) {
                return response()->json([
                    'error' => 'daily_limit_exceeded',
                    'message' => 'Daily limit reached. Please upgrade to Pro for unlimited audits.'
                ], 429);
            }
        }

        try {
            /** @var \App\Services\SeoAuditService $auditService */
            $auditService = app(\App\Services\SeoAuditService::class);
            $result = $auditService->audit(
                url: $validated['url'],
                targetKeyword: $validated['target_keyword'] ?? null,
                userId: $user?->id,
                ipAddress: $ip
            );

            // Save report
            $report = \App\Models\SeoAuditReport::create([
                'uuid' => (string) \Illuminate\Support\Str::uuid(),
                'user_id' => $user?->id,
                'ip_address' => $ip,
                'url' => $validated['url'],
                'domain' => parse_url($validated['url'], PHP_URL_HOST),
                'target_keyword' => $validated['target_keyword'] ?? null,
                'overall_score' => $result->overallScore,
                'technical_score' => $result->technicalScore,
                'onpage_score' => $result->onpageScore,
                'performance_score' => $result->performanceScore,
                'ai_readiness_score' => $result->aiReadinessScore,
                'issues_critical' => $result->issuesCritical,
                'issues_warning' => $result->issuesWarning,
                'issues_passed' => $result->issuesPassed,
                'meta_title' => $result->metaTitle,
                'meta_description' => $result->metaDescription,
                'canonical_url' => $result->canonicalUrl,
                'h1' => $result->h1,
                'word_count' => $result->wordCount,
                'load_time' => $result->loadTime,
                'audit_data' => $result->auditData,
            ]);

            return response()->json([
                'report' => $report,
                'remaining' => $user?->isPro() ? 'unlimited' : (isset($limit) ? max(0, $limit - ($todayCount + 1)) : 1),
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'error' => 'invalid_url',
                'message' => $e->getMessage()
            ], 422);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('SEO Audit failed', [
                'url' => $validated['url'],
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'error' => 'audit_failed',
                'message' => 'The SEO Audit failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a previously generated SEO Report by UUID.
     * GET /api/ai/seo-auditor/report/{uuid}
     */
    public function getSeoReport(Request $request, string $uuid): JsonResponse
    {
        $report = \App\Models\SeoAuditReport::where('uuid', $uuid)->firstOrFail();

        if ($report->created_at->addDays(7)->isPast()) {
            return response()->json([
                'error' => 'expired',
                'message' => 'This report has expired.'
            ], 410);
        }

        return response()->json([
            'report' => $report
        ]);
    }
}

