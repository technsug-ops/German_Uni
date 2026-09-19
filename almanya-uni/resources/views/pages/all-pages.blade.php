@extends('layouts.app')

@section('title', __('All pages') . ' — ' . brand('name'))

<x-seo
    :title="__('All pages') . ' — ' . brand('name')"
    :description="__('Every tool, guide and section on :brand in one list — including the ones hidden from the mobile menu.', ['brand' => brand('name')])"
/>

@section('content')
<div class="max-w-5xl mx-auto px-4 py-10">

    <header class="mb-8">
        <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-3">{{ __('All pages') }}</h1>
        <p class="text-gray-700 leading-relaxed">
            {{ __('The mobile menu is kept short on purpose, so some tools do not appear there. This page lists everything — marked items are the ones hidden from the mobile menu.') }}
        </p>
    </header>

    {{-- Menü grupları --}}
    @foreach ($menu as $group)
        <section class="mb-10">
            <h2 class="text-lg font-bold text-gray-900 mb-4 pb-2 border-b border-gray-200">
                {{ $group['label'] }}
                <span class="ml-1 text-sm font-normal text-gray-400">({{ $group['items']->count() }})</span>
            </h2>

            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach ($group['items'] as $item)
                    <a href="{{ $item->resolved_url }}"
                       class="group flex items-start gap-3 p-3.5 rounded-xl border border-gray-200 bg-white hover:border-primary-300 hover:bg-primary-50/40 transition no-underline">
                        <span class="shrink-0 inline-flex items-center justify-center w-8 h-8 rounded-lg bg-gray-100 text-gray-500 group-hover:bg-primary-100 group-hover:text-primary-600">
                            {!! e_icon($item->icon, 'w-4 h-4') !!}
                        </span>
                        <span class="min-w-0">
                            <span class="flex items-center gap-1.5 flex-wrap">
                                <span class="font-semibold text-gray-900 group-hover:text-primary-700">{{ $item->label }}</span>
                                @if ($item->badge)
                                    <span class="text-[10px] font-bold uppercase px-1.5 py-0.5 rounded bg-amber-400 text-amber-900">{{ __($item->badge) }}</span>
                                @endif
                                @if ($item->hide_on_mobile)
                                    <span class="text-[10px] font-semibold px-1.5 py-0.5 rounded bg-gray-100 text-gray-500"
                                          title="{{ __('Not shown in the mobile menu') }}">{{ __('desktop menu') }}</span>
                                @endif
                            </span>
                            @if ($item->description)
                                <span class="block text-xs text-gray-500 mt-0.5 leading-snug">{{ $item->description }}</span>
                            @endif
                        </span>
                    </a>
                @endforeach
            </div>
        </section>
    @endforeach

    {{-- Kurum / hakkında sayfaları: menüde değil, footer'da duruyorlar --}}
    <section class="mb-10">
        <h2 class="text-lg font-bold text-gray-900 mb-4 pb-2 border-b border-gray-200">{{ __('About & institutional') }}</h2>
        @php
            $institutional = [
                ['label' => __('About Us'),        'url' => route('about')],
                ['label' => __('Team'),            'url' => route('team')],
                ['label' => __('Contact'),         'url' => route('contact')],
                ['label' => __('Link to us'),      'url' => route('link-to-us'),   'note' => __('Press & data')],
                ['label' => __('Advertise with us'),'url' => route('advertise'),   'note' => __('Partnerships')],
                ['label' => __('Imprint'),         'url' => route('legal.impressum')],
                ['label' => __('Privacy Policy'),  'url' => route('legal.privacy')],
                ['label' => __('Cookie Policy'),   'url' => route('legal.cookies')],
            ];
        @endphp
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-3">
            @foreach ($institutional as $p)
                <a href="{{ $p['url'] }}"
                   class="group flex items-center justify-between gap-3 p-3.5 rounded-xl border border-gray-200 bg-white hover:border-primary-300 hover:bg-primary-50/40 transition no-underline">
                    <span class="min-w-0">
                        <span class="font-semibold text-gray-900 group-hover:text-primary-700">{{ $p['label'] }}</span>
                        @if (! empty($p['note']))
                            <span class="block text-xs text-gray-500 mt-0.5">{{ $p['note'] }}</span>
                        @endif
                    </span>
                    <span class="shrink-0 text-gray-300 group-hover:text-primary-500" aria-hidden="true">→</span>
                </a>
            @endforeach
        </div>
    </section>

    <p class="text-sm text-gray-500">
        {{ __('Looking for something specific? Use the search box at the top of the page.') }}
    </p>

</div>
@endsection
