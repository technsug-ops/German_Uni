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

{{-- ─────────────── 2-KOLON GRUPLU GÖRÜNÜM (varsa) ─────────────── --}}
@if (! empty($collection['groups']))
    @php
        $bySlug = collect($universities->items())->keyBy('slug');
        $accentMap = [
            'emerald' => ['head' => 'text-emerald-700', 'badge' => 'bg-emerald-100 text-emerald-800', 'border' => 'hover:border-emerald-400'],
            'amber'   => ['head' => 'text-amber-700',   'badge' => 'bg-amber-100 text-amber-800',     'border' => 'hover:border-amber-400'],
        ];
    @endphp
    <section class="bg-gray-50 py-10">
        <div class="max-w-[1400px] mx-auto px-4">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                @foreach ($collection['groups'] as $group)
                    @php $a = $accentMap[$group['accent']] ?? $accentMap['emerald']; @endphp
                    <div>
                        <h2 class="text-lg font-bold mb-4 inline-flex items-center gap-2 {{ $a['head'] }}">
                            <span aria-hidden="true">{{ $group['icon'] }}</span> {{ __($group['title']) }}
                        </h2>
                        <div class="space-y-3">
                            @foreach ($group['items'] as $slug => $meta)
                                @php $u = $bySlug[$slug] ?? null; @endphp
                                @if ($u)
                                    <a href="{{ route('universities.show', $slug) }}"
                                       class="group/card flex items-start gap-3 bg-white border border-gray-200 {{ $a['border'] }} hover:shadow-md transition rounded-xl p-4">
                                        @if (! empty($u['logo_url']))
                                            <img src="{{ $u['logo_url'] }}" alt="{{ $u['name_de'] }}"
                                                 class="w-12 h-12 object-contain bg-gray-50 rounded p-1 shrink-0" loading="lazy" decoding="async">
                                        @else
                                            <span class="w-12 h-12 rounded bg-primary-100 text-primary-700 flex items-center justify-center font-bold shrink-0">{{ mb_substr($u['name_de'], 0, 2) }}</span>
                                        @endif
                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-start justify-between gap-2">
                                                <span class="font-bold text-gray-900 leading-snug group-hover/card:text-primary-700 transition">{{ $u['name_de'] }}</span>
                                                <span class="shrink-0 text-[11px] font-bold uppercase px-2 py-0.5 rounded-full {{ $a['badge'] }}">{{ __($meta['status']) }}</span>
                                            </div>
                                            @if (! empty($u['city_name']))
                                                <div class="text-xs text-gray-500 mb-1 inline-flex items-center gap-1"><x-svg-icon name="map-pin" class="w-3 h-3" /> {{ $u['city_name'] }}</div>
                                            @endif
                                            <p class="text-sm text-gray-600 leading-relaxed">{{ __($meta['note']) }}</p>
                                        </div>
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
            <p class="text-xs text-gray-500 mt-5 max-w-3xl">{{ __('Verified from official university pages. Conditions change per semester — always confirm with the university before applying.') }}</p>
        </div>
    </section>
@endif

{{-- ─────────────── GRID (reuses universities._grid) ─────────────── --}}
<section class="bg-gray-50 py-10">
    <div class="max-w-[1400px] mx-auto px-4">
        @if (empty($collection['groups']))
            @include('universities._grid')
        @endif

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
