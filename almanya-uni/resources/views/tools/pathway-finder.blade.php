@extends('layouts.app')

@section('title', __('Germany Pathway Finder') . ' — ' . brand('name'))

<x-seo
    :title="__('Germany Pathway Finder — 5 questions, your route revealed')"
    :description="__('Should you do Studienkolleg, Bachelor, Master, PhD, Ausbildung or Sprachkurs? 5 questions, weighted scoring, real durations + cost + best-for criteria.')"
/>

<x-tool-schema tool="pathway-finder" />

@section('content')

<section class="bg-gradient-to-br from-indigo-700 via-purple-600 to-pink-600 text-white">
    <div class="max-w-[1100px] mx-auto px-4 py-10 md:py-14">
        <nav class="text-sm text-indigo-100 mb-3">
            <a href="{{ route('home') }}" class="hover:text-white">{{ __('Home') }}</a>
            <span class="mx-2 opacity-50">›</span>
            <a href="{{ route('tools.index') }}" class="hover:text-white">{{ __('Tools') }}</a>
            <span class="mx-2 opacity-50">›</span>
            <span class="text-white">{{ __('Pathway Finder') }}</span>
        </nav>
        <h1 class="text-3xl md:text-5xl font-extrabold leading-tight drop-shadow mb-3 inline-flex items-center gap-3">
            <x-svg-icon name="map" class="w-8 h-8 md:w-10 md:h-10" />
            {{ __('Which Germany pathway is right for you?') }}
        </h1>
        <p class="text-lg md:text-xl text-indigo-100 max-w-3xl">
            {{ __('5 questions → Studienkolleg, Bachelor, Master, PhD, Ausbildung or Sprachkurs. Each path with real duration, cost and language requirements.') }}
        </p>
    </div>
</section>

