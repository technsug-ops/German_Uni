@extends('layouts.app')

@section('title', __('All Programs') . ' — ' . number_format($total_all, 0, ',', '.') . ' ' . __('Programs') . ' — ' . brand('name'))

<x-seo
    :title="__('All Programs — :n Programs in Germany', ['n' => number_format($total_all, 0, ',', '.')])"
    :description="__('Filter all Bachelor, Master, PhD programs in Germany by field, language, university. :n programs in our database.', ['n' => number_format($total_all, 0, ',', '.')])"
/>
<x-json-ld :data="\App\Support\Seo::breadcrumbs([
    ['name' => __('Home'), 'url' => route('home')],
    ['name' => __('Programs'), 'url' => route('programs.index')],
])" />

@php
    $degreeLabels = [
        'bachelor' => degree_label('bachelor'), 'master' => degree_label('master'), 'phd' => degree_label('phd'),
        'staatsexamen' => degree_label('staatsexamen'), 'diplom' => degree_label('diplom'), 'magister' => __('Magister'), 'other' => __('Other'),
    ];
    $langLabels = ['de' => __('German'), 'en' => __('English'), 'both' => __('German + English')];
    $semesterLabels = ['winter' => __('Winter (Wintersemester)'), 'summer' => __('Summer (Sommersemester)')];
    $durationLabels = ['short' => __('Short (1-3 sem)'), 'mid' => __('Medium (4-6 sem)'), 'long' => __('Long (7+ sem)')];

    $nonEuTuitionStates = ['baden-wurttemberg', 'sachsen-anhalt'];

    $hasFilter = (bool) ($filters['q'] || $filters['degree'] || $filters['language'] || $filters['field']
                       || $filters['state'] || $filters['uni'] || $filters['has_tr'] || $filters['free_only']
                       || $filters['no_app_fee'] || $filters['uni_assist'] || $filters['semester']
                       || $filters['deadline_open'] || $filters['duration_range']);

    // Gelişmiş arama'yı otomatik aç: temel olmayan bir filtre seçiliyse
    $advancedActive = (bool) ($filters['field'] || $filters['state'] || $filters['uni']
                            || $filters['has_tr'] || $filters['free_only'] || $filters['no_app_fee']
                            || $filters['uni_assist'] || $filters['semester'] || $filters['deadline_open']
                            || $filters['duration_range']);
@endphp

@section('content')

