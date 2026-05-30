@extends('layouts.app')

@section('title', ($q !== '' ? __('Search results for ":q"', ['q' => $q]) : __('Search')) . '  — ' . brand('name'))

@php
    $typeLabel = fn ($t) => match ($t) {
        'public' => __('Public'),
        'private' => __('Private'),
        'applied_sciences' => __('Applied Sciences'),
        'art' => __('Art'),
        'religion' => __('Religious'),
        default => $t ? ucfirst($t) : '-',
    };
    $degreeLabel = fn ($d) => match ($d) {
        'bachelor' => 'Bachelor',
        'master' => 'Master',
        'phd' => 'PhD',
        'staatsexamen' => 'Staatsexamen',
        default => ucfirst((string) $d),
    };
    $suggestions = ['TU München', 'Heidelberg', 'Berlin', 'Sperrkonto', 'Anmeldung', 'DAAD', __('Engineering'), 'Pharma'];
    $grandTotal = array_sum($totals);
@endphp

@section('content')
@if ($q === '')
    {{-- ─────────── LANDING (Google style) ─────────── --}}
    <div class="min-h-[calc(100vh-80px)] flex items-center justify-center px-4">
        <div class="w-full max-w-xl flex flex-col items-center">
            <div class="mb-6 text-center">
                <h1 class="text-5xl md:text-6xl font-bold tracking-tight">
                    <span class="text-primary-500">Almanya</span><span class="text-accent-500">Uni</span>
                </h1>
                <p class="text-gray-500 mt-2 text-sm">{{ __('University, city, program — all in a single search.') }}</p>
            </div>

            <form method="GET" action="{{ route('search.index') }}" class="w-full">
                <div class="relative group">
                    <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 group-focus-within:text-primary-500 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" name="q" value="{{ $q }}" autofocus autocomplete="off"
                        placeholder="{{ __('Search a university, city, program or topic...') }}"
                        class="w-full pl-12 pr-4 py-3 text-sm border border-gray-200 rounded-full shadow-sm hover:shadow-md focus:shadow-md focus:outline-none focus:border-primary-400 transition bg-white">
                </div>
            </form>

            <div class="mt-8 text-center w-full">
                <p class="text-xs uppercase tracking-wider text-gray-400 mb-2">{{ __('Popular searches') }}</p>
                <div class="flex flex-wrap items-center justify-center gap-2">
                    @foreach ($suggestions as $s)
                        <a href="{{ route('search.index', ['q' => $s]) }}"
                           class="inline-block bg-white border border-gray-200 hover:border-primary-400 hover:bg-primary-50 hover:text-primary-700 text-gray-700 text-xs px-3 py-1 rounded-full transition">
                            {{ $s }}
                        </a>
                    @endforeach
                </div>
            </div>

            <p class="text-xs text-gray-400 mt-8 text-center">
                {{ __('Typos are tolerated. You can type a city, university, program, or content (e.g. "Brandenburger Tor", "Sperrkonto").') }}
            </p>
        </div>
    </div>
