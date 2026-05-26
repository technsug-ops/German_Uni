<?php

/**
 * AlmanyaUni — i18n / çok-dilli yapı yapılandırması
 *
 * - default: TR (Türk öğrenciler birincil kitle)
 * - supported: TR + EN + DE şu an aktif; AR/FA için stub'lar hazır
 *
 * URL pattern:
 *   - Default (TR): /programs                — prefix YOK
 *   - Diğer diller: /en/programs, /de/programme, /ar/...
 */

return [

    // Default = EN (root, prefix'siz) — HEDEF. İçerik hazır olana kadar en coming_soon,
    // root otomatik /tr'ye yönlenir. EN hazır olunca: en active=true + coming_soon kaldır.
    'default' => 'en',

    /**
     * Aktif diller. Anahtarlar URL prefix (default için boş).
     * Bir dil "active=false" ise UI'da görünmez ama lang dosyaları hazır kalır.
     */
    'locales' => [
        'tr' => [
            'name'        => 'Türkçe',
            'native_name' => 'Türkçe',
            'flag'        => '🇹🇷',
            'active'      => true,
            'is_default'  => true,
            'direction'   => 'ltr',
            'date_format' => 'd.m.Y',
        ],
        'en' => [
            'name'        => 'English',
            'native_name' => 'English',
            'flag'        => '🇬🇧',
            'active'      => true,        // 2026-05-24 aktive: EN içerik tam (uni/program/profession/blog/faq)
            'coming_soon' => false,
            'is_default'  => false,
            'direction'   => 'ltr',
            'date_format' => 'd.m.Y',
        ],
        'de' => [
            'name'        => 'Deutsch',
            'native_name' => 'Deutsch',
            'flag'        => '🇩🇪',
            'active'      => true,        // 2026-05-24 aktive: DE lang dosyası 2152 key + blog/faq DE
            'coming_soon' => false,
            'is_default'  => false,
            'direction'   => 'ltr',
            'date_format' => 'd.m.Y',
        ],
        'fr' => [
            'name'        => 'Français',
            'native_name' => 'Français',
            'flag'        => '🇫🇷',
            'active'      => false,
            'coming_soon' => true,
            'is_default'  => false,
            'direction'   => 'ltr',
            'date_format' => 'd.m.Y',
        ],
        'ar' => [
            'name'        => 'Arabic',
            'native_name' => 'العربية',
            'flag'        => '🇸🇦',
            'active'      => false,   // Henüz açık değil
            'is_default'  => false,
            'direction'   => 'rtl',
            'date_format' => 'd/m/Y',
        ],
        'fa' => [
            'name'        => 'Persian',
            'native_name' => 'فارسی',
            'flag'        => '🇮🇷',
            'active'      => false,
            'is_default'  => false,
            'direction'   => 'rtl',
            'date_format' => 'd/m/Y',
        ],
    ],

    /**
     * DB content alanları için locale → kolon fallback zinciri.
     * Örnek: $uni->getLocalizedAttribute('name') şöyle çalışır:
     *   tr → name_tr → name_en → name_de
     *   en → name_en → name_de → name_tr
     *   de → name_de → name_en → name_tr
     */
    'content_fallback' => [
        'tr' => ['tr', 'en', 'de'],
        'en' => ['en', 'de', 'tr'],
        'de' => ['de', 'en', 'tr'],
        'fr' => ['fr', 'en', 'de', 'tr'],
        'ar' => ['ar', 'en', 'de', 'tr'],
        'fa' => ['fa', 'en', 'de', 'tr'],
    ],
];
