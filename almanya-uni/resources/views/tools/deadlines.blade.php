@extends('layouts.app')

@section('title', __('Application Calendar — Upcoming Deadlines') . ' — ' . brand('name'))

<x-seo
    :title="__('Application Calendar — German University Deadlines')"
    :description="__('Filter upcoming application dates, add to your calendar. 7,000+ German university programs, winter and summer terms.')"
/>

@php
    $degreeLabel = fn ($d) => match ($d) {
        'bachelor' => 'Bachelor',
        'master'   => 'Master',
        'phd'      => 'PhD',
        'staatsexamen' => 'Staatsexamen',
        default    => ucfirst((string) $d),
    };
    $langLabel = fn ($l) => match ($l) {
        'en'   => '🇬🇧 ' . __('English'),
        'de'   => '🇩🇪 ' . __('German'),
        'both' => '🇬🇧🇩🇪 ' . __('Both'),
        default => '—',
    };

    // Hangi semester'ı göstereceğiz (filter'a göre)?
    $showSemester = function ($p) use ($filters) {
        $w = $p->application_deadline_winter;
        $s = $p->application_deadline_summer;
        if ($filters['semester'] === 'winter') return ['winter', $w];
        if ($filters['semester'] === 'summer') return ['summer', $s];
        // Auto: en yakın olanı seç
        if ($w && $s) return $w->lt($s) ? ['winter', $w] : ['summer', $s];
        if ($w) return ['winter', $w];
        return ['summer', $s];
    };
@endphp

@section('content')

{{-- HERO --}}
<section class="bg-gradient-to-br from-indigo-700 via-indigo-600 to-violet-500 text-white">
    <div class="max-w-[1400px] mx-auto px-4 py-10 md:py-14">
        <nav class="text-sm text-indigo-100 mb-3">
            <a href="/" class="hover:text-white">{{ __('Home') }}</a>
            <span class="mx-2 opacity-50">›</span>
            <a href="{{ route('tools.index') }}" class="hover:text-white">{{ __('Tools') }}</a>
            <span class="mx-2 opacity-50">›</span>
            <span class="text-white">{{ __('Application Calendar') }}</span>
        </nav>
        <h1 class="text-3xl md:text-5xl font-extrabold leading-tight drop-shadow mb-3">📅 {{ __('Application Calendar') }}</h1>
        <p class="text-lg md:text-xl text-indigo-100 max-w-3xl">
            {{ __('Filter upcoming application deadlines and add them to your calendar. Winter (Wintersemester) September-July, Summer (Sommersemester) January-March.') }}
        </p>
    </div>
</section>

