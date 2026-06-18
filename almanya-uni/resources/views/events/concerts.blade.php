@extends('layouts.app')

@section('title', __('Germany Concerts & Culture Calendar') . ' — ' . brand('name'))

<x-seo
    :title="__('Germany Concerts & Culture Calendar')"
    :description="__('Concerts, theatre, comedy and festivals across Germany — Berlin, Munich, Cologne, Frankfurt, Hamburg and more. Browse by city.')"
/>

@section('content')

{{-- HERO --}}
<section class="bg-gradient-to-br from-rose-600 via-red-600 to-pink-600 text-white">
    <div class="max-w-[1400px] mx-auto px-4 py-10 md:py-14">
        <nav class="text-sm text-rose-100 mb-3">
            <a href="/" class="hover:text-white">{{ __('Home') }}</a>
            <span class="mx-2 opacity-60">›</span>
            <a href="{{ route('events.index') }}" class="hover:text-white">{{ __('Events') }}</a>
            <span class="mx-2 opacity-60">›</span>
            <span class="text-white">{{ __('Concerts & Culture') }}</span>
        </nav>
        <h1 class="text-3xl md:text-5xl font-extrabold leading-tight drop-shadow mb-3 flex items-center gap-3">
            <x-svg-icon name="calendar" class="w-9 h-9 md:w-11 md:h-11" /> {{ __('Germany Concerts & Culture Calendar') }}
        </h1>
        <p class="text-lg text-rose-100 max-w-3xl">
            {{ __('Concerts, theatre, comedy and festivals across Germany. Pick your city below. Tickets are sold on the official organiser sites.') }}
        </p>
    </div>
</section>

