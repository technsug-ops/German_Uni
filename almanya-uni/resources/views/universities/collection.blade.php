@extends('layouts.app')

@php
    $accentGradients = [
        'primary' => 'from-primary-700 via-primary-600 to-accent-500',
        'accent'  => 'from-emerald-700 via-teal-600 to-cyan-600',
        'amber'   => 'from-amber-700 via-orange-600 to-rose-600',
    ];
    $heroGradient = $accentGradients[$collection['accent']] ?? $accentGradients['primary'];

    $cTitle    = __($collection['title']);
    $cSubtitle = __($collection['subtitle']);
    $cIntro    = __($collection['intro']);

    // ItemList structured data — each curated university as a linked list item (SEO).
    $itemList = [
        '@context' => 'https://schema.org',
        '@type'    => 'ItemList',
        'name'     => $cTitle,
        'itemListElement' => collect($universities->items())->values()->map(fn ($u, $i) => [
            '@type'    => 'ListItem',
            'position' => $i + 1,
            'url'      => route('universities.show', $u['slug']),
            'name'     => $u['name_de'],
        ])->all(),
    ];
@endphp

@section('title', $cTitle . ' — ' . brand('name'))
<x-seo :title="$cTitle" :description="$cSubtitle" />
<x-json-ld :data="\App\Support\Seo::breadcrumbs([
    ['name' => __('Home'), 'url' => route('home')],
    ['name' => __('Universities'), 'url' => route('universities.index')],
    ['name' => $cTitle, 'url' => route('universities.collection', $slug)],
])" />
<x-json-ld :data="$itemList" />

@section('content')

{{-- ─────────────── HERO ─────────────── --}}
<section class="bg-gradient-to-br {{ $heroGradient }} text-white">
    <div class="max-w-[1400px] mx-auto px-4 py-12 md:py-16">
        <nav class="text-sm text-white/80 mb-3">
            <a href="{{ route('home') }}" class="hover:text-white">{{ __('Home') }}</a>
            <span class="mx-2 opacity-50">›</span>
            <a href="{{ route('universities.index') }}" class="hover:text-white">{{ __('Universities') }}</a>
            <span class="mx-2 opacity-50">›</span>
            <span class="text-white">{{ $cTitle }}</span>
        </nav>
        <h1 class="text-3xl md:text-5xl font-extrabold mb-3 leading-tight flex items-center gap-3">
            <span aria-hidden="true">{{ $collection['icon'] }}</span> {{ $cTitle }}
        </h1>
        <p class="text-lg md:text-xl text-white/90 max-w-3xl">{{ $cSubtitle }}</p>
    </div>
</section>

{{-- ─────────────── INTRO ─────────────── --}}
<section class="bg-white border-b border-gray-200">
    <div class="max-w-[1400px] mx-auto px-4 py-6">
        <p class="text-gray-700 max-w-3xl leading-relaxed">{{ $cIntro }}</p>
    </div>
</section>

{{-- ─────────────── GRID (reuses universities._grid) ─────────────── --}}
<section class="bg-gray-50 py-10">
    <div class="max-w-[1400px] mx-auto px-4">
        @include('universities._grid')

        {{-- Cross-links to the other curated collections --}}
        <div class="mt-12 pt-8 border-t border-gray-200">
            <h2 class="text-lg font-bold text-gray-900 mb-4">{{ __('Other university categories') }}</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach (\App\Support\UniversityCollections::all() as $oSlug => $o)
                    @if ($oSlug !== $slug)
                        <a href="{{ route('universities.collection', $oSlug) }}"
                           class="group flex items-center gap-3 bg-white border border-gray-200 rounded-xl p-4 hover:border-primary-500 hover:shadow-md transition">
                            <span class="text-2xl shrink-0" aria-hidden="true">{{ $o['icon'] }}</span>
                            <span class="font-semibold text-gray-900 group-hover:text-primary-600 leading-snug">{{ __($o['title']) }}</span>
                        </a>
                    @endif
                @endforeach
                <a href="{{ route('universities.index') }}"
                   class="group flex items-center gap-3 bg-white border border-gray-200 rounded-xl p-4 hover:border-primary-500 hover:shadow-md transition">
                    <span class="text-2xl shrink-0" aria-hidden="true">🎓</span>
                    <span class="font-semibold text-gray-900 group-hover:text-primary-600 leading-snug">{{ __('All universities') }}</span>
                </a>
            </div>
        </div>
    </div>
</section>

@endsection