{{-- FILTER BAR --}}
<section class="bg-white border-b border-gray-200 shadow-sm sticky top-0 z-10">
    <div class="max-w-[1400px] mx-auto px-4 py-3">
        <form method="GET" action="{{ route('tools.deadlines') }}" class="space-y-2">
            <div class="grid grid-cols-1 md:grid-cols-[1fr_140px_160px_160px_140px_auto] gap-2">
                <input type="text" name="q" value="{{ $filters['q'] }}"
                       placeholder="{{ __('Search program or uni...') }}"
                       class="px-3 py-2 rounded-lg border border-gray-300 text-sm">
                <select name="degree" onchange="this.form.submit()" class="px-3 py-2 rounded-lg border border-gray-300 text-sm bg-white">
                    <option value="">{{ __('All degrees') }}</option>
                    <option value="bachelor" @selected($filters['degree'] === 'bachelor')>Bachelor</option>
                    <option value="master" @selected($filters['degree'] === 'master')>Master</option>
                    <option value="phd" @selected($filters['degree'] === 'phd')>PhD</option>
                </select>
                <select name="language" onchange="this.form.submit()" class="px-3 py-2 rounded-lg border border-gray-300 text-sm bg-white">
                    <option value="">{{ __('All languages') }}</option>
                    <option value="en" @selected($filters['language'] === 'en')>🇬🇧 {{ __('English') }}</option>
                    <option value="de" @selected($filters['language'] === 'de')>🇩🇪 {{ __('German') }}</option>
                    <option value="both" @selected($filters['language'] === 'both')>🇬🇧🇩🇪 {{ __('Both') }}</option>
                </select>
                <select name="field" onchange="this.form.submit()" class="px-3 py-2 rounded-lg border border-gray-300 text-sm bg-white">
                    <option value="">{{ __('All fields') }}</option>
                    @foreach ($fields as $f)
                        <option value="{{ $f->slug }}" @selected($filters['field'] === $f->slug)>{{ $f->icon }} {{ $f->name }}</option>
                    @endforeach
                </select>
                <select name="semester" onchange="this.form.submit()" class="px-3 py-2 rounded-lg border border-gray-300 text-sm bg-white">
                    <option value="">{{ __('All semesters') }}</option>
                    <option value="winter" @selected($filters['semester'] === 'winter')>❄️ {{ __('Winter') }}</option>
                    <option value="summer" @selected($filters['semester'] === 'summer')>☀️ {{ __('Summer') }}</option>
                </select>
                <button class="px-5 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm transition">{{ __('Search') }}</button>
            </div>

            {{-- Quick window chips --}}
            <div class="flex items-center flex-wrap gap-2 pt-2 border-t border-gray-100">
                <span class="text-xs text-gray-500 mr-1">{{ __('Time:') }}</span>
                @php
                    $windows = [
                        'next-30d'  => '🔥 ' . __('Next 30 days'),
                        'next-90d'  => '📅 ' . __('3 months'),
                        'next-6mo'  => '📆 ' . __('6 months'),
                        'next-year' => '🗓️ ' . __('1 year'),
                    ];
                @endphp
                @foreach ($windows as $val => $lbl)
                    <a href="{{ request()->fullUrlWithQuery(['window' => $val]) }}"
                       class="text-xs px-3 py-1.5 rounded-full border transition
                              {{ ($filters['window'] ?? 'next-90d') === $val
                                 ? 'bg-indigo-600 text-white border-indigo-600'
                                 : 'bg-indigo-50 text-indigo-700 border-indigo-200 hover:bg-indigo-100' }}">
                        {{ $lbl }}
                    </a>
                @endforeach

                @if(array_filter($filters))
                    <a href="{{ route('tools.deadlines') }}" class="ml-2 text-xs text-accent-600 hover:text-accent-800 underline">↻ {{ __('Reset') }}</a>
                @endif

                <span class="ml-auto text-xs text-gray-500">
                    <strong>{{ number_format($programs->total(), 0, ',', '.') }}</strong> {{ __('programs') }}
                </span>
            </div>
        </form>
    </div>
</section>

