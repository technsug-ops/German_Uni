{{--
    İzleyici <noscript> fallback'leri — <body> açılışından hemen sonra.
    JS kapalı ziyaretçiler için (GTM iframe + Meta noscript img).

    RIZA: ikisi de PAZARLAMA kategorisindedir ve yalnızca pazarlama rızasıyla basılır.
    Eskiden GTM iframe'i HİÇBİR rıza kontrolü olmadan basılıyordu — JS kapalı bir
    ziyaretçi için bu, rıza öncesi Google'a istek gitmesi demekti. Meta ise artık
    kullanılmayan 'accepted' biçimine bakıyordu.
--}}
@php
    $gtmId       = setting('google_tag_manager_id');
    $metaPixel   = setting('meta_pixel_id');
    $marketingOk = \App\Support\Consent::marketing(request());
@endphp

@if ($marketingOk && $gtmId)
{{-- Google Tag Manager (noscript) --}}
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ $gtmId }}"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
@endif

@if ($marketingOk && $metaPixel)
{{-- Meta Pixel (noscript) --}}
<noscript><img height="1" width="1" style="display:none"
    src="https://www.facebook.com/tr?id={{ $metaPixel }}&ev=PageView&noscript=1"
    alt=""></noscript>
@endif
