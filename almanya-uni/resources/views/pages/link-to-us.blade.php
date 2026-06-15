@extends('layouts.app')

@section('title', __('Link to us / Press') . ' — ' . brand('name'))

<x-seo
    :title="__('Link to us / Press Kit') . ' — ' . brand('name')"
    :description="__('Ready-to-use links, badges and citable statistics about studying in Germany. Cite :brand with a link.', ['brand' => brand('name')])"
/>

@php
    $domain = 'https://' . brand('domain');
    $name   = brand('name');
    $pct    = $totals['programs'] ? (int) round($totals['programs_en'] / $totals['programs'] * 100) : 0;
    $linkHtml = '<a href="' . $domain . '">' . $name . ' — ' . __('Germany study & university guide') . '</a>';
    $linkMd   = '[' . $name . ' — ' . __('Germany study & university guide') . '](' . $domain . ')';

    // Rozet varyantları — koyu / açık / sayılı. Hepsi tek <a>, inline style (her sitede çalışır).
    $badgeBase = 'display:inline-flex;align-items:center;gap:6px;padding:6px 12px;border-radius:8px;font:600 13px/1 sans-serif;text-decoration:none;';
    $badges = [
        'dark'  => ['label' => __('Dark'),  'html' => '<a href="' . $domain . '" style="' . $badgeBase . 'background:#1A1A1A;color:#fff;">🇩🇪 ' . __('Data by') . ' ' . $name . '</a>'],
        'light' => ['label' => __('Light'), 'html' => '<a href="' . $domain . '" style="' . $badgeBase . 'background:#fff;color:#1A1A1A;border:1px solid #e5e7eb;">🇩🇪 ' . __('Data by') . ' ' . $name . '</a>'],
        'count' => ['label' => __('With numbers'), 'html' => '<a href="' . $domain . '" style="' . $badgeBase . 'background:#1a368d;color:#fff;">🇩🇪 ' . number_format($totals['programs']) . ' ' . __('German study programs') . ' · ' . $name . '</a>'],
    ];

    // Konu-bazlı hazır linkler — spesifik araç/sayfalara derin linkler.
    $deepLinks = [
        ['label' => __('Cost of living calculator'), 'url' => route('tools.cost-of-living'), 'text' => __('Germany cost of living calculator')],
        ['label' => __('Study statistics'),          'url' => route('stats'),                'text' => __('Germany study statistics')],
        ['label' => __('Scholarships'),              'url' => route('scholarships.index'),   'text' => __('Scholarships for studying in Germany')],
        ['label' => __('Student cities'),            'url' => route('cities.index'),         'text' => __('Best student cities in Germany')],
        ['label' => __('Why study in Germany'),      'url' => route('study.germany'),        'text' => __('Why study in Germany — guide')],
    ];

    // Basın kiti — logo dosyaları + marka renkleri + boilerplate.
    $pressAssets = [
        ['label' => __('Logo (SVG)'),       'file' => '/img/logos/applytogerman.svg'],
        ['label' => __('Logo, white (SVG)'),'file' => '/img/logos/applytogerman-white.svg'],
        ['label' => __('Icon (SVG)'),       'file' => '/img/logos/applytogerman-icon.svg'],
        ['label' => __('Full logo kit (ZIP)'),'file' => '/img/logos/ApplyToGerman-Logo-Final.zip'],
    ];
    $brandColors = [['#1A1A1A', __('Brand black')], ['#1a368d', __('Primary blue')]];
    $boilerplate = __(':brand is a free, independent guide for international students applying to German universities: :programs study programs across :unis universities, plus cost and eligibility tools, scholarships, visa and city guides.', [
        'brand' => $name, 'programs' => number_format($totals['programs']), 'unis' => number_format($totals['universities']),
    ]) . ' ' . $domain;

    $citation = __(':programs study programs (:pct% English-taught) at :unis German universities — :brand.', [
        'programs' => number_format($totals['programs']),
        'pct'      => $pct,
        'unis'     => number_format($totals['universities']),
        'brand'    => $name,
    ]) . ' ' . __('Source:') . ' ' . $domain . '/germany-study-statistics';

    $embedSrc  = $domain . '/embed/cost-of-living?lang=' . app()->getLocale();
    $embedHtml = '<iframe src="' . $embedSrc . '" width="100%" height="560" loading="lazy" style="border:0;max-width:480px" title="' . e(__('Germany Cost of Living Calculator') . ' — ' . $name) . '"></iframe>';
    $statsSrc  = $domain . '/embed/stats?lang=' . app()->getLocale();
    $statsHtml = '<iframe src="' . $statsSrc . '" width="100%" height="200" loading="lazy" style="border:0;max-width:560px" title="' . e(__('Germany Study Statistics') . ' — ' . $name) . '"></iframe>';
