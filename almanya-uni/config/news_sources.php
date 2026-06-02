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
 * NOT: Kaynaklar artık DB'den (news_sources tablosu / admin "Haber Kaynakları"
 * paneli) okunur. Bu dosya YALNIZCA tablo henüz migrate edilmemişse/boşsa
 * fallback + ilk seed kaynağıdır. Canlıda kaynak eklemek/düzenlemek için paneli kullan.
 */
return [
    // Otomatik çekimde her kaynaktan en fazla kaç aday alınsın
    'max_per_source' => 6,
    // Adayın yaşı bu günden eskiyse atla (bayat haber çekme)
    'max_age_days'   => 45,

    'feeds' => [
        // ✅ Google News RSS — her zaman güncel Almanya içeriği döner (100+ item).
        // Sorgu zaten Almanya-odaklı; keyword filtre güvenlik ağı. Link = Google
        // yönlendirmesi (kaynağa gider); AI taslak başlık+özetten ÖZGÜN brief üretir.
        [
            'name'             => 'Google News · Vize & Göç',
            'url'              => 'https://news.google.com/rss/search?q=Germany%20student%20visa%20OR%20Chancenkarte%20OR%20%22skilled%20immigration%22&hl=en-US&gl=US&ceid=US:en',
            'default_category' => 'visa-residence',
            'keywords'         => ['Germany', 'German', 'Deutschland', 'Chancenkarte', 'visa', 'immigration'],
            'enabled'          => true,
        ],
        [
            'name'             => 'Google News · Üniversite & Burs',
            'url'              => 'https://news.google.com/rss/search?q=%22study%20in%20Germany%22%20(university%20OR%20DAAD%20OR%20scholarship%20OR%20students)&hl=en-US&gl=US&ceid=US:en',
            'default_category' => 'universities',
            'keywords'         => ['Germany', 'German', 'Deutschland', 'DAAD', 'university', 'scholarship'],
            'enabled'          => true,
        ],

        // ✅ DOĞRULANDI — geniş uluslararası eğitim kaynağı; keyword ile Almanya süzülür.
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
