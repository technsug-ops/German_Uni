@php
    $event = $event ?? null;
    if (! $event) return;
    $startTs = $event->starts_at->timestamp * 1000;
    $isLive = $event->is_live ?? false;
    $bannerColor = $event->type_color ?? '#1E40AF';
@endphp

<div id="eventBanner"
     data-start="{{ $startTs }}"
     data-storage-key="event-banner-{{ $event->id }}"
     class="hidden relative z-30 text-white shadow-md border-b-2 border-white/20"
     style="background: linear-gradient(90deg, {{ $bannerColor }}, {{ $bannerColor }}ee);">
    <div class="max-w-[1400px] mx-auto px-4 py-2 flex items-center gap-3 text-sm">
        <span class="text-lg shrink-0">{{ $event->type_emoji }}</span>
        <div class="flex-1 min-w-0 flex items-center gap-3 flex-wrap">
            @if ($isLive)
                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-red-500 text-white text-[10px] font-bold uppercase tracking-wider">
                    <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                    {{ __('Live') }}
                </span>
                <strong class="truncate">{{ $event->type_label }} "{{ $event->title }}"</strong>
                <span class="text-white/80 text-xs whitespace-nowrap">— {{ __('on air now') }}</span>
            @else
                <strong class="truncate">{{ $event->type_label }} "{{ $event->title }}" — {{ __('starts in') }}</strong>
                <div class="flex items-center gap-1 tabular-nums font-mono text-sm">
                    <span class="bg-black/30 px-1.5 py-0.5 rounded"><span data-cd="d">--</span><span class="text-[10px] opacity-70 ml-0.5">{{ __('D') }}</span></span>
                    <span class="opacity-60">:</span>
                    <span class="bg-black/30 px-1.5 py-0.5 rounded"><span data-cd="h">--</span><span class="text-[10px] opacity-70 ml-0.5">{{ __('H') }}</span></span>
                    <span class="opacity-60">:</span>
                    <span class="bg-black/30 px-1.5 py-0.5 rounded"><span data-cd="m">--</span><span class="text-[10px] opacity-70 ml-0.5">{{ __('M') }}</span></span>
                </div>
            @endif
        </div>

        @if ($event->registration_url || $event->online_url)
            <a href="{{ $event->registration_url ?: $event->online_url }}"
               target="_blank" rel="noopener"
               class="inline-flex items-center gap-1.5 px-3 py-1 bg-white text-gray-900 font-semibold text-xs rounded hover:bg-gray-100 transition shrink-0">
                {{ $isLive ? __('Join now →') : ($event->price_eur > 0 ? __('Register →') : __('Free registration →')) }}
            </a>
        @endif

        <button id="eventBannerClose"
                class="shrink-0 w-7 h-7 rounded hover:bg-white/15 flex items-center justify-center text-white/70 hover:text-white transition text-lg"
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
