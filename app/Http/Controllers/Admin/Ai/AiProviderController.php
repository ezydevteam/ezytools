<?php

namespace App\Http\Controllers\Admin\Ai;

use App\Http\Controllers\Controller;
use App\Models\AiProvider;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Http;

class AiProviderController extends Controller
{
    public function index()
    {
        $providers = AiProvider::withCount(['models' => fn($q) => $q->where('is_active', true)])->get();

        return Inertia::render('Admin/Ai/Providers/Index', [
            'providers' => $providers
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:ai_providers',
            'label' => 'required|string|max:100',
            'base_url' => 'nullable|string|url',
            'api_key' => 'nullable|string',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
        ]);

        if ($validated['is_default'] ?? false) {
            AiProvider::where('is_default', true)->update(['is_default' => false]);
        }

        AiProvider::create($validated);

        return back()->with('success', 'Provider created successfully.');
    }

    public function update(Request $request, AiProvider $provider)
    {
        $validated = $request->validate([
            'label' => 'required|string|max:100',
            'base_url' => 'nullable|string|url',
            'api_key' => 'nullable|string',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
        ]);

        if (empty($validated['api_key'])) {
            unset($validated['api_key']);
        }

        if ($validated['is_default'] ?? false) {
            AiProvider::where('id', '!=', $provider->id)->update(['is_default' => false]);
        }

        $provider->update($validated);

        return back()->with('success', 'Provider updated successfully.');
    }

    public function destroy(AiProvider $provider)
    {
        if ($provider->usages()->exists() || $provider->models()->exists()) {
            return back()->with('error', 'Cannot delete provider with existing models or usage history.');
        }

        $provider->delete();

        return back()->with('success', 'Provider deleted successfully.');
    }

    public function testConnection(AiProvider $provider)
    {
        try {
            $response = null;
            $baseUrl = rtrim($provider->base_url, '/');

            if ($provider->name === 'openai' || $provider->name === 'grok') {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $provider->api_key,
                ])->get("{$baseUrl}/models");
            } elseif ($provider->name === 'gemini') {
                $response = Http::get("{$baseUrl}/v1beta/models?key={$provider->api_key}");
            }

            if ($response && $response->successful()) {
                return back()->with('success', 'Connection successful!');
            }

            return back()->with('error', 'Connection failed. Status: ' . ($response ? $response->status() : 'Unknown'));
        } catch (\Exception $e) {
            return back()->with('error', 'Connection failed: ' . $e->getMessage());
        }
    }
}
