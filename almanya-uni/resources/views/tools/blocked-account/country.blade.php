@extends('layouts.app')

@section('title', __('Blocked Account for :demonym Students — Germany 2026', ['demonym' => $country['demonym_' . app()->getLocale()] ?? $country['demonym_en']]) . ' — ' . brand('name'))

<x-seo
    :title="__('Blocked Account for :demonym Students — Germany Sperrkonto Guide 2026', ['demonym' => $country['demonym_' . app()->getLocale()] ?? $country['demonym_en']])"
    :description="__('Open a German Sperrkonto from :country in 2026. Compare providers, required amount €11,904, step-by-step guide for :demonym students.', ['country' => $country['name'], 'demonym' => $country['demonym_' . app()->getLocale()] ?? $country['demonym_en']])"
/>

@section('content')

{{-- HERO --}}
<section class="bg-gradient-to-br from-indigo-700 via-blue-600 to-cyan-500 text-white">
    <div class="max-w-[1400px] mx-auto px-4 py-10 md:py-14">
        <nav class="text-sm text-blue-100 mb-3">
            <a href="{{ route('home') }}" class="hover:text-white">{{ __('Home') }}</a>
            <span class="mx-2 opacity-50">›</span>
            <a href="{{ route('tools.blocked-account') }}" class="hover:text-white">{{ __('Blocked Account Finder') }}</a>
            <span class="mx-2 opacity-50">›</span>
            <span class="text-white">{{ $country['name'] }}</span>
        </nav>
        <h1 class="text-3xl md:text-5xl font-extrabold leading-tight drop-shadow mb-3">
            {{ $country['flag'] }} {{ __('Blocked Account for :demonym Students', ['demonym' => $country['demonym_' . app()->getLocale()] ?? $country['demonym_en']]) }}
        </h1>
        <p class="text-lg text-blue-100 max-w-3xl">
            {!! __('You need <strong class="text-white">€11,904</strong> in a German Sperrkonto for the student visa. This page compares providers that work for applicants from :country and shows specific steps.', ['country' => $country['name']]) !!}
        </p>
    </div>
</section>

