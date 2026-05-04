<?php

namespace App\Http\Controllers\Admin\Ai;

use App\Http\Controllers\Controller;
use App\Models\AiModel;
use App\Models\AiProvider;
use App\Models\AiToolConfig;
use App\Models\Tool;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AiToolConfigController extends Controller
{
    public function edit(Tool $tool)
    {
        $tool->load('aiConfig');
        $providers = AiProvider::active()->get(['id', 'name', 'label']);
        $models = AiModel::active()->get(['id', 'provider_id', 'name', 'label']);

        return Inertia::render('Admin/Ai/ToolConfig', [
            'tool' => $tool,
            'config' => $tool->aiConfig,
            'providers' => $providers,
            'models' => $models,
            'languages' => config('ai_languages.languages'),
        ]);
    }

    public function update(Request $request, Tool $tool)
    {
        $validated = $request->validate([
            'provider_id' => 'nullable|exists:ai_providers,id',
            'model_id' => 'nullable|exists:ai_models,id',
            'pro_provider_id' => 'nullable|exists:ai_providers,id',
            'pro_model_id' => 'nullable|exists:ai_models,id',
            'fallback_provider_id' => 'nullable|exists:ai_providers,id',
            'fallback_model_id' => 'nullable|exists:ai_models,id',
            'system_prompt' => 'required|string',
            'max_tokens_free' => 'required|integer|min:1',
            'max_tokens_pro' => 'required|integer|min:1',
            'max_input_length_free' => 'required|integer|min:1',
            'max_input_length_pro' => 'required|integer|min:1',
            'temperature' => 'required|numeric|min:0|max:2',
            'supported_languages' => 'nullable|array',
            'supported_languages.*' => 'string',
            'default_language' => 'nullable|string|max:20',
            'output_format' => 'nullable|string|in:text,json,markdown,html',
            'show_language_selector' => 'boolean',
            'enable_rtl_support' => 'boolean',
        ]);

        AiToolConfig::updateOrCreate(
            ['tool_id' => $tool->id],
            $validated
        );

        return back()->with('success', 'AI Configuration updated successfully.');
    }
}
