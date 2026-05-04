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

class ProPaymentSuccessMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public Subscription $subscription,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pro Payment Successful — ' . config('app.name', 'EzyTools') . ' Receipt',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.pro-payment-success',
            with: [
                'name'          => $this->user->name,
                'planLabel'     => $this->subscription->plan === 'yearly' ? 'Yearly Plan' : 'Monthly Plan',
                'transactionId' => $this->subscription->transaction_id ?? 'N/A',
                'paymentMethod' => $this->subscription->payment_method ?? 'Online',
                'paidAt'        => $this->subscription->starts_at?->format('d M Y') ?? now()->format('d M Y'),
                'expiresAt'     => $this->subscription->expires_at?->format('d M Y') ?? 'N/A',
                'amount'        => $this->subscription->amount ?? 0,
                'dashboardUrl'  => url('/dashboard'),
            ]
        );
    }
}
