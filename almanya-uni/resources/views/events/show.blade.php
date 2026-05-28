@extends('layouts.app')

@section('title', $event->title . ' — ' . __('Event') . '  — ' . brand('name'))

<x-seo
    :title="$event->meta_title ?: $event->title"
    :description="$event->meta_description ?: \Illuminate\Support\Str::limit(strip_tags($event->description_md), 160)"
    :image="$event->banner_url"
/>

{{-- Schema.org Event — Google "Events on Google" rich snippet eligibility --}}
@push('head')
<script type="application/ld+json">{!! json_encode(array_filter([
    '@context'    => 'https://schema.org',
    '@type'       => $event->mode === 'online' ? 'OnlineEvent' : 'Event',
    'name'        => $event->title,
    'description' => \Illuminate\Support\Str::limit(strip_tags($event->description_md ?? ''), 500),
    'startDate'   => $event->starts_at?->toIso8601String(),
    'endDate'     => $event->ends_at?->toIso8601String(),
    'eventStatus' => 'https://schema.org/EventScheduled',
    'eventAttendanceMode' => match ($event->mode) {
        'online'  => 'https://schema.org/OnlineEventAttendanceMode',
        'hybrid'  => 'https://schema.org/MixedEventAttendanceMode',
        default   => 'https://schema.org/OfflineEventAttendanceMode',
    },
    'inLanguage'  => app()->getLocale(),
    'image'       => $event->banner_url ? [$event->banner_url] : null,
    'url'         => url()->current(),
    'location'    => $event->mode === 'online'
        ? ['@type' => 'VirtualLocation', 'url' => $event->online_url ?: url()->current()]
        : array_filter([
            '@type'   => 'Place',
            'name'    => $event->location_name,
            'address' => array_filter([
                '@type'           => 'PostalAddress',
                'addressLocality' => $event->location_city,
                'addressCountry'  => 'DE',
            ]),
        ]),
    'organizer'   => $event->host ? [
        '@type' => 'Person',
        'name'  => $event->host,
    ] : null,
    'offers'      => [
        '@type'         => 'Offer',
        'price'         => (string) ($event->price_eur ?? 0),
        'priceCurrency' => 'EUR',
        'availability'  => 'https://schema.org/InStock',
        'validFrom'     => $event->created_at?->toIso8601String(),
        'url'           => $event->registration_url ?: url()->current(),
    ],
    'isAccessibleForFree' => ($event->price_eur ?? 0) == 0,
]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
@endpush

@section('content')

{{-- HERO --}}
<section class="text-white" style="background: linear-gradient(135deg, {{ $event->type_color }}, {{ $event->type_color }}cc);">
    <div class="max-w-[1400px] mx-auto px-4 py-12 md:py-16">
        <nav class="text-sm opacity-80 mb-3">
            <a href="/" class="hover:text-white">{{ __('Home') }}</a>
            <span class="mx-2">›</span>
            <a href="{{ route('events.index') }}" class="hover:text-white">{{ __('Events') }}</a>
            <span class="mx-2">›</span>
            <span class="text-white">{{ $event->title }}</span>
        </nav>

        <div class="flex items-center gap-2 mb-3 flex-wrap">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/15 backdrop-blur ring-1 ring-white/25 text-sm">
                {{ $event->type_emoji }} {{ $event->type_label }}
            </span>
            @if ($event->is_live)
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-red-500 text-white text-xs font-bold uppercase">
                    <span class="w-2 h-2 rounded-full bg-white animate-pulse"></span>{{ __('Live now') }}
                </span>
            @endif
            @if ($event->mode === 'offline')
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/15 text-xs">📍 {{ __('In person') }}</span>
            @elseif ($event->mode === 'hybrid')
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/15 text-xs">🔄 {{ __('Hybrid') }}</span>
            @else
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/15 text-xs">💻 {{ __('Online') }}</span>
            @endif
        </div>

        <h1 class="text-3xl md:text-5xl font-extrabold leading-tight drop-shadow mb-3">{{ $event->title }}</h1>
        @if ($event->host || $event->hostUser)
            <p class="text-lg opacity-90 mb-2">
                👤
                @if ($event->hostUser?->slug)
                    <a href="{{ route('author.show', $event->hostUser->slug) }}" class="hover:underline">{{ $event->hostUser->name ?? $event->host }}</a>
                    @if ($event->hostUser->role_label)
                        <span class="opacity-75 text-sm">· {{ $event->hostUser->role_label }}</span>
                    @endif
                @else
                    {{ $event->host }}
                @endif
            </p>
        @endif
    </div>
</section>

<div class="max-w-[1400px] mx-auto px-4 py-10 grid grid-cols-1 lg:grid-cols-3 gap-8">
    {{-- Main content --}}
    <main class="lg:col-span-2 space-y-6">
        @if ($event->description_md)
            <section class="bg-white border border-gray-200 rounded-xl p-6 prose prose-sm max-w-none">
                {!! \Illuminate\Support\Str::markdown($event->description_md) !!}
            </section>
        @endif
    </main>

    {{-- Sidebar --}}
    <aside class="space-y-4">
        <div class="bg-white border-2 rounded-xl p-6 sticky top-20 shadow-lg" style="border-color: {{ $event->type_color }};">
            <h3 class="text-sm font-bold uppercase tracking-wider text-gray-500 mb-4">📅 {{ __('Details') }}</h3>

            <div class="space-y-3 text-sm">
                <div>
                    <p class="text-xs text-gray-500">{{ __('Starts') }}</p>
                    <p class="font-semibold text-gray-900">{{ $event->starts_at->translatedFormat('d M Y, l') }}</p>
                    <p class="text-sm text-gray-700">{{ $event->starts_at->format('H:i') }} ({{ $event->timezone }})</p>
                </div>
                @if ($event->ends_at)
                    <div>
                        <p class="text-xs text-gray-500">{{ __('Ends') }}</p>
                        <p class="text-sm text-gray-700">{{ $event->ends_at->format('d M, H:i') }}</p>
                    </div>
                @endif
                @if ($event->mode === 'offline' && $event->location_name)
                    <div class="pt-2 border-t border-gray-100">
                        <p class="text-xs text-gray-500">{{ __('Address') }}</p>
                        <p class="text-sm text-gray-900">{{ $event->location_name }}</p>
                        @if ($event->location_city)
                            <p class="text-xs text-gray-600">{{ $event->location_city }}</p>
                        @endif
                    </div>
                @endif
                <div class="pt-2 border-t border-gray-100">
                    <p class="text-xs text-gray-500">{{ __('Price') }}</p>
                    @if ($event->price_eur > 0)
                        <p class="font-bold text-amber-700">{{ number_format($event->price_eur, 0, ',', '.') }} €</p>
                    @else
                        <p class="font-bold text-emerald-700">🎟️ {{ __('Free') }}</p>
                    @endif
                </div>
                @if ($event->max_attendees)
                    <div class="pt-2 border-t border-gray-100">
                        <p class="text-xs text-gray-500">{{ __('Capacity') }}</p>
                        <p class="text-sm text-gray-700">{{ $event->registered_count }}/{{ $event->max_attendees }} {{ __('people') }}</p>
                        <div class="mt-1 w-full bg-gray-200 rounded-full h-1.5">
                            <div class="h-full rounded-full" style="background: {{ $event->type_color }}; width: {{ min(100, ($event->registered_count / $event->max_attendees) * 100) }}%"></div>
                        </div>
                    </div>
                @endif
            </div>

            @if ($event->registration_url || $event->online_url)
                <a href="{{ $event->is_live && $event->online_url ? $event->online_url : $event->registration_url }}"
                   target="_blank" rel="noopener"
                   class="block mt-5 text-center py-3 rounded-lg text-white font-bold transition shadow-md hover:opacity-90"
                   style="background: {{ $event->type_color }};">
                    {{ $event->is_live ? '🔴 ' . __('Join Now') : ($event->price_eur > 0 ? '💳 ' . __('Register') : '🎟️ ' . __('Register Free')) }}
                </a>
            @endif

            @if (! $event->is_past && $event->starts_at->isFuture())
                <p class="text-xs text-center text-gray-500 mt-3">
                    {!! __('Starts in <strong>:time</strong>', ['time' => $event->starts_at->diffForHumans(null, true)]) !!}
                </p>
            @endif

            {{-- Paylaş --}}
            @php
                $shareUrl = urlencode(url()->current());
                $shareText = urlencode($event->title . ' — ' . $event->starts_at->format('d M Y, H:i'));
            @endphp
            <div class="pt-4 mt-4 border-t border-gray-100">
                <p class="text-xs text-gray-500 mb-2 text-center">{{ __('Share this event') }}</p>
                <div class="flex items-center justify-center gap-2">
                    <a href="https://wa.me/?text={{ $shareText }}%20{{ $shareUrl }}" target="_blank" rel="noopener"
                       aria-label="{{ __('Share on WhatsApp') }}"
                       class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-emerald-500 hover:bg-emerald-600 text-white transition" title="WhatsApp">💬</a>
                    <a href="https://twitter.com/intent/tweet?url={{ $shareUrl }}&text={{ $shareText }}" target="_blank" rel="noopener"
                       aria-label="{{ __('Share on X / Twitter') }}"
                       class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-gray-900 hover:bg-black text-white transition font-bold" title="X (Twitter)">𝕏</a>
                    <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ $shareUrl }}" target="_blank" rel="noopener"
                       aria-label="{{ __('Share on LinkedIn') }}"
                       class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-blue-700 hover:bg-blue-800 text-white transition" title="LinkedIn">in</a>
                    <a href="https://t.me/share/url?url={{ $shareUrl }}&text={{ $shareText }}" target="_blank" rel="noopener"
                       aria-label="{{ __('Share on Telegram') }}"
                       class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-sky-500 hover:bg-sky-600 text-white transition" title="Telegram">✈️</a>
                    <button type="button"
                            onclick="navigator.clipboard.writeText('{{ url()->current() }}').then(() => { this.textContent='✓'; setTimeout(() => this.textContent='🔗', 1500); })"
                            aria-label="{{ __('Copy link') }}"
                            class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-gray-200 hover:bg-gray-300 text-gray-700 transition" title="{{ __('Copy link') }}">🔗</button>
                </div>
            </div>
        </div>

        @if ($related->isNotEmpty())
            <div class="bg-white border border-gray-200 rounded-xl p-5">
                <h3 class="text-sm font-bold uppercase tracking-wider text-gray-500 mb-3">{{ __('Related Events') }}</h3>
                <div class="space-y-2">
                    @foreach ($related as $r)
                        <a href="{{ route('events.show', $r->slug) }}" class="block text-sm text-gray-900 hover:text-indigo-700 leading-snug">
                            <p class="font-semibold">{{ $r->title }}</p>
                            <p class="text-xs text-gray-500">{{ $r->starts_at->translatedFormat('d M, H:i') }}</p>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </aside>
</div>
@endsection
