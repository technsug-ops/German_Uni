<?php

namespace App\Support;

use Illuminate\Support\Collection;

/**
 * SSS konu sayfasını alt-konulara böler (accordion gruplaması için).
 *
 * intent alanı soru-tipi (Nasıl/Ne kadar) ve seed kaynağı (community) taşıdığı
 * için kategori olarak işe yaramaz. Bunun yerine soru metnindeki anahtar
 * kelimelerden (TR/EN/DE + dile-bağımsız özel adlar: Sperrkonto, 81a, Fintiba)
 * render anında alt-konu çıkarırız. Heuristik: ilk eşleşen kategori kazanır
 * (özel → genel sıralı), hiçbiri tutmazsa "Diğer".
 *
 * Tek-konuya özgü değil; tüm SSS konularında çalışır. Bir konuda eşleşmeyen
 * kategori (ör. Konut konusunda "Sperrkonto") boş kalır → gösterilmez.
 */
class FaqCategorizer
{
    /**
     * Sıra = eşleşme önceliği (yukarıdaki kazanır). Anahtarlar __() ile çevrilir.
     * Regex'ler mb_strtolower'lanmış soru metnine /u ile uygulanır.
     */
    private const RULES = [
        'Health insurance'              => 'sigorta|insurance|krankenversicher|versicherung',
        'Blocked account (Sperrkonto)'  => 'sperrkonto|bloke hesap|blocked account|fintiba|expatrio|bloke para',
        'Pre-approval (Vorabzustimmung)' => 'ön onay|on onay|vorabzustimmung|81\s?a|pre-?approval',
        'Language course & certificate' => 'dil kursu|dil sertifika|dil seviye|dil yeterlilik|dil testi|dil sınav|sprachkurs|sprachzertifikat|sprachzeugnis|language course|language certificate|language test',
        'Interview'                     => 'mülakat|mulakat|görüşme|gorusme|interview|gespräch|gesprach',
        'Translation & certification'   => 'tercüme|tercume|yeminli|apostil|noter|notar|übersetzung|ubersetzung|beglaubig|sworn translation',
        'Family'                        => 'eşin|eşim|eşini|çocuğ|çocuk|cocuk|velayet|aile birleş|family|spouse|child|familie|ehepartner|custody',
        'Rejection & reapplication'     => 'reddedil|\bred\b|itiraz|yeniden başvur|tekrar başvur|uzatma|uzat\b|ablehnung|rejection|refus|appeal|extension|verlänger',
        'Appointment & processing time' => 'randevu|appointment|termin|kaç gün|kaç hafta|kaç ay|kaç yıl|ne kadar sür|sürede|işlem süresi|teslim|sonuçlan|sonuç ne kadar|bekleme|geçerli|gültig|valid|processing time|how long|dauer|bearbeitungszeit',
        'Application & documents'       => 'evrak|belge|document|unterlagen|\bform\b|başvur|basvur|apply|antrag|nasıl alın|nasıl yapıl|how to|gerekli|chancenkarte|fırsat kartı',
    ];

    public const OTHER = 'Other questions';

    /**
     * Kategori → ilgili araç eşlemesi (FAQ ↔ Tool çapraz bağlama / funnel).
     * Anahtar = kategori (RULES anahtarıyla aynı), değer = [route, label(çevrilir), locales?].
     * locales doluysa CTA yalnızca o dillerde gösterilir (ör. iData = tr).
     */
    private const TOOL_MAP = [
        'Blocked account (Sperrkonto)'  => ['route' => 'tools.blocked-account', 'label' => 'Blocked Account (Sperrkonto) Finder'],
        'Health insurance'              => ['route' => 'tools.health-insurance', 'label' => 'Health Insurance Comparison'],
        'Language course & certificate' => ['route' => 'tools.language-certificates', 'label' => 'Language Certificates'],
        'Appointment & processing time' => ['route' => 'tools.visa-appointment', 'label' => 'Visa Appointment (iData)', 'locales' => ['tr']],
    ];

    /** Bir kategori için ilgili aracı döndürür (varsa), yoksa null. */
    public static function toolFor(string $categoryKey): ?array
    {
        return self::TOOL_MAP[$categoryKey] ?? null;
    }

    /** Bir sorunun kategori anahtarını döndürür (çevrilmemiş; etiket için __() çağırın). */
    public static function keyFor(?string $question): string
    {
        $q = mb_strtolower((string) $question, 'UTF-8');

        foreach (self::RULES as $key => $pattern) {
            if (preg_match('/' . $pattern . '/u', $q)) {
                return $key;
            }
        }

        return self::OTHER;
    }

    /**
     * Bir FAQ için kategori anahtarı: admin elle seçtiyse (geçerli bir anahtarsa)
     * o kazanır; yoksa soru metninden heuristik. Override sınır sorularını düzeltir.
     */
    public static function resolveKey($faq): string
    {
        $override = is_object($faq) ? ($faq->category ?? null) : null;
        if ($override && in_array($override, self::validKeys(), true)) {
            return $override;
        }

        return self::keyFor(is_object($faq) ? ($faq->question ?? '') : (string) $faq);
    }

    /** Geçerli kategori anahtarları (RULES + Diğer). */
    public static function validKeys(): array
    {
        return [...array_keys(self::RULES), self::OTHER];
    }

    /** Filament select için [anahtar => çevrili etiket]. */
    public static function categoryOptions(): array
    {
        $opts = [];
        foreach (self::validKeys() as $key) {
            $opts[$key] = __($key);
        }

        return $opts;
    }

    /**
     * FAQ koleksiyonunu kategoriye göre gruplar. Sadece dolu gruplar, RULES
     * sırasıyla; "Diğer" daima sonda. Dönüş: [['key','label','faqs'], ...].
     */
    public static function group(Collection $faqs): array
    {
        $buckets = $faqs->groupBy(fn ($faq) => self::resolveKey($faq));

        $order = array_keys(self::RULES);
        $order[] = self::OTHER;

        $groups = [];
        foreach ($order as $key) {
            if ($buckets->has($key) && $buckets[$key]->isNotEmpty()) {
                $groups[] = [
                    'key'   => $key,
                    'label' => __($key),
                    'faqs'  => $buckets[$key],
                ];
            }
        }

        return $groups;
    }
}
