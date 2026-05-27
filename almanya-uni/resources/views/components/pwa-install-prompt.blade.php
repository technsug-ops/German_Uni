{{--
    PWA install prompt — bottom-sheet style.
    Behavior:
      - Android/Chrome: captures `beforeinstallprompt`, shows banner with native install button
      - iOS Safari: detects iOS UA, shows "Add to Home Screen" instructions (iOS has no API)
      - Already installed (display-mode: standalone): never shows
      - Dismissed: 14-day cooldown stored in localStorage
      - First-time UX: appears 8s after page load, not immediately (less aggressive)
--}}
<div id="pwaInstallPrompt"
     class="hidden fixed bottom-3 left-3 right-3 md:left-auto md:right-4 md:max-w-sm z-[60] bg-white border border-gray-200 rounded-2xl shadow-2xl"
     role="dialog" aria-labelledby="pwaPromptTitle" aria-describedby="pwaPromptDesc">
    <div class="p-4">
        <div class="flex items-start gap-3 mb-3">
            <div class="shrink-0 w-12 h-12 bg-primary-50 rounded-xl flex items-center justify-center text-2xl">
                📲
            </div>
            <div class="flex-1 min-w-0">
                <p id="pwaPromptTitle" class="font-bold text-gray-900 leading-tight mb-1">
                    {{ __(':brand on your home screen', ['brand' => brand('name')]) }}
                </p>
                <p id="pwaPromptDesc" class="text-xs text-gray-600 leading-snug">
                    {{ __('Install as an app — faster, offline-ready, no app store needed.') }}
                </p>
            </div>
            <button type="button" id="pwaPromptClose"
                    class="shrink-0 w-7 h-7 rounded-full hover:bg-gray-100 flex items-center justify-center text-gray-400 hover:text-gray-700"
                    aria-label="{{ __('Close') }}">
                ✕
            </button>
        </div>

        {{-- Android/Chrome native install --}}
        <div id="pwaPromptAndroid" class="hidden">
            <button type="button" id="pwaInstallBtn"
                    class="w-full px-4 py-2.5 bg-primary-600 hover:bg-primary-700 text-white font-semibold text-sm rounded-lg transition">
                ⬇ {{ __('Install app') }}
            </button>
        </div>

        {{-- iOS manual instructions --}}
        <div id="pwaPromptIOS" class="hidden text-xs text-gray-700 leading-relaxed bg-gray-50 border border-gray-200 rounded-lg p-3">
            <p class="font-semibold mb-1">{{ __('Add to Home Screen on iOS:') }}</p>
            <ol class="list-decimal list-inside space-y-0.5">
                <li>{!! __('Tap the <strong>Share</strong> button in Safari (square with arrow)') !!}</li>
                <li>{!! __('Scroll down and choose <strong>"Add to Home Screen"</strong>') !!}</li>
                <li>{{ __('Tap "Add" — the icon will appear like any app') }}</li>
            </ol>
        </div>
    </div>
</div>

@once
@push('scripts')
<script>
(function () {
    const PROMPT = document.getElementById('pwaInstallPrompt');
    if (!PROMPT) return;

    const STORAGE_KEY = 'pwa_install_dismissed_at';
    const COOLDOWN_DAYS = 14;
    const DELAY_MS = 8000;

    // Helpers
    const isStandalone = () =>
        window.matchMedia('(display-mode: standalone)').matches ||
        window.navigator.standalone === true;

    const isIOS = () => {
        const ua = (window.navigator.userAgent || '').toLowerCase();
        return /iphone|ipad|ipod/.test(ua) && !window.MSStream;
    };

    const wasDismissedRecently = () => {
        try {
            const at = parseInt(localStorage.getItem(STORAGE_KEY) || '0', 10);
            if (!at) return false;
            const days = (Date.now() - at) / (1000 * 60 * 60 * 24);
            return days < COOLDOWN_DAYS;
        } catch (e) { return false; }
    };

    const dismiss = () => {
        try { localStorage.setItem(STORAGE_KEY, String(Date.now())); } catch (e) {}
        PROMPT.classList.add('hidden');
    };

    // Don't show if already installed or recently dismissed
    if (isStandalone() || wasDismissedRecently()) return;

    // Close button
    document.getElementById('pwaPromptClose')?.addEventListener('click', dismiss);

    // ── Android / Chrome path ──
    let deferredPrompt = null;
    window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault();
        deferredPrompt = e;
        setTimeout(() => {
            document.getElementById('pwaPromptAndroid').classList.remove('hidden');
            PROMPT.classList.remove('hidden');
        }, DELAY_MS);
    });

    document.getElementById('pwaInstallBtn')?.addEventListener('click', async () => {
        if (!deferredPrompt) return;
        deferredPrompt.prompt();
        const { outcome } = await deferredPrompt.userChoice;
        deferredPrompt = null;
        // Both accept & dismiss count as "done" for now
        dismiss();
        if (outcome === 'accepted' && window.gtag) {
            window.gtag('event', 'pwa_install', { method: 'banner' });
        }
    });

    window.addEventListener('appinstalled', () => {
        dismiss();
        PROMPT.classList.add('hidden');
    });

    // ── iOS path ──
    if (isIOS()) {
        setTimeout(() => {
            document.getElementById('pwaPromptIOS').classList.remove('hidden');
            PROMPT.classList.remove('hidden');
        }, DELAY_MS);
    }
})();
</script>
@endpush
@endonce
