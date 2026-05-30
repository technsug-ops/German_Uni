@extends('layouts.app')

@section('title', __('Housing — Germany Student Accommodation Guide') . '  — ' . brand('name'))

<x-seo
    :title="__('How to Find Housing as a Student in Germany?')"
    :description="__('Studierendenwerk dorms, WG search, Wohnungsanfrage templates and real experience tips. For international students looking for housing in Germany.')"
/>

@section('content')

{{-- HERO --}}
<section class="bg-gradient-to-br from-primary-700 via-primary-600 to-primary-800 text-white">
    <div class="max-w-[1400px] mx-auto px-4 py-12">
        <span class="inline-flex items-center gap-1.5 bg-accent-500 text-xs font-bold uppercase tracking-wider px-3 py-1 rounded-full mb-4">
            <x-svg-icon name="home" class="w-3.5 h-3.5" />
            {{ __('New') }}
        </span>
        <h1 class="text-3xl md:text-5xl font-extrabold leading-tight mb-4">{{ __('How to Find Housing in Germany?') }}</h1>
        <p class="text-lg text-primary-100 max-w-3xl">
            {!! __('Finding housing in Germany is complex but manageable. Step-by-step guide for <strong>Studierendenwerk dorms</strong>, <strong>WG (shared rooms)</strong> and private apartments, ready-to-use <strong>German email templates</strong>, and <strong>real experiences</strong> from international students.') !!}
        </p>
    </div>
</section>

{{-- Stats --}}
<section class="bg-white border-b border-gray-200">
    <div class="max-w-[1400px] mx-auto px-4 py-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
            <div><p class="text-3xl font-extrabold text-primary-700">{{ $stats['cities'] }}</p><p class="text-xs text-gray-600 mt-1">{{ __('Cities') }}</p></div>
            <div><p class="text-3xl font-extrabold text-primary-700">{{ $stats['dorms'] }}</p><p class="text-xs text-gray-600 mt-1">{{ __('Official dorm guides') }}</p></div>
            <div><p class="text-3xl font-extrabold text-accent-600">{{ $stats['templates'] }}</p><p class="text-xs text-gray-600 mt-1">{{ __('Email templates') }}</p></div>
            <div><p class="text-3xl font-extrabold text-primary-700">{{ $stats['tips'] }}</p><p class="text-xs text-gray-600 mt-1">{{ __('Community tips') }}</p></div>
        </div>
    </div>
</section>

@if (session('status'))
    <div class="max-w-[1400px] mx-auto px-4 pt-6">
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-green-800 inline-flex items-center gap-2">
            <x-svg-icon name="check-circle" class="w-5 h-5" />
            {{ session('status') }}
        </div>
    </div>
@endif

