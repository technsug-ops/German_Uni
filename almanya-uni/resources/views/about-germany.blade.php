@extends('layouts.app')

@section('title', __('Study in Germany — Why Germany, City Map, and 10 Reasons') . ' — ' . brand('name'))

<x-seo
    :title="__('Study in Germany — Why Germany?')"
    :description="__('10 reasons students choose Germany + a city-industry map. :unis universities, :programs programs, :en English-taught programs.', ['unis' => $stats['universities'], 'programs' => number_format($stats['programs'], 0, ',', '.'), 'en' => $stats['programs_en']])"
/>

@section('content')

{{-- HERO --}}
<section class="bg-gradient-to-br from-primary-700 via-primary-600 to-accent-500 text-white">
    <div class="max-w-[1400px] mx-auto px-4 py-12 md:py-16">
        <nav class="text-sm text-primary-100 mb-3">
            <a href="/" class="hover:text-white">{{ __('Home') }}</a>
            <span class="mx-2 opacity-50">›</span>
            <span class="text-white">{{ __('Study in Germany') }}</span>
        </nav>
        <h1 class="text-3xl md:text-5xl font-extrabold leading-tight drop-shadow mb-4">
            🇩🇪 {{ __('Why Study in Germany?') }}
        </h1>
        <p class="text-lg md:text-xl text-primary-50 max-w-3xl leading-relaxed">
            @if (app()->getLocale() === 'tr')
                Avrupa'nın en büyük ekonomisi, ücretsiz devlet üniversiteleri, 18 ay iş arama vizesi
                ve {{ number_format(3_000_000, 0, ',', '.') }}+ Türk topluluğu. Almanya'da öğrenci olmanın getirdiği
                <strong class="text-white">sayılarla kanıtlanmış</strong> avantajlar.
            @else
                {{ __('Europe\'s largest economy, free public universities, 18-month job seeker visa, and a multicultural international community.') }}
                <strong class="text-white">{{ __('Numbers-proven advantages') }}</strong> {{ __('of being a student in Germany.') }}
            @endif
        </p>

        {{-- Quick stats --}}
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3 mt-8">
            <div class="bg-white/15 backdrop-blur rounded-lg p-3 ring-1 ring-white/20">
                <p class="text-2xl font-bold">{{ $stats['universities'] }}</p>
                <p class="text-xs text-primary-100 mt-0.5">{{ __('Universities') }}</p>
            </div>
            <div class="bg-white/15 backdrop-blur rounded-lg p-3 ring-1 ring-white/20">
                <p class="text-2xl font-bold">{{ number_format($stats['programs'], 0, ',', '.') }}</p>
                <p class="text-xs text-primary-100 mt-0.5">{{ __('Total Programs') }}</p>
            </div>
            <div class="bg-white/15 backdrop-blur rounded-lg p-3 ring-1 ring-white/20">
                <p class="text-2xl font-bold">{{ number_format($stats['programs_en'], 0, ',', '.') }}</p>
                <p class="text-xs text-primary-100 mt-0.5">{{ __('English Programs') }}</p>
            </div>
            <div class="bg-white/15 backdrop-blur rounded-lg p-3 ring-1 ring-white/20">
                <p class="text-2xl font-bold">{{ $stats['cities'] }}</p>
                <p class="text-xs text-primary-100 mt-0.5">{{ __('Student Cities') }}</p>
            </div>
            <div class="bg-white/15 backdrop-blur rounded-lg p-3 ring-1 ring-white/20">
                <p class="text-2xl font-bold">{{ $stats['states'] }}</p>
                <p class="text-xs text-primary-100 mt-0.5">{{ __('Federal States') }}</p>
            </div>
            <div class="bg-white/15 backdrop-blur rounded-lg p-3 ring-1 ring-white/20">
                <p class="text-2xl font-bold">{{ $stats['scholarships'] }}</p>
                <p class="text-xs text-primary-100 mt-0.5">{{ __('Scholarships') }}</p>
            </div>
        </div>
    </div>
</section>

{{-- 10 REASONS --}}
<section class="max-w-[1400px] mx-auto px-4 py-14">
    <div class="text-center mb-10">
        <h2 class="text-2xl md:text-4xl font-bold text-gray-900 mb-3">{{ __('10 Reasons for International Students') }}</h2>
        <p class="text-gray-600 max-w-2xl mx-auto">{{ __('Not marketing language — backed by numbers, laws, and real community experience.') }}</p>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach ($reasons as $i => $r)
            <article class="bg-white border border-gray-200 hover:border-primary-400 hover:shadow-md transition rounded-xl p-5 flex gap-4">
                <div class="shrink-0 inline-flex items-center justify-center w-12 h-12 rounded-lg bg-primary-50 text-primary-600">{!! e_icon($r['icon'], 'w-7 h-7') !!}</div>
                <div class="min-w-0">
                    <h3 class="font-bold text-gray-900 mb-1 leading-tight">
                        <span class="text-xs text-primary-600 font-extrabold mr-1">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}.</span>
                        {{ $r['title'] }}
                    </h3>
                    <p class="text-sm text-gray-600 leading-relaxed">{{ $r['desc'] }}</p>
                </div>
            </article>
        @endforeach
    </div>
