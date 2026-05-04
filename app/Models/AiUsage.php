<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiUsage extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'tool_id',
        'user_id',
        'ip_address',
        'provider_id',
        'model_id',
        'input_tokens',
        'output_tokens',
        'cost_usd',
        'status',
        'error_message',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'cost_usd' => 'decimal:8',
            'created_at' => 'datetime',
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

    public function provider(): BelongsTo
    {
        return $this->belongsTo(AiProvider::class, 'provider_id');
    }

    public function model(): BelongsTo
    {
        return $this->belongsTo(AiModel::class, 'model_id');
    }

    /**
     * Get today's usage count for a user or IP.
     */
    public static function todayCountFor(?int $userId, ?string $ip): int
    {
        return static::where('created_at', '>=', today())
            ->where(function ($q) use ($userId, $ip) {
                if ($userId) {
                    $q->where('user_id', $userId);
                } else {
                    $q->where('ip_address', $ip);
                }
            })
            ->where('status', 'success')
            ->count();
    }

    /**
     * Get today's total cost in USD.
     */
    public static function todayTotalCost(): float
    {
        return (float) static::where('created_at', '>=', today())
            ->sum('cost_usd');
    }
}
