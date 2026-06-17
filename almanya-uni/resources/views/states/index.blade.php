@extends('layouts.app')

@section('title', __('16 German States — Guide for International Students') . ' — ' . brand('name'))
<x-seo :description="__('Discover Germany\'s 16 federal states: Bayern, NRW, Berlin, Baden-Württemberg... Cities, universities, and student life in each state.')" />
<x-json-ld :data="\App\Support\Seo::breadcrumbs([
    ['name' => __('Home'), 'url' => route('home')],
    ['name' => __('States'), 'url' => route('states.index')],
])" />

@section('content')

{{-- HERO --}}
<section class="bg-gradient-to-br from-primary-700 via-primary-600 to-accent-500 text-white">
    <div class="max-w-[1400px] mx-auto px-4 py-12 md:py-16">
        <nav class="text-sm text-primary-100 mb-3">
            <a href="/" class="hover:text-white">{{ __('Home') }}</a>
            <span class="mx-2 opacity-50">›</span>
            <span class="text-white">{{ __('States') }}</span>
        </nav>
        <h1 class="text-3xl md:text-5xl font-extrabold mb-3 leading-tight">
            {{ __('German Federal States') }}
        </h1>
        <p class="text-lg md:text-xl text-primary-100 max-w-3xl mb-6">
            {{ __('Germany consists of 16 federal states (Bundesländer). Each has its own education system, university culture, and identity. Which state is right for you?') }}
        </p>
        <div class="flex flex-wrap gap-4 text-sm">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/15 backdrop-blur ring-1 ring-white/20">
                <x-svg-icon name="map" class="w-6 h-6" />
                <span>{!! __('<strong class="text-lg">:n</strong> states', ['n' => $states->count()]) !!}</span>
            </div>
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/15 backdrop-blur ring-1 ring-white/20">
                <x-svg-icon name="building-office" class="w-6 h-6" />
                <span>{!! __('<strong class="text-lg">:n</strong> universities', ['n' => number_format($states->sum('uni_count'))]) !!}</span>
            </div>
        </div>
    </div>
</section>

{{-- INTERACTIVE MAP --}}
<section class="bg-gray-50 border-b border-gray-200 py-10">
    <div class="max-w-[1400px] mx-auto px-4">
        <x-germany-states-map />
    </div>
</section>

