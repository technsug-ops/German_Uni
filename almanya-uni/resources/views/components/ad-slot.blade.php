@props([
    'type' => 'banner',          // banner | square | inline | sidebar | affiliate-card
    'slot' => null,              // adsense slot key (banner_top, in_content, sidebar, vs.)
    'affiliate' => null,         // affiliate key (expatrio, fintiba, mawista, ...) → bilinen affiliate ise onu render et
    'context' => null,           // visa | sperrkonto | insurance — uygun affiliate'i seç
    'label' => null,
])

@php $label = $label ?? __('Ad'); @endphp

@php
    $adsense = config('ads.adsense');
    $placeholder = config('ads.placeholder');
    // Premium üyeler için AdSense + placeholder reklamları gizlenir
    // (Affiliate kart içerik-uyumlu sponsor mantığı — premium'da da kalır)
    $isPremium = optional(auth()->user())->isPremium() ?? false;

    // Affiliate render mantığı (öncelikli)
    $affiliateConfig = null;
    if ($affiliate) {
        $affiliateConfig = config("ads.affiliates.$affiliate");
    } elseif ($context && $type === 'affiliate-card') {
        // Context'e göre ilk active affiliate'i bul
        $candidates = config("ads.context_rules.$context", config('ads.context_rules.default', []));
        foreach ($candidates as $key) {
            $cand = config("ads.affiliates.$key");
            if ($cand && ! empty($cand['active'])) {
                $affiliateConfig = $cand;
                break;
            }
        }
    }

    // AdSense slot mantığı
    $adsenseSlotId = $slot && $adsense['enabled']
        ? ($adsense['slots'][$slot] ?? null)
        : null;

    // Boyut ipuçları (AdSense responsive ama placeholder için hint)
    $sizes = [
        'banner'  => 'min-h-[90px] md:min-h-[120px]',
        'square'  => 'min-h-[250px] aspect-square max-w-[300px] mx-auto',
        'inline'  => 'min-h-[200px]',
        'sidebar' => 'min-h-[600px] max-w-[300px]',
    ];
    $sizeCls = $sizes[$type] ?? 'min-h-[120px]';
@endphp

{{-- AFFILIATE KARTI --}}
@if ($affiliateConfig && ! empty($affiliateConfig['active']))
    <div class="ad-slot-affiliate bg-gradient-to-br from-accent-50 to-primary-50 border border-accent-200 rounded-xl p-5 md:p-6 my-6 shadow-sm">
        <div class="flex items-start gap-4">
            <div class="flex-shrink-0 w-12 h-12 bg-accent-500 text-white rounded-lg flex items-center justify-center text-xl">
                💰
            </div>
            <div class="flex-1">
                <div class="flex items-baseline gap-2 mb-1">
                    <span class="inline-block text-[10px] uppercase tracking-wide bg-white text-gray-600 px-2 py-0.5 rounded font-semibold">{{ __('Sponsor') }}</span>
                    <span class="text-xs text-gray-500">{{ $affiliateConfig['partner'] }}</span>
                </div>
                <h3 class="font-bold text-gray-900 mb-1.5 leading-tight">{{ $affiliateConfig['label'] }}</h3>
                <p class="text-sm text-gray-700 leading-relaxed mb-3">{{ $affiliateConfig['desc'] }}</p>
                <a href="{{ $affiliateConfig['url'] }}" target="_blank" rel="sponsored noopener nofollow"
                   class="inline-flex items-center gap-2 bg-accent-500 hover:bg-accent-600 text-white px-5 py-2.5 rounded-lg font-bold text-sm transition shadow-sm">
                    {{ $affiliateConfig['cta'] }}
                </a>
                <p class="text-[11px] text-gray-500 mt-2">{{ $affiliateConfig['disclaimer'] }}</p>
            </div>
        </div>
    </div>

{{-- ADSENSE SLOT (aktif + slot ID dolu + premium değil) --}}
@elseif ($adsenseSlotId && ! $isPremium)
    <div class="ad-slot ad-slot--{{ $type }} my-6 text-center {{ $sizeCls }}">
        <p class="text-[10px] uppercase tracking-wide text-gray-400 mb-1">{{ $label }}</p>
        <ins class="adsbygoogle"
             style="display:block"
             data-ad-client="{{ $adsense['client_id'] }}"
             data-ad-slot="{{ $adsenseSlotId }}"
             data-ad-format="auto"
             data-full-width-responsive="true"></ins>
        @once @push('scripts')
            <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client={{ $adsense['client_id'] }}" crossorigin="anonymous"></script>
        @endpush @endonce
        <script>(adsbygoogle = window.adsbygoogle || []).push({});</script>
    </div>

{{-- PLACEHOLDER (dev / staging — AdSense onayı bekleniyorsa, premium değil) --}}
@elseif ($placeholder['enabled'] && ! $isPremium)
    <div class="ad-slot-placeholder my-6 {{ $sizeCls }} bg-gray-50 border-2 border-dashed border-gray-300 rounded-lg flex flex-col items-center justify-center text-center p-6">
        <div class="text-3xl text-gray-400 mb-2">📢</div>
        <p class="text-sm text-gray-500 font-semibold">{{ $placeholder['label'] }}</p>
        <p class="text-xs text-gray-400 mt-1">{{ ucfirst($type) }} · {{ $slot ?? 'default' }}</p>
    </div>
@endif
