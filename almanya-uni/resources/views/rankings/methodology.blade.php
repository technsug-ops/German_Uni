@extends('layouts.app')

@section('title', $methodology['title'] . ' — ' . brand('name'))

<x-seo
    :title="$methodology['title']"
    :description="$methodology['intro']"
/>

@push('head')
<script type="application/ld+json">{!! json_encode([
    '@context' => 'https://schema.org',
    '@type'    => 'TechArticle',
    'headline' => $methodology['title'],
    'description' => $methodology['intro'],
    'datePublished' => '2026-01-01',
    'dateModified'  => now()->toDateString(),
    'author' => [
        '@type' => 'Organization',
        'name'  => brand('name'),
        'url'   => url('/'),
    ],
    'isAccessibleForFree' => true,
    'about' => [
        '@type' => 'Thing',
        'name'  => $config['title'] ?? 'University Ranking',
    ],
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
@endpush

@section('content')

<section class="bg-gradient-to-br from-indigo-700 via-purple-600 to-rose-600 text-white">
    <div class="max-w-[1000px] mx-auto px-4 py-10 md:py-12">
        <nav class="text-sm text-indigo-100 mb-3">
            <a href="{{ route('home') }}" class="hover:text-white">{{ __('Home') }}</a>
            <span class="mx-2 opacity-60">›</span>
            <a href="{{ route('rankings.index') }}" class="hover:text-white">{{ __('Rankings') }}</a>
            <span class="mx-2 opacity-60">›</span>
            <a href="{{ route('rankings.show', $config['slug']) }}" class="hover:text-white">{{ $config['title'] ?? $config['slug'] }}</a>
            <span class="mx-2 opacity-60">›</span>
            <span class="text-white">{{ __('Methodology') }}</span>
        </nav>
        <h1 class="text-2xl md:text-4xl font-extrabold leading-tight drop-shadow mb-3">
            {{ $methodology['title'] }}
        </h1>
        <p class="text-base md:text-lg text-indigo-100 max-w-3xl">{{ $methodology['intro'] }}</p>
    </div>
</section>

<div class="max-w-[1000px] mx-auto px-4 py-10 space-y-8">

    @if (! empty($methodology['indicators']))
        {{-- Indicators table --}}
        <section class="bg-white border border-gray-200 rounded-xl overflow-hidden">
            <div class="bg-gray-50 border-b border-gray-200 px-5 py-3 flex items-center justify-between">
                <h2 class="text-base font-bold text-gray-900">{{ __('Indicators & weights') }}</h2>
                <span class="text-xs text-gray-500">{{ count($methodology['indicators']) }} {{ __('indicators') }}</span>
            </div>

            <ul class="divide-y divide-gray-100">
                @foreach ($methodology['indicators'] as $key => $ind)
                    @php
                        $weight = (float) ($ind['weight'] ?? 0);
                    @endphp
                    <li class="px-5 py-4 flex items-start gap-4">
                        {{-- Weight badge --}}
                        <div class="shrink-0 w-14 text-center">
                            <div class="text-2xl font-extrabold text-indigo-700 leading-none">{{ rtrim(rtrim(number_format($weight, 1), '0'), '.') }}<span class="text-xs text-gray-400">%</span></div>
                        </div>

                        <div class="flex-1 min-w-0">
                            <h3 class="font-bold text-gray-900 text-sm md:text-base">{{ __($ind['label'] ?? $key) }}</h3>
                            @if (! empty($ind['tooltip']))
                                <p class="text-xs md:text-sm text-gray-600 mt-1 leading-relaxed">{{ __($ind['tooltip']) }}</p>
                            @endif

                            {{-- weight bar --}}
                            <div class="mt-2 w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-indigo-500 to-purple-500 rounded-full" style="width: {{ min(100, $weight) }}%"></div>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>

            @php $totalWeight = collect($methodology['indicators'])->sum('weight'); @endphp
            <div class="bg-gray-50 border-t border-gray-200 px-5 py-2.5 text-xs text-gray-600 text-right">
                {{ __('Total weight:') }} <strong class="text-gray-900">{{ rtrim(rtrim(number_format($totalWeight, 1), '0'), '.') }}%</strong>
            </div>
        </section>
    @endif

    {{-- Source + note --}}
    <section class="bg-amber-50 border border-amber-200 rounded-xl p-5">
        <h2 class="font-bold text-gray-900 mb-1 flex items-center gap-2">{{ $methodology['source_label'] }}</h2>
        @if (str_starts_with($methodology['source_url'] ?? '', 'http'))
            <a href="{{ $methodology['source_url'] }}" target="_blank" rel="noopener noreferrer nofollow"
               class="text-sm text-amber-800 hover:text-amber-900 underline break-all">
                {{ $methodology['source_text'] }} ↗
            </a>
        @else
            <a href="{{ $methodology['source_url'] }}"
               class="text-sm text-amber-800 hover:text-amber-900 underline">
                {{ $methodology['source_text'] }} →
            </a>
        @endif
        @if (! empty($methodology['note']))
            <p class="text-xs text-gray-700 mt-3 leading-relaxed italic">{{ $methodology['note'] }}</p>
        @endif
    </section>

    {{-- E-E-A-T explainer — why this matters --}}
    <section class="bg-white border border-gray-200 rounded-xl p-5">
        <h2 class="font-bold text-gray-900 mb-2">{{ __('Why we publish methodology') }}</h2>
        <div class="text-sm text-gray-700 space-y-2 leading-relaxed">
            <p>{{ __('Every ranking on this site is computed from a public, transparent indicator set. We do not move universities up or down for commercial reasons.') }}</p>
            <p>{{ __('Where the ranking source is external (QS, THE, ARWU), we link to the original methodology document. Where the signal is computed in-house (community mention score, programme count), we explain the inputs end-to-end.') }}</p>
            <p>{{ __('Spot a number that does not match the official source? Open a feedback ticket — we update within 48 hours.') }}</p>
        </div>
    </section>

    {{-- Back to ranking --}}
    <div class="text-center">
        <a href="{{ route('rankings.show', $config['slug']) }}"
           class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-6 py-3 rounded-lg shadow-md transition">
            ← {{ __('Back to the ranking') }}
        </a>
    </div>

</div>

@endsection
