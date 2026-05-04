<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ToolSetting extends Model
{
    protected $fillable = [
        'tool_id',
        'key',
        'value',
        'type',
        'label',
        'description',
    ];

    /**
     * Get the tool this setting belongs to.
     */
    public function tool(): BelongsTo
    {
        return $this->belongsTo(Tool::class);
    }

    /**
     * Get the typed value based on the type field.
     */
    public function getTypedValueAttribute()
    {
        return match ($this->type) {
            'number' => is_numeric($this->value) ? (float) $this->value : 0,
            'boolean' => filter_var($this->value, FILTER_VALIDATE_BOOLEAN),
            'json' => json_decode($this->value, true) ?? [],
            default => $this->value,
        };
    }
}
