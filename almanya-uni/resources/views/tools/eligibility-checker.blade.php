@extends('layouts.app')

@section('title', __('Germany University Eligibility Checker') . ' — ' . brand('name'))

<x-seo
    :title="__('Germany University Eligibility Checker')"
    :description="__('Is your diploma recognized by German universities? Free 30-second check based on Anabin classification — for 15 most common countries.')"
/>

<x-tool-schema tool="eligibility-checker" />

@section('content')

{{-- HERO --}}
<section class="bg-gradient-to-br from-indigo-700 via-blue-600 to-purple-600 text-white">
    <div class="max-w-[1400px] mx-auto px-4 py-10 md:py-14">
        <nav class="text-sm text-blue-100 mb-3">
            <a href="{{ route('home') }}" class="hover:text-white">{{ __('Home') }}</a>
            <span class="mx-2 opacity-50">›</span>
            <a href="{{ route('tools.index') }}" class="hover:text-white">{{ __('Tools') }}</a>
            <span class="mx-2 opacity-50">›</span>
            <span class="text-white">{{ __('Eligibility Checker') }}</span>
        </nav>
        <h1 class="text-3xl md:text-5xl font-extrabold leading-tight drop-shadow mb-3">
            🎓 {{ __('Is your diploma recognized in Germany?') }}
        </h1>
        <p class="text-lg md:text-xl text-blue-100 max-w-3xl">
            {{ __('Free 30-second check — based on official Anabin classification. Find out if you can apply directly, need a Studienkolleg, or other requirements.') }}
        </p>
    </div>
</section>

