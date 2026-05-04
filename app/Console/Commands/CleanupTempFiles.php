<?php

namespace App\Console\Commands;

use App\Models\AiUsage;
use App\Models\PdfJob;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CleanupTempFiles extends Command
{
    protected $signature = 'cleanup:temp-files
                            {--dry-run : Show what would be deleted without deleting}';

    protected $description = 'Delete expired PDF/video temp files, old logs, stale DB records, and optimize tables';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $deleted = 0;
        $freed = 0;

        $this->info('🧹 Cleanup Temp Files' . ($dryRun ? ' (DRY RUN)' : ''));
        $this->newLine();

        // 1. Delete expired PDF jobs
        $this->line('--- Expired PDF Jobs ---');
        $expiredPdfJobs = PdfJob::where('expires_at', '<', now())
            ->where('status', 'done')
            ->get();

        foreach ($expiredPdfJobs as $job) {
            // Delete input files
            if (is_array($job->input_files)) {
                foreach ($job->input_files as $file) {
                    if (file_exists($file)) {
                        $freed += filesize($file);
                        if (!$dryRun) {
                            @unlink($file);
                        }
                    }
                }
            }

            // Delete output file
            if ($job->output_file && file_exists($job->output_file)) {
                $freed += filesize($job->output_file);
                if (!$dryRun) {
                    @unlink($job->output_file);
                }
            }

            // Delete job folder
            $folder = storage_path('app/private/pdf-jobs/' . $job->id);
            if (File::isDirectory($folder)) {
                if (!$dryRun) {
                    File::deleteDirectory($folder);
                }
            }

            if (!$dryRun) {
                $job->delete();
            }
            $deleted++;
        }

        $this->line("  Expired PDF jobs: {$deleted}");

        // 2. Delete orphan PDF job folders older than 2 hours
        $this->line('--- Orphan Folders ---');
        $orphanCount = 0;
        $pdfJobsDir = storage_path('app/private/pdf-jobs');

        if (File::isDirectory($pdfJobsDir)) {
            $directories = File::directories($pdfJobsDir);
            foreach ($directories as $dir) {
                $lastModified = File::lastModified($dir);
                if (now()->timestamp - $lastModified > 7200) { // 2 hours
                    if (!$dryRun) {
                        File::deleteDirectory($dir);
                    }
                    $orphanCount++;
                }
            }
        }
        $this->line("  Orphan folders removed: {$orphanCount}");
        $deleted += $orphanCount;

        // 3. Delete expired voice job files
        $this->line('--- Voice Job Cleanup ---');
        $voiceDir = storage_path('app/private/voice-jobs');
        $voiceCount = 0;
        if (File::isDirectory($voiceDir)) {
            $directories = File::directories($voiceDir);
            foreach ($directories as $dir) {
                $lastModified = File::lastModified($dir);
                if (now()->timestamp - $lastModified > 3600) { // 1 hour
                    if (!$dryRun) {
                        File::deleteDirectory($dir);
                    }
                    $voiceCount++;
                }
            }
        }
        $this->line("  Expired voice jobs: {$voiceCount}");
        $deleted += $voiceCount;

        // 4. Clear old email logs (keep 90 days)
        $this->line('--- Database Cleanup ---');
        if (class_exists(\App\Models\EmailLog::class)) {
            $emailCount = \App\Models\EmailLog::where('created_at', '<', now()->subDays(90))->count();
            if ($emailCount > 0 && !$dryRun) {
                \App\Models\EmailLog::where('created_at', '<', now()->subDays(90))->delete();
            }
            $this->line("  Old email logs: {$emailCount}");
        }

        // 5. Clear old AI usage logs (keep 90 days)
        $aiUsageCount = AiUsage::where('created_at', '<', now()->subDays(90))->count();
        if ($aiUsageCount > 0 && !$dryRun) {
            AiUsage::where('created_at', '<', now()->subDays(90))->delete();
        }
        $this->line("  Old AI usage logs: {$aiUsageCount}");

        // 6. Clear old tool usages (keep 30 days)
        if (class_exists(\App\Models\ToolUsage::class)) {
            $toolUsageCount = \App\Models\ToolUsage::where('created_at', '<', now()->subDays(30))->count();
            if ($toolUsageCount > 0 && !$dryRun) {
                \App\Models\ToolUsage::where('created_at', '<', now()->subDays(30))->delete();
            }
            $this->line("  Old tool usage records: {$toolUsageCount}");
        }

        // 7. Clear expired SEO audit reports (7 days)
        if (class_exists(\App\Models\SeoAuditReport::class)) {
            $seoCount = \App\Models\SeoAuditReport::where('created_at', '<', now()->subDays(7))->count();
            if ($seoCount > 0 && !$dryRun) {
                \App\Models\SeoAuditReport::where('created_at', '<', now()->subDays(7))->delete();
            }
            $this->line("  Expired SEO reports: {$seoCount}");
        }

        // 8. Clear expired password reset tokens
        $tokenCount = DB::table('password_reset_tokens')
            ->where('created_at', '<', now()->subHours(2))
            ->count();
        if ($tokenCount > 0 && !$dryRun) {
            DB::table('password_reset_tokens')
                ->where('created_at', '<', now()->subHours(2))
                ->delete();
        }
        $this->line("  Expired password tokens: {$tokenCount}");

        // 9. Clear old failed jobs (30 days)
        $failedCount = DB::table('failed_jobs')
            ->where('failed_at', '<', now()->subDays(30))
            ->count();
        if ($failedCount > 0 && !$dryRun) {
            DB::table('failed_jobs')
                ->where('failed_at', '<', now()->subDays(30))
                ->delete();
        }
        $this->line("  Old failed jobs: {$failedCount}");

        // Summary
        $freedMB = round($freed / 1024 / 1024, 2);
        $this->newLine();
        $this->info("✅ Cleanup complete: {$deleted} items processed, {$freedMB}MB freed" . ($dryRun ? ' (dry run)' : ''));

        // Log to file
        if (!$dryRun) {
            Log::info('Cleanup completed', [
                'deleted' => $deleted,
                'freed_mb' => $freedMB,
            ]);
        }

        return self::SUCCESS;
    }
}
