<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiVoiceJob extends Model
{
    protected $fillable = [
        'user_id',
        'ip_address',
        'text_input',
        'language',
        'voice_id',
        'speed',
        'pitch',
        'output_path',
        'file_size',
        'duration_seconds',
        'status',
        'provider',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'speed' => 'decimal:2',
            'pitch' => 'decimal:2',
            'duration_seconds' => 'decimal:2',
            'expires_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function voice(): BelongsTo
    {
        return $this->belongsTo(AiVoice::class, 'voice_id');
    }

    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<', now());
    }
}
