<?php

namespace App\Mail;

use App\Services\Mail\MailBody;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OutreachMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  string       $layout       'personal' (sade) | 'rich' (şablonlu)
     * @param  array        $signature    ['name','role','phone','site','imprint']
     * @param  array<string> $filePaths   Eklenecek dosyaların mutlak yolları
     */
    public function __construct(
        public string $subjectLine,
        public string $bodyText,
        public string $fromEmail = 'partnerships@applytogerman.com',
        public string $fromName = 'ApplyToGerman',
        public ?string $mailerName = null,
        public ?string $replyToAddress = null,
        public string $layout = 'personal',
        public ?string $preheader = null,
        public ?string $heroUrl = null,
        public ?string $ctaLabel = null,
        public ?string $ctaUrl = null,
        public array $signature = [],
        public bool $showSignature = true,
        public array $filePaths = [],
        public string $lang = 'tr',
    ) {
        // Kutuya özel mailer; yoksa OUTREACH_MAILER; o da yoksa varsayılan.
        $this->mailer($mailerName ?: (env('OUTREACH_MAILER') ?: config('mail.default')));
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address($this->fromEmail, $this->fromName),
            subject: $this->subjectLine,
            replyTo: $this->replyToAddress
                ? [new Address($this->replyToAddress)]
                : null,
        );
    }

    public function content(): Content
    {
        $shared = [
            'subjectLine'   => $this->subjectLine,
            'layout'        => $this->layout,
            'preheader'     => $this->preheader,
            'heroUrl'       => $this->heroUrl,
            'ctaLabel'      => $this->ctaLabel,
            'ctaUrl'        => $this->ctaUrl,
            'signature'     => $this->signature,
            'showSignature' => $this->showSignature,
            'fromEmail'     => $this->fromEmail,
            'lang'          => $this->lang,
        ];

        return new Content(
            view: 'emails.outreach',
            text: 'emails.outreach-text',
            with: $shared + [
                'bodyHtml' => MailBody::toHtml($this->bodyText),
                'bodyText' => MailBody::toText($this->bodyText),
            ],
        );
    }

    /** @return array<Attachment> */
    public function attachments(): array
    {
        return collect($this->filePaths)
            ->filter(fn ($path) => is_string($path) && is_file($path))
            ->map(fn ($path) => Attachment::fromPath($path))
            ->values()
            ->all();
    }
}
