@props([
    'provider',   // BlockedAccountProvider | HealthInsuranceProvider (cta_url + trackedUrl())
    'ctx' => null, // 'index' | 'show' | 'comparison'
    'class' => '',
])

{{--
    Tek noktadan affiliate CTA: takipli URL (/go/{type}/{slug}) + rel="sponsored
    nofollow noopener" (SEO/güvenlik). cta_url yoksa hiç render etme. İçerik slot'tan.
--}}
@if ($provider->cta_url)
    <a href="{{ $provider->trackedUrl($ctx) }}"
       target="_blank"
       rel="noopener sponsored nofollow"
       data-affiliate="{{ $provider->slug }}"
       {{ $attributes->merge(['class' => $class]) }}>{{ $slot }}</a>
@endif
