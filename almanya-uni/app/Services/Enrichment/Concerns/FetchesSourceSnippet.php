<?php

namespace App\Services\Enrichment\Concerns;

use Illuminate\Support\Facades\Http;

/**
 * Küratörlü kaynak linkinden (resmi site, iyi makale…) temiz grounding metni çeker.
 * Şehir + üni enrichment paylaşır. HTML gürültülü; nav/script atılır, sınırlı uzunluk.
 */
trait FetchesSourceSnippet
{
    protected function fetchOfficialSnippet(string $url): ?string
    {
        try {
            $res = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/120 Safari/537.36',
            ])->timeout(15)->get($url);
            if (! $res->ok()) return null;
            $html = $res->body();
        } catch (\Throwable $e) {
            return null;
        }

        // Charset normalize: kaynak UTF-8 değilse (ör. ISO-8859-1) dönüştür. Aksi halde
        // ü/ß gibi karakterler bozuk bayt olur ve AI isteği json_encode'da "Malformed
        // UTF-8" ile çöker (Gießen vakası). Content-Type header → <meta charset> sırasıyla.
        $charset = null;
        if (preg_match('/charset=["\']?([\w\-]+)/i', (string) $res->header('Content-Type'), $cm)) {
            $charset = strtoupper($cm[1]);
        } elseif (preg_match('/<meta[^>]+charset=["\']?([\w\-]+)/i', $html, $cm)) {
            $charset = strtoupper($cm[1]);
        }
        if ($charset && ! in_array($charset, ['UTF-8', 'UTF8'], true)) {
            $converted = @mb_convert_encoding($html, 'UTF-8', $charset);
            if (is_string($converted) && $converted !== '') $html = $converted;
        }

        $parts = [];
        if (preg_match('/<meta[^>]+name=["\']description["\'][^>]+content=["\']([^"\']+)/i', $html, $m)) {
            $parts[] = html_entity_decode($m[1], ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }
        $body = preg_replace('#<(script|style|nav|header|footer)[^>]*>.*?</\1>#is', ' ', $html);
        $body = preg_replace('#<[^>]+>#', ' ', $body);
        $body = html_entity_decode($body, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $body = trim(preg_replace('/\s+/u', ' ', $body));
        if ($body !== '') $parts[] = mb_substr($body, 0, 1500);

        $out = trim(implode("\n", $parts));
        $out = $this->scrubUtf8($out); // son güvenlik: kalan geçersiz baytları at
        return $out !== '' ? $out : null;
    }

    /** Geçersiz UTF-8 baytlarını temizler — grounding metni json_encode'da çökmesin. */
    protected function scrubUtf8(string $s): string
    {
        if ($s === '' || preg_match('//u', $s)) {
            return $s; // zaten geçerli UTF-8
        }
        $clean = @iconv('UTF-8', 'UTF-8//IGNORE', $s);
        return $clean !== false ? $clean : (string) preg_replace('/[^\x00-\x7F]/', '', $s);
    }

    /** Birden çok kaynağı çekip grounding metnine ekler. */
    protected function appendSourceSnippets(string $sourceText, array $sourceUrls): string
    {
        foreach ($sourceUrls as $src) {
            if ($src && ($snippet = $this->fetchOfficialSnippet($src))) {
                $sourceText .= "\n\n[Kaynak: $src]\n" . $snippet;
            }
        }
        return $sourceText;
    }
}