</section>

{{-- CITY-INDUSTRY MAP --}}
<section class="bg-gray-50 border-y border-gray-200 py-14">
    <div class="max-w-[1400px] mx-auto px-4">
        <div class="text-center mb-10">
            <h2 class="text-2xl md:text-4xl font-bold text-gray-900 mb-3 inline-flex items-center gap-3 justify-center"><x-svg-icon name="map" class="w-8 h-8 text-primary-600" /> {{ __('City-Industry Map') }}</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">
                {{ __('In which city should you study your field? Germany\'s industry map is the most important input for your career plan — because internships + jobs cluster by city.') }}
            </p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($cityIndustries as $ci)
                <a href="{{ route('cities.show', $ci['slug']) }}"
                   class="group block bg-white border border-gray-200 hover:border-primary-500 hover:shadow-lg transition rounded-xl overflow-hidden">
                    <div class="aspect-[16/9] bg-gradient-to-br from-primary-100 to-accent-100 relative overflow-hidden">
                        @if ($ci['image_url'])
                            <img src="{{ $ci['image_url'] }}" alt="{{ $ci['city'] }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                 loading="lazy">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <span class="text-5xl font-extrabold text-primary-300/60">{{ mb_substr($ci['city'], 0, 2) }}</span>
                            </div>
                        @endif
                        <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/70 to-transparent p-3">
                            <h3 class="text-white font-bold text-lg drop-shadow leading-tight">{{ $ci['city'] }}</h3>
                        </div>
                    </div>
                    <div class="p-4">
                        <div class="flex flex-wrap gap-1.5 mb-2">
                            @foreach ($ci['industries'] as $ind)
                                <span class="text-[11px] px-2 py-0.5 rounded bg-primary-50 text-primary-700 font-medium">{{ $ind }}</span>
                            @endforeach
                        </div>
                        <p class="text-sm text-gray-600 leading-relaxed line-clamp-3">{{ $ci['desc'] }}</p>
                        <p class="text-xs font-semibold text-primary-600 group-hover:text-primary-800 mt-3">{{ __(':city guide', ['city' => $ci['city']]) }} →</p>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="text-center mt-8">
            <a href="{{ route('cities.index') }}"
               class="inline-flex items-center gap-2 bg-primary-600 hover:bg-primary-700 text-white px-6 py-3 rounded-lg font-semibold transition shadow-sm">
                <x-svg-icon name="building-office" class="w-5 h-5" /> {{ __('All student cities (:count) →', ['count' => $stats['cities']]) }}
            </a>
        </div>
    </div>
</section>

