@php
    if (! isset($popup) || ! $popup) return;
    /** @var \App\Models\Popup $popup */
    $title    = $popup->title();
    $body     = $popup->body();
    $cta      = $popup->ctaLabel();
    $secondary= $popup->secondaryLabel();
    $emoji    = $popup->emoji ?: ($popup->media_type === 'video' ? '🎬' : '✨');
    $accent   = $popup->accent_color ?: null;
    $theme    = $popup->theme;
    $position = $popup->position;
    $mediaType= $popup->media_type ?: 'text';

    // Trigger delay
    $delayMs = match ($popup->trigger) {
        'page_load' => 0,
        'time_5s'   => 5000,
        'time_15s'  => 15000,
        'scroll_50' => 0,
        'exit_intent' => 0,
        default => (int) ($popup->delay_ms ?: 5000),
    };

    // Cookie key (unique per popup version)
    $cookieKey = 'popup_dismissed_' . $popup->key;
    $dismissDays = max(0, (int) $popup->dismiss_days);

    // Theme classes — wrapper + card
    $themeWrap = match ($theme) {
        'banner_top'    => 'fixed inset-x-0 top-0 z-[80] flex justify-center px-4 pt-3 pointer-events-none',
        'banner_bottom' => 'fixed inset-x-0 bottom-0 z-[80] flex justify-center px-4 pb-3 pointer-events-none',
        'side_card'     => 'fixed bottom-4 right-4 z-[80] max-w-sm pointer-events-none',
        'fullscreen'    => 'fixed inset-0 z-[80] flex items-center justify-center p-4',
        default         => match ($position) {
            'top'          => 'fixed inset-x-0 top-4 z-[80] flex justify-center px-4 pointer-events-none',
            'bottom'       => 'fixed inset-x-0 bottom-4 z-[80] flex justify-center px-4 pointer-events-none',
            'bottom_right' => 'fixed bottom-4 right-4 z-[80] max-w-sm pointer-events-none',
            'bottom_left'  => 'fixed bottom-4 left-4 z-[80] max-w-sm pointer-events-none',
            default        => 'fixed inset-0 z-[80] flex items-center justify-center p-4',
        },
    };

    $themeCard = match ($theme) {
        'gradient'      => 'bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-500 text-white rounded-2xl shadow-2xl border border-white/10',
        'minimal'       => 'bg-white text-gray-900 rounded-2xl shadow-xl ring-1 ring-gray-200',
        'banner_top'    => 'bg-gradient-to-r from-indigo-700 to-purple-700 text-white rounded-xl shadow-lg w-full max-w-4xl',
        'banner_bottom' => 'bg-gradient-to-r from-purple-700 to-pink-600 text-white rounded-xl shadow-lg w-full max-w-4xl',
        'side_card'     => 'bg-white text-gray-900 rounded-2xl shadow-2xl ring-1 ring-gray-200',
        'fullscreen'    => 'bg-gradient-to-br from-indigo-700 via-purple-600 to-pink-500 text-white rounded-3xl shadow-2xl max-w-2xl w-full',
        default         => 'bg-white text-gray-900 rounded-2xl shadow-xl',
    };

    $needsBackdrop = in_array($theme, ['gradient', 'minimal', 'fullscreen']) && in_array($position, ['center', 'top', 'bottom']);
@endphp

<div
    x-data="popupController({
        cookieKey: @js($cookieKey),
        dismissDays: {{ $dismissDays }},
        trigger: @js($popup->trigger),
        delay: {{ $delayMs }},
        popupId: {{ $popup->id }},
    })"
    x-show="open" x-cloak
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 scale-95"
    x-transition:enter-end="opacity-100 scale-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    @keydown.escape.window="dismiss"
    class="{{ $themeWrap }}"
