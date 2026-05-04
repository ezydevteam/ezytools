<?php

namespace Database\Seeders;

use App\Models\Tool;
use Illuminate\Database\Seeder;

class ImageConverterToolsSeeder extends Seeder
{
    public function run(): void
    {
        $tools = [
            [
                'name' => 'Favicon Maker',
                'slug' => 'favicon-maker',
                'short_description' => 'Create favicons from any image in multiple sizes',
                'description' => 'Generate professional favicons from any image. Create ICO and PNG favicons in standard sizes (16x16, 32x32, 48x48, 64x64, 128x128, 256x256). Supports custom background colors and transparent backgrounds.',
                'component_name' => 'FaviconMaker',
                'icon' => 'SparklesIcon',
                'order' => 40,
            ],
            [
                'name' => 'JPG to PNG Converter',
                'slug' => 'jpg-to-png-converter',
                'short_description' => 'Convert JPEG images to PNG format instantly',
                'description' => 'Convert your JPG/JPEG images to PNG format with full quality. PNG supports transparency and lossless compression, making it ideal for logos, icons, and graphics that need sharp edges.',
                'component_name' => 'JpgToPng',
                'icon' => 'ArrowsRightLeftIcon',
                'order' => 41,
            ],
            [
                'name' => 'JPG to WebP Converter',
                'slug' => 'jpg-to-webp-converter',
                'short_description' => 'Convert JPEG images to WebP for smaller file sizes',
                'description' => 'Convert JPG/JPEG images to modern WebP format. WebP provides 25-35% smaller file sizes compared to JPEG at the same visual quality, perfect for faster web page loading.',
                'component_name' => 'JpgToWebp',
                'icon' => 'ArrowsRightLeftIcon',
                'order' => 42,
            ],
            [
                'name' => 'PNG to SVG Converter',
                'slug' => 'png-to-svg-converter',
                'short_description' => 'Convert PNG images to scalable SVG format',
                'description' => 'Convert your PNG images to SVG vector format. Choose between embed mode (preserves exact quality) or trace mode (converts to monochrome vector paths). Perfect for logos and icons that need to scale without quality loss.',
                'component_name' => 'PngToSvg',
                'icon' => 'ArrowsRightLeftIcon',
                'order' => 43,
            ],
            [
                'name' => 'PNG to JPG Converter',
                'slug' => 'png-to-jpg-converter',
                'short_description' => 'Convert PNG images to JPG with custom background color',
                'description' => 'Convert PNG images to JPG format with adjustable quality. Customize the background color for transparent PNG areas. Great for reducing file size when transparency is not needed.',
                'component_name' => 'PngToJpg',
                'icon' => 'ArrowsRightLeftIcon',
                'order' => 44,
            ],
            [
                'name' => 'WebP to JPG/PNG Converter',
                'slug' => 'webp-converter',
                'short_description' => 'Convert WebP images to JPG or PNG format',
                'description' => 'Convert WebP images to widely compatible JPG or PNG formats. Useful when you need to share images with applications that don\'t support WebP, or when you need transparency (PNG) or universal compatibility (JPG).',
                'component_name' => 'WebpConverter',
                'icon' => 'ArrowsRightLeftIcon',
                'order' => 45,
            ],
        ];

        foreach ($tools as $toolData) {
            Tool::updateOrCreate(
                ['slug' => $toolData['slug']],
                array_merge($toolData, [
                    'category_id' => 4,
                    'is_active' => true,
                    'is_premium' => false,
                    'daily_limit_free' => 20,
                    'daily_limit_pro' => -1,
                    'usage_count' => 0,
                ])
            );
        }
    }
}
