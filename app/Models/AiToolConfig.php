<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiToolConfig extends Model
{
    protected $fillable = [
        'tool_id',
        'provider_id',
        'model_id',
        'pro_provider_id',
        'pro_model_id',
        'fallback_provider_id',
        'fallback_model_id',
        'system_prompt',
        'max_tokens_free',
        'max_tokens_pro',
        'max_input_length_free',
        'max_input_length_pro',
        'temperature',
        'supported_languages',
        'default_language',
        'output_format',
        'show_language_selector',
        'enable_rtl_support',
        'credit_cost',
    ];

    protected function casts(): array
    {
        return [
            'temperature' => 'decimal:2',
            'supported_languages' => 'array',
            'show_language_selector' => 'boolean',
            'enable_rtl_support' => 'boolean',
        ];
    }

    public function tool(): BelongsTo
    {
        return $this->belongsTo(Tool::class);
    }

    // Free tier relations
    public function freeProvider(): BelongsTo
    {
        return $this->belongsTo(AiProvider::class, 'provider_id');
    }

    public function freeModel(): BelongsTo
    {
        return $this->belongsTo(AiModel::class, 'model_id');
    }

    // Pro tier relations
    public function proProvider(): BelongsTo
    {
        return $this->belongsTo(AiProvider::class, 'pro_provider_id');
    }

    public function proModel(): BelongsTo
    {
        return $this->belongsTo(AiModel::class, 'pro_model_id');
    }

    // Fallback relations
    public function fallbackProvider(): BelongsTo
    {
        return $this->belongsTo(AiProvider::class, 'fallback_provider_id');
    }

    public function fallbackModel(): BelongsTo
    {
        return $this->belongsTo(AiModel::class, 'fallback_model_id');
    }

    /**
     * Get the appropriate provider for the given user tier.
     */
    public function getProviderForTier(bool $isPro): ?AiProvider
    {
        return $isPro
            ? ($this->proProvider ?? $this->freeProvider)
            : $this->freeProvider;
    }

    /**
     * Get the appropriate model for the given user tier.
     */
    public function getModelForTier(bool $isPro): ?AiModel
    {
        return $isPro
            ? ($this->proModel ?? $this->freeModel)
            : $this->freeModel;
    }

    /**
     * Get the max tokens for the given user tier.
     */
    public function getMaxTokensForTier(bool $isPro): int
    {
        return $isPro ? $this->max_tokens_pro : $this->max_tokens_free;
    }
}
