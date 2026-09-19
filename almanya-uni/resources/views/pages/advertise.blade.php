@extends('layouts.app')

@section('title', __('Advertise with us') . ' — ' . brand('name'))

<x-seo
    :title="__('Advertise with us') . ' — ' . brand('name')"
    :description="__('Reach students planning their studies in Germany. Banner placements and sponsored partnerships in Turkish, German and English.')"
/>

@php
    $name = brand('name');
@endphp

@section('content')
<div class="max-w-4xl mx-auto px-4 py-10">

    {{-- Başlık --}}
    <header class="mb-10">
        <p class="text-xs uppercase tracking-wide text-primary-600 font-semibold mb-2">{{ __('Partnerships') }}</p>
        <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-4">{{ __('Advertise with us') }}</h1>
        <p class="text-lg text-gray-700 leading-relaxed">
            {{ __(':brand is read by people making a concrete decision: which university to apply to, how to finance it, and how to get a visa. That is a narrow audience with high intent — and it is reachable in three languages.', ['brand' => $name]) }}
        </p>
    </header>

    {{-- Kitle --}}
    <section class="mb-10">
        <h2 class="text-xl font-bold text-gray-900 mb-4">{{ __('Who reads this site') }}</h2>
        <div class="grid sm:grid-cols-3 gap-4">
            <div class="bg-white border border-gray-200 rounded-xl p-5">
                <div class="text-2xl font-extrabold text-gray-900">{{ number_format($stats['programs']) }}</div>
                <div class="text-sm text-gray-600 mt-1">{{ __('study programmes in the catalogue') }}</div>
            </div>
            <div class="bg-white border border-gray-200 rounded-xl p-5">
                <div class="text-2xl font-extrabold text-gray-900">{{ number_format($stats['universities']) }}</div>
                <div class="text-sm text-gray-600 mt-1">{{ __('universities covered') }}</div>
            </div>
            <div class="bg-white border border-gray-200 rounded-xl p-5">
                <div class="text-2xl font-extrabold text-gray-900">{{ number_format($stats['cities']) }}</div>
                <div class="text-sm text-gray-600 mt-1">{{ __('student cities') }}</div>
            </div>
        </div>
        <p class="text-sm text-gray-600 mt-4 leading-relaxed">
            {{ __('Typical readers: prospective and current international students, their families, and graduates moving into the German job market. The strongest topics are admissions, blocked accounts and insurance, visas and residence, housing, and life after arrival.') }}
        </p>
    </section>

    {{-- Diller --}}
    <section class="mb-10">
        <h2 class="text-xl font-bold text-gray-900 mb-4">{{ __('Three languages, three audiences') }}</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-sm border border-gray-200 rounded-lg overflow-hidden">
                <thead class="bg-gray-50 text-gray-700">
                    <tr>
                        <th class="text-left font-semibold px-4 py-3">{{ __('Language') }}</th>
                        <th class="text-left font-semibold px-4 py-3">{{ __('Audience') }}</th>
                        <th class="text-left font-semibold px-4 py-3">{{ __('Published articles') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr>
                        <td class="px-4 py-3 font-semibold">{{ __('Turkish') }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ __('Applicants from Türkiye and Turkish-speaking families') }}</td>
                        <td class="px-4 py-3">{{ number_format($stats['posts_tr']) }}</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 font-semibold">{{ __('English') }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ __('International applicants comparing countries and programmes') }}</td>
                        <td class="px-4 py-3">{{ number_format($stats['posts_en']) }}</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 font-semibold">{{ __('German') }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ __('Readers already in Germany: residence, work and everyday questions') }}</td>
                        <td class="px-4 py-3">{{ number_format($stats['posts_de']) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <p class="text-sm text-gray-600 mt-3">
            {{ __('Campaigns can be booked for a single language or across all three. Creatives are served in the reader\'s own language, so one campaign does not appear in the wrong language.') }}
        </p>
    </section>

    {{-- Yerleşimler --}}
    <section class="mb-10">
        <h2 class="text-xl font-bold text-gray-900 mb-4">{{ __('Placements') }}</h2>
        <div class="grid sm:grid-cols-2 gap-4">
            @foreach ($placements as $key => $p)
                <div class="border border-gray-200 rounded-xl p-5 bg-white">
                    <div class="font-semibold text-gray-900">{{ __($p['label']) }}</div>
                    <div class="text-sm text-gray-500 mt-1">{{ $p['size'] }}</div>
                    <code class="text-[11px] text-gray-400">{{ $key }}</code>
                </div>
            @endforeach
        </div>
        <p class="text-sm text-gray-600 mt-4 leading-relaxed">
            {{ __('Beyond banners we also run content partnerships: a clearly labelled sponsor card inside a relevant guide, linked to the topic the reader is already on.') }}
        </p>
    </section>

    {{-- Kurallar --}}
    <section class="mb-10">
        <h2 class="text-xl font-bold text-gray-900 mb-4">{{ __('What we do and do not accept') }}</h2>
        <div class="grid sm:grid-cols-2 gap-4">
            <div class="border border-green-200 bg-green-50/60 rounded-xl p-5">
                <div class="font-semibold text-gray-900 mb-2">{{ __('We accept') }}</div>
                <ul class="text-sm text-gray-700 space-y-1.5 list-disc list-inside">
                    <li>{{ __('Blocked accounts, insurance, banking and money transfer') }}</li>
                    <li>{{ __('Language schools, exam preparation, accommodation platforms') }}</li>
                    <li>{{ __('Universities and recognised education providers') }}</li>
                    <li>{{ __('Career, relocation and legal services') }}</li>
                </ul>
            </div>
            <div class="border border-red-200 bg-red-50/60 rounded-xl p-5">
                <div class="font-semibold text-gray-900 mb-2">{{ __('We do not accept') }}</div>
                <ul class="text-sm text-gray-700 space-y-1.5 list-disc list-inside">
                    <li>{{ __('Guaranteed visa or guaranteed admission claims') }}</li>
                    <li>{{ __('Services that misrepresent official procedures or fees') }}</li>
                    <li>{{ __('Ads disguised as editorial content') }}</li>
                    <li>{{ __('Gambling, and anything unsuitable for a student audience') }}</li>
                </ul>
            </div>
        </div>
        <p class="text-sm text-gray-600 mt-4">
            {{ __('Every paid placement is labelled. Editorial content is never sold: a partnership cannot change what a guide recommends.') }}
        </p>
    </section>

    {{-- İletişim --}}
    <section class="bg-primary-50 border border-primary-100 rounded-xl p-6 md:p-8">
        <h2 class="text-xl font-bold text-gray-900 mb-2">{{ __('Request the media kit') }}</h2>
        <p class="text-gray-700 mb-4 leading-relaxed">
            {{ __('Tell us the language, the placement and the period you have in mind, and we will send current traffic figures and pricing.') }}
        </p>
        <div class="flex flex-wrap gap-3">
            <a href="mailto:{{ $contact }}?subject={{ rawurlencode(__('Advertising enquiry')) }}"
               class="inline-flex items-center gap-2 bg-primary-600 hover:bg-primary-700 text-white font-bold px-6 py-3 rounded-lg transition">
                {{ $contact }}
            </a>
            <a href="{{ route('contact') }}"
               class="inline-flex items-center gap-2 bg-white hover:bg-gray-50 text-gray-800 font-semibold px-6 py-3 rounded-lg border border-gray-200 transition">
                {{ __('Contact form') }}
            </a>
        </div>
        <p class="text-xs text-gray-500 mt-4">
            {{ __('Traffic figures are shared on request rather than published here, so that what you receive is current rather than a number frozen on a page.') }}
        </p>
    </section>

</div>
@endsection
