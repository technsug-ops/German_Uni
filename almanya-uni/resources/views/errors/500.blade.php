@extends('layouts.app')

@section('title', '500 — ' . __('Server error') . ' — ' . brand('name'))

@push('meta')
    <meta name="robots" content="noindex">
@endpush

@section('content')

<section class="min-h-[70vh] bg-gradient-to-br from-rose-50 via-white to-orange-50 flex items-center">
    <div class="max-w-2xl mx-auto px-4 py-16 text-center">

        <div class="text-9xl md:text-[10rem] font-extrabold leading-none mb-2 bg-gradient-to-r from-rose-600 via-orange-500 to-rose-600 bg-clip-text text-transparent">
            500
        </div>

        <h1 class="text-2xl md:text-4xl font-extrabold text-gray-900 mb-3">{{ __('Something went wrong') }}</h1>
        <p class="text-gray-600 text-lg max-w-xl mx-auto mb-8">
            {{ __('An unexpected error occurred on our server. Our team has been notified — please try again in a few minutes.') }}
        </p>

        <div class="flex flex-wrap gap-3 justify-center">
            <a href="{{ url()->previous() }}"
               class="px-6 py-3 rounded-lg bg-white border border-gray-300 hover:border-primary-400 text-gray-700 font-semibold transition">
                ← {{ __('Go back') }}
            </a>
            <a href="{{ route('home') }}"
               class="px-6 py-3 rounded-lg bg-primary-600 hover:bg-primary-700 text-white font-semibold transition">
                {{ __('Go to homepage') }}
            </a>
            <button type="button" onclick="document.getElementById('feedbackToggle')?.click()"
                    class="px-6 py-3 rounded-lg bg-amber-500 hover:bg-amber-600 text-white font-semibold transition">
                💬 {{ __('Report this error') }}
            </button>
        </div>

        <p class="text-xs text-gray-400 mt-8">
            {{ __('Error code:') }} 500 · {{ now()->format('d.m.Y H:i') }}
        </p>
    </div>
</section>

@endsection
