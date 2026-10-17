{{-- SEO P1-1 "Ana rehber" kutusu: aynı niyetteki alt/rakip sayfadan niyetin ana URL'sine tek bağlantı (config/seo_intents.php).
     Niyet yoksa, ana URL bu dilde çözülmüyorsa veya sayfa ana URL'nin kendisiyse hiçbir şey basmaz. --}}
@props(['intent' => null])
@php
    $cfg = $intent ? (\App\Support\SeoIntents::intents()[$intent] ?? null) : null;
    $guideUrl = $cfg ? \App\Support\SeoIntents::primaryUrl($intent) : null;
    if ($guideUrl && rtrim($guideUrl, '/') === rtrim(url()->current(), '/')) {
        $guideUrl = null;
    }
@endphp
@if ($guideUrl)
    <aside {{ $attributes->merge(['class' => 'rounded-xl border border-gray-200 border-l-4 border-l-primary-600 bg-white p-4 sm:p-5 no-print']) }}
           data-seo-intent="{{ $intent }}" aria-label="{{ __('Main guide') }}">
        <div class="flex items-start gap-3">
            <span class="hidden sm:inline-flex shrink-0 w-10 h-10 rounded-lg bg-primary-50 text-primary-700 items-center justify-center" aria-hidden="true">
                <x-svg-icon :name="$cfg['icon'] ?? 'book-open'" class="w-5 h-5" />
            </span>
            <div class="min-w-0">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Main guide') }}</p>
                <a href="{{ $guideUrl }}" class="font-bold text-gray-900 hover:text-primary-700 underline-offset-2 hover:underline">{{ __($cfg['label']) }} →</a>
                <p class="text-sm text-gray-600 mt-0.5">{{ __($cfg['description']) }}</p>
            </div>
        </div>
    </aside>
@endif
