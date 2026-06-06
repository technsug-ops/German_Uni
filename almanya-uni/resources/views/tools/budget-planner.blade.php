@extends('layouts.app')

@section('title', __('Budget Planner — Monthly Student Budget for Germany') . '  — ' . brand('name'))

<x-seo
    :title="__('Budget Planner — Monthly Student Budget for Germany')"
    :description="__('Monthly income + expense + savings goal. City-based, including Werkstudent income. A planning tool for international students.')"
/>

<x-tool-schema tool="budget-planner" />

@section('content')

{{-- HERO --}}
<section class="bg-gradient-to-br from-emerald-700 via-emerald-600 to-teal-500 text-white">
    <div class="max-w-[1400px] mx-auto px-4 py-10 md:py-14">
        <nav class="text-sm text-emerald-100 mb-3">
            <a href="/" class="hover:text-white">{{ __('Home') }}</a>
            <span class="mx-2 opacity-50">›</span>
            <a href="{{ route('tools.index') }}" class="hover:text-white">{{ __('Tools') }}</a>
            <span class="mx-2 opacity-50">›</span>
            <span class="text-white">{{ __('Budget Planner') }}</span>
        </nav>
        <h1 class="text-3xl md:text-5xl font-extrabold leading-tight drop-shadow mb-3 inline-flex items-center gap-3">
            <x-svg-icon name="arrow-trending-up" class="w-8 h-8 md:w-10 md:h-10" />
            {{ __('Budget Planner') }}
        </h1>
        <p class="text-lg md:text-xl text-emerald-100 max-w-3xl">
            {{ __('Add up your monthly income sources + city-based expenses, set your savings goal. See instantly whether your budget is enough and by how much.') }}
        </p>
    </div>
</section>

