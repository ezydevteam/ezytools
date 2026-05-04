<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SeoAuditReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'user_id',
        'ip_address',
        'url',
        'domain',
        'target_keyword',
        'overall_score',
        'technical_score',
        'onpage_score',
        'performance_score',
        'ai_readiness_score',
        'issues_critical',
        'issues_warning',
        'issues_passed',
        'meta_title',
        'meta_description',
        'canonical_url',
        'h1',
        'word_count',
        'load_time',
        'audit_data',
    ];

    protected function casts(): array
    {
        return [
            'overall_score' => 'integer',
            'technical_score' => 'integer',
            'onpage_score' => 'integer',
            'performance_score' => 'integer',
            'ai_readiness_score' => 'integer',
            'issues_critical' => 'integer',
            'issues_warning' => 'integer',
            'issues_passed' => 'integer',
            'word_count' => 'integer',
            'load_time' => 'float',
            'audit_data' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
