@extends('layouts.app')

@php
    $title = $author->name . ' — ' . ($author->role_label ?: __('Author')) . ' · ' . brand('name');
    $description = $author->bio
        ? \Illuminate\Support\Str::limit($author->bio, 160)
        : __('Articles, expertise and contributions by :name on AlmanyaUni.', ['name' => $author->name]);

    $socials = (array) ($author->social_links ?? []);
    $sameAs = [];
    foreach ($socials as $type => $value) {
        if (! $value) continue;
        $sameAs[] = match ($type) {
            'twitter', 'x'  => 'https://x.com/' . ltrim($value, '@'),
            'linkedin'      => str_starts_with($value, 'http') ? $value : 'https://linkedin.com/in/' . $value,
            'github'        => 'https://github.com/' . ltrim($value, '@'),
            'website', 'url'=> $value,
            'email'         => 'mailto:' . $value,
            default         => $value,
        };
    }
    $sameAs = array_values(array_filter($sameAs));
@endphp

@section('title', $title)
<x-seo :title="$title" :description="$description" :image="$author->avatar_url" />

@push('head')
<script type="application/ld+json">{!! json_encode([
    '@context' => 'https://schema.org',
    '@graph'   => [
        [
            '@type'       => 'ProfilePage',
            '@id'         => url()->current() . '#profile',
            'mainEntity'  => ['@id' => url()->current() . '#person'],
            'inLanguage'  => app()->getLocale(),
        ],
        array_filter([
            '@type'       => 'Person',
            '@id'         => url()->current() . '#person',
            'name'        => $author->name,
            'description' => $author->bio,
            'jobTitle'    => $author->role_label,
            'image'       => $author->avatar_url,
            'url'         => url()->current(),
            'sameAs'      => $sameAs,
            'worksFor'    => [
                '@type' => 'Organization',
                'name'  => brand('name'),
                'url'   => url('/'),
            ],
        ]),
    ],
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
@endpush

@section('content')

<section class="bg-gradient-to-br from-indigo-700 via-purple-600 to-pink-500 text-white">
    <div class="max-w-[1100px] mx-auto px-4 py-12 md:py-14">
        <nav class="text-sm text-indigo-100 mb-3">
            <a href="{{ route('home') }}" class="hover:text-white">{{ __('Home') }}</a>
            <span class="mx-2 opacity-60">›</span>
            <a href="{{ route('team') }}" class="hover:text-white">{{ __('Team') }}</a>
            <span class="mx-2 opacity-60">›</span>
            <span class="text-white">{{ $author->name }}</span>
        </nav>

        <div class="flex flex-col md:flex-row gap-6 items-start md:items-center">
            @if ($author->avatar_url)
                <img src="{{ $author->avatar_url }}" alt="{{ $author->name }}"
                     class="w-24 h-24 md:w-32 md:h-32 rounded-full object-cover ring-4 ring-white/30 flex-shrink-0">
            @endif
            <div class="flex-1 min-w-0">
                <h1 class="text-3xl md:text-5xl font-extrabold leading-tight drop-shadow mb-2">{{ $author->name }}</h1>
                @if ($author->role_label)
                    <p class="text-lg md:text-xl text-indigo-100 font-semibold mb-2">{{ $author->role_label }}</p>
                @endif
                @if ($author->bio)
                    <p class="text-base text-indigo-50 max-w-2xl leading-relaxed">{{ $author->bio }}</p>
                @endif

                @if (! empty($sameAs))
                    <div class="flex flex-wrap gap-2 mt-4">
                        @foreach ($sameAs as $url)
                            @php
                                $host = parse_url($url, PHP_URL_HOST) ?: '';
                                $icon = match (true) {
                                    str_contains($host, 'linkedin') => '💼',
                                    str_contains($host, 'x.com'), str_contains($host, 'twitter') => '𝕏',
                                    str_contains($host, 'github')   => '⌨️',
                                    str_starts_with($url, 'mailto') => '✉️',
                                    default                          => '🌐',
                                };
                            @endphp
                            <a href="{{ $url }}" target="_blank" rel="noopener nofollow me"
                               class="inline-flex items-center gap-1.5 bg-white/15 hover:bg-white/25 backdrop-blur-sm px-3 py-1.5 rounded-full text-xs font-medium transition">
                                <span>{{ $icon }}</span>
                                <span>{{ str_replace(['https://', 'http://', 'mailto:'], '', $url) }}</span>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

<div class="max-w-[1100px] mx-auto px-4 py-10">

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-10">
        <div class="bg-white border border-gray-200 rounded-xl p-4 text-center">
            <div class="text-2xl md:text-3xl font-extrabold text-indigo-600">{{ $stats['posts'] }}</div>
            <div class="text-xs text-gray-500 mt-1">{{ __('Articles') }}</div>
        </div>
        @if ($stats['total_views'] > 0)
            <div class="bg-white border border-gray-200 rounded-xl p-4 text-center">
                <div class="text-2xl md:text-3xl font-extrabold text-indigo-600">{{ number_format($stats['total_views']) }}</div>
                <div class="text-xs text-gray-500 mt-1">{{ __('Total views') }}</div>
            </div>
        @endif
        @if ($stats['first_post'])
            <div class="bg-white border border-gray-200 rounded-xl p-4 text-center">
                <div class="text-base md:text-lg font-bold text-gray-900">{{ $stats['first_post']->format('M Y') }}</div>
                <div class="text-xs text-gray-500 mt-1">{{ __('First article') }}</div>
            </div>
        @endif
        @if ($stats['latest_post'])
            <div class="bg-white border border-gray-200 rounded-xl p-4 text-center">
                <div class="text-base md:text-lg font-bold text-gray-900">{{ $stats['latest_post']->format('M Y') }}</div>
                <div class="text-xs text-gray-500 mt-1">{{ __('Latest article') }}</div>
            </div>
        @endif
    </div>

    {{-- Articles list --}}
    @if ($posts->isNotEmpty())
        <h2 class="text-2xl font-bold text-gray-900 mb-5 flex items-center gap-2">✍️ {{ __('Articles by :name', ['name' => $author->name]) }}</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach ($posts as $p)
                <a href="{{ route('blog.show', $p->slug) }}"
                   class="block bg-white border border-gray-200 rounded-xl p-5 hover:border-indigo-300 hover:shadow-md transition">
                    @if ($p->category)
                        <span class="inline-block text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full"
                              style="background:{{ $p->category->color ?? '#6366f1' }}1a; color:{{ $p->category->color ?? '#6366f1' }};">
                            {{ $p->category->name }}
                        </span>
                    @endif
                    <h3 class="text-base font-bold text-gray-900 mt-2 line-clamp-2 flex items-start gap-2 flex-wrap">
                        <span>{{ $p->title }}</span>
                        <x-new-badge :date="$p->published_at" />
                    </h3>
                    @if ($p->excerpt)
                        <p class="text-sm text-gray-600 mt-2 line-clamp-3 leading-relaxed">{{ $p->excerpt }}</p>
                    @endif
                    <div class="flex items-center gap-3 mt-3 text-xs text-gray-400">
                        @if ($p->published_at)
                            <span>📅 {{ $p->published_at->format('d.m.Y') }}</span>
                        @endif
                        <span>⏱️ {{ $p->reading_minutes }} {{ __('min') }}</span>
                    </div>
                </a>
            @endforeach
        </div>
    @else
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-5 text-center text-gray-700">
            <p class="text-sm">{{ __(':name has no published articles yet.', ['name' => $author->name]) }}</p>
        </div>
    @endif

    {{-- Etkinlikler (host olarak) --}}
    @if (! empty($events) && $events->isNotEmpty())
        <div class="mt-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-5 flex items-center gap-2">
                📅 {{ __('Events hosted by :name', ['name' => $author->name]) }}
                @if ($stats['upcoming_events'] > 0)
                    <span class="text-sm font-normal text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full">{{ __(':n upcoming', ['n' => $stats['upcoming_events']]) }}</span>
                @endif
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach ($events as $e)
                    @php
                        $title = match (app()->getLocale()) {
                            'de' => $e->title_de ?: $e->title_tr,
                            'en' => $e->title_en ?: $e->title_tr,
                            default => $e->title_tr ?: $e->title_de,
                        };
                        $isUpcoming = $e->starts_at && $e->starts_at->isFuture();
                    @endphp
                    <a href="{{ route('events.show', $e->slug) }}"
                       class="block bg-white border {{ $isUpcoming ? 'border-emerald-300' : 'border-gray-200' }} rounded-xl p-5 hover:shadow-md transition">
                        <div class="flex items-center gap-2 mb-2 flex-wrap">
                            @if ($isUpcoming)
                                <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full">{{ __('Upcoming') }}</span>
                            @else
                                <span class="text-[10px] font-bold uppercase tracking-wider text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full">{{ __('Past') }}</span>
                            @endif
                            <span class="text-[10px] font-semibold text-gray-600">{{ $e->mode === 'online' ? '💻 ' . __('Online') : '📍 ' . $e->location_city }}</span>
                        </div>
                        <h3 class="text-base font-bold text-gray-900 line-clamp-2">{{ $title }}</h3>
                        <p class="text-xs text-gray-500 mt-2">📅 {{ $e->starts_at?->translatedFormat('d M Y, H:i') }}</p>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    <div class="mt-10 text-center">
        <a href="{{ route('team') }}" class="inline-flex items-center gap-2 text-sm text-indigo-600 hover:underline">
            ← {{ __('Back to team') }}
        </a>
    </div>

</div>

@endsection