{{-- ACADEMIC STRUCTURE --}}
<section class="max-w-[1400px] mx-auto px-4 py-14">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-start">
        <div>
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4 inline-flex items-center gap-2"><x-svg-icon name="academic-cap" class="w-7 h-7 text-primary-600" /> {{ __('Higher Education Structure') }}</h2>
            <div class="space-y-4">
                <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-5">
                    <h3 class="font-bold text-emerald-900 mb-2 inline-flex items-center gap-2"><x-svg-icon name="check-circle" class="w-5 h-5 text-emerald-600" /> {{ __('Public Universities (~85%)') }}</h3>
                    <p class="text-sm text-emerald-800 leading-relaxed">
                        {{ __('Tuition fee is') }} <strong>{{ __('FREE') }}</strong>, {{ __('only') }} <strong>€150-350/{{ __('semester') }}</strong> {{ __('semester contribution.') }}
                        {{ __('This includes campus services and regional public transport pass.') }}
                    </p>
                </div>
                <div class="bg-amber-50 border border-amber-200 rounded-xl p-5">
                    <h3 class="font-bold text-amber-900 mb-2 inline-flex items-center gap-2"><x-svg-icon name="currency-euro" class="w-5 h-5 text-amber-600" /> {{ __('Private Universities (~15%)') }}</h3>
                    <p class="text-sm text-amber-800 leading-relaxed">
                        <strong>€6,000-15,000/{{ __('year') }}</strong> {{ __('tuition fee. More English-taught programs, more flexible admissions. IU, Constructor, Hertie School are well-known.') }}
                    </p>
                </div>
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-5">
                    <h3 class="font-bold text-blue-900 mb-2 inline-flex items-center gap-2"><x-svg-icon name="wrench-screwdriver" class="w-5 h-5 text-blue-600" /> {{ __('Universities of Applied Sciences (FH/HAW)') }}</h3>
                    <p class="text-sm text-blue-800 leading-relaxed">
                        {{ __('Practice-oriented colleges. Mandatory industry internships for engineering + business. Employment rate is') }}
                        <strong>{{ __('15-20% higher') }}</strong> {{ __('than classical universities.') }}
                    </p>
                </div>
            </div>
        </div>

        <div>
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4 inline-flex items-center gap-2"><x-svg-icon name="banknotes" class="w-7 h-7 text-primary-600" /> {{ __('Annual Cost Summary') }}</h2>
            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="text-left px-4 py-3 font-semibold text-gray-700">{{ __('Item') }}</th>
                            <th class="text-right px-4 py-3 font-semibold text-gray-700">EUR</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr>
                            <td class="px-4 py-3 text-gray-800">{{ __('Blocked account (required for visa)') }}</td>
                            <td class="px-4 py-3 text-right font-semibold">€11,904</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3 text-gray-800">{{ __('Tuition (public uni)') }}</td>
                            <td class="px-4 py-3 text-right font-semibold text-emerald-600">€0</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3 text-gray-800">{{ __('Semester fee') }}</td>
                            <td class="px-4 py-3 text-right font-semibold">€300-700</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3 text-gray-800">{{ __('Health insurance') }}</td>
                            <td class="px-4 py-3 text-right font-semibold">€1,500</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3 text-gray-800">{{ __('Living (rent + food + transport)') }}</td>
                            <td class="px-4 py-3 text-right font-semibold">€10,000-13,000</td>
                        </tr>
                        <tr class="bg-primary-50 font-bold">
                            <td class="px-4 py-3 text-primary-900">{{ __('Minimum total') }}</td>
                            <td class="px-4 py-3 text-right text-primary-700">~€13,500</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <p class="text-xs text-gray-500 mt-2">{{ __('Blocked account is withdrawn after arrival — not a net expense.') }}</p>
            <div class="mt-4 flex flex-wrap gap-2">
                <a href="{{ route('tools.visa-cost') }}" class="inline-flex items-center gap-1.5 text-sm px-3 py-1.5 rounded-full bg-primary-50 text-primary-700 hover:bg-primary-100 font-semibold"><x-svg-icon name="banknotes" class="w-3.5 h-3.5" /> {{ __('Calculate Visa Cost') }}</a>
                <a href="{{ route('tools.budget-planner') }}" class="inline-flex items-center gap-1.5 text-sm px-3 py-1.5 rounded-full bg-primary-50 text-primary-700 hover:bg-primary-100 font-semibold"><x-svg-icon name="chart-bar" class="w-3.5 h-3.5" /> {{ __('Budget Planner') }}</a>
            </div>
        </div>
    </div>
</section>

{{-- COMMUNITY SECTION — locale-aware --}}
@if (app()->getLocale() === 'tr')
    {{-- TR: Türk öğrenci topluluğu --}}
    <section class="bg-gradient-to-r from-red-600 to-rose-500 text-white py-14">
        <div class="max-w-[1400px] mx-auto px-4">
            <h2 class="text-2xl md:text-4xl font-bold mb-4">🇹🇷 Almanya'daki Türk Topluluğu</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                <div class="bg-white/15 backdrop-blur rounded-lg p-5">
                    <p class="text-3xl font-bold mb-1">3M+</p>
                    <p class="text-sm text-red-100">Almanya'da yaşayan Türk vatandaşı</p>
                </div>
                <div class="bg-white/15 backdrop-blur rounded-lg p-5">
                    <p class="text-3xl font-bold mb-1">~50K</p>
                    <p class="text-sm text-red-100">Türk öğrenci (2026)</p>
                </div>
                <div class="bg-white/15 backdrop-blur rounded-lg p-5">
                    <p class="text-3xl font-bold mb-1">130+</p>
                    <p class="text-sm text-red-100">Türk öğrenci derneği & ATÖM</p>
                </div>
            </div>
            <p class="text-red-50 leading-relaxed text-lg max-w-3xl">
                Yalnız değilsin. Her büyük şehirde Türk öğrenci derneği, Türk marketi, kebapçı, kahvehane var.
                <strong class="text-white">Berlin, Köln, Frankfurt, Stuttgart, Hamburg</strong> en kalabalık Türk diasporasına ev sahipliği yapıyor —
                bu hem kültürel destek hem de Türkçe iş ilanı + Türk işveren ağı demek.
            </p>
        </div>
    </section>
