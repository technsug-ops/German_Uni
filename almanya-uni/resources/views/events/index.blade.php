@extends('layouts.app')

@section('title', __('Events — Webinars, Workshops, Meetups') . ' — ' . brand('name'))

<x-seo
    :title="__('Germany University Events')"
    :description="__('AlmanyaUni live streams, webinars, workshops and meetups. Online + offline events for international students.')"
/>

@section('content')

{{-- HERO --}}
<section class="bg-gradient-to-br from-indigo-700 via-indigo-600 to-purple-600 text-white">
    <div class="max-w-[1400px] mx-auto px-4 py-12 md:py-16">
        <nav class="text-sm text-indigo-100 mb-3">
            <a href="/" class="hover:text-white">{{ __('Home') }}</a>
            <span class="mx-2 opacity-60">›</span>
            <span class="text-white">{{ __('Events') }}</span>
        </nav>
        <h1 class="text-3xl md:text-5xl font-extrabold leading-tight drop-shadow mb-3 flex items-center gap-3"><x-svg-icon name="calendar" class="w-9 h-9 md:w-11 md:h-11" /> {{ __('Events') }}</h1>
        <p class="text-lg md:text-xl text-indigo-100 max-w-3xl mb-5">
            {{ __('Live webinars, workshops, university open days, panels and student meetups. All free (unless otherwise noted).') }}
        </p>
        <div class="flex flex-wrap items-center gap-3 text-sm">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/15 backdrop-blur ring-1 ring-white/25"><x-svg-icon name="tag" class="w-3.5 h-3.5" /> {{ __('Free by default') }}</span>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/15 backdrop-blur ring-1 ring-white/25"><x-svg-icon name="pencil" class="w-3.5 h-3.5" /> {{ __('No registration required') }}</span>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/15 backdrop-blur ring-1 ring-white/25"><x-svg-icon name="play" class="w-3.5 h-3.5" /> {{ __('Recording later') }}</span>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/15 backdrop-blur ring-1 ring-white/25"><x-svg-icon name="chat-bubble" class="w-3.5 h-3.5" /> {{ __('Live Q&A') }}</span>
        </div>
    </div>
</section>

