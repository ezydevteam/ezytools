<?php

namespace App\Http\Controllers;

use App\Models\Tool;
use App\Models\ToolCategory;
use App\Models\AdSpace;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ToolController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $toolsQuery = Tool::with('category')->active()->ordered();

        if ($search) {
            $toolsQuery->search($search);
        }

        $categories = ToolCategory::active()->ordered()->get();
        
        return Inertia::render('Tools/Index', [
            'tools' => $toolsQuery->get(),
            'categories' => $categories,
            'search' => $search,
        ]);
    }

    public function category($slug)
    {
        $category = ToolCategory::where('slug', $slug)->active()->firstOrFail();
        $tools = $category->activeTools()->with('category')->get();

        return Inertia::render('Tools/Category', [
            'category' => $category,
            'tools' => $tools,
            'meta' => app(\App\Services\MetaService::class)->forCategory($category),
        ]);
    }

    public function show($categorySlug, $slug)
    {
        $tool = Tool::with(['settings', 'category', 'aiConfig', 'faqs', 'seoContent'])->where('slug', $slug)->active()->firstOrFail();
        
        // Log basic view
        $tool->increment('usage_count');

        // Premium tool access check
        $user = auth()->user();
        $canUseTool = !$tool->is_premium || ($user && $user->isPro());

        $ads = [
            'top' => AdSpace::active()->forPosition('tool-top')->visibleTo(auth()->check() ? (auth()->user()->isPro() ? 'none' : 'free') : 'guest')->first(),
            'bottom' => AdSpace::active()->forPosition('tool-bottom')->visibleTo(auth()->check() ? (auth()->user()->isPro() ? 'none' : 'free') : 'guest')->first(),
            'sidebar' => AdSpace::active()->forPosition('tool-sidebar')->visibleTo(auth()->check() ? (auth()->user()->isPro() ? 'none' : 'free') : 'guest')->first(),
        ];

        $isFavorited = false;
        if (auth()->check()) {
            $isFavorited = auth()->user()->favoriteTools()->where('tool_id', $tool->id)->exists();
        }

        // Load related tools
        $related = $tool->relatedTools()->with('category')->get();

        $meta = app(\App\Services\MetaService::class)->forTool($tool);
        $schema = app(\App\Services\SchemaService::class);

        // Load tool-specific extra data
        $extraProps = [];
        if ($tool->slug === 'ai-voice-generator') {
            $extraProps['voices'] = \App\Models\AiVoice::where('is_active', true)
                ->orderBy('language')
                ->orderBy('name')
                ->get(['id', 'provider', 'provider_voice_id', 'name', 'language', 'gender', 'accent', 'is_active', 'is_pro_only', 'preview_url']);
        }

        return Inertia::render('Tools/Show', array_merge([
            'tool' => $tool->append(['average_rating', 'review_count']),
            'settings' => $tool->getSettingsArray(),
            'seoContent' => $tool->seoContent,
            'related' => $related,
            'ads' => $ads,
            'is_favorited' => $isFavorited,
            'can_use_tool' => $canUseTool,
            'meta' => $meta,
            'schemas' => [
                $schema->webApplication($tool),
                $schema->breadcrumb([
                    ['name' => 'Home', 'url' => url('/')],
                    ['name' => $tool->category->name, 'url' => route('tools.category', $tool->category->slug)],
                    ['name' => $tool->name, 'url' => route('tools.show', [$tool->category->slug, $tool->slug])],
                ]),
                $schema->faqPage($tool->faqs->toArray()),
            ],
        ], $extraProps));
    }
}
