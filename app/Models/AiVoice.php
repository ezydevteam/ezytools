<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiVoice extends Model
{
    protected $fillable = [
        'provider',
        'provider_voice_id',
        'name',
        'language',
        'gender',
        'accent',
        'is_active',
        'is_pro_only',
        'preview_url',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_pro_only' => 'boolean',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForLanguage($query, string $language)
    {
        return $query->where('language', $language);
    }

    public function jobs()
    {
        return $this->hasMany(AiVoiceJob::class, 'voice_id');
    }
}
