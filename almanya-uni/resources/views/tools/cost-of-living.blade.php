@extends('layouts.app')

@section('title', __('Germany Cost of Living Calculator') . ' — ' . brand('name'))

<x-seo
    :title="__('Germany Cost of Living Calculator + DAAD Official Guide')"
    :description="__('Monthly 992 € Sperrkonto for students in Germany, 26 city-based cost breakdowns, tuition fees (BW/Bayern), semester contribution. DAAD official data.')"
/>

<x-tool-schema tool="cost-of-living" />

<x-json-ld :data="[
    '@context' => 'https://schema.org',
    '@type' => 'Article',
    'headline' => __('Germany Cost of Living Guide — DAAD Official Data'),
    'description' => __('Sperrkonto 992 €/month, tuition fees by state, semester contribution 70-430 €, real international student costs 950-2,200 €.'),
    'datePublished' => '2026-05-19',
    'inLanguage' => app()->getLocale(),
    'author' => ['@type' => 'Organization', 'name' => 'AlmanyaUni'],
    'publisher' => [
        '@type' => 'Organization',
        'name' => 'AlmanyaUni',
        'url' => url('/'),
    ],
]" />

@section('content')
<div class="bg-gradient-to-r from-primary-500 to-primary-700 text-white py-10">
    <div class="max-w-[1400px] mx-auto px-4">
        <nav class="text-sm text-primary-100 mb-3">
            <a href="{{ route('tools.index') }}" class="hover:text-white">{{ __('Tools') }}</a>
            <span class="mx-2">/</span>
            <span>{{ __('Cost of Living') }}</span>
        </nav>
        <h1 class="text-3xl md:text-4xl font-bold mb-3">💰 {{ __('Cost of Living Calculator') }}</h1>
        <p class="text-primary-100 max-w-3xl">
            {{ __('What will your monthly expenses be as a student in Germany? Pick a city, housing type and lifestyle — see realistic numbers.') }}
        </p>
    </div>
</div>

