@extends('layouts.app')

@section('title', $provider->name . ' ' . __('Blocked Account (Sperrkonto) — Review & Prices') . '  — ' . brand('name'))

<x-seo
    :title="$provider->name . ' ' . __('Sperrkonto Review')"
    :description="$provider->description ?: $provider->name . ' ' . __('blocked account provider — detailed review. Price, activation time, features and advantages.')"
/>

@section('content')
{{-- HERO --}}
<section class="bg-gradient-to-br from-indigo-700 via-blue-600 to-cyan-500 text-white">
    <div class="max-w-[1400px] mx-auto px-4 py-10">
        <nav class="text-sm text-blue-100 mb-3">
            <a href="/" class="hover:text-white">{{ __('Home') }}</a>
            <span class="mx-2 opacity-50">›</span>
            <a href="{{ route('tools.index') }}" class="hover:text-white">{{ __('Tools') }}</a>
            <span class="mx-2 opacity-50">›</span>
            <a href="{{ route('tools.blocked-account') }}" class="hover:text-white">{{ __('Blocked Account') }}</a>
            <span class="mx-2 opacity-50">›</span>
            <span class="text-white">{{ $provider->name }}</span>
        </nav>

        <div class="flex flex-col md:flex-row md:items-center gap-5">
            @if ($provider->logo_url)
                <div class="bg-white rounded-xl p-3 shrink-0">
                    <img src="{{ $provider->logo_url }}" alt="{{ $provider->name }}" class="h-16 max-w-[200px] object-contain" loading="lazy">
                </div>
            @else
                <div class="w-20 h-20 rounded-xl bg-white/15 backdrop-blur ring-1 ring-white/25 flex items-center justify-center text-3xl font-extrabold shrink-0">
                    {{ mb_substr($provider->name, 0, 2) }}
                </div>
            @endif
            <div class="flex-1">
                <h1 class="text-3xl md:text-4xl font-extrabold leading-tight drop-shadow">
                    {{ $provider->name }}
                </h1>
                <p class="mt-1 text-blue-100">
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-white/15 ring-1 ring-white/20 text-sm">
                        {{ $provider->type_emoji }} {{ $provider->type_label }}
                    </span>
                    @if ($provider->backend_bank)
                        <span class="ml-2 text-sm opacity-90">{{ __('Bank:') }} {{ $provider->backend_bank }}</span>
                    @endif
                </p>
                @if ($provider->description)
                    <p class="mt-3 text-lg text-blue-50 max-w-3xl">{{ $provider->description }}</p>
                @endif
            </div>
        </div>
    </div>
</section>

