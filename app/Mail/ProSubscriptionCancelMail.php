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

class ProSubscriptionCancelMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public Subscription $subscription,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Pro Subscription Has Been Cancelled — ' . config('app.name', 'EzyTools'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.pro-subscription-cancel',
            with: [
                'name'        => $this->user->name,
                'expiresAt'   => $this->subscription->expires_at?->format('d F Y') ?? 'N/A',
                'renewUrl'    => url('/pricing'),
                'feedbackUrl' => url('/pricing'),
            ]
        );
    }
}
