<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ToolReview extends Model
{
    protected $fillable = [
        'tool_id', 'user_id', 'rating', 'ip_address', 'is_approved',
    ];

    protected function casts(): array
    {
        return [
            'is_approved' => 'boolean',
        ];
    }

    public function tool(): BelongsTo
    {
        return $this->belongsTo(Tool::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
