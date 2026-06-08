<?php

namespace App\Mail;

use App\Models\Subscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WeeklyDigest extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Subscriber $subscriber,
        public array $items,
        public array $stats,
        public array $deadlines = [],
    ) {}

    public function envelope(): Envelope
    {
        // Subscriber'ın geldiği brand (varsa) — yoksa fallback (TR-first: almanyauni)
        $brandKey = $this->subscriber->brand_key ?: brand_key();
        $name = brand('name', $brandKey);
        $fromAddr = brand('mail_from', $brandKey) ?: config('mail.from.address');
        $fromName = brand('mail_from_name', $brandKey) ?: $name;

        return new Envelope(
            to: [new Address($this->subscriber->email, $this->subscriber->name ?: '')],
            from: new Address($fromAddr, $fromName),
            subject: '📬 ' . $name . ' Haftalık — ' . count($this->items) . ' yeni içerik',
        );
    }

    public function content(): Content
    {
        $brandKey = $this->subscriber->brand_key ?: brand_key();
        return new Content(
            view: 'emails.weekly-digest',
            with: [
                'subscriber' => $this->subscriber,
                'items' => $this->items,
                'stats' => $this->stats,
                'deadlines' => $this->deadlines,
                'unsubscribeUrl' => route('newsletter.unsubscribe', $this->subscriber->unsubscribe_token),
                'brandKey' => $brandKey,
                'brandName' => brand('name', $brandKey),
                'brandDomain' => brand('domain', $brandKey),
                'brandHomeUrl' => 'https://' . brand('domain', $brandKey),
            ],
        );
    }
}
