@extends('layouts.app')

@section('title', __('My Profile') . '  — ' . brand('name'))

@php
    $tabs = [
        'dashboard' => [__('Overview'),      '📊'],
        'profile'   => [__('My Profile'),    '👤'],
        'favorites' => [__('My Favorites'),  '❤️'],
        'activity'  => [__('My Activity'),   '🕒'],
        'quiz'      => [__('Quiz History'),  '🎯'],
    ];
    $activeTab = $tab ?? 'dashboard';

    $highSchoolLabels = [
        'anadolu' => __('Anadolu High School'), 'fen' => __('Science High School'),
        'duz' => __('General High School'), 'meslek' => __('Vocational High School'),
        'imam_hatip' => __('Imam Hatip High School'), 'other' => __('Other'),
    ];
    $statusLabels = [
        'lise_ogrencisi' => __('High school student'), 'lise_mezunu' => __('High school graduate'),
        'uni_ogrencisi' => __('University student'), 'uni_mezunu' => __('University graduate'),
        'calisan' => __('Working'), 'other' => __('Other'),
    ];
    $degreeLabels = [
        'bachelor' => 'Bachelor', 'master' => 'Master', 'phd' => __('Doctorate'),
        'studienkolleg' => 'Studienkolleg', 'other' => __('Other'),
    ];
@endphp

@section('content')

{{-- HERO --}}
<section class="bg-gradient-to-br from-primary-700 via-primary-600 to-primary-800 text-white">
    <div class="max-w-6xl mx-auto px-4 py-8">
        <div class="flex items-center gap-4 mb-3">
            <div class="w-16 h-16 rounded-full bg-accent-500 flex items-center justify-center text-3xl font-extrabold">
                {{ strtoupper(mb_substr($user->name, 0, 1)) }}
            </div>
            <div>
                <h1 class="text-2xl md:text-3xl font-extrabold">{{ $user->name }}</h1>
                <p class="text-primary-100 text-sm">{{ $user->email }}</p>
                <div class="flex flex-wrap gap-1.5 mt-1.5">
                    @if ($user->is_admin)
                        <span class="inline-block bg-accent-500 text-xs font-bold uppercase tracking-wider px-2 py-0.5 rounded-full">⚙️ {{ __('Admin') }}</span>
                    @endif
                    @if ($user->is_author)
                        <span class="inline-block bg-indigo-500 text-xs font-bold uppercase tracking-wider px-2 py-0.5 rounded-full">✍️ {{ $user->role_label ?: __('Author') }}</span>
                    @endif
                    @if ($user->is_contributor)
                        <span class="inline-block bg-emerald-500 text-xs font-bold uppercase tracking-wider px-2 py-0.5 rounded-full" title="{{ __('Approved community contributor') }}">🌱 {{ __('Community Contributor') }}</span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Profil tamamlama progress --}}
        @if ($user->profile_completion < 100)
            <div class="bg-white/10 backdrop-blur border border-white/20 rounded-lg p-3 text-sm">
                <div class="flex items-center justify-between mb-2">
                    <span>{{ __('Profile completion') }}: <strong>{{ $user->profile_completion }}%</strong></span>
                    <a href="{{ route('profile.edit', ['tab' => 'profile']) }}" class="underline hover:text-white">{{ __('Complete profile') }} →</a>
                </div>
                <div class="w-full bg-white/20 rounded-full h-2 overflow-hidden">
                    <div class="bg-accent-500 h-full transition-all" style="width: {{ $user->profile_completion }}%;"></div>
                </div>
            </div>
        @endif
    </div>
</section>

{{-- TAB BAR --}}
<nav class="bg-white border-b border-gray-200 sticky top-16 z-30 shadow-sm">
    <div class="max-w-6xl mx-auto px-4">
        <div class="flex overflow-x-auto -mb-px">
            @foreach ($tabs as $k => [$label, $emoji])
                <a href="{{ route('profile.edit', ['tab' => $k]) }}"
                   class="px-4 py-3 text-sm font-semibold border-b-2 whitespace-nowrap transition
                          {{ $activeTab === $k
                                ? 'border-primary-500 text-primary-700'
                                : 'border-transparent text-gray-600 hover:text-primary-600 hover:border-primary-200' }}">
                    {{ $emoji }} {{ $label }}
                </a>
            @endforeach
        </div>
    </div>
</nav>

<div class="max-w-6xl mx-auto px-4 py-8">

@if (session('status') === 'profile-updated')
    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6 text-green-800">
        ✓ {{ __('Profile updated.') }}
    </div>
@endif

