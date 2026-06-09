@props([
    'source' => 'unknown',
    'variant' => 'card',       // card | inline | dark
    'heading' => null,
    'subheading' => null,
    'showName' => true,
])

@php
    $heading = $heading ?? __('Weekly Germany guide delivered to your inbox');
    $subheading = $subheading ?? __('New blog posts, deadline reminders, scholarship alerts. No spam. Unsubscribe anytime.');
    $variants = [
        'card' => [
            'wrap'  => 'bg-gradient-to-br from-primary-50 via-white to-accent-50 border border-primary-100 rounded-2xl p-6 md:p-8 shadow-sm',
            'title' => 'text-2xl md:text-3xl font-extrabold text-gray-900 mb-2',
            'desc'  => 'text-gray-700 mb-5',
            'btn'   => 'bg-accent-500 hover:bg-accent-600 text-white',
        ],
        'inline' => [
            'wrap'  => 'bg-white border border-gray-200 rounded-xl p-5',
            'title' => 'text-lg font-bold text-gray-900 mb-1',
            'desc'  => 'text-sm text-gray-600 mb-4',
            'btn'   => 'bg-primary-700 hover:bg-primary-800 text-white',
        ],
        'dark' => [
            'wrap'  => 'bg-gradient-to-br from-primary-800 to-primary-900 text-white rounded-2xl p-6 md:p-8 shadow-lg',
            'title' => 'text-2xl md:text-3xl font-extrabold text-white mb-2',
            'desc'  => 'text-primary-100 mb-5',
            'btn'   => 'bg-accent-500 hover:bg-accent-600 text-white',
        ],
    ];
    $v = $variants[$variant] ?? $variants['card'];
    $formId = 'nl-' . uniqid();
@endphp

<div class="{{ $v['wrap'] }}" data-newsletter-form>
    <h3 class="{{ $v['title'] }}">📬 {{ $heading }}</h3>
    <p class="{{ $v['desc'] }}">{{ $subheading }}</p>

    <form id="{{ $formId }}" action="{{ route('newsletter.subscribe') }}" method="POST" class="space-y-3" novalidate>
        @csrf
        <input type="hidden" name="source" value="{{ $source }}">

        {{-- 🍯 honeypot --}}
        <div style="position:absolute;left:-9999px;top:-9999px;opacity:0;pointer-events:none;" aria-hidden="true">
            <label>{{ __('Website (leave empty):') }} <input type="text" name="website" tabindex="-1" autocomplete="off"></label>
        </div>

        @if ($showName)
            <input type="text" name="name" placeholder="{{ __('Your name (optional)') }}" maxlength="100"
                   class="w-full px-4 py-3 rounded-lg border {{ $variant === 'dark' ? 'bg-white/10 border-white/20 text-white placeholder-white/60' : 'bg-white border-gray-300 text-gray-900' }} focus:outline-none focus:ring-2 focus:ring-primary-500"
                   value="{{ old('name') }}">
        @endif

        <div class="flex flex-col sm:flex-row gap-2">
            <input type="email" name="email" required placeholder="{{ __('Your email address') }}" maxlength="191"
                   class="flex-1 px-4 py-3 rounded-lg border {{ $variant === 'dark' ? 'bg-white/10 border-white/20 text-white placeholder-white/60' : 'bg-white border-gray-300 text-gray-900' }} focus:outline-none focus:ring-2 focus:ring-primary-500"
                   value="{{ old('email') }}">
            <button type="submit"
                    class="{{ $v['btn'] }} px-6 py-3 rounded-lg font-bold transition whitespace-nowrap shadow-sm">
                <span data-newsletter-btn-text>{{ __('Subscribe') }} →</span>
                <span data-newsletter-btn-loading class="hidden">{{ __('Sending...') }}</span>
            </button>
        </div>

        {{-- Bülten dili — kullanıcı seçer (yoksa sayfa dili). Bülten BU dilde gönderilir. --}}
        <div class="flex items-center gap-2 text-sm {{ $variant === 'dark' ? 'text-primary-100' : 'text-gray-700' }}">
            <label for="{{ $formId }}-lang" class="shrink-0 font-medium">{{ __('Newsletter language') }}:</label>
            <select id="{{ $formId }}-lang" name="language"
                    class="px-3 py-2 rounded-lg border text-gray-900 bg-white {{ $variant === 'dark' ? 'border-white/30' : 'border-gray-300' }} focus:outline-none focus:ring-2 focus:ring-primary-500">
                @foreach (config('locale.locales', []) as $code => $cfg)
                    @if ($cfg['active'] ?? false)
                        <option value="{{ $code }}" @selected(old('language', app()->getLocale()) === $code)>{{ $cfg['native_name'] ?? strtoupper($code) }}</option>
                    @endif
                @endforeach
            </select>
        </div>

        <label class="flex items-start gap-2 text-xs cursor-pointer {{ $variant === 'dark' ? 'text-primary-100' : 'text-gray-600' }}">
            <input type="checkbox" name="gdpr_consent" value="1" required
                   class="mt-0.5 w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
            <span>
                {{ __('I consent to my email address being used for :brand newsletter delivery.', ['brand' => brand('name')]) }}
                <a href="{{ route('legal.privacy') }}" target="_blank" rel="noopener" class="underline {{ $variant === 'dark' ? 'text-accent-400' : 'text-primary-700' }}">{{ __('Privacy policy') }}</a>
            </span>
        </label>

        <x-math-captcha compact />

        <div data-newsletter-msg class="hidden text-sm rounded-lg p-3"></div>
    </form>
</div>

@once
    @push('scripts')
    <script>
    (function () {
        const i18n = {
            success: @json(__('Success.')),
            error: @json(__('An error occurred.')),
            network: @json(__('Connection error. Try again in a moment.')),
        };
        document.querySelectorAll('[data-newsletter-form]').forEach(function (block) {
            const form = block.querySelector('form');
            if (!form) return;

            const btnText = form.querySelector('[data-newsletter-btn-text]');
            const btnLoad = form.querySelector('[data-newsletter-btn-loading]');
            const msgBox  = form.querySelector('[data-newsletter-msg]');

            form.addEventListener('submit', async function (e) {
                e.preventDefault();
                msgBox.className = 'hidden text-sm rounded-lg p-3';

                btnText.classList.add('hidden');
                btnLoad.classList.remove('hidden');
                form.querySelector('button[type=submit]').disabled = true;

                try {
                    const formData = new FormData(form);
                    const resp = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: formData,
                    });
                    const data = await resp.json();

                    msgBox.textContent = data.message || (resp.ok ? i18n.success : i18n.error);
                    msgBox.className = 'text-sm rounded-lg p-3 ' + (data.ok
                        ? 'bg-green-50 border border-green-200 text-green-800'
                        : 'bg-red-50 border border-red-200 text-red-800');

                    if (data.ok) {
                        form.querySelector('input[name=email]').value = '';
                        const nameI = form.querySelector('input[name=name]');
                        if (nameI) nameI.value = '';
                        form.querySelector('input[name=gdpr_consent]').checked = false;
                    }
                } catch (err) {
                    msgBox.textContent = i18n.network;
                    msgBox.className = 'text-sm rounded-lg p-3 bg-red-50 border border-red-200 text-red-800';
                } finally {
                    btnText.classList.remove('hidden');
                    btnLoad.classList.add('hidden');
                    form.querySelector('button[type=submit]').disabled = false;
                }
            });
        });
    })();
    </script>
    @endpush
@endonce
