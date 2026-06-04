@extends('layouts.app')

@section('title', __('German Language Certificates for University — TestDaF, DSH, telc, Goethe') . ' — ' . brand('name'))

<x-seo
    :title="__('German Language Certificates for University')"
    :description="__('TestDaF vs DSH vs telc C1 Hochschule vs Goethe C2: which German certificate do you need for university admission? Levels, cost, where to take it — compared and verified.')"
/>

<x-json-ld :data="\App\Support\Seo::breadcrumbs([
    ['name' => __('Home'), 'url' => route('home')],
    ['name' => __('Tools'), 'url' => route('tools.index')],
    ['name' => __('Language Certificates'), 'url' => route('tools.language-certificates')],
])" />

@php
    // Doğrulanmış (2026-06-04): Auswärtiges Amt-akredite + üniversite admission sayfaları.
    $rows = [
        ['name' => 'TestDaF', 'sub' => 'TDN 4', 'level' => '≈ C1', 'where' => __('Worldwide'),      'cost' => '~€195',    'best' => __('Best when applying from abroad'), 'star' => true],
        ['name' => 'DSH',     'sub' => 'DSH-2', 'level' => '≈ C1', 'where' => __('Germany only'),    'cost' => '€100–150', 'best' => __('Only if already in Germany'),     'star' => false],
        ['name' => 'telc Deutsch C1 Hochschule', 'sub' => 'C1', 'level' => 'C1', 'where' => __('Worldwide'), 'cost' => __('Varies'), 'best' => __('Standardized, widely accepted'), 'star' => true],
        ['name' => 'Goethe-Zertifikat C2 (GDS)', 'sub' => 'C2', 'level' => 'C2', 'where' => __('Worldwide'), 'cost' => __('Varies'), 'best' => __('Exempts you from TestDaF/DSH'), 'star' => false],
    ];
@endphp

@section('content')
{{-- HERO --}}
<section class="bg-gradient-to-br from-violet-700 via-purple-600 to-fuchsia-500 text-white">
    <div class="max-w-[1400px] mx-auto px-4 py-10 md:py-14">
        <nav class="text-sm text-violet-100 mb-3">
            <a href="/" class="hover:text-white">{{ __('Home') }}</a>
            <span class="mx-2 opacity-50">›</span>
            <a href="{{ route('tools.index') }}" class="hover:text-white">{{ __('Tools') }}</a>
            <span class="mx-2 opacity-50">›</span>
            <span class="text-white">{{ __('Language Certificates') }}</span>
        </nav>
        <h1 class="text-3xl md:text-5xl font-extrabold leading-tight drop-shadow mb-3 inline-flex items-center gap-3">
            <x-svg-icon name="academic-cap" class="w-8 h-8 md:w-10 md:h-10" />
            {{ __('German Language Certificates for University') }}
        </h1>
        <p class="text-lg md:text-xl text-violet-100 max-w-3xl">
            {{ __('Most German-taught programs require C1 German. Five certificates are accepted for university admission — here is how they compare and which one fits you.') }}
        </p>
    </div>
</section>

