<?php

namespace App\Services\Ai;

use App\Models\AiModel;
use App\Models\AiProvider;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * OpenAI-compatible driver. Works with OpenAI, Grok (xAI), and any
 * provider that implements the OpenAI chat completions API.
 */
class OpenAiDriver implements AiDriverInterface
{
    public function chat(
        AiProvider $provider,
        AiModel $model,
        string $systemPrompt,
        string $userMessage,
        int $maxTokens,
        float $temperature,
    ): AiResponse {
        $baseUrl = $provider->base_url ?: 'https://api.openai.com/v1';

        try {
            $response = Http::timeout(60)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $provider->api_key,
                    'Content-Type' => 'application/json',
                ])
                ->post("{$baseUrl}/chat/completions", [
                    'model' => $model->name,
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => $userMessage],
                    ],
                    'max_tokens' => $maxTokens,
                    'temperature' => $temperature,
                ]);

            if ($response->failed()) {
                $errorBody = $response->json('error.message', 'Unknown API error');
                Log::error("OpenAI driver error [{$provider->name}]", [
                    'status' => $response->status(),
                    'error' => $errorBody,
                ]);

                return AiResponse::failed($errorBody, $provider->name, $model->name);
            }

            $data = $response->json();
            $content = $data['choices'][0]['message']['content'] ?? '';
            $usage = $data['usage'] ?? [];

            return new AiResponse(
                content: trim($content),
                inputTokens: $usage['prompt_tokens'] ?? 0,
                outputTokens: $usage['completion_tokens'] ?? 0,
                model: $model->name,
                provider: $provider->name,
            );
        } catch (\Exception $e) {
            Log::error("OpenAI driver exception [{$provider->name}]", [
                'error' => $e->getMessage(),
            ]);

            return AiResponse::failed($e->getMessage(), $provider->name, $model->name);
        }
    }
}
