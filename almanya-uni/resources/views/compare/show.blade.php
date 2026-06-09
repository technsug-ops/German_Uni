@extends('layouts.app')

@section('title', __('Compare :n universities — ' . brand('name'), ['n' => count($universities)]))
@section('meta_description', __('Compare :n German universities side by side: programs, student count, cost of living, application.', ['n' => count($universities)]))

@php
    $typeLabel = fn ($t) => match ($t) {
        'public' => __('Public'),
        'private' => __('Private'),
        'applied_sciences' => __('Applied Sciences'),
        'art' => __('Art'),
        'religion' => __('Religious'),
        default => $t ? ucfirst($t) : '—',
    };

    // Highlight helpers — sayısal değerlerde en yüksek/en düşük yeşil işaretlensin
    $maxStudent = collect($universities)->max('student_count') ?: 0;
    $oldestYear = collect($universities)->where('founded_year', '>', 0)->min('founded_year') ?: 0;
    $maxPrograms = collect($universities)->pluck('programs.total')->max() ?: 0;
    $maxEnglish = collect($universities)->pluck('programs.english')->max() ?: 0;

    $cols = count($universities);
    $headerColClass = match ($cols) {
        2 => 'w-1/2',
        3 => 'w-1/3',
        4 => 'w-1/4',
        default => 'w-1/2',
    };

    $boolBadge = fn ($v) => $v
        ? '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 text-xs font-semibold"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="w-3.5 h-3.5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg> ' . e(__('Yes')) . '</span>'
        : '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-gray-100 text-gray-500 text-xs">— ' . e(__('No')) . '</span>';
@endphp

@section('content')

{{-- HERO --}}
<section class="bg-gradient-to-br from-primary-700 via-primary-600 to-accent-500 text-white">
    <div class="max-w-[1400px] mx-auto px-4 py-8 md:py-10">
        <nav class="text-sm text-primary-100 mb-3">
            <a href="/" class="hover:text-white">{{ __('Home') }}</a>
            <span class="mx-2 opacity-50">›</span>
            <a href="{{ route('compare.index') }}" class="hover:text-white">{{ __('Compare') }}</a>
            <span class="mx-2 opacity-50">›</span>
            <span class="text-white">{{ __('Result') }}</span>
        </nav>
        <div class="flex items-end justify-between gap-3 flex-wrap">
            <div>
                <h1 class="text-2xl md:text-4xl font-extrabold leading-tight">{{ __('Compare :n universities', ['n' => count($universities)]) }}</h1>
                <p class="text-primary-100 mt-1 text-sm">{{ __('Programs, student count, cost of living and application details side by side.') }}</p>
            </div>
            <a href="{{ route('compare.index', ['slugs' => $slug_csv]) }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-white text-primary-700 font-semibold hover:bg-gray-100 transition">
                ← {{ __('Edit selection') }}
            </a>
        </div>
    </div>
</section>

