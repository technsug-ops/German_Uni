@extends('layouts.app')

@php
    // abort(410, 'university') → kurum metni (SEO Recovery P0-4); varsayılan program metni değişmedi.
    $goneUniversity = isset($exception) && $exception->getMessage() === 'university';
    $goneTitle = $goneUniversity ? __('This university is no longer listed') : __('This programme is no longer listed');
@endphp

@section('title', $goneTitle . ' — ' . brand('name'))

@push('meta')
    <meta name="robots" content="noindex">
@endpush

@section('content')

<section class="min-h-[70vh] bg-gradient-to-br from-primary-50 via-white to-accent-50 flex items-center">
    <div class="max-w-3xl mx-auto px-4 py-16 text-center">

        <h1 class="text-2xl md:text-4xl font-extrabold text-gray-900 mb-3">{{ $goneTitle }}</h1>
        <p class="text-gray-600 text-lg max-w-xl mx-auto mb-8">
            @if ($goneUniversity)
                {{ __('This institution was removed from our database and has no current equivalent on this site. You can search our current universities instead:') }}
            @else
                {{ __('It was removed because it is not a degree programme or its record was outdated. You can search our current programmes instead:') }}
            @endif
        </p>

        <form action="{{ route('search.index') }}" method="GET" class="max-w-xl mx-auto mb-8">
            <div class="relative">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"><x-svg-icon name="search" class="w-5 h-5" /></span>
                <input type="text" name="q"
                       placeholder="{{ __('Search universities, cities, programs...') }}"
                       autofocus
                       class="w-full pl-12 pr-4 py-3 rounded-full border border-gray-300 shadow-md focus:border-primary-500 focus:ring-2 focus:ring-primary-100 focus:outline-none">
            </div>
        </form>

        @if ($goneUniversity)
            <a href="{{ route('universities.index') }}"
               class="inline-flex items-center gap-2 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-full px-6 py-3 transition">
                <x-svg-icon name="building-office" class="w-5 h-5" /> {{ __('All Universities') }}
            </a>
        @else
        <a href="{{ route('programs.index') }}"
           class="inline-flex items-center gap-2 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-full px-6 py-3 transition">
            <x-svg-icon name="book-open" class="w-5 h-5" /> {{ __('Browse all programmes') }}
        </a>
        @endif
    </div>
</section>

@endsection
