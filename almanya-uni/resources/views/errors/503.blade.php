@extends('layouts.app')

@section('title', __('Maintenance') . ' — ' . brand('name'))

@push('meta')
    <meta name="robots" content="noindex">
@endpush

@section('content')

<section class="min-h-[70vh] bg-gradient-to-br from-amber-50 via-white to-yellow-50 flex items-center">
    <div class="max-w-2xl mx-auto px-4 py-16 text-center">

        <div class="flex justify-center mb-4 text-gray-400"><x-svg-icon name="wrench-screwdriver" class="w-20 h-20 md:w-28 md:h-28" /></div>

        <h1 class="text-2xl md:text-4xl font-extrabold text-gray-900 mb-3">{{ __('Site is under maintenance') }}</h1>
        <p class="text-gray-600 text-lg max-w-xl mx-auto mb-8">
            {{ __(':brand is currently under maintenance. We will be back online within a few minutes.', ['brand' => brand('name')]) }}
        </p>

        <p class="text-sm text-gray-500">
            {{ __('For urgent matters:') }}
            <a href="mailto:admin@applytogerman.com" class="text-primary-600 hover:underline">admin@applytogerman.com</a>
        </p>
    </div>
</section>

@endsection
