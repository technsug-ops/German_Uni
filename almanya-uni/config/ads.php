<?php

/**
 * AlmanyaUni reklam ve affiliate yapılandırması.
 *
 * Davranış:
 *   - adsense.client_id BOŞ ise AdSense devre dışı, slot'lar görünmez
 *   - placeholder.enabled true ise AdSense yokken zarif "Reklam alanı" kutusu görünür
 *   - affiliate kart slot'ları her zaman çalışır (kullanıcıya değerli içerik)
 */

return [

    'adsense' => [
        'client_id'    => env('ADSENSE_CLIENT_ID'),         // ca-pub-XXXXXXXXX
        'enabled'      => filled(env('ADSENSE_CLIENT_ID')),
        'auto_ads'     => env('ADSENSE_AUTO_ADS', false),    // true → script tag ekle, AdSense otomatik yerleştirir
        'slots' => [
            'banner_top'    => env('ADSENSE_SLOT_BANNER_TOP'),
            'banner_bottom' => env('ADSENSE_SLOT_BANNER_BOTTOM'),
            'in_content'    => env('ADSENSE_SLOT_IN_CONTENT'),
            'sidebar'       => env('ADSENSE_SLOT_SIDEBAR'),
            'forum_top'     => env('ADSENSE_SLOT_FORUM_TOP'),
            'forum_bottom'  => env('ADSENSE_SLOT_FORUM_BOTTOM'),
        ],
    ],

    'placeholder' => [
        'enabled' => env('ADS_PLACEHOLDER_ENABLED', true),    // dev/staging için
        'label'   => 'Reklam alanı (yakında)',
    ],

    /**
     * Affiliate ortaklar — kullanıcıya değerli içerik gibi gösterilir.
     * label/desc/url/disclaimer + tip (Sperrkonto / Sigorta / Dil / Yurt)
     */
    'affiliates' => [
        'expatrio' => [
            'partner'   => 'Expatrio',
            'category'  => 'sperrkonto',
            'label'     => 'Sperrkonto (Bloke Hesap) — 5 dakikada aç',
            'desc'      => 'Almanya vize başvurusu için 11.904 € bloke hesap. Online açılış, 49 € kuruluş, 5 € aylık. Vize için Sperrkontobestätigung otomatik.',
            'cta'       => '👉 Hızlı Sperrkonto aç',
            'url'       => env('AFFILIATE_EXPATRIO_URL', 'https://www.expatrio.com'),
            'logo'      => null,
            'active'    => filled(env('AFFILIATE_EXPATRIO_URL')),
            'disclaimer' => '* Affiliate link — kullanıcıya ek ücret yansımaz.',
        ],
        'fintiba' => [
            'partner'   => 'Fintiba',
            'category'  => 'sperrkonto',
            'label'     => 'Fintiba Sperrkonto — Alman vize standardı',
            'desc'      => 'Almanya konsoloslukları tarafından tanınan Sperrkonto sağlayıcısı. Online açılış, 89 € kuruluş, 4.90 € aylık.',
            'cta'       => '👉 Fintiba ile aç',
            'url'       => env('AFFILIATE_FINTIBA_URL', 'https://www.fintiba.com'),
            'logo'      => null,
            'active'    => filled(env('AFFILIATE_FINTIBA_URL')),
            'disclaimer' => '* Affiliate link — kullanıcıya ek ücret yansımaz.',
        ],
        'mawista' => [
            'partner'   => 'Mawista',
            'category'  => 'sigorta',
            'label'     => 'Mawista — Öğrenci Sağlık Sigortası',
            'desc'      => 'Almanya vize için 12 ay sağlık sigortası, ~80 €/ay. Türk öğrenciler için en uygun fiyatlı paketler.',
            'cta'       => '👉 Sigorta teklifi al',
            'url'       => env('AFFILIATE_MAWISTA_URL', 'https://www.mawista.com'),
            'logo'      => null,
            'active'    => filled(env('AFFILIATE_MAWISTA_URL')),
            'disclaimer' => '* Affiliate link — kullanıcıya ek ücret yansımaz.',
        ],
        'care_concept' => [
            'partner'   => 'Care Concept',
            'category'  => 'sigorta',
            'label'     => 'Care Concept — Uluslararası Öğrenci Sigortası',
            'desc'      => 'Avrupa\'da yaygın özel sağlık sigortası, vize için yeterli kapsam. Esnek paketler.',
            'cta'       => '👉 Sigorta detayı',
            'url'       => env('AFFILIATE_CARECONCEPT_URL', 'https://www.care-concept.de'),
            'logo'      => null,
            'active'    => filled(env('AFFILIATE_CARECONCEPT_URL')),
            'disclaimer' => '* Affiliate link — kullanıcıya ek ücret yansımaz.',
        ],
    ],

    /**
     * İçerik-bazlı affiliate öneri kuralı.
     * Hangi sayfada hangi affiliate gösterilsin (post slug pattern → affiliate key).
     */
    'context_rules' => [
        'visa'        => ['expatrio', 'fintiba', 'mawista'],
        'sperrkonto'  => ['expatrio', 'fintiba'],
        'insurance'   => ['mawista', 'care_concept'],
        'default'     => ['expatrio', 'mawista'],
    ],

];
