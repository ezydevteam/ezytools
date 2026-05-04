<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiDetectionResult extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'tool_usage_id',
        'input_text',
        'input_length',
        'language_detected',
        'overall_score',
        'verdict',
        'sentence_scores',
        'burstiness_score',
        'perplexity_score',
        'provider_used',
        'processing_ms',
    ];

    protected function casts(): array
    {
        return [
            'overall_score' => 'decimal:2',
            'burstiness_score' => 'decimal:2',
            'perplexity_score' => 'decimal:2',
            'sentence_scores' => 'array',
            'created_at' => 'datetime',
        ];
    }
}