<div class="max-w-[1400px] mx-auto px-4 py-10">
    <form method="GET" action="{{ route('tools.cost-of-living') }}" class="bg-white border border-gray-200 rounded-lg p-6 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="city" class="block text-sm font-semibold text-gray-700 mb-2">{{ __('City') }}</label>
                <select name="city" id="city" required class="w-full border border-gray-300 rounded px-3 py-2 focus:border-primary-500 focus:outline-none">
                    <option value="">— {{ __('Choose city') }} —</option>
                    @foreach ($cities as $c)
                        <option value="{{ $c->id }}" @selected($selected_city_id === $c->id)>
                            {{ $c->name }}@if ($c->state) ({{ $c->state->name }}) @endif
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="housing" class="block text-sm font-semibold text-gray-700 mb-2">{{ __('Housing type') }}</label>
                <select name="housing" id="housing" class="w-full border border-gray-300 rounded px-3 py-2 focus:border-primary-500 focus:outline-none">
                    <option value="wg"        @selected($housing === 'wg')>{{ __('WG (shared room)') }}</option>
                    <option value="studio"    @selected($housing === 'studio')>{{ __('Studio (1-Zimmer)') }}</option>
                    <option value="apartment" @selected($housing === 'apartment')>{{ __('Apartment (2-Zimmer)') }}</option>
                </select>
            </div>
            <div>
                <label for="lifestyle" class="block text-sm font-semibold text-gray-700 mb-2">{{ __('Lifestyle') }}</label>
                <select name="lifestyle" id="lifestyle" class="w-full border border-gray-300 rounded px-3 py-2 focus:border-primary-500 focus:outline-none">
                    <option value="frugal"      @selected($lifestyle === 'frugal')>{{ __('Frugal (-25%)') }}</option>
                    <option value="normal"      @selected($lifestyle === 'normal')>{{ __('Normal') }}</option>
                    <option value="comfortable" @selected($lifestyle === 'comfortable')>{{ __('Comfortable (+30%)') }}</option>
                </select>
            </div>
        </div>
        <div class="mt-5">
            <button type="submit" class="bg-accent-500 hover:bg-accent-600 text-white font-semibold px-6 py-2.5 rounded transition">
                {{ __('Calculate') }}
            </button>
        </div>
    </form>

    @if ($result)
        @if (($result['source'] ?? null) === 'ai')
            <div class="mb-4 bg-amber-50 border border-amber-200 rounded-lg p-4 text-sm flex items-start gap-3">
                <span class="text-2xl shrink-0">⚠️</span>
                <div>
                    <p class="font-semibold text-amber-900 mb-1">{{ __('Approximate data — AI generated') }}</p>
                    <p class="text-amber-800">{{ $result['source_note'] ?? __('No manually verified data for this city. The numbers below were estimated by AI from Wikipedia and community information. Verify with the city\'s official page for accurate info.') }}</p>
                    <a href="{{ route('cities.show', $result['city']->slug) }}" class="text-amber-700 hover:text-amber-900 font-semibold text-xs mt-2 inline-block">
                        {{ __('See :city city guide →', ['city' => $result['city']->name]) }}
                    </a>
                </div>
            </div>
        @endif

        <section class="bg-white border border-gray-200 rounded-lg overflow-hidden">
            <div class="bg-gray-50 border-b border-gray-200 px-6 py-4">
                <h2 class="text-xl font-bold text-gray-900">
                    {{ __(':city — Monthly Cost', ['city' => $result['city']->name]) }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    {{ $result['housing_label'] }} · {{ $result['lifestyle_label'] }}
                    @if ($result['tier'])
                        ·
                        <span class="inline-block text-xs font-semibold uppercase tracking-wide px-2 py-0.5 rounded
                            @switch($result['tier'])
                                @case('very_expensive') bg-red-100 text-red-700 @break
                                @case('expensive')      bg-orange-100 text-orange-700 @break
                                @case('mid')            bg-yellow-100 text-yellow-700 @break
                                @default                bg-green-100 text-green-700
                            @endswitch
                        ">{{ str_replace('_', ' ', $result['tier']) }}</span>
                    @endif
                </p>
            </div>

            <table class="w-full">
                <tbody>
                @foreach ($result['items'] as $item)
                    <tr class="border-b border-gray-100 last:border-b-0">
                        <td class="px-6 py-3 text-gray-700">
                            {{ $item['label'] }}
                            @if (! $item['fixed'])
                                <span class="text-xs text-gray-400">({{ __('by lifestyle') }})</span>
                            @endif
                        </td>
                        <td class="px-6 py-3 text-right font-semibold text-gray-900">
                            {{ number_format($item['value'], 0, ',', '.') }} €
                        </td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-primary-50">
                        <td class="px-6 py-4 font-bold text-primary-900">{{ __('Total monthly') }}</td>
                        <td class="px-6 py-4 text-right text-2xl font-bold text-primary-900">
                            {{ number_format($result['total_month'], 0, ',', '.') }} €
                        </td>
                    </tr>
                    <tr class="bg-primary-50 border-t border-primary-100">
                        <td class="px-6 py-2 text-sm text-primary-800">{{ __('Total yearly') }}</td>
                        <td class="px-6 py-2 text-right font-semibold text-primary-800">
                            {{ number_format($result['total_year'], 0, ',', '.') }} €
                        </td>
                    </tr>
                </tfoot>
            </table>

            <div class="border-t border-gray-200 px-6 py-4 bg-accent-50">
                <h3 class="font-semibold text-accent-900 mb-1">🏦 {{ __('Sperrkonto (Blocked Account)') }}</h3>
                <p class="text-sm text-accent-800">
                    {{ __('Minimum yearly amount required for visa as of 2025:') }}
                    <strong>{{ number_format($result['blocked_account'], 0, ',', '.') }} €</strong>
                    ({{ number_format($result['blocked_account'] / 12, 0, ',', '.') }} €/{{ __('month') }}).
                </p>
            </div>
        </section>

        <p class="text-xs text-gray-500 mt-4">
            {{ __('Numbers are estimates, sources: DAAD, Studierendenwerk, WG-Gesucht, Numbeo (2025). Real expenses vary by lifestyle and city district.') }}
        </p>
    @endif
</div>

{{-- ════════════════════════════════════════════════════ --}}
{{-- DAAD OFFICIAL GUIDE                                   --}}
{{-- ════════════════════════════════════════════════════ --}}
<section class="bg-gray-50 border-t border-gray-200 py-12">
    <div class="max-w-[1400px] mx-auto px-4 space-y-10">

        <header>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-bold uppercase tracking-wider mb-3">
                📚 {{ __('DAAD Official Information') }}
            </div>
            <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-3">{{ __('Education & Living Costs in Germany') }}</h2>
            <p class="text-gray-600 text-lg max-w-3xl">
                {{ __('According to the official data of DAAD (Deutscher Akademischer Austauschdienst) — German Academic Exchange Service. Germany is not expensive compared to Denmark or Switzerland, but higher than Poland or the Czech Republic.') }}
            </p>
        </header>

        {{-- SPERRKONTO --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
            <h3 class="text-2xl font-bold text-gray-900 mb-4 flex items-center gap-2">🏦 {{ __('Sperrkonto (Blocked Account)') }}</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-5">
                <div class="bg-primary-50 ring-1 ring-primary-200 rounded-lg p-4">
                    <p class="text-xs uppercase tracking-wider text-primary-700 font-semibold">{{ __('Monthly minimum') }}</p>
                    <p class="text-3xl font-extrabold text-primary-900 mt-1">992 €</p>
                    <p class="text-xs text-primary-700 mt-1">{{ __('as of 2025') }}</p>
                </div>
                <div class="bg-accent-50 ring-1 ring-accent-200 rounded-lg p-4">
                    <p class="text-xs uppercase tracking-wider text-accent-700 font-semibold">{{ __('Yearly total') }}</p>
                    <p class="text-3xl font-extrabold text-accent-900 mt-1">11.904 €</p>
                    <p class="text-xs text-accent-700 mt-1">{{ __('For visa application') }}</p>
                </div>
                <div class="bg-amber-50 ring-1 ring-amber-200 rounded-lg p-4">
                    <p class="text-xs uppercase tracking-wider text-amber-700 font-semibold">{{ __('Bank fee') }}</p>
                    <p class="text-3xl font-extrabold text-amber-900 mt-1">70-150 €</p>
                    <p class="text-xs text-amber-700 mt-1">{{ __('One-time account opening') }}</p>
                </div>
            </div>
            <p class="text-sm text-gray-700 leading-relaxed">
                <strong>{{ __('If you are from outside the EU or EEA') }}</strong>, {{ __('proof of financial resources is required for a student visa. Money stays blocked until you arrive in Germany, then you can withdraw up to 992 €/month. Accepted alternatives:') }}
                <strong>{{ __('parental income proof') }}</strong>, {{ __('recognized') }} <strong>{{ __('scholarship acceptance letter') }}</strong>.
            </p>
            <p class="text-xs text-gray-500 mt-3">
                <a href="https://www.daad.de" target="_blank" rel="noopener" class="text-primary-600 hover:underline">DAAD</a> ·
                <a href="https://www.auswaertiges-amt.de" target="_blank" rel="noopener" class="text-primary-600 hover:underline">Auswärtiges Amt</a>
            </p>
        </div>

        {{-- ÖĞRENİM ÜCRETİ --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
            <h3 class="text-2xl font-bold text-gray-900 mb-4 flex items-center gap-2">🎓 {{ __('Tuition Fees') }}</h3>
            <p class="text-gray-700 mb-5">
                {{ __('The vast majority of') }} <strong>{{ __('higher education institutions in Germany are state-funded') }}</strong>.
                {{ __('Bachelor\'s and most master\'s programs have') }} <strong>{{ __('NO tuition fee') }}</strong>.
            </p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div class="bg-emerald-50 ring-1 ring-emerald-200 rounded-lg p-4">
                    <p class="text-xs uppercase tracking-wider text-emerald-700 font-bold">{{ __('14 States') }}</p>
                    <p class="text-2xl font-extrabold text-emerald-900 mt-1">{{ __('Free') }}</p>
                    <p class="text-xs text-emerald-700 mt-2">{{ __('Bachelor + most master at public universities') }}</p>
                </div>
                <div class="bg-amber-50 ring-1 ring-amber-200 rounded-lg p-4">
                    <p class="text-xs uppercase tracking-wider text-amber-700 font-bold">Baden-Württemberg</p>
                    <p class="text-2xl font-extrabold text-amber-900 mt-1">1.500 €/{{ __('semester') }}</p>
                    <p class="text-xs text-amber-700 mt-2">{{ __('Only non-EU bachelor + master students. PhD and special programs exempt.') }}</p>
                </div>
                <div class="bg-rose-50 ring-1 ring-rose-200 rounded-lg p-4">
                    <p class="text-xs uppercase tracking-wider text-rose-700 font-bold">Bayern</p>
                    <p class="text-2xl font-extrabold text-rose-900 mt-1">{{ __('Variable') }}</p>
                    <p class="text-xs text-rose-700 mt-2">{{ __('Each university sets its own for non-EU. Can be several thousand €/semester.') }}</p>
                </div>
            </div>

            <p class="text-sm text-gray-600 mt-4">
                <strong>{{ __('Private universities') }}</strong> {{ __('may charge higher fees for bachelor\'s. Check the type first from the') }}
                <a href="{{ route('universities.index') }}" class="text-primary-600 hover:underline">{{ __('university list') }}</a>.
            </p>
        </div>

        {{-- DÖNEM KATKISI --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
            <h3 class="text-2xl font-bold text-gray-900 mb-2 flex items-center gap-2">💳 {{ __('Semester Contribution') }}</h3>
            <p class="text-gray-700 mb-4">
                {{ __('All students pay a contribution of') }} <strong>70-430 €</strong> {{ __('once per semester. This is NOT a tuition fee — it includes student services + AStA + often') }} <strong>Semesterticket</strong>.
            </p>

            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-xs uppercase tracking-wider text-gray-500 font-bold mb-3">{{ __('Example: University of Cologne 2024/25 WS — 304.25 €') }}</p>
                <ul class="space-y-1.5 text-sm">
                    <li class="flex justify-between"><span class="text-gray-700">🚊 {{ __('Semesterticket (transport)') }}</span><strong>176,40 €</strong></li>
                    <li class="flex justify-between"><span class="text-gray-700">🏛️ {{ __('Student services (Studierendenwerk)') }}</span><strong>110,00 €</strong></li>
                    <li class="flex justify-between"><span class="text-gray-700">🗳️ {{ __('AStA (student government)') }}</span><strong>11,00 €</strong></li>
                    <li class="flex justify-between"><span class="text-gray-700">⚽ {{ __('Student sports') }}</span><strong>1,75 €</strong></li>
                    <li class="flex justify-between"><span class="text-gray-700">📋 {{ __('Faculty student council') }}</span><strong>2,60 €</strong></li>
                    <li class="flex justify-between"><span class="text-gray-700">📑 {{ __('Administrative fee') }}</span><strong>2,50 €</strong></li>
                </ul>
            </div>
        </div>

        {{-- DAAD 22. SOSYAL ARAŞTIRMA --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
            <h3 class="text-2xl font-bold text-gray-900 mb-2 flex items-center gap-2">📊 {{ __('DAAD 22nd Social Survey (2023)') }}</h3>
            <p class="text-gray-700 mb-4">
                {{ __('According to DZHW\'s official survey, the average monthly cost of') }} <strong>{{ __('all students') }}</strong>.
                <em>{{ __('Higher in practice for international students') }}</em> — {{ __('these figures also cover Germans + those living with family.') }}
            </p>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-2 text-sm">
                @php
                    $daad = [
                        [__('🏠 Rent (utilities included)'), '410 €', 'rent'],
                        [__('🍽️ Food'), '198 €', 'food'],
                        [__('🏥 Health insurance + doctor + medicine'), '100 €', 'health'],
                        [__('🚌 Transport'), '89 €', 'transport'],
                        [__('🎓 Tuition fee'), '76 €', 'tuition'],
                        [__('🎭 Leisure / culture / sport'), '65 €', 'leisure'],
                        [__('👕 Clothing'), '46 €', 'clothing'],
                        [__('💳 Semester contribution'), '36 €', 'contribution'],
                        [__('📚 Study materials'), '31 €', 'materials'],
                        [__('📞 Phone / Internet / TV license'), '32 €', 'phone'],
                        [__('🛒 Other'), '144 €', 'other'],
                    ];
                @endphp
                @foreach ($daad as [$label, $amount, $key])
                    <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
                        <span class="text-gray-700">{{ $label }}</span>
                        <strong class="text-gray-900">{{ $amount }}</strong>
                    </div>
                @endforeach
            </div>

            <div class="mt-5 flex flex-wrap items-center justify-between gap-3 bg-primary-50 rounded-lg p-4">
                <div>
                    <p class="text-xs uppercase tracking-wider text-primary-700 font-semibold">{{ __('Total average') }}</p>
                    <p class="text-2xl font-extrabold text-primary-900">876 €/{{ __('month') }}</p>
                </div>
                <p class="text-xs text-primary-700 max-w-sm">
                    {{ __('The visa requires proof of') }} <strong>992 €/{{ __('month') }}</strong> — {{ __('the state acknowledges that real cost is higher than the DAAD average.') }}
                </p>
            </div>
        </div>

        {{-- ULUSLARARASI ÖĞRENCİ GERÇEĞİ --}}
        <div class="bg-gradient-to-br from-amber-50 to-orange-50 ring-1 ring-amber-200 rounded-2xl p-6">
            <h3 class="text-2xl font-bold text-gray-900 mb-2 flex items-center gap-2">⚠️ {{ __('International Student Reality') }}</h3>
            <p class="text-gray-700 mb-4 leading-relaxed">
                {{ __('DAAD\'s 876 €/month average covers German students + those living with family + old rental contracts.') }}
                <strong>{{ __('Real cost is higher for a newly arrived international student') }}</strong>:
            </p>

            <table class="w-full bg-white rounded-lg overflow-hidden text-sm">
                <thead class="bg-amber-100 text-amber-900">
                    <tr>
                        <th class="px-4 py-2 text-left font-bold">{{ __('City Type') }}</th>
                        <th class="px-4 py-2 text-right font-bold">{{ __('Realistic Range (€/month)') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr><td class="px-4 py-2.5">München</td><td class="px-4 py-2.5 text-right font-semibold text-rose-700">1.600 – 2.200</td></tr>
                    <tr><td class="px-4 py-2.5">Berlin · Frankfurt</td><td class="px-4 py-2.5 text-right font-semibold text-rose-700">1.300 – 1.700</td></tr>
                    <tr><td class="px-4 py-2.5">Stuttgart</td><td class="px-4 py-2.5 text-right font-semibold text-amber-700">1.250 – 1.600</td></tr>
                    <tr><td class="px-4 py-2.5">Hamburg</td><td class="px-4 py-2.5 text-right font-semibold text-amber-700">1.200 – 1.500</td></tr>
                    <tr><td class="px-4 py-2.5">Köln · Düsseldorf</td><td class="px-4 py-2.5 text-right font-semibold text-amber-700">1.150 – 1.450</td></tr>
                    <tr><td class="px-4 py-2.5">{{ __('NRW cheap (Dortmund, Bochum, Essen)') }}</td><td class="px-4 py-2.5 text-right font-semibold text-emerald-700">950 – 1.250</td></tr>
                    <tr><td class="px-4 py-2.5">{{ __('East (Leipzig, Dresden, Erfurt)') }}</td><td class="px-4 py-2.5 text-right font-semibold text-emerald-700">900 – 1.150</td></tr>
                </tbody>
            </table>

            <p class="text-xs text-gray-600 mt-4">
                {{ __('The calculator above assumes a WG + student-life scenario. If you are looking for a studio or 1+1 apartment, numbers can rise by') }} <strong>+50-120%</strong>.
            </p>
        </div>

        {{-- FİNANSMAN --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
            <h3 class="text-2xl font-bold text-gray-900 mb-3 flex items-center gap-2">💼 {{ __('Financing Options') }}</h3>
            <p class="text-gray-700 mb-4">
                {{ __('International students do not have') }} <strong>{{ __('unlimited work permission') }}</strong>. {{ __('A side job can boost your budget but covering all expenses is hard. Applying for scholarships is critical.') }}
            </p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <a href="https://www.daad.de/en/study-and-research-in-germany/scholarships/"
                   target="_blank" rel="noopener"
                   class="group flex items-start gap-3 p-4 bg-gray-50 rounded-lg hover:bg-primary-50 transition">
                    <div class="text-2xl">🎯</div>
                    <div>
                        <p class="font-bold text-gray-900 group-hover:text-primary-700">{{ __('DAAD Scholarships') }}</p>
                        <p class="text-xs text-gray-600 mt-0.5">{{ __('Official German exchange service — exclusively for international students') }}</p>
                    </div>
                </a>
                <a href="https://www.stipendienlotse.de"
                   target="_blank" rel="noopener"
                   class="group flex items-start gap-3 p-4 bg-gray-50 rounded-lg hover:bg-primary-50 transition">
                    <div class="text-2xl">🔍</div>
                    <div>
                        <p class="font-bold text-gray-900 group-hover:text-primary-700">Stipendienlotse</p>
                        <p class="text-xs text-gray-600 mt-0.5">{{ __('Federal government scholarship search platform') }}</p>
                    </div>
                </a>
                <a href="https://www.deutschlandstipendium.de"
                   target="_blank" rel="noopener"
                   class="group flex items-start gap-3 p-4 bg-gray-50 rounded-lg hover:bg-primary-50 transition">
                    <div class="text-2xl">🇩🇪</div>
                    <div>
                        <p class="font-bold text-gray-900 group-hover:text-primary-700">Deutschlandstipendium</p>
                        <p class="text-xs text-gray-600 mt-0.5">{{ __('300 €/month, based on merit and social engagement') }}</p>
                    </div>
                </a>
                <a href="https://www.studentenwerke.de"
                   target="_blank" rel="noopener"
                   class="group flex items-start gap-3 p-4 bg-gray-50 rounded-lg hover:bg-primary-50 transition">
                    <div class="text-2xl">🏛️</div>
                    <div>
                        <p class="font-bold text-gray-900 group-hover:text-primary-700">Studentenwerke</p>
                        <p class="text-xs text-gray-600 mt-0.5">{{ __('Dormitories + emergency aid funds') }}</p>
                    </div>
                </a>
            </div>
        </div>

        {{-- KAYNAK CITATION --}}
        <p class="text-xs text-gray-500 text-center">
            📖 {{ __('Source:') }} <a href="https://www.daad.de/en/study-and-research-in-germany/plan-your-studies/tuition-fees-and-living-expenses/" target="_blank" rel="noopener" class="text-primary-600 hover:underline">{{ __('DAAD — Education & Living Costs') }}</a> · {{ __('DZHW 22nd Social Survey 2023') }} · Auswärtiges Amt 2025
        </p>

    </div>
</section>
@endsection
