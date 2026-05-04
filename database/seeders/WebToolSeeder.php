<?php

namespace Database\Seeders;

use App\Models\Tool;
use App\Models\ToolCategory;
use Illuminate\Database\Seeder;

class WebToolSeeder extends Seeder
{
    public function run(): void
    {
        $category = ToolCategory::where('slug', 'web-tools')->first();

        if (!$category) {
            $this->command->error('Web Tools category not found!');
            return;
        }

        $maxOrder = Tool::where('category_id', $category->id)->max('order') ?? 50;

        $tools = [
            [
                'name' => 'URL Shortener',
                'short_description' => 'Shorten long URLs instantly',
                'slug' => 'url-shortener',
                'component_name' => 'UrlShortener',
                'icon' => 'LinkIcon',
            ],
            [
                'name' => 'DNS Lookup',
                'short_description' => 'Look up DNS records for any domain',
                'slug' => 'dns-lookup',
                'component_name' => 'DnsLookup',
                'icon' => 'ServerIcon',
            ],
            [
                'name' => 'IP Lookup',
                'short_description' => 'Find IP address geolocation & details',
                'slug' => 'ip-lookup',
                'component_name' => 'IpLookup',
                'icon' => 'GlobeAltIcon',
            ],
            [
                'name' => 'WHOIS Lookup',
                'short_description' => 'Check domain registration & ownership',
                'slug' => 'whois-lookup',
                'component_name' => 'WhoisLookup',
                'icon' => 'MagnifyingGlassCircleIcon',
            ],
            [
                'name' => 'Google Cache Checker',
                'short_description' => 'Check if a page is cached by Google',
                'slug' => 'google-cache-checker',
                'component_name' => 'GoogleCacheChecker',
                'icon' => 'ArchiveBoxIcon',
            ],
            [
                'name' => 'Meta Tags Checker',
                'short_description' => 'Analyze SEO meta tags of any website',
                'slug' => 'meta-tags-checker',
                'component_name' => 'MetaTagsChecker',
                'icon' => 'TagIcon',
            ],
            [
                'name' => 'Ping Tool',
                'short_description' => 'Test server response time & connectivity',
                'slug' => 'ping-tool',
                'component_name' => 'PingTool',
                'icon' => 'SignalIcon',
            ],
            [
                'name' => 'Hosting Checker',
                'short_description' => 'Detect the hosting provider of any website',
                'slug' => 'hosting-checker',
                'component_name' => 'HostingChecker',
                'icon' => 'ServerStackIcon',
            ],
        ];

        foreach ($tools as $i => $toolData) {
            $existing = Tool::where('slug', $toolData['slug'])->first();
            if ($existing) {
                $this->command->warn("⏭ Skipped: {$toolData['name']} (already exists)");
                continue;
            }

            Tool::create([
                'category_id' => $category->id,
                'name' => $toolData['name'],
                'short_description' => $toolData['short_description'],
                'slug' => $toolData['slug'],
                'component_name' => $toolData['component_name'],
                'icon' => $toolData['icon'],
                'is_active' => true,
                'is_premium' => false,
                'daily_limit_free' => 20,
                'daily_limit_pro' => -1,
                'order' => $maxOrder + $i + 1,
            ]);

            $this->command->info("✓ Created: {$toolData['name']}");
        }
    }
}
