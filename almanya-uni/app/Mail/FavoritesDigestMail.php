<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FavoritesDigestMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public array $payload,
    ) {}

    public function envelope(): Envelope
    {
        // Kullanıcının kaydolduğu brand (varsa) — yoksa fallback
        $brandKey = $this->user->brand_key ?: brand_key();
        $name = brand('name', $brandKey);
        $fromAddr = brand('mail_from', $brandKey) ?: config('mail.from.address');
        $fromName = brand('mail_from_name', $brandKey) ?: $name;

        $favCount = $this->payload['favorites_count'] ?? 0;
        $dlCount = count($this->payload['upcoming_deadlines'] ?? []);
        $newCount = $this->payload['new_count'] ?? 0;

        $subject = $dlCount > 0
            ? "⏰ $name — " . __(':count favorite programme deadlines approaching', ['count' => $dlCount])
            : ($newCount > 0
                ? "✨ $name — " . __(':count new programmes added to your favorites', ['count' => $newCount])
                : "⭐ $name — " . __('Weekly digest for :count favorites', ['count' => $favCount]));

        return new Envelope(
            to: [new Address($this->user->email, $this->user->name ?: '')],
            from: new Address($fromAddr, $fromName),
            subject: $subject,
        );
    }

    public function content(): Content
    {
        $brandKey = $this->user->brand_key ?: brand_key();
        return new Content(
            view: 'emails.favorites-digest',
            with: [
                'user' => $this->user,
                'payload' => $this->payload,
                'brandKey' => $brandKey,
                'brandName' => brand('name', $brandKey),
                'brandDomain' => brand('domain', $brandKey),
                'brandHomeUrl' => 'https://' . brand('domain', $brandKey),
            ],
        );
    }
}
