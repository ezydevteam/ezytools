<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);

        RateLimiter::for('ai', function (Request $request) {
            $user = $request->user();

            if ($user?->isPro()) {
                return Limit::none();
            }

            if ($user) {
                return Limit::perDay((int) \App\Models\AiSetting::getValue('daily_limit_registered', 10))
                    ->by($user->id)
                    ->response(fn() => response()->json([
                        'error' => 'daily_limit_exceeded',
                        'message' => 'Your daily limit has been reached. Upgrade to Pro to use unlimited AI.',
                    ], 429));
            }

            return Limit::perDay((int) \App\Models\AiSetting::getValue('daily_limit_guest', 3))
                ->by($request->ip())
                ->response(fn() => response()->json([
                    'error' => 'daily_limit_exceeded',
                    'message' => 'Your daily limit has been reached. Upgrade to Pro to use unlimited AI.',
                ], 429));
        });

        // Dynamic Google Config from Site Settings
        if (!app()->runningInConsole()) {
            $googleId = \App\Models\SiteSetting::getValue('google_client_id');
            $googleSecret = \App\Models\SiteSetting::getValue('google_client_secret');

            if ($googleId) config(['services.google.client_id' => $googleId]);
            if ($googleSecret) config(['services.google.client_secret' => $googleSecret]);

            // Analytics Config
            $propertyId = \App\Models\SiteSetting::getValue('analytics_property_id');
            if ($propertyId) {
                config(['analytics.property_id' => $propertyId]);
            }
            $credentials = \App\Models\SiteSetting::getValue('analytics_credentials_json');
            if ($credentials) {
                $decoded = json_decode($credentials, true);
                if (is_array($decoded)) {
                    config(['analytics.service_account_credentials_json' => $decoded]);
                }
            }
        }

        // Dynamic Mail Config
        if (!app()->runningInConsole()) {
            $mailHost = \App\Models\SiteSetting::getValue('mail_host');
            if ($mailHost) {
                config([
                    'mail.mailers.smtp.host' => $mailHost,
                    'mail.mailers.smtp.port' => \App\Models\SiteSetting::getValue('mail_port', 1025),
                    'mail.mailers.smtp.username' => \App\Models\SiteSetting::getValue('mail_username'),
                    'mail.mailers.smtp.password' => \App\Models\SiteSetting::getValue('mail_password'),
                    'mail.mailers.smtp.encryption' => \App\Models\SiteSetting::getValue('mail_encryption', 'null'),
                    'mail.from.address' => \App\Models\SiteSetting::getValue('mail_from_address'),
                    'mail.from.name' => \App\Models\SiteSetting::getValue('mail_from_name'),
                ]);
            }
        }
    }
}
