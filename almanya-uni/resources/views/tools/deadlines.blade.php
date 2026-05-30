@extends('layouts.app')

@section('title', __('Application Calendar — Upcoming Deadlines') . ' — ' . brand('name'))

<x-seo
    :title="__('Application Calendar — German University Deadlines')"
    :description="__('Filter upcoming application dates, add to your calendar. 7,000+ German university programs, winter and summer terms.')"
/>

<x-tool-schema tool="deadlines" />

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
        <h1 class="text-3xl md:text-5xl font-extrabold leading-tight drop-shadow mb-3 inline-flex items-center gap-3">
            <x-svg-icon name="calendar" class="w-8 h-8 md:w-10 md:h-10" />
            {{ __('Application Calendar') }}
        </h1>
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
                    <option value="winter" @selected($filters['semester'] === 'winter')>{{ __('Winter') }}</option>
                    <option value="summer" @selected($filters['semester'] === 'summer')>{{ __('Summer') }}</option>
                </select>
                <button class="px-5 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm transition">{{ __('Search') }}</button>
            </div>

            {{-- Quick window chips --}}
            <div class="flex items-center flex-wrap gap-2 pt-2 border-t border-gray-100">
                <span class="text-xs text-gray-500 mr-1">{{ __('Time:') }}</span>
                @php
                    $windows = [
                        'next-30d'  => ['fire',           __('Next 30 days')],
                        'next-90d'  => ['calendar',       __('3 months')],
                        'next-6mo'  => ['calendar-days',  __('6 months')],
                        'next-year' => ['calendar-days',  __('1 year')],
                    ];
                @endphp
                @foreach ($windows as $val => [$ico, $lbl])
                    <a href="{{ request()->fullUrlWithQuery(['window' => $val]) }}"
                       class="inline-flex items-center gap-1 text-xs px-3 py-1.5 rounded-full border transition
                              {{ ($filters['window'] ?? 'next-90d') === $val
                                 ? 'bg-indigo-600 text-white border-indigo-600'
                                 : 'bg-indigo-50 text-indigo-700 border-indigo-200 hover:bg-indigo-100' }}">
                        <x-svg-icon name="{{ $ico }}" class="w-3.5 h-3.5" />
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

