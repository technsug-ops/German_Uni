@extends('layouts.app')

@section('title', __('Most Preferred Fields & Universities in Germany') . ' — ' . brand('name'))

<x-seo
    :title="__('Most Preferred Fields & Universities in Germany')"
    :description="__('Which fields and universities do international students in Germany prefer most? Compare top universities by world ranking and monthly cost with our interactive explorer.')"
/>

<x-json-ld :data="\App\Support\Seo::breadcrumbs([
    ['name' => __('Home'), 'url' => route('home')],
    ['name' => __('Universities'), 'url' => route('universities.index')],
    ['name' => __('Most Preferred'), 'url' => route('popular-universities')],
])" />

@php
    $itemList = [
        '@context' => 'https://schema.org',
        '@type'    => 'ItemList',
        'name'     => __('Most Preferred Universities in Germany'),
        'itemListElement' => collect($explorer)->take(20)->values()->map(fn ($u, $i) => [
            '@type'    => 'ListItem',
            'position' => $i + 1,
            'url'      => route('universities.show', $u['slug']),
            'name'     => $u['full_name'],
        ])->all(),
    ];
@endphp
<x-json-ld :data="$itemList" />

@section('content')

{{-- ─────────────── HERO ─────────────── --}}
<section class="bg-gradient-to-br from-primary-800 via-primary-700 to-accent-600 text-white">
    <div class="max-w-[1100px] mx-auto px-4 py-12 md:py-16">
        <nav class="text-sm text-white/75 mb-3">
            <a href="{{ route('home') }}" class="hover:text-white">{{ __('Home') }}</a>
            <span class="mx-2 opacity-60">›</span>
            <a href="{{ route('universities.index') }}" class="hover:text-white">{{ __('Universities') }}</a>
            <span class="mx-2 opacity-60">›</span>
            <span class="text-white">{{ __('Most Preferred') }}</span>
        </nav>
        <h1 class="text-3xl md:text-5xl font-extrabold leading-tight drop-shadow mb-3">
            {{ __('Most Preferred Fields & Universities in Germany') }}
        </h1>
        <p class="text-lg text-white/90 max-w-3xl">
            {{ __('Germany is one of Europe\'s most popular destinations for international students — thanks to free or low-tuition universities and strong career prospects. Based on DAAD and higher-education data, international students concentrate in a handful of fields and institutions.') }}
        </p>
    </div>
</section>