<div class="max-w-[1400px] mx-auto px-4 py-10">
    <form method="GET" action="{{ route('tools.budget-planner') }}" class="grid grid-cols-1 lg:grid-cols-[1fr_360px] gap-8">

        {{-- ───── SOL: Inputlar ───── --}}
        <div class="space-y-5">
            {{-- 1. ŞEHİR + HOUSING --}}
            <section class="bg-white border border-gray-200 rounded-xl p-5">
                <h3 class="font-semibold text-gray-900 mb-3 flex items-center gap-2"><x-svg-icon name="building-office" class="w-6 h-6" /> {{ __('City and accommodation') }}</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <select name="city" onchange="this.form.submit()" class="px-3 py-2 rounded-lg border border-gray-300 text-sm">
                        <option value="">{{ __('Select city...') }}</option>
                        @foreach ($cities as $c)
                            <option value="{{ $c->id }}" @selected($selected_city_id == $c->id)>{{ $c->name }}</option>
                        @endforeach
                    </select>
                    <select name="housing" onchange="this.form.submit()" class="px-3 py-2 rounded-lg border border-gray-300 text-sm">
                        <option value="wg" @selected($housing === 'wg')>{{ __('WG (shared flat)') }}</option>
                        <option value="studio" @selected($housing === 'studio')>{{ __('Studio') }}</option>
                        <option value="apartment" @selected($housing === 'apartment')>{{ __('Apartment') }}</option>
                    </select>
                    <select name="lifestyle" onchange="this.form.submit()" class="px-3 py-2 rounded-lg border border-gray-300 text-sm">
                        <option value="frugal" @selected($lifestyle === 'frugal')>{{ __('Frugal') }}</option>
                        <option value="normal" @selected($lifestyle === 'normal')>{{ __('Normal') }}</option>
                        <option value="comfortable" @selected($lifestyle === 'comfortable')>{{ __('Comfortable') }}</option>
                    </select>
                </div>
                @if ($expense)
                    <p class="text-xs text-gray-500 mt-3">
                        {{ __('Total monthly expense:') }} <strong class="text-gray-900">{{ number_format($expense_total, 0, ',', '.') }} €</strong>
                        <a href="{{ route('tools.cost-of-living', ['city' => $selected_city_id, 'housing' => $housing, 'lifestyle' => $lifestyle]) }}" class="ml-2 text-emerald-600 hover:underline">{{ __('Detailed breakdown') }} →</a>
                    </p>
                @else
                    <p class="text-xs text-amber-600 mt-3 inline-flex items-center gap-1"><x-svg-icon name="exclamation-triangle" class="w-3.5 h-3.5" /> {{ __('Pick a city → an automatic expense estimate will appear.') }}</p>
                @endif
            </section>

            {{-- 2. GELİR KAYNAKLARI --}}
            <section class="bg-white border border-gray-200 rounded-xl p-5">
                <h3 class="font-semibold text-gray-900 mb-3 flex items-center gap-2"><x-svg-icon name="banknotes" class="w-6 h-6" /> {{ __('Monthly income sources') }}</h3>

                @php
                    $incomeItems = [
                        'sperrkonto'  => ['banknotes',     __('Sperrkonto withdrawal'), __('€992 standard (annual €11,904 ÷ 12)')],
                        'scholarship' => ['trophy',        __('Scholarship (monthly)'), __('DAAD €992, other scholarships range €300-1500')],
                        'job'         => ['briefcase',     __('Work income (Werkstudent etc.)'), __('Max €538 under Werkstudent status (tax advantage)')],
                        'family'      => ['users',         __('Family support'), __('Money transfer from home')],
                    ];
                @endphp

                <div class="space-y-3">
                    @foreach ($incomeItems as $key => [$icon, $lbl, $desc])
                        <div class="flex items-start gap-3 pb-3 border-b border-gray-100 last:border-0">
                            <div class="text-emerald-600 shrink-0"><x-svg-icon name="{{ $icon }}" class="w-6 h-6" /></div>
                            <div class="flex-1">
                                <label class="text-sm font-medium text-gray-900 block">{{ $lbl }}</label>
                                <p class="text-xs text-gray-500">{{ $desc }}</p>
                            </div>
                            <div class="flex items-center gap-1 shrink-0">
                                <input type="number" name="{{ $key }}" value="{{ $income[$key] }}"
                                       min="0" max="5000" step="50"
                                       onchange="this.form.submit()"
                                       class="w-24 px-2 py-1.5 text-right font-semibold border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500">
                                <span class="text-gray-600 font-semibold">€</span>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4 pt-3 border-t-2 border-emerald-200 flex items-center justify-between bg-emerald-50 -mx-5 -mb-5 px-5 py-3 rounded-b-xl">
                    <strong class="text-gray-900">{{ __('Total monthly income') }}</strong>
                    <strong class="text-xl text-emerald-700">{{ number_format($total_income, 0, ',', '.') }} €</strong>
                </div>
            </section>

            {{-- 3. TASARRUF HEDEFİ --}}
            <section class="bg-white border border-gray-200 rounded-xl p-5">
                <h3 class="font-semibold text-gray-900 mb-3 flex items-center gap-2"><x-svg-icon name="target" class="w-6 h-6" /> {{ __('Monthly savings goal') }}</h3>
                <div class="flex items-center gap-3">
                    <input type="range" name="savings_goal" value="{{ $savings_goal }}"
                           min="0" max="500" step="25"
                           oninput="document.getElementById('savings-val').textContent = this.value + ' €'; this.form.requestSubmit();"
                           class="flex-1 accent-emerald-600">
                    <span id="savings-val" class="text-lg font-bold text-emerald-700 w-20 text-right">{{ $savings_goal }} €</span>
                </div>
                <p class="text-xs text-gray-500 mt-2">{{ __('Amount you want to save for an emergency fund, vacation, or returning home.') }}</p>
            </section>
        </div>

        {{-- ───── SAĞ: Özet ───── --}}
        <aside class="space-y-4">
            <div class="bg-white border-2 border-emerald-500 rounded-xl p-6 sticky top-20 shadow-lg">
                <h3 class="text-sm font-bold uppercase tracking-wider text-gray-500 mb-4 inline-flex items-center gap-1.5"><x-svg-icon name="chart-bar" class="w-4 h-4" /> {{ __('Budget Summary') }}</h3>

                <div class="space-y-3 text-sm">
                    <div class="flex items-baseline justify-between">
                        <span class="text-gray-600 inline-flex items-center gap-1"><x-svg-icon name="plus" class="w-3.5 h-3.5" /> {{ __('Income') }}</span>
                        <strong class="text-emerald-700">{{ number_format($total_income, 0, ',', '.') }} €</strong>
                    </div>
                    <div class="flex items-baseline justify-between pb-3 border-b border-gray-100">
                        <span class="text-gray-600 inline-flex items-center gap-1"><x-svg-icon name="minus" class="w-3.5 h-3.5" /> {{ __('Expense') }}</span>
                        <strong class="text-red-700">{{ number_format($expense_total, 0, ',', '.') }} €</strong>
                    </div>
                    <div class="flex items-baseline justify-between">
                        <span class="text-gray-700 font-semibold inline-flex items-center gap-1"><x-svg-icon name="scale" class="w-3.5 h-3.5" /> {{ __('Net (income - expense)') }}</span>
                        <strong class="text-xl {{ $net_balance >= 0 ? 'text-emerald-700' : 'text-red-700' }}">
                            {{ ($net_balance >= 0 ? '+' : '') . number_format($net_balance, 0, ',', '.') }} €
                        </strong>
                    </div>
                    <div class="flex items-baseline justify-between text-xs">
                        <span class="text-gray-500 inline-flex items-center gap-1"><x-svg-icon name="target" class="w-3.5 h-3.5" /> {{ __('Goal') }}</span>
                        <span class="font-semibold text-gray-700">{{ $savings_goal }} € / {{ __('month') }}</span>
                    </div>
                </div>

                {{-- Durum --}}
                <div class="mt-5 p-3 rounded-lg {{ $covers_goal && $expense_total > 0 ? 'bg-emerald-50 border border-emerald-200' : ($net_balance < 0 ? 'bg-red-50 border border-red-200' : 'bg-amber-50 border border-amber-200') }}">
                    @if ($expense_total === 0)
                        <p class="text-sm font-semibold text-amber-800 inline-flex items-center gap-1.5"><x-svg-icon name="exclamation-triangle" class="w-4 h-4" /> {{ __('Pick a city first') }}</p>
                    @elseif ($net_balance < 0)
                        <p class="text-sm font-semibold text-red-800 inline-flex items-center gap-1.5"><x-svg-icon name="x-circle" class="w-4 h-4" /> {{ __('Your budget is not enough') }}</p>
                        <p class="text-xs text-red-700 mt-1">{{ __(':amount€ short. Increase income or reduce expenses.', ['amount' => abs($net_balance)]) }}</p>
                    @elseif ($covers_goal)
                        <p class="text-sm font-semibold text-emerald-800 inline-flex items-center gap-1.5"><x-svg-icon name="check-circle" class="w-4 h-4" /> {{ __('Goal achieved!') }}</p>
                        <p class="text-xs text-emerald-700 mt-1">{{ __(':amount€ extra savings available.', ['amount' => $net_balance - $savings_goal]) }}</p>
                    @else
                        <p class="text-sm font-semibold text-amber-800 inline-flex items-center gap-1.5"><x-svg-icon name="exclamation-triangle" class="w-4 h-4" /> {{ __('Not enough for the goal') }}</p>
                        <p class="text-xs text-amber-700 mt-1">{{ __(':amount€ more needed.', ['amount' => $savings_goal - $net_balance]) }}</p>
                    @endif
                </div>

                {{-- Suggestions --}}
                @if (! empty($suggestions))
                    <div class="mt-4 space-y-2">
                        @foreach ($suggestions as $s)
                            @php
                                $cls = match($s['type']) {
                                    'success' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
                                    'warning' => 'bg-amber-50 text-amber-800 border-amber-200',
                                    'info'    => 'bg-blue-50 text-blue-800 border-blue-200',
                                    default   => 'bg-gray-50 text-gray-700 border-gray-200',
                                };
                            @endphp
                            <div class="text-xs px-3 py-2 rounded-lg border {{ $cls }} leading-relaxed">
                                {{ $s['msg'] }}
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- Cross-links --}}
                <div class="mt-5 pt-4 border-t border-gray-100 space-y-2">
                    <a href="{{ route('scholarships.daad') }}" class="block text-center text-sm font-semibold text-emerald-700 hover:underline"><span class="inline-flex items-center gap-1.5"><x-svg-icon name="trophy" class="w-4 h-4" /> {{ __('Find a scholarship (boost income)') }}</span></a>
                    @php($werkstudentGuide = published_post_url('almanyada-ogrenci-isleri-20-saat-kurali-vergi-ve-saglik-sigortasi-rehberi'))
                    @if ($werkstudentGuide)
                    <a href="{{ $werkstudentGuide }}" class="block text-center text-sm font-semibold text-emerald-700 hover:underline"><span class="inline-flex items-center gap-1.5"><x-svg-icon name="briefcase" class="w-4 h-4" /> {{ __('Werkstudent guide') }}</span></a>
                    @endif
                    <a href="{{ route('tools.cost-of-living') }}" class="block text-center text-sm font-semibold text-emerald-700 hover:underline"><span class="inline-flex items-center gap-1.5"><x-svg-icon name="banknotes" class="w-4 h-4" /> {{ __('City cost breakdown') }}</span></a>
                </div>
            </div>
        </aside>
    </form>
</div>

{{-- Auto-FAQ (AIO + Featured Snippet) --}}
<x-faq-section
    :title="__('Frequently Asked Questions about Student Budget Planning')"
    :subtitle="__('Werkstudent rules, Bafög eligibility, and pre-arrival savings')"
    :faqs="\App\Support\PageFaq::forBudgetPlanner()"
/>
@endsection
