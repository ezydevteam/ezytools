<?php

namespace App\Http\Controllers\Admin\Ai;

use App\Http\Controllers\Controller;
use App\Models\AiSetting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AiSettingsController extends Controller
{
    public function index()
    {
        $settings = AiSetting::allAsArray();

        // Mask API keys — only show last 4 chars for display
        $keyFields = ['elevenlabs_api_key', 'openai_tts_api_key', 'google_tts_api_key'];
        foreach ($keyFields as $field) {
            if (!empty($settings[$field])) {
                $settings[$field] = str_repeat('•', 8) . substr($settings[$field], -4);
            }
        }

        return Inertia::render('Admin/Ai/Settings', [
            'settings' => $settings,
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'daily_limit_guest' => 'required|integer|min:0',
            'daily_limit_registered' => 'required|integer|min:0',
            'daily_limit_pro' => 'required|integer|min:-1',
            'max_daily_spend_usd' => 'required|numeric|min:0',
            'alert_spend_exceed_usd' => 'required|numeric|min:0',
            'auto_disable_on_budget_exceed' => 'required|boolean',
            'credit_system_enabled' => 'required|boolean',
            'free_ai_credit_limit' => 'nullable|integer|min:-1',
            'pro_ai_credit_limit' => 'nullable|integer|min:-1',
            'credit_cost_default' => 'nullable|integer|min:1',
            'elevenlabs_api_key' => 'nullable|string|max:200',
            'openai_tts_api_key' => 'nullable|string|max:200',
            'google_tts_api_key' => 'nullable|string|max:200',
        ]);

        // Separate API keys — only save if non-empty (blank = keep existing)
        $apiKeys = ['elevenlabs_api_key', 'openai_tts_api_key', 'google_tts_api_key'];

        foreach ($validated as $key => $value) {
            // Skip empty API keys to preserve existing values
            if (in_array($key, $apiKeys)) {
                if (empty($value)) {
                    continue;
                }
            }

            if (is_bool($value)) {
                $value = $value ? 'true' : 'false';
            }
            AiSetting::setValue($key, (string) $value);
        }

        return back()->with('success', 'Global AI Settings updated successfully.');
    }
}

