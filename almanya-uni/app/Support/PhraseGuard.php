<?php

namespace App\Support;

/**
 * OSV Faz 0: birebir yanlış ifade (deny-list) kontrolü — kelime filtresi değil, bilinen yanlış cümle/rakam parçası.
 * Hem `OfficialKnownWrongRegressionTest` (database/data/osv/known_wrong.json) hem `chat:eval` (must_contain /
 * must_not_contain) aynı eşleştirmeyi kullanır.
 *
 * Eşleştirme büyük/küçük harf duyarsızdır; boşluklar (NBSP dahil) tek boşluğa indirgenir.
 * Tarihsel bağlam serbesttir: bir isabetin hemen önündeki pencerede (varsayılan 40 karakter) `allow_context`
 * işaretlerinden biri varsa ("eskiden", "früher", "previously", "2024" …) isabet sayılmaz.
 */
class PhraseGuard
{
    public static function normalise(string $text): string
    {
        $text = str_replace(["\u{00A0}", "\u{202F}", "\u{2009}"], ' ', $text);

        return preg_replace('/[ \t]+/u', ' ', $text) ?? $text;
    }

    /**
     * @param array<int,string> $allowContext
     * @return array<int,string> isabet bağlamları (boş = temiz)
     */
    public static function hits(string $text, string $phrase, array $allowContext = [], int $window = 40): array
    {
        $text = self::normalise($text);
        $phrase = self::normalise($phrase);
        if ($phrase === '') {
            return [];
        }
        $hits = [];
        $offset = 0;
        while (($pos = mb_stripos($text, $phrase, $offset)) !== false) {
            $before = mb_substr($text, max(0, $pos - $window), min($pos, $window));
            $allowed = false;
            foreach ($allowContext as $marker) {
                if ($marker !== '' && mb_stripos($before, self::normalise($marker)) !== false) {
                    $allowed = true;
                    break;
                }
            }
            if (! $allowed) {
                $hits[] = trim(mb_substr($text, max(0, $pos - 50), mb_strlen($phrase) + 100));
            }
            $offset = $pos + max(1, mb_strlen($phrase));
        }

        return $hits;
    }

    /**
     * must_not_contain: her öğe bir ifade (string) ya da {phrase, allow_context?} dizisi.
     *
     * @return array<int,string> bulunan yasak ifadeler
     */
    public static function forbidden(string $text, array $phrases, array $allowContext = []): array
    {
        $found = [];
        foreach ($phrases as $p) {
            $phrase = is_array($p) ? (string) ($p['phrase'] ?? '') : (string) $p;
            $allow = is_array($p) ? array_merge($allowContext, $p['allow_context'] ?? []) : $allowContext;
            if (self::hits($text, $phrase, $allow) !== []) {
                $found[] = $phrase;
            }
        }

        return $found;
    }

    /**
     * must_contain: her öğe bir ifade ya da alternatifler listesi (biri yeterli).
     *
     * @return array<int,string> eksik gereksinimler ("a | b" biçiminde)
     */
    public static function missing(string $text, array $required): array
    {
        $text = self::normalise($text);
        $missing = [];
        foreach ($required as $req) {
            $alts = array_values(array_filter(array_map('strval', (array) $req), fn ($a) => $a !== ''));
            $ok = false;
            foreach ($alts as $alt) {
                if (mb_stripos($text, self::normalise($alt)) !== false) {
                    $ok = true;
                    break;
                }
            }
            if ($alts && ! $ok) {
                $missing[] = implode(' | ', $alts);
            }
        }

        return $missing;
    }
}
