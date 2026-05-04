<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tool;
use App\Models\ToolCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ToolManagerController extends Controller
{
    public function index(Request $request)
    {
        $tools = Tool::with('category')->orderBy('id')->get();
        $categories = ToolCategory::ordered()->get(['id', 'name', 'slug']);

        return Inertia::render('Admin/Tools/Index', [
            'tools' => $tools,
            'categories' => $categories,
        ]);
    }

    public function create()
    {
        $categories = ToolCategory::ordered()->get();
        return Inertia::render('Admin/Tools/Create', [
            'categories' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:tool_categories,id',
            'name' => 'required|string|max:255',
            'short_description' => 'required|string|max:255',
            'slug' => 'required|string|unique:tools,slug|max:255',
            'component_name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'is_premium' => 'boolean',
            'daily_limit_free' => 'required|integer',
            'daily_limit_pro' => 'required|integer',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'order' => 'integer',
        ]);

        Tool::create($validated);

        return redirect()->route('admin.tools.index')->with('success', 'Tool created successfully.');
    }

    public function edit(Tool $tool)
    {
        $categories = ToolCategory::ordered()->get();
        return Inertia::render('Admin/Tools/Edit', [
            'tool' => $tool,
            'categories' => $categories,
        ]);
    }

    public function update(Request $request, Tool $tool)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:tool_categories,id',
            'name' => 'required|string|max:255',
            'short_description' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:tools,slug,' . $tool->id,
            'component_name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'is_premium' => 'boolean',
            'daily_limit_free' => 'required|integer',
            'daily_limit_pro' => 'required|integer',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'order' => 'integer',
        ]);

        $tool->update($validated);

        return redirect()->route('admin.tools.index')->with('success', 'Tool updated successfully.');
    }

    public function destroy(Tool $tool)
    {
        $tool->delete();
        return redirect()->route('admin.tools.index')->with('success', 'Tool deleted successfully.');
    }

    public function settings(Tool $tool)
    {
        $settings = $tool->settings;
        return Inertia::render('Admin/Tools/Settings', [
            'tool' => $tool,
            'settings' => $settings,
        ]);
    }

    public function updateSettings(Request $request, Tool $tool)
    {
        $validated = $request->validate([
            'settings' => 'required|array',
            'settings.*.key' => 'required|string',
            'settings.*.value' => 'nullable',
            'settings.*.type' => 'required|in:text,number,boolean,json',
            'settings.*.label' => 'nullable|string',
        ]);

        foreach ($validated['settings'] as $setting) {
            $value = $setting['value'];
            if ($setting['type'] === 'json' && is_array($value)) {
                $value = json_encode($value);
            } elseif ($setting['type'] === 'boolean') {
                $value = filter_var($value, FILTER_VALIDATE_BOOLEAN) ? 'true' : 'false';
            }

            $tool->settings()->updateOrCreate(
                ['key' => $setting['key']],
                [
                    'value' => $value,
                    'type' => $setting['type'],
                    'label' => $setting['label'] ?? null,
                ]
            );
        }

        return redirect()->back()->with('success', 'Tool settings updated successfully.');
    }
}
