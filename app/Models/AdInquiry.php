<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdInquiry extends Model
{
    protected $fillable = [
        'inquiry_id',
        'name',
        'email',
        'company',
        'website',
        'ad_spaces',
        'duration',
        'budget',
        'message',
        'status',
        'admin_notes',
    ];

    protected function casts(): array
    {
        return [
            'ad_spaces' => 'array',
        ];
    }

    /**
     * Generate a unique inquiry ID.
     */
    public static function generateInquiryId(): string
    {
        do {
            $id = 'ADQ-' . strtoupper(substr(uniqid(), -6));
        } while (static::where('inquiry_id', $id)->exists());

        return $id;
    }

    /**
     * Scope by status.
     */
    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }
}
