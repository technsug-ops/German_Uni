@extends('layouts.app')

@section('title', __('Tuition-free study programs in Germany') . ' — ' . brand('name'))

<x-seo
    :title="__('Tuition-free study programs in Germany')"
    :description="__('Browse :count tuition-free study programs at public universities in Germany. Study for free — only a small semester fee. Filter by subject and university.', ['count' => number_format($total, 0, '.', '.')])"
/>

@section('content')

{{-- HERO --}}
<section class="bg-gradient-to-br from-emerald-700 via-green-600 to-teal-600 text-white">
    <div class="max-w-[1100px] mx-auto px-4 py-12 md:py-16">
        <nav class="text-sm text-emerald-100 mb-3">
            <a href="/" class="hover:text-white">{{ __('Home') }}</a>
            <span class="mx-2 opacity-60">›</span>
            <span class="text-white">{{ __('Tuition-free programs') }}</span>
        </nav>
        <h1 class="text-3xl md:text-5xl font-extrabold leading-tight drop-shadow mb-3">
            {{ __('Tuition-free study programs in Germany') }}
        </h1>
        <p class="text-lg text-emerald-50 max-w-3xl mb-5">
            {{ __('Public universities in Germany charge no tuition — you pay only a small semester contribution (≈ €100–350), which usually includes a public-transport ticket.') }}
        </p>
        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/15 backdrop-blur ring-1 ring-white/25 text-sm font-semibold">
            <span class="text-base">💶</span>
            {{ __(':count tuition-free programs', ['count' => number_format($total, 0, '.', '.')]) }}
        </div>
    </div>
</section>

<div class="max-w-[1100px] mx-auto px-4 py-10 space-y-12">

    {{-- Açıklama --}}
    <section class="bg-emerald-50 border border-emerald-200 rounded-xl p-5 md:p-6">
        <h2 class="font-bold text-gray-900 text-lg mb-2 inline-flex items-center gap-2">ℹ️ {{ __('Is studying in Germany really free?') }}</h2>
        <p class="text-sm text-gray-700 leading-relaxed">
            {{ __('At state (public) universities, yes — there is no tuition fee for Bachelor\'s or Master\'s, regardless of nationality. You pay a per-semester contribution of roughly €100–350 (often including a semester transport ticket). Exceptions: private universities charge tuition, and a few states (e.g. Baden-Württemberg) charge non-EU students ~€1,500/semester. Always confirm on the official program page.') }}
        </p>
    </section>

    {{-- Alanlara göre --}}
    <section>
        <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ __('Tuition-free programs by subject') }}</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($fields as $field)
                <a href="{{ route('programs.index', ['field' => $field->slug, 'free_only' => 1]) }}"
                   class="group flex items-center gap-3 bg-white border border-gray-200 hover:border-emerald-400 hover:shadow-md transition rounded-xl p-4">
                    <span class="inline-flex items-center justify-center w-11 h-11 rounded-lg text-white shrink-0"
                          style="background-color: {{ $field->color ?: '#059669' }};">{!! e_icon($field->icon, 'w-6 h-6') !!}</span>
                    <span class="min-w-0 flex-1">
                        <span class="block font-bold text-gray-900 group-hover:text-emerald-700 truncate">{{ $field->name }}</span>
                        <span class="block text-xs text-gray-500">{{ __(':count tuition-free programs', ['count' => $field->cnt]) }}</span>
                    </span>
                    <x-svg-icon name="arrow-right" class="w-4 h-4 text-gray-300 group-hover:text-emerald-500 shrink-0" />
                </a>
            @endforeach
        </div>
    </section>

    {{-- Üniversitelere göre --}}
    @if ($topUnis->isNotEmpty())
        <section>
            <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ __('Public universities with the most tuition-free programs') }}</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @foreach ($topUnis as $uni)
                    <a href="{{ route('programs.index', ['uni' => $uni->slug, 'free_only' => 1]) }}"
                       class="group flex items-center justify-between gap-3 bg-white border border-gray-200 hover:border-emerald-400 transition rounded-lg px-4 py-3">
                        <span class="font-semibold text-gray-900 group-hover:text-emerald-700 truncate">{{ $uni->display_name ?? $uni->name }}</span>
                        <span class="shrink-0 text-xs font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full">{{ $uni->cnt }}</span>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- CTA --}}
    <section class="text-center">
        <a href="{{ route('programs.index', ['free_only' => 1]) }}"
           class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-6 py-3 rounded-lg shadow-md transition">
            {{ __('See all tuition-free programs') }} <x-svg-icon name="arrow-right" class="w-4 h-4" />
        </a>
    </section>

    {{-- SSS --}}
    <x-faq-section
        :title="__('Frequently Asked Questions — tuition-free study')"
        :faqs="[
            ['q' => __('What is the semester contribution?'), 'a' => __('A mandatory per-semester fee (≈ €100–350) covering student services and usually a public-transport ticket. It is not tuition — it is far smaller and the same for everyone.')],
            ['q' => __('Do non-EU students pay tuition?'), 'a' => __('At most public universities, no. The exception is a few states (e.g. Baden-Württemberg) that charge non-EU students about €1,500 per semester. Check the program page for your state.')],
            ['q' => __('Are private universities free too?'), 'a' => __('No. Private universities charge tuition (often several thousand euros per semester). The free option is the public (state) universities, which is the large majority in Germany.')],
        ]"
    />
</div>
@endsection
