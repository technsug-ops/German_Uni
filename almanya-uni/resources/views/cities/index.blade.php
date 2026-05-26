@extends('layouts.app')

@section('title', __('German University Cities — Student Guide') . ' — ' . brand('name'))
@section('meta_description', __('Discover student-friendly cities in Germany: cost of living, universities, culture, places to visit and student life.'))

@section('content')

{{-- ─────────────── HERO ─────────────── --}}
<section class="bg-gradient-to-br from-primary-700 via-primary-600 to-accent-500 text-white">
    <div class="max-w-[1400px] mx-auto px-4 py-12 md:py-16">
        <nav class="text-sm text-primary-100 mb-3">
            <a href="/" class="hover:text-white">{{ __('Home') }}</a>
            <span class="mx-2 opacity-50">›</span>
            <span class="text-white">{{ __('Cities') }}</span>
        </nav>
        <h1 class="text-3xl md:text-5xl font-extrabold mb-3 leading-tight">
            {{ __('German University Cities') }}
        </h1>
        <p class="text-lg md:text-xl text-primary-100 max-w-3xl mb-6">
            {{ __('Cost of living, student culture, places to visit and universities — one-page guide for each city.') }}
        </p>
        <div class="flex flex-wrap gap-4 text-sm">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/15 backdrop-blur ring-1 ring-white/20">
                <span class="text-2xl">🏙️</span>
                <span><strong class="text-lg">{{ $cities->total() }}</strong> {{ __('cities') }}</span>
            </div>
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/15 backdrop-blur ring-1 ring-white/20">
                <span class="text-2xl">🗺️</span>
                <span><strong class="text-lg">{{ $states->count() }}</strong> {{ __('states') }}</span>
            </div>
        </div>
    </div>
</section>

{{-- ─────────────── FILTER BAR ─────────────── --}}
<section class="bg-white border-b border-gray-200 shadow-sm">
    <div class="max-w-[1400px] mx-auto px-4 py-4">
        <form method="GET" action="{{ route('cities.index') }}" class="space-y-3">
            <div class="grid grid-cols-1 md:grid-cols-[1fr_220px_180px_auto_auto] gap-3">
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">🔍</span>
                    <input type="text" name="q" value="{{ $filters['q'] ?? '' }}"
                           placeholder="{{ __('Search city (Berlin, München, Hamburg…)') }}"
                           class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-300 bg-white text-gray-900 focus:ring-2 focus:ring-primary-500 focus:border-primary-500"/>
                </div>
                <select name="state"
                        class="px-4 py-2.5 rounded-lg border border-gray-300 bg-white text-gray-900">
                    <option value="">🗺️ {{ __('All states') }}</option>
                    @foreach($states as $st)
                        <option value="{{ $st->slug }}" @selected(($filters['state'] ?? '') === $st->slug)>{{ $st->name }}</option>
                    @endforeach
                </select>
                <select name="sort" class="px-4 py-2.5 rounded-lg border border-gray-300 bg-white text-gray-900">
                    <option value="uni_count" @selected(($filters['sort'] ?? 'uni_count') === 'uni_count')>📚 {{ __('University count (most → least)') }}</option>
                    <option value="population" @selected(($filters['sort'] ?? '') === 'population')>👥 {{ __('Population (most → least)') }}</option>
                    <option value="name" @selected(($filters['sort'] ?? '') === 'name')>🔤 {{ __('Alphabetical') }}</option>
                </select>
                <button class="px-6 py-2.5 rounded-lg bg-primary-600 hover:bg-primary-700 text-white font-semibold transition">
                    {{ __('Filter') }}
                </button>
                @if(array_filter($filters ?? []))
                    <a href="{{ route('cities.index') }}"
                       class="px-6 py-2.5 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-center transition">
                        {{ __('Reset') }}
                    </a>
                @endif
            </div>

            {{-- Hızlı chip filtreleri --}}
            <div class="flex items-center flex-wrap gap-2 pt-2 border-t border-gray-100">
                <span class="text-xs text-gray-500 mr-1">{{ __('City size:') }}</span>

                @php
                    $sizeChips = [
                        'small'  => ['🏘️ ' . __('Small'), '< 200K',
                            'bg-amber-600 text-white border-amber-600',
                            'bg-amber-50 text-amber-700 border-amber-200 hover:bg-amber-100'],
                        'medium' => ['🏙️ ' . __('Medium'), '200K-1M',
                            'bg-blue-600 text-white border-blue-600',
                            'bg-blue-50 text-blue-700 border-blue-200 hover:bg-blue-100'],
                        'large'  => ['🌆 ' . __('Metropolis'), '> 1M',
                            'bg-rose-600 text-white border-rose-600',
                            'bg-rose-50 text-rose-700 border-rose-200 hover:bg-rose-100'],
                    ];
                @endphp

                @foreach ($sizeChips as $val => [$lbl, $rng, $activeCls, $idleCls])
                    <a href="{{ request()->fullUrlWithQuery(['size' => ($filters['size'] ?? '') === $val ? null : $val]) }}"
                       class="inline-flex items-center gap-1 text-xs px-3 py-1.5 rounded-full border transition
                              {{ ($filters['size'] ?? '') === $val ? $activeCls : $idleCls }}">
                        {{ $lbl }} <span class="opacity-75">({{ $rng }})</span>
                    </a>
                @endforeach

                <span class="text-xs text-gray-400 mx-2">·</span>
                <span class="text-xs text-gray-500">{{ __('University density:') }}</span>

                @php
                    $uniChips = [
                        'few' => ['🎓 ' . __('1-2 unis'),
                            'bg-emerald-600 text-white border-emerald-600',
                            'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100'],
                        'mid' => ['🎓 ' . __('3-5 unis'),
                            'bg-teal-600 text-white border-teal-600',
                            'bg-teal-50 text-teal-700 border-teal-200 hover:bg-teal-100'],
                        'many' => ['🎓 ' . __('6+ unis'),
                            'bg-indigo-600 text-white border-indigo-600',
                            'bg-indigo-50 text-indigo-700 border-indigo-200 hover:bg-indigo-100'],
                    ];
                @endphp

                @foreach ($uniChips as $val => [$lbl, $activeCls, $idleCls])
                    <a href="{{ request()->fullUrlWithQuery(['uni_count' => ($filters['uni_count'] ?? '') === $val ? null : $val]) }}"
                       class="inline-flex items-center gap-1 text-xs px-3 py-1.5 rounded-full border transition
                              {{ ($filters['uni_count'] ?? '') === $val ? $activeCls : $idleCls }}">
                        {{ $lbl }}
                    </a>
                @endforeach
            </div>
        </form>
    </div>
