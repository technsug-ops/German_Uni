@extends('layouts.app')

@section('title', __('Germany Scholarships — Complete Guide for International Students') . ' — ' . brand('name'))

<x-seo
    :title="__('Germany Scholarships — DAAD, Erasmus+, Government, Foundations')"
    :description="__('Scholarship programs valid in Germany for international students: DAAD (bachelor/master/PhD/postdoc), Erasmus+, Deutschlandstipendium, foundation scholarships.')"
/>

@section('content')

<section class="bg-gradient-to-br from-primary-700 via-primary-600 to-accent-500 text-white">
    <div class="max-w-[1400px] mx-auto px-4 py-12 md:py-16">
        <nav class="text-sm text-primary-100 mb-3">
            <a href="/" class="hover:text-white">{{ __('Home') }}</a>
            <span class="mx-2 opacity-50">›</span>
            <span class="text-white">{{ __('Scholarships') }}</span>
        </nav>
        <h1 class="text-3xl md:text-5xl font-extrabold mb-3 leading-tight flex items-center gap-3"><x-svg-icon name="academic-cap" class="w-9 h-9 md:w-11 md:h-11" /> {{ __('Germany Scholarships') }}</h1>
        <p class="text-lg md:text-xl text-primary-100 max-w-3xl mb-6">
            {{ __('All scholarship categories valid in Germany for international students. Filter by education level, field and nationality — discover what fits you.') }}
        </p>
        <div class="flex flex-wrap gap-3 text-sm">
            <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/15 backdrop-blur ring-1 ring-white/20">
                <x-svg-icon name="globe" class="w-4 h-4" /> {{ __('166+ DAAD programs') }}
            </span>
            <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/15 backdrop-blur ring-1 ring-white/20">
                <x-svg-icon name="currency-euro" class="w-4 h-4" /> {{ __('300-2,150 €/month range') }}
            </span>
            <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/15 backdrop-blur ring-1 ring-white/20">
                <x-svg-icon name="calendar" class="w-4 h-4" /> {{ __('Applications open year-round') }}
            </span>
        </div>
    </div>
</section>

