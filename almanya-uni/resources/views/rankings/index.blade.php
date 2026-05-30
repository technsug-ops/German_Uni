@extends('layouts.app')

@section('title', __('Germany University Rankings') . ' — ' . brand('name'))

<x-seo
    :title="__('Germany University Rankings')"
    :description="__('The largest, oldest, and customized university rankings in Germany. Public, private, applied sciences, and state-based lists.')"
/>

@php
    $categoryMeta = [
        'oncelik' => ['title' => __('Student & Community Favorites'), 'desc' => __('Universities most suitable for international students and most discussed in the community.'), 'icon' => 'star',           'flag' => '🇹🇷'],
        'dunya'   => ['title' => __('World Rankings'),     'desc' => __('German universities in QS World + Times Higher Education lists.'),                                      'icon' => 'globe'],
        'genel'   => ['title' => __('General Rankings'),      'desc' => __('Largest, oldest, and newest German universities.'),                                                  'icon' => 'chart-bar'],
        'alan'    => ['title' => __('By Field / Subject'),     'desc' => __('Strongest universities in engineering, IT, medicine, law and other fields.'),                       'icon' => 'target'],
        'tur'     => ['title' => __('By University Type'), 'desc' => __('Public, private, applied sciences, and arts universities.'),                                            'icon' => 'building-office'],
        'eyalet'  => ['title' => __('By State'),        'desc' => __('Universities located in Germany\'s 16 federal states.'),                                                   'icon' => 'map'],
    ];

    // Slug'a göre ikon
    $rankIcon = function ($slug) {
        return match (true) {
            str_contains($slug, 'turk-ogrenci')       => 'star',
            str_contains($slug, 'toplulukta')         => 'chat',
            str_contains($slug, 'qs-world')           => 'globe',
            str_contains($slug, 'the-world')          => 'trophy',
            str_contains($slug, 'en-iyi')             => 'target',
            str_contains($slug, 'en-buyuk')           => 'users',
            str_contains($slug, 'en-eski')            => 'building-office',
            str_contains($slug, 'en-yeni')            => 'sparkles',
            str_contains($slug, 'devlet')             => 'check-circle',
            str_contains($slug, 'ozel')               => 'star',
            str_contains($slug, 'uygulamali')         => 'wrench-screwdriver',
            str_contains($slug, 'sanat')              => 'photo',
            str_contains($slug, 'universiteleri')     => 'map',
            default                                   => 'chart-bar',
        };
    };
@endphp

@section('content')
<div class="bg-gradient-to-r from-primary-500 to-primary-700 text-white py-12">
    <div class="max-w-[1400px] mx-auto px-4">
        <h1 class="text-4xl md:text-5xl font-bold mb-3 inline-flex items-center gap-3">
            <x-svg-icon name="chart-bar" class="w-8 h-8 md:w-10 md:h-10" />
            {{ __('Germany University Rankings') }}
        </h1>
        <p class="text-lg text-primary-100 mb-6">
            {!! __('Discover the best universities in Germany across <strong>:n</strong> different categories.', ['n' => $total]) !!}
        </p>

        {{-- Quick stats highlight --}}
        @if (! empty($stats))
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mt-4">
                @if ($stats['largest_uni'])
                    <a href="{{ route('rankings.show', 'en-buyuk-universiteler') }}"
                       class="bg-white/10 hover:bg-white/20 border border-white/20 backdrop-blur rounded-lg p-4 transition">
                        <p class="text-xs uppercase tracking-wider text-primary-200 mb-1 inline-flex items-center gap-1"><x-svg-icon name="users" class="w-3.5 h-3.5" /> {{ __('Largest') }}</p>
                        <p class="font-bold leading-tight">{{ $stats['largest_uni']->name_de }}</p>
                        <p class="text-sm text-accent-300 mt-1">{{ __(':n students', ['n' => number_format($stats['largest_uni']->student_count)]) }}</p>
                    </a>
                @endif
                @if ($stats['oldest_uni'])
                    <a href="{{ route('rankings.show', 'en-eski-universiteler') }}"
                       class="bg-white/10 hover:bg-white/20 border border-white/20 backdrop-blur rounded-lg p-4 transition">
                        <p class="text-xs uppercase tracking-wider text-primary-200 mb-1 inline-flex items-center gap-1"><x-svg-icon name="building-office" class="w-3.5 h-3.5" /> {{ __('Oldest') }}</p>
                        <p class="font-bold leading-tight">{{ $stats['oldest_uni']->name_de }}</p>
                        <p class="text-sm text-accent-300 mt-1">{{ __('Year :y', ['y' => $stats['oldest_uni']->founded_year]) }}</p>
                    </a>
                @endif
                @if ($stats['newest_uni'])
                    <a href="{{ route('rankings.show', 'en-yeni-universiteler') }}"
                       class="bg-white/10 hover:bg-white/20 border border-white/20 backdrop-blur rounded-lg p-4 transition">
                        <p class="text-xs uppercase tracking-wider text-primary-200 mb-1 inline-flex items-center gap-1"><x-svg-icon name="sparkles" class="w-3.5 h-3.5" /> {{ __('Newest') }}</p>
                        <p class="font-bold leading-tight">{{ $stats['newest_uni']->name_de }}</p>
                        <p class="text-sm text-accent-300 mt-1">{{ __('Year :y', ['y' => $stats['newest_uni']->founded_year]) }}</p>
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>

