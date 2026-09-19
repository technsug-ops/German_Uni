<?php

/**
 * AlmanyaUni reklam ve affiliate yapılandırması.
 *
 * Slot önceliği (components/ad-slot.blade.php):
 *   1) Affiliate kartı  — içerik-uyumlu sponsor, premium üyelerde de kalır
 *   2) AdSense          — client_id + slot ID dolu VE çerez onayı verilmişse
 *   3) "Reklam Ver"     — satılmamış envanteri boş bırakmak yerine davet kartı
 *
 * ÖNEMLİ (i18n): affiliate metinleri dile göre seçilir. Yeni bir ortak eklerken
 * tr/de/en üçünü de doldur — eksik dil 'en'e, o da yoksa 'tr'ye düşer, yani
 * Almanca sayfada Türkçe metin çıkma ihtimali yalnızca sen boş bırakırsan doğar.
 */

return [

    'adsense' => [
        'client_id'    => env('ADSENSE_CLIENT_ID'),         // ca-pub-XXXXXXXXXXXXXXXX
        'enabled'      => filled(env('ADSENSE_CLIENT_ID')),
        'auto_ads'     => env('ADSENSE_AUTO_ADS', false),    // true → script tag ekle, AdSense otomatik yerleştirir
        // AEA/İngiltere trafiğine reklam göstermek için Google sertifikalı bir onay
        // mekanizması (CMP) şart. Site kendi çerez banner'ını Consent Mode v2 ile
        // kullanıyor; AdSense panelindeki "Privacy & messaging" mesajı da açılmalı.
        // Bu bayrak açıkken onay verilmeden AdSense script'i basılmaz.
        'require_consent' => env('ADSENSE_REQUIRE_CONSENT', true),
        'slots' => [
            'banner_top'    => env('ADSENSE_SLOT_BANNER_TOP'),
            'banner_bottom' => env('ADSENSE_SLOT_BANNER_BOTTOM'),
            'in_content'    => env('ADSENSE_SLOT_IN_CONTENT'),
            'sidebar'       => env('ADSENSE_SLOT_SIDEBAR'),
            'forum_top'     => env('ADSENSE_SLOT_FORUM_TOP'),
            'forum_bottom'  => env('ADSENSE_SLOT_FORUM_BOTTOM'),
        ],
    ],

    /**
     * Satılmamış envanter: boş kutu yerine "bu alanı kiralayın" daveti.
     * Metinler __() ile çevrilir (lang/tr.json + lang/de.json).
     */
    'house' => [
        'enabled' => env('ADS_HOUSE_ENABLED', true),
    ],

    /**
     * Doğrudan reklam satışı için envanter tanımı — /advertise sayfası bunu okur.
     * Fiyat bilinçli olarak KOD'da tutulmuyor; teklif e-posta ile veriliyor.
     */
    'inventory' => [
        'placements' => [
            'banner_top'    => ['label' => 'Leaderboard (top of article)',   'size' => '728×90 / responsive'],
            'in_content'    => ['label' => 'In-content (mid article)',       'size' => '336×280 / responsive'],
            'sidebar'       => ['label' => 'Sidebar (desktop)',              'size' => '300×600'],
            'banner_bottom' => ['label' => 'Footer banner',                  'size' => '728×90 / responsive'],
        ],
        'contact_email' => env('ADS_CONTACT_EMAIL', 'partnerships@applytogerman.com'),
    ],

    /**
     * Affiliate ortaklar — kullanıcıya değerli içerik gibi gösterilir.
     * text[locale] => label / desc / cta / disclaimer
     */
    'affiliates' => [

        'expatrio' => [
            'partner'  => 'Expatrio',
            'category' => 'sperrkonto',
            'url'      => env('AFFILIATE_EXPATRIO_URL', 'https://www.expatrio.com'),
            'logo'     => null,
            'active'   => filled(env('AFFILIATE_EXPATRIO_URL')),
            'text' => [
                'tr' => [
                    'label'      => 'Sperrkonto (Bloke Hesap) — 5 dakikada aç',
                    'desc'       => 'Almanya vize başvurusu için 11.904 € bloke hesap. Online açılış, 49 € kuruluş, 5 € aylık. Vize için Sperrkontobestätigung otomatik gelir.',
                    'cta'        => 'Hızlı Sperrkonto aç',
                    'disclaimer' => '* Affiliate link — kullanıcıya ek ücret yansımaz.',
                ],
                'de' => [
                    'label'      => 'Sperrkonto in wenigen Minuten eröffnen',
                    'desc'       => 'Sperrkonto über 11.904 € für den Visumantrag. Online-Eröffnung, 49 € Einrichtung, 5 € monatlich. Die Sperrkontobestätigung kommt automatisch.',
                    'cta'        => 'Sperrkonto eröffnen',
                    'disclaimer' => '* Affiliate-Link — für dich entstehen keine Mehrkosten.',
                ],
                'en' => [
                    'label'      => 'Open a blocked account in minutes',
                    'desc'       => 'A €11,904 blocked account for your German visa application. Online setup, €49 opening fee, €5 per month, with the confirmation issued automatically.',
                    'cta'        => 'Open a blocked account',
                    'disclaimer' => '* Affiliate link — no extra cost to you.',
                ],
            ],
        ],

        'fintiba' => [
            'partner'  => 'Fintiba',
            'category' => 'sperrkonto',
            'url'      => env('AFFILIATE_FINTIBA_URL', 'https://www.fintiba.com'),
            'logo'     => null,
            'active'   => filled(env('AFFILIATE_FINTIBA_URL')),
            'text' => [
                'tr' => [
                    'label'      => 'Fintiba Sperrkonto — Alman vize standardı',
                    'desc'       => 'Almanya konsoloslukları tarafından tanınan Sperrkonto sağlayıcısı. Online açılış, 89 € kuruluş, 4,90 € aylık.',
                    'cta'        => 'Fintiba ile aç',
                    'disclaimer' => '* Affiliate link — kullanıcıya ek ücret yansımaz.',
                ],
                'de' => [
                    'label'      => 'Fintiba Sperrkonto — von Auslandsvertretungen anerkannt',
                    'desc'       => 'Anbieter für Sperrkonten, den deutsche Auslandsvertretungen anerkennen. Online-Eröffnung, 89 € Einrichtung, 4,90 € monatlich.',
                    'cta'        => 'Mit Fintiba eröffnen',
                    'disclaimer' => '* Affiliate-Link — für dich entstehen keine Mehrkosten.',
                ],
                'en' => [
                    'label'      => 'Fintiba blocked account — recognised by German missions',
                    'desc'       => 'A blocked account provider recognised by German missions abroad. Online setup, €89 opening fee, €4.90 per month.',
                    'cta'        => 'Open with Fintiba',
                    'disclaimer' => '* Affiliate link — no extra cost to you.',
                ],
            ],
        ],

        'mawista' => [
            'partner'  => 'Mawista',
            'category' => 'sigorta',
            'url'      => env('AFFILIATE_MAWISTA_URL', 'https://www.mawista.com'),
            'logo'     => null,
            'active'   => filled(env('AFFILIATE_MAWISTA_URL')),
            'text' => [
                'tr' => [
                    'label'      => 'Mawista — Öğrenci Sağlık Sigortası',
                    'desc'       => 'Vize için kabul edilen 12 aylık sağlık sigortası, ~80 €/ay. Uluslararası öğrenciler için uygun fiyatlı paketler.',
                    'cta'        => 'Sigorta teklifi al',
                    'disclaimer' => '* Affiliate link — kullanıcıya ek ücret yansımaz.',
                ],
                'de' => [
                    'label'      => 'Mawista — Krankenversicherung für Studierende',
                    'desc'       => 'Für das Visum anerkannte Krankenversicherung über 12 Monate, ab rund 80 € im Monat. Tarife für internationale Studierende.',
                    'cta'        => 'Angebot ansehen',
                    'disclaimer' => '* Affiliate-Link — für dich entstehen keine Mehrkosten.',
                ],
                'en' => [
                    'label'      => 'Mawista — health insurance for students',
                    'desc'       => 'Visa-accepted health cover for 12 months from around €80 per month, with plans built for international students.',
                    'cta'        => 'See the offer',
                    'disclaimer' => '* Affiliate link — no extra cost to you.',
                ],
            ],
        ],

        'care_concept' => [
            'partner'  => 'Care Concept',
            'category' => 'sigorta',
            'url'      => env('AFFILIATE_CARECONCEPT_URL', 'https://www.care-concept.de'),
            'logo'     => null,
            'active'   => filled(env('AFFILIATE_CARECONCEPT_URL')),
            'text' => [
                'tr' => [
                    'label'      => 'Care Concept — Uluslararası Öğrenci Sigortası',
                    'desc'       => 'Avrupa\'da yaygın özel sağlık sigortası, vize için yeterli kapsam. Esnek paketler.',
                    'cta'        => 'Sigorta detayı',
                    'disclaimer' => '* Affiliate link — kullanıcıya ek ücret yansımaz.',
                ],
                'de' => [
                    'label'      => 'Care Concept — Versicherung für internationale Studierende',
                    'desc'       => 'In Europa verbreitete private Krankenversicherung mit visumstauglichem Umfang und flexiblen Tarifen.',
                    'cta'        => 'Tarife ansehen',
                    'disclaimer' => '* Affiliate-Link — für dich entstehen keine Mehrkosten.',
                ],
                'en' => [
                    'label'      => 'Care Concept — insurance for international students',
                    'desc'       => 'Private health cover widely used across Europe, with visa-compliant scope and flexible plans.',
                    'cta'        => 'See the plans',
                    'disclaimer' => '* Affiliate link — no extra cost to you.',
                ],
            ],
        ],
    ],

    /**
     * İçerik-bazlı affiliate öneri kuralı.
     * Hangi bağlamda hangi ortak gösterilsin.
     */
    'context_rules' => [
        'visa'        => ['expatrio', 'fintiba', 'mawista'],
        'sperrkonto'  => ['expatrio', 'fintiba'],
        'insurance'   => ['mawista', 'care_concept'],
        'default'     => ['expatrio', 'mawista'],
    ],

];
