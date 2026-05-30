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
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/></svg>
                <span><strong class="text-lg">{{ $cities->total() }}</strong> {{ __('cities') }}</span>
            </div>
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/15 backdrop-blur ring-1 ring-white/20">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503 3.498 4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 0 0-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0Z"/></svg>
                <span><strong class="text-lg">{{ $states->count() }}</strong> {{ __('states') }}</span>
            </div>
        </div>
    </div>
</section>

{{-- ─────────────── FILTER BAR ─────────────── --}}
<section class="bg-white border-b border-gray-200 shadow-sm">
    <div class="max-w-[1400px] mx-auto px-4 py-4">
        <form method="GET" action="{{ route('cities.index') }}" class="space-y-3"
              data-async-filter-form="#async-filter-results"
              data-no-loading>
            <div class="grid grid-cols-1 md:grid-cols-[1fr_180px_auto_auto] gap-3">
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
                    </span>
                    <input type="text" name="q" value="{{ $filters['q'] ?? '' }}"
                           placeholder="{{ __('Search city (Berlin, München, Hamburg…)') }}"
                           class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-300 bg-white text-gray-900 focus:ring-2 focus:ring-primary-500 focus:border-primary-500"/>
                </div>
                {{-- State filter rendered as upper chip row below; hidden input keeps the
                     value in this form so async filter still posts it. --}}
                <input type="hidden" name="state" value="{{ $filters['state'] ?? '' }}"/>
                <select name="sort" class="px-4 py-2.5 rounded-lg border border-gray-300 bg-white text-gray-900">
                    <option value="uni_count" @selected(($filters['sort'] ?? 'uni_count') === 'uni_count')>{{ __('University count (most → least)') }}</option>
                    <option value="population" @selected(($filters['sort'] ?? '') === 'population')>{{ __('Population (most → least)') }}</option>
                    <option value="name" @selected(($filters['sort'] ?? '') === 'name')>{{ __('Alphabetical') }}</option>
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

            {{-- Hızlı chip filtreleri (state filtresi sol sidebar'da, aşağıda) --}}
            <div class="flex items-center flex-wrap gap-2 pt-2 border-t border-gray-100">
                <span class="text-xs text-gray-500 mr-1">{{ __('City size:') }}</span>

                @php
                    $sizeChips = [
                        'small'  => [__('Small'), '< 200K',
                            'bg-amber-600 text-white border-amber-600',
                            'bg-amber-50 text-amber-700 border-amber-200 hover:bg-amber-100'],
                        'medium' => [__('Medium'), '200K-1M',
                            'bg-blue-600 text-white border-blue-600',
                            'bg-blue-50 text-blue-700 border-blue-200 hover:bg-blue-100'],
                        'large'  => [__('Metropolis'), '> 1M',
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
                        'few' => [__('1-2 unis'),
                            'bg-emerald-600 text-white border-emerald-600',
                            'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100'],
                        'mid' => [__('3-5 unis'),
                            'bg-teal-600 text-white border-teal-600',
                            'bg-teal-50 text-teal-700 border-teal-200 hover:bg-teal-100'],
                        'many' => [__('6+ unis'),
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
        <div class="lg:grid lg:grid-cols-[240px_1fr] lg:gap-6">

            {{-- ── Sol sidebar: eyaletler ──
                 Why not <details>? Its UA rendering hides non-summary children
                 via a shadow slot, which CSS (even lg:!block !important) can't
                 override — so on desktop the sidebar disappeared when no state
                 was active. Checkbox-peer is the CSS-only alternative that
                 plays nice with Tailwind responsive variants. --}}
            <aside class="mb-6 lg:mb-0">
                <input type="checkbox" id="state-filter-toggle"
                       class="peer sr-only"
                       @if(! empty($filters['state'] ?? null)) checked @endif>
                <label for="state-filter-toggle"
                       class="lg:hidden cursor-pointer select-none flex items-center justify-between gap-2 px-4 py-3 bg-white rounded-lg border border-gray-200 text-sm font-semibold text-gray-900 mb-2">
                    <span class="inline-flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503 3.498 4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 0 0-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0Z"/></svg>
                        {{ __('Filter by state') }}
                    </span>
                    <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>
                </label>
                <nav aria-label="{{ __('Filter by state') }}"
                     class="hidden peer-checked:block lg:!block lg:sticky lg:top-4 bg-white rounded-lg border border-gray-200 overflow-hidden">
                        <div class="px-4 py-3 border-b border-gray-100 text-xs font-semibold text-gray-500 uppercase tracking-wide hidden lg:flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503 3.498 4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 0 0-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0Z"/></svg>
                            {{ __('States') }}
                        </div>
                        <ul class="divide-y divide-gray-100 max-h-[70vh] overflow-y-auto">
                            @php $allActive = empty($filters['state'] ?? null); @endphp
                            <li>
                                <a href="{{ request()->fullUrlWithQuery(['state' => null]) }}"
                                   class="flex items-center justify-between px-4 py-2 text-sm transition
                                          {{ $allActive
                                              ? 'bg-primary-50 text-primary-700 font-semibold border-l-4 border-primary-600'
                                              : 'text-gray-700 hover:bg-gray-50 border-l-4 border-transparent' }}">
                                    <span>{{ __('All states') }}</span>
                                </a>
                            </li>
                            @foreach($states as $st)
                                @php $isActive = ($filters['state'] ?? '') === $st->slug; @endphp
                                <li>
                                    <a href="{{ request()->fullUrlWithQuery(['state' => $isActive ? null : $st->slug]) }}"
                                       class="flex items-center justify-between gap-2 px-4 py-2 text-sm transition
                                              {{ $isActive
                                                  ? 'bg-primary-50 text-primary-700 font-semibold border-l-4 border-primary-600'
                                                  : 'text-gray-700 hover:bg-gray-50 border-l-4 border-transparent' }}">
                                        <span class="truncate">{{ $st->name }}</span>
                                        @if($st->cities_count > 0)
                                            <span class="text-xs text-gray-400 shrink-0">{{ $st->cities_count }}</span>
                                        @endif
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </nav>
                </details>
            </aside>

            {{-- ── Sağ ana: sonuçlar ── --}}
            <div id="async-filter-results" data-async-filter aria-live="polite" aria-busy="false">
                @include('cities._grid')
            </div>
        </div>
    </div>
</section>

@endsection