{{-- 3 ana yol --}}
<section class="max-w-[1400px] mx-auto px-4 py-12">
    <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">{{ __('3 Main Ways to Find Housing') }}</h2>
    <p class="text-gray-600 mb-6">{{ __('Which one fits your situation best?') }}</p>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <div class="bg-white border border-gray-200 rounded-xl p-6">
            <div class="text-primary-600 mb-3"><x-svg-icon name="building-office" class="w-8 h-8" /></div>
            <h3 class="font-bold text-gray-900 mb-2">{{ __('Studierendenwerk Dorms') }}</h3>
            <p class="text-sm text-gray-600 mb-3">{{ __('Public student dorms. Cheap, furnished, but long waitlists.') }}</p>
            <ul class="text-xs text-gray-500 space-y-1 mb-4">
                <li class="inline-flex items-center gap-1"><x-svg-icon name="check" class="w-3.5 h-3.5 text-emerald-600" /> {{ __('Rent between 180-550 €/month') }}</li>
                <li class="inline-flex items-center gap-1"><x-svg-icon name="check" class="w-3.5 h-3.5 text-emerald-600" /> {{ __('Furnished + internet included') }}</li>
                <li class="inline-flex items-center gap-1"><x-svg-icon name="x-mark" class="w-3.5 h-3.5 text-red-500" /> {{ __('2-18 month waitlist') }}</li>
                <li class="inline-flex items-center gap-1"><x-svg-icon name="x-mark" class="w-3.5 h-3.5 text-red-500" /> {{ __('Limited spots') }}</li>
            </ul>
            <a href="#dorms" class="text-sm font-semibold text-primary-600 hover:text-primary-800">{{ __('Dorms in 15 cities') }} →</a>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl p-6">
            <div class="text-primary-600 mb-3"><x-svg-icon name="users" class="w-8 h-8" /></div>
            <h3 class="font-bold text-gray-900 mb-2">{{ __('WG (Shared Room)') }}</h3>
            <p class="text-sm text-gray-600 mb-3">{{ __('Most common student housing type. Find listings on WG-Gesucht, apply, meet.') }}</p>
            <ul class="text-xs text-gray-500 space-y-1 mb-4">
                <li class="inline-flex items-center gap-1"><x-svg-icon name="check" class="w-3.5 h-3.5 text-emerald-600" /> {{ __('Fast (1-3 weeks)') }}</li>
                <li class="inline-flex items-center gap-1"><x-svg-icon name="check" class="w-3.5 h-3.5 text-emerald-600" /> 250-700 €/{{ __('month') }}</li>
                <li class="inline-flex items-center gap-1"><x-svg-icon name="check" class="w-3.5 h-3.5 text-emerald-600" /> {{ __('Social life') }}</li>
                <li class="inline-flex items-center gap-1"><x-svg-icon name="x-mark" class="w-3.5 h-3.5 text-red-500" /> {{ __('Casting process is tiring') }}</li>
            </ul>
            <a href="#templates" class="text-sm font-semibold text-primary-600 hover:text-primary-800">{{ __('WG email template') }} →</a>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl p-6">
            <div class="text-primary-600 mb-3"><x-svg-icon name="building-office" class="w-8 h-8" /></div>
            <h3 class="font-bold text-gray-900 mb-2">{{ __('Private Apartment / Studio') }}</h3>
            <p class="text-sm text-gray-600 mb-3">{{ __('ImmoScout, eBay Kleinanzeigen. Solo apartment if your budget allows.') }}</p>
            <ul class="text-xs text-gray-500 space-y-1 mb-4">
                <li class="inline-flex items-center gap-1"><x-svg-icon name="check" class="w-3.5 h-3.5 text-emerald-600" /> {{ __('Private space') }}</li>
                <li class="inline-flex items-center gap-1"><x-svg-icon name="x-mark" class="w-3.5 h-3.5 text-red-500" /> 600-1500 €/{{ __('month') }}</li>
                <li class="inline-flex items-center gap-1"><x-svg-icon name="x-mark" class="w-3.5 h-3.5 text-red-500" /> {{ __('SCHUFA + Sperrkonto + 3 payslips') }}</li>
                <li class="inline-flex items-center gap-1"><x-svg-icon name="x-mark" class="w-3.5 h-3.5 text-red-500" /> {{ __('Hard for foreigners') }}</li>
            </ul>
            <a href="#templates" class="text-sm font-semibold text-primary-600 hover:text-primary-800">{{ __('Wohnungsanfrage template') }} →</a>
        </div>
    </div>
</section>

{{-- Yurt Sağlayıcılar CTA --}}
<section class="bg-gradient-to-r from-emerald-700 to-teal-600 text-white">
    <div class="max-w-[1400px] mx-auto px-4 py-10">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-center">
            <div class="md:col-span-2">
                <h2 class="text-2xl md:text-3xl font-extrabold mb-2 inline-flex items-center gap-2"><x-svg-icon name="building-office" class="w-7 h-7" /> {{ __('29 Studierendenwerk + 8 Private Companies + 4 Portals') }}</h2>
                <p class="text-emerald-100">
                    {{ __('Student dorms in Germany = 2 main categories. Public (cheap, long wait) and private companies (fast, expensive). We have all prices, capacities, waiting times and direct contact links.') }}
                </p>
            </div>
            <div class="md:text-right">
                <a href="{{ route('housing.providers') }}"
                   class="inline-flex items-center gap-2 bg-white text-emerald-700 hover:bg-gray-100 px-6 py-3 rounded-lg font-bold shadow-lg transition">
                    <x-svg-icon name="search" class="w-5 h-5" />
                    {{ __('All Providers') }} →
                </a>
            </div>
        </div>
    </div>
</section>

