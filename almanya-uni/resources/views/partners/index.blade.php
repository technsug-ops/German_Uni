@extends('layouts.app')

@section('title', $title . ' — ' . brand('name'))

<x-seo :title="$title" :description="$intro" />

@section('content')

<section class="bg-gradient-to-br from-indigo-700 via-indigo-600 to-violet-500 text-white">
    <div class="max-w-[1200px] mx-auto px-4 py-12 md:py-14">
        <nav class="text-sm text-indigo-100 mb-3">
            <a href="/" class="hover:text-white">{{ __('Home') }}</a>
            <span class="mx-2 opacity-60">›</span>
            <span class="text-white">{{ $title }}</span>
        </nav>
        <h1 class="text-3xl md:text-4xl font-extrabold leading-tight drop-shadow mb-3">{{ $title }}</h1>
        <p class="text-lg text-indigo-100 max-w-3xl">{{ $intro }}</p>
    </div>
</section>

<div class="max-w-[1200px] mx-auto px-4 py-8">
    {{-- Tip filtreleri --}}
    <div class="flex flex-wrap gap-2 mb-6">
        <a href="{{ route($indexRoute) }}"
           class="px-3 py-1.5 rounded-lg text-sm font-medium border {{ ! $activeType ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-700 border-gray-200 hover:bg-gray-50' }}">
            {{ __('All') }}
        </a>
        @foreach ($types as $key => $meta)
            <a href="{{ route($indexRoute, ['type' => $key]) }}"
               class="px-3 py-1.5 rounded-lg text-sm font-medium border {{ $activeType === $key ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-700 border-gray-200 hover:bg-gray-50' }}">
                {{ $meta['label'] }}
            </a>
        @endforeach
    </div>

    @if ($items->isEmpty())
        <div class="bg-indigo-50 border border-indigo-100 rounded-2xl p-10 text-center">
            <p class="text-gray-700 font-semibold">{{ __('Listings are coming soon.') }}</p>
            <p class="text-gray-500 text-sm mt-1">{{ __('We are curating trusted providers. Check back shortly.') }}</p>
        </div>
    @else
        @foreach ($grouped as $type => $group)
            <h2 class="text-lg font-bold text-gray-800 mt-8 mb-3 flex items-center gap-2">
                {{ $types[$type]['label'] ?? $type }}
                <span class="text-sm font-normal text-gray-400">({{ $group->count() }})</span>
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($group as $item)
                    @include('partners._card', ['item' => $item, 'kind' => $kind, 'showRoute' => $showRoute])
                @endforeach
            </div>
        @endforeach
    @endif

    <p class="text-xs text-gray-400 mt-10">
        {{ __('Listed providers are independent. We may earn a commission from some partners — this never changes our editorial selection.') }}
    </p>
</div>

@endsection