{{-- =================================================================== --}}
{{-- HERO + SEARCH (Hochschulkompass tarzı 2 katmanlı)                     --}}
{{-- =================================================================== --}}
<section class="bg-gradient-to-br from-primary-700 via-primary-600 to-primary-800 text-white">
    <div class="max-w-[1400px] mx-auto px-4 py-10">
        <h1 class="text-3xl md:text-4xl font-extrabold mb-2">{{ __('Programs & Subjects') }}</h1>
        <p class="text-primary-100 mb-4">
            <strong>{{ number_format($total_all, 0, ',', '.') }}</strong> {{ __('programs') }} ·
            <strong>{{ number_format($total_en, 0, ',', '.') }}</strong> {{ __('English') }}
        </p>
        <div class="flex flex-wrap gap-2 mb-6">
            <a href="{{ route('admission-free.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-semibold transition">
                🟢 {{ __('NC-free (zulassungsfrei) programs') }} <x-svg-icon name="arrow-right" class="w-4 h-4" />
            </a>
            <a href="{{ route('discover.english') }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-blue-500 hover:bg-blue-600 text-white text-sm font-semibold transition">
                🇬🇧 {{ __('English-taught programs') }} <x-svg-icon name="arrow-right" class="w-4 h-4" />
            </a>
            <a href="{{ route('discover.tuition-free') }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-green-600 hover:bg-green-700 text-white text-sm font-semibold transition">
                💶 {{ __('Tuition-free programs') }} <x-svg-icon name="arrow-right" class="w-4 h-4" />
            </a>
        </div>

        <form action="{{ route('programs.index') }}" method="GET" class="bg-white rounded-xl shadow-2xl p-4 text-gray-900"
              data-async-filter-form="#async-filter-results"
              data-no-loading>
            {{-- BASİT ARAMA: search + 3 select + Ara butonu --}}
            <div class="grid grid-cols-1 md:grid-cols-12 gap-2 items-stretch">
                {{-- Search input --}}
                <div class="md:col-span-6 flex items-center px-3 border border-gray-300 rounded-lg">
                    <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.3-4.3M10.5 18a7.5 7.5 0 1 0 0-15 7.5 7.5 0 0 0 0 15Z"/>
                    </svg>
                    <input type="text" name="q" value="{{ $filters['q'] }}"
                           placeholder="{{ __('Studiengang, subject area, university...') }}"
                           class="flex-1 px-3 py-2.5 placeholder-gray-400 focus:outline-none bg-transparent">
                </div>

                {{-- Derece (Abschluss) --}}
                <div class="md:col-span-2">
                    <select name="degree" onchange="this.form.submit()" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:border-primary-500 focus:outline-none bg-white">
                        <option value="">{{ __('Degree') }}</option>
                        @foreach ($degreeLabels as $k => $label)
                            <option value="{{ $k }}" @selected($filters['degree'] === $k)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Dil (Hauptunterrichtssprache) --}}
                <div class="md:col-span-2">
                    <select name="language" onchange="this.form.submit()" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:border-primary-500 focus:outline-none bg-white">
                        <option value="">{{ __('Language') }}</option>
                        @foreach ($langLabels as $k => $label)
                            <option value="{{ $k }}" @selected($filters['language'] === $k)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Ara butonu --}}
                <div class="md:col-span-2">
                    <button type="submit" class="w-full h-full bg-accent-500 hover:bg-accent-600 text-white font-bold px-4 py-2.5 rounded-lg transition">
                        {{ __('Search') }}
                    </button>
                </div>
            </div>

            {{-- Quick filter chips (her zaman görünür — popüler kombinasyonlar tek tıkla) --}}
            <div class="flex items-center flex-wrap gap-2 mt-3 pt-3 border-t border-gray-100">
                <span class="text-xs text-gray-500 mr-1">{{ __('Quick:') }}</span>

                {{-- 1) Sadece İngilizce --}}
                <a href="{{ request()->fullUrlWithQuery(['language' => $filters['language'] === 'en' ? null : 'en']) }}"
                   class="inline-flex items-center gap-1 text-xs px-3 py-1.5 rounded-full border transition
                          {{ $filters['language'] === 'en' ? 'bg-blue-600 text-white border-blue-600' : 'bg-blue-50 text-blue-700 border-blue-200 hover:bg-blue-100' }}">
                    {{ __('English only') }}
                </a>

                {{-- 2) Türkçe açıklamalı (description_tr) --}}
                @if (app()->getLocale() === 'tr')
                <a href="{{ request()->fullUrlWithQuery(['has_tr' => $filters['has_tr'] ? null : 1]) }}"
                   class="inline-flex items-center gap-1 text-xs px-3 py-1.5 rounded-full border transition
                          {{ $filters['has_tr'] ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100' }}">
                    Türkçe rehberli
                </a>
                @endif

                {{-- 3) Bachelor only --}}
                <a href="{{ request()->fullUrlWithQuery(['degree' => $filters['degree'] === 'bachelor' ? null : 'bachelor']) }}"
                   class="inline-flex items-center gap-1 text-xs px-3 py-1.5 rounded-full border transition
                          {{ $filters['degree'] === 'bachelor' ? 'bg-amber-600 text-white border-amber-600' : 'bg-amber-50 text-amber-700 border-amber-200 hover:bg-amber-100' }}">
                    Bachelor
                </a>

                {{-- 4) Master only --}}
                <a href="{{ request()->fullUrlWithQuery(['degree' => $filters['degree'] === 'master' ? null : 'master']) }}"
                   class="inline-flex items-center gap-1 text-xs px-3 py-1.5 rounded-full border transition
                          {{ $filters['degree'] === 'master' ? 'bg-purple-600 text-white border-purple-600' : 'bg-purple-50 text-purple-700 border-purple-200 hover:bg-purple-100' }}">
                    Master
                </a>

                {{-- 5) Başvuru ücretsiz --}}
                <a href="{{ request()->fullUrlWithQuery(['no_app_fee' => $filters['no_app_fee'] ? null : 1]) }}"
                   class="inline-flex items-center gap-1 text-xs px-3 py-1.5 rounded-full border transition
                          {{ $filters['no_app_fee'] ? 'bg-teal-600 text-white border-teal-600' : 'bg-teal-50 text-teal-700 border-teal-200 hover:bg-teal-100' }}">
                    {{ __('Free application') }}
                </a>

                {{-- 6) Deadline açık --}}
                <a href="{{ request()->fullUrlWithQuery(['deadline_open' => $filters['deadline_open'] ? null : 1]) }}"
                   class="inline-flex items-center gap-1 text-xs px-3 py-1.5 rounded-full border transition
                          {{ $filters['deadline_open'] ? 'bg-rose-600 text-white border-rose-600' : 'bg-rose-50 text-rose-700 border-rose-200 hover:bg-rose-100' }}">
                    {{ __('Open deadline') }}
                </a>

                {{-- 7) Uni-Assist üzerinden --}}
                <a href="{{ request()->fullUrlWithQuery(['uni_assist' => $filters['uni_assist'] ? null : 1]) }}"
                   class="inline-flex items-center gap-1 text-xs px-3 py-1.5 rounded-full border transition
                          {{ $filters['uni_assist'] ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-indigo-50 text-indigo-700 border-indigo-200 hover:bg-indigo-100' }}">
                    {{ __('Uni-Assist member') }}
                </a>
            </div>

            {{-- "Erweiterte Suche" toggle ve sayaç --}}
            <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-100 text-sm">
                <button type="button" id="advToggle"
                        class="inline-flex items-center gap-2 text-primary-700 hover:text-primary-900 font-semibold">
                    <svg class="w-4 h-4 transition-transform" id="advArrow" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                    {{ __('Advanced Search') }}
                    @if ($advancedActive)
                        <span class="inline-block w-2 h-2 rounded-full bg-accent-500 animate-pulse" title="{{ __('Active filters present') }}"></span>
                    @endif
                </button>
                <p class="text-gray-600">
                    <strong class="text-primary-700">{{ number_format($programs->total(), 0, ',', '.') }}</strong> {{ __('results') }}
                    @if ($hasFilter)
                        <a href="{{ route('programs.index') }}" class="ml-3 text-accent-600 hover:text-accent-800">↻ {{ __('Clear') }}</a>
                    @endif
                </p>
            </div>

            {{-- =========== ERWEITERTE SUCHE PANEL =========== --}}
            <div id="advPanel" class="{{ $advancedActive ? '' : 'hidden' }} mt-4 pt-4 border-t border-gray-100">

                {{-- STUDIENGANGSMERKMALE (Program Özellikleri) --}}
                <h3 class="text-xs font-bold uppercase tracking-wider text-gray-500 mb-3">{{ __('Program properties') }}</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-5">

                    {{-- Studienform — DB'de boş, "yakında" --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">{{ __('Study form') }}</label>
                        <select disabled class="w-full border border-gray-200 rounded px-3 py-2 text-sm bg-gray-50 text-gray-400 cursor-not-allowed">
                            <option>{{ __('Coming soon') }}</option>
                        </select>
                    </div>

                    {{-- Fächergruppen (Alan) --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">{{ __('Field') }}</label>
                        <select name="field" onchange="this.form.submit()" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:border-primary-500 focus:outline-none">
                            <option value="">{{ __('All') }}</option>
                            @foreach ($fields as $f)
                                <option value="{{ $f->slug }}" @selected($filters['field'] === $f->slug)>{{ $f->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Studienbeginn (Sömestre) --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">{{ __('Start semester') }}</label>
                        <select name="semester" onchange="this.form.submit()" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:border-primary-500 focus:outline-none">
                            <option value="">{{ __('All') }}</option>
                            @foreach ($semesterLabels as $k => $label)
                                <option value="{{ $k }}" @selected($filters['semester'] === $k)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Süre --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">{{ __('Duration') }}</label>
                        <select name="duration_range" onchange="this.form.submit()" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:border-primary-500 focus:outline-none">
                            <option value="">{{ __('All') }}</option>
                            @foreach ($durationLabels as $k => $label)
                                <option value="{{ $k }}" @selected($filters['duration_range'] === $k)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Zulassungsmodus (NC) — DB'de boş --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">{{ __('Admission mode') }} <span class="text-gray-400">(NC)</span></label>
                        <select disabled class="w-full border border-gray-200 rounded px-3 py-2 text-sm bg-gray-50 text-gray-400 cursor-not-allowed">
                            <option>{{ __('Coming soon') }}</option>
                        </select>
                    </div>

                    {{-- Studieren ohne Abitur — DB'de yok --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">{{ __('Study without Abitur') }}</label>
                        <select disabled class="w-full border border-gray-200 rounded px-3 py-2 text-sm bg-gray-50 text-gray-400 cursor-not-allowed">
                            <option>{{ __('Coming soon') }}</option>
                        </select>
                    </div>

                    {{-- Mastertyp — DB'de yok --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">{{ __('Master type') }}</label>
                        <select disabled class="w-full border border-gray-200 rounded px-3 py-2 text-sm bg-gray-50 text-gray-400 cursor-not-allowed">
                            <option>{{ __('Coming soon') }}</option>
                        </select>
                    </div>

                    {{-- Doppelabschluss — DB'de yok --}}
                    <div class="flex items-end">
                        <label class="flex items-center gap-2 text-sm cursor-not-allowed text-gray-400">
                            <input type="checkbox" disabled class="rounded">
                            <span>{{ __('Double degree') }} <span class="text-gray-300">({{ __('coming soon') }})</span></span>
                        </label>
                    </div>
                </div>

                {{-- HOCHSCHULMERKMALE --}}
                <h3 class="text-xs font-bold uppercase tracking-wider text-gray-500 mb-3 mt-2">{{ __('University and geographic properties') }}</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-5">

                    {{-- Hochschulname --}}
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-gray-700 mb-1">{{ __('University name') }} <span class="text-gray-400">(slug)</span></label>
                        <input type="text" name="uni" value="{{ $filters['uni'] }}"
                               placeholder="{{ __('e.g. universitat-heidelberg-q151510') }}"
                               class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:border-primary-500 focus:outline-none">
                    </div>

                    {{-- Bundesland (Eyalet) --}}
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-gray-700 mb-1">{{ __('Federal state') }}</label>
                        <select name="state" onchange="this.form.submit()" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:border-primary-500 focus:outline-none">
                            <option value="">{{ __('All') }}</option>
                            @foreach ($states as $s)
                                <option value="{{ $s->slug }}" @selected($filters['state'] === $s->slug)>{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Hochschultyp / Trägerschaft --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">{{ __('University type') }}</label>
                        <select disabled class="w-full border border-gray-200 rounded px-3 py-2 text-sm bg-gray-50 text-gray-400 cursor-not-allowed">
                            <option>{{ __('Coming soon') }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">{{ __('Funding type') }}</label>
                        <select disabled class="w-full border border-gray-200 rounded px-3 py-2 text-sm bg-gray-50 text-gray-400 cursor-not-allowed">
                            <option>{{ __('Coming soon') }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">{{ __('Postal code') }} <span class="text-gray-400">(PLZ)</span></label>
                        <input type="text" disabled placeholder="{{ __('Coming soon') }}" class="w-full border border-gray-200 rounded px-3 py-2 text-sm bg-gray-50 text-gray-400 cursor-not-allowed">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">{{ __('Within radius') }}</label>
                        <select disabled class="w-full border border-gray-200 rounded px-3 py-2 text-sm bg-gray-50 text-gray-400 cursor-not-allowed">
                            <option>{{ __('Coming soon (5/10/25/50 km)') }}</option>
                        </select>
                    </div>
                </div>

                {{-- INTERNATIONAL STUDENT FİLTRELERİ --}}
                <h3 class="text-xs font-bold uppercase tracking-wider text-accent-700 mb-3 mt-2">{{ __('International Student Filters') }}</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-5">
                    @if (app()->getLocale() === 'tr')
                    <label class="flex items-center gap-2 text-sm cursor-pointer p-2 bg-amber-50 hover:bg-amber-100 rounded border border-amber-200">
                        <input type="checkbox" name="has_tr" value="1" onchange="this.form.submit()" @checked($filters['has_tr']) class="rounded border-gray-300 text-primary-600">
                        <span>Türkçe açıklamalı</span>
                    </label>
                    @endif
                    <label class="flex items-center gap-2 text-sm cursor-pointer p-2 bg-amber-50 hover:bg-amber-100 rounded border border-amber-200">
                        <input type="checkbox" name="uni_assist" value="1" onchange="this.form.submit()" @checked($filters['uni_assist']) class="rounded border-gray-300 text-primary-600">
                        <span>{{ __('Uni-Assist member') }}</span>
                    </label>
                    <label class="flex items-center gap-2 text-sm cursor-pointer p-2 bg-amber-50 hover:bg-amber-100 rounded border border-amber-200">
                        <input type="checkbox" name="free_only" value="1" onchange="this.form.submit()" @checked($filters['free_only']) class="rounded border-gray-300 text-primary-600">
                        <span>{{ __('Tuition-free') }}</span>
                    </label>
                    <label class="flex items-center gap-2 text-sm cursor-pointer p-2 bg-amber-50 hover:bg-amber-100 rounded border border-amber-200">
                        <input type="checkbox" name="no_app_fee" value="1" onchange="this.form.submit()" @checked($filters['no_app_fee']) class="rounded border-gray-300 text-primary-600">
                        <span>{{ __('Free application') }}</span>
                    </label>
                    <label class="flex items-center gap-2 text-sm cursor-pointer p-2 bg-amber-50 hover:bg-amber-100 rounded border border-amber-200">
                        <input type="checkbox" name="deadline_open" value="1" onchange="this.form.submit()" @checked($filters['deadline_open']) class="rounded border-gray-300 text-primary-600">
                        <span>{{ __('Open applications') }}</span>
                    </label>
                    <div class="text-xs text-gray-500 p-2 self-center">
                        {!! __('Studienkolleg, VPD, TestDaF level, etc. <strong>coming soon</strong>.') !!}
                    </div>
                </div>

                {{-- Sıralama + Submit --}}
                <div class="flex flex-col sm:flex-row gap-3 items-end justify-between pt-4 border-t border-gray-100">
                    <div class="w-full sm:w-64">
                        <label class="block text-xs font-semibold text-gray-700 mb-1">{{ __('Sort') }}</label>
                        <select name="sort" onchange="this.form.submit()" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:border-primary-500 focus:outline-none">
                            <option value="relevance" @selected($filters['sort'] === 'relevance')>{{ __('Relevance (translated entries first)') }}</option>
                            <option value="name"      @selected($filters['sort'] === 'name')>{{ __('Name (A-Z)') }}</option>
                            <option value="duration"  @selected($filters['sort'] === 'duration')>{{ __('Duration (short → long)') }}</option>
                            <option value="deadline"  @selected($filters['sort'] === 'deadline')>{{ __('Nearest deadline') }}</option>
                        </select>
                    </div>
                    <div class="flex gap-2 w-full sm:w-auto">
                        <a href="{{ route('programs.index') }}"
                           class="px-5 py-2.5 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 transition text-sm font-semibold">
                            ↻ {{ __('Reset all') }}
                        </a>
                        <button type="submit"
                                class="flex-1 sm:flex-initial bg-primary-700 hover:bg-primary-800 text-white font-bold px-8 py-2.5 rounded-lg transition">
                            {{ __('Show :n results', ['n' => number_format($programs->total(), 0, ',', '.')]) }}
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

@if (($admission_counts ?? collect())->isNotEmpty())
    {{-- DB'de NC verisi VAR — yalnızca >0 program içeren değerin chip'ini göster.
         (bundesweit verisi yoksa chip hiç çıkmaz → "tıkla, 0 sonuç" hayal kırıklığı yok.) --}}
    <div class="bg-blue-50 border-b border-blue-200">
        <div class="max-w-[1400px] mx-auto px-4 py-3 flex items-center gap-3 text-sm flex-wrap">
            <span class="text-blue-900 font-semibold flex-shrink-0">{{ __('Admission mode') }}:</span>
            @if (($admission_counts['zulassungsfrei'] ?? 0) > 0)
            <a href="{{ request()->fullUrlWithQuery(['admission' => 'zulassungsfrei', 'page' => null]) }}"
               class="inline-block text-xs font-semibold px-3 py-1.5 rounded transition whitespace-nowrap
                      {{ ($filters['admission'] ?? null) === 'zulassungsfrei'
                            ? 'bg-blue-600 text-white'
                            : 'bg-white border border-blue-300 text-blue-700 hover:bg-blue-100' }}">
                {{ __('NC Frei (Zulassungsfrei)') }} ({{ $admission_counts['zulassungsfrei'] }})
            </a>
            @endif
            @if (($admission_counts['oertlich'] ?? 0) > 0)
            <a href="{{ request()->fullUrlWithQuery(['admission' => 'oertlich', 'page' => null]) }}"
               class="inline-block text-xs font-semibold px-3 py-1.5 rounded transition whitespace-nowrap
                      {{ ($filters['admission'] ?? null) === 'oertlich'
                            ? 'bg-blue-600 text-white'
                            : 'bg-white border border-blue-300 text-blue-700 hover:bg-blue-100' }}">
                {{ __('Local NC (Örtlich)') }} ({{ $admission_counts['oertlich'] }})
            </a>
            @endif
            @if (($admission_counts['bundesweit'] ?? 0) > 0)
            <a href="{{ request()->fullUrlWithQuery(['admission' => 'bundesweit', 'page' => null]) }}"
               class="inline-block text-xs font-semibold px-3 py-1.5 rounded transition whitespace-nowrap
                      {{ ($filters['admission'] ?? null) === 'bundesweit'
                            ? 'bg-blue-600 text-white'
                            : 'bg-white border border-blue-300 text-blue-700 hover:bg-blue-100' }}">
                {{ __('Nationwide NC (Bundesweit)') }} ({{ $admission_counts['bundesweit'] }})
            </a>
            @endif
            @if (! empty($filters['admission']))
                <a href="{{ request()->fullUrlWithQuery(['admission' => null, 'page' => null]) }}" class="text-xs text-gray-600 hover:text-gray-900 underline">{{ __('clear') }}</a>
            @endif
            <span class="ml-auto text-xs text-blue-700">
                {{ __('More detailed:') }} <a href="{{ hochschulkompass_url($filters['q'] ?: null) }}" target="_blank" rel="noopener" class="underline hover:text-blue-900">Hochschulkompass ↗</a>
            </span>
        </div>
    </div>
@else
    {{-- DB'de NC verisi yok — Hochschulkompass'a yönlendir --}}
    <div class="bg-blue-50 border-b border-blue-200">
        <div class="max-w-[1400px] mx-auto px-4 py-3 flex items-start gap-3 text-sm">
            <p class="text-blue-900 flex-1">
                {!! __('<strong>NC status (Zulassungsmodus)</strong> data is not currently stored in our database. Hochschulkompass\'s official <em>Zulassungsmodus</em> filter offers the most up-to-date information:') !!}
            </p>
            <div class="flex flex-wrap gap-2 flex-shrink-0">
                <a href="{{ hochschulkompass_url($filters['q'] ?: null, 'zulassungsfrei') }}"
                   target="_blank" rel="noopener"
                   class="inline-block bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold px-3 py-1.5 rounded transition whitespace-nowrap">
                    {{ __('NC Frei programs') }} →
                </a>
                <a href="{{ hochschulkompass_url($filters['q'] ?: null) }}"
                   target="_blank" rel="noopener"
                   class="inline-block bg-white hover:bg-blue-100 border border-blue-300 text-blue-700 text-xs font-semibold px-3 py-1.5 rounded transition whitespace-nowrap">
                    {{ __('Search on Hochschulkompass') }}
                </a>
            </div>
        </div>
    </div>
@endif

{{-- Uluslararası öğrenci bilgi banner --}}
<div class="bg-amber-50 border-b border-amber-200">
    <div class="max-w-[1400px] mx-auto px-4 py-3 flex items-start gap-3 text-sm">
        <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-5.25m0 0a6.01 6.01 0 0 0 1.5-.189m-1.5.189a6.01 6.01 0 0 1-1.5-.189m3.75 7.478a12.06 12.06 0 0 1-4.5 0m3.75 2.383a14.406 14.406 0 0 1-3 0M14.25 18v-.192c0-.983.658-1.823 1.508-2.316a7.5 7.5 0 1 0-7.517 0c.85.493 1.509 1.333 1.509 2.316V18"/></svg>
        <p class="text-amber-900">
            {!! __('<strong>Critical for international students:</strong> Depending on your high school type, a Bachelor\'s may require <strong>1 year of Studienkolleg</strong>. For German programs you need <strong>TestDaF TDN 4 / DSH 2</strong>; for English programs <strong>IELTS 6.5</strong>. In <strong>Baden-Württemberg</strong> and <strong>Sachsen-Anhalt</strong>, non-EU students pay a <strong>€1,500/sem</strong> tuition fee.') !!}
            <a href="{{ route('faqs.topic', 'vize') }}" class="underline hover:text-amber-950">{{ __('FAQ') }} →</a>
        </p>
    </div>
</div>

{{-- =================================================================== --}}
{{-- SONUÇLAR — full width                                                 --}}
{{-- =================================================================== --}}
<div class="max-w-[1400px] mx-auto px-4 py-6">
    <div id="async-filter-results" data-async-filter aria-live="polite" aria-busy="false">
        @include('programs._grid')
    </div>
</div>

<script>
(function () {
    const btn   = document.getElementById('advToggle');
    const panel = document.getElementById('advPanel');
    const arrow = document.getElementById('advArrow');
    if (!btn || !panel) return;

    // Açıksa ok aşağı, kapalıysa sağa
    const isOpen = !panel.classList.contains('hidden');
    arrow.style.transform = isOpen ? 'rotate(0deg)' : 'rotate(-90deg)';

    btn.addEventListener('click', () => {
        const open = panel.classList.toggle('hidden') === false;
        arrow.style.transform = open ? 'rotate(0deg)' : 'rotate(-90deg)';
    });
})();
</script>
@endsection
