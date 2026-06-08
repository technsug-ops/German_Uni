@extends('layouts.app')

@section('title', __('Tools and Calculators') . ' — ' . brand('name'))

<x-seo
    :title="__('Germany Student Tools — Calculators')"
    :description="__('Cost of living, grade converter, university match quiz — all calculators for studying in Germany in one place.')"
/>

@php
    $locale = app()->getLocale();
    $fallback = 'en';
    $toolsRegistry = config('tools_schema', []);
    $itemListElements = [];
    $position = 1;
    foreach ($toolsRegistry as $tKey => $tCfg) {
        $tName = $tCfg['name'][$locale] ?? $tCfg['name'][$fallback] ?? null;
        $tDesc = $tCfg['description'][$locale] ?? $tCfg['description'][$fallback] ?? null;
        $tUrl  = isset($tCfg['route']) ? route($tCfg['route']) : null;
        if (!$tName || !$tUrl) continue;
        $itemListElements[] = [
            '@type' => 'ListItem',
            'position' => $position++,
            'item' => [
                '@type' => 'WebApplication',
                'name' => $tName,
                'description' => $tDesc,
                'url' => $tUrl,
                'applicationCategory' => 'EducationalApplication',
                'operatingSystem' => 'Any',
                'isAccessibleForFree' => true,
                'offers' => ['@type' => 'Offer', 'price' => '0', 'priceCurrency' => 'EUR'],
            ],
        ];
    }
@endphp

<x-json-ld :data="[
    '@context' => 'https://schema.org',
    '@type' => 'ItemList',
    'name' => __('Germany Student Tools'),
    'description' => __('Free interactive tools for international students applying to German universities.'),
    'numberOfItems' => count($itemListElements),
    'itemListElement' => $itemListElements,
]" />

@section('content')
<div class="bg-gradient-to-r from-primary-500 to-primary-700 text-white py-12">
    <div class="max-w-[1400px] mx-auto px-4">
        <h1 class="text-3xl md:text-5xl font-bold mb-4 leading-tight">{{ __('Tools & Calculators') }}</h1>
        <p class="text-lg text-primary-100 max-w-3xl">
            {{ __('The numerical side of being a student in Germany — interactive tools for cost of living, grade conversion, and finding the university that fits you best.') }}
        </p>
    </div>
</div>

<div class="max-w-[1400px] mx-auto px-4 py-10">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @foreach ($tools as $tool)
            @if ($tool['live'])
                <a href="{{ $tool['route'] }}"
                   class="block bg-white border border-gray-200 hover:border-primary-500 hover:shadow-md transition rounded-lg p-6">
                    <div class="text-4xl mb-3">{{ $tool['icon'] }}</div>
                    <h2 class="font-bold text-lg text-gray-900 mb-2">{{ $tool['title'] }}</h2>
                    <p class="text-sm text-gray-600">{{ $tool['description'] }}</p>
                    <span class="inline-block mt-3 text-sm font-semibold text-primary-600">{{ __('Use the tool') }} →</span>
                </a>
            @else
                <div class="block bg-gray-50 border border-gray-200 rounded-lg p-6 opacity-60 cursor-not-allowed">
                    <div class="text-4xl mb-3 grayscale">{{ $tool['icon'] }}</div>
                    <h2 class="font-bold text-lg text-gray-900 mb-2">{{ $tool['title'] }}</h2>
                    <p class="text-sm text-gray-600">{{ $tool['description'] }}</p>
                    <span class="inline-block mt-3 text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Coming soon') }}</span>
                </div>
            @endif
        @endforeach
    </div>
</div>
@endsection
