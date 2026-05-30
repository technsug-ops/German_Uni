@extends('layouts.app')

@section('title', __('404 — Page not found') . '  — ' . brand('name'))

@push('meta')
    <meta name="robots" content="noindex">
@endpush

@section('content')

<section class="min-h-[70vh] bg-gradient-to-br from-primary-50 via-white to-accent-50 flex items-center">
    <div class="max-w-3xl mx-auto px-4 py-16 text-center">

        {{-- Big 404 --}}
        <div class="text-9xl md:text-[10rem] font-extrabold leading-none mb-2 bg-gradient-to-r from-primary-600 via-accent-500 to-primary-600 bg-clip-text text-transparent">
            404
        </div>

        <h1 class="text-2xl md:text-4xl font-extrabold text-gray-900 mb-3">{{ __('The page you are looking for is lost') }}</h1>
        <p class="text-gray-600 text-lg max-w-xl mx-auto mb-8">
            {{ __('The URL may be wrong, or the page may have been moved or deleted. You can try one of the options below:') }}
        </p>

        {{-- Search box --}}
        <form action="{{ route('search.index') }}" method="GET" class="max-w-xl mx-auto mb-8">
            <div class="relative">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"><x-svg-icon name="search" class="w-5 h-5" /></span>
                <input type="text" name="q"
                       placeholder="{{ __('Search universities, cities, programs...') }}"
                       autofocus
                       class="w-full pl-12 pr-4 py-3 rounded-full border border-gray-300 shadow-md focus:border-primary-500 focus:ring-2 focus:ring-primary-100 focus:outline-none">
            </div>
        </form>

        {{-- Quick links grid --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 max-w-2xl mx-auto mb-8">
            @php
                $links = [
                    ['url' => route('home'), 'icon' => 'home', 'label' => __('Home')],
                    ['url' => route('universities.index'), 'icon' => 'academic-cap', 'label' => __('Universities')],
                    ['url' => route('cities.index'), 'icon' => 'building-office', 'label' => __('Cities')],
                    ['url' => route('programs.index'), 'icon' => 'book-open', 'label' => __('Programs')],
                    ['url' => route('fields.index'), 'icon' => 'target', 'label' => __('Fields')],
                    ['url' => route('states.index'), 'icon' => 'map', 'label' => __('States')],
                    ['url' => route('tools.cost-of-living'), 'icon' => 'currency-euro', 'label' => __('Cost')],
                    ['url' => route('faqs.index'), 'icon' => 'question-mark-circle', 'label' => __('FAQ')],
                ];
            @endphp
            @foreach ($links as $l)
                <a href="{{ $l['url'] }}"
                   class="group bg-white border border-gray-200 hover:border-primary-500 hover:shadow-md rounded-xl p-4 transition">
                    <div class="inline-flex items-center justify-center w-10 h-10 mx-auto mb-1 rounded-lg bg-primary-50 text-primary-600"><x-svg-icon :name="$l['icon']" class="w-6 h-6" /></div>
                    <div class="text-sm font-semibold text-gray-700 group-hover:text-primary-600">{{ $l['label'] }}</div>
                </a>
            @endforeach
        </div>

        {{-- Report link --}}
        <p class="text-sm text-gray-500">
            {{ __('Did you follow a broken link?') }}
            <button type="button" onclick="document.getElementById('feedbackToggle')?.click()" class="text-primary-600 hover:underline">
                {{ __('Report it to us') }}
            </button>
        </p>
    </div>
</section>

@endsection
