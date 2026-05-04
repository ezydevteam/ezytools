<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ToolSeoContent extends Model
{
    protected $fillable = [
        'tool_id', 'how_to_title', 'how_to_title_en',
        'how_to_content', 'how_to_content_en', 'how_to_steps',
        'about_title', 'about_title_en',
        'about_content', 'about_content_en',
        'use_cases', 'last_updated_at',
    ];

    protected function casts(): array
    {
        return [
            'how_to_steps' => 'array',
            'use_cases' => 'array',
            'last_updated_at' => 'datetime',
        ];
    }

    public function tool(): BelongsTo
    {
        return $this->belongsTo(Tool::class);
    }
}
