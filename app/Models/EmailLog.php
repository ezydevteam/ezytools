<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id', 'type', 'subject', 'status',
        'opened_at', 'clicked_at', 'metadata', 'created_at',
    ];

    protected function casts(): array
    {
        return [
            'opened_at'  => 'datetime',
            'clicked_at' => 'datetime',
            'metadata'   => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
