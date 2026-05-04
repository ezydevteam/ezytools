<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $user->load('activeSubscription');
        $recentUsages = \App\Models\AiUsage::where('user_id', $user->id)
            ->with(['tool' => function ($query) {
                $query->with('category');
            }])
            ->where('status', 'success')
            ->latest('created_at')
            ->take(10)
            ->get()
            ->unique('tool_id')
            ->values();
        
        $favorites = $user->favoriteTools()->with('category')->take(10)->get();

        $creditEnabled = \App\Models\AiSetting::getValue('credit_system_enabled', 'true') === 'true';
        $maxCredits = $user->isPro()
            ? (int) \App\Models\AiSetting::getValue('pro_ai_credit_limit', 1000)
            : (int) \App\Models\AiSetting::getValue('free_ai_credit_limit', 100);

        // Check for pending subscriptions (recent ones within 24 hours)
        $pendingSubscription = \App\Models\Subscription::where('user_id', $user->id)
            ->where('status', 'pending')
            ->where('created_at', '>=', now()->subHours(24))
            ->latest()
            ->first(['id', 'plan', 'amount', 'currency', 'status', 'created_at']);

        return Inertia::render('User/Dashboard', [
            'user' => $user,
            'recentUsages' => $recentUsages,
            'favorites' => $favorites,
            'creditInfo' => [
                'enabled' => $creditEnabled,
                'max' => $maxCredits,
            ],
            'pendingSubscription' => $pendingSubscription,
        ]);
    }
}
