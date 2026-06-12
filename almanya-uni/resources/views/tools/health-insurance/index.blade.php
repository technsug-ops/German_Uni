@extends('layouts.app')

@section('title', __('Student Health Insurance Comparison — Germany') . ' — ' . brand('name'))

<x-seo
    :title="__('Student Health Insurance Comparison (Germany)')"
    :description="__('Compare public (GKV), private (PKV) and expat health insurance for studying in Germany. TK, AOK, BARMER, DR-WALTER, MAWISTA, ottonova — price, visa acceptance and enrolment validity side by side.')"
/>

@section('content')
{{-- HERO --}}
<section class="bg-gradient-to-br from-emerald-700 via-teal-600 to-cyan-500 text-white">
    <div class="max-w-[1400px] mx-auto px-4 py-10 md:py-14">
        <nav class="text-sm text-emerald-100 mb-3">
            <a href="/" class="hover:text-white">{{ __('Home') }}</a>
            <span class="mx-2 opacity-50">›</span>
            <a href="{{ route('tools.index') }}" class="hover:text-white">{{ __('Tools') }}</a>
            <span class="mx-2 opacity-50">›</span>
            <span class="text-white">{{ __('Health Insurance') }}</span>
        </nav>
        <h1 class="text-3xl md:text-5xl font-extrabold leading-tight drop-shadow mb-3 inline-flex items-center gap-3">
            <x-svg-icon name="heart" class="w-8 h-8 md:w-10 md:h-10" />
            {{ __('Student Health Insurance Comparison') }}
        </h1>
        <p class="text-lg md:text-xl text-emerald-100 max-w-3xl">
            {!! __('Health insurance is <strong class="text-white">mandatory</strong> for your German student visa and university enrolment. Compare public, private and expat options side by side and pick what fits your situation.') !!}
        </p>

        {{-- Quick facts --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mt-6 max-w-[1400px]">
            <div class="bg-white/15 backdrop-blur rounded-lg p-3 ring-1 ring-white/20">
                <p class="text-2xl font-bold">{{ $totals['total'] }}</p>
                <p class="text-xs text-emerald-100 mt-0.5">{{ __('Providers compared') }}</p>
            </div>
            <div class="bg-white/15 backdrop-blur rounded-lg p-3 ring-1 ring-white/20">
                <p class="text-2xl font-bold">~€136</p>
                <p class="text-xs text-emerald-100 mt-0.5">{{ __('Public (GKV) monthly') }}</p>
            </div>
            <div class="bg-white/15 backdrop-blur rounded-lg p-3 ring-1 ring-white/20">
                <p class="text-2xl font-bold">~€30+</p>
                <p class="text-xs text-emerald-100 mt-0.5">{{ __('Expat plan monthly') }}</p>
            </div>
            <div class="bg-white/15 backdrop-blur rounded-lg p-3 ring-1 ring-white/20">
                <p class="text-2xl font-bold">30</p>
                <p class="text-xs text-emerald-100 mt-0.5">{{ __('GKV age limit') }}</p>
            </div>
        </div>
    </div>
</section>

<div class="max-w-[1400px] mx-auto px-4 py-10">

    {{-- Featured-snippet box — Google AIO target --}}
    <x-featured-snippet
        :question="__('Which health insurance do I need to study in Germany?')"
        :answer="__('Enrolled students under 30 take public (statutory/GKV) insurance such as TK, AOK or BARMER (~€136/month) — it is valid for both the visa and university enrolment. During a language course or Studienkolleg, or if you are over 30 / a PhD candidate, you use cheaper expat insurance (DR-WALTER, MAWISTA) or private insurance (ottonova) for the visa.')"
        :steps="[
            ['title' => __('Identify your phase'), 'description' => __('Language course / Studienkolleg → expat. Enrolled degree student → public (GKV).')],
            ['title' => __('Check your age'), 'description' => __('Under 30 and enrolled → public. Over 30 / PhD / scholar → private (PKV).')],
            ['title' => __('Pick a provider'), 'description' => __('Compare price, visa acceptance and enrolment validity in the table below.')],
            ['title' => __('Get the confirmation'), 'description' => __('Use the insurance certificate for your visa appointment and matriculation.')],
        ]"
    />

    {{-- ════════════════════════════════════════════════════════════════ --}}
    {{-- DECISION HELPER — Public vs Private vs Expat                        --}}
    {{-- ════════════════════════════════════════════════════════════════ --}}
    <section class="mb-10 bg-white border-2 border-emerald-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="bg-gradient-to-r from-emerald-50 to-teal-50 border-b border-emerald-100 px-6 py-4">
            <h2 class="text-xl md:text-2xl font-bold text-gray-900 inline-flex items-center gap-2">
                <x-svg-icon name="light-bulb" class="w-6 h-6" /> {{ __('Which type is right for me?') }}
            </h2>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="rounded-xl border border-emerald-200 bg-emerald-50/50 p-5">
                <p class="text-xs font-bold uppercase tracking-wide text-emerald-700 mb-1">{{ __('Public · GKV') }}</p>
                <p class="font-bold text-gray-900 mb-2">{{ __('Enrolled & under 30') }}</p>
                <p class="text-sm text-gray-700 leading-relaxed">{{ __('Mandatory statutory insurance for degree students. Valid for both visa and enrolment. ~€136/month, fixed by law.') }}</p>
            </div>
            <div class="rounded-xl border border-amber-200 bg-amber-50/50 p-5">
                <p class="text-xs font-bold uppercase tracking-wide text-amber-700 mb-1">{{ __('Expat · Incoming') }}</p>
                <p class="font-bold text-gray-900 mb-2">{{ __('Language course / Studienkolleg') }}</p>
                <p class="text-sm text-gray-700 leading-relaxed">{{ __('Cheap cover (from ~€30/month) accepted for the visa before you can join a public fund. Not valid for degree enrolment on its own.') }}</p>
            </div>
            <div class="rounded-xl border border-indigo-200 bg-indigo-50/50 p-5">
                <p class="text-xs font-bold uppercase tracking-wide text-indigo-700 mb-1">{{ __('Private · PKV') }}</p>
                <p class="font-bold text-gray-900 mb-2">{{ __('Over 30 / PhD / scholar') }}</p>
                <p class="text-sm text-gray-700 leading-relaxed">{{ __('For those who cannot join a public fund. Broader coverage but pricier, and switching back to public is hard.') }}</p>
            </div>
        </div>
    </section>

    {{-- FILTER CHIPS --}}
    <div class="flex flex-wrap gap-2 mb-6 items-center">
        <span class="text-sm font-semibold text-gray-700 mr-2">{{ __('Filter:') }}</span>
        @php
            $chips = [
                null         => [__('All'),                 'heart'],
                'cheapest'   => [__('Cheapest'),            'currency-euro'],
                'public'     => [__('Public (GKV)'),        'building-office'],
                'expat'      => [__('Expat / Incoming'),    'globe'],
                'private'    => [__('Private (PKV)'),       'cursor-arrow-rays'],
                'enrollment' => [__('Valid for enrolment'), 'check'],
                'english'    => [__('English support'),     'chat-bubble'],
            ];
        @endphp
        @foreach ($chips as $key => [$label, $iconName])
            <a href="{{ route('tools.health-insurance', $key ? ['filter' => $key] : []) }}"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-medium transition
                      {{ $filter === $key ? 'bg-primary-600 text-white shadow-sm' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                <x-svg-icon name="{{ $iconName }}" class="w-3.5 h-3.5" />
                <span>{{ $label }}</span>
            </a>
        @endforeach
    </div>

    {{-- BİR BAKIŞTA KARŞILAŞTIR --}}
    <x-provider-comparison :providers="$providers" kind="insurance" />

    {{-- SAĞLAYICI KARTLARI --}}
    @if ($providers->isEmpty())
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-8 text-center">
            <p class="text-amber-900 font-semibold">{{ __('No providers match this filter.') }}</p>
            <a href="{{ route('tools.health-insurance') }}" class="inline-block mt-3 text-primary-600 font-semibold hover:underline">← {{ __('All providers') }}</a>
        </div>
    @else
        <div class="space-y-4">
            @foreach ($providers as $p)
                <article class="bg-white border border-gray-200 rounded-2xl overflow-hidden hover:shadow-lg hover:border-primary-300 transition
                                @if($p->is_featured) ring-2 ring-emerald-300 @endif">
                    <div class="grid grid-cols-1 md:grid-cols-[280px_1fr_240px]">
                        {{-- SOL: Logo + Tip --}}
                        <div class="p-6 bg-gradient-to-br from-gray-50 to-white border-r border-gray-100 flex flex-col items-center justify-center text-center">
                            @if ($p->is_featured)
                                <span class="inline-flex items-center gap-1 mb-2 px-2 py-0.5 text-xs font-bold rounded-full bg-emerald-400 text-emerald-900">
                                    <x-svg-icon name="star" class="w-3 h-3" />
                                    {{ __('FEATURED') }}
                                </span>
                            @endif
                            @if ($p->logo_url)
                                <img src="{{ $p->logo_url }}" alt="{{ $p->name }} logo" class="h-16 max-w-full object-contain mb-3" loading="lazy">
                            @else
                                <div class="w-16 h-16 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-500 flex items-center justify-center text-white text-2xl font-extrabold mb-3">
                                    {{ mb_substr($p->name, 0, 2) }}
                                </div>
                            @endif
                            <h2 class="text-lg font-bold text-gray-900 leading-tight">{{ $p->name }}</h2>
                            <span class="inline-block mt-1 px-2 py-0.5 text-xs font-medium rounded
                                         @switch($p->type)
                                             @case('public') bg-emerald-50 text-emerald-700 @break
                                             @case('private') bg-indigo-50 text-indigo-700 @break
                                             @default bg-amber-50 text-amber-700
                                         @endswitch">
                                {{ __($p->type_label) }}
                            </span>
                            @if ($p->best_for_label)
                                <p class="text-xs text-gray-500 mt-1.5">{{ __('Best for:') }} {{ $p->best_for_label }}</p>
                            @endif
                        </div>

                        {{-- ORTA: Özellikler --}}
                        <div class="p-6">
                            @if ($p->description)
                                <p class="text-gray-700 mb-4 leading-relaxed">{{ $p->description }}</p>
                            @endif

                            {{-- Bilgi satırı --}}
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-4">
                                <div>
                                    <p class="text-xs text-gray-500 uppercase tracking-wide font-semibold">{{ __('Monthly') }}</p>
                                    <p class="font-bold text-gray-900">{{ $p->monthly_range }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase tracking-wide font-semibold">{{ __('Visa') }}</p>
                                    <p class="font-bold {{ $p->accepted_for_visa ? 'text-emerald-700' : 'text-gray-400' }}">
                                        {{ $p->accepted_for_visa ? __('Accepted') : '—' }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase tracking-wide font-semibold">{{ __('Enrolment') }}</p>
                                    <p class="font-bold {{ $p->accepted_for_enrollment ? 'text-emerald-700' : 'text-amber-600' }}">
                                        {{ $p->accepted_for_enrollment ? __('Valid') : __('No') }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase tracking-wide font-semibold">{{ __('Age limit') }}</p>
                                    <p class="font-bold text-gray-900">{{ $p->age_limit ? '≤ ' . $p->age_limit : __('None') }}</p>
                                </div>
                            </div>

                            {{-- Kapsam badge'leri --}}
                            <div class="flex flex-wrap gap-1.5 mb-3">
                                @if ($p->covers_dental)
                                    <span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium rounded bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100">
                                        <x-svg-icon name="check" class="w-3.5 h-3.5" /> {{ __('Dental') }}
                                    </span>
                                @endif
                                @if ($p->covers_pregnancy)
                                    <span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium rounded bg-pink-50 text-pink-700 ring-1 ring-pink-100">
                                        <x-svg-icon name="check" class="w-3.5 h-3.5" /> {{ __('Pregnancy') }}
                                    </span>
                                @endif
                                @if ($p->covers_mental_health)
                                    <span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium rounded bg-purple-50 text-purple-700 ring-1 ring-purple-100">
                                        <x-svg-icon name="check" class="w-3.5 h-3.5" /> {{ __('Mental health') }}
                                    </span>
                                @endif
                                @if ($p->english_support)
                                    <span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium rounded bg-blue-50 text-blue-700 ring-1 ring-blue-100">
                                        <x-svg-icon name="chat-bubble" class="w-3.5 h-3.5" /> {{ __('English support') }}
                                    </span>
                                @endif
                                @if ($p->digital_signup)
                                    <span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium rounded bg-gray-50 text-gray-700 ring-1 ring-gray-100">
                                        <x-svg-icon name="cursor-arrow-rays" class="w-3.5 h-3.5" /> {{ __('Online signup') }}
                                    </span>
                                @endif
                            </div>

                            {{-- Pros (TR-only — EN/DE'ye sızmasın diye gizli; bilgi badge'lerde mevcut) --}}
                            @if (app()->getLocale() === 'tr' && is_array($p->pros) && count($p->pros))
                                <ul class="space-y-1 text-sm">
                                    @foreach (array_slice($p->pros, 0, 3) as $pro)
                                        <li class="flex items-start gap-2 text-gray-700">
                                            <span class="text-emerald-500 mt-0.5"><x-svg-icon name="check" class="w-4 h-4" /></span>
                                            <span>{{ $pro }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>

                        {{-- SAĞ: CTA --}}
                        <div class="p-6 bg-gray-50 border-t md:border-t-0 md:border-l border-gray-100 flex flex-col justify-center gap-3">
                            <x-affiliate-link :provider="$p" ctx="index"
                                class="block text-center bg-primary-600 hover:bg-primary-700 text-white font-bold py-3 px-4 rounded-lg transition shadow-sm hover:shadow">
                                {{ __('Visit site') }} →
                            </x-affiliate-link>
                            <a href="{{ route('tools.health-insurance.show', $p->slug) }}"
                               class="block text-center bg-white border border-gray-300 hover:bg-gray-50 text-gray-800 font-semibold py-3 px-4 rounded-lg transition">
                                {{ __('View details') }}
                            </a>
                            @if ($p->last_verified_at)
                                <p class="text-[10px] text-gray-400 text-center">
                                    {{ __('Last verified:') }} {{ $p->last_verified_at->format('Y-m-d') }}
                                </p>
                            @endif
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    @endif

    {{-- BİLGİLENDİRME --}}
    <section class="mt-12 grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-blue-50 border border-blue-100 rounded-xl p-6">
            <h2 class="text-xl font-bold text-blue-900 mb-3 inline-flex items-center gap-2"><x-svg-icon name="light-bulb" class="w-5 h-5" /> {{ __('Public or private — can I choose?') }}</h2>
            <p class="text-blue-800 leading-relaxed text-sm">
                {!! __('If you enrol in a degree program and are under 30, you are <strong>required</strong> to take public (statutory/GKV) insurance — you cannot freely pick private. Private (PKV) is only for those who cannot join a public fund (over 30, PhD candidates, scholarship holders). Once you choose private as a student, switching back to public is very difficult.') !!}
            </p>
        </div>

        @if (app()->getLocale() === 'tr')
        <div class="bg-emerald-50 border border-emerald-100 rounded-xl p-6">
            <h2 class="text-xl font-bold text-emerald-900 mb-3">Türk öğrenciler için</h2>
            <ul class="text-emerald-800 text-sm space-y-2 leading-relaxed">
                <li>• <strong>Dil kursu / Studienkolleg</strong> aşamasında expat sigorta (DR-WALTER, MAWISTA) vize için yeterli ve ucuzdur.</li>
                <li>• Üniversiteye <strong>kayıt olunca</strong> yasal kasaya (TK/AOK/BARMER) geçersin — kayıt için bu zorunlu.</li>
                <li>• <strong>Expatrio/Fintiba</strong> paketleri zaten TK ya da DR-WALTER’e yönlendirir; doğrudan da açabilirsin.</li>
                <li>• 30 yaş üstü ya da doktora/burslu isen public’e giremezsin → ottonova gibi PKV.</li>
            </ul>
        </div>
        @else
        <div class="bg-emerald-50 border border-emerald-100 rounded-xl p-6">
            <h2 class="text-xl font-bold text-emerald-900 mb-3 inline-flex items-center gap-2"><x-svg-icon name="globe" class="w-5 h-5" /> {{ __('Good to know') }}</h2>
            <ul class="text-emerald-800 text-sm space-y-2 leading-relaxed">
                <li>{!! __('• During a <strong>language course / Studienkolleg</strong>, cheap expat insurance (DR-WALTER, MAWISTA) is enough for the visa.') !!}</li>
                <li>{!! __('• Once you <strong>enrol</strong> in a degree, you move to a statutory fund (TK/AOK/BARMER) — required for matriculation.') !!}</li>
                <li>{!! __('• Bundles like <strong>Expatrio/Fintiba</strong> simply route you to TK or DR-WALTER; you can also sign up directly.') !!}</li>
                <li>{{ __('• Over 30 or a PhD/scholarship holder? You cannot join a public fund — use a private (PKV) plan.') }}</li>
            </ul>
        </div>
        @endif
    </section>

    {{-- Cross-link --}}
    <section class="mt-8 grid grid-cols-1 sm:grid-cols-3 gap-3">
        <a href="{{ route('tools.blocked-account') }}" class="block bg-white border border-gray-200 rounded-xl p-4 hover:border-primary-400 hover:shadow-sm transition">
            <p class="mb-1 text-primary-600"><x-svg-icon name="building-library" class="w-6 h-6" /></p>
            <p class="font-bold text-gray-900">{{ __('Blocked Account') }}</p>
            <p class="text-xs text-gray-500 mt-0.5">{{ __('Sperrkonto providers') }}</p>
        </a>
        <a href="{{ route('tools.visa-cost') }}" class="block bg-white border border-gray-200 rounded-xl p-4 hover:border-primary-400 hover:shadow-sm transition">
            <p class="mb-1 text-primary-600"><x-svg-icon name="banknotes" class="w-6 h-6" /></p>
            <p class="font-bold text-gray-900">{{ __('Visa Cost') }}</p>
            <p class="text-xs text-gray-500 mt-0.5">{{ __('Total visa budget') }}</p>
        </a>
        <a href="{{ route('tools.cost-of-living') }}" class="block bg-white border border-gray-200 rounded-xl p-4 hover:border-primary-400 hover:shadow-sm transition">
            <p class="mb-1 text-primary-600"><x-svg-icon name="chart-bar" class="w-6 h-6" /></p>
            <p class="font-bold text-gray-900">{{ __('Cost of Living') }}</p>
            <p class="text-xs text-gray-500 mt-0.5">{{ __('Monthly expenses by city') }}</p>
        </a>
    </section>

    {{-- Disclaimer --}}
    <p class="text-xs text-gray-400 mt-8 text-center max-w-3xl mx-auto">
        {{ __('Prices and coverage are taken from the providers\' own sites and may change. Public (GKV) rates are regulated and nearly identical across funds. Always verify on the official site before signing up. :brand may have affiliate partnerships with some providers — it costs you nothing extra and supports the platform.', ['brand' => brand('name')]) }}
    </p>
</div>
@endsection