<div class="max-w-[1400px] mx-auto px-4 py-8 space-y-10">

    {{-- 🔥 ÖNE ÇIKANLAR --}}
    @if ($highlights->isNotEmpty())
        <section>
            <h2 class="text-xl font-bold text-gray-900 mb-4 inline-flex items-center gap-2">🔥 {{ __('Highlights') }}</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($highlights as $e)
                    <a href="{{ route('events.show', $e->slug) }}"
                       class="group block rounded-2xl overflow-hidden border border-gray-200 hover:shadow-lg transition">
                        <div class="relative p-5 h-28 flex flex-col justify-between text-white"
                             style="background: linear-gradient(135deg, {{ $e->type_color }}, rgba(0,0,0,.5));">
                            <div class="flex items-center justify-between gap-2">
                                <span class="inline-flex items-center gap-1 text-[11px] font-bold uppercase tracking-wide bg-white/20 backdrop-blur px-2 py-0.5 rounded-full">
                                    {!! e_icon($e->type_emoji, 'w-3 h-3') !!} {{ $e->type_label }}
                                </span>
                                <span class="text-[11px] font-semibold bg-black/25 px-2 py-0.5 rounded-full whitespace-nowrap">{{ $e->starts_at->translatedFormat('d M') }}</span>
                            </div>
                            <div class="font-extrabold text-lg leading-tight line-clamp-2 drop-shadow">{{ $e->title }}</div>
                        </div>
                        <div class="bg-white px-4 py-3 flex items-center justify-between text-sm">
                            <span class="inline-flex items-center gap-1 text-gray-600 truncate">
                                <x-svg-icon name="map-pin" class="w-4 h-4 text-rose-500" /> {{ $e->location_city ?: '—' }}
                            </span>
                            <span class="font-semibold text-rose-600 inline-flex items-center gap-1 group-hover:translate-x-0.5 transition">{{ __('Details') }} →</span>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- 📍 ŞEHRE GÖRE GEZ — fotoğraflı şehir filtresi --}}
    @if ($cities->isNotEmpty())
        <section>
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-xl font-bold text-gray-900 inline-flex items-center gap-2"><x-svg-icon name="map-pin" class="w-5 h-5 text-rose-600" /> {{ __('Browse by city') }}</h2>
                @if ($activeCity)
                    <a href="{{ route('events.concerts', array_filter(['type' => $type, 'when' => $when])) }}" class="text-sm text-rose-600 hover:underline">{{ __('All Germany') }} ✕</a>
                @endif
            </div>
            @php $badgePalette = ['bg-pink-500', 'bg-red-500', 'bg-cyan-600', 'bg-indigo-600', 'bg-rose-600']; @endphp
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
                @foreach ($cities as $city)
                    @php $badge = $badgePalette[$loop->index % count($badgePalette)]; @endphp
                    <a href="{{ route('events.concerts', array_filter(['city' => $city->slug, 'type' => $type, 'when' => $when])) }}"
                       class="group">
                        <div class="relative h-44 rounded-2xl overflow-hidden ring-2 transition
                                    {{ $activeCity?->slug === $city->slug ? 'ring-rose-500' : 'ring-transparent hover:ring-rose-300' }}">
                            @if ($city->image_url)
                                <img src="{{ $city->image_url }}" alt="{{ $city->name }}" loading="lazy"
                                     class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition duration-300">
                            @else
                                <div class="absolute inset-0 bg-gradient-to-br from-gray-600 to-gray-800"></div>
                            @endif
                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
                            <div class="absolute inset-x-0 bottom-0 p-4">
                                <div class="font-extrabold text-white text-xl uppercase tracking-wide leading-none mb-2 drop-shadow">{{ $city->name }}</div>
                                <span class="inline-block text-[11px] font-bold uppercase px-2.5 py-1 rounded text-white {{ $badge }}">{{ __('Find events') }}</span>
                            </div>
                        </div>
                        <div class="mt-1.5 text-sm text-gray-500">{{ __(':count events', ['count' => $cityCounts[$city->id] ?? 0]) }}</div>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- FİLTRELER: gün + tip --}}
    <section class="bg-white border border-gray-200 rounded-xl p-4 space-y-3">
        {{-- Gün filtresi --}}
        @php $dayFilters = ['' => __('All'), 'today' => __('Today'), 'weekend' => __('This weekend'), 'week' => __('This week'), 'month' => __('This month')]; @endphp
        <div class="flex items-center flex-wrap gap-2">
            <span class="text-xs font-semibold text-gray-400 mr-1 w-10">{{ __('Day:') }}</span>
            @foreach ($dayFilters as $key => $label)
                <a href="{{ route('events.concerts', array_filter(['city' => $activeCity?->slug, 'type' => $type, 'when' => $key])) }}"
                   class="text-xs px-3 py-1.5 rounded-full border transition
                          {{ ($when ?? '') === $key ? 'bg-rose-600 text-white border-rose-600' : 'bg-gray-50 text-gray-600 border-gray-200 hover:bg-gray-100' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        {{-- Tip filtresi --}}
        @if (! empty($typeOptions))
            <div class="flex items-center flex-wrap gap-2 pt-3 border-t border-gray-100">
                <span class="text-xs font-semibold text-gray-400 mr-1 w-10">{{ __('Type:') }}</span>
                <a href="{{ route('events.concerts', array_filter(['city' => $activeCity?->slug, 'when' => $when])) }}"
                   class="text-xs px-3 py-1.5 rounded-full border transition
                          {{ ! $type ? 'bg-rose-600 text-white border-rose-600' : 'bg-rose-50 text-rose-700 border-rose-200 hover:bg-rose-100' }}">
                    {{ __('All') }}
                </a>
                @foreach ($typeOptions as $key => $meta)
                    <a href="{{ route('events.concerts', array_filter(['type' => $key, 'city' => $activeCity?->slug, 'when' => $when])) }}"
                       class="inline-flex items-center gap-1 text-xs px-3 py-1.5 rounded-full border transition
                              {{ $type === $key ? 'bg-rose-600 text-white border-rose-600' : 'bg-rose-50 text-rose-700 border-rose-200 hover:bg-rose-100' }}">
                        {!! e_icon($meta['emoji'] ?? '', 'w-3 h-3') !!} {{ $meta['label'] }}
                    </a>
                @endforeach
            </div>
        @endif
    </section>

    {{-- KARTLAR --}}
    <section>
        <h2 class="text-xl font-bold text-gray-900 mb-4 inline-flex items-center gap-2">
            <x-svg-icon name="arrow-right" class="w-5 h-5 text-rose-600" />
            {{ $activeCity
                ? __(':city — concerts & events', ['city' => $activeCity->name])
                : __('Upcoming concerts & events (:count)', ['count' => $events->total()]) }}
        </h2>

        @if ($events->isEmpty())
            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-8 text-center">
                <p class="text-yellow-900">{{ __('No events found for this selection. Try another city or clear the filter.') }}</p>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($events as $e)
                    @include('events._card', ['event' => $e, 'isLive' => false])
                @endforeach
            </div>
            <div class="mt-6">{{ $events->links() }}</div>
        @endif
    </section>

    {{-- 🔔 BANA HABER VER --}}
    <section class="max-w-2xl mx-auto">
        @include('events._alert-subscribe', ['alertCities' => $alertCities])
    </section>

    {{-- Kaynak notu --}}
    <p class="text-xs text-gray-400 text-center max-w-2xl mx-auto">
        {{ __('Event data is aggregated from public sources (e.g. Ticketmaster). Always confirm date, venue and tickets on the official organiser page.') }}
    </p>
</div>
@endsection
