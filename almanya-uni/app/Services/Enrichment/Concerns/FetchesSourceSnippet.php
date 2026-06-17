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
        return $out !== '' ? $out : null;
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
