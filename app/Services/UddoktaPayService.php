<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UddoktaPayService
{
    protected $baseUrl;
    protected $apiKey;

    public function __construct()
    {
        $this->baseUrl = \App\Models\SiteSetting::getValue('uddoktapay_base_url', config('services.uddoktapay.base_url', env('UDDOKTAPAY_BASE_URL', 'https://sandbox.uddoktapay.com/api/checkout-v2')));
        $this->apiKey = \App\Models\SiteSetting::getValue('uddoktapay_api_key', config('services.uddoktapay.api_key', env('UDDOKTAPAY_API_KEY')));
    }

    /**
     * Init payment and get payment URL
     *
     * @param array $data ['full_name', 'email', 'amount', 'metadata', 'redirect_url', 'cancel_url', 'webhook_url']
     * @return mixed|null
     */
    public function initPayment(array $data)
    {
        if (!$this->apiKey || !$this->baseUrl) {
            Log::error('UddoktaPay credentials missing in .env');
            return null;
        }

        try {
            $response = Http::withHeaders([
                'RT-UDDOKTAPAY-API-KEY' => $this->apiKey,
                'Content-Type' => 'application/json'
            ])->post($this->baseUrl, [
                'full_name' => $data['full_name'],
                'email' => $data['email'],
                'amount' => $data['amount'],
                'metadata' => $data['metadata'] ?? [],
                'redirect_url' => $data['redirect_url'],
                'cancel_url' => $data['cancel_url'],
                'webhook_url' => $data['webhook_url'],
            ]);

            if ($response->successful() && $response->json('status')) {
                return $response->json('payment_url');
            }

            Log::error('UddoktaPay Init Failed: ' . $response->body());
            return null;

        } catch (\Exception $e) {
            Log::error('UddoktaPay Exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Verify payment status
     *
     * @param string $invoiceId
     * @return mixed|null
     */
    public function verifyPayment($invoiceId)
    {
        try {
            // Usually the verify URL is base_url without /checkout-v2 + /verify-payment
            $verifyUrl = str_replace('/checkout-v2', '/verify-payment', $this->baseUrl);
            
            $response = Http::withHeaders([
                'RT-UDDOKTAPAY-API-KEY' => $this->apiKey,
                'Content-Type' => 'application/json'
            ])->post($verifyUrl, [
                'invoice_id' => $invoiceId
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('UddoktaPay Verify Failed: ' . $response->body());
            return null;

        } catch (\Exception $e) {
            Log::error('UddoktaPay Verify Exception: ' . $e->getMessage());
            return null;
        }
    }
}
