@extends('layouts.app')

@section('title', __('This programme is no longer listed') . ' — ' . brand('name'))

@push('meta')
    <meta name="robots" content="noindex">
@endpush

@section('content')

<section class="min-h-[70vh] bg-gradient-to-br from-primary-50 via-white to-accent-50 flex items-center">
    <div class="max-w-3xl mx-auto px-4 py-16 text-center">

        <h1 class="text-2xl md:text-4xl font-extrabold text-gray-900 mb-3">{{ __('This programme is no longer listed') }}</h1>
        <p class="text-gray-600 text-lg max-w-xl mx-auto mb-8">
            {{ __('It was removed because it is not a degree programme or its record was outdated. You can search our current programmes instead:') }}
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

        <a href="{{ route('programs.index') }}"
           class="inline-flex items-center gap-2 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-full px-6 py-3 transition">
            <x-svg-icon name="book-open" class="w-5 h-5" /> {{ __('Browse all programmes') }}
        </a>
    </div>
</section>

@endsection