<div class="max-w-[1400px] mx-auto px-4 py-10 grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-8">
    {{-- ANA İÇERİK --}}
    <div class="space-y-8">
        {{-- FACT BOX --}}
        <section class="bg-white border border-gray-200 rounded-xl p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">💰 {{ __('Pricing') }}</h2>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide font-semibold">{{ __('Setup fee') }}</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $provider->setup_fee_eur ? '€' . number_format((float)$provider->setup_fee_eur, 2) : '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide font-semibold">{{ __('Monthly service') }}</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $provider->monthly_fee_eur ? '€' . number_format((float)$provider->monthly_fee_eur, 2) : '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide font-semibold">{{ __('1st year total') }}</p>
                    <p class="text-2xl font-bold text-primary-700 mt-1">{{ $provider->first_year_cost_eur ? '€' . number_format($provider->first_year_cost_eur, 0) : '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide font-semibold">{{ __('Activation') }}</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $provider->activation_range }}</p>
                </div>
                @if ($provider->required_yearly_deposit_eur)
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide font-semibold">{{ __('Yearly deposit') }}</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">€{{ number_format($provider->required_yearly_deposit_eur, 0, ',', '.') }}</p>
                    </div>
                @endif
                @if ($provider->monthly_withdrawal_limit_eur)
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide font-semibold">{{ __('Monthly withdrawal') }}</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">€{{ number_format($provider->monthly_withdrawal_limit_eur, 0, ',', '.') }}</p>
                    </div>
                @endif
            </div>
        </section>

        {{-- SAĞLIK SİGORTASI --}}
        @if ($provider->combo_insurance)
            <section class="bg-emerald-50 border border-emerald-200 rounded-xl p-6">
                <h2 class="text-xl font-bold text-emerald-900 mb-2">🏥 {{ __('Health Insurance Combo') }}</h2>
                <p class="text-emerald-800">
                    {{ __('This provider offers a health insurance combo package together with the blocked account.') }}
                    @if ($provider->insurance_provider_name)
                        <strong>{{ __('Insurance provider(s):') }}</strong> {{ $provider->insurance_provider_name }}.
                    @endif
                    @if ($provider->insurance_monthly_eur)
                        {{ __('Monthly add-on fee:') }} <strong>€{{ number_format((float)$provider->insurance_monthly_eur, 2) }}</strong>.
                    @endif
                </p>
            </section>
        @endif

        {{-- PROS / CONS --}}
        @if ((is_array($provider->pros) && count($provider->pros)) || (is_array($provider->cons) && count($provider->cons)))
            <section class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @if (is_array($provider->pros) && count($provider->pros))
                    <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-5">
                        <h3 class="font-bold text-emerald-900 mb-3">✓ {{ __('Pros') }}</h3>
                        <ul class="space-y-2">
                            @foreach ($provider->pros as $pro)
                                <li class="flex items-start gap-2 text-emerald-800">
                                    <span class="text-emerald-500 mt-0.5">✓</span>
                                    <span>{{ $pro }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @if (is_array($provider->cons) && count($provider->cons))
                    <div class="bg-red-50 border border-red-200 rounded-xl p-5">
                        <h3 class="font-bold text-red-900 mb-3">✗ {{ __('Cons') }}</h3>
                        <ul class="space-y-2">
                            @foreach ($provider->cons as $con)
                                <li class="flex items-start gap-2 text-red-800">
                                    <span class="text-red-500 mt-0.5">✗</span>
                                    <span>{{ $con }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </section>
        @endif

        {{-- UZUN AÇIKLAMA --}}
        @if ($provider->description_long)
            <section class="prose prose-sm max-w-none bg-white border border-gray-200 rounded-xl p-6">
                <h2>📖 {{ __('Detailed Review') }}</h2>
                {!! \Illuminate\Support\Str::markdown($provider->description_long) !!}
            </section>
        @endif

        {{-- EK ÖZELLİKLER --}}
        @if (is_array($provider->features) && count($provider->features))
            <section class="bg-white border border-gray-200 rounded-xl p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-3">✨ {{ __('Additional Features') }}</h2>
                <div class="flex flex-wrap gap-2">
                    @foreach ($provider->features as $f)
                        <span class="px-3 py-1 text-sm rounded-full bg-gray-100 text-gray-800">{{ $f }}</span>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- VİZE / TÜRK ÖĞRENCİ --}}
        @if ($provider->visa_recognition_note || $provider->turkish_students_note)
            <section class="bg-blue-50 border border-blue-200 rounded-xl p-6 space-y-4">
                @if ($provider->visa_recognition_note)
                    <div>
                        <h3 class="font-bold text-blue-900 mb-1">🛂 {{ __('Visa Recognition') }}</h3>
                        <p class="text-blue-800">{{ $provider->visa_recognition_note }}</p>
                    </div>
                @endif
                @if ($provider->turkish_students_note)
                    <div>
                        @if (app()->getLocale() === 'tr')
                            <h3 class="font-bold text-blue-900 mb-1">🇹🇷 Türk Öğrenciler İçin</h3>
                        @else
                            <h3 class="font-bold text-blue-900 mb-1">🇹🇷 {{ __('Notes for Turkish Students') }}</h3>
                        @endif
                        <p class="text-blue-800">{{ $provider->turkish_students_note }}</p>
                    </div>
                @endif
            </section>
        @endif

        {{-- BENZER SAĞLAYICILAR --}}
        @if ($similar->isNotEmpty())
            <section>
                <h2 class="text-xl font-bold text-gray-900 mb-4">{{ __('Similar :type providers', ['type' => $provider->type_label]) }}</h2>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    @foreach ($similar as $s)
                        <a href="{{ route('tools.blocked-account.show', $s->slug) }}"
                           class="block bg-white border border-gray-200 rounded-xl p-4 hover:border-primary-400 hover:shadow-sm transition">
                            <p class="font-bold text-gray-900">{{ $s->name }}</p>
                            <p class="text-xs text-gray-500 mt-1">
                                @if ($s->first_year_cost_eur)
                                    {{ __('1st year: €:cost', ['cost' => number_format($s->first_year_cost_eur, 0)]) }}
                                @else
                                    {{ __('Click for details') }}
                                @endif
                            </p>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif
    </div>

    {{-- SIDEBAR --}}
    <aside class="space-y-4">
        <div class="bg-white border border-gray-200 rounded-xl p-5 sticky top-20">
            @if ($provider->cta_url)
                <a href="{{ $provider->cta_url }}" target="_blank" rel="noopener sponsored"
                   class="block text-center bg-primary-600 hover:bg-primary-700 text-white font-bold py-3 px-4 rounded-lg transition shadow-sm">
                    {{ __('Apply to :name →', ['name' => $provider->name]) }}
                </a>
                @if ($provider->affiliate_url)
                    <p class="text-[10px] text-gray-400 mt-1 text-center">{{ __('Affiliate link · commission possible') }}</p>
                @endif
            @endif

            <div class="mt-4 pt-4 border-t border-gray-100 space-y-2 text-sm">
                @if ($provider->has_mobile_app)
                    <div class="flex items-center gap-2 text-gray-700">
                        <span>📱</span><span>{{ __('Mobile app') }}</span>
                    </div>
                @endif
                @if ($provider->bafin_licensed)
                    <div class="flex items-center gap-2 text-gray-700">
                        <span>✓</span><span>{{ __('BaFin licensed') }}</span>
                    </div>
                @endif
                @if (is_array($provider->supported_languages) && count($provider->supported_languages))
                    <div class="text-gray-700">
                        <p class="font-semibold mb-1">🌐 {{ __('Support languages') }}</p>
                        <div class="flex flex-wrap gap-1">
                            @foreach ($provider->supported_languages as $lang)
                                <span class="px-2 py-0.5 text-xs rounded bg-gray-100 uppercase">{{ $lang }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif
                @if ($provider->website_url)
                    <a href="{{ $provider->website_url }}" target="_blank" rel="noopener"
                       class="block text-center mt-3 text-primary-600 hover:underline text-sm">
                        {{ __('Official website →') }}
                    </a>
                @endif
            </div>

            @if ($provider->last_verified_at)
                <p class="text-[10px] text-gray-400 mt-3 text-center">
                    {{ __('Information last updated: :date', ['date' => $provider->last_verified_at->format('Y-m-d')]) }}
                </p>
            @endif
        </div>

        <a href="{{ route('tools.blocked-account') }}"
           class="block text-center bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold py-2.5 px-4 rounded-lg transition">
            ← {{ __('All providers') }}
        </a>
    </aside>
</div>
@endsection
