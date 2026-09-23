{{--
    Pazarlama & analitik izleyiciler — <head> içine.

    MİMARİ: BASIC Consent Mode (2026-09-23'te Advanced'den geçildi).

    Eski kurulumda gtag.js KOŞULSUZ yükleniyor, Consent Mode v2 yalnızca izinleri
    "denied" yapıyordu. Bu, Advanced Consent Mode'dur: tag yine de yüklenir ve
    Google'a cookieless ping gider — yani rıza verilmese bile IP, user-agent,
    referrer ve sayfa adresi Google'a ulaşır. Gizlilik metnindeki "rıza yoksa
    aktarım olmaz" ifadesi bu yüzden teknik olarak yanlıştı.

    Artık HİÇBİR üçüncü taraf script'i rıza öncesi DOM'a girmez:
      - Analitik (GA4, Microsoft Clarity)  → yalnızca analitik rızası
      - Pazarlama (Meta, TikTok, Ads, GTM) → yalnızca pazarlama rızası
    Karar verilmemiş ziyaretçi, reddetmiş ziyaretçiyle aynı muameleyi görür.

    Bootstrap fonksiyonları rıza olmasa da tanımlanır; böylece banner'dan gelen
    onay sayfa yenilemeden araçları başlatabilir. Rıza geri çekilince sayfa
    yeniden yüklenir — yüklenmiş bir script'i "durdurmanın" tek güvenilir yolu
    onu hiç çalıştırmamaktır.

    Tüm ID'ler /admin → Ayarlar → Entegrasyonlar'dan (settings tablosu) gelir.
--}}
@php
    $gaId        = setting('google_analytics_id');
    $adsId       = setting('google_ads_id');
    $gtmId       = setting('google_tag_manager_id');
    $metaPixel   = setting('meta_pixel_id');
    $tiktokPixel = setting('tiktok_pixel_id');
    $clarityId   = setting('microsoft_clarity_id');

    $analyticsOk = \App\Support\Consent::analytics(request());
    $marketingOk = \App\Support\Consent::marketing(request());

    // Herhangi bir sağlayıcı yapılandırılmış mı? (hiçbiri yoksa script hiç basılmaz)
    $anyConfigured = $gaId || $adsId || $gtmId || $metaPixel || $tiktokPixel || $clarityId;
@endphp

