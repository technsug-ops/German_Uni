@extends('layouts.app')

@section('title', brand('name') . ' — ' . __('Germany University, Cost & Scholarship Guide'))

<x-seo
    :description="__('Germany university, visa, and cost of living guide for international students. :unis universities, :faqs answered questions, interactive tools.', ['unis' => $totals['universities'], 'faqs' => $faq_stats['total']])"
/>

@section('content')

{{-- =================================================================== --}}
{{-- HERO --}}
{{-- =================================================================== --}}
<section class="relative overflow-hidden bg-gradient-to-br from-primary-700 via-primary-600 to-primary-800 text-white">
    {{-- Pattern --}}
    <div aria-hidden="true" class="absolute inset-0 opacity-10 pointer-events-none"
         style="background-image: radial-gradient(circle at 1px 1px, white 1px, transparent 0); background-size: 24px 24px;"></div>

    <div class="relative max-w-[1400px] mx-auto px-4 py-14 md:py-20 grid grid-cols-1 lg:grid-cols-12 gap-10 items-center">
        {{-- Left: copy + search --}}
        <div class="lg:col-span-7">
            <span class="inline-flex items-center gap-2 bg-white/10 border border-white/20 backdrop-blur px-3.5 py-1.5 rounded-full text-xs font-semibold uppercase tracking-wide mb-5">
                <span class="text-accent-400"><x-svg-icon name="academic-cap" class="w-4 h-4" /></span>
                {{ __('For international students · 2026 updated') }}
            </span>

            <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold leading-[1.05] mb-5">
                {!! __('The short path to finding the <span class="text-accent-400">right university</span> in Germany.') !!}
            </h1>
            <p class="text-lg md:text-xl text-primary-100 max-w-2xl mb-7">
                <strong class="text-white">{{ number_format($totals['programs'], 0, ',', '.') }}</strong> {{ __('programs') }},
                <strong class="text-white">{{ $totals['universities'] }}</strong> {{ __('universities') }},
                <strong class="text-white">{{ $totals['cities'] }}</strong> {{ __('cities') }}.
                {{ __('English programs, scholarships, visa costs and deadlines — all on one platform.') }}
            </p>

            {{-- Search --}}
            <form action="/arama" method="GET" class="mb-4">
                <div class="flex flex-col sm:flex-row gap-2 bg-white p-2 rounded-2xl shadow-2xl">
                    <div class="flex items-center flex-1 px-3">
                        <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.3-4.3M10.5 18a7.5 7.5 0 1 0 0-15 7.5 7.5 0 0 0 0 15Z"/>
                        </svg>
                        <input
                            type="text"
                            name="q"
                            placeholder="{{ __('TUM, Berlin, Engineering, Medicine...') }}"
                            class="flex-1 px-3 py-3 text-gray-900 placeholder-gray-400 focus:outline-none bg-transparent"
                        >
                        <a href="{{ route('search.index') }}" title="{{ __('Advanced search') }}"
                           class="hidden sm:flex items-center text-gray-400 hover:text-primary-600 px-2 border-l border-gray-200 transition">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 7h12M3 12h9M3 17h6M17 7l3 0M14 12l6 0M11 17l9 0"/>
                            </svg>
                        </a>
                    </div>
                    <button type="submit" class="inline-flex items-center justify-center gap-2 bg-accent-500 hover:bg-accent-600 active:bg-accent-700 px-7 py-3.5 rounded-xl font-semibold transition shadow-md">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.3-4.3M10.5 18a7.5 7.5 0 1 0 0-15 7.5 7.5 0 0 0 0 15Z"/>
                        </svg>
                        {{ __('Search Universities') }}
                    </button>
                </div>
            </form>

            {{-- Quick chips --}}
            <div class="flex flex-wrap items-center gap-2 text-sm">
                <span class="text-primary-200 mr-1">{{ __('Popular:') }}</span>
                <a href="{{ route('programs.index', ['language' => 'en']) }}" class="inline-flex items-center gap-1.5 bg-white/10 hover:bg-white/20 border border-white/15 px-3.5 py-1.5 rounded-full transition">🇬🇧 {{ __('English Programs') }}</a>
                <a href="{{ route('scholarships.daad') }}" class="inline-flex items-center gap-1.5 bg-white/10 hover:bg-white/20 border border-white/15 px-3.5 py-1.5 rounded-full transition"><x-svg-icon name="trophy" class="w-3.5 h-3.5" /> {{ __('DAAD Scholarship') }}</a>
                <a href="{{ route('tools.deadlines') }}" class="inline-flex items-center gap-1.5 bg-white/10 hover:bg-white/20 border border-white/15 px-3.5 py-1.5 rounded-full transition"><x-svg-icon name="calendar" class="w-3.5 h-3.5" /> {{ __('Deadline calendar') }}</a>
                <a href="{{ route('search.index') }}" class="text-accent-400 hover:text-accent-300 font-semibold px-2 transition">{{ __('See all popular searches') }} →</a>
            </div>
        </div>

        {{-- Right: Brandenburg hero image (brand-aware: foto = ApplyToGerman/AlmanyaUni ikisinde de) --}}
        <div class="lg:col-span-5 hidden lg:block">
            <div class="relative">
                {{-- soft blob arka --}}
                <div aria-hidden="true" class="absolute -inset-4 bg-gradient-to-br from-accent-500/20 to-primary-400/10 blur-2xl rounded-[40%]"></div>
                <img src="{{ asset('img/hero/brandenburg.webp') }}"
                     alt="{{ __('Brandenburg Gate, Berlin') }}"
                     width="1400" height="933"
                     fetchpriority="high" decoding="async"
                     class="relative w-full h-auto rounded-3xl shadow-2xl ring-1 ring-white/10 object-cover">
                {{-- uçak rozeti (seyahat ipucu) --}}
                <div aria-hidden="true" class="absolute -top-3 -right-2 text-white/80 rotate-12">
                    <svg class="w-9 h-9 drop-shadow-lg" fill="currentColor" viewBox="0 0 24 24"><path d="M21 16v-2l-8-5V3.5a1.5 1.5 0 0 0-3 0V9l-8 5v2l8-2.5V19l-2 1.5V22l3.5-1 3.5 1v-1.5L13 19v-5.5L21 16z"/></svg>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== Stat kartı şeridi (mockup: 4 metrik, beyaz kart, renkli ikon daireleri) ===== --}}
    <div class="relative max-w-[1400px] mx-auto px-4 pb-14 md:pb-20">
        <div class="bg-white rounded-2xl shadow-2xl p-6 md:p-7 grid grid-cols-2 lg:grid-cols-4 gap-6 divide-y sm:divide-y-0 lg:divide-x divide-gray-100">
            {{-- Program --}}
            <div class="flex items-center gap-4 px-2">
                <span class="flex-shrink-0 w-12 h-12 rounded-xl bg-blue-500 text-white flex items-center justify-center shadow-sm">
                    <x-svg-icon name="book-open" class="w-6 h-6" />
                </span>
                <div>
                    <div class="text-2xl md:text-3xl font-extrabold text-primary-900 leading-none">{{ number_format($totals['programs'], 0, ',', '.') }}</div>
                    <div class="text-sm font-semibold text-gray-900 mt-1">{{ __('Programs') }}</div>
                    <div class="text-xs text-gray-500">{{ __('All majors') }}</div>
                </div>
            </div>
            {{-- Üniversite --}}
            <div class="flex items-center gap-4 px-2 lg:pl-6">
                <span class="flex-shrink-0 w-12 h-12 rounded-xl bg-purple-500 text-white flex items-center justify-center shadow-sm">
                    <x-svg-icon name="academic-cap" class="w-6 h-6" />
                </span>
                <div>
                    <div class="text-2xl md:text-3xl font-extrabold text-primary-900 leading-none">{{ $totals['universities'] }}</div>
                    <div class="text-sm font-semibold text-gray-900 mt-1">{{ __('Universities') }}</div>
                    <div class="text-xs text-gray-500">{{ __('All across Germany') }}</div>
                </div>
            </div>
            {{-- Şehir --}}
            <div class="flex items-center gap-4 px-2 lg:pl-6">
                <span class="flex-shrink-0 w-12 h-12 rounded-xl bg-emerald-500 text-white flex items-center justify-center shadow-sm">
                    <x-svg-icon name="map-pin" class="w-6 h-6" />
                </span>
                <div>
                    <div class="text-2xl md:text-3xl font-extrabold text-primary-900 leading-none">{{ $totals['cities'] }}</div>
                    <div class="text-sm font-semibold text-gray-900 mt-1">{{ __('Cities') }}</div>
                    <div class="text-xs text-gray-500">{{ __('In every region') }}</div>
                </div>
            </div>
            {{-- İngilizce Program --}}
            <div class="flex items-center gap-4 px-2 lg:pl-6">
                <span class="flex-shrink-0 w-12 h-12 rounded-xl bg-accent-500 text-white flex items-center justify-center shadow-sm">
                    <x-svg-icon name="globe" class="w-6 h-6" />
                </span>
                <div>
                    <div class="text-2xl md:text-3xl font-extrabold text-primary-900 leading-none">{{ number_format($totals['programs_en'], 0, ',', '.') }}</div>
                    <div class="text-sm font-semibold text-gray-900 mt-1">{{ __('English Programs') }}</div>
                    <div class="text-xs text-gray-500">{{ __('Fully in English') }}</div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- =================================================================== --}}
{{-- TRUST SIGNALS — resmi kaynak şeffaflığı + gerçek istatistikler --}}
{{-- =================================================================== --}}
<section class="bg-white border-b border-gray-100">
    <div class="max-w-[1400px] mx-auto px-4 py-8">

        {{-- Resmi kaynak badges --}}
        <div class="text-center mb-7">
            <p class="text-xs uppercase tracking-widest text-gray-500 font-semibold mb-3">
                {{ __('Powered by official data sources') }}
            </p>
            <div class="flex flex-wrap items-center justify-center gap-x-8 gap-y-3">
                <a href="https://www.daad.de" target="_blank" rel="noopener nofollow" class="group flex items-center gap-2 text-gray-700 hover:text-primary-700 transition" title="DAAD — Deutscher Akademischer Austauschdienst">
                    <span class="w-2 h-2 rounded-full bg-amber-500 group-hover:scale-125 transition-transform"></span>
                    <span class="text-sm font-bold">DAAD</span>
                    <span class="text-xs text-gray-500">{{ __('14k programs · 166 scholarships') }}</span>
                </a>
                <span class="text-gray-300">·</span>
                <a href="https://www.wikidata.org" target="_blank" rel="noopener nofollow" class="group flex items-center gap-2 text-gray-700 hover:text-primary-700 transition" title="Wikidata — open knowledge base">
                    <span class="w-2 h-2 rounded-full bg-blue-500 group-hover:scale-125 transition-transform"></span>
                    <span class="text-sm font-bold">Wikidata</span>
                    <span class="text-xs text-gray-500">{{ __('464 universities') }}</span>
                </a>
                <span class="text-gray-300">·</span>
                <a href="https://berufenet.arbeitsagentur.de" target="_blank" rel="noopener nofollow" class="group flex items-center gap-2 text-gray-700 hover:text-primary-700 transition" title="BERUFENET — Bundesagentur für Arbeit">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 group-hover:scale-125 transition-transform"></span>
                    <span class="text-sm font-bold">BERUFENET</span>
                    <span class="text-xs text-gray-500">{{ __('3,558 professions') }}</span>
                </a>
                <span class="text-gray-300">·</span>
                <a href="https://anabin.kmk.org" target="_blank" rel="noopener nofollow" class="group flex items-center gap-2 text-gray-700 hover:text-primary-700 transition" title="Anabin — KMK">
                    <span class="w-2 h-2 rounded-full bg-purple-500 group-hover:scale-125 transition-transform"></span>
                    <span class="text-sm font-bold">Anabin (KMK)</span>
                    <span class="text-xs text-gray-500">{{ __('15 country eligibility') }}</span>
                </a>
                <span class="text-gray-300">·</span>
                <a href="https://www.hochschulkompass.de" target="_blank" rel="noopener nofollow" class="group flex items-center gap-2 text-gray-700 hover:text-primary-700 transition" title="Hochschulkompass — HRK">
                    <span class="w-2 h-2 rounded-full bg-rose-500 group-hover:scale-125 transition-transform"></span>
                    <span class="text-sm font-bold">Hochschulkompass</span>
                    <span class="text-xs text-gray-500">{{ __('admission verify') }}</span>
                </a>
            </div>
        </div>

        {{-- 4 trust pillar --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center max-w-5xl mx-auto">
            <div>
                <div class="flex justify-center mb-1 text-primary-600"><x-svg-icon name="check-circle" class="w-8 h-8" /></div>
                <p class="text-sm font-bold text-gray-900">{{ __('100% Free') }}</p>
                <p class="text-xs text-gray-500 mt-0.5">{{ __('No signup, no paywall') }}</p>
            </div>
            <div>
                <div class="flex justify-center mb-1 text-primary-600"><x-svg-icon name="chart-bar" class="w-8 h-8" /></div>
                <p class="text-sm font-bold text-gray-900">{{ __('Official sources only') }}</p>
                <p class="text-xs text-gray-500 mt-0.5">{{ __('DAAD, Wikidata, BERUFENET') }}</p>
            </div>
            <div>
                <div class="flex justify-center mb-1 text-primary-600"><x-svg-icon name="academic-cap" class="w-8 h-8" /></div>
                <p class="text-sm font-bold text-gray-900">{{ __('10+ years of consulting') }}</p>
                <p class="text-xs text-gray-500 mt-0.5">{{ __('Real-world experience') }}</p>
            </div>
            <div>
                <div class="flex justify-center mb-1 text-primary-600"><x-svg-icon name="globe" class="w-8 h-8" /></div>
                <p class="text-sm font-bold text-gray-900">{{ __('3 languages') }}</p>
                <p class="text-xs text-gray-500 mt-0.5">{{ __('TR · EN · DE — full coverage') }}</p>
            </div>
        </div>
    </div>
</section>

{{-- =================================================================== --}}
{{-- TOOLS BAR --}}
{{-- =================================================================== --}}
<section class="bg-white border-b border-gray-200">
    <div class="max-w-[1400px] mx-auto px-4 py-10">
        <div class="flex items-end justify-between mb-6 flex-wrap gap-3">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">{{ __('Tools') }}</h2>
                <p class="text-gray-600 text-sm">{{ __('Decide with numbers, stop guessing.') }}</p>
            </div>
            <a href="{{ route('tools.index') }}" class="text-primary-600 hover:text-primary-800 font-semibold text-sm whitespace-nowrap">
                {{ __('All tools') }} →
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            {{-- Cost of Living --}}
            <a href="{{ route('tools.cost-of-living') }}"
               title="{{ __('Cost of Living') }} — {{ __('City + housing type → monthly expense estimate.') }}"
               class="group relative block bg-gradient-to-br from-emerald-50 via-white to-emerald-50/40 border border-emerald-200 hover:border-emerald-400 rounded-2xl p-5 transition hover:shadow-lg overflow-hidden">
                <svg class="absolute -right-6 -bottom-6 w-32 h-32 text-emerald-100 opacity-60 group-hover:opacity-90 transition" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2L2 7v10c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V7l-10-5zm0 2.18L19.82 8H4.18L12 4.18zM4 17V10h16v7H4zm2-5h2v3H6v-3zm4 0h2v3h-2v-3zm4 0h2v3h-2v-3z"/>
                </svg>
                <div class="relative flex items-start gap-4">
                    <div class="flex-shrink-0 w-14 h-14 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center shadow-md group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-bold text-gray-900 mb-1">{{ __('Cost of Living') }}</h3>
                        <p class="text-sm text-gray-600 line-clamp-2">{{ __('City + housing type → monthly expense estimate.') }}</p>
                        <span class="inline-block mt-2 text-xs font-semibold text-emerald-700 group-hover:text-emerald-900">
                            {{ __('Calculate') }} →
                        </span>
                    </div>
                </div>
            </a>

            {{-- Grade Converter --}}
            <a href="{{ route('tools.grade-converter') }}"
               title="{{ __('Grade Converter') }} — {{ __('Convert your grade to the German 1-5 system.') }}"
               class="group relative block bg-gradient-to-br from-blue-50 via-white to-blue-50/40 border border-blue-200 hover:border-blue-400 rounded-2xl p-5 transition hover:shadow-lg overflow-hidden">
                <svg class="absolute -right-6 -bottom-6 w-32 h-32 text-blue-100 opacity-60 group-hover:opacity-90 transition" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M3 13h2v8H3v-8zm4-6h2v14H7V7zm4-4h2v18h-2V3zm4 8h2v10h-2V11zm4-4h2v14h-2V7z"/>
                </svg>
                <div class="relative flex items-start gap-4">
                    <div class="flex-shrink-0 w-14 h-14 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center shadow-md group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-bold text-gray-900 mb-1">{{ __('Grade Converter') }}</h3>
                        <p class="text-sm text-gray-600 line-clamp-2">{{ __('Convert your grade to the German 1-5 system.') }}</p>
                        <span class="inline-block mt-2 text-xs font-semibold text-blue-700 group-hover:text-blue-900">
                            {{ __('Convert') }} →
                        </span>
                    </div>
                </div>
            </a>

            {{-- Uni Match Quiz --}}
            <a href="{{ route('tools.recommendation') }}"
               title="{{ __('Uni Match Quiz') }} — {{ __('5 questions, the best universities for you.') }}"
               class="group relative block bg-gradient-to-br from-purple-50 via-white to-purple-50/40 border border-purple-200 hover:border-purple-400 rounded-2xl p-5 transition hover:shadow-lg overflow-hidden">
                <svg class="absolute -right-6 -bottom-6 w-32 h-32 text-purple-100 opacity-60 group-hover:opacity-90 transition" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm0-14c-3.31 0-6 2.69-6 6s2.69 6 6 6 6-2.69 6-6-2.69-6-6-6zm0 10c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4zm0-6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                </svg>
                <div class="relative flex items-start gap-4">
                    <div class="flex-shrink-0 w-14 h-14 rounded-xl bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center shadow-md group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-bold text-gray-900 mb-1">{{ __('Uni Match Quiz') }}</h3>
                        <p class="text-sm text-gray-600 line-clamp-2">{{ __('5 questions, the best universities for you.') }}</p>
                        <span class="inline-block mt-2 text-xs font-semibold text-purple-700 group-hover:text-purple-900">
                            {{ __('Start quiz') }} →
                        </span>
                    </div>
                </div>
            </a>
        </div>
    </div>
</section>

{{-- =================================================================== --}}
{{-- POPULAR CITIES --}}
{{-- =================================================================== --}}
<section class="bg-gray-50 py-14 border-y border-gray-200">
    <div class="max-w-[1400px] mx-auto px-4">
        <div class="flex items-end justify-between mb-6 flex-wrap gap-3">
            <div>
                <h2 class="text-2xl md:text-3xl font-bold text-gray-900">{{ __('Popular Student Cities') }}</h2>
                <p class="text-gray-600 text-sm">{{ __('By university density') }}</p>
            </div>
            <a href="{{ route('cities.index') }}" class="text-primary-600 hover:text-primary-800 font-semibold text-sm whitespace-nowrap">
                {{ __('All cities') }} →
            </a>
        </div>

        @if ($cities && count($cities) > 0)
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach ($cities as $city)
                    @php
                        $seed = crc32($city['name']);
                        $palettes = ['from-blue-500 to-cyan-400', 'from-purple-500 to-pink-500', 'from-amber-500 to-orange-400', 'from-emerald-500 to-teal-400', 'from-rose-500 to-fuchsia-500', 'from-indigo-500 to-violet-500'];
                        $palette = $palettes[$seed % count($palettes)];
                    @endphp
                    <a href="{{ route('cities.show', $city['slug']) }}"
                       class="group bg-white rounded-xl overflow-hidden border border-gray-200 hover:border-primary-500 hover:shadow-lg hover:-translate-y-0.5 transition-all flex flex-col">
                        {{-- Gradient + letter always rendered; image overlays if URL present, removed on error (Wikimedia 429 fallback) --}}
                        <div class="aspect-[4/3] overflow-hidden relative bg-gradient-to-br {{ $palette }}">
                            <span class="absolute inset-0 flex items-center justify-center text-5xl font-extrabold text-white/90 drop-shadow pointer-events-none">{{ mb_substr($city['name'], 0, 1) }}</span>
                            @if(!empty($city['image_url']))
                                <img src="{{ $city['image_url'] }}" alt="{{ __(':city — student city in Germany', ['city' => $city['name']]) }}"
                                     width="400" height="300" loading="lazy" decoding="async"
                                     onerror="this.remove()"
                                     class="relative w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            @endif
                            @if(!empty($city['has_content']))
                                <span class="absolute top-2 left-2 inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-700 text-white text-[10px] font-bold uppercase tracking-wider shadow-sm">
                                    <x-svg-icon name="sparkles" class="w-3 h-3" />
                                    {{ __('Guide') }}
                                </span>
                            @endif
                            <span class="absolute bottom-2 right-2 inline-block px-2 py-0.5 rounded-full bg-black/60 backdrop-blur text-white text-xs font-semibold">
                                {{ $city['universities_count'] }} {{ __('uni') }}
                            </span>
                        </div>
                        <div class="p-3">
                            <h3 class="font-bold text-gray-900 group-hover:text-primary-600 transition leading-tight">{{ $city['name'] }}</h3>
                            @if ($city['state_name'])
                                <p class="text-xs text-gray-500 mt-0.5 truncate">{{ $city['state_name'] }}</p>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</section>

{{-- =================================================================== --}}
{{-- ÖNE ÇIKAN ÜNİVERSİTELER --}}
{{-- =================================================================== --}}
@if (!empty($featured_universities))
<section class="max-w-[1400px] mx-auto px-4 py-14">
    <div class="flex items-end justify-between mb-6 flex-wrap gap-3">
        <div>
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900">{{ __('Featured Universities') }}</h2>
            <p class="text-gray-600 text-sm">{{ __('Most applied and largest universities') }}</p>
        </div>
        <a href="{{ route('universities.index') }}" class="text-primary-600 hover:text-primary-800 font-semibold text-sm whitespace-nowrap">
            {{ __('All universities') }} →
        </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach ($featured_universities as $uni)
            @php
                $seed = crc32($uni['name_de']);
                $palettes = ['from-blue-500 to-cyan-400', 'from-purple-500 to-pink-500', 'from-amber-500 to-orange-400', 'from-emerald-500 to-teal-400', 'from-rose-500 to-fuchsia-500', 'from-indigo-500 to-violet-500'];
                $palette = $palettes[$seed % count($palettes)];
                $typeBadge = match($uni['type'] ?? null) {
                    'public' => [__('Public'), 'bg-emerald-50 text-emerald-700'],
                    'private' => [__('Private'), 'bg-amber-50 text-amber-700'],
                    'applied_sciences' => ['HAW', 'bg-blue-50 text-blue-700'],
                    'art' => [__('Art'), 'bg-pink-50 text-pink-700'],
                    default => [null, null],
                };
            @endphp
            <a href="{{ route('universities.show', $uni['slug']) }}"
               class="group bg-white rounded-xl overflow-hidden border border-gray-200 hover:border-primary-500 hover:shadow-lg hover:-translate-y-0.5 transition-all flex flex-col">
                {{-- Gradient + initials always behind; image overlays if URL present, removed on error --}}
                <div class="aspect-[16/9] overflow-hidden relative bg-gradient-to-br {{ $palette }}">
                    <span class="absolute inset-0 flex items-center justify-center text-4xl font-extrabold text-white/90 drop-shadow pointer-events-none">{{ mb_substr($uni['name_de'], 0, 2) }}</span>
                    @if(!empty($uni['image_url']))
                        <img src="{{ $uni['image_url'] }}" alt="{{ $uni['name_de'] }} — {{ __('University in Germany') }}"
                             width="640" height="360" loading="lazy" decoding="async"
                             onerror="this.remove()"
                             class="relative w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    @endif
                    @if($typeBadge[0])
                        <span class="absolute top-2 left-2 inline-block px-2 py-0.5 rounded {{ $typeBadge[1] }} text-xs font-semibold ring-1 ring-white/40 shadow-sm">
                            {{ $typeBadge[0] }}
                        </span>
                    @endif
                    @if($uni['logo_url'] && $uni['image_url'])
                        <div class="absolute bottom-2 left-2 w-10 h-10 bg-white rounded-lg ring-1 ring-white/60 shadow-md p-1 flex items-center justify-center">
                            <img src="{{ $uni['logo_url'] }}" alt="{{ $uni['name_de'] }} logo" width="36" height="36" onerror="this.remove()" class="max-w-full max-h-full object-contain" loading="lazy" decoding="async"/>
                        </div>
                    @endif
                </div>
                <div class="p-4 flex-1 flex flex-col">
                    <h3 class="font-bold text-gray-900 group-hover:text-primary-600 transition leading-tight line-clamp-2 mb-1">{{ $uni['name_de'] }}</h3>
                    @if($uni['city_name'])
                        <p class="text-xs text-gray-500 mb-3 inline-flex items-center gap-1"><x-svg-icon name="map-pin" class="w-3.5 h-3.5" /> {{ $uni['city_name'] }}</p>
                    @endif
                    <div class="mt-auto flex items-center justify-between pt-2 border-t border-gray-100 text-xs">
                        @if($uni['student_count'])
                            <span class="text-accent-700 font-bold">{{ number_format($uni['student_count']) }} <span class="font-normal text-gray-500">{{ __('students') }}</span></span>
                        @endif
                        @if($uni['founded_year'])
                            <span class="text-gray-500">est. {{ $uni['founded_year'] }}</span>
                        @endif
                    </div>
                </div>
            </a>
        @endforeach
    </div>
</section>
@endif

{{-- =================================================================== --}}
{{-- TOP ALANLAR --}}
{{-- =================================================================== --}}
@if (! empty($top_fields) && $top_fields->count() > 0)
<section class="bg-gray-50 py-14 border-y border-gray-200">
    <div class="max-w-[1400px] mx-auto px-4">
        <div class="flex items-end justify-between mb-6 flex-wrap gap-3">
            <div>
                <h2 class="text-2xl md:text-3xl font-bold text-gray-900 inline-flex items-center gap-2">
                    <x-svg-icon name="target" class="w-7 h-7" />
                    {{ __('Popular Study Fields') }}
                </h2>
                <p class="text-gray-600 text-sm">{{ __('Top 6 fields by program count — choose your path') }}</p>
            </div>
            <a href="{{ route('fields.index') }}" class="text-primary-600 hover:text-primary-800 font-semibold text-sm">{{ __('All fields') }} →</a>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
            @foreach ($top_fields as $field)
                <a href="{{ route('fields.show', $field->slug) }}"
                   title="{{ $field->name }} — {{ number_format($field->programs_count) }} {{ __('programs') }}"
                   class="group bg-white rounded-xl border border-gray-200 hover:border-primary-400 hover:shadow-lg transition p-5 text-center">
                    <div class="mb-2 flex justify-center" style="color: {{ $field->color ?? '#1e40af' }};">{!! e_icon($field->icon, 'w-10 h-10') !!}</div>
                    <h3 class="font-bold text-gray-900 group-hover:text-primary-600 text-sm mb-1 leading-tight">{{ $field->name }}</h3>
                    <p class="text-xs text-gray-500">{{ number_format($field->programs_count) }} {{ __('programs') }}</p>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- =================================================================== --}}
{{-- DAAD BURSU HIGHLIGHT --}}
{{-- =================================================================== --}}
@if (! empty($featured_scholarships) && $featured_scholarships->count() > 0)
<section class="max-w-[1400px] mx-auto px-4 py-14">
    <div class="bg-gradient-to-br from-emerald-50 via-white to-teal-50 border border-emerald-200 rounded-2xl p-6 md:p-8">
        <div class="flex items-start justify-between mb-6 flex-wrap gap-3">
            <div>
                <span class="inline-block text-xs font-semibold uppercase tracking-wider text-emerald-700 mb-2">
                    <span class="inline-flex items-center gap-1"><x-svg-icon name="trophy" class="w-3.5 h-3.5" /> {{ __(':count Scholarship Programs', ['count' => $totals['scholarships']]) }}</span>
                </span>
                <h2 class="text-2xl md:text-3xl font-bold text-gray-900">{{ __('Study in Germany with a Scholarship') }}</h2>
                <p class="text-gray-600 text-sm mt-1">{{ __('DAAD, EPOS, Helmut-Schmidt and more — lightens the Sperrkonto burden') }}</p>
            </div>
            <a href="{{ route('scholarships.daad') }}" class="inline-flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2.5 rounded-lg font-semibold text-sm transition shadow-md whitespace-nowrap" title="{{ __('DAAD Guide') }} — {{ __('German Academic Exchange Service') }}">
                <span class="inline-flex items-center gap-1.5"><x-svg-icon name="trophy" class="w-4 h-4" /> {{ __('DAAD Guide') }}</span> →
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
            @foreach ($featured_scholarships as $s)
                <a href="{{ route('scholarships.show', $s->slug) }}"
                   title="{{ $s->name }}{{ $s->is_daad ? ' — DAAD' : '' }}"
                   class="group bg-white rounded-lg border border-emerald-100 hover:border-emerald-400 hover:shadow-md transition p-4">
                    @if ($s->is_daad)
                        <span class="inline-block text-xs px-2 py-0.5 rounded bg-emerald-100 text-emerald-700 font-semibold mb-2">DAAD</span>
                    @endif
                    <h3 class="font-semibold text-gray-900 group-hover:text-emerald-700 leading-tight text-sm line-clamp-2">
                        {{ $s->name }}
                    </h3>
                    @if ($s->programmname)
                        <p class="text-xs text-gray-500 mt-1.5 line-clamp-2">{{ \Illuminate\Support\Str::limit($s->programmname, 80) }}</p>
                    @endif
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- =================================================================== --}}
{{-- 6 ARAÇ TANITIMI --}}
{{-- =================================================================== --}}
<section class="bg-gradient-to-br from-primary-50 to-accent-50 py-14 border-y border-primary-100">
    <div class="max-w-[1400px] mx-auto px-4">
        <div class="flex items-end justify-between mb-6 flex-wrap gap-3">
            <div>
                <span class="inline-block text-xs font-semibold uppercase tracking-wider text-primary-700 mb-2">
                    <span class="inline-flex items-center gap-1"><x-svg-icon name="wrench-screwdriver" class="w-3.5 h-3.5" /> {{ __('Interactive tools') }}</span>
                </span>
                <h2 class="text-2xl md:text-3xl font-bold text-gray-900">{{ __('6 tools to help you decide') }}</h2>
                <p class="text-gray-600 text-sm mt-1">{{ __('Is your budget enough, how much is the visa, when is the nearest deadline — calculate instantly') }}</p>
            </div>
            <a href="{{ route('tools.index') }}" class="text-primary-600 hover:text-primary-800 font-semibold text-sm">{{ __('All tools') }} →</a>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
            @php
                $homeTools = [
                    ['route' => route('tools.cost-of-living'), 'icon' => 'banknotes',          'lbl' => __('Cost of Living'),   'desc' => __('130 cities')],
                    ['route' => route('tools.budget-planner'), 'icon' => 'arrow-trending-up',  'lbl' => __('Budget Planner'),   'desc' => __('Net calc')],
                    ['route' => route('tools.visa-cost'),      'icon' => 'currency-euro',      'lbl' => __('Visa Cost'),        'desc' => __('10 items')],
                    ['route' => route('tools.deadlines'),      'icon' => 'calendar',           'lbl' => __('Application Calendar'), 'desc' => __('ICS export')],
                    ['route' => route('tools.grade-converter'),'icon' => 'chart-bar',          'lbl' => __('Grade Converter'),  'desc' => 'TR→DE'],
                    ['route' => route('tools.recommendation'), 'icon' => 'target',             'lbl' => __('Uni Quiz'),         'desc' => __('5 questions')],
                ];
            @endphp
            @foreach ($homeTools as $t)
                <a href="{{ $t['route'] }}"
                   title="{{ $t['lbl'] }} — {{ $t['desc'] }}"
                   class="group bg-white rounded-xl border border-gray-200 hover:border-primary-500 hover:shadow-md transition p-4 text-center">
                    <div class="flex justify-center mb-2 text-primary-600"><x-svg-icon name="{{ $t['icon'] }}" class="w-7 h-7" /></div>
                    <h3 class="font-semibold text-gray-900 group-hover:text-primary-600 text-sm leading-tight">{{ $t['lbl'] }}</h3>
                    <p class="text-xs text-gray-500 mt-1">{{ $t['desc'] }}</p>
                </a>
            @endforeach
        </div>
    </div>
