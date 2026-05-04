<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiModel extends Model
{
    protected $fillable = [
        'provider_id',
        'name',
        'label',
        'context_window',
        'cost_per_1k_input_tokens',
        'cost_per_1k_output_tokens',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'cost_per_1k_input_tokens' => 'decimal:6',
            'cost_per_1k_output_tokens' => 'decimal:6',
        ];
    }

    /**
     * Get the provider this model belongs to.
     */
    public function provider(): BelongsTo
    {
        return $this->belongsTo(AiProvider::class, 'provider_id');
    }

    /**
     * Scope for active models.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Calculate cost for given token counts.
     */
    public function calculateCost(int $inputTokens, int $outputTokens): float
    {
        $inputCost = ($inputTokens / 1000) * (float) $this->cost_per_1k_input_tokens;
        $outputCost = ($outputTokens / 1000) * (float) $this->cost_per_1k_output_tokens;

        return $inputCost + $outputCost;
    }
}
