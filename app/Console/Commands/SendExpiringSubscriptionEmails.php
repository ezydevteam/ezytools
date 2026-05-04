<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use App\Services\MailService;
use Illuminate\Console\Command;

class SendExpiringSubscriptionEmails extends Command
{
    protected $signature = 'subscriptions:notify-expiring';
    protected $description = 'Send emails for expiring, expired subscriptions';

    public function handle(MailService $mailService): void
    {
        // 3 days before expiry
        $expiring3 = Subscription::with('user')
            ->where('status', 'active')
            ->whereDate('expires_at', now()->addDays(3)->toDateString())
            ->get();

        foreach ($expiring3 as $sub) {
            $mailService->sendProExpiringSoon($sub->user, $sub);
        }
        $this->info("Sent {$expiring3->count()} expiring-3-day emails.");

        // 1 day before expiry
        $expiring1 = Subscription::with('user')
            ->where('status', 'active')
            ->whereDate('expires_at', now()->addDay()->toDateString())
            ->get();

        foreach ($expiring1 as $sub) {
            $mailService->sendProExpiringSoon($sub->user, $sub);
        }
        $this->info("Sent {$expiring1->count()} expiring-1-day emails.");

        // Expired today
        $expired = Subscription::with('user')
            ->where('status', 'active')
            ->whereDate('expires_at', now()->toDateString())
            ->get();

        foreach ($expired as $sub) {
            $sub->update(['status' => 'expired']);
            $sub->user->update(['subscription_type' => 'free']);
            $mailService->sendProExpired($sub->user, $sub);
        }
        $this->info("Processed {$expired->count()} expired subscriptions.");
    }
}
