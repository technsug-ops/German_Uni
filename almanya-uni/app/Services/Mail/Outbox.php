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

        // Kolon henüz migrate edilmediyse (deploy > migrate sırası) gönderim patlamasın.
        if (isset($extra['contact_id']) && ! \Illuminate\Support\Facades\Schema::hasColumn('email_messages', 'contact_id')) {
            unset($extra['contact_id']);
        }

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
            // Kutuya özel mailer AÇIKÇA seçiliyor.
            // Neden: Mail::to(...)->send($mailable) kullanıldığında Laravel,
            // mailable'ın kendi $mailer adını YOK SAYAR (PendingMail zaten çözülmüş
            // bir Mailer örneğiyle çalışır). Bu yüzden admin@/partnerships@ kutuları
            // için tanımlı SMTP ayarları devreye girmiyor, her şey varsayılan
            // mailer'dan gidiyordu. Mail::mailer($ad) ile gönderince doğru kutu kullanılır.
            $mailerName = $box['mailer'] ?? null;
            $sender = $mailerName && config("mail.mailers.$mailerName")
                ? Mail::mailer($mailerName)
                : Mail::mailer();

            $sender->to($toEmail, $toName)->send(new OutreachMail(
                subjectLine: $subject,
                bodyText: $body,
                fromEmail: $box['email'],
                fromName: $box['name'] ?? 'ApplyToGerman',
                mailerName: $box['mailer'] ?? null,
                replyToAddress: $box['email'], // yanıtlar aynı kutuya
            ));

            $msg->update(['status' => 'sent', 'sent_at' => now()]);

            // Kurumsal kontak defteri: "mail atıldı" + son temas tarihi.
            if ($msg->contact_id ?? null) {
                \App\Models\OutreachContact::find($msg->contact_id)?->markContacted();
            }
        } catch (\Throwable $e) {
            report($e);
            $msg->update(['status' => 'failed', 'error' => self::humanizeError($e, $box)]);
        }

        return $msg;
    }

    /**
     * SMTP hatalarını panelde okunabilir hâle getirir.
     *
     * Neden: ham Symfony Mailer mesajı ("php_network_getaddresses: getaddrinfo for
     * ... failed") panelde görünce ne yapılacağı belli olmuyordu. En sık iki sebep
     * yanlış sunucu adı ve yanlış kimlik bilgisi; ikisi de tek bir env satırıyla
     * çözülüyor, o yüzden hangi değişkene bakılacağı mesaja yazılıyor.
     */
    private static function humanizeError(\Throwable $e, ?array $box): string
    {
        $raw = $e->getMessage();

        // Hangi env değişkeni bu kutunun SMTP host'unu belirliyor?
        $hostVar = match ($box['mailer'] ?? null) {
            'mailbox_admin' => 'ADMIN_MAIL_HOST',
            'outreach'      => 'OUTREACH_MAIL_HOST',
            default         => 'MAIL_HOST',
        };
        $host = config('mail.mailers.' . ($box['mailer'] ?? 'smtp') . '.host');

        // Host hiç tanımlı değilse "çözümlenemedi" demek yanıltıcı olur: aranacak yer
        // DNS değil, .env. (Aynı anahtar .env'de iki kez geçiyorsa SON tanım kazanır —
        // dolu satırdan sonra boş bir tekrar varsa değer boşalır.)
        if (blank($host)) {
            return "SMTP sunucu adı BOŞ: .env'de {$hostVar} tanımlı değil ya da sonradan boş olarak "
                . "tekrar tanımlanmış (aynı anahtar iki kez geçiyorsa son tanım geçerlidir). "
                . "Dosyada {$hostVar} satırlarının tamamını ara. Ham hata: {$raw}";
        }

        if (str_contains($raw, 'getaddrinfo') || str_contains($raw, 'Name or service not known')) {
            return "SMTP sunucu adı çözümlenemedi: \"{$host}\". Bu ad DNS'te yok — {$hostVar} değerini düzelt "
                . "(barındırma panelindeki giden sunucu adı ya da mail.<alan-adın>). Ham hata: {$raw}";
        }

        if (str_contains($raw, 'Connection refused') || str_contains($raw, 'Connection timed out')) {
            return "SMTP sunucusuna bağlanılamadı: \"{$host}\". Ad doğru ama port/bağlantı engelli olabilir — "
                . "{$hostVar} ve port ayarını kontrol et. Ham hata: {$raw}";
        }

        if (str_contains($raw, 'Authentication') || str_contains($raw, '535')) {
            return "SMTP kimlik doğrulaması reddedildi ({$host}). Kullanıcı adı/parola hatalı olabilir. Ham hata: {$raw}";
        }

        return $raw;
    }
}
