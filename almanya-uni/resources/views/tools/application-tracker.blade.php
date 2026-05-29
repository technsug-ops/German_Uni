@extends('layouts.app')

@section('title', __('Application Tracker — Your Germany Journey') . ' — ' . brand('name'))

<x-seo
    :title="__('Application Tracker — Your Germany Journey')"
    :description="__('Track your German university application from eligibility check to Anmeldung — 8 steps, one dashboard. Free, anonymous, optional.')"
/>

@section('content')

<section class="bg-gradient-to-br from-indigo-700 via-blue-600 to-purple-600 text-white">
    <div class="max-w-[1100px] mx-auto px-4 py-12 md:py-14">
        <nav class="text-sm text-blue-100 mb-3">
            <a href="{{ route('home') }}" class="hover:text-white">{{ __('Home') }}</a>
            <span class="mx-2 opacity-50">›</span>
            <a href="{{ route('tools.index') }}" class="hover:text-white">{{ __('Tools') }}</a>
            <span class="mx-2 opacity-50">›</span>
            <span class="text-white">{{ __('Application Tracker') }}</span>
        </nav>
        <h1 class="text-3xl md:text-5xl font-extrabold leading-tight drop-shadow mb-3">
            🗺️ {{ __('Application Tracker') }}
        </h1>
        <p class="text-lg md:text-xl text-blue-100 max-w-2xl">
            {{ __('Track your German university application from Anabin to Anmeldung — 8 steps on one dashboard.') }}
        </p>
        @if ($tracker)
            <div class="mt-5 inline-flex items-center gap-3 bg-white/15 backdrop-blur rounded-xl px-4 py-2.5 text-sm">
                <span>{{ __('Target') }}:</span>
                <strong class="capitalize">{{ str_replace('_', ' ', $tracker->target_semester) }}</strong>
                <span class="opacity-60">·</span>
                <strong class="uppercase">{{ $tracker->degree_level }}</strong>
                <span class="opacity-60">·</span>
                <strong>{{ $tracker->country_origin }}</strong>
            </div>
        @endif
    </div>
</section>

