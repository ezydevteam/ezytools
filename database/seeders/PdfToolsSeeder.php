<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PdfToolsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $category = \App\Models\ToolCategory::updateOrCreate(
            ['slug' => 'pdf-tools'],
            [
                'name' => 'PDF Tools',
                'description' => 'A complete suite of PDF utilities',
                'icon' => 'DocumentIcon',
                'order' => 2,
                'is_active' => true,
            ]
        );

        $tools = [
            [
                'name' => 'PDF Editor',
                'slug' => 'pdf-editor',
                'description' => 'Edit PDF files directly in your browser. Add text, images, annotations, and signatures.',
                'icon' => 'PencilSquareIcon',
                'is_premium' => true,
            ],
            [
                'name' => 'Merge PDF',
                'slug' => 'pdf-merge',
                'description' => 'Combine multiple PDFs into one unified document.',
                'icon' => 'DocumentDuplicateIcon',
                'is_premium' => false,
            ],
            [
                'name' => 'Split PDF',
                'slug' => 'pdf-split',
                'description' => 'Separate one page or a whole set for easy conversion into independent PDF files.',
                'icon' => 'ScissorsIcon',
                'is_premium' => false,
            ],
            [
                'name' => 'Compress PDF',
                'slug' => 'pdf-compress',
                'description' => 'Reduce file size while optimizing for maximal PDF quality.',
                'icon' => 'ArrowsPointingInIcon',
                'is_premium' => false,
            ],
            [
                'name' => 'Protect PDF',
                'slug' => 'pdf-password-protect',
                'description' => 'Encrypt your PDF with a password to prevent unauthorized access.',
                'icon' => 'LockClosedIcon',
                'is_premium' => false,
            ],
            [
                'name' => 'Unlock PDF',
                'slug' => 'pdf-remove-password',
                'description' => 'Remove PDF password security, giving you the freedom to use your PDFs as you want.',
                'icon' => 'LockOpenIcon',
                'is_premium' => false,
            ],
            [
                'name' => 'Watermark PDF',
                'slug' => 'pdf-watermark',
                'description' => 'Stamp an image or text over your PDF in seconds.',
                'icon' => 'PhotoIcon',
                'is_premium' => true, // Free adds 'ezytools.app' watermark
            ],
            [
                'name' => 'Rotate PDF',
                'slug' => 'pdf-rotate',
                'description' => 'Rotate your PDFs the way you need them.',
                'icon' => 'ArrowPathIcon',
                'is_premium' => false,
            ],
            [
                'name' => 'PDF to Word',
                'slug' => 'pdf-to-word',
                'description' => 'Convert your PDF to Word documents with incredible accuracy.',
                'icon' => 'DocumentTextIcon',
                'is_premium' => true,
            ],
            [
                'name' => 'PDF to JPG',
                'slug' => 'pdf-to-images',
                'description' => 'Extract all images that are inside a PDF or convert each page to a JPG image.',
                'icon' => 'PhotoIcon',
                'is_premium' => false,
            ],
            [
                'name' => 'JPG to PDF',
                'slug' => 'images-to-pdf',
                'description' => 'Convert JPG images to PDF in seconds. Easily adjust orientation and margins.',
                'icon' => 'DocumentIcon',
                'is_premium' => false,
            ],
            [
                'name' => 'Add Page Numbers',
                'slug' => 'pdf-page-numbers',
                'description' => 'Add page numbers into PDFs with ease. Choose your positions, dimensions, typography.',
                'icon' => 'HashtagIcon',
                'is_premium' => false,
            ],
            [
                'name' => 'Organize PDF',
                'slug' => 'pdf-reorder-pages',
                'description' => 'Sort, add and delete PDF pages.',
                'icon' => 'Bars3Icon',
                'is_premium' => false,
            ],
            [
                'name' => 'Edit Metadata',
                'slug' => 'pdf-metadata',
                'description' => 'Edit the metadata of your PDF files (Author, Title, Creator, etc.).',
                'icon' => 'CogIcon',
                'is_premium' => false,
            ],
            [
                'name' => 'Repair PDF',
                'slug' => 'pdf-repair',
                'description' => 'Repair a damaged or corrupted PDF file and recover data from it.',
                'icon' => 'WrenchScrewdriverIcon',
                'is_premium' => true,
            ],
        ];

        foreach ($tools as $index => $toolData) {
            $tool = \App\Models\Tool::updateOrCreate(
                ['slug' => $toolData['slug']],
                [
                    'category_id' => $category->id,
                    'name' => $toolData['name'],
                    'short_description' => $toolData['description'],
                    'description' => $toolData['description'],
                    'description_bn' => $toolData['description'],
                    'icon' => $toolData['icon'],
                    'order' => $index + 1,
                    'is_active' => true,
                    'is_premium' => $toolData['is_premium'],
                    'component_name' => \Illuminate\Support\Str::studly($toolData['slug']),
                ]
            );

            $settings = [
                ['key' => 'guest_max_mb', 'value' => 5, 'type' => 'number', 'label' => 'Guest Max MB'],
                ['key' => 'free_max_mb', 'value' => 10, 'type' => 'number', 'label' => 'Free Max MB'],
                ['key' => 'pro_max_mb', 'value' => 50, 'type' => 'number', 'label' => 'Pro Max MB'],
                ['key' => 'guest_max_files', 'value' => 1, 'type' => 'number', 'label' => 'Guest Max Files'],
                ['key' => 'free_max_files', 'value' => 3, 'type' => 'number', 'label' => 'Free Max Files'],
                ['key' => 'pro_max_files', 'value' => 20, 'type' => 'number', 'label' => 'Pro Max Files'],
                ['key' => 'retention_minutes', 'value' => 60, 'type' => 'number', 'label' => 'Retention (minutes)'],
                ['key' => 'enabled_bangla_fonts', 'value' => json_encode(['SutonnyMJ', 'SutonnyMJBold', 'NikoshBAN', 'NikoshBANBold', 'HindSiliguri', 'Kalpurush']), 'type' => 'json', 'label' => 'Bangla Fonts'],
                ['key' => 'add_ezytools_watermark', 'value' => !$toolData['is_premium'] ? 'true' : 'false', 'type' => 'boolean', 'label' => 'Add Watermark'],
                ['key' => 'ezytools_watermark_text', 'value' => 'ezytools.app', 'type' => 'text', 'label' => 'Watermark Text'],
            ];

            foreach ($settings as $setting) {
                $tool->settings()->updateOrCreate(
                    ['key' => $setting['key']],
                    [
                        'value' => $setting['value'],
                        'type' => $setting['type'],
                        'label' => $setting['label'],
                    ]
                );
            }
        }
    }
}
