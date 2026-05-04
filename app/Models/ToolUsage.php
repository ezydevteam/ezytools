<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ToolUsage extends Model
{
    protected $fillable = [
        'tool_id',
        'user_id',
        'ip_address',
        'used_at',
    ];

    protected function casts(): array
    {
        return [
            'used_at' => 'datetime',
        ];
    }

    /**
     * Get the tool.
     */
    public function tool(): BelongsTo
    {
        return $this->belongsTo(Tool::class);
    }

    /**
     * Get the user (nullable for guests).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for today's usages.
     */
    public function scopeToday($query)
    {
        return $query->whereDate('used_at', today());
    }

    /**
     * Scope for a specific tool and IP.
     */
    public function scopeForToolAndIp($query, int $toolId, string $ip)
    {
        return $query->where('tool_id', $toolId)
            ->where('ip_address', $ip);
    }

    /**
     * Scope for a specific tool and user.
     */
    public function scopeForToolAndUser($query, int $toolId, int $userId)
    {
        return $query->where('tool_id', $toolId)
            ->where('user_id', $userId);
    }
}
