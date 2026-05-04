<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PdfJob extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [];

    protected $casts = [
        'input_files' => 'array',
        'expires_at' => 'datetime',
    ];
}
