<?php

/**
 * SEO defaults — brand-agnostic fallback değerler.
 * Runtime'da brand-specific değerler config/brand.php'den brand() helper ile çözülür.
 * Buradaki değerler SADECE FALLBACK (brand resolve edilemediği edge case'ler için).
 */
return [
    'site_name' => 'AlmanyaUni',

    'default' => [
        'title' => 'AlmanyaUni — Almanya Üniversite & Kariyer Rehberi',
        'description' => 'Türk öğrenciler için Almanya rehberi: 488 üniversite, 18.306 program, 180 şehir, 3.560 meslek. Burslar, başvuru rehberleri, yaşam maliyeti hesaplayıcı ve kariyer araçları — ücretsiz.',
        // og:image fallback brand'a göre runtime'da seçilir (components/seo.blade.php)
        'image' => '/og-default.png',
        'image_width' => 1200,
        'image_height' => 630,
    ],

    'organization' => [
        'name' => 'AlmanyaUni',
        'url' => env('APP_URL', 'http://localhost'),
        'logo' => '/logo.png',
        'sameAs' => [
            // Twitter, Facebook, Instagram URL'leri ileride
        ],
    ],

    'twitter' => [
        // brand-specific handle config/brand.php'den (brand('twitter'))
        'handle' => '@almanyauni',
        'card' => 'summary_large_image',
    ],

    'locale' => 'tr_TR',

    /**
     * Search Console verification tag'leri (.env'den oku).
     * Almanyauni.com'a yüklenince:
     *   1. Google Search Console → URL ekle → HTML tag yöntemi → content="..." kısmını al
     *   2. .env'e: GOOGLE_SITE_VERIFICATION=...
     *   3. Bing/Yandex için de aynı şekilde
     */
    'verification' => [
        'google' => env('GOOGLE_SITE_VERIFICATION'),
        'bing'   => env('BING_SITE_VERIFICATION'),
        'yandex' => env('YANDEX_SITE_VERIFICATION'),
    ],
];
