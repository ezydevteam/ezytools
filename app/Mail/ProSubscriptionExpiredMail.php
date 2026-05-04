<?php

namespace App\Mail;

use App\Models\Subscription;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ProSubscriptionExpiredMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public Subscription $subscription,
        public int $returnDiscount = 20,
        public string $couponCode = 'COMEBACK20',
        public int $monthlyPrice = 499,
        public int $yearlyPrice = 3999,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Pro Subscription Has Expired — ' . config('app.name', 'EzyTools'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.pro-subscription-expired',
            with: [
                'name'           => $this->user->name,
                'expiredAt'      => $this->subscription->expires_at?->format('d F Y') ?? 'N/A',
                'renewUrl'       => url('/pricing'),
                'returnDiscount' => $this->returnDiscount,
                'couponCode'     => $this->couponCode,
                'monthlyPrice'   => $this->monthlyPrice,
                'yearlyPrice'    => $this->yearlyPrice,
            ]
        );
    }
}