>
    @if ($needsBackdrop)
        <div @click="dismiss" class="fixed inset-0 bg-black/60 backdrop-blur-sm pointer-events-auto"></div>
    @endif

    <div
        class="relative pointer-events-auto {{ $themeCard }} {{ in_array($theme, ['banner_top','banner_bottom']) ? '' : 'max-w-md' }} max-h-[88vh] overflow-y-auto"
        @if ($accent) style="--popup-accent: {{ $accent }};" @endif
    >
        {{-- Dismiss button --}}
        @if ($popup->show_dismiss_button)
            <button @click="dismiss"
                    class="absolute top-3 right-3 w-8 h-8 rounded-full bg-black/15 hover:bg-black/25 backdrop-blur flex items-center justify-center text-lg z-10 transition"
                    aria-label="{{ __('Close') }}">
                <span class="-mt-0.5">×</span>
            </button>
        @endif

        {{-- ════ MEDIA ════ --}}
        @if ($mediaType === 'image' && $popup->image_url)
            <div class="relative w-full aspect-[16/9] overflow-hidden {{ in_array($theme,['banner_top','banner_bottom']) ? 'hidden' : 'rounded-t-2xl' }}">
                <img src="{{ $popup->image_url }}" alt="{{ $title }}" class="w-full h-full object-cover">
            </div>
        @endif

        @if ($mediaType === 'video' && $popup->video_url)
            @php $provider = $popup->videoProvider(); $embed = $popup->videoEmbedUrl(); @endphp
            <div class="relative w-full aspect-video bg-black {{ in_array($theme,['banner_top','banner_bottom']) ? 'hidden' : 'rounded-t-2xl overflow-hidden' }}">
                @if ($provider === 'mp4')
                    <video src="{{ $embed }}"
                           class="w-full h-full object-cover"
                           controls
                           {{ $popup->video_autoplay ? 'autoplay' : '' }}
                           {{ $popup->video_muted    ? 'muted'    : '' }}
                           playsinline
                           loading="lazy"></video>
                @else
                    <iframe src="{{ $embed }}"
                            class="w-full h-full"
                            frameborder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen
                            loading="lazy"></iframe>
                @endif
            </div>
        @endif

        {{-- ════ TEXT CONTENT ════ --}}
        <div class="p-6 md:p-8">
            @if ($title)
                <h2 class="text-2xl md:text-3xl font-extrabold leading-tight mb-2 flex items-start gap-2 flex-wrap">
                    <span>{{ $emoji }}</span>
                    <span class="flex-1">{{ $title }}</span>
                </h2>
            @endif

            @if ($body)
                <p class="text-sm md:text-base opacity-90 leading-relaxed whitespace-pre-line mb-5">{{ $body }}</p>
            @endif

            <div class="flex flex-wrap items-center gap-3">
                @if ($cta && $popup->cta_url)
                    <a href="{{ $popup->cta_url }}"
                       @click="trackClick"
                       @if ($popup->cta_external) target="_blank" rel="noopener noreferrer" @endif
                       class="inline-flex items-center gap-2 font-bold px-5 py-2.5 rounded-xl shadow transition
                              {{ in_array($theme, ['minimal', 'side_card']) ? 'bg-indigo-600 hover:bg-indigo-700 text-white' : 'bg-white text-indigo-700 hover:bg-indigo-50' }}">
                        {{ $cta }} →
                    </a>
                @endif

                @if ($secondary)
                    <button @click="dismiss" class="text-sm font-semibold opacity-75 hover:opacity-100 underline">
                        {{ $secondary }}
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>

@once
    @push('scripts')
    <script>
    function popupController(cfg) {
        return {
            open: false,
            init() {
                // Cookie check first — if dismissed, skip entirely
                if (this.isDismissed()) return;

                // Increment view counter (one-shot per session)
                this.trackView();

                // Wire trigger
                if (cfg.trigger === 'page_load') {
                    this.show();
                } else if (cfg.trigger === 'time_5s' || cfg.trigger === 'time_15s') {
                    setTimeout(() => this.show(), cfg.delay || 5000);
                } else if (cfg.trigger === 'scroll_50') {
                    const onScroll = () => {
                        const scrolled = window.scrollY + window.innerHeight;
                        if (scrolled >= document.body.scrollHeight * 0.5) {
                            this.show();
                            window.removeEventListener('scroll', onScroll);
                        }
                    };
                    window.addEventListener('scroll', onScroll, { passive: true });
                } else if (cfg.trigger === 'exit_intent') {
                    let triggered = false;
                    document.addEventListener('mouseout', (e) => {
                        if (triggered) return;
                        if (e.clientY <= 0 || e.relatedTarget === null) {
                            triggered = true;
                            this.show();
                        }
                    });
                } else {
                    setTimeout(() => this.show(), cfg.delay || 5000);
                }
            },
            show() { this.open = true; },
            dismiss() {
                this.open = false;
                this.setDismissCookie();
                this.trackDismiss();
            },
            trackClick() {
                this.setDismissCookie();
                fetch('/popups/' + cfg.popupId + '/track/click', { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || '' } }).catch(() => {});
            },
            trackView()    { fetch('/popups/' + cfg.popupId + '/track/view',    { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || '' } }).catch(() => {}); },
            trackDismiss() { fetch('/popups/' + cfg.popupId + '/track/dismiss', { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || '' } }).catch(() => {}); },
            isDismissed() {
                const v = document.cookie.split('; ').find(r => r.startsWith(cfg.cookieKey + '='));
                return !!v;
            },
            setDismissCookie() {
                if (cfg.dismissDays <= 0) return;
                const d = new Date();
                d.setTime(d.getTime() + cfg.dismissDays * 24 * 60 * 60 * 1000);
                document.cookie = cfg.cookieKey + '=1; expires=' + d.toUTCString() + '; path=/; SameSite=Lax';
            },
        };
    }
    </script>
    @endpush
@endonce
