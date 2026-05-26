<?php

namespace App\Mail;

use App\Models\Subscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewsletterConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Subscriber $subscriber)
    {
    }

    public function envelope(): Envelope
    {
        $brandKey = $this->subscriber->brand_key ?: brand_key();
        $name = brand('name', $brandKey);
        $fromAddr = brand('mail_from', $brandKey) ?: config('mail.from.address');
        $fromName = brand('mail_from_name', $brandKey) ?: $name;

        return new Envelope(
            from: new Address($fromAddr, $fromName),
            subject: $name . ' — E-posta adresini doğrula 📬',
        );
    }

    public function content(): Content
    {
        $brandKey = $this->subscriber->brand_key ?: brand_key();
        $domain = brand('domain', $brandKey);
        $base = 'https://' . $domain;

        return new Content(
            view: 'emails.newsletter-confirmation',
            with: [
                'subscriber' => $this->subscriber,
                'confirmUrl' => $base . '/newsletter/confirm/' . $this->subscriber->confirm_token,
                'unsubscribeUrl' => $base . '/newsletter/unsubscribe/' . $this->subscriber->unsubscribe_token,
                'brandKey' => $brandKey,
                'brandName' => brand('name', $brandKey),
                'brandDomain' => $domain,
                'brandHomeUrl' => $base,
            ],
        );
    }
}
