@extends('layouts.app')

@section('title', __('Blocked Account (Sperrkonto) Finder — Germany Student Visa') . ' — ' . brand('name'))

<x-seo
    :title="__('Blocked Account (Sperrkonto) Finder')"
    :description="__('Compare blocked account providers for the Germany student visa. Expatrio, Fintiba, Coracle, Deutsche Bank — price, speed, insurance combo features side by side.')"
/>

<x-tool-schema tool="blocked-account" />

@section('content')
{{-- HERO --}}
<section class="bg-gradient-to-br from-indigo-700 via-blue-600 to-cyan-500 text-white">
    <div class="max-w-[1400px] mx-auto px-4 py-10 md:py-14">
        <nav class="text-sm text-blue-100 mb-3">
            <a href="/" class="hover:text-white">{{ __('Home') }}</a>
            <span class="mx-2 opacity-50">›</span>
            <a href="{{ route('tools.index') }}" class="hover:text-white">{{ __('Tools') }}</a>
            <span class="mx-2 opacity-50">›</span>
            <span class="text-white">{{ __('Blocked Account Finder') }}</span>
        </nav>
        <h1 class="text-3xl md:text-5xl font-extrabold leading-tight drop-shadow mb-3 inline-flex items-center gap-3">
            <x-svg-icon name="banknotes" class="w-8 h-8 md:w-10 md:h-10" />
            {{ __('Blocked Account (Sperrkonto) Finder') }}
        </h1>
        <p class="text-lg md:text-xl text-blue-100 max-w-3xl">
            {!! __('You need to deposit <strong class="text-white">€11,904</strong> to a blocked account for the Germany student visa. Compare providers side by side and pick the most suitable.') !!}
        </p>

        {{-- Quick facts --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mt-6 max-w-[1400px]">
            <div class="bg-white/15 backdrop-blur rounded-lg p-3 ring-1 ring-white/20">
                <p class="text-2xl font-bold">€11.904</p>
                <p class="text-xs text-blue-100 mt-0.5">{{ __('Annual deposit (2026)') }}</p>
            </div>
            <div class="bg-white/15 backdrop-blur rounded-lg p-3 ring-1 ring-white/20">
                <p class="text-2xl font-bold">€992</p>
                <p class="text-xs text-blue-100 mt-0.5">{{ __('Monthly withdrawal limit') }}</p>
            </div>
            <div class="bg-white/15 backdrop-blur rounded-lg p-3 ring-1 ring-white/20">
                <p class="text-2xl font-bold">{{ $totals['total'] }}</p>
                <p class="text-xs text-blue-100 mt-0.5">{{ __('Providers compared') }}</p>
            </div>
            <div class="bg-white/15 backdrop-blur rounded-lg p-3 ring-1 ring-white/20">
                <p class="text-2xl font-bold">{{ $totals['with_insurance'] }}</p>
                <p class="text-xs text-blue-100 mt-0.5">{{ __('Insurance combo included') }}</p>
            </div>
        </div>
    </div>
</section>

<div class="max-w-[1400px] mx-auto px-4 py-10">

    {{-- Featured-snippet box — Google AIO targets this Q+concise-answer+steps pattern --}}
    <x-featured-snippet
        :question="__('How do I open a Sperrkonto for the German student visa?')"
        :answer="__('A blocked account (Sperrkonto) is a German bank account holding €11,904 for one year as proof of finances for your student visa. You can open it fully online with FinTech providers like Expatrio, Fintiba or Coracle in 2–7 business days; traditional banks take 2–6 weeks.')"
        :steps="[
            ['title' => __('Pick a provider'), 'description' => __('Compare setup fees, monthly cost, and insurance combo (table below).')],
            ['title' => __('Open the account online'), 'description' => __('Sign up + verify ID via video call (VideoIdent / POSTIdent).')],
            ['title' => __('Transfer €11,904'), 'description' => __('Wire the annual amount from your home country bank.')],
            ['title' => __('Receive the blocking confirmation'), 'description' => __('Use it for your visa appointment at the German embassy.')],
            ['title' => __('Withdraw monthly after arrival'), 'description' => __('Up to €992/month is unblocked automatically each month.')],
        ]"
    />
    {{-- ════════════════════════════════════════════════════════════════ --}}
    {{-- CALCULATOR — Sperrkonto Total Cost Calculator                       --}}
    {{-- ════════════════════════════════════════════════════════════════ --}}
    <section x-data="sperrkontoCalc()" class="mb-10 bg-white border-2 border-emerald-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="bg-gradient-to-r from-emerald-50 to-teal-50 border-b border-emerald-100 px-6 py-4">
            <h2 class="text-xl md:text-2xl font-bold text-gray-900 flex items-center gap-2">
                <x-svg-icon name="chart-bar" class="w-6 h-6" /> {{ __('Blocked Account Calculator') }}
            </h2>
            <p class="text-sm text-gray-600 mt-1">{{ __('Calculate the total cost of your Sperrkonto including monthly deposit + setup fee + service charges.') }}</p>
        </div>

        <div class="p-6 grid grid-cols-1 lg:grid-cols-5 gap-6">
            {{-- INPUTS --}}
            <div class="lg:col-span-3 space-y-5">
                {{-- Duration --}}
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="text-sm font-semibold text-gray-700">{{ __('Stay duration (months)') }}</label>
                        <span class="text-sm font-bold text-emerald-700" x-text="months + ' ' + (months == 1 ? '{{ __('month') }}' : '{{ __('months') }}')"></span>
                    </div>
                    <input type="range" min="6" max="36" step="1" x-model.number="months"
                           class="w-full accent-emerald-600">
                    <div class="flex justify-between text-xs text-gray-400 mt-1">
                        <span>6</span><span>12</span><span>18</span><span>24</span><span>30</span><span>36</span>
                    </div>
                </div>

                {{-- Monthly amount --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">{{ __('Monthly withdrawal limit (€)') }}</label>
                    <div class="flex items-center gap-2">
                        <input type="number" min="100" max="3000" step="1" x-model.number="monthlyEur"
                               class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                        <span class="text-xs text-gray-500 whitespace-nowrap">{{ __('Default: €992 (2026)') }}</span>
                    </div>
                </div>

                {{-- Provider --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">{{ __('Provider') }}</label>
                    <select x-model="providerKey" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                        @foreach ($providers as $p)
                            @php
                                $setupFee = $p->setup_fee_eur ?? 89;
                                $annualFee = $p->yearly_fee_eur ?? (($p->monthly_fee_eur ?? 5) * 12);
                            @endphp
                            <option value="{{ $p->slug }}" data-setup="{{ $setupFee }}" data-annual="{{ $annualFee }}">
                                {{ $p->name }} — setup €{{ $setupFee }} · annual €{{ $annualFee }}
                            </option>
                        @endforeach
                        <option value="__custom" data-setup="89" data-annual="60">{{ __('Custom amounts') }}</option>
                    </select>
                </div>

                {{-- Custom fees (manuel) --}}
                <div class="grid grid-cols-2 gap-3" x-show="providerKey === '__custom'" x-transition>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">{{ __('Setup fee (€)') }}</label>
                        <input type="number" min="0" step="1" x-model.number="customSetup"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">{{ __('Annual fee (€)') }}</label>
                        <input type="number" min="0" step="1" x-model.number="customAnnual"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                    </div>
                </div>

                {{-- Insurance combo toggle --}}
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" x-model="includeInsurance" class="w-5 h-5 rounded text-emerald-600 focus:ring-emerald-500">
                    <span class="text-sm font-medium text-gray-700">{{ __('Include health insurance (~€100/month)') }}</span>
                </label>
            </div>

            {{-- RESULT --}}
            <div class="lg:col-span-2 bg-gradient-to-br from-emerald-50 to-teal-50 border border-emerald-200 rounded-xl p-5">
                <p class="text-xs font-semibold uppercase tracking-wider text-emerald-700 mb-3">{{ __('Total cost') }}</p>

                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">{{ __('Blocked deposit') }}</span>
                        <span class="font-mono font-semibold text-gray-900" x-text="'€' + deposit.toLocaleString('de-DE')"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">{{ __('Setup fee') }}</span>
                        <span class="font-mono text-gray-700" x-text="'€' + setupFee.toLocaleString('de-DE')"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">{{ __('Service fees') }}</span>
                        <span class="font-mono text-gray-700" x-text="'€' + serviceFees.toLocaleString('de-DE')"></span>
                    </div>
                    <div class="flex justify-between" x-show="includeInsurance">
                        <span class="text-gray-600">{{ __('Insurance') }}</span>
                        <span class="font-mono text-gray-700" x-text="'€' + insuranceCost.toLocaleString('de-DE')"></span>
                    </div>
                </div>

                <div class="mt-4 pt-4 border-t border-emerald-200">
                    <div class="flex justify-between items-baseline">
                        <span class="text-sm font-bold text-emerald-900">{{ __('Total') }}</span>
                        <span class="text-3xl font-extrabold text-emerald-700 font-mono" x-text="'€' + total.toLocaleString('de-DE')"></span>
                    </div>
                    <p class="text-xs text-gray-500 mt-1" x-text="'≈ $' + (total * 1.08).toLocaleString('de-DE', {maximumFractionDigits: 0}) + ' USD'"></p>
                </div>

                <div class="mt-3 text-xs text-gray-500 leading-relaxed inline-flex items-start gap-1.5">
                    <x-svg-icon name="light-bulb" class="w-3.5 h-3.5 mt-0.5 shrink-0" />
                    {{ __('Deposit is your own money — you withdraw it monthly. Setup + service fees are paid to the provider.') }}
                </div>
            </div>
        </div>
    </section>

    <script>
        function sperrkontoCalc() {
            return {
                months: 12,
                monthlyEur: 992,
                providerKey: @json($providers->first()?->slug ?? '__custom'),
                customSetup: 89,
                customAnnual: 60,
                includeInsurance: false,

                get providerSetup() {
                    if (this.providerKey === '__custom') return this.customSetup;
                    const opt = document.querySelector(`option[value="${this.providerKey}"]`);
                    return opt ? Number(opt.dataset.setup) : 89;
                },
                get providerAnnual() {
                    if (this.providerKey === '__custom') return this.customAnnual;
                    const opt = document.querySelector(`option[value="${this.providerKey}"]`);
                    return opt ? Number(opt.dataset.annual) : 60;
                },
                get deposit()      { return this.monthlyEur * this.months; },
                get setupFee()     { return this.providerSetup; },
                get serviceFees()  { return Math.round(this.providerAnnual * this.months / 12); },
                get insuranceCost(){ return this.includeInsurance ? 100 * this.months : 0; },
                get total()        { return this.deposit + this.setupFee + this.serviceFees + this.insuranceCost; },
            };
        }
    </script>

    {{-- FILTER CHIPS --}}
    <div class="flex flex-wrap gap-2 mb-6 items-center">
        <span class="text-sm font-semibold text-gray-700 mr-2">{{ __('Filter:') }}</span>
        @php
            $chips = [
                null        => [__('All'),                'banknotes',        null],
                'cheapest'  => [__('Cheapest'),           'currency-euro',    null],
                'turkish'   => [__('Turkish support'),    null,               '🇹🇷'],
                'insurance' => [__('Insurance included'), 'shield-check',     null],
                'fast'      => [__('Fast (≤7 days)'),     'fire',             null],
                'fintech'   => ['FinTech',                'cursor-arrow-rays', null],
                'bank'      => [__('Bank'),               'building-office',  null],
            ];
        @endphp
        @foreach ($chips as $key => [$label, $iconName, $flag])
            <a href="{{ route('tools.blocked-account', $key ? ['filter' => $key] : []) }}"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-medium transition
                      {{ $filter === $key ? 'bg-primary-600 text-white shadow-sm' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                @if ($flag)
                    <span>{{ $flag }}</span>
                @else
                    <x-svg-icon name="{{ $iconName }}" class="w-3.5 h-3.5" />
                @endif
                <span>{{ $label }}</span>
            </a>
        @endforeach
    </div>

    {{-- SAĞLAYICI KARTLARI --}}
    @if ($providers->isEmpty())
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-8 text-center">
            <p class="text-amber-900 font-semibold">{{ __('No providers match this filter.') }}</p>
            <a href="{{ route('tools.blocked-account') }}" class="inline-block mt-3 text-primary-600 font-semibold hover:underline">← {{ __('All providers') }}</a>
        </div>
    @else
        <div class="space-y-4">
            @foreach ($providers as $p)
                <article class="bg-white border border-gray-200 rounded-2xl overflow-hidden hover:shadow-lg hover:border-primary-300 transition
                                @if($p->is_featured) ring-2 ring-amber-300 @endif">
                    <div class="grid grid-cols-1 md:grid-cols-[280px_1fr_240px]">
                        {{-- SOL: Logo + Tip + Featured badge --}}
                        <div class="p-6 bg-gradient-to-br from-gray-50 to-white border-r border-gray-100 flex flex-col items-center justify-center text-center">
                            @if ($p->is_featured)
                                <span class="inline-flex items-center gap-1 mb-2 px-2 py-0.5 text-xs font-bold rounded-full bg-amber-400 text-amber-900">
                                    <x-svg-icon name="star" class="w-3 h-3" />
                                    {{ __('FEATURED') }}
                                </span>
                            @endif
                            @if ($p->logo_url)
                                <img src="{{ $p->logo_url }}" alt="{{ $p->name }} logo" class="h-16 max-w-full object-contain mb-3" loading="lazy">
                            @else
                                <div class="w-16 h-16 rounded-xl bg-gradient-to-br from-indigo-500 to-blue-500 flex items-center justify-center text-white text-2xl font-extrabold mb-3">
                                    {{ mb_substr($p->name, 0, 2) }}
                                </div>
                            @endif
                            <h2 class="text-xl font-bold text-gray-900">{{ $p->name }}</h2>
                            <span class="inline-block mt-1 px-2 py-0.5 text-xs font-medium rounded
                                         {{ $p->type === 'fintech' ? 'bg-indigo-50 text-indigo-700' : 'bg-amber-50 text-amber-700' }}">
                                {{ $p->type_emoji }} {{ $p->type_label }}
                            </span>
                            @if ($p->backend_bank)
                                <p class="text-xs text-gray-500 mt-1">{{ __('Bank:') }} {{ $p->backend_bank }}</p>
                            @endif
                        </div>

                        {{-- ORTA: Özellikler --}}
                        <div class="p-6">
                            @if ($p->description)
                                <p class="text-gray-700 mb-4 leading-relaxed">{{ $p->description }}</p>
                            @endif

                            {{-- Fiyat satırı --}}
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-4">
                                <div>
                                    <p class="text-xs text-gray-500 uppercase tracking-wide font-semibold">{{ __('Setup') }}</p>
                                    <p class="font-bold text-gray-900">{{ $p->setup_fee_eur ? '€' . number_format((float)$p->setup_fee_eur, 0) : '—' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase tracking-wide font-semibold">{{ __('Monthly') }}</p>
                                    <p class="font-bold text-gray-900">{{ $p->monthly_fee_eur ? '€' . number_format((float)$p->monthly_fee_eur, 2) : '—' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase tracking-wide font-semibold">{{ __('Activation') }}</p>
                                    <p class="font-bold text-gray-900">{{ $p->activation_range }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase tracking-wide font-semibold">{{ __('1st year total') }}</p>
                                    <p class="font-bold text-primary-700">{{ $p->first_year_cost_eur ? '€' . number_format($p->first_year_cost_eur, 0) : '—' }}</p>
                                </div>
                            </div>

                            {{-- Özellik badge'leri --}}
                            <div class="flex flex-wrap gap-1.5 mb-3">
                                @if ($p->combo_insurance)
                                    <span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium rounded bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100">
                                        <x-svg-icon name="shield-check" class="w-3.5 h-3.5" /> {{ __('Insurance combo') }}
                                        @if ($p->insurance_provider_name)
                                            <span class="opacity-75">· {{ $p->insurance_provider_name }}</span>
                                        @endif
                                    </span>
                                @endif
                                @if ($p->has_mobile_app)
                                    <span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium rounded bg-blue-50 text-blue-700 ring-1 ring-blue-100">
                                        <x-svg-icon name="cursor-arrow-rays" class="w-3.5 h-3.5" /> {{ __('Mobile app') }}
                                    </span>
                                @endif
                                @if ($p->bafin_licensed)
                                    <span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium rounded bg-purple-50 text-purple-700 ring-1 ring-purple-100">
                                        <x-svg-icon name="check" class="w-3.5 h-3.5" /> {{ __('BaFin licensed') }}
                                    </span>
                                @endif
                                @if (is_array($p->supported_languages) && in_array('tr', $p->supported_languages))
                                    <span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium rounded bg-red-50 text-red-700 ring-1 ring-red-100">
                                        🇹🇷 {{ __('Turkish support') }}
                                    </span>
                                @endif
                            </div>

                            {{-- Pros (varsa, 3'e kadar) --}}
                            @if (is_array($p->pros) && count($p->pros))
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
                            @if ($p->cta_url)
                                <a href="{{ $p->cta_url }}" target="_blank" rel="noopener sponsored"
                                   class="block text-center bg-primary-600 hover:bg-primary-700 text-white font-bold py-3 px-4 rounded-lg transition shadow-sm hover:shadow">
                                    {{ __('Apply') }} →
                                </a>
                            @endif
                            <a href="{{ route('tools.blocked-account.show', $p->slug) }}"
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

    {{-- BİLGİLENDİRME / SSS --}}
    <section class="mt-12 grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-blue-50 border border-blue-100 rounded-xl p-6">
            <h2 class="text-xl font-bold text-blue-900 mb-3 inline-flex items-center gap-2"><x-svg-icon name="light-bulb" class="w-5 h-5" /> {{ __('What is Sperrkonto?') }}</h2>
            <p class="text-blue-800 leading-relaxed text-sm">
                {!! __('It is the blocked account mandatory for the Germany student visa. You deposit enough money for a year (<strong>€11,904</strong> for 2026) at a German bank, and after arrival you can withdraw <strong>€992</strong> monthly. After you get the visa and arrive in Germany the account is "unblocked".') !!}
            </p>
            {{-- Regülasyon rakamının kaynağı (GEO #3 güven sinyali + halüsinasyon savunması) --}}
            <x-source-note
                :sources="[['name' => 'Auswärtiges Amt', 'url' => 'https://www.auswaertiges-amt.de/'], 'BAföG-Höchstsatz']"
                updated="2026-06-04"
                class="!bg-white/60 !border-blue-100"
            />
        </div>

        @if (app()->getLocale() === 'tr')
        <div class="bg-emerald-50 border border-emerald-100 rounded-xl p-6">
            <h2 class="text-xl font-bold text-emerald-900 mb-3">🇹🇷 Türk öğrenciler için</h2>
            <ul class="text-emerald-800 text-sm space-y-2 leading-relaxed">
                <li>• <strong>İstanbul/Ankara Konsolosluğu</strong> tüm büyük sağlayıcıları kabul eder (Expatrio, Fintiba, Coracle, Deutsche Bank).</li>
                <li>• Online açılabilen FinTech'ler (Expatrio/Fintiba/Coracle) <strong>1-5 gün</strong> içinde aktif olur.</li>
                <li>• Deutsche Bank gibi geleneksel bankalar <strong>4-6 hafta</strong> sürer.</li>
                <li>• Para transferi için Wise/Western Union maliyeti hesaba kat (~%1-2).</li>
            </ul>
        </div>
        @else
        <div class="bg-emerald-50 border border-emerald-100 rounded-xl p-6">
            <h2 class="text-xl font-bold text-emerald-900 mb-3 inline-flex items-center gap-2"><x-svg-icon name="globe" class="w-5 h-5" /> {{ __('For international students') }}</h2>
            <ul class="text-emerald-800 text-sm space-y-2 leading-relaxed">
                <li>{!! __('• <strong>German embassies/consulates</strong> accept all major providers (Expatrio, Fintiba, Coracle, Deutsche Bank).') !!}</li>
                <li>{!! __('• Online FinTechs (Expatrio/Fintiba/Coracle) are active within <strong>1-5 days</strong>.') !!}</li>
                <li>{!! __('• Traditional banks like Deutsche Bank take <strong>4-6 weeks</strong>.') !!}</li>
                <li>{{ __('• Account for the cost of Wise/Western Union money transfers (~1-2%).') }}</li>
            </ul>
        </div>
        @endif
    </section>

    {{-- Cross-link --}}
    <section class="mt-8 grid grid-cols-1 sm:grid-cols-3 gap-3">
        <a href="{{ route('tools.visa-cost') }}" class="block bg-white border border-gray-200 rounded-xl p-4 hover:border-primary-400 hover:shadow-sm transition">
            <p class="mb-1 text-primary-600"><x-svg-icon name="banknotes" class="w-6 h-6" /></p>
            <p class="font-bold text-gray-900">{{ __('Visa Cost') }}</p>
            <p class="text-xs text-gray-500 mt-0.5">{{ __('Total visa budget') }}</p>
        </a>
        <a href="{{ route('tools.budget-planner') }}" class="block bg-white border border-gray-200 rounded-xl p-4 hover:border-primary-400 hover:shadow-sm transition">
            <p class="mb-1 text-primary-600"><x-svg-icon name="chart-bar" class="w-6 h-6" /></p>
            <p class="font-bold text-gray-900">{{ __('Budget Planner') }}</p>
            <p class="text-xs text-gray-500 mt-0.5">{{ __('Monthly living expenses') }}</p>
        </a>
        <a href="{{ route('tools.deadlines') }}" class="block bg-white border border-gray-200 rounded-xl p-4 hover:border-primary-400 hover:shadow-sm transition">
            <p class="mb-1 text-primary-600"><x-svg-icon name="calendar" class="w-6 h-6" /></p>
            <p class="font-bold text-gray-900">{{ __('Application Calendar') }}</p>
            <p class="text-xs text-gray-500 mt-0.5">{{ __('Deadlines + ICS export') }}</p>
        </a>
    </section>

    {{-- ════════════════════════════════════════════════════════════════ --}}
    {{-- SPERRKONTO BY COUNTRY                                               --}}
    {{-- ════════════════════════════════════════════════════════════════ --}}
    <section class="mt-12 mb-8">
        <div class="flex items-end justify-between mb-5 flex-wrap gap-3">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 inline-flex items-center gap-2"><x-svg-icon name="globe" class="w-6 h-6" /> {{ __('Sperrkonto by Country') }}</h2>
                <p class="text-sm text-gray-600 mt-1">{{ __('Country-specific guides for the top 10 international student origins.') }}</p>
            </div>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3">
            @foreach (\App\Http\Controllers\Web\BlockedAccountController::countriesData() as $key => $c)
                <a href="{{ route('tools.blocked-account.country', $key) }}"
                   class="group bg-white border border-gray-200 hover:border-primary-400 hover:shadow-md rounded-xl px-4 py-3 transition flex items-center gap-3">
                    <span class="text-2xl">{{ $c['flag'] }}</span>
                    <div class="min-w-0">
                        <p class="font-semibold text-gray-900 group-hover:text-primary-700 truncate">{{ $c['name'] }}</p>
                        <p class="text-xs text-gray-500">{{ __('Sperrkonto guide') }}</p>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    {{-- Disclaimer --}}
    <p class="text-xs text-gray-400 mt-8 text-center max-w-3xl mx-auto">
        {{ __('Prices and features are taken from the providers\' own sites and may change. Always verify on the official site before applying. :brand has an affiliate partnership with some providers — your application costs you nothing extra but supports the platform.', ['brand' => brand('name')]) }}
    </p>
</div>

{{-- Auto-generated FAQ (AIO + Featured Snippet eligibility) --}}
<x-faq-section
    :title="__('Frequently Asked Questions about Sperrkonto')"
    :subtitle="__('Everything you need to know about opening a blocked account in Germany')"
    :faqs="\App\Support\PageFaq::forBlockedAccount()"
/>
@endsection
