@extends('layouts.app')

@section('title', __('Student Dorm Providers — Studierendenwerk + Private Companies') . '  — ' . brand('name'))

<x-seo
    :title="__('Germany Student Dorm Providers — Studierendenwerk + Private Companies')"
    :description="__('Student dorms in Germany: 29 Studierendenwerk (public) + 8 private companies (The Fizz, YouniQ, Uniapart etc.) + 4 portals. Prices, capacity, waiting times, contact info.')"
/>

@section('content')

{{-- HERO --}}
<section class="bg-gradient-to-br from-emerald-700 via-emerald-600 to-teal-500 text-white">
    <div class="max-w-[1400px] mx-auto px-4 py-12 md:py-16">
        <nav class="text-sm text-emerald-100 mb-3">
            <a href="/" class="hover:text-white">{{ __('Home') }}</a>
            <span class="mx-2 opacity-60">›</span>
            <a href="{{ route('housing.index') }}" class="hover:text-white">{{ __('Housing') }}</a>
            <span class="mx-2 opacity-60">›</span>
            <span class="text-white">{{ __('Providers') }}</span>
        </nav>
        <h1 class="text-3xl md:text-5xl font-extrabold leading-tight drop-shadow mb-3 flex items-center gap-3"><x-svg-icon name="building-office" class="w-9 h-9 md:w-11 md:h-11" /> {{ __('Student Dorm Providers') }}</h1>
        <p class="text-lg md:text-xl text-emerald-100 max-w-3xl">
            {{ __('Student dorms in Germany = 2 categories. Public (Studierendenwerk) → cheap but long waiting. Private companies (The Fizz, YouniQ) → fast but expensive. Apply to both in parallel.') }}
        </p>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mt-6">
            <div class="bg-white/10 backdrop-blur rounded-lg p-3">
                <p class="text-2xl font-bold">{{ $stats['studierendenwerk'] }}</p>
                <p class="text-xs text-emerald-100 inline-flex items-center gap-1"><x-svg-icon name="building-office" class="w-3.5 h-3.5" /> Studierendenwerk</p>
            </div>
            <div class="bg-white/10 backdrop-blur rounded-lg p-3">
                <p class="text-2xl font-bold">{{ $stats['private_chain'] }}</p>
                <p class="text-xs text-emerald-100 inline-flex items-center gap-1"><x-svg-icon name="briefcase" class="w-3.5 h-3.5" /> {{ __('Private Company') }}</p>
            </div>
            <div class="bg-white/10 backdrop-blur rounded-lg p-3">
                <p class="text-2xl font-bold">{{ $stats['platform'] }}</p>
                <p class="text-xs text-emerald-100 inline-flex items-center gap-1"><x-svg-icon name="globe" class="w-3.5 h-3.5" /> {{ __('Platform / Portal') }}</p>
            </div>
            <div class="bg-white/10 backdrop-blur rounded-lg p-3">
                <p class="text-2xl font-bold">{{ number_format($stats['total_capacity'], 0, ',', '.') }}</p>
                <p class="text-xs text-emerald-100 inline-flex items-center gap-1"><x-svg-icon name="home" class="w-3.5 h-3.5" /> {{ __('Total STW beds') }}</p>
            </div>
        </div>
    </div>
</section>

{{-- Featured-snippet box — Google AIO targets Q+concise-answer+steps pattern --}}
<section class="max-w-[1400px] mx-auto px-4 pt-8">
    <x-featured-snippet
        :question="__('How do I find student housing in Germany?')"
        :answer="__('You have two main paths: Studierendenwerk (public student dorms — €250–400/month, 1–2 semester waitlist) and private providers (The Fizz, YouniQ, Uniapart — €500–900/month, immediately available). Apply to both in parallel and add WG-Gesucht for shared flats.')"
        :steps="[
            ['title' => __('Apply to Studierendenwerk first'), 'description' => __('6–12 months before semester start — cheapest option, but long waitlist.')],
            ['title' => __('Add private student housing in parallel'), 'description' => __('The Fizz, YouniQ, MILESTONE — fast confirmation, higher price.')],
            ['title' => __('Search WG-Gesucht for shared flats'), 'description' => __('Start 4–8 weeks before move-in date for best results.')],
            ['title' => __('Book temporary housing as backup'), 'description' => __('HousingAnywhere or Airbnb for the first 2–4 weeks if needed.')],
            ['title' => __('Register your address (Anmeldung)'), 'description' => __('Within 2 weeks of moving in at the Bürgeramt.')],
        ]"
    />
</section>

