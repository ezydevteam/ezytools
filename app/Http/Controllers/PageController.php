<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class PageController extends Controller
{
    public function about()
    {
        return Inertia::render('Pages/About');
    }

    public function privacy()
    {
        return Inertia::render('Pages/PrivacyPolicy');
    }

    public function terms()
    {
        return Inertia::render('Pages/TermsOfService');
    }

    public function contact()
    {
        return Inertia::render('Pages/Contact');
    }

    public function faq(\Illuminate\Http\Request $request)
    {
        $country = $this->resolveCountry($request->ip());

        switch ($country) {
            case 'BD':
                $symbol = '৳';
                $monthly = (int) \App\Models\SiteSetting::getValue('pro_price_monthly', 299);
                break;
            case 'IN':
                $symbol = '₹';
                $monthly = (int) \App\Models\SiteSetting::getValue('pro_price_monthly_inr', 249);
                break;
            default:
                $symbol = '$';
                $monthly = (int) \App\Models\SiteSetting::getValue('pro_price_monthly_usd', 5);
                break;
        }

        return Inertia::render('Pages/Faq', [
            'guestLimit' => \App\Models\AiSetting::getValue('daily_limit_guest', '3'),
            'registeredLimit' => \App\Models\AiSetting::getValue('daily_limit_registered', '10'),
            'proPrice' => $monthly,
            'proCurrencySymbol' => $symbol,
            'proAiCreditLimit' => \App\Models\AiSetting::getValue('pro_ai_credit_limit', '1000'),
        ]);
    }

    protected function resolveCountry(string $ip): string
    {
        if (in_array($ip, ['127.0.0.1', '::1']) || str_starts_with($ip, '192.168.') || str_starts_with($ip, '10.')) {
            return 'BD';
        }
        try {
            $response = \Illuminate\Support\Facades\Http::timeout(2)->get("http://ip-api.com/json/{$ip}?fields=countryCode");
            if ($response->ok() && $response->json('countryCode')) {
                return $response->json('countryCode');
            }
        } catch (\Exception $e) {}
        return 'US';
    }

    public function gdpr()
    {
        return Inertia::render('Pages/Gdpr');
    }

    public function doNotSell()
    {
        return Inertia::render('Pages/DoNotSell');
    }

    public function sitemap()
    {
        $categories = \App\Models\ToolCategory::active()->ordered()->with('activeTools')->get()
            ->map(function ($cat) {
                $cat->setRelation('tools', $cat->activeTools);
                return $cat;
            });

        return Inertia::render('Pages/Sitemap', [
            'categories' => $categories,
        ]);
    }
}
