@extends('layouts.app')

@section('title', __('Pricing — Free, Premium & Pro') . ' — ' . brand('name'))

<x-seo
    :title="__('Pricing — Free, Premium & Pro')"
    :description="__('All core content is 100% free. Premium adds mentor sessions, advanced tools, in-portal + email deadline reminders, and ad-free experience. Pro is a one-time package for full application review.')"
/>

@section('content')

{{-- HERO --}}
<section class="bg-gradient-to-br from-primary-700 via-indigo-600 to-purple-600 text-white">
    <div class="max-w-[1400px] mx-auto px-4 py-12 md:py-16 text-center">
        <span class="inline-flex items-center gap-2 bg-white/10 border border-white/20 backdrop-blur px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wide mb-4">
            ⭐ {{ __('Coming soon') }}
        </span>
        <h1 class="text-4xl md:text-5xl font-extrabold leading-tight drop-shadow mb-4">
            {{ __('Free always — Premium when you need more') }}
        </h1>
        <p class="text-lg md:text-xl text-primary-100 max-w-2xl mx-auto">
            {{ __('All universities, programs, tools and FAQs stay free forever. Premium adds 1-on-1 mentor support, advanced tools, and a personalized journey.') }}
        </p>
    </div>
</section>

<div class="max-w-[1400px] mx-auto px-4 py-12">

    @if (session('status'))
        <div class="max-w-2xl mx-auto mb-8 bg-emerald-50 border border-emerald-200 rounded-xl p-4 text-emerald-800 text-sm">
            ✓ {{ session('status') }}
        </div>
    @endif

    {{-- 3 TIER --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">

        {{-- FREE --}}
        <div class="bg-white rounded-2xl border-2 border-gray-200 p-6 flex flex-col">
            <div class="mb-4">
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('Free') }}</p>
                <p class="text-4xl font-extrabold text-gray-900 mt-1">€0</p>
                <p class="text-sm text-gray-500">{{ __('forever') }}</p>
            </div>
            <ul class="space-y-2.5 text-sm text-gray-700 flex-1 mb-6">
                <li class="flex gap-2"><span class="text-emerald-600">✓</span>{{ __('464 universities + 14,527 programs browse') }}</li>
                <li class="flex gap-2"><span class="text-emerald-600">✓</span>{{ __('All 8+ calculators & tools') }}</li>
                <li class="flex gap-2"><span class="text-emerald-600">✓</span>{{ __('Eligibility Checker + Sperrkonto Finder') }}</li>
                <li class="flex gap-2"><span class="text-emerald-600">✓</span>{{ __('Application Tracker (8 steps)') }}</li>
                <li class="flex gap-2"><span class="text-emerald-600">✓</span>{{ __('Blog + FAQ + Forum') }}</li>
                <li class="flex gap-2"><span class="text-emerald-600">✓</span>{{ __('Free signup → saved favorites + email digest') }}</li>
            </ul>
            <a href="{{ route('home') }}" class="block text-center bg-gray-100 hover:bg-gray-200 text-gray-900 font-semibold py-2.5 rounded-lg transition">
                {{ __('Start exploring') }}
            </a>
        </div>

        {{-- PREMIUM --}}
        <div class="bg-gradient-to-br from-primary-50 to-white rounded-2xl border-2 border-primary-500 ring-4 ring-primary-100 p-6 flex flex-col relative shadow-lg">
            <span class="absolute -top-3 left-1/2 -translate-x-1/2 bg-accent-500 text-white text-xs font-bold uppercase tracking-wider px-3 py-1 rounded-full">
                {{ __('Most popular') }}
            </span>
            <div class="mb-4">
                <p class="text-xs font-semibold uppercase tracking-wider text-primary-700">{{ __('Premium') }}</p>
                <p class="text-4xl font-extrabold text-primary-900 mt-1">€14<span class="text-base font-normal text-gray-500">/{{ __('mo') }}</span></p>
                <p class="text-sm text-gray-500">{{ __('billed monthly · cancel anytime') }}</p>
            </div>
            <ul class="space-y-2.5 text-sm text-gray-800 flex-1 mb-6">
                <li class="flex gap-2"><span class="text-primary-600">✓</span>{{ __('Everything in Free') }}</li>
                <li class="flex gap-2 font-medium"><span class="text-primary-600">★</span>{{ __('1× 20-min 1-on-1 mentor session per month') }}</li>
                <li class="flex gap-2 font-medium"><span class="text-primary-600">★</span>{{ __('In-portal deadline alerts + email reminders') }}</li>
                <li class="flex gap-2 font-medium"><span class="text-primary-600">★</span>{{ __('Advanced analytics on your journey') }}</li>
                <li class="flex gap-2 font-medium"><span class="text-primary-600">★</span>{{ __('Ad-free experience') }}</li>
                <li class="flex gap-2 font-medium"><span class="text-primary-600">★</span>{{ __('Personalized university recommendations') }}</li>
                <li class="flex gap-2 font-medium"><span class="text-primary-600">★</span>{{ __('Priority email support (24h)') }}</li>
            </ul>
            <a href="#interest-form" class="block text-center bg-primary-600 hover:bg-primary-700 text-white font-bold py-3 rounded-lg transition shadow-md">
                {{ __('Get early access — 20% off') }} →
            </a>
        </div>

        {{-- PRO --}}
        <div class="bg-white rounded-2xl border-2 border-gray-200 p-6 flex flex-col">
            <div class="mb-4">
                <p class="text-xs font-semibold uppercase tracking-wider text-purple-700">{{ __('Pro') }}</p>
                <p class="text-4xl font-extrabold text-gray-900 mt-1">€49<span class="text-base font-normal text-gray-500"> {{ __('one-time') }}</span></p>
                <p class="text-sm text-gray-500">{{ __('full application package') }}</p>
            </div>
            <ul class="space-y-2.5 text-sm text-gray-700 flex-1 mb-6">
                <li class="flex gap-2"><span class="text-purple-600">✓</span>{{ __('Full application review by mentor') }}</li>
                <li class="flex gap-2"><span class="text-purple-600">✓</span>{{ __('CV + motivation letter feedback') }}</li>
                <li class="flex gap-2"><span class="text-purple-600">✓</span>{{ __('Visa interview prep (mock interview)') }}</li>
                <li class="flex gap-2"><span class="text-purple-600">✓</span>{{ __('2 hours mentor time (split as you like)') }}</li>
                <li class="flex gap-2"><span class="text-purple-600">✓</span>{{ __('Document checklist + apostille guide') }}</li>
                <li class="flex gap-2"><span class="text-purple-600">✓</span>{{ __('All Premium features for 3 months') }}</li>
            </ul>
            <a href="#interest-form" class="block text-center bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2.5 rounded-lg transition">
                {{ __('Get notified') }}
            </a>
        </div>
    </div>

    {{-- INTEREST FORM --}}
    <section id="interest-form" class="max-w-2xl mx-auto bg-gradient-to-br from-amber-50 to-white border-2 border-amber-200 rounded-2xl p-6 md:p-8 scroll-mt-20">
        <div class="text-center mb-6">
            <span class="inline-block text-3xl mb-2">⭐</span>
            <h2 class="text-2xl md:text-3xl font-extrabold text-gray-900">{{ __('Get notified when Premium launches') }}</h2>
            <p class="text-gray-600 mt-2">{{ __('Be the first to know + lock in 20% lifetime discount.') }}</p>
        </div>

        <form method="POST" action="{{ route('pricing.interest') }}" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">{{ __('Email') }} *</label>
                    <input type="email" name="email" required value="{{ old('email') }}"
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">{{ __('Name') }} ({{ __('optional') }})</label>
                    <input type="text" name="name" value="{{ old('name') }}"
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">{{ __('Which tier interests you?') }}</label>
                <div class="grid grid-cols-3 gap-2">
                    @foreach (['premium' => __('Premium €14/mo'), 'pro' => __('Pro €49 once'), 'undecided' => __('Not sure yet')] as $val => $lbl)
                        <label class="cursor-pointer">
                            <input type="radio" name="tier_interest" value="{{ $val }}" {{ old('tier_interest', 'premium') === $val ? 'checked' : '' }} class="peer hidden">
                            <span class="block text-center text-sm py-2.5 px-2 border-2 border-gray-200 rounded-lg peer-checked:border-amber-500 peer-checked:bg-amber-50 peer-checked:text-amber-900 peer-checked:font-bold transition">
                                {{ $lbl }}
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">{{ __('What do you most want from Premium?') }} ({{ __('optional') }})</label>
                <textarea name="note" rows="2" placeholder="{{ __('e.g. mentor for visa prep, ad-free experience...') }}"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:border-amber-500 focus:ring-1 focus:ring-amber-500">{{ old('note') }}</textarea>
            </div>

            <x-math-captcha compact />

            <button type="submit" class="w-full bg-amber-500 hover:bg-amber-600 text-white font-bold px-6 py-3 rounded-lg shadow-md transition">
                {{ __('Notify me — lock 20% discount') }} →
            </button>
            <p class="text-xs text-gray-500 text-center">{{ __('No spam. Single email when premium launches. Unsubscribe anytime.') }}</p>
        </form>
    </section>

    {{-- FAQ --}}
    <section class="max-w-3xl mx-auto mt-12">
        <h2 class="text-2xl font-bold text-gray-900 mb-5 text-center">{{ __('Common questions') }}</h2>
        <div class="space-y-3" x-data="{ open: 0 }">
            @foreach ([
                __('Is the core content really free?') => __('Yes. All universities, programs, rankings, tools, FAQs, blog — 100% free forever. We sustain the site through affiliate partnerships and (eventually) Premium.'),
                __('When does Premium launch?') => __('Targeting Q2 2026. Sign up above to lock in 20% lifetime discount.'),
                __('Who are the mentors?') => __('German university alumni + admission consultants. We partner with Mentorde.com — a focused mentorship platform.'),
                __('Can I cancel anytime?') => __('Yes, Premium is monthly. No long-term contract.'),
                __('What about Pro?') => __('One-time payment for full application support (review + CV + visa prep + 2h mentor). Perfect for the season before your application deadline.'),
            ] as $q => $a)
                @php $i = $loop->index; @endphp
                <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                    <button @click="open = (open === {{ $i }} ? null : {{ $i }})" class="w-full text-left p-4 flex items-center justify-between hover:bg-gray-50">
                        <span class="font-semibold text-gray-900">{{ $q }}</span>
                        <span class="text-gray-400" x-text="open === {{ $i }} ? '−' : '+'"></span>
                    </button>
                    <div x-show="open === {{ $i }}" x-collapse class="px-4 pb-4 text-sm text-gray-700 leading-relaxed">{{ $a }}</div>
                </div>
            @endforeach
        </div>
    </section>
</div>
@endsection