{{-- Type filter chips --}}
<section class="bg-white border-b border-gray-200 sticky top-0 z-10">
    <div class="max-w-[1400px] mx-auto px-4 py-3 flex items-center flex-wrap gap-2">
        <span class="text-xs text-gray-500 mr-1">{{ __('Type:') }}</span>
        <a href="{{ route('housing.providers') }}"
           class="text-xs px-3 py-1.5 rounded-full border transition
                  {{ ! $type ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-gray-50 text-gray-600 border-gray-200 hover:bg-gray-100' }}">
            {{ __('All') }}
        </a>
        @foreach (\App\Models\HousingProvider::TYPES as $key => $meta)
            <a href="{{ route('housing.providers', ['type' => $key]) }}"
               class="inline-flex items-center gap-1.5 text-xs px-3 py-1.5 rounded-full border transition
                      {{ $type === $key ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-gray-50 text-gray-700 border-gray-200 hover:bg-gray-100' }}">
                {!! e_icon($meta['emoji'] ?? '', 'w-3.5 h-3.5') !!} {{ $meta['label'] }}
            </a>
        @endforeach
    </div>
</section>

<div class="max-w-[1400px] mx-auto px-4 py-10 space-y-12">

    {{-- 1) ÖZEL ŞİRKETLER (FEATURED) --}}
    @if ($grouped->has('private_chain') && (! $type || $type === 'private_chain'))
        <section>
            <div class="flex items-baseline justify-between mb-4">
                <h2 class="text-2xl font-bold text-gray-900 flex items-center gap-2"><x-svg-icon name="briefcase" class="w-7 h-7 text-emerald-700" /> {{ __('Private Dorm Companies') }}</h2>
                <span class="text-xs text-gray-500">{{ __(':count companies', ['count' => $grouped['private_chain']->count()]) }}</span>
            </div>
            <p class="text-sm text-gray-600 mb-4 max-w-3xl">
                {{ __('Fast move-in, modern design, internet+utilities included. Expensive (€300–€1,200/month) but no waiting. Fully furnished — show up with your suitcase.') }}
            </p>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($grouped['private_chain'] as $p)
                    @include('housing._provider_card', ['provider' => $p])
                @endforeach
            </div>
        </section>
    @endif

    {{-- 2) STUDIERENDENWERK --}}
    @if ($grouped->has('studierendenwerk') && (! $type || $type === 'studierendenwerk'))
        <section>
            <div class="flex items-baseline justify-between mb-4">
                <h2 class="text-2xl font-bold text-gray-900 flex items-center gap-2"><x-svg-icon name="building-office" class="w-7 h-7 text-emerald-700" /> {{ __('Studierendenwerk (Public)') }}</h2>
                <span class="text-xs text-gray-500">{{ __(':count regional bodies', ['count' => $grouped['studierendenwerk']->count()]) }}</span>
            </div>
            <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-5 text-sm text-amber-900 flex items-start gap-2">
                <x-svg-icon name="exclamation-triangle" class="w-5 h-5 flex-shrink-0 text-amber-600" />
                <span>{!! __('<strong>Apply early!</strong> Waiting list is 1–7 semesters. Apply as soon as you get your university acceptance; the process runs in parallel.') !!}</span>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($grouped['studierendenwerk'] as $p)
                    @include('housing._provider_card', ['provider' => $p])
                @endforeach
            </div>
        </section>
    @endif

    {{-- 3) PLATFORMLAR --}}
    @if ($grouped->has('platform') && (! $type || $type === 'platform'))
        <section>
            <h2 class="text-2xl font-bold text-gray-900 mb-3 flex items-center gap-2"><x-svg-icon name="globe" class="w-7 h-7 text-emerald-700" /> {{ __('Search Portals') }}</h2>
            <p class="text-sm text-gray-600 mb-4 max-w-3xl">
                {{ __('Active portals for WG (shared apartment) and private rentals. WG-Gesucht is the #1 choice for international students.') }}
            </p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach ($grouped['platform'] as $p)
                    @include('housing._provider_card', ['provider' => $p])
                @endforeach
            </div>
        </section>
    @endif

    {{-- CTA --}}
    <div class="bg-gradient-to-r from-emerald-700 to-teal-600 text-white rounded-xl p-8 text-center">
        <h3 class="text-2xl font-bold mb-2 inline-flex items-center gap-2 justify-center"><x-svg-icon name="home" class="w-7 h-7" /> {{ __('Which city has which option?') }}</h3>
        <p class="text-emerald-100 mb-4 max-w-2xl mx-auto">
            {{ __('To compare dorm options by city, go to the Cities page or jump directly to the city you want to move to.') }}
        </p>
        <a href="{{ route('cities.index') }}"
           class="inline-flex items-center gap-2 bg-white text-emerald-700 hover:bg-gray-100 px-6 py-3 rounded-lg font-bold shadow-lg transition">
            <x-svg-icon name="building-office" class="w-5 h-5" /> {{ __('City Comparison') }} →
        </a>
    </div>
</div>

{{-- Auto-generated FAQ (AIO + Featured Snippet eligibility) --}}
<x-faq-section
    :title="__('Frequently Asked Questions about Student Housing in Germany')"
    :subtitle="__('Practical answers about finding accommodation as an international student')"
    :faqs="\App\Support\PageFaq::forHousing()"
/>
@endsection