<div class="max-w-[1400px] mx-auto px-4 py-10">
    {{-- COUNTRY-SPECIFIC NOTE --}}
    <div class="bg-amber-50 border-l-4 border-amber-400 rounded-r-lg p-5 mb-8">
        <div class="flex gap-3 items-start">
            <span class="text-2xl">{{ $country['flag'] }}</span>
            <div>
                <p class="font-bold text-amber-900 mb-1">{{ __('Important for :country applicants', ['country' => $country['name']]) }}</p>
                <p class="text-sm text-amber-800 leading-relaxed">{{ $country['note_focus'] }}</p>
            </div>
        </div>
    </div>

    {{-- SUMMARY CARDS --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-10">
        <div class="bg-white border border-gray-200 rounded-xl p-4">
            <p class="text-2xl font-bold text-gray-900">€11,904</p>
            <p class="text-xs text-gray-500 mt-1">{{ __('Required deposit (12 mo)') }}</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-4">
            <p class="text-2xl font-bold text-gray-900">€992</p>
            <p class="text-xs text-gray-500 mt-1">{{ __('Monthly limit') }}</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-4">
            <p class="text-2xl font-bold text-emerald-700">{{ $providers->count() }}</p>
            <p class="text-xs text-gray-500 mt-1">{{ __('Providers for :country', ['country' => $country['name']]) }}</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-4">
            <p class="text-2xl font-bold text-blue-700">1-7 {{ __('days') }}</p>
            <p class="text-xs text-gray-500 mt-1">{{ __('Typical activation') }}</p>
        </div>
    </div>

    {{-- PROVIDERS --}}
    <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ __('Recommended Providers') }}</h2>
    @if ($providers->isEmpty())
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-6">
            <p class="text-amber-900">{{ __('No providers currently match :country.', ['country' => $country['name']]) }}</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-12">
            @foreach ($providers as $p)
                <a href="{{ route('tools.blocked-account.show', $p->slug) }}"
                   class="group bg-white border border-gray-200 hover:border-primary-400 hover:shadow-md transition rounded-xl p-5">
                    <div class="flex items-start gap-4">
                        @if ($p->logo_url)
                            <img src="{{ $p->logo_url }}" alt="{{ $p->name }}" class="w-12 h-12 object-contain rounded">
                        @else
                            <div class="w-12 h-12 bg-gray-100 rounded flex items-center justify-center font-bold text-gray-600">{{ substr($p->name, 0, 2) }}</div>
                        @endif
                        <div class="flex-1 min-w-0">
                            <h3 class="font-bold text-gray-900 group-hover:text-primary-700 transition">{{ $p->name }}</h3>
                            <p class="text-xs text-gray-500 mt-0.5">{{ $p->type_label ?? $p->type }}</p>
                            <div class="flex items-center gap-3 mt-2 text-sm">
                                <span class="font-mono text-emerald-700">€{{ number_format($p->first_year_cost_eur ?? 0, 0, ',', '.') }}</span>
                                @if ($p->activation_days_max)
                                    <span class="text-gray-500">⚡ {{ $p->activation_days_max }} {{ __('days') }}</span>
                                @endif
                                @if ($p->combo_insurance)
                                    <span class="bg-emerald-100 text-emerald-800 px-1.5 py-0.5 rounded text-xs font-semibold">{{ __('+ Insurance') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @endif

    {{-- STEP-BY-STEP --}}
    <section class="bg-gradient-to-br from-gray-50 to-white border border-gray-200 rounded-2xl p-6 md:p-8 mb-12">
        <h2 class="text-2xl font-bold text-gray-900 mb-5">{{ __('Step-by-step for :demonym applicants', ['demonym' => $country['demonym_' . app()->getLocale()] ?? $country['demonym_en']]) }}</h2>
        <ol class="space-y-4">
            @foreach ([
                ['1', __('Choose a provider'), __('Compare providers above. Look for: total cost, activation speed, insurance combo, language support.')],
                ['2', __('Open the account online'), __('Apply with passport scan + admission letter (or university offer). Most providers approve in 1-3 days.')],
                ['3', __('Transfer the deposit'), __('Wire €11,904 from your :country bank account. SWIFT transfer takes 2-5 business days.', ['country' => $country['name']])],
                ['4', __('Receive confirmation'), __('Provider sends a "blocked account confirmation" PDF. This is what the visa officer requires.')],
                ['5', __('Attach to visa application'), __('Include the confirmation PDF + bank statement in your visa file at the German embassy.')],
                ['6', __('Withdraw monthly in Germany'), __('After arrival + Anmeldung, link the account to a German current account. Withdraw €992/month.')],
            ] as [$num, $title, $desc])
                <li class="flex gap-4">
                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-primary-600 text-white font-bold flex items-center justify-center">{{ $num }}</div>
                    <div>
                        <h3 class="font-semibold text-gray-900">{{ $title }}</h3>
                        <p class="text-sm text-gray-600 mt-1 leading-relaxed">{{ $desc }}</p>
                    </div>
                </li>
            @endforeach
        </ol>
    </section>

    {{-- CALCULATOR LINK --}}
    <a href="{{ route('tools.blocked-account') }}#sperrkontoCalc"
       class="block bg-gradient-to-r from-emerald-600 to-teal-600 text-white rounded-2xl p-6 mb-12 hover:shadow-lg transition">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="text-2xl font-bold">🧮 {{ __('Calculate your total cost') }}</p>
                <p class="text-emerald-100 text-sm mt-1">{{ __('Pick duration, provider, insurance — see total in seconds.') }}</p>
            </div>
            <span class="text-3xl">→</span>
        </div>
    </a>

    {{-- OTHER COUNTRIES --}}
    <section class="border-t border-gray-200 pt-8">
        <h2 class="text-xl font-bold text-gray-900 mb-4">{{ __('Sperrkonto for other countries') }}</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            @foreach ($otherCountries as $key => $oc)
                <a href="{{ route('tools.blocked-account.country', $key) }}"
                   class="bg-white border border-gray-200 hover:border-primary-400 rounded-lg px-4 py-3 transition group">
                    <span class="text-2xl">{{ $oc['flag'] }}</span>
                    <span class="ml-2 font-semibold text-gray-900 group-hover:text-primary-700">{{ $oc['name'] }}</span>
                </a>
            @endforeach
        </div>
    </section>
</div>
@endsection