<section class="bg-gray-50 py-8">
    <div class="max-w-[1400px] mx-auto px-4">

        @if ($too_few)
            <div class="bg-yellow-50 border border-yellow-300 rounded-lg p-8 text-center">
                <div class="flex justify-center mb-3 text-yellow-700"><x-svg-icon name="scale" class="w-12 h-12" /></div>
                <p class="font-semibold text-yellow-900 mb-2">{{ __('At least 2 universities required') }}</p>
                <p class="text-sm text-yellow-800 mb-4">{{ __('You need to pick 2 to 4 universities to compare.') }}</p>
                <a href="{{ route('compare.index', ['slugs' => $slug_csv]) }}"
                   class="inline-block bg-primary-600 hover:bg-primary-700 text-white px-6 py-2 rounded-lg font-semibold transition">
                    {{ __('Pick universities') }}
                </a>
            </div>
        @else
            {{-- ÜST: GÖRSEL KARTLAR --}}
            <div class="grid grid-cols-2 lg:grid-cols-{{ $cols }} gap-4 mb-6">
                @foreach ($universities as $uni)
                    <a href="{{ route('universities.show', $uni['slug']) }}"
                       class="group bg-white rounded-xl overflow-hidden border-2 border-gray-200 hover:border-primary-500 hover:shadow-lg transition-all">
                        <div class="aspect-[16/9] overflow-hidden bg-gray-100 relative">
                            @if ($uni['image_url'])
                                <img src="{{ $uni['image_url'] }}" alt="{{ $uni['name_de'] }}" loading="lazy"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-primary-500 to-accent-500 flex items-center justify-center">
                                    <span class="text-4xl font-extrabold text-white/90">{{ mb_substr($uni['name_de'], 0, 2) }}</span>
                                </div>
                            @endif
                            @if ($uni['logo_url'] && $uni['image_url'])
                                <div class="absolute bottom-2 left-2 w-12 h-12 bg-white rounded-lg shadow ring-1 ring-white/60 p-1 flex items-center justify-center">
                                    <img src="{{ $uni['logo_url'] }}" alt="" class="max-w-full max-h-full object-contain" loading="lazy" decoding="async"/>
                                </div>
                            @endif
                            <span class="absolute top-2 right-2 inline-block px-2 py-0.5 rounded bg-black/60 backdrop-blur text-white text-xs font-semibold">
                                {{ $typeLabel($uni['type']) }}
                            </span>
                        </div>
                        <div class="p-3">
                            <h3 class="font-bold text-gray-900 group-hover:text-primary-600 leading-snug line-clamp-2 text-sm">{{ $uni['name_de'] }}</h3>
                            @if ($uni['city_name'])
                                <p class="text-xs text-gray-500 mt-1 inline-flex items-center gap-1"><x-svg-icon name="map-pin" class="w-3.5 h-3.5" /> {{ $uni['city_name'] }}@if ($uni['state_name']) · {{ $uni['state_name'] }}@endif</p>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>

            {{-- KARŞILAŞTIRMA TABLOSU --}}
            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <colgroup>
                            <col class="w-48">
                            @foreach ($universities as $u)
                                <col class="{{ $headerColClass }}">
                            @endforeach
                        </colgroup>

                        <tbody class="divide-y divide-gray-100">
                            {{-- AKADEMİK GROUP --}}
                            <tr class="bg-primary-50">
                                <td colspan="{{ $cols + 1 }}" class="px-4 py-2 text-xs font-bold text-primary-700 uppercase tracking-wider"><span class="inline-flex items-center gap-1.5"><x-svg-icon name="book-open" class="w-3.5 h-3.5" /> {{ __('Academic') }}</span></td>
                            </tr>
                            <tr>
                                <td class="bg-gray-50 px-4 py-3 font-semibold text-gray-700">{{ __('Type') }}</td>
                                @foreach ($universities as $u)
                                    <td class="px-4 py-3">{{ $typeLabel($u['type']) }}</td>
                                @endforeach
                            </tr>
                            <tr>
                                <td class="bg-gray-50 px-4 py-3 font-semibold text-gray-700">{{ __('Founded') }}</td>
                                @foreach ($universities as $u)
                                    @php $isOldest = $u['founded_year'] && $u['founded_year'] == $oldestYear && $cols > 1; @endphp
                                    <td class="px-4 py-3 {{ $isOldest ? 'bg-emerald-50' : '' }}">
                                        @if ($u['founded_year'])
                                            {{ $u['founded_year'] }}
                                            @if ($isOldest)
                                                <span class="inline-flex items-center text-emerald-600 ml-1" title="{{ __('Oldest') }}"><x-svg-icon name="trophy" class="w-3.5 h-3.5" /></span>
                                            @endif
                                        @else
                                            <span class="text-gray-400">—</span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                            <tr>
                                <td class="bg-gray-50 px-4 py-3 font-semibold text-gray-700">{{ __('Students') }}</td>
                                @foreach ($universities as $u)
                                    @php $isMax = $u['student_count'] && $u['student_count'] == $maxStudent && $cols > 1; @endphp
                                    <td class="px-4 py-3 {{ $isMax ? 'bg-emerald-50 font-semibold' : '' }}">
                                        @if ($u['student_count'])
                                            {{ number_format($u['student_count']) }}
                                            @if ($isMax)
                                                <span class="inline-flex items-center text-emerald-600 ml-1" title="{{ __('Largest') }}"><x-svg-icon name="trophy" class="w-3.5 h-3.5" /></span>
                                            @endif
                                        @else
                                            <span class="text-gray-400">—</span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>

                            {{-- PROGRAMLAR GROUP --}}
                            <tr class="bg-primary-50">
                                <td colspan="{{ $cols + 1 }}" class="px-4 py-2 text-xs font-bold text-primary-700 uppercase tracking-wider"><span class="inline-flex items-center gap-1.5"><x-svg-icon name="academic-cap" class="w-3.5 h-3.5" /> {{ __('Programs') }}</span></td>
                            </tr>
                            <tr>
                                <td class="bg-gray-50 px-4 py-3 font-semibold text-gray-700">{{ __('Total programs') }}</td>
                                @foreach ($universities as $u)
                                    @php $t = $u['programs']['total'] ?? 0; $isMax = $t > 0 && $t == $maxPrograms && $cols > 1; @endphp
                                    <td class="px-4 py-3 {{ $isMax ? 'bg-emerald-50 font-semibold' : '' }}">
                                        @if ($t > 0)
                                            <a href="{{ route('programs.index', ['uni' => $u['slug']]) }}" class="text-primary-600 hover:underline">{{ $t }}</a>
                                            @if ($isMax) <span class="inline-flex items-center text-emerald-600 ml-1" title="{{ __('Most') }}"><x-svg-icon name="trophy" class="w-3.5 h-3.5" /></span> @endif
                                        @else <span class="text-gray-400">—</span> @endif
                                    </td>
                                @endforeach
                            </tr>
                            <tr>
                                <td class="bg-gray-50 px-4 py-3 font-semibold text-gray-700">Bachelor</td>
                                @foreach ($universities as $u)
                                    <td class="px-4 py-3">{{ $u['programs']['bachelor'] ?? 0 }}</td>
                                @endforeach
                            </tr>
                            <tr>
                                <td class="bg-gray-50 px-4 py-3 font-semibold text-gray-700">Master</td>
                                @foreach ($universities as $u)
                                    <td class="px-4 py-3">{{ $u['programs']['master'] ?? 0 }}</td>
                                @endforeach
                            </tr>
                            <tr>
                                <td class="bg-gray-50 px-4 py-3 font-semibold text-gray-700">PhD</td>
                                @foreach ($universities as $u)
                                    <td class="px-4 py-3">{{ $u['programs']['phd'] ?? 0 }}</td>
                                @endforeach
                            </tr>
                            <tr>
                                <td class="bg-gray-50 px-4 py-3 font-semibold text-gray-700">{{ __('English-taught programs') }}</td>
                                @foreach ($universities as $u)
                                    @php $e = $u['programs']['english'] ?? 0; $pct = $u['programs']['english_pct'] ?? 0; $isMax = $e > 0 && $e == $maxEnglish && $cols > 1; @endphp
                                    <td class="px-4 py-3 {{ $isMax ? 'bg-emerald-50 font-semibold' : '' }}">
                                        @if ($e > 0)
                                            <div class="flex items-baseline gap-2">
                                                <span>{{ $e }}</span>
                                                <span class="text-xs text-gray-500">({{ $pct }}%)</span>
                                                @if ($isMax) <span class="inline-flex items-center text-emerald-600" title="{{ __('Most English-taught') }}"><x-svg-icon name="trophy" class="w-3.5 h-3.5" /></span> @endif
                                            </div>
                                            <div class="mt-1 w-full bg-gray-200 rounded-full h-1.5 overflow-hidden">
                                                <div class="bg-blue-500 h-full" style="width: {{ $pct }}%"></div>
                                            </div>
                                        @else <span class="text-gray-400">—</span> @endif
                                    </td>
                                @endforeach
                            </tr>

                            {{-- GÜÇLÜ ALANLAR --}}
                            <tr>
                                <td class="bg-gray-50 px-4 py-3 font-semibold text-gray-700 align-top">{{ __('Strong fields') }}</td>
                                @foreach ($universities as $u)
                                    <td class="px-4 py-3">
                                        @if (! empty($u['top_fields']))
                                            <div class="flex flex-col gap-1">
                                                @foreach ($u['top_fields'] as $f)
                                                    <a href="{{ route('fields.show', $f['slug']) }}"
                                                       class="inline-flex items-center gap-1.5 text-xs text-gray-700 hover:text-primary-600 transition">
                                                        <span class="text-gray-500">{!! e_icon($f['icon'] ?? null, 'w-3.5 h-3.5 shrink-0') !!}</span>
                                                        <span class="font-medium">{{ $f['name'] }}</span>
                                                        <span class="text-gray-400">({{ $f['count'] }})</span>
                                                    </a>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-gray-400 text-xs italic">{{ __('no data') }}</span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>

                            {{-- LOKASYON & ŞEHİR --}}
                            <tr class="bg-primary-50">
                                <td colspan="{{ $cols + 1 }}" class="px-4 py-2 text-xs font-bold text-primary-700 uppercase tracking-wider"><span class="inline-flex items-center gap-1.5"><x-svg-icon name="map" class="w-3.5 h-3.5" /> {{ __('Location') }}</span></td>
                            </tr>
                            <tr>
                                <td class="bg-gray-50 px-4 py-3 font-semibold text-gray-700">{{ __('City') }}</td>
                                @foreach ($universities as $u)
                                    <td class="px-4 py-3">
                                        @if ($u['city_slug'] ?? null)
                                            <a href="{{ route('cities.show', $u['city_slug']) }}" class="text-primary-600 hover:underline">{{ $u['city_name'] }}</a>
                                        @else {{ $u['city_name'] ?? '—' }} @endif
                                    </td>
                                @endforeach
                            </tr>
                            <tr>
                                <td class="bg-gray-50 px-4 py-3 font-semibold text-gray-700">{{ __('State') }}</td>
                                @foreach ($universities as $u)
                                    <td class="px-4 py-3">{{ $u['state_name'] ?? '—' }}</td>
                                @endforeach
                            </tr>
                            <tr>
                                <td class="bg-gray-50 px-4 py-3 font-semibold text-gray-700">{{ __('City population') }}</td>
                                @foreach ($universities as $u)
                                    @php
                                        $sizeBadge = match ($u['city_size'] ?? null) {
                                            'metropol' => ['building-office', __('Metropolis'), 'bg-rose-50 text-rose-700'],
                                            'orta'     => ['building-office', __('Medium'),     'bg-blue-50 text-blue-700'],
                                            'küçük'    => ['home',            __('Small'),      'bg-amber-50 text-amber-700'],
                                            default    => null,
                                        };
                                    @endphp
                                    <td class="px-4 py-3">
                                        @if (! empty($u['city_population']))
                                            <div class="flex items-baseline gap-2">
                                                <strong class="text-gray-900">{{ number_format($u['city_population']) }}</strong>
                                                @if ($sizeBadge)
                                                    <span class="inline-flex items-center gap-1 text-xs px-1.5 py-0.5 rounded {{ $sizeBadge[2] }}"><x-svg-icon name="{{ $sizeBadge[0] }}" class="w-3 h-3" /> {{ $sizeBadge[1] }}</span>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-gray-400 text-xs italic">{{ __('no data') }}</span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                            <tr>
                                <td class="bg-gray-50 px-4 py-3 font-semibold text-gray-700">{{ __('Monthly cost of living') }}</td>
                                @foreach ($universities as $u)
                                    <td class="px-4 py-3">
                                        @if (!empty($u['city_cost']['total']))
                                            <strong class="text-gray-900">{{ $u['city_cost']['total'] }} {{ $u['city_cost']['currency'] ?? '€' }}</strong>
                                            <span class="text-xs text-gray-500 block">({{ $u['city_name'] }})</span>
                                        @else
                                            <span class="text-gray-400 text-xs italic">{{ __('no data') }}</span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>

                            {{-- BAŞVURU --}}
                            <tr class="bg-primary-50">
                                <td colspan="{{ $cols + 1 }}" class="px-4 py-2 text-xs font-bold text-primary-700 uppercase tracking-wider"><span class="inline-flex items-center gap-1.5"><x-svg-icon name="document-text" class="w-3.5 h-3.5" /> {{ __('Application') }}</span></td>
                            </tr>
                            <tr>
                                <td class="bg-gray-50 px-4 py-3 font-semibold text-gray-700">{{ __('Uni-Assist member') }}</td>
                                @foreach ($universities as $u)
                                    <td class="px-4 py-3">{!! $boolBadge($u['is_uni_assist_member'] ?? false) !!}</td>
                                @endforeach
                            </tr>
                            <tr>
                                <td class="bg-gray-50 px-4 py-3 font-semibold text-gray-700">{{ __('HRK member') }}</td>
                                @foreach ($universities as $u)
                                    <td class="px-4 py-3">{!! $boolBadge($u['hrk_member'] ?? false) !!}</td>
                                @endforeach
                            </tr>
                            <tr>
                                <td class="bg-gray-50 px-4 py-3 font-semibold text-gray-700">{{ __('Official website') }}</td>
                                @foreach ($universities as $u)
                                    <td class="px-4 py-3">
                                        @if ($u['website_url'])
                                            <a href="{{ $u['website_url'] }}" target="_blank" rel="noopener" class="text-primary-600 hover:underline text-xs break-all">
                                                {{ parse_url($u['website_url'], PHP_URL_HOST) }} ↗
                                            </a>
                                        @else <span class="text-gray-400">—</span> @endif
                                    </td>
                                @endforeach
                            </tr>
                            <tr>
                                <td class="bg-gray-50 px-4 py-3 font-semibold text-gray-700">Wikipedia</td>
                                @foreach ($universities as $u)
                                    <td class="px-4 py-3">
                                        <div class="flex gap-2 flex-wrap text-xs">
                                            @if ($u['wikipedia_url_de'])
                                                <a href="{{ $u['wikipedia_url_de'] }}" target="_blank" rel="noopener" class="text-primary-600 hover:underline">DE</a>
                                            @endif
                                            @if ($u['wikipedia_url_en'])
                                                <a href="{{ $u['wikipedia_url_en'] }}" target="_blank" rel="noopener" class="text-primary-600 hover:underline">EN</a>
                                            @endif
                                            @if (!$u['wikipedia_url_de'] && !$u['wikipedia_url_en'])
                                                <span class="text-gray-400">—</span>
                                            @endif
                                        </div>
                                    </td>
                                @endforeach
                            </tr>

                            {{-- AlmanyaUni İÇERİĞİ --}}
                            <tr class="bg-primary-50">
                                <td colspan="{{ $cols + 1 }}" class="px-4 py-2 text-xs font-bold text-primary-700 uppercase tracking-wider"><span class="inline-flex items-center gap-1.5"><x-svg-icon name="book-open" class="w-3.5 h-3.5" /> {{ __('AlmanyaUni Guide') }}</span></td>
                            </tr>
                            <tr>
                                <td class="bg-gray-50 px-4 py-3 font-semibold text-gray-700">{{ __('Detailed guide') }}</td>
                                @foreach ($universities as $u)
                                    <td class="px-4 py-3">
                                        @if ($u['has_content'])
                                            <a href="{{ route('universities.show', $u['slug']) }}" class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 text-xs font-semibold hover:bg-emerald-100">
                                                <span class="inline-flex items-center gap-1"><x-svg-icon name="sparkles" class="w-3.5 h-3.5" /> {{ __('Available') }}</span> →
                                            </a>
                                        @else
                                            <span class="inline-block px-2 py-0.5 rounded bg-gray-100 text-gray-500 text-xs">— {{ __('Coming soon') }}</span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                            <tr>
                                <td class="bg-gray-50 px-4 py-3 font-semibold text-gray-700 align-top">{{ __('Introduction') }}</td>
                                @foreach ($universities as $u)
                                    <td class="px-4 py-3 text-xs text-gray-700 leading-relaxed">
                                        @if ($u['intro_snippet'])
                                            {{ $u['intro_snippet'] }}
                                        @elseif ($u['description_de'] || $u['description_en'])
                                            {{ \Illuminate\Support\Str::limit($u['description_de'] ?? $u['description_en'], 200) }}
                                        @else
                                            <span class="text-gray-400 italic">{{ __('No introduction') }}</span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- CTA --}}
            <div class="mt-6 flex flex-wrap gap-3 justify-center">
                <a href="{{ route('compare.index') }}" class="px-5 py-2.5 rounded-lg bg-white border border-gray-300 hover:border-primary-400 text-gray-700 font-semibold transition">
                    {{ __('New comparison') }}
                </a>
                <a href="{{ route('compare.index', ['slugs' => $slug_csv]) }}" class="px-5 py-2.5 rounded-lg bg-primary-600 hover:bg-primary-700 text-white font-semibold transition">
                    {{ __('Edit this selection') }}
                </a>
            </div>
        @endif
    </div>
</section>

@endsection
