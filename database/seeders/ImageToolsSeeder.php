<?php

namespace Database\Seeders;

use App\Models\Tool;
use Illuminate\Database\Seeder;

class ImageToolsSeeder extends Seeder
{
    public function run(): void
    {
        $tools = [
            [
                'name' => 'Image Background Remover',
                'slug' => 'image-background-remover',
                'short_description' => 'Remove background from images instantly',
                'description' => 'Remove the background from any image with one click. Uses AI-powered edge detection. Works with portraits, products, and more.',
                'component_name' => 'ImageBgRemover',
                'icon' => 'ScissorsIcon',
                'order' => 36,
            ],
            [
                'name' => 'Collage Maker',
                'slug' => 'collage-maker',
                'short_description' => 'Create beautiful photo collages',
                'description' => 'Combine multiple photos into stunning collage layouts. Choose from grid, mosaic, and freeform styles. Download in high resolution.',
                'component_name' => 'CollageMaker',
                'icon' => 'Squares2X2Icon',
                'order' => 37,
            ],
            [
                'name' => 'YouTube Cover Photo Maker',
                'slug' => 'youtube-cover-photo-maker',
                'short_description' => 'Design YouTube channel art and thumbnails',
                'description' => 'Create professional YouTube channel banners (2560x1440) and video thumbnails (1280x720). Add text, images, and customize colors.',
                'component_name' => 'YoutubeCoverMaker',
                'icon' => 'PlayIcon',
                'order' => 38,
            ],
            [
                'name' => 'Facebook Cover Photo Maker',
                'slug' => 'facebook-cover-photo-maker',
                'short_description' => 'Design Facebook cover photos and banners',
                'description' => 'Create stunning Facebook cover photos (820x312) for your profile or page. Add text, overlay images, and choose from gradient backgrounds.',
                'component_name' => 'FacebookCoverMaker',
                'icon' => 'UserGroupIcon',
                'order' => 39,
            ],
        ];

        foreach ($tools as $toolData) {
            Tool::updateOrCreate(
                ['slug' => $toolData['slug']],
                array_merge($toolData, [
                    'category_id' => 4,
                    'is_active' => true,
                    'is_premium' => false,
                    'daily_limit_free' => 10,
                    'daily_limit_pro' => -1,
                    'usage_count' => 0,
                ])
            );
        }
    }
}
