@extends('layouts.app')

@section('title', __('Pricing — Free, Premium & Pro') . ' — ' . brand('name'))

<x-seo
    :title="__('Pricing — Free, Premium & Pro')"
    :description="__('All core content is 100% free. Premium adds mentor sessions, advanced tools, in-portal + email deadline reminders, and ad-free experience. Pro is a one-time package for full application review.')"
/>

@section('content')

{{-- HERO with decorative sparkles + animated CTA + live social proof --}}
<section class="relative bg-gradient-to-br from-primary-700 via-indigo-600 to-purple-600 text-white overflow-hidden">
    <div class="absolute inset-0 pointer-events-none opacity-50">
        <svg class="absolute top-8 left-[10%] w-12 h-12 animate-pulse" viewBox="0 0 24 24" fill="white" fill-opacity="0.4"><path d="M12 2L14.5 9.5L22 12L14.5 14.5L12 22L9.5 14.5L2 12L9.5 9.5L12 2Z"/></svg>
        <svg class="absolute top-1/3 right-[12%] w-10 h-10 animate-pulse" style="animation-delay: 0.7s" viewBox="0 0 24 24" fill="white" fill-opacity="0.5"><circle cx="12" cy="12" r="3"/></svg>
        <svg class="absolute bottom-12 left-[18%] w-16 h-16 animate-pulse" style="animation-delay: 1.4s" viewBox="0 0 24 24" fill="white" fill-opacity="0.3"><path d="M12 2L14.5 9.5L22 12L14.5 14.5L12 22L9.5 14.5L2 12L9.5 9.5L12 2Z"/></svg>
        <svg class="absolute bottom-8 right-[20%] w-8 h-8 animate-bounce" style="animation-duration: 3s" viewBox="0 0 24 24" fill="white" fill-opacity="0.35"><path d="M12 2 L13 10 L22 12 L13 14 L12 22 L11 14 L2 12 L11 10 Z"/></svg>
        <div class="absolute -bottom-10 -left-10 w-72 h-72 bg-amber-400/30 rounded-full blur-3xl"></div>
        <div class="absolute -top-10 -right-10 w-80 h-80 bg-indigo-400/30 rounded-full blur-3xl"></div>
    </div>

    <div class="relative max-w-[1400px] mx-auto px-4 py-14 md:py-20 text-center">
        @php $total = $counts['total'] ?? 0; @endphp

        @if ($total >= 5)
            <span class="inline-flex items-center gap-2 bg-white/15 border border-white/25 backdrop-blur px-4 py-1.5 rounded-full text-sm font-semibold mb-4 animate-pulse" style="animation-duration: 3s">
                <x-svg-icon name="fire" class="w-4 h-4 text-amber-300" /> {{ __(':n people already on the early-bird list', ['n' => number_format($total)]) }}
            </span>
        @else
            <span class="inline-flex items-center gap-2 bg-white/10 border border-white/20 backdrop-blur px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wide mb-4">
                <x-svg-icon name="star" class="w-3.5 h-3.5 text-amber-300" /> {{ __('Coming soon') }}
            </span>
        @endif

        <h1 class="text-4xl md:text-6xl font-extrabold leading-[1.05] drop-shadow mb-4">
            {{ __('Free always') }}
            <span class="bg-gradient-to-r from-amber-200 via-pink-200 to-white bg-clip-text text-transparent">
                — {{ __('Premium when you need more') }}
            </span>
        </h1>
        <p class="text-lg md:text-xl text-primary-100 max-w-2xl mx-auto mb-7">
            {{ __('All universities, programs, tools and FAQs stay free forever. Premium adds 1-on-1 mentor support, advanced tools, and a personalized journey.') }}
        </p>

        <a href="#interest-form" class="group relative inline-flex items-center gap-3 bg-amber-500 hover:bg-amber-600 text-white font-bold px-7 py-4 rounded-xl shadow-2xl transition">
            <span class="absolute inset-0 rounded-xl bg-amber-400 opacity-50 animate-ping group-hover:hidden" style="animation-duration: 2s"></span>
            <span class="relative inline-flex items-center gap-2"><x-svg-icon name="star" class="w-4 h-4" /> {{ __('Lock 20% lifetime discount') }}</span>
            <span class="relative group-hover:translate-x-1 transition">→</span>
        </a>

        @if ($total >= 5)
            <div class="flex flex-wrap items-center justify-center gap-x-6 gap-y-2 mt-6 text-sm text-primary-100">
                @if (($counts['beta_signups'] ?? 0) > 0)
                    <span class="flex items-center gap-1"><x-svg-icon name="rocket-launch" class="w-4 h-4" /> <strong>{{ $counts['beta_signups'] }}</strong> {{ __('beta testers') }}</span>
                @endif
                @if (($counts['premium_tier'] ?? 0) > 0)
                    <span class="flex items-center gap-1"><x-svg-icon name="star" class="w-4 h-4" /> <strong>{{ $counts['premium_tier'] }}</strong> {{ __('Premium') }}</span>
                @endif
                @if (($counts['pro_tier'] ?? 0) > 0)
                    <span class="flex items-center gap-1"><x-svg-icon name="sparkles" class="w-4 h-4" /> <strong>{{ $counts['pro_tier'] }}</strong> {{ __('Pro') }}</span>
                @endif
            </div>
        @endif
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
            <span class="inline-flex items-center justify-center w-12 h-12 mx-auto mb-2 rounded-full bg-amber-100 text-amber-600"><x-svg-icon name="star" class="w-7 h-7" /></span>
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

            {{-- Beta tester opt-in — bonus path before public launch --}}
            <div class="bg-white rounded-xl border-2 border-purple-200 p-4">
                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" name="wants_beta" value="1" {{ old('wants_beta') ? 'checked' : '' }}
                           class="mt-0.5 w-5 h-5 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                    <span>
                        <span class="block font-bold text-purple-900 text-sm inline-flex items-center gap-1.5"><x-svg-icon name="rocket-launch" class="w-4 h-4" /> {{ __('Join the beta tester program') }}</span>
                        <span class="block text-xs text-gray-600 mt-0.5">{{ __('Get 3 months free + lifetime 30% off in exchange for feedback. We invite ~20 testers in the next 2 weeks.') }}</span>
                    </span>
                </label>
            </div>

            <x-math-captcha compact />

            <button type="submit" class="w-full bg-amber-500 hover:bg-amber-600 text-white font-bold px-6 py-3 rounded-lg shadow-md transition">
                {{ __('Notify me — lock 20% discount') }} →
            </button>
            <p class="text-xs text-gray-500 text-center">{{ __('No spam. Single email when premium launches. Unsubscribe anytime.') }}</p>
        </form>

        {{-- Live counter footer --}}
        @if (($counts['total'] ?? 0) >= 5)
            <div class="mt-6 pt-5 border-t border-amber-200 flex flex-wrap items-center justify-center gap-x-6 gap-y-2 text-sm text-gray-700">
                <span class="flex items-center gap-1.5"><x-svg-icon name="users" class="w-4 h-4 text-gray-500" /> <strong>{{ number_format($counts['total']) }}</strong> {{ __('signups so far') }}</span>
                @if ($counts['beta_signups'] > 0)
                    <span class="flex items-center gap-1.5"><x-svg-icon name="rocket-launch" class="w-4 h-4 text-purple-600" /> <strong>{{ $counts['beta_signups'] }}</strong> {{ __('beta testers') }}</span>
                @endif
            </div>
        @endif
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
