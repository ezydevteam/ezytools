<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Tool;
use App\Models\Subscription;
use App\Models\ToolUsage;
use App\Models\ToolCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->query('period', 'today');
        [$from, $to, $periodLabel] = $this->resolvePeriod($period);

        // Period-scoped stats
        $usageQuery = ToolUsage::query();
        $signupQuery = User::query();
        $revenueQuery = Subscription::query()->where('status', 'active');

        if ($from) {
            $usageQuery->where('used_at', '>=', $from);
            $signupQuery->where('created_at', '>=', $from);
            $revenueQuery->where('created_at', '>=', $from);
        }
        if ($to) {
            $usageQuery->where('used_at', '<=', $to);
            $signupQuery->where('created_at', '<=', $to);
            $revenueQuery->where('created_at', '<=', $to);
        }

        $stats = [
            'total_users' => User::count(),
            'new_users' => (clone $signupQuery)->count(),
            'total_tools' => Tool::count(),
            'active_tools' => Tool::where('is_active', true)->count(),
            'total_categories' => ToolCategory::where('is_active', true)->count(),
            'total_usages' => (clone $usageQuery)->count(),
            'active_subscriptions' => Subscription::active()->count(),
            'pro_users' => User::where('subscription_type', 'pro')->count(),
            'revenue' => (clone $revenueQuery)->sum('amount'),
        ];

        // Build trend data based on period
        $usageTrend = $this->buildTrend($from, $to, $period, 'usage');
        $signupTrend = $this->buildTrend($from, $to, $period, 'signup');
        $revenueTrend = $this->buildTrend($from, $to, $period, 'revenue');
        $aiCostTrend = $this->buildTrend($from, $to, $period, 'ai_cost');

        $recentSignups = User::latest()
            ->take(5)
            ->get(['id', 'name', 'email', 'avatar', 'role', 'subscription_type', 'created_at']);

        $topTools = Tool::with('category:id,name')
            ->orderByDesc('usage_count')
            ->take(10)
            ->get(['id', 'name', 'slug', 'usage_count', 'is_active', 'is_premium', 'category_id']);

        $topCountries = [];
        $visitorSources = [];

        try {
            $analyticsPeriod = \Spatie\Analytics\Period::create($from ?? Carbon::today()->subDays(27), $to ?? Carbon::now());

            $countries = \Spatie\Analytics\Facades\Analytics::fetchTopCountries($analyticsPeriod, 5);
            foreach ($countries as $country) {
                $topCountries[] = [
                    'country' => $country['country'],
                    'code' => $this->getCountryCode($country['country']),
                    'visitors' => $country['screenPageViews'] ?? 0,
                ];
            }

            $referrers = \Spatie\Analytics\Facades\Analytics::fetchTopReferrers($analyticsPeriod, 5);
            $totalReferrers = collect($referrers)->sum('screenPageViews');
            foreach ($referrers as $referrer) {
                $views = $referrer['screenPageViews'] ?? 0;
                $visitorSources[] = [
                    'source' => $referrer['pageReferrer'] ?? 'Direct',
                    'visitors' => $views,
                    'percentage' => $totalReferrers > 0 ? round(($views / $totalReferrers) * 100) : 0,
                ];
            }
        } catch (\Exception $e) {
            // Analytics not configured or error
        }

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
            'recentSignups' => $recentSignups,
            'topTools' => $topTools,
            'usageTrend' => $usageTrend,
            'signupTrend' => $signupTrend,
            'revenueTrend' => $revenueTrend,
            'aiCostTrend' => $aiCostTrend,
            'topCountries' => $topCountries,
            'visitorSources' => $visitorSources,
            'period' => $period,
            'periodLabel' => $periodLabel,
            'periodOptions' => $this->getPeriodOptions(),
        ]);
    }

    /**
     * Resolve start/end dates and label from period key.
     */
    protected function resolvePeriod(string $period): array
    {
        return match (true) {
            $period === 'today' => [Carbon::today(), Carbon::now(), 'Today'],
            $period === 'lifetime' => [null, null, 'Lifetime'],
            $period === 'last28' => [Carbon::today()->subDays(27), Carbon::now(), 'Last 28 Days'],
            $period === 'this_year' => [Carbon::now()->startOfYear(), Carbon::now(), 'This Year (' . date('Y') . ')'],
            str_starts_with($period, 'month_') => $this->resolveMonth($period),
            default => [Carbon::today(), Carbon::now(), 'Today'],
        };
    }

    /**
     * Resolve a month period like "month_2026_01".
     */
    protected function resolveMonth(string $period): array
    {
        $parts = explode('_', $period);
        if (count($parts) !== 3) {
            return [Carbon::today(), Carbon::now(), 'Today'];
        }

        $date = Carbon::createFromDate((int) $parts[1], (int) $parts[2], 1);
        return [
            $date->copy()->startOfMonth(),
            $date->copy()->endOfMonth(),
            $date->format('F Y'),
        ];
    }

    /**
     * Build period options for the frontend select.
     */
    protected function getPeriodOptions(): array
    {
        $options = [
            ['value' => 'today', 'label' => 'Today'],
            ['value' => 'lifetime', 'label' => 'Lifetime'],
            ['value' => 'last28', 'label' => 'Last 28 Days'],
            ['value' => 'this_year', 'label' => 'This Year (' . date('Y') . ')'],
        ];

        // Add each month from the first user signup to now
        $firstUser = User::orderBy('created_at')->first();
        $startDate = $firstUser ? Carbon::parse($firstUser->created_at)->startOfMonth() : Carbon::now()->startOfMonth();
        $current = Carbon::now()->startOfMonth();

        // Collect months
        $months = [];
        $cursor = $current->copy();
        while ($cursor->gte($startDate)) {
            $months[] = [
                'value' => 'month_' . $cursor->format('Y_m'),
                'label' => $cursor->format('F Y'),
            ];
            $cursor->subMonth();
        }

        return array_merge($options, $months);
    }

    /**
     * Build trend data (daily or monthly or hourly buckets depending on range).
     */
    protected function buildTrend(?Carbon $from, ?Carbon $to, string $period, string $type): array
    {
        $trend = [];

        if ($period === 'today') {
            // Hourly buckets for today
            $start = Carbon::today();
            $end = Carbon::now();
            $cursor = $start->copy();

            while ($cursor->lte($end)) {
                $hourEnd = $cursor->copy()->endOfHour();

                if ($type === 'usage') {
                    $val = ToolUsage::whereBetween('used_at', [$cursor, $hourEnd])->count();
                } elseif ($type === 'signup') {
                    $val = User::whereBetween('created_at', [$cursor, $hourEnd])->count();
                } elseif ($type === 'revenue') {
                    $val = Subscription::whereBetween('created_at', [$cursor, $hourEnd])->where('status', 'active')->sum('amount');
                } else {
                    $val = \App\Models\AiUsage::whereBetween('created_at', [$cursor, $hourEnd])->sum('cost_usd');
                }

                $trend[] = [
                    'date' => $cursor->format('H:i'),
                    'label' => $cursor->format('H:i'),
                    'count' => is_numeric($val) ? round($val, 2) : 0,
                ];
                $cursor->addHour();
            }
        } elseif ($period === 'lifetime' || $period === 'this_year') {
            // Monthly buckets
            $start = $from ?? Carbon::now()->subMonths(11)->startOfMonth();
            $end = $to ?? Carbon::now();
            $cursor = $start->copy()->startOfMonth();

            while ($cursor->lte($end)) {
                $monthStart = $cursor->copy()->startOfMonth();
                $monthEnd = $cursor->copy()->endOfMonth();

                if ($type === 'usage') {
                    $val = ToolUsage::whereBetween('used_at', [$monthStart, $monthEnd])->count();
                } elseif ($type === 'signup') {
                    $val = User::whereBetween('created_at', [$monthStart, $monthEnd])->count();
                } elseif ($type === 'revenue') {
                    $val = Subscription::whereBetween('created_at', [$monthStart, $monthEnd])->where('status', 'active')->sum('amount');
                } else {
                    $val = \App\Models\AiUsage::whereBetween('created_at', [$monthStart, $monthEnd])->sum('cost_usd');
                }

                $trend[] = [
                    'date' => $cursor->format('M Y'),
                    'label' => $cursor->format('M'),
                    'count' => is_numeric($val) ? round($val, 2) : 0,
                ];
                $cursor->addMonth();
            }
        } else {
            // Daily buckets
            $start = $from ?? Carbon::today()->subDays(27);
            $end = $to ?? Carbon::now();
            $days = $start->diffInDays($end);

            $step = max(1, intdiv($days, 28));
            $cursor = $start->copy();

            while ($cursor->lte($end)) {
                $dayEnd = $cursor->copy()->addDays($step - 1)->endOfDay();
                if ($dayEnd->gt($end)) $dayEnd = $end->copy();

                if ($type === 'usage') {
                    $val = ToolUsage::whereBetween('used_at', [$cursor->copy()->startOfDay(), $dayEnd])->count();
                } elseif ($type === 'signup') {
                    $val = User::whereBetween('created_at', [$cursor->copy()->startOfDay(), $dayEnd])->count();
                } elseif ($type === 'revenue') {
                    $val = Subscription::whereBetween('created_at', [$cursor->copy()->startOfDay(), $dayEnd])->where('status', 'active')->sum('amount');
                } else {
                    $val = \App\Models\AiUsage::whereBetween('created_at', [$cursor->copy()->startOfDay(), $dayEnd])->sum('cost_usd');
                }

                $trend[] = [
                    'date' => $cursor->format('M d'),
                    'label' => $cursor->format('d'),
                    'count' => is_numeric($val) ? round($val, 2) : 0,
                ];

                $cursor->addDays($step);
            }
        }

        return $trend;
    }

    protected function getCountryCode(string $countryName): string
    {
        $map = [
            'United States' => 'us', 'India' => 'in', 'Bangladesh' => 'bd', 'United Kingdom' => 'gb', 'Canada' => 'ca',
            'Germany' => 'de', 'France' => 'fr', 'Australia' => 'au', 'Brazil' => 'br', 'Japan' => 'jp',
            'China' => 'cn', 'Russia' => 'ru', 'Italy' => 'it', 'Spain' => 'es', 'Mexico' => 'mx',
            'Indonesia' => 'id', 'Pakistan' => 'pk', 'Nigeria' => 'ng', 'Egypt' => 'eg', 'Turkey' => 'tr',
            'Vietnam' => 'vn', 'Philippines' => 'ph', 'Thailand' => 'th', 'South Africa' => 'za', 'South Korea' => 'kr',
        ];
        return $map[$countryName] ?? 'un';
    }
}
