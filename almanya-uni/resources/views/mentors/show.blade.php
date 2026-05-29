@extends('layouts.app')

@section('title', $mentor->name . ' — Mentor — ' . brand('name'))

<x-seo
    :title="$mentor->name . ' — ' . ($mentor->headline ?? __('AlmanyaUni Mentor'))"
    :description="\Illuminate\Support\Str::limit($mentor->bio ?? $mentor->headline ?? __('Germany career mentoring'), 160)"
/>

@section('content')

{{-- HERO --}}
<section class="bg-gradient-to-br from-emerald-700 via-emerald-600 to-teal-500 text-white">
    <div class="max-w-[1400px] mx-auto px-4 py-10 md:py-14">
        <nav class="text-sm text-emerald-100 mb-3">
            <a href="/" class="hover:text-white">{{ __('Home') }}</a>
            <span class="mx-2 opacity-60">›</span>
            <a href="{{ route('mentors.index') }}" class="hover:text-white">{{ __('Mentors') }}</a>
            <span class="mx-2 opacity-60">›</span>
            <span class="text-white">{{ $mentor->name }}</span>
        </nav>

        <div class="flex items-center gap-5">
            @if ($mentor->avatar_url)
                <img src="{{ $mentor->avatar_url }}" alt="{{ $mentor->name }}"
                     class="w-24 h-24 md:w-32 md:h-32 rounded-full object-cover ring-4 ring-white/30 shadow-xl">
            @else
                <div class="w-24 h-24 md:w-32 md:h-32 rounded-full bg-white/20 backdrop-blur text-white text-3xl md:text-4xl font-extrabold flex items-center justify-center ring-4 ring-white/30 shadow-xl">
                    {{ $mentor->initials }}
                </div>
            @endif
            <div class="flex-1 min-w-0">
                <h1 class="text-3xl md:text-5xl font-extrabold leading-tight drop-shadow mb-1">{{ $mentor->name }}</h1>
                @if ($mentor->headline)
                    <p class="text-lg md:text-xl text-emerald-100">{{ $mentor->headline }}</p>
                @endif
                @if ($mentor->city)
                    <p class="text-sm text-emerald-200 mt-1">📍 {{ $mentor->city }}</p>
                @endif
            </div>
        </div>
    </div>
</section>

