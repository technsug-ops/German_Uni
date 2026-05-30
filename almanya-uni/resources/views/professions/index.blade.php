@extends('layouts.app')

@section('title', __('Professions — What Jobs Can You Do in Germany?') . ' — ' . brand('name'))

<x-seo
    :title="__('Professions — What Jobs Can You Do in Germany?')"
    :description="__('Definition, education path and related university programs for :total professions in Germany. Bundesagentur für Arbeit data.', ['total' => number_format($totals['all'], 0, ',', '.')])"
/>

@php
    $typeLabels = [
        'ausbildung'    => ['Ausbildung', 'wrench-screwdriver', 'green'],
        'studienberuf'  => ['Studienberuf', 'academic-cap', 'blue'],
        'weiterbildung' => ['Weiterbildung', 'chart-bar', 'purple'],
        'grundberuf'    => ['Grundberuf', 'briefcase', 'amber'],
        'other'         => [__('Other'), 'book-open', 'gray'],
    ];
    $hasFilter = (bool) ($filters['q'] || $filters['type'] || $filters['field']);
@endphp

@section('content')
<section class="bg-gradient-to-br from-primary-700 via-primary-600 to-accent-500 text-white">
    <div class="max-w-[1400px] mx-auto px-4 py-12 md:py-16">
        <nav class="text-sm text-primary-100 mb-3">
            <a href="/" class="hover:text-white">{{ __('Home') }}</a>
            <span class="mx-2 opacity-50">›</span>
            <span class="text-white">{{ __('Professions') }}</span>
        </nav>
        <h1 class="text-3xl md:text-5xl font-extrabold mb-3 leading-tight">{{ __('Professions — Working Life in Germany') }}</h1>
        <p class="text-lg md:text-xl text-primary-100 max-w-3xl mb-6">
            {{ __('Definition, education path and tasks of :total professions in Germany — from Bundesagentur für Arbeit data.', ['total' => number_format($totals['all'], 0, ',', '.')]) }}
        </p>

        <form method="GET" action="{{ route('professions.index') }}" class="bg-white rounded-xl shadow-2xl p-3 text-gray-900"
              data-async-filter-form="#async-filter-results"
              data-no-loading>
            <div class="grid grid-cols-1 md:grid-cols-12 gap-2 mb-2">
                <div class="md:col-span-7 flex items-center px-3 border border-gray-300 rounded-lg">
                    <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.3-4.3M10.5 18a7.5 7.5 0 1 0 0-15 7.5 7.5 0 0 0 0 15Z"/>
                    </svg>
                    <input type="text" name="q" value="{{ $filters['q'] }}"
                           placeholder="{{ __('Search Beruf, Tätigkeit, KldB code...') }}"
                           class="flex-1 px-3 py-2.5 placeholder-gray-400 focus:outline-none bg-transparent">
                </div>
                <div class="md:col-span-3">
                    <select name="type" onchange="this.form.submit()" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 bg-white">
                        <option value="">{{ __('All types') }}</option>
                        @foreach ($typeLabels as $k => [$label, $iconName, $color])
                            <option value="{{ $k }}" @selected($filters['type'] === $k)>{{ $label }} ({{ $totals[$k] ?? 0 }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-2">
                    <button type="submit" class="w-full h-full bg-accent-500 hover:bg-accent-600 text-white font-bold px-4 py-2.5 rounded-lg transition">
                        {{ __('Search') }}
                    </button>
                </div>
            </div>

            {{-- Alan (field) filtresi --}}
            <div class="border-t border-gray-100 pt-2 px-1">
                <div class="flex items-center flex-wrap gap-1.5">
                    <span class="text-xs text-gray-500 mr-1">{{ __('Field:') }}</span>
                    <a href="{{ route('professions.index', array_filter(['q' => $filters['q'], 'type' => $filters['type']])) }}"
                       class="text-xs px-3 py-1 rounded-full border transition
                              {{ ! $filters['field'] ? 'bg-primary-600 text-white border-primary-600' : 'bg-gray-50 text-gray-600 border-gray-200 hover:bg-gray-100' }}">
                        {{ __('All') }}
                    </a>
                    @foreach ($fields as $f)
                        <a href="{{ route('professions.index', array_filter(['q' => $filters['q'], 'type' => $filters['type'], 'field' => $f->slug])) }}"
                           class="inline-flex items-center gap-1.5 text-xs px-3 py-1 rounded-full border transition
                                  {{ $filters['field'] === $f->slug ? 'bg-primary-600 text-white border-primary-600' : 'bg-gray-50 text-gray-700 border-gray-200 hover:bg-gray-100' }}">
                            {!! e_icon($f->icon, 'w-3.5 h-3.5') !!}
                            <span>{{ $f->name }}</span>
                            <span class="opacity-60">({{ $f->professions_count }})</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </form>
    </div>
</section>

<div class="max-w-[1400px] mx-auto px-4 py-8">
    <div id="async-filter-results" data-async-filter aria-live="polite" aria-busy="false">
        @include('professions._grid')
    </div>
</div>
@endsection
