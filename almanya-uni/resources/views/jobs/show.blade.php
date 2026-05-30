@extends('layouts.app')

@section('title', $job->title . ' — ' . brand('name'))

<x-seo
    :title="$job->title"
    :description="$job->excerpt ?: __('Academic position at') . ' ' . ($job->university?->display_name ?: 'Germany')"
/>

@push('head')
<script type="application/ld+json">{!! json_encode(array_filter([
    '@context'         => 'https://schema.org',
    '@type'            => 'JobPosting',
    'title'            => $job->title,
    'description'      => $job->description ?: $job->excerpt,
    'datePosted'       => optional($job->posted_at)->toIso8601String(),
    'validThrough'     => optional($job->deadline_at)->toIso8601String(),
    'employmentType'   => match ($job->employment_type) {
        'full_time'  => 'FULL_TIME',
        'part_time'  => 'PART_TIME',
        'fixed_term' => 'CONTRACTOR',
        'permanent'  => 'FULL_TIME',
        default      => 'FULL_TIME',
    },
    'hiringOrganization' => $job->university ? [
        '@type' => 'CollegeOrUniversity',
        'name'  => $job->university->display_name,
        'sameAs' => url('/universities/' . $job->university->slug),
    ] : null,
    'jobLocation' => $job->city ? [
        '@type' => 'Place',
        'address' => [
            '@type'           => 'PostalAddress',
            'addressLocality' => $job->city->name,
            'addressCountry'  => 'DE',
        ],
    ] : null,
    'jobLocationType' => $job->is_remote ? 'TELECOMMUTE' : null,
    'baseSalary' => ($job->salary_min_eur && $job->salary_max_eur) ? [
        '@type' => 'MonetaryAmount',
        'currency' => 'EUR',
        'value' => [
            '@type'    => 'QuantitativeValue',
            'minValue' => $job->salary_min_eur,
            'maxValue' => $job->salary_max_eur,
            'unitText' => 'YEAR',
        ],
    ] : null,
    'directApply' => (bool) $job->application_url,
    'url' => url()->current(),
], fn ($v) => $v !== null), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
@endpush

@section('content')

<section class="bg-gradient-to-br from-slate-800 via-gray-900 to-zinc-900 text-white">
    <div class="max-w-[1100px] mx-auto px-4 py-10 md:py-12">
        <nav class="text-sm text-gray-300 mb-3">
            <a href="{{ route('home') }}" class="hover:text-white">{{ __('Home') }}</a>
            <span class="mx-2 opacity-50">›</span>
            <a href="{{ route('jobs.index') }}" class="hover:text-white">{{ __('Academic Jobs') }}</a>
            <span class="mx-2 opacity-50">›</span>
            <span class="text-white">{{ \Illuminate\Support\Str::limit($job->title, 60) }}</span>
        </nav>

        @if ($job->is_featured)
            <span class="inline-flex items-center gap-1 mb-3 px-3 py-1 bg-amber-400 text-amber-900 text-xs font-bold rounded-full"><x-svg-icon name="star" class="w-3.5 h-3.5" /> {{ __('Featured position') }}</span>
        @endif

        <h1 class="text-2xl md:text-4xl font-extrabold leading-tight drop-shadow mb-3 flex items-center gap-3">
            <span class="inline-flex">{!! e_icon($job->position_icon, 'w-7 h-7 md:w-8 md:h-8') !!}</span>
            <span>{{ $job->title }}</span>
        </h1>

        <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-sm text-gray-300">
            <span class="font-semibold text-white">{{ $job->position_label }}</span>
            @if ($job->university)
                <span>·</span>
                <a href="{{ route('universities.show', $job->university->slug) }}" class="inline-flex items-center gap-1 text-white hover:underline">
                    <x-svg-icon name="academic-cap" class="w-4 h-4" /> {{ $job->university->display_name }}
                </a>
            @endif
            @if ($job->city)
                <span>·</span>
                <a href="{{ route('cities.show', $job->city->slug) }}" class="inline-flex items-center gap-1 text-white hover:underline">
                    <x-svg-icon name="map-pin" class="w-4 h-4" /> {{ $job->city->name }}
                </a>
            @endif
            @if ($job->is_remote)
                <span>·</span>
                <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-emerald-500/30 text-emerald-200 rounded-full text-xs font-bold"><x-svg-icon name="globe" class="w-3 h-3" /> {{ __('Remote OK') }}</span>
            @endif
        </div>
    </div>
</section>

<div class="max-w-[1100px] mx-auto px-4 py-8">

    <div class="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-6">

        {{-- MAIN COL --}}
        <div class="space-y-6 min-w-0">

            {{-- Excerpt --}}
            @if ($job->excerpt)
                <p class="text-lg text-gray-800 leading-relaxed italic border-l-4 border-indigo-500 pl-4 py-1">
                    {{ $job->excerpt }}
                </p>
            @endif

            {{-- Description --}}
            @if ($job->description)
                <article class="bg-white border border-gray-200 rounded-xl p-6 prose max-w-none prose-headings:text-gray-900 prose-a:text-indigo-700">
                    <h2 class="text-lg font-bold text-gray-900 mb-3 inline-flex items-center gap-2"><x-svg-icon name="pencil" class="w-5 h-5 text-indigo-600" /> {{ __('Position description') }}</h2>
                    {!! \Illuminate\Support\Str::markdown($job->description) !!}
                </article>
            @endif

            {{-- Requirements --}}
            @if ($job->requirements)
                <article class="bg-white border border-gray-200 rounded-xl p-6 prose max-w-none">
                    <h2 class="text-lg font-bold text-gray-900 mb-3 inline-flex items-center gap-2"><x-svg-icon name="check-circle" class="w-5 h-5 text-emerald-600" /> {{ __('Requirements') }}</h2>
                    {!! \Illuminate\Support\Str::markdown($job->requirements) !!}
                </article>
            @endif

            {{-- Apply CTA (large) --}}
            @if ($job->application_url)
                <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-2xl p-6 md:p-8 text-center shadow-lg">
                    <h2 class="text-2xl font-bold mb-2 inline-flex items-center gap-2 justify-center"><x-svg-icon name="envelope" class="w-6 h-6 text-white" /> {{ __('Ready to apply?') }}</h2>
                    @if ($job->deadline_at)
                        <p class="text-sm opacity-90 mb-4 inline-flex items-center gap-1.5 justify-center">
                            <x-svg-icon name="clock" class="w-4 h-4" /> {{ __('Application deadline:') }} <strong>{{ $job->deadline_at->translatedFormat('d M Y') }}</strong>
                        </p>
                    @endif
                    <a href="{{ $job->application_url }}" target="_blank" rel="noopener noreferrer nofollow"
                       class="inline-flex items-center gap-2 bg-white text-indigo-700 hover:bg-indigo-50 font-bold px-8 py-3 rounded-lg shadow transition">
                        {{ __('Open application page') }} ↗
                    </a>
                    @if ($job->source_name)
                        <p class="text-xs opacity-80 mt-3">{{ __('Source:') }} {{ $job->source_name }}</p>
                    @endif
                </div>
            @endif
        </div>

        {{-- SIDEBAR --}}
        <aside class="space-y-4">
            <div class="bg-white border border-gray-200 rounded-xl p-5 sticky top-4">
                <h3 class="font-bold text-gray-900 mb-3 inline-flex items-center gap-2"><x-svg-icon name="map-pin" class="w-5 h-5 text-indigo-600" /> {{ __('At a glance') }}</h3>
                <dl class="space-y-2.5 text-sm">
                    <div>
                        <dt class="text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('Employment') }}</dt>
                        <dd class="text-gray-900 font-semibold inline-flex items-center gap-1.5">
                            @switch($job->employment_type)
                                @case('full_time')  <x-svg-icon name="clock" class="w-4 h-4 text-gray-500" /> {{ __('Full time') }} @break
                                @case('part_time')  <x-svg-icon name="clock" class="w-4 h-4 text-gray-500" /> {{ __('Part time') }} @break
                                @case('fixed_term') <x-svg-icon name="calendar" class="w-4 h-4 text-gray-500" /> {{ __('Fixed term') }} @break
                                @case('permanent')  <x-svg-icon name="shield-check" class="w-4 h-4 text-gray-500" /> {{ __('Permanent') }} @break
                            @endswitch
                        </dd>
                    </div>
                    @if ($job->salary_display)
                        <div>
                            <dt class="text-xs font-bold text-gray-500 uppercase tracking-wider inline-flex items-center gap-1.5"><x-svg-icon name="banknotes" class="w-3.5 h-3.5" /> {{ __('Compensation') }}</dt>
                            <dd class="text-emerald-700 font-bold">{{ $job->salary_display }}</dd>
                        </div>
                    @endif
                    @if ($job->field)
                        <div>
                            <dt class="text-xs font-bold text-gray-500 uppercase tracking-wider inline-flex items-center gap-1.5"><x-svg-icon name="target" class="w-3.5 h-3.5" /> {{ __('Field') }}</dt>
                            <dd>
                                <a href="{{ route('fields.show', $job->field->slug) }}" class="inline-flex items-center gap-1.5 text-indigo-700 hover:underline font-semibold">
                                    {!! e_icon($job->field->icon, 'w-4 h-4') !!} {{ $job->field->name_tr }}
                                </a>
                            </dd>
                        </div>
                    @endif
                    @if ($job->posted_at)
                        <div>
                            <dt class="text-xs font-bold text-gray-500 uppercase tracking-wider inline-flex items-center gap-1.5"><x-svg-icon name="calendar" class="w-3.5 h-3.5" /> {{ __('Posted') }}</dt>
                            <dd class="text-gray-700">{{ $job->posted_at->translatedFormat('d M Y') }} ({{ $job->posted_at->diffForHumans() }})</dd>
                        </div>
                    @endif
                    @if ($job->deadline_at)
                        @php $days = $job->days_until_deadline; @endphp
                        <div>
                            <dt class="text-xs font-bold text-gray-500 uppercase tracking-wider inline-flex items-center gap-1.5"><x-svg-icon name="clock" class="w-3.5 h-3.5" /> {{ __('Deadline') }}</dt>
                            <dd class="font-bold {{ $days < 7 ? 'text-rose-600' : ($days < 14 ? 'text-amber-600' : 'text-gray-900') }}">
                                {{ $job->deadline_at->translatedFormat('d M Y') }}
                                @if ($days >= 0)
                                    <span class="text-xs ml-1">({{ $days }} {{ __('days left') }})</span>
                                @else
                                    <span class="text-xs ml-1 text-rose-500">({{ __('closed') }})</span>
                                @endif
                            </dd>
                        </div>
                    @endif
                </dl>
            </div>
        </aside>
    </div>

    {{-- Related --}}
    @if ($related->isNotEmpty())
        <section class="mt-12">
            <h2 class="text-xl font-bold text-gray-900 mb-4 inline-flex items-center gap-2"><x-svg-icon name="link" class="w-5 h-5 text-indigo-600" /> {{ __('Related positions') }}</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @foreach ($related as $r)
                    <a href="{{ route('jobs.show', $r->slug) }}" class="block bg-white border border-gray-200 rounded-xl p-4 hover:shadow-md transition">
                        <div class="flex items-start gap-3">
                            <div class="shrink-0 inline-flex items-center justify-center w-10 h-10 rounded-lg bg-indigo-50 text-indigo-600">{!! e_icon($r->position_icon, 'w-5 h-5') !!}</div>
                            <div class="flex-1 min-w-0">
                                <h3 class="font-bold text-gray-900 leading-snug">{{ \Illuminate\Support\Str::limit($r->title, 80) }}</h3>
                                <p class="text-xs text-gray-600 mt-1">
                                    {{ $r->position_label }}
                                    @if ($r->university)
                                        · {{ $r->university->short_name ?: $r->university->display_name }}
                                    @endif
                                </p>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

</div>

@endsection
