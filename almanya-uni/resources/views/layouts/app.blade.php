<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ $localeConfig['direction'] ?? 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', brand('name'))</title>
    <style>[x-cloak]{display:none!important}</style>

    {{-- Global Schema.org (every page) --}}
    <script type="application/ld+json">{!! json_encode(\App\Support\Seo::organization(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
    <script type="application/ld+json">{!! json_encode(\App\Support\Seo::website(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>

    {{-- hreflang alternates (SEO) --}}
    @php
        $activeLocales = collect(config('locale.locales', []))->filter(fn ($c) => ! empty($c['active']))->keys();
        // x-default: default dil hazırsa o, değilse ilk aktif dil (şu an tr → /tr)
        $__defCfg = config('locale.locales.' . config('locale.default') . '');
        $xDefaultLocale = (! empty($__defCfg['active']) && empty($__defCfg['coming_soon']))
            ? config('locale.default')
            : ($activeLocales->first() ?? config('locale.default'));
        // Menü sayıları — dinamik + 6 saat cache (her sayfada DB sorgusu olmasın)
        $navCounts = cache()->remember('nav_counts_v1', now()->addHours(6), fn () => [
            'universities' => \App\Models\University::where('is_active', 1)->count(),
            'programs'     => \App\Models\Program::where('is_active', 1)->count(),
            'cities'       => \App\Models\City::where('is_active', 1)->count(),
            'professions'  => \App\Models\Profession::where('is_active', 1)->count(),
            'states'       => \App\Models\State::count(),
            'fields'       => \App\Models\FieldOfStudy::count(),
        ]);
    @endphp
    @foreach ($activeLocales as $loc)
        <link rel="alternate" hreflang="{{ $loc }}" href="{{ localized_url($loc) }}">
    @endforeach
    <link rel="alternate" hreflang="x-default" href="{{ localized_url($xDefaultLocale) }}">

    {{-- PWA + iOS native --}}
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#1e40af">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="{{ brand('apple_title') }}">
    <link rel="apple-touch-icon" href="/favicon.ico">

    {{-- Self-canonical (her sayfa) — query string'siz mevcut URL (duplicate content önleme) --}}
    <link rel="canonical" href="{{ url(request()->path()) }}">

    @stack('meta')
    @stack('head')

    {{-- Search Console verification tag'leri (.env'den) --}}
    @if($v = config('seo.verification.google'))
        <meta name="google-site-verification" content="{{ $v }}">
    @endif
    @if($v = config('seo.verification.bing'))
        <meta name="msvalidate.01" content="{{ $v }}">
    @endif
    @if($v = config('seo.verification.yandex'))
        <meta name="yandex-verification" content="{{ $v }}">
    @endif

    {{-- RSS feed discovery --}}
    <link rel="alternate" type="application/rss+xml" title="{{ brand('name') . ' — ' . __('Latest Content') }}" href="{{ url('/rss.xml') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white text-gray-900 antialiased">

    {{-- =================================================== --}}
    {{-- EVENT TOP BANNER (öne çıkan yaklaşan etkinlik)        --}}
    {{-- =================================================== --}}
    @php $currentEvent = \App\Models\Event::currentBanner(); @endphp
    @if ($currentEvent)
        <x-event-banner :event="$currentEvent" />
    @endif

    {{-- =================================================== --}}
    {{-- HEADER                                                 --}}
    {{-- =================================================== --}}
    <header class="bg-primary-700 text-white shadow-md sticky top-0 z-50">
        <nav class="max-w-[1400px] mx-auto px-4 py-3 flex items-center justify-between gap-4">
            {{-- Logo (locale-aware brand) --}}
            <a href="{{ route('home') }}" class="flex items-center font-extrabold text-xl whitespace-nowrap" aria-label="{{ brand('name') }}" title="{{ brand('name') }} — {{ __('Home') }}">
                <x-brand-logo variant="white" />
            </a>

            {{-- Desktop nav — Mega menü (5 kategori) --}}
            <div class="hidden md:flex items-center gap-0.5 text-sm font-medium">
                @php
                    $isActive = fn ($pattern) => request()->is($pattern);
                    $btnBase  = 'px-3 py-2 rounded-md transition whitespace-nowrap inline-flex items-center gap-1';
                    $active   = 'bg-white/15 text-white';
                    $inactive = 'text-primary-100 hover:bg-white/10 hover:text-white';

                    $kesfetActive   = $isActive('universities*') || $isActive('programs*') || $isActive('cities*') || $isActive('states*') || $isActive('fields*') || $isActive('professions*') || $isActive('map*') || $isActive('rankings*') || $isActive('compare*');
                    $araclarActive  = $isActive('tools*') || $isActive('housing*');
                    $firsatActive   = $isActive('scholarships*') || $isActive('events*') || $isActive('etkinlik*');
                    $icerikActive   = $isActive('blog*') || $isActive('faq*') || $isActive('about*');
                @endphp

                @php
                    // Mega panel ortak stilleri
                    $panelWrap = 'mega-panel hidden absolute left-0 top-full pt-3 z-50'; // top-full+pt = hover köprüsü (boşlukta kapanmaz)
                    $panelCard = 'bg-white text-gray-900 rounded-2xl shadow-2xl border border-gray-200 p-3';
                    $megaHead  = 'px-3 pt-1 pb-2 mb-1 border-b border-gray-100';
                    // İkon rozetli + açıklamalı item
                    $itemCls = 'group/mi flex items-start gap-3 px-3 py-2.5 rounded-xl hover:bg-primary-50 transition';
                @endphp

                {{-- 1) KEŞFET --}}
                @php $kesfetItems = \App\Models\MenuPage::forGroup('kesfet')->where('key', '!=', 'compare.index'); @endphp
                @if ($kesfetItems->isNotEmpty())
                <div class="relative group" data-mega>
                    <button class="{{ $btnBase }} {{ $kesfetActive ? $active : $inactive }}">
                        🔍 {{ __('Explore') }}
                        <svg class="w-3 h-3 transition-transform group-hover:rotate-180" fill="currentColor" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/></svg>
                    </button>
                    <div class="{{ $panelWrap }} w-[520px]">
                        <div class="{{ $panelCard }}">
                            <div class="{{ $megaHead }}">
                                <p class="text-sm font-bold text-gray-900">🔍 {{ __('Explore') }}</p>
                                <p class="text-xs text-gray-500">{{ number_format($navCounts['universities'], 0, ',', '.') }} {{ __('universities') }} · {{ number_format($navCounts['programs'], 0, ',', '.') }} {{ __('programs') }} · {{ number_format($navCounts['cities'], 0, ',', '.') }} {{ __('cities') }}</p>
                            </div>
                            <div class="grid grid-cols-2 gap-0.5">
                                @foreach ($kesfetItems as $item)
                                    @if ($url = $item->resolved_url)
                                        <a href="{{ $url }}" class="{{ $itemCls }}" title="{{ $item->label }}{{ $item->description ? ' — ' . $item->description : '' }}">
                                            <span class="w-9 h-9 rounded-lg bg-primary-50 group-hover/mi:bg-primary-100 flex items-center justify-center text-lg shrink-0">{{ $item->icon }}</span>
                                            <span class="min-w-0">
                                                <span class="block font-semibold text-gray-900 leading-tight">{{ $item->label }}</span>
                                                <span class="block text-xs text-gray-400 leading-tight">{{ $item->description }}</span>
                                            </span>
                                        </a>
                                    @endif
                                @endforeach
                            </div>
                            @if (\App\Models\MenuPage::isKeyEnabled('compare.index'))
                            <a href="{{ route('compare.index') }}" class="flex items-center justify-center gap-2 mt-1 px-3 py-2.5 rounded-xl bg-gradient-to-r from-primary-600 to-primary-700 text-white font-semibold text-sm hover:from-primary-700 hover:to-primary-800 transition" title="{{ __('Compare') }} — {{ __('2-4 universities side by side') }}">
                                ⚖️ {{ __('Compare') }} <span class="text-primary-200 font-normal">— {{ __('2-4 universities side by side') }}</span>
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
                @endif

                {{-- 2) ARAÇLAR --}}
                @php $araclarItems = \App\Models\MenuPage::forGroup('araclar')->where('key', '!=', 'tools.index'); @endphp
                @if ($araclarItems->isNotEmpty())
                <div class="relative group" data-mega>
                    <button class="{{ $btnBase }} {{ $araclarActive ? $active : $inactive }}">
                        🛠️ {{ __('Tools') }}
                        <svg class="w-3 h-3 transition-transform group-hover:rotate-180" fill="currentColor" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/></svg>
                    </button>
                    <div class="{{ $panelWrap }} w-[540px]">
                        <div class="{{ $panelCard }}">
                            <div class="{{ $megaHead }}">
                                <p class="text-sm font-bold text-gray-900">🛠️ {{ __('Decision Tools') }}</p>
                                <p class="text-xs text-gray-500">{{ __('Decide with numbers, don\'t guess') }}</p>
                            </div>
                            <div class="grid grid-cols-2 gap-0.5">
                                @foreach ($araclarItems as $item)
                                    @if ($url = $item->resolved_url)
                                        <a href="{{ $url }}" class="{{ $itemCls }}" title="{{ $item->label }}{{ $item->description ? ' — ' . $item->description : '' }}">
                                            <span class="w-9 h-9 rounded-lg bg-primary-50 group-hover/mi:bg-primary-100 flex items-center justify-center text-lg shrink-0">{{ $item->icon }}</span>
                                            <span class="min-w-0 flex-1">
                                                <span class="font-semibold text-gray-900 leading-tight flex items-center gap-1.5">{{ $item->label }} @if($item->badge)<span class="text-[9px] font-bold uppercase px-1 py-0.5 rounded bg-rose-100 text-rose-700">{{ $item->badge }}</span>@endif</span>
                                                <span class="block text-xs text-gray-400 leading-tight">{{ $item->description }}</span>
                                            </span>
                                        </a>
                                    @endif
                                @endforeach
                            </div>
                            @if (\App\Models\MenuPage::isKeyEnabled('tools.index'))
                            <a href="{{ route('tools.index') }}" class="block text-xs text-primary-600 hover:text-primary-800 px-3 py-2 text-center font-medium" title="{{ __('See all tools') }}">{{ __('See all tools →') }}</a>
                            @endif
                        </div>
                    </div>
                </div>
                @endif

                {{-- 3) FIRSATLAR --}}
                @php $firsatItems = \App\Models\MenuPage::forGroup('firsatlar'); @endphp
                @if ($firsatItems->isNotEmpty())
                <div class="relative group" data-mega>
                    <button class="{{ $btnBase }} {{ $firsatActive ? $active : $inactive }}">
                        🎖️ {{ __('Opportunities') }}
                        <svg class="w-3 h-3 transition-transform group-hover:rotate-180" fill="currentColor" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/></svg>
                    </button>
                    <div class="{{ $panelWrap }} w-[440px]">
                        <div class="{{ $panelCard }}">
                            <div class="{{ $megaHead }}">
                                <p class="text-sm font-bold text-gray-900">🎖️ {{ __('Opportunities') }}</p>
                                <p class="text-xs text-gray-500">{{ __('Scholarship · event · mentor') }}</p>
                            </div>
                            <div class="space-y-0.5">
                                @foreach ($firsatItems as $item)
                                    @if ($url = $item->resolved_url)
                                        <a href="{{ $url }}" class="group/mi flex items-start gap-3 px-3 py-2.5 rounded-xl hover:bg-emerald-50 transition">
                                            <span class="w-9 h-9 rounded-lg bg-emerald-50 flex items-center justify-center text-lg shrink-0">{{ $item->icon }}</span>
                                            <span class="min-w-0 flex-1">
                                                <span class="font-semibold text-gray-900 leading-tight flex items-center gap-1.5">{{ $item->label }} @if($item->badge)<span class="text-[9px] font-bold uppercase px-1 py-0.5 rounded bg-amber-100 text-amber-700">{{ $item->badge }}</span>@endif</span>
                                                <span class="block text-xs text-gray-400 leading-tight">{{ $item->description }}</span>
                                            </span>
                                        </a>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                {{-- 4) İÇERİK --}}
                @php $icerikItems = \App\Models\MenuPage::forGroup('icerik'); @endphp
                @if ($icerikItems->isNotEmpty())
                <div class="relative group" data-mega>
                    <button class="{{ $btnBase }} {{ $icerikActive ? $active : $inactive }}">
                        📚 {{ __('Content') }}
                        <svg class="w-3 h-3 transition-transform group-hover:rotate-180" fill="currentColor" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/></svg>
                    </button>
                    <div class="{{ $panelWrap }} w-[400px]">
                        <div class="{{ $panelCard }}">
                            <div class="{{ $megaHead }}">
                                <p class="text-sm font-bold text-gray-900">📚 {{ __('Content & Community') }}</p>
                                <p class="text-xs text-gray-500">{{ __('Guides · questions · team') }}</p>
                            </div>
                            <div class="space-y-0.5">
                                @foreach ($icerikItems as $item)
                                    @if ($url = $item->resolved_url)
                                        <a href="{{ $url }}" class="group/mi flex items-start gap-3 px-3 py-2.5 rounded-xl hover:bg-primary-50 transition">
                                            <span class="w-9 h-9 rounded-lg bg-primary-50 group-hover/mi:bg-primary-100 flex items-center justify-center text-lg shrink-0">{{ $item->icon }}</span>
                                            <span class="min-w-0 flex-1">
                                                <span class="block font-semibold text-gray-900 leading-tight">{{ $item->label }}</span>
                                                <span class="block text-xs text-gray-400 leading-tight">{{ $item->description }}</span>
                                            </span>
                                        </a>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            {{-- Forum + sağ tarafa öne çıkan CTA --}}
            @php $forumItem = \App\Models\MenuPage::findByKey('forum'); @endphp
            @if ($forumItem && ($forumUrl = $forumItem->resolved_url))
            <a href="{{ $forumUrl }}"
               class="hidden md:inline-flex items-center gap-1.5 px-3 py-2 rounded-md bg-accent-500 hover:bg-accent-600 text-white font-semibold text-sm shadow-md transition whitespace-nowrap"
               title="{{ __('Community Forum') }}">
                {{ $forumItem->icon }} {{ $forumItem->label }}
            </a>
            @endif

            {{-- Right side: auth-aware --}}
            <div class="hidden md:flex items-center gap-3 text-sm">
                {{-- Locale switcher — sadece birden çok aktif dil varsa göster --}}
                @if ($activeLocales->count() > 1)
                <div class="relative" id="localeMenuWrap">
                    <button type="button" id="localeMenuBtn"
                            class="inline-flex items-center gap-1.5 px-2.5 py-2 rounded-md hover:bg-white/10 transition text-sm">
                        <span>{{ $localeConfig['flag'] ?? '🌐' }}</span>
                        <span class="uppercase font-semibold">{{ app()->getLocale() }}</span>
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/></svg>
                    </button>
                    <div id="localeMenu" class="hidden absolute right-0 mt-2 w-40 bg-white text-gray-900 rounded-md shadow-lg border border-gray-200 py-1 z-50">
                        @foreach ($activeLocales as $loc)
                            @php $cfg = config("locale.locales.$loc"); @endphp
                            <a href="{{ localized_url($loc) }}"
                               class="flex items-center gap-2 px-3 py-2 hover:bg-gray-50 text-sm {{ app()->getLocale() === $loc ? 'font-bold bg-gray-50' : '' }}"
                               title="{{ $cfg['native_name'] }}"
                               hreflang="{{ $loc }}">
                                <span>{{ $cfg['flag'] }}</span>
                                <span>{{ $cfg['native_name'] }}</span>
                                @if (app()->getLocale() === $loc) <span class="ml-auto text-primary-600">✓</span> @endif
                            </a>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Header search — açılır kutu --}}
                <div class="relative" id="headerSearchWrap">
                    <button type="button" id="headerSearchBtn"
                            class="p-2 hover:bg-white/10 rounded-md transition"
                            title="{{ __('Ara') }}" aria-label="{{ __('Ara') }}">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.3-4.3M10.5 18a7.5 7.5 0 1 0 0-15 7.5 7.5 0 0 0 0 15Z"/>
                        </svg>
                    </button>
                    <div id="headerSearchDropdown"
                         class="hidden absolute right-0 mt-2 w-96 max-w-[calc(100vw-2rem)] bg-white text-gray-900 rounded-xl shadow-2xl border border-gray-200 z-50 overflow-hidden">
                        <form action="{{ route('search.index') }}" method="GET" class="p-3 border-b border-gray-100">
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">🔍</span>
                                <input type="text" name="q" id="headerSearchInput"
                                       placeholder="{{ __('University, city, program...') }}"
                                       autocomplete="off"
                                       class="w-full pl-10 pr-3 py-2.5 text-sm rounded-lg border border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-100 focus:outline-none">
                            </div>
                        </form>

                        {{-- Live suggestions container --}}
                        <div id="headerSearchResults" class="max-h-[60vh] overflow-y-auto"></div>

                        {{-- Default: popüler chip'ler --}}
                        <div id="headerSearchDefault" class="p-3">
                            <p class="text-xs text-gray-500 mb-2">{{ __('Popular:') }}</p>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach (['TUM', 'Berlin', 'Heidelberg', __('Engineering'), 'Sperrkonto'] as $sug)
                                    <a href="{{ route('search.index', ['q' => $sug]) }}"
                                       class="px-2 py-1 text-xs rounded-full bg-gray-100 hover:bg-primary-50 hover:text-primary-700 text-gray-700 transition"
                                       title="{{ __('Search:') }} {{ $sug }}">{{ $sug }}</a>
                                @endforeach
                            </div>
                            <p class="text-xs text-gray-400 mt-3">{{ __('Open with ⌘K / Ctrl+K · ESC to close') }}</p>
                        </div>
                    </div>
                </div>

                @auth
                    <div class="relative" id="userMenuWrap">
                        <button type="button" id="userMenuBtn"
                                class="inline-flex items-center gap-2 px-3 py-2 rounded-md hover:bg-white/10 transition">
                            <span class="inline-flex items-center justify-center w-7 h-7 bg-accent-500 rounded-full font-bold text-sm">
                                {{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                            </span>
                            <span class="hidden lg:inline">{{ auth()->user()->name }}</span>
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/></svg>
                        </button>
                        <div id="userMenu" class="hidden absolute right-0 mt-2 w-48 bg-white text-gray-900 rounded-md shadow-lg border border-gray-200 py-1 z-50">
                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 hover:bg-gray-50" title="{{ __('Profile') }}">👤 {{ __('Profile') }}</a>
                            @if (auth()->user()->is_admin)
                                <a href="/admin" class="block px-4 py-2 hover:bg-gray-50" title="{{ __('Admin Panel') }}">⚙️ {{ __('Admin Panel') }}</a>
                            @endif
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 hover:bg-gray-50 text-red-600">
                                    ↪ {{ __('Log out') }}
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="text-primary-100 hover:text-white transition" title="{{ __('Log in') }}">{{ __('Log in') }}</a>
                    <a href="{{ route('register') }}" class="bg-accent-500 hover:bg-accent-600 px-4 py-2 rounded-md font-semibold transition" title="{{ __('Sign up') }} — {{ __('Create your free account') }}">{{ __('Sign up') }}</a>
                @endauth
            </div>

            {{-- Mobile burger --}}
            <style>
                @media (min-width: 768px) {
                    [data-mega]:hover > .mega-panel,
                    [data-mega]:focus-within > .mega-panel { display: block !important; }
                }
            </style>
            <button id="mobileMenuBtn" class="md:hidden p-2 hover:bg-white/10 rounded-md" aria-label="{{ __('Menu') }}">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
        </nav>

        {{-- Mobile drawer --}}
        <div id="mobileMenu" class="md:hidden hidden border-t border-white/15 bg-primary-800 max-h-[85vh] overflow-y-auto">
            <div class="px-3 py-3 text-sm font-medium" x-data="{ open: null }">
                {{-- Hızlı arama --}}
                <form action="{{ route('search.index') }}" method="GET" class="mb-3">
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-primary-300">🔍</span>
                        <input type="text" name="q" placeholder="{{ __('Quick search…') }}"
                               class="w-full pl-9 pr-3 py-2 text-sm rounded-md bg-white/10 placeholder-primary-300 text-white focus:bg-white/20 focus:outline-none">
                    </div>
                </form>

                @php
                    $mobileGroups = [
                        'kesfet'    => ['label' => __('Explore'),              'icon' => '🔍'],
                        'araclar'   => ['label' => __('Tools'),                'icon' => '🛠️'],
                        'firsatlar' => ['label' => __('Opportunities'),        'icon' => '🎁'],
                        'icerik'    => ['label' => __('Content & Community'),  'icon' => '📚'],
                    ];
                    $forumStandalone = \App\Models\MenuPage::forGroup('standalone');
                @endphp

                <div class="space-y-1.5">
                @foreach ($mobileGroups as $gKey => $gMeta)
                    @php $items = \App\Models\MenuPage::forGroup($gKey); @endphp
                    @if ($items->isNotEmpty())
                        @php
                            $totalCount = $items->count() + ($gKey === 'icerik' ? $forumStandalone->count() : 0);
                        @endphp
                        <div class="rounded-lg overflow-hidden bg-white/5">
                            {{-- Accordion başlığı --}}
                            <button type="button"
                                    @click="open = (open === '{{ $gKey }}' ? null : '{{ $gKey }}')"
                                    class="w-full flex items-center justify-between px-3 py-2.5 hover:bg-white/10 transition">
                                <span class="flex items-center gap-2 text-white font-semibold">
                                    <span>{{ $gMeta['icon'] }}</span>
                                    <span>{{ $gMeta['label'] }}</span>
                                    <span class="text-[10px] bg-white/15 text-primary-100 px-1.5 py-0.5 rounded-full font-bold">{{ $totalCount }}</span>
                                </span>
                                <span class="text-primary-300 transition-transform"
                                      :class="open === '{{ $gKey }}' ? 'rotate-90' : ''">›</span>
                            </button>

                            {{-- Accordion içerik --}}
                            <div x-show="open === '{{ $gKey }}'" x-cloak x-collapse class="bg-primary-900/40 border-t border-white/10">
                                @foreach ($items as $item)
                                    @if ($url = $item->resolved_url)
                                        <a href="{{ $url }}" class="flex items-center gap-2 px-4 py-2 text-primary-100 hover:bg-white/10 hover:text-white {{ $item->key === 'tools.index' ? 'text-xs italic' : '' }}" title="{{ $item->label }}{{ $item->description ? ' — ' . $item->description : '' }}">
                                            <span class="shrink-0">{{ $item->icon }}</span>
                                            <span class="flex-1 truncate">{{ $item->label }}</span>
                                            @if ($item->badge)
                                                <span class="shrink-0 text-[10px] font-bold uppercase px-1.5 py-0.5 rounded bg-amber-400 text-amber-900">{{ $item->badge }}</span>
                                            @endif
                                        </a>
                                    @endif
                                @endforeach
                                @if ($gKey === 'icerik' && $forumStandalone->isNotEmpty())
                                    @foreach ($forumStandalone as $std)
                                        @if ($url = $std->resolved_url)
                                            <a href="{{ $url }}" class="flex items-center gap-2 px-4 py-2 text-primary-100 hover:bg-white/10 hover:text-white" title="{{ $std->label }}">
                                                <span class="shrink-0">{{ $std->icon }}</span>
                                                <span class="flex-1 truncate">{{ $std->label }}</span>
                                            </a>
                                        @endif
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    @endif
                @endforeach
                </div>

                {{-- Dil değiştirici — kompakt segmented control --}}
                @if ($activeLocales->count() > 1)
                    <div class="pt-3 mt-3 border-t border-white/15">
                        <div class="flex items-center gap-2 px-1">
                            <span class="text-[10px] uppercase tracking-wider text-primary-300 font-bold shrink-0">🌐 {{ __('Dil') }}</span>
                            <div class="inline-flex items-center bg-white/10 rounded-md p-0.5 ml-auto">
                                @foreach ($activeLocales as $loc)
                                    @php $cfg = config("locale.locales.$loc"); @endphp
                                    <a href="{{ localized_url($loc) }}"
                                       class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs transition {{ app()->getLocale() === $loc ? 'bg-white text-primary-900 font-bold shadow-sm' : 'text-primary-100 hover:text-white' }}"
                                       title="{{ $cfg['native_name'] }}"
                                       hreflang="{{ $loc }}">
                                        <span class="text-sm leading-none">{{ $cfg['flag'] }}</span>
                                        <span class="uppercase font-semibold">{{ $loc }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <div class="pt-2 mt-2 border-t border-white/15">
                    @auth
                        <p class="px-3 py-1 text-xs text-primary-200">{{ __('Hello, :name', ['name' => auth()->user()->name]) }}</p>
                        <a href="{{ route('profile.edit') }}" class="block px-3 py-2 rounded-md text-primary-100 hover:bg-white/10 hover:text-white" title="{{ __('Profile') }}">👤 {{ __('Profile') }}</a>
                        @if (auth()->user()->is_admin)
                            <a href="/admin" class="block px-3 py-2 rounded-md text-primary-100 hover:bg-white/10 hover:text-white" title="{{ __('Admin Panel') }}">⚙️ {{ __('Admin Panel') }}</a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left px-3 py-2 rounded-md text-red-300 hover:bg-white/10" title="{{ __('Log out') }}">
                                ↪ {{ __('Log out') }}
                            </button>
                        </form>
                    @else
                        <div class="flex gap-2">
                            <a href="{{ route('login') }}" class="flex-1 text-center px-3 py-2 rounded-md bg-white/10 hover:bg-white/20" title="{{ __('Log in') }}">{{ __('Log in') }}</a>
                            <a href="{{ route('register') }}" class="flex-1 text-center px-3 py-2 rounded-md bg-accent-500 hover:bg-accent-600 font-semibold" title="{{ __('Sign up') }} — {{ __('Create your free account') }}">{{ __('Sign up') }}</a>
                        </div>
                    @endauth
                </div>
            </div>
        </div>

        <script>
            (function () {
                const mBtn = document.getElementById('mobileMenuBtn');
                const mMenu = document.getElementById('mobileMenu');
                if (mBtn && mMenu) {
                    mBtn.addEventListener('click', () => mMenu.classList.toggle('hidden'));
                    // Aynı sayfaya tıklayınca menü kapansın (mobile UX)
                    mMenu.addEventListener('click', (e) => {
                        if (e.target.tagName === 'A' && !e.target.closest('form')) {
                            mMenu.classList.add('hidden');
                        }
                    });
                    // Dışarı tıklayınca kapat
                    document.addEventListener('click', (e) => {
                        if (! mMenu.classList.contains('hidden') && ! mBtn.contains(e.target) && ! mMenu.contains(e.target)) {
                            mMenu.classList.add('hidden');
                        }
                    });
                }

                const uBtn = document.getElementById('userMenuBtn');
                const uMenu = document.getElementById('userMenu');
                const uWrap = document.getElementById('userMenuWrap');
                if (uBtn && uMenu && uWrap) {
                    uBtn.addEventListener('click', (e) => {
                        e.stopPropagation();
                        uMenu.classList.toggle('hidden');
                    });
                    document.addEventListener('click', (e) => {
                        if (! uWrap.contains(e.target)) uMenu.classList.add('hidden');
                    });
                }

                // Locale switcher dropdown
                const lBtn = document.getElementById('localeMenuBtn');
                const lMenu = document.getElementById('localeMenu');
                const lWrap = document.getElementById('localeMenuWrap');
                if (lBtn && lMenu && lWrap) {
                    lBtn.addEventListener('click', (e) => {
                        e.stopPropagation();
                        lMenu.classList.toggle('hidden');
                    });
                    document.addEventListener('click', (e) => {
                        if (! lWrap.contains(e.target)) lMenu.classList.add('hidden');
                    });
                }

                // "Daha ▼" nav dropdown
                const mrBtn = document.getElementById('moreMenuBtn');
                const mrMenu = document.getElementById('moreMenu');
                const mrWrap = document.getElementById('moreMenuWrap');
                if (mrBtn && mrMenu && mrWrap) {
                    mrBtn.addEventListener('click', (e) => {
                        e.stopPropagation();
                        mrMenu.classList.toggle('hidden');
                    });
                    document.addEventListener('click', (e) => {
                        if (! mrWrap.contains(e.target)) mrMenu.classList.add('hidden');
                    });
                }

                // Header search dropdown + live autocomplete
                const hsBtn = document.getElementById('headerSearchBtn');
                const hsDD = document.getElementById('headerSearchDropdown');
                const hsWrap = document.getElementById('headerSearchWrap');
                const hsInput = document.getElementById('headerSearchInput');
                const hsResults = document.getElementById('headerSearchResults');
                const hsDefault = document.getElementById('headerSearchDefault');

                if (hsBtn && hsDD && hsWrap) {
                    hsBtn.addEventListener('click', (e) => {
                        e.stopPropagation();
                        const wasHidden = hsDD.classList.contains('hidden');
                        hsDD.classList.toggle('hidden');
                        if (wasHidden) setTimeout(() => hsInput?.focus(), 50);
                    });
                    document.addEventListener('click', (e) => {
                        if (! hsWrap.contains(e.target)) hsDD.classList.add('hidden');
                    });
                    document.addEventListener('keydown', (e) => {
                        if (e.key === 'Escape') hsDD.classList.add('hidden');
                        if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
                            e.preventDefault();
                            hsDD.classList.remove('hidden');
                            setTimeout(() => hsInput?.focus(), 50);
                        }
                    });

                    // Live autocomplete — debounced fetch
                    let debounceTimer = null;
                    let currentReq = null;

                    function escapeHtml(s) {
                        return String(s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
                    }

                    function renderResults(data) {
                        if (!data.results || data.results.length === 0) {
                            hsResults.innerHTML = `
                                <div class="p-6 text-center">
                                    <div class="text-3xl mb-2">🔍</div>
                                    <p class="text-sm text-gray-600">{{ __('No results for') }} "${escapeHtml(data.q)}"</p>
                                </div>`;
                            hsResults.classList.remove('hidden');
                            hsDefault.classList.add('hidden');
                            return;
                        }

                        let html = '<ul class="divide-y divide-gray-100">';
                        data.results.forEach(r => {
                            const img = r.image
                                ? `<img src="${escapeHtml(r.image)}" alt="" class="w-10 h-10 object-cover rounded shrink-0 bg-gray-100" loading="lazy" decoding="async">`
                                : `<div class="w-10 h-10 rounded bg-primary-100 flex items-center justify-center text-base shrink-0">${escapeHtml(r.icon || '·')}</div>`;
                            html += `
                                <li>
                                    <a href="${escapeHtml(r.url)}" class="flex items-start gap-3 p-3 hover:bg-gray-50 transition">
                                        ${img}
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs text-gray-500 mb-0.5">${escapeHtml(r.type_label)}</p>
                                            <p class="font-semibold text-gray-900 text-sm leading-tight truncate">${escapeHtml(r.title)}</p>
                                            ${r.subtitle ? `<p class="text-xs text-gray-500 truncate">${escapeHtml(r.subtitle)}</p>` : ''}
                                        </div>
                                    </a>
                                </li>`;
                        });
                        html += '</ul>';
                        html += `<a href="${escapeHtml(data.all_url)}" class="block p-3 text-center text-sm text-primary-600 hover:bg-primary-50 font-semibold border-t border-gray-100">{{ __('See all results →') }}</a>`;
                        hsResults.innerHTML = html;
                        hsResults.classList.remove('hidden');
                        hsDefault.classList.add('hidden');
                    }

                    hsInput?.addEventListener('input', (e) => {
                        const q = e.target.value.trim();
                        clearTimeout(debounceTimer);

                        if (q.length < 2) {
                            hsResults.innerHTML = '';
                            hsResults.classList.add('hidden');
                            hsDefault.classList.remove('hidden');
                            return;
                        }

                        debounceTimer = setTimeout(async () => {
                            if (currentReq) currentReq.abort();
                            const ctrl = new AbortController();
                            currentReq = ctrl;
                            try {
                                const res = await fetch('/search/suggest?q=' + encodeURIComponent(q), {
                                    signal: ctrl.signal,
                                    headers: { 'Accept': 'application/json' },
                                });
                                if (res.ok) renderResults(await res.json());
                            } catch (err) {
                                // abort - sessizce geç
                            }
                        }, 250);
                    });
                }
            })();
        </script>
    </header>

    {{-- =================================================== --}}
    {{-- MAIN                                                  --}}
    {{-- =================================================== --}}
    <main class="min-h-screen">
        @yield('content')
        {{ $slot ?? '' }}
    </main>

    {{-- =================================================== --}}
    {{-- NEWSLETTER (footer üstü)                              --}}
    {{-- =================================================== --}}
    <section class="max-w-[1400px] mx-auto px-4 mt-16">
        <x-newsletter-form
            source="footer"
            variant="card"
            :heading="__('Get the weekly Germany guide in your inbox')"
            :subheading="__('New blog posts, application deadlines, scholarship announcements. No spam, unsubscribe anytime.')" />
    </section>

    {{-- =================================================== --}}
    {{-- FOOTER                                                --}}
    {{-- =================================================== --}}
    <footer class="bg-primary-900 text-white mt-12">
        <div class="max-w-[1400px] mx-auto px-4 py-12">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 mb-8">
                <div class="col-span-2 md:col-span-1">
                    <a href="{{ route('home') }}" class="flex items-center font-extrabold text-lg mb-3" aria-label="{{ brand('name') }}" title="{{ brand('name') }} — {{ __('Home') }}">
                        <x-brand-logo variant="white" height="h-8" />
                    </a>
                    <p class="text-primary-200 text-sm">{{ __('University and education guide to Germany for international students.') }}</p>
                    @include('partials._social_icons')
                </div>
                <div>
                    <h4 class="font-semibold mb-3 text-white">{{ __('Explore') }}</h4>
                    <ul class="space-y-2 text-primary-200 text-sm">
                        <li><a href="{{ route('universities.index') }}" class="hover:text-white transition" title="{{ __('Universities') }} — {{ __('Browse universities in Germany') }}">{{ __('Universities') }}</a></li>
                        <li><a href="{{ route('programs.index') }}" class="hover:text-white transition" title="{{ __('Programs') }} — {{ __('Bachelor, Master and PhD programs') }}">{{ __('Programs') }}</a></li>
                        <li><a href="{{ route('rankings.index') }}" class="hover:text-white transition" title="{{ __('Rankings') }} — {{ __('University rankings') }}">{{ __('Rankings') }}</a></li>
                        <li><a href="{{ route('faqs.index') }}" class="hover:text-white transition" title="{{ __('Frequently Asked Questions') }}">{{ __('FAQ') }}</a></li>
                        <li><a href="{{ route('blog.index') }}" class="hover:text-white transition" title="{{ __('Blog') }} — {{ __('Guides and tips') }}">{{ __('Blog') }}</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-3 text-white">{{ __('Tools') }}</h4>
                    <ul class="space-y-2 text-primary-200 text-sm">
                        <li><a href="{{ route('tools.cost-of-living') }}" class="hover:text-white transition" title="{{ __('Cost of Living') }} — {{ __('Living costs by city') }}">{{ __('Cost of Living') }}</a></li>
                        <li><a href="{{ route('tools.grade-converter') }}" class="hover:text-white transition" title="{{ __('Grade Converter') }} — {{ __('Convert your GPA to German system') }}">{{ __('Grade Converter') }}</a></li>
                        <li><a href="{{ route('tools.recommendation') }}" class="hover:text-white transition" title="{{ __('University Recommendation') }} — {{ __('Find the best fit for you') }}">{{ __('University Recommendation') }}</a></li>
                        <li><a href="{{ route('compare.index') }}" class="hover:text-white transition" title="{{ __('Compare') }} — {{ __('2-4 universities side by side') }}">{{ __('Compare') }}</a></li>
                        <li><a href="{{ route('professions.index') }}" class="hover:text-white transition" title="{{ __('Professions') }} — {{ __('Job market in Germany') }}">{{ __('Professions') }} 🔒</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-3 text-white">{{ __('About') }}</h4>
                    <ul class="space-y-2 text-primary-200 text-sm">
                        <li><a href="{{ route('about') }}" class="hover:text-white transition" title="{{ __('About Us') }} — {{ brand('name') }}">{{ __('About Us') }}</a></li>
                        <li><a href="mailto:technsug@gmail.com" class="hover:text-white transition" title="{{ __('Contact') }} — technsug@gmail.com">{{ __('Contact') }}</a></li>
                        <li><a href="mailto:technsug@gmail.com?subject=Contribute" class="hover:text-white transition" title="{{ __('Contribute') }} — {{ __('Help improve the site') }}">{{ __('Contribute') }}</a></li>
                        <li><a href="{{ route('legal.privacy') }}" class="hover:text-white transition" title="{{ __('Privacy Policy') }}">{{ __('Privacy Policy') }}</a></li>
                        <li><a href="{{ route('legal.terms') }}" class="hover:text-white transition" title="{{ __('Terms of Use') }}">{{ __('Terms of Use') }}</a></li>
                        <li><a href="{{ route('legal.cookies') }}" class="hover:text-white transition" title="{{ __('Cookie Policy') }}">{{ __('Cookie Policy') }}</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-primary-800 pt-6 flex flex-col md:flex-row justify-between items-center gap-3 text-sm">
                <p class="text-primary-300">&copy; {{ date('Y') }} {{ brand('copyright') }}. {{ __('All rights reserved.') }}</p>
                <div class="flex items-center gap-4">
                    <a href="{{ url('/rss.xml') }}" class="text-primary-300 hover:text-white inline-flex items-center gap-1 text-xs" title="RSS Feed — {{ __('Subscribe for latest content') }}">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M2 5a1 1 0 011-1h.01a8 8 0 018.13 8.13V14a1 1 0 11-2 0v-1.87A6 6 0 003.01 6H3a1 1 0 01-1-1zm0 4a1 1 0 011-1h.01A4 4 0 017 12.13V14a1 1 0 11-2 0v-1.87a2 2 0 00-2-2H3a1 1 0 01-1-1zm0 4a1 1 0 011-1 2 2 0 012 2 1 1 0 11-2 0 1 1 0 01-1-1z"/></svg>
                        RSS
                    </a>
                    <a href="{{ route('api.docs') }}" class="text-primary-300 hover:text-white text-xs" title="{{ __('Public API documentation') }}">API</a>
                    <p class="text-primary-400 text-xs">{{ __('Up-to-date guide on studying in Germany') }}</p>
                </div>
            </div>
        </div>
    </footer>

    {{-- PWA Service Worker registration --}}
    <script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function () {
            navigator.serviceWorker.register('/sw.js').catch(function (err) {
                console.warn('SW registration failed:', err);
            });
        });
    }
    </script>

    {{-- ════════ Feedback Widget ════════ --}}
    <div id="feedbackContainer">
        <button type="button" id="feedbackToggle"
                class="fixed bottom-4 right-4 z-40 w-12 h-12 md:w-14 md:h-14 rounded-full bg-primary-600 hover:bg-primary-700 text-white shadow-lg flex items-center justify-center text-xl md:text-2xl transition hover:scale-110"
                title="{{ __('Feedback') }}" aria-label="{{ __('Feedback') }}">
            💬
        </button>

        <div id="feedbackModal" class="hidden fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-end md:items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-y-auto">
                <div class="p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">💬 {{ __('Feedback') }}</h3>
                            <p class="text-xs text-gray-500 mt-1">{{ __('Tell us about a bug, suggestion or issue') }}</p>
                        </div>
                        <button type="button" id="feedbackClose"
                                class="w-8 h-8 rounded-full hover:bg-gray-100 flex items-center justify-center text-gray-500" aria-label="{{ __('Close') }}">×</button>
                    </div>

                    <form id="feedbackForm" class="space-y-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">{{ __('Topic type *') }}</label>
                            <select name="type" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:border-primary-500 focus:outline-none">
                                <option value="general">💬 {{ __('General') }}</option>
                                <option value="bug">🐛 {{ __('Bug report') }}</option>
                                <option value="suggestion">💡 {{ __('Suggestion') }}</option>
                                <option value="content">📝 {{ __('Content correction') }}</option>
                                <option value="partnership">🤝 {{ __('Partnership') }}</option>
                                <option value="other">❓ {{ __('Other') }}</option>
                            </select>
                        </div>

                        @guest
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">{{ __('Your name') }}</label>
                                <input type="text" name="name" maxlength="120" placeholder="{{ __('Optional') }}"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:border-primary-500 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">{{ __('Email') }}</label>
                                <input type="email" name="email" maxlength="255" placeholder="{{ __('Add if you want a reply') }}"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:border-primary-500 focus:outline-none">
                            </div>
                        @endguest

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">{{ __('Your message *') }}</label>
                            <textarea name="message" required minlength="5" maxlength="5000" rows="5"
                                      placeholder="{{ __('Describe the issue, suggestion or observation in detail...') }}"
                                      class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:border-primary-500 focus:outline-none"></textarea>
                            <p class="text-xs text-gray-400 mt-1">{{ __('Min 5 characters') }}</p>
                        </div>

                        <input type="hidden" name="page_url" id="feedbackPageUrl">

                        <button type="submit" id="feedbackSubmit"
                                class="w-full bg-primary-600 hover:bg-primary-700 text-white font-semibold py-2.5 rounded-lg transition">
                            {{ __('Send') }}
                        </button>
                        <p id="feedbackResult" class="hidden text-sm text-center"></p>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
    (function() {
        const toggle = document.getElementById('feedbackToggle');
        const modal = document.getElementById('feedbackModal');
        const close = document.getElementById('feedbackClose');
        const form = document.getElementById('feedbackForm');
        const submit = document.getElementById('feedbackSubmit');
        const result = document.getElementById('feedbackResult');
        const pageUrlInput = document.getElementById('feedbackPageUrl');
        if (!toggle || !modal) return;

        function open() {
            modal.classList.remove('hidden');
            pageUrlInput.value = window.location.pathname + window.location.search;
            document.body.style.overflow = 'hidden';
        }
        function hide() {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
            setTimeout(() => { form.reset(); result.classList.add('hidden'); }, 200);
        }

        toggle.addEventListener('click', open);
        close.addEventListener('click', hide);
        modal.addEventListener('click', e => { if (e.target === modal) hide(); });
        document.addEventListener('keydown', e => { if (e.key === 'Escape' && !modal.classList.contains('hidden')) hide(); });

        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            submit.disabled = true;
            submit.textContent = '{{ __('Sending…') }}';
            result.classList.add('hidden');

            try {
                const fd = new FormData(form);
                const data = Object.fromEntries(fd.entries());
                const res = await fetch('{{ route('feedback.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(data),
                });
                const out = await res.json();
                if (res.ok) {
                    result.textContent = '✓ ' + (out.message || '{{ __('Thank you!') }}');
                    result.className = 'text-sm text-center text-emerald-600 font-semibold';
                    result.classList.remove('hidden');
                    setTimeout(hide, 2000);
                } else {
                    result.textContent = out.message || '{{ __('An error occurred, please try again') }}';
                    result.className = 'text-sm text-center text-red-600';
                    result.classList.remove('hidden');
                }
            } catch (e) {
                result.textContent = '{{ __('Connection error — check your internet') }}';
                result.className = 'text-sm text-center text-red-600';
                result.classList.remove('hidden');
            } finally {
                submit.disabled = false;
                submit.textContent = '{{ __('Send') }}';
            }
        });
    })();
    </script>

    {{-- ════════ KVKK Cookie Consent ════════ --}}
    {{-- Mobile: top (feedback button alt sağda olduğu için üstten girer) --}}
    {{-- Desktop: bottom-left (max-w-md, feedback button sağ altta kalabilir) --}}
    <div id="cookieConsent"
         class="hidden fixed top-20 left-4 right-4 md:top-auto md:bottom-4 md:right-auto md:left-4 md:max-w-md z-50
                bg-white border border-gray-200 rounded-xl shadow-2xl p-5"
         role="dialog" aria-labelledby="cookieConsentTitle">
        <div class="flex items-start gap-3 mb-3">
            <span class="text-3xl">🍪</span>
            <div>
                <h3 id="cookieConsentTitle" class="font-bold text-gray-900 mb-1">{{ __('Cookie Preferences') }}</h3>
                <p class="text-sm text-gray-600 leading-relaxed">
                    {{ __('We keep') }} <strong>{{ __('anonymous visitor statistics') }}</strong> {{ __('to improve the site (no Google Analytics, hosted on our own server). Your IP is hashed, no personal info is stored.') }}
                    <a href="{{ route('legal.cookies') }}" class="text-primary-600 hover:underline">{{ __('Details') }}</a>
                </p>
            </div>
        </div>
        <div class="flex gap-2 flex-wrap">
            <button type="button" id="cookieAccept"
                    class="flex-1 bg-primary-600 hover:bg-primary-700 text-white font-semibold px-4 py-2 rounded-lg text-sm transition">
                {{ __('Accept') }}
            </button>
            <button type="button" id="cookieReject"
                    class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold px-4 py-2 rounded-lg text-sm transition">
                {{ __('Reject') }}
            </button>
        </div>
    </div>

    <script>
    (function () {
        const KEY = 'almanyauni_cookie_consent';
        const banner = document.getElementById('cookieConsent');
        const accept = document.getElementById('cookieAccept');
        const reject = document.getElementById('cookieReject');
        if (!banner) return;

        const choice = localStorage.getItem(KEY);
        if (!choice) {
            // İlk ziyaret — banner göster
            setTimeout(() => banner.classList.remove('hidden'), 1500);
        }

        function hide() {
            banner.classList.add('hidden');
        }

        function setCookie(name, value, days) {
            const d = new Date();
            d.setTime(d.getTime() + days * 86400000);
            document.cookie = name + '=' + value + ';expires=' + d.toUTCString() + ';path=/;SameSite=Lax';
        }

        accept?.addEventListener('click', function () {
            localStorage.setItem(KEY, 'accepted');
            setCookie('almanyauni_consent', 'accepted', 365);
            hide();
        });

        reject?.addEventListener('click', function () {
            localStorage.setItem(KEY, 'rejected');
            setCookie('almanyauni_consent', 'rejected', 365);
            // Analytics tracker cookie sil
            document.cookie = 'almanyauni_uid=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
            hide();
        });
    })();
    </script>

    @auth
    <script>
    // Favori toggle — herhangi bir .fav-btn'e tıklayınca AJAX
    (function () {
        document.addEventListener('click', async function (e) {
            const btn = e.target.closest('.fav-btn');
            if (! btn) return;
            e.preventDefault();

            const type = btn.dataset.favType;
            const id = btn.dataset.favId;
            const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
            if (! csrf) return;

            btn.disabled = true;
            try {
                const resp = await fetch('/favorites/toggle', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({ type, id }),
                });
                if (! resp.ok) throw new Error('Hata: ' + resp.status);
                const data = await resp.json();

                // Görsel toggle
                const icon = btn.querySelector('.fav-icon');
                const label = btn.querySelector('.fav-label');
                if (data.action === 'added') {
                    btn.classList.remove('bg-white', 'border-gray-300', 'text-gray-700');
                    btn.classList.add('bg-pink-50', 'border-pink-300', 'text-pink-700');
                    icon?.setAttribute('fill', 'currentColor');
                    icon?.classList.add('fill-current');
                    if (label) label.textContent = '{{ __('In favorites') }}';
                } else {
                    btn.classList.remove('bg-pink-50', 'border-pink-300', 'text-pink-700');
                    btn.classList.add('bg-white', 'border-gray-300', 'text-gray-700');
                    icon?.setAttribute('fill', 'none');
                    icon?.classList.remove('fill-current');
                    if (label) label.textContent = '{{ __('Favorite') }}';
                }
            } catch (err) {
                console.error(err);
                alert('{{ __('Action failed. Please try again.') }}');
            } finally {
                btn.disabled = false;
            }
        });
    })();
    </script>
    @endauth

    @stack('scripts')

    {{-- Otomatik iç-linkleme: glossary tooltip + entity link — global (tüm içerik sayfaları) --}}
    <style>
        .glossary-term { border-bottom: 1.5px dotted #6366f1; cursor: help; color: #4338ca; font-weight: 500; }
        .glossary-term:hover { background: #eef2ff; border-radius: 3px; }
        .auto-link { color: #4f46e5; text-decoration: underline; text-decoration-style: dotted; text-underline-offset: 2px; font-weight: 500; }
        .auto-link:hover { text-decoration-style: solid; background: #eef2ff; border-radius: 3px; }
        .glossary-tip { position: absolute; z-index: 60; max-width: 280px; background: #1e293b; color: #f1f5f9; font-size: 13px; line-height: 1.5; padding: 10px 12px; border-radius: 8px; box-shadow: 0 8px 24px rgba(0,0,0,.18); opacity: 0; transition: opacity .12s; pointer-events: none; }
        .glossary-tip.visible { opacity: 1; }
    </style>
    <script>
    (function () {
        let tip = null;
        function showTip(el) {
            const text = el.dataset.tip;
            if (!text) return;
            hideTip();
            tip = document.createElement('div');
            tip.className = 'glossary-tip';
            tip.textContent = text;
            document.body.appendChild(tip);
            const r = el.getBoundingClientRect();
            const tr = tip.getBoundingClientRect();
            let left = r.left + r.width / 2 - tr.width / 2 + window.scrollX;
            left = Math.max(8, Math.min(left, window.innerWidth - tr.width - 8));
            let top = r.bottom + window.scrollY + 8;
            if (r.bottom + tr.height + 16 > window.innerHeight) top = r.top + window.scrollY - tr.height - 8;
            tip.style.left = left + 'px';
            tip.style.top = top + 'px';
            tip.classList.add('visible');
        }
        function hideTip() { if (tip) { tip.remove(); tip = null; } }
        document.addEventListener('mouseenter', e => { if (e.target.classList && e.target.classList.contains('glossary-term')) showTip(e.target); }, true);
        document.addEventListener('mouseleave', e => { if (e.target.classList && e.target.classList.contains('glossary-term')) hideTip(); }, true);
        document.addEventListener('click', e => {
            if (e.target.classList && e.target.classList.contains('glossary-term')) {
                e.preventDefault();
                (tip && tip.classList.contains('visible')) ? hideTip() : showTip(e.target);
            }
        });
        window.addEventListener('scroll', hideTip, { passive: true });
    })();
    </script>
</body>
</html>
