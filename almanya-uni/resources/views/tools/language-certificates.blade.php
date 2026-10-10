@extends('layouts.app')

@section('title', __('German Language Certificates for University — TestDaF, DSH, telc, Goethe') . ' — ' . brand('name'))

<x-seo
    :title="__('German Language Certificates for University')"
    :description="__('TestDaF, DSH, telc C1 Hochschule, Goethe C2 or C1: what each German certificate is for, which the HRK/KMK framework lists and what to check.')"
/>

<x-json-ld :data="\App\Support\Seo::breadcrumbs([
    ['name' => __('Home'), 'url' => route('home')],
    ['name' => __('Tools'), 'url' => route('tools.index')],
    ['name' => __('Language Certificates'), 'url' => route('tools.language-certificates')],
])" />

@php
    // Doğrulandı 2026-10-10: HRK/KMK Rahmenordnung (RO-DT, HRK 04.11.2025 / KMK 27.11.2025) §§ 2–8, testdaf.de,
    // telc.net, Goethe-Institut sınav tanımları, uni-assist, LMU ve RWTH dil sayfaları. Ücretler yer/tarihe göre
    // değiştiği için gösterilmez. Kabul edilen belge ve seviyeyi üniversite/program belirler.
    $rows = [
        ['name' => 'TestDaF', 'sub' => 'TDN 3 · TDN 4 · TDN 5',
         'use' => __('Test for university admission, digital or on paper.'),
         'who' => __('Listed in the framework regulation. TDN 4 in all four parts counts as proof for all programmes; the university sets the level for each programme.'),
         'check' => __('The TDN required in each part, and whether the university only accepts a recent certificate.')],
        ['name' => 'DSH', 'sub' => 'DSH-1 · DSH-2 · DSH-3',
         'use' => __('University entrance exam offered by universities and recognised Studienkollegs in Germany.'),
         'who' => __('Listed in the framework regulation. A registered DSH is recognised by German universities; DSH-2 counts for all programmes. An admission based on DSH-1 at one university does not bind others.'),
         'check' => __('The DSH level the programme requires, and whether DSH-1 is accepted.')],
        ['name' => 'telc Deutsch C1 Hochschule', 'sub' => 'C1',
         'use' => __('Exam at C1 designed for university entrance.'),
         'who' => __('Listed as an exempting certificate in the framework regulation; individual universities can have different rules.'),
         'check' => __('Whether the programme\'s list of certificates includes it.')],
        ['name' => 'Goethe-Zertifikat C2: GDS', 'sub' => 'C2',
         'use' => __('The highest Goethe-Institut exam.'),
         'who' => __('Listed as an exempting certificate in the framework regulation. Recognition of older Goethe diplomas is up to the university.'),
         'check' => __('Usually accepted as listed; check the programme page if you hold an older Goethe diploma.')],
        ['name' => 'Goethe-Zertifikat C1', 'sub' => 'C1',
         'use' => __('General German at C1.'),
         'who' => __('Not in the framework regulation, so each university decides. RWTH Aachen accepts it, for example; LMU München lists only the C2 certificate.'),
         'check' => __('Whether Goethe C1 appears on the programme\'s list.')],
        ['name' => 'DSD II', 'sub' => __('School diploma'),
         'use' => __('The KMK German Language Diploma, second level, usually taken at school.'),
         'who' => __('Listed in the framework regulation.'),
         'check' => __('The level shown on the diploma.')],
        ['name' => __('Course attendance certificate'), 'sub' => __('Not an exam'),
         'use' => __('Shows that you attended or completed a German course.'),
         'who' => __('Many universities do not accept it as language proof. Some accept it at the application stage for admission-free programmes, with the exam result due by enrolment.'),
         'check' => __('What is enough at application and what must be handed in by enrolment.')],
    ];
    $locale = app()->getLocale();
    $guideSlug = [
        'tr' => ['goethe-telc-testdaf-dsh-difference-german-language-exam-comparison-for-turkish', 'goethe-telc-testdaf-dsh-differences-german-language-exam-comparison-for-turkish'],
        'en' => ['goethe-telc-testdaf-dsh-difference-german-language-exam-comparison-for-turkish-en', 'goethe-telc-testdaf-dsh-differences-german-language-exam-comparison-for-turkish-en'],
        'de' => ['goethe-telc-testdaf-dsh-difference-german-language-exam-comparison-for-turkish-de', 'goethe-telc-testdaf-dsh-differences-german-language-exam-comparison-for-turkish-de'],
    ][$locale] ?? null;
    $guidePost = $guideSlug ? \App\Models\Post::published()->where('locale', $locale)->whereIn('slug', $guideSlug)->first() : null;
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
            {{ __('German-taught degree programmes require proof of German. Which certificate and which level are accepted is decided by the university and the programme. Here is what each certificate is and what to check.') }}
        </p>
    </div>