</section>

{{-- ─────────────── RESULTS ─────────────── --}}
<section class="bg-gray-50 py-10">
    <div class="max-w-[1400px] mx-auto px-4">
        <div class="flex items-center justify-between mb-6">
            <p class="text-sm text-gray-600">
                {!! __('Found <strong class="text-gray-900">:n</strong> cities', ['n' => $cities->total()]) !!}
            </p>
            <p class="text-xs text-gray-500">{{ __('Sorted by number of universities') }}</p>
        </div>

        @if($cities->isEmpty())
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-8 text-center text-yellow-800">
                <p class="text-lg">{{ __('No city matches these filters.') }}</p>
                <a href="{{ route('cities.index') }}" class="inline-block mt-3 text-primary-600 hover:underline">{{ __('Clear filters') }} →</a>
            </div>
        @else
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
                @foreach($cities as $city)
                    @php
                        $seed = crc32($city->name);
                        $palettes = [
                            'from-blue-500 to-cyan-400',
                            'from-purple-500 to-pink-500',
                            'from-amber-500 to-orange-400',
                            'from-emerald-500 to-teal-400',
                            'from-rose-500 to-fuchsia-500',
                            'from-indigo-500 to-violet-500',
                        ];
                        $palette = $palettes[$seed % count($palettes)];
                        $initial = mb_substr($city->name, 0, 1);
                    @endphp
                    <a href="{{ route('cities.show', $city->slug) }}"
                       class="group bg-white rounded-xl overflow-hidden border border-gray-200 hover:border-primary-500 hover:shadow-lg hover:-translate-y-0.5 transition-all flex flex-col">
                        {{-- Image / placeholder --}}
                        <div class="aspect-[4/3] overflow-hidden bg-gray-100 relative">
                            @if($city->image_url)
                                <img src="{{ $city->image_url }}" alt="{{ $city->name }}"
                                     loading="lazy"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"/>
                            @else
                                <div class="w-full h-full bg-gradient-to-br {{ $palette }} flex items-center justify-center">
                                    <span class="text-5xl font-extrabold text-white/90 drop-shadow">{{ $initial }}</span>
                                </div>
                            @endif
                            <span class="absolute bottom-2 right-2 inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-black/60 backdrop-blur text-white text-xs font-semibold">
                                {{ __(':n unis', ['n' => $city->universities_count]) }}
                            </span>
                        </div>
                        {{-- Text --}}
                        <div class="p-3">
                            <h3 class="font-bold text-gray-900 group-hover:text-primary-600 transition leading-tight truncate">
                                {{ $city->name }}
                            </h3>
                            @if($city->state)
                                <p class="text-xs text-gray-500 truncate mt-0.5">{{ $city->state->name }}</p>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-8">{{ $cities->links() }}</div>
        @endif
    </div>
</section>

@endsection