{{-- FILTER BAR --}}
<section class="bg-white border-b border-gray-200 shadow-sm">
    <div class="max-w-[1400px] mx-auto px-4 py-4">
        <div class="flex items-center flex-wrap gap-2">
            <span class="text-xs text-gray-500 mr-1">{{ __('Region:') }}</span>

            @php
                $regionChips = [
                    'nord'  => [__('North'), 'globe', 'HH, HB, NDS, SH, MV',
                        'bg-blue-600 text-white border-blue-600',
                        'bg-blue-50 text-blue-700 border-blue-200 hover:bg-blue-100'],
                    'sued'  => [__('South'), 'mountain', 'BY, BW',
                        'bg-amber-600 text-white border-amber-600',
                        'bg-amber-50 text-amber-700 border-amber-200 hover:bg-amber-100'],
                    'west'  => [__('West'), 'wrench-screwdriver', 'NRW, RP, HE, SL',
                        'bg-emerald-600 text-white border-emerald-600',
                        'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100'],
                    'ost'   => [__('East'), 'sparkles', 'BE, BB, SN, ST, TH',
                        'bg-rose-600 text-white border-rose-600',
                        'bg-rose-50 text-rose-700 border-rose-200 hover:bg-rose-100'],
                ];
            @endphp

            @foreach ($regionChips as $val => [$lbl, $iconName, $hint, $activeCls, $idleCls])
                <a href="{{ request()->fullUrlWithQuery(['region' => ($filters['region'] ?? '') === $val ? null : $val]) }}"
                   class="inline-flex items-center gap-1.5 text-xs px-3 py-1.5 rounded-full border transition
                          {{ ($filters['region'] ?? '') === $val ? $activeCls : $idleCls }}"
                   title="{{ $hint }}">
                    <x-svg-icon :name="$iconName" class="w-3.5 h-3.5" /> {{ $lbl }}
                </a>
            @endforeach

            <span class="text-xs text-gray-400 mx-2">·</span>
            <span class="text-xs text-gray-500">{{ __('Sort:') }}</span>

            @php
                $sortChips = [
                    'name'       => ['list-bullet', __('Alphabetical')],
                    'uni_count'  => ['academic-cap', __('Uni count')],
                    'population' => ['users', __('Population')],
                ];
            @endphp

            @foreach ($sortChips as $val => [$iconName, $lbl])
                <a href="{{ request()->fullUrlWithQuery(['sort' => $val]) }}"
                   class="inline-flex items-center gap-1 text-xs px-3 py-1.5 rounded-full border transition
                          {{ ($filters['sort'] ?? 'name') === $val ? 'bg-gray-800 text-white border-gray-800' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50' }}">
                    <x-svg-icon :name="$iconName" class="w-3.5 h-3.5" /> {{ $lbl }}
                </a>
            @endforeach

            @if(array_filter($filters ?? []))
                <a href="{{ route('states.index') }}" class="ml-2 text-xs text-accent-600 hover:text-accent-800 underline">↻ {{ __('Reset') }}</a>
            @endif

            <span class="ml-auto text-xs text-gray-500">{{ __(':n states shown', ['n' => $states->count()]) }}</span>
        </div>
    </div>
</section>

{{-- GRID --}}
<section class="bg-gray-50 py-10">
    <div class="max-w-[1400px] mx-auto px-4">
        @if ($states->isEmpty())
            <x-empty-state
                icon="🗺️"
                :title="__('No states available right now')"
                :description="__('Try browsing cities directly while we are updating the catalog.')"
                :actions="[
                    ['label' => __('Cities'), 'url' => route('cities.index'), 'primary' => true],
                    ['label' => __('Universities'), 'url' => route('universities.index')],
                ]"
            />
        @else
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach ($states as $state)
                @php
                    $seed = crc32($state->name);
                    $palettes = ['from-blue-500 to-cyan-400', 'from-purple-500 to-pink-500', 'from-amber-500 to-orange-400', 'from-emerald-500 to-teal-400', 'from-rose-500 to-fuchsia-500', 'from-indigo-500 to-violet-500'];
                    $palette = $palettes[$seed % count($palettes)];
                @endphp
                <a href="{{ route('states.show', $state->slug) }}"
                   class="group bg-white rounded-xl overflow-hidden border border-gray-200 hover:border-primary-500 hover:shadow-lg hover:-translate-y-0.5 transition-all flex flex-col">
                    <div class="aspect-[4/3] overflow-hidden bg-gray-100 relative">
                        @if($state->image_url)
                            <img src="{{ $state->image_url }}" alt="{{ $state->name }}" loading="lazy"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        @else
                            <div class="w-full h-full bg-gradient-to-br {{ $palette }} flex items-center justify-center">
                                <span class="text-5xl font-extrabold text-white/90 drop-shadow">{{ mb_substr($state->name, 0, 1) }}</span>
                            </div>
                        @endif
                        @if($state->content_blocks)
                            <span class="absolute top-2 left-2 inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-500 text-white text-[10px] font-bold uppercase tracking-wider shadow-sm"><x-svg-icon name="sparkles" class="w-3 h-3" /> {{ __('Guide') }}</span>
                        @endif
                        <span class="absolute bottom-2 right-2 inline-block px-2 py-0.5 rounded-full bg-black/60 backdrop-blur text-white text-xs font-semibold">
                            {{ __(':n unis', ['n' => $state->uni_count]) }}
                        </span>
                    </div>
                    <div class="p-4">
                        <h3 class="font-bold text-gray-900 group-hover:text-primary-600 transition leading-tight">{{ $state->name }}</h3>
                        <p class="text-xs text-gray-500 mt-2">{{ __(':n cities', ['n' => $state->cities_count]) }}</p>
                    </div>
                </a>
            @endforeach
        </div>
        @endif
    </div>
</section>

@endsection
