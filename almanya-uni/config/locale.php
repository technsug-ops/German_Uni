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
            'active'      => false,
            'coming_soon' => true,
            'is_default'  => false,
            'direction'   => 'rtl',
            'date_format' => 'd/m/Y',
        ],
        'fa' => [
            'name'        => 'Persian',
            'native_name' => 'فارسی',
            'flag'        => '🇮🇷',
            'active'      => false,
            'coming_soon' => true,
            'is_default'  => false,
            'direction'   => 'rtl',
            'date_format' => 'd/m/Y',
        ],
        'es' => [
            'name'        => 'Spanish',
            'native_name' => 'Español',
            'flag'        => '🇪🇸',
            'active'      => false,
            'coming_soon' => true,
            'is_default'  => false,
            'direction'   => 'ltr',
            'date_format' => 'd/m/Y',
        ],
        'it' => [
            'name'        => 'Italian',
            'native_name' => 'Italiano',
            'flag'        => '🇮🇹',
            'active'      => false,
            'coming_soon' => true,
            'is_default'  => false,
            'direction'   => 'ltr',
            'date_format' => 'd/m/Y',
        ],
        'pt' => [
            'name'        => 'Portuguese',
            'native_name' => 'Português',
            'flag'        => '🇵🇹',
            'active'      => false,
            'coming_soon' => true,
            'is_default'  => false,
            'direction'   => 'ltr',
            'date_format' => 'd/m/Y',
        ],
        'ru' => [
            'name'        => 'Russian',
            'native_name' => 'Русский',
            'flag'        => '🇷🇺',
            'active'      => false,
            'coming_soon' => true,
            'is_default'  => false,
            'direction'   => 'ltr',
            'date_format' => 'd.m.Y',
        ],
        'pl' => [
            'name'        => 'Polish',
            'native_name' => 'Polski',
            'flag'        => '🇵🇱',
            'active'      => false,
            'coming_soon' => true,
            'is_default'  => false,
            'direction'   => 'ltr',
            'date_format' => 'd.m.Y',
        ],
        'zh' => [
            'name'        => 'Chinese',
            'native_name' => '中文',
            'flag'        => '🇨🇳',
            'active'      => false,
            'coming_soon' => true,
            'is_default'  => false,
            'direction'   => 'ltr',
            'date_format' => 'Y-m-d',
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
        'es' => ['es', 'en', 'de', 'tr'],
        'it' => ['it', 'en', 'de', 'tr'],
        'pt' => ['pt', 'en', 'de', 'tr'],
        'ru' => ['ru', 'en', 'de', 'tr'],
        'pl' => ['pl', 'en', 'de', 'tr'],
        'zh' => ['zh', 'en', 'de', 'tr'],
    ],

    /**
     * Fallback for any locale not listed above (new languages added later):
     * own locale → EN → DE → TR. Read via config('locale.content_fallback.<x>')
     * with this default, so a new language needs no new fallback entry.
     */
    'content_fallback_default' => ['en', 'de', 'tr'],
];
