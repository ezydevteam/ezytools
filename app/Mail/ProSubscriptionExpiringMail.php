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

class ProSubscriptionExpiringMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public Subscription $subscription,
        public int $daysLeft = 3,
        public bool $hasOffer = false,
        public int $offerDiscount = 0,
        public ?string $offerExpiry = null,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Your Pro Subscription Expires in {$this->daysLeft} Days — " . config('app.name', 'EzyTools'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.pro-subscription-expiring',
            with: [
                'name'          => $this->user->name,
                'daysLeft'      => $this->daysLeft,
                'expiresAt'     => $this->subscription->expires_at?->format('d F Y') ?? 'N/A',
                'renewUrl'      => url('/pricing'),
                'hasOffer'      => $this->hasOffer,
                'offerDiscount' => $this->offerDiscount,
                'offerExpiry'   => $this->offerExpiry,
            ]
        );
    }
}
