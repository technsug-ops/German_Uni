@extends('layouts.app')

@section('title', __('Link to us / Press') . ' — ' . brand('name'))

<x-seo
    :title="__('Link to us / Press Kit') . ' — ' . brand('name')"
    :description="__('Ready-to-use links, badges and citable statistics about studying in Germany. Cite :brand with a link.', ['brand' => brand('name')])"
/>

@php
    $domain = 'https://' . brand('domain');
    $name   = brand('name');
    $linkHtml = '<a href="' . $domain . '">' . $name . ' — ' . __('Germany study & university guide') . '</a>';
    $linkMd   = '[' . $name . ' — ' . __('Germany study & university guide') . '](' . $domain . ')';
    $badgeHtml = '<a href="' . $domain . '" style="display:inline-flex;align-items:center;gap:6px;padding:6px 12px;border-radius:8px;background:#0A0A0A;color:#fff;font:600 13px/1 sans-serif;text-decoration:none;">🇩🇪 ' . __('Data by') . ' ' . $name . '</a>';
    $citation = __(':programs study programs (:pct% English-taught) at :unis German universities — :brand.', [
        'programs' => number_format($totals['programs']),
        'pct'      => $totals['programs'] ? (int) round($totals['programs_en'] / $totals['programs'] * 100) : 0,
        'unis'     => number_format($totals['universities']),
        'brand'    => $name,
    ]) . ' ' . __('Source:') . ' ' . $domain . '/germany-study-statistics';

    $embedSrc  = $domain . '/embed/cost-of-living?lang=' . app()->getLocale();
    $embedHtml = '<iframe src="' . $embedSrc . '" width="100%" height="560" loading="lazy" style="border:0;max-width:480px" title="' . e(__('Germany Cost of Living Calculator') . ' — ' . $name) . '"></iframe>';
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

    {{-- Rozet --}}
    <section>
        <h2 class="text-xl md:text-2xl font-bold text-gray-900 mb-1">{{ __('Badge') }}</h2>
        <p class="text-sm text-gray-600 mb-4">{{ __('Paste this where you cite our data — it renders as a small linked badge.') }}</p>
        <div class="flex items-center gap-4 flex-wrap mb-3">
            <span class="text-xs text-gray-500">{{ __('Preview:') }}</span>
            {!! $badgeHtml !!}
        </div>
        <div class="flex items-center justify-between mb-1.5">
            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">HTML</span>
            <button type="button" @click="copy('badge', $refs.badge.textContent)"
                    class="text-xs font-semibold px-2.5 py-1 rounded transition"
                    :class="copied === 'badge' ? 'bg-emerald-100 text-emerald-700' : 'bg-primary-600 text-white hover:bg-primary-700'"
                    x-text="copied === 'badge' ? '{{ __('Copied!') }}' : '{{ __('Copy') }}'"></button>
        </div>
        <pre x-ref="badge" class="bg-gray-50 border border-gray-200 rounded-lg p-3 text-xs text-gray-800 overflow-x-auto whitespace-pre-wrap">{{ $badgeHtml }}</pre>
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

    {{-- İletişim --}}
    <section class="bg-gray-50 rounded-2xl p-6 text-center">
        <h2 class="text-lg font-bold text-gray-900 mb-1">{{ __('Press & partnerships') }}</h2>
        <p class="text-sm text-gray-600">{{ __('Journalists, bloggers or partners — get in touch for custom data or a quote.') }}</p>
        <a href="{{ route('contact', ['type' => 'partnership']) }}" class="inline-flex items-center gap-2 mt-3 bg-primary-600 hover:bg-primary-700 text-white font-bold py-2.5 px-5 rounded-lg transition">{{ __('Contact us') }} →</a>
    </section>
</div>
@endsection
