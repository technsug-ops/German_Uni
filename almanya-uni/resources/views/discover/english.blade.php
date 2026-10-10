@extends('layouts.app')

{{-- Program seçimi sayfası. Kurum seçimi: universities/collections/english-taught-universities. Sayılar katalog dil
     etiketidir (resmî doğrulama değil); "tamamen İngilizce" (en) ve "iki dilli" (both) ayrı tutulur. --}}
@php
    $fmt = fn ($n) => number_format((int) $n, 0, ',', '.');
    $cards = [
        ['degree' => 'bachelor', 'language' => 'en',   'title' => __('English-taught Bachelor\'s programmes'), 'note' => __('Fully in English')],
        ['degree' => 'bachelor', 'language' => 'both', 'title' => __('Bilingual Bachelor\'s programmes'), 'note' => __('Taught in German and English')],
        ['degree' => 'master',   'language' => 'en',   'title' => __('English-taught Master\'s programmes'), 'note' => __('Fully in English')],
        ['degree' => 'master',   'language' => 'both', 'title' => __('Bilingual Master\'s programmes'), 'note' => __('Taught in German and English')],
    ];
@endphp

@section('title', __('English-taught Bachelor\'s and Master\'s programmes in Germany') . ' — ' . brand('name'))

<x-seo
    :title="__('English-taught Bachelor\'s and Master\'s programmes in Germany')"
    :description="__('English-taught Bachelor\'s and Master\'s programmes in Germany: fully English and bilingual listed separately, plus what to check before you apply.')"
/>

@section('content')

{{-- HERO --}}
<section class="bg-gradient-to-br from-blue-700 via-indigo-600 to-blue-700 text-white">
    <div class="max-w-[1100px] mx-auto px-4 py-12 md:py-16">
        <nav class="text-sm text-blue-100 mb-3">
            <a href="/" class="hover:text-white">{{ __('Home') }}</a>
            <span class="mx-2 opacity-60">›</span>
            <span class="text-white">{{ __('English-taught programs') }}</span>
        </nav>
        <h1 class="text-3xl md:text-5xl font-extrabold leading-tight drop-shadow mb-3">
            {{ __('English-taught Bachelor\'s and Master\'s programmes in Germany') }}
        </h1>
        <p class="text-lg text-blue-50 max-w-3xl">
            {{ __('Filter fully English-taught and bilingual programmes separately. Before you apply, check the language requirement on the programme\'s official page.') }}
        </p>
    </div>
</section>

