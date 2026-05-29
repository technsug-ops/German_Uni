<?php

namespace App\Mail;

use App\Models\ApplicationTracker;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class JourneyReminder extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public ApplicationTracker $tracker,
    ) {}

    public function envelope(): Envelope
    {
        $brandKey = brand_key();
        $name     = brand('name', $brandKey);
        $fromAddr = brand('mail_from', $brandKey) ?: config('mail.from.address');
        $fromName = brand('mail_from_name', $brandKey) ?: $name;

        $next = $this->tracker->nextStep();
        $subject = $next
            ? "{$name} — " . __('Next step: :step', ['step' => __($next['title'])])
            : "{$name} — " . __('Your Germany journey awaits');

        return new Envelope(
            from: new Address($fromAddr, $fromName),
            subject: $subject,
        );
    }

    public function content(): Content
    {
        $brandKey = brand_key();
        $base     = 'https://' . brand('domain', $brandKey);
        $locale   = app()->getLocale();

        return new Content(
            view: 'emails.journey-reminder',
            with: [
                'user'          => $this->user,
                'tracker'       => $this->tracker,
                'nextStep'      => $this->tracker->nextStep(),
                'progressPct'   => $this->tracker->progressPercent(),
                'completedCnt'  => $this->tracker->completedCount(),
                'totalSteps'    => count(ApplicationTracker::STEPS),
                'journeyUrl'    => $base . '/' . $locale . '/journey',
                'brandName'     => brand('name', $brandKey),
                'brandHomeUrl'  => $base,
            ],
        );
    }
}