{{-- Kategori jump chips --}}
<div class="bg-white border-b border-gray-200 sticky top-0 z-10">
    <div class="max-w-[1400px] mx-auto px-4 py-3 flex flex-wrap items-center gap-2">
        <span class="text-xs text-gray-500 mr-2">{{ __('Categories:') }}</span>
        @foreach (['oncelik', 'dunya', 'genel', 'alan', 'tur', 'eyalet'] as $cat)
            @if (! empty($grouped[$cat]))
                <a href="#cat-{{ $cat }}"
                   class="inline-flex items-center gap-1.5 text-xs px-3 py-1.5 rounded-full bg-primary-50 text-primary-700 hover:bg-primary-100 transition border border-primary-100">
                    @if (! empty($categoryMeta[$cat]['flag']))
                        {{ $categoryMeta[$cat]['flag'] }}
                    @endif
                    <x-svg-icon name="{{ $categoryMeta[$cat]['icon'] }}" class="w-3.5 h-3.5" />
                    {{ $categoryMeta[$cat]['title'] }}
                    <span class="opacity-60">({{ count($grouped[$cat]) }})</span>
                </a>
            @endif
        @endforeach
    </div>
</div>

<div class="max-w-[1400px] mx-auto px-4 py-10">
    @foreach (['oncelik', 'dunya', 'genel', 'alan', 'tur', 'eyalet'] as $cat)
        @if (!empty($grouped[$cat]))
            <section id="cat-{{ $cat }}" class="mb-12 scroll-mt-20">
                <div class="flex items-baseline gap-3 mb-2">
                    <h2 class="text-2xl md:text-3xl font-bold inline-flex items-center gap-2">
                        @if (! empty($categoryMeta[$cat]['flag']))
                            <span>{{ $categoryMeta[$cat]['flag'] }}</span>
                        @endif
                        <x-svg-icon name="{{ $categoryMeta[$cat]['icon'] }}" class="w-6 h-6 md:w-7 md:h-7" />
                        {{ $categoryMeta[$cat]['title'] }}
                    </h2>
                    <span class="text-sm text-gray-500">({{ count($grouped[$cat]) }})</span>
                </div>
                <p class="text-gray-600 mb-6">{{ $categoryMeta[$cat]['desc'] }}</p>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach ($grouped[$cat] as $rank)
                        <a href="{{ route('rankings.show', $rank['slug']) }}"
                           class="group block bg-white border border-gray-200 hover:border-primary-500 hover:shadow-md transition rounded-xl p-5">
                            <div class="flex items-start gap-3 mb-2">
                                <span class="text-primary-600 shrink-0"><x-svg-icon name="{{ $rankIcon($rank['slug']) }}" class="w-6 h-6" /></span>
                                <h3 class="text-base font-bold text-primary-900 leading-tight group-hover:text-primary-600 flex-1">{{ $rank['title'] }}</h3>
                            </div>
                            <p class="text-sm text-gray-600 leading-relaxed line-clamp-2">{{ $rank['description'] }}</p>
                            <p class="text-primary-600 group-hover:text-primary-800 font-semibold text-sm mt-3">{{ __('See list') }} →</p>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif
    @endforeach
</div>
@endsection
