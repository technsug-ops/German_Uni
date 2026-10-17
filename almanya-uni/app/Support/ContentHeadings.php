<?php

namespace App\Support;

/**
 * SEO Recovery P1-1 / Paket B — yazı gövdesinde H1 bırakmaz; sayfanın tek H1'i şablondaki başlıktır.
 * Yalnız render katmanı: kayıtlı content_md / content_html değişmez.
 *
 * - Gövdenin en başındaki H1 başlıkla aynı anlamdaysa (yıl ve noktalama hariç kelime örtüşmesi) kaldırılır.
 * - Diğer her H1 (farklı anlamlı öndeki H1 dahil) aynı içerikle H2'ye çevrilir.
 *   Çevrilen başlığa önceden kimlik verilir; böylece TocBuilder'ın sıra numaralı kimlikleri (toc-hN)
 *   ve tekrar eden slug ekleri kaymaz, mevcut çapalar bozulmaz.
 * - <pre>/<code> içindeki metin dokunulmaz (HTML'de kod bloğu kaçışlıdır; ayrıca pre içi atlanır).
 */
class ContentHeadings
{
    /** Kelime örtüşmesi (Jaccard) bu değerin altındaysa öndeki H1 "farklı bölüm başlığı" sayılır. */
    private const SAME_TITLE_MIN_OVERLAP = 0.3;

    public static function normalizeH1(?string $html, ?string $title): string
    {
        $html = (string) $html;
        if ($html === '' || stripos($html, '<h1') === false) {
            return $html;
        }

        // Kod blokları geçici olarak yer tutucuya alınır.
        $blocks = [];
        $work = preg_replace_callback('/<pre\b.*?<\/pre>/is', function ($m) use (&$blocks) {
            $blocks[] = $m[0];

            return "\x00PRE" . (count($blocks) - 1) . "\x00";
        }, $html) ?? $html;

        $n = 0;
        $work = preg_replace_callback('/<h1\b([^>]*)>(.*?)<\/h1>\s*/is', function ($m) use (&$n, $title, $work) {
            $n++;
            $inner = $m[2];
            // Yalnız gövdenin en başındaki ilk H1 kaldırılabilir.
            if ($n === 1 && self::isLeading($work, $m[0]) && self::sameAsTitle(strip_tags($inner), (string) $title)) {
                return '';
            }
            $attrs = $m[1];
            if (! preg_match('/\bid\s*=/i', $attrs)) {
                $attrs = ' id="post-heading-' . $n . '"' . $attrs;
            }

            return "<h2{$attrs}>{$inner}</h2>\n";
        }, $work) ?? $work;

        return preg_replace_callback('/\x00PRE(\d+)\x00/', fn ($m) => $blocks[(int) $m[1]], $work) ?? $work;
    }

    /** Eşleşmenin önünde görünür metin yoksa (yalnız boşluk/yorum) gövdenin başındadır. */
    private static function isLeading(string $html, string $match): bool
    {
        $pos = strpos($html, $match);
        if ($pos === false) {
            return false;
        }
        $before = preg_replace('/<!--.*?-->/s', '', substr($html, 0, $pos)) ?? '';

        return trim(strip_tags($before)) === '';
    }

    public static function sameAsTitle(string $heading, string $title): bool
    {
        $a = self::words($heading);
        $b = self::words($title);
        if ($a === [] || $b === []) {
            return false;
        }
        if ($a === $b || ! array_diff($a, $b) || ! array_diff($b, $a)) {
            return true;
        }
        $overlap = count(array_intersect($a, $b)) / count(array_unique(array_merge($a, $b)));

        return $overlap >= self::SAME_TITLE_MIN_OVERLAP;
    }

    /** Küçük harf, aksansız, yıl ve noktalama atılmış, tekil kelime kümesi (≥2 harf). */
    private static function words(string $s): array
    {
        $s = html_entity_decode($s, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $s = str_replace(['İ', 'I', 'ı'], ['i', 'i', 'i'], $s);
        $s = mb_strtolower($s, 'UTF-8');
        if (class_exists(\Normalizer::class)) {
            $s = preg_replace('/\p{Mn}+/u', '', \Normalizer::normalize($s, \Normalizer::FORM_D) ?: $s) ?? $s;
        }
        $s = preg_replace('/\b(19|20)\d\d\b/u', ' ', $s) ?? $s;
        $s = preg_replace('/[^\p{L}\p{N}]+/u', ' ', $s) ?? $s;
        $w = array_filter(explode(' ', $s), fn ($x) => mb_strlen($x) >= 2);

        return array_values(array_unique($w));
    }
}
