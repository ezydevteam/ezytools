<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ─── Cleanup Tasks ───

// Every 15 min — delete expired PDF/video temp files and stale DB records
Schedule::command('cleanup:temp-files')->everyFifteenMinutes();

// Hourly — clean up temporary uploads and orphan files
Schedule::command('cleanup:uploads --force')->hourly();

// Daily 3 AM — deep cleanup: old logs + compiled views
Schedule::command('cleanup:uploads --hours=48 --force')->dailyAt('03:00');

// ─── Business Tasks ───

// Daily 9 AM — subscription expiry notification emails
Schedule::command('subscriptions:notify-expiring')->dailyAt('09:00')->timezone('Asia/Dhaka');

// ─── Cache & Maintenance ───

// Hourly — clear expired cache tags
Schedule::command('cache:prune-stale-tags')->hourly();