<div class="max-w-[1100px] mx-auto px-4 py-10">

    @if (session('status'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg p-3 mb-6 text-sm">
            ✓ {{ session('status') }}
        </div>
    @endif

    @if (! $tracker)
        {{-- ONBOARDING --}}
        <section class="bg-white border border-gray-200 rounded-2xl p-6 md:p-10">
            <div class="text-center mb-8">
                <div class="text-5xl mb-3">🧭</div>
                <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">{{ __('Let\'s start your journey') }}</h2>
                <p class="text-sm text-gray-600 max-w-xl mx-auto">{{ __('Tell us your target — we will build a personal 8-step dashboard. No registration needed, your progress is saved with a private cookie.') }}</p>
            </div>

            <form method="POST" action="{{ route('tools.application-tracker.start') }}" class="max-w-md mx-auto space-y-5">
                @csrf

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">{{ __('I want a') }} ...</label>
                    <select name="degree_level" required class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                        <option value="bachelor">🎓 {{ __('Bachelor\'s degree') }}</option>
                        <option value="master">📚 {{ __('Master\'s degree') }}</option>
                        <option value="phd">🔬 {{ __('PhD / Doctorate') }}</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">{{ __('Target semester') }}</label>
                    <select name="target_semester" required class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                        <option value="winter_2027">{{ __('Winter Semester 2027') }} (Oct 2027)</option>
                        <option value="summer_2028">{{ __('Summer Semester 2028') }} (Apr 2028)</option>
                        <option value="winter_2028">{{ __('Winter Semester 2028') }} (Oct 2028)</option>
                        <option value="open">{{ __('Open — exploring') }}</option>
                    </select>
                </div>

                <input type="hidden" name="country_origin" value="TR">

                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 rounded-lg shadow-sm transition">
                    🚀 {{ __('Start tracking') }}
                </button>

                <p class="text-xs text-gray-500 text-center">{{ __('No email required. Anonymous. Your progress lives in a cookie you control.') }}</p>
            </form>
        </section>

        {{-- Niçin tracker --}}
        <section class="mt-10 grid grid-cols-1 sm:grid-cols-3 gap-4">
            @php
                $benefits = [
                    ['icon' => '📋', 'title' => __('8 official steps'), 'desc' => __('Eligibility → Application → Visa → Anmeldung. The canonical Germany path mapped from DAAD + KMK sources.')],
                    ['icon' => '⏱️', 'title' => __('Realistic timelines'), 'desc' => __('Each step shows estimated days needed. Plan backwards from your target semester.')],
                    ['icon' => '🔗', 'title' => __('Linked guidance'), 'desc' => __('Every step links to the corresponding howto guide + tool on AlmanyaUni.')],
                ];
            @endphp
            @foreach ($benefits as $b)
                <div class="bg-white border border-gray-200 rounded-xl p-5">
                    <div class="text-3xl mb-2">{{ $b['icon'] }}</div>
                    <h3 class="font-bold text-gray-900 text-sm mb-1">{{ $b['title'] }}</h3>
                    <p class="text-xs text-gray-600 leading-relaxed">{{ $b['desc'] }}</p>
                </div>
            @endforeach
        </section>

    @else
        {{-- DASHBOARD --}}
        @php $normalized = $tracker->normalized_steps; @endphp

        {{-- Progress summary --}}
        <section class="bg-white border-2 border-indigo-200 rounded-2xl p-6 mb-6 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
                <div>
                    <h2 class="text-xl md:text-2xl font-bold text-gray-900">{{ __('Your progress') }}</h2>
                    <p class="text-sm text-gray-600">
                        <strong class="text-indigo-700">{{ $tracker->completed_count }} / {{ count($steps) }}</strong> {{ __('steps completed') }}
                        @if ($tracker->in_progress_count > 0)
                            · <strong class="text-amber-700">{{ $tracker->in_progress_count }}</strong> {{ __('in progress') }}
                        @endif
                        @if ($tracker->completed_at)
                            · <span class="font-bold text-emerald-700">🎉 {{ __('All done!') }}</span>
                        @endif
                    </p>
                </div>
                <form method="POST" action="{{ route('tools.application-tracker.reset') }}"
                      onsubmit="return confirm('{{ __('Reset your tracker? All progress will be lost.') }}')">
                    @csrf
                    <button type="submit" class="text-xs text-rose-600 hover:text-rose-700 hover:underline">
                        🔄 {{ __('Reset tracker') }}
                    </button>
                </form>
            </div>

            <div class="w-full bg-gray-100 rounded-full h-3 overflow-hidden">
                <div class="h-full bg-gradient-to-r from-indigo-500 to-purple-500 rounded-full transition-all duration-500"
                     style="width: {{ $tracker->progress_percent }}%"></div>
            </div>
            <p class="text-right text-xs text-gray-500 mt-1">{{ $tracker->progress_percent }}%</p>
        </section>

        {{-- 8 step cards --}}
        <div class="space-y-3">
            @foreach ($steps as $stepId => $meta)
                @php
                    $stepStatus = $normalized[$stepId]['status'];
                    $stepNote = $normalized[$stepId]['note'];
                    $stepCompletedAt = $normalized[$stepId]['completed_at'];
                    $borderClass = $stepStatus === 'completed' ? 'border-emerald-300 bg-emerald-50/30' :
                                   ($stepStatus === 'in_progress' ? 'border-amber-300 bg-amber-50/30' : 'border-gray-200 bg-white');
                @endphp

                <article id="step-{{ $stepId }}" class="border-2 {{ $borderClass }} rounded-xl p-5 transition">
                    <div class="flex items-start gap-4 flex-wrap">
                        {{-- Step number + icon --}}
                        <div class="shrink-0">
                            @if ($stepStatus === 'completed')
                                <div class="w-12 h-12 rounded-full bg-emerald-500 text-white flex items-center justify-center text-xl shadow-md">✓</div>
                            @elseif ($stepStatus === 'in_progress')
                                <div class="w-12 h-12 rounded-full bg-amber-500 text-white flex items-center justify-center text-xl shadow-md animate-pulse">{{ $meta['icon'] }}</div>
                            @else
                                <div class="w-12 h-12 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center text-xl">{{ $meta['icon'] }}</div>
                            @endif
                        </div>

                        {{-- Step content --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap mb-1">
                                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('Step') }} {{ $stepId }}/{{ count($steps) }}</span>
                                @if ($stepStatus === 'completed')
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-full">✓ {{ __('Completed') }}</span>
                                @elseif ($stepStatus === 'in_progress')
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-amber-700 bg-amber-100 px-2 py-0.5 rounded-full">⏳ {{ __('In progress') }}</span>
                                @else
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full">⚪ {{ __('Not started') }}</span>
                                @endif
                                <span class="text-[10px] text-gray-500">⏱️ ~{{ $meta['estimate_days'] }} {{ __('days') }}</span>
                            </div>
                            <h3 class="font-bold text-gray-900 text-lg leading-snug">{{ __($meta['title']) }}</h3>
                            <p class="text-sm text-gray-700 mt-1 leading-relaxed">{{ __($meta['description']) }}</p>

                            @if ($stepCompletedAt)
                                <p class="text-xs text-emerald-700 mt-2">✓ {{ __('Completed') }} · {{ $stepCompletedAt->translatedFormat('d M Y') }}</p>
                            @endif

                            @if ($stepNote)
                                <div class="mt-3 bg-white border border-gray-200 rounded-lg p-3">
                                    <p class="text-xs text-gray-500 mb-1">📝 {{ __('Your note') }}:</p>
                                    <p class="text-sm text-gray-700 whitespace-pre-line">{{ $stepNote }}</p>
                                </div>
                            @endif

                            {{-- Related links --}}
                            <div class="flex flex-wrap gap-2 mt-3">
                                @if ($meta['related_route'])
                                    <a href="{{ route($meta['related_route']) }}"
                                       class="inline-flex items-center gap-1 text-xs font-semibold text-indigo-600 hover:text-indigo-800 bg-indigo-50 hover:bg-indigo-100 px-3 py-1.5 rounded-full transition">
                                        🛠️ {{ __('Use the tool') }} →
                                    </a>
                                @endif
                                @if ($meta['related_blog_slug'])
                                    <a href="{{ route('blog.show', $meta['related_blog_slug']) }}"
                                       class="inline-flex items-center gap-1 text-xs font-semibold text-indigo-600 hover:text-indigo-800 bg-indigo-50 hover:bg-indigo-100 px-3 py-1.5 rounded-full transition">
                                        📖 {{ __('Read the guide') }} →
                                    </a>
                                @endif
                            </div>

                            {{-- Step actions --}}
                            <form method="POST" action="{{ route('tools.application-tracker.step', $stepId) }}" class="mt-4 space-y-2">
                                @csrf
                                <textarea name="note" rows="2" maxlength="500" placeholder="{{ __('Optional note (your own progress, blockers, deadlines)') }}"
                                          class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">{{ $stepNote }}</textarea>
                                <div class="flex flex-wrap gap-2">
                                    <button type="submit" name="status" value="pending"
                                            class="px-3 py-1.5 rounded-lg border border-gray-300 text-gray-700 text-xs font-semibold hover:bg-gray-50 transition">
                                        ⚪ {{ __('Mark not started') }}
                                    </button>
                                    <button type="submit" name="status" value="in_progress"
                                            class="px-3 py-1.5 rounded-lg border border-amber-300 text-amber-700 text-xs font-semibold hover:bg-amber-50 transition">
                                        ⏳ {{ __('Mark in progress') }}
                                    </button>
                                    <button type="submit" name="status" value="completed"
                                            class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition">
                                        ✓ {{ __('Mark completed') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        {{-- Footer note --}}
        <p class="text-xs text-gray-500 mt-8 text-center">
            {{ __('Tracker is private — only you can see it. Anonymous trackers expire after 12 months of inactivity.') }}
        </p>
    @endif

</div>

@endsection
