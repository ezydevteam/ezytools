<?php

namespace App\Services\Ai;

use App\Models\AiModel;
use App\Models\AiProvider;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Google Gemini API driver using the generateContent endpoint.
 */
class GeminiDriver implements AiDriverInterface
{
    public function chat(
        AiProvider $provider,
        AiModel $model,
        string $systemPrompt,
        string $userMessage,
        int $maxTokens,
        float $temperature,
    ): AiResponse {
        $apiKey = $provider->api_key;
        $baseUrl = $provider->base_url ?: 'https://generativelanguage.googleapis.com';

        try {
            $response = Http::timeout(60)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post("{$baseUrl}/v1beta/models/{$model->name}:generateContent?key={$apiKey}", [
                    'system_instruction' => [
                        'parts' => [['text' => $systemPrompt]],
                    ],
                    'contents' => [
                        [
                            'role' => 'user',
                            'parts' => [['text' => $userMessage]],
                        ],
                    ],
                    'generationConfig' => [
                        'maxOutputTokens' => $maxTokens,
                        'temperature' => $temperature,
                    ],
                ]);

            if ($response->failed()) {
                $errorBody = $response->json('error.message', 'Unknown Gemini error');
                Log::error('Gemini driver error', [
                    'status' => $response->status(),
                    'error' => $errorBody,
                ]);

                return AiResponse::failed($errorBody, $provider->name, $model->name);
            }

            $data = $response->json();
            $content = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';
            $usageMetadata = $data['usageMetadata'] ?? [];

            return new AiResponse(
                content: trim($content),
                inputTokens: $usageMetadata['promptTokenCount'] ?? 0,
                outputTokens: $usageMetadata['candidatesTokenCount'] ?? 0,
                model: $model->name,
                provider: $provider->name,
            );
        } catch (\Exception $e) {
            Log::error('Gemini driver exception', [
                'error' => $e->getMessage(),
            ]);

            return AiResponse::failed($e->getMessage(), $provider->name, $model->name);
        }
    }
}
