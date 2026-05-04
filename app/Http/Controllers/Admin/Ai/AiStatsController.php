<?php

namespace App\Http\Controllers\Admin\Ai;

use App\Http\Controllers\Controller;
use App\Models\AiUsage;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class AiStatsController extends Controller
{
    public function index(Request $request)
    {
        $days = $request->query('days', 30);
        $startDate = Carbon::today()->subDays($days - 1);

        // Daily usage chart data
        $dailyUsage = AiUsage::where('created_at', '>=', $startDate)
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as requests'),
                DB::raw('SUM(cost_usd) as cost')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Provider cost pie chart data
        $providerCosts = AiUsage::with('provider')
            ->where('created_at', '>=', $startDate)
            ->select(
                'provider_id',
                DB::raw('SUM(cost_usd) as total_cost')
            )
            ->groupBy('provider_id')
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->provider ? $item->provider->label : 'Unknown',
                    'cost' => (float) $item->total_cost,
                ];
            });

        // Recent requests
        $recentRequests = AiUsage::with(['tool', 'user', 'provider', 'model'])
            ->orderByDesc('created_at')
            ->limit(50)
            ->get();

        return Inertia::render('Admin/Ai/Stats', [
            'dailyUsage' => $dailyUsage,
            'providerCosts' => $providerCosts,
            'recentRequests' => $recentRequests,
            'days' => (int) $days,
        ]);
    }
}
