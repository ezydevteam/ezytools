<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\User;
use App\Services\UddoktaPayService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Services\MailService;

class SubscriptionController extends Controller
{
    protected $paymentService;
    protected $mailService;

    public function __construct(UddoktaPayService $paymentService, MailService $mailService)
    {
        $this->paymentService = $paymentService;
        $this->mailService = $mailService;
    }

    /**
     * Show Pricing Page with geo-based currency.
     */
    public function pricing(Request $request)
    {
        $country = $this->resolveCountry($request->ip());

        // Select currency based on country
        switch ($country) {
            case 'BD':
                $currency = 'BDT';
                $symbol = '৳';
                $monthly = (int) setting('pro_price_monthly', 299);
                $yearly = (int) setting('pro_price_yearly', 2499);
                break;
            case 'IN':
                $currency = 'INR';
                $symbol = '₹';
                $monthly = (int) setting('pro_price_monthly_inr', 249);
                $yearly = (int) setting('pro_price_yearly_inr', 1999);
                break;
            default:
                $currency = 'USD';
                $symbol = '$';
                $monthly = (int) setting('pro_price_monthly_usd', 5);
                $yearly = (int) setting('pro_price_yearly_usd', 49);
                break;
        }

        return Inertia::render('Pricing', [
            'monthlyPrice' => $monthly,
            'yearlyPrice' => $yearly,
            'currency' => $currency,
            'currencySymbol' => $symbol,
            'country' => $country,
            'credits' => [
                'enabled' => \App\Models\AiSetting::getValue('credit_system_enabled', 'true') === 'true',
                'free' => (int) \App\Models\AiSetting::getValue('free_ai_credit_limit', 100),
                'pro' => (int) \App\Models\AiSetting::getValue('pro_ai_credit_limit', 1000),
            ],
            'schemas' => [
                app(\App\Services\SchemaService::class)->product(),
            ],
        ]);
    }

