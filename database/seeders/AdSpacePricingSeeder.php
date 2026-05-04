<?php

namespace Database\Seeders;

use App\Models\AdSpace;
use Illuminate\Database\Seeder;

class AdSpacePricingSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'position'        => 'header-banner',
                'description'     => 'Premium banner displayed at the top of every page. Maximum visibility across the entire platform.',
                'dimensions'      => '728×90 / Responsive',
                'est_impressions' => '50K-100K/day',
                'price_3d'        => 15.00,
                'price_7d'        => 30.00,
                'price_30d'       => 99.00,
            ],
            [
                'position'        => 'footer-banner',
                'description'     => 'Banner displayed in the footer section on every page. Consistent exposure with lower cost.',
                'dimensions'      => '728×90 / Responsive',
                'est_impressions' => '30K-80K/day',
                'price_3d'        => 8.00,
                'price_7d'        => 18.00,
                'price_30d'       => 59.00,
            ],
            [
                'position'        => 'tool-top',
                'description'     => 'Displayed above the tool interface on every tool page. High engagement position.',
                'dimensions'      => '728×90 / 320×100',
                'est_impressions' => '20K-60K/day',
                'price_3d'        => 12.00,
                'price_7d'        => 25.00,
                'price_30d'       => 79.00,
            ],
            [
                'position'        => 'tool-bottom',
                'description'     => 'Shown below the tool output area. Users see it after completing a task.',
                'dimensions'      => '728×90 / 320×100',
                'est_impressions' => '15K-40K/day',
                'price_3d'        => 8.00,
                'price_7d'        => 18.00,
                'price_30d'       => 59.00,
            ],
            [
                'position'        => 'tool-sidebar',
                'description'     => 'Sidebar ad on tool pages. Always visible while users interact with tools.',
                'dimensions'      => '300×250 / 300×600',
                'est_impressions' => '15K-40K/day',
                'price_3d'        => 10.00,
                'price_7d'        => 22.00,
                'price_30d'       => 69.00,
            ],
            [
                'position'        => 'homepage-middle',
                'description'     => 'Displayed in the middle of the homepage between tool category sections.',
                'dimensions'      => '728×90 / Responsive',
                'est_impressions' => '20K-50K/day',
                'price_3d'        => 12.00,
                'price_7d'        => 25.00,
                'price_30d'       => 79.00,
            ],
        ];

        foreach ($data as $item) {
            AdSpace::where('position', $item['position'])->update($item);
        }

        // Create new ad spaces
        $newSpaces = [
            [
                'name'            => 'Blog Top',
                'position'        => 'blog-top',
                'description'     => 'Banner at the top of blog article pages. Targets readers and content consumers.',
                'dimensions'      => '728×90 / Responsive',
                'est_impressions' => '5K-15K/day',
                'price_3d'        => 5.00,
                'price_7d'        => 12.00,
                'price_30d'       => 39.00,
                'type'            => 'custom_html',
                'is_active'       => true,
                'is_available'    => true,
                'show_to'         => 'all',
            ],
            [
                'name'            => 'Blog Sidebar',
                'position'        => 'blog-sidebar',
                'description'     => 'Sidebar ad on blog pages. Sticky visibility while reading articles.',
                'dimensions'      => '300×250',
                'est_impressions' => '5K-15K/day',
                'price_3d'        => 5.00,
                'price_7d'        => 12.00,
                'price_30d'       => 39.00,
                'type'            => 'custom_html',
                'is_active'       => true,
                'is_available'    => true,
                'show_to'         => 'all',
            ],
            [
                'name'            => 'Tool Inline',
                'position'        => 'tool-inline',
                'description'     => 'Native inline ad within tool content area. Blends naturally with the UI for high engagement.',
                'dimensions'      => '468×60 / Responsive',
                'est_impressions' => '10K-30K/day',
                'price_3d'        => 10.00,
                'price_7d'        => 22.00,
                'price_30d'       => 69.00,
                'type'            => 'custom_html',
                'is_active'       => true,
                'is_available'    => true,
                'show_to'         => 'all',
            ],
            [
                'name'            => 'Category Top',
                'position'        => 'category-top',
                'description'     => 'Banner at the top of category listing pages. Visible when users browse tool categories.',
                'dimensions'      => '728×90 / Responsive',
                'est_impressions' => '8K-20K/day',
                'price_3d'        => 7.00,
                'price_7d'        => 15.00,
                'price_30d'       => 49.00,
                'type'            => 'custom_html',
                'is_active'       => true,
                'is_available'    => true,
                'show_to'         => 'all',
            ],
        ];

        foreach ($newSpaces as $space) {
            AdSpace::firstOrCreate(
                ['position' => $space['position']],
                $space
            );
        }
    }
}
