@extends('layouts.app')

@section('title', __('DAAD Scholarships — Complete Guide for International Students') . ' — ' . brand('name'))

<x-seo
    :title="__('DAAD Scholarships — Complete Guide for International Students')"
    :description="__('Scholarships offered by DAAD for studying in Germany: Bachelor, Master, PhD, Postdoc, Summer School, Arts, Architecture. Amounts, application process, deadlines.')"
/>

<x-json-ld :data="\App\Support\Seo::breadcrumbs([
    ['name' => __('Home'), 'url' => route('home')],
    ['name' => __('Scholarships'), 'url' => route('scholarships.index')],
    ['name' => 'DAAD', 'url' => route('scholarships.daad')],
])" />

<x-json-ld :data="[
    '@context' => 'https://schema.org',
    '@type' => 'Article',
    'headline' => __('DAAD Scholarships — Complete Guide for International Students'),
    'description' => __('DAAD\'s 166+ scholarship programs: bachelor, master, PhD, postdoc, summer school, arts. Monthly 934-2,670 € support.'),
    'inLanguage' => app()->getLocale(),
    'datePublished' => '2026-05-19',
    'author' => ['@type' => 'Organization', 'name' => 'AlmanyaUni'],
    'publisher' => ['@type' => 'Organization', 'name' => 'AlmanyaUni', 'url' => url('/')],
]" />

@section('content')

{{-- HERO --}}
<section class="bg-gradient-to-br from-blue-700 via-blue-600 to-cyan-500 text-white">
    <div class="max-w-[1400px] mx-auto px-4 py-12 md:py-16">
        <nav class="text-sm text-blue-100 mb-3">
            <a href="/" class="hover:text-white">{{ __('Home') }}</a>
            <span class="mx-2 opacity-50">›</span>
            <a href="{{ route('scholarships.index') }}" class="hover:text-white">{{ __('Scholarships') }}</a>
            <span class="mx-2 opacity-50">›</span>
            <span class="text-white">DAAD</span>
        </nav>
        <div class="flex items-start gap-5 flex-wrap">
            <div class="text-7xl">🇩🇪</div>
            <div class="flex-1 min-w-0">
                <h1 class="text-3xl md:text-5xl font-extrabold leading-tight">{{ __('DAAD Scholarships') }}</h1>
                <p class="text-lg text-blue-100 mt-2">{{ __('Deutscher Akademischer Austauschdienst — German Academic Exchange Service') }}</p>
                <p class="text-base text-blue-100 mt-2 max-w-3xl">
                    {!! __('Germany\'s official academic exchange organization. The broadest scholarship portfolio for international students: <strong class="text-white">166+ programs</strong>, every level from bachelor to professor.') !!}
                </p>
            </div>
        </div>
    </div>
</section>

{{-- TOC --}}
<section class="bg-white border-b border-gray-200 sticky top-0 z-30 shadow-sm">
    <div class="max-w-[1400px] mx-auto px-4 py-3 flex flex-wrap gap-2 overflow-x-auto text-sm">
        <a href="#overview" class="px-3 py-1.5 rounded-full bg-gray-100 hover:bg-primary-50 text-gray-700 whitespace-nowrap">📋 {{ __('Overview') }}</a>
        <a href="#bachelor" class="px-3 py-1.5 rounded-full bg-gray-100 hover:bg-primary-50 text-gray-700 whitespace-nowrap">🎓 {{ __('Bachelor') }}</a>
        <a href="#master" class="px-3 py-1.5 rounded-full bg-gray-100 hover:bg-primary-50 text-gray-700 whitespace-nowrap">📚 {{ __('Master') }}</a>
        <a href="#phd" class="px-3 py-1.5 rounded-full bg-gray-100 hover:bg-primary-50 text-gray-700 whitespace-nowrap">🔬 PhD</a>
        <a href="#postdoc" class="px-3 py-1.5 rounded-full bg-gray-100 hover:bg-primary-50 text-gray-700 whitespace-nowrap">🎯 Postdoc</a>
        <a href="#summer" class="px-3 py-1.5 rounded-full bg-gray-100 hover:bg-primary-50 text-gray-700 whitespace-nowrap">🌞 {{ __('Summer School') }}</a>
        <a href="#art" class="px-3 py-1.5 rounded-full bg-gray-100 hover:bg-primary-50 text-gray-700 whitespace-nowrap">🎨 {{ __('Arts') }}</a>
        <a href="#how-to" class="px-3 py-1.5 rounded-full bg-gray-100 hover:bg-primary-50 text-gray-700 whitespace-nowrap">📝 {{ __('Application') }}</a>
        <a href="#contact" class="px-3 py-1.5 rounded-full bg-gray-100 hover:bg-primary-50 text-gray-700 whitespace-nowrap">📞 {{ __('Turkey Office') }}</a>
    </div>
