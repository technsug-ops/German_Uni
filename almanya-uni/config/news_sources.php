<?php

/**
 * Otomatik haber çekimi (news:fetch) için küratörlü RSS/Atom kaynak kayıt defteri.
 *
 * KURALLAR:
 *  - Sadece RESMİ / güvenilir kaynaklar (YMYL: vize/yasa doğruluğu kritik).
 *  - Çekilen şey SADECE başlık + özet + link → ÖZGÜN editöryel taslağa dönüşür
 *    (telif: birebir basmıyoruz, atıf + deep-link veriyoruz).
 *  - default_category: önerilen haber kategorisi slug'ı (categories.kind='news').
 *
 * Yeni kaynak = buraya bir satır. Kod değişikliği gerekmez.
 */
return [
    // Otomatik çekimde her kaynaktan en fazla kaç aday alınsın
    'max_per_source' => 6,
    // Adayın yaşı bu günden eskiyse atla (bayat haber çekme)
    'max_age_days'   => 45,

    'feeds' => [
        // ✅ DOĞRULANDI — çalışan feed. Geniş uluslararası eğitim kaynağı →
        // 'keywords' ile SADECE Almanya-alakalı haberler aday olur.
        [
            'name'             => 'ICEF Monitor',
            'url'              => 'https://monitor.icef.com/feed/',
            'default_category' => 'universities',
            'keywords'         => ['Germany', 'German', 'Deutschland', 'DAAD', 'Berlin', 'Munich', 'Chancenkarte'],
            'enabled'          => true,
        ],

        // ⏳ RSS URL'leri doğrulanmalı — resmi siteler açık RSS sunmuyor.
        // Doğru feed bulununca url'yi güncelle + enabled=true yap.
        [
            'name'             => 'DAAD',
            'url'              => 'https://www.daad.de/en/rss/',
            'default_category' => 'universities',
            'enabled'          => false,
        ],
        [
            'name'             => 'Make-it-in-Germany',
            'url'              => 'https://www.make-it-in-germany.com/en/rss',
            'default_category' => 'visa-residence',
            'enabled'          => false,
        ],
        [
            'name'             => 'Mediendienst Integration',
            'url'              => 'https://mediendienst-integration.de/feed.html',
            'default_category' => 'integration',
            'enabled'          => false,
        ],
    ],
];
