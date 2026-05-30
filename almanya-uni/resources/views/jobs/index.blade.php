@extends('layouts.app')

@section('title', __('Academic Jobs in Germany') . ' — ' . brand('name'))

<x-seo
    :title="__('Academic Jobs in Germany — PhD, Postdoc, Lecturer, Professor positions')"
    :description="__('Curated academic and research job postings across German universities. Filter by position type, field, language, city. Updated continuously.')"
/>

@push('head')
<script type="application/ld+json">{!! json_encode([
    '@context' => 'https://schema.org',
    '@type'    => 'CollectionPage',
    'name'     => __('Academic Jobs in Germany'),
    'description' => __('PhD, Postdoc, Lecturer and Professor positions in German higher-education institutions.'),
    'url'         => url()->current(),
    'numberOfItems' => $stats['total'] ?? 0,
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
@endpush

@section('content')

<section class="bg-gradient-to-br from-slate-800 via-gray-900 to-zinc-900 text-white">
    <div class="max-w-[1200px] mx-auto px-4 py-10 md:py-14">
        <nav class="text-sm text-gray-300 mb-3">
            <a href="{{ route('home') }}" class="hover:text-white">{{ __('Home') }}</a>
            <span class="mx-2 opacity-50">›</span>
            <span class="text-white">{{ __('Academic Jobs') }}</span>
        </nav>
        <h1 class="text-3xl md:text-5xl font-extrabold leading-tight drop-shadow mb-3 inline-flex items-center gap-3">
            <x-svg-icon name="briefcase" class="w-8 h-8 md:w-10 md:h-10" />
            {{ __('Academic Jobs in Germany') }}
        </h1>
        <p class="text-lg md:text-xl text-gray-300 max-w-3xl mb-5">
            {{ __('Curated PhD, Postdoc, Lecturer, Professor and Research positions across German higher-education institutions.') }}
        </p>

        {{-- Hero stats --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 max-w-3xl">
            <div class="bg-white/10 backdrop-blur-sm rounded-xl px-4 py-3">
                <div class="text-xs uppercase tracking-wider opacity-80 inline-flex items-center gap-1"><x-svg-icon name="list-bullet" class="w-3.5 h-3.5" /> {{ __('Open positions') }}</div>
                <div class="text-2xl font-extrabold">{{ number_format($stats['total']) }}</div>
            </div>
            <div class="bg-white/10 backdrop-blur-sm rounded-xl px-4 py-3">
                <div class="text-xs uppercase tracking-wider opacity-80 inline-flex items-center gap-1"><x-svg-icon name="beaker" class="w-3.5 h-3.5" /> {{ __('PhD') }}</div>
                <div class="text-2xl font-extrabold">{{ number_format($stats['phd']) }}</div>
            </div>
            <div class="bg-white/10 backdrop-blur-sm rounded-xl px-4 py-3">
                <div class="text-xs uppercase tracking-wider opacity-80 inline-flex items-center gap-1"><x-svg-icon name="beaker" class="w-3.5 h-3.5" /> {{ __('Postdoc') }}</div>
                <div class="text-2xl font-extrabold">{{ number_format($stats['postdoc']) }}</div>
            </div>
            <div class="bg-white/10 backdrop-blur-sm rounded-xl px-4 py-3">
                <div class="text-xs uppercase tracking-wider opacity-80 inline-flex items-center gap-1"><x-svg-icon name="clock" class="w-3.5 h-3.5" /> {{ __('Expiring soon') }}</div>
                <div class="text-2xl font-extrabold text-amber-300">{{ number_format($stats['expiring']) }}</div>
            </div>
        </div>
    </div>
</section>

<div class="max-w-[1200px] mx-auto px-4 py-8">

    {{-- FILTER FORM --}}
    <form method="GET" action="{{ route('jobs.index') }}" class="bg-white border border-gray-200 rounded-xl shadow-sm p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 lg:grid-cols-6 gap-3 items-end">
            <div class="md:col-span-2">
                <label class="block text-xs font-bold text-gray-700 mb-1 inline-flex items-center gap-1"><x-svg-icon name="search" class="w-3.5 h-3.5" /> {{ __('Keyword') }}</label>
                <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="{{ __('e.g. machine learning, mechanical, ...') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">{{ __('Position') }}</label>
                <select name="type" class="w-full px-2 py-2 border border-gray-300 rounded-lg text-sm">
                    <option value="">{{ __('All') }}</option>
                    @foreach ($positionTypes as $key => $meta)
                        <option value="{{ $key }}" @selected(($filters['type'] ?? '') === $key)>{{ __($meta['label_en']) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">{{ __('Language') }}</label>
                <select name="lang" class="w-full px-2 py-2 border border-gray-300 rounded-lg text-sm">
                    <option value="">{{ __('All') }}</option>
                    <option value="en" @selected(($filters['lang'] ?? '') === 'en')>🇬🇧 EN</option>
                    <option value="de" @selected(($filters['lang'] ?? '') === 'de')>🇩🇪 DE</option>
                    <option value="both" @selected(($filters['lang'] ?? '') === 'both')>{{ __('Both') }}</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">{{ __('Field') }}</label>
                <select name="field" class="w-full px-2 py-2 border border-gray-300 rounded-lg text-sm">
                    <option value="">{{ __('All') }}</option>
                    @foreach ($fields as $f)
                        <option value="{{ $f->slug }}" @selected(($filters['field'] ?? '') === $f->slug)>{{ $f->name_tr }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex flex-col gap-2">
                <label class="inline-flex items-center gap-2 text-xs">
                    <input type="checkbox" name="remote_only" value="1" @checked($filters['remote_only'] ?? false) class="rounded border-gray-300">
                    <span class="font-semibold text-gray-700 inline-flex items-center gap-1"><x-svg-icon name="globe" class="w-3.5 h-3.5" /> {{ __('Remote only') }}</span>
                </label>
                <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white text-sm font-bold px-4 py-2 rounded-lg shadow-sm">
                    {{ __('Filter') }}
                </button>
            </div>
        </div>
    </form>

    {{-- Results count --}}
    <div class="flex items-center justify-between mb-4">
        <p class="text-sm text-gray-600">
            <strong class="text-gray-900">{{ $jobs->total() }}</strong> {{ __('positions found') }}
        </p>
        @if ($jobs->total() > 0)
            <p class="text-xs text-gray-500">{{ __('Sorted by: featured first, then most recent') }}</p>
        @endif
    </div>

    {{-- JOB LIST --}}
    @if ($jobs->isEmpty())
        <div class="bg-amber-50 border-2 border-amber-200 rounded-2xl p-8 text-center">
            <div class="flex justify-center mb-3 text-amber-600"><x-svg-icon name="envelope" class="w-12 h-12" /></div>
            <h2 class="text-xl font-bold text-gray-900 mb-2">{{ __('No matching positions') }}</h2>
            <p class="text-sm text-gray-700 mb-4">{{ __('Try removing some filters, or check the related links below.') }}</p>
            <div class="flex flex-wrap items-center justify-center gap-2">
                <a href="{{ route('jobs.index') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white font-bold rounded-lg text-sm">{{ __('Clear all filters') }}</a>
                <a href="{{ route('scholarships.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-white border-2 border-gray-300 hover:bg-gray-50 text-gray-900 font-bold rounded-lg text-sm"><x-svg-icon name="trophy" class="w-4 h-4" /> {{ __('Browse scholarships') }}</a>
            </div>
        </div>
    @else
        <div class="space-y-3">
            @foreach ($jobs as $job)
                <article class="bg-white border-2 {{ $job->is_featured ? 'border-amber-300' : 'border-gray-200' }} rounded-xl p-5 hover:shadow-lg transition group">
                    @if ($job->is_featured)
                        <p class="inline-flex items-center gap-1 mb-2 px-2 py-0.5 bg-amber-100 text-amber-800 text-xs font-bold rounded-full"><x-svg-icon name="star" class="w-3 h-3" /> {{ __('Featured') }}</p>
                    @endif
                    <div class="flex items-start gap-4 flex-wrap">
                        <div class="shrink-0 w-12 h-12 flex items-center justify-center rounded-lg bg-indigo-50 text-indigo-600">{!! e_icon($job->position_icon, 'w-7 h-7') !!}</div>
                        <div class="flex-1 min-w-0">
                            <a href="{{ route('jobs.show', $job->slug) }}" class="block">
                                <h2 class="text-lg md:text-xl font-bold text-gray-900 group-hover:text-indigo-700 transition leading-snug">
                                    {{ $job->title }}
                                </h2>
                            </a>
                            <div class="flex flex-wrap gap-x-3 gap-y-1 text-xs text-gray-600 mt-1.5">
                                <span class="inline-flex items-center gap-1 font-semibold">{{ $job->position_label }}</span>
                                @if ($job->university)
                                    <span>·</span>
                                    <span class="inline-flex items-center gap-1"><x-svg-icon name="academic-cap" class="w-3.5 h-3.5" /> {{ $job->university->short_name ?: $job->university->display_name }}</span>
                                @endif
                                @if ($job->city)
                                    <span>·</span>
                                    <span class="inline-flex items-center gap-1"><x-svg-icon name="map-pin" class="w-3.5 h-3.5" /> {{ $job->city->name }}</span>
                                @endif
                                @if ($job->is_remote)
                                    <span>·</span>
                                    <span class="inline-flex items-center gap-1 text-emerald-700 font-semibold"><x-svg-icon name="globe" class="w-3.5 h-3.5" /> {{ __('Remote OK') }}</span>
                                @endif
                                @if ($job->language === 'en')
                                    <span>·</span><span>🇬🇧 English</span>
                                @elseif ($job->language === 'de')
                                    <span>·</span><span>🇩🇪 Deutsch</span>
                                @elseif ($job->language === 'both')
                                    <span>·</span><span class="inline-flex items-center gap-1"><x-svg-icon name="globe" class="w-3.5 h-3.5" /> EN + DE</span>
                                @endif
                            </div>
                            @if ($job->excerpt)
                                <p class="text-sm text-gray-700 mt-2 leading-relaxed">{{ \Illuminate\Support\Str::limit($job->excerpt, 180) }}</p>
                            @endif
                        </div>

                        {{-- Right column: deadline + salary --}}
                        <div class="shrink-0 text-right space-y-1">
                            @if ($job->deadline_at)
                                @php $days = $job->days_until_deadline; @endphp
                                <div class="text-xs font-bold {{ $days < 7 ? 'text-rose-600' : ($days < 14 ? 'text-amber-600' : 'text-gray-600') }}">
                                    @if ($days >= 0)
                                        <span class="inline-flex items-center gap-1"><x-svg-icon name="clock" class="w-3.5 h-3.5" /> {{ $days }} {{ __('days left') }}</span>
                                    @else
                                        {{ __('Closed') }}
                                    @endif
                                </div>
                                <div class="text-xs text-gray-500">{{ $job->deadline_at->translatedFormat('d M Y') }}</div>
                            @endif
                            @if ($job->salary_display)
                                <div class="text-xs font-semibold text-emerald-700 inline-flex items-center gap-1"><x-svg-icon name="banknotes" class="w-3.5 h-3.5" /> {{ $job->salary_display }}</div>
                            @endif
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $jobs->links() }}
        </div>
    @endif

    {{-- Bottom info card --}}
    <section class="mt-12 bg-gradient-to-r from-slate-50 to-gray-50 border border-gray-200 rounded-2xl p-6 md:p-8">
        <h2 class="text-xl font-bold text-gray-900 mb-2 inline-flex items-center gap-2"><x-svg-icon name="light-bulb" class="w-5 h-5" /> {{ __('About academic jobs in Germany') }}</h2>
        <ul class="text-sm text-gray-700 space-y-2 leading-relaxed list-disc list-inside">
            <li>{{ __('Most academic positions follow the TV-L pay scale (E13 / E14) — standard for public universities.') }}</li>
            <li>{{ __('PhD positions are usually 65–100% TV-L E13 (€1,800–2,800/month gross, 3–4 years).') }}</li>
            <li>{{ __('Postdoc positions are TV-L E13 or E14 100% (€3,200–4,500/month gross, 2–3 years).') }}</li>
            <li>{{ __('English-language positions are common in STEM and increasingly in social sciences.') }}</li>
            <li>{{ __('Visa: §18d (researcher) or §20 (job seeker) is typical — most positions sponsor.') }}</li>
        </ul>
    </section>

</div>

@endsection
