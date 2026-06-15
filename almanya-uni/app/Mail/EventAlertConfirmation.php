<?php

namespace App\Mail;

use App\Models\EventCitySubscription;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EventAlertConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public EventCitySubscription $subscription)
    {
    }

    public function envelope(): Envelope
    {
        $name     = brand('name');
        $fromAddr = brand('mail_from') ?: config('mail.from.address');
        $fromName = brand('mail_from_name') ?: $name;
        $city     = $this->subscription->city?->name;

        return new Envelope(
            from: new Address($fromAddr, $fromName),
            subject: $name . ' — ' . __('Confirm your :city event alerts 📬', ['city' => $city]),
        );
    }

    public function content(): Content
    {
        $base = 'https://' . brand('domain');

        return new Content(
            view: 'emails.event-alert-confirmation',
            with: [
                'subscription'   => $this->subscription,
                'city'           => $this->subscription->city,
                'confirmUrl'     => $base . '/events/alerts/confirm/' . $this->subscription->confirm_token,
                'unsubscribeUrl' => $base . '/events/alerts/unsubscribe/' . $this->subscription->unsubscribe_token,
                'brandName'      => brand('name'),
                'brandHomeUrl'   => $base,
            ],
        );
    }
}
