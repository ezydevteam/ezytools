<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Carbon;

class CleanupUploads extends Command
{
    protected $signature = 'cleanup:uploads
                            {--hours=2 : Delete files older than this many hours}
                            {--force : Skip confirmation}
                            {--dry-run : Show what would be deleted without deleting}';

    protected $description = 'Clean up temporary uploads, orphaned files, and stale storage data';

    /**
     * Directories to clean (relative to storage/app).
     * Files older than --hours will be deleted.
     */
    private array $tempDirectories = [
        'private/pdf-jobs',     // Legacy PDF job files (system removed)
        'private/temp',         // General temp uploads
        'private/tmp',          // Alternative temp directory
    ];

    /**
     * Directories in public storage to clean old files from.
     * Only files (not subdirectories) older than threshold are removed.
     */
    private array $publicTempDirectories = [
        'public/temp',
        'public/tmp',
    ];

    public function handle(): int
    {
        $hours = (int) $this->option('hours');
        $dryRun = $this->option('dry-run');
        $threshold = Carbon::now()->subHours($hours);

        $this->info("🧹 Cleanup Uploads — removing files older than {$hours} hours");
        if ($dryRun) {
            $this->warn('DRY RUN mode — nothing will be deleted');
        }
        $this->newLine();

        $totalFiles = 0;
        $totalSize = 0;

        // 1. Clean temporary directories (remove all old files)
        foreach ($this->tempDirectories as $dir) {
            $path = storage_path("app/{$dir}");
            if (!File::isDirectory($path)) {
                continue;
            }

            [$files, $size] = $this->cleanDirectory($path, $threshold, $dryRun, removeEmptyDirs: true);
            $totalFiles += $files;
            $totalSize += $size;
        }

        // 2. Clean public temp directories
        foreach ($this->publicTempDirectories as $dir) {
            $path = storage_path("app/{$dir}");
            if (!File::isDirectory($path)) {
                continue;
            }

            [$files, $size] = $this->cleanDirectory($path, $threshold, $dryRun, removeEmptyDirs: true);
            $totalFiles += $files;
            $totalSize += $size;
        }

        // 3. Clean old Laravel log files (keep last 7 days)
        $logThreshold = Carbon::now()->subDays(7);
        $logPath = storage_path('logs');
        if (File::isDirectory($logPath)) {
            [$files, $size] = $this->cleanOldLogs($logPath, $logThreshold, $dryRun);
            $totalFiles += $files;
            $totalSize += $size;
        }

        // 4. Clean compiled views cache
        $viewCachePath = storage_path('framework/views');
        if (File::isDirectory($viewCachePath)) {
            $viewFiles = File::files($viewCachePath);
            $oldViews = collect($viewFiles)->filter(fn($f) => Carbon::createFromTimestamp($f->getMTime())->lt($threshold));
            if ($oldViews->isNotEmpty()) {
                $viewSize = $oldViews->sum(fn($f) => $f->getSize());
                $this->line("  <comment>views cache</comment>: {$oldViews->count()} old compiled views (" . $this->formatBytes($viewSize) . ")");
                if (!$dryRun) {
                    foreach ($oldViews as $f) {
                        File::delete($f->getPathname());
                    }
                }
                $totalFiles += $oldViews->count();
                $totalSize += $viewSize;
            }
        }

        // 5. Clean up expired SEO reports older than 7 days
        $expiredReportsCount = \App\Models\SeoAuditReport::where('created_at', '<', Carbon::now()->subDays(7))->count();
        if ($expiredReportsCount > 0) {
            $this->line("  <comment>seo reports</comment>: deleting {$expiredReportsCount} expired audit reports");
            if (!$dryRun) {
                \App\Models\SeoAuditReport::where('created_at', '<', Carbon::now()->subDays(7))->delete();
            }
        }

        $this->newLine();
        $this->info("✅ Total: {$totalFiles} files — " . $this->formatBytes($totalSize) . ($dryRun ? ' (dry run)' : ' cleaned'));

        return self::SUCCESS;
    }

    /**
     * Clean a directory of files older than threshold.
     * Returns [fileCount, totalSizeBytes].
     */
    private function cleanDirectory(string $path, Carbon $threshold, bool $dryRun, bool $removeEmptyDirs = false): array
    {
        $files = 0;
        $size = 0;
        $dirName = str_replace(storage_path('app') . DIRECTORY_SEPARATOR, '', $path);

        // Get all files recursively
        $allFiles = File::allFiles($path);
        $oldFiles = collect($allFiles)->filter(fn($f) => Carbon::createFromTimestamp($f->getMTime())->lt($threshold));

        if ($oldFiles->isEmpty()) {
            return [0, 0];
        }

        $totalSize = $oldFiles->sum(fn($f) => $f->getSize());
        $this->line("  <comment>{$dirName}</comment>: {$oldFiles->count()} files (" . $this->formatBytes($totalSize) . ")");

        if (!$dryRun) {
            foreach ($oldFiles as $file) {
                File::delete($file->getPathname());
            }
        }

        $files = $oldFiles->count();
        $size = $totalSize;

        // Remove empty subdirectories
        if ($removeEmptyDirs && !$dryRun) {
            $this->removeEmptyDirectories($path);
        }

        return [$files, $size];
    }

    /**
     * Clean old log files, keeping last N days.
     */
    private function cleanOldLogs(string $path, Carbon $threshold, bool $dryRun): array
    {
        $files = File::files($path);
        $oldLogs = collect($files)
            ->filter(fn($f) => $f->getExtension() === 'log')
            ->filter(fn($f) => Carbon::createFromTimestamp($f->getMTime())->lt($threshold))
            ->filter(fn($f) => $f->getFilename() !== 'laravel.log'); // Keep current log

        if ($oldLogs->isEmpty()) {
            return [0, 0];
        }

        $totalSize = $oldLogs->sum(fn($f) => $f->getSize());
        $this->line("  <comment>logs</comment>: {$oldLogs->count()} old log files (" . $this->formatBytes($totalSize) . ")");

        if (!$dryRun) {
            foreach ($oldLogs as $f) {
                File::delete($f->getPathname());
            }
        }

        return [$oldLogs->count(), $totalSize];
    }

    /**
     * Recursively remove empty directories.
     */
    private function removeEmptyDirectories(string $path): void
    {
        $dirs = File::directories($path);
        foreach ($dirs as $dir) {
            $this->removeEmptyDirectories($dir);
            if (count(File::allFiles($dir)) === 0 && count(File::directories($dir)) === 0) {
                File::deleteDirectory($dir);
            }
        }
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes === 0) return '0 B';
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = floor(log($bytes, 1024));
        return round($bytes / pow(1024, $i), 1) . ' ' . $units[$i];
    }
}
