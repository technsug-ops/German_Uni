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
            'name'           => 'AlmanyaUni',
            'tagline'        => [
                'tr' => 'Almanya\'da eğitim rehberin',
                'en' => 'Your guide to studying in Germany',
                'de' => 'Dein Wegweiser fürs Studium in Deutschland',
            ],
            'domain'         => 'almanyauni.com',
            'logo'           => '/img/logos/almanyauni.svg',
            'logo_white'     => '/img/logos/almanyauni-white.svg',
            'favicon'        => '/favicon.ico',
            'og_image'       => '/img/og/almanyauni.png',
            'twitter'        => '@almanyauni',
            'mail_from'      => 'merhaba@almanyauni.com',
            'mail_from_name' => 'AlmanyaUni',
            'copyright'      => 'AlmanyaUni',
            'apple_title'    => 'AlmanyaUni',
            'default_locale' => 'tr',
            'theme_color'    => '#1e40af',
        ],
        'applytogerman' => [
            'name'           => 'ApplyToGerman',
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
            'copyright'      => 'ApplyToGerman',
            'apple_title'    => 'ApplyToGerman',
            'default_locale' => 'en',
            'theme_color'    => '#0f172a',
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