<div class="max-w-[1400px] mx-auto px-4 py-10 grid grid-cols-1 lg:grid-cols-3 gap-8">
    <main class="lg:col-span-2 space-y-6">
        @if ($mentor->bio)
            <section class="bg-white border border-gray-200 rounded-xl p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-3">{{ __('About') }}</h2>
                <p class="text-gray-700 leading-relaxed whitespace-pre-line">{{ $mentor->bio }}</p>
            </section>
        @endif

        @if ($mentor->university || $mentor->current_company)
            <section class="bg-white border border-gray-200 rounded-xl p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-3">{{ __('Background') }}</h2>
                <div class="space-y-2 text-sm">
                    @if ($mentor->current_role || $mentor->current_company)
                        <p>💼 <strong>{{ $mentor->current_role }}</strong>@if ($mentor->current_company) · {{ $mentor->current_company }}@endif</p>
                    @endif
                    @if ($mentor->university)
                        <p>🎓 <strong>{{ $mentor->university }}</strong>@if ($mentor->field_of_study) · {{ $mentor->field_of_study }}@endif @if ($mentor->graduation_year)({{ $mentor->graduation_year }})@endif</p>
                    @endif
                </div>
            </section>
        @endif

        @if (! empty($mentor->topics))
            <section class="bg-white border border-gray-200 rounded-xl p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-3">{{ __('Mentoring Topics') }}</h2>
                <div class="flex flex-wrap gap-2">
                    @foreach ($mentor->topics as $t)
                        <a href="{{ route('mentors.index', ['topic' => $t]) }}"
                           class="text-sm px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 hover:bg-emerald-100">{{ $t }}</a>
                    @endforeach
                </div>
            </section>
        @endif
    </main>

    <aside class="space-y-4">
        <div class="bg-white border-2 border-emerald-500 rounded-xl p-6 sticky top-20 shadow-lg">
            <h3 class="text-sm font-bold uppercase tracking-wider text-gray-500 mb-4">📅 {{ __('Mentoring Details') }}</h3>

            <div class="space-y-3 text-sm">
                <div class="flex items-baseline justify-between">
                    <span class="text-gray-600">{{ __('Rate') }}</span>
                    @if ($mentor->is_free)
                        <strong class="text-emerald-700">🎁 {{ __('Free') }}</strong>
                    @else
                        <strong class="text-amber-700 text-lg">{{ number_format($mentor->rate_eur, 0, ',', '.') }} €</strong>
                    @endif
                </div>
                @if ($mentor->session_duration)
                    <div class="flex items-baseline justify-between">
                        <span class="text-gray-600">{{ __('Duration') }}</span>
                        <span class="font-semibold">{{ $mentor->session_duration }}</span>
                    </div>
                @endif
                @if ($mentor->availability)
                    <div class="flex items-baseline justify-between">
                        <span class="text-gray-600">{{ __('Availability') }}</span>
                        <span>{{ $mentor->availability }}</span>
                    </div>
                @endif
                @if (! empty($mentor->languages))
                    <div class="flex items-baseline justify-between">
                        <span class="text-gray-600">{{ __('Language') }}</span>
                        <span>{{ collect($mentor->languages)->map(fn($l) => match($l) {'tr'=>'🇹🇷 TR','de'=>'🇩🇪 DE','en'=>'🇬🇧 EN',default=>$l})->join(' · ') }}</span>
                    </div>
                @endif
            </div>

            {{-- In-app booking (preferred — auto-Jitsi + email both parties) --}}
            @auth
                <div x-data="{ open: false }" class="mt-5">
                    <button @click="open = true"
                            class="w-full py-3 rounded-lg bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold transition shadow-md">
                        🎥 {{ __('Book a Jitsi session') }}
                    </button>

                    {{-- Booking modal --}}
                    <div x-show="open" x-cloak
                         class="fixed inset-0 z-[80] flex items-center justify-center p-4"
                         @keydown.escape.window="open = false">
                        <div @click="open = false" class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>
                        <form method="POST" action="{{ route('mentors.book', $mentor->slug) }}"
                              class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-y-auto p-6">
                            @csrf
                            <button type="button" @click="open = false"
                                    class="absolute top-3 right-3 w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-lg">×</button>

                            <h3 class="text-xl font-extrabold text-gray-900 mb-1">📅 {{ __('Book with :name', ['name' => $mentor->name]) }}</h3>
                            <p class="text-sm text-gray-600 mb-4">{{ __('Auto-generated Jitsi link, no third-party account needed.') }}</p>

                            <div class="space-y-3">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-1">📅 {{ __('Date & time') }}</label>
                                    <input type="datetime-local" name="scheduled_at" required
                                           min="{{ now()->addHour()->format('Y-m-d\TH:i') }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-1">⏱️ {{ __('Duration') }}</label>
                                    <select name="duration_minutes" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                        <option value="15">15 {{ __('minutes') }}</option>
                                        <option value="30" selected>30 {{ __('minutes') }}</option>
                                        <option value="45">45 {{ __('minutes') }}</option>
                                        <option value="60">60 {{ __('minutes') }}</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-1">💬 {{ __('Topic (optional)') }}</label>
                                    <input type="text" name="topic" maxlength="200"
                                           placeholder="{{ __('e.g. Studienkolleg application timeline') }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-1">📝 {{ __('Background / questions') }}</label>
                                    <textarea name="notes" rows="3" maxlength="2000"
                                              placeholder="{{ __('Share what you want to discuss so the mentor can prepare.') }}"
                                              class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"></textarea>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-1">🗣️ {{ __('Preferred language') }}</label>
                                    <select name="preferred_language" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                        <option value="tr">🇹🇷 Türkçe</option>
                                        <option value="en">🇬🇧 English</option>
                                        <option value="de">🇩🇪 Deutsch</option>
                                    </select>
                                </div>
                            </div>

                            <button type="submit"
                                    class="w-full mt-5 py-2.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold transition">
                                🎥 {{ __('Confirm booking') }}
                            </button>
                            <p class="text-xs text-gray-500 text-center mt-2">{{ __('You and the mentor will receive an email with the Jitsi link.') }}</p>
                        </form>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}"
                   class="block mt-5 text-center py-3 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold transition shadow-md">
                    🔐 {{ __('Login to book') }}
                </a>
            @endauth

            @if ($mentor->contact_url && ! str_starts_with($mentor->contact_url, 'mailto:'))
                <a href="{{ $mentor->contact_url }}"
                   target="_blank" rel="noopener"
                   class="block mt-2 text-center py-2 rounded-lg bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 font-semibold transition text-sm">
                    📅 {{ __('Or use external calendar') }} ↗
                </a>
            @endif

            {{-- Sosyal --}}
            <div class="flex items-center justify-center gap-3 mt-4 pt-4 border-t border-gray-100">
                @if ($mentor->linkedin_url)
                    <a href="{{ $mentor->linkedin_url }}" target="_blank" rel="noopener" class="text-gray-500 hover:text-blue-700" title="LinkedIn">💼</a>
                @endif
                @if ($mentor->twitter_url)
                    <a href="{{ $mentor->twitter_url }}" target="_blank" rel="noopener" class="text-gray-500 hover:text-blue-500" title="Twitter">𝕏</a>
                @endif
                @if ($mentor->github_url)
                    <a href="{{ $mentor->github_url }}" target="_blank" rel="noopener" class="text-gray-500 hover:text-gray-900" title="GitHub">⌨️</a>
                @endif
                @if ($mentor->website_url)
                    <a href="{{ $mentor->website_url }}" target="_blank" rel="noopener" class="text-gray-500 hover:text-emerald-700" title="Website">🌐</a>
                @endif
            </div>
        </div>

        @if ($related->isNotEmpty())
            <div class="bg-white border border-gray-200 rounded-xl p-5">
                <h3 class="text-sm font-bold uppercase tracking-wider text-gray-500 mb-3">{{ __('Similar Mentors') }}</h3>
                <div class="space-y-3">
                    @foreach ($related as $r)
                        <a href="{{ route('mentors.show', $r->slug) }}" class="flex items-center gap-3 hover:bg-gray-50 -mx-2 px-2 py-1.5 rounded transition">
                            @if ($r->avatar_url)
                                <img src="{{ $r->avatar_url }}" alt="" class="w-10 h-10 rounded-full object-cover">
                            @else
                                <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-700 font-bold flex items-center justify-center text-sm">{{ $r->initials }}</div>
                            @endif
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-sm text-gray-900 truncate">{{ $r->name }}</p>
                                <p class="text-xs text-gray-500 truncate">{{ $r->headline }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </aside>
</div>
@endsection