<section class="bg-gray-50 py-12">
    <div class="max-w-[1400px] mx-auto px-4 space-y-10">

        {{-- DAAD KART --}}
        <a href="{{ route('scholarships.daad') }}"
           class="group block bg-white rounded-2xl border border-gray-200 hover:border-primary-500 hover:shadow-xl transition-all overflow-hidden">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-0">
                <div class="bg-gradient-to-br from-blue-600 via-blue-500 to-cyan-400 p-8 text-white">
                    <div class="text-6xl mb-3">🇩🇪</div>
                    <h2 class="text-2xl font-extrabold">DAAD</h2>
                    <p class="text-sm text-blue-100 mt-2">Deutscher Akademischer Austauschdienst</p>
                </div>
                <div class="md:col-span-2 p-8">
                    <h3 class="text-xl font-bold text-gray-900 group-hover:text-primary-600 mb-2">{{ __('DAAD Scholarships — Complete Guide for International Students') }}</h3>
                    <p class="text-gray-600 mb-4">
                        {{ __('Germany\'s official academic exchange service — the broadest scholarship program for international students. 8 categories from bachelor to postdoc, from summer school to artist grants.') }}
                    </p>
                    <div class="flex flex-wrap gap-2 text-xs">
                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-blue-50 text-blue-700"><x-svg-icon name="academic-cap" class="w-3.5 h-3.5" /> {{ __('Master 934 €/month') }}</span>
                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-purple-50 text-purple-700"><x-svg-icon name="beaker" class="w-3.5 h-3.5" /> {{ __('PhD 1,300 €/month') }}</span>
                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-emerald-50 text-emerald-700"><x-svg-icon name="target" class="w-3.5 h-3.5" /> {{ __('Postdoc 2,150 €/month') }}</span>
                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-amber-50 text-amber-700"><x-svg-icon name="sparkles" class="w-3.5 h-3.5" /> {{ __('Summer school') }}</span>
                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-rose-50 text-rose-700"><x-svg-icon name="paint-brush" class="w-3.5 h-3.5" /> {{ __('Artists') }}</span>
                    </div>
                    <span class="inline-flex items-center gap-1 mt-5 text-primary-600 group-hover:text-primary-700 font-semibold">
                        {{ __('Go to detailed guide →') }}
                    </span>
                </div>
            </div>
        </a>

        {{-- DİĞER BURS KAYNAKLARI --}}
        <section>
            <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ __('Other Major Scholarship Sources') }}</h2>
            <p class="text-gray-600 mb-5">{{ __('Scholarship programs valid in Germany beyond DAAD, open to international students.') }}</p>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                {{-- Erasmus+ --}}
                <a href="https://erasmus-plus.ec.europa.eu" target="_blank" rel="noopener"
                   class="group bg-white rounded-xl border border-gray-200 hover:border-primary-500 hover:shadow-md transition p-6">
                    <div class="text-4xl mb-3">🇪🇺</div>
                    <h3 class="font-bold text-gray-900 group-hover:text-primary-600 mb-1">Erasmus+ Programme</h3>
                    <p class="text-sm text-gray-600 mb-3">{{ __('EU exchange program. Study a semester/year in Germany from your home university.') }}</p>
                    <p class="text-xs text-gray-500">{{ __('350-550 €/month (by country tier)') }}</p>
                </a>

                {{-- Deutschlandstipendium --}}
                <a href="https://www.deutschlandstipendium.de" target="_blank" rel="noopener"
                   class="group bg-white rounded-xl border border-gray-200 hover:border-primary-500 hover:shadow-md transition p-6">
                    <div class="text-4xl mb-3">🇩🇪</div>
                    <h3 class="font-bold text-gray-900 group-hover:text-primary-600 mb-1">Deutschlandstipendium</h3>
                    <p class="text-sm text-gray-600 mb-3">{{ __('Half government, half private sponsor. Based on achievement and social engagement. Open to students of every nationality.') }}</p>
                    <p class="text-xs text-gray-500">{{ __('300 €/month, 1-2 semesters') }}</p>
                </a>

                {{-- Friedrich-Ebert --}}
                <a href="https://www.fes.de/studienfoerderung" target="_blank" rel="noopener"
                   class="group bg-white rounded-xl border border-gray-200 hover:border-primary-500 hover:shadow-md transition p-6">
                    <div class="inline-flex items-center justify-center w-12 h-12 mb-3 rounded-lg bg-rose-50 text-rose-600"><x-svg-icon name="heart" class="w-7 h-7" /></div>
                    <h3 class="font-bold text-gray-900 group-hover:text-primary-600 mb-1">Friedrich-Ebert-Stiftung</h3>
                    <p class="text-sm text-gray-600 mb-3">{{ __('Foundation close to SPD. For students aligned with social-democratic values — bachelor + master + PhD.') }}</p>
                    <p class="text-xs text-gray-500">{{ __('934 €/month (master) - 1,300 € (PhD)') }}</p>
                </a>

                {{-- Heinrich-Böll --}}
                <a href="https://www.boell.de/en/foundation/scholarships" target="_blank" rel="noopener"
                   class="group bg-white rounded-xl border border-gray-200 hover:border-primary-500 hover:shadow-md transition p-6">
                    <div class="inline-flex items-center justify-center w-12 h-12 mb-3 rounded-lg bg-emerald-50 text-emerald-600"><x-svg-icon name="leaf" class="w-7 h-7" /></div>
                    <h3 class="font-bold text-gray-900 group-hover:text-primary-600 mb-1">Heinrich-Böll-Stiftung</h3>
                    <p class="text-sm text-gray-600 mb-3">{{ __('Foundation linked to the Greens. Focus on environment, democracy and diversity. Bachelor + master + PhD.') }}</p>
                    <p class="text-xs text-gray-500">{{ __('934-1,300 €/month') }}</p>
                </a>

                {{-- Konrad-Adenauer --}}
                <a href="https://www.kas.de/en/scholarship-programmes" target="_blank" rel="noopener"
                   class="group bg-white rounded-xl border border-gray-200 hover:border-primary-500 hover:shadow-md transition p-6">
                    <div class="inline-flex items-center justify-center w-12 h-12 mb-3 rounded-lg bg-blue-50 text-blue-600"><x-svg-icon name="building-office" class="w-7 h-7" /></div>
                    <h3 class="font-bold text-gray-900 group-hover:text-primary-600 mb-1">Konrad-Adenauer-Stiftung</h3>
                    <p class="text-sm text-gray-600 mb-3">{{ __('Close to CDU. Liberal democracy, economics, social responsibility values. Bachelor + master + PhD.') }}</p>
                    <p class="text-xs text-gray-500">{{ __('934-1,300 €/month') }}</p>
                </a>

                {{-- Hanns-Seidel --}}
                <a href="https://www.hss.de/scholarships/" target="_blank" rel="noopener"
                   class="group bg-white rounded-xl border border-gray-200 hover:border-primary-500 hover:shadow-md transition p-6">
                    <div class="inline-flex items-center justify-center w-12 h-12 mb-3 rounded-lg bg-indigo-50 text-indigo-600"><x-svg-icon name="flag" class="w-7 h-7" /></div>
                    <h3 class="font-bold text-gray-900 group-hover:text-primary-600 mb-1">Hanns-Seidel-Stiftung</h3>
                    <p class="text-sm text-gray-600 mb-3">{{ __('Linked to CSU, focused on Bavaria. Bachelor/master/PhD for international students.') }}</p>
                    <p class="text-xs text-gray-500">{{ __('934-1,300 €/month') }}</p>
                </a>

                {{-- Rosa-Luxemburg --}}
                <a href="https://www.rosalux.de/en/foundation/scholarship-department" target="_blank" rel="noopener"
                   class="group bg-white rounded-xl border border-gray-200 hover:border-primary-500 hover:shadow-md transition p-6">
                    <div class="inline-flex items-center justify-center w-12 h-12 mb-3 rounded-lg bg-rose-50 text-rose-600"><x-svg-icon name="scale" class="w-7 h-7" /></div>
                    <h3 class="font-bold text-gray-900 group-hover:text-primary-600 mb-1">Rosa-Luxemburg-Stiftung</h3>
                    <p class="text-sm text-gray-600 mb-3">{{ __('Close to Die Linke. Focus on equality and global justice. Master + PhD heavy.') }}</p>
                    <p class="text-xs text-gray-500">{{ __('934-1,300 €/month') }}</p>
                </a>

                {{-- Studienstiftung --}}
                <a href="https://www.studienstiftung.de/en/" target="_blank" rel="noopener"
                   class="group bg-white rounded-xl border border-gray-200 hover:border-primary-500 hover:shadow-md transition p-6">
                    <div class="inline-flex items-center justify-center w-12 h-12 mb-3 rounded-lg bg-amber-50 text-amber-600"><x-svg-icon name="trophy" class="w-7 h-7" /></div>
                    <h3 class="font-bold text-gray-900 group-hover:text-primary-600 mb-1">Studienstiftung des deutschen Volkes</h3>
                    <p class="text-sm text-gray-600 mb-3">{{ __('Germany\'s most prestigious academic scholarship. Only the top 1% of students. Limited slots for international students.') }}</p>
                    <p class="text-xs text-gray-500">{{ __('300 €/month + book + Reisekosten') }}</p>
                </a>

                {{-- AvH --}}
                <a href="https://www.humboldt-foundation.de" target="_blank" rel="noopener"
                   class="group bg-white rounded-xl border border-gray-200 hover:border-primary-500 hover:shadow-md transition p-6">
                    <div class="inline-flex items-center justify-center w-12 h-12 mb-3 rounded-lg bg-purple-50 text-purple-600"><x-svg-icon name="beaker" class="w-7 h-7" /></div>
                    <h3 class="font-bold text-gray-900 group-hover:text-primary-600 mb-1">Alexander von Humboldt Foundation</h3>
                    <p class="text-sm text-gray-600 mb-3">{{ __('Postdoc and senior researchers only. For top researchers from all over the world.') }}</p>
                    <p class="text-xs text-gray-500">{{ __('2,670 €/month + research support') }}</p>
                </a>
            </div>
        </section>

        {{-- ARAMA İPUÇLARI --}}
        <section class="bg-amber-50 border border-amber-200 rounded-2xl p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-3 inline-flex items-center gap-2"><x-svg-icon name="search" class="w-5 h-5 text-amber-700" /> {{ __('Tips for Scholarship Search') }}</h2>
            <ul class="space-y-2 text-sm text-gray-700">
                <li class="flex items-start gap-2"><span class="text-amber-600">▸</span><strong>{{ __('Apply early') }}</strong> — {{ __('Most DAAD scholarship deadlines are 12-15 months before the target term.') }}</li>
                <li class="flex items-start gap-2"><span class="text-amber-600">▸</span>{{ __('Use') }} <strong>Stipendienlotse</strong> — <a href="https://www.stipendienlotse.de" target="_blank" class="text-primary-600 hover:underline">stipendienlotse.de</a> {{ __('is the federal government platform with fuzzy search.') }}</li>
                <li class="flex items-start gap-2"><span class="text-amber-600">▸</span><strong>{{ __('Mentor letter') }}</strong> — {{ __('Most scholarships require a professor reference letter — ask 2 weeks in advance.') }}</li>
                <li class="flex items-start gap-2"><span class="text-amber-600">▸</span><strong>{{ __('Apply to several') }}</strong> — {{ __('If DAAD rejects, try a foundation; if foundation rejects, try Erasmus+. Always have a plan B.') }}</li>
                <li class="flex items-start gap-2"><span class="text-amber-600">▸</span><strong>{{ __('Ask at the German university') }}</strong> — {{ __('The Auslandsamt office of your target university may have its own scholarships.') }}</li>
            </ul>
        </section>

    </div>
