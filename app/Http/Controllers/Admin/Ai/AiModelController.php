<?php

namespace App\Http\Controllers\Admin\Ai;

use App\Http\Controllers\Controller;
use App\Models\AiModel;
use App\Models\AiProvider;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AiModelController extends Controller
{
    public function index()
    {
        $models = AiModel::with('provider')->get();
        $providers = AiProvider::active()->get(['id', 'name', 'label']);

        return Inertia::render('Admin/Ai/Models/Index', [
            'models' => $models,
            'providers' => $providers
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'provider_id' => 'required|exists:ai_providers,id',
            'name' => 'required|string|max:100',
            'label' => 'required|string|max:150',
            'context_window' => 'required|integer|min:1',
            'cost_per_1k_input_tokens' => 'required|numeric|min:0',
            'cost_per_1k_output_tokens' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        AiModel::create($validated);

        return back()->with('success', 'Model created successfully.');
    }

    public function update(Request $request, AiModel $model)
    {
        $validated = $request->validate([
            'provider_id' => 'required|exists:ai_providers,id',
            'name' => 'required|string|max:100',
            'label' => 'required|string|max:150',
            'context_window' => 'required|integer|min:1',
            'cost_per_1k_input_tokens' => 'required|numeric|min:0',
            'cost_per_1k_output_tokens' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $model->update($validated);

        return back()->with('success', 'Model updated successfully.');
    }

    public function destroy(AiModel $model)
    {
        try {
            $model->delete();
            return back()->with('success', 'Model deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Cannot delete model because it is in use.');
        }
    }
}
