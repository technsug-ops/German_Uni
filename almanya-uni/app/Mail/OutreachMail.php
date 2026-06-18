<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OutreachMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $subjectLine,
        public string $bodyText,
        public ?string $replyToAddress = null,
    ) {
        // Use a dedicated outreach mailer if configured, else the default mailer.
        $this->mailer(env('OUTREACH_MAILER') ?: config('mail.default'));
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('partnerships@applytogerman.com', 'ApplyToGerman'),
            subject: $this->subjectLine,
            replyTo: $this->replyToAddress
                ? [new Address($this->replyToAddress)]
                : null,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.outreach',
            with: ['body' => $this->bodyText],
        );
    }
}
