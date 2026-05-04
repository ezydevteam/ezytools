<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'phone', 'phone_verified_at', 'password', 'role', 'is_active',
        'google_id', 'google_token', 'avatar',
        'subscription_type', 'subscription_expires_at',
        'daily_usage_count', 'last_usage_date', 'ai_credit',
        'two_factor_secret', 'two_factor_recovery_codes', 'two_factor_confirmed_at',
    ];

    protected $hidden = [
        'password', 'remember_token', 'two_factor_secret', 'two_factor_recovery_codes'
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'is_pro',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'subscription_expires_at' => 'datetime',
            'last_usage_date' => 'date',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    /**
     * Check if user is admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user has active pro subscription.
     */
    public function isPro(): bool
    {
        return $this->subscription_type === 'pro'
            && ($this->subscription_expires_at === null || $this->subscription_expires_at->isFuture());
    }

    /**
     * Get the is_pro attribute.
     */
    public function getIsProAttribute(): bool
    {
        return $this->isPro();
    }

    /**
     * Get user's subscriptions.
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Get user's tool usages.
     */
    public function toolUsages(): HasMany
    {
        return $this->hasMany(ToolUsage::class);
    }

    /**
     * Get user's favorite tools.
     */
    public function favoriteTools(): BelongsToMany
    {
        return $this->belongsToMany(Tool::class, 'favorites')
            ->withTimestamps();
    }

    /**
     * Get active subscription.
     */
    public function activeSubscription(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Subscription::class)
            ->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            })
            ->latest('id');
    }
}
