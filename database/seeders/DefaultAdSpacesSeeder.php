<?php

namespace Database\Seeders;

use App\Models\AdSpace;
use Illuminate\Database\Seeder;

class DefaultAdSpacesSeeder extends Seeder
{
    public function run(): void
    {
        $positions = [
            'header-banner',
            'footer-banner',
            'tool-top',
            'tool-bottom',
            'tool-sidebar',
            'homepage-middle',
        ];

        foreach ($positions as $position) {
            AdSpace::updateOrCreate(
                ['position' => $position],
                [
                    'name' => ucwords(str_replace('-', ' ', $position)),
                    'type' => 'custom_html',
                    'code' => '<div class="bg-gray-200 dark:bg-gray-800 text-center py-4 text-gray-500 rounded">Ad Space: ' . $position . '</div>',
                    'is_active' => true,
                    'show_to' => 'free',
                ]
            );
        }
    }
}
