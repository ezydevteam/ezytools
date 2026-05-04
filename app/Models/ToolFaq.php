<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ToolFaq extends Model
{
    use HasFactory;

    protected $fillable = [
        'tool_id',
        'question',
        'question_bn',
        'answer',
        'answer_bn',
        'order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function tool(): BelongsTo
    {
        return $this->belongsTo(Tool::class);
    }

    /**
     * Scope for active FAQs ordered correctly.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('order');
    }
}
