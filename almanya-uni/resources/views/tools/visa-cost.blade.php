@extends('layouts.app')

@section('title', __('Visa Cost Calculator — Total Budget for the German Student Visa') . '  — ' . brand('name'))

<x-seo
    :title="__('Visa Cost Calculator')"
    :description="__('Add up every cost of the German student visa process step by step: Sperrkonto, visa fee, uni-assist, language exam, translation, flight. Up-to-date 2025 figures.')"
/>

@section('content')
{{-- HERO --}}
<section class="bg-gradient-to-br from-emerald-700 via-emerald-600 to-teal-500 text-white">
    <div class="max-w-[1400px] mx-auto px-4 py-10 md:py-14">
        <nav class="text-sm text-emerald-100 mb-3">
            <a href="/" class="hover:text-white">{{ __('Home') }}</a>
            <span class="mx-2 opacity-50">›</span>
            <a href="{{ route('tools.index') }}" class="hover:text-white">{{ __('Tools') }}</a>
            <span class="mx-2 opacity-50">›</span>
            <span class="text-white">{{ __('Visa Cost') }}</span>
        </nav>
        <h1 class="text-3xl md:text-5xl font-extrabold leading-tight drop-shadow mb-3">
            💸 {{ __('Visa Cost Calculator') }}
        </h1>
        <p class="text-lg md:text-xl text-emerald-100 max-w-3xl">
            {{ __('Every cost of the German student visa process on one page. From Sperrkonto to the flight ticket — what you must not skip, and what you get back.') }}
        </p>
    </div>
</section>

