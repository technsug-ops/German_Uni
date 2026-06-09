@props([
    'source' => 'sticky-footer',
])

@auth
    {{-- Already logged-in users get the newsletter weekly anyway through their account flow --}}
@else
@php
    // Hide on dismissable routes (admin, login, register, popup-conflict pages, legal)
    $currentPath = '/' . trim(request()->path(), '/');
    $hideOn = ['admin', 'login', 'register', 'password', '_system', 'newsletter'];
    $shouldHide = false;
    foreach ($hideOn as $segment) {
        if (str_contains($currentPath, '/' . $segment) || str_starts_with($currentPath, '/' . $segment)) {
            $shouldHide = true;
            break;
        }
    }
@endphp

@if (! $shouldHide)
<div
    x-data="newsletterSticky()"
    x-show="open"
    x-cloak
    x-transition:enter="transition ease-out duration-400"
    x-transition:enter-start="translate-y-full opacity-0"
    x-transition:enter-end="translate-y-0 opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="translate-y-0 opacity-100"
    x-transition:leave-end="translate-y-full opacity-0"
    class="fixed bottom-0 inset-x-0 z-[70] pointer-events-none px-3 pb-3 md:px-6 md:pb-4"
>
    <div class="pointer-events-auto max-w-3xl mx-auto bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-500 text-white rounded-2xl shadow-2xl ring-1 ring-white/10 overflow-hidden"
         x-show="!expanded">
        <div class="flex items-center gap-3 p-3 md:p-4">
            <div class="flex-1 min-w-0">
                <p class="font-bold text-sm md:text-base leading-tight">
                    {{ __('Weekly Germany guide — 1–2 emails/week, no spam.') }}
                </p>
                <p class="text-xs text-indigo-100 mt-0.5 hidden md:block">
                    {{ __('New posts, deadlines, scholarship alerts.') }}
                </p>
            </div>
            <button
                @click="expanded = true"
                class="shrink-0 bg-white text-indigo-700 hover:bg-indigo-50 font-bold text-xs md:text-sm px-3 md:px-4 py-2 rounded-lg shadow-sm transition whitespace-nowrap"
            >
                {{ __('Subscribe') }} →
            </button>
            <button
                @click="dismiss"
                class="shrink-0 w-8 h-8 rounded-full hover:bg-white/15 flex items-center justify-center text-lg transition"
                aria-label="{{ __('Close') }}"
            >×</button>
        </div>
    </div>

    {{-- Expanded form (uses existing newsletter-form pattern but inline-minimal) --}}
    <div class="pointer-events-auto max-w-3xl mx-auto bg-gradient-to-br from-indigo-700 via-purple-700 to-pink-600 text-white rounded-2xl shadow-2xl ring-1 ring-white/10 overflow-hidden p-4 md:p-5 relative"
         x-show="expanded"
         x-transition>
        <button
            @click="dismiss"
            class="absolute top-3 right-3 w-8 h-8 rounded-full hover:bg-white/15 flex items-center justify-center text-lg transition"
            aria-label="{{ __('Close') }}"
        >×</button>

        <p class="font-extrabold text-base md:text-lg mb-1 flex items-center gap-2">{{ __('Weekly Germany guide delivered to your inbox') }}</p>
        <p class="text-xs text-indigo-100 mb-3">{{ __('No spam. Unsubscribe anytime.') }}</p>

        <form method="POST" action="{{ route('newsletter.subscribe') }}" class="space-y-2" @submit.prevent="submit($event)" x-ref="form">
            @csrf
            <input type="hidden" name="source" value="{{ $source }}">
            <div style="position:absolute;left:-9999px;opacity:0;" aria-hidden="true">
                <input type="text" name="website" tabindex="-1" autocomplete="off">
            </div>

            <div class="flex flex-col sm:flex-row gap-2">
                <input
                    type="email"
                    name="email"
                    required
                    placeholder="{{ __('Your email address') }}"
                    class="flex-1 px-3 py-2 rounded-lg bg-white/15 backdrop-blur ring-1 ring-white/25 placeholder-white/60 text-white text-sm focus:bg-white/20 focus:ring-white/50 focus:outline-none"
                >
                <button type="submit" class="bg-white text-indigo-700 hover:bg-indigo-50 font-bold px-4 py-2 rounded-lg shadow-sm text-sm whitespace-nowrap transition" :disabled="sending">
                    <span x-show="!sending">{{ __('Subscribe') }} →</span>
                    <span x-show="sending">{{ __('Sending...') }}</span>
                </button>
            </div>

            <div class="flex items-center gap-2 text-[11px] text-indigo-100">
                <label for="nls-lang" class="shrink-0">{{ __('Newsletter language') }}:</label>
                <select id="nls-lang" name="language" class="px-2 py-1.5 rounded-lg bg-white text-gray-900 text-sm ring-1 ring-white/25 focus:outline-none">
                    @foreach (config('locale.locales', []) as $code => $cfg)
                        @if ($cfg['active'] ?? false)
                            <option value="{{ $code }}" @selected(app()->getLocale() === $code)>{{ $cfg['native_name'] ?? strtoupper($code) }}</option>
                        @endif
                    @endforeach
                </select>
            </div>

            <label class="flex items-start gap-2 text-[11px] text-indigo-100 cursor-pointer">
                <input type="checkbox" name="gdpr_consent" value="1" required class="mt-0.5 rounded">
                <span>{{ __('I consent to receive newsletter emails.') }}</span>
            </label>

            <x-math-captcha compact />

            <p x-show="message" x-text="message" :class="success ? 'text-emerald-200' : 'text-rose-200'" class="text-xs mt-2"></p>
        </form>
    </div>
</div>

@push('scripts')
<script>
function newsletterSticky() {
    return {
        open: false,
        expanded: false,
        sending: false,
        message: '',
        success: false,
        cookieKey: 'newsletter_sticky_dismissed',
        init() {
            // Hide if already dismissed (cookie set)
            if (this.isDismissed()) return;
            // Slide in after 12 s of reading time
            setTimeout(() => { this.open = true; }, 12000);
        },
        dismiss() {
            this.open = false;
            this.expanded = false;
            const d = new Date();
            d.setTime(d.getTime() + 14 * 24 * 60 * 60 * 1000); // 14-day cool-off
            document.cookie = this.cookieKey + '=1; expires=' + d.toUTCString() + '; path=/; SameSite=Lax';
        },
        isDismissed() {
            return document.cookie.split('; ').some(r => r.startsWith(this.cookieKey + '='));
        },
        async submit(e) {
            this.sending = true;
            this.message = '';
            try {
                const form = this.$refs.form;
                const fd = new FormData(form);
                const r = await fetch(form.action, {
                    method: 'POST',
                    body: fd,
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                });
                const data = await r.json();
                this.success = !!data.ok;
                this.message = data.message || '';
                if (data.ok) {
                    setTimeout(() => this.dismiss(), 3500); // give them time to read success
                }
            } catch (err) {
                this.success = false;
                this.message = @json(__('Connection error. Try again in a moment.'));
            } finally {
                this.sending = false;
            }
        }
    };
}
</script>
@endpush
@endif
@endauth
