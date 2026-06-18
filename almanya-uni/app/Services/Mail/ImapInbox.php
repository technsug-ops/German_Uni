<?php

namespace App\Services\Mail;

use App\Models\EmailMessage;
use Illuminate\Support\Carbon;

/**
 * partnerships@ kutusunu native ext-imap ile okur ve gelen mailleri
 * email_messages tablosuna (direction=inbound) idempotent senkronlar.
 * Yeni composer paketi YOK. ext-imap kapalıysa nazikçe hata döndürür.
 *
 * Kredansiyel SADECE config('services.imap') (env IMAP_*) üzerinden gelir.
 */
class ImapInbox
{
    /** ext-imap yüklü mü + config dolu mu? */
    public static function available(): bool
    {
        return function_exists('imap_open') && filled(config('services.imap.host'));
    }

    /** Neden kullanılamıyor? (UI mesajı için) */
    public static function unavailableReason(): ?string
    {
        if (! function_exists('imap_open')) {
            return 'Sunucuda PHP "imap" eklentisi (ext-imap) etkin değil. KAS panelinden açılmalı.';
        }
        if (blank(config('services.imap.host'))) {
            return 'IMAP ayarları eksik. Prod .env içine IMAP_HOST / IMAP_USERNAME / IMAP_PASSWORD eklenmeli.';
        }

        return null;
    }

    /**
     * Son N maili çek, email_messages'a yaz. Senkronlanan (yeni) sayısını döndürür.
     *
     * @throws \RuntimeException bağlantı/okuma hatasında
     */
    public function sync(int $limit = 40): int
    {
        if (! self::available()) {
            throw new \RuntimeException(self::unavailableReason() ?? 'IMAP kullanılamıyor.');
        }

        $cfg = config('services.imap');
        $flags = '/imap/' . ($cfg['encryption'] ? $cfg['encryption'] : '')
            . (($cfg['validate_cert'] ?? true) ? '' : '/novalidate-cert');
        $mailbox = '{' . $cfg['host'] . ':' . $cfg['port'] . $flags . '}' . ($cfg['folder'] ?? 'INBOX');

        // imap_open uyarı yerine exception fırlatsın
        $conn = @imap_open($mailbox, (string) $cfg['username'], (string) $cfg['password'], 0, 1);
        if ($conn === false) {
            throw new \RuntimeException('IMAP bağlantısı başarısız: ' . imap_last_error());
        }

        $synced = 0;

        try {
            $total = imap_num_msg($conn);
            if ($total < 1) {
                return 0;
            }

            $start = max(1, $total - $limit + 1);
            // Yeniden eskiye
            for ($i = $total; $i >= $start; $i--) {
                $overview = imap_fetch_overview($conn, (string) $i, 0)[0] ?? null;
                if (! $overview) {
                    continue;
                }

                $messageId = trim($overview->message_id ?? '') ?: ('imap-' . ($overview->uid ?? $i));

                // Idempotent: aynı message_id zaten varsa atla
                $exists = EmailMessage::where('direction', 'inbound')
                    ->where('message_id', $messageId)->exists();
                if ($exists) {
                    continue;
                }

                $fromEmail = '';
                $fromName = null;
                if (! empty($overview->from)) {
                    $addrs = imap_rfc822_parse_adrlist($overview->from, $cfg['host']);
                    if (! empty($addrs[0])) {
                        $fromEmail = ($addrs[0]->mailbox ?? '') . '@' . ($addrs[0]->host ?? '');
                        $fromName = isset($addrs[0]->personal) ? $this->decode($addrs[0]->personal) : null;
                    }
                }

                EmailMessage::create([
                    'direction'  => 'inbound',
                    'to_email'   => (string) $cfg['username'],
                    'from_email' => $fromEmail ?: 'unknown',
                    'to_name'    => $fromName,                     // gönderen adı (görüntüleme kolaylığı)
                    'subject'    => $this->decode($overview->subject ?? '(konu yok)'),
                    'body'       => $this->extractBody($conn, $i),
                    'status'     => ($overview->seen ?? false) ? 'sent' : 'queued', // okundu/okunmadı işareti
                    'message_id' => $messageId,
                    'sent_at'    => isset($overview->date) ? $this->parseDate($overview->date) : null,
                ]);

                $synced++;
            }
        } finally {
            imap_close($conn);
        }

        return $synced;
    }

    /** MIME-encoded header (=?UTF-8?...) çöz. */
    private function decode(string $text): string
    {
        $out = '';
        foreach (imap_mime_header_decode($text) as $part) {
            $charset = strtoupper($part->charset);
            $out .= ($charset === 'DEFAULT' || $charset === 'US-ASCII')
                ? $part->text
                : (@mb_convert_encoding($part->text, 'UTF-8', $charset) ?: $part->text);
        }

        return $out;
    }

    /** text/plain gövdeyi çek + transfer-encoding çöz. */
    private function extractBody($conn, int $msgNo): string
    {
        $structure = imap_fetchstructure($conn, $msgNo);

        // multipart ise text/plain parçasını bul
        if (! empty($structure->parts)) {
            foreach ($structure->parts as $idx => $part) {
                if (strtoupper($part->subtype ?? '') === 'PLAIN') {
                    $raw = imap_fetchbody($conn, $msgNo, (string) ($idx + 1));

                    return $this->decodeBody($raw, $part->encoding ?? 0);
                }
            }
            // text/plain yoksa ilk parçayı al (genelde HTML)
            $raw = imap_fetchbody($conn, $msgNo, '1');

            return strip_tags($this->decodeBody($raw, $structure->parts[0]->encoding ?? 0));
        }

        // tek parçalı
        $raw = imap_body($conn, $msgNo);

        return $this->decodeBody($raw, $structure->encoding ?? 0);
    }

    /** IMAP transfer-encoding sabitleri: 3=base64, 4=quoted-printable. */
    private function decodeBody(string $raw, int $encoding): string
    {
        $decoded = match ($encoding) {
            3 => base64_decode($raw),
            4 => quoted_printable_decode($raw),
            default => $raw,
        };

        return trim($decoded);
    }

    private function parseDate(string $date): ?string
    {
        try {
            return Carbon::parse($date)->toDateTimeString();
        } catch (\Throwable) {
            return null;
        }
    }
}
