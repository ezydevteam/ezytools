<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SubscriptionController extends Controller
{
    public function index(Request $request)
    {
        $query = Subscription::with('user:id,name,email');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })->orWhere('transaction_id', 'like', "%{$search}%");
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $subscriptions = $query->latest()->paginate(15)->withQueryString();

        return Inertia::render('Admin/Subscriptions/Index', [
            'subscriptions' => $subscriptions,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    public function update(Request $request, Subscription $subscription)
    {
        $request->validate([
            'status' => 'required|in:pending,active,expired,cancelled',
            'expires_at' => 'nullable|date',
        ]);

        $subscription->update([
            'status' => $request->status,
            'expires_at' => $request->expires_at,
        ]);

        // Sync user's subscription status
        if ($request->status === 'active') {
            $subscription->user->update([
                'subscription_type' => 'pro',
                'subscription_expires_at' => $request->expires_at,
            ]);
        } elseif ($request->status === 'expired' || $request->status === 'cancelled') {
            // Check if user has other active subscriptions
            $hasActive = $subscription->user->subscriptions()->where('status', 'active')->where('expires_at', '>', now())->exists();
            if (!$hasActive) {
                $subscription->user->update([
                    'subscription_type' => 'free',
                    'subscription_expires_at' => null,
                ]);
            }
        }

        return redirect()->back()->with('success', 'Subscription updated successfully.');
    }

    public function destroy(Subscription $subscription)
    {
        if ($subscription->status === 'active' && ($subscription->expires_at === null || $subscription->expires_at->isFuture())) {
            return redirect()->back()->with('error', 'Cannot delete an active subscription. Please expire or cancel it first.');
        }

        $subscription->delete();
        return redirect()->back()->with('success', 'Subscription deleted successfully.');
    }
}
