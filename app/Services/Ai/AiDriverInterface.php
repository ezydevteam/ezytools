<?php

namespace App\Services\Ai;

use App\Models\AiModel;
use App\Models\AiProvider;

/**
 * Interface that all AI provider drivers must implement.
 */
interface AiDriverInterface
{
    /**
     * Send a message to the AI provider and return a response.
     */
    public function chat(
        AiProvider $provider,
        AiModel $model,
        string $systemPrompt,
        string $userMessage,
        int $maxTokens,
        float $temperature,
    ): AiResponse;
}
