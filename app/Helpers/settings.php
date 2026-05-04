<?php

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Cache;

if (!function_exists('setting')) {
    /**
     * Get a site setting by key.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function setting(string $key, $default = null)
    {
        return Cache::remember(
            "setting_{$key}", 
            3600,
            fn() => SiteSetting::getValue($key, $default)
        );
    }
}