{{-- ============================================ --}}
{{-- DASHBOARD                                       --}}
{{-- ============================================ --}}
@if ($activeTab === 'dashboard')

    {{-- ════ ONBOARDING (yeni kullanıcı/düşük skor) ════ --}}
    @php
        $favCount = $user->favorites()->count();
        $quizCount = $user->quizResults()->count();
        $steps = [
            ['key' => 'profile', 'done' => $user->profile_completion >= 75,
             'emoji' => '👤', 'title' => __('Complete your profile'),
             'desc' => __('Enter target field, degree and budget — we will give you personalised recommendations'),
             'cta' => __('Go to profile'), 'url' => route('profile.edit', ['tab' => 'profile'])],
            ['key' => 'quiz', 'done' => $quizCount >= 1,
             'emoji' => '🎯', 'title' => __('Take the quiz'),
             'desc' => __('Answer 5 questions and discover the universities that fit you best'),
             'cta' => __('Start the quiz'), 'url' => route('tools.recommendation')],
            ['key' => 'favorites', 'done' => $favCount >= 3,
             'emoji' => '❤️', 'title' => __('Add 3+ favorites'),
             'desc' => __('Favorite the universities/programs you like and add them to your compare list'),
             'cta' => __('Browse universities'), 'url' => route('universities.index')],
            ['key' => 'cities', 'done' => $user->activities()->where('viewable_type', \App\Models\City::class)->count() >= 3,
             'emoji' => '🏙️', 'title' => __('Explore cities'),
             'desc' => __('Browse at least 3 city pages — cost of living, culture, universities'),
             'cta' => __('Browse cities'), 'url' => route('cities.index')],
        ];
        $completedSteps = collect($steps)->where('done', true)->count();
        $isOnboardingActive = $user->almanyauni_score < 50 && $completedSteps < 4;
    @endphp

    @if ($isOnboardingActive)
        <section class="bg-gradient-to-br from-amber-50 via-orange-50 to-rose-50 ring-1 ring-amber-200 rounded-2xl p-6 mb-8 shadow-sm">
            <div class="flex items-start justify-between gap-4 mb-5 flex-wrap">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-100 text-amber-800 text-xs font-bold uppercase tracking-wider mb-2">
                        🚀 {{ __('Getting Started') }}
                    </div>
                    <h2 class="text-2xl font-extrabold text-gray-900">{{ __('Welcome, :name!', ['name' => $user->name]) }}</h2>
                    <p class="text-gray-600 mt-1">
                        @if ($completedSteps === 0)
                            {{ __('Let\'s take 4 small steps to make the most of AlmanyaUni.') }}
                        @else
                            <strong class="text-amber-700">{{ $completedSteps }}/4</strong> {{ __('steps completed.') }}
                            @if ($completedSteps < 4) {{ __(':n steps to go.', ['n' => 4 - $completedSteps]) }} @endif
                        @endif
                    </p>
                </div>
                <div class="text-right text-sm">
                    <div class="text-3xl font-extrabold text-amber-700">{{ $completedSteps }}/4</div>
                    <p class="text-xs text-gray-500">{{ __('completed') }}</p>
                </div>
            </div>

            {{-- Progress bar --}}
            <div class="w-full bg-amber-100 rounded-full h-2 overflow-hidden mb-5">
                <div class="bg-gradient-to-r from-amber-400 to-orange-500 h-full transition-all"
                     style="width: {{ ($completedSteps / 4) * 100 }}%;"></div>
            </div>

            {{-- Steps --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @foreach ($steps as $i => $step)
                    <div class="flex items-start gap-3 bg-white rounded-lg p-4 ring-1 {{ $step['done'] ? 'ring-emerald-300 bg-emerald-50/30' : 'ring-gray-200' }}">
                        <div class="shrink-0 w-10 h-10 rounded-full flex items-center justify-center text-xl {{ $step['done'] ? 'bg-emerald-100' : 'bg-amber-100' }}">
                            @if ($step['done'])
                                <span class="text-emerald-600">✓</span>
                            @else
                                <span>{{ $step['emoji'] }}</span>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-xs font-bold text-gray-400 uppercase">{{ __('Step') }} {{ $i + 1 }}</span>
                                @if ($step['done'])
                                    <span class="text-xs font-semibold text-emerald-600">✓ {{ __('Done') }}</span>
                                @endif
                            </div>
                            <h3 class="font-bold text-gray-900 leading-tight {{ $step['done'] ? 'line-through opacity-60' : '' }}">{{ $step['title'] }}</h3>
                            <p class="text-xs text-gray-600 mt-1">{{ $step['desc'] }}</p>
                            @if (!$step['done'])
                                <a href="{{ $step['url'] }}"
                                   class="inline-block mt-2 text-xs font-semibold text-amber-700 hover:text-amber-900 hover:underline">
                                    {{ $step['cta'] }} →
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @elseif ($completedSteps === 4)
        <section class="bg-gradient-to-r from-emerald-500 to-teal-500 text-white rounded-2xl p-6 mb-8 shadow-md">
            <div class="flex items-center gap-4">
                <div class="text-5xl">🎉</div>
                <div class="flex-1">
                    <h2 class="text-xl font-extrabold">{{ __('Congratulations, getting-started guide completed!') }}</h2>
                    <p class="text-emerald-100 text-sm mt-1">{{ __('You\'re now using AlmanyaUni to the fullest. Push your score to 80+ to reach the 🏆 Ready level.') }}</p>
                </div>
            </div>
        </section>
    @endif

    {{-- ════ ALMANYAUNI SKOR ════ --}}
    @php
        $score = $user->almanyauni_score;
        $level = $user->almanyauni_level;
        $badges = $user->almanyauni_badges;
        $levelColor = $level['color'];
    @endphp
    <section class="bg-gradient-to-br from-{{ $levelColor }}-50 via-white to-primary-50 ring-1 ring-{{ $levelColor }}-200 rounded-2xl p-6 mb-8 shadow-sm">
        <div class="flex items-start justify-between gap-4 flex-wrap">
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-2">
                    <span class="text-xs font-semibold uppercase tracking-wider text-{{ $levelColor }}-700">{{ __('AlmanyaUni Score') }}</span>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-{{ $levelColor }}-100 text-{{ $levelColor }}-700 text-xs font-bold">
                        {{ $level['emoji'] }} {{ $level['name'] }}
                    </span>
                </div>

                <div class="flex items-baseline gap-2 mb-3">
                    <span class="text-5xl font-extrabold text-gray-900">{{ $score }}</span>
                    <span class="text-2xl text-gray-400">/100</span>
                </div>

                <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden mb-3">
                    <div class="bg-gradient-to-r from-{{ $levelColor }}-400 to-{{ $levelColor }}-600 h-full transition-all"
                         style="width: {{ $score }}%;"></div>
                </div>

                {{-- Tier ladder --}}
                <div class="flex items-center gap-1 text-xs">
                    @foreach ([
                        1 => ['🌱', __('Newcomer'), 0],
                        2 => ['📚', __('Researcher'), 20],
                        3 => ['🎯', __('Decision maker'), 40],
                        4 => ['✈️', __('On the way to Germany'), 60],
                        5 => ['🏆', __('Ready'), 80],
                    ] as $tier => [$emoji, $name, $threshold])
                        <div class="flex-1 text-center">
                            <div class="text-lg {{ $level['tier'] >= $tier ? 'opacity-100' : 'opacity-30' }}">{{ $emoji }}</div>
                            <div class="text-[10px] {{ $level['tier'] >= $tier ? 'text-gray-700 font-semibold' : 'text-gray-400' }}">{{ $threshold }}+</div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Rozetler --}}
            @if (!empty($badges))
                <div class="bg-white rounded-xl p-4 ring-1 ring-gray-200 min-w-[200px]">
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2">🏅 {{ __('Badges') }} ({{ count($badges) }})</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($badges as $b)
                            <span title="{{ $b['desc'] }}"
                                  class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-gray-50 ring-1 ring-gray-200 text-xs font-medium hover:bg-amber-50 hover:ring-amber-300 transition">
                                {{ $b['emoji'] }} {{ $b['name'] }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        {{-- Skor artırma ipuçları --}}
        @if ($score < 100)
            <div class="mt-5 pt-5 border-t border-gray-200">
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2">📈 {{ __('How to boost your score?') }}</p>
                <div class="flex flex-wrap gap-2 text-xs">
                    @if ($user->profile_completion < 100)
                        <a href="{{ route('profile.edit', ['tab' => 'profile']) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full bg-amber-100 hover:bg-amber-200 text-amber-800 transition">
                            👤 {{ __('Complete profile') }} (+{{ 30 - round($user->profile_completion * 0.30) }})
                        </a>
                    @endif
                    @if ($user->favorites()->count() < 15)
                        <a href="{{ route('universities.index') }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full bg-pink-100 hover:bg-pink-200 text-pink-800 transition">
                            ❤️ {{ __('Add favorites (+2 each)') }}
                        </a>
                    @endif
                    @if ($user->quizResults()->count() < 1)
                        <a href="{{ route('tools.recommendation') }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full bg-blue-100 hover:bg-blue-200 text-blue-800 transition">
                            🎯 {{ __('Take the quiz (+5)') }}
                        </a>
                    @endif
                </div>
            </div>
        @endif
    </section>

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">{{ __('University') }}</p>
            <p class="text-3xl font-extrabold text-primary-700">{{ $stats['universities'] }}</p>
            <p class="text-xs text-gray-500 mt-1">{{ __('in my favorites') }}</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">{{ __('Program') }}</p>
            <p class="text-3xl font-extrabold text-primary-700">{{ $stats['programs'] }}</p>
            <p class="text-xs text-gray-500 mt-1">{{ __('in my favorites') }}</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">{{ __('Profession') }}</p>
            <p class="text-3xl font-extrabold text-primary-700">{{ $stats['professions'] }}</p>
            <p class="text-xs text-gray-500 mt-1">{{ __('in my favorites') }}</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">{{ __('Activity') }}</p>
            <p class="text-3xl font-extrabold text-accent-600">{{ $stats['activities'] }}</p>
            <p class="text-xs text-gray-500 mt-1">{{ __('page views') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Son favoriler --}}
        <section class="bg-white border border-gray-200 rounded-xl p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-bold text-gray-900">❤️ {{ __('Recent Favorites') }}</h2>
                <a href="{{ route('profile.edit', ['tab' => 'favorites']) }}" class="text-xs font-semibold text-primary-600 hover:text-primary-800">{{ __('All') }} →</a>
            </div>
            @if ($recent_favorites->isEmpty())
                <p class="text-sm text-gray-500">{!! __('You have not added any favorites yet. On a university, program or profession page click the <strong>❤ Favorite</strong> button.') !!}</p>
            @else
                <ul class="space-y-2">
                    @foreach ($recent_favorites as $f)
                        @php
                            $item = $f->favoriteable;
                            $url = match (true) {
                                $item instanceof \App\Models\University => route('universities.show', $item->slug),
                                $item instanceof \App\Models\Program    => route('programs.show', $item->slug),
                                $item instanceof \App\Models\Profession => route('professions.show', $item->slug),
                                default => '#',
                            };
                            $typeLabel = match (true) {
                                $item instanceof \App\Models\University => __('University'),
                                $item instanceof \App\Models\Program    => __('Program'),
                                $item instanceof \App\Models\Profession => __('Profession'),
                                default => '',
                            };
                        @endphp
                        <li>
                            <a href="{{ $url }}" class="flex items-center justify-between gap-2 p-2 hover:bg-gray-50 rounded transition">
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-semibold text-gray-900 truncate">{{ $item?->name_de ?? __('(deleted)') }}</p>
                                    <p class="text-xs text-gray-500">{{ $typeLabel }} · {{ $f->created_at->diffForHumans() }}</p>
                                </div>
                                <span class="text-pink-500">❤</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>

        {{-- Son aktivite --}}
        <section class="bg-white border border-gray-200 rounded-xl p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-bold text-gray-900">🕒 {{ __('Recent Activity') }}</h2>
                <a href="{{ route('profile.edit', ['tab' => 'activity']) }}" class="text-xs font-semibold text-primary-600 hover:text-primary-800">{{ __('All') }} →</a>
            </div>
            @if ($recent_activities->isEmpty())
                <p class="text-sm text-gray-500">{{ __('No activity yet. As you view universities, programs or professions they will appear here.') }}</p>
            @else
                <ul class="space-y-2">
                    @foreach ($recent_activities as $a)
                        @php
                            $item = $a->viewable;
                            $url = match (true) {
                                $item instanceof \App\Models\University => route('universities.show', $item->slug),
                                $item instanceof \App\Models\Program    => route('programs.show', $item->slug),
                                $item instanceof \App\Models\Profession => route('professions.show', $item->slug),
                                default => '#',
                            };
                        @endphp
                        <li>
                            <a href="{{ $url }}" class="flex items-center justify-between gap-2 p-2 hover:bg-gray-50 rounded transition">
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-semibold text-gray-900 truncate">{{ $a->label ?: __('Deleted record') }}</p>
                                    <p class="text-xs text-gray-500">{{ $a->viewed_at?->diffForHumans() }}</p>
                                </div>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>
    </div>

    {{-- Hızlı CTA --}}
    <section class="mt-8 bg-gradient-to-br from-accent-50 to-white border border-accent-200 rounded-xl p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-3">🚀 {{ __('What\'s next?') }}</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-sm">
            <a href="{{ route('tools.recommendation') }}" class="block bg-white border border-gray-200 hover:border-primary-400 rounded-lg p-4 transition">
                <p class="font-semibold text-gray-900">🎯 {{ __('Uni Quiz') }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ __('Universities matching you in 5 questions') }}</p>
            </a>
            <a href="{{ route('tools.cost-of-living') }}" class="block bg-white border border-gray-200 hover:border-primary-400 rounded-lg p-4 transition">
                <p class="font-semibold text-gray-900">💰 {{ __('Cost of Living') }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ __('Estimate your target city') }}</p>
            </a>
            <a href="{{ route('professions.index') }}" class="block bg-white border border-gray-200 hover:border-primary-400 rounded-lg p-4 transition">
                <p class="font-semibold text-gray-900">💼 {{ __('Professions') }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ __('3,560 professions from BERUFENET') }}</p>
            </a>
        </div>
    </section>

