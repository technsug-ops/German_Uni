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

        <h1 class="text-3xl md:text-5xl font-extrabold leading-tight drop-shadow mb-3 inline-flex items-center gap-3">
            <x-svg-icon name="map" class="w-8 h-8 md:w-10 md:h-10" />
            {{ __('My Germany Application Journey') }}
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
                <p class="text-sm text-primary-100 mt-4 inline-flex items-center gap-1.5 flex-wrap">
                    <x-svg-icon name="arrow-right" class="w-4 h-4" /> {{ __('Next up:') }} <strong class="text-white">{{ __($current['title']) }}</strong>
                    <span class="opacity-70">— ~{{ __($current['duration']) }}</span>
                </p>
            @else
                <p class="text-sm font-bold mt-4 inline-flex items-center gap-1.5"><x-svg-icon name="trophy" class="w-4 h-4" /> {{ __('All steps completed! Viel Erfolg in Deutschland!') }}</p>
            @endif
        </div>
    </div>
</section>

<div class="max-w-[1400px] mx-auto px-4 py-10">

    @if (session('status'))
        <div class="mb-6 bg-emerald-50 border border-emerald-200 rounded-lg px-4 py-3 text-emerald-800 text-sm inline-flex items-center gap-2">
            <x-svg-icon name="check-circle" class="w-4 h-4" />
            {{ session('status') }}
        </div>
    @endif

    @if ($isGuest ?? false)
        <div class="mb-6 bg-amber-50 border border-amber-200 rounded-xl p-4 flex items-start gap-3">
            <span class="text-amber-600"><x-svg-icon name="check-circle" class="w-6 h-6" /></span>
            <div class="flex-1 min-w-0">
                <p class="font-bold text-amber-900">{{ __('Guest mode — progress saved only on this device') }}</p>
                <p class="text-sm text-amber-800 mt-0.5">
                    {{ __('Tick steps freely. If you clear cookies or switch device, your progress will be lost.') }}
                    <a href="{{ route('login') }}" class="font-semibold text-amber-900 underline hover:text-amber-950">{{ __('Log in to save permanently') }}</a> →
                </p>
            </div>
        </div>
    @endif

    {{-- INFOGRAPHIC ROADMAP — Vertical timeline with phase grouping --}}
    @php
        // Phase tanımları — 8 step 3 evre'ye dağıtılmış
        $phases = [
            'preparation' => [
                'label'    => __('Phase 1 · Preparation'),
                'subtitle' => __('Verify eligibility, choose universities, prep language'),
                'icon'     => 'book-open',
                'flag'     => null,
                'range'    => [1, 3],
                'color'    => 'blue',     // blue-500/indigo-600 gradient
                'hex'      => '#4f46e5',
            ],
            'application' => [
                'label'    => __('Phase 2 · Application'),
                'subtitle' => __('Submit through uni-assist, wait for acceptance'),
                'icon'     => 'document-text',
                'flag'     => null,
                'range'    => [4, 6],
                'color'    => 'purple',   // purple-600/fuchsia-600
                'hex'      => '#a21caf',
            ],
            'arrival' => [
                'label'    => __('Phase 3 · Arriving in Germany'),
                'subtitle' => __('Sperrkonto, visa, Anmeldung, Krankenkasse'),
                'icon'     => null,
                'flag'     => '🇩🇪',
                'range'    => [7, 8],
                'color'    => 'emerald',  // emerald-600/teal-600
                'hex'      => '#059669',
            ],
        ];

        // step order'a göre grupla
        $stepsByPhase = [];
        foreach ($phases as $key => $phase) {
            $stepsByPhase[$key] = array_filter($steps, fn ($s) => $s['order'] >= $phase['range'][0] && $s['order'] <= $phase['range'][1]);
        }
    @endphp

    <div class="relative mb-12">
        @foreach ($phases as $phaseKey => $phase)
            @php
                $phaseSteps = $stepsByPhase[$phaseKey];
                $phaseDoneCount = collect($phaseSteps)->filter(fn ($s) => $tracker->isStepCompleted($s['key']))->count();
                $phaseTotalCount = count($phaseSteps);
                $phaseCompleted = $phaseDoneCount === $phaseTotalCount && $phaseTotalCount > 0;
            @endphp

            {{-- PHASE HEADER --}}
            <div class="flex items-center gap-3 mb-6 mt-{{ $loop->first ? '0' : '12' }}">
                <div class="flex-shrink-0 w-14 h-14 rounded-2xl flex items-center justify-center text-2xl shadow-lg ring-4 ring-white text-white"
                     style="background: linear-gradient(135deg, {{ $phase['hex'] }}, {{ $phase['hex'] }}cc);">
                    @if ($phase['flag'])
                        <span class="grayscale-0">{{ $phase['flag'] }}</span>
                    @else
                        <x-svg-icon name="{{ $phase['icon'] }}" class="w-7 h-7" />
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <h2 class="text-lg md:text-xl font-extrabold text-gray-900 leading-tight">{{ $phase['label'] }}</h2>
                    <p class="text-sm text-gray-500">{{ $phase['subtitle'] }}</p>
                </div>
                <div class="text-right shrink-0">
                    <div class="text-xs uppercase tracking-wider font-bold text-gray-400">{{ __('Progress') }}</div>
                    <div class="text-lg font-extrabold" style="color: {{ $phaseCompleted ? '#059669' : $phase['hex'] }};">
                        {{ $phaseDoneCount }}/{{ $phaseTotalCount }}
                        @if ($phaseCompleted) <x-svg-icon name="check" class="inline w-4 h-4" /> @endif
                    </div>
                </div>
            </div>

            {{-- VERTICAL TIMELINE for this phase --}}
            <div class="relative pl-3 md:pl-6">
                @foreach ($phaseSteps as $stepIndex => $step)
                    @php
                        $done = $tracker->isStepCompleted($step['key']);
                        $isCurrent = $current && $current['key'] === $step['key'];
                        $isLastInPhase = $loop->last;
                        $isLastOverall = $loop->parent->last && $loop->last;

                        // Circle (milestone) styling
                        $circleStyle = $done
                            ? 'background: linear-gradient(135deg, #10b981, #059669); color: white;'
                            : ($isCurrent
                                ? 'background: linear-gradient(135deg, ' . $phase['hex'] . ', ' . $phase['hex'] . 'cc); color: white;'
                                : 'background: white; color: ' . $phase['hex'] . '; border: 3px solid ' . $phase['hex'] . '40;');

                        $connectorStyle = $done
                            ? 'background: linear-gradient(to bottom, #10b981, #10b981);'
                            : 'background: linear-gradient(to bottom, ' . $phase['hex'] . '40, ' . $phase['hex'] . '20);';
                    @endphp

                    <div class="relative flex gap-4 md:gap-6 {{ ! $isLastInPhase ? 'pb-6' : '' }}">
                        {{-- Vertical connector line (continues from previous to next) --}}
                        @if (! $isLastOverall)
                            <div class="absolute left-7 md:left-8 top-16 bottom-0 w-1 rounded-full"
                                 style="{{ $connectorStyle }}"></div>
                        @endif

                        {{-- Milestone Circle --}}
                        <div class="relative shrink-0 z-10">
                            <div class="w-14 h-14 md:w-16 md:h-16 rounded-full flex flex-col items-center justify-center font-extrabold shadow-md ring-4 ring-white transition"
                                 style="{{ $circleStyle }}">
                                @if ($done)
                                    <x-svg-icon name="check" class="w-7 h-7" />
                                @else
                                    <span class="text-[10px] uppercase tracking-wider opacity-80">{{ __('Step') }}</span>
                                    <span class="text-lg leading-none">{{ $step['order'] }}</span>
                                @endif
                            </div>
                            @if ($isCurrent && ! $done)
                                <span class="absolute -top-1 -right-1 inline-flex w-4 h-4 rounded-full bg-amber-400 animate-ping"></span>
                                <span class="absolute -top-1 -right-1 inline-flex w-4 h-4 rounded-full bg-amber-500"></span>
                            @endif
                        </div>

                        {{-- Step Content Card --}}
                        <div class="flex-1 min-w-0 -mt-1">
                            <div class="bg-white border-2 rounded-xl p-4 md:p-5 transition shadow-sm hover:shadow-md
                                        {{ $done ? 'border-emerald-200 bg-emerald-50/40' : ($isCurrent ? 'shadow-md' : 'border-gray-200') }}"
                                 @if (! $done && ! $isCurrent) style="border-color: {{ $phase['hex'] }}30;" @endif
                                 @if ($isCurrent && ! $done) style="border-color: {{ $phase['hex'] }};" @endif>

                                <div class="flex items-baseline justify-between gap-3 flex-wrap mb-1">
                                    <h3 class="font-extrabold text-gray-900 text-base md:text-lg leading-snug">
                                        {{ __($step['title']) }}
                                        @if ($isCurrent && ! $done)
                                            <span class="ml-1.5 text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full text-white"
                                                  style="background: {{ $phase['hex'] }};">{{ __('Current') }}</span>
                                        @endif
                                        @if ($done)
                                            <span class="ml-1.5 inline-flex items-center gap-1 text-[10px] font-bold uppercase tracking-wider bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded-full"><x-svg-icon name="check" class="w-3 h-3" /> {{ __('Done') }}</span>
                                        @endif
                                    </h3>
                                    <span class="inline-flex items-center gap-1 text-xs text-gray-500 whitespace-nowrap font-semibold"><x-svg-icon name="clock" class="w-3.5 h-3.5" /> {{ __($step['duration']) }}</span>
                                </div>
                                <p class="text-sm text-gray-600 leading-relaxed">{{ __($step['desc']) }}</p>

                                <div class="flex flex-wrap items-center gap-3 mt-3">
                                    @if ($step['tool_route'])
                                        <a href="{{ route($step['tool_route']) }}"
                                           class="inline-flex items-center gap-1.5 text-sm font-bold px-3 py-1.5 rounded-lg shadow-sm transition
                                                  {{ $done ? 'bg-white border-2 border-emerald-300 text-emerald-700 hover:bg-emerald-100' : 'text-white hover:opacity-90' }}"
                                           @if (! $done) style="background: {{ $phase['hex'] }};" @endif>
                                            <x-svg-icon name="wrench-screwdriver" class="w-4 h-4" /> {{ __($step['tool_label'] ?? 'Open tool') }} →
                                        </a>
                                    @endif

                                    <form method="POST" action="{{ route('journey.step.toggle', $step['key']) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="text-xs font-semibold hover:underline {{ $done ? 'text-gray-500 hover:text-gray-700' : 'text-gray-600 hover:text-gray-900' }}">
                                            @if ($done) <span class="inline-flex items-center gap-1"><x-svg-icon name="arrow-path" class="w-3.5 h-3.5" /> {{ __('Mark as not done') }}</span>
                                            @else <span class="inline-flex items-center gap-1"><x-svg-icon name="check" class="w-3.5 h-3.5" /> {{ __('Mark as done') }}</span>
                                            @endif
                                        </button>
                                    </form>

                                    @auth
                                        <button type="button" x-data
                                                @click="$dispatch('step-expand-{{ $step['key'] }}')"
                                                class="text-xs font-semibold text-indigo-700 hover:underline">
                                            <x-svg-icon name="pencil" class="w-3.5 h-3.5" /> {{ $tracker->stepNote($step['key']) ? __('Edit note + deadline') : __('Add note / deadline') }}
                                        </button>
                                    @endauth
                                </div>

                                {{-- Step metadata: completed_at + deadline countdown + note --}}
                                @php
                                    $completedAt = $tracker->stepCompletedAt($step['key']);
                                    $deadline    = $tracker->stepDeadline($step['key']);
                                    $note        = $tracker->stepNote($step['key']);
                                @endphp

                                @if ($completedAt || $deadline || $note)
                                    <div class="mt-3 pt-3 border-t border-gray-100 flex flex-wrap gap-3 text-xs">
                                        @if ($completedAt)
                                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-md bg-emerald-50 text-emerald-800 font-semibold">
                                                <x-svg-icon name="check-circle" class="w-3.5 h-3.5" /> {{ __('Done :ago', ['ago' => $completedAt->diffForHumans()]) }}
                                            </span>
                                        @endif
                                        @if ($deadline)
                                            @php $daysLeft = (int) now()->startOfDay()->diffInDays($deadline->startOfDay(), false); @endphp
                                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-md font-semibold
                                                {{ $daysLeft < 0 ? 'bg-rose-50 text-rose-800' : ($daysLeft <= 7 ? 'bg-amber-50 text-amber-800' : 'bg-blue-50 text-blue-800') }}">
                                                <x-svg-icon name="calendar" class="w-3.5 h-3.5" /> {{ $deadline->translatedFormat('d M Y') }}
                                                @if ($daysLeft >= 0)
                                                    <span class="text-xs font-normal">({{ __(':n days left', ['n' => $daysLeft]) }})</span>
                                                @else
                                                    <span class="text-xs font-normal">({{ __('overdue') }})</span>
                                                @endif
                                            </span>
                                        @endif
                                        @if ($note)
                                            <div class="w-full mt-1 p-2 rounded-lg bg-gray-50 border border-gray-200 text-sm text-gray-700 whitespace-pre-line inline-flex items-start gap-2">
                                                <x-svg-icon name="chat" class="w-3.5 h-3.5 mt-1 shrink-0" /> {{ $note }}
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                {{-- Inline edit form (auth only) — toggled by Alpine listener --}}
                                @auth
                                    <div x-data="{ open: false }"
                                         x-show="open"
                                         x-cloak
                                         @step-expand-{{ $step['key'] }}.window="open = !open"
                                         class="mt-3 p-3 rounded-lg bg-indigo-50/50 border border-indigo-200">
                                        <form method="POST" action="{{ route('journey.step.update', $step['key']) }}" class="space-y-2">
                                            @csrf
                                            @method('PATCH')
                                            <div>
                                                <label class="block text-xs font-semibold text-gray-700 mb-1 inline-flex items-center gap-1"><x-svg-icon name="calendar" class="w-3.5 h-3.5" /> {{ __('Deadline (optional)') }}</label>
                                                <input type="date" name="deadline"
                                                       value="{{ $deadline?->toDateString() }}"
                                                       min="{{ now()->toDateString() }}"
                                                       class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-lg">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-semibold text-gray-700 mb-1 inline-flex items-center gap-1"><x-svg-icon name="chat" class="w-3.5 h-3.5" /> {{ __('Personal note (only you can see)') }}</label>
                                                <textarea name="note" rows="3" maxlength="2000"
                                                          placeholder="{{ __('e.g. Documents collected from TU Berlin, deadline 15 January') }}"
                                                          class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-lg">{{ $note }}</textarea>
                                            </div>
                                            <div class="flex justify-end gap-2">
                                                <button type="button" @click="open = false" class="text-xs font-semibold text-gray-600 hover:text-gray-900 px-3 py-1.5">{{ __('Cancel') }}</button>
                                                <button type="submit" class="text-xs font-bold bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded-md inline-flex items-center gap-1.5"><x-svg-icon name="check" class="w-3.5 h-3.5" /> {{ __('Save') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                @endauth
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Phase divider — "milestone reached" celebration --}}
            @if ($phaseCompleted && ! $loop->last)
                <div class="flex items-center gap-2 mt-6 mb-2 pl-12 md:pl-16">
                    <span class="text-emerald-600"><x-svg-icon name="trophy" class="w-6 h-6" /></span>
                    <span class="text-sm font-bold text-emerald-700">{{ __(':phase complete — next phase begins below', ['phase' => $phase['label']]) }}</span>
                </div>
            @endif
        @endforeach

        {{-- Final celebration when ALL done --}}
        @if ($done === $total && $total > 0)
            <div class="mt-8 bg-gradient-to-r from-amber-100 via-orange-100 to-rose-100 border-2 border-amber-300 rounded-2xl p-6 text-center">
                <div class="flex justify-center mb-2 text-amber-600"><x-svg-icon name="trophy" class="w-12 h-12" /></div>
                <h3 class="text-xl md:text-2xl font-extrabold text-gray-900 mb-1">{{ __('Viel Erfolg in Deutschland!') }}</h3>
                <p class="text-sm text-gray-700">{{ __('All 8 steps complete. You made it through the bureaucracy maze — share your story so the next student gets here faster.') }}</p>
            </div>
        @endif
    </div>

    {{-- PREFERENCES --}}
    <section class="bg-white border border-gray-200 rounded-xl p-6 mb-8">
        <h2 class="text-lg font-bold text-gray-900 mb-4 inline-flex items-center gap-2"><x-svg-icon name="cog" class="w-5 h-5" /> {{ __('My preferences') }}</h2>
        <form method="POST" action="{{ route('journey.update') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
            @csrf @method('PATCH')

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">{{ __('Target intake') }}</label>
                <select name="target_intake" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                    <option value="">{{ __('Choose...') }}</option>
                    {{-- Mevsim adı çevrilir (TR: Kış/Yaz), yıl olduğu gibi → dil sızıntısı önlenir. --}}
                    @foreach (['winter-2026' => ['Winter', 2026], 'summer-2027' => ['Summer', 2027], 'winter-2027' => ['Winter', 2027], 'summer-2028' => ['Summer', 2028]] as $val => [$season, $year])
                        <option value="{{ $val }}" @selected($tracker->target_intake === $val)>{{ __($season) }} {{ $year }}</option>
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
        <h2 class="text-lg font-bold text-gray-900 mb-3 inline-flex items-center gap-2"><x-svg-icon name="light-bulb" class="w-5 h-5" /> {{ __('Tips') }}</h2>
        <ul class="space-y-2 text-sm text-gray-700">
            <li>• {{ __('Steps don\'t have to be done in strict order — but most depend on each other.') }}</li>
            <li>• {{ __('Documents (Step 3) takes the longest — start early, in parallel with applications.') }}</li>
            <li>• {{ __('Open Sperrkonto (Step 6) only after you have the acceptance letter — embassies want fresh proof.') }}</li>
            <li>• {{ __('Visa appointment (Step 7) waits can be 2-3 months — book as soon as you have your acceptance letter.') }}</li>
        </ul>
    </section>
</div>
@endsection
