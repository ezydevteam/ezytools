<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\SiteSetting;

class SmsService
{
    protected $apiToken;
    protected $sid;
    protected $from;

    public function __construct()
    {
        $this->sid = SiteSetting::getValue('sms_sid', config('services.sms.sid'));
        $this->apiToken = SiteSetting::getValue('sms_token', config('services.sms.token'));
        $this->from = SiteSetting::getValue('sms_from', config('services.sms.from'));
    }

    /**
     * Send OTP via SMS.
     * Default implementation uses a generic HTTP request pattern 
     * common in local providers, but can be adapted for Twilio.
     */
    public function sendOtp($to, $otp)
    {
        $message = "Your EzyTools verification code is: {$otp}";

        // If SID is present, assume Twilio
        if ($this->sid) {
            return $this->sendTwilio($to, $message);
        }

        // Default: Generic HTTP GET/POST for local providers (e.g. BulkSMSBD)
        return $this->sendGeneric($to, $message);
    }

    protected function sendTwilio($to, $message)
    {
        try {
            $response = Http::withBasicAuth($this->sid, $this->apiToken)
                ->asForm()
                ->post("https://api.twilio.com/2010-04-01/Accounts/{$this->sid}/Messages.json", [
                    'To' => $to,
                    'From' => $this->from,
                    'Body' => $message,
                ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error("Twilio SMS failed: " . $e->getMessage());
            return false;
        }
    }

    protected function sendGeneric($to, $message)
    {
        // Example for a common local provider API
        // This should be customized based on the actual provider's documentation
        try {
            $response = Http::get("https://api.sms-provider.com/send", [
                'api_token' => $this->apiToken,
                'to' => $to,
                'message' => $message,
                'from' => $this->from,
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error("Generic SMS failed: " . $e->getMessage());
            return false;
        }
    }
}
