@extends('layouts.app')

@section('title', __('Professional Application Templates') . ' — ' . brand('name'))

<x-seo
    :title="__('Professional Application Templates for Germany')"
    :description="__('Ready-to-use German application templates: CV (Lebenslauf), motivation letter (Motivationsschreiben), recommendation letter and application emails — with a fill-in guide.')"
/>

@php
    $catLabels = [
        'application' => __('Application documents'),
        'finance'     => __('Finance & visa'),
        'career'      => __('Career & jobs'),
        'housing'     => __('Housing'),
    ];
@endphp

@section('content')
{{-- HERO --}}
<section class="bg-gradient-to-br from-violet-700 via-purple-700 to-indigo-800 text-white">
    <div class="max-w-[1100px] mx-auto px-4 py-12 md:py-16">
        <nav class="text-sm text-violet-200 mb-3">
            <a href="/" class="hover:text-white">{{ __('Home') }}</a>
            <span class="mx-2 opacity-60">›</span>
            <span class="text-white">{{ __('Templates') }}</span>
        </nav>
        <h1 class="text-3xl md:text-5xl font-extrabold leading-tight mb-3 inline-flex items-center gap-3">
            <x-svg-icon name="document-text" class="w-9 h-9" />
            {{ __('Professional Application Templates') }}
        </h1>
        <p class="text-lg text-violet-50 max-w-2xl">
            {{ __('Copy-ready German templates that meet what universities and authorities expect — each with a step-by-step fill-in guide.') }}
        </p>
        <div class="mt-6 inline-flex items-center gap-2 bg-white/10 backdrop-blur rounded-lg px-4 py-2 text-sm">
            <x-svg-icon name="check" class="w-4 h-4 text-emerald-300" />
            {{ __(':count ready templates — German format, no guesswork.', ['count' => $templates->count()]) }}
        </div>
    </div>
</section>

<div class="max-w-[1100px] mx-auto px-4 py-12 space-y-12">
    @forelse ($byCategory as $category => $items)
        <section>
            <h2 class="text-xl md:text-2xl font-bold text-gray-900 mb-5">{{ $catLabels[$category] ?? ucfirst($category) }}</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                @foreach ($items as $t)
                    <a href="{{ route('templates.show', $t->slug) }}"
                       class="group bg-white rounded-2xl border border-gray-200 hover:border-violet-400 hover:shadow-lg transition p-5 flex gap-4">
                        <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-violet-50 text-violet-600 flex items-center justify-center">
                            <x-svg-icon :name="$t->icon" class="w-6 h-6" />
                        </div>
                        <div class="min-w-0">
                            <h3 class="font-bold text-gray-900 group-hover:text-violet-700 leading-snug">{{ $t->title }}</h3>
                            @if ($t->description)
                                <p class="text-sm text-gray-600 mt-1 line-clamp-2">{{ $t->description }}</p>
                            @endif
                            <span class="inline-flex items-center gap-1 mt-2 text-violet-600 text-sm font-semibold">
                                {{ __('Open template') }} <span class="group-hover:translate-x-0.5 transition">→</span>
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    @empty
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-8 text-center">
            <p class="text-amber-900 font-semibold">{{ __('Templates are coming soon.') }}</p>
        </div>
    @endforelse

    {{-- Yumuşak premium upsell (henüz gating yok) --}}
    <section class="bg-violet-50 rounded-2xl p-6 md:p-8 text-center">
        <h2 class="text-xl font-bold text-gray-900 mb-2">{{ __('More templates on the way') }}</h2>
        <p class="text-sm text-gray-600 max-w-2xl mx-auto">{{ __('We are building a full library of application templates and guides. Join the early list to get them first.') }}</p>
        <a href="{{ route('pricing') }}" class="inline-flex items-center gap-2 mt-4 bg-violet-600 hover:bg-violet-700 text-white font-bold py-2.5 px-5 rounded-lg transition">
            {{ __('See Premium') }} →
        </a>
    </section>
</div>
@endsection
