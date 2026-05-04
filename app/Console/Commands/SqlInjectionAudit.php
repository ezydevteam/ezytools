<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class SqlInjectionAudit extends Command
{
    protected $signature = 'audit:sql-injection';
    protected $description = 'Scan code for potential SQL injection vulnerabilities';

    /**
     * Dangerous patterns that indicate potential SQL injection.
     */
    private array $dangerousPatterns = [
        '/DB::(?:select|insert|update|delete|statement)\s*\(\s*["\'].*\$/' =>
            'Raw DB query with variable interpolation',

        '/whereRaw\s*\(\s*["\'].*\$(?!\s*\])/' =>
            'whereRaw with variable interpolation (no bindings)',

        '/orderByRaw\s*\(\s*["\'].*\$/' =>
            'orderByRaw with variable interpolation',

        '/selectRaw\s*\(\s*["\'].*\$/' =>
            'selectRaw with variable interpolation',

        '/havingRaw\s*\(\s*["\'].*\$/' =>
            'havingRaw with variable interpolation',

        '/(?:DB::|->where|->find)\s*\(.*\$_(?:GET|POST|REQUEST)/' =>
            'Direct $_GET/$_POST/$_REQUEST in query',

        '/DB::raw\s*\(\s*["\'].*\$/' =>
            'DB::raw with variable interpolation',
    ];

    public function handle(): int
    {
        $this->info('=== SQL Injection Audit ===');
        $this->newLine();
        $issues = [];

        // Collect all PHP files from app/ and routes/
        $directories = [
            app_path(),
            base_path('routes'),
        ];

        $files = [];
        foreach ($directories as $dir) {
            if (File::isDirectory($dir)) {
                $files = array_merge($files, $this->getPhpFiles($dir));
            }
        }

        $this->line("Scanning " . count($files) . " PHP files...");
        $this->newLine();

        foreach ($files as $file) {
            $content = File::get($file);
            $lines = explode("\n", $content);
            $relPath = str_replace(base_path() . DIRECTORY_SEPARATOR, '', $file);

            foreach ($lines as $lineNum => $line) {
                $trimmed = trim($line);
                // Skip comments
                if (str_starts_with($trimmed, '//') || str_starts_with($trimmed, '*') || str_starts_with($trimmed, '/*')) {
                    continue;
                }

                foreach ($this->dangerousPatterns as $pattern => $description) {
                    if (preg_match($pattern, $line)) {
                        $issues[] = [
                            'file' => $relPath,
                            'line' => $lineNum + 1,
                            'description' => $description,
                            'code' => trim(substr($trimmed, 0, 80)),
                        ];
                    }
                }
            }
        }

        if (empty($issues)) {
            $this->info('✅ No SQL injection vulnerabilities found!');
        } else {
            $this->error(count($issues) . ' potential issue(s) found:');
            $this->newLine();
            $this->table(
                ['File', 'Line', 'Issue', 'Code'],
                array_map(fn($i) => [
                    $i['file'],
                    $i['line'],
                    $i['description'],
                    strlen($i['code']) > 60 ? substr($i['code'], 0, 57) . '...' : $i['code'],
                ], $issues)
            );
        }

        // Also check: controllers that accept Request but don't validate
        $this->newLine();
        $this->checkRequestValidation();

        return empty($issues) ? self::SUCCESS : self::FAILURE;
    }

    /**
     * Check that controllers using Request also call validate().
     */
    private function checkRequestValidation(): void
    {
        $this->info('--- Checking Request Validation ---');

        $controllerDir = app_path('Http/Controllers');
        if (!File::isDirectory($controllerDir)) {
            return;
        }

        $controllers = $this->getPhpFiles($controllerDir);
        $noValidation = [];

        foreach ($controllers as $file) {
            $content = File::get($file);
            $relPath = str_replace(base_path() . DIRECTORY_SEPARATOR, '', $file);

            // Find methods that accept a Request or subclass of Request
            if (preg_match_all('/function\s+(\w+)\s*\(.*?(?:Request)\s+\$/', $content, $matches)) {
                foreach ($matches[1] as $method) {
                    // Skip constructor and common non-data or boilerplate methods
                    if (in_array($method, [
                        '__construct', 'index', 'show', 'create', 'edit', 'destroy', 
                        '__invoke', 'redirect', 'callback', 'faq', 'about', 'contact',
                        'privacy', 'terms', 'gdpr', 'doNotSell'
                    ])) {
                        continue;
                    }

                    // Check if validation is used in the file
                    // We look for common validation indicators:
                    // 1. $request->validate(...)
                    // 2. $request->validated()
                    // 3. FormRequest in the 'use' or 'extends'
                    // 4. Validator::make(...)
                    // 5. Custom Request types that usually imply validation (ending in Request)
                    $hasValidation = preg_match('/\$request->validate|->validated\(\)|Validator::make|FormRequest/', $content)
                        || preg_match('/function\s+' . $method . '\s*\(.*?\w+Request\s+\$/', $content);

                    if (!$hasValidation) {
                        $noValidation[] = "{$relPath}::{$method}()";
                    }
                }
            }
        }

        if (!empty($noValidation)) {
            $this->warn('Controllers/methods possibly missing validation:');
            foreach ($noValidation as $item) {
                $this->line("  ⚠️  {$item}");
            }
        } else {
            $this->line('✅ All controllers appear to have validation');
        }
    }

    /**
     * Recursively get all PHP files in a directory.
     */
    private function getPhpFiles(string $directory): array
    {
        $files = [];
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory, \RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $files[] = $file->getPathname();
            }
        }

        return $files;
    }
}
