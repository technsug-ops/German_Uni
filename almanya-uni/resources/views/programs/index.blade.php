@extends('layouts.app')

@section('title', __('All Programs') . ' — ' . number_format($total_all, 0, ',', '.') . ' ' . __('Programs') . '  — ' . brand('name'))

<x-seo
    :title="__('All Programs — :n Programs in Germany', ['n' => number_format($total_all, 0, ',', '.')])"
    :description="__('Filter all Bachelor, Master, PhD programs in Germany by field, language, university. :n programs in our database.', ['n' => number_format($total_all, 0, ',', '.')])"
/>

@php
    $degreeLabels = [
        'bachelor' => 'Bachelor', 'master' => 'Master', 'phd' => __('PhD'),
        'staatsexamen' => 'Staatsexamen', 'diplom' => 'Diplom', 'magister' => 'Magister', 'other' => __('Other'),
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
        <p class="text-primary-100 mb-6">
            <strong>{{ number_format($total_all, 0, ',', '.') }}</strong> {{ __('programs') }} ·
            <strong>{{ number_format($total_en, 0, ',', '.') }}</strong> {{ __('English') }}
        </p>

        <form action="{{ route('programs.index') }}" method="GET" class="bg-white rounded-xl shadow-2xl p-4 text-gray-900">
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
                    🇬🇧 {{ __('English only') }}
                </a>

                {{-- 2) Türkçe açıklamalı (description_tr) --}}
                @if (app()->getLocale() === 'tr')
                <a href="{{ request()->fullUrlWithQuery(['has_tr' => $filters['has_tr'] ? null : 1]) }}"
                   class="inline-flex items-center gap-1 text-xs px-3 py-1.5 rounded-full border transition
                          {{ $filters['has_tr'] ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100' }}">
                    🇹🇷 Türkçe rehberli
                </a>
                @endif

                {{-- 3) Bachelor only --}}
                <a href="{{ request()->fullUrlWithQuery(['degree' => $filters['degree'] === 'bachelor' ? null : 'bachelor']) }}"
                   class="inline-flex items-center gap-1 text-xs px-3 py-1.5 rounded-full border transition
                          {{ $filters['degree'] === 'bachelor' ? 'bg-amber-600 text-white border-amber-600' : 'bg-amber-50 text-amber-700 border-amber-200 hover:bg-amber-100' }}">
                    🎓 Bachelor
                </a>

                {{-- 4) Master only --}}
                <a href="{{ request()->fullUrlWithQuery(['degree' => $filters['degree'] === 'master' ? null : 'master']) }}"
                   class="inline-flex items-center gap-1 text-xs px-3 py-1.5 rounded-full border transition
                          {{ $filters['degree'] === 'master' ? 'bg-purple-600 text-white border-purple-600' : 'bg-purple-50 text-purple-700 border-purple-200 hover:bg-purple-100' }}">
                    🎓 Master
                </a>

                {{-- 5) Başvuru ücretsiz --}}
                <a href="{{ request()->fullUrlWithQuery(['no_app_fee' => $filters['no_app_fee'] ? null : 1]) }}"
                   class="inline-flex items-center gap-1 text-xs px-3 py-1.5 rounded-full border transition
                          {{ $filters['no_app_fee'] ? 'bg-teal-600 text-white border-teal-600' : 'bg-teal-50 text-teal-700 border-teal-200 hover:bg-teal-100' }}">
                    💰 {{ __('Free application') }}
                </a>

                {{-- 6) Deadline açık --}}
                <a href="{{ request()->fullUrlWithQuery(['deadline_open' => $filters['deadline_open'] ? null : 1]) }}"
                   class="inline-flex items-center gap-1 text-xs px-3 py-1.5 rounded-full border transition
                          {{ $filters['deadline_open'] ? 'bg-rose-600 text-white border-rose-600' : 'bg-rose-50 text-rose-700 border-rose-200 hover:bg-rose-100' }}">
                    📅 {{ __('Open deadline') }}
                </a>

                {{-- 7) Uni-Assist üzerinden --}}
                <a href="{{ request()->fullUrlWithQuery(['uni_assist' => $filters['uni_assist'] ? null : 1]) }}"
                   class="inline-flex items-center gap-1 text-xs px-3 py-1.5 rounded-full border transition
                          {{ $filters['uni_assist'] ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-indigo-50 text-indigo-700 border-indigo-200 hover:bg-indigo-100' }}">
                    🤝 {{ __('Uni-Assist member') }}
                </a>
            </div>

            {{-- "Erweiterte Suche" toggle ve sayaç --}}
            <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-100 text-sm">
                <button type="button" id="advToggle"
                        class="inline-flex items-center gap-2 text-primary-700 hover:text-primary-900 font-semibold">
                    <svg class="w-4 h-4 transition-transform" id="advArrow" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                    {{ __('Erweiterte Suche / Advanced Search') }}
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
                <h3 class="text-xs font-bold uppercase tracking-wider text-gray-500 mb-3">{{ __('Studiengangsmerkmale (Program Properties)') }}</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-5">

                    {{-- Studienform — DB'de boş, "yakında" --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Studienform</label>
                        <select disabled class="w-full border border-gray-200 rounded px-3 py-2 text-sm bg-gray-50 text-gray-400 cursor-not-allowed">
                            <option>{{ __('Coming soon') }}</option>
                        </select>
                    </div>

                    {{-- Fächergruppen (Alan) --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Fächergruppen <span class="text-gray-400">({{ __('Field') }})</span></label>
                        <select name="field" onchange="this.form.submit()" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:border-primary-500 focus:outline-none">
                            <option value="">{{ __('All') }}</option>
                            @foreach ($fields as $f)
                                <option value="{{ $f->slug }}" @selected($filters['field'] === $f->slug)>{{ $f->icon }} {{ $f->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Studienbeginn (Sömestre) --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Studienbeginn <span class="text-gray-400">({{ __('Semester') }})</span></label>
                        <select name="semester" onchange="this.form.submit()" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:border-primary-500 focus:outline-none">
                            <option value="">{{ __('All') }}</option>
                            @foreach ($semesterLabels as $k => $label)
                                <option value="{{ $k }}" @selected($filters['semester'] === $k)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Süre --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Studiendauer <span class="text-gray-400">({{ __('Duration') }})</span></label>
                        <select name="duration_range" onchange="this.form.submit()" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:border-primary-500 focus:outline-none">
                            <option value="">{{ __('All') }}</option>
                            @foreach ($durationLabels as $k => $label)
                                <option value="{{ $k }}" @selected($filters['duration_range'] === $k)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Zulassungsmodus (NC) — DB'de boş --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Zulassungsmodus <span class="text-gray-400">(NC)</span></label>
                        <select disabled class="w-full border border-gray-200 rounded px-3 py-2 text-sm bg-gray-50 text-gray-400 cursor-not-allowed">
                            <option>{{ __('Coming soon') }}</option>
                        </select>
                    </div>

                    {{-- Studieren ohne Abitur — DB'de yok --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Studieren ohne Abitur</label>
                        <select disabled class="w-full border border-gray-200 rounded px-3 py-2 text-sm bg-gray-50 text-gray-400 cursor-not-allowed">
                            <option>{{ __('Coming soon') }}</option>
                        </select>
                    </div>

                    {{-- Mastertyp — DB'de yok --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Mastertyp</label>
                        <select disabled class="w-full border border-gray-200 rounded px-3 py-2 text-sm bg-gray-50 text-gray-400 cursor-not-allowed">
                            <option>{{ __('Coming soon') }}</option>
                        </select>
                    </div>

                    {{-- Doppelabschluss — DB'de yok --}}
                    <div class="flex items-end">
                        <label class="flex items-center gap-2 text-sm cursor-not-allowed text-gray-400">
                            <input type="checkbox" disabled class="rounded">
                            <span>Doppelabschluss <span class="text-gray-300">({{ __('coming soon') }})</span></span>
                        </label>
                    </div>
                </div>

                {{-- HOCHSCHULMERKMALE --}}
                <h3 class="text-xs font-bold uppercase tracking-wider text-gray-500 mb-3 mt-2">{{ __('Hochschulmerkmale, geografische Merkmale (University and geographic properties)') }}</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-5">

                    {{-- Hochschulname --}}
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Hochschulname <span class="text-gray-400">(slug)</span></label>
                        <input type="text" name="uni" value="{{ $filters['uni'] }}"
                               placeholder="{{ __('e.g. universitat-heidelberg-q151510') }}"
                               class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:border-primary-500 focus:outline-none">
                    </div>

                    {{-- Bundesland (Eyalet) --}}
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Bundesland <span class="text-gray-400">({{ __('State') }})</span></label>
                        <select name="state" onchange="this.form.submit()" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:border-primary-500 focus:outline-none">
                            <option value="">{{ __('All') }}</option>
                            @foreach ($states as $s)
                                <option value="{{ $s->slug }}" @selected($filters['state'] === $s->slug)>{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Hochschultyp / Trägerschaft --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Hochschultyp</label>
                        <select disabled class="w-full border border-gray-200 rounded px-3 py-2 text-sm bg-gray-50 text-gray-400 cursor-not-allowed">
                            <option>{{ __('Coming soon') }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Trägerschaft</label>
                        <select disabled class="w-full border border-gray-200 rounded px-3 py-2 text-sm bg-gray-50 text-gray-400 cursor-not-allowed">
                            <option>{{ __('Coming soon') }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">PLZ <span class="text-gray-400">({{ __('Postal code') }})</span></label>
                        <input type="text" disabled placeholder="{{ __('Coming soon') }}" class="w-full border border-gray-200 rounded px-3 py-2 text-sm bg-gray-50 text-gray-400 cursor-not-allowed">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">im Umkreis von</label>
                        <select disabled class="w-full border border-gray-200 rounded px-3 py-2 text-sm bg-gray-50 text-gray-400 cursor-not-allowed">
                            <option>{{ __('Coming soon (5/10/25/50 km)') }}</option>
                        </select>
                    </div>
                </div>

                {{-- INTERNATIONAL STUDENT FİLTRELERİ --}}
                <h3 class="text-xs font-bold uppercase tracking-wider text-accent-700 mb-3 mt-2">🌍 {{ __('International Student Filters') }}</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-5">
                    @if (app()->getLocale() === 'tr')
                    <label class="flex items-center gap-2 text-sm cursor-pointer p-2 bg-amber-50 hover:bg-amber-100 rounded border border-amber-200">
                        <input type="checkbox" name="has_tr" value="1" onchange="this.form.submit()" @checked($filters['has_tr']) class="rounded border-gray-300 text-primary-600">
                        <span>🇹🇷 Türkçe açıklamalı</span>
                    </label>
                    @endif
                    <label class="flex items-center gap-2 text-sm cursor-pointer p-2 bg-amber-50 hover:bg-amber-100 rounded border border-amber-200">
                        <input type="checkbox" name="uni_assist" value="1" onchange="this.form.submit()" @checked($filters['uni_assist']) class="rounded border-gray-300 text-primary-600">
                        <span>📋 {{ __('Uni-Assist member') }}</span>
                    </label>
                    <label class="flex items-center gap-2 text-sm cursor-pointer p-2 bg-amber-50 hover:bg-amber-100 rounded border border-amber-200">
                        <input type="checkbox" name="free_only" value="1" onchange="this.form.submit()" @checked($filters['free_only']) class="rounded border-gray-300 text-primary-600">
                        <span>💰 {{ __('Tuition-free') }}</span>
                    </label>
                    <label class="flex items-center gap-2 text-sm cursor-pointer p-2 bg-amber-50 hover:bg-amber-100 rounded border border-amber-200">
                        <input type="checkbox" name="no_app_fee" value="1" onchange="this.form.submit()" @checked($filters['no_app_fee']) class="rounded border-gray-300 text-primary-600">
                        <span>📝 {{ __('Free application') }}</span>
                    </label>
                    <label class="flex items-center gap-2 text-sm cursor-pointer p-2 bg-amber-50 hover:bg-amber-100 rounded border border-amber-200">
                        <input type="checkbox" name="deadline_open" value="1" onchange="this.form.submit()" @checked($filters['deadline_open']) class="rounded border-gray-300 text-primary-600">
                        <span>📅 {{ __('Open applications') }}</span>
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
                            ↻ Zurücksetzen
                        </a>
                        <button type="submit"
                                class="flex-1 sm:flex-initial bg-primary-700 hover:bg-primary-800 text-white font-bold px-8 py-2.5 rounded-lg transition">
                            {{ __(':n Treffer anzeigen', ['n' => number_format($programs->total(), 0, ',', '.')]) }}
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

@if ($admission_data_available)
    {{-- DB'de NC verisi VAR — kendi filter chip'lerimizi göster --}}
    <div class="bg-blue-50 border-b border-blue-200">
        <div class="max-w-[1400px] mx-auto px-4 py-3 flex items-center gap-3 text-sm flex-wrap">
            <span class="text-blue-900 font-semibold flex-shrink-0">🔓 Zulassungsmodus:</span>
            <a href="{{ request()->fullUrlWithQuery(['admission' => 'zulassungsfrei', 'page' => null]) }}"
               class="inline-block text-xs font-semibold px-3 py-1.5 rounded transition whitespace-nowrap
                      {{ ($filters['admission'] ?? null) === 'zulassungsfrei'
                            ? 'bg-blue-600 text-white'
                            : 'bg-white border border-blue-300 text-blue-700 hover:bg-blue-100' }}">
                🔓 {{ __('NC Frei (Zulassungsfrei)') }}
            </a>
            <a href="{{ request()->fullUrlWithQuery(['admission' => 'oertlich', 'page' => null]) }}"
               class="inline-block text-xs font-semibold px-3 py-1.5 rounded transition whitespace-nowrap
                      {{ ($filters['admission'] ?? null) === 'oertlich'
                            ? 'bg-blue-600 text-white'
                            : 'bg-white border border-blue-300 text-blue-700 hover:bg-blue-100' }}">
                {{ __('Local NC (Örtlich)') }}
            </a>
            <a href="{{ request()->fullUrlWithQuery(['admission' => 'bundesweit', 'page' => null]) }}"
               class="inline-block text-xs font-semibold px-3 py-1.5 rounded transition whitespace-nowrap
                      {{ ($filters['admission'] ?? null) === 'bundesweit'
                            ? 'bg-blue-600 text-white'
                            : 'bg-white border border-blue-300 text-blue-700 hover:bg-blue-100' }}">
                {{ __('Nationwide NC (Bundesweit)') }}
            </a>
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
            <span class="text-xl flex-shrink-0">🔓</span>
            <p class="text-blue-900 flex-1">
                {!! __('<strong>NC status (Zulassungsmodus)</strong> data is not currently stored in our database. Hochschulkompass\'s official <em>Zulassungsmodus</em> filter offers the most up-to-date information:') !!}
            </p>
            <div class="flex flex-wrap gap-2 flex-shrink-0">
                <a href="{{ hochschulkompass_url($filters['q'] ?: null, 'zulassungsfrei') }}"
                   target="_blank" rel="noopener"
                   class="inline-block bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold px-3 py-1.5 rounded transition whitespace-nowrap">
                    🔓 {{ __('NC Frei programs') }} →
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
        <span class="text-xl flex-shrink-0">💡</span>
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

    <div class="flex items-center justify-between mb-4 flex-wrap gap-2">
        <p class="text-sm text-gray-700">
            <strong>{{ number_format($programs->total(), 0, ',', '.') }}</strong> {{ __('results') }}
            @if ($hasFilter)
                <span class="text-gray-500">{{ __('(filtered — :n total)', ['n' => number_format($total_all, 0, ',', '.')]) }}</span>
            @endif
        </p>
        <p class="text-sm text-gray-500">
            {{ __('Page :current / :last', ['current' => $programs->currentPage(), 'last' => max(1, $programs->lastPage())]) }}
        </p>
    </div>

    @if ($programs->isEmpty())
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-8 text-center">
            <p class="text-yellow-900 font-semibold mb-2">{{ __('No results found.') }}</p>
            <p class="text-yellow-800 text-sm mb-4">{{ __('Try relaxing the filters or change your search term.') }}</p>
            <a href="{{ route('programs.index') }}" class="inline-block bg-primary-600 hover:bg-primary-700 text-white px-5 py-2 rounded font-semibold transition">
                {{ __('All Programs') }}
            </a>
        </div>
    @else
        <div class="space-y-3">
            @foreach ($programs as $p)
                <a href="{{ route('programs.show', $p->slug) }}"
                   class="group block bg-white border border-gray-200 hover:border-primary-400 hover:shadow-md transition rounded-xl p-5">
                    <div class="flex items-start gap-4">
                        @if ($p->university->logo_url)
                            <img src="{{ $p->university->logo_url }}" alt=""
                                 class="w-12 h-12 object-contain bg-gray-50 rounded p-1 flex-shrink-0" loading="lazy" decoding="async">
                        @else
                            <div class="w-12 h-12 bg-primary-100 text-primary-700 rounded flex items-center justify-center font-bold flex-shrink-0">
                                {{ mb_substr($p->university->short_name ?? $p->university->name_de, 0, 2) }}
                            </div>
                        @endif

                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2 mb-1">
                                <h3 class="font-bold text-gray-900 leading-snug group-hover:text-primary-700 transition">
                                    {{ $p->name_de }}
                                </h3>
                                <div class="flex flex-wrap gap-1 flex-shrink-0">
                                    @if ($p->degree)
                                        <span class="inline-block text-xs font-semibold px-2 py-0.5 rounded-full
                                            @switch($p->degree)
                                                @case('bachelor') bg-green-100 text-green-700 @break
                                                @case('master')   bg-blue-100 text-blue-700 @break
                                                @case('phd')      bg-purple-100 text-purple-700 @break
                                                @default          bg-gray-100 text-gray-700
                                            @endswitch
                                        ">{{ $degreeLabels[$p->degree] ?? $p->degree }}</span>
                                    @endif
                                    @if ($p->language)
                                        <span class="inline-block text-xs font-semibold px-2 py-0.5 rounded-full whitespace-nowrap
                                            @switch($p->language)
                                                @case('en')   bg-blue-100 text-blue-700 @break
                                                @case('de')   bg-emerald-100 text-emerald-700 @break
                                                @case('both') bg-amber-100 text-amber-800 @break
                                            @endswitch
                                        ">
                                            @switch($p->language)
                                                @case('en') EN @break
                                                @case('de') DE @break
                                                @case('both') DE+EN @break
                                            @endswitch
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <p class="text-sm text-gray-600 mb-2">
                                {{ $p->university->display_name }}
                                @if ($p->university->city)
                                    · <span class="text-gray-500">{{ $p->university->city->name }}</span>
                                @endif
                            </p>

                            <div class="flex flex-wrap gap-3 text-xs text-gray-500 mb-2">
                                @if ($p->field)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-white"
                                          style="background-color: {{ $p->field->color }};">
                                        {{ $p->field->icon }} {{ $p->field->name }}
                                    </span>
                                @endif
                                @if ($p->degree_specification)
                                    <span>{{ $p->degree_specification }}</span>
                                @endif
                                @if ($p->duration_semesters)
                                    <span>⏱️ {{ $p->duration_semesters }} {{ __('sem') }}</span>
                                @endif
                                @if (! is_null($p->tuition_fee_eur))
                                    <span>
                                        💶 {{ $p->tuition_fee_eur == 0
                                            ? __('Free')
                                            : number_format($p->tuition_fee_eur, 0, ',', '.') . ' €/' . __('sem') }}
                                    </span>
                                @elseif ($p->university?->city?->state && in_array($p->university->city->state->slug, $nonEuTuitionStates))
                                    <span class="text-orange-700">💶 {{ __('Non-EU: ~€1,500/sem') }}</span>
                                @endif
                                @if ($p->application_deadline_winter)
                                    <span>📅 {{ __('Winter:') }} {{ $p->application_deadline_winter->format('d.m') }}</span>
                                @endif
                                @if ($p->application_deadline_summer)
                                    <span>📅 {{ __('Summer:') }} {{ $p->application_deadline_summer->format('d.m') }}</span>
                                @endif
                                @if ($p->university?->is_uni_assist_member)
                                    <span class="text-blue-700">📋 Uni-Assist</span>
                                @endif
                                @if ($p->admission_mode === 'zulassungsfrei')
                                    <span class="inline-flex items-center gap-1 text-emerald-700 font-semibold">🔓 {{ __('NC Frei') }}</span>
                                @elseif ($p->admission_mode === 'oertlich')
                                    <span class="text-orange-700">⚠️ {{ __('Local NC') }}</span>
                                @elseif ($p->admission_mode === 'bundesweit')
                                    <span class="text-red-700">🚦 {{ __('Nationwide NC') }}</span>
                                @endif
                            </div>

                            @if ($p->description && app()->getLocale() === 'tr')
                                <p class="text-sm text-gray-700 line-clamp-2">{{ \Illuminate\Support\Str::limit($p->description, 180) }}</p>
                            @elseif ($p->description_en)
                                <p class="text-sm text-gray-600 line-clamp-2">
                                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider mr-1">EN</span>
                                    {{ \Illuminate\Support\Str::limit($p->description_en, 160) }}
                                </p>
                            @endif
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $programs->links() }}
        </div>
    @endif
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
