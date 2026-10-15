<?php

namespace App\Support;

/**
 * OSV Faz 0 (G9): çeviri sonrası deterministik karşılaştırma.
 *
 * Kaynak ve çeviri metninden "değişmemesi gereken" parçaların çoklu kümesini çıkarır ve farkı döndürür:
 *   - urls       : http(s) bağlantıları (sondaki noktalama atılır)
 *   - dates      : GG.AA.YYYY ve ISO YYYY-MM-DD → Y-m-d
 *   - paragraphs : § atıfları ("§ 16b Abs. 3" = "§16b(3)" = "§ 16b para. 3")
 *   - amounts    : € tutarları ("45.934,20 €" = "€45,934.20" = "45934,20 EUR")
 *   - numbers    : geri kalan sayılar (1.091 = 1,091 = 1 091; 1,5 = 1.5)
 *
 * Bilerek gevşek: tek haneli çıplak tamsayılar (0–9) karşılaştırılmaz — çevirmen "2 yıl"ı "two years"
 * yazabilir (yazıyla sayı zorunlu değil). Tutar, tarih ve § atfındaki rakamlar her zaman sayılır.
 */
class TranslationInvariants
{
    public const CATEGORIES = ['urls', 'dates', 'paragraphs', 'amounts', 'numbers'];

    /**
     * Çeviri promptlarına eklenen ortak kural (TranslateContentBlocks.php / I18nFillProse.php ifadesinin genişletilmiş hâli).
     * extract() ile birebir aynı kategorileri sayar; çıktı bu kurala uymazsa çeviri kaydedilmez.
     */
    public const PROMPT_RULE = '- DO NOT translate or change: numeric values, prices and amounts (e.g. "11.904 €", "45.934,20 €" — keep the exact figures and the € sign; never round, convert currency or "update" a figure), ranges (e.g. "300-450"), dates (keep the same digits and format, e.g. "14.10.2026"), legal references (e.g. "§ 16b Abs. 3 AufenthG"), URLs and source links. You may adapt only the thousands/decimal separators to the target language.'
        . "\n" . '- Do NOT add information, numbers, deadlines or claims not present in the source. A "checked/kontrol tarihi/geprüft am" date is the date the source was checked — never turn it into a "since/from/ab/itibaren" (effective) date.';

    /** @return array<string, array<int,string>> kategori → kanonik değerler (çoklu küme, sıralı) */
    public static function extract(string $text): array
    {
        $out = array_fill_keys(self::CATEGORIES, []);
        $text = str_replace(["\u{00A0}", "\u{202F}", "\u{2009}"], ' ', $text);

        // 1) URL'ler (içlerindeki rakamlar sayı sayılmasın)
        $text = preg_replace_callback('~https?://[^\s<>"\'\)\]]+~iu', function ($m) use (&$out) {
            $out['urls'][] = rtrim($m[0], '.,;:!?*');

            return ' ';
        }, $text);

        // 2) Tarihler
        $text = preg_replace_callback('/\b(\d{4})-(\d{2})-(\d{2})\b/', function ($m) use (&$out) {
            $out['dates'][] = sprintf('%04d-%02d-%02d', $m[1], $m[2], $m[3]);

            return ' ';
        }, $text);
        $text = preg_replace_callback('/(?<![\d.,])(\d{1,2})\.(\d{1,2})\.(\d{4})(?![\d])/', function ($m) use (&$out) {
            $out['dates'][] = sprintf('%04d-%02d-%02d', $m[3], $m[2], $m[1]);

            return ' ';
        }, $text);

        // 3) § atıfları
        $text = preg_replace_callback('/§§?\s?(\d+[a-z]?)(?:\s?(?:Abs\.?|Absatz|para\.?|paragraph|fıkra)\s?(\d+)|\s?\((\d+)\))?/iu', function ($m) use (&$out) {
            $sub = ($m[2] ?? '') !== '' ? $m[2] : ($m[3] ?? '');
            $out['paragraphs'][] = '§' . strtolower($m[1]) . ($sub !== '' ? "($sub)" : '');

            return ' ';
        }, $text);

        // 4) € tutarları (sembol önde veya arkada)
        $num = '\d{1,3}(?:[., ]\d{3})+(?:[.,]\d{1,2})?|\d+(?:[.,]\d{1,2})?';
        $text = preg_replace_callback('/(?:€|EUR\b|Euro\b)\s?(' . $num . ')(?![\d])/u', function ($m) use (&$out) {
            $out['amounts'][] = '€' . self::normaliseNumber($m[1]);

            return ' ';
        }, $text);
        $text = preg_replace_callback('/(?<![\d.,])(' . $num . ')\s?(?:€|EUR\b|Euros?\b)/u', function ($m) use (&$out) {
            $out['amounts'][] = '€' . self::normaliseNumber($m[1]);

            return ' ';
        }, $text);

        // 5) Diğer sayılar
        preg_match_all('/(?<![\d.,])(?:\d{1,3}(?:[.,]\d{3})+(?:[.,]\d+)?|\d{1,3}(?: \d{3})+(?![\d.,]\d)|\d+(?:[.,]\d+)?)(?![\d])/u', $text, $m);
        foreach ($m[0] as $tok) {
            $n = self::normaliseNumber($tok);
            if (preg_match('/^\d$/', $n)) {
                continue; // tek haneli çıplak tamsayı: yazıyla yazılabilir
            }
            $out['numbers'][] = $n;
        }

        foreach ($out as &$list) {
            sort($list, SORT_STRING);
        }

        return $out;
    }

