<?php

namespace App\Mail;

use App\Models\City;
use App\Models\EventCitySubscription;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

/**
 * Haftalık şehir etkinlik digest'i — abonenin şehrinde yeni eklenen yaklaşan etkinlikler.
 */
class EventCityDigestMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public EventCitySubscription $subscription,
        public City $city,
        public Collection $events,
    ) {
    }

    public function envelope(): Envelope
    {
        $name     = brand('name');
        $fromAddr = brand('mail_from') ?: config('mail.from.address');
        $fromName = brand('mail_from_name') ?: $name;
        $count    = $this->events->count();

        return new Envelope(
            from: new Address($fromAddr, $fromName),
            subject: __(':count new events in :city this week 🎵', ['count' => $count, 'city' => $this->city->name]),
        );
    }

    public function content(): Content
    {
        $base = 'https://' . brand('domain');

        return new Content(
            view: 'emails.event-city-digest',
            with: [
                'city'           => $this->city,
                'events'         => $this->events,
                'eventsUrl'      => $base . '/events?category=culture',
                'unsubscribeUrl' => $base . '/events/alerts/unsubscribe/' . $this->subscription->unsubscribe_token,
                'brandName'      => brand('name'),
                'brandHomeUrl'   => $base,
                'baseUrl'        => $base,
            ],
        );
    }
}
