<?php

namespace App\Mail;

use App\Models\Feedback;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FeedbackReceived extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Feedback $feedback) {}

    public function envelope(): Envelope
    {
        $typeLabel = Feedback::TYPES[$this->feedback->type] ?? $this->feedback->type;
        // Feedback HTTP request'inden geliyor → brand() request host'tan resolve eder
        $brandKey = brand_key();
        $fromAddr = brand('mail_from', $brandKey) ?: config('mail.from.address');
        $brandName = brand('name', $brandKey);

        return new Envelope(
            from: new Address($fromAddr, $brandName . ' Feedback'),
            subject: "[Feedback] {$typeLabel} — " . \Illuminate\Support\Str::limit($this->feedback->message, 60),
            // Yanıtlanması istenirse kullanıcının email'ine
            replyTo: $this->feedback->email
                ? [new Address($this->feedback->email, $this->feedback->name ?: '')]
                : null,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.feedback-received',
            with: ['feedback' => $this->feedback],
        );
    }
}
