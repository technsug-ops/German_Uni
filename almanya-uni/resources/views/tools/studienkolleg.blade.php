@extends('layouts.app')

@section('title', __('Studienkolleg Finder — Foundation Year for International Students in Germany') . ' — ' . brand('name'))

<x-seo
    :title="__('Studienkolleg Finder — Foundation Year in Germany')"
    :description="__('30+ official Studienkollegs in Germany — public + private, by city, track (T/M/W/G/S), tuition fee. Compare and apply.')"
/>

<x-tool-schema tool="studienkolleg" />

<x-json-ld :data="\App\Support\Seo::breadcrumbs([
    ['name' => __('Home'), 'url' => route('home')],
    ['name' => __('Tools'), 'url' => route('tools.index')],
    ['name' => __('Studienkolleg Finder'), 'url' => route('tools.studienkolleg')],
])" />

@section('content')

{{-- HERO --}}
<section class="bg-gradient-to-br from-violet-700 via-purple-600 to-pink-500 text-white">
    <div class="max-w-[1400px] mx-auto px-4 py-10 md:py-14">
        <nav class="text-sm text-violet-100 mb-3">
            <a href="/" class="hover:text-white">{{ __('Home') }}</a>
            <span class="mx-2 opacity-50">›</span>
            <a href="{{ route('tools.index') }}" class="hover:text-white">{{ __('Tools') }}</a>
            <span class="mx-2 opacity-50">›</span>
            <span class="text-white">{{ __('Studienkolleg Finder') }}</span>
        </nav>
        <h1 class="text-3xl md:text-5xl font-extrabold leading-tight drop-shadow mb-3">
            🎓 {{ __('Studienkolleg Finder') }}
        </h1>
        <p class="text-lg md:text-xl text-violet-100 max-w-3xl">
            {{ __('Your German high-school equivalent is rated Anabin H-? You need a Studienkolleg — a 1-year foundation year before Bachelor. Compare 30+ official Studienkollegs by city, track, and tuition.') }}
        </p>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mt-6 max-w-[1400px]">
            <div class="bg-white/15 backdrop-blur rounded-lg p-3 ring-1 ring-white/20">
                <p class="text-2xl font-bold">{{ $stats['total'] }}</p>
                <p class="text-xs text-violet-100 mt-0.5">{{ __('Total Studienkollegs') }}</p>
            </div>
            <div class="bg-white/15 backdrop-blur rounded-lg p-3 ring-1 ring-white/20">
                <p class="text-2xl font-bold">{{ $stats['staatlich'] }}</p>
                <p class="text-xs text-violet-100 mt-0.5">{{ __('Public (free)') }}</p>
            </div>
            <div class="bg-white/15 backdrop-blur rounded-lg p-3 ring-1 ring-white/20">
                <p class="text-2xl font-bold">{{ $stats['privat'] }}</p>
                <p class="text-xs text-violet-100 mt-0.5">{{ __('Private (paid)') }}</p>
            </div>
            <div class="bg-white/15 backdrop-blur rounded-lg p-3 ring-1 ring-white/20">
                <p class="text-2xl font-bold">{{ $stats['cities'] }}</p>
                <p class="text-xs text-violet-100 mt-0.5">{{ __('Cities covered') }}</p>
            </div>
        </div>
    </div>
</section>

