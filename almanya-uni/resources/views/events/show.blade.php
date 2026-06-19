@extends('layouts.app')

@section('title', $event->title . ' — ' . __('Event') . ' — ' . brand('name'))

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
        'name'  => __($event->host),
    ] : null,
    'offers'      => [
        '@type'         => 'Offer',
        'price'         => (string) ($event->price_eur ?? 0),
        'priceCurrency' => 'EUR',
        'availability'  => 'https://schema.org/InStock',
        'validFrom'     => $event->created_at?->toIso8601String(),
        'url'           => $event->registration_url ?: url()->current(),
    ],
    'isAccessibleForFree' => ! is_null($event->price_eur) && $event->price_eur == 0,
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
                {{ $event->type_label }}
            </span>
            @if ($event->is_live)
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-red-500 text-white text-xs font-bold uppercase">
                    <span class="w-2 h-2 rounded-full bg-white animate-pulse"></span>{{ __('Live now') }}
                </span>
            @endif
            @if ($event->mode === 'offline')
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/15 text-xs"><x-svg-icon name="map-pin" class="w-3.5 h-3.5" /> {{ __('In person') }}</span>
            @elseif ($event->mode === 'hybrid')
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/15 text-xs"><x-svg-icon name="arrow-path" class="w-3.5 h-3.5" /> {{ __('Hybrid') }}</span>
            @else
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/15 text-xs"><x-svg-icon name="globe" class="w-3.5 h-3.5" /> {{ __('Online') }}</span>
            @endif
            @if ($event->language_flag && $event->source !== 'ticketmaster')
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/20 backdrop-blur text-xs font-bold ring-1 ring-white/30" title="{{ __('Event language') }}">
                    {{ $event->language_flag }} {{ $event->language_label }}
                </span>
            @endif
            @if ($event->isRecurring())
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-400/30 backdrop-blur text-xs font-bold ring-1 ring-amber-300/50" title="{{ __('This event repeats') }}">
                    <x-svg-icon name="arrow-path" class="w-3.5 h-3.5" /> {{ match($event->recurrence_rule) { 'weekly' => __('Weekly'), 'biweekly' => __('Every 2 weeks'), 'monthly' => __('Monthly'), default => __('Repeating') } }}
                </span>
            @elseif ($event->isSeriesChild())
                <a href="{{ $event->parentEvent ? route('events.show', $event->parentEvent->slug) : '#' }}"
                   class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-400/30 backdrop-blur text-xs font-bold ring-1 ring-amber-300/50 hover:bg-amber-400/50 transition" title="{{ __('Part of a series — click to see the original') }}">
                    <x-svg-icon name="calendar-days" class="w-3.5 h-3.5" /> {{ __('Part of series') }}
                </a>
            @endif
        </div>

        <h1 class="text-3xl md:text-5xl font-extrabold leading-tight drop-shadow mb-3">{{ $event->title }}</h1>
        @if ($event->host || $event->hostUser)
            <p class="text-lg opacity-90 mb-2 inline-flex items-center gap-1.5">
                <x-svg-icon name="user" class="w-4 h-4" />
                @if ($event->hostUser?->slug)
                    <a href="{{ route('author.show', $event->hostUser->slug) }}" class="hover:underline">{{ $event->hostUser->name ?? __($event->host) }}</a>
                    @if ($event->hostUser->role_label)
                        <span class="opacity-75 text-sm">· {{ __($event->hostUser->role_label) }}</span>
                    @endif
                @else
                    {{ __($event->host) }}
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

        {{-- RSVP — Meetup tarzı katılım kaydı --}}
        @if (! $event->starts_at->isPast())
            <section id="rsvp" class="bg-white border border-gray-200 rounded-xl p-6">
                <div class="flex items-center justify-between mb-4 flex-wrap gap-2">
                    <h2 class="text-xl md:text-2xl font-bold text-gray-900 flex items-center gap-2">
                        <x-svg-icon name="tag" class="w-6 h-6" /> {{ __('Are you joining?') }}
                    </h2>
                    @if ($event->goingRsvps->count() > 0)
                        <p class="text-sm text-gray-600">
                            <span class="font-bold text-emerald-700">{{ $event->goingRsvps->count() }}</span>
                            {{ __('attending') }}
                            @if ($event->maybe_count > 0)
                                · <span class="font-semibold text-amber-700">{{ $event->maybe_count }}</span> {{ __('maybe') }}
                            @endif
                        </p>
                    @endif
                </div>

                @if (session('rsvp_status'))
                    <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg p-3 mb-4 text-sm inline-flex items-center gap-2">
                        <x-svg-icon name="check-circle" class="w-4 h-4" />
                        {{ session('rsvp_status') }}
                    </div>
                @endif

                @if (isset($myRsvp) && $myRsvp)
                    <div class="bg-{{ $myRsvp->status === 'going' ? 'emerald' : ($myRsvp->status === 'maybe' ? 'amber' : 'gray') }}-50 border border-{{ $myRsvp->status === 'going' ? 'emerald' : ($myRsvp->status === 'maybe' ? 'amber' : 'gray') }}-200 rounded-lg p-3 mb-4">
                        <p class="text-sm text-gray-700">
                            {{ __('Your current RSVP:') }}
                            <strong class="inline-flex items-center gap-1">
                                @if ($myRsvp->status === 'going') <x-svg-icon name="check-circle" class="w-4 h-4" /> {{ __('Going') }}
                                @elseif ($myRsvp->status === 'maybe') <x-svg-icon name="information-circle" class="w-4 h-4" /> {{ __('Maybe') }}
                                @else <x-svg-icon name="x-circle" class="w-4 h-4" /> {{ __('Cancelled') }}
                                @endif
                            </strong>
                            <span class="text-xs text-gray-500">· {{ __('You can change it any time below.') }}</span>
                        </p>
                    </div>
                @endif

                <form method="POST" action="{{ route('events.rsvp', $event->slug) }}" class="space-y-3">
                    @csrf
                    <div class="grid grid-cols-3 gap-2">
                        <button type="submit" name="status" value="going"
                                class="px-3 py-3 rounded-lg border-2 border-emerald-300 hover:bg-emerald-50 hover:border-emerald-500 text-emerald-700 font-semibold transition text-sm inline-flex items-center justify-center gap-1.5">
                            <x-svg-icon name="check-circle" class="w-4 h-4" /> {{ __('Going') }}
                        </button>
                        <button type="submit" name="status" value="maybe"
                                class="px-3 py-3 rounded-lg border-2 border-amber-300 hover:bg-amber-50 hover:border-amber-500 text-amber-700 font-semibold transition text-sm inline-flex items-center justify-center gap-1.5">
                            <x-svg-icon name="information-circle" class="w-4 h-4" /> {{ __('Maybe') }}
                        </button>
                        <button type="submit" name="status" value="cancelled"
                                class="px-3 py-3 rounded-lg border-2 border-gray-200 hover:bg-gray-50 text-gray-600 font-semibold transition text-sm inline-flex items-center justify-center gap-1.5">
                            <x-svg-icon name="x-circle" class="w-4 h-4" /> {{ __('Can\'t make it') }}
                        </button>
                    </div>

                    @guest
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-2">
                            <input type="text" name="attendee_name" maxlength="80" required
                                   value="{{ old('attendee_name') }}"
                                   placeholder="{{ __('Your name') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:border-indigo-500 text-sm">
                            <input type="email" name="attendee_email" maxlength="150" required
                                   value="{{ old('attendee_email') }}"
                                   placeholder="{{ __('Email (for Zoom link)') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:border-indigo-500 text-sm">
                        </div>
                        <p class="text-[11px] text-gray-400">{{ __('Email is only used to send you the Zoom/event link before the start.') }}</p>
                    @endguest

                    <textarea name="note" maxlength="500" rows="2"
                              placeholder="{{ __('Optional note for the host (e.g. specific question)') }}"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:border-indigo-500 text-sm">{{ old('note') }}</textarea>

                    {{-- Honeypot --}}
                    <input type="text" name="website" tabindex="-1" autocomplete="off"
                           style="position:absolute;left:-9999px;opacity:0" aria-hidden="true">
                </form>
            </section>

            {{-- Reviews (only after event ends) --}}
        @endif

        @if ($event->starts_at->isPast())
            <section id="reviews" class="bg-white border border-gray-200 rounded-xl p-6">
                @php
                    $approvedReviews = $event->approvedReviews;
                    $reviewCount = $approvedReviews->count();
                    $avgRating = $reviewCount > 0 ? round($approvedReviews->avg('rating'), 1) : null;
                @endphp

                <div class="flex items-center justify-between mb-4 flex-wrap gap-2">
                    <h2 class="text-xl md:text-2xl font-bold text-gray-900 flex items-center gap-2">
                        ⭐ {{ __('Reviews') }}
                        @if ($reviewCount > 0)
                            <span class="text-base font-normal text-gray-500">({{ $reviewCount }})</span>
                        @endif
                    </h2>
                    @if ($avgRating)
                        <div class="text-sm text-gray-700">
                            <span class="text-lg text-amber-500">{{ str_repeat('★', round($avgRating)) }}{{ str_repeat('☆', 5 - round($avgRating)) }}</span>
                            <strong>{{ $avgRating }}</strong> / 5
                        </div>
                    @endif
                </div>

                @if (session('review_status'))
                    <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg p-3 mb-4 text-sm">
                        <span class="inline-flex items-center gap-2"><x-svg-icon name="check-circle" class="w-4 h-4" /> {{ session('review_status') }}</span>
                    </div>
                @endif

                {{-- Form --}}
                <form method="POST" action="{{ route('events.review', $event->slug) }}" class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-6 space-y-3">
                    @csrf
                    <div>
                        <p class="text-sm font-semibold text-gray-700 mb-2">{{ __('Your rating') }}:</p>
                        <div class="flex items-center gap-1" id="rating-stars">
                            @for ($i = 1; $i <= 5; $i++)
                                <label class="cursor-pointer text-3xl text-gray-300 hover:text-amber-400 transition" data-star="{{ $i }}">
                                    <input type="radio" name="rating" value="{{ $i }}" required class="sr-only">
                                    ★
                                </label>
                            @endfor
                        </div>
                    </div>

                    <textarea name="body" maxlength="1500" rows="3"
                              placeholder="{{ __('What did you think? (optional)') }}"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:border-indigo-500 text-sm"></textarea>

                    @guest
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            <input type="text" name="attendee_name" maxlength="80" required placeholder="{{ __('Your name') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:border-indigo-500 text-sm">
                            <input type="email" name="attendee_email" maxlength="150" required placeholder="{{ __('Email (not published)') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:border-indigo-500 text-sm">
                        </div>
                    @endguest

                    <input type="text" name="website" tabindex="-1" autocomplete="off" style="position:absolute;left:-9999px;opacity:0" aria-hidden="true">

                    <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white font-bold px-4 py-2 rounded-lg text-sm transition">
                        ⭐ {{ __('Submit review') }}
                    </button>
                </form>

                {{-- Reviews list --}}
                @if ($approvedReviews->isNotEmpty())
                    <div class="space-y-4">
                        @foreach ($approvedReviews as $r)
                            <article id="review-{{ $r->id }}" class="bg-white border border-gray-200 rounded-lg p-4">
                                <div class="flex items-start gap-3">
                                    @if ($r->display_avatar)
                                        <img src="{{ $r->display_avatar }}" alt="{{ $r->display_name }}" class="w-9 h-9 rounded-full object-cover flex-shrink-0">
                                    @else
                                        <div class="w-9 h-9 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-sm flex-shrink-0">
                                            {{ mb_substr($r->display_name, 0, 1) }}
                                        </div>
                                    @endif
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span class="font-semibold text-sm text-gray-900">{{ $r->display_name }}</span>
                                            <span class="text-amber-500 text-sm">{{ $r->stars }}</span>
                                            @if ($r->is_pinned)
                                                <span class="inline-flex items-center gap-1 text-[10px] uppercase font-bold tracking-wider text-amber-700 bg-amber-50 px-2 py-0.5 rounded"><x-svg-icon name="star" class="w-3 h-3" /> {{ __('Featured') }}</span>
                                            @endif
                                            <time class="text-xs text-gray-400 ml-auto" datetime="{{ $r->created_at->toIso8601String() }}">{{ $r->created_at->diffForHumans() }}</time>
                                        </div>
                                        @if ($r->body)
                                            <p class="text-sm text-gray-700 mt-1 whitespace-pre-line leading-relaxed">{{ $r->body }}</p>
                                        @endif
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @else
                    <p class="text-center text-sm text-gray-500 py-4">{{ __('No reviews yet — be the first.') }}</p>
                @endif
            </section>

            {{-- Schema.org AggregateRating + Review --}}
            @if ($reviewCount > 0)
                @push('head')
                <script type="application/ld+json">{!! json_encode([
                    '@context' => 'https://schema.org',
                    '@type'    => 'Event',
                    'name'     => $event->title,
                    'aggregateRating' => [
                        '@type'       => 'AggregateRating',
                        'ratingValue' => (string) $avgRating,
                        'reviewCount' => $reviewCount,
                        'bestRating'  => '5',
                        'worstRating' => '1',
                    ],
                    'review' => $approvedReviews->take(10)->map(fn ($r) => array_filter([
                        '@type'        => 'Review',
                        'reviewRating' => [
                            '@type'       => 'Rating',
                            'ratingValue' => (string) $r->rating,
                            'bestRating'  => '5',
                        ],
                        'author'       => ['@type' => 'Person', 'name' => $r->display_name],
                        'datePublished'=> $r->created_at->format('Y-m-d'),
                        'reviewBody'   => $r->body,
                    ]))->all(),
                ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
                @endpush
            @endif
        @endif

        @if (! $event->starts_at->isPast())
            {{-- Attendees list --}}
            @if ($event->goingRsvps->count() > 0)
                <section class="bg-white border border-gray-200 rounded-xl p-6">
                    <h2 class="text-base font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <x-svg-icon name="users" class="w-5 h-5" /> {{ __('Who\'s coming') }}
                        <span class="text-sm font-normal text-gray-500">({{ $event->goingRsvps->count() }})</span>
                    </h2>
                    <div class="flex flex-wrap gap-3">
                        @foreach ($event->goingRsvps->take(30) as $rsvp)
                            <div class="flex items-center gap-2 bg-gray-50 border border-gray-200 rounded-full pl-1 pr-3 py-1" title="{{ $rsvp->display_name }}">
                                @if ($rsvp->display_avatar)
                                    <img src="{{ $rsvp->display_avatar }}" alt="{{ $rsvp->display_name }}" class="w-7 h-7 rounded-full object-cover">
                                @else
                                    <div class="w-7 h-7 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-xs">
                                        {{ mb_substr($rsvp->display_name, 0, 1) }}
                                    </div>
                                @endif
                                <span class="text-xs font-medium text-gray-700">{{ \Illuminate\Support\Str::limit($rsvp->display_name, 20) }}</span>
                            </div>
                        @endforeach
                        @if ($event->goingRsvps->count() > 30)
                            <div class="inline-flex items-center px-3 py-1 rounded-full bg-indigo-50 text-indigo-700 text-xs font-semibold">
                                +{{ $event->goingRsvps->count() - 30 }} {{ __('more') }}
                            </div>
                        @endif
                    </div>
                </section>
            @endif
        @endif

        @push('scripts')
        <script>
        (function(){
            const labels = document.querySelectorAll('#rating-stars label');
            const inputs = document.querySelectorAll('#rating-stars input');
            function paint(n) {
                labels.forEach((l, i) => { l.style.color = i < n ? '#f59e0b' : '#d1d5db'; });
            }
            labels.forEach((l, i) => {
                l.addEventListener('mouseenter', () => paint(i + 1));
                l.addEventListener('mouseleave', () => {
                    const selected = document.querySelector('#rating-stars input:checked');
                    paint(selected ? parseInt(selected.value) : 0);
                });
                l.addEventListener('click', () => { inputs[i].checked = true; paint(i + 1); });
            });
        })();
        </script>
        @endpush
    </main>

    {{-- Sidebar --}}
    <aside class="space-y-4">
        <div class="bg-white border-2 rounded-xl p-6 sticky top-20 shadow-lg" style="border-color: {{ $event->type_color }};">
            <h3 class="text-sm font-bold uppercase tracking-wider text-gray-500 mb-4 inline-flex items-center gap-1.5"><x-svg-icon name="calendar" class="w-4 h-4" /> {{ __('Details') }}</h3>

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
                    @elseif (is_null($event->price_eur))
                        <p class="font-bold text-gray-500 text-sm">{{ __('See ticket page') }}</p>
                    @else
                        <p class="font-bold text-emerald-700 inline-flex items-center gap-1"><x-svg-icon name="tag" class="w-4 h-4" /> {{ __('Free') }}</p>
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
                @php $isTicketmaster = $event->source === 'ticketmaster'; @endphp
                <a href="{{ $event->is_live && $event->online_url ? $event->online_url : $event->registration_url }}"
                   target="_blank" rel="noopener nofollow{{ $isTicketmaster ? ' sponsored' : '' }}"
                   class="block mt-5 text-center py-3 rounded-lg text-white font-bold transition shadow-md hover:opacity-90"
                   style="background: {{ $event->type_color }};">
                    @if ($isTicketmaster)
                        {{ __('Buy Tickets') }} ↗
                    @else
                        {{ $event->is_live ? __('Join Now') : ($event->price_eur > 0 ? __('Register') : __('Register Free')) }}
                    @endif
                </a>
                @if ($isTicketmaster)
                    {{-- Ticketmaster ToS: kaynak attribution zorunlu --}}
                    <p class="text-[11px] text-center text-gray-400 mt-2">{{ __('Event data powered by Ticketmaster') }}</p>
                @endif
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
                       class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-emerald-500 hover:bg-emerald-600 text-white transition" title="WhatsApp"><x-svg-icon name="chat" class="w-4 h-4" /></a>
                    <a href="https://twitter.com/intent/tweet?url={{ $shareUrl }}&text={{ $shareText }}" target="_blank" rel="noopener"
                       aria-label="{{ __('Share on X / Twitter') }}"
                       class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-gray-900 hover:bg-black text-white transition font-bold" title="X (Twitter)">𝕏</a>
                    <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ $shareUrl }}" target="_blank" rel="noopener"
                       aria-label="{{ __('Share on LinkedIn') }}"
                       class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-blue-700 hover:bg-blue-800 text-white transition" title="LinkedIn">in</a>
                    <a href="https://t.me/share/url?url={{ $shareUrl }}&text={{ $shareText }}" target="_blank" rel="noopener"
                       aria-label="{{ __('Share on Telegram') }}"
                       class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-sky-500 hover:bg-sky-600 text-white transition" title="Telegram"><x-svg-icon name="plane" class="w-4 h-4" /></a>
                    <button type="button"
                            onclick="navigator.clipboard.writeText('{{ url()->current() }}').then(() => { this.dataset.copied='1'; setTimeout(() => { this.dataset.copied=''; }, 1500); })"
                            aria-label="{{ __('Copy link') }}"
                            class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-gray-200 hover:bg-gray-300 text-gray-700 transition" title="{{ __('Copy link') }}">
                        <x-svg-icon name="link" class="w-4 h-4" />
                    </button>
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
