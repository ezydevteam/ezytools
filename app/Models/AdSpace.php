<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdSpace extends Model
{
    protected $fillable = [
        'name',
        'position',
        'description',
        'dimensions',
        'est_impressions',
        'price_3d',
        'price_7d',
        'price_30d',
        'type',
        'code',
        'image_url',
        'link_url',
        'is_active',
        'is_available',
        'show_to',
    ];

    protected function casts(): array
    {
        return [
            'is_active'    => 'boolean',
            'is_available' => 'boolean',
            'price_3d'     => 'decimal:2',
            'price_7d'     => 'decimal:2',
            'price_30d'    => 'decimal:2',
        ];
    }

    /**
     * Scope for active ad spaces.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for a specific position.
     */
    public function scopeForPosition($query, string $position)
    {
        return $query->where('position', $position);
    }

    /**
     * Scope for ads visible to a user type.
     */
    public function scopeVisibleTo($query, ?string $userType = 'guest')
    {
        return $query->where(function ($q) use ($userType) {
            $q->where('show_to', 'all')
              ->orWhere('show_to', $userType);
            
            if ($userType === 'guest') {
                $q->orWhere('show_to', 'free');
            }
        });
    }
}
