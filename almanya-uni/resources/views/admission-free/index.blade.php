@extends('layouts.app')

@section('title', __('NC-free (zulassungsfrei) study programs in Germany') . ' — ' . brand('name'))

<x-seo
    :title="__('NC-free study programs in Germany')"
    :description="__('Browse :count NC-free (zulassungsfrei) study programs in Germany — open admission, no grade restriction. Filter by subject and university.', ['count' => number_format($totalNcFree, 0, '.', '.')])"
/>

@section('content')

{{-- HERO --}}
<section class="bg-gradient-to-br from-emerald-700 via-emerald-600 to-teal-600 text-white">
    <div class="max-w-[1100px] mx-auto px-4 py-12 md:py-16">
        <nav class="text-sm text-emerald-100 mb-3">
            <a href="/" class="hover:text-white">{{ __('Home') }}</a>
            <span class="mx-2 opacity-60">›</span>
            <span class="text-white">{{ __('NC-free programs') }}</span>
        </nav>
        <h1 class="text-3xl md:text-5xl font-extrabold leading-tight drop-shadow mb-3">
            {{ __('NC-free (zulassungsfrei) study programs in Germany') }}
        </h1>
        <p class="text-lg text-emerald-50 max-w-3xl mb-5">
            {{ __('Programs with open admission — you can apply without a grade-based NC (numerus clausus). The most reassuring option if your GPA is a concern.') }}
        </p>
        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/15 backdrop-blur ring-1 ring-white/25 text-sm font-semibold">
            <span class="w-2 h-2 rounded-full bg-emerald-300 animate-pulse"></span>
            {{ __(':count NC-free programs', ['count' => number_format($totalNcFree, 0, '.', '.')]) }}
        </div>
    </div>
</section>

<div class="max-w-[1100px] mx-auto px-4 py-10 space-y-12">

    {{-- Açıklama --}}
    <section class="bg-emerald-50 border border-emerald-200 rounded-xl p-5 md:p-6">
        <h2 class="font-bold text-gray-900 text-lg mb-2 inline-flex items-center gap-2">ℹ️ {{ __('What does “NC-free” mean?') }}</h2>
        <p class="text-sm text-gray-700 leading-relaxed">
            {{ __('“Zulassungsfrei” means the program has no numerus clausus (NC) — there is no minimum grade cut-off. If you meet the basic requirements (degree, language certificate), you are admitted. This is the lowest-risk route for international students worried about their grades. Always confirm the current status on the official program page, as admission can change per semester.') }}
        </p>
    </section>

    {{-- Alanlara göre --}}
    <section>
        <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ __('NC-free programs by subject') }}</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($fields as $field)
                <a href="{{ route('admission-free.by-subject', $field->slug) }}"
                   class="group flex items-center gap-3 bg-white border border-gray-200 hover:border-emerald-400 hover:shadow-md transition rounded-xl p-4">
                    <span class="inline-flex items-center justify-center w-11 h-11 rounded-lg text-white shrink-0"
                          style="background-color: {{ $field->color ?: '#059669' }};">{!! e_icon($field->icon, 'w-6 h-6') !!}</span>
                    <span class="min-w-0 flex-1">
                        <span class="block font-bold text-gray-900 group-hover:text-emerald-700 truncate">{{ $field->name }}</span>
                        <span class="block text-xs text-gray-500">{{ __(':count NC-free programs', ['count' => $field->nc_free_count]) }}</span>
                    </span>
                    <x-svg-icon name="arrow-right" class="w-4 h-4 text-gray-300 group-hover:text-emerald-500 shrink-0" />
                </a>
            @endforeach
        </div>
    </section>

    {{-- Üniversitelere göre --}}
    @if ($topUnis->isNotEmpty())
        <section>
            <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ __('Universities with the most NC-free programs') }}</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @foreach ($topUnis as $uni)
                    <a href="{{ route('admission-free.by-university', $uni->slug) }}"
                       class="group flex items-center justify-between gap-3 bg-white border border-gray-200 hover:border-emerald-400 transition rounded-lg px-4 py-3">
                        <span class="font-semibold text-gray-900 group-hover:text-emerald-700 truncate">{{ $uni->display_name ?? $uni->name }}</span>
                        <span class="shrink-0 text-xs font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full">{{ $uni->nc_free_count }}</span>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- CTA --}}
    <section class="text-center">
        <a href="{{ route('programs.index') }}"
           class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-6 py-3 rounded-lg shadow-md transition">
            {{ __('Browse all programs') }} <x-svg-icon name="arrow-right" class="w-4 h-4" />
        </a>
    </section>

    {{-- SSS --}}
    <x-faq-section
        :title="__('Frequently Asked Questions — NC-free programs')"
        :faqs="[
            ['q' => __('What is a numerus clausus (NC)?'), 'a' => __('The NC is a grade-based admission restriction. When more students apply than there are places, the university admits by GPA. A “zulassungsfrei” (NC-free) program has no such cut-off.')],
            ['q' => __('Am I guaranteed admission to an NC-free program?'), 'a' => __('Not automatically — you still need to meet the formal requirements (recognized degree, language certificate, sometimes a specific prerequisite). But there is no grade competition, so your GPA does not block you.')],
            ['q' => __('Can the NC status change?'), 'a' => __('Yes. Admission status is set per semester. Always confirm on the official university program page before applying.')],
        ]"
    />
</div>
@endsection
