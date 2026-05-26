@extends('layouts.app')

@section('title', __('My Germany Application Journey') . ' — ' . brand('name'))

<x-seo
    :title="__('My Germany Application Journey')"
    :description="__('Track your progress through 8 steps: from eligibility check to arrival in Germany. All your tools in one dashboard.')"
    :no-index="true"
/>

@php
    $current = $tracker->currentStep();
    $percent = $tracker->progressPercent();
    $done = $tracker->completedCount();
    $total = count($steps);
@endphp

@section('content')

{{-- HERO --}}
<section class="bg-gradient-to-br from-primary-700 via-indigo-600 to-purple-600 text-white">
    <div class="max-w-[1400px] mx-auto px-4 py-10 md:py-12">
        <nav class="text-sm text-primary-100 mb-3">
            <a href="{{ route('home') }}" class="hover:text-white">{{ __('Home') }}</a>
            <span class="mx-2 opacity-50">›</span>
            <a href="{{ route('profile.edit') }}" class="hover:text-white">{{ __('Profile') }}</a>
            <span class="mx-2 opacity-50">›</span>
            <span class="text-white">{{ __('Journey') }}</span>
        </nav>

        <h1 class="text-3xl md:text-5xl font-extrabold leading-tight drop-shadow mb-3">
            🗺️ {{ __('My Germany Application Journey') }}
        </h1>
        <p class="text-lg text-primary-100 max-w-2xl mb-6">
            {{ __('8 steps from "is my diploma valid" to "I\'m enrolled in Germany". Tick as you complete — everything you need is connected.') }}
        </p>

        {{-- PROGRESS BAR --}}
        <div class="bg-white/10 backdrop-blur rounded-xl p-5 ring-1 ring-white/20">
            <div class="flex items-baseline justify-between mb-3">
                <div>
                    <p class="text-xs uppercase tracking-wider text-primary-200">{{ __('Overall progress') }}</p>
                    <p class="text-2xl md:text-3xl font-extrabold">{{ $done }} / {{ $total }} {{ __('steps') }}</p>
                </div>
                <p class="text-4xl md:text-5xl font-extrabold">{{ $percent }}<span class="text-xl">%</span></p>
            </div>
            <div class="h-3 bg-white/15 rounded-full overflow-hidden">
                <div class="h-full bg-gradient-to-r from-accent-400 to-emerald-400 rounded-full transition-all" style="width: {{ $percent }}%"></div>
            </div>
            @if ($current)
                <p class="text-sm text-primary-100 mt-4">
                    👉 {{ __('Next up:') }} <strong class="text-white">{{ $current['emoji'] }} {{ __($current['title']) }}</strong>
                    <span class="opacity-70">— ~{{ __($current['duration']) }}</span>
                </p>
            @else
                <p class="text-sm font-bold mt-4">🎉 {{ __('All steps completed! Viel Erfolg in Deutschland!') }}</p>
            @endif
        </div>
    </div>
</section>

