@extends('layouts.app')

@section('title', $university->name . ' — ' . __('NC-Free (Zulassungsfrei) Programs') . ' — ' . brand('name'))

<x-seo
    :title="$university->name . ': ' . __('NC-Free Programs')"
    :description="$university->name . ' — ' . __('Programs you can apply to without NC (zulassungsfrei). Open admission, no quota limit.')"
    :noindex="! $has_data"
/>

@section('content')
<section class="bg-gradient-to-br from-emerald-700 via-emerald-600 to-emerald-800 text-white">
    <div class="max-w-[1400px] mx-auto px-4 py-12">
        <nav class="text-sm text-emerald-100 mb-3">
            <a href="{{ route('universities.index') }}" class="hover:text-white">{{ __('Universities') }}</a>
            <span class="mx-2">/</span>
            <a href="{{ route('universities.show', $university->slug) }}" class="hover:text-white truncate inline-block max-w-md align-bottom">{{ $university->display_name }}</a>
            <span class="mx-2">/</span>
            <span>{{ __('NC-Free') }}</span>
        </nav>
        <span class="inline-block bg-white/15 backdrop-blur border border-white/20 text-xs font-bold uppercase tracking-wider px-3 py-1 rounded-full mb-3">
            Zulassungsfrei · Ohne NC
        </span>
        <h1 class="text-3xl md:text-4xl lg:text-5xl font-extrabold leading-tight mb-3">
            {{ $university->display_name }}: {{ __('NC-Free Programs') }}
        </h1>
        <p class="text-lg text-emerald-100 max-w-3xl">
            {!! __('Programs at <strong>:uni</strong> you can apply to <strong>without NC</strong>. No quota limit — if you submit the required documents, you get in.', ['uni' => e($university->name)]) !!}
        </p>
    </div>
</section>

<div class="max-w-[1400px] mx-auto px-4 py-8">

    @if (! $has_data)
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-8 mb-8">
            <div class="flex items-start gap-4">
                <div class="flex-1">
                    <h2 class="text-xl font-bold text-blue-900 mb-2">{{ __('NC-Free Data Loading') }}</h2>
                    <p class="text-blue-800 mb-4">
                        {!! __('Our database holds a total of <strong>:total</strong> programs at <strong>:uni</strong>, but the NC status for each one has not yet arrived from our partner data provider.', ['total' => number_format($total_at_uni, 0, ',', '.'), 'uni' => e($university->name)]) !!}
                    </p>
                    <p class="text-blue-800 mb-4">
                        {{ __('For this information you can use the official Hochschulkompass filter:') }}
                    </p>
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ hochschulkompass_url($university->name, 'zulassungsfrei') }}"
                           target="_blank" rel="noopener"
                           class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold px-5 py-2.5 rounded-lg transition">
                            Hochschulkompass: {{ $university->short_name ?? $university->name }} {{ __('NC-Free') }} →
                        </a>
                        <a href="{{ route('universities.show', $university->slug) }}"
                           class="inline-block bg-white hover:bg-gray-50 border border-gray-300 text-gray-800 font-semibold px-5 py-2.5 rounded-lg transition">
                            {{ __('Back to university page') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 mb-6 flex items-center justify-between flex-wrap gap-3">
            <div>
                <p class="text-sm text-emerald-800">
                    {!! __('<strong class="text-emerald-900 text-lg">:found</strong> NC-Free programs · Out of :total programs total.', ['found' => $programs->total(), 'total' => number_format($total_at_uni, 0, ',', '.')]) !!}
                </p>
            </div>
            <a href="{{ hochschulkompass_url($university->name, 'zulassungsfrei') }}" target="_blank" rel="noopener"
               class="text-xs text-emerald-700 hover:text-emerald-900 underline">
                {{ __('View on Hochschulkompass too') }} ↗
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach ($programs as $p)
                <a href="{{ route('programs.show', $p->slug) }}"
                   class="block bg-white border border-gray-200 hover:border-emerald-400 hover:shadow-md transition rounded-xl p-5">
                    <h3 class="font-bold text-gray-900 leading-snug mb-1">{{ $p->name }}</h3>
                    <div class="flex flex-wrap gap-2 mt-2 text-xs">
                        <span class="inline-block bg-emerald-100 text-emerald-700 font-semibold px-2 py-0.5 rounded-full">{{ __('NC-Free') }}</span>
                        <span class="inline-block text-gray-600">{{ ucfirst($p->degree) }}</span>
                        @if ($p->language)
                            <span class="inline-block text-gray-600">{{ strtoupper($p->language) }}</span>
                        @endif
                        @if ($p->field)
                            <span class="inline-flex items-center gap-1 text-white text-xs px-2 py-0.5 rounded-full"
                                  style="background-color: {{ $p->field->color }};">
                                {!! e_icon($p->field->icon, 'w-3.5 h-3.5') !!}
                                {{ $p->field->name_tr }}
                            </span>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-8">{{ $programs->links() }}</div>
    @endif

    <section class="mt-12 bg-gray-50 border border-gray-200 rounded-xl p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-3">{{ __('Application tips for :uni', ['uni' => $university->name]) }}</h2>
        <ul class="space-y-2 text-sm text-gray-700">
            <li>{!! __('• NC-Free programs may still require application via <strong>Uni-Assist</strong> (for Uni-Assist member universities)') !!}</li>
            <li>{!! __('• For German-taught programs you need at least <strong>TestDaF TDN 4</strong> or <strong>DSH 2</strong>') !!}</li>
            <li>{!! __('• For English-taught programs <strong>IELTS 6.5</strong> or equivalent') !!}</li>
            @if (app()->getLocale() === 'tr')
                <li>• Türk lisesi diplomanı doğrudan Almanca <em>Hochschulzugangsberechtigung</em>'a denkleştirebilirsin (Anabin standardı)</li>
            @else
                <li>{!! __('• Your high school diploma can be equated to a German <em>Hochschulzugangsberechtigung</em> (Anabin standard)') !!}</li>
            @endif
        </ul>
        <div class="mt-4 flex flex-wrap gap-2">
            <a href="{{ route('universities.show', $university->slug) }}" class="text-sm text-primary-600 hover:underline">→ {{ __('University detail page') }}</a>
            <a href="{{ route('faqs.topic', 'master') }}" class="text-sm text-primary-600 hover:underline ml-3">→ {{ __('Master application FAQ') }}</a>
        </div>
    </section>
</div>
@endsection