</section>

<div class="max-w-4xl mx-auto px-4">
    <x-featured-snippet
        :question="__('How can I get a scholarship to study in Germany?')"
        :answer="__('Main pathways: DAAD (academic merit + need, ~934 EUR/month + tuition + travel), Deutschlandstipendium (300 EUR/month, merit-based), and political foundations (Heinrich-Böll, Konrad-Adenauer, Friedrich-Ebert, Rosa-Luxemburg) that value social engagement alongside grades. Apply 9–15 months before your programme start.')"
        :steps="[
            ['title' => __('Identify matching scholarships'), 'description' => __('Filter by your field, degree level, and country eligibility.')],
            ['title' => __('Prepare a strong motivation letter'), 'description' => __('Highlight academic plan + social engagement + return contribution.')],
            ['title' => __('Collect required documents'), 'description' => __('Transcripts, language certificate, CV, recommendation letters, project proposal.')],
            ['title' => __('Apply early (9–15 months ahead)'), 'description' => __('Most scholarship deadlines close long before programme start.')],
            ['title' => __('Combine multiple smaller scholarships if needed'), 'description' => __('Deutschlandstipendium + foundation top-ups can stack legally.')],
        ]"
    />
</div>

<x-faq-section
    :title="__('Frequently Asked Questions about Scholarships')"
    :subtitle="__('Funding paths for international students in Germany')"
    :faqs="\App\Support\PageFaq::forScholarships()"
/>

@endsection
