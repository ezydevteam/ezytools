<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AiProvider extends Model
{
    protected $fillable = [
        'name',
        'label',
        'api_key',
        'base_url',
        'is_active',
        'is_default',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_default' => 'boolean',
            'api_key' => 'encrypted',
        ];
    }

    /**
     * Get models for this provider.
     */
    public function models(): HasMany
    {
        return $this->hasMany(AiModel::class, 'provider_id');
    }

    /**
     * Get active models for this provider.
     */
    public function activeModels(): HasMany
    {
        return $this->hasMany(AiModel::class, 'provider_id')
            ->where('is_active', true);
    }

    /**
     * Get AI usages for this provider.
     */
    public function usages(): HasMany
    {
        return $this->hasMany(AiUsage::class, 'provider_id');
    }

    /**
     * Scope for active providers.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the default provider.
     */
    public static function getDefault(): ?self
    {
        return static::where('is_default', true)->where('is_active', true)->first();
    }
}
