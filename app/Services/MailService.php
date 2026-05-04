<?php

namespace App\Services;

use App\Mail\CampaignMail;
use App\Mail\OtpForgotPasswordMail;
use App\Mail\OtpRegisterMail;
use App\Mail\ProPaymentSuccessMail;
use App\Mail\ProSubscriptionCancelMail;
use App\Mail\ProSubscriptionExpiredMail;
use App\Mail\ProSubscriptionExpiringMail;
use App\Mail\WelcomeMail;
use App\Models\EmailCampaign;
use App\Models\EmailLog;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;

class MailService
{
    public function sendRegisterOtp(User $user, string $otp): void
    {
        Mail::to($user->email)->queue(new OtpRegisterMail(
            name: $user->name,
            otp: $otp,
            expiryMinutes: 10,
        ));

        $this->log($user, 'otp_register', 'OTP Verification');
    }

    public function sendForgotPasswordOtp(User $user, string $otp): void
    {
        Mail::to($user->email)->queue(new OtpForgotPasswordMail(
            name: $user->name,
            otp: $otp,
            resetUrl: url('/forgot-password'),
            expiryMinutes: 15,
        ));

        $this->log($user, 'otp_forgot_password', 'Password Reset OTP');
    }

    public function sendWelcome(User $user): void
    {
        Mail::to($user->email)
            ->queue(new WelcomeMail(
                user: $user,
                dashboardUrl: url('/dashboard'),
                proUrl: url('/pricing'),
            ));

        $this->log($user, 'welcome', 'Welcome to EzyTools');
    }

    public function sendProPaymentSuccess(User $user, Subscription $sub): void
    {
        Mail::to($user->email)->queue(new ProPaymentSuccessMail($user, $sub));
        $this->log($user, 'pro_payment_success', 'Pro Payment Receipt');
    }

    public function sendProCancelled(User $user, Subscription $sub): void
    {
        Mail::to($user->email)->queue(new ProSubscriptionCancelMail($user, $sub));
        $this->log($user, 'pro_cancelled', 'Pro Subscription Cancelled');
    }

    public function sendProExpiringSoon(User $user, Subscription $sub): void
    {
        $daysLeft = (int) now()->diffInDays($sub->expires_at);

        Mail::to($user->email)->queue(new ProSubscriptionExpiringMail(
            user: $user,
            subscription: $sub,
            daysLeft: $daysLeft,
            hasOffer: false,
        ));

        $this->log($user, 'pro_expiring', 'Pro Subscription Expiring');
    }

    public function sendProExpired(User $user, Subscription $sub): void
    {
        Mail::to($user->email)->queue(new ProSubscriptionExpiredMail(
            user: $user,
            subscription: $sub,
            returnDiscount: 20,
            couponCode: 'COMEBACK20',
            monthlyPrice: 499,
            yearlyPrice: 3999,
        ));

        $this->log($user, 'pro_expired', 'Pro Subscription Expired');
    }

    public function sendCampaign(EmailCampaign $campaign, Collection $users): void
    {
        $campaign->update(['status' => 'sending']);

        foreach ($users->chunk(50) as $chunk) {
            foreach ($chunk as $user) {
                Mail::to($user->email)->queue(new CampaignMail($user, $campaign));
            }
            usleep(500000); // 0.5 second rate limiting between chunks
        }

        $campaign->update([
            'status'           => 'sent',
            'sent_at'          => now(),
            'total_recipients' => $users->count(),
        ]);
    }

    private function log(User $user, string $type, string $subject): void
    {
        if (!$user->id) {
            return;
        }

        EmailLog::create([
            'user_id'    => $user->id,
            'type'       => $type,
            'subject'    => $subject,
            'status'     => 'sent',
            'created_at' => now(),
        ]);
    }
}