{{-- ============================================ --}}
{{-- PROFILE FORM                                    --}}
{{-- ============================================ --}}
@elseif ($activeTab === 'profile')

    <form method="POST" action="{{ route('profile.update') }}" class="space-y-6">
        @csrf
        @method('PATCH')

        <section class="bg-white border border-gray-200 rounded-xl p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4">{{ __('Basic Information') }}</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">{{ __('Full Name') }} *</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-primary-500 focus:outline-none">
                    @error('name')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">{{ __('Email') }} *</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-primary-500 focus:outline-none">
                    @error('email')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </section>

        <section class="bg-white border border-gray-200 rounded-xl p-6">
            @if (app()->getLocale() === 'tr')
                <h2 class="text-lg font-bold text-gray-900 mb-1">🇹🇷 {{ __('Turkish Student Information') }}</h2>
            @else
                <h2 class="text-lg font-bold text-gray-900 mb-1">🎓 {{ __('Student Background') }}</h2>
            @endif
            <p class="text-sm text-gray-500 mb-4">{!! __('This information is used so we can give you <strong>personalised recommendations</strong>. None of it is required.') !!}</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">{{ __('High school type') }}</label>
                    <select name="high_school_type" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-primary-500 focus:outline-none">
                        <option value="">— {{ __('Select') }} —</option>
                        @foreach ($highSchoolLabels as $k => $label)
                            <option value="{{ $k }}" @selected(old('high_school_type', $user->high_school_type) === $k)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">{{ __('Important for Studienkolleg requirement') }}</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">{{ __('Current status') }}</label>
                    <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-primary-500 focus:outline-none">
                        <option value="">— {{ __('Select') }} —</option>
                        @foreach ($statusLabels as $k => $label)
                            <option value="{{ $k }}" @selected(old('status', $user->status) === $k)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">{{ __('My German level') }}</label>
                    <input type="text" name="german_level" value="{{ old('german_level', $user->german_level) }}"
                           placeholder="{{ __('A1, B2, TestDaF-4, DSH-2, etc.') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-primary-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">{{ __('My English level') }}</label>
                    <input type="text" name="english_level" value="{{ old('english_level', $user->english_level) }}"
                           placeholder="{{ __('B2, IELTS 6.5, TOEFL 90, etc.') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-primary-500 focus:outline-none">
                </div>
            </div>
        </section>

        <section class="bg-white border border-gray-200 rounded-xl p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4">🎯 {{ __('My Goals') }}</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">{{ __('Target field') }}</label>
                    <select name="target_field_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-primary-500 focus:outline-none">
                        <option value="">— {{ __('Select') }} —</option>
                        @foreach ($fields as $f)
                            <option value="{{ $f->id }}" @selected(old('target_field_id', $user->target_field_id) == $f->id)>{{ $f->icon }} {{ $f->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">{{ __('Target degree') }}</label>
                    <select name="target_degree" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-primary-500 focus:outline-none">
                        <option value="">— {{ __('Select') }} —</option>
                        @foreach ($degreeLabels as $k => $label)
                            <option value="{{ $k }}" @selected(old('target_degree', $user->target_degree) === $k)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">{{ __('Target semester') }}</label>
                    <input type="text" name="target_semester" value="{{ old('target_semester', $user->target_semester) }}"
                           placeholder="{{ __('Winter 2026, Summer 2027, etc.') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-primary-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">{{ __('Monthly budget (€)') }}</label>
                    <input type="number" name="monthly_budget_eur" value="{{ old('monthly_budget_eur', $user->monthly_budget_eur) }}"
                           min="0" max="99999" placeholder="800"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-primary-500 focus:outline-none">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">{{ __('Preferred state') }}</label>
                    <select name="preferred_state_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-primary-500 focus:outline-none">
                        <option value="">— {{ __('No preference') }} —</option>
                        @foreach ($states as $s)
                            <option value="{{ $s->id }}" @selected(old('preferred_state_id', $user->preferred_state_id) == $s->id)>{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">{{ __('About me') }}</label>
                    <textarea name="bio" rows="3" maxlength="1000"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-primary-500 focus:outline-none"
                              placeholder="{{ __('Tell us a bit about yourself and your goals…') }}">{{ old('bio', $user->bio) }}</textarea>
                </div>
            </div>
        </section>

        <div class="flex justify-end gap-3">
            <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white font-semibold px-6 py-2.5 rounded-lg transition">
                {{ __('Save') }}
            </button>
        </div>
    </form>

    {{-- Şifre değiştirme + hesap silme — Breeze partials --}}
    <section class="mt-10 bg-white border border-gray-200 rounded-xl p-6">
        @include('profile.partials.update-password-form')
    </section>

    <section class="mt-6 bg-red-50 border border-red-200 rounded-xl p-6">
        @include('profile.partials.delete-user-form')
    </section>

{{-- ============================================ --}}
{{-- FAVORITES                                       --}}
{{-- ============================================ --}}
@elseif ($activeTab === 'favorites')

    {{-- Üniversiteler — image-rich + compare seçimi --}}
    <section class="bg-white border border-gray-200 rounded-xl p-6 mb-6">
        <div class="flex items-center justify-between mb-4 flex-wrap gap-2">
            <h2 class="font-bold text-gray-900">❤️ {{ __('Universities') }} <span class="text-gray-500 font-normal">({{ $fav_universities->count() }})</span></h2>
            @if ($fav_universities->count() >= 2)
                <p class="text-xs text-gray-500">{{ __('Pick 2-4 → Compare') }}</p>
            @endif
        </div>
        @if ($fav_universities->isEmpty())
            <p class="text-sm text-gray-500">{{ __('No favorite universities yet.') }} <a href="{{ route('universities.index') }}" class="text-primary-600 hover:underline">{{ __('Browse universities') }} →</a></p>
        @else
            <form id="favUniCompareForm" action="{{ route('compare.show') }}" method="GET">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach ($fav_universities as $f)
                        @php $item = $f->favoriteable; @endphp
                        @if ($item)
                            <div class="group relative bg-white rounded-xl overflow-hidden border border-gray-200 hover:border-primary-500 hover:shadow-md transition flex flex-col">
                                <label class="absolute top-2 left-2 z-10 cursor-pointer">
                                    <input type="checkbox" name="favSlugs[]" value="{{ $item->slug }}"
                                           class="w-5 h-5 rounded border-gray-300 bg-white shadow text-primary-600 focus:ring-primary-500 favoriteCheckbox">
                                </label>
                                <a href="{{ route('universities.show', $item->slug) }}" class="block">
                                    <div class="aspect-[16/9] overflow-hidden bg-gray-100 relative">
                                        @if($item->image_url)
                                            <img src="{{ $item->image_url }}" alt="" loading="lazy"
                                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                        @else
                                            <div class="w-full h-full bg-gradient-to-br from-primary-500 to-accent-500 flex items-center justify-center">
                                                <span class="text-3xl font-extrabold text-white/90">{{ mb_substr($item->name_de, 0, 2) }}</span>
                                            </div>
                                        @endif
                                        @if($item->logo_url && $item->image_url)
                                            <div class="absolute bottom-2 left-2 w-10 h-10 bg-white rounded-lg ring-1 ring-white/60 shadow p-1 flex items-center justify-center">
                                                <img src="{{ $item->logo_url }}" alt="" class="max-w-full max-h-full object-contain" loading="lazy" decoding="async"/>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="p-3">
                                        <h3 class="font-bold text-gray-900 group-hover:text-primary-600 leading-tight line-clamp-2 text-sm">{{ $item->name_de }}</h3>
                                        @if($item->city)
                                            <p class="text-xs text-gray-500 mt-1">📍 {{ $item->city->name }}</p>
                                        @endif
                                    </div>
                                </a>
                                {{-- Not alanı --}}
                                <div class="px-3 pb-3">
                                    <details class="text-xs">
                                        <summary class="cursor-pointer text-gray-500 hover:text-primary-600">
                                            @if($f->note) 📝 {{ __('Note') }} <span class="text-gray-400">({{ __('edit') }})</span>
                                            @else ➕ {{ __('Add note') }} @endif
                                        </summary>
                                        <textarea class="favNoteInput w-full mt-2 px-2 py-1 text-xs border border-gray-300 rounded focus:border-primary-500 focus:outline-none"
                                                  rows="2" placeholder="{{ __('My note about this university…') }}"
                                                  data-favorite-id="{{ $f->id }}">{{ $f->note }}</textarea>
                                        <div class="flex justify-end mt-1">
                                            <button type="button" class="favNoteSave text-xs px-2 py-0.5 bg-primary-600 hover:bg-primary-700 text-white rounded"
                                                    data-favorite-id="{{ $f->id }}">{{ __('Save') }}</button>
                                        </div>
                                    </details>
                                    @if($f->note)
                                        <p class="text-xs text-gray-600 italic mt-1 line-clamp-2 favNoteDisplay-{{ $f->id }}">{{ $f->note }}</p>
                                    @endif
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
                <div id="favCompareBar"
                     class="hidden fixed bottom-4 left-1/2 -translate-x-1/2 z-40 bg-primary-700 text-white rounded-full shadow-lg px-6 py-3 flex items-center gap-4">
                    <span class="text-sm font-semibold"><span id="favCount">0</span> {{ __('selected') }}</span>
                    <button type="submit" name="action" value="compare"
                            class="bg-accent-500 hover:bg-accent-600 px-4 py-1.5 rounded-full text-sm font-bold transition">
                        ⚖️ {{ __('Compare') }} →
                    </button>
                </div>
            </form>
        @endif
    </section>

    {{-- Programlar --}}
    <section class="bg-white border border-gray-200 rounded-xl p-6 mb-6">
        <h2 class="font-bold text-gray-900 mb-4">❤️ {{ __('Programs') }} <span class="text-gray-500 font-normal">({{ $fav_programs->count() }})</span></h2>
        @if ($fav_programs->isEmpty())
            <p class="text-sm text-gray-500">{{ __('No favorite programs yet.') }} <a href="{{ route('programs.index') }}" class="text-primary-600 hover:underline">{{ __('Browse programs') }} →</a></p>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @foreach ($fav_programs as $f)
                    @php $item = $f->favoriteable; @endphp
                    @if ($item)
                        <a href="{{ route('programs.show', $item->slug) }}"
                           class="block bg-gray-50 hover:bg-primary-50 border border-gray-200 hover:border-primary-400 rounded-lg p-4 transition">
                            <div class="flex items-start justify-between gap-2">
                                <div class="flex-1 min-w-0">
                                    <p class="font-semibold text-gray-900 leading-snug">{{ $item->name_de }}</p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        @if($item->degree){{ ucfirst($item->degree) }} · @endif
                                        @if($item->university) {{ $item->university->display_name }} @endif
                                    </p>
                                </div>
                                @if($item->field)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium text-white shrink-0"
                                          style="background-color: {{ $item->field->color }};">
                                        {{ $item->field->icon }}
                                    </span>
                                @endif
                            </div>
                        </a>
                    @endif
                @endforeach
            </div>
        @endif
    </section>

    {{-- Meslekler --}}
    <section class="bg-white border border-gray-200 rounded-xl p-6 mb-6">
        <h2 class="font-bold text-gray-900 mb-4">❤️ {{ __('Professions') }} <span class="text-gray-500 font-normal">({{ $fav_professions->count() }})</span></h2>
        @if ($fav_professions->isEmpty())
            <p class="text-sm text-gray-500">{{ __('No favorite professions yet.') }}</p>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @foreach ($fav_professions as $f)
                    @php $item = $f->favoriteable; @endphp
                    @if ($item)
                        <a href="{{ route('professions.show', $item->slug) }}"
                           class="block bg-gray-50 hover:bg-primary-50 border border-gray-200 hover:border-primary-400 rounded-lg p-4 transition">
                            <p class="font-semibold text-gray-900 leading-snug">{{ $item->name_de }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ $f->created_at->diffForHumans() }}</p>
                        </a>
                    @endif
                @endforeach
            </div>
        @endif
    </section>

    {{-- Compare bar + Note save JS --}}
    @push('scripts')
    <script>
        (function() {
            const checks = document.querySelectorAll('.favoriteCheckbox');
            const bar = document.getElementById('favCompareBar');
            const count = document.getElementById('favCount');
            const form = document.getElementById('favUniCompareForm');

            function update() {
                const sel = document.querySelectorAll('.favoriteCheckbox:checked');
                count.textContent = sel.length;
                if (sel.length >= 2 && sel.length <= 4) {
                    bar.classList.remove('hidden');
                } else if (sel.length > 4) {
                    bar.classList.remove('hidden');
                    count.innerHTML = sel.length + ' <small>({{ __('max 4') }})</small>';
                } else {
                    bar.classList.add('hidden');
                }
            }
            checks.forEach(c => c.addEventListener('change', update));

            if (form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const sel = Array.from(document.querySelectorAll('.favoriteCheckbox:checked'))
                                     .slice(0, 4)
                                     .map(c => c.value);
                    if (sel.length < 2) return;
                    window.location = '{{ route('compare.show') }}?slugs=' + sel.join(',');
                });
            }

            // Note save (ajax)
            document.querySelectorAll('.favNoteSave').forEach(btn => {
                btn.addEventListener('click', async function() {
                    const id = this.dataset.favoriteId;
                    const textarea = document.querySelector(`.favNoteInput[data-favorite-id="${id}"]`);
                    const note = textarea.value;
                    btn.textContent = @json(__('Saving…'));
                    btn.disabled = true;
                    try {
                        const res = await fetch('/profile/favorites/' + id + '/note', {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({ note: note }),
                        });
                        if (res.ok) {
                            btn.textContent = @json('✓ ' . __('Saved'));
                            const disp = document.querySelector('.favNoteDisplay-' + id);
                            if (disp) disp.textContent = note;
                            setTimeout(() => { btn.textContent = @json(__('Save')); btn.disabled = false; }, 1500);
                        } else {
                            btn.textContent = @json(__('Error!')); setTimeout(() => { btn.textContent = @json(__('Save')); btn.disabled = false; }, 1500);
                        }
                    } catch (e) {
                        btn.textContent = @json(__('Error!')); setTimeout(() => { btn.textContent = @json(__('Save')); btn.disabled = false; }, 1500);
                    }
                });
            });
        })();
    </script>
    @endpush

