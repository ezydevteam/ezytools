<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OtpRegisterMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $name,
        public string $otp,
        public int $expiryMinutes = 10,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Verify Your Account — ' . config('app.name', 'EzyTools'),
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.otp-register');
    }
}
