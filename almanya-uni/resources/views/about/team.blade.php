@extends('layouts.app')

@section('title', __('Team & Contributors') . ' — ' . brand('name'))

<x-seo
    :title="__('Team & Contributors') . ' — ' . brand('name')"
    :description="__('Founder, content editors and mentors building AlmanyaUni — the Germany study guide for international students. Growing together.')"
/>

@section('content')

{{-- ════ HERO — vibrant + animated ════ --}}
<section class="relative bg-gradient-to-br from-indigo-700 via-purple-600 to-pink-500 text-white overflow-hidden">
    {{-- Decorative dots, sparkles, swooshes --}}
    <div class="absolute inset-0 pointer-events-none opacity-50">
        <svg class="absolute top-6 left-[15%] w-12 h-12 animate-pulse" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M12 2L14.5 9.5L22 12L14.5 14.5L12 22L9.5 14.5L2 12L9.5 9.5L12 2Z" fill="white" fill-opacity="0.4"/>
        </svg>
        <svg class="absolute top-1/3 right-[10%] w-10 h-10 animate-pulse" style="animation-delay: 0.7s" viewBox="0 0 24 24" fill="none">
            <circle cx="12" cy="12" r="3" fill="white" fill-opacity="0.5"/>
        </svg>
        <svg class="absolute bottom-8 left-[20%] w-16 h-16 animate-pulse" style="animation-delay: 1.4s" viewBox="0 0 24 24" fill="none">
            <path d="M12 2L14.5 9.5L22 12L14.5 14.5L12 22L9.5 14.5L2 12L9.5 9.5L12 2Z" fill="white" fill-opacity="0.3"/>
        </svg>
        <svg class="absolute top-12 right-[25%] w-6 h-6" viewBox="0 0 24 24" fill="white" fill-opacity="0.4">
            <circle cx="12" cy="12" r="2"/>
        </svg>
        <svg class="absolute bottom-12 right-[15%] w-8 h-8 animate-bounce" style="animation-duration: 3s" viewBox="0 0 24 24" fill="white" fill-opacity="0.35">
            <path d="M12 2 L13 10 L22 12 L13 14 L12 22 L11 14 L2 12 L11 10 Z"/>
        </svg>
        <div class="absolute -bottom-10 -left-10 w-72 h-72 bg-pink-400/30 rounded-full blur-3xl"></div>
        <div class="absolute -top-10 -right-10 w-80 h-80 bg-indigo-400/30 rounded-full blur-3xl"></div>
    </div>

    <div class="relative max-w-[1400px] mx-auto px-4 py-14 md:py-20">
        <nav class="text-sm text-indigo-100 mb-4">
            <a href="/" class="hover:text-white">{{ __('Home') }}</a>
            <span class="mx-2 opacity-60">›</span>
            <span class="text-white">{{ __('Team') }}</span>
        </nav>

        <div class="grid md:grid-cols-[1fr_auto] gap-8 items-center">
            <div>
                <span class="inline-block px-3 py-1 mb-3 rounded-full bg-white/15 backdrop-blur-sm text-xs font-bold uppercase tracking-wider ring-1 ring-white/25">✨ {{ __('Meet the team') }}</span>
                <h1 class="text-4xl md:text-6xl font-extrabold leading-[1.05] drop-shadow mb-4">
                    {{ __('The people behind') }}
                    <br>
                    <span class="bg-gradient-to-r from-amber-200 via-pink-200 to-white bg-clip-text text-transparent">{{ brand('name') }}</span>
                </h1>
                <p class="text-lg md:text-xl text-indigo-100 max-w-2xl leading-relaxed">
                    {{ __('Founder, editors and mentors growing the Germany study guide for international students. :count guide articles published and counting.', ['count' => $totalPosts]) }}
                </p>

                {{-- Hero stats --}}
                <div class="flex flex-wrap gap-3 mt-6">
                    <div class="bg-white/15 backdrop-blur-sm ring-1 ring-white/25 rounded-2xl px-5 py-3">
                        <div class="text-2xl md:text-3xl font-extrabold">{{ $founders->count() + $editors->count() + $others->count() }}</div>
                        <div class="text-xs uppercase tracking-wider opacity-80">{{ __('Editors') }}</div>
                    </div>
                    <div class="bg-white/15 backdrop-blur-sm ring-1 ring-white/25 rounded-2xl px-5 py-3">
                        <div class="text-2xl md:text-3xl font-extrabold">{{ number_format($totalPosts) }}</div>
                        <div class="text-xs uppercase tracking-wider opacity-80">{{ __('Articles') }}</div>
                    </div>
                    @if ($mentors->isNotEmpty())
                        <div class="bg-white/15 backdrop-blur-sm ring-1 ring-white/25 rounded-2xl px-5 py-3">
                            <div class="text-2xl md:text-3xl font-extrabold">{{ $mentors->count() }}+</div>
                            <div class="text-xs uppercase tracking-wider opacity-80">{{ __('Mentors') }}</div>
                        </div>
                    @endif
                    @if ($totalContributions > 0)
                        <div class="bg-white/15 backdrop-blur-sm ring-1 ring-white/25 rounded-2xl px-5 py-3">
                            <div class="text-2xl md:text-3xl font-extrabold">{{ number_format($totalContributions) }}</div>
                            <div class="text-xs uppercase tracking-wider opacity-80">{{ __('Contributions') }}</div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Decorative grouped avatar stack (the "team illustration" feel) --}}
            <div class="hidden md:flex flex-shrink-0 -space-x-4">
                @php
                    $heroAvatars = $founders->concat($editors)->concat($others)->take(6);
                    $heroColors = ['from-amber-400 to-orange-500','from-pink-400 to-rose-500','from-emerald-400 to-teal-500','from-sky-400 to-indigo-500','from-violet-400 to-purple-500','from-yellow-400 to-amber-500'];
                @endphp
                @foreach ($heroAvatars as $idx => $p)
                    <div class="relative w-16 h-16 md:w-20 md:h-20 rounded-full ring-4 ring-white/40 overflow-hidden hover:scale-110 hover:z-10 transition" style="z-index: {{ 10 - $idx }}">
                        @if ($p->avatar_url)
                            <img src="{{ $p->avatar_url }}" alt="{{ $p->name }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-white font-bold text-lg bg-gradient-to-br {{ $heroColors[$idx % count($heroColors)] }}">
                                {{ collect(explode(' ', $p->name))->map(fn($n) => mb_substr($n,0,1))->take(2)->implode('') }}
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

