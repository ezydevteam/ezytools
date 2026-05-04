<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ToolController;
use App\Http\Controllers\ToolReviewController;
use App\Http\Controllers\User\DashboardController as UserDashboardController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ToolSeoController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/tools', [ToolController::class, 'index'])->name('tools.index');
Route::get('/tools/ai/ai-seo-auditor/report/{uuid}', function ($uuid) {
    $report = \App\Models\SeoAuditReport::where('uuid', $uuid)->firstOrFail();
    $tool = \App\Models\Tool::with(['settings', 'category', 'faqs', 'seoContent'])
        ->where('slug', 'ai-seo-auditor')
        ->active()
        ->firstOrFail();
    $tool->increment('usage_count');
    return \Inertia\Inertia::render('Tools/Show', [
        'tool' => $tool->append(['average_rating', 'review_count']),
        'settings' => $tool->getSettingsArray(),
        'seoContent' => $tool->seoContent,
        'report' => $report,
        'can_use_tool' => true,
        'ads' => [
            'top' => \App\Models\AdSpace::active()->forPosition('tool-top')->first(),
            'bottom' => \App\Models\AdSpace::active()->forPosition('tool-bottom')->first(),
            'sidebar' => \App\Models\AdSpace::active()->forPosition('tool-sidebar')->first(),
        ],
    ]);
})->name('tools.seo-auditor.report');
Route::get('/tools/{category}', [ToolController::class, 'category'])->name('tools.category');
Route::get('/tools/{category}/{slug}', [ToolController::class, 'show'])->name('tools.show');

// Tool Rating (public)
Route::post('/tools/{tool}/rate', [ToolReviewController::class, 'rate'])->name('tools.rate');

// Static Pages
Route::get('/about', [\App\Http\Controllers\PageController::class, 'about'])->name('pages.about');
Route::get('/privacy-policy', [\App\Http\Controllers\PageController::class, 'privacy'])->name('pages.privacy');
Route::get('/terms-of-service', [\App\Http\Controllers\PageController::class, 'terms'])->name('pages.terms');
Route::get('/contact', [\App\Http\Controllers\PageController::class, 'contact'])->name('pages.contact');
Route::get('/faq', [\App\Http\Controllers\PageController::class, 'faq'])->name('pages.faq');
Route::get('/gdpr', [\App\Http\Controllers\PageController::class, 'gdpr'])->name('pages.gdpr');
Route::get('/do-not-sell', [\App\Http\Controllers\PageController::class, 'doNotSell'])->name('pages.do-not-sell');
Route::get('/advertise', [\App\Http\Controllers\AdvertiseController::class, 'index'])->name('pages.advertise');
Route::post('/advertise', [\App\Http\Controllers\AdvertiseController::class, 'submit'])->name('advertise.submit');

Route::get('/sitemap', [\App\Http\Controllers\PageController::class, 'sitemap'])->name('pages.sitemap');

// Sitemaps
Route::get('/sitemap.xml', [\App\Http\Controllers\SitemapController::class, 'index'])->name('sitemap.xml');
Route::get('/sitemap-tools.xml', [\App\Http\Controllers\SitemapController::class, 'tools']);

// Robots.txt
Route::get('/robots.txt', function () {
    return response()->view('robots')
        ->header('Content-Type', 'text/plain');
});

// OG Image Generator
Route::get('/og-image/{slug}', [\App\Http\Controllers\OgImageController::class, 'show'])->name('og-image');

