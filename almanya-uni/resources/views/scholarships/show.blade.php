@php
    $title = $scholarship->name_en ?? $scholarship->name_de ?? ('DAAD #' . $scholarship->sap_objid);
    $loc = app()->getLocale();
    // DAAD verisi sadece EN/DE'de var. Locale-aware tercih:
    //   - DE locale → DE öncelik, EN fallback
    //   - TR / EN / diğer → EN öncelik, DE fallback
    $intro = $loc === 'de'
        ? ($scholarship->introductionText('de') ?? $scholarship->introductionText('en'))
        : ($scholarship->introductionText('en') ?? $scholarship->introductionText('de'));
    $qEn = $scholarship->qText('en');
    $qDe = $scholarship->qText('de');
    // Eligibility: tercih edilen dil + ikincisi (varsa) toggle ile
    $qPrimary       = $loc === 'de' ? ($qDe ?: $qEn)            : ($qEn ?: $qDe);
    $qPrimaryLabel  = $loc === 'de' ? ($qDe ? 'Deutsch' : 'English') : ($qEn ? 'English' : 'Deutsch');
    $qSecondary     = $loc === 'de' ? ($qDe ? $qEn : null)      : ($qEn ? $qDe : null);
    $qSecondaryLabel= $loc === 'de' ? 'English'                 : 'Deutsch';
@endphp

@extends('layouts.app')

@section('title', $title . ' — ' . __('DAAD Scholarship') . '  — ' . brand('name'))

@push('head')
    @if ($scholarship->detail_url)
        <link rel="canonical" href="{{ $scholarship->detail_url }}">
    @endif
@endpush

<x-seo
    :title="$title . ' — ' . __('DAAD Scholarship Program')"
    :description="\Illuminate\Support\Str::limit(strip_tags((string) $intro), 160) ?: __('DAAD scholarship database entry.')"
    :image="$scholarship->slug ? route('og.image', ['type' => 'scholarship', 'slug' => $scholarship->slug . '.png']) : null"
/>

@section('content')

<section class="bg-gradient-to-br from-primary-700 via-primary-600 to-accent-500 text-white">
    <div class="max-w-[1400px] mx-auto px-4 py-8 md:py-12">
        <nav class="text-sm text-primary-100 mb-3">
            <a href="/" class="hover:text-white">{{ __('Home') }}</a>
            <span class="mx-2 opacity-50">›</span>
            <a href="{{ route('scholarships.index') }}" class="hover:text-white">{{ __('Scholarships') }}</a>
            <span class="mx-2 opacity-50">›</span>
            <span class="text-white">{{ \Illuminate\Support\Str::limit($title, 60) }}</span>
        </nav>

        <div class="flex flex-wrap items-start gap-3 mb-3">
            <h1 class="text-2xl md:text-4xl font-extrabold leading-tight flex-1">{{ $title }}</h1>
            @if ($scholarship->is_daad)
                <span class="px-3 py-1 rounded-full bg-white/15 text-sm font-semibold ring-1 ring-white/20">DAAD</span>
            @else
                <span class="px-3 py-1 rounded-full bg-white/15 text-sm font-semibold ring-1 ring-white/20">{{ __('Partner institution') }}</span>
            @endif
        </div>

        @if ($scholarship->programmname_en ?? $scholarship->programmname_de)
            <p class="text-primary-100">{{ $scholarship->programmname_en ?? $scholarship->programmname_de }}</p>
        @endif

        @if ($scholarship->removed_at)
            <div class="mt-4 inline-block px-3 py-2 rounded-lg bg-rose-500/30 ring-1 ring-rose-200 text-sm">
                ⚠️ {{ __('This scholarship was not seen in the last DAAD database sync (:date). It may no longer be active.', ['date' => $scholarship->removed_at->format('d M Y')]) }}
            </div>
        @endif
    </div>
</section>