{{-- RESULTS --}}
<div class="max-w-[1400px] mx-auto px-4 py-8">
    @if ($programs->isEmpty())
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-8 text-center">
            <div class="text-5xl mb-3">🔍</div>
            <p class="text-yellow-900 font-semibold mb-2">{{ __('No programs found with these filters.') }}</p>
            <a href="{{ route('tools.deadlines') }}" class="text-primary-600 hover:underline">{{ __('Reset filters') }} →</a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
            @foreach ($programs as $p)
                @php
                    [$sem, $deadline] = $showSemester($p);
                    if (!$deadline) continue;
                    $daysLeft = (int) $today->diffInDays($deadline, false);
                    $urgencyClass = match (true) {
                        $daysLeft < 14 => 'bg-red-50 border-red-200 text-red-700',
                        $daysLeft < 30 => 'bg-amber-50 border-amber-200 text-amber-700',
                        $daysLeft < 90 => 'bg-blue-50 border-blue-200 text-blue-700',
                        default        => 'bg-gray-50 border-gray-200 text-gray-700',
                    };
                @endphp
                <article class="bg-white border border-gray-200 hover:border-indigo-400 hover:shadow-md transition rounded-xl p-4 flex flex-col">
                    <div class="flex items-start justify-between gap-2 mb-2">
                        <div class="inline-flex items-center gap-1 text-xs px-2 py-1 rounded-full {{ $urgencyClass }} font-semibold">
                            {{ $sem === 'winter' ? '❄️ ' . __('Winter') : '☀️ ' . __('Summer') }}
                            ·
                            {{ $deadline->format('d.m.Y') }}
                        </div>
                        <span class="text-xs font-bold {{ $daysLeft < 30 ? 'text-red-600' : 'text-gray-500' }}">
                            {{ __(':n days', ['n' => $daysLeft]) }}
                        </span>
                    </div>

                    <a href="{{ route('programs.show', $p->slug) }}"
                       class="font-semibold text-gray-900 hover:text-indigo-600 leading-tight mb-1 line-clamp-2">
                        {{ $p->name_de }}
                    </a>

                    <div class="flex items-center gap-2 text-xs text-gray-600 mt-1">
                        @if ($p->university?->logo_url)
                            <img src="{{ $p->university->logo_url }}" alt="" class="w-5 h-5 object-contain shrink-0" loading="lazy" decoding="async">
                        @endif
                        <a href="{{ route('universities.show', $p->university->slug) }}" class="hover:text-indigo-600 truncate">{{ $p->university?->name_de }}</a>
                    </div>

                    <div class="flex flex-wrap gap-1.5 mt-2 text-xs">
                        <span class="px-1.5 py-0.5 rounded bg-gray-100 text-gray-700">{{ $degreeLabel($p->degree) }}</span>
                        <span class="px-1.5 py-0.5 rounded bg-gray-100 text-gray-700">{{ $langLabel($p->language) }}</span>
                        @if ($p->field)
                            <span class="px-1.5 py-0.5 rounded text-xs" style="background-color: {{ $p->field->color }}20; color: {{ $p->field->color }}">{{ $p->field->icon }} {{ $p->field->name_tr }}</span>
                        @endif
                    </div>

                    <a href="{{ route('tools.deadlines.ics', ['slugs' => $p->slug, 'semester' => $sem]) }}"
                       class="mt-3 inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-semibold transition">
                        📥 {{ __('Add to my calendar (.ics)') }}
                    </a>
                </article>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $programs->links() }}
        </div>
    @endif
</div>

{{-- BİLGİ BANDI --}}
<section class="max-w-[1400px] mx-auto px-4 pb-12">
    <div class="bg-gradient-to-br from-indigo-50 to-violet-50 border border-indigo-200 rounded-xl p-6">
        <h3 class="font-bold text-gray-900 mb-2">💡 {{ __('Application Periods in Germany') }}</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-700">
            <div>
                <p class="font-semibold mb-1">❄️ Wintersemester ({{ __('Winter') }})</p>
                <ul class="list-disc list-inside space-y-1 text-xs">
                    <li>{{ __('Starts in October, runs through February') }}</li>
                    <li>{!! __('Most unis: <strong>July 15</strong> deadline') !!}</li>
                    <li>{{ __('NC programs via Uni-Assist') }}</li>
                    <li>{{ __('Plan 6-9 months ahead from your home country') }}</li>
                </ul>
            </div>
            <div>
                <p class="font-semibold mb-1">☀️ Sommersemester ({{ __('Summer') }})</p>
                <ul class="list-disc list-inside space-y-1 text-xs">
                    <li>{{ __('Starts in April, runs through September') }}</li>
                    <li>{!! __('Most unis: <strong>January 15</strong> deadline') !!}</li>
                    <li>{{ __('Fewer programs open (mostly Master)') }}</li>
                    <li>{{ __('Limited options for Bachelor') }}</li>
                </ul>
            </div>
        </div>
        <p class="text-xs text-gray-500 mt-4">
            {{ __('⚠️ Deadlines may vary by uni and program. Verify on the uni\'s official site.') }}
            <a href="{{ route('blog.show', 'uni-assist-basvuru-rehberi-a-z-almanya-universite-hayalinize-adim-adim-ulasin') }}" class="text-indigo-600 hover:underline">{{ __('Uni-Assist guide') }} →</a>
        </p>
    </div>
</section>
@endsection