</section>

{{-- =================================================================== --}}
{{-- FAQ TEASER --}}
{{-- =================================================================== --}}
<section class="max-w-[1400px] mx-auto px-4 py-14">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-1">
            <span class="inline-block text-xs font-semibold uppercase tracking-wider text-primary-600 mb-2">
                {{ __('Frequently Asked Questions') }}
            </span>
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-3 leading-tight">
                {{ __('Everything you want to know, answered.') }}
            </h2>
            <p class="text-gray-600 mb-5">
                {{ __('Visa, language tests, Uni-Assist, Studienkolleg, dorm, health insurance, blocked account and much more.') }}
                <strong>{{ __(':n questions', ['n' => $faq_stats['total']]) }}</strong>, {{ __(':n topics', ['n' => $faq_stats['topics']]) }} — {{ __('all answered.') }}
            </p>
            <a href="{{ route('faqs.index') }}" class="inline-flex items-center gap-2 bg-primary-600 hover:bg-primary-700 text-white font-semibold px-5 py-2.5 rounded-lg transition" title="{{ __('See all questions') }} — {{ $faq_stats['total'] }} {{ __('questions') }}">
                {{ __('See all questions') }} →
            </a>
        </div>

        <div class="lg:col-span-2">
            @if ($featured_faqs->isNotEmpty())
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach ($featured_faqs as $f)
                        <a href="{{ route('faqs.show', [$f->topic->slug, $f->slug]) }}"
                           title="{{ $f->question }}"
                           class="group block bg-white border border-gray-200 hover:border-primary-400 hover:shadow-md transition rounded-lg p-4">
                            <div class="flex items-start gap-2 mb-2">
                                @if ($f->topic->icon)
                                    <span style="color: {{ $f->topic->color ?? '#1E40AF' }};">{!! e_icon($f->topic->icon, 'w-5 h-5') !!}</span>
                                @endif
                                <span class="text-xs font-semibold uppercase tracking-wide"
                                      style="color: {{ $f->topic->color ?? '#1E40AF' }}">
                                    {{ $f->topic->name }}
                                </span>
                            </div>
                            <p class="font-semibold text-gray-900 leading-snug group-hover:text-primary-700 transition">
                                {{ $f->question }}
                            </p>
                            <p class="text-xs text-gray-500 mt-2">{{ $f->answer_minutes ?? 1 }} {{ __('min read') }}</p>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</section>

