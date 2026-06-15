{{--
    Şehir etkinlik bildirimi abone formu.
    Değişkenler:
      $city        (opsiyonel) — set ise tek şehir (hidden city_id), değilse dropdown
      $alertCities (opsiyonel) — dropdown için şehir listesi (id, name accessor)
--}}
@php $city = $city ?? null; $alertCities = $alertCities ?? collect(); @endphp

<div class="bg-gradient-to-br from-rose-50 to-orange-50 border border-rose-200 rounded-2xl p-5 md:p-6">
    <div class="flex items-start gap-3 mb-3">
        <span class="text-2xl" aria-hidden="true">📩</span>
        <div>
            <h3 class="font-bold text-gray-900 leading-snug">
                @if ($city)
                    {{ __("Don't miss events in :city", ['city' => $city->name]) }}
                @else
                    {{ __('Get event alerts for your city') }}
                @endif
            </h3>
            <p class="text-sm text-gray-600">{{ __('A weekly email with new concerts, theatre and cultural events.') }}</p>
        </div>
    </div>

    @if (session('event_alert_success'))
        <p class="mb-3 text-sm font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-lg px-3 py-2">{{ session('event_alert_success') }}</p>
    @endif
    @if (session('event_alert_error'))
        <p class="mb-3 text-sm font-semibold text-rose-700 bg-rose-50 border border-rose-200 rounded-lg px-3 py-2">{{ session('event_alert_error') }}</p>
    @endif
    @error('email')
        <p class="mb-3 text-sm font-semibold text-rose-700">{{ $message }}</p>
    @enderror

    <form method="POST" action="{{ route('events.alerts.subscribe') }}" class="space-y-3">
        @csrf
        <input type="hidden" name="source" value="{{ $city ? 'city_page' : 'events_page' }}">
        {{-- 🍯 honeypot --}}
        <input type="text" name="website" tabindex="-1" autocomplete="off" class="hidden" aria-hidden="true">

        @if ($city)
            <input type="hidden" name="city_id" value="{{ $city->id }}">
        @else
            <select name="city_id" required class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:border-rose-500 focus:ring-1 focus:ring-rose-500">
                <option value="">{{ __('Select a city…') }}</option>
                @foreach ($alertCities as $ac)
                    <option value="{{ $ac->id }}" @selected((string) old('city_id') === (string) $ac->id)>{{ $ac->name }}</option>
                @endforeach
            </select>
        @endif

        <input type="email" name="email" required value="{{ old('email', auth()->user()->email ?? '') }}"
               placeholder="{{ __('your@email.com') }}"
               class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:border-rose-500 focus:ring-1 focus:ring-rose-500">

        <label class="flex items-start gap-2 text-xs text-gray-600">
            <input type="checkbox" name="gdpr_consent" value="1" required class="mt-0.5">
            <span>{{ __('I agree to receive event emails and accept the privacy terms. I can unsubscribe any time.') }}</span>
        </label>

        <button type="submit"
                class="w-full inline-flex items-center justify-center gap-2 bg-rose-600 hover:bg-rose-700 text-white font-bold px-5 py-2.5 rounded-lg shadow-sm transition">
            🔔 {{ __('Notify me') }}
        </button>
    </form>

    @if ($city && config('services.webpush.public_key'))
        {{-- Tarayıcı push aboneliği (anlık bildirim, email'e ek). VAPID yoksa/JS yoksa/izin yok → gizlenir --}}
        <div class="mt-3 pt-3 border-t border-rose-200/60 text-center">
            <button type="button"
                    data-push-alert
                    data-city-id="{{ $city->id }}"
                    data-subscribe-url="{{ route('events.alerts.push.subscribe') }}"
                    data-msg-loading="{{ __('Enabling…') }}"
                    data-msg-done="{{ __('✅ Browser notifications on') }}"
                    data-msg-denied="{{ __('Notifications blocked in your browser') }}"
                    data-msg-error="{{ __('Could not enable notifications') }}"
                    class="inline-flex items-center justify-center gap-1.5 text-sm font-semibold text-rose-700 hover:text-rose-900 disabled:opacity-60">
                <span data-push-label>🔔 {{ __('Or: instant browser notifications') }}</span>
            </button>
            <p data-push-note="{{ $city->id }}" class="text-xs text-gray-500 mt-1"></p>
        </div>
    @endif
</div>
