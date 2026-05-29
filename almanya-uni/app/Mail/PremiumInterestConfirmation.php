<?php

namespace App\Mail;

use App\Models\PremiumInterest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PremiumInterestConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public PremiumInterest $interest) {}

    public function envelope(): Envelope
    {
        $brandKey = brand_key();
        $name     = brand('name', $brandKey);
        $fromAddr = brand('mail_from', $brandKey) ?: config('mail.from.address');
        $fromName = brand('mail_from_name', $brandKey) ?: $name;

        $locale = $this->interest->locale ?? 'tr';
        app()->setLocale($locale);

        $subject = $this->interest->wants_beta
            ? "{$name} — " . __('Welcome to the beta tester list 🚀')
            : "{$name} — " . __('You\'re on the Premium early-bird list ⭐');

        return new Envelope(
            from: new Address($fromAddr, $fromName),
            subject: $subject,
        );
    }

    public function content(): Content
    {
        $brandKey = brand_key();
        $base     = 'https://' . brand('domain', $brandKey);
        $locale   = $this->interest->locale ?? 'tr';

        return new Content(
            view: 'emails.premium-interest-confirmation',
            with: [
                'interest'     => $this->interest,
                'locale'       => $locale,
                'brandName'    => brand('name', $brandKey),
                'brandHomeUrl' => $base,
                'pricingUrl'   => $base . '/' . $locale . '/pricing',
                'isBeta'       => $this->interest->wants_beta,
            ],
        );
    }
}