<div class="max-w-[1400px] mx-auto px-4 py-10">

    {{-- FORM --}}
    <form method="POST" action="{{ route('tools.eligibility-checker') }}" class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6 md:p-8 mb-10">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            {{-- Country --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">{{ __('Your country (diploma origin)') }}</label>
                <select name="country" required class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                    <option value="">{{ __('Select country...') }}</option>
                    @foreach ($countries as $key => $c)
                        <option value="{{ $key }}" @selected(($old['country'] ?? '') === $key)>{{ $c['flag'] }} {{ $c['name'] }}</option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-500 mt-1.5">{{ __('Choose the country where you completed your most recent diploma — not your citizenship.') }}</p>
            </div>

            {{-- Current degree --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">{{ __('Your current degree') }}</label>
                <select name="degree" required class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                    <option value="high_school" @selected(($old['degree'] ?? '') === 'high_school')>{{ __('High school diploma') }}</option>
                    <option value="bachelor" @selected(($old['degree'] ?? '') === 'bachelor')>{{ __('Bachelor\'s degree') }}</option>
                    <option value="master" @selected(($old['degree'] ?? '') === 'master')>{{ __('Master\'s degree') }}</option>
                </select>
            </div>

            {{-- Target degree --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">{{ __('What do you want to study in Germany?') }}</label>
                <select name="target" required class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                    <option value="bachelor" @selected(($old['target'] ?? '') === 'bachelor')>{{ __('Bachelor\'s') }}</option>
                    <option value="master" @selected(($old['target'] ?? '') === 'master')>{{ __('Master\'s') }}</option>
                    <option value="phd" @selected(($old['target'] ?? '') === 'phd')>{{ __('PhD / Doctorate') }}</option>
                </select>
            </div>

            {{-- Language --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">{{ __('Language level') }}</label>
                <select name="language" required class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                    <option value="german_c1" @selected(($old['language'] ?? '') === 'german_c1')>{{ __('German C1+ (TestDaF / DSH)') }}</option>
                    <option value="english_b2" @selected(($old['language'] ?? '') === 'english_b2')>{{ __('English B2+ (IELTS 6.0)') }}</option>
                    <option value="both" @selected(($old['language'] ?? '') === 'both')>{{ __('Both German + English') }}</option>
                    <option value="none" @selected(($old['language'] ?? '') === 'none')>{{ __('Neither yet — learning') }}</option>
                </select>
            </div>

            {{-- GPA (optional) --}}
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-2">{{ __('Your GPA (optional, 0-100 or 1-5)') }}</label>
                <input type="number" name="gpa" min="0" max="100" step="0.1" value="{{ $old['gpa'] ?? '' }}"
                       placeholder="{{ __('e.g. 85 or 2.3') }}"
                       class="w-full md:w-1/2 px-3 py-2.5 border border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                <p class="text-xs text-gray-500 mt-1">{{ __('100-scale (Turkish) or 1-5 (German). Helps gauge competitiveness.') }}</p>
            </div>
        </div>

        <button type="submit" class="mt-6 w-full md:w-auto bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-8 py-3 rounded-lg shadow-md transition">
            {{ __('Check Eligibility →') }}
        </button>
    </form>

    {{-- RESULT --}}
    @if ($result)
        @php
            $verdictMeta = match ($result['verdict']) {
                'ok'             => ['emoji' => '✅', 'color' => 'emerald', 'label' => __('You can apply directly'), 'desc' => __('Your diploma is recognized — proceed with university application.')],
                'conditional'    => ['emoji' => '🟡', 'color' => 'amber',   'label' => __('Conditional eligibility'), 'desc' => __('Some requirements need attention — see below.')],
                'needs_prep'     => ['emoji' => '📚', 'color' => 'blue',    'label' => __('Preparation required'), 'desc' => __('You need a foundation course (Studienkolleg) or equivalent before applying.')],
                'not_eligible'   => ['emoji' => '❌', 'color' => 'rose',    'label' => __('Path not viable'), 'desc' => __('Your current path does not match the target — see suggestions.')],
            };
        @endphp

        <section class="bg-{{ $verdictMeta['color'] }}-50 border-2 border-{{ $verdictMeta['color'] }}-300 rounded-2xl p-6 md:p-8 mb-6" id="result">
            <div class="flex items-start gap-4 mb-5">
                <span class="text-5xl">{{ $verdictMeta['emoji'] }}</span>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-{{ $verdictMeta['color'] }}-700 mb-1">{{ __('Result') }}</p>
                    <h2 class="text-2xl md:text-3xl font-extrabold text-{{ $verdictMeta['color'] }}-900">{{ $verdictMeta['label'] }}</h2>
                    <p class="text-{{ $verdictMeta['color'] }}-800 mt-2">{{ $verdictMeta['desc'] }}</p>
                </div>
            </div>

            {{-- Anabin classification --}}
            @if ($result['anabin'] !== 'unknown')
                <div class="bg-white border border-{{ $verdictMeta['color'] }}-200 rounded-lg p-4 mb-4">
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-600 mb-1">{{ __('Anabin Classification') }}</p>
                    <p class="font-mono font-bold text-xl text-gray-900">{{ $result['anabin'] }}
                        <span class="text-sm text-gray-500 font-normal">
                            @if ($result['anabin'] === 'H+') {{ __('— Fully recognized') }}
                            @elseif ($result['anabin'] === 'H+-') {{ __('— Partially recognized (subject-dependent)') }}
                            @elseif ($result['anabin'] === 'H-') {{ __('— Not directly recognized') }}
                            @endif
                        </span>
                    </p>
                </div>
            @endif

            {{-- OK points --}}
            @if (!empty($result['ok_points']))
                <div class="space-y-2 mb-4">
                    @foreach ($result['ok_points'] as $p)
                        <div class="flex gap-2 text-sm text-gray-800"><span class="text-emerald-600">✓</span><span>{{ $p }}</span></div>
                    @endforeach
                </div>
            @endif

            {{-- Issues / requirements --}}
            @if (!empty($result['issues']))
                <div class="space-y-2 mb-4">
                    @foreach ($result['issues'] as $i)
                        <div class="flex gap-2 text-sm text-gray-800"><span class="text-amber-600">!</span><span>{{ $i }}</span></div>
                    @endforeach
                </div>
            @endif

            {{-- Required steps --}}
            <div class="flex flex-wrap gap-2 mt-5">
                @if ($result['needs_studienkolleg'])
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-blue-100 text-blue-800 text-xs font-semibold">📚 {{ __('Studienkolleg required') }}</span>
                @endif
                @if ($result['needs_aps'])
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-purple-100 text-purple-800 text-xs font-semibold">📜 {{ __('APS certificate required') }}</span>
                @endif
                @if ($result['needs_testas'])
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-cyan-100 text-cyan-800 text-xs font-semibold">📝 {{ __('TestAS recommended') }}</span>
                @endif
            </div>
        </section>

        {{-- Next steps --}}
        <section class="bg-white border border-gray-200 rounded-2xl p-6 md:p-8 mb-6">
            <h3 class="text-xl font-bold text-gray-900 mb-4">{{ __('Recommended next steps') }}</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <a href="https://anabin.kmk.org/anabin.html" target="_blank" rel="noopener nofollow" class="block bg-gradient-to-br from-blue-50 to-white border border-blue-200 hover:border-blue-400 rounded-xl p-4 transition">
                    <p class="font-bold text-gray-900 mb-1">🔍 {{ __('Verify on Anabin (official)') }}</p>
                    <p class="text-xs text-gray-600">{{ __('Cross-check the official KMK database for your specific university + diploma.') }}</p>
                </a>
                <a href="{{ route('tools.recommendation') }}" class="block bg-gradient-to-br from-purple-50 to-white border border-purple-200 hover:border-purple-400 rounded-xl p-4 transition">
                    <p class="font-bold text-gray-900 mb-1">🎯 {{ __('University Match Quiz') }}</p>
                    <p class="text-xs text-gray-600">{{ __('5 questions → universities that fit your profile.') }}</p>
                </a>
                <a href="{{ route('tools.blocked-account') }}" class="block bg-gradient-to-br from-emerald-50 to-white border border-emerald-200 hover:border-emerald-400 rounded-xl p-4 transition">
                    <p class="font-bold text-gray-900 mb-1">🏦 {{ __('Blocked Account Finder') }}</p>
                    <p class="text-xs text-gray-600">{{ __('Required €11,904 for the student visa — compare providers.') }}</p>
                </a>
                <a href="{{ route('tools.deadlines') }}" class="block bg-gradient-to-br from-amber-50 to-white border border-amber-200 hover:border-amber-400 rounded-xl p-4 transition">
                    <p class="font-bold text-gray-900 mb-1">📅 {{ __('Application Deadlines') }}</p>
                    <p class="text-xs text-gray-600">{{ __('Winter (mid-July) + Summer (mid-January) intake calendar.') }}</p>
                </a>
            </div>
        </section>
    @endif

    {{-- WHAT IS ANABIN? --}}
    <section class="bg-gradient-to-br from-gray-50 to-white border border-gray-200 rounded-2xl p-6 md:p-8 mb-6">
        <h2 class="text-xl font-bold text-gray-900 mb-3">{{ __('What is Anabin?') }}</h2>
        <p class="text-sm text-gray-700 leading-relaxed mb-3">
            {{ __('Anabin is the official German database (run by KMK — Conference of Education Ministers) that classifies foreign education credentials. It tells universities whether your diploma is equivalent to the German Abitur (high school) or recognized at higher levels.') }}
        </p>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-sm">
            <div class="bg-white border border-emerald-200 rounded-lg p-3">
                <p class="font-mono font-bold text-emerald-700">H+</p>
                <p class="text-gray-700 text-xs mt-1">{{ __('Fully recognized — direct application possible') }}</p>
            </div>
            <div class="bg-white border border-amber-200 rounded-lg p-3">
                <p class="font-mono font-bold text-amber-700">H+-</p>
                <p class="text-gray-700 text-xs mt-1">{{ __('Partially recognized — depends on subject and program') }}</p>
            </div>
            <div class="bg-white border border-rose-200 rounded-lg p-3">
                <p class="font-mono font-bold text-rose-700">H-</p>
                <p class="text-gray-700 text-xs mt-1">{{ __('Not directly recognized — Studienkolleg or 1-2 semesters required') }}</p>
            </div>
        </div>
        <p class="text-xs text-gray-500 mt-4">
            {{ __('This tool is informational. Final decision is made by the university\'s admissions office.') }}
            <a href="https://anabin.kmk.org/anabin.html" target="_blank" rel="noopener nofollow" class="text-indigo-600 hover:underline">anabin.kmk.org →</a>
        </p>
    </section>
</div>

{{-- Auto-FAQ (AIO + Featured Snippet) --}}
<x-faq-section
    :title="__('Frequently Asked Questions about University Eligibility')"
    :subtitle="__('Anabin classification, Studienkolleg path, and diploma recognition')"
    :faqs="\App\Support\PageFaq::forEligibility()"
/>
@endsection
