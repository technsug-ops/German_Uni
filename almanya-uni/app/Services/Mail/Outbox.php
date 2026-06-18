<?php

namespace App\Services\Mail;

use App\Mail\OutreachMail;
use App\Models\EmailMessage;
use Illuminate\Support\Facades\Mail;

/**
 * Çok-kutulu giden mail. config('services.mailboxes') üzerinden kutu (admin /
 * partnerships / …) seçilir; her kutunun kendi gönderen adresi + SMTP mailer'ı vardır.
 * Gönderim email_messages'a loglanır (mailbox etiketiyle). İleride kutu eklemek:
 * config/services.php > mailboxes + .env.
 */
class Outbox
{
    /** Panelde seçim için: key => etiket. */
    public static function options(): array
    {
        return collect(config('services.mailboxes', []))
            ->mapWithKeys(fn ($box, $key) => [$key => ($box['label'] ?? $key) . ' — ' . ($box['email'] ?? '')])
            ->all();
    }

    public static function get(string $key): ?array
    {
        return config("services.mailboxes.$key");
    }

    /**
     * Bir kutudan mail gönder + logla. Hata fırlatmaz; sonucu EmailMessage->status
     * (sent|failed) üzerinden döndürür. Çağıran bildirimini buna göre verir.
     */
    public static function send(
        string $mailboxKey,
        string $toEmail,
        ?string $toName,
        string $subject,
        string $body,
        array $extra = [],
    ): EmailMessage {
        $box = self::get($mailboxKey);

        $msg = EmailMessage::create(array_merge([
            'direction'  => 'outbound',
            'mailbox'    => $mailboxKey,
            'to_email'   => $toEmail,
            'to_name'    => $toName,
            'from_email' => $box['email'] ?? $mailboxKey,
            'subject'    => $subject,
            'body'       => $body,
            'status'     => 'queued',
        ], $extra));

        if (! $box) {
            $msg->update(['status' => 'failed', 'error' => "Bilinmeyen mail kutusu: {$mailboxKey}"]);

            return $msg;
        }

        try {
            Mail::to($toEmail, $toName)->send(new OutreachMail(
                subjectLine: $subject,
                bodyText: $body,
                fromEmail: $box['email'],
                fromName: $box['name'] ?? 'ApplyToGerman',
                mailerName: $box['mailer'] ?? null,
                replyToAddress: $box['email'], // yanıtlar aynı kutuya
            ));

            $msg->update(['status' => 'sent', 'sent_at' => now()]);
        } catch (\Throwable $e) {
            report($e);
            $msg->update(['status' => 'failed', 'error' => $e->getMessage()]);
        }

        return $msg;
    }
}