<div class="max-w-[1100px] mx-auto px-4 py-10">

    {{-- Featured snippet (AIO hedefi) --}}
    <x-featured-snippet
        :question="__('Which German certificate do I need for university — TestDaF or DSH?')"
        :answer="__('For German-taught programs you usually need C1 German. TestDaF (TDN 4) and telc C1 Hochschule can be taken worldwide and are accepted at virtually all German universities — best if you apply from abroad. DSH (DSH-2) is taken only in Germany, at the university. Goethe-Zertifikat C2 also qualifies and exempts you from TestDaF/DSH.')"
    />

    {{-- KARŞILAŞTIRMA TABLOSU --}}
    <section class="mt-8 bg-white border border-gray-200 rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-left">
                    <tr>
                        <th class="px-4 py-3 font-semibold text-gray-700">{{ __('Certificate') }}</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">{{ __('Level') }}</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">{{ __('Where') }}</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">{{ __('Cost') }}</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">{{ __('Best for') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($rows as $r)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-bold text-gray-900">
                                {{ $r['name'] }}
                                @if ($r['star'])<span class="ml-1 text-xs font-medium text-violet-600">★</span>@endif
                                <span class="block text-xs font-normal text-gray-500">{{ $r['sub'] }}</span>
                            </td>
                            <td class="px-4 py-3 text-gray-700">{{ $r['level'] }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $r['where'] }}</td>
                            <td class="px-4 py-3 font-mono text-gray-700">{{ $r['cost'] }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $r['best'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-4 py-2 text-xs text-gray-500 border-t border-gray-100">
            ★ {{ __('Bookable worldwide — practical when applying from your home country.') }} · {{ __('DSD II (school diploma) is also accepted.') }}
        </div>
    </section>

    {{-- SKOR GEREKSİNİMLERİ --}}
    <section class="mt-8 bg-indigo-50 border border-indigo-100 rounded-xl p-6">
        <h2 class="text-xl font-bold text-indigo-900 mb-3 inline-flex items-center gap-2">
            <x-svg-icon name="check" class="w-5 h-5" /> {{ __('Score requirements') }}
        </h2>
        <ul class="text-indigo-800 text-sm space-y-2 leading-relaxed">
            <li>• <strong>{{ __('C1-taught programs (most)') }}:</strong> {{ __('TestDaF TDN 4 in all four parts, DSH-2, or telc C1 Hochschule.') }}</li>
            <li>• <strong>{{ __('B2-level programs') }}:</strong> {{ __('TestDaF TDN 3 or DSH-1 may be enough.') }}</li>
            <li>• <strong>{{ __('Competitive programs (medicine, law, some TU9)') }}:</strong> {{ __('may require TDN 5 / DSH-3.') }}</li>
        </ul>
        <x-source-note
            :sources="[
                ['name' => 'TestDaF', 'url' => 'https://www.testdaf.de/'],
                ['name' => 'Goethe-Institut', 'url' => 'https://www.goethe.de/'],
            ]"
            updated="2026-06-04"
            :note="__('Each program sets its own requirement — always confirm on the university\'s admissions page.')"
            class="!bg-white/60 !border-indigo-100"
        />
    </section>

    {{-- HANGİSİNİ SEÇMELİSİN --}}
    <section class="mt-8 bg-emerald-50 border border-emerald-100 rounded-xl p-6">
        <h2 class="text-xl font-bold text-emerald-900 mb-3 inline-flex items-center gap-2">
            <x-svg-icon name="light-bulb" class="w-5 h-5" /> {{ __('Which one should you take?') }}
        </h2>
        <ul class="text-emerald-800 text-sm space-y-2 leading-relaxed">
            <li>• {{ __('Applying from your home country? Choose TestDaF or telc C1 Hochschule — both are bookable worldwide.') }}</li>
            <li>• {{ __('DSH is cheaper but only available inside Germany (e.g. during a Studienkolleg or prep course).') }}</li>
            <li>• {{ __('Already have Goethe-Zertifikat C2? It is accepted and exempts you from a separate university exam.') }}</li>
            <li>• {{ __('Need to reach C1 first?') }} <a href="{{ route('language-courses.index') }}" class="underline">{{ __('Find a German course') }}</a> {{ __('(university, private or online).') }}</li>
        </ul>
    </section>

    {{-- Cross-link --}}
    <section class="mt-8 grid grid-cols-1 sm:grid-cols-3 gap-3">
        <a href="{{ route('language-courses.index') }}" class="block bg-white border border-gray-200 rounded-xl p-4 hover:border-violet-400 hover:shadow-sm transition">
            <p class="mb-1 text-violet-600"><x-svg-icon name="academic-cap" class="w-6 h-6" /></p>
            <p class="font-bold text-gray-900">{{ __('Language Courses') }}</p>
            <p class="text-xs text-gray-500 mt-0.5">{{ __('Where to learn German') }}</p>
        </a>
        <a href="{{ route('tools.visa-appointment') }}" class="block bg-white border border-gray-200 rounded-xl p-4 hover:border-violet-400 hover:shadow-sm transition">
            <p class="mb-1 text-violet-600"><x-svg-icon name="calendar" class="w-6 h-6" /></p>
            <p class="font-bold text-gray-900">{{ __('Visa Appointment') }}</p>
            <p class="text-xs text-gray-500 mt-0.5">{{ __('iData step-by-step') }}</p>
        </a>
        <a href="{{ route('tools.blocked-account') }}" class="block bg-white border border-gray-200 rounded-xl p-4 hover:border-violet-400 hover:shadow-sm transition">
            <p class="mb-1 text-violet-600"><x-svg-icon name="banknotes" class="w-6 h-6" /></p>
            <p class="font-bold text-gray-900">{{ __('Blocked Account') }}</p>
            <p class="text-xs text-gray-500 mt-0.5">{{ __('Sperrkonto for the visa') }}</p>
        </a>
    </section>

    {{-- Disclaimer --}}
    <p class="text-xs text-gray-400 mt-8 text-center max-w-3xl mx-auto">
        {{ __('Levels and costs are based on official exam-provider and university sources verified on the date shown and may change. Always confirm the exact requirement on your target university\'s admissions page.') }}
    </p>
</div>
@endsection