<section class="bg-gray-50 py-10">
    <div class="max-w-[1400px] mx-auto px-4 grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ANA İÇERİK --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- AÇIKLAMA --}}
            @if ($intro)
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-3">📝 {{ __('About the Scholarship') }}</h2>
                    <div class="prose prose-sm max-w-none text-gray-700">
                        {!! $intro !!}
                    </div>
                </div>
            @endif

            {{-- BAŞVURU KOŞULLARI — locale-aware tek dil + ikincisi toggle --}}
            @if ($qPrimary)
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-3">✅ {{ __('Eligibility') }}</h2>
                    <div class="prose prose-sm max-w-none text-gray-700">{!! $qPrimary !!}</div>
                    @if ($qSecondary)
                        <details class="mt-4 text-sm">
                            <summary class="cursor-pointer text-gray-600 hover:text-gray-900">
                                🌐 {{ __('Show :lang version', ['lang' => $qSecondaryLabel]) }}
                            </summary>
                            <div class="prose prose-sm max-w-none text-gray-700 mt-3 pt-3 border-t border-gray-100">{!! $qSecondary !!}</div>
                        </details>
                    @endif
                </div>
            @endif

            {{-- DEADLINE --}}
            @if ($scholarship->deadline)
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-3">📅 {{ __('Application Dates') }}</h2>
                    @if ($scholarship->deadline->general_en ?? $scholarship->deadline->general_de)
                        <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-4 text-sm text-gray-800">
                            {!! $scholarship->deadline->general_en ?? $scholarship->deadline->general_de !!}
                        </div>
                    @endif
                    @if (!empty($scholarship->deadline->countries_json) && is_array($scholarship->deadline->countries_json))
                        <details>
                            <summary class="cursor-pointer text-sm font-semibold text-gray-700">
                                {{ __('Country-specific deadlines (:count)', ['count' => count($scholarship->deadline->countries_json)]) }}
                            </summary>
                            <div class="mt-3 max-h-80 overflow-y-auto border-t border-gray-100 pt-3 text-sm text-gray-700 space-y-2">
                                @foreach ($scholarship->deadline->countries_json as $cd)
                                    <div class="pb-2 border-b border-gray-50">
                                        <strong>{{ $cd['nameEn'] ?? $cd['nameDe'] ?? '?' }}</strong>:
                                        {{ $cd['deadlineEn'] ?? $cd['deadlineDe'] ?? '—' }}
                                    </div>
                                @endforeach
                            </div>
                        </details>
                    @endif
                </div>
            @endif

            {{-- RESMİ DAAD LİNKİ --}}
            @if ($scholarship->detail_url)
                <a href="{{ $scholarship->detail_url }}" target="_blank" rel="noopener noreferrer"
                   class="block bg-blue-600 hover:bg-blue-700 text-white rounded-xl p-5 text-center font-semibold transition">
                    🔗 {{ __('See full details on the official DAAD scholarship page') }} →
                </a>
            @endif

            {{-- İLGİLİ BURSLAR --}}
            @if ($related->isNotEmpty())
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-3">🔍 {{ __('Related Scholarships') }}</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        @foreach ($related as $r)
                            <a href="{{ route('scholarships.show', $r->slug) }}"
                               class="block p-4 rounded-lg border border-gray-200 hover:border-primary-500 hover:bg-primary-50 transition">
                                <div class="font-semibold text-gray-900 line-clamp-1">{{ $loc === 'de' ? ($r->name_de ?? $r->name_en) : ($r->name_en ?? $r->name_de) }}</div>
                                @php $programmname = $loc === 'de' ? ($r->programmname_de ?? $r->programmname_en) : ($r->programmname_en ?? $r->programmname_de); @endphp
                                @if ($programmname)
                                    <div class="text-xs text-gray-500 line-clamp-1 mt-1">{{ $programmname }}</div>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        {{-- SIDEBAR --}}
        <aside class="space-y-4">

            @if ($scholarship->statuses->isNotEmpty())
                <div class="bg-white rounded-xl border border-gray-200 p-5">
                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">{{ __('Target group') }}</h3>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach ($scholarship->statuses as $s)
                            <span class="px-2 py-1 rounded-full bg-amber-50 text-amber-700 text-sm">{{ $s->name_en ?? $s->name_de }}</span>
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($scholarship->subjects->isNotEmpty())
                <div class="bg-white rounded-xl border border-gray-200 p-5">
                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">{{ __('Subject group') }}</h3>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach ($scholarship->subjects as $sub)
                            <span class="px-2 py-1 rounded-full bg-emerald-50 text-emerald-700 text-sm">{{ $sub->name_en ?? $sub->name_de }}</span>
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($scholarship->intentions->isNotEmpty())
                <div class="bg-white rounded-xl border border-gray-200 p-5">
                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">{{ __('Intention') }}</h3>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach ($scholarship->intentions as $i)
                            <span class="px-2 py-1 rounded-full bg-purple-50 text-purple-700 text-sm">{{ $i->name_en ?? $i->name_de }}</span>
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($scholarship->origins->isNotEmpty())
                <div class="bg-white rounded-xl border border-gray-200 p-5">
                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">
                        {{ __('Eligible countries (:count)', ['count' => $scholarship->origins->count()]) }}
                    </h3>
                    <div class="max-h-64 overflow-y-auto text-sm text-gray-700 space-y-1">
                        @foreach ($scholarship->origins->sortBy('name_en') as $o)
                            <div>{{ $o->name_en ?? $o->name_de }}</div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="bg-white rounded-xl border border-gray-200 p-5 text-xs text-gray-500">
                <strong class="text-gray-700 block mb-1">{{ __('Data source') }}</strong>
                {{ __('DAAD scholarship database (official).') }}
                @if ($scholarship->last_seen_at)
                    {{ __('Last sync:') }} {{ $scholarship->last_seen_at->format('d M Y') }}
                @endif
            </div>
        </aside>

    </div>
</section>

@endsection
