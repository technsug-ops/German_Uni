@extends('layouts.app')

@section('title', __('Maintenance') . ' — ' . brand('name'))

@push('meta')
    <meta name="robots" content="noindex">
@endpush

@section('content')

<section class="min-h-[70vh] bg-gradient-to-br from-amber-50 via-white to-yellow-50 flex items-center">
    <div class="max-w-2xl mx-auto px-4 py-16 text-center">

        <div class="text-7xl md:text-9xl mb-4">🛠️</div>

        <h1 class="text-2xl md:text-4xl font-extrabold text-gray-900 mb-3">{{ __('Site is under maintenance') }}</h1>
        <p class="text-gray-600 text-lg max-w-xl mx-auto mb-8">
            {{ __(':brand is currently under maintenance. We will be back online within a few minutes.', ['brand' => brand('name')]) }}
        </p>

        <p class="text-sm text-gray-500">
            {{ __('For urgent matters:') }}
            <a href="mailto:info@{{ str_replace('www.', '', request()->getHost()) }}" class="text-primary-600 hover:underline">info@{{ str_replace('www.', '', request()->getHost()) }}</a>
        </p>
    </div>
</section>

@endsection
