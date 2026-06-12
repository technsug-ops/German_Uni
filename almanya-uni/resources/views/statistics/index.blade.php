@extends('layouts.app')

@section('title', __('Germany Study Statistics') . ' — ' . brand('name'))

<x-seo
    :title="__('Germany Study Statistics — Universities, Programs & Costs') . ' — ' . brand('name')"
    :description="__('Quotable data on studying in Germany: :uni universities, :prog programs (:en% English-taught), cheapest student cities and programs by field.', ['uni' => $totals['universities'], 'prog' => number_format($totals['programs']), 'en' => $totals['en_pct']])"
/>

@php
    $domain = 'https://' . brand('domain');
@endphp
@push('head')
<script type="application/ld+json">{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'Dataset',
    'name' => __('Germany Study Statistics'),
    'description' => __('Statistics on German higher education: universities, study programs, English-taught share, student city costs.'),
    'url' => $domain . '/germany-study-statistics',
    'creator' => ['@type' => 'Organization', 'name' => brand('name'), 'url' => $domain],
    'license' => $domain . '/terms',
    'isAccessibleForFree' => true,
    'variableMeasured' => [
        ['@type' => 'PropertyValue', 'name' => 'universities', 'value' => $totals['universities']],
        ['@type' => 'PropertyValue', 'name' => 'study programs', 'value' => $totals['programs']],
        ['@type' => 'PropertyValue', 'name' => 'English-taught programs', 'value' => $totals['programs_en']],
    ],
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
@endpush

@section('content')

{{-- HERO --}}
<section class="bg-gradient-to-br from-primary-800 via-primary-700 to-primary-900 text-white">
    <div class="max-w-[1400px] mx-auto px-4 py-12 md:py-16">
        <nav class="text-sm text-primary-200 mb-3">
            <a href="/" class="hover:text-white">{{ __('Home') }}</a>
            <span class="mx-2 opacity-60">›</span>
            <span class="text-white">{{ __('Statistics') }}</span>
        </nav>
        <h1 class="text-3xl md:text-5xl font-extrabold leading-tight mb-3">{{ __('Germany Study Statistics') }}</h1>
        <p class="text-lg text-primary-50 max-w-3xl">{{ __('Free, quotable data on studying in Germany — cite us with a link.') }}</p>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mt-8 max-w-3xl">
            @foreach ([
                [number_format($totals['universities']), __('Universities')],
                [number_format($totals['programs']), __('Study programs')],
                [$totals['en_pct'] . '%', __('English-taught')],
                [number_format($totals['cities']), __('Student cities')],
            ] as [$v, $l])
                <div class="bg-white/10 backdrop-blur rounded-xl p-4 text-center">
                    <div class="text-2xl md:text-3xl font-extrabold">{{ $v }}</div>
                    <div class="text-xs text-primary-100 mt-1">{{ $l }}</div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<div class="max-w-[1400px] mx-auto px-4 py-12 space-y-14">

    {{-- En çok İngilizce program sunan üniler --}}
    <section>
        <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-1 inline-flex items-center gap-2">
            <x-svg-icon name="academic-cap" class="w-7 h-7 text-primary-600" />
            {{ __('Universities with the most English-taught programs') }}
        </h2>
        <p class="text-gray-600 text-sm mb-5">{{ __(':count of :total programs (:pct%) are taught in English.', ['count' => number_format($totals['programs_en']), 'total' => number_format($totals['programs']), 'pct' => $totals['en_pct']]) }}</p>
        <div class="overflow-x-auto rounded-xl border border-gray-200">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 text-left">
                    <tr><th class="px-4 py-3 w-12">#</th><th class="px-4 py-3">{{ __('University') }}</th><th class="px-4 py-3 text-right">{{ __('English programs') }}</th></tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($top_uni_en as $u)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-gray-400 font-semibold">{{ $loop->iteration }}</td>
                            <td class="px-4 py-3"><a href="{{ route('universities.show', $u->slug) }}" class="font-semibold text-gray-900 hover:text-primary-600">{{ $u->name_de }}</a></td>
                            <td class="px-4 py-3 text-right font-extrabold text-primary-700">{{ $u->c }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

    {{-- En ucuz öğrenci şehirleri --}}
    @if ($cheapest_cities->isNotEmpty())
    <section>
        <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-1 inline-flex items-center gap-2">
            <x-svg-icon name="building-office" class="w-7 h-7 text-emerald-600" />
            {{ __('Cheapest student cities (monthly living cost)') }}
        </h2>
        <p class="text-gray-600 text-sm mb-5">{{ __('Estimated monthly cost: shared-room rent + food + transport + utilities + extras.') }}</p>
        <div class="overflow-x-auto rounded-xl border border-gray-200">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 text-left">
                    <tr><th class="px-4 py-3 w-12">#</th><th class="px-4 py-3">{{ __('City') }}</th><th class="px-4 py-3 text-right">{{ __('Est. monthly') }}</th></tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($cheapest_cities as $c)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-gray-400 font-semibold">{{ $loop->iteration }}</td>
                            <td class="px-4 py-3"><a href="{{ route('cities.show', $c->slug) }}" class="font-semibold text-gray-900 hover:text-emerald-600">{{ $c->name_de }}</a></td>
                            <td class="px-4 py-3 text-right font-extrabold text-emerald-700">€{{ number_format($c->total) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
    @endif

    {{-- Alan başına program --}}
    <section>
        <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-5 inline-flex items-center gap-2">
            <x-svg-icon name="target" class="w-7 h-7 text-amber-600" />
            {{ __('Programs by field of study') }}
        </h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach ($top_fields as $f)
                <a href="{{ route('fields.show', $f->slug) }}" class="group bg-white rounded-xl border border-gray-200 hover:border-primary-400 hover:shadow-lg transition p-4 text-center">
                    <div class="mb-2 flex justify-center text-primary-600">{!! e_icon($f->icon, 'w-9 h-9') !!}</div>
                    <h3 class="font-bold text-gray-900 group-hover:text-primary-600 text-sm leading-tight mb-0.5">{{ $f->name }}</h3>
                    <p class="text-xs text-gray-500">{{ number_format($f->programs_count) }} {{ __('programs') }}</p>
                </a>
            @endforeach
        </div>
    </section>

    {{-- Cite / paylaş --}}
    <section class="bg-primary-50 rounded-2xl p-6 md:p-8 text-center">
        <h2 class="text-xl font-bold text-gray-900 mb-2">{{ __('Use this data?') }}</h2>
        <p class="text-sm text-gray-600 max-w-2xl mx-auto">{{ __('You may use these statistics for free — please cite :brand with a link to this page.', ['brand' => brand('name')]) }}</p>
        <p class="text-xs text-gray-400 mt-3">{{ $domain }}/germany-study-statistics</p>
    </section>

</div>
@endsection
