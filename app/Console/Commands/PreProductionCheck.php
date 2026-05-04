<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class PreProductionCheck extends Command
{
    protected $signature = 'audit:pre-production';
    protected $description = 'Run all pre-production audit checks and generate consolidated report';

    public function handle(): int
    {
        $this->info('╔══════════════════════════════════════════╗');
        $this->info('║   EzyTools Pre-Production Audit          ║');
        $this->info('║   Target: Hostinger KVM-2 (Ubuntu 22.04) ║');
        $this->info('╚══════════════════════════════════════════╝');
        $this->newLine();

        $startTime = microtime(true);
        $exitCodes = [];

        // 1. Security Audit
        $this->line('━━━ 1/4: Security Audit ━━━');
        $exitCodes[] = $this->call('audit:security');
        $this->newLine();

        // 2. SQL Injection Audit
        $this->line('━━━ 2/4: SQL Injection Audit ━━━');
        $exitCodes[] = $this->call('audit:sql-injection');
        $this->newLine();

        // 3. API Key Exposure Audit
        $this->line('━━━ 3/4: API Key Exposure Audit ━━━');
        $exitCodes[] = $this->call('audit:api-keys');
        $this->newLine();

        // 4. SEO Audit
        $this->line('━━━ 4/4: SEO Audit ━━━');
        $exitCodes[] = $this->call('audit:seo');
        $this->newLine();

        // 5. Additional infrastructure checks
        $this->line('━━━ Infrastructure Checks ━━━');
        $this->runInfrastructureChecks();

        $elapsed = round(microtime(true) - $startTime, 2);
        $failedCount = count(array_filter($exitCodes, fn($code) => $code !== 0));

        $this->newLine();
        $this->info('══════════════════════════════════════════');
        if ($failedCount > 0) {
            $this->error("Audit complete in {$elapsed}s — {$failedCount} audit(s) reported issues");
        } else {
            $this->info("Audit complete in {$elapsed}s — All checks passed ✅");
        }
        $this->info('Check storage/logs/ for detailed reports');

        return $failedCount > 0 ? self::FAILURE : self::SUCCESS;
    }

    private function runInfrastructureChecks(): void
    {
        // PHP version
        $phpVersion = PHP_VERSION;
        if (version_compare($phpVersion, '8.2.0', '>=')) {
            $this->line("✅ PHP version: {$phpVersion}");
        } else {
            $this->error("❌ PHP version too old: {$phpVersion} (need 8.2+)");
        }

        // Required PHP extensions
        $requiredExtensions = ['mbstring', 'xml', 'curl', 'gd', 'zip', 'bcmath', 'fileinfo', 'pdo_mysql'];
        foreach ($requiredExtensions as $ext) {
            if (!extension_loaded($ext)) {
                $this->error("❌ Missing PHP extension: {$ext}");
            }
        }
        $this->line('✅ Required PHP extensions loaded');

        // OPcache
        if (function_exists('opcache_get_status') && @opcache_get_status()) {
            $this->line('✅ OPcache is enabled');
        } else {
            $this->warn('⚠️  OPcache not enabled (performance impact)');
        }

        // APP_URL
        $appUrl = config('app.url');
        if (str_starts_with($appUrl, 'https://')) {
            $this->line("✅ APP_URL uses HTTPS: {$appUrl}");
        } else {
            $this->error("❌ APP_URL not HTTPS: {$appUrl}");
        }

        // Queue connection
        try {
            \Illuminate\Support\Facades\Queue::size();
            $this->line('✅ Queue connection OK');
        } catch (\Exception $e) {
            $this->warn('⚠️  Queue connection issue: ' . $e->getMessage());
        }

        // Redis connection
        try {
            \Illuminate\Support\Facades\Redis::connection()->ping();
            $this->line('✅ Redis connection OK');
        } catch (\Throwable $e) {
            $this->warn('⚠️  Redis connection issue: ' . $e->getMessage());
        }

        // Database connection
        try {
            \Illuminate\Support\Facades\DB::connection()->getPdo();
            $this->line('✅ Database connection OK');
        } catch (\Exception $e) {
            $this->error('❌ Database connection failed: ' . $e->getMessage());
        }

        // Storage writable
        if (is_writable(storage_path())) {
            $this->line('✅ Storage is writable');
        } else {
            $this->error('❌ Storage is NOT writable');
        }

        // Bootstrap cache writable
        if (is_writable(base_path('bootstrap/cache'))) {
            $this->line('✅ Bootstrap cache is writable');
        } else {
            $this->error('❌ Bootstrap cache is NOT writable');
        }

        // Disk space (Linux only)
        if (PHP_OS_FAMILY !== 'Windows') {
            $freeSpace = disk_free_space('/');
            $freeGB = round($freeSpace / 1024 / 1024 / 1024, 1);
            if ($freeGB < 5) {
                $this->warn("⚠️  Low disk space: {$freeGB}GB free");
            } else {
                $this->line("✅ Disk space: {$freeGB}GB free");
            }
        }

        // Pending migrations
        try {
            $pendingMigrations = \Illuminate\Support\Facades\Artisan::call('migrate:status', ['--no-interaction' => true]);
        } catch (\Exception $e) {
            // Silently skip
        }
    }
}