@endphp

@section('content')
<section class="bg-gradient-to-br from-primary-800 via-primary-700 to-primary-900 text-white">
    <div class="max-w-[900px] mx-auto px-4 py-12 md:py-16">
        <nav class="text-sm text-primary-200 mb-3">
            <a href="/" class="hover:text-white">{{ __('Home') }}</a>
            <span class="mx-2 opacity-60">›</span>
            <span class="text-white">{{ __('Link to us') }}</span>
        </nav>
        <h1 class="text-3xl md:text-5xl font-extrabold leading-tight mb-3">{{ __('Link to us / Press') }}</h1>
        <p class="text-lg text-primary-50 max-w-2xl">{{ __('Writing about studying in Germany? Use our links, badges and data — free. Just credit :brand with a link.', ['brand' => $name]) }}</p>
    </div>
</section>

<div class="max-w-[900px] mx-auto px-4 py-12 space-y-10" x-data="{ copied: null, copy(id, text) { navigator.clipboard.writeText(text).then(() => { this.copied = id; setTimeout(() => this.copied = null, 1800); }); } }">

    {{-- Kim olduğumuz --}}
    <section class="flex items-center gap-4">
        <img src="{{ asset(brand('logo')) }}" alt="{{ $name }}" class="h-10 hidden sm:block">
        <p class="text-gray-700">{{ __(':brand is a free guide to German universities and study programs for international students — :programs programs across :unis universities, plus decision tools, scholarships and guides.', ['brand' => $name, 'programs' => number_format($totals['programs']), 'unis' => number_format($totals['universities'])]) }}</p>
    </section>

    {{-- Hazır linkler --}}
    <section>
        <h2 class="text-xl md:text-2xl font-bold text-gray-900 mb-4">{{ __('Ready-to-use links') }}</h2>
        <div class="space-y-4">
            @foreach ([
                ['id' => 'html', 'label' => 'HTML', 'code' => $linkHtml],
                ['id' => 'md', 'label' => 'Markdown', 'code' => $linkMd],
            ] as $snip)
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ $snip['label'] }}</span>
                        <button type="button" @click="copy('{{ $snip['id'] }}', $refs.{{ $snip['id'] }}.textContent)"
                                class="text-xs font-semibold px-2.5 py-1 rounded transition"
                                :class="copied === '{{ $snip['id'] }}' ? 'bg-emerald-100 text-emerald-700' : 'bg-primary-600 text-white hover:bg-primary-700'"
                                x-text="copied === '{{ $snip['id'] }}' ? '{{ __('Copied!') }}' : '{{ __('Copy') }}'"></button>
                    </div>
                    <pre x-ref="{{ $snip['id'] }}" class="bg-gray-50 border border-gray-200 rounded-lg p-3 text-xs text-gray-800 overflow-x-auto whitespace-pre-wrap">{{ $snip['code'] }}</pre>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Rozet varyantları --}}
    <section>
        <h2 class="text-xl md:text-2xl font-bold text-gray-900 mb-1">{{ __('Badges') }}</h2>
        <p class="text-sm text-gray-600 mb-4">{{ __('Pick a style and paste the HTML where you cite our data — it renders as a small linked badge.') }}</p>
        <div class="space-y-5">
            @foreach ($badges as $key => $b)
                <div>
                    <div class="flex items-center gap-3 flex-wrap mb-2 {{ $key === 'light' ? 'bg-gray-100 rounded-lg p-3' : '' }}">
                        <span class="text-xs text-gray-500 w-24">{{ $b['label'] }}</span>
                        {!! $b['html'] !!}
                    </div>
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">HTML</span>
                        <button type="button" @click="copy('badge_{{ $key }}', $refs.badge_{{ $key }}.textContent)"
                                class="text-xs font-semibold px-2.5 py-1 rounded transition"
                                :class="copied === 'badge_{{ $key }}' ? 'bg-emerald-100 text-emerald-700' : 'bg-primary-600 text-white hover:bg-primary-700'"
                                x-text="copied === 'badge_{{ $key }}' ? '{{ __('Copied!') }}' : '{{ __('Copy') }}'"></button>
                    </div>
                    <pre x-ref="badge_{{ $key }}" class="bg-gray-50 border border-gray-200 rounded-lg p-3 text-xs text-gray-800 overflow-x-auto whitespace-pre-wrap">{{ $b['html'] }}</pre>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Konu-bazlı hazır linkler --}}
    <section>
        <h2 class="text-xl md:text-2xl font-bold text-gray-900 mb-1">{{ __('Link to a specific tool or guide') }}</h2>
        <p class="text-sm text-gray-600 mb-4">{{ __('Writing about a specific topic? Link straight to the most relevant page.') }}</p>
        <div class="divide-y divide-gray-100 border border-gray-200 rounded-xl overflow-hidden">
            @foreach ($deepLinks as $i => $dl)
                @php $code = '<a href="' . $dl['url'] . '">' . $dl['text'] . '</a>'; @endphp
                <div class="flex items-center gap-3 p-3 hover:bg-gray-50">
                    <div class="flex-1 min-w-0">
                        <div class="font-semibold text-gray-900 text-sm">{{ $dl['label'] }}</div>
                        <code class="text-xs text-gray-400 truncate block">{{ $dl['url'] }}</code>
                    </div>
                    <pre x-ref="dl_{{ $i }}" class="hidden">{{ $code }}</pre>
                    <button type="button" @click="copy('dl_{{ $i }}', $refs.dl_{{ $i }}.textContent)"
                            class="shrink-0 text-xs font-semibold px-3 py-1.5 rounded transition"
                            :class="copied === 'dl_{{ $i }}' ? 'bg-emerald-100 text-emerald-700' : 'bg-primary-600 text-white hover:bg-primary-700'"
                            x-text="copied === 'dl_{{ $i }}' ? '{{ __('Copied!') }}' : '{{ __('Copy HTML') }}'"></button>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Gömülebilir hesaplayıcı --}}
    <section>
        <h2 class="text-xl md:text-2xl font-bold text-gray-900 mb-1">{{ __('Embed our calculator') }}</h2>
        <p class="text-sm text-gray-600 mb-4">{{ __('Add our interactive Germany cost-of-living calculator to your blog or forum — free, no signup. It links back to us.') }}</p>
        <div class="grid md:grid-cols-2 gap-6 items-start">
            <div>
                <span class="text-xs text-gray-500 block mb-2">{{ __('Live preview:') }}</span>
                <iframe src="{{ $embedSrc }}" width="100%" height="560" loading="lazy" style="border:0;max-width:480px" title="{{ __('Germany Cost of Living Calculator') }} — {{ $name }}"></iframe>
            </div>
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('Embed code') }}</span>
                    <button type="button" @click="copy('embed', $refs.embed.textContent)"
                            class="text-xs font-semibold px-2.5 py-1 rounded transition"
                            :class="copied === 'embed' ? 'bg-emerald-100 text-emerald-700' : 'bg-primary-600 text-white hover:bg-primary-700'"
                            x-text="copied === 'embed' ? '{{ __('Copied!') }}' : '{{ __('Copy') }}'"></button>
                </div>
                <pre x-ref="embed" class="bg-gray-50 border border-gray-200 rounded-lg p-3 text-xs text-gray-800 overflow-x-auto whitespace-pre-wrap">{{ $embedHtml }}</pre>
                <p class="text-xs text-gray-400 mt-2">{{ __('Paste this HTML where you want the calculator to appear.') }}</p>
            </div>
        </div>
    </section>

    {{-- Gömülebilir istatistik sayacı --}}
    <section>
        <h2 class="text-xl md:text-2xl font-bold text-gray-900 mb-1">{{ __('Embed our live stats') }}</h2>
        <p class="text-sm text-gray-600 mb-4">{{ __('A compact, always-up-to-date counter of German higher-education numbers — great for articles and infographics.') }}</p>
        <div class="space-y-4">
            <div>
                <span class="text-xs text-gray-500 block mb-2">{{ __('Live preview:') }}</span>
                <iframe src="{{ $statsSrc }}" width="100%" height="200" loading="lazy" style="border:0;max-width:560px" title="{{ __('Germany Study Statistics') }} — {{ $name }}"></iframe>
            </div>
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('Embed code') }}</span>
                    <button type="button" @click="copy('statsembed', $refs.statsembed.textContent)"
                            class="text-xs font-semibold px-2.5 py-1 rounded transition"
                            :class="copied === 'statsembed' ? 'bg-emerald-100 text-emerald-700' : 'bg-primary-600 text-white hover:bg-primary-700'"
                            x-text="copied === 'statsembed' ? '{{ __('Copied!') }}' : '{{ __('Copy') }}'"></button>
                </div>
                <pre x-ref="statsembed" class="bg-gray-50 border border-gray-200 rounded-lg p-3 text-xs text-gray-800 overflow-x-auto whitespace-pre-wrap">{{ $statsHtml }}</pre>
            </div>
        </div>
    </section>

    {{-- Veriyi alıntıla --}}
    <section>
        <h2 class="text-xl md:text-2xl font-bold text-gray-900 mb-1">{{ __('Cite our data') }}</h2>
        <p class="text-sm text-gray-600 mb-4">{{ __('Free to quote our German study statistics — please link back.') }}</p>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">
            @foreach ([
                [number_format($totals['programs']), __('Study programs')],
                [number_format($totals['universities']), __('Universities')],
                [($totals['programs'] ? (int) round($totals['programs_en'] / $totals['programs'] * 100) : 0) . '%', __('English-taught')],
                [number_format($totals['scholarships']), __('Scholarships')],
            ] as [$v, $l])
                <div class="bg-primary-50 rounded-xl p-4 text-center">
                    <div class="text-2xl font-extrabold text-primary-800">{{ $v }}</div>
                    <div class="text-xs text-gray-600 mt-1">{{ $l }}</div>
                </div>
            @endforeach
        </div>
        <div class="flex items-center justify-between mb-1.5">
            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('Citation') }}</span>
            <button type="button" @click="copy('cite', $refs.cite.textContent)"
                    class="text-xs font-semibold px-2.5 py-1 rounded transition"
                    :class="copied === 'cite' ? 'bg-emerald-100 text-emerald-700' : 'bg-primary-600 text-white hover:bg-primary-700'"
                    x-text="copied === 'cite' ? '{{ __('Copied!') }}' : '{{ __('Copy') }}'"></button>
        </div>
        <pre x-ref="cite" class="bg-gray-50 border border-gray-200 rounded-lg p-3 text-xs text-gray-800 overflow-x-auto whitespace-pre-wrap">{{ $citation }}</pre>
        <a href="{{ route('stats') }}" class="inline-block mt-3 text-primary-600 font-semibold text-sm hover:underline">{{ __('See full statistics') }} →</a>
    </section>

    {{-- Basın kiti --}}
    <section>
        <h2 class="text-xl md:text-2xl font-bold text-gray-900 mb-1">{{ __('Press kit') }}</h2>
        <p class="text-sm text-gray-600 mb-4">{{ __('Logos, brand colors and a ready-to-use description for journalists and bloggers.') }}</p>

        <h3 class="text-sm font-semibold text-gray-700 mb-2">{{ __('Logos') }}</h3>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
            @foreach ($pressAssets as $a)
                <a href="{{ asset($a['file']) }}" download
                   class="flex flex-col items-center justify-center gap-2 border border-gray-200 rounded-xl p-4 hover:border-primary-400 hover:shadow-sm transition text-center">
                    <span class="text-2xl">⬇</span>
                    <span class="text-xs font-semibold text-gray-700 leading-tight">{{ $a['label'] }}</span>
                </a>
            @endforeach
        </div>

        <h3 class="text-sm font-semibold text-gray-700 mb-2">{{ __('Brand colors') }}</h3>
        <div class="flex gap-4 mb-6 flex-wrap">
            @foreach ($brandColors as [$hex, $clabel])
                <div class="flex items-center gap-2">
                    <span class="w-8 h-8 rounded-lg border border-gray-200" style="background:{{ $hex }}"></span>
                    <div>
                        <div class="text-xs font-semibold text-gray-700">{{ $clabel }}</div>
                        <code class="text-xs text-gray-400">{{ $hex }}</code>
                    </div>
                </div>
            @endforeach
        </div>

        <h3 class="text-sm font-semibold text-gray-700 mb-2">{{ __('Boilerplate (about us)') }}</h3>
        <div class="flex items-center justify-between mb-1.5">
            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('Description') }}</span>
            <button type="button" @click="copy('boiler', $refs.boiler.textContent)"
                    class="text-xs font-semibold px-2.5 py-1 rounded transition"
                    :class="copied === 'boiler' ? 'bg-emerald-100 text-emerald-700' : 'bg-primary-600 text-white hover:bg-primary-700'"
                    x-text="copied === 'boiler' ? '{{ __('Copied!') }}' : '{{ __('Copy') }}'"></button>
        </div>
        <pre x-ref="boiler" class="bg-gray-50 border border-gray-200 rounded-lg p-3 text-xs text-gray-800 overflow-x-auto whitespace-pre-wrap">{{ $boilerplate }}</pre>
    </section>

    {{-- İletişim --}}
    <section class="bg-gray-50 rounded-2xl p-6 text-center">
        <h2 class="text-lg font-bold text-gray-900 mb-1">{{ __('Press & partnerships') }}</h2>
        <p class="text-sm text-gray-600">{{ __('Journalists, bloggers or partners — get in touch for custom data or a quote.') }}</p>
        <a href="{{ route('contact', ['type' => 'partnership']) }}" class="inline-flex items-center gap-2 mt-3 bg-primary-600 hover:bg-primary-700 text-white font-bold py-2.5 px-5 rounded-lg transition">{{ __('Contact us') }} →</a>
    </section>
</div>
@endsection