{{-- RESULTS — grouped by urgency window --}}
<div class="max-w-[1400px] mx-auto px-4 py-8">
    @if ($programs->isEmpty())
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-8 text-center">
            <div class="flex justify-center mb-3 text-gray-400"><x-svg-icon name="search" class="w-12 h-12" /></div>
            <p class="text-yellow-900 font-semibold mb-2">{{ __('No programs found with these filters.') }}</p>
            <a href="{{ route('tools.deadlines') }}" class="text-primary-600 hover:underline">{{ __('Reset filters') }} →</a>
        </div>
    @else
        @php
            // Group programs by urgency bucket. Each item carries its resolved [sem, deadline, daysLeft].
            $buckets = [
                'urgent'  => ['label' => __('Next 30 days'),     'icon' => 'fire',           'border' => 'border-red-300',    'badge' => 'bg-red-100 text-red-800',       'items' => []],
                'soon'    => ['label' => __('1–3 months'),       'icon' => 'calendar',       'border' => 'border-amber-300',  'badge' => 'bg-amber-100 text-amber-800',   'items' => []],
                'planned' => ['label' => __('3–6 months'),       'icon' => 'calendar-days',  'border' => 'border-blue-300',   'badge' => 'bg-blue-100 text-blue-800',     'items' => []],
                'future'  => ['label' => __('Later'),            'icon' => 'calendar-days',  'border' => 'border-gray-300',   'badge' => 'bg-gray-100 text-gray-700',     'items' => []],
            ];
            foreach ($programs as $p) {
                [$sem, $deadline] = $showSemester($p);
                if (! $deadline) continue;
                $daysLeft = (int) $today->diffInDays($deadline, false);
                $key = match (true) {
                    $daysLeft < 30  => 'urgent',
                    $daysLeft < 90  => 'soon',
                    $daysLeft < 180 => 'planned',
                    default         => 'future',
                };
                $buckets[$key]['items'][] = compact('p', 'sem', 'deadline', 'daysLeft');
            }
        @endphp

        <div class="space-y-8">
            @foreach ($buckets as $key => $bucket)
                @continue(empty($bucket['items']))
                <section>
                    <header class="flex items-baseline justify-between gap-3 mb-3 pb-2 border-b-2 {{ $bucket['border'] }}">
                        <h2 class="text-base md:text-lg font-bold text-gray-900 inline-flex items-center gap-2">
                            <x-svg-icon name="{{ $bucket['icon'] }}" class="w-5 h-5" />
                            {{ $bucket['label'] }}
                        </h2>
                        <span class="text-xs md:text-sm text-gray-500 font-semibold">
                            {{ trans_choice('{1} :n program|[2,*] :n programs', count($bucket['items']), ['n' => number_format(count($bucket['items']))]) }}
                        </span>
                    </header>

                    <ul class="divide-y divide-gray-100 border border-gray-200 rounded-xl overflow-hidden bg-white">
                        @foreach ($bucket['items'] as $row)
                            @php $p = $row['p']; $sem = $row['sem']; $deadline = $row['deadline']; $daysLeft = $row['daysLeft']; @endphp
                            <li class="hover:bg-gray-50 transition">
                                <div class="grid grid-cols-12 items-center gap-3 px-3 md:px-4 py-3">
                                    {{-- Date + days left --}}
                                    <div class="col-span-4 md:col-span-2 flex md:flex-col items-baseline md:items-start gap-2 md:gap-0.5">
                                        <span class="text-sm md:text-base font-bold text-gray-900 tabular-nums">{{ $deadline->format('d.m.Y') }}</span>
                                        <span class="inline-flex text-[11px] font-bold px-1.5 py-0.5 rounded {{ $bucket['badge'] }} tabular-nums">
                                            {{ __(':n days', ['n' => $daysLeft]) }}
                                        </span>
                                    </div>

                                    {{-- Semester --}}
                                    <div class="hidden md:block col-span-1">
                                        <span class="inline-flex items-center text-xs text-gray-600 font-medium" title="{{ $sem === 'winter' ? __('Winter') : __('Summer') }}">
                                            <x-svg-icon name="{{ $sem === 'winter' ? 'sparkles' : 'sparkles' }}" class="w-4 h-4" />
                                        </span>
                                    </div>

                                    {{-- Program + uni --}}
                                    <div class="col-span-8 md:col-span-6 min-w-0">
                                        <a href="{{ route('programs.show', $p->slug) }}"
                                           class="block font-semibold text-gray-900 hover:text-indigo-600 leading-tight truncate">
                                            {{ $p->name_de }}
                                        </a>
                                        <a href="{{ route('universities.show', $p->university->slug) }}"
                                           class="flex items-center gap-1.5 text-xs text-gray-500 hover:text-indigo-600 truncate mt-0.5">
                                            @if ($p->university?->logo_url)
                                                <img src="{{ $p->university->logo_url }}" alt="" class="w-4 h-4 object-contain shrink-0" loading="lazy" decoding="async">
                                            @endif
                                            <span class="truncate">{{ $p->university?->name_de }}</span>
                                        </a>
                                    </div>

                                    {{-- Meta chips --}}
                                    <div class="col-span-12 md:col-span-2 flex flex-wrap items-center gap-1 text-[11px] md:justify-end">
                                        <span class="px-1.5 py-0.5 rounded bg-gray-100 text-gray-700 font-medium">{{ $degreeLabel($p->degree) }}</span>
                                        @if ($p->language)
                                            <span class="px-1.5 py-0.5 rounded bg-gray-100 text-gray-700 font-medium" title="{{ $langLabel($p->language) }}">
                                                {{ $p->language === 'both' ? '🇬🇧🇩🇪' : ($p->language === 'en' ? '🇬🇧' : '🇩🇪') }}
                                            </span>
                                        @endif
                                        @if ($p->field)
                                            <span class="px-1.5 py-0.5 rounded font-medium truncate max-w-[120px]"
                                                  style="background-color: {{ $p->field->color }}20; color: {{ $p->field->color }}"
                                                  title="{{ $p->field->name_tr }}">
                                                {{ $p->field->icon }}
                                            </span>
                                        @endif
                                    </div>

                                    {{-- .ics download --}}
                                    <div class="col-span-12 md:col-span-1 flex md:justify-end">
                                        <a href="{{ route('tools.deadlines.ics', ['slugs' => $p->slug, 'semester' => $sem]) }}"
                                           class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 transition"
                                           title="{{ __('Add to my calendar (.ics)') }}"
                                           aria-label="{{ __('Add to my calendar (.ics)') }}">
                                            <x-svg-icon name="calendar" class="w-4 h-4" />
                                        </a>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </section>
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
        <h3 class="font-bold text-gray-900 mb-2 inline-flex items-center gap-2"><x-svg-icon name="light-bulb" class="w-5 h-5" /> {{ __('Application Periods in Germany') }}</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-700">
            <div>
                <p class="font-semibold mb-1 inline-flex items-center gap-1.5"><x-svg-icon name="sparkles" class="w-4 h-4" /> Wintersemester ({{ __('Winter') }})</p>
                <ul class="list-disc list-inside space-y-1 text-xs">
                    <li>{{ __('Starts in October, runs through February') }}</li>
                    <li>{!! __('Most unis: <strong>July 15</strong> deadline') !!}</li>
                    <li>{{ __('NC programs via Uni-Assist') }}</li>
                    <li>{{ __('Plan 6-9 months ahead from your home country') }}</li>
                </ul>
            </div>
            <div>
                <p class="font-semibold mb-1 inline-flex items-center gap-1.5"><x-svg-icon name="sparkles" class="w-4 h-4" /> Sommersemester ({{ __('Summer') }})</p>
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

{{-- Auto-FAQ (AIO + Featured Snippet) --}}
<x-faq-section
    :title="__('Frequently Asked Questions about German Application Deadlines')"
    :subtitle="__('Winter vs. summer intake, late deadlines, and iCal calendar export')"
    :faqs="\App\Support\PageFaq::forDeadlines()"
/>
@endsection
