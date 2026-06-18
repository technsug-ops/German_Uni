<?php

namespace App\Services\Mail;

use App\Models\EmailMessage;
use Illuminate\Support\Carbon;

/**
 * Çok-kutulu IMAP gelen kutusu. config('services.mailboxes') içindeki her kutunun
 * imap ayarı varsa native ext-imap ile okunur ve email_messages'a (direction=inbound,
 * mailbox=<key>) idempotent senkronlanır. Yeni composer paketi YOK. ext-imap
 * kapalıysa nazikçe hata döndürür.
 */
class ImapInbox
{
    /** ext-imap yüklü mü + en az bir kutuda imap host var mı? */
    public static function available(): bool
    {
        if (! function_exists('imap_open')) {
            return false;
        }
        foreach (config('services.mailboxes', []) as $box) {
            if (filled($box['imap']['host'] ?? null) && filled($box['imap']['username'] ?? null)) {
                return true;
            }
        }

        return false;
    }

    public static function unavailableReason(): ?string
    {
        if (! function_exists('imap_open')) {
            return 'Sunucuda PHP "imap" eklentisi (ext-imap) etkin değil. KAS panelinden açılmalı.';
        }
        foreach (config('services.mailboxes', []) as $box) {
            if (filled($box['imap']['host'] ?? null) && filled($box['imap']['username'] ?? null)) {
                return null;
            }
        }

        return 'IMAP ayarları eksik. Prod .env içine ilgili IMAP_* anahtarları eklenmeli.';
    }

    /** Tüm kutuları çek. Toplam yeni mail sayısını döndürür. */
    public function sync(int $limit = 40): int
    {
        if (! function_exists('imap_open')) {
            throw new \RuntimeException(self::unavailableReason() ?? 'IMAP kullanılamıyor.');
        }

        $total = 0;
        foreach (config('services.mailboxes', []) as $key => $box) {
            $cfg = $box['imap'] ?? [];
            if (blank($cfg['host'] ?? null) || blank($cfg['username'] ?? null)) {
                continue;
            }
            $total += $this->syncMailbox((string) $key, $box, $cfg, $limit);
        }

        return $total;
    }

    /** Tek kutu çek. */
    private function syncMailbox(string $key, array $box, array $cfg, int $limit): int
    {
        $flags = '/imap/' . ($cfg['encryption'] ? $cfg['encryption'] : '')
            . (($cfg['validate_cert'] ?? true) ? '' : '/novalidate-cert');
        $mailbox = '{' . $cfg['host'] . ':' . ($cfg['port'] ?? 993) . $flags . '}' . ($cfg['folder'] ?? 'INBOX');

        $conn = @imap_open($mailbox, (string) $cfg['username'], (string) $cfg['password'], 0, 1);
        if ($conn === false) {
            throw new \RuntimeException("IMAP bağlantısı başarısız ({$key}): " . imap_last_error());
        }

        $toEmail = $box['email'] ?? $cfg['username'];
        $synced = 0;

        try {
            $count = imap_num_msg($conn);
            if ($count < 1) {
                return 0;
            }

            $start = max(1, $count - $limit + 1);
            for ($i = $count; $i >= $start; $i--) {
                $overview = imap_fetch_overview($conn, (string) $i, 0)[0] ?? null;
                if (! $overview) {
                    continue;
                }

                $messageId = trim($overview->message_id ?? '') ?: ('imap-' . $key . '-' . ($overview->uid ?? $i));

                $exists = EmailMessage::where('direction', 'inbound')
                    ->where('mailbox', $key)
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
                    'mailbox'    => $key,
                    'to_email'   => (string) $toEmail,
                    'from_email' => $fromEmail ?: 'unknown',
                    'to_name'    => $fromName,
                    'subject'    => $this->decode($overview->subject ?? '(konu yok)'),
                    'body'       => $this->extractBody($conn, $i),
                    'status'     => ($overview->seen ?? false) ? 'sent' : 'queued',
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

        if (! empty($structure->parts)) {
            foreach ($structure->parts as $idx => $part) {
                if (strtoupper($part->subtype ?? '') === 'PLAIN') {
                    $raw = imap_fetchbody($conn, $msgNo, (string) ($idx + 1));

                    return $this->decodeBody($raw, $part->encoding ?? 0);
                }
            }
            $raw = imap_fetchbody($conn, $msgNo, '1');

            return strip_tags($this->decodeBody($raw, $structure->parts[0]->encoding ?? 0));
        }

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
