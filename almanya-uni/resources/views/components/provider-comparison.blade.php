@props([
    'providers',     // Collection<BlockedAccountProvider|HealthInsuranceProvider>
    'kind',          // 'sperrkonto' | 'insurance'
])

{{--
    "Bir bakışta karşılaştır" — karar-hızlandıran kompakt tablo (kartların ÜSTÜNDE).
    Sağlayıcılar satır, anahtar metrikler sütun, her satırda takipli CTA. Mobilde
    yatay kaydırma. ≥2 sağlayıcı yoksa render etme.
--}}
@php $providers = collect($providers); @endphp
@if ($providers->count() >= 2)
<section class="mb-8" aria-label="{{ __('Quick comparison') }}">
    <h2 class="text-lg md:text-xl font-bold text-gray-900 mb-3 inline-flex items-center gap-2">
        <x-svg-icon name="target" class="w-5 h-5 text-primary-600" />
        {{ __('Compare at a glance') }}
    </h2>
    <div class="overflow-x-auto rounded-xl border border-gray-200 shadow-sm">
        <table class="w-full text-sm whitespace-nowrap">
            <thead class="bg-gray-50 text-gray-600 text-left">
                <tr>
                    <th class="px-4 py-3 sticky left-0 bg-gray-50 z-10">{{ __('Provider') }}</th>
                    @if ($kind === 'sperrkonto')
                        <th class="px-4 py-3 text-right">{{ __('1st year total') }}</th>
                        <th class="px-4 py-3 text-right">{{ __('Monthly') }}</th>
                        <th class="px-4 py-3">{{ __('Activation') }}</th>
                        <th class="px-4 py-3 text-center">{{ __('Insurance') }}</th>
                    @else
                        <th class="px-4 py-3 text-right">{{ __('Monthly') }}</th>
                        <th class="px-4 py-3">{{ __('Type') }}</th>
                        <th class="px-4 py-3 text-center">{{ __('Visa') }}</th>
                        <th class="px-4 py-3 text-center">{{ __('English') }}</th>
                    @endif
                    <th class="px-4 py-3 text-right"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach ($providers as $p)
                    <tr class="hover:bg-gray-50/70 @if($p->is_featured) bg-amber-50/40 @endif">
                        {{-- Sağlayıcı --}}
                        <td class="px-4 py-3 sticky left-0 bg-white z-10 @if($p->is_featured) bg-amber-50/40 @endif">
                            <a href="{{ route($kind === 'sperrkonto' ? 'tools.blocked-account.show' : 'tools.health-insurance.show', $p->slug) }}"
                               class="font-bold text-gray-900 hover:text-primary-600 inline-flex items-center gap-1.5">
                                @if ($p->is_featured)<x-svg-icon name="star" class="w-3.5 h-3.5 text-amber-400" />@endif
                                {{ $p->name }}
                            </a>
                        </td>

                        @if ($kind === 'sperrkonto')
                            <td class="px-4 py-3 text-right font-extrabold text-primary-700">{{ $p->first_year_cost_eur ? '€' . number_format($p->first_year_cost_eur, 0) : '—' }}</td>
                            <td class="px-4 py-3 text-right text-gray-800">{{ $p->monthly_fee_eur ? '€' . number_format((float) $p->monthly_fee_eur, 2) : '—' }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $p->activation_range }}</td>
                            <td class="px-4 py-3 text-center">
                                @if ($p->combo_insurance)
                                    <x-svg-icon name="check" class="w-4 h-4 text-emerald-600 inline" />
                                @else
                                    <span class="text-gray-300">—</span>
                                @endif
                            </td>
                        @else
                            <td class="px-4 py-3 text-right font-extrabold text-primary-700">{{ $p->monthly_range }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $p->type_label }}</td>
                            <td class="px-4 py-3 text-center">
                                @if ($p->accepted_for_visa)
                                    <x-svg-icon name="check" class="w-4 h-4 text-emerald-600 inline" />
                                @else
                                    <span class="text-gray-300">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if ($p->english_support)
                                    <x-svg-icon name="check" class="w-4 h-4 text-emerald-600 inline" />
                                @else
                                    <span class="text-gray-300">—</span>
                                @endif
                            </td>
                        @endif

                        {{-- Takipli CTA --}}
                        <td class="px-4 py-3 text-right">
                            <x-affiliate-link :provider="$p" ctx="comparison"
                                class="inline-block bg-primary-600 hover:bg-primary-700 text-white font-bold text-xs py-2 px-3.5 rounded-lg transition">
                                {{ __('Apply') }} →
                            </x-affiliate-link>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <p class="text-[11px] text-gray-400 mt-2">{{ __('Affiliate links — no extra cost to you. We may earn a commission.') }}</p>
</section>
@endif