{{-- =================================================================== --}}
{{-- STATES --}}
{{-- =================================================================== --}}
<section class="bg-gradient-to-br from-primary-50 to-white border-t border-gray-200 py-14">
    <div class="max-w-[1400px] mx-auto px-4">
        <div class="text-center mb-8">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">{{ __('Explore by State') }}</h2>
            <p class="text-gray-600 text-sm">{{ __('Jump to any of Germany\'s 16 federal states') }}</p>
        </div>

        @if ($states && count($states) > 0)
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                @foreach ($states as $state)
                    <a href="{{ route('universities.index', ['state' => $state['slug']]) }}"
                       title="{{ __('Universities in :state', ['state' => $state['name']]) }} — {{ $state['cities_count'] }} {{ __('cities') }}"
                       class="group bg-white border border-primary-100 hover:border-primary-500 hover:bg-primary-50 transition p-4 rounded-lg text-center">
                        <h3 class="font-semibold text-primary-900 group-hover:text-primary-700 transition leading-tight">
                            {{ $state['name'] }}
                        </h3>
                        <p class="text-xs text-gray-500 mt-1">{{ $state['cities_count'] }} {{ __('cities') }}</p>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</section>

{{-- =================================================================== --}}
{{-- BLOG --}}
{{-- =================================================================== --}}
@if (!empty($latest_posts))
    <section class="max-w-[1400px] mx-auto px-4 py-14">
        <div class="flex items-end justify-between mb-6 flex-wrap gap-3">
            <div>
                <h2 class="text-2xl md:text-3xl font-bold text-gray-900">{{ __('Latest Blog Posts') }}</h2>
                <p class="text-gray-600 text-sm">{{ __('Germany education and student life guides') }}</p>
            </div>
            <a href="{{ route('blog.index') }}" class="text-primary-600 hover:text-primary-800 font-semibold text-sm whitespace-nowrap" title="{{ __('All posts') }}">
                {{ __('All posts') }} →
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            @foreach ($latest_posts as $post)
                <a href="{{ route('blog.show', $post['slug']) }}"
                   title="{{ $post['title'] }}{{ $post['category_name'] ? ' — ' . $post['category_name'] : '' }}"
                   class="group block bg-white border border-gray-200 hover:border-primary-400 hover:shadow-md transition rounded-xl p-5">
                    @if ($post['category_name'])
                        <span class="inline-block text-xs font-semibold uppercase tracking-wider mb-2"
                              style="color: {{ $post['category_color'] ?? '#1E40AF' }}">
                            {{ $post['category_name'] }}
                        </span>
                    @endif
                    <h3 class="text-lg font-bold leading-tight mb-2 text-gray-900 group-hover:text-primary-700 transition">
                        {{ $post['title'] }}
                    </h3>
                    @if ($post['excerpt'])
                        <p class="text-sm text-gray-600 mb-3">{{ \Illuminate\Support\Str::limit($post['excerpt'], 120) }}</p>
                    @endif
                    <p class="text-xs text-gray-500">
                        @if ($post['published_at'])
                            {{ $post['published_at']->translatedFormat('d M Y') }} ·
                        @endif
                        {{ $post['reading_minutes'] }} {{ __('min read') }}
                    </p>
                </a>
            @endforeach
        </div>
    </section>
@endif

{{-- =================================================================== --}}
{{-- CTA BANNER --}}
{{-- =================================================================== --}}
<section class="bg-gradient-to-r from-primary-700 to-primary-800 text-white">
    <div class="max-w-[1400px] mx-auto px-4 py-12 text-center">
        <h2 class="text-2xl md:text-3xl font-bold mb-3">{{ __('Which university fits you?') }}</h2>
        <p class="text-primary-100 mb-6 max-w-2xl mx-auto">
            {{ __('In 5 short questions, we\'ll match the best German universities based on your budget, city preference and study field.') }}
        </p>
        <a href="{{ route('tools.recommendation') }}"
           class="inline-block bg-accent-500 hover:bg-accent-600 text-white font-bold px-8 py-3.5 rounded-lg shadow-lg transition"
           title="{{ __('Uni Match Quiz') }} — {{ __('5 questions, the best universities for you.') }}">
            <span class="inline-flex items-center gap-1.5"><x-svg-icon name="target" class="w-4 h-4" /> {{ __('Start quiz — 60 seconds') }}</span>
        </a>
    </div>
</section>

@endsection