@php
    $colors = ['#4f46e5','#0891b2','#db2777','#059669','#d97706','#7c3aed'];
    $initials = fn($name) => collect(explode(' ', trim($name)))->map(fn($p) => mb_substr($p,0,1))->take(2)->implode('');
    // Yazar verisini modal için JS objesine çevir
    $authorData = fn($p, $color) => [
        'name'        => $p->name,
        'role'        => $p->role_label,
        'bio'         => $p->bio,
        'avatar'      => $p->avatar_url,
        'initials'    => $initials($p->name),
        'color'       => $color,
        'posts_count' => $p->posts_count,
        'posts'       => $p->posts->map(fn($post) => [
            'title' => $post->title,
            'url'   => route('blog.show', $post->slug),
            'date'  => optional($post->published_at)->format('d.m.Y'),
            'min'   => $post->reading_minutes,
        ])->all(),
    ];
@endphp

<div x-data="{ open: false, author: {} }" class="max-w-[1400px] mx-auto px-4 py-12 space-y-14">

    {{-- ════ KURUCU — oversized hero card with quote ════ --}}
    @if ($founders->isNotEmpty())
        <section>
            <div class="flex items-center gap-3 mb-6">
                <span class="inline-block px-3 py-1.5 rounded-full bg-gradient-to-r from-amber-400 to-orange-500 text-white text-xs font-extrabold uppercase tracking-wider shadow-md">👑 {{ __('Founder') }}</span>
                <div class="h-px flex-1 bg-gradient-to-r from-orange-200 via-pink-200 to-transparent"></div>
            </div>
            <div class="grid grid-cols-1 {{ $founders->count() > 1 ? 'md:grid-cols-2' : '' }} gap-5">
                @foreach ($founders as $i => $p)
                    <article @click="author = @js($authorData($p, $colors[$i % count($colors)])); open = true"
                             class="group relative bg-gradient-to-br from-indigo-50 via-white to-pink-50 border-2 border-indigo-200 rounded-3xl p-6 md:p-8 shadow-md hover:shadow-2xl hover:-translate-y-1 transition-all cursor-pointer overflow-hidden">
                        {{-- decorative shapes --}}
                        <div class="absolute -top-12 -right-12 w-44 h-44 bg-gradient-to-br from-indigo-300/40 to-purple-300/40 rounded-full blur-2xl"></div>
                        <div class="absolute -bottom-12 -left-12 w-40 h-40 bg-gradient-to-br from-pink-300/40 to-amber-300/40 rounded-full blur-2xl"></div>
                        <svg class="absolute top-3 right-4 w-5 h-5 text-amber-400 animate-pulse" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2L14.5 9.5L22 12L14.5 14.5L12 22L9.5 14.5L2 12L9.5 9.5L12 2Z"/></svg>

                        <div class="relative flex flex-col sm:flex-row gap-6 items-start sm:items-center">
                            {{-- Avatar with crown halo --}}
                            <div class="relative shrink-0">
                                @if ($p->avatar_url)
                                    <img src="{{ $p->avatar_url }}" alt="{{ $p->name }}" class="w-24 h-24 md:w-32 md:h-32 rounded-full object-cover ring-4 ring-white shadow-xl group-hover:scale-105 transition">
                                @else
                                    <div class="w-24 h-24 md:w-32 md:h-32 rounded-full flex items-center justify-center text-white text-3xl md:text-4xl font-extrabold shadow-xl ring-4 ring-white group-hover:scale-105 transition" style="background: linear-gradient(135deg, {{ $colors[$i % count($colors)] }}, #8b5cf6);">{{ $initials($p->name) }}</div>
                                @endif
                                <span class="absolute -top-2 -right-2 text-2xl md:text-3xl drop-shadow-md group-hover:rotate-12 transition">👑</span>
                            </div>

                            <div class="flex-1 min-w-0">
                                <h3 class="text-2xl md:text-3xl font-extrabold text-gray-900">{{ $p->name }}</h3>
                                <p class="text-sm font-bold text-indigo-700 mb-3 uppercase tracking-wider">{{ $p->role_label }}</p>
                                @if ($p->bio)
                                    <blockquote class="relative pl-5 border-l-4 border-indigo-300 text-sm md:text-base text-gray-700 leading-relaxed italic">
                                        {{ $p->bio }}
                                    </blockquote>
                                @endif
                                <div class="flex flex-wrap gap-3 mt-4 items-center">
                                    @if ($p->posts_count > 0)
                                        <span class="inline-flex items-center gap-1 text-xs font-bold px-3 py-1 rounded-full bg-amber-100 text-amber-900">✍️ {{ __(':count posts', ['count' => $p->posts_count]) }}</span>
                                    @endif
                                    @if ($p->slug)
                                        <a href="{{ route('author.show', $p->slug) }}" @click.stop
                                           class="inline-flex items-center gap-1 text-xs font-bold px-3 py-1 rounded-full bg-indigo-600 text-white hover:bg-indigo-700 transition">
                                            📄 {{ __('View profile') }} →
                                        </a>
                                    @endif
                                    {{-- Social pills (LinkedIn, X, GitHub, vb.) admin-ekledigi social_links'ten --}}
                                    @php
                                        $socials = (array) ($p->social_links ?? []);
                                        $iconMap = [
                                            'linkedin' => ['emoji' => '💼', 'prefix' => 'https://linkedin.com/in/'],
                                            'twitter'  => ['emoji' => '𝕏',  'prefix' => 'https://x.com/'],
                                            'x'        => ['emoji' => '𝕏',  'prefix' => 'https://x.com/'],
                                            'github'   => ['emoji' => '⌨️', 'prefix' => 'https://github.com/'],
                                            'website'  => ['emoji' => '🌐', 'prefix' => ''],
                                            'url'      => ['emoji' => '🌐', 'prefix' => ''],
                                            'email'    => ['emoji' => '✉️', 'prefix' => 'mailto:'],
                                        ];
                                    @endphp
                                    @foreach ($socials as $type => $val)
                                        @if (! $val) @continue @endif
                                        @php
                                            $m = $iconMap[strtolower((string) $type)] ?? null;
                                            if (! $m) continue;
                                            $url = str_starts_with($val, 'http') || str_starts_with($val, 'mailto:') ? $val : $m['prefix'] . ltrim($val, '@/');
                                        @endphp
                                        <a href="{{ $url }}" target="_blank" rel="noopener noreferrer me" @click.stop
                                           class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-white/80 hover:bg-white border border-gray-200 transition shadow-sm"
                                           title="{{ ucfirst($type) }}">
                                            <span class="text-sm">{{ $m['emoji'] }}</span>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    @endif

    {{-- ════ İÇERİK EDİTÖRLERİ — colorful themed cards ════ --}}
    @if ($editors->isNotEmpty())
        @php
            // Each editor gets a unique color theme (cycle through 6)
            $themes = [
                ['from' => 'from-rose-400',    'to' => 'to-pink-500',    'bg' => 'from-rose-50/80 to-pink-50',     'ring' => 'ring-rose-200',    'badge' => 'bg-rose-100 text-rose-800',       'icon' => '🎨'],
                ['from' => 'from-sky-400',     'to' => 'to-indigo-500',  'bg' => 'from-sky-50/80 to-indigo-50',    'ring' => 'ring-sky-200',     'badge' => 'bg-sky-100 text-sky-800',         'icon' => '🌊'],
                ['from' => 'from-emerald-400', 'to' => 'to-teal-500',    'bg' => 'from-emerald-50/80 to-teal-50',  'ring' => 'ring-emerald-200', 'badge' => 'bg-emerald-100 text-emerald-800', 'icon' => '🌱'],
                ['from' => 'from-amber-400',   'to' => 'to-orange-500',  'bg' => 'from-amber-50/80 to-orange-50',  'ring' => 'ring-amber-200',   'badge' => 'bg-amber-100 text-amber-800',     'icon' => '☀️'],
                ['from' => 'from-violet-400',  'to' => 'to-purple-500',  'bg' => 'from-violet-50/80 to-purple-50', 'ring' => 'ring-violet-200',  'badge' => 'bg-violet-100 text-violet-800',   'icon' => '🔮'],
                ['from' => 'from-fuchsia-400', 'to' => 'to-rose-500',    'bg' => 'from-fuchsia-50/80 to-rose-50',  'ring' => 'ring-fuchsia-200', 'badge' => 'bg-fuchsia-100 text-fuchsia-800', 'icon' => '🌸'],
            ];
        @endphp
        <section>
            <div class="flex items-center gap-3 mb-6">
                <span class="inline-block px-3 py-1.5 rounded-full bg-gradient-to-r from-sky-500 to-indigo-600 text-white text-xs font-extrabold uppercase tracking-wider shadow-md">✍️ {{ __('Content Editors') }}</span>
                <div class="h-px flex-1 bg-gradient-to-r from-indigo-200 via-sky-100 to-transparent"></div>
                <span class="text-sm text-gray-500 hidden sm:inline">{{ $editors->count() }} {{ __('people') }}</span>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach ($editors as $i => $p)
                    @php $t = $themes[$i % count($themes)]; @endphp
                    <article @click="author = @js($authorData($p, $colors[$i % count($colors)])); open = true"
                             class="group relative bg-gradient-to-br {{ $t['bg'] }} border border-gray-200 rounded-3xl p-6 text-center hover:shadow-xl hover:-translate-y-1.5 hover:rotate-[-0.5deg] transition-all cursor-pointer overflow-hidden">
                        {{-- decorative corner emoji --}}
                        <span class="absolute top-3 right-3 text-xl opacity-30 group-hover:opacity-100 group-hover:scale-125 transition">{{ $t['icon'] }}</span>

                        {{-- Avatar with gradient halo ring --}}
                        <div class="relative inline-block mb-3">
                            <div class="absolute inset-0 rounded-full bg-gradient-to-br {{ $t['from'] }} {{ $t['to'] }} blur-md opacity-50 group-hover:opacity-90 group-hover:scale-110 transition"></div>
                            @if ($p->avatar_url)
                                <img src="{{ $p->avatar_url }}" alt="{{ $p->name }}" class="relative w-24 h-24 rounded-full object-cover ring-4 ring-white shadow-lg group-hover:scale-105 transition">
                            @else
                                <div class="relative w-24 h-24 rounded-full flex items-center justify-center text-white text-2xl font-extrabold shadow-lg ring-4 ring-white group-hover:scale-105 transition bg-gradient-to-br {{ $t['from'] }} {{ $t['to'] }}">{{ $initials($p->name) }}</div>
                            @endif
                        </div>

                        <h3 class="font-extrabold text-gray-900 text-lg">{{ $p->name }}</h3>
                        <p class="text-xs font-bold text-gray-600 uppercase tracking-wider mt-0.5 mb-2">{{ $p->role_label }}</p>
                        @if ($p->bio)
                            <p class="text-xs text-gray-700 leading-relaxed line-clamp-3 mb-3">{{ $p->bio }}</p>
                        @endif

                        <div class="flex flex-wrap gap-1.5 justify-center items-center">
                            @if ($p->posts_count > 0)
                                <span class="inline-flex items-center gap-1 text-[11px] font-bold px-2.5 py-1 rounded-full {{ $t['badge'] }}">✍️ {{ $p->posts_count }}</span>
                            @endif
                            @if ($p->slug)
                                <a href="{{ route('author.show', $p->slug) }}" @click.stop
                                   class="inline-flex items-center text-[11px] font-bold px-2.5 py-1 rounded-full bg-white text-gray-700 hover:bg-gray-100 border border-gray-200 transition" title="{{ __('View profile') }}">
                                    📄
                                </a>
                            @endif
                            @php $socials = (array) ($p->social_links ?? []); @endphp
                            @foreach ($socials as $type => $val)
                                @if (! $val) @continue @endif
                                @php
                                    $lt = strtolower((string) $type);
                                    $emoji = ['linkedin' => '💼','twitter' => '𝕏','x' => '𝕏','github' => '⌨️','website' => '🌐','url' => '🌐','email' => '✉️'][$lt] ?? null;
                                    if (! $emoji) continue;
                                    $url = str_starts_with($val, 'http') || str_starts_with($val, 'mailto:') ? $val
                                        : (['linkedin' => 'https://linkedin.com/in/','twitter' => 'https://x.com/','x' => 'https://x.com/','github' => 'https://github.com/','email' => 'mailto:'][$lt] ?? '') . ltrim($val, '@/');
                                @endphp
                                <a href="{{ $url }}" target="_blank" rel="noopener noreferrer me" @click.stop
                                   class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-white/90 hover:bg-white border border-gray-200 text-xs shadow-sm transition" title="{{ ucfirst($type) }}">
                                    {{ $emoji }}
                                </a>
                            @endforeach
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    @endif

    {{-- ════ MENTORLAR — emerald theme with availability dot ════ --}}
    @if ($mentors->isNotEmpty())
        <section>
            <div class="flex items-center gap-3 mb-6 flex-wrap">
                <span class="inline-block px-3 py-1.5 rounded-full bg-gradient-to-r from-emerald-500 to-teal-600 text-white text-xs font-extrabold uppercase tracking-wider shadow-md">🤝 {{ __('Mentors') }}</span>
                <div class="h-px flex-1 bg-gradient-to-r from-emerald-200 via-teal-100 to-transparent"></div>
                <a href="{{ route('mentors.index') }}" class="text-sm font-bold text-emerald-700 hover:text-emerald-900">{{ __('All mentors →') }}</a>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($mentors as $m)
                    <a href="{{ route('mentors.show', $m->slug) }}"
                       class="group relative flex items-center gap-3 bg-gradient-to-br from-emerald-50/60 to-teal-50/60 border border-emerald-200 rounded-2xl p-4 hover:border-emerald-400 hover:shadow-lg hover:-translate-y-0.5 transition">
                        <div class="relative shrink-0">
                            @if ($m->avatar_url)
                                <img src="{{ $m->avatar_url }}" alt="{{ $m->name }}" class="w-14 h-14 rounded-full object-cover ring-2 ring-white shadow-sm">
                            @else
                                <div class="w-14 h-14 rounded-full bg-gradient-to-br from-emerald-500 to-teal-600 text-white font-extrabold flex items-center justify-center text-base ring-2 ring-white shadow-sm">{{ $initials($m->name) }}</div>
                            @endif
                            <span class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 rounded-full bg-emerald-500 ring-2 ring-white" title="{{ __('Available') }}"></span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-gray-900 truncate">{{ $m->name }}</p>
                            <p class="text-xs text-gray-600 truncate">{{ $m->headline ?: $m->current_company }}</p>
                        </div>
                        <span class="text-emerald-500 text-lg opacity-0 group-hover:opacity-100 group-hover:translate-x-1 transition">→</span>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- DİĞER KATKI SAĞLAYANLAR --}}
    @if ($others->isNotEmpty())
        <section>
            <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-2">🌱 {{ __('Contributors') }}</h2>
            <div class="flex flex-wrap gap-4">
                @foreach ($others as $i => $p)
                    <div class="flex items-center gap-3 bg-white border border-gray-200 rounded-xl px-4 py-3">
                        @if ($p->avatar_url)
                            <img src="{{ $p->avatar_url }}" alt="{{ $p->name }}" class="w-10 h-10 rounded-full object-cover">
                        @else
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-white text-sm font-bold" style="background: {{ $colors[$i % count($colors)] }};">{{ $initials($p->name) }}</div>
                        @endif
                        <div>
                            <p class="font-semibold text-sm text-gray-900">{{ $p->name }}</p>
                            <p class="text-xs text-gray-500">{{ $p->role_label ?: __('Contributor') }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    {{-- ════ TOPLULUK KATKICILARI — vibrant pill cloud ════ --}}
    @if (! empty($contributors) && $contributors->isNotEmpty())
        <section>
            <div class="flex items-center gap-3 mb-6 flex-wrap">
                <span class="inline-block px-3 py-1.5 rounded-full bg-gradient-to-r from-amber-500 to-orange-600 text-white text-xs font-extrabold uppercase tracking-wider shadow-md">🌱 {{ __('Community Contributors') }}</span>
                <div class="h-px flex-1 bg-gradient-to-r from-amber-200 via-orange-100 to-transparent"></div>
                <span class="text-sm font-semibold text-gray-600">{{ __(':contributions approved · :people+ people', ['contributions' => $totalContributions, 'people' => $contributors->count()]) }}</span>
            </div>
            <p class="text-sm text-gray-600 mb-5 max-w-2xl">{{ __('Community members who guide other international students by sharing their experience. Share yours and you will appear here too.') }}</p>
            <div class="flex flex-wrap gap-2.5">
                @foreach ($contributors as $i => $c)
                    @php
                        $pillThemes = [
                            'from-rose-100 to-pink-100 text-rose-900 border-rose-200',
                            'from-sky-100 to-indigo-100 text-sky-900 border-sky-200',
                            'from-emerald-100 to-teal-100 text-emerald-900 border-emerald-200',
                            'from-amber-100 to-orange-100 text-amber-900 border-amber-200',
                            'from-violet-100 to-purple-100 text-violet-900 border-violet-200',
                            'from-fuchsia-100 to-rose-100 text-fuchsia-900 border-fuchsia-200',
                        ];
                        $pill = $pillThemes[$i % count($pillThemes)];
                    @endphp
                    <div class="group flex items-center gap-2.5 bg-gradient-to-r {{ $pill }} border rounded-full pl-1.5 pr-3.5 py-1.5 hover:shadow-md hover:scale-105 transition cursor-default" title="{{ __('Community Contributor') }}">
                        @if ($c->avatar_url)
                            <img src="{{ $c->avatar_url }}" alt="{{ $c->name }}" class="w-8 h-8 rounded-full object-cover ring-2 ring-white">
                        @else
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold ring-2 ring-white" style="background: {{ $colors[$i % count($colors)] }};">{{ $initials($c->name) }}</div>
                        @endif
                        <span class="text-sm font-bold">{{ $c->name }}</span>
                        <span class="text-sm group-hover:rotate-12 transition">🌱</span>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    {{-- ════ JOIN US CTA — closing card ════ --}}
    <section class="relative overflow-hidden bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-500 text-white rounded-3xl p-8 md:p-12 shadow-2xl">
        <div class="absolute -top-12 -right-12 w-48 h-48 bg-pink-300/30 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-12 -left-12 w-56 h-56 bg-indigo-300/30 rounded-full blur-3xl"></div>
        <svg class="absolute top-6 right-8 w-8 h-8 text-amber-200 animate-pulse" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2L14.5 9.5L22 12L14.5 14.5L12 22L9.5 14.5L2 12L9.5 9.5L12 2Z"/></svg>

        <div class="relative grid md:grid-cols-[1fr_auto] gap-6 items-center">
            <div>
                <h2 class="text-2xl md:text-3xl font-extrabold mb-2">{{ __('Want to join the team?') }}</h2>
                <p class="text-base md:text-lg text-indigo-100 max-w-2xl">{{ __('We grow with the contributions of international students. Share your experience, write an article, become a mentor.') }}</p>
            </div>
            <div class="flex flex-wrap gap-3 shrink-0">
                <a href="/contribute" class="inline-flex items-center gap-2 bg-white text-indigo-700 hover:bg-indigo-50 font-bold px-5 py-3 rounded-xl shadow-lg transition">
                    ✍️ {{ __('Share experience') }}
                </a>
                @if (\Illuminate\Support\Facades\Route::has('mentors.index'))
                    <a href="{{ route('mentors.index') }}" class="inline-flex items-center gap-2 bg-white/15 backdrop-blur-sm ring-1 ring-white/30 hover:bg-white/25 text-white font-bold px-5 py-3 rounded-xl transition">
                        🤝 {{ __('Become a mentor') }}
                    </a>
                @endif
            </div>
        </div>
    </section>

    {{-- ════ YAZAR PROFİL MODAL ════ --}}
    <div x-show="open" x-cloak
         @keydown.escape.window="open = false"
         class="fixed inset-0 z-[70] flex items-center justify-center p-4">
        {{-- backdrop --}}
        <div @click="open = false" class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>
        {{-- panel --}}
        <div x-show="open" x-transition
             class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[85vh] overflow-y-auto">
            {{-- kapat --}}
            <button @click="open = false" class="absolute top-4 right-4 w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-gray-500 text-lg z-10">×</button>

            {{-- profil başlık --}}
            <div class="p-6 pb-4 border-b border-gray-100">
                <div class="flex items-center gap-4">
                    <template x-if="author.avatar">
                        <img :src="author.avatar" :alt="author.name" class="w-20 h-20 rounded-full object-cover ring-4 ring-indigo-100">
                    </template>
                    <template x-if="!author.avatar">
                        <div class="w-20 h-20 rounded-full flex items-center justify-center text-white text-2xl font-extrabold" :style="`background: ${author.color}`" x-text="author.initials"></div>
                    </template>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-xl font-bold text-gray-900" x-text="author.name"></h3>
                        <p class="text-sm font-semibold text-indigo-600" x-text="author.role"></p>
                        <p class="text-xs text-gray-400 mt-1"><span x-text="author.posts_count"></span> {{ __('posts') }}</p>
                    </div>
                </div>
                <p class="text-sm text-gray-600 leading-relaxed mt-4" x-text="author.bio"></p>
            </div>

            {{-- yazıları --}}
            <div class="p-6 pt-4">
                <p class="text-xs font-bold uppercase tracking-wider text-gray-500 mb-3">✍️ {{ __('Posts') }}</p>
                <template x-if="!author.posts || author.posts.length === 0">
                    <p class="text-sm text-gray-400">{{ __('No posts published yet.') }}</p>
                </template>
                <div class="space-y-2">
                    <template x-for="post in author.posts" :key="post.url">
                        <a :href="post.url" class="block p-3 rounded-lg border border-gray-100 hover:bg-indigo-50 hover:border-indigo-200 transition">
                            <p class="font-semibold text-sm text-gray-900 leading-snug" x-text="post.title"></p>
                            <p class="text-xs text-gray-400 mt-1">
                                <span x-text="post.date"></span> · <span x-text="post.min"></span> {{ __('min read') }}
                            </p>
                        </a>
                    </template>
                </div>
            </div>
        </div>
    </div>

    {{-- KATIL CTA --}}
    <section class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-2xl p-8 text-center">
        <h2 class="text-2xl font-extrabold mb-2">🌟 {{ __('Contribute too') }}</h2>
        <p class="text-indigo-100 mb-5 max-w-2xl mx-auto">
            {{ __('Content writing, German translation, mentoring or sharing your student experience — AlmanyaUni grows with community support.') }}
        </p>
        <div class="flex flex-wrap gap-3 justify-center">
            <a href="{{ route('contribute') }}"
               title="{{ __('Share your experience') }}"
               class="bg-white text-indigo-700 hover:bg-gray-100 px-6 py-3 rounded-lg font-bold shadow-lg transition">🌱 {{ __('Share your experience') }}</a>
            <a href="{{ route('contribute') }}?role=editor"
               title="{{ __('Become a content editor') }} — technsug@gmail.com"
               class="bg-white/15 backdrop-blur hover:bg-white/25 px-6 py-3 rounded-lg font-bold transition">✍️ {{ __('Become a content editor') }}</a>
            <a href="{{ route('mentors.index') }}"
               title="{{ __('Become a mentor') }}"
               class="bg-white/15 backdrop-blur hover:bg-white/25 px-6 py-3 rounded-lg font-bold transition">🤝 {{ __('Become a mentor') }}</a>
        </div>
    </section>
</div>
@endsection
