<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ToolCategory extends Model
{
    protected $fillable = [
        'name',
        'description',
        'slug',
        'icon',
        'order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get tools in this category.
     */
    public function tools(): HasMany
    {
        return $this->hasMany(Tool::class, 'category_id')
            ->orderBy('order');
    }

    /**
     * Get only active tools in this category.
     */
    public function activeTools(): HasMany
    {
        return $this->hasMany(Tool::class, 'category_id')
            ->where('is_active', true)
            ->orderBy('order');
    }

    /**
     * Scope for active categories.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for ordered categories.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }
}