<div class="max-w-[1100px] mx-auto px-4 py-10">

    {{-- FORM --}}
    <form method="POST" action="{{ route('tools.pathway-finder') }}" class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6 md:p-8 mb-10 space-y-6">
        @csrf

        {{-- Q1 Education --}}
        <div>
            <label class="block text-sm font-bold text-gray-900 mb-2">1. {{ __('What is your current education level?') }}</label>
            <select name="education_level" required class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                <option value="">{{ __('Select...') }}</option>
                <option value="high_school" @selected(($old['education_level'] ?? '') === 'high_school')>{{ __('High school graduate') }}</option>
                <option value="bachelor_student" @selected(($old['education_level'] ?? '') === 'bachelor_student')>{{ __('Currently in Bachelor (want to transfer)') }}</option>
                <option value="bachelor_grad" @selected(($old['education_level'] ?? '') === 'bachelor_grad')>{{ __('Bachelor graduate') }}</option>
                <option value="master_grad" @selected(($old['education_level'] ?? '') === 'master_grad')>{{ __('Master graduate') }}</option>
                <option value="working" @selected(($old['education_level'] ?? '') === 'working')>{{ __('Working professional (2+ years)') }}</option>
            </select>
        </div>

        {{-- Q2 German --}}
        <div>
            <label class="block text-sm font-bold text-gray-900 mb-2">2. {{ __('What is your German level?') }}</label>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                @foreach (['none' => __('None / A0'), 'a1_a2' => 'A1–A2', 'b1_b2' => 'B1–B2', 'c1_plus' => 'C1+'] as $key => $label)
                    <label class="flex items-center justify-center px-3 py-2.5 border border-gray-300 rounded-lg cursor-pointer hover:border-indigo-400 hover:bg-indigo-50 transition has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-100 has-[:checked]:text-indigo-900 has-[:checked]:font-bold">
                        <input type="radio" name="german_level" value="{{ $key }}" required @checked(($old['german_level'] ?? '') === $key) class="sr-only">
                        <span class="text-sm">{{ $label }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        {{-- Q3 Age --}}
        <div>
            <label class="block text-sm font-bold text-gray-900 mb-2">3. {{ __('Your age?') }}</label>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                @foreach (['17_22' => '17–22', '23_28' => '23–28', '29_35' => '29–35', '35_plus' => '35+'] as $key => $label)
                    <label class="flex items-center justify-center px-3 py-2.5 border border-gray-300 rounded-lg cursor-pointer hover:border-indigo-400 hover:bg-indigo-50 transition has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-100 has-[:checked]:text-indigo-900 has-[:checked]:font-bold">
                        <input type="radio" name="age_band" value="{{ $key }}" required @checked(($old['age_band'] ?? '') === $key) class="sr-only">
                        <span class="text-sm">{{ $label }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        {{-- Q4 Budget --}}
        <div>
            <label class="block text-sm font-bold text-gray-900 mb-2">4. {{ __('Your monthly budget?') }}</label>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                @foreach (['under_800' => __('Under €800/month (tight)'), '800_1100' => '€800–1,100/month', '1100_plus' => __('€1,100+/month')] as $key => $label)
                    <label class="flex items-center justify-center text-center px-3 py-2.5 border border-gray-300 rounded-lg cursor-pointer hover:border-indigo-400 hover:bg-indigo-50 transition has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-100 has-[:checked]:text-indigo-900 has-[:checked]:font-bold">
                        <input type="radio" name="budget_monthly" value="{{ $key }}" required @checked(($old['budget_monthly'] ?? '') === $key) class="sr-only">
                        <span class="text-sm">{{ $label }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        {{-- Q5 Timeline --}}
        <div>
            <label class="block text-sm font-bold text-gray-900 mb-2">5. {{ __('Your timeline?') }}</label>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                @foreach (['fast_6m' => __('Fast — within 6 months'), 'normal_1y' => __('Normal — about 1 year'), 'long_2y' => __('Long — 2+ years OK')] as $key => $label)
                    <label class="flex items-center justify-center text-center px-3 py-2.5 border border-gray-300 rounded-lg cursor-pointer hover:border-indigo-400 hover:bg-indigo-50 transition has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-100 has-[:checked]:text-indigo-900 has-[:checked]:font-bold">
                        <input type="radio" name="timeline" value="{{ $key }}" required @checked(($old['timeline'] ?? '') === $key) class="sr-only">
                        <span class="text-sm">{{ $label }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <button type="submit"
                class="w-full md:w-auto bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-8 py-3 rounded-lg shadow-md transition">
            <x-svg-icon name="search" class="w-5 h-5" /> {{ __('Show me my pathway') }}
        </button>
    </form>

    {{-- RESULT --}}
    @if ($result)
        @php
            $top = $result['top_pathway'];
            [$from, $to, $border, $tcolor] = $top['colors'];
        @endphp

        <section class="space-y-6">
            {{-- HERO RESULT CARD --}}
            <div class="bg-gradient-to-br {{ $from }} {{ $to }} text-white rounded-2xl p-6 md:p-8 shadow-lg">
                <p class="text-sm font-bold uppercase tracking-wider opacity-90 mb-2 inline-flex items-center gap-1.5"><x-svg-icon name="star" class="w-4 h-4" /> {{ __('Best match for you') }}</p>
                <div class="flex items-start gap-4 flex-wrap mb-4">
                    <div class="text-6xl">{{ $top['icon'] }}</div>
                    <div class="flex-1 min-w-0">
                        <h2 class="text-2xl md:text-3xl font-extrabold">{{ $top['name'] }}</h2>
                        <p class="text-sm md:text-base opacity-95 mt-1">{{ $top['subtitle'] }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-3 gap-3 mb-5">
                    <div class="bg-white/15 backdrop-blur-sm rounded-lg p-3">
                        <div class="text-xs uppercase tracking-wider opacity-80 mb-0.5 inline-flex items-center gap-1"><x-svg-icon name="clock" class="w-3.5 h-3.5" /> {{ __('Duration') }}</div>
                        <div class="text-sm font-bold">{{ $top['duration'] }}</div>
                    </div>
                    <div class="bg-white/15 backdrop-blur-sm rounded-lg p-3">
                        <div class="text-xs uppercase tracking-wider opacity-80 mb-0.5 inline-flex items-center gap-1"><x-svg-icon name="language" class="w-3.5 h-3.5" /> {{ __('Language') }}</div>
                        <div class="text-sm font-bold">{{ $top['language'] }}</div>
                    </div>
                    <div class="bg-white/15 backdrop-blur-sm rounded-lg p-3 col-span-2 md:col-span-1">
                        <div class="text-xs uppercase tracking-wider opacity-80 mb-0.5 inline-flex items-center gap-1"><x-svg-icon name="banknotes" class="w-3.5 h-3.5" /> {{ __('Cost') }}</div>
                        <div class="text-sm font-bold">{{ $top['cost'] }}</div>
                    </div>
                </div>

                <p class="text-sm md:text-base opacity-95 mb-5 leading-relaxed">
                    <strong>{{ __('Best for:') }}</strong> {{ $top['best_for'] }}
                </p>

                <a href="{{ $top['next_url'] }}"
                   class="inline-flex items-center gap-2 bg-white text-gray-900 hover:bg-gray-100 font-bold px-5 py-2.5 rounded-lg shadow transition">
                    {{ $top['next_label'] }} →
                </a>
            </div>

            {{-- Personal notes --}}
            @if (! empty($result['notes']))
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-5">
                    <p class="font-bold text-gray-900 text-sm mb-2 inline-flex items-center gap-1.5"><x-svg-icon name="light-bulb" class="w-4 h-4" /> {{ __('Notes based on your answers') }}</p>
                    <ul class="space-y-2">
                        @foreach ($result['notes'] as $note)
                            <li class="flex gap-2 items-start text-sm text-gray-700">
                                <span class="text-blue-500 mt-0.5">•</span>
                                <span class="leading-relaxed">{{ $note }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Runner-ups --}}
            @if ($result['second_pathway'] || $result['third_pathway'])
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach ([$result['second_pathway'], $result['third_pathway']] as $i => $p)
                        @continue (! $p)
                        @php [$pFrom, $pTo, $pBorder, $pText] = $p['colors']; @endphp
                        <article class="bg-white border-2 {{ $pBorder }} rounded-xl p-5">
                            <p class="text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">
                                {{ $i === 0 ? __('Second choice') : __('Also worth considering') }}
                            </p>
                            <div class="flex items-start gap-3 mb-3">
                                <div class="text-4xl">{{ $p['icon'] }}</div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-lg font-bold text-gray-900">{{ $p['name'] }}</h3>
                                    <p class="text-xs text-gray-600 mt-0.5">{{ $p['subtitle'] }}</p>
                                </div>
                            </div>
                            <p class="text-sm text-gray-700 mb-3 leading-relaxed">{{ $p['best_for'] }}</p>
                            <a href="{{ $p['next_url'] }}" class="text-sm font-bold {{ $pText }} hover:underline">
                                {{ $p['next_label'] }} →
                            </a>
                        </article>
                    @endforeach
                </div>
            @endif

            {{-- Bar chart of all pathway scores --}}
            <details class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                <summary class="cursor-pointer px-5 py-3 font-bold text-sm text-gray-900 hover:bg-gray-50 select-none">
                    <span class="inline-flex items-center gap-1.5"><x-svg-icon name="chart-bar" class="w-4 h-4" /> {{ __('See how all pathways scored for you') }}</span>
                </summary>
                <div class="p-5 space-y-2.5">
                    @php $maxScore = max($result['scores']); @endphp
                    @foreach ($result['scores'] as $key => $score)
                        @php $p = $pathways[$key]; @endphp
                        <div>
                            <div class="flex items-center justify-between text-xs mb-1">
                                <span class="font-semibold text-gray-700">{{ $p['icon'] }} {{ $p['name'] }}</span>
                                <span class="font-mono text-gray-500">{{ $score }} pts</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-indigo-500 to-purple-500 rounded-full" style="width: {{ $maxScore > 0 ? min(100, ($score / $maxScore) * 100) : 0 }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </details>
        </section>
    @endif

    {{-- All pathways overview (always visible at bottom) --}}
    <section class="mt-14">
        <h2 class="text-2xl font-bold text-gray-900 mb-2 inline-flex items-center gap-2"><x-svg-icon name="book-open" class="w-6 h-6" /> {{ __('All 6 Germany pathways') }}</h2>
        <p class="text-sm text-gray-600 mb-5">{{ __('Real durations + costs + language requirements. The quiz above picks the best fit; here is the full menu.') }}</p>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            @foreach ($pathways as $p)
                <article class="bg-white border border-gray-200 rounded-xl p-4 hover:shadow-md transition">
                    <div class="flex items-start gap-3">
                        <div class="text-3xl shrink-0">{{ $p['icon'] }}</div>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-bold text-gray-900">{{ $p['name'] }}</h3>
                            <p class="text-xs text-gray-500 mt-0.5">{{ $p['subtitle'] }}</p>
                            <p class="text-xs text-gray-700 mt-2 leading-relaxed">{{ \Illuminate\Support\Str::limit($p['best_for'], 120) }}</p>
                            <a href="{{ $p['next_url'] }}" class="text-xs font-bold text-indigo-700 hover:text-indigo-900 hover:underline inline-block mt-2">
                                {{ $p['next_label'] }} →
                            </a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </section>

</div>

@endsection
