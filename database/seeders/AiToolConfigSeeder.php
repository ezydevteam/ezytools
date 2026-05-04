<?php

namespace Database\Seeders;

use App\Models\AiToolConfig;
use App\Models\Tool;
use Illuminate\Database\Seeder;

class AiToolConfigSeeder extends Seeder
{
    public function run(): void
    {
        $configs = [
            ['slug' => 'ai-content-detector', 'free' => 1000, 'pro' => 5000],
            ['slug' => 'ai-detector-humanizer', 'free' => 1000, 'pro' => 5000],
            ['slug' => 'ai-voice-generator', 'free' => 200, 'pro' => 2000],
        ];

        foreach ($configs as $c) {
            $tool = Tool::where('slug', $c['slug'])->first();
            if ($tool) {
                AiToolConfig::updateOrCreate(
                    ['tool_id' => $tool->id],
                    [
                        'max_input_length_free' => $c['free'],
                        'max_input_length_pro' => $c['pro'],
                        'max_tokens_free' => 1024,
                        'max_tokens_pro' => 4096,
                        'temperature' => 0.7,
                        'credit_cost' => 1,
                    ]
                );
                $this->command->info("✓ Config: {$c['slug']} (free: {$c['free']}, pro: {$c['pro']})");
            }
        }
    }
}