// Blog
Route::get('/blog', [\App\Http\Controllers\BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [\App\Http\Controllers\BlogController::class, 'show'])->name('blog.show');

// User Dashboard Routes
Route::middleware('auth')->prefix('dashboard')->name('user.')->group(function () {
    Route::get('/', [UserDashboardController::class, 'index'])->name('dashboard');
    Route::get('/favorites', [\App\Http\Controllers\User\FavoriteController::class, 'index'])->name('favorites');
    Route::post('/tools/{tool}/favorite', [\App\Http\Controllers\User\FavoriteController::class, 'toggle'])->name('tools.favorite');
    // We will add subscription routes later
});

use App\Http\Controllers\Admin\ToolManagerController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\UserManagerController;
use App\Http\Controllers\Admin\AdSpaceController;
use App\Http\Controllers\Admin\SettingsController;

// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Tools Management
    Route::resource('tools', ToolManagerController::class)->except(['show']);
    Route::get('tools/{tool}/settings', [ToolManagerController::class, 'settings'])->name('tools.settings');
    Route::post('tools/{tool}/settings', [ToolManagerController::class, 'updateSettings'])->name('tools.settings.update');

    // Tool SEO Content
    Route::get('tools/{tool}/seo', [ToolSeoController::class, 'edit'])->name('tools.seo');
    Route::post('tools/{tool}/seo', [ToolSeoController::class, 'update'])->name('tools.seo.update');
    Route::post('tools/{tool}/faqs', [ToolSeoController::class, 'storeFaq'])->name('tools.faqs.store');
    Route::put('tools/{tool}/faqs/{faq}', [ToolSeoController::class, 'updateFaq'])->name('tools.faqs.update');
    Route::delete('tools/{tool}/faqs/{faq}', [ToolSeoController::class, 'destroyFaq'])->name('tools.faqs.destroy');
    Route::post('tools/{tool}/faqs/reorder', [ToolSeoController::class, 'reorderFaqs'])->name('tools.faqs.reorder');
    Route::post('tools/{tool}/related', [ToolSeoController::class, 'updateRelated'])->name('tools.related.update');


    // Categories
    Route::resource('categories', CategoryController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::post('categories/reorder', [CategoryController::class, 'reorder'])->name('categories.reorder');

    // Blog
    Route::resource('blog', \App\Http\Controllers\Admin\BlogController::class)->except(['show']);

    // Users
    Route::resource('users', UserManagerController::class)->except(['create', 'show', 'edit']);
    Route::put('users/{user}/toggle-active', [UserManagerController::class, 'toggleActive'])->name('users.toggle');
    Route::put('users/{user}/role', [UserManagerController::class, 'updateRole'])->name('users.role');
    Route::post('users/{user}/credits', [UserManagerController::class, 'updateCredits'])->name('users.credits');
    Route::post('users/{user}/make-pro', [UserManagerController::class, 'makePro'])->name('users.make-pro');

    // Subscriptions
    Route::resource('subscriptions', \App\Http\Controllers\Admin\SubscriptionController::class)->only(['index', 'update', 'destroy']);

    // Email Campaigns
    Route::get('emails', [\App\Http\Controllers\Admin\EmailCampaignController::class, 'index'])->name('emails.index');
    Route::get('emails/create', [\App\Http\Controllers\Admin\EmailCampaignController::class, 'create'])->name('emails.create');
    Route::post('emails', [\App\Http\Controllers\Admin\EmailCampaignController::class, 'store'])->name('emails.store');
    Route::get('emails/{campaign}/preview', [\App\Http\Controllers\Admin\EmailCampaignController::class, 'preview'])->name('emails.preview');
    Route::post('emails/{campaign}/send', [\App\Http\Controllers\Admin\EmailCampaignController::class, 'send'])->name('emails.send');
    Route::post('emails/{campaign}/duplicate', [\App\Http\Controllers\Admin\EmailCampaignController::class, 'duplicate'])->name('emails.duplicate');
    Route::delete('emails/{campaign}', [\App\Http\Controllers\Admin\EmailCampaignController::class, 'destroy'])->name('emails.destroy');

    // Ad Spaces
    Route::get('ads', [AdSpaceController::class, 'index'])->name('ads.index');
    Route::put('ads/{ad}', [AdSpaceController::class, 'update'])->name('ads.update');

    // Ad Inquiries
    Route::get('ad-inquiries', [\App\Http\Controllers\Admin\AdInquiryController::class, 'index'])->name('ad-inquiries.index');
    Route::put('ad-inquiries/{inquiry}', [\App\Http\Controllers\Admin\AdInquiryController::class, 'update'])->name('ad-inquiries.update');
    Route::delete('ad-inquiries/{inquiry}', [\App\Http\Controllers\Admin\AdInquiryController::class, 'destroy'])->name('ad-inquiries.destroy');

    // Settings
    Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('settings', [SettingsController::class, 'update'])->name('settings.update');

    // AI Management
    Route::prefix('ai')->name('ai.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\Ai\AiDashboardController::class, 'index'])->name('dashboard');

        Route::resource('providers', \App\Http\Controllers\Admin\Ai\AiProviderController::class)->except(['show']);
        Route::post('providers/{provider}/test', [\App\Http\Controllers\Admin\Ai\AiProviderController::class, 'testConnection'])->name('providers.test');

        Route::resource('models', \App\Http\Controllers\Admin\Ai\AiModelController::class)->except(['show']);

        Route::get('tools/{tool}/config', [\App\Http\Controllers\Admin\Ai\AiToolConfigController::class, 'edit'])->name('tools.config');
        Route::put('tools/{tool}/config', [\App\Http\Controllers\Admin\Ai\AiToolConfigController::class, 'update'])->name('tools.config.update');

        Route::get('settings', [\App\Http\Controllers\Admin\Ai\AiSettingsController::class, 'index'])->name('settings');
        Route::post('settings', [\App\Http\Controllers\Admin\Ai\AiSettingsController::class, 'update'])->name('settings.update');

        Route::resource('voices', \App\Http\Controllers\Admin\Ai\AiVoiceController::class)->except(['show', 'create', 'edit']);

        Route::get('stats', [\App\Http\Controllers\Admin\Ai\AiStatsController::class, 'index'])->name('stats');
    });
});