    /**
     * Ayırıcıları normalleştirir: "1.091" / "1,091" / "1 091" → "1091"; "45.934,20" / "45,934.20" → "45934.2";
     * "1,5" / "1.5" → "1.5"; "992,00" → "992".
     */
    public static function normaliseNumber(string $tok): string
    {
        $tok = str_replace(' ', '', trim($tok));
        $hasDot = str_contains($tok, '.');
        $hasComma = str_contains($tok, ',');
        if ($hasDot && $hasComma) {
            $dec = strrpos($tok, '.') > strrpos($tok, ',') ? '.' : ',';
            $thou = $dec === '.' ? ',' : '.';
            $tok = str_replace([$thou, $dec], ['', '.'], $tok);
        } elseif ($hasDot || $hasComma) {
            $sep = $hasDot ? '.' : ',';
            $parts = explode($sep, $tok);
            $thousands = count($parts) > 2 || strlen(end($parts)) === 3;
            $tok = $thousands ? implode('', $parts) : implode('.', $parts);
        }
        if (str_contains($tok, '.')) {
            $tok = rtrim(rtrim($tok, '0'), '.');
        }
        $tok = ltrim($tok, '0');

        return $tok === '' || $tok[0] === '.' ? '0' . $tok : $tok;
    }

    /**
     * Çoklu küme farkı. Boş dizi = değişmez parçalar korunmuş.
     *
     * @return array<string, array{missing: array<int,string>, extra: array<int,string>}>
     */
    public static function compare(string $source, string $translation): array
    {
        $a = self::extract($source);
        $b = self::extract($translation);
        $diff = [];
        foreach (self::CATEGORIES as $cat) {
            $missing = self::multisetMinus($a[$cat], $b[$cat]);
            $extra = self::multisetMinus($b[$cat], $a[$cat]);
            if ($missing || $extra) {
                $diff[$cat] = ['missing' => $missing, 'extra' => $extra];
            }
        }

        return $diff;
    }

    /**
     * Yalnız "kaynakta olmayan" parçalar (özet/başlık gibi kısaltılmış alanlar için: eksik serbest, uydurma yasak).
     *
     * @return array<string, array{missing: array<int,string>, extra: array<int,string>}>
     */
    public static function unexpected(string $source, string $translation): array
    {
        $a = self::extract($source);
        $b = self::extract($translation);
        $diff = [];
        foreach (self::CATEGORIES as $cat) {
            $extra = array_values(array_diff(array_unique($b[$cat]), $a[$cat]));
            if ($extra) {
                $diff[$cat] = ['missing' => [], 'extra' => $extra];
            }
        }

        return $diff;
    }

    public static function ok(string $source, string $translation): bool
    {
        return self::compare($source, $translation) === [];
    }

    /** Komut çıktısı/log için tek satır özet. */
    public static function summarize(array $diff): string
    {
        $parts = [];
        foreach ($diff as $cat => $d) {
            $bits = [];
            if ($d['missing']) {
                $bits[] = '-' . implode(' -', array_slice($d['missing'], 0, 5));
            }
            if ($d['extra']) {
                $bits[] = '+' . implode(' +', array_slice($d['extra'], 0, 5));
            }
            $parts[] = $cat . ': ' . implode(' ', $bits);
        }

        return implode('; ', $parts);
    }

    /** @param array<int,string> $a @param array<int,string> $b @return array<int,string> */
    private static function multisetMinus(array $a, array $b): array
    {
        $counts = array_count_values($b);
        $out = [];
        foreach ($a as $v) {
            if (($counts[$v] ?? 0) > 0) {
                $counts[$v]--;
            } else {
                $out[] = $v;
            }
        }

        return $out;
    }
}