{{-- ============================================ --}}
{{-- ACTIVITY                                        --}}
{{-- ============================================ --}}
@elseif ($activeTab === 'activity')

    <section class="bg-white border border-gray-200 rounded-xl p-6">
        <h2 class="font-bold text-gray-900 mb-4">🕒 {{ __('Recently Viewed') }}</h2>
        @if ($activities->isEmpty())
            <p class="text-sm text-gray-500">{{ __('No activity yet.') }}</p>
        @else
            <ul class="divide-y divide-gray-100">
                @foreach ($activities as $a)
                    @php
                        $item = $a->viewable;
                        if (!$item) continue;
                        $url = match (true) {
                            $item instanceof \App\Models\University => route('universities.show', $item->slug),
                            $item instanceof \App\Models\Program    => route('programs.show', $item->slug),
                            $item instanceof \App\Models\Profession => route('professions.show', $item->slug),
                            default => '#',
                        };
                        $type = class_basename($a->viewable_type);
                    @endphp
                    <li class="py-3">
                        <a href="{{ $url }}" class="flex items-center justify-between gap-3 hover:bg-gray-50 rounded p-2 -m-2 transition">
                            <div class="min-w-0 flex-1">
                                <p class="font-semibold text-gray-900 truncate">{{ $a->label ?? $item->name_de }}</p>
                                <p class="text-xs text-gray-500">{{ $type }} · {{ $a->viewed_at?->diffForHumans() }}</p>
                            </div>
                            <span class="text-gray-400">→</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif
    </section>