</section>

<div class="max-w-[1100px] mx-auto px-4 py-10">

    {{-- Featured snippet (AIO hedefi) --}}
    <x-featured-snippet
        :question="__('Which German certificate do I need for university — TestDaF or DSH?')"
        :answer="__('Both are listed in the HRK/KMK framework regulation for German-taught study: TestDaF with TDN 4 in all four parts and DSH-2 count as proof for all programmes. telc Deutsch C1 Hochschule and Goethe-Zertifikat C2 are listed as exempting certificates. Goethe C1 is not in the framework regulation, so it depends on the university. The level each programme requires is set by the university.')"
    />

    {{-- KARŞILAŞTIRMA TABLOSU --}}
    <section class="mt-8 bg-white border border-gray-200 rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-left">
                    <tr>
                        <th class="px-4 py-3 font-semibold text-gray-700">{{ __('Certificate') }}</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">{{ __('What it is used for') }}</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">{{ __('Who decides whether it is accepted') }}</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">{{ __('What to check on the programme page') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 align-top">
                    @foreach ($rows as $r)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-bold text-gray-900 min-w-[9rem]">
                                {{ $r['name'] }}
                                <span class="block text-xs font-normal text-gray-500">{{ $r['sub'] }}</span>
                            </td>
                            <td class="px-4 py-3 text-gray-700 min-w-[12rem]">{{ $r['use'] }}</td>
                            <td class="px-4 py-3 text-gray-700 min-w-[14rem]">{{ $r['who'] }}</td>
                            <td class="px-4 py-3 text-gray-600 min-w-[12rem]">{{ $r['check'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-4 py-2 text-xs text-gray-500 border-t border-gray-100">
            {{ __('Framework regulation = the HRK/KMK framework regulation on German language exams for study at German universities (RO-DT), version of November 2025.') }}
        </div>
    </section>

    {{-- SEVİYELER --}}
    <section class="mt-8 bg-indigo-50 border border-indigo-100 rounded-xl p-6">
        <h2 class="text-xl font-bold text-indigo-900 mb-3 inline-flex items-center gap-2">
            <x-svg-icon name="check" class="w-5 h-5" /> {{ __('Levels in the framework regulation') }}
        </h2>
        <ul class="text-indigo-800 text-sm space-y-2 leading-relaxed">
            <li>• {{ __('TestDaF with TDN 4 in all four parts and DSH-2 count as proof for all programmes.') }}</li>
            <li>• {{ __('TDN 3 and DSH-1 are entry levels. They are only enough where the university allows them for a programme.') }}</li>
            <li>• {{ __('TDN 5 and DSH-3 are above the level the regulation requires; a university can still ask for more in a specific programme.') }}</li>
        </ul>
        <x-source-note
            :sources="[
                ['name' => 'HRK — RO-DT', 'url' => 'https://www.hrk.de/themen/internationales/internationale-studierende-und-forschende/hochschulzugang-fuer-internationale-studierende/sprachnachweis-deutsch/'],
                ['name' => 'TestDaF', 'url' => 'https://www.testdaf.de/de/teilnehmende/mein-testdaf/faq/faq-ergebnisse-und-zertifikat/'],
                ['name' => 'uni-assist', 'url' => 'https://www.uni-assist.de/bewerben/dokumente-sammeln/sprachzertifikate/'],
            ]"
            updated="2026-10-10"
            :note="__('Each program sets its own requirement — always confirm on the university\'s admissions page.')"
            class="!bg-white/60 !border-indigo-100"
        />
    </section>

    {{-- KARAR --}}
    <section class="mt-8 bg-emerald-50 border border-emerald-100 rounded-xl p-6">
        <h2 class="text-xl font-bold text-emerald-900 mb-3 inline-flex items-center gap-2">
            <x-svg-icon name="light-bulb" class="w-5 h-5" /> {{ __('Which one should you take?') }}
        </h2>
        <ul class="text-emerald-800 text-sm space-y-2 leading-relaxed">
            <li>• {{ __('Start with the programme\'s list of accepted certificates; the university decides which certificates and levels count.') }}</li>
            <li>• {{ __('TestDaF and telc are taken at test centres; DSH is offered by universities and recognised Studienkollegs in Germany.') }}</li>
            <li>• {{ __('A Goethe C1 certificate is not accepted everywhere, because it is not in the framework regulation.') }}</li>
            <li>• {{ __('A course attendance certificate is not an exam result. Check what the university accepts at application and what is due by enrolment.') }}</li>
            <li>• {{ __('Need to reach C1 first?') }} <a href="{{ route('language-courses.index') }}" class="underline">{{ __('Find a German course') }}</a> {{ __('(university, private or online).') }}</li>
        </ul>
        @if ($guidePost)
            <p class="mt-4"><a href="{{ $guidePost->publicUrl() }}" class="inline-flex items-center gap-1 px-3 py-2 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-semibold transition">{{ __('Read the guide: German level and certificates for university') }} →</a></p>
        @endif
    </section>

    {{-- Cross-link --}}
    <section class="mt-8 grid grid-cols-1 sm:grid-cols-3 gap-3">
        <a href="{{ route('language-courses.index') }}" class="block bg-white border border-gray-200 rounded-xl p-4 hover:border-violet-400 hover:shadow-sm transition">
            <p class="mb-1 text-violet-600"><x-svg-icon name="academic-cap" class="w-6 h-6" /></p>
            <p class="font-bold text-gray-900">{{ __('Language Courses') }}</p>
            <p class="text-xs text-gray-500 mt-0.5">{{ __('Where to learn German') }}</p>
        </a>
        <a href="{{ route('discover.english') }}" class="block bg-white border border-gray-200 rounded-xl p-4 hover:border-violet-400 hover:shadow-sm transition">
            <p class="mb-1 text-violet-600"><x-svg-icon name="academic-cap" class="w-6 h-6" /></p>
            <p class="font-bold text-gray-900">{{ __('English-taught programs') }}</p>
            <p class="text-xs text-gray-500 mt-0.5">{{ __('Bachelor\'s and Master\'s taught in English') }}</p>
        </a>
        <a href="{{ route('tools.visa-appointment') }}" class="block bg-white border border-gray-200 rounded-xl p-4 hover:border-violet-400 hover:shadow-sm transition">
            <p class="mb-1 text-violet-600"><x-svg-icon name="calendar" class="w-6 h-6" /></p>
            <p class="font-bold text-gray-900">{{ __('Visa Appointment') }}</p>
            <p class="text-xs text-gray-500 mt-0.5">{{ __('iData step-by-step') }}</p>
        </a>
    </section>

    {{-- Disclaimer --}}
    <p class="text-xs text-gray-400 mt-8 text-center max-w-3xl mx-auto">
        {{ __('Based on the HRK/KMK framework regulation, exam providers and university pages checked on the date shown. Always confirm the exact requirement on your target university\'s admissions page.') }}
    </p>
</div>
@endsection