@if ($anyConfigured)
<script>
(function () {
    var ids = {
        ga:      @json($gaId),
        ads:     @json($adsId),
        gtm:     @json($gtmId),
        meta:    @json($metaPixel),
        tiktok:  @json($tiktokPixel),
        clarity: @json($clarityId)
    };
    var started = {};

    function inject(src) {
        var s = document.createElement('script');
        s.async = true;
        s.src = src;
        document.head.appendChild(s);
    }

    window.dataLayer = window.dataLayer || [];
    function gtag() { dataLayer.push(arguments); }
    window.gtag = gtag;

    // ── ANALİTİK: GA4 + Microsoft Clarity ───────────────────────────────
    window.__startAnalytics = function () {
        if (ids.ga && !started.ga) {
            started.ga = true;
            // Reklam kullanılmıyor: yalnızca analytics_storage açılır.
            gtag('consent', 'default', {
                'ad_storage': 'denied',
                'ad_user_data': 'denied',
                'ad_personalization': 'denied',
                'analytics_storage': 'granted'
            });
            gtag('js', new Date());
            gtag('config', ids.ga);
            inject('https://www.googletagmanager.com/gtag/js?id=' + encodeURIComponent(ids.ga));
        }

        if (ids.clarity && !started.clarity) {
            started.clarity = true;
            (function (c, l, a, r, i, t, y) {
                c[a] = c[a] || function () { (c[a].q = c[a].q || []).push(arguments) };
                t = l.createElement(r); t.async = 1; t.src = "https://www.clarity.ms/tag/" + i;
                y = l.getElementsByTagName(r)[0]; y.parentNode.insertBefore(t, y);
            })(window, document, "clarity", "script", ids.clarity);
            // Clarity'ye rızayı açıkça bildir (Consent v2; eski API'ye de düşer).
            try {
                window.clarity('consentv2', { ad_Storage: 'denied', analytics_Storage: 'granted' });
            } catch (e) {
                try { window.clarity('consent'); } catch (e2) {}
            }
        }
    };

    // ── PAZARLAMA: Meta, TikTok, Google Ads, GTM ────────────────────────
    // ID'nin tanımlı olması TEK BAŞINA yeterli değildir; pazarlama rızası şarttır.
    window.__startMarketing = function () {
        if (ids.ads && !started.ads) {
            started.ads = true;
            gtag('consent', 'update', {
                'ad_storage': 'granted', 'ad_user_data': 'granted', 'ad_personalization': 'granted'
            });
            gtag('js', new Date());
            gtag('config', ids.ads);
            if (!ids.ga) inject('https://www.googletagmanager.com/gtag/js?id=' + encodeURIComponent(ids.ads));
        }

        if (ids.gtm && !started.gtm) {
            started.gtm = true;
            (function (w, d, s, l, i) {
                w[l] = w[l] || []; w[l].push({ 'gtm.start': new Date().getTime(), event: 'gtm.js' });
                var f = d.getElementsByTagName(s)[0], j = d.createElement(s), dl = l != 'dataLayer' ? '&l=' + l : '';
                j.async = true; j.src = 'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
                f.parentNode.insertBefore(j, f);
            })(window, document, 'script', 'dataLayer', ids.gtm);
        }

        if (ids.meta && !started.meta) {
            started.meta = true;
            !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
            n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
            n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
            t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
            document,'script','https://connect.facebook.net/en_US/fbevents.js');
            fbq('init', ids.meta);
            fbq('track', 'PageView');
        }

        if (ids.tiktok && !started.tiktok) {
            started.tiktok = true;
            !function (w, d, t) {
                w.TiktokAnalyticsObject=t;var ttq=w[t]=w[t]||[];
                ttq.methods=['page','track','identify','instances','debug','on','off','once','ready','alias','group','enableCookie','disableCookie','holdConsent','revokeConsent','grantConsent'];
                ttq.setAndDefer=function(t,e){t[e]=function(){t.push([e].concat(Array.prototype.slice.call(arguments,0)))}};
                for(var i=0;i<ttq.methods.length;i++)ttq.setAndDefer(ttq,ttq.methods[i]);
                ttq.instance=function(t){for(var e=ttq._i[t]||[],n=0;n<ttq.methods.length;n++)ttq.setAndDefer(e,ttq.methods[n]);return e};
                ttq.load=function(e,n){var r='https://analytics.tiktok.com/i18n/pixel/events.js',o=n&&n.partner;ttq._i=ttq._i||{};ttq._i[e]=[];ttq._i[e]._u=r;ttq._t=ttq._t||{};ttq._t[e]=+new Date;ttq._o=ttq._o||{};ttq._o[e]=n||{};var s=d.createElement('script');s.type='text/javascript';s.async=!0;s.src=r+'?sdkid='+e+'&lib='+t;var a=d.getElementsByTagName('script')[0];a.parentNode.insertBefore(s,a)};
                ttq.load(ids.tiktok);
                ttq.page();
            }(window, document, 'ttq');
        }
    };

    // ── RIZA GERİ ÇEKME ────────────────────────────────────────────────
    // Yüklenmiş bir script'i JS ile "durdurmak" güvenilir değildir; bu yüzden
    // çerezler silinir, sağlayıcılara red bildirilir ve sayfa yeniden yüklenir.
    // Yenilemeden sonra sunucu hiçbir izleyici script'i basmaz.
    window.__clearAnalyticsCookies = function () {
        var host = location.hostname;
        var domains = ['', host, '.' + host];
        var parts = host.split('.');
        if (parts.length > 2) domains.push('.' + parts.slice(-2).join('.'));

        document.cookie.split(';').forEach(function (c) {
            var name = c.split('=')[0].trim();
            if (!/^(_ga|_gid|_gat|_clck|_clsk|CLID|MUID|ANONCHK|SM|MR|almanyauni_uid)/.test(name)) return;
            domains.forEach(function (d) {
                document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;' + (d ? ' domain=' + d + ';' : '');
            });
        });
    };

    window.__withdrawConsent = function () {
        try {
            if (window.gtag) gtag('consent', 'update', {
                'ad_storage': 'denied', 'ad_user_data': 'denied',
                'ad_personalization': 'denied', 'analytics_storage': 'denied'
            });
        } catch (e) {}
        try { window.clarity('consentv2', { ad_Storage: 'denied', analytics_Storage: 'denied' }); } catch (e) {}
        window.__clearAnalyticsCookies();
    };

    // Sunucu tarafı otomatik başlatma. İşaretler testlerin "tanım" ile
    // "çalıştırma"yı ayırt edebilmesi için: fonksiyon gövdesi her zaman sayfada
    // bulunur, belirleyici olan bu satırların basılıp basılmadığıdır.
    @if ($analyticsOk)
    /* consent-autostart:analytics */ window.__startAnalytics();
    @endif
    @if ($marketingOk)
    /* consent-autostart:marketing */ window.__startMarketing();
    @endif
})();
</script>
@endif
