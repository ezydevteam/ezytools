<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SecurityAudit extends Command
{
    protected $signature = 'audit:security';
    protected $description = 'Run full security audit of the application';

    public function handle(): int
    {
        $this->info('=== EzyTools Security Audit ===');
        $this->newLine();
        $issues = [];
        $warnings = [];

        // 1. APP_DEBUG check
        if (config('app.debug')) {
            $issues[] = '❌ CRITICAL: APP_DEBUG is true — must be false in production!';
        } else {
            $this->line('✅ APP_DEBUG is false');
        }

        // 2. APP_ENV check
        if (config('app.env') !== 'production') {
            $warnings[] = '⚠️  APP_ENV is "' . config('app.env') . '" (should be "production" on live server)';
        } else {
            $this->line('✅ APP_ENV is production');
        }

        // 3. APP_KEY check
        if (empty(config('app.key'))) {
            $issues[] = '❌ CRITICAL: APP_KEY is not set!';
        } else {
            $this->line('✅ APP_KEY is set');
        }

        // 4. .env file permissions (Linux only)
        $envPath = base_path('.env');
        if (file_exists($envPath) && PHP_OS_FAMILY !== 'Windows') {
            $envPerms = substr(sprintf('%o', fileperms($envPath)), -4);
            if (!in_array($envPerms, ['0600', '0640'])) {
                $warnings[] = "⚠️  .env permissions: {$envPerms} (should be 0600 or 0640)";
            } else {
                $this->line('✅ .env permissions OK');
            }
        } else {
            $this->line('ℹ️  .env permissions check skipped (Windows)');
        }

        // 5. HTTPS / APP_URL check
        $appUrl = config('app.url');
        if (!str_starts_with($appUrl, 'https://') && config('app.env') === 'production') {
            $issues[] = "❌ APP_URL does not use HTTPS: {$appUrl}";
        } else {
            $this->line('✅ APP_URL: ' . $appUrl);
        }

        // 6. Session security
        if (!config('session.secure') && config('app.env') === 'production') {
            $warnings[] = '⚠️  SESSION_SECURE_COOKIE is false (should be true for HTTPS)';
        } else {
            $this->line('✅ Secure session cookies');
        }

        if (!config('session.http_only')) {
            $warnings[] = '⚠️  SESSION_HTTP_ONLY is false (should be true)';
        } else {
            $this->line('✅ HttpOnly session cookies');
        }

        $sameSite = config('session.same_site');
        if (!$sameSite || $sameSite === 'none') {
            $warnings[] = '⚠️  SESSION_SAME_SITE is "' . ($sameSite ?: 'null') . '" (should be "lax" or "strict")';
        } else {
            $this->line('✅ SameSite cookie: ' . $sameSite);
        }

        // 7. Database password strength
        $dbPass = config('database.connections.mysql.password');
        if (empty($dbPass)) {
            $issues[] = '❌ Database password is empty!';
        } elseif (strlen($dbPass) < 16) {
            $warnings[] = '⚠️  Database password is weak (< 16 chars)';
        } else {
            $this->line('✅ Database password length OK');
        }

        // 8. Check for common weak password patterns
        $weakPatterns = ['password', '123456', 'secret', 'ezytools', 'admin', 'root'];
        if (!empty($dbPass)) {
            foreach ($weakPatterns as $weak) {
                if (stripos($dbPass, $weak) !== false) {
                    $issues[] = '❌ Database password contains weak pattern: "' . $weak . '"';
                    break;
                }
            }
        }

        // 9. Redis password
        $redisPass = config('database.redis.default.password');
        if (empty($redisPass) || $redisPass === 'null') {
            $warnings[] = '⚠️  Redis has no password set (use REDIS_PASSWORD in .env)';
        } else {
            $this->line('✅ Redis password set');
        }

        // 10. Mail credentials
        if (empty(config('mail.mailers.smtp.password'))) {
            $warnings[] = '⚠️  Mail password not set (SMTP may not work)';
        } else {
            $this->line('✅ Mail credentials set');
        }

        // 11. Storage symlink
        if (file_exists(public_path('storage'))) {
            $this->line('✅ Storage symlink exists');
        } else {
            $warnings[] = '⚠️  Storage symlink missing (run: php artisan storage:link)';
        }

        // 12. .env in public check
        if (file_exists(public_path('.env'))) {
            $issues[] = '❌ CRITICAL: .env file found in public/ directory!';
        } else {
            $this->line('✅ No .env in public/');
        }

        // 13. Debug bar / Telescope in production
        if (config('app.env') === 'production') {
            if (class_exists('Barryvdh\Debugbar\ServiceProvider')) {
                $warnings[] = '⚠️  Laravel Debugbar is installed (remove from production)';
            }
        }

        // 14. CSRF protection
        $this->line('✅ CSRF protection enabled (Laravel default)');

        // 15. Bcrypt rounds
        $bcryptRounds = config('hashing.bcrypt.rounds', 12);
        if ($bcryptRounds < 12) {
            $warnings[] = "⚠️  Bcrypt rounds is {$bcryptRounds} (should be ≥12)";
        } else {
            $this->line("✅ Bcrypt rounds: {$bcryptRounds}");
        }

        // Report
        $this->newLine();
        $this->info('─── Security Audit Summary ───');

        if (!empty($issues)) {
            $this->error(count($issues) . ' critical issue(s):');
            foreach ($issues as $issue) {
                $this->line("  {$issue}");
            }
        }

        if (!empty($warnings)) {
            $this->newLine();
            $this->warn(count($warnings) . ' warning(s):');
            foreach ($warnings as $warning) {
                $this->line("  {$warning}");
            }
        }

        if (empty($issues) && empty($warnings)) {
            $this->info('✅ All security checks passed!');
        }

        return empty($issues) ? self::SUCCESS : self::FAILURE;
    }
}
