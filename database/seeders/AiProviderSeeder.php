<?php

namespace Database\Seeders;

use App\Models\AiModel;
use App\Models\AiProvider;
use App\Models\AiSetting;
use Illuminate\Database\Seeder;

class AiProviderSeeder extends Seeder
{
    public function run(): void
    {
        // Global AI Settings
        $settings = [
            'daily_limit_guest' => '3',
            'daily_limit_registered' => '10',
            'daily_limit_pro' => '-1',
            'max_daily_spend_usd' => '5.00',
            'auto_disable_on_budget_exceed' => 'true',
            'alert_spend_exceed_usd' => '3.00',
            // Credit System
            'credit_system_enabled' => 'true',
            'free_ai_credit_limit' => '100',
            'pro_ai_credit_limit' => '1000',
            'credit_cost_default' => '1',
        ];

        foreach ($settings as $key => $value) {
            AiSetting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        // Providers
        $openAi = AiProvider::updateOrCreate(
            ['name' => 'openai'],
            [
                'label' => 'OpenAI',
                'base_url' => 'https://api.openai.com/v1',
                'is_active' => true,
                'is_default' => true,
            ]
        );

        $gemini = AiProvider::updateOrCreate(
            ['name' => 'gemini'],
            [
                'label' => 'Google Gemini',
                'base_url' => 'https://generativelanguage.googleapis.com',
                'is_active' => true,
                'is_default' => false,
            ]
        );

        $grok = AiProvider::updateOrCreate(
            ['name' => 'grok'],
            [
                'label' => 'Grok (xAI)',
                'base_url' => 'https://api.x.ai/v1',
                'is_active' => true,
                'is_default' => false,
            ]
        );

        // Models
        // OpenAI Models
        AiModel::updateOrCreate(
            ['provider_id' => $openAi->id, 'name' => 'gpt-4o-mini'],
            [
                'label' => 'GPT-4o Mini',
                'context_window' => 128000,
                'cost_per_1k_input_tokens' => 0.00015,
                'cost_per_1k_output_tokens' => 0.00060,
                'is_active' => true,
            ]
        );

        AiModel::updateOrCreate(
            ['provider_id' => $openAi->id, 'name' => 'gpt-4o'],
            [
                'label' => 'GPT-4o',
                'context_window' => 128000,
                'cost_per_1k_input_tokens' => 0.0025,
                'cost_per_1k_output_tokens' => 0.0100,
                'is_active' => true,
            ]
        );

        AiModel::updateOrCreate(
            ['provider_id' => $openAi->id, 'name' => 'gpt-4.5-preview'],
            [
                'label' => 'GPT-4.5 Preview',
                'context_window' => 128000,
                'cost_per_1k_input_tokens' => 0.0050,
                'cost_per_1k_output_tokens' => 0.0150,
                'is_active' => true,
            ]
        );

        AiModel::updateOrCreate(
            ['provider_id' => $openAi->id, 'name' => 'gpt-5'],
            [
                'label' => 'GPT-5',
                'context_window' => 256000,
                'cost_per_1k_input_tokens' => 0.0100,
                'cost_per_1k_output_tokens' => 0.0300,
                'is_active' => true,
            ]
        );

        AiModel::updateOrCreate(
            ['provider_id' => $openAi->id, 'name' => 'o1-mini'],
            [
                'label' => 'o1 Mini',
                'context_window' => 128000,
                'cost_per_1k_input_tokens' => 0.0030,
                'cost_per_1k_output_tokens' => 0.0120,
                'is_active' => true,
            ]
        );

        AiModel::updateOrCreate(
            ['provider_id' => $openAi->id, 'name' => 'o3-mini'],
            [
                'label' => 'o3 Mini',
                'context_window' => 200000,
                'cost_per_1k_input_tokens' => 0.0011,
                'cost_per_1k_output_tokens' => 0.0044,
                'is_active' => true,
            ]
        );

        // Gemini Models
        AiModel::updateOrCreate(
            ['provider_id' => $gemini->id, 'name' => 'gemini-2.5-flash'],
            [
                'label' => 'Gemini 2.5 Flash',
                'context_window' => 1048576,
                'cost_per_1k_input_tokens' => 0.00010,
                'cost_per_1k_output_tokens' => 0.00040,
                'is_active' => true,
            ]
        );

        AiModel::updateOrCreate(
            ['provider_id' => $gemini->id, 'name' => 'gemini-2.5-pro'],
            [
                'label' => 'Gemini 2.5 Pro',
                'context_window' => 2097152,
                'cost_per_1k_input_tokens' => 0.00125,
                'cost_per_1k_output_tokens' => 0.00500,
                'is_active' => true,
            ]
        );



        AiModel::updateOrCreate(
            ['provider_id' => $gemini->id, 'name' => 'gemini-1.5-pro'],
            [
                'label' => 'Gemini 1.5 Pro',
                'context_window' => 2097152,
                'cost_per_1k_input_tokens' => 0.00125,
                'cost_per_1k_output_tokens' => 0.00500,
                'is_active' => true,
            ]
        );

        // Grok Models
        AiModel::updateOrCreate(
            ['provider_id' => $grok->id, 'name' => 'grok-2-mini'],
            [
                'label' => 'Grok 2 Mini',
                'context_window' => 128000,
                'cost_per_1k_input_tokens' => 0.00030,
                'cost_per_1k_output_tokens' => 0.00050,
                'is_active' => true,
            ]
        );

        AiModel::updateOrCreate(
            ['provider_id' => $grok->id, 'name' => 'grok-2'],
            [
                'label' => 'Grok 2',
                'context_window' => 128000,
                'cost_per_1k_input_tokens' => 0.00300,
                'cost_per_1k_output_tokens' => 0.01500,
                'is_active' => true,
            ]
        );
        AiModel::updateOrCreate(
            ['provider_id' => $grok->id, 'name' => 'grok-3-mini'],
            [
                'label' => 'Grok 3 Mini',
                'context_window' => 128000,
                'cost_per_1k_input_tokens' => 0.00030,
                'cost_per_1k_output_tokens' => 0.00050,
                'is_active' => true,
            ]
        );

        AiModel::updateOrCreate(
            ['provider_id' => $grok->id, 'name' => 'grok-3'],
            [
                'label' => 'Grok 3',
                'context_window' => 128000,
                'cost_per_1k_input_tokens' => 0.00300,
                'cost_per_1k_output_tokens' => 0.01500,
                'is_active' => true,
            ]
        );
    }
}