<div class="max-w-[1400px] mx-auto px-4 py-10">

    @if (session('status'))
        <div class="mb-6 bg-emerald-50 border border-emerald-200 rounded-lg px-4 py-3 text-emerald-800 text-sm">
            ✓ {{ session('status') }}
        </div>
    @endif

    @if ($isGuest ?? false)
        <div class="mb-6 bg-amber-50 border border-amber-200 rounded-xl p-4 flex items-start gap-3">
            <span class="text-2xl">💾</span>
            <div class="flex-1 min-w-0">
                <p class="font-bold text-amber-900">{{ __('Guest mode — progress saved only on this device') }}</p>
                <p class="text-sm text-amber-800 mt-0.5">
                    {{ __('Tick steps freely. If you clear cookies or switch device, your progress will be lost.') }}
                    <a href="{{ route('login') }}" class="font-semibold text-amber-900 underline hover:text-amber-950">{{ __('Log in to save permanently') }}</a> →
                </p>
            </div>
        </div>
    @endif

    {{-- STEP CARDS --}}
    <div class="space-y-3 mb-10">
        @foreach ($steps as $step)
            @php
                $done = $tracker->isStepCompleted($step['key']);
                $isCurrent = $current && $current['key'] === $step['key'];
                $cardCls = $done
                    ? 'bg-emerald-50 border-emerald-300'
                    : ($isCurrent ? 'bg-white border-indigo-400 ring-2 ring-indigo-200 shadow-md' : 'bg-white border-gray-200 hover:border-gray-300');
            @endphp
            <div class="border-2 rounded-xl p-5 transition {{ $cardCls }}">
                <div class="flex items-start gap-4">
                    {{-- Step number / check --}}
                    <div class="flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center font-bold text-lg
                                {{ $done ? 'bg-emerald-600 text-white' : ($isCurrent ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-600') }}">
                        @if ($done) ✓ @else {{ $step['order'] }} @endif
                    </div>

                    {{-- Content --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-baseline justify-between gap-3 flex-wrap">
                            <h3 class="font-bold text-gray-900 text-lg">
                                {{ $step['emoji'] }} {{ __($step['title']) }}
                                @if ($isCurrent && ! $done)
                                    <span class="ml-1.5 text-xs font-semibold bg-indigo-100 text-indigo-800 px-1.5 py-0.5 rounded">{{ __('Current') }}</span>
                                @endif
                            </h3>
                            <span class="text-xs text-gray-500 whitespace-nowrap">⏱ {{ __($step['duration']) }}</span>
                        </div>
                        <p class="text-sm text-gray-600 mt-1">{{ __($step['desc']) }}</p>

                        {{-- Tool + Toggle --}}
                        <div class="flex flex-wrap items-center gap-3 mt-3">
                            @if ($step['tool_route'])
                                <a href="{{ route($step['tool_route']) }}"
                                   class="inline-flex items-center gap-1.5 text-sm font-semibold px-3 py-1.5 rounded-lg
                                          {{ $done ? 'bg-white border border-emerald-300 text-emerald-700 hover:bg-emerald-100' : 'bg-indigo-600 text-white hover:bg-indigo-700' }}">
                                    {{ __('Open tool') }} →
                                </a>
                            @endif

                            <form method="POST" action="{{ route('journey.step.toggle', $step['key']) }}" class="inline">
                                @csrf
                                <button type="submit" class="text-xs font-medium {{ $done ? 'text-gray-500 hover:text-gray-700' : 'text-indigo-600 hover:text-indigo-800' }}">
                                    @if ($done)
                                        ↺ {{ __('Mark as not done') }}
                                    @else
                                        ✓ {{ __('Mark as done') }}
                                    @endif
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- PREFERENCES --}}
    <section class="bg-white border border-gray-200 rounded-xl p-6 mb-8">
        <h2 class="text-lg font-bold text-gray-900 mb-4">⚙️ {{ __('My preferences') }}</h2>
        <form method="POST" action="{{ route('journey.update') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
            @csrf @method('PATCH')

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">{{ __('Target intake') }}</label>
                <select name="target_intake" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                    <option value="">{{ __('Choose...') }}</option>
                    @foreach (['winter-2026' => 'Winter 2026', 'summer-2027' => 'Summer 2027', 'winter-2027' => 'Winter 2027', 'summer-2028' => 'Summer 2028'] as $val => $lbl)
                        <option value="{{ $val }}" @selected($tracker->target_intake === $val)>{{ $lbl }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">{{ __('Target degree') }}</label>
                <select name="target_degree" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                    <option value="">{{ __('Choose...') }}</option>
                    <option value="bachelor" @selected($tracker->target_degree === 'bachelor')>{{ __('Bachelor\'s') }}</option>
                    <option value="master" @selected($tracker->target_degree === 'master')>{{ __('Master\'s') }}</option>
                    <option value="phd" @selected($tracker->target_degree === 'phd')>{{ __('PhD') }}</option>
                </select>
            </div>

            <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white font-semibold px-5 py-2 rounded-lg text-sm">
                {{ __('Save') }}
            </button>
        </form>
    </section>

    {{-- TIPS --}}
    <section class="bg-gradient-to-br from-amber-50 to-white border border-amber-200 rounded-xl p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-3">💡 {{ __('Tips') }}</h2>
        <ul class="space-y-2 text-sm text-gray-700">
            <li>• {{ __('Steps don\'t have to be done in strict order — but most depend on each other.') }}</li>
            <li>• {{ __('Documents (Step 3) takes the longest — start early, in parallel with applications.') }}</li>
            <li>• {{ __('Open Sperrkonto (Step 6) only after you have the acceptance letter — embassies want fresh proof.') }}</li>
            <li>• {{ __('Visa appointment (Step 7) waits can be 2-3 months — book as soon as you have your acceptance letter.') }}</li>
        </ul>
    </section>
</div>
@endsection
