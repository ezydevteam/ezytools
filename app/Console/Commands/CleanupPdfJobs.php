<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

class CleanupPdfJobs extends Command
{
    protected $signature = 'pdf:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up expired PDF jobs and their files';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $expiredJobs = \App\Models\PdfJob::where('expires_at', '<', now())->get();
        foreach ($expiredJobs as $job) {
            \Illuminate\Support\Facades\Storage::disk('private')->deleteDirectory("pdf-jobs/{$job->id}");
            $job->delete();
        }
        $this->info("Cleaned up {$expiredJobs->count()} expired PDF jobs.");
    }
}