<div class="max-w-[1100px] mx-auto px-4 py-10 space-y-14">

    {{-- ─────────────── EN ÇOK TERCİH EDİLEN BÖLÜMLER ─────────────── --}}
    <section>
        <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ __('Most Preferred Fields') }}</h2>
        <p class="text-gray-600 mb-5 max-w-3xl">{{ __('Students tend to choose fields with strong career prospects and a wide choice of English-taught programs:') }}</p>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @php
                $fields = [
                    ['🛠️', __('Engineering'), __('Mechanical, Computer, Electrical-Electronics, Automotive and Industrial Engineering are the most in-demand. Germany\'s automotive and heavy-industry base makes these especially attractive.')],
                    ['📊', __('Business, Economics & Management'), __('Popular for post-graduation careers at global companies and the wide choice of English-taught MBA and business programs.')],
                    ['🔬', __('Mathematics & Natural Sciences'), __('Physics, Chemistry, Biology and Biotechnology — a magnet for researchers thanks to institutions like the Max Planck Society.')],
                    ['🩺', __('Medicine & Health Sciences'), __('Medicine, Dentistry and Psychology are prestigious and highly sought — but admission (Numerus Clausus, NC) is among the toughest.')],
                    ['🏛️', __('Architecture & Design'), __('Urban planning and sustainable architecture programs attract strong, steady interest.')],
                ];
            @endphp
            @foreach ($fields as [$icon, $title, $desc])
                <div class="bg-white border border-gray-200 rounded-xl p-5">
                    <h3 class="font-bold text-gray-900 mb-1 inline-flex items-center gap-2"><span aria-hidden="true">{{ $icon }}</span> {{ $title }}</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">{{ $desc }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- ─────────────── ALMAN ÜNİVERSİTELERİ GEZGİNİ (etkileşimli) ─────────────── --}}
    <section
        x-data="{
            q: '',
            cat: '',
            sort: 'rank',
            rows: @js($explorer),
            get filtered() {
                let r = this.rows.filter(u => {
                    let okCat = !this.cat || u.type_label === this.cat;
                    let term = this.q.trim().toLowerCase();
                    let okQ = !term || (u.name + ' ' + u.full_name + ' ' + (u.city||'')).toLowerCase().includes(term);
                    return okCat && okQ;
                });
                r.sort((a, b) => this.sort === 'cost'
                    ? ((a.cost ?? 99999) - (b.cost ?? 99999))
                    : (a.rank - b.rank));
                return r;
            }
        }"
        class="scroll-mt-20" id="explorer">
        <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ __('German Universities Explorer') }}</h2>
        <p class="text-gray-600 mb-4 max-w-3xl">
            {{ __('Compare the most preferred universities and sort them by world ranking or estimated monthly cost. World ranking is the QS World University Ranking; monthly cost is an estimate (student room rent + living expenses) and varies by lifestyle.') }}
        </p>

        {{-- Kontroller --}}
        <div class="flex flex-col sm:flex-row gap-3 mb-4">
            <input type="search" x-model="q"
                   placeholder="{{ __('Search university or city') }}"
                   class="flex-1 rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
            <select x-model="cat" class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm bg-white focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                <option value="">{{ __('All categories') }}</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat }}">{{ __($cat) }}</option>
                @endforeach
            </select>
            <div class="inline-flex rounded-lg border border-gray-300 overflow-hidden text-sm font-semibold">
                <button type="button" @click="sort = 'rank'"
                        :class="sort === 'rank' ? 'bg-primary-600 text-white' : 'bg-white text-gray-600'"
                        class="px-4 py-2.5 transition">{{ __('World ranking') }}</button>
                <button type="button" @click="sort = 'cost'"
                        :class="sort === 'cost' ? 'bg-primary-600 text-white' : 'bg-white text-gray-600'"
                        class="px-4 py-2.5 transition border-l border-gray-300">{{ __('Monthly cost') }}</button>
            </div>
        </div>

        {{-- Tablo --}}
        <div class="overflow-x-auto border border-gray-200 rounded-xl bg-white">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 border-b border-gray-200 bg-gray-50">
                        <th class="px-4 py-3 font-semibold">{{ __('University') }}</th>
                        <th class="px-4 py-3 font-semibold">{{ __('City') }}</th>
                        <th class="px-4 py-3 font-semibold hidden md:table-cell">{{ __('Featured Fields') }}</th>
                        <th class="px-4 py-3 font-semibold whitespace-nowrap">{{ __('World Ranking') }}</th>
                        <th class="px-4 py-3 font-semibold whitespace-nowrap">{{ __('Monthly Cost') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="u in filtered" :key="u.slug">
                        <tr class="border-b border-gray-100 last:border-0 hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <a :href="u.url"
                                   class="font-semibold text-primary-700 hover:underline" x-text="u.name"></a>
                            </td>
                            <td class="px-4 py-3 text-gray-700" x-text="u.city || '—'"></td>
                            <td class="px-4 py-3 text-gray-500 hidden md:table-cell" x-text="u.fields.length ? u.fields.join(', ') : '—'"></td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="font-bold text-gray-900">#<span x-text="u.rank"></span></span>
                                <span class="text-xs text-gray-400">QS</span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap font-semibold text-gray-900" x-text="u.cost ? '€' + u.cost : '—'"></td>
                        </tr>
                    </template>
                    <tr x-show="filtered.length === 0">
                        <td colspan="5" class="px-4 py-8 text-center text-gray-400">{{ __('No universities match your filters.') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <p class="text-xs text-gray-500 mt-2">
            <span x-text="filtered.length"></span> {{ __('universities listed') }} ·
            {{ __('Ranking: QS World University Rankings. Monthly cost is an estimate and varies by city and lifestyle.') }}
        </p>
    </section>

    {{-- ─────────────── ÜLKE TRENDLERİ ─────────────── --}}
    <section>
        <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ __('Where international students come from — and what they choose') }}</h2>
        <p class="text-gray-600 mb-5 max-w-3xl">{{ __('Germany now hosts over 400,000 international students, and a few countries dominate the market. Their goals differ sharply by region:') }}</p>
        <div class="overflow-x-auto border border-gray-200 rounded-xl bg-white">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 border-b border-gray-200 bg-gray-50">
                        <th class="px-4 py-3 font-semibold">{{ __('Source Country') }}</th>
                        <th class="px-4 py-3 font-semibold">{{ __('Main focus in Germany') }}</th>
                        <th class="px-4 py-3 font-semibold">{{ __('Preferred language & level') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $trends = [
                            [__('India'), __('IT, Software, Electrical Engineering, Data Science'), __('Almost entirely English, mostly Master\'s programs.')],
                            [__('China'), __('Mechanical Engineering, Business/Economics, Materials Science'), __('Both English and German; balanced between engineering and business.')],
                            [__('Syria & Middle East'), __('Medicine, Dentistry, Civil Engineering, Architecture'), __('Mainly German, at Bachelor\'s level.')],
                            [__('Europe (EU) & USA'), __('Humanities, Political Science, Arts, Law'), __('Mostly German programs; Erasmus/exchange and social sciences.')],
                        ];
                    @endphp
                    @foreach ($trends as [$country, $focus, $lang])
                        <tr class="border-b border-gray-100 last:border-0">
                            <td class="px-4 py-3 font-semibold text-gray-900 whitespace-nowrap">{{ $country }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $focus }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $lang }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

    {{-- ─────────────── İKİ STRATEJİ ─────────────── --}}
    <section>
        <h2 class="text-2xl font-bold text-gray-900 mb-5">{{ __('Two strategies international students follow') }}</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div class="bg-primary-50 border border-primary-200 rounded-xl p-5">
                <h3 class="font-bold text-primary-900 mb-2 inline-flex items-center gap-2"><span aria-hidden="true">🏆</span> {{ __('Global prestige seekers') }}</h3>
                <p class="text-sm text-gray-700 leading-relaxed">
                    {{ __('Students aiming for academic careers or jobs at global giants (Siemens, BMW, SAP) cluster at TU9 research universities like TUM, RWTH Aachen and KIT. Their world-ranked degrees carry weight back home.') }}
                </p>
            </div>
            <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-5">
                <h3 class="font-bold text-emerald-900 mb-2 inline-flex items-center gap-2"><span aria-hidden="true">🚀</span> {{ __('Fast track to work') }}</h3>
                <p class="text-sm text-gray-700 leading-relaxed">
                    {{ __('The fastest-rising trend: students who want to stay and start working at mid-sized industrial firms choose Universities of Applied Sciences (Fachhochschule) — e.g. Rhine-Waal University of Applied Sciences, with over 50% international students and many fully English-taught programs.') }}
                </p>
            </div>
        </div>
    </section>

    {{-- ─────────────── İNGİLİZCE PROGRAMLARIN ETKİSİ ─────────────── --}}
    <section class="bg-blue-50 border border-blue-200 rounded-xl p-5 md:p-6">
        <h2 class="font-bold text-gray-900 text-lg mb-2 inline-flex items-center gap-2">🇬🇧 {{ __('The role of English-taught programs') }}</h2>
        <p class="text-sm text-gray-700 leading-relaxed mb-3">
            {{ __('The single biggest factor shaping international students\' choice of field and university is the availability of English-taught programs. Business and Engineering are the most popular fields precisely because they offer the most English-taught programs.') }}
        </p>
        <p class="text-sm text-gray-700 leading-relaxed">
            <strong>{{ __('Bachelor\'s vs Master\'s:') }}</strong>
            {{ __('English-taught Master\'s programs are far more common than Bachelor\'s. Most English Bachelor\'s are at private or international universities, while public universities offer the widest English Master\'s selection.') }}
        </p>
    </section>

    {{-- ─────────────── CTA ─────────────── --}}
    <section class="flex flex-wrap gap-3 justify-center">
        <a href="{{ route('universities.index') }}" class="inline-flex items-center gap-2 bg-primary-600 hover:bg-primary-700 text-white font-bold px-6 py-3 rounded-lg shadow-md transition">
            {{ __('Explore all universities') }} <x-svg-icon name="arrow-right" class="w-4 h-4" />
        </a>
        <a href="{{ route('discover.english') }}" class="inline-flex items-center gap-2 bg-white border border-gray-300 hover:border-primary-400 text-gray-800 font-bold px-6 py-3 rounded-lg transition">
            {{ __('Browse English-taught programs') }}
        </a>
        <a href="{{ route('admission-free.index') }}" class="inline-flex items-center gap-2 bg-white border border-gray-300 hover:border-primary-400 text-gray-800 font-bold px-6 py-3 rounded-lg transition">
            {{ __('Find NC-free programs') }}
        </a>
    </section>

    {{-- ─────────────── SSS ─────────────── --}}
    <x-faq-section
        :title="__('Frequently Asked Questions — Popular fields & universities')"
        :faqs="[
            ['q' => __('Which field is most preferred by international students in Germany?'), 'a' => __('Engineering and Business/Economics, largely because they offer the most English-taught programs and the strongest career prospects.')],
            ['q' => __('Which universities are most preferred?'), 'a' => __('Research-focused TU9 universities (TUM, RWTH Aachen, KIT and others) for prestige, and Universities of Applied Sciences (Fachhochschulen) for a fast route into the job market.')],
            ['q' => __('How much does it cost to study in Germany per month?'), 'a' => __('Most public universities are tuition-free (only a small semester contribution). Living costs typically range €900–€1,300 per month depending on the city — rent is the biggest factor.')],
        ]"
    />
</div>
@endsection
