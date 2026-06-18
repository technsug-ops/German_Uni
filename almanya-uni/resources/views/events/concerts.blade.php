@extends('layouts.app')

@section('title', __('Germany Concerts & Culture Calendar') . ' — ' . brand('name'))

<x-seo
    :title="__('Germany Concerts & Culture Calendar')"
    :description="__('Concerts, theatre, comedy and festivals across Germany — Berlin, Munich, Cologne, Frankfurt, Hamburg and more. Browse by city.')"
/>

@section('content')

{{-- HERO --}}
<section class="bg-gradient-to-br from-rose-600 via-red-600 to-pink-600 text-white">
    <div class="max-w-[1400px] mx-auto px-4 py-12 md:py-16">
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
        <p class="text-lg md:text-xl text-rose-100 max-w-3xl mb-5">
            {{ __('Concerts, theatre, comedy and festivals across Germany. Pick your city below. Tickets are sold on the official organiser sites.') }}
        </p>
        <a href="{{ route('events.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-white/15 backdrop-blur ring-1 ring-white/25 hover:bg-white/25 transition text-sm font-semibold">
            <x-svg-icon name="users" class="w-4 h-4" /> {{ __('Looking for our own student events? See Community Events →') }}
        </a>
    </div>
</section>

{{-- Şehrine göre keşfet — fotoğraflı şehir kartları (eventim tarzı) --}}
<section class="bg-white border-b border-gray-200">
    <div class="max-w-[1400px] mx-auto px-4 py-4">
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-sm font-bold text-gray-700 inline-flex items-center gap-1.5"><x-svg-icon name="map-pin" class="w-4 h-4 text-rose-600" /> {{ __('Browse by city') }}</h2>
            @if ($activeCity)
                <a href="{{ route('events.concerts', array_filter(['type' => $type])) }}" class="text-xs text-rose-600 hover:underline">{{ __('All Germany') }} ✕</a>
            @endif
        </div>

        @php $badgePalette = ['bg-pink-500', 'bg-red-500', 'bg-cyan-600', 'bg-indigo-600', 'bg-rose-600', 'bg-emerald-600', 'bg-fuchsia-600']; @endphp
        <div class="flex gap-4 overflow-x-auto pb-2 -mx-1 px-1 snap-x">
            {{-- Tüm Almanya --}}
            <a href="{{ route('events.concerts', array_filter(['type' => $type])) }}"
               class="group shrink-0 snap-start w-44">
                <div class="relative h-44 rounded-xl overflow-hidden ring-2 transition
                            {{ ! $activeCity ? 'ring-rose-500' : 'ring-transparent hover:ring-rose-300' }}">
                    <div class="absolute inset-0 bg-gradient-to-br from-rose-600 to-pink-600"></div>
                    <div class="absolute top-3 left-3 text-2xl">🇩🇪</div>
                    <div class="absolute inset-x-0 bottom-0 p-3">
                        <div class="font-extrabold text-white text-lg uppercase tracking-wide leading-none mb-2 drop-shadow">{{ __('All Germany') }}</div>
                        <span class="inline-block text-[11px] font-bold uppercase px-2 py-1 rounded bg-white text-rose-700">{{ __('Find events') }}</span>
                    </div>
                </div>
                <div class="mt-1.5 text-xs text-gray-500">{{ __(':count events', ['count' => $events->total()]) }}</div>
            </a>
            @foreach ($cities as $city)
                @php $badge = $badgePalette[$loop->index % count($badgePalette)]; @endphp
                <a href="{{ route('events.concerts', array_filter(['city' => $city->slug, 'type' => $type])) }}"
                   class="group shrink-0 snap-start w-44">
                    <div class="relative h-44 rounded-xl overflow-hidden ring-2 transition
                                {{ $activeCity?->slug === $city->slug ? 'ring-rose-500' : 'ring-transparent hover:ring-rose-300' }}">
                        @if ($city->image_url)
                            <img src="{{ $city->image_url }}" alt="{{ $city->name }}" loading="lazy"
                                 class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition duration-300">
                        @else
                            <div class="absolute inset-0 bg-gradient-to-br from-gray-600 to-gray-800"></div>
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
                        <div class="absolute inset-x-0 bottom-0 p-3">
                            <div class="font-extrabold text-white text-lg uppercase tracking-wide leading-none mb-2 drop-shadow">{{ $city->name }}</div>
                            <span class="inline-block text-[11px] font-bold uppercase px-2 py-1 rounded text-white {{ $badge }}">{{ __('Find events') }}</span>
                        </div>
                    </div>
                    <div class="mt-1.5 text-xs text-gray-500">{{ __(':count events', ['count' => $cityCounts[$city->id] ?? 0]) }}</div>
                </a>
            @endforeach
        </div>

        {{-- Tip filtresi (Konser / Tiyatro / Komedi …) --}}
        @if (! empty($typeOptions))
            <div class="flex items-center flex-wrap gap-2 mt-3 pt-3 border-t border-gray-100">
                <span class="text-xs text-gray-400 mr-1">{{ __('Type:') }}</span>
                <a href="{{ route('events.concerts', array_filter(['city' => $activeCity?->slug])) }}"
                   class="text-[11px] px-2.5 py-1 rounded-full border transition
                          {{ ! $type ? 'bg-rose-600 text-white border-rose-600' : 'bg-rose-50 text-rose-700 border-rose-200 hover:bg-rose-100' }}">
                    {{ __('All') }}
                </a>
                @foreach ($typeOptions as $key => $meta)
                    <a href="{{ route('events.concerts', array_filter(['type' => $key, 'city' => $activeCity?->slug])) }}"
                       class="inline-flex items-center gap-1 text-[11px] px-2.5 py-1 rounded-full border transition
                              {{ $type === $key ? 'bg-rose-600 text-white border-rose-600' : 'bg-rose-50 text-rose-700 border-rose-200 hover:bg-rose-100' }}">
                        {!! e_icon($meta['emoji'] ?? '', 'w-3 h-3') !!} {{ $meta['label'] }}
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</section>