{{-- Kira aralığı tablo + Bütçe cross-link --}}
@if (! empty($rentRanges) && $rentRanges->isNotEmpty())
<section class="bg-gray-50 border-y border-gray-200 py-12">
    <div class="max-w-[1400px] mx-auto px-4">
        <div class="flex items-end justify-between mb-6 flex-wrap gap-3">
            <div>
                <h2 class="text-2xl md:text-3xl font-bold text-gray-900 inline-flex items-center gap-2"><x-svg-icon name="currency-euro" class="w-7 h-7" /> {{ __('How much in which city?') }}</h2>
                <p class="text-gray-600 mt-1">{{ __('Current 2025 rent ranges (€/month) for the most popular student cities') }}</p>
            </div>
            <a href="{{ route('tools.budget-planner') }}"
               class="inline-flex items-center gap-1.5 bg-primary-600 hover:bg-primary-700 text-white font-semibold px-4 py-2 rounded-lg text-sm transition shadow-md whitespace-nowrap">
                <x-svg-icon name="arrow-trending-up" class="w-4 h-4" /> {{ __('Budget planner') }} →
            </a>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="text-left px-4 py-3 font-semibold text-gray-700">{{ __('City') }}</th>
                            <th class="text-right px-4 py-3 font-semibold text-gray-700"><span class="inline-flex items-center gap-1"><x-svg-icon name="users" class="w-3.5 h-3.5" /> WG</span></th>
                            <th class="text-right px-4 py-3 font-semibold text-gray-700"><span class="inline-flex items-center gap-1"><x-svg-icon name="building-office" class="w-3.5 h-3.5" /> {{ __('Studio') }}</span></th>
                            <th class="text-right px-4 py-3 font-semibold text-gray-700"><span class="inline-flex items-center gap-1"><x-svg-icon name="home" class="w-3.5 h-3.5" /> {{ __('Apartment') }}</span></th>
                            <th class="text-right px-4 py-3 font-semibold text-gray-700"><span class="inline-flex items-center gap-1"><x-svg-icon name="academic-cap" class="w-3.5 h-3.5" /> {{ __('Uni') }}</span></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($rentRanges as $r)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 py-3">
                                    <a href="{{ route('cities.show', $r['slug']) }}" class="font-semibold text-primary-700 hover:underline">{{ $r['name'] }}</a>
                                </td>
                                <td class="px-4 py-3 text-right tabular-nums">
                                    @if ($r['wg']) <strong class="text-gray-900">{{ $r['wg'] }}</strong> € @else <span class="text-gray-400">—</span> @endif
                                </td>
                                <td class="px-4 py-3 text-right tabular-nums">
                                    @if ($r['studio']) {{ $r['studio'] }} € @else <span class="text-gray-400">—</span> @endif
                                </td>
                                <td class="px-4 py-3 text-right tabular-nums">
                                    @if ($r['apartment']) {{ $r['apartment'] }} € @else <span class="text-gray-400">—</span> @endif
                                </td>
                                <td class="px-4 py-3 text-right tabular-nums text-gray-600">{{ $r['uni_count'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mt-6">
            <a href="{{ route('tools.cost-of-living') }}"
               class="block bg-white border border-gray-200 hover:border-primary-400 hover:shadow transition rounded-xl p-4 text-center">
                <div class="flex justify-center mb-1 text-primary-600"><x-svg-icon name="banknotes" class="w-6 h-6" /></div>
                <p class="font-semibold text-gray-900 text-sm">{{ __('Cost of living calculator') }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ __('Rent + food + transport + insurance') }}</p>
            </a>
            <a href="{{ route('tools.budget-planner') }}"
               class="block bg-white border border-gray-200 hover:border-primary-400 hover:shadow transition rounded-xl p-4 text-center">
                <div class="flex justify-center mb-1 text-primary-600"><x-svg-icon name="arrow-trending-up" class="w-6 h-6" /></div>
                <p class="font-semibold text-gray-900 text-sm">{{ __('Budget planner') }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ __('Income vs expense + savings target') }}</p>
            </a>
            <a href="{{ route('blog.show', 'sperrkonto-2025-tam-rehber-almanya-vizesi-icin-bloke-hesap') }}"
               class="block bg-white border border-gray-200 hover:border-primary-400 hover:shadow transition rounded-xl p-4 text-center">
                <div class="flex justify-center mb-1 text-primary-600"><x-svg-icon name="banknotes" class="w-6 h-6" /></div>
                <p class="font-semibold text-gray-900 text-sm">{{ __('Sperrkonto guide') }}</p>
                <p class="text-xs text-gray-500 mt-1">992€/{{ __('month') }} × 12 = 11.904€</p>
            </a>
        </div>
    </div>
</section>
@endif

{{-- Mail şablonları --}}
<section id="templates" class="bg-gradient-to-br from-accent-50 to-white border-y border-accent-100 py-12">
    <div class="max-w-[1400px] mx-auto px-4">
        <div class="flex items-end justify-between mb-6 flex-wrap gap-3">
            <div>
                <h2 class="text-2xl md:text-3xl font-bold text-gray-900 inline-flex items-center gap-2"><x-svg-icon name="envelope" class="w-7 h-7" /> {{ __('Ready-to-use German Email Templates') }}</h2>
                <p class="text-gray-600 mt-1">{{ __('Don\'t know German? Fill in these ready templates, copy-paste and send. Each one has a line-by-line explanation.') }}</p>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach ($templates as $t)
                <a href="{{ route('housing.template', $t->slug) }}"
                   class="block bg-white border border-gray-200 hover:border-accent-400 hover:shadow-md transition rounded-xl p-5">
                    <span class="inline-block text-xs font-bold uppercase tracking-wider text-accent-700 mb-2">
                        @switch($t->category)
                            @case('wg-anfrage') {{ __('WG Application') }} @break
                            @case('wohnungsanfrage') {{ __('Apartment Application') }} @break
                            @case('dorm-application') {{ __('Dorm Follow-up') }} @break
                            @case('besichtigung') {{ __('Viewing') }} @break
                            @default {{ $t->category }}
                        @endswitch
                    </span>
                    <h3 class="font-bold text-gray-900 mb-2">{{ $t->localized('title') }}</h3>
                    <p class="text-sm text-gray-600">{{ $t->localized('description') }}</p>
                    <span class="inline-block mt-3 text-sm font-semibold text-accent-600">{{ __('Open template') }} →</span>
                </a>
            @endforeach
        </div>
    </div>
</section>

{{-- Studierendenwerk Yurtları --}}
<section id="dorms" class="max-w-[1400px] mx-auto px-4 py-12">
    <div class="flex items-end justify-between mb-6 flex-wrap gap-3">
        <div>
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 inline-flex items-center gap-2"><x-svg-icon name="building-office" class="w-7 h-7" /> {{ __('Studierendenwerk Dorms') }}</h2>
            <p class="text-gray-600 mt-1">{{ __('Official student dorm guide for 15 major cities — application links and waiting times.') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @foreach ($dorms as $d)
            <div class="bg-white border border-gray-200 rounded-xl p-5">
                <div class="flex items-start justify-between gap-2 mb-2">
                    <div>
                        <h3 class="font-bold text-gray-900 leading-snug">{{ $d->city_name }}</h3>
                        <p class="text-xs text-gray-500">{{ $d->organization }}</p>
                    </div>
                    @if ($d->waitlist_avg)
                        <span class="inline-flex items-center gap-1 bg-yellow-100 text-yellow-800 text-xs font-semibold px-2 py-0.5 rounded-full whitespace-nowrap">
                            <x-svg-icon name="clock" class="w-3 h-3" />
                            {{ $d->waitlist_avg }}
                        </span>
                    @endif
                </div>

                @if ($d->rent_min_eur || $d->rent_max_eur)
                    <p class="text-sm text-gray-700 mb-2 inline-flex items-center gap-1">
                        <x-svg-icon name="currency-euro" class="w-4 h-4" /> <strong>{{ $d->rent_min_eur }}-{{ $d->rent_max_eur }} €</strong>/{{ __('month') }}
                    </p>
                @endif

                @if ($d->localized('notes'))
                    <p class="text-sm text-gray-600 line-clamp-3 mb-3">{{ $d->localized('notes') }}</p>
                @endif

                <div class="flex flex-wrap gap-2 text-xs">
                    <a href="{{ $d->website_url }}" target="_blank" rel="noopener"
                       class="inline-flex items-center gap-1 bg-primary-50 hover:bg-primary-100 text-primary-700 font-semibold px-3 py-1.5 rounded-md transition">
                        <x-svg-icon name="globe" class="w-3.5 h-3.5" /> {{ __('Official Site') }}
                    </a>
                    @if ($d->application_url)
                        <a href="{{ $d->application_url }}" target="_blank" rel="noopener"
                           class="inline-flex items-center gap-1 bg-accent-500 hover:bg-accent-600 text-white font-semibold px-3 py-1.5 rounded-md transition">
                            <x-svg-icon name="pencil" class="w-3.5 h-3.5" /> {{ __('Apply') }} →
                        </a>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</section>

{{-- Topluluk deneyimleri --}}
<section class="bg-primary-50 border-y border-primary-100 py-12">
    <div class="max-w-[1400px] mx-auto px-4">
        <div class="flex items-end justify-between mb-6 flex-wrap gap-3">
            <div>
                <h2 class="text-2xl md:text-3xl font-bold text-gray-900 inline-flex items-center gap-2"><x-svg-icon name="chat" class="w-7 h-7" /> {{ __('Community Experiences') }}</h2>
                <p class="text-gray-600 mt-1">{{ __('Real experiences from international students. Scam warnings, landlord conversations, neighborhood tips.') }}</p>
            </div>
            @auth
                <a href="{{ route('housing.tip-create') }}"
                   class="inline-flex items-center gap-2 bg-accent-500 hover:bg-accent-600 text-white font-bold px-5 py-2.5 rounded-lg transition">
                    <x-svg-icon name="pencil" class="w-4 h-4" />
                    {{ __('Share Your Experience') }}
                </a>
            @else
                <a href="{{ route('login') }}"
                   class="inline-block bg-accent-500 hover:bg-accent-600 text-white font-bold px-5 py-2.5 rounded-lg transition">
                    {{ __('Sign In & Share') }}
                </a>
            @endauth
        </div>

        @if ($tips->isEmpty())
            <div class="bg-white border border-gray-200 rounded-xl p-8 text-center">
                <p class="text-gray-600 mb-2">{{ __('No experiences shared yet.') }}</p>
                <p class="text-sm text-gray-500">{{ __('If you went through housing search in Germany, be the first to share! The next student won\'t face the same difficulties thanks to you.') }}</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($tips as $tip)
                    <article class="bg-white border border-gray-200 rounded-xl p-5">
                        <div class="flex items-start justify-between gap-2 mb-3">
                            <h3 class="font-bold text-gray-900 leading-snug">{{ $tip->title }}</h3>
                            <span class="inline-block text-xs font-semibold bg-primary-100 text-primary-700 px-2 py-0.5 rounded-full whitespace-nowrap">
                                @switch($tip->category)
                                    @case('wg') WG @break
                                    @case('private') {{ __('Private') }} @break
                                    @case('dorm') {{ __('Dorm') }} @break
                                    @case('scam-warning') {{ __('Scam') }} @break
                                    @case('landlord-talk') {{ __('Landlord') }} @break
                                    @default {{ $tip->category }}
                                @endswitch
                            </span>
                        </div>
                        <p class="text-sm text-gray-700 line-clamp-4 mb-3">{{ $tip->content }}</p>
                        <p class="text-xs text-gray-500">
                            @if ($tip->city_name) <span class="inline-flex items-center gap-1"><x-svg-icon name="map-pin" class="w-3.5 h-3.5" /> {{ $tip->city_name }}</span> · @endif
                            {{ $tip->user->name ?? __('Anonymous') }} · {{ $tip->created_at->diffForHumans() }}
                        </p>
                    </article>
                @endforeach
            </div>
            <div class="text-center mt-6">
                <a href="{{ route('housing.tips') }}" class="inline-block text-primary-600 hover:text-primary-800 font-semibold">{{ __('See all experiences') }} →</a>
            </div>
        @endif
    </div>
</section>

{{-- Ev arama altın kuralları --}}
<section class="max-w-[1400px] mx-auto px-4 py-12">
    <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-6 inline-flex items-center gap-2"><x-svg-icon name="exclamation-triangle" class="w-7 h-7" /> {{ __('Golden Rules for Housing Search in Germany') }}</h2>

    <div class="space-y-3">
        @php
            $housingRules = app()->getLocale() === 'tr' ? [
                ['exclamation-triangle', 'Asla peşin para yatırma!', 'Ev sahibinin "rezervasyon için 500€ yolla" demesi %99 dolandırıcılık. Sözleşme imzalanmadan, anahtarı görmeden, gerçek kişiyi (video bile yetmez) görmeden para gönderme. Klassik sahteci: "Ben İngiltere\'deyim, anahtarı Airbnb gibi vereceğim" der.'],
                ['calendar',             'Erken başla — minimum 4 ay önce', 'Üniversite kabul mektubun gelir gelmez yurt başvurusu yap. WG aramaya da semestir başlamadan 6-8 hafta önce başla. Son hafta bulmak imkansız.'],
                ['document-text',        'Belgeleri önceden hazırla', 'Sperrkonto onayı, üniversite kabul mektubu, pasaport kopyası, varsa Türkçe + Almanca CV. Vermieter bunları talep ettiğinde 1 saatte cevap verebilmelisin.'],
                ['chat',                 'Almanca mail at, hatalı bile olsa', 'Vermieter (ev sahibi) İngilizce başvuruyu genelde okumaz veya ikinci sıraya koyar. Mail şablonlarımızı kullan — hatasız Almanca çıkacak.'],
                ['play',                 'Video-call öner', 'Almanya\'da değilsen, Vermieter\'in "kim olduğunu nasıl bileyim" endişesi var. Video-call (Zoom/WhatsApp) önerirsen ciddi izlenim verirsin.'],
                ['home',                 'Şehir merkezi şart değil', 'Yurt veya kira merkezde 600€ ise, S-Bahn ile 20 dk uzakta 350€\'ya benzer kalite bulabilirsin. Almanya\'da toplu ulaşım iyi.'],
                ['check-circle',         'Anmeldung\'u 14 gün içinde yap', 'Eve taşınınca 14 gün içinde Bürgeramt\'a kaydını yaptırman ZORUNLU. Wohnungsgeberbestätigung Vermieter\'den almak şart. Bunu Anmeldung olmadan banka hesabı açamazsın, vize uzatamazsın.'],
            ] : [
                ['exclamation-triangle', 'Never pay upfront!', 'If a landlord says "send 500€ to reserve", it\'s 99% a scam. Don\'t send money before signing a contract, seeing the keys, and meeting the real person (a video isn\'t enough). Classic scammer line: "I\'m in the UK, I\'ll give you the keys Airbnb-style".'],
                ['calendar',             'Start early — minimum 4 months ahead', 'Apply for a dorm as soon as your acceptance letter arrives. Start the WG search 6-8 weeks before the semester begins. Finding something the last week is impossible.'],
                ['document-text',        'Prepare your documents in advance', 'Sperrkonto confirmation, university acceptance letter, passport copy, and your CV (in English and German if possible). You should be able to respond within an hour when the Vermieter requests them.'],
                ['chat',                 'Email in German, even if imperfect', 'Vermieter (landlord) usually ignores English applications or puts them at the bottom of the pile. Use our email templates — flawless German guaranteed.'],
                ['play',                 'Suggest a video call', 'If you\'re not in Germany, the Vermieter wonders "how do I know who you are?". Suggesting a video call (Zoom/WhatsApp) makes a serious impression.'],
                ['home',                 'The city center is not mandatory', 'If a dorm or rent in the center costs 600€, you can find similar quality 20 minutes away by S-Bahn for 350€. Public transport in Germany is great.'],
                ['check-circle',         'Do your Anmeldung within 14 days', 'After moving in, you MUST register at the Bürgeramt within 14 days. Getting the Wohnungsgeberbestätigung from the Vermieter is required. Without Anmeldung you can\'t open a bank account or extend your visa.'],
            ];
        @endphp
        @foreach ($housingRules as [$icon, $title, $text])
            <div class="bg-white border border-gray-200 rounded-xl p-5 flex items-start gap-4">
                <span class="text-primary-600 flex-shrink-0"><x-svg-icon name="{{ $icon }}" class="w-8 h-8" /></span>
                <div>
                    <h3 class="font-bold text-gray-900 mb-1">{{ $title }}</h3>
                    <p class="text-sm text-gray-700 leading-relaxed">{{ $text }}</p>
                </div>
            </div>
        @endforeach
    </div>
</section>

{{-- CTA --}}
<section class="bg-gradient-to-r from-primary-700 to-primary-800 text-white">
    <div class="max-w-[1400px] mx-auto px-4 py-12 text-center">
        <h2 class="text-2xl md:text-3xl font-bold mb-3">{{ __('Share your experience too!') }}</h2>
        <p class="text-primary-100 mb-6 max-w-2xl mx-auto">
            {{ __('A story, tip, or even a scam warning from your housing search in Germany is very valuable for the next student. Sign up, share your experience — everyone benefits.') }}
        </p>
        @auth
            <a href="{{ route('housing.tip-create') }}" class="inline-flex items-center gap-2 bg-accent-500 hover:bg-accent-600 text-white font-bold px-8 py-3.5 rounded-lg shadow-lg transition">
                <x-svg-icon name="pencil" class="w-5 h-5" />
                {{ __('Share My Experience') }}
            </a>
        @else
            <a href="{{ route('register') }}" class="inline-block bg-accent-500 hover:bg-accent-600 text-white font-bold px-8 py-3.5 rounded-lg shadow-lg transition">
                {{ __('Sign Up Free') }}
            </a>
        @endauth
    </div>
</section>

@endsection