    /**
     * Resolve the visitor's country code from their IP.
     */
    protected function resolveCountry(string $ip): string
    {
        // Local/private IPs default to BD for dev
        if (in_array($ip, ['127.0.0.1', '::1']) || str_starts_with($ip, '192.168.') || str_starts_with($ip, '10.')) {
            return 'BD';
        }

        try {
            $response = \Illuminate\Support\Facades\Http::timeout(2)
                ->get("http://ip-api.com/json/{$ip}?fields=countryCode");

            if ($response->ok() && $response->json('countryCode')) {
                return $response->json('countryCode');
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning("GeoIP lookup failed for {$ip}: " . $e->getMessage());
        }

        return 'US'; // Default fallback
    }

    /**
     * Handle Subscription Checkout
     */
    public function checkout(Request $request)
    {
        $request->validate([
            'plan' => 'required|in:monthly,yearly',
        ]);

        $user = auth()->user();

        // UddoktaPay processes in BDT — always charge BDT amount
        $amount = $request->plan === 'monthly'
            ? (int) setting('pro_price_monthly', 299)
            : (int) setting('pro_price_yearly', 2499);

        // Create Pending Subscription
        $subscription = Subscription::create([
            'user_id' => $user->id,
            'plan' => $request->plan,
            'amount' => $amount,
            'currency' => 'BDT',
            'status' => 'pending',
            'transaction_id' => 'TRX-' . strtoupper(uniqid()),
        ]);

        $paymentUrl = $this->paymentService->initPayment([
            'full_name' => $user->name,
            'email' => $user->email,
            'amount' => $amount,
            'metadata' => [
                'subscription_id' => $subscription->id,
                'user_id' => $user->id,
                'plan' => $request->plan
            ],
            'redirect_url' => route('subscription.success'),
            'cancel_url' => route('subscription.cancel'),
            'webhook_url' => route('api.payment.webhook'),
        ]);

        if ($paymentUrl) {
            return Inertia::location($paymentUrl);
        }

        return back()->with('error', 'Payment gateway is currently unavailable. Please try again later.');
    }

    /**
     * Success Callback URL — verify payment status and redirect accordingly
     */
    public function success(Request $request)
    {
        $invoiceId = $request->query('invoice_id');

        if ($invoiceId) {
            $verification = $this->paymentService->verifyPayment($invoiceId);

            if ($verification && isset($verification['status'])) {
                if ($verification['status'] === 'COMPLETED') {
                    // Activate immediately if webhook hasn't done it yet
                    $metadata = $verification['metadata'] ?? [];
                    $subscriptionId = $metadata['subscription_id'] ?? null;

                    if ($subscriptionId) {
                        $subscription = Subscription::find($subscriptionId);
                        if ($subscription && $subscription->status === 'pending') {
                            $monthsToAdd = $subscription->plan === 'yearly' ? 12 : 1;
                            $subscription->update([
                                'status' => 'active',
                                'payment_method' => $verification['payment_method'] ?? 'uddoktapay',
                                'starts_at' => now(),
                                'expires_at' => now()->addMonths($monthsToAdd),
                            ]);

                            $user = User::find($subscription->user_id);
                            if ($user) {
                                $proCredits = (int) \App\Models\AiSetting::getValue('pro_ai_credit_limit', 1000);
                                $user->update([
                                    'subscription_type' => 'pro',
                                    'subscription_expires_at' => $subscription->expires_at,
                                    'ai_credit' => $proCredits,
                                ]);
                            }
                        }
                    }

                    return Inertia::render('Subscription/Success');
                }

                if ($verification['status'] === 'PENDING') {
                    return redirect()->route('subscription.pending');
                }
            }
        }

        // No invoice_id or unknown status — check if user has a recent pending subscription
        $pending = Subscription::where('user_id', auth()->id())
            ->where('status', 'pending')
            ->where('created_at', '>=', now()->subHours(2))
            ->latest()
            ->first();

        if ($pending) {
            return redirect()->route('subscription.pending');
        }

        return Inertia::render('Subscription/Success');
    }

    /**
     * Pending Payment Page
     */
    public function pending()
    {
        $subscription = Subscription::where('user_id', auth()->id())
            ->where('status', 'pending')
            ->latest()
            ->first();

        return Inertia::render('Subscription/Pending', [
            'subscription' => $subscription,
        ]);
    }

    /**
     * Cancel Callback URL
     */
    public function cancel()
    {
        return Inertia::render('Subscription/Cancel');
    }

    /**
     * Cancel Active Subscription (User Action)
     */
    public function cancelActive()
    {
        $user = auth()->user();

        // Update active subscription to cancelled
        Subscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->update(['status' => 'cancelled']);

        // Remove Pro benefits
        $user->update([
            'subscription_type' => 'free',
            'subscription_expires_at' => null,
        ]);

        // Send cancellation email
        $sub = Subscription::where('user_id', $user->id)->latest()->first();
        if ($sub && $user->email) {
            $this->mailService->sendProCancelled($user, $sub);
        }

        return back()->with('success', 'Your subscription has been cancelled.');
    }

    /**
     * Webhook/IPN Handler (No CSRF)
     */
    public function webhook(Request $request)
    {
        $invoiceId = $request->header('INVOICE-ID') ?? $request->invoice_id;

        if (!$invoiceId) {
            return response()->json(['message' => 'Invoice ID missing'], 400);
        }

        $verification = $this->paymentService->verifyPayment($invoiceId);

        if ($verification && isset($verification['status']) && $verification['status'] === 'COMPLETED') {

            $metadata = $verification['metadata'] ?? [];
            $subscriptionId = $metadata['subscription_id'] ?? null;

            if ($subscriptionId) {
                $subscription = Subscription::find($subscriptionId);

                if ($subscription && $subscription->status === 'pending') {
                    $monthsToAdd = $subscription->plan === 'yearly' ? 12 : 1;

                    $subscription->update([
                        'status' => 'active',
                        'payment_method' => $verification['payment_method'] ?? 'uddoktapay',
                        'starts_at' => now(),
                        'expires_at' => now()->addMonths($monthsToAdd),
                    ]);

                    // Update User Pro Status
                    $user = User::find($subscription->user_id);
                    if ($user) {
                        $proCredits = (int) \App\Models\AiSetting::getValue('pro_ai_credit_limit', 1000);
                        $user->update([
                            'is_pro' => true,
                            'ai_credit' => $proCredits,
                        ]);

                        // Send payment success email
                        if ($user->email) {
                            $this->mailService->sendProPaymentSuccess($user, $subscription);
                        }
                    }

                    return response()->json(['message' => 'Subscription activated successfully']);
                }
            }
        }

        return response()->json(['message' => 'Invalid payment or already processed'], 400);
    }
}
