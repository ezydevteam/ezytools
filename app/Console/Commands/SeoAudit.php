<?php

namespace App\Console\Commands;

use App\Models\Tool;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class SeoAudit extends Command
{
    protected $signature = 'audit:seo';
    protected $description = 'Run comprehensive SEO audit on all active tools';

    public function handle(): int
    {
        $this->info('=== EzyTools SEO Audit ===');
        $this->newLine();

        $tools = Tool::with(['category', 'seoContent', 'faqs'])
            ->where('is_active', true)
            ->get();

        $issues = [];
        $warnings = [];
        $passed = 0;

        foreach ($tools as $tool) {
            $toolIssues = [];

            // 1. Meta title
            if (empty($tool->meta_title)) {
                $toolIssues[] = 'Missing meta_title';
            } elseif (strlen($tool->meta_title) > 60) {
                $toolIssues[] = 'meta_title too long (' . strlen($tool->meta_title) . '/60 chars)';
            } elseif (strlen($tool->meta_title) < 30) {
                $toolIssues[] = 'meta_title too short (' . strlen($tool->meta_title) . '/30 min chars)';
            }

            // 2. Meta description
            if (empty($tool->meta_description)) {
                $toolIssues[] = 'Missing meta_description';
            } elseif (strlen($tool->meta_description) > 160) {
                $toolIssues[] = 'meta_description too long (' . strlen($tool->meta_description) . '/160 chars)';
            } elseif (strlen($tool->meta_description) < 80) {
                $toolIssues[] = 'meta_description too short (' . strlen($tool->meta_description) . '/80 min chars)';
            }

            // 3. Slug format
            if (!preg_match('/^[a-z0-9-]+$/', $tool->slug)) {
                $toolIssues[] = 'Invalid slug format (use lowercase, numbers, hyphens only)';
            }

            // 4. SEO content (how-to sections)
            if (!$tool->seoContent) {
                $toolIssues[] = 'Missing SEO content (how-to section)';
            } else {
                if (empty($tool->seoContent->how_to_content)) {
                    $toolIssues[] = 'Missing how-to content (Bangla)';
                }
                if (empty($tool->seoContent->how_to_content_en)) {
                    $toolIssues[] = 'Missing how-to content (English)';
                }
                if (empty($tool->seoContent->about_content)) {
                    $toolIssues[] = 'Missing about content';
                }
            }

            // 5. FAQs (required for FAQ schema / rich results)
            if ($tool->faqs->isEmpty()) {
                $toolIssues[] = 'No FAQs (FAQ schema missing — affects rich results)';
            } elseif ($tool->faqs->count() < 3) {
                $toolIssues[] = 'Less than 3 FAQs (recommend 5+)';
            }

            // 6. Short description
            if (empty($tool->short_description)) {
                $toolIssues[] = 'Missing short_description';
            }

            // 7. Icon
            if (empty($tool->icon)) {
                $toolIssues[] = 'Missing icon';
            }

            // 8. Related tools (internal linking)
            if (method_exists($tool, 'relatedTools') && $tool->relatedTools->isEmpty()) {
                $warnings[] = "{$tool->name}: No related tools configured (affects internal linking)";
            }

            if (!empty($toolIssues)) {
                $issues[$tool->name] = $toolIssues;
            } else {
                $passed++;
            }
        }

        // Additional SEO checks
        $this->checkSitemap($tools);
        $this->checkRobotsTxt();
        $this->checkOgImages($tools);

        // Report
        $this->newLine();
        $this->info("─── SEO Audit Summary ───");
        $this->info("✅ Passed: {$passed} / {$tools->count()} tools");

        if (!empty($issues)) {
            $this->newLine();
            $this->error(count($issues) . ' tool(s) have SEO issues:');
            foreach ($issues as $toolName => $toolIssues) {
                $this->line("\n  📌 {$toolName}:");
                foreach ($toolIssues as $issue) {
                    $this->line("     ❌ {$issue}");
                }
            }
        }

        if (!empty($warnings)) {
            $this->newLine();
            $this->warn(count($warnings) . ' warning(s):');
            foreach ($warnings as $w) {
                $this->line("  ⚠️  {$w}");
            }
        }

        // Save report as JSON
        $report = [
            'generated_at' => now()->toDateTimeString(),
            'total_tools' => $tools->count(),
            'passed' => $passed,
            'failed' => count($issues),
            'issues' => $issues,
            'warnings' => $warnings,
        ];

        $reportPath = storage_path('logs/seo-audit-' . date('Y-m-d') . '.json');
        file_put_contents($reportPath, json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        $this->newLine();
        $this->info("Report saved to: {$reportPath}");

        return empty($issues) ? self::SUCCESS : self::FAILURE;
    }

    private function checkSitemap(Collection $tools): void
    {
        $this->newLine();
        $this->line('--- Sitemap Check ---');

        if (file_exists(public_path('sitemap.xml'))) {
            $this->line('✅ sitemap.xml exists');
        } else {
            $this->warn('⚠️  No static sitemap.xml found (ensure dynamic sitemap route works)');
        }

        $this->line("ℹ️  {$tools->count()} active tools should be in sitemap");
    }

    private function checkRobotsTxt(): void
    {
        $this->newLine();
        $this->line('--- Robots.txt Check ---');

        if (file_exists(public_path('robots.txt'))) {
            $content = file_get_contents(public_path('robots.txt'));
            $this->line('✅ robots.txt exists');

            if (str_contains($content, 'Disallow: /')) {
                if (!str_contains($content, 'Sitemap:')) {
                    $this->warn('⚠️  robots.txt has no Sitemap directive');
                }
            }
        } else {
            $this->warn('⚠️  robots.txt not found in public/');
        }
    }

    private function checkOgImages(Collection $tools): void
    {
        $this->newLine();
        $this->line('--- OG Image Check ---');

        $missing = 0;
        foreach ($tools as $tool) {
            $ogPath = public_path("images/tools/og/{$tool->slug}.png");
            if (!file_exists($ogPath)) {
                $missing++;
            }
        }

        if ($missing > 0) {
            $this->warn("⚠️  {$missing}/{$tools->count()} tools missing OG images (public/images/tools/og/)");
        } else {
            $this->line("✅ All tools have OG images");
        }
    }
}