{{-- ============================================ --}}
{{-- QUIZ HISTORY                                    --}}
{{-- ============================================ --}}
@elseif ($activeTab === 'quiz')

    <section class="bg-white border border-gray-200 rounded-xl p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-bold text-gray-900">🎯 {{ __('Quiz History') }}</h2>
            <a href="{{ route('tools.recommendation') }}" class="text-sm font-semibold bg-accent-500 hover:bg-accent-600 text-white px-3 py-1.5 rounded-lg transition">
                {{ __('New Quiz') }}
            </a>
        </div>
        @if ($quiz_results->isEmpty())
            <p class="text-sm text-gray-500 mb-4">{{ __('You haven\'t taken the quiz yet.') }}</p>
            <a href="{{ route('tools.recommendation') }}" class="inline-block bg-primary-600 hover:bg-primary-700 text-white font-semibold px-5 py-2.5 rounded-lg transition">
                🎯 {{ __('Start the University Recommendation Quiz') }}
            </a>
        @else
            <div class="space-y-4">
                @foreach ($quiz_results as $q)
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-start justify-between mb-2">
                            <p class="font-semibold text-gray-900">
                                @switch($q->quiz_type)
                                    @case('recommendation') {{ __('University Recommendation') }} @break
                                    @default {{ $q->quiz_type }}
                                @endswitch
                            </p>
                            <span class="text-xs text-gray-500">{{ $q->created_at->diffForHumans() }}</span>
                        </div>
                        @if (! empty($q->result['university_names']))
                            <p class="text-sm text-gray-600 mb-2">{{ __(':n universities recommended:', ['n' => count($q->result['university_names'])]) }}</p>
                            <div class="flex flex-wrap gap-2">
                                @foreach (array_slice($q->result['university_names'], 0, 6) as $name)
                                    <span class="inline-block bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded-full">{{ $name }}</span>
                                @endforeach
                            </div>
                        @endif
                        <details class="mt-3">
                            <summary class="cursor-pointer text-xs text-primary-600 hover:text-primary-800">{{ __('Show my answers') }}</summary>
                            <ul class="text-xs text-gray-600 mt-2 space-y-0.5">
                                @if (! empty($q->result['budget_label']))    <li>• {{ __('Budget') }}: <strong>{{ $q->result['budget_label'] }}</strong></li> @endif
                                @if (! empty($q->result['city_size_label']))<li>• {{ __('City') }}: <strong>{{ $q->result['city_size_label'] }}</strong></li> @endif
                                @if (! empty($q->result['lang_label']))     <li>• {{ __('Language') }}: <strong>{{ $q->result['lang_label'] }}</strong></li> @endif
                                @if (! empty($q->result['uni_type_label'])) <li>• {{ __('University type') }}: <strong>{{ $q->result['uni_type_label'] }}</strong></li> @endif
                                @if (! empty($q->result['field_label']))    <li>• {{ __('Field') }}: <strong>{{ $q->result['field_label'] }}</strong></li> @endif
                            </ul>
                        </details>
                    </div>
                @endforeach
            </div>
        @endif
    </section>

@endif

</div>
@endsection