<div class="max-w-[1100px] mx-auto px-4 py-10 space-y-12">

    {{-- Rapor verisi (Temmuz 2025, yayımlanmış sayım). Katalog sayılarıyla karışmasın diye ayrı kutu ve ayrı atıf. --}}
    <section data-report-box="english-programmes" class="rounded-xl bg-slate-50 ring-1 ring-slate-200 p-5 md:p-6">
        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 mb-1">{{ __('Published figures, July 2025') }}</p>
        <h2 class="font-bold text-gray-900 text-lg mb-2">{{ __('English-taught Bachelor\'s and Master\'s options') }}</h2>
        <p class="text-sm text-gray-700 leading-relaxed">{{ __('In July 2025, Germany had 1,928 English-taught Master\'s programmes and 424 English-taught Bachelor\'s programmes.') }}</p>
        <p class="text-xs text-gray-500 mt-2 leading-relaxed">{{ __('These are published figures for July 2025. They are not the number of programmes in our catalogue and not a count of programmes open for applications today.') }}</p>
        <p class="text-xs text-gray-500 mt-2 leading-relaxed">{{ __('Source: ApplyToGerman Germany Student Statistics Report 2026 (prepared by the ApplyToGerman team), p. 6; HRK Higher Education Compass / DZHW calculations; Wissenschaft weltoffen kompakt 2026, Figures 36–37.') }}</p>
    </section>

    {{-- Derece × öğretim dili (katalog sayıları) --}}
    <section>
        <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ __('Choose by degree and language of instruction') }}</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @foreach ($cards as $card)
                <a href="{{ route('programs.index', ['degree' => $card['degree'], 'language' => $card['language']]) }}"
                   class="group flex items-center justify-between gap-3 bg-white border border-gray-200 hover:border-blue-400 hover:shadow-md transition rounded-xl p-5">
                    <span class="min-w-0">
                        <span class="block font-bold text-gray-900 group-hover:text-blue-700">{{ $card['title'] }}</span>
                        <span class="block text-sm text-gray-600">{{ $card['note'] }}</span>
                        <span class="block text-xs text-gray-500 mt-1">{{ __(':count programmes in our catalogue', ['count' => $fmt($counts[$card['degree'] . '.' . $card['language']] ?? 0)]) }}</span>
                    </span>
                    <x-svg-icon name="arrow-right" class="w-5 h-5 text-gray-300 group-hover:text-blue-500 shrink-0" />
                </a>
            @endforeach
        </div>
        <p class="text-xs text-gray-500 mt-3 max-w-3xl leading-relaxed">
            {{ __('The numbers are programmes in our catalogue with this language label. They are not the total for Germany, and the language label has not been checked against the official page for every programme. Bilingual programmes can require German for part of the studies.') }}
        </p>
    </section>

    {{-- Seçmeden önce kontrol --}}
    <section class="bg-blue-50 border border-blue-200 rounded-xl p-5 md:p-6">
        <h2 class="font-bold text-gray-900 text-lg mb-3">{{ __('What to check before you choose a programme') }}</h2>
        <ol class="list-decimal pl-5 space-y-2 text-sm text-gray-700 leading-relaxed">
            <li>{{ __('Language of instruction: is the programme fully taught in English, or partly in German?') }}</li>
            <li>{{ __('English proof for the application: which certificates and scores the programme accepts, and whether a previous degree taught in English can replace a test.') }}</li>
            <li>{{ __('Extra German requirement: whether German is required, and at which stage (application, enrolment, during the studies or before graduation).') }}</li>
            <li>{{ __('Academic fit: which previous degree and subject credits the programme expects.') }}</li>
            <li>{{ __('Application route and deadline: uni-assist or the university\'s own portal, and the deadline for your applicant group.') }}</li>
        </ol>
        @if ($guides->isNotEmpty())
            <div class="flex flex-wrap gap-3 mt-4">
                @if ($guides->has('master'))
                    <a href="{{ $guides['master']->publicUrl() }}" class="inline-flex items-center gap-1 px-3 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold transition">{{ __('How to apply for an English-taught Master\'s') }} →</a>
                @endif
                @if ($guides->has('language'))
                    <a href="{{ $guides['language']->publicUrl() }}" class="inline-flex items-center gap-1 px-3 py-2 rounded-lg bg-white ring-1 ring-blue-300 hover:ring-blue-500 text-blue-800 text-sm font-semibold transition">{{ __('Check language certificate requirements') }} →</a>
                @endif
            </div>
        @endif
    </section>

    {{-- Alanlara göre (yalnız tamamen İngilizce) --}}
    @if ($fields->isNotEmpty())
        <section>
            <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ __('Fully English-taught programmes by subject') }}</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($fields as $field)
                    <a href="{{ route('programs.index', ['field' => $field->slug, 'language' => 'en']) }}"
                       class="group flex items-center gap-3 bg-white border border-gray-200 hover:border-blue-400 hover:shadow-md transition rounded-xl p-4">
                        <span class="inline-flex items-center justify-center w-11 h-11 rounded-lg text-white shrink-0"
                              style="background-color: {{ $field->color ?: '#2563eb' }};">{!! e_icon($field->icon, 'w-6 h-6') !!}</span>
                        <span class="min-w-0 flex-1">
                            <span class="block font-bold text-gray-900 group-hover:text-blue-700 truncate">{{ $field->name }}</span>
                            <span class="block text-xs text-gray-500">{{ __(':count programmes in our catalogue', ['count' => $fmt($field->cnt)]) }}</span>
                        </span>
                        <x-svg-icon name="arrow-right" class="w-4 h-4 text-gray-300 group-hover:text-blue-500 shrink-0" />
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- Kurum seçimi: koleksiyon --}}
    <section class="rounded-xl bg-white ring-1 ring-gray-200 p-5 md:p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="font-bold text-gray-900 text-lg mb-1">{{ __('Looking for a university first?') }}</h2>
            <p class="text-sm text-gray-600 max-w-2xl">{{ __('See universities with officially checked examples of English-taught programmes, including the language certificate each programme asks for.') }}</p>
        </div>
        <a href="{{ route('universities.collection', 'english-taught-universities') }}"
           class="shrink-0 inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-bold px-5 py-3 rounded-lg shadow-md transition">
            {{ __('Universities with English-taught programmes') }} <x-svg-icon name="arrow-right" class="w-4 h-4" />
        </a>
    </section>

    {{-- SSS --}}
    <x-faq-section
        :title="__('Frequently Asked Questions — English-taught programs')"
        :faqs="[
            ['q' => __('Do I need German for an English-taught programme?'), 'a' => __('For admission to a fully English-taught programme, universities usually ask for English, not German. Some programmes still expect basic German at a later stage, for example before graduation, and bilingual programmes can require German for part of the studies. Outside the lecture hall, German helps with everyday life and many student jobs.')],
            ['q' => __('Is IELTS or TOEFL always required?'), 'a' => __('There is no single rule. Each programme decides which English certificates and scores it accepts, and whether a previous degree taught in English can replace a test. Check the language requirement on the programme\'s official page.')],
            ['q' => __('Are there English-taught Bachelor\'s programmes?'), 'a' => __('Yes, but there are far fewer than English-taught Master\'s programmes. Most Bachelor\'s programmes at public universities are taught in German, so check the language of instruction first.')],
            ['q' => __('Are English-taught programmes tuition-free?'), 'a' => __('The language of instruction does not decide the fee. Most public universities charge no general tuition fees, but Baden-Württemberg charges students from outside the EU/EEA €1,500 per semester and some universities, such as TUM, set their own fees. The semester contribution is paid everywhere.')],
        ]"
    />
</div>
@endsection
