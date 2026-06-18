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

{{-- City tabs (vasistdas tarzı) --}}
<section class="bg-white border-b border-gray-200 sticky top-0 z-10">
    <div class="max-w-[1400px] mx-auto px-4 py-3 space-y-2">
        <div class="flex items-center flex-wrap gap-2">
            <span class="text-xs text-gray-500 mr-1">{{ __('City:') }}</span>
            <a href="{{ route('events.concerts', array_filter(['type' => $type])) }}"
               class="text-xs px-3 py-1.5 rounded-full border transition
                      {{ ! $activeCity ? 'bg-rose-600 text-white border-rose-600' : 'bg-gray-50 text-gray-600 border-gray-200 hover:bg-gray-100' }}">
                {{ __('All Germany') }}
            </a>
            @foreach ($cities as $city)
                <a href="{{ route('events.concerts', array_filter(['city' => $city->slug, 'type' => $type])) }}"
                   class="text-xs px-3 py-1.5 rounded-full border transition
                          {{ $activeCity?->slug === $city->slug ? 'bg-rose-600 text-white border-rose-600' : 'bg-gray-50 text-gray-700 border-gray-200 hover:bg-gray-100' }}">
                    {{ $city->name }}
                </a>
            @endforeach
        </div>

        {{-- Tip filtresi (Konser / Tiyatro / Komedi …) --}}
        @if (! empty($typeOptions))
            <div class="flex items-center flex-wrap gap-2 pt-1 border-t border-gray-100">
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
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($events as $e)
                @include('events._card', ['event' => $e, 'isLive' => false])
            @endforeach
        </div>

        <div class="mt-8">
            {{ $events->links() }}
        </div>
    @endif

    {{-- Kaynak notu --}}
    <p class="mt-8 text-xs text-gray-400 text-center max-w-2xl mx-auto">
        {{ __('Event data is aggregated from public sources (e.g. Ticketmaster). Always confirm date, venue and tickets on the official organiser page.') }}
    </p>
</div>
@endsection
