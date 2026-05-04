<?php

namespace App\Http\Middleware;

use App\Models\ToolCategory;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => fn () => $request->user(),
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
                'two_factor_setup' => fn () => $request->session()->get('two_factor_setup'),
                'recovery_codes' => fn () => $request->session()->get('recovery_codes'),
            ],
            'ziggy' => fn () => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ],
            'navCategories' => fn () => ToolCategory::active()
                ->ordered()
                ->with(['activeTools:id,name,slug,category_id,icon'])
                ->get(['id', 'name', 'slug', 'icon']),
            'settings' => [
                'site_name' => \App\Models\SiteSetting::getValue('site_name', 'EzyTools'),
                'site_logo' => \App\Models\SiteSetting::getValue('site_logo'),
                'site_favicon' => \App\Models\SiteSetting::getValue('site_favicon'),
                'social_facebook' => \App\Models\SiteSetting::getValue('social_facebook'),
                'social_youtube' => \App\Models\SiteSetting::getValue('social_youtube'),
                'social_reddit' => \App\Models\SiteSetting::getValue('social_reddit'),
                'social_telegram' => \App\Models\SiteSetting::getValue('social_telegram'),
            ],
            'meta' => fn () => app(\App\Services\MetaService::class)->forHome(),
            'aiSpendAlert' => fn () => $this->getAiSpendAlert($request),
        ];
    }

    /**
     * Check if AI spending has exceeded the alert threshold.
     * Only computed for admin users to avoid unnecessary DB queries.
     */
    protected function getAiSpendAlert(Request $request): ?array
    {
        if (!$request->user()?->isAdmin()) {
            return null;
        }

        $alertThreshold = (float) \App\Models\AiSetting::getValue('alert_spend_exceed_usd', 3.00);
        $todaySpend = \App\Models\AiUsage::todayTotalCost();

        if ($todaySpend >= $alertThreshold) {
            $maxBudget = (float) \App\Models\AiSetting::getValue('max_daily_spend_usd', 5.00);
            return [
                'spent' => round($todaySpend, 4),
                'threshold' => $alertThreshold,
                'budget' => $maxBudget,
                'percent' => $maxBudget > 0 ? round(($todaySpend / $maxBudget) * 100, 1) : 0,
            ];
        }

        return null;
    }
}

