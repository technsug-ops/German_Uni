@extends('layouts.app')

@section('title', $config['title'] . '  — ' . brand('name'))

<x-seo :title="$config['title']" :description="$config['description']" />

<x-json-ld :data="\App\Support\Seo::breadcrumbs([
    ['name' => __('Home'), 'url' => route('home')],
    ['name' => __('Rankings'), 'url' => route('rankings.index')],
    ['name' => $config['title'], 'url' => route('rankings.show', $config['slug'])],
])" />

@if (!empty($universities))
    <x-json-ld :data="\App\Support\Seo::itemList(
        $config['title'],
        array_map(fn ($u) => ['url' => route('universities.show', $u['slug']), 'name' => $u['name_de']], $universities),
        $config['description']
    )" />
@endif

@php
    $typeLabel = fn ($t) => match ($t) {
        'public' => __('Public'),
        'private' => __('Private'),
        'applied_sciences' => __('Applied Sciences'),
        'art' => __('Art'),
        'religion' => __('Religious'),
        default => $t ? ucfirst($t) : '-',
    };
    $countLabel = $config['count_label'] ?? 'öğrenci';
    $showQsBreakdown = $countLabel === 'qs_rank';
    $qsIndicators = \App\Models\University::QS_INDICATORS;
    // Yeni: tüm ranking tipleri için methodology metadata (i18n hazır)
    $methodology = \App\Services\RankingService::methodologyFor($countLabel);
@endphp

@section('content')
<div class="bg-gradient-to-r from-primary-500 to-primary-700 text-white py-12">
    <div class="max-w-[1400px] mx-auto px-4">
        <nav class="text-sm text-primary-100 mb-3">
            <a href="{{ route('rankings.index') }}" class="hover:text-white">← {{ __('All Rankings') }}</a>
        </nav>
        <h1 class="text-3xl md:text-5xl font-bold mb-4 leading-tight">{{ $config['title'] }}</h1>
        <p class="text-lg text-primary-100 max-w-3xl">{{ $config['description'] }}</p>
    </div>
</div>

