@extends('layouts.app')

@section('title', __('Events — Webinars, Workshops, Meetups') . ' — ' . brand('name'))

<x-seo
    :title="__('Germany University Events')"
    :description="__('AlmanyaUni live streams, webinars, workshops and meetups. Online + offline events for international students.')"
/>

@section('content')

{{-- HERO --}}
<section class="bg-gradient-to-br from-indigo-700 via-indigo-600 to-purple-600 text-white">
    <div class="max-w-[1400px] mx-auto px-4 py-12 md:py-16">
        <nav class="text-sm text-indigo-100 mb-3">
            <a href="/" class="hover:text-white">{{ __('Home') }}</a>
            <span class="mx-2 opacity-60">›</span>
            <span class="text-white">{{ __('Events') }}</span>
        </nav>
        <h1 class="text-3xl md:text-5xl font-extrabold leading-tight drop-shadow mb-3">📅 {{ __('Events') }}</h1>
        <p class="text-lg md:text-xl text-indigo-100 max-w-3xl">
            {{ __('Live webinars, workshops, university open days, panels and student meetups. All free (unless otherwise noted).') }}
        </p>
    </div>
</section>

{{-- Category + Type filter chips --}}
<section class="bg-white border-b border-gray-200 sticky top-0 z-10">
    <div class="max-w-[1400px] mx-auto px-4 py-3 space-y-2">
        {{-- Kategoriler (7) --}}
        <div class="flex items-center flex-wrap gap-2">
            <span class="text-xs text-gray-500 mr-1">{{ __('Category:') }}</span>
            <a href="{{ route('events.index') }}"
               class="text-xs px-3 py-1.5 rounded-full border transition
                      {{ ! $category && ! $type ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-gray-50 text-gray-600 border-gray-200 hover:bg-gray-100' }}">
                {{ __('All') }}
            </a>
            @foreach ($categories as $cat)
                <a href="{{ route('events.index', ['category' => $cat->slug]) }}"
                   class="text-xs px-3 py-1.5 rounded-full border transition
                          {{ $category === $cat->slug ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-gray-50 text-gray-700 border-gray-200 hover:bg-gray-100' }}">
                    {{ $cat->icon }} {{ $cat->name_tr }}
                </a>
            @endforeach
        </div>

        {{-- Aktif kategorideki tipler --}}
        @if ($category)
            @php
                $typesInCat = collect(\App\Models\Event::TYPES)
                    ->filter(fn ($m) => ($m['category'] ?? null) === $category);
            @endphp
            @if ($typesInCat->isNotEmpty())
                <div class="flex items-center flex-wrap gap-2 pt-1 border-t border-gray-100">
                    <span class="text-xs text-gray-400 mr-1">{{ __('Type:') }}</span>
                    @foreach ($typesInCat as $key => $meta)
                        <a href="{{ route('events.index', ['type' => $key]) }}"
                           class="text-[11px] px-2.5 py-1 rounded-full border transition
                                  {{ $type === $key ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-indigo-50 text-indigo-700 border-indigo-200 hover:bg-indigo-100' }}">
                            {{ $meta['emoji'] }} {{ $meta['label'] }}
                        </a>
                    @endforeach
                </div>
            @endif
        @endif
    </div>
</section>

<div class="max-w-[1400px] mx-auto px-4 py-10">

    {{-- LIVE NOW --}}
    @if ($live->isNotEmpty())
        <section class="mb-10">
            <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-red-500 text-white text-xs font-bold uppercase tracking-wider">
                    <span class="w-2 h-2 rounded-full bg-white animate-pulse"></span>
                    {{ __('Live') }}
                </span>
                {{ __('On air now') }}
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach ($live as $e)
                    @include('events._card', ['event' => $e, 'isLive' => true])
                @endforeach
            </div>
        </section>
    @endif

    {{-- UPCOMING --}}
    <section class="mb-10">
        <h2 class="text-2xl font-bold text-gray-900 mb-4">🔜 {{ __('Upcoming events (:count)', ['count' => $upcoming->count()]) }}</h2>
        @if ($upcoming->isEmpty())
            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-8 text-center">
                <p class="text-yellow-900">{{ __('No upcoming events at the moment. Subscribe to our newsletter to get announcements first.') }}</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($upcoming as $e)
                    @include('events._card', ['event' => $e, 'isLive' => false])
                @endforeach
            </div>
        @endif
    </section>

    {{-- PAST --}}
    @if ($past->isNotEmpty())
        <section class="mb-10">
            <h2 class="text-2xl font-bold text-gray-700 mb-4">📚 {{ __('Past events') }}</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 opacity-75">
                @foreach ($past as $e)
                    @include('events._card', ['event' => $e, 'isLive' => false, 'isPast' => true])
                @endforeach
            </div>
        </section>
    @endif
</div>
@endsection