</section>

<section class="bg-gray-50 py-12">
    <div class="max-w-[1400px] mx-auto px-4 space-y-10">

        {{-- ═══════ OVERVIEW ═══════ --}}
        <article id="overview" class="bg-white rounded-2xl border border-gray-200 p-6 md:p-8 shadow-sm">
            <h2 class="text-2xl md:text-3xl font-extrabold text-gray-900 mb-4">📋 {{ __('What is DAAD, why is it important?') }}</h2>
            <p class="text-gray-700 leading-relaxed mb-4">
                {!! __('DAAD (<em>Deutscher Akademischer Austauschdienst</em>) was founded in 1925 and is the world\'s largest academic exchange organization. Funded by the German federal government, it is an <strong>independent</strong> academic bridge. The <strong>most trusted and comprehensive</strong> scholarship source for international students.') !!}
            </p>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mt-5">
                <div class="bg-blue-50 rounded-lg p-4 text-center">
                    <div class="text-3xl font-extrabold text-blue-700">166+</div>
                    <p class="text-xs text-blue-700 mt-1">{{ __('Scholarship programs') }}</p>
                </div>
                <div class="bg-purple-50 rounded-lg p-4 text-center">
                    <div class="text-3xl font-extrabold text-purple-700">100K+</div>
                    <p class="text-xs text-purple-700 mt-1">{{ __('Scholarships awarded annually') }}</p>
                </div>
                <div class="bg-emerald-50 rounded-lg p-4 text-center">
                    <div class="text-3xl font-extrabold text-emerald-700">€650M</div>
                    <p class="text-xs text-emerald-700 mt-1">{{ __('Annual budget') }}</p>
                </div>
                <div class="bg-amber-50 rounded-lg p-4 text-center">
                    <div class="text-3xl font-extrabold text-amber-700">2.670€</div>
                    <p class="text-xs text-amber-700 mt-1">{{ __('Max monthly (postdoc)') }}</p>
                </div>
            </div>

            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                <div class="flex items-start gap-3 bg-emerald-50 rounded-lg p-3">
                    <span class="text-2xl shrink-0">✅</span>
                    <div>
                        <p class="font-semibold text-emerald-900">{{ __('What does it cover?') }}</p>
                        <p class="text-emerald-800">{{ __('All costs from DAAD: stipend + health insurance + travel + research allowance') }}</p>
                    </div>
                </div>
                <div class="flex items-start gap-3 bg-amber-50 rounded-lg p-3">
                    <span class="text-2xl shrink-0">⚠️</span>
                    <div>
                        <p class="font-semibold text-amber-900">{{ __('Is it repayable?') }}</p>
                        <p class="text-amber-800">{{ __('No — it is a grant (non-repayable). The only condition: stay in Germany and complete the program.') }}</p>
                    </div>
                </div>
            </div>
        </article>

        {{-- ═══════ BACHELOR ═══════ --}}
        <article id="bachelor" class="bg-white rounded-2xl border border-gray-200 p-6 md:p-8 shadow-sm">
            <h2 class="text-2xl md:text-3xl font-extrabold text-gray-900 mb-4">🎓 {{ __('Bachelor Scholarships') }}</h2>
            <p class="text-gray-700 mb-5">
                {!! __('<strong>Important:</strong> DAAD generally does <strong>not</strong> award direct scholarships to new bachelor students. Comprehensive options are limited for international students at the bachelor stage — alternatives below.') !!}
            </p>

            <div class="space-y-4">
                <div class="border-l-4 border-blue-500 pl-4 py-2">
                    <h3 class="font-bold text-gray-900">DAAD Going Abroad Exchange</h3>
                    <p class="text-sm text-gray-600 mt-1">
                        {!! __('<strong>Target:</strong> One or two semesters of exchange in Germany while studying bachelor at your home university. <strong>Duration:</strong> 1-2 semesters · <strong>Amount:</strong> 850-1,000 €/month + travel') !!}
                    </p>
                </div>

                <div class="border-l-4 border-blue-500 pl-4 py-2">
                    <h3 class="font-bold text-gray-900">HAW.International — Applied Sciences Exchange</h3>
                    <p class="text-sm text-gray-600 mt-1">
                        {!! __('<strong>Target:</strong> Internship/exchange for applied sciences (Fachhochschule) students. <strong>Duration:</strong> 3-12 months · <strong>Amount:</strong> 1,075 €/month + accommodation') !!}
                    </p>
                </div>

                <div class="border-l-4 border-gray-300 pl-4 py-2">
                    <h3 class="font-bold text-gray-900">{{ __('Alternative: Erasmus+ Programme') }}</h3>
                    <p class="text-sm text-gray-600 mt-1">
                        {{ __('If your home university has an Erasmus+ agreement, apply via Erasmus. The most practical path for bachelor.') }}
                        <a href="https://erasmus-plus.ec.europa.eu" target="_blank" class="text-primary-600 hover:underline">erasmus-plus.ec.europa.eu</a>
                    </p>
                </div>
            </div>
        </article>

        {{-- ═══════ MASTER ═══════ --}}
        <article id="master" class="bg-white rounded-2xl border border-gray-200 p-6 md:p-8 shadow-sm">
            <h2 class="text-2xl md:text-3xl font-extrabold text-gray-900 mb-4">📚 {{ __('Master Scholarships') }}</h2>
            <p class="text-gray-700 mb-5">
                {!! __('DAAD\'s <strong>most active</strong> scholarship category. The most realistic application route for international students. Monthly <strong>934 €</strong> + health insurance + travel + 460 € research allowance.') !!}
            </p>

            <div class="space-y-4">
                <div class="bg-blue-50 rounded-lg p-4 border-l-4 border-blue-500">
                    <h3 class="font-bold text-gray-900 mb-1">⭐ Study Scholarships for Foreign Graduates ({{ __('All fields') }})</h3>
                    <p class="text-sm text-gray-700 mb-2">
                        {{ __('The main DAAD scholarship for foreign bachelor graduates. Open to all fields.') }}
                    </p>
                    <ul class="text-xs text-gray-600 space-y-0.5">
                        <li>{!! __('<strong>Duration:</strong> 12-24 months (until master is completed)') !!}</li>
                        <li>{!! __('<strong>Amount:</strong> 934 €/month + 460 €/year research allowance') !!}</li>
                        <li>{!! __('<strong>Plus:</strong> Health insurance, travel allowance, language course (free)') !!}</li>
                        <li>{!! __('<strong>Deadline:</strong> October/November (for autumn intake)') !!}</li>
                        <li>{!! __('<strong>Requirement:</strong> Final-year bachelor or graduate (max 6 years ago), B1+ German or B2+ English') !!}</li>
                    </ul>
                </div>

                <div class="bg-purple-50 rounded-lg p-4 border-l-4 border-purple-500">
                    <h3 class="font-bold text-gray-900 mb-1">⭐ Helmut-Schmidt-Programme (Public Policy & Good Governance)</h3>
                    <p class="text-sm text-gray-700 mb-2">
                        {{ __('Elite program for those who want to become leaders in public policy, democracy, economy. Limited quota, highly competitive.') }}
                    </p>
                    <ul class="text-xs text-gray-600 space-y-0.5">
                        <li>{!! __('<strong>Duration:</strong> 24 months (full master)') !!}</li>
                        <li>{!! __('<strong>Amount:</strong> 934 €/month + extra grant') !!}</li>
                        <li>{!! __('<strong>Target:</strong> Citizens of developing/transition countries') !!}</li>
                        <li>{!! __('<strong>Deadline:</strong> July') !!}</li>
                    </ul>
                </div>

                <div class="bg-emerald-50 rounded-lg p-4 border-l-4 border-emerald-500">
                    <h3 class="font-bold text-gray-900 mb-1">⭐ EPOS — Development-Related Postgraduate Courses</h3>
                    <p class="text-sm text-gray-700 mb-2">
                        {{ __('Specific master programs for developing countries (development, engineering, health, environment). English master programs at 45+ German universities.') }}
                    </p>
                    <ul class="text-xs text-gray-600 space-y-0.5">
                        <li>{!! __('<strong>Duration:</strong> 12-24 months') !!}</li>
                        <li>{!! __('<strong>Amount:</strong> 934 €/month + travel + research + family allowance') !!}</li>
                        <li>{!! __('<strong>Programs:</strong> Renewable Energy, Public Health, Tropical Agriculture, etc.') !!}</li>
                        <li>{!! __('<strong>Deadline:</strong> Program-specific — usually September-October') !!}</li>
                    </ul>
                </div>

                <div class="bg-rose-50 rounded-lg p-4 border-l-4 border-rose-500">
                    <h3 class="font-bold text-gray-900 mb-1">DAAD-WHO Scholarship — Public Health</h3>
                    <p class="text-sm text-gray-700 mb-2">
                        {{ __('For a master in public health in cooperation with WHO. Priority for health professionals.') }}
                    </p>
                    <ul class="text-xs text-gray-600 space-y-0.5">
                        <li>{!! __('<strong>Duration:</strong> 12-24 months · <strong>Amount:</strong> 934 €/month + tuition') !!}</li>
                    </ul>
                </div>

                <div class="bg-amber-50 rounded-lg p-4 border-l-4 border-amber-500">
                    <h3 class="font-bold text-gray-900 mb-1">Music Scholarships — Performing Arts</h3>
                    <p class="text-sm text-gray-700 mb-2">
                        {{ __('Master/diploma programs for music, composition, opera, dance.') }}
                    </p>
                    <ul class="text-xs text-gray-600 space-y-0.5">
                        <li>{!! __('<strong>Duration:</strong> 12-24 months · <strong>Amount:</strong> 934 €/month + artist materials allowance') !!}</li>
                        <li>{!! __('<strong>Requirement:</strong> Audition / portfolio mandatory') !!}</li>
                    </ul>
                </div>
            </div>
        </article>

        {{-- ═══════ PhD ═══════ --}}
        <article id="phd" class="bg-white rounded-2xl border border-gray-200 p-6 md:p-8 shadow-sm">
            <h2 class="text-2xl md:text-3xl font-extrabold text-gray-900 mb-4">🔬 {{ __('PhD (Doctoral) Scholarships') }}</h2>
            <p class="text-gray-700 mb-5">
                {!! __('Doctoral level is DAAD\'s <strong>flagship category</strong>. Monthly <strong>1,300 €</strong> and above, for 3-4 years. Comprehensive options for those who want to pursue a doctorate from abroad.') !!}
            </p>

            <div class="space-y-4">
                <div class="bg-blue-50 rounded-lg p-4 border-l-4 border-blue-500">
                    <h3 class="font-bold text-gray-900 mb-1">⭐ Research Grants — Doctoral Programmes in Germany</h3>
                    <p class="text-sm text-gray-700 mb-2">
                        {{ __('For international researchers who want to do a full PhD in Germany. All scientific fields.') }}
                    </p>
                    <ul class="text-xs text-gray-600 space-y-0.5">
                        <li>{!! __('<strong>Duration:</strong> 3-4 years') !!}</li>
                        <li>{!! __('<strong>Amount:</strong> 1,300 €/month + 460 €/year printing + health + travel + 1,000 € family allowance (if applicable)') !!}</li>
                        <li>{!! __('<strong>Deadline:</strong> Usually November') !!}</li>
                        <li>{!! __('<strong>Requirement:</strong> Master graduate, acceptance letter from target professor, research proposal') !!}</li>
                    </ul>
                </div>

                <div class="bg-purple-50 rounded-lg p-4 border-l-4 border-purple-500">
                    <h3 class="font-bold text-gray-900 mb-1">⭐ Bi-nationally Supervised Doctoral Degree (Co-tutelle)</h3>
                    <p class="text-sm text-gray-700 mb-2">
                        {{ __('Continue your PhD at your home university + work with a co-supervisor in Germany. Dual degree.') }}
                    </p>
                    <ul class="text-xs text-gray-600 space-y-0.5">
                        <li>{!! __('<strong>Duration:</strong> Total 36 months (at least 18 months in Germany)') !!}</li>
                        <li>{!! __('<strong>Amount:</strong> 1,300 €/month (in Germany)') !!}</li>
                        <li>{!! __('<strong>Advantage:</strong> Joint supervisor + diploma in two countries') !!}</li>
                    </ul>
                </div>

                <div class="bg-emerald-50 rounded-lg p-4 border-l-4 border-emerald-500">
                    <h3 class="font-bold text-gray-900 mb-1">Research Grants — One-Year Grants for Doctoral Candidates</h3>
                    <p class="text-sm text-gray-700 mb-2">
                        {{ __('If you are doing a PhD at home, one year of research in Germany. Archive/laboratory access for your thesis.') }}
                    </p>
                    <ul class="text-xs text-gray-600 space-y-0.5">
                        <li>{!! __('<strong>Duration:</strong> 7-10 months · <strong>Amount:</strong> 1,300 €/month') !!}</li>
                    </ul>
                </div>

                <div class="bg-amber-50 rounded-lg p-4 border-l-4 border-amber-500">
                    <h3 class="font-bold text-gray-900 mb-1">Research Grants — Short-Term Grants (1-6 Months)</h3>
                    <p class="text-sm text-gray-700 mb-2">
                        {{ __('Short-term research stay — fieldwork, archive research, conference.') }}
                    </p>
                    <ul class="text-xs text-gray-600 space-y-0.5">
                        <li>{!! __('<strong>Duration:</strong> 1-6 months · <strong>Amount:</strong> 1,300 €/month') !!}</li>
                    </ul>
                </div>
            </div>
        </article>

        {{-- ═══════ POSTDOC ═══════ --}}
        <article id="postdoc" class="bg-white rounded-2xl border border-gray-200 p-6 md:p-8 shadow-sm">
            <h2 class="text-2xl md:text-3xl font-extrabold text-gray-900 mb-4">🎯 {{ __('Postdoc & Academic Scholarships') }}</h2>
            <p class="text-gray-700 mb-5">
                {!! __('The highest-amount DAAD scholarships for post-doctoral researchers — starting from <strong>2,150 €</strong>/month.') !!}
            </p>

            <div class="space-y-4">
                <div class="bg-blue-50 rounded-lg p-4 border-l-4 border-blue-500">
                    <h3 class="font-bold text-gray-900 mb-1">⭐ Research Stays for University Academics & Scientists</h3>
                    <p class="text-sm text-gray-700 mb-2">
                        {{ __('1-3 month research stay in Germany for academics working at a foreign university.') }}
                    </p>
                    <ul class="text-xs text-gray-600 space-y-0.5">
                        <li>{!! __('<strong>Duration:</strong> 1-3 months · <strong>Amount:</strong> 2,150 €/month (lecturer) or 2,500 €/month (professor)') !!}</li>
                    </ul>
                </div>

                <div class="bg-purple-50 rounded-lg p-4 border-l-4 border-purple-500">
                    <h3 class="font-bold text-gray-900 mb-1">⭐ DAAD PRIME — Postdoctoral Researchers International Mobility</h3>
                    <p class="text-sm text-gray-700 mb-2">
                        {{ __('For those who want to do a postdoc abroad and return to Germany, or vice versa. Hybrid model.') }}
                    </p>
                    <ul class="text-xs text-gray-600 space-y-0.5">
                        <li>{!! __('<strong>Duration:</strong> 18 months (12 months abroad + 6 months in Germany)') !!}</li>
                        <li>{!! __('<strong>Amount:</strong> Like salaried employee in Germany, ~3,500 €/month net') !!}</li>
                    </ul>
                </div>

                <div class="bg-emerald-50 rounded-lg p-4 border-l-4 border-emerald-500">
                    <h3 class="font-bold text-gray-900 mb-1">Alexander von Humboldt Foundation Postdoc</h3>
                    <p class="text-sm text-gray-700 mb-2">
                        {{ __('Close partner with DAAD. The most prestigious German postdoc scholarship.') }}
                    </p>
                    <ul class="text-xs text-gray-600 space-y-0.5">
                        <li>{!! __('<strong>Duration:</strong> 6-24 months · <strong>Amount:</strong> 2,670 €/month + research support + travel') !!}</li>
                        <li>{!! __('<strong>Requirement:</strong> PhD obtained within the last 4 years, international publications') !!}</li>
                    </ul>
                </div>
            </div>
        </article>

        {{-- ═══════ SUMMER ═══════ --}}
        <article id="summer" class="bg-white rounded-2xl border border-gray-200 p-6 md:p-8 shadow-sm">
            <h2 class="text-2xl md:text-3xl font-extrabold text-gray-900 mb-4">🌞 {{ __('Summer School & Short-Term') }}</h2>
            <p class="text-gray-700 mb-5">
                {{ __('For a short Germany experience. Language + culture + academic content for 3-4 weeks.') }}
            </p>

            <div class="space-y-4">
                <div class="bg-amber-50 rounded-lg p-4 border-l-4 border-amber-500">
                    <h3 class="font-bold text-gray-900 mb-1">⭐ University Summer Course Grants (Hochschulsommerkurse)</h3>
                    <p class="text-sm text-gray-700 mb-2">
                        {{ __('3-4 week German + academic course at a German university during summer months.') }}
                    </p>
                    <ul class="text-xs text-gray-600 space-y-0.5">
                        <li>{!! __('<strong>Duration:</strong> 3-4 weeks (June-September) · <strong>Amount:</strong> 1,075 € + course fee included + travel') !!}</li>
                        <li>{!! __('<strong>Target:</strong> Bachelor 2nd-year+ students · <strong>Deadline:</strong> December') !!}</li>
                    </ul>
                </div>

                <div class="bg-rose-50 rounded-lg p-4 border-l-4 border-rose-500">
                    <h3 class="font-bold text-gray-900 mb-1">DAAD-RISE Worldwide — {{ __('Engineering/Science Internship') }}</h3>
                    <p class="text-sm text-gray-700 mb-2">
                        {{ __('Summer internship for engineering, natural sciences, IT bachelor students. You are matched with PhD candidates.') }}
                    </p>
                    <ul class="text-xs text-gray-600 space-y-0.5">
                        <li>{!! __('<strong>Duration:</strong> 6-12 weeks · <strong>Amount:</strong> 750 €/month + travel + accommodation') !!}</li>
                    </ul>
                </div>

                <div class="bg-blue-50 rounded-lg p-4 border-l-4 border-blue-500">
                    <h3 class="font-bold text-gray-900 mb-1">WISE — Working Internships in Science & Engineering</h3>
                    <p class="text-sm text-gray-700 mb-2">
                        {{ __('For North American students. DAAD-RISE is more suitable from other regions.') }}
                    </p>
                </div>

                <div class="bg-emerald-50 rounded-lg p-4 border-l-4 border-emerald-500">
                    <h3 class="font-bold text-gray-900 mb-1">German Language Course Grants</h3>
                    <p class="text-sm text-gray-700 mb-2">
                        {{ __('Intensive German summer course — Goethe-Institut or university language centers.') }}
                    </p>
                    <ul class="text-xs text-gray-600 space-y-0.5">
                        <li>{!! __('<strong>Duration:</strong> 8 weeks · <strong>Amount:</strong> 1,075 € + course fee') !!}</li>
                    </ul>
                </div>
            </div>
        </article>

        {{-- ═══════ ARTS ═══════ --}}
        <article id="art" class="bg-white rounded-2xl border border-gray-200 p-6 md:p-8 shadow-sm">
            <h2 class="text-2xl md:text-3xl font-extrabold text-gray-900 mb-4">🎨 {{ __('Arts, Architecture & Film') }}</h2>
            <p class="text-gray-700 mb-5">
                {{ __('DAAD\'s art-focused programs for creative disciplines.') }}
            </p>

            <div class="space-y-4">
                <div class="bg-rose-50 rounded-lg p-4 border-l-4 border-rose-500">
                    <h3 class="font-bold text-gray-900 mb-1">Study Scholarships for Graduates of Fine Arts, Architecture, Music, Performing Arts</h3>
                    <p class="text-sm text-gray-700 mb-2">
                        {{ __('For master/postgraduate in fine arts, architecture, music, performing arts.') }}
                    </p>
                    <ul class="text-xs text-gray-600 space-y-0.5">
                        <li>{!! __('<strong>Duration:</strong> 10-24 months · <strong>Amount:</strong> 934 €/month + artist materials allowance + travel') !!}</li>
                        <li>{!! __('<strong>Requirement:</strong> Portfolio + audition + acceptance from arts universities') !!}</li>
                    </ul>
                </div>

                <div class="bg-purple-50 rounded-lg p-4 border-l-4 border-purple-500">
                    <h3 class="font-bold text-gray-900 mb-1">Berlinale Talents — {{ __('Film Sector') }}</h3>
                    <p class="text-sm text-gray-700 mb-2">
                        {{ __('6-day workshop at the Berlin Film Festival for young film professionals.') }}
                    </p>
                </div>
            </div>
        </article>

        {{-- ═══════ HOW TO APPLY ═══════ --}}
        <article id="how-to" class="bg-white rounded-2xl border border-gray-200 p-6 md:p-8 shadow-sm">
            <h2 class="text-2xl md:text-3xl font-extrabold text-gray-900 mb-4">📝 {{ __('Application Process — Step by Step') }}</h2>

            <div class="space-y-4">
                <div class="flex gap-4">
                    <div class="shrink-0 w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-lg">1</div>
                    <div>
                        <h3 class="font-bold text-gray-900">{{ __('Choose program + check eligibility') }}</h3>
                        <p class="text-sm text-gray-700">
                            {!! __('Use filters on <a href="https://www2.daad.de/deutschland/stipendium/datenbank/en/21148-scholarship-database/" target="_blank" class="text-primary-600 hover:underline">DAAD Scholarship Database</a>: <strong>Country</strong>, level (Bachelor/Master/PhD), subject group. Read eligibility requirements carefully.') !!}
                        </p>
                    </div>
                </div>

                <div class="flex gap-4">
                    <div class="shrink-0 w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-lg">2</div>
                    <div>
                        <h3 class="font-bold text-gray-900">{{ __('Start 12-15 months in advance') }}</h3>
                        <p class="text-sm text-gray-700">
                            {!! __('Most programs\' deadline is <strong>12-15 months before</strong> the start date. For autumn 2026 intake, apply in October/November 2025.') !!}
                        </p>
                    </div>
                </div>

                <div class="flex gap-4">
                    <div class="shrink-0 w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-lg">3</div>
                    <div>
                        <h3 class="font-bold text-gray-900">{{ __('Gather documents') }}</h3>
                        <ul class="text-sm text-gray-700 mt-1 space-y-1 list-disc list-inside">
                            <li>{{ __('Bachelor/master diploma + transcript (apostilled)') }}</li>
                            <li>{{ __('CV (Europass format preferred)') }}</li>
                            <li>{!! __('<strong>Motivation letter</strong> (2-3 pages — why Germany, why this program)') !!}</li>
                            <li>{{ __('2-3 recommendation letters (professor/employer)') }}</li>
                            <li>{{ __('Language certificate: TestDaF / DSH (German) or TOEFL / IELTS (English)') }}</li>
                            <li>{!! __('For Master+: <strong>acceptance letter</strong> from target professor (mandatory for research grants)') !!}</li>
                            <li>{{ __('Research proposal (for PhD/postdoc, 5-15 pages)') }}</li>
                        </ul>
                    </div>
                </div>

                <div class="flex gap-4">
                    <div class="shrink-0 w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-lg">4</div>
                    <div>
                        <h3 class="font-bold text-gray-900">{{ __('Online application via DAAD Portal') }}</h3>
                        <p class="text-sm text-gray-700">
                            {!! __('Upload all documents as PDF via <a href="https://portal.daad.de" target="_blank" class="text-primary-600 hover:underline">portal.daad.de</a>. Be careful, the deadline is strict.') !!}
                        </p>
                    </div>
                </div>

                <div class="flex gap-4">
                    <div class="shrink-0 w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-lg">5</div>
                    <div>
                        <h3 class="font-bold text-gray-900">{{ __('Interview (selection committee)') }}</h3>
                        <p class="text-sm text-gray-700">
                            {{ __('Those who pass pre-evaluation are interviewed at the local DAAD office. In German or English. Preparation: your research proposal, future plan, why DAAD.') }}
                        </p>
                    </div>
                </div>

                <div class="flex gap-4">
                    <div class="shrink-0 w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-lg">6</div>
                    <div>
                        <h3 class="font-bold text-gray-900">{{ __('Result — 3-4 months later') }}</h3>
                        <p class="text-sm text-gray-700">
                            {{ __('Result notification by email. If accepted: acceptance letter for visa application (replaces Sperrkonto). If rejected: apply again next term, you can request feedback.') }}
                        </p>
                    </div>
                </div>
            </div>
        </article>

        {{-- ═══════ LOCAL OFFICE ═══════ --}}
        <article id="contact" class="bg-gradient-to-br from-blue-50 to-cyan-50 rounded-2xl ring-1 ring-blue-200 p-6 md:p-8">
            @if (app()->getLocale() === 'tr')
            <h2 class="text-2xl md:text-3xl font-extrabold text-gray-900 mb-4">📞 DAAD Türkiye Ofisi (İstanbul)</h2>
            <p class="text-gray-700 mb-5">
                Türk öğrenciler için <strong>resmi yerel ofis</strong>. Mülakatlar burada yapılır, sorular için randevu alabilirsin.
            </p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-white rounded-lg p-4">
                    <p class="text-xs text-gray-500 uppercase tracking-wider font-bold mb-1">📍 Adres</p>
                    <p class="text-sm text-gray-900">
                        İnönü Caddesi No: 59<br>
                        Gümüşsuyu, Taksim<br>
                        34437 İstanbul
                    </p>
                </div>
                <div class="bg-white rounded-lg p-4">
                    <p class="text-xs text-gray-500 uppercase tracking-wider font-bold mb-1">🌐 İletişim</p>
                    <p class="text-sm text-gray-900">
                        <a href="https://www.daad-turkey.org/" target="_blank" class="text-primary-600 hover:underline">daad-turkey.org</a><br>
                        <a href="mailto:info@daad-turkey.org" class="text-primary-600 hover:underline">info@daad-turkey.org</a><br>
                        Tel: +90 212 251 25 12
                    </p>
                </div>
            </div>
            @else
            <h2 class="text-2xl md:text-3xl font-extrabold text-gray-900 mb-4">📞 {{ __('DAAD Regional Offices') }}</h2>
            <p class="text-gray-700 mb-5">
                {!! __('DAAD has <strong>regional information centers</strong> in many countries. Interviews are held there, and you can book appointments for questions.') !!}
            </p>
            <p class="text-sm text-gray-700">
                {!! __('Find your local DAAD office at <a href="https://www.daad.de/en/the-daad/offices-and-representatives/" target="_blank" class="text-primary-600 hover:underline">daad.de offices and representatives</a>.') !!}
            </p>
            @endif

            <div class="mt-5 grid grid-cols-2 md:grid-cols-4 gap-3 text-sm">
                <a href="https://www2.daad.de/deutschland/stipendium/datenbank/en/21148-scholarship-database/" target="_blank"
                   class="block bg-white rounded-lg p-3 hover:bg-blue-50 transition text-center">
                    <div class="text-2xl mb-1">🔍</div>
                    <p class="font-semibold text-gray-900">Database</p>
                </a>
                <a href="https://portal.daad.de" target="_blank"
                   class="block bg-white rounded-lg p-3 hover:bg-blue-50 transition text-center">
                    <div class="text-2xl mb-1">🌐</div>
                    <p class="font-semibold text-gray-900">Portal</p>
                </a>
                <a href="https://www.daad.de/en/" target="_blank"
                   class="block bg-white rounded-lg p-3 hover:bg-blue-50 transition text-center">
                    <div class="text-2xl mb-1">🇩🇪</div>
                    <p class="font-semibold text-gray-900">daad.de</p>
                </a>
                @if (app()->getLocale() === 'tr')
                <a href="https://www.daad-turkey.org/" target="_blank"
                   class="block bg-white rounded-lg p-3 hover:bg-blue-50 transition text-center">
                    <div class="text-2xl mb-1">🇹🇷</div>
                    <p class="font-semibold text-gray-900">DAAD Türkiye</p>
                </a>
                @else
                <a href="https://www.daad.de/en/the-daad/offices-and-representatives/" target="_blank"
                   class="block bg-white rounded-lg p-3 hover:bg-blue-50 transition text-center">
                    <div class="text-2xl mb-1">🌍</div>
                    <p class="font-semibold text-gray-900">{{ __('Local Offices') }}</p>
                </a>
                @endif
            </div>
        </article>

        {{-- SOURCE --}}
        <p class="text-xs text-gray-500 text-center">
            📖 {{ __('Source:') }} <a href="https://www.daad.de" target="_blank" class="text-primary-600 hover:underline">{{ __('DAAD official site') }}</a> ·
            DAAD Stipendienprogramme 2022 PDF · {{ __('Updated:') }} 2025
        </p>

    </div>
</section>

@endsection
