<?php

namespace App\Services\Ai;

/**
 * Standardized response from any AI provider.
 */
class AiResponse
{
    public function __construct(
        public readonly string $content,
        public readonly int $inputTokens,
        public readonly int $outputTokens,
        public readonly string $model,
        public readonly string $provider,
        public readonly bool $success = true,
        public readonly ?string $error = null,
    ) {}

    /**
     * Create a failed response.
     */
    public static function failed(string $error, string $provider = '', string $model = ''): self
    {
        return new self(
            content: '',
            inputTokens: 0,
            outputTokens: 0,
            model: $model,
            provider: $provider,
            success: false,
            error: $error,
        );
    }
}
