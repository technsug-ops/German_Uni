@extends('layouts.app')

@section('title', __('English-taught study programs in Germany') . ' — ' . brand('name'))

<x-seo
    :title="__('English-taught study programs in Germany')"
    :description="__('Browse :count study programs taught in English in Germany — Bachelor\'s & Master\'s, no German required. Filter by subject and university.', ['count' => number_format($total, 0, '.', '.')])"
/>

@section('content')

{{-- HERO --}}
<section class="bg-gradient-to-br from-blue-700 via-indigo-600 to-blue-700 text-white">
    <div class="max-w-[1100px] mx-auto px-4 py-12 md:py-16">
        <nav class="text-sm text-blue-100 mb-3">
            <a href="/" class="hover:text-white">{{ __('Home') }}</a>
            <span class="mx-2 opacity-60">›</span>
            <span class="text-white">{{ __('English-taught programs') }}</span>
        </nav>
        <h1 class="text-3xl md:text-5xl font-extrabold leading-tight drop-shadow mb-3">
            {{ __('English-taught study programs in Germany') }}
        </h1>
        <p class="text-lg text-blue-50 max-w-3xl mb-5">
            {{ __('Study in Germany without German — these programs are taught fully in English. Tuition is free or low at most public universities.') }}
        </p>
        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/15 backdrop-blur ring-1 ring-white/25 text-sm font-semibold">
            <span class="text-base">🇬🇧</span>
            {{ __(':count English-taught programs', ['count' => number_format($total, 0, '.', '.')]) }}
        </div>
    </div>
</section>

<div class="max-w-[1100px] mx-auto px-4 py-10 space-y-12">

    {{-- Açıklama --}}
    <section class="bg-blue-50 border border-blue-200 rounded-xl p-5 md:p-6">
        <h2 class="font-bold text-gray-900 text-lg mb-2 inline-flex items-center gap-2">ℹ️ {{ __('Can I study in Germany in English?') }}</h2>
        <p class="text-sm text-gray-700 leading-relaxed">
            {{ __('Yes. Hundreds of Bachelor\'s and Master\'s programs are taught entirely in English — you can be admitted with an English certificate (IELTS/TOEFL) and no German. Public universities charge little or no tuition. You will still need basic German for daily life and many jobs, but not for the degree itself.') }}
        </p>
    </section>

    {{-- Alanlara göre --}}
    <section>
        <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ __('English-taught programs by subject') }}</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($fields as $field)
                <a href="{{ route('programs.index', ['field' => $field->slug, 'language' => 'en']) }}"
                   class="group flex items-center gap-3 bg-white border border-gray-200 hover:border-blue-400 hover:shadow-md transition rounded-xl p-4">
                    <span class="inline-flex items-center justify-center w-11 h-11 rounded-lg text-white shrink-0"
                          style="background-color: {{ $field->color ?: '#2563eb' }};">{!! e_icon($field->icon, 'w-6 h-6') !!}</span>
                    <span class="min-w-0 flex-1">
                        <span class="block font-bold text-gray-900 group-hover:text-blue-700 truncate">{{ $field->name }}</span>
                        <span class="block text-xs text-gray-500">{{ __(':count English programs', ['count' => $field->cnt]) }}</span>
                    </span>
                    <x-svg-icon name="arrow-right" class="w-4 h-4 text-gray-300 group-hover:text-blue-500 shrink-0" />
                </a>
            @endforeach
        </div>
    </section>

    {{-- Üniversitelere göre --}}
    @if ($topUnis->isNotEmpty())
        <section>
            <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ __('Universities with the most English-taught programs') }}</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @foreach ($topUnis as $uni)
                    <a href="{{ route('programs.index', ['uni' => $uni->slug, 'language' => 'en']) }}"
                       class="group flex items-center justify-between gap-3 bg-white border border-gray-200 hover:border-blue-400 transition rounded-lg px-4 py-3">
                        <span class="font-semibold text-gray-900 group-hover:text-blue-700 truncate">{{ $uni->display_name ?? $uni->name }}</span>
                        <span class="shrink-0 text-xs font-bold text-blue-700 bg-blue-50 px-2 py-0.5 rounded-full">{{ $uni->cnt }}</span>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- CTA --}}
    <section class="text-center">
        <a href="{{ route('programs.index', ['language' => 'en']) }}"
           class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-bold px-6 py-3 rounded-lg shadow-md transition">
            {{ __('See all English-taught programs') }} <x-svg-icon name="arrow-right" class="w-4 h-4" />
        </a>
    </section>

    {{-- SSS --}}
    <x-faq-section
        :title="__('Frequently Asked Questions — English-taught programs')"
        :faqs="[
            ['q' => __('Do I need German to study in English?'), 'a' => __('Not for the degree — English-taught programs require an English certificate (IELTS/TOEFL), not German. But A1–B1 German helps a lot for daily life, part-time jobs and staying after graduation.')],
            ['q' => __('Are English-taught programs free?'), 'a' => __('At most public universities tuition is free or a few hundred euros per semester, regardless of language. Some private universities and a few states charge more — always check the program page.')],
            ['q' => __('Are there English Bachelor\'s programs or only Master\'s?'), 'a' => __('Both exist, but English-taught Master\'s programs are far more common than Bachelor\'s. Many English Bachelor\'s are at private or international universities.')],
        ]"
    />
</div>
@endsection
