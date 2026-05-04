<?php

namespace Database\Seeders;

use App\Models\ToolCategory;
use Illuminate\Database\Seeder;

class ToolCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Text Tools',
                'description' => 'Various tools for text formatting and counting',
                'slug' => 'text-tools',
                'icon' => 'DocumentTextIcon',
                'order' => 1,
            ],
            [
                'name' => 'Calculators',
                'description' => 'A collection of useful calculators',
                'slug' => 'calculators',
                'icon' => 'CalculatorIcon',
                'order' => 2,
            ],
            [
                'name' => 'Date & Time',
                'description' => 'Tools for date and time calculations',
                'slug' => 'date-time',
                'icon' => 'CalendarIcon',
                'order' => 3,
            ],
            [
                'name' => 'File Tools',
                'description' => 'Utilities for file compression and conversion',
                'slug' => 'file-tools',
                'icon' => 'FolderIcon',
                'order' => 4,
            ],
            [
                'name' => 'Business Tools',
                'description' => 'Tools to generate invoices and receipts',
                'slug' => 'business-tools',
                'icon' => 'BriefcaseIcon',
                'order' => 5,
            ],
            [
                'name' => 'Developer Tools',
                'description' => 'Handy tools for formatting and encoding',
                'slug' => 'developer-tools',
                'icon' => 'CodeBracketIcon',
                'order' => 6,
            ],
            [
                'name' => 'Web Tools',
                'description' => 'Useful web and SEO utilities',
                'slug' => 'web-tools',
                'icon' => 'GlobeAltIcon',
                'order' => 7,
            ],
            [
                'name' => 'Unit Converters',
                'description' => 'Convert between different units of measurement',
                'slug' => 'unit-converters',
                'icon' => 'ArrowsRightLeftIcon',
                'order' => 8,
            ],
        ];

        foreach ($categories as $category) {
            ToolCategory::updateOrCreate(['slug' => $category['slug']], $category);
        }
    }
}