{{-- Category + Type filter chips --}}
<section class="bg-white border-b border-gray-200 sticky top-0 z-10">
    <div class="max-w-[1400px] mx-auto px-4 py-3 space-y-2">
        {{-- Kategoriler (7) --}}
        <div class="flex items-center flex-wrap gap-2">
            <span class="text-xs text-gray-500 mr-1">{{ __('Category:') }}</span>
            <a href="{{ route('events.index') }}"
               class="text-xs px-3 py-1.5 rounded-full border transition
                      {{ ! $category && ! $type ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-gray-50 text-gray-600 border-gray-200 hover:bg-gray-100' }}">
                {{ __('All') }}
            </a>
            @foreach ($categories as $cat)
                <a href="{{ route('events.index', ['category' => $cat->slug]) }}"
                   class="inline-flex items-center gap-1.5 text-xs px-3 py-1.5 rounded-full border transition
                          {{ $category === $cat->slug ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-gray-50 text-gray-700 border-gray-200 hover:bg-gray-100' }}">
                    {!! e_icon($cat->icon, 'w-3.5 h-3.5') !!} {{ $cat->name }}
                </a>
            @endforeach
        </div>

        {{-- Aktif kategorideki tipler --}}
        @if ($category)
            @php
                $typesInCat = collect(\App\Models\Event::TYPES)
                    ->filter(fn ($m) => ($m['category'] ?? null) === $category);
            @endphp
            @if ($typesInCat->isNotEmpty())
                <div class="flex items-center flex-wrap gap-2 pt-1 border-t border-gray-100">
                    <span class="text-xs text-gray-400 mr-1">{{ __('Type:') }}</span>
                    @foreach ($typesInCat as $key => $meta)
                        <a href="{{ route('events.index', ['type' => $key]) }}"
                           class="inline-flex items-center gap-1 text-[11px] px-2.5 py-1 rounded-full border transition
                                  {{ $type === $key ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-indigo-50 text-indigo-700 border-indigo-200 hover:bg-indigo-100' }}">
                            {!! e_icon($meta['emoji'] ?? '', 'w-3 h-3') !!} {{ $meta['label'] }}
                        </a>
                    @endforeach
                </div>
            @endif
        @endif
    </div>
</section>

<div class="max-w-[1400px] mx-auto px-4 py-10">

    {{-- Dış konser & kültür takvimi — çapraz link --}}
    @if (($concertCount ?? 0) > 0)
        <a href="{{ route('events.concerts') }}"
           class="group flex items-center justify-between gap-4 mb-8 rounded-xl border-2 border-rose-200 bg-gradient-to-r from-rose-50 to-pink-50 px-5 py-4 hover:border-rose-400 transition">
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center justify-center w-11 h-11 rounded-lg bg-rose-600 text-white text-xl">🎵</span>
                <div>
                    <h2 class="font-bold text-gray-900 text-base md:text-lg">{{ __('Germany Concerts & Culture Calendar') }}</h2>
                    <p class="text-xs md:text-sm text-gray-600">{{ __('Concerts, theatre, comedy & festivals across Germany — browse by city. (:count events)', ['count' => $concertCount]) }}</p>
                </div>
            </div>
            <span class="shrink-0 inline-flex items-center gap-1.5 text-sm font-semibold text-rose-700 group-hover:translate-x-0.5 transition">
                {{ __('Browse') }} <x-svg-icon name="arrow-right" class="w-4 h-4" />
            </span>
        </a>
    @endif

    {{-- LIVE NOW --}}
    @if ($live->isNotEmpty())
        <section class="mb-10">
            <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-red-500 text-white text-xs font-bold uppercase tracking-wider">
                    <span class="w-2 h-2 rounded-full bg-white animate-pulse"></span>
                    {{ __('Live') }}
                </span>
                {{ __('On air now') }}
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach ($live as $e)
                    @include('events._card', ['event' => $e, 'isLive' => true])
                @endforeach
            </div>
        </section>
    @endif

    @php $highlights = $upcoming->take(6); $restUpcoming = $upcoming->slice(6); @endphp

    {{-- 🔥 ÖNE ÇIKANLAR --}}
    @if ($highlights->isNotEmpty())
        <section class="mb-10">
            <h2 class="text-2xl font-bold text-gray-900 mb-4 inline-flex items-center gap-2">🔥 {{ __('Highlights') }}</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($highlights as $e)
                    <a href="{{ route('events.show', $e->slug) }}"
                       class="group block rounded-2xl overflow-hidden border border-gray-200 bg-white hover:shadow-lg transition">
                        <div class="relative p-5 h-32 flex flex-col justify-between text-white"
                             style="background: linear-gradient(135deg, {{ $e->type_color }}, rgba(0,0,0,.5));">
                            <div class="flex items-center justify-between gap-2">
                                <span class="inline-flex items-center gap-1 text-[11px] font-bold uppercase tracking-wide bg-white/20 backdrop-blur px-2 py-0.5 rounded-full">{!! e_icon($e->type_emoji, 'w-3 h-3') !!} {{ $e->type_label }}</span>
                                <span class="text-[11px] font-semibold bg-black/25 px-2 py-0.5 rounded-full whitespace-nowrap">{{ $e->starts_at->translatedFormat('d M · H:i') }}</span>
                            </div>
                            <div class="font-extrabold text-lg leading-tight line-clamp-2 drop-shadow">{{ $e->title }}</div>
                        </div>
                        <div class="px-4 py-3 flex items-center justify-between text-sm">
                            <span class="inline-flex items-center gap-1 text-gray-600 truncate">
                                @if ($e->mode === 'online')
                                    <x-svg-icon name="play" class="w-4 h-4 text-indigo-500" /> {{ __('Online') }}
                                @else
                                    <x-svg-icon name="map-pin" class="w-4 h-4 text-indigo-500" /> {{ $e->location_city ?: __('In person') }}
                                @endif
                            </span>
                            <span class="font-semibold text-indigo-600 inline-flex items-center gap-1 group-hover:translate-x-0.5 transition">{{ __('Details') }} →</span>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- UPCOMING (öne çıkanlar dışında kalanlar) --}}
    @if ($upcoming->isEmpty())
        <section class="mb-10">
            <h2 class="text-2xl font-bold text-gray-900 mb-4 inline-flex items-center gap-2"><x-svg-icon name="arrow-right" class="w-6 h-6 text-indigo-600" /> {{ __('Upcoming events (:count)', ['count' => 0]) }}</h2>
            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-8 text-center">
                <p class="text-yellow-900">{{ __('No upcoming events at the moment. Subscribe to our newsletter to get announcements first.') }}</p>
            </div>
        </section>
    @elseif ($restUpcoming->isNotEmpty())
        <section class="mb-10">
            <h2 class="text-2xl font-bold text-gray-900 mb-4 inline-flex items-center gap-2"><x-svg-icon name="arrow-right" class="w-6 h-6 text-indigo-600" /> {{ __('Upcoming events (:count)', ['count' => $restUpcoming->count()]) }}</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($restUpcoming as $e)
                    @include('events._card', ['event' => $e, 'isLive' => false])
                @endforeach
            </div>
        </section>
    @endif

    {{-- PAST --}}
    @if ($past->isNotEmpty())
        <section class="mb-10">
            <h2 class="text-2xl font-bold text-gray-700 mb-4 inline-flex items-center gap-2"><x-svg-icon name="book-open" class="w-6 h-6" /> {{ __('Past events') }}</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 opacity-75">
                @foreach ($past as $e)
                    @include('events._card', ['event' => $e, 'isLive' => false, 'isPast' => true])
                @endforeach
            </div>
        </section>
    @endif

    {{-- Şehir etkinlik bildirimi aboneliği --}}
    <section class="mt-12 mb-10 max-w-2xl mx-auto">
        @include('events._alert-subscribe', ['alertCities' => $alertCities])
    </section>

    {{-- Event types — tanıtım kartları --}}
    <section class="mt-14 mb-10">
        <div class="text-center mb-8">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2 inline-flex items-center gap-2 justify-center"><x-svg-icon name="target" class="w-7 h-7 text-indigo-600" /> {{ __('What kind of events do we run?') }}</h2>
            <p class="text-sm text-gray-600 max-w-2xl mx-auto">{{ __('Six recurring formats — pick what fits your stage of the journey.') }}</p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @php
                $formats = [
                    ['icon' => 'chat-bubble', 'title' => __('Live webinar (60 min)'), 'desc' => __('30-min topic-deep dive + 30-min Q&A on Zoom. Most fundamentals (visa, Sperrkonto, Anabin) covered here.')],
                    ['icon' => 'wrench-screwdriver', 'title' => __('Hands-on workshop (90 min)'), 'desc' => __('Bring your own laptop: live uni-assist application, Sperrkonto setup, real Bürgeramt appointment search.')],
                    ['icon' => 'users', 'title' => __('Expert panel (75 min)'), 'desc' => __('3-4 alumni or experts on one theme (Werkstudent law, scholarship strategy, German bureaucracy).')],
                    ['icon' => 'sparkles', 'title' => __('Mentor matching night (60 min)'), 'desc' => __('Speed-dating-style: 6-min rotations with 5-6 mentors. Find your mentor in one evening.')],
                    ['icon' => 'building-office', 'title' => __('University open day (online)'), 'desc' => __('Direct calls with German uni admissions offices — TUM, LMU, Heidelberg, etc.')],
                    ['icon' => 'cake', 'title' => __('Student meetup (offline)'), 'desc' => __('In-person meetups in Berlin, Munich, Frankfurt, Hamburg. Free, casual, Turkish + English.')],
                ];
            @endphp
            @foreach ($formats as $f)
                <div class="bg-white border border-gray-200 rounded-xl p-5 hover:border-indigo-300 transition">
                    <div class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-indigo-50 text-indigo-600 mb-2"><x-svg-icon :name="$f['icon']" class="w-5 h-5" /></div>
                    <h3 class="font-bold text-gray-900 text-base mb-1">{{ $f['title'] }}</h3>
                    <p class="text-xs text-gray-600 leading-relaxed">{{ $f['desc'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Nasıl katılırım (3 adım) --}}
    <section class="mb-10 bg-gradient-to-br from-indigo-50 to-purple-50 border border-indigo-200 rounded-xl p-6 md:p-8">
        <div class="text-center mb-8">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2 inline-flex items-center gap-2 justify-center"><x-svg-icon name="list-bullet" class="w-7 h-7 text-indigo-600" /> {{ __('How attendance works') }}</h2>
            <p class="text-sm text-gray-600">{{ __('Three simple steps — no commitment, no spam.') }}</p>
        </div>
        <ol class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <li class="bg-white border border-gray-200 rounded-lg p-5">
                <div class="flex items-center gap-2 mb-2">
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-indigo-600 text-white font-bold text-sm">1</span>
                    <h3 class="font-bold text-gray-900">{{ __('RSVP if you want') }}</h3>
                </div>
                <p class="text-xs text-gray-600 leading-relaxed">{{ __('Click "Going" on the event page. Your name appears in the attendees grid. RSVP is optional — drop-ins also welcome.') }}</p>
            </li>
            <li class="bg-white border border-gray-200 rounded-lg p-5">
                <div class="flex items-center gap-2 mb-2">
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-indigo-600 text-white font-bold text-sm">2</span>
                    <h3 class="font-bold text-gray-900">{{ __('Get the Zoom link') }}</h3>
                </div>
                <p class="text-xs text-gray-600 leading-relaxed">{{ __('We post the link on the event page 1 hour before start. If you RSVPed with email, we send it directly too. No login walls.') }}</p>
            </li>
            <li class="bg-white border border-gray-200 rounded-lg p-5">
                <div class="flex items-center gap-2 mb-2">
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-indigo-600 text-white font-bold text-sm">3</span>
                    <h3 class="font-bold text-gray-900">{{ __('Watch live or recording') }}</h3>
                </div>
                <p class="text-xs text-gray-600 leading-relaxed">{{ __('Live for Q&A; otherwise the recording goes up within 48 hours on the same event page. Public + free forever.') }}</p>
            </li>
        </ol>
    </section>

    {{-- Speaker / öneri CTA --}}
    <section class="mb-10 bg-gradient-to-r from-amber-100 to-orange-100 border-2 border-amber-200 rounded-xl p-6 md:p-8 flex flex-col md:flex-row items-center justify-between gap-5">
        <div>
            <h2 class="text-xl md:text-2xl font-bold text-amber-900 mb-2 inline-flex items-center gap-2"><x-svg-icon name="chat-bubble" class="w-6 h-6 text-amber-700" /> {{ __('Want to speak at an AlmanyaUni event?') }}</h2>
            <p class="text-sm text-amber-800 max-w-2xl">{{ __('Are you an alum, professional, or topic expert? We run 2-3 events a month — apply to host one. Your topic, our audience, free promotion.') }}</p>
        </div>
        <a href="{{ route('contact', ['type' => 'partnership', 'subject' => 'Speaker Application']) }}"
           class="shrink-0 inline-flex items-center gap-2 bg-amber-600 hover:bg-amber-700 text-white font-bold px-6 py-3 rounded-lg shadow-md transition whitespace-nowrap">
            <x-svg-icon name="pencil" class="w-4 h-4" /> {{ __('Apply as speaker') }}
        </a>
    </section>

    {{-- SSS --}}
    <x-faq-section
        :title="__('Frequently Asked Questions about Events')"
        :subtitle="__('Format, RSVP, recordings, and language')"
        :faqs="[
            ['q' => __('Are events really free?'), 'a' => __('Yes — every AlmanyaUni event is free by default. If a specific event has a fee (rare; usually for a hands-on workshop with materials cost), it is clearly shown with a €X badge on the event card and in the sticky sidebar.')],
            ['q' => __('Do I have to register?'), 'a' => __('No. You can RSVP if you want your spot counted, or if you want the Zoom link delivered via email. But drop-ins (just click the live Zoom link on the event page) are equally welcome.')],
            ['q' => __('What language are events in?'), 'a' => __('Each event page shows a flag chip with the presentation language: 🇹🇷 Türkçe, 🇬🇧 English, 🇩🇪 Deutsch, or 🌍 multi (Turkish + German). Most events are in Turkish; international panels are in English.')],
            ['q' => __('Will there be a recording?'), 'a' => __('Yes for most webinars and panels. The recording goes up on the same event page within 48 hours. Workshops with live exercises are not recorded (interaction matters).')],
            ['q' => __('Can I propose a topic?'), 'a' => __('Absolutely — that is how half our events get planned. Email technsug@gmail.com with your topic, your background, and a sentence on why this audience needs it.')],
        ]"
    />
</div>
@endsection
