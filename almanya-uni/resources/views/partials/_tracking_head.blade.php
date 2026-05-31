{{--
    Pazarlama & analitik izleyiciler — <head> içine.

    Tüm ID'ler /admin → Ayarlar → Entegrasyonlar sayfasından (settings tablosu) gelir.
    Bir ID boşsa o entegrasyonun kodu hiç basılmaz.

    KVKK/GDPR: tracking_require_consent açıkken (varsayılan) Google için Consent
    Mode v2 ile her şey "denied" başlar; ziyaretçi çerez banner'ından "Kabul Et"e
    basınca window.grantTrackingConsent() çağrılır → Google izinleri güncellenir,
    Meta & TikTok pixelleri o an yüklenir. Reddederse hiçbiri çalışmaz.
--}}
@php
    $gaId           = setting('google_analytics_id');
    $adsId          = setting('google_ads_id');
    $gtmId          = setting('google_tag_manager_id');
    $metaPixel      = setting('meta_pixel_id');
    $tiktokPixel    = setting('tiktok_pixel_id');
    $requireConsent = (bool) setting('tracking_require_consent', '1');

    $hasGtag    = $gaId || $adsId;                  // gtag.js gerekiyor mu?
    $anyTracker = $hasGtag || $gtmId || $metaPixel || $tiktokPixel;

    // Sunucu tarafı onay durumu (dönen ziyaretçi için ilk render'da doğru başlasın)
    $consentCookie  = request()->cookie('almanyauni_consent'); // accepted | rejected | null
    $consentGranted = ! $requireConsent || $consentCookie === 'accepted';
@endphp

@if ($anyTracker)
<script>
(function () {
    window.dataLayer = window.dataLayer || [];
    function gtag(){ dataLayer.push(arguments); }
    window.gtag = gtag;

    @if ($requireConsent)
    // ── Google Consent Mode v2 — varsayılan durum ──
    gtag('consent', 'default', {
        'ad_storage':         '{{ $consentGranted ? 'granted' : 'denied' }}',
        'ad_user_data':       '{{ $consentGranted ? 'granted' : 'denied' }}',
        'ad_personalization': '{{ $consentGranted ? 'granted' : 'denied' }}',
        'analytics_storage':  '{{ $consentGranted ? 'granted' : 'denied' }}',
        'wait_for_update':    500
    });
    @endif

    @if ($hasGtag)
    gtag('js', new Date());
    @if ($gaId)  gtag('config', '{{ $gaId }}');  @endif
    @if ($adsId) gtag('config', '{{ $adsId }}'); @endif
    @endif

    // ── Meta & TikTok pixel'leri: onay gelene dek YÜKLENMEZ ──
    var metaId   = @json($metaPixel);
    var tiktokId = @json($tiktokPixel);

    window.__initMetaPixel = function () {
        if (!metaId || window.__metaLoaded) return;
        window.__metaLoaded = true;
        !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
        n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
        document,'script','https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', metaId);
        fbq('track', 'PageView');
    };

    window.__initTikTokPixel = function () {
        if (!tiktokId || window.__tiktokLoaded) return;
        window.__tiktokLoaded = true;
        !function (w, d, t) {
            w.TiktokAnalyticsObject=t;var ttq=w[t]=w[t]||[];
            ttq.methods=['page','track','identify','instances','debug','on','off','once','ready','alias','group','enableCookie','disableCookie','holdConsent','revokeConsent','grantConsent'];
            ttq.setAndDefer=function(t,e){t[e]=function(){t.push([e].concat(Array.prototype.slice.call(arguments,0)))}};
            for(var i=0;i<ttq.methods.length;i++)ttq.setAndDefer(ttq,ttq.methods[i]);
            ttq.instance=function(t){for(var e=ttq._i[t]||[],n=0;n<ttq.methods.length;n++)ttq.setAndDefer(e,ttq.methods[n]);return e};
            ttq.load=function(e,n){var r='https://analytics.tiktok.com/i18n/pixel/events.js',o=n&&n.partner;ttq._i=ttq._i||{};ttq._i[e]=[];ttq._i[e]._u=r;ttq._t=ttq._t||{};ttq._t[e]=+new Date;ttq._o=ttq._o||{};ttq._o[e]=n||{};var s=d.createElement('script');s.type='text/javascript';s.async=!0;s.src=r+'?sdkid='+e+'&lib='+t;var a=d.getElementsByTagName('script')[0];a.parentNode.insertBefore(s,a)};
            ttq.load(tiktokId);
            ttq.page();
        }(window, document, 'ttq');
    };

    // ── Onay güncelleyicileri (çerez banner'ı çağırır) ──
    window.grantTrackingConsent = function () {
        @if ($hasGtag || $gtmId)
        gtag('consent', 'update', {
            'ad_storage': 'granted', 'ad_user_data': 'granted',
            'ad_personalization': 'granted', 'analytics_storage': 'granted'
        });
        @endif
        window.__initMetaPixel();
        window.__initTikTokPixel();
    };
    window.denyTrackingConsent = function () {
        @if ($hasGtag || $gtmId)
        gtag('consent', 'update', {
            'ad_storage': 'denied', 'ad_user_data': 'denied',
            'ad_personalization': 'denied', 'analytics_storage': 'denied'
        });
        @endif
    };

    @if ($consentGranted)
    // Onay zaten var (ya gerekmiyor ya da ziyaretçi daha önce kabul etmiş)
    window.__initMetaPixel();
    window.__initTikTokPixel();
    @endif
})();
</script>

@if ($hasGtag)
<script async src="https://www.googletagmanager.com/gtag/js?id={{ $gaId ?: $adsId }}"></script>
@endif

@if ($gtmId)
{{-- Google Tag Manager --}}
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','{{ $gtmId }}');</script>
@endif
@endif