@else
    {{-- EN/DE/FR: Generic international student community --}}
    <section class="bg-gradient-to-r from-indigo-700 to-purple-600 text-white py-14">
        <div class="max-w-[1400px] mx-auto px-4">
            <h2 class="text-2xl md:text-4xl font-bold mb-4 inline-flex items-center gap-3"><x-svg-icon name="globe" class="w-8 h-8" /> {{ __('International Student Community in Germany') }}</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                <div class="bg-white/15 backdrop-blur rounded-lg p-5">
                    <p class="text-3xl font-bold mb-1">400K+</p>
                    <p class="text-sm text-indigo-100">{{ __('International students (2026)') }}</p>
                </div>
                <div class="bg-white/15 backdrop-blur rounded-lg p-5">
                    <p class="text-3xl font-bold mb-1">170+</p>
                    <p class="text-sm text-indigo-100">{{ __('Countries represented') }}</p>
                </div>
                <div class="bg-white/15 backdrop-blur rounded-lg p-5">
                    <p class="text-3xl font-bold mb-1">12M+</p>
                    <p class="text-sm text-indigo-100">{{ __('People with migration background') }}</p>
                </div>
            </div>
            <p class="text-indigo-50 leading-relaxed text-lg max-w-3xl">
                {{ __('Germany is Europe\'s #1 destination for international students — third worldwide after the US and UK. Every major city has active international student associations, cultural centers, and language courses. 95% of universities provide dedicated International Office support.') }}
            </p>
        </div>
    </section>
@endif

{{-- NEXT STEP --}}
<section class="max-w-[1400px] mx-auto px-4 py-14">
    <div class="text-center mb-8">
        <h2 class="text-2xl md:text-4xl font-bold text-gray-900 mb-3">{{ __('Next Step') }}</h2>
        <p class="text-gray-600">{{ __('Once you\'ve decided on Germany, your journey starts here.') }}</p>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <a href="{{ route('tools.recommendation') }}"
           class="block bg-white border border-gray-200 hover:border-primary-500 hover:shadow-md transition rounded-xl p-6 text-center">
            <div class="inline-flex items-center justify-center w-14 h-14 mx-auto mb-3 rounded-xl bg-primary-50 text-primary-600"><x-svg-icon name="target" class="w-8 h-8" /></div>
            <h3 class="font-bold text-gray-900 mb-1">{{ __('University Match Quiz') }}</h3>
            <p class="text-sm text-gray-600">{{ __('8 questions to show your top 5 universities') }}</p>
        </a>
        <a href="{{ route('rankings.show', 'turk-ogrenci-favorisi-universiteler') }}"
           class="block bg-white border border-gray-200 hover:border-primary-500 hover:shadow-md transition rounded-xl p-6 text-center">
            <div class="text-4xl mb-3">🇹🇷</div>
            <h3 class="font-bold text-gray-900 mb-1">{{ __('Turkish Student Favorites') }}</h3>
            <p class="text-sm text-gray-600">{{ __('uni-assist + English programs + size weighted list') }}</p>
        </a>
        <a href="{{ route('programs.index') }}"
           class="block bg-white border border-gray-200 hover:border-primary-500 hover:shadow-md transition rounded-xl p-6 text-center">
            <div class="inline-flex items-center justify-center w-14 h-14 mx-auto mb-3 rounded-xl bg-primary-50 text-primary-600"><x-svg-icon name="book-open" class="w-8 h-8" /></div>
            <h3 class="font-bold text-gray-900 mb-1">{{ __(':count English Programs', ['count' => number_format($stats['programs_en'], 0, ',', '.')]) }}</h3>
            <p class="text-sm text-gray-600">{{ __('No German? No problem — search directly.') }}</p>
        </a>
    </div>

    @if ($latestPosts->isNotEmpty())
        <div class="mt-12">
            <h3 class="text-xl font-bold text-gray-900 mb-4 inline-flex items-center gap-2"><x-svg-icon name="newspaper" class="w-6 h-6 text-primary-600" /> {{ __('Related Guides') }}</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach ($latestPosts as $p)
                    <a href="{{ route('blog.show', $p->slug) }}"
                       class="block bg-white border border-gray-200 hover:border-primary-400 hover:shadow-sm transition rounded-lg p-4">
                        <h4 class="font-bold text-gray-900 mb-1 leading-snug line-clamp-2">{{ $p->title }}</h4>
                        @if ($p->excerpt)
                            <p class="text-xs text-gray-500 line-clamp-2 mb-2">{{ $p->excerpt }}</p>
                        @endif
                        <p class="text-xs text-primary-600 font-semibold">{{ $p->reading_minutes ?? 5 }} {{ __('min read') }} →</p>
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</section>

@endsection
