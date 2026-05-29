@extends('layouts.app')

@section('title', __('Inspire Me — Discover Germany') . ' — ' . brand('name'))

<x-seo
    :title="__('Inspire Me — Random discovery across Germany')"
    :description="__('Stuck choosing? Hit refresh and discover a random German university, city, programme, scholarship, profession and field. Every reload = 6 new picks.')"
/>

@section('content')

<section class="bg-gradient-to-br from-fuchsia-700 via-pink-600 to-amber-600 text-white">
    <div class="max-w-[1200px] mx-auto px-4 py-10 md:py-14">
        <nav class="text-sm text-pink-100 mb-3">
            <a href="{{ route('home') }}" class="hover:text-white">{{ __('Home') }}</a>
            <span class="mx-2 opacity-50">›</span>
            <a href="{{ route('tools.index') }}" class="hover:text-white">{{ __('Tools') }}</a>
            <span class="mx-2 opacity-50">›</span>
            <span class="text-white">{{ __('Inspire Me') }}</span>
        </nav>
        <h1 class="text-3xl md:text-5xl font-extrabold leading-tight drop-shadow mb-3">
            ✨ {{ __('Inspire Me — Discover Germany') }}
        </h1>
        <p class="text-lg md:text-xl text-pink-100 max-w-3xl mb-5">
            {{ __('Stuck choosing? Each refresh = 6 random picks: a university, a city, an English-taught programme, a scholarship, a profession and a study field.') }}
        </p>
        <form method="GET" action="{{ route('tools.inspire-me') }}">
            <button type="submit"
                    class="inline-flex items-center gap-2 bg-white text-pink-700 hover:bg-pink-50 font-bold px-6 py-3 rounded-lg shadow-md transition">
                🎲 {{ __('Shuffle — show me 6 new picks') }}
            </button>
        </form>
    </div>
</section>

