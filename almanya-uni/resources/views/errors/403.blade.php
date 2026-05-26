@extends('layouts.app')

@section('title', '403 — Yetkin yok — ' . brand('name'))

@push('meta')
    <meta name="robots" content="noindex">
@endpush

@section('content')

<section class="min-h-[70vh] bg-gradient-to-br from-primary-50 via-white to-amber-50 flex items-center">
    <div class="max-w-2xl mx-auto px-4 py-16 text-center">

        <div class="text-7xl md:text-9xl mb-4">🔒</div>

        <h1 class="text-2xl md:text-4xl font-extrabold text-gray-900 mb-3">Erişimin yok</h1>
        <p class="text-gray-600 text-lg max-w-xl mx-auto mb-8">
            Bu sayfaya erişmek için yetkin yok. Giriş yapman gerekiyor olabilir.
        </p>

        <div class="flex flex-wrap gap-3 justify-center">
            @guest
                <a href="{{ route('login') }}"
                   class="px-6 py-3 rounded-lg bg-primary-600 hover:bg-primary-700 text-white font-semibold transition">
                    Giriş Yap
                </a>
                <a href="{{ route('register') }}"
                   class="px-6 py-3 rounded-lg bg-white border border-gray-300 hover:border-primary-400 text-gray-700 font-semibold transition">
                    Kayıt Ol
                </a>
            @else
                <a href="{{ route('home') }}"
                   class="px-6 py-3 rounded-lg bg-primary-600 hover:bg-primary-700 text-white font-semibold transition">
                    Ana sayfaya git
                </a>
            @endguest
        </div>
    </div>
</section>

@endsection
