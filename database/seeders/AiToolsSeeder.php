<?php

namespace Database\Seeders;

use App\Models\AiVoice;
use App\Models\Tool;
use App\Models\ToolCategory;
use Illuminate\Database\Seeder;

class AiToolsSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedVoices();
        $this->seedTools();
    }

    protected function seedVoices(): void
    {
        $voices = [
            // ElevenLabs
            ['provider' => 'elevenlabs', 'provider_voice_id' => 'pNInz6obpgDQGcFmaJgB', 'name' => 'Bangla Female — Priya', 'language' => 'bangla', 'gender' => 'female', 'accent' => 'Standard', 'is_pro_only' => false],
            ['provider' => 'elevenlabs', 'provider_voice_id' => 'VR6AewLTigWG4xSOukaG', 'name' => 'Bangla Male — Arman', 'language' => 'bangla', 'gender' => 'male', 'accent' => 'Standard', 'is_pro_only' => false],
            ['provider' => 'elevenlabs', 'provider_voice_id' => 'EXAVITQu4vr4xnSDxMaL', 'name' => 'Bangla Female — Riya (Pro)', 'language' => 'bangla', 'gender' => 'female', 'accent' => 'Premium', 'is_pro_only' => true],
            ['provider' => 'elevenlabs', 'provider_voice_id' => '21m00Tcm4TlvDq8ikWAM', 'name' => 'English Female — Rachel', 'language' => 'english', 'gender' => 'female', 'accent' => 'American', 'is_pro_only' => false],
            ['provider' => 'elevenlabs', 'provider_voice_id' => 'pNInz6obpgDQGcFmaJgA', 'name' => 'English Male — Adam', 'language' => 'english', 'gender' => 'male', 'accent' => 'American', 'is_pro_only' => false],

            // OpenAI TTS
            ['provider' => 'openai', 'provider_voice_id' => 'nova', 'name' => 'English — Nova (Female)', 'language' => 'english', 'gender' => 'female', 'accent' => 'Neutral', 'is_pro_only' => false],
            ['provider' => 'openai', 'provider_voice_id' => 'onyx', 'name' => 'English — Onyx (Male)', 'language' => 'english', 'gender' => 'male', 'accent' => 'Deep', 'is_pro_only' => false],
            ['provider' => 'openai', 'provider_voice_id' => 'shimmer', 'name' => 'English — Shimmer', 'language' => 'english', 'gender' => 'female', 'accent' => 'Warm', 'is_pro_only' => true],

            // Google TTS
            ['provider' => 'google', 'provider_voice_id' => 'bn-BD-Standard-A', 'name' => 'Bangla BD — Female', 'language' => 'bangla', 'gender' => 'female', 'accent' => 'Bangladesh', 'is_pro_only' => false],
            ['provider' => 'google', 'provider_voice_id' => 'bn-BD-Standard-B', 'name' => 'Bangla BD — Male', 'language' => 'bangla', 'gender' => 'male', 'accent' => 'Bangladesh', 'is_pro_only' => false],
            ['provider' => 'google', 'provider_voice_id' => 'hi-IN-Standard-A', 'name' => 'Hindi — Female', 'language' => 'hindi', 'gender' => 'female', 'accent' => 'Standard', 'is_pro_only' => false],
            ['provider' => 'google', 'provider_voice_id' => 'ar-XA-Standard-B', 'name' => 'Arabic — Male', 'language' => 'arabic', 'gender' => 'male', 'accent' => 'Standard', 'is_pro_only' => false],
            ['provider' => 'google', 'provider_voice_id' => 'ur-PK-Standard-A', 'name' => 'Urdu — Female', 'language' => 'urdu', 'gender' => 'female', 'accent' => 'Standard', 'is_pro_only' => false],
        ];

        foreach ($voices as $voice) {
            AiVoice::updateOrCreate(
                ['provider' => $voice['provider'], 'provider_voice_id' => $voice['provider_voice_id']],
                array_merge($voice, ['is_active' => true])
            );
        }

        $this->command->info('✓ Seeded ' . count($voices) . ' AI voices');
    }

    protected function seedTools(): void
    {
        // Find or create AI Tools category
        $category = ToolCategory::firstOrCreate(
            ['slug' => 'ai-tools'],
            [
                'name' => 'AI Tools',
                'description' => 'Powerful AI-powered tools for content creation and analysis.',
                'icon' => 'SparklesIcon',
                'is_active' => true,
                'order' => 1,
            ]
        );

        // Get existing max order
        $maxOrder = Tool::where('category_id', $category->id)->max('order') ?? 0;

        $tools = [
            [
                'name' => 'AI Content Detector',
                'slug' => 'ai-content-detector',
                'short_description' => 'Detect whether text is written by AI or a human using advanced analysis.',
                'component_name' => 'AiContentDetector',
                'icon' => 'MagnifyingGlassIcon',
                'is_premium' => false,
                'is_active' => true,
                'daily_limit_free' => 10,
                'daily_limit_pro' => -1,
            ],
            [
                'name' => 'AI Detector & Humanizer',
                'slug' => 'ai-detector-humanizer',
                'short_description' => 'Detect AI content and humanize it to sound natural — all in one tool.',
                'component_name' => 'AiDetectorHumanizer',
                'icon' => 'SparklesIcon',
                'is_premium' => false,
                'is_active' => true,
                'daily_limit_free' => 5,
                'daily_limit_pro' => -1,
            ],
            [
                'name' => 'AI Voice Generator',
                'slug' => 'ai-voice-generator',
                'short_description' => 'Convert text to natural-sounding speech in Bangla, English, Hindi, Arabic & Urdu.',
                'component_name' => 'AiVoiceGenerator',
                'icon' => 'SpeakerWaveIcon',
                'is_premium' => false,
                'is_active' => true,
                'daily_limit_free' => 3,
                'daily_limit_pro' => -1,
            ],
            [
                'name' => 'AI SEO Auditor',
                'slug' => 'ai-seo-auditor',
                'short_description' => 'Run a full SEO audit of any URL and get AI recommendations.',
                'component_name' => 'AiSeoAuditor',
                'icon' => 'MagnifyingGlassCircleIcon',
                'is_premium' => false,
                'is_active' => true,
                'daily_limit_free' => 5,
                'daily_limit_pro' => -1,
            ],
        ];

        foreach ($tools as $i => $toolData) {
            Tool::updateOrCreate(
                ['slug' => $toolData['slug']],
                array_merge($toolData, [
                    'category_id' => $category->id,
                    'order' => $maxOrder + $i + 1,
                    'usage_count' => rand(500, 5000),
                ])
            );

            $this->command->info("✓ Seeded tool: {$toolData['name']}");
        }
    }
}