<div class="max-w-[1400px] mx-auto px-4 py-10">

    {{-- Featured-snippet box (AIO target) --}}
    <x-featured-snippet
        :question="__('Do I need a Studienkolleg to study in Germany?')"
        :answer="__('You need a Studienkolleg if your secondary-school qualification is rated H- in the official Anabin database (most non-EU diplomas). It is a 1-year preparatory programme ending with the Feststellungsprüfung exam. Public Studienkollegs are tuition-free; private ones charge 8,000–15,000 EUR per year.')"
        :steps="[
            ['title' => __('Check your eligibility'), 'description' => __('Use our Anabin Eligibility Checker to see if you need it.')],
            ['title' => __('Get conditional admission'), 'description' => __('Apply to a German university — they will issue a conditional admission letter (Zulassungsbescheid mit Auflage).')],
            ['title' => __('Apply to Studienkolleg + take Aufnahmetest'), 'description' => __('Apply via uni-assist or directly. Pass the entrance exam testing German + maths.')],
            ['title' => __('Complete 1 year + pass Feststellungsprüfung'), 'description' => __('Choose your track (T/M/W/G/S) matching your intended Bachelor field.')],
            ['title' => __('Apply for Bachelor with the new certificate'), 'description' => __('The Feststellungsprüfung certificate is equivalent to a German Abitur for your chosen field.')],
        ]"
    />

    {{-- FILTERS --}}
    <section class="mt-8 mb-6 flex flex-wrap items-center gap-2">
        <span class="text-xs text-gray-500 mr-1">{{ __('Type:') }}</span>
        <a href="{{ route('tools.studienkolleg') }}"
           class="px-3 py-1.5 rounded-full text-xs font-semibold transition {{ ! $activeFilter['type'] ? 'bg-violet-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            {{ __('All') }} ({{ $stats['total'] }})
        </a>
        <a href="{{ route('tools.studienkolleg', ['type' => 'staatlich']) }}"
           class="px-3 py-1.5 rounded-full text-xs font-semibold transition {{ $activeFilter['type'] === 'staatlich' ? 'bg-violet-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            🏛️ {{ __('Public (free)') }} ({{ $stats['staatlich'] }})
        </a>
        <a href="{{ route('tools.studienkolleg', ['type' => 'privat']) }}"
           class="px-3 py-1.5 rounded-full text-xs font-semibold transition {{ $activeFilter['type'] === 'privat' ? 'bg-violet-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            🏢 {{ __('Private (paid)') }} ({{ $stats['privat'] }})
        </a>

        <span class="text-xs text-gray-500 ml-4 mr-1">{{ __('Track:') }}</span>
        @foreach (['T', 'M', 'W', 'G', 'S'] as $tr)
            <a href="{{ route('tools.studienkolleg', array_filter(['type' => $activeFilter['type'], 'track' => $activeFilter['track'] === $tr ? null : $tr])) }}"
               class="px-2.5 py-1.5 rounded-md text-xs font-bold transition {{ $activeFilter['track'] === $tr ? 'bg-purple-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
               title="{{ $trackLabels[$tr] }}">
                {{ $tr }}-Kurs
            </a>
        @endforeach
    </section>

    {{-- LIST --}}
    @if ($studienkollegs->isEmpty())
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-8 text-center">
            <p class="text-gray-700">{{ __('No Studienkollegs match this filter. Try a broader search.') }}</p>
            <a href="{{ route('tools.studienkolleg') }}" class="inline-block mt-3 text-violet-600 hover:underline font-semibold">{{ __('Show all') }} →</a>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            @foreach ($studienkollegs as $sk)
                @php
                    $cityName = $sk->city?->name ?? $sk->city_name_cache ?? '—';
                    $desc = $sk->localized('description');
                @endphp
                <article class="bg-white border border-gray-200 hover:border-violet-400 rounded-xl p-5 transition shadow-sm hover:shadow">
                    <div class="flex items-start justify-between gap-3 mb-2">
                        <div class="flex-1 min-w-0">
                            <h3 class="font-bold text-gray-900 leading-tight mb-1">{{ $sk->name }}</h3>
                            <p class="text-sm text-gray-500">📍 {{ $cityName }}</p>
                        </div>
                        <span class="shrink-0 text-xs font-bold px-2 py-1 rounded-full {{ $sk->type === 'staatlich' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                            {{ $sk->type_label }}
                        </span>
                    </div>

                    @if ($desc)
                        <p class="text-sm text-gray-700 leading-relaxed mb-3">{{ \Illuminate\Support\Str::limit($desc, 180) }}</p>
                    @endif

                    {{-- Tracks --}}
                    @if (! empty($sk->tracks))
                        <div class="flex flex-wrap gap-1 mb-3">
                            @foreach ($sk->tracks as $tr)
                                <span class="text-[10px] font-bold uppercase tracking-wider bg-purple-50 text-purple-700 px-1.5 py-0.5 rounded" title="{{ $trackLabels[$tr] ?? $tr }}">
                                    {{ $tr }}-Kurs
                                </span>
                            @endforeach
                        </div>
                    @endif

                    <dl class="grid grid-cols-2 gap-2 text-xs text-gray-600 mb-4">
                        @if ($sk->semester_fee_eur !== null && $sk->semester_fee_eur > 0)
                            <div>
                                <dt class="text-gray-500">{{ __('Annual fee') }}</dt>
                                <dd class="font-bold text-gray-900">{{ number_format($sk->semester_fee_eur, 0, ',', '.') }} EUR</dd>
                            </div>
                        @else
                            <div>
                                <dt class="text-gray-500">{{ __('Tuition') }}</dt>
                                <dd class="font-bold text-emerald-700">{{ __('Free') }}</dd>
                            </div>
                        @endif
                        @if ($sk->entrance_exam)
                            <div>
                                <dt class="text-gray-500">{{ __('Entrance exam') }}</dt>
                                <dd class="font-semibold text-gray-900 capitalize">{{ $sk->entrance_exam }}</dd>
                            </div>
                        @endif
                    </dl>

                    @if ($sk->website_url)
                        <a href="{{ $sk->website_url }}" target="_blank" rel="noopener nofollow"
                           class="inline-flex items-center gap-1.5 bg-violet-600 hover:bg-violet-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
                            {{ __('Official site') }} ↗
                        </a>
                    @endif
                </article>
            @endforeach
        </div>
    @endif

    {{-- Disclaimer --}}
    <p class="text-xs text-gray-400 mt-8 text-center max-w-3xl mx-auto">
        {{ __('Information compiled from official Studienkolleg websites + DAAD. Verify current admission rules + fees on the institution\'s own site before applying. List updated periodically.') }}
    </p>
</div>

{{-- Auto-FAQ (AIO + Featured Snippet) --}}
<x-faq-section
    :title="__('Frequently Asked Questions about Studienkolleg')"
    :subtitle="__('Everything you need to know about the foundation year in Germany')"
    :faqs="\App\Support\PageFaq::forStudienkolleg()"
/>

@endsection
