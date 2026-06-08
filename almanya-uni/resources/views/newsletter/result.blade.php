@extends('layouts.app')

@section('title', $title . ' — ' . brand('name'))

@push('meta')
    <meta name="robots" content="noindex, nofollow">
@endpush

@section('content')

<section class="max-w-2xl mx-auto px-4 py-20">
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-8 md:p-12 text-center">

        <div class="w-16 h-16 mx-auto rounded-full flex items-center justify-center text-3xl mb-6
                    {{ $success ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
            {{ $success ? '✓' : '!' }}
        </div>

        <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900 mb-3">{{ $title }}</h1>
        <p class="text-gray-700 leading-relaxed mb-8">{{ $message }}</p>

        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ route('home') }}"
               class="inline-flex items-center justify-center gap-2 bg-primary-700 hover:bg-primary-800 text-white px-6 py-3 rounded-lg font-semibold transition">
                🏠 Ana sayfaya dön
            </a>
            <a href="{{ route('blog.index') }}"
               class="inline-flex items-center justify-center gap-2 bg-white border border-gray-300 hover:border-primary-500 text-gray-800 hover:text-primary-700 px-6 py-3 rounded-lg font-semibold transition">
                📚 Blog'a gözat
            </a>
        </div>

        @if (! empty($subscriber) && $success && ! $subscriber->is_confirmed)
            {{-- Unsubscribe sonrası geri dönüş için ipucu --}}
            <p class="text-xs text-gray-500 mt-8">
                E-posta: <span class="font-mono">{{ $subscriber->email }}</span>
            </p>
        @endif

    </div>
</section>

@endsection