<div class="max-w-[1400px] mx-auto px-4 py-12">
    @if (empty($universities))
        <div class="bg-yellow-50 border border-yellow-300 rounded-lg p-8 text-center">
            <p class="text-yellow-900 font-semibold">{{ __('Not enough data for this ranking yet.') }}</p>
            <a href="{{ route('rankings.index') }}" class="inline-block mt-4 text-primary-600 hover:text-primary-800 font-semibold">
                {{ __('Back to other rankings') }} →
            </a>
        </div>
    @else
        <div class="mb-6 text-gray-600">
            {!! __('<strong>:n universities</strong> listed.', ['n' => count($universities)]) !!}
        </div>

        <div class="space-y-4">
            @foreach ($universities as $uni)
                @php
                    $overall = $showQsBreakdown && !empty($uni['qs_overall_score']) ? (float)$uni['qs_overall_score'] : null;
                    $donutColor = $overall === null ? '#cbd5e1' : ($overall >= 80 ? '#10b981' : ($overall >= 60 ? '#3b82f6' : ($overall >= 40 ? '#f59e0b' : '#fb7185')));
                    $donutDeg = $overall !== null ? round($overall * 3.6) : 0;
                @endphp
                <div class="group relative bg-white border border-gray-200 hover:border-primary-400 hover:shadow-xl hover:-translate-y-0.5 transition-all duration-200 rounded-2xl overflow-hidden">
                <a href="{{ route('universities.show', $uni['slug']) }}"
                   class="flex items-center gap-5 p-5">

                    {{-- Rank ribbon --}}
                    <div class="shrink-0 w-14 h-14 rounded-2xl bg-gradient-to-br from-primary-600 to-primary-700 text-white flex items-center justify-center shadow-md">
                        <div class="text-center leading-none">
                            <div class="text-[9px] uppercase tracking-wider opacity-80">{{ __('Rank') }}</div>
                            <div class="text-xl font-extrabold">{{ $uni['rank'] }}</div>
                        </div>
                    </div>

                    {{-- Logo (büyük) --}}
                    <div class="shrink-0">
                        @if ($uni['logo_url'])
                            <div class="w-20 h-20 bg-white border-2 border-gray-100 rounded-xl p-2 flex items-center justify-center shadow-sm">
                                <img src="{{ $uni['logo_url'] }}" alt="{{ $uni['name_de'] }}" class="max-w-full max-h-full object-contain" loading="lazy" decoding="async">
                            </div>
                        @else
                            <div class="w-20 h-20 bg-gradient-to-br from-primary-500 to-accent-500 rounded-xl flex items-center justify-center shadow-sm">
                                <span class="text-2xl font-extrabold text-white">{{ mb_substr($uni['name_de'], 0, 2) }}</span>
                            </div>
                        @endif
                    </div>

                    {{-- Info --}}
                    <div class="flex-1 min-w-0">
                        <h3 class="text-xl font-bold text-gray-900 leading-tight mb-1 group-hover:text-primary-700 transition">{{ $uni['name_de'] }}</h3>
                        <p class="text-sm text-gray-600 mb-2 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 shrink-0 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/></svg>
                            {{ $uni['city_name'] ?? '-' }}@if (!empty($uni['state_name']))<span class="text-gray-400 mx-1">·</span>{{ $uni['state_name'] }}@endif
                        </p>
                        <div class="flex flex-wrap items-center gap-1.5">
                            @if ($uni['type'])
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-primary-50 text-primary-700 text-[11px] font-semibold ring-1 ring-primary-100">
                                    {{ $typeLabel($uni['type']) }}
                                </span>
                            @endif
                            @if ($uni['founded_year'])
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-gray-50 text-gray-600 text-[11px] font-semibold ring-1 ring-gray-100">
                                    🏛️ {{ $uni['founded_year'] }}
                                </span>
                            @endif
                            @if (!empty($uni['student_count']) && !$showQsBreakdown)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-gray-50 text-gray-600 text-[11px] font-semibold ring-1 ring-gray-100">
                                    👥 {{ number_format($uni['student_count'], 0, ',', '.') }}
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Right: overall donut (QS) or stat --}}
                    @if ($showQsBreakdown && $overall !== null)
                        <div class="shrink-0 hidden md:flex flex-col items-center">
                            <div class="relative w-20 h-20" style="background: conic-gradient({{ $donutColor }} {{ $donutDeg }}deg, #f1f5f9 0deg); border-radius: 50%;">
                                <div class="absolute inset-2 bg-white rounded-full flex flex-col items-center justify-center">
                                    <span class="text-xl font-extrabold tabular-nums" style="color: {{ $donutColor }};">{{ number_format($overall, 1) }}</span>
                                    <span class="text-[9px] text-gray-400 uppercase tracking-wide">{{ __('Overall') }}</span>
                                </div>
                            </div>
                            <p class="text-[10px] text-emerald-700 font-bold mt-1.5">🌐 QS #{{ $uni['qs_world_rank'] }}</p>
                        </div>
                    @else
                        <div class="shrink-0 text-right">
                            @if ($countLabel === 'öğrenci' && $uni['student_count'])
                                <div class="text-3xl font-extrabold text-accent-600 tabular-nums">{{ number_format($uni['student_count']) }}</div>
                                <div class="text-[10px] uppercase tracking-wider text-gray-400 font-bold">{{ __('students') }}</div>
                            @elseif ($countLabel === 'kuruluş' && $uni['founded_year'])
                                <div class="text-3xl font-extrabold text-accent-600 tabular-nums">{{ $uni['founded_year'] }}</div>
                                <div class="text-[10px] uppercase tracking-wider text-gray-400 font-bold">{{ __('founded') }}</div>
                            @elseif ($countLabel === 'qs_rank' && !empty($uni['qs_world_rank']))
                                <div class="text-3xl font-extrabold text-emerald-600 tabular-nums">#{{ $uni['qs_world_rank'] }}</div>
                                <div class="text-[10px] uppercase tracking-wider text-gray-400 font-bold">QS World</div>
                            @elseif ($countLabel === 'the_rank' && !empty($uni['the_world_rank']))
                                <div class="text-3xl font-extrabold text-indigo-600 tabular-nums">#{{ $uni['the_world_rank'] }}</div>
                                <div class="text-[10px] uppercase tracking-wider text-gray-400 font-bold">THE World</div>
                            @elseif ($countLabel === 'arwu_rank' && !empty($uni['arwu_world_rank']))
                                <div class="text-3xl font-extrabold text-red-600 tabular-nums">#{{ $uni['arwu_world_rank'] }}</div>
                                <div class="text-[10px] uppercase tracking-wider text-gray-400 font-bold">ARWU</div>
                            @elseif ($countLabel === 'topluluk_skoru' && !empty($uni['community_mention_score']))
                                <div class="text-3xl font-extrabold text-rose-600 tabular-nums">{{ number_format($uni['community_mention_score']) }}</div>
                                <div class="text-[10px] uppercase tracking-wider text-gray-400 font-bold">{{ __('community') }}</div>
                            @elseif ($countLabel === 'program' && !empty($uni['program_count']))
                                <div class="text-3xl font-extrabold text-primary-600 tabular-nums">{{ $uni['program_count'] }}</div>
                                <div class="text-[10px] uppercase tracking-wider text-gray-400 font-bold">{{ __('programs') }}</div>
                            @elseif ($uni['student_count'])
                                <div class="text-3xl font-extrabold text-accent-600 tabular-nums">{{ number_format($uni['student_count']) }}</div>
                                <div class="text-[10px] uppercase tracking-wider text-gray-400 font-bold">{{ __('students') }}</div>
                            @endif
                        </div>
                    @endif
                </a>

                @if ($showQsBreakdown)
                    @php
                        $hasAnyIndicator = collect($qsIndicators)->keys()->some(fn ($k) => !empty($uni[$k]));
                    @endphp
                    @if ($hasAnyIndicator)
                        <div class="border-t border-gray-100 bg-gradient-to-br from-slate-50 to-white px-6 py-5">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-2.5">
                                @foreach ($qsIndicators as $key => $meta)
                                    @php
                                        $val = !empty($uni[$key]) ? (float) $uni[$key] : null;
                                        [$barColor, $textColor, $bgTint] = $val === null
                                            ? ['bg-gray-200', 'text-gray-400', 'bg-gray-100']
                                            : ($val >= 80
                                                ? ['bg-emerald-500', 'text-emerald-700', 'bg-emerald-50']
                                                : ($val >= 60
                                                    ? ['bg-blue-500', 'text-blue-700', 'bg-blue-50']
                                                    : ($val >= 40
                                                        ? ['bg-amber-500', 'text-amber-700', 'bg-amber-50']
                                                        : ['bg-rose-400', 'text-rose-700', 'bg-rose-50'])));
                                    @endphp
                                    <div class="group/ind py-1" title="{{ __($meta['tooltip']) }}">
                                        <div class="flex items-baseline gap-3 mb-1.5">
                                            <span class="text-[13px] font-semibold text-gray-800 truncate flex-1 leading-tight group-hover/ind:text-gray-900 transition">{{ __($meta['label']) }}</span>
                                            <span class="text-[10px] text-gray-400 font-bold tabular-nums shrink-0">{{ $meta['weight'] }}%</span>
                                            <span class="text-lg font-extrabold {{ $textColor }} tabular-nums shrink-0 w-10 text-right leading-none">{{ $val !== null ? number_format($val, 0) : '–' }}</span>
                                        </div>
                                        <div class="h-2.5 {{ $bgTint }} rounded-full overflow-hidden ring-1 ring-gray-100">
                                            <div class="h-full {{ $barColor }} rounded-full transition-all duration-700 ease-out" style="width: {{ $val !== null ? min(100, $val) : 0 }}%"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endif
                </div>
            @endforeach
        </div>

        {{-- Methodology — tüm ranking tipleri için (QS/THE/ARWU/Community/Largest/Oldest/Field). i18n via __() --}}
        <x-ranking-methodology :methodology="$methodology" />

        @if ($methodology && ! empty($methodology['indicators']))
            <div class="mt-4 text-center">
                <a href="{{ route('rankings.methodology', $config['slug']) }}"
                   class="inline-flex items-center gap-2 text-sm font-semibold text-indigo-600 hover:text-indigo-800 hover:underline">
                    📐 {{ __('Full methodology page') }} →
                </a>
            </div>
        @endif

        <div class="mt-10 bg-primary-50 border border-primary-200 rounded-lg p-6 text-center">
            <h3 class="text-xl font-bold mb-2">{{ __('Didn\'t find what you were looking for?') }}</h3>
            <p class="text-gray-700 mb-4">{{ __('Browse rankings in other categories.') }}</p>
            <a href="{{ route('rankings.index') }}" class="inline-block bg-primary-500 hover:bg-primary-600 text-white px-6 py-2 rounded font-semibold transition">
                {{ __('All Rankings') }} →
            </a>
        </div>
    @endif
</div>
@endsection