@else
    {{-- ─────────── RESULTS ─────────── --}}
    <div class="border-b border-gray-200 bg-white sticky top-0 z-20">
        <div class="max-w-[1400px] mx-auto px-4 py-4 flex items-center gap-4">
            <a href="{{ route('search.index') }}" class="flex-shrink-0 text-2xl font-bold tracking-tight">
                <span class="text-primary-500">Almanya</span><span class="text-accent-500">Uni</span>
            </a>
            <form method="GET" action="{{ route('search.index') }}" class="flex-1 max-w-xl">
                <div class="relative">
                    <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" name="q" value="{{ $q }}"
                        class="w-full pl-11 pr-4 py-2.5 text-sm border border-gray-200 rounded-full shadow-sm focus:outline-none focus:border-primary-400 transition">
                </div>
            </form>
        </div>

        {{-- Section anchor pills --}}
        <div class="max-w-[1400px] mx-auto px-4 pb-3 flex items-center gap-2 overflow-x-auto text-xs">
            <span class="text-gray-500">{{ __(':n results', ['n' => number_format($grandTotal)]) }} @if ($took_ms !== null)({{ $took_ms }}ms)@endif:</span>
            @if ($totals['universities'])
                <a href="#unis" class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-700 whitespace-nowrap"><x-svg-icon name="academic-cap" class="w-3.5 h-3.5" /> {{ __('Universities') }} ({{ $totals['universities'] }})</a>
            @endif
            @if ($totals['cities'])
                <a href="#cities" class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-700 whitespace-nowrap"><x-svg-icon name="building-office" class="w-3.5 h-3.5" /> {{ __('Cities') }} ({{ $totals['cities'] }})</a>
            @endif
            @if ($totals['programs'])
                <a href="#programs" class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-700 whitespace-nowrap"><x-svg-icon name="book-open" class="w-3.5 h-3.5" /> {{ __('Programs') }} ({{ $totals['programs'] }})</a>
            @endif
            @if ($totals['professions'] ?? 0)
                <a href="#professions" class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-700 whitespace-nowrap"><x-svg-icon name="briefcase" class="w-3.5 h-3.5" /> {{ __('Professions') }} ({{ $totals['professions'] }})</a>
            @endif
            @if ($totals['scholarships'] ?? 0)
                <a href="#scholarships" class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-700 whitespace-nowrap"><x-svg-icon name="trophy" class="w-3.5 h-3.5" /> {{ __('Scholarships') }} ({{ $totals['scholarships'] }})</a>
            @endif
            @if ($totals['posts'] ?? 0)
                <a href="#posts" class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-700 whitespace-nowrap"><x-svg-icon name="document-text" class="w-3.5 h-3.5" /> {{ __('Blog') }} ({{ $totals['posts'] }})</a>
            @endif
            @if ($totals['fields'] ?? 0)
                <a href="#fields" class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-700 whitespace-nowrap"><x-svg-icon name="globe" class="w-3.5 h-3.5" /> {{ __('Fields') }} ({{ $totals['fields'] }})</a>
            @endif
            @if ($totals['studienkollegs'] ?? 0)
                <a href="#studienkollegs" class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-700 whitespace-nowrap"><x-svg-icon name="academic-cap" class="w-3.5 h-3.5" /> {{ __('Studienkolleg') }} ({{ $totals['studienkollegs'] }})</a>
            @endif
            @if ($totals['housing'] ?? 0)
                <a href="#housing" class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-700 whitespace-nowrap"><x-svg-icon name="home" class="w-3.5 h-3.5" /> {{ __('Housing') }} ({{ $totals['housing'] }})</a>
            @endif
            @if ($totals['sperrkonto'] ?? 0)
                <a href="#sperrkonto" class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-700 whitespace-nowrap"><x-svg-icon name="banknotes" class="w-3.5 h-3.5" /> {{ __('Sperrkonto') }} ({{ $totals['sperrkonto'] }})</a>
            @endif
        </div>
    </div>

    <div class="max-w-[1400px] mx-auto px-4 py-6">
        @if ($grandTotal === 0)
            <div class="py-12 px-6 text-center bg-white rounded-2xl border border-gray-200 shadow-sm max-w-2xl mx-auto">
                <div class="flex justify-center mb-4 text-gray-400"><x-svg-icon name="search" class="w-12 h-12" /></div>
                <p class="text-gray-900 text-lg md:text-xl font-semibold mb-2">{!! __('No results found for "<strong>:q</strong>"', ['q' => e($q)]) !!}</p>
                <p class="text-gray-500 text-sm mb-6">{{ __('Try a different word. Spelling, language, or abbreviations do not matter.') }}</p>

                {{-- Suggested actions --}}
                <div class="bg-primary-50 border border-primary-100 rounded-xl p-4 mb-5 text-left">
                    <p class="text-xs font-bold uppercase tracking-wider text-primary-700 mb-2 inline-flex items-center gap-1.5">
                        <x-svg-icon name="light-bulb" class="w-3.5 h-3.5" />
                        {{ __('Tips') }}
                    </p>
                    <ul class="text-sm text-gray-700 space-y-1 list-disc list-inside">
                        <li>{{ __('Shorter keywords often work better (e.g. "TUM" instead of "Technische Universität München")') }}</li>
                        <li>{{ __('Try the English or German name (e.g. "Munich" instead of "München")') }}</li>
                        <li>{{ __('Check our browsing pages below — they may have what you need') }}</li>
                    </ul>
                </div>

                {{-- Browse alternatives --}}
                <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                    @php
                        $browse = [
                            ['url' => route('universities.index'), 'icon' => 'academic-cap',     'label' => __('All Universities')],
                            ['url' => route('cities.index'),       'icon' => 'building-office',  'label' => __('Cities')],
                            ['url' => route('programs.index'),     'icon' => 'book-open',        'label' => __('Programs')],
                            ['url' => route('faqs.index'),         'icon' => 'information-circle','label' => __('FAQ')],
                        ];
                    @endphp
                    @foreach ($browse as $b)
                        <a href="{{ $b['url'] }}"
                           class="bg-white border border-gray-200 hover:border-primary-400 hover:bg-primary-50 rounded-lg p-3 transition text-sm font-medium text-gray-700">
                            <div class="flex justify-center mb-1 text-primary-600"><x-svg-icon name="{{ $b['icon'] }}" class="w-6 h-6" /></div>
                            {{ $b['label'] }}
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- ────────── CITIES ────────── --}}
        @if ($cities->isNotEmpty())
            <section id="cities" class="mb-10">
                <header class="flex items-baseline justify-between mb-4">
                    <h2 class="text-lg font-bold text-gray-900 inline-flex items-center gap-2">
                        <x-svg-icon name="building-office" class="w-5 h-5" />
                        {{ __('Cities') }} <span class="text-sm font-normal text-gray-500">({{ $totals['cities'] }})</span>
                    </h2>
                    @if ($totals['cities'] > $cities->count())
                        <a href="{{ route('cities.index', ['q' => $q]) }}" class="text-sm text-primary-600 hover:underline">{{ __('See all') }} →</a>
                    @endif
                </header>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                    @foreach ($cities as $city)
                        <a href="{{ route('cities.show', $city->slug) }}"
                           class="group bg-white rounded-xl overflow-hidden border border-gray-200 hover:border-primary-500 hover:shadow-md transition flex flex-col">
                            <div class="aspect-[4/3] overflow-hidden bg-gray-100">
                                @if ($city->image_url)
                                    <img src="{{ $city->image_url }}" alt="{{ $city->name }}" loading="lazy"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-blue-500 to-cyan-400 flex items-center justify-center">
                                        <span class="text-4xl font-extrabold text-white/90">{{ mb_substr($city->name, 0, 1) }}</span>
                                    </div>
                                @endif
                            </div>
                            <div class="p-3">
                                <h3 class="font-bold text-gray-900 group-hover:text-primary-600 truncate">{{ $city->name }}</h3>
                                <p class="text-xs text-gray-500 mt-0.5 truncate">
                                    @if ($city->state) {{ $city->state->name }} · @endif
                                    {{ $city->universities_count }} {{ __('uni') }}
                                </p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- ────────── UNIVERSITIES ────────── --}}
        @if ($universities->isNotEmpty())
            <section id="unis" class="mb-10">
                <header class="flex items-baseline justify-between mb-4">
                    <h2 class="text-lg font-bold text-gray-900 inline-flex items-center gap-2">
                        <x-svg-icon name="academic-cap" class="w-5 h-5" />
                        {{ __('Universities') }} <span class="text-sm font-normal text-gray-500">({{ $totals['universities'] }})</span>
                    </h2>
                    @if ($totals['universities'] > $universities->count())
                        <a href="{{ route('universities.index', ['q' => $q]) }}" class="text-sm text-primary-600 hover:underline">{{ __('See all') }} →</a>
                    @endif
                </header>
                <div class="space-y-3">
                    @foreach ($universities as $uni)
                        <a href="{{ route('universities.show', $uni->slug) }}"
                           class="group flex gap-3 bg-white rounded-lg border border-gray-200 hover:border-primary-500 hover:shadow-md transition p-3">
                            @if ($uni->image_url)
                                <div class="w-20 h-20 rounded-lg overflow-hidden bg-gray-100 shrink-0">
                                    <img src="{{ $uni->image_url }}" alt="" class="w-full h-full object-cover" loading="lazy" decoding="async">
                                </div>
                            @elseif ($uni->logo_url)
                                <div class="w-20 h-20 bg-white rounded-lg ring-1 ring-gray-200 p-2 flex items-center justify-center shrink-0">
                                    <img src="{{ $uni->logo_url }}" alt="" class="max-w-full max-h-full object-contain" loading="lazy" decoding="async">
                                </div>
                            @else
                                <div class="w-20 h-20 rounded-lg bg-gradient-to-br from-primary-500 to-accent-500 flex items-center justify-center shrink-0">
                                    <span class="text-2xl font-extrabold text-white">{{ mb_substr($uni->name, 0, 2) }}</span>
                                </div>
                            @endif
                            <div class="flex-1 min-w-0">
                                <h3 class="font-bold text-gray-900 group-hover:text-primary-600 leading-snug">{{ $uni->display_name }}</h3>
                                <p class="text-xs text-gray-500 mt-1">
                                    <span class="inline-flex items-center gap-1"><x-svg-icon name="map-pin" class="w-3.5 h-3.5" /> {{ $uni->city?->name ?? '—' }}@if ($uni->city?->state?->name) · {{ $uni->city->state->name }}@endif</span>
                                </p>
                                <div class="flex flex-wrap gap-2 mt-2 text-xs">
                                    @if ($uni->type)
                                        <span class="inline-block px-2 py-0.5 rounded bg-primary-50 text-primary-700 font-medium">{{ $typeLabel($uni->type) }}</span>
                                    @endif
                                    @if ($uni->student_count)
                                        <span class="inline-flex items-center gap-1 text-gray-600"><x-svg-icon name="users" class="w-3.5 h-3.5" /> {{ number_format($uni->student_count) }}</span>
                                    @endif
                                    @if ($uni->founded_year)
                                        <span class="inline-flex items-center gap-1 text-gray-600"><x-svg-icon name="calendar" class="w-3.5 h-3.5" /> {{ $uni->founded_year }}</span>
                                    @endif
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- ────────── PROGRAMS ────────── --}}
        @if ($programs->isNotEmpty())
            <section id="programs" class="mb-10">
                <header class="flex items-baseline justify-between mb-4">
                    <h2 class="text-lg font-bold text-gray-900 inline-flex items-center gap-2">
                        <x-svg-icon name="book-open" class="w-5 h-5" />
                        {{ __('Programs') }} <span class="text-sm font-normal text-gray-500">({{ $totals['programs'] }})</span>
                    </h2>
                    @if ($totals['programs'] > $programs->count())
                        <a href="{{ route('programs.index', ['q' => $q]) }}" class="text-sm text-primary-600 hover:underline">{{ __('See all') }} →</a>
                    @endif
                </header>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach ($programs as $p)
                        <a href="{{ route('programs.show', $p->slug) }}"
                           class="group bg-white rounded-lg border border-gray-200 hover:border-primary-500 hover:shadow-md transition p-4 flex flex-col">
                            <div class="flex items-start justify-between gap-2 mb-2">
                                <h3 class="font-semibold text-gray-900 group-hover:text-primary-600 leading-snug">{{ $p->name }}</h3>
                                @if ($p->language)
                                    <span class="text-xs font-semibold px-2 py-0.5 rounded-full whitespace-nowrap
                                        @if ($p->language === 'en') bg-blue-100 text-blue-700
                                        @elseif ($p->language === 'de') bg-green-100 text-green-700
                                        @elseif ($p->language === 'both') bg-purple-100 text-purple-700
                                        @else bg-gray-100 text-gray-600 @endif">
                                        {{ $p->language === 'both' ? 'DE+EN' : strtoupper($p->language) }}
                                    </span>
                                @endif
                            </div>
                            <p class="text-xs text-gray-500 mb-2">
                                {{ $degreeLabel($p->degree) }}@if ($p->university) · {{ $p->university->display_name }}@endif
                            </p>
                            @if ($p->field)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-white text-xs w-fit"
                                      style="background-color: {{ $p->field->color }}">
                                    {{ $p->field->icon }} {{ $p->field->name_tr }}
                                </span>
                            @endif
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- ────────── PROFESSIONS ────────── --}}
        @if (($professions ?? collect())->isNotEmpty())
            @php
                $profTypeLabels = [
                    'ausbildung' => 'Ausbildung',
                    'studienberuf' => 'Studienberuf',
                    'weiterbildung' => 'Weiterbildung',
                    'grundberuf' => 'Grundberuf',
                ];
            @endphp
            <section id="professions" class="mb-10">
                <header class="flex items-baseline justify-between mb-4">
                    <h2 class="text-lg font-bold text-gray-900 inline-flex items-center gap-2">
                        <x-svg-icon name="briefcase" class="w-5 h-5" />
                        {{ __('Professions') }} <span class="text-sm font-normal text-gray-500">({{ $totals['professions'] }})</span>
                    </h2>
                    @if ($totals['professions'] > $professions->count())
                        <a href="{{ route('professions.index', ['q' => $q]) }}" class="text-sm text-primary-600 hover:underline">{{ __('See all') }} →</a>
                    @endif
                </header>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach ($professions as $prof)
                        <a href="{{ route('professions.show', $prof->slug) }}"
                           class="group bg-white rounded-lg border border-gray-200 hover:border-primary-500 hover:shadow-md transition p-4 flex flex-col">
                            <div class="flex items-start gap-2 mb-1">
                                @if ($prof->field?->icon)
                                    <span class="text-lg shrink-0">{{ $prof->field->icon }}</span>
                                @endif
                                <h3 class="font-semibold text-gray-900 group-hover:text-primary-600 leading-snug flex-1">
                                    {{ $prof->name_tr ?: $prof->name }}
                                </h3>
                            </div>
                            @if ($prof->name_tr && $prof->name_tr !== $prof->name)
                                <p class="text-xs text-gray-500 italic">{{ $prof->name }}</p>
                            @endif
                            <div class="flex flex-wrap gap-2 mt-2 text-xs">
                                @if ($prof->type && isset($profTypeLabels[$prof->type]))
                                    <span class="px-2 py-0.5 rounded bg-blue-50 text-blue-700">{{ $profTypeLabels[$prof->type] }}</span>
                                @endif
                                @if ($prof->kldb_code)
                                    <span class="text-gray-500 font-mono">KldB {{ $prof->kldb_code }}</span>
                                @endif
                                @if ($prof->field?->name_tr)
                                    <span class="text-gray-500">{{ $prof->field->name_tr }}</span>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- ────────── SCHOLARSHIPS ────────── --}}
        @if (($scholarships ?? collect())->isNotEmpty())
            <section id="scholarships" class="mb-10">
                <header class="flex items-baseline justify-between mb-4">
                    <h2 class="text-lg font-bold text-gray-900 inline-flex items-center gap-2">
                        <x-svg-icon name="trophy" class="w-5 h-5" />
                        {{ __('Scholarships') }} <span class="text-sm font-normal text-gray-500">({{ $totals['scholarships'] }})</span>
                    </h2>
                    @if ($totals['scholarships'] > $scholarships->count())
                        <a href="{{ route('scholarships.index', ['q' => $q]) }}" class="text-sm text-primary-600 hover:underline">{{ __('See all') }} →</a>
                    @endif
                </header>
                <div class="space-y-3">
                    @foreach ($scholarships as $s)
                        <a href="{{ route('scholarships.show', $s->slug) }}"
                           class="group block bg-white rounded-lg border border-gray-200 hover:border-primary-500 hover:shadow-md transition p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex-1">
                                    <h3 class="font-semibold text-gray-900 group-hover:text-primary-600 leading-snug">
                                        {{ $s->name }}
                                    </h3>
                                    @if ($s->programmname)
                                        <p class="text-sm text-gray-600 mt-1 line-clamp-2">{{ \Illuminate\Support\Str::limit($s->programmname, 150) }}</p>
                                    @endif
                                </div>
                                @if ($s->is_daad)
                                    <span class="shrink-0 px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-xs font-semibold">DAAD</span>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- ────────── BLOG POSTS ────────── --}}
        @if (($posts ?? collect())->isNotEmpty())
            <section id="posts" class="mb-10">
                <header class="flex items-baseline justify-between mb-4">
                    <h2 class="text-lg font-bold text-gray-900 inline-flex items-center gap-2">
                        <x-svg-icon name="document-text" class="w-5 h-5" />
                        {{ __('Blog posts') }} <span class="text-sm font-normal text-gray-500">({{ $totals['posts'] }})</span>
                    </h2>
                    @if ($totals['posts'] > $posts->count())
                        <a href="{{ route('blog.index') }}" class="text-sm text-primary-600 hover:underline">{{ __('Blog') }} →</a>
                    @endif
                </header>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach ($posts as $post)
                        <a href="{{ route('blog.show', $post->slug) }}"
                           class="group bg-white rounded-lg border border-gray-200 hover:border-primary-500 hover:shadow-md transition p-4 flex flex-col">
                            <div class="flex items-baseline justify-between gap-2 mb-1">
                                @if ($post->category)
                                    <span class="text-xs font-semibold uppercase tracking-wide" style="color: {{ $post->category->color ?? '#1E40AF' }}">
                                        {{ __($post->category->name) }}
                                    </span>
                                @endif
                                <span class="text-xs text-gray-500">{{ $post->reading_minutes }} {{ __('min') }}</span>
                            </div>
                            <h3 class="font-semibold text-gray-900 group-hover:text-primary-600 leading-snug mb-2">{{ $post->title }}</h3>
                            @if ($post->excerpt)
                                <p class="text-sm text-gray-600 line-clamp-2 flex-1">{{ \Illuminate\Support\Str::limit($post->excerpt, 140) }}</p>
                            @endif
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- ────────── FIELDS (Eğitim Alanları) ────────── --}}
        @if (($fields ?? collect())->isNotEmpty())
            <section id="fields" class="mb-10">
                <header class="flex items-baseline justify-between mb-4">
                    <h2 class="text-lg font-bold text-gray-900 inline-flex items-center gap-2">
                        <x-svg-icon name="globe" class="w-5 h-5" />
                        {{ __('Study Fields') }} <span class="text-sm font-normal text-gray-500">({{ $totals['fields'] }})</span>
                    </h2>
                    <a href="{{ route('fields.index') }}" class="text-sm text-primary-600 hover:underline">{{ __('All') }} →</a>
                </header>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                    @foreach ($fields as $field)
                        <a href="{{ route('fields.show', $field->slug) }}"
                           class="group bg-white rounded-lg border border-gray-200 hover:border-primary-500 hover:shadow-md transition p-3 flex items-center gap-3">
                            <span class="text-2xl">{{ $field->icon ?: '' }}</span>
                            <div class="flex-1 min-w-0">
                                <h3 class="font-semibold text-gray-900 group-hover:text-primary-600 truncate">{{ $field->name }}</h3>
                                <p class="text-xs text-gray-500 truncate">{{ $field->name }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        @if (($studienkollegs ?? collect())->isNotEmpty())
            <section id="studienkollegs" class="mb-10">
                <header class="flex items-baseline justify-between mb-4">
                    <h2 class="text-lg font-bold text-gray-900 inline-flex items-center gap-2">
                        <x-svg-icon name="academic-cap" class="w-5 h-5" />
                        {{ __('Studienkolleg') }} <span class="text-sm font-normal text-gray-500">({{ $totals['studienkollegs'] }})</span>
                    </h2>
                    <a href="{{ route('tools.studienkolleg') }}" class="text-sm text-primary-600 hover:underline">{{ __('All') }} →</a>
                </header>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach ($studienkollegs as $sk)
                        <a href="{{ route('tools.studienkolleg') }}#{{ $sk->slug }}"
                           class="group bg-white rounded-lg border border-gray-200 hover:border-violet-500 hover:shadow-md transition p-3 flex items-start gap-3">
                            <span class="text-2xl text-primary-600"><x-svg-icon name="academic-cap" class="w-6 h-6" /></span>
                            <div class="flex-1 min-w-0">
                                <h3 class="font-semibold text-gray-900 group-hover:text-violet-600">{{ $sk->name }}</h3>
                                <p class="text-xs text-gray-500">{{ $sk->city_name_cache }} · {{ $sk->type === 'privat' ? __('Private') : __('Public (free)') }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        @if (($housing ?? collect())->isNotEmpty())
            <section id="housing" class="mb-10">
                <header class="flex items-baseline justify-between mb-4">
                    <h2 class="text-lg font-bold text-gray-900 inline-flex items-center gap-2">
                        <x-svg-icon name="home" class="w-5 h-5" />
                        {{ __('Housing Providers') }} <span class="text-sm font-normal text-gray-500">({{ $totals['housing'] }})</span>
                    </h2>
                    <a href="{{ route('housing.providers') }}" class="text-sm text-primary-600 hover:underline">{{ __('All') }} →</a>
                </header>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach ($housing as $hp)
                        <a href="{{ url('/housing/providers/' . $hp->slug) }}"
                           class="group bg-white rounded-lg border border-gray-200 hover:border-emerald-500 hover:shadow-md transition p-3 flex items-start gap-3">
                            @if ($hp->logo_url)
                                <img src="{{ $hp->logo_url }}" alt="" class="w-10 h-10 object-contain rounded">
                            @else
                                <span class="text-2xl text-primary-600"><x-svg-icon name="home" class="w-6 h-6" /></span>
                            @endif
                            <div class="flex-1 min-w-0">
                                <h3 class="font-semibold text-gray-900 group-hover:text-emerald-600">{{ $hp->name }}</h3>
                                <p class="text-xs text-gray-500">{{ $hp->type }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        @if (($sperrkonto ?? collect())->isNotEmpty())
            <section id="sperrkonto" class="mb-10">
                <header class="flex items-baseline justify-between mb-4">
                    <h2 class="text-lg font-bold text-gray-900 inline-flex items-center gap-2">
                        <x-svg-icon name="banknotes" class="w-5 h-5" />
                        {{ __('Blocked Account (Sperrkonto)') }} <span class="text-sm font-normal text-gray-500">({{ $totals['sperrkonto'] }})</span>
                    </h2>
                    <a href="{{ route('tools.blocked-account') }}" class="text-sm text-primary-600 hover:underline">{{ __('All') }} →</a>
                </header>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach ($sperrkonto as $ba)
                        <a href="{{ url('/tools/sperrkonto/' . $ba->slug) }}"
                           class="group bg-white rounded-lg border border-gray-200 hover:border-blue-500 hover:shadow-md transition p-3 flex items-start gap-3">
                            @if ($ba->logo_url)
                                <img src="{{ $ba->logo_url }}" alt="" class="w-10 h-10 object-contain rounded">
                            @else
                                <span class="text-2xl text-primary-600"><x-svg-icon name="banknotes" class="w-6 h-6" /></span>
                            @endif
                            <div class="flex-1 min-w-0">
                                <h3 class="font-semibold text-gray-900 group-hover:text-blue-600">{{ $ba->name }}</h3>
                                <p class="text-xs text-gray-500">{{ $ba->type === 'fintech' ? 'FinTech' : __('Traditional bank') }}@if ($ba->yearly_fee_eur) · {{ number_format($ba->yearly_fee_eur, 0) }} EUR{{ __('/yr') }}@endif</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif
    </div>
@endif
@endsection
