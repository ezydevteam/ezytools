<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tool extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'short_description',
        'slug',
        'component_name',
        'icon',
        'is_active',
        'is_premium',
        'daily_limit_free',
        'daily_limit_pro',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'supported_languages',
        'default_language',
        'order',
        'usage_count',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_premium' => 'boolean',
            'supported_languages' => 'array',
        ];
    }

    /**
     * Get the category this tool belongs to.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ToolCategory::class, 'category_id');
    }

    /**
     * Get tool settings.
     */
    public function settings(): HasMany
    {
        return $this->hasMany(ToolSetting::class);
    }

    /**
     * Get tool usages.
     */
    public function usages(): HasMany
    {
        return $this->hasMany(ToolUsage::class);
    }

    /**
     * Get users who favorited this tool.
     */
    public function favoritedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorites')
            ->withTimestamps();
    }

    /**
     * Get AI configuration for this tool.
     */
    public function aiConfig(): HasOne
    {
        return $this->hasOne(AiToolConfig::class);
    }

    /**
     * Get AI usage records for this tool.
     */
    public function aiUsages(): HasMany
    {
        return $this->hasMany(AiUsage::class);
    }

    /**
     * Check if this is an AI-powered tool.
     */
    public function isAiTool(): bool
    {
        return $this->category && $this->category->slug === 'ai-tools';
    }

    /**
     * Get settings as key-value array.
     */
    public function getSettingsArray(): array
    {
        return $this->settings->pluck('value', 'key')->toArray();
    }

    /**
     * Scope for active tools.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for ordered tools.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    /**
     * Scope for popular tools.
     */
    public function scopePopular($query)
    {
        return $query->orderByDesc('usage_count');
    }

    /**
     * Get tool FAQs.
     */
    public function faqs(): HasMany
    {
        return $this->hasMany(ToolFaq::class)->orderBy('order');
    }

    /**
     * Get tool SEO content.
     */
    public function seoContent(): HasOne
    {
        return $this->hasOne(ToolSeoContent::class);
    }

    /**
     * Get related tools.
     */
    public function relatedTools(): BelongsToMany
    {
        return $this->belongsToMany(
            Tool::class, 'tool_related',
            'tool_id', 'related_tool_id'
        )->withPivot('relation_type', 'order')
         ->orderByPivot('order');
    }

    /**
     * Get all ratings/reviews.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(ToolReview::class);
    }

    /**
     * Average rating.
     */
    public function getAverageRatingAttribute(): float
    {
        return (float) ($this->reviews()->avg('rating') ?? 0);
    }

    /**
     * Total review count.
     */
    public function getReviewCountAttribute(): int
    {
        return $this->reviews()->count();
    }

    /**
     * Get formatted usage count (e.g. 1.2K, 3.4M).
     */
    public function getFormattedUsageCountAttribute(): string
    {
        $count = $this->usage_count;
        if ($count >= 1000000) return number_format($count / 1000000, 1) . 'M';
        if ($count >= 1000)    return number_format($count / 1000, 1) . 'K';
        return number_format($count);
    }

    /**
     * Scope for searching tools.
     */
    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('short_description', 'like', "%{$term}%")
              ->orWhere('description', 'like', "%{$term}%")
              ->orWhere('description_bn', 'like', "%{$term}%");
        });
    }
}