<div class="max-w-[1400px] mx-auto px-4 py-10">
    <form method="GET" action="{{ route('tools.visa-cost') }}" class="grid grid-cols-1 lg:grid-cols-[1fr_360px] gap-8">
        {{-- ───── Sol: Kalemler ───── --}}
        <div class="space-y-3">
            @foreach ($items as $key => $item)
                @php $value = $values[$key]; $isRecov = !empty($item['recoverable']); @endphp
                <div class="bg-white border border-gray-200 rounded-xl p-5 hover:border-primary-300 transition">
                    <div class="flex items-start gap-3">
                        <div class="text-3xl shrink-0">{{ $item['icon'] }}</div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-baseline justify-between gap-2 flex-wrap">
                                <h3 class="font-semibold text-gray-900">
                                    {{ $item['label'] }}
                                    @if ($item['required'])
                                        <span class="ml-1 text-xs px-1.5 py-0.5 rounded bg-red-50 text-red-700 font-medium">{{ __('Required') }}</span>
                                    @else
                                        <span class="ml-1 text-xs px-1.5 py-0.5 rounded bg-gray-100 text-gray-600">{{ __('Optional') }}</span>
                                    @endif
                                    @if ($isRecov)
                                        <span class="ml-1 text-xs px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-700 font-medium">↻ {{ __('You get it back') }}</span>
                                    @endif
                                </h3>
                                <div class="flex items-center gap-2">
                                    <input type="number" name="{{ $key }}" value="{{ $value }}"
                                           min="{{ $item['min'] }}" max="{{ $item['max'] }}" step="5"
                                           class="w-28 px-3 py-1.5 text-right font-bold text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                           onchange="this.form.submit()">
                                    <span class="font-semibold text-gray-700">€</span>
                                </div>
                            </div>
                            <p class="text-sm text-gray-600 mt-1.5">{{ $item['desc'] }}</p>
                            <input type="range" name="{{ $key }}" value="{{ $value }}"
                                   min="{{ $item['min'] }}" max="{{ $item['max'] }}" step="10"
                                   class="w-full mt-3 accent-primary-600"
                                   oninput="this.form.submit()"
                                   style="display:none">
                        </div>
                    </div>
                </div>
            @endforeach

            <p class="text-xs text-gray-500 pt-2 px-1">
                💡 {!! __('<strong>Tip:</strong> You can change the number next to each item to match your own situation. The form saves automatically.') !!}
            </p>
        </div>

        {{-- ───── Sağ: Sticky özet ───── --}}
        <aside class="space-y-4">
            <div class="bg-white border-2 border-emerald-500 rounded-xl p-6 sticky top-20 shadow-lg">
                <h3 class="text-sm font-bold uppercase tracking-wider text-gray-500 mb-4">📊 {{ __('Summary') }}</h3>

                <div class="space-y-3">
                    <div class="flex items-baseline justify-between pb-3 border-b border-gray-100">
                        <span class="text-sm text-gray-600">{{ __('Total expense') }}</span>
                        <strong class="text-2xl font-extrabold text-gray-900">{{ number_format($total, 0, ',', '.') }} €</strong>
                    </div>

                    <div class="flex items-baseline justify-between text-sm">
                        <span class="text-gray-600">↻ {{ __('You get back') }}</span>
                        <span class="font-semibold text-emerald-700">{{ number_format($recoverable, 0, ',', '.') }} €</span>
                    </div>
                    <p class="text-xs text-gray-500 -mt-1 italic">{{ __('You withdraw €992 every month from Sperrkonto; over 12 months you receive everything back.') }}</p>

                    <div class="flex items-baseline justify-between text-sm pt-3 border-t border-gray-100">
                        <span class="text-gray-700 font-semibold">{{ __('Net spending') }}</span>
                        <strong class="text-lg font-bold text-red-700">{{ number_format($non_recoverable, 0, ',', '.') }} €</strong>
                    </div>
                    <p class="text-xs text-gray-500 -mt-1">{{ __('The part you cannot recover (visa, insurance, flight, etc.)') }}</p>
                </div>

                <div class="mt-6 pt-5 border-t border-gray-100 space-y-3">
                    <a href="{{ route('blog.show', 'sperrkonto-2025-tam-rehber-almanya-vizesi-icin-bloke-hesap') }}"
                       class="block text-center w-full px-4 py-2.5 rounded-lg bg-primary-600 hover:bg-primary-700 text-white font-semibold text-sm transition">
                        📚 {{ __('Sperrkonto Guide') }}
                    </a>
                    <a href="{{ route('blog.show', 'almanya-vize-randevusu-nasil-alinir-idata-cilesi-ve-hizli-cozum-yollari') }}"
                       class="block text-center w-full px-4 py-2.5 rounded-lg bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-semibold text-sm transition">
                        🗓️ {{ __('Visa Appointment Guide') }}
                    </a>
                    <a href="{{ route('scholarships.daad') }}"
                       class="block text-center w-full px-4 py-2.5 rounded-lg bg-white border border-emerald-300 hover:bg-emerald-50 text-emerald-700 font-semibold text-sm transition">
                        🎖️ {{ __('DAAD Scholarships (reduce the cost)') }}
                    </a>
                </div>

                <p class="text-xs text-gray-400 mt-5 leading-relaxed border-t border-gray-100 pt-4">
                    ℹ️ {{ __('Numbers are average values for 2025. Consulate fees and insurance prices may change over time.') }}
                </p>
            </div>

            {{-- "Genel ortalama" karşılaştırma --}}
            <div class="bg-gradient-to-br from-amber-50 to-orange-50 border border-amber-200 rounded-xl p-5">
                <p class="text-xs font-bold uppercase tracking-wider text-amber-800 mb-2">📈 {{ __('Comparison') }}</p>
                <p class="text-sm text-amber-900 leading-relaxed">
                    <strong>{{ __('International student average:') }}</strong> ~13.000 € ({{ __('Sperrkonto included') }})<br>
                    <strong>{{ __('Your total:') }}</strong>
                    <span class="font-bold {{ $total > 13500 ? 'text-red-700' : ($total < 12500 ? 'text-emerald-700' : 'text-amber-900') }}">
                        {{ number_format($total, 0, ',', '.') }} €
                    </span>
                </p>
            </div>
        </aside>
    </form>
</div>
@endsection
