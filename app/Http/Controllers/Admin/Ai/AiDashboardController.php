<?php

namespace App\Http\Controllers\Admin\Ai;

use App\Http\Controllers\Controller;
use App\Models\AiProvider;
use App\Models\AiUsage;
use App\Models\Tool;
use Carbon\Carbon;
use Inertia\Inertia;

class AiDashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();

        $stats = [
            'requests_today' => AiUsage::where('created_at', '>=', $today)->count(),
            'tokens_month' => AiUsage::where('created_at', '>=', $thisMonth)->sum('input_tokens') + AiUsage::where('created_at', '>=', $thisMonth)->sum('output_tokens'),
            'cost_month' => (float) AiUsage::where('created_at', '>=', $thisMonth)->sum('cost_usd'),
            'most_used_tool' => Tool::withCount('aiUsages')
                ->whereHas('category', fn($q) => $q->where('slug', 'ai-tools'))
                ->orderByDesc('ai_usages_count')
                ->first()?->name ?? 'None',
        ];

        $providers = AiProvider::withCount(['models' => function($q) {
            $q->where('is_active', true);
        }])->get()->map(function ($provider) use ($thisMonth) {
            $provider->cost_this_month = (float) $provider->usages()->where('created_at', '>=', $thisMonth)->sum('cost_usd');
            return $provider;
        });

        return Inertia::render('Admin/Ai/Dashboard', [
            'stats' => $stats,
            'providers' => $providers,
        ]);
    }
}