<div class="max-w-[1400px] mx-auto px-4 py-10">

    <div class="flex items-center justify-between mb-4">
        <h2 class="text-2xl font-bold text-gray-900 inline-flex items-center gap-2">
            <x-svg-icon name="arrow-right" class="w-6 h-6 text-rose-600" />
            {{ $activeCity
                ? __(':city — concerts & events', ['city' => $activeCity->name])
                : __('Upcoming concerts & events (:count)', ['count' => $events->total()]) }}
        </h2>
    </div>

    @if ($events->isEmpty())
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-8 text-center">
            <p class="text-yellow-900">{{ __('No events found for this selection. Try another city or clear the filter.') }}</p>
        </div>
    @else
        {{-- Kompakt liste — güne göre gruplu (takvim görünümü) --}}
        <div class="bg-white rounded-xl border border-gray-200 divide-y divide-gray-100 overflow-hidden">
            @php $grouped = $events->getCollection()->groupBy(fn ($e) => $e->starts_at->format('Y-m-d')); @endphp
            @foreach ($grouped as $day => $dayEvents)
                <div class="bg-gray-50 px-4 py-2 text-xs font-bold text-gray-500 uppercase tracking-wide">
                    {{ \Illuminate\Support\Carbon::parse($day)->translatedFormat('d F Y · l') }}
                </div>
                @foreach ($dayEvents as $e)
                    <a href="{{ route('events.show', $e->slug) }}"
                       class="group flex items-center gap-3 px-4 py-3 hover:bg-rose-50 transition">
                        <span class="shrink-0 w-1.5 h-10 rounded-full" style="background: {{ $e->type_color }};"></span>
                        <div class="shrink-0 w-12 text-center">
                            <div class="text-sm font-semibold text-gray-700">{{ $e->starts_at->format('H:i') }}</div>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="font-semibold text-gray-900 group-hover:text-rose-700 truncate">{{ $e->title }}</div>
                            <div class="text-xs text-gray-500 flex items-center gap-2 truncate">
                                <span class="inline-flex items-center gap-1">{!! e_icon($e->type_emoji, 'w-3 h-3') !!} {{ $e->type_label }}</span>
                                @if ($e->location_city)
                                    <span class="text-gray-300">·</span>
                                    <span class="inline-flex items-center gap-1"><x-svg-icon name="map-pin" class="w-3 h-3 text-gray-400" /> {{ $e->location_city }}</span>
                                @endif
                            </div>
                        </div>
                        <x-svg-icon name="arrow-right" class="w-4 h-4 text-gray-300 group-hover:text-rose-500 shrink-0" />
                    </a>
                @endforeach
            @endforeach
        </div>

        <div class="mt-6">
            {{ $events->links() }}
        </div>
    @endif

    {{-- Kaynak notu --}}
    <p class="mt-8 text-xs text-gray-400 text-center max-w-2xl mx-auto">
        {{ __('Event data is aggregated from public sources (e.g. Ticketmaster). Always confirm date, venue and tickets on the official organiser page.') }}
    </p>
</div>
@endsection
