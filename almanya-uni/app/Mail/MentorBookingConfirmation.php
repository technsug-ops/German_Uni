<?php

namespace App\Mail;

use App\Models\MentorSession;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Sent to BOTH parties (user + mentor) when a mentor session is booked.
 * Pass $recipient = 'user' or 'mentor' to switch subject + greeting.
 */
class MentorBookingConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public MentorSession $session,
        public string $recipient = 'user',
    ) {}

    public function envelope(): Envelope
    {
        $brandKey = brand_key();
        $name     = brand('name', $brandKey);
        $fromAddr = brand('mail_from', $brandKey) ?: config('mail.from.address');
        $fromName = brand('mail_from_name', $brandKey) ?: $name;

        $mentorName = $this->session->mentor->name ?? 'Mentor';
        $userName   = $this->session->user->name   ?? 'Student';

        $subject = $this->recipient === 'mentor'
            ? "{$name} — " . __('New session booking from :user', ['user' => $userName])
            : "{$name} — " . __('Your session with :mentor is booked', ['mentor' => $mentorName]);

        return new Envelope(
            from: new Address($fromAddr, $fromName),
            subject: $subject,
        );
    }

    public function content(): Content
    {
        $brandKey = brand_key();
        $base     = 'https://' . brand('domain', $brandKey);

        return new Content(
            view: 'emails.mentor-booking-confirmation',
            with: [
                'session'      => $this->session,
                'mentor'       => $this->session->mentor,
                'user'         => $this->session->user,
                'recipient'    => $this->recipient,
                'jitsiUrl'     => $this->session->jitsiUrl(),
                'scheduledAt'  => $this->session->scheduled_at,
                'duration'     => $this->session->duration_minutes,
                'brandName'    => brand('name', $brandKey),
                'brandHomeUrl' => $base,
            ],
        );
    }
}
