<?php

namespace App\Mail;

use App\Models\Subscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Sent immediately after a subscriber confirms their email.
 * Hand-picked value content (top tools + posts) — locale-aware.
 */
class NewsletterWelcome extends Mailable
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

        $locale = $this->subscriber->language ?: 'tr';
        $subject = match ($locale) {
            'en' => "Welcome to {$name} 🎓 — Start here",
            'de' => "Willkommen bei {$name} 🎓 — Starte hier",
            default => "Hoş geldin — {$name} ailesine katıldın 🎓",
        };

        return new Envelope(
            from: new Address($fromAddr, $fromName),
            subject: $subject,
        );
    }

    public function content(): Content
    {
        $brandKey = $this->subscriber->brand_key ?: brand_key();
        $domain   = brand('domain', $brandKey);
        $base     = 'https://' . $domain;
        $locale   = $this->subscriber->language ?: 'tr';

        // Switch app locale so all __() inside the email template render in
        // the subscriber's chosen language (not the queue worker's default).
        $oldLocale = app()->getLocale();
        app()->setLocale($locale);

        // Top tools shown to every new subscriber. Real, live links — no fake.
        $topTools = [
            ['emoji' => '🧭', 'title' => __('Pathway Finder'),      'desc' => __('5 questions → Studienkolleg, Bachelor, Master, PhD, Ausbildung or Sprachkurs.'), 'url' => $base . '/' . $locale . '/tools/pathway-finder'],
            ['emoji' => '🛡️', 'title' => __('Professional Recognition'), 'desc' => __('Is your job recognised in Germany? 6 popular professions covered.'), 'url' => $base . '/' . $locale . '/tools/professional-recognition'],
            ['emoji' => '💰', 'title' => __('Cost of Living'),       'desc' => __('Monthly student budget by city — DAAD baseline.'), 'url' => $base . '/' . $locale . '/tools/cost-of-living'],
            ['emoji' => '🎯', 'title' => __('University Match Quiz'), 'desc' => __('5 questions → universities that fit you best.'), 'url' => $base . '/' . $locale . '/tools/recommendation'],
            ['emoji' => '🏦', 'title' => __('Sperrkonto Finder'),     'desc' => __('Compare blocked-account providers side by side.'), 'url' => $base . '/' . $locale . '/tools/sperrkonto'],
        ];

        // Restore locale so the queue worker isn't sticky to one language for following jobs
        app()->setLocale($oldLocale);

        return new Content(
            view: 'emails.newsletter-welcome',
            with: [
                'subscriber'     => $this->subscriber,
                'locale'         => $locale,
                'brandKey'       => $brandKey,
                'brandName'      => brand('name', $brandKey),
                'brandDomain'    => $domain,
                'brandHomeUrl'   => $base,
                'unsubscribeUrl' => $base . '/newsletter/unsubscribe/' . $this->subscriber->unsubscribe_token,
                'topTools'       => $topTools,
            ],
        );
    }
}
