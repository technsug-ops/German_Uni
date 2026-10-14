<?php

namespace App\Support;

/**
 * İçerik metninden emojiyi anlamı koruyarak kaldırır (2026-10-14, docs/AI_VISUAL_AUDIT_2026-10-14.md §2).
 *
 * Kurallar (Markdown satır bazında):
 *  - Satır başı ✅ ⚠️ 💡 ⚖️ vb.: emoji kalkar. Art arda ≥2 böyle satır bir madde listesine dönüşür; tek satır paragraf kalır.
 *    "❌ **Nachteile:**" gibi ":" ile biten başlık satırı liste maddesi olmaz.
 *  - Aynı blokta hem ✅ hem ❌ varsa anlam kaybolmasın diye maddeler tek renkli ✓ / ✗ işaretini taşır.
 *  - Tablo hücresi: yalnız ✅ → Evet/Yes/Ja, yalnız ❌ → Hayır/No/Nein; ★★★★★ → 5/5; diğer emojiler silinir.
 *  - Diğer tüm emojiler (satır içi, başlık, bayrak) silinir. Oklar (→ ↔) ve ✓ ✗ tipografik işaretler emoji sayılmaz.
 * İdempotent: emojisiz metin aynen döner.
 */
class EmojiCleaner
{
    /** Emoji karakterleri (+ varyasyon seçici, ZWJ, ten rengi, bayrak harfleri). Oklar ve ✓✗ hariç. */
    private const EMOJI = '(?:[\x{1F300}-\x{1FAFF}\x{1F000}-\x{1F2FF}\x{2600}-\x{2712}\x{2714}\x{2716}\x{2718}-\x{27BF}\x{2B50}\x{2B55}\x{231A}\x{231B}\x{23E9}-\x{23FA}\x{2B1B}\x{2B1C}\x{3030}\x{303D}\x{3297}\x{3299}\x{24C2}\x{2139}][\x{FE0F}\x{FE0E}\x{200D}\x{1F3FB}-\x{1F3FF}]*)+';

    private const YES = ['tr' => 'Evet', 'en' => 'Yes', 'de' => 'Ja'];

    private const NO = ['tr' => 'Hayır', 'en' => 'No', 'de' => 'Nein'];

    public static function hasEmoji(?string $s): bool
    {
        return $s !== null && preg_match('/' . self::EMOJI . '/u', $s) === 1;
    }

    /** Tek satırlık alanlar (başlık, özet, meta, soru): emojiyi sil, boşlukları toparla. */
    public static function cleanInline(?string $s): ?string
    {
        if ($s === null || ! self::hasEmoji($s)) {
            return $s;
        }
        $out = preg_replace('/\s*' . self::EMOJI . '\s*/u', ' ', $s);

        return trim(preg_replace('/ {2,}/', ' ', $out));
    }

    public static function cleanMarkdown(?string $md, string $locale = 'en'): ?string
    {
        if ($md === null || ! self::hasEmoji($md)) {
            return $md;
        }
        $nl = str_contains($md, "\r\n") ? "\r\n" : "\n";
        $lines = preg_split('/\R/u', $md);
        $lead = '/^(\s*)((?:[-*+]|\d+[.)])\s+)?(\*\*|__)?\s*(' . self::EMOJI . ')\s*/u';

        // Bloklar: satır başında emoji taşıyan ardışık satırlar.
        $info = [];
        foreach ($lines as $i => $l) {
            $info[$i] = (! str_starts_with(ltrim($l), '|') && preg_match($lead, $l, $m)) ? $m : null;
        }

        $out = [];
        $n = count($lines);
        for ($i = 0; $i < $n; $i++) {
            $l = $lines[$i];
            if (str_starts_with(ltrim($l), '|')) {
                $out[] = self::cleanTableRow($l, $locale);
                continue;
            }
            if ($info[$i] === null) {
                $out[] = self::stripInline($l);
                continue;
            }
            // Bloğu topla.
            $j = $i;
            while ($j + 1 < $n && $info[$j + 1] !== null) {
                $j++;
            }
            $block = array_slice($lines, $i, $j - $i + 1, true);
            $emojis = array_map(fn ($k) => self::base($info[$k][4]), array_keys($block));
            $mixed = in_array('✅', $emojis, true) && in_array('❌', $emojis, true);
            $items = array_filter(array_keys($block), fn ($k) => ! self::isLabel($lines[$k], $info[$k]));
            $asList = count($items) >= 2;

            foreach ($block as $k => $line) {
                $m = $info[$k];
                $indent = $m[1];
                $marker = $m[2] ?? '';
                $bold = $m[3] ?? '';
                $rest = self::stripInline(substr($line, strlen($m[0])));
                $text = $bold . $rest;
                $e = self::base($m[4]);
                $sign = $mixed ? ($e === '✅' ? '✓ ' : ($e === '❌' ? '✗ ' : '')) : '';
                if (self::isLabel($line, $m)) {
                    $out[] = $indent . $text;               // başlık satırı: madde değil
                } elseif ($marker !== '') {
                    $out[] = $indent . $marker . $sign . $text; // zaten liste maddesi
                } elseif ($asList) {
                    $out[] = $indent . '- ' . $sign . $text;
                } else {
                    $out[] = $indent . $sign . $text;
                }
            }
            $i = $j;
        }

        return implode($nl, $out);
    }

