{{--
    İzleyici <noscript> fallback'leri — <body> açılışından hemen sonra.
    JS kapalı ziyaretçiler için (GTM iframe + Meta noscript img).
--}}
@php
    $gtmId          = setting('google_tag_manager_id');
    $metaPixel      = setting('meta_pixel_id');
    $requireConsent = (bool) setting('tracking_require_consent', '1');
    $consentGranted = ! $requireConsent || request()->cookie('almanyauni_consent') === 'accepted';
@endphp

@if ($gtmId)
{{-- Google Tag Manager (noscript) --}}
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ $gtmId }}"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
@endif

@if ($metaPixel && $consentGranted)
{{-- Meta Pixel (noscript) --}}
<noscript><img height="1" width="1" style="display:none"
    src="https://www.facebook.com/tr?id={{ $metaPixel }}&ev=PageView&noscript=1"
    alt=""></noscript>
@endif
