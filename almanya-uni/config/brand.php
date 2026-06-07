<?php

/**
 * Domain-aware brand definition.
 *
 * almanyauni.com   → "AlmanyaUni" brand (her locale'da bu marka)
 * applytogerman.com → "ApplyToGerman" brand (her locale'da bu marka)
 *
 * Domain ile brand sabit, locale ile içerik dili değişir. İki kavram bağımsız.
 *   almanyauni.com/en       → AlmanyaUni brand + EN content
 *   applytogerman.com/tr    → ApplyToGerman brand + TR content
 *
 * Domain map yoksa fallback brand kullanılır.
 */
return [
    'brands' => [
        'almanyauni' => [
            'name'           => 'ApplyToGerman (AlmanyaUni)',
            'tagline'        => [
                'tr' => 'Almanya\'da eğitim rehberin',
                'en' => 'Your guide to studying in Germany',
                'de' => 'Dein Wegweiser fürs Studium in Deutschland',
            ],
            'domain'         => 'almanyauni.com',
            // AlmanyaUni görsel kimliği gizlendi → ApplyToGerman logosu kullanılır.
            // Orijinal AlmanyaUni varlıkları korundu: docs/brand-almanyauni-archive.md
            'logo'           => '/img/logos/applytogerman.svg',
            'logo_white'     => '/img/logos/applytogerman-white.svg',
            'favicon'        => '/favicon-atg.ico',
            'og_image'       => '/img/og/applytogerman.png',
            'twitter'        => '@applytogerman',
            'mail_from'      => 'merhaba@almanyauni.com',
            'mail_from_name' => 'ApplyToGerman',
            'copyright'      => 'ApplyToGerman (AlmanyaUni)',
            'apple_title'    => 'ApplyToGerman',
            'default_locale' => 'tr',
            'theme_color'    => '#1A1A1A', // Marka siyahı (ApplyToGerman logo sistemi)
        ],
        'applytogerman' => [
            'name'           => 'ApplyToGerman (AlmanyaUni)',
            'tagline'        => [
                'tr' => 'Almanya başvuru rehberi',
                'en' => 'Apply to study in Germany',
                'de' => 'Bewerbungsleitfaden für Deutschland',
            ],
            'domain'         => 'applytogerman.com',
            'logo'           => '/img/logos/applytogerman.svg',
            'logo_white'     => '/img/logos/applytogerman-white.svg',
            'favicon'        => '/favicon-atg.ico',
            'og_image'       => '/img/og/applytogerman.png',
            'twitter'        => '@applytogerman',
            'mail_from'      => 'hello@applytogerman.com',
            'mail_from_name' => 'ApplyToGerman',
            'copyright'      => 'ApplyToGerman (AlmanyaUni)',
            'apple_title'    => 'ApplyToGerman',
            'default_locale' => 'en',
            'theme_color'    => '#1A1A1A', // Marka siyahı (ApplyToGerman logo sistemi)
        ],
    ],

    // Host (lowercase, www-stripped) → brand key
    'domains' => [
        'almanyauni.com'    => 'almanyauni',
        'applytogerman.com' => 'applytogerman',
        'germanyuni.com'    => 'applytogerman', // beklemede; brand seçildi
        'localhost'         => 'almanyauni',   // dev varsayılan
        '127.0.0.1'         => 'almanyauni',
    ],

    'fallback' => 'almanyauni',
];
