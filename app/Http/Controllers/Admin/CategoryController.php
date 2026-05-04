<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ToolCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = ToolCategory::withCount('tools')->ordered()->get();
        return Inertia::render('Admin/Categories/Index', [
            'categories' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'slug' => 'required|string|unique:tool_categories,slug|max:255',
            'icon' => 'nullable|string|max:255',
            'order' => 'integer',
            'is_active' => 'boolean',
        ]);

        ToolCategory::create($validated);

        return redirect()->back()->with('success', 'Category created successfully.');
    }

    public function update(Request $request, ToolCategory $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:tool_categories,slug,' . $category->id,
            'icon' => 'nullable|string|max:255',
            'order' => 'integer',
            'is_active' => 'boolean',
        ]);

        $category->update($validated);

        return redirect()->back()->with('success', 'Category updated successfully.');
    }

    public function destroy(ToolCategory $category)
    {
        if ($category->tools()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete category with attached tools.');
        }
        
        $category->delete();
        return redirect()->back()->with('success', 'Category deleted successfully.');
    }

    /**
     * Reorder categories via drag & drop.
     */
    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:tool_categories,id',
        ]);

        \DB::transaction(function () use ($validated) {
            foreach ($validated['ids'] as $index => $id) {
                ToolCategory::where('id', $id)->update(['order' => $index]);
            }
        });

        return redirect()->back()->with('success', 'Category order updated.');
    }
}
