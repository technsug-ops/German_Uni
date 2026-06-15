@extends('layouts.app')

@section('title', __('German Universities — Guide for International Students') . ' — ' . brand('name'))
<x-seo :description="__('464 official German universities (Hochschule): programs, applications, cost of living and a complete guide for international students.')" />
<x-json-ld :data="\App\Support\Seo::breadcrumbs([
    ['name' => __('Home'), 'url' => route('home')],
    ['name' => __('Universities'), 'url' => route('universities.index')],
])" />

{{-- $typeLabel and $typeBadgeColor closures are provided by UniversityWebController so they're also available when the _grid partial is rendered for XHR async-filter responses. --}}

@section('content')

{{-- ─────────────── HERO ─────────────── --}}
<section class="bg-gradient-to-br from-primary-700 via-primary-600 to-accent-500 text-white">
    <div class="max-w-[1400px] mx-auto px-4 py-12 md:py-16">
        <nav class="text-sm text-primary-100 mb-3">
            <a href="/" class="hover:text-white">{{ __('Home') }}</a>
            <span class="mx-2 opacity-50">›</span>
            <span class="text-white">{{ __('Universities') }}</span>
        </nav>
        <h1 class="text-3xl md:text-5xl font-extrabold mb-3 leading-tight">
            {{ __('German Universities') }}
        </h1>
        <p class="text-lg md:text-xl text-primary-100 max-w-3xl mb-6">
            {{ __('Germany\'s 464 official higher education institutions (Hochschule) — public, private, applied sciences and art universities. Programs, cost of living and application guide.') }}
        </p>
        <div class="flex flex-wrap gap-4 text-sm">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/15 backdrop-blur ring-1 ring-white/20">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5"/></svg>
                <span><strong class="text-lg">{{ $total }}</strong> {{ __('universities') }}</span>
            </div>
            @if(!empty($states))
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/15 backdrop-blur ring-1 ring-white/20">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503 3.498 4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 0 0-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0Z"/></svg>
                    <span><strong class="text-lg">{{ count($states) }}</strong> {{ __('states') }}</span>
                </div>
            @endif
        </div>
    </div>
</section>

{{-- ─────────────── FILTER BAR ─────────────── --}}
<section class="bg-white border-b border-gray-200 shadow-sm">
    <div class="max-w-[1400px] mx-auto px-4 py-4">
        <form method="GET" action="{{ route('universities.index') }}" class="space-y-3"
              data-async-filter-form="#async-filter-results"
              data-no-loading>
            <div class="grid grid-cols-1 md:grid-cols-[1fr_180px_180px_140px_auto_auto] gap-3">
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
                    </span>
                    <input type="text" name="q" value="{{ request('q') }}"
                           placeholder="{{ __('University name (TU München, Heidelberg...)') }}"
                           class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-300 bg-white text-gray-900 focus:ring-2 focus:ring-primary-500 focus:border-primary-500"/>
                </div>
                <select name="type" class="px-4 py-2.5 rounded-lg border border-gray-300 bg-white text-gray-900">
                    <option value="">{{ __('All types') }}</option>
                    @foreach (($available_types ?? []) as $t)
                        <option value="{{ $t }}" @selected(request('type') === $t)>{{ $typeLabel($t) }}</option>
                    @endforeach
                </select>
                <select name="state" class="px-4 py-2.5 rounded-lg border border-gray-300 bg-white text-gray-900">
                    <option value="">{{ __('All states') }}</option>
                    @foreach (($states ?? []) as $s)
                        <option value="{{ $s->slug }}" @selected(request('state') === $s->slug)>{{ $s->name }}</option>
                    @endforeach
                </select>
                <input type="text" name="city" value="{{ request('city') }}"
                       placeholder="{{ __('City') }}"
                       class="px-4 py-2.5 rounded-lg border border-gray-300 bg-white text-gray-900"/>
                <button class="px-6 py-2.5 rounded-lg bg-primary-600 hover:bg-primary-700 text-white font-semibold transition">
                    {{ __('Filter') }}
                </button>
                @if(request()->hasAny(['q', 'type', 'state', 'city', 'english', 'uni_assist', 'size']))
                    <a href="{{ route('universities.index') }}"
                       class="px-6 py-2.5 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-center transition">
                        {{ __('Reset') }}
                    </a>
                @endif
            </div>

            {{-- Quick toggle chips (kritik filtreler) --}}
            <div class="flex items-center flex-wrap gap-2 pt-2 border-t border-gray-100">
                <span class="text-xs text-gray-500 mr-1">{{ __('Quick filter:') }}</span>

                <label class="inline-flex items-center gap-1.5 text-xs px-3 py-1.5 rounded-full border cursor-pointer transition
                    {{ request('english') === '1' ? 'bg-blue-600 text-white border-blue-600' : 'bg-blue-50 text-blue-700 border-blue-200 hover:bg-blue-100' }}">
                    <input type="checkbox" name="english" value="1" @checked(request('english') === '1') class="hidden" onchange="this.form.submit()">
                    {{ __('Has English programs') }}
                </label>

                <label class="inline-flex items-center gap-1.5 text-xs px-3 py-1.5 rounded-full border cursor-pointer transition
                    {{ request('uni_assist') === '1' ? 'bg-purple-600 text-white border-purple-600' : 'bg-purple-50 text-purple-700 border-purple-200 hover:bg-purple-100' }}">
                    <input type="checkbox" name="uni_assist" value="1" @checked(request('uni_assist') === '1') class="hidden" onchange="this.form.submit()">
                    {{ __('Uni-Assist member') }}
                </label>

                <span class="text-xs text-gray-400 mx-2">·</span>
                <span class="text-xs text-gray-500">{{ __('Size:') }}</span>

                @php
                    $sizeChips = [
                        'small'  => [__('Small'), __('< 5K students'),
                            'bg-amber-600 text-white border-amber-600',
                            'bg-amber-50 text-amber-700 border-amber-200 hover:bg-amber-100'],
                        'medium' => [__('Medium'), '5K–20K',
                            'bg-orange-600 text-white border-orange-600',
                            'bg-orange-50 text-orange-700 border-orange-200 hover:bg-orange-100'],
                        'large'  => [__('Large'), '> 20K',
                            'bg-rose-600 text-white border-rose-600',
                            'bg-rose-50 text-rose-700 border-rose-200 hover:bg-rose-100'],
                    ];
                @endphp

                @foreach ($sizeChips as $val => [$lbl, $rng, $activeCls, $idleCls])
                    <a href="{{ request()->fullUrlWithQuery(['size' => request('size') === $val ? null : $val]) }}"
                       class="inline-flex items-center gap-1 text-xs px-3 py-1.5 rounded-full border transition
                              {{ request('size') === $val ? $activeCls : $idleCls }}">
                        {{ $lbl }} <span class="opacity-75">({{ $rng }})</span>
                    </a>
                @endforeach
            </div>
        </form>
    </div>
</section>

{{-- ─────────────── CURATED CATEGORIES ─────────────── --}}
@if (!empty($collections))
<section class="bg-white py-8 border-b border-gray-200">
    <div class="max-w-[1400px] mx-auto px-4">
        <h2 class="text-lg font-bold text-gray-900 mb-4">{{ __('Browse by category') }}</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($collections as $cSlug => $c)
                <a href="{{ route('universities.collection', $cSlug) }}"
                   class="group flex items-start gap-3 bg-gray-50 border border-gray-200 rounded-xl p-4 hover:border-primary-500 hover:bg-white hover:shadow-md transition">
                    <span class="text-3xl shrink-0" aria-hidden="true">{{ $c['icon'] }}</span>
                    <span class="min-w-0">
                        <span class="block font-bold text-gray-900 group-hover:text-primary-600 leading-snug">{{ __($c['title']) }}</span>
                        <span class="block text-xs text-gray-500 mt-1">{{ count($c['uni_slugs']) }} {{ __('universities') }} →</span>
                    </span>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ─────────────── RESULTS ─────────────── --}}
<section class="bg-gray-50 py-10">
    <div class="max-w-[1400px] mx-auto px-4">
        <div id="async-filter-results" data-async-filter aria-live="polite" aria-busy="false">
            @include('universities._grid')
        </div>
    </div>
</section>

@endsection