// Fallback Dashboard Route for Breeze Redirects
Route::get('/dashboard-redirect', function () {
    if (auth()->check() && auth()->user()->isAdmin()) {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('user.dashboard');
})->middleware('auth')->name('dashboard');

// Public Subscriptions Route
Route::get('/pricing', [\App\Http\Controllers\SubscriptionController::class, 'pricing'])->name('pricing');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Subscriptions (Protected)
    Route::post('/subscription/checkout', [\App\Http\Controllers\SubscriptionController::class, 'checkout'])->name('subscription.checkout');
    Route::get('/subscription/success', [\App\Http\Controllers\SubscriptionController::class, 'success'])->name('subscription.success');
    Route::get('/subscription/pending', [\App\Http\Controllers\SubscriptionController::class, 'pending'])->name('subscription.pending');
    Route::get('/subscription/cancel', [\App\Http\Controllers\SubscriptionController::class, 'cancel'])->name('subscription.cancel');
    Route::post('/subscription/cancel-active', [\App\Http\Controllers\SubscriptionController::class, 'cancelActive'])->name('subscription.cancel-active');

    // Two-Factor Authentication
    Route::post('/user/two-factor-authentication', [\App\Http\Controllers\Auth\TwoFactorAuthController::class, 'enable'])->name('two-factor.enable');
    Route::post('/user/confirmed-two-factor-authentication', [\App\Http\Controllers\Auth\TwoFactorAuthController::class, 'confirm'])->name('two-factor.confirm');
    Route::delete('/user/two-factor-authentication', [\App\Http\Controllers\Auth\TwoFactorAuthController::class, 'disable'])->name('two-factor.disable');
});

Route::middleware('guest')->group(function () {
    Route::get('two-factor-challenge', [\App\Http\Controllers\Auth\TwoFactorAuthController::class, 'showChallenge'])->name('two-factor.login');
    Route::post('two-factor-challenge', [\App\Http\Controllers\Auth\TwoFactorAuthController::class, 'verifyChallenge']);
});

require __DIR__.'/auth.php';
