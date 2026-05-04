<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailCampaign extends Model
{
    protected $fillable = [
        'name', 'subject', 'preheader', 'body_heading', 'body_content',
        'cta_text', 'cta_url', 'target_audience', 'scheduled_at',
        'sent_at', 'total_recipients', 'status',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'sent_at'      => 'datetime',
        ];
    }

    public function scopeDraft($q)    { return $q->where('status', 'draft'); }
    public function scopeSent($q)     { return $q->where('status', 'sent'); }
    public function scopeScheduled($q){ return $q->where('status', 'scheduled'); }
}
