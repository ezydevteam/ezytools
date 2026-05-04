<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ApiKeyExposureAudit extends Command
{
    protected $signature = 'audit:api-keys';
    protected $description = 'Check for exposed API keys in frontend code and build assets';

    /**
     * Patterns that match sensitive API keys or secrets.
     */
    private array $sensitivePatterns = [
        '/sk-[a-zA-Z0-9]{20,}/'                            => 'OpenAI API Key',
        '/AIza[a-zA-Z0-9_\-]{35}/'                         => 'Google/Gemini API Key',
        '/xai-[a-zA-Z0-9]{20,}/'                           => 'Grok API Key',
        '/AAAA[a-zA-Z0-9_\-]{140,}/'                       => 'Firebase Key',
        '/[0-9]+-[a-z0-9]+\.apps\.googleusercontent\.com/' => 'Google OAuth Client',
        '/sslcz_[a-z0-9_]+/'                               => 'SSLCommerz Key',
        '/DB_PASSWORD\s*=\s*\S+/'                           => 'Database Password',
        '/APP_KEY\s*=\s*base64:/'                           => 'Laravel App Key',
    ];

    /**
     * Patterns for direct API calls from frontend (should go via backend).
     */
    private array $frontendApiPatterns = [
        '/https:\/\/api\.openai\.com/i'                => 'Direct OpenAI API call in frontend',
        '/https:\/\/generativelanguage\.googleapis/i'  => 'Direct Gemini API call in frontend',
        '/https:\/\/api\.x\.ai/i'                      => 'Direct Grok API call in frontend',
        '/process\.env\.[A-Z_]*(?:KEY|SECRET|PASSWORD)/i' => 'Env secret reference in frontend',
    ];

    public function handle(): int
    {
        $this->info('=== API Key Exposure Audit ===');
        $this->newLine();
        $issues = [];

        // 1. Scan compiled JS build files
        $this->line('--- Scanning build assets ---');
        $buildDir = public_path('build/assets');
        if (File::isDirectory($buildDir)) {
            $buildFiles = File::glob($buildDir . '/*.js');
            $this->line("Found " . count($buildFiles) . " JS build files");

            foreach ($buildFiles as $file) {
                $content = File::get($file);
                $relPath = 'public/build/assets/' . basename($file);

                foreach ($this->sensitivePatterns as $pattern => $description) {
                    if (preg_match($pattern, $content, $matches)) {
                        $issues[] = [
                            'file' => $relPath,
                            'description' => $description,
                            'match' => substr($matches[0], 0, 20) . '...',
                        ];
                    }
                }
            }
        } else {
            $this->line('ℹ️  No build directory found (run npm run build first)');
        }

        // 2. Scan Vue/JS source files
        $this->newLine();
        $this->line('--- Scanning source files ---');
        $jsDir = resource_path('js');
        $sourceFiles = $this->getFilesRecursive($jsDir, ['vue', 'js', 'ts']);
        $this->line("Found " . count($sourceFiles) . " source files");

        foreach ($sourceFiles as $file) {
            $content = File::get($file);
            $relPath = str_replace(base_path() . DIRECTORY_SEPARATOR, '', $file);

            // Check for hardcoded API calls bypassing backend
            foreach ($this->frontendApiPatterns as $pattern => $description) {
                if (preg_match($pattern, $content)) {
                    // Ignore if it's clearly a placeholder or example
                    if (preg_match('/placeholder\s*=\s*["\'][^"\']*' . preg_quote(str_replace('/', '\/', trim($pattern, '/')), '/') . '/i', $content)) {
                        continue;
                    }
                    
                    // Specific ignore for the OpenAI API placeholder in admin settings
                    if (str_contains($content, 'placeholder="https://api.openai.com/v1"')) {
                        continue;
                    }

                    $issues[] = [
                        'file' => $relPath,
                        'description' => $description,
                        'match' => 'Pattern match',
                    ];
                }
            }

            // Check for hardcoded API keys in source
            foreach ($this->sensitivePatterns as $pattern => $description) {
                if (preg_match($pattern, $content, $matches)) {
                    $issues[] = [
                        'file' => $relPath,
                        'description' => $description . ' (in source)',
                        'match' => substr($matches[0], 0, 20) . '...',
                    ];
                }
            }
        }

        // 3. Check Inertia shared data for sensitive info
        $this->newLine();
        $this->line('--- Checking Inertia shared data ---');
        $this->checkInertiaSharedData();

        // 4. Check .env is not web accessible
        if (file_exists(public_path('.env'))) {
            $issues[] = [
                'file' => 'public/.env',
                'description' => '🚨 CRITICAL: .env file in public folder!',
                'match' => 'File exists',
            ];
        } else {
            $this->line('✅ No .env in public/');
        }

        // 5. Check for .env.backup or similar in public
        $envVariants = ['.env.backup', '.env.production', '.env.local', '.env.staging'];
        foreach ($envVariants as $variant) {
            if (file_exists(public_path($variant))) {
                $issues[] = [
                    'file' => "public/{$variant}",
                    'description' => "CRITICAL: {$variant} in public folder!",
                    'match' => 'File exists',
                ];
            }
        }

        // Report
        $this->newLine();
        $this->info('─── API Key Exposure Summary ───');

        if (empty($issues)) {
            $this->info('✅ No API key exposures found!');
        } else {
            $this->error(count($issues) . ' exposure risk(s) found:');
            $this->newLine();
            $this->table(
                ['File', 'Risk', 'Match'],
                array_map(fn($i) => [$i['file'], $i['description'], $i['match']], $issues)
            );
        }

        return empty($issues) ? self::SUCCESS : self::FAILURE;
    }

    /**
     * Check HandleInertiaRequests for accidentally shared sensitive data.
     */
    private function checkInertiaSharedData(): void
    {
        $file = app_path('Http/Middleware/HandleInertiaRequests.php');
        if (!file_exists($file)) {
            $this->warn('⚠️  HandleInertiaRequests.php not found');
            return;
        }

        $content = File::get($file);

        $sensitiveKeys = [
            'api_key', 'secret', 'password', 'token',
            'openai', 'gemini', 'grok', 'sslcz',
            'private_key', 'app_key',
        ];

        $found = false;
        foreach ($sensitiveKeys as $key) {
            if (stripos($content, "'{$key}'") !== false || stripos($content, "\"{$key}\"") !== false) {
                $this->warn("⚠️  HandleInertiaRequests may expose '{$key}' to frontend");
                $found = true;
            }
        }

        if (!$found) {
            $this->line('✅ Inertia shared data appears safe');
        }
    }

    /**
     * Recursively get files with specific extensions.
     */
    private function getFilesRecursive(string $directory, array $extensions): array
    {
        if (!File::isDirectory($directory)) {
            return [];
        }

        $files = [];
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory, \RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && in_array($file->getExtension(), $extensions)) {
                $files[] = $file->getPathname();
            }
        }

        return $files;
    }
}
