<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tool;
use App\Models\ToolSeoContent;
use App\Models\ToolFaq;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ToolSeoController extends Controller
{
    public function edit(Tool $tool)
    {
        $seoContent = $tool->seoContent ?? new ToolSeoContent(['tool_id' => $tool->id]);
        $faqs = $tool->faqs;
        $related = $tool->relatedTools()->with('category')->get();
        $allTools = Tool::where('id', '!=', $tool->id)
            ->where('is_active', true)
            ->with('category')
            ->orderBy('name')
            ->get(['id', 'name', 'slug', 'icon', 'category_id']);

        return Inertia::render('Admin/Tools/Seo', [
            'tool'       => $tool->load('category'),
            'seoContent' => $seoContent,
            'faqs'       => $faqs,
            'related'    => $related,
            'allTools'   => $allTools,
        ]);
    }

    public function update(Request $request, Tool $tool)
    {
        $validated = $request->validate([
            'how_to_title'      => 'nullable|string|max:200',
            'how_to_title_en'   => 'nullable|string|max:200',
            'how_to_content'    => 'nullable|string',
            'how_to_content_en' => 'nullable|string',
            'how_to_steps'      => 'nullable|array',
            'about_title'       => 'nullable|string|max:200',
            'about_title_en'    => 'nullable|string|max:200',
            'about_content'     => 'nullable|string',
            'about_content_en'  => 'nullable|string',
            'use_cases'         => 'nullable|array',
        ]);

        $tool->seoContent()->updateOrCreate(
            ['tool_id' => $tool->id],
            [...$validated, 'last_updated_at' => now()]
        );

        return back()->with('success', 'SEO content saved successfully.');
    }

    public function storeFaq(Request $request, Tool $tool)
    {
        $validated = $request->validate([
            'question'    => 'required|string|max:500',
            'question_bn' => 'nullable|string|max:500',
            'answer'      => 'required|string',
            'answer_bn'   => 'nullable|string',
        ]);

        $maxOrder = $tool->faqs()->max('order') ?? 0;

        $tool->faqs()->create([
            ...$validated,
            'order' => $maxOrder + 1,
        ]);

        return back()->with('success', 'FAQ added successfully.');
    }

    public function updateFaq(Request $request, Tool $tool, ToolFaq $faq)
    {
        $validated = $request->validate([
            'question'    => 'required|string|max:500',
            'question_bn' => 'nullable|string|max:500',
            'answer'      => 'required|string',
            'answer_bn'   => 'nullable|string',
            'is_active'   => 'boolean',
        ]);

        $faq->update($validated);

        return back()->with('success', 'FAQ updated successfully.');
    }

    public function destroyFaq(Tool $tool, ToolFaq $faq)
    {
        $faq->delete();
        return back()->with('success', 'FAQ deleted successfully.');
    }

    public function reorderFaqs(Request $request, Tool $tool)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer|exists:tool_faqs,id',
        ]);

        foreach ($request->order as $index => $faqId) {
            ToolFaq::where('id', $faqId)->update(['order' => $index]);
        }

        return back()->with('success', 'FAQ order updated.');
    }

    public function updateRelated(Request $request, Tool $tool)
    {
        $request->validate([
            'related_tools' => 'nullable|array|max:6',
            'related_tools.*.id' => 'required|exists:tools,id',
            'related_tools.*.relation_type' => 'required|in:similar,complement,next_step',
        ]);

        // Sync related tools
        $syncData = [];
        foreach ($request->related_tools ?? [] as $index => $item) {
            $syncData[$item['id']] = [
                'relation_type' => $item['relation_type'],
                'order' => $index,
            ];
        }

        $tool->relatedTools()->sync($syncData);

        return back()->with('success', 'Related tools updated.');
    }
}
