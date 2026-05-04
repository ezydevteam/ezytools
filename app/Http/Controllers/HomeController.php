<?php

namespace App\Http\Controllers;

use App\Models\ToolCategory;
use App\Models\Tool;
use App\Models\AdSpace;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index()
    {
        $categories = ToolCategory::active()->ordered()->get();
        $popularTools = Tool::with('category')->active()->popular()->take(12)->get();
        $allTools = Tool::with('category')->active()->get();
        $ads = [
            'header' => AdSpace::active()->forPosition('header-banner')->visibleTo(auth()->check() ? (auth()->user()->isPro() ? 'none' : 'free') : 'guest')->first(),
            'middle' => AdSpace::active()->forPosition('homepage-middle')->visibleTo(auth()->check() ? (auth()->user()->isPro() ? 'none' : 'free') : 'guest')->first(),
        ];

        return Inertia::render('Home', [
            'categories' => $categories,
            'popularTools' => $popularTools,
            'allTools' => $allTools,
            'ads' => $ads,
        ]);
    }
}
