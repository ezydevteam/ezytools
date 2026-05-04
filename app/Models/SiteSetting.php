<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'label',
    ];

    /**
     * Get a setting value by key with optional default.
     */
    public static function getValue(string $key, mixed $default = null): mixed
    {
        $setting = Cache::remember("site_setting_{$key}", 3600, function () use ($key) {
            $record = static::where('key', $key)->first(['value', 'type']);
            return $record ? ['value' => $record->value, 'type' => $record->type] : null;
        });

        if (!$setting) {
            return $default;
        }

        return match ($setting['type'] ?? '') {
            'number' => is_numeric($setting['value']) ? (float) $setting['value'] : $default,
            'boolean' => filter_var($setting['value'], FILTER_VALIDATE_BOOLEAN),
            'json' => json_decode($setting['value'], true) ?? $default,
            default => $setting['value'] ?? $default,
        };
    }

    /**
     * Set a setting value.
     */
    public static function setValue(string $key, mixed $value): void
    {
        $setting = static::where('key', $key)->first();

        if ($setting) {
            $setting->update(['value' => is_array($value) ? json_encode($value) : $value]);
        }

        Cache::forget("site_setting_{$key}");
    }

    /**
     * Get all settings for a group.
     */
    public static function getGroup(string $group): array
    {
        return Cache::remember("site_settings_group_{$group}", 3600, function () use ($group) {
            return static::where('group', $group)
                ->get()
                ->pluck('value', 'key')
                ->toArray();
        });
    }

    /**
     * Clear all setting caches.
     */
    public static function clearCache(): void
    {
        $settings = static::all();
        foreach ($settings as $setting) {
            Cache::forget("site_setting_{$setting->key}");
        }
        foreach (['general', 'seo', 'subscriptions', 'payment_gateways', 'social', 'custom_code', 'api', 'mail'] as $group) {
            Cache::forget("site_settings_group_{$group}");
        }
    }
}
