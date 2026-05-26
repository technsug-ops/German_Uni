@extends('layouts.app')

@section('title', __('Germany Education Glossary') . ' — ' . brand('name'))

<x-seo
    :title="__('Germany Education Glossary — APS, Anabin, DAAD, Sperrkonto, Studienkolleg explained')"
    :description="__('Plain-language explanations of all key terms for studying in Germany: APS, Anabin, Uni-Assist, Sperrkonto, Studienkolleg, EU Blue Card, DAAD, ECTS. Each entry with FAQ and official sources.')"
/>

@section('content')

{{-- HERO --}}
<section class="bg-gradient-to-br from-indigo-700 via-purple-600 to-pink-500 text-white">
    <div class="max-w-[1100px] mx-auto px-4 py-12 md:py-16">
        <nav class="text-sm text-indigo-100 mb-3">
            <a href="{{ route('home') }}" class="hover:text-white">{{ __('Home') }}</a>
            <span class="mx-2 opacity-60">›</span>
            <span class="text-white">{{ __('Glossary') }}</span>
        </nav>
        <h1 class="text-3xl md:text-5xl font-extrabold leading-tight drop-shadow mb-3">
            📘 {{ __('Germany Education Glossary') }}
        </h1>
        <p class="text-lg md:text-xl text-indigo-100 max-w-3xl">
            {{ __('Plain-language explanations of the key terms you\'ll encounter while applying to and studying in Germany. Each entry has FAQ and links to official sources.') }}
        </p>
    </div>
</section>

<div class="max-w-[1100px] mx-auto px-4 py-12">
    {{-- Grid of entries --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        @foreach ($entries as $entry)
            <a href="{{ route('glossary.show', $entry['slug']) }}"
               title="{{ $entry['title'] }} — {{ __('Glossary') }}"
               class="group block bg-white border border-gray-200 hover:border-indigo-400 hover:shadow-lg rounded-2xl p-6 transition">
                <div class="flex items-start gap-4">
                    <span class="text-4xl shrink-0">{{ $entry['icon'] }}</span>
                    <div class="flex-1 min-w-0">
                        <h2 class="text-xl font-bold text-gray-900 group-hover:text-indigo-700 leading-tight mb-2">
                            {{ $entry['title'] }}
                        </h2>
                        <p class="text-sm text-gray-600 leading-relaxed line-clamp-3">{{ $entry['short'] }}</p>
                        <span class="inline-flex items-center gap-1 mt-3 text-xs font-semibold text-indigo-600 group-hover:text-indigo-800">
                            {{ __('Read more') }} →
                        </span>
                    </div>
                </div>
            </a>
        @endforeach
    </div>

    {{-- CTA --}}
    <section class="mt-12 bg-gradient-to-br from-primary-50 to-white border border-primary-200 rounded-2xl p-6 md:p-8 text-center">
        <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ __('Need to know more?') }}</h2>
        <p class="text-gray-700 mb-5">{{ __('Browse our FAQ for ' . count($entries) . '+ answered questions about studying in Germany.', ['count' => count($entries)]) }}</p>
        <div class="flex flex-wrap gap-3 justify-center">
            <a href="{{ route('faqs.index') }}" class="bg-primary-600 hover:bg-primary-700 text-white font-semibold px-6 py-3 rounded-lg transition" title="{{ __('FAQ') }}">💬 {{ __('Frequently Asked Questions') }}</a>
            <a href="{{ route('tools.index') }}" class="bg-white border border-primary-300 hover:bg-primary-50 text-primary-700 font-semibold px-6 py-3 rounded-lg transition" title="{{ __('All tools') }}">🛠️ {{ __('Decision Tools') }}</a>
        </div>
    </section>
</div>

@endsection