    private static function base(string $e): string
    {
        return preg_replace('/[\x{FE0F}\x{FE0E}]/u', '', $e);
    }

    /** "❌ **Nachteile:**", "⚠️ **Dikkat:**" gibi ":" ile biten kısa başlık satırı. */
    private static function isLabel(string $line, array $m): bool
    {
        $rest = trim(preg_replace('/' . self::EMOJI . '/u', '', substr($line, strlen($m[0]))));
        $plain = trim(str_replace(['**', '__'], '', $rest));

        return ($m[2] ?? '') === '' && $plain !== '' && str_ends_with($plain, ':') && mb_strlen($plain) <= 60;
    }

    private static function stripInline(string $l): string
    {
        if (! self::hasEmoji($l)) {
            return $l;
        }
        // Başlık "## 🎓 Başlık" ve "**💡 Pratik öneri:**" kalıpları dahil: emoji + çevresindeki tek boşluk.
        $l = preg_replace('/(^|(?<=[\s*_(#>|]))' . self::EMOJI . '\s?/u', '', $l);
        $l = preg_replace('/\s?' . self::EMOJI . '/u', '', $l);

        return preg_replace('/(?<=\S) {2,}(?=\S)/', ' ', $l);
    }

    private static function cleanTableRow(string $row, string $locale): string
    {
        if (! self::hasEmoji($row) && ! str_contains($row, '★')) {
            return $row;
        }
        $yes = self::YES[$locale] ?? self::YES['en'];
        $no = self::NO[$locale] ?? self::NO['en'];
        $cells = explode('|', $row);
        // Satırda ❌ ile karşıtlık varsa metinli ✅ hücresi de "Evet –" ile anlamını korur.
        $contrast = str_contains($row, '❌');
        foreach ($cells as $c => $cell) {
            $t = trim($cell);
            if ($t === '') {
                continue;
            }
            $lead = preg_match('/^(\*\*)?\s*(' . self::EMOJI . ')/u', $t, $m) ? self::base($m[2]) : null;
            if (preg_match('/^★+☆*$/u', $t)) {
                $new = mb_substr_count($t, '★') . '/5';
            } else {
                $stripped = trim(self::stripInline($t));
                $strippedPlain = trim(str_replace(['**', '__'], '', $stripped));
                if ($strippedPlain === '' && $lead === '✅') {
                    $new = $yes;
                } elseif ($strippedPlain === '' && $lead === '❌') {
                    $new = $no;
                } elseif ($lead === '❌') {
                    $new = $no . ' – ' . $stripped;
                } elseif ($lead === '✅' && ($contrast || str_starts_with($strippedPlain, '('))) {
                    $new = $yes . (str_starts_with($strippedPlain, '(') ? ' ' : ' – ') . $stripped;
                } else {
                    $new = $stripped;
                }
            }
            $cells[$c] = ' ' . $new . ' ';
        }

        return implode('|', $cells);
    }
}