<div class="max-w-[1200px] mx-auto px-4 py-10">

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">

        {{-- UNIVERSITY --}}
        @if ($uni)
            <article class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition group">
                <a href="{{ route('universities.show', $uni->slug) }}" class="block">
                    @if ($uni->image_url)
                        <div class="aspect-[16/10] overflow-hidden bg-gray-100">
                            <img src="{{ $uni->image_url }}" alt="{{ $uni->name_de }}" loading="lazy"
                                 class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                        </div>
                    @endif
                    <div class="p-5">
                        <p class="text-xs font-bold uppercase tracking-wider text-indigo-600 mb-1.5">🎓 {{ __('University') }}</p>
                        <h2 class="text-lg font-extrabold text-gray-900 mb-2 leading-tight group-hover:text-indigo-700 transition">
                            {{ $uni->short_name ?: $uni->name_de }}
                        </h2>
                        <div class="flex flex-wrap gap-1.5 text-xs text-gray-600">
                            @if ($uni->city)
                                <span>📍 {{ $uni->city->name }}</span>
                            @endif
                            @if ($uni->student_count)
                                <span>·</span><span>👥 {{ number_format($uni->student_count) }} {{ __('students') }}</span>
                            @endif
                            @if ($uni->founded_year && $uni->founded_year > 1000)
                                <span>·</span><span>🗓️ {{ $uni->founded_year }}</span>
                            @endif
                        </div>
                    </div>
                </a>
            </article>
        @endif

        {{-- CITY --}}
        @if ($city)
            <article class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition group">
                <a href="{{ route('cities.show', $city->slug) }}" class="block">
                    @if ($city->image_url)
                        <div class="aspect-[16/10] overflow-hidden bg-gray-100">
                            <img src="{{ $city->image_url }}" alt="{{ $city->name }}" loading="lazy"
                                 class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                        </div>
                    @endif
                    <div class="p-5">
                        <p class="text-xs font-bold uppercase tracking-wider text-emerald-600 mb-1.5">🏙️ {{ __('City') }}</p>
                        <h2 class="text-lg font-extrabold text-gray-900 mb-2 leading-tight group-hover:text-emerald-700 transition">
                            {{ $city->name }}
                        </h2>
                        <div class="flex flex-wrap gap-1.5 text-xs text-gray-600">
                            <span>🎓 {{ $city->universities_count }} {{ __('universities') }}</span>
                            @if ($city->population)
                                <span>·</span><span>👥 {{ number_format($city->population) }} {{ __('population') }}</span>
                            @endif
                        </div>
                    </div>
                </a>
            </article>
        @endif

        {{-- PROGRAM --}}
        @if ($program)
            <article class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition group">
                <a href="{{ route('programs.show', $program->slug) }}" class="block">
                    <div class="aspect-[16/10] bg-gradient-to-br from-blue-500 via-indigo-500 to-purple-600 flex items-center justify-center relative">
                        <div class="text-6xl opacity-90">{{ $program->field?->icon ?: '📚' }}</div>
                        @if ($program->language === 'en')
                            <span class="absolute top-3 right-3 px-2 py-1 bg-white/20 backdrop-blur-sm rounded text-xs font-bold text-white">🇬🇧 EN</span>
                        @elseif ($program->language === 'both')
                            <span class="absolute top-3 right-3 px-2 py-1 bg-white/20 backdrop-blur-sm rounded text-xs font-bold text-white">🇬🇧🇩🇪</span>
                        @endif
                    </div>
                    <div class="p-5">
                        <p class="text-xs font-bold uppercase tracking-wider text-blue-600 mb-1.5">📖 {{ __('Programme') }}</p>
                        <h2 class="text-lg font-extrabold text-gray-900 mb-2 leading-tight group-hover:text-blue-700 transition">
                            {{ \Illuminate\Support\Str::limit($program->name_en ?: $program->name_de, 70) }}
                        </h2>
                        <div class="flex flex-wrap gap-1.5 text-xs text-gray-600">
                            @if ($program->university)
                                <span>🎓 {{ $program->university->short_name ?: $program->university->name_de }}</span>
                            @endif
                            @if ($program->degree)
                                <span>·</span><span>{{ ucfirst($program->degree) }}</span>
                            @endif
                            @if ($program->duration_semesters)
                                <span>·</span><span>{{ $program->duration_semesters }} {{ __('semesters') }}</span>
                            @endif
                        </div>
                    </div>
                </a>
            </article>
        @endif

        {{-- SCHOLARSHIP --}}
        @if ($scholarship)
            <article class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition group">
                <a href="{{ route('scholarships.show', $scholarship->slug) }}" class="block">
                    <div class="aspect-[16/10] bg-gradient-to-br from-amber-400 via-yellow-500 to-orange-500 flex items-center justify-center relative">
                        <div class="text-6xl">💰</div>
                        @if ($scholarship->is_daad)
                            <span class="absolute top-3 right-3 px-2 py-1 bg-white/30 backdrop-blur-sm rounded text-xs font-bold text-white">DAAD</span>
                        @endif
                    </div>
                    <div class="p-5">
                        <p class="text-xs font-bold uppercase tracking-wider text-amber-600 mb-1.5">🏆 {{ __('Scholarship') }}</p>
                        <h2 class="text-lg font-extrabold text-gray-900 mb-2 leading-tight group-hover:text-amber-700 transition">
                            {{ \Illuminate\Support\Str::limit($scholarship->name, 80) }}
                        </h2>
                        @if ($scholarship->programmname)
                            <p class="text-xs text-gray-600">{{ \Illuminate\Support\Str::limit($scholarship->programmname, 80) }}</p>
                        @endif
                    </div>
                </a>
            </article>
        @endif

        {{-- PROFESSION --}}
        @if ($profession)
            <article class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition group">
                <a href="{{ route('professions.show', $profession->slug) }}" class="block">
                    <div class="aspect-[16/10] bg-gradient-to-br from-slate-600 via-gray-700 to-zinc-800 flex items-center justify-center">
                        <div class="text-6xl">💼</div>
                    </div>
                    <div class="p-5">
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">🧑‍💼 {{ __('Profession') }}</p>
                        <h2 class="text-lg font-extrabold text-gray-900 mb-2 leading-tight group-hover:text-slate-800 transition">
                            {{ $profession->name_tr ?: $profession->name_de }}
                        </h2>
                        @if ($profession->cluster_label)
                            <p class="text-xs text-gray-600">📂 {{ $profession->cluster_label }}</p>
                        @endif
                        @if ($profession->description_tr)
                            <p class="text-xs text-gray-700 mt-2 leading-relaxed">{{ \Illuminate\Support\Str::limit(strip_tags($profession->description_tr), 100) }}</p>
                        @endif
                    </div>
                </a>
            </article>
        @endif

        {{-- FIELD --}}
        @if ($field)
            <article class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition group">
                <a href="{{ route('fields.show', $field->slug) }}" class="block">
                    <div class="aspect-[16/10] flex items-center justify-center" style="background: linear-gradient(135deg, {{ $field->color ?: '#6366f1' }}99, {{ $field->color ?: '#6366f1' }})">
                        <div class="text-6xl">{{ $field->icon ?: '🎯' }}</div>
                    </div>
                    <div class="p-5">
                        <p class="text-xs font-bold uppercase tracking-wider mb-1.5" style="color: {{ $field->color ?: '#6366f1' }}">🎯 {{ __('Field of Study') }}</p>
                        <h2 class="text-lg font-extrabold text-gray-900 mb-2 leading-tight">
                            {{ $field->name_tr }}
                        </h2>
                        <p class="text-xs text-gray-600">📚 {{ $field->programs_count }} {{ __('programmes available') }}</p>
                    </div>
                </a>
            </article>
        @endif

    </div>

    {{-- Bottom CTA --}}
    <div class="mt-10 text-center bg-gradient-to-r from-fuchsia-50 to-pink-50 border-2 border-pink-200 rounded-2xl p-6 md:p-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-2">🎲 {{ __('Not quite the inspiration you needed?') }}</h2>
        <p class="text-sm text-gray-700 mb-5">{{ __('Hit shuffle for 6 fresh picks. Or take the structured route — quizzes that match your profile in 5 questions.') }}</p>
        <div class="flex flex-wrap items-center justify-center gap-3">
            <form method="GET" action="{{ route('tools.inspire-me') }}">
                <button type="submit"
                        class="inline-flex items-center gap-2 bg-pink-600 hover:bg-pink-700 text-white font-bold px-6 py-3 rounded-lg shadow-md transition">
                    🔄 {{ __('Shuffle again') }}
                </button>
            </form>
            <a href="{{ route('tools.pathway-finder') }}"
               class="inline-flex items-center gap-2 bg-white text-gray-900 hover:bg-gray-50 border-2 border-gray-300 font-bold px-6 py-3 rounded-lg transition">
                🧭 {{ __('Pathway Finder quiz') }}
            </a>
            <a href="{{ route('tools.recommendation') }}"
               class="inline-flex items-center gap-2 bg-white text-gray-900 hover:bg-gray-50 border-2 border-gray-300 font-bold px-6 py-3 rounded-lg transition">
                🎯 {{ __('University Match quiz') }}
            </a>
        </div>
    </div>

</div>

@endsection
