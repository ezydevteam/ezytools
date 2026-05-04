<?php

namespace App\Mail;

use App\Models\EmailCampaign;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CampaignMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public EmailCampaign $campaign,
        public ?string $couponCode = null,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->campaign->subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.campaign',
            with: [
                'name'             => $this->user->name,
                'heading'          => $this->campaign->body_heading ?? $this->campaign->subject,
                'bodyContent'      => $this->campaign->body_content ?? '',
                'ctaText'          => $this->campaign->cta_text,
                'ctaUrl'           => $this->campaign->cta_url,
                'couponCode'       => $this->couponCode,
                'showOfferBox'     => !empty($this->couponCode),
                'offerTitle'       => 'Special Offer!',
                'offerDescription' => '',
                'offerExpiry'      => null,
                'disclaimer'       => null,
            ]
        );
    }
}
