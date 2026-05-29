@php
    $event = $event ?? null;
    if (! $event) return;
    $startTs = $event->starts_at->timestamp * 1000;
    $isLive = $event->is_live ?? false;
    // Sıcak amber→turuncu→kırmızı gradient — hero'nun mavi/indigo'su ile maksimum kontrast.
    // Live durumunda tam kırmızı, upcoming'de amber-to-orange.
    $bannerGradient = $isLive
        ? 'linear-gradient(90deg, #dc2626 0%, #ea580c 100%)'
        : 'linear-gradient(90deg, #f59e0b 0%, #f97316 60%, #ef4444 100%)';
    $bannerCta = route('events.show', $event->slug) . '#rsvp';
@endphp

<div id="eventBanner"
     data-start="{{ $startTs }}"
     data-storage-key="event-banner-{{ $event->id }}"
     class="hidden relative z-30 text-white shadow-lg border-b-2 border-amber-300/60"
     style="background: {{ $bannerGradient }}; box-shadow: 0 4px 12px -4px rgba(239,68,68,0.4);">
    <div class="max-w-[1400px] mx-auto px-4 py-2.5 flex items-center gap-3 text-sm">
        {{-- Emoji + pulse glow --}}
        <span class="relative shrink-0 inline-flex items-center justify-center">
            <span class="absolute inset-0 rounded-full bg-white/30 animate-ping" style="animation-duration: 2s;"></span>
            <span class="relative text-xl">{{ $isLive ? '🔴' : $event->type_emoji }}</span>
        </span>

        <div class="flex-1 min-w-0 flex items-center gap-3 flex-wrap">
            @if ($isLive)
                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-white text-red-600 text-[10px] font-extrabold uppercase tracking-wider">
                    <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></span>
                    {{ __('Live') }}
                </span>
                <a href="{{ route('events.show', $event->slug) }}" class="font-bold truncate hover:underline">"{{ $event->title }}"</a>
                <span class="text-white/95 text-xs whitespace-nowrap font-semibold">— {{ __('on air now') }}</span>
            @else
                <a href="{{ route('events.show', $event->slug) }}" class="font-bold truncate hover:underline drop-shadow-sm">{{ $event->type_label }}: "{{ $event->title }}"</a>
                <span class="text-white/95 text-xs whitespace-nowrap font-semibold">{{ __('starts in') }}</span>
                @if ($event->language_flag)
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-white/20 backdrop-blur text-[11px] font-bold whitespace-nowrap" title="{{ __('Event language') }}">
                        {{ $event->language_flag }} {{ $event->language_label }}
                    </span>
                @endif
                <div class="flex items-center gap-1 tabular-nums font-mono">
                    <span class="bg-black/35 px-2 py-0.5 rounded font-bold text-sm shadow-sm"><span data-cd="d">--</span><span class="text-[10px] opacity-80 ml-0.5">{{ __('D') }}</span></span>
                    <span class="opacity-70">:</span>
                    <span class="bg-black/35 px-2 py-0.5 rounded font-bold text-sm shadow-sm"><span data-cd="h">--</span><span class="text-[10px] opacity-80 ml-0.5">{{ __('H') }}</span></span>
                    <span class="opacity-70">:</span>
                    <span class="bg-black/35 px-2 py-0.5 rounded font-bold text-sm shadow-sm"><span data-cd="m">--</span><span class="text-[10px] opacity-80 ml-0.5">{{ __('M') }}</span></span>
                </div>
            @endif
        </div>

        {{-- CTA — RSVP varsa öncelikli, sonra registration_url, sonra online_url --}}
        <a href="{{ $isLive && $event->online_url ? $event->online_url : $bannerCta }}"
           @if($isLive) target="_blank" rel="noopener" @endif
           class="inline-flex items-center gap-1.5 px-4 py-1.5 bg-white text-rose-700 font-extrabold text-xs rounded shadow-md hover:bg-amber-50 hover:scale-105 active:scale-100 transition shrink-0">
            {{ $isLive ? '🔴 ' . __('Join now →') : '🎟️ ' . __('RSVP free →') }}
        </a>

        <button id="eventBannerClose"
                class="shrink-0 w-7 h-7 rounded hover:bg-white/20 flex items-center justify-center text-white/80 hover:text-white transition text-lg"
                aria-label="{{ __('Close') }}" title="{{ __('Hide for 24 hours') }}">×</button>
    </div>
</div>

<script>
(function () {
    const banner = document.getElementById('eventBanner');
    if (!banner) return;
    const storageKey = banner.dataset.storageKey;
    const hidden = localStorage.getItem(storageKey);
    if (hidden && Date.now() < parseInt(hidden, 10)) return;

    const startTs = parseInt(banner.dataset.start, 10);
    const d = banner.querySelector('[data-cd="d"]');
    const h = banner.querySelector('[data-cd="h"]');
    const m = banner.querySelector('[data-cd="m"]');

    function pad(n) { return n < 10 ? '0' + n : String(n); }

    function tick() {
        const diff = startTs - Date.now();
        if (diff <= 0) return; // başlamış — JS güncellemeyi durdur
        const sec = Math.floor(diff / 1000);
        const days = Math.floor(sec / 86400);
        const hours = Math.floor((sec % 86400) / 3600);
        const mins = Math.floor((sec % 3600) / 60);
        if (d) d.textContent = pad(days);
        if (h) h.textContent = pad(hours);
        if (m) m.textContent = pad(mins);
    }

    tick();
    setInterval(tick, 30 * 1000); // 30 saniyede bir yeterli

    banner.classList.remove('hidden');
    // Banner artık normal block (relative) — body padding veya header top offset gerekmiyor.
    // Scroll edince banner doğal olarak yukarı kayar, header tek başına sticky kalır → çakışma yok.

    document.getElementById('eventBannerClose')?.addEventListener('click', () => {
        banner.classList.add('hidden');
        const hideUntil = Date.now() + 24 * 60 * 60 * 1000;
        localStorage.setItem(storageKey, String(hideUntil));
    });
})();
</script>
