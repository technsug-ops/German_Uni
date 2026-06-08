@extends('layouts.app')

@section('title', $field->name_tr . ' — ' . __('NC Frei (Zulassungsfrei) Programs') . ' — ' . brand('name'))

<x-seo
    :title="$field->name_tr . ': ' . $programs->total() . ' ' . __('NC Frei Programs')"
    :description="__(':field German university programs you can apply to without NC (zulassungsfrei). Open admission, no quota.', ['field' => $field->name])"
/>

@section('content')
<section class="bg-gradient-to-br from-emerald-700 via-emerald-600 to-emerald-800 text-white">
    <div class="max-w-[1400px] mx-auto px-4 py-12">
        <nav class="text-sm text-emerald-100 mb-3">
            <a href="{{ route('programs.index') }}" class="hover:text-white">{{ __('Programs') }}</a>
            <span class="mx-2">/</span>
            <a href="{{ route('programs.index', ['field' => $field->slug]) }}" class="hover:text-white">{{ $field->name }}</a>
            <span class="mx-2">/</span>
            <span>NC Frei</span>
        </nav>
        <span class="inline-block bg-white/15 backdrop-blur border border-white/20 text-xs font-bold uppercase tracking-wider px-3 py-1 rounded-full mb-3">
            🔓 Zulassungsfrei · Ohne NC
        </span>
        <h1 class="text-3xl md:text-4xl lg:text-5xl font-extrabold leading-tight mb-3">
            {{ $field->icon }} {{ $field->name }}: {{ __('NC Frei Programs') }}
        </h1>
        <p class="text-lg text-emerald-100 max-w-3xl">
            {{ __('Programs in :field in Germany you can apply to without NC.', ['field' => $field->name]) }}
            {{ __('No Numerus Clausus — admission is guaranteed once you complete the required documents.') }}
        </p>
    </div>
</section>

<div class="max-w-[1400px] mx-auto px-4 py-8">

    @if (! $has_data)
        {{-- Data yok — Hochschulkompass'a yönlendir --}}
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-8 mb-8">
            <div class="flex items-start gap-4">
                <span class="text-4xl flex-shrink-0">🔓</span>
                <div class="flex-1">
                    <h2 class="text-xl font-bold text-blue-900 mb-2">{{ __('NC Frei (Zulassungsfrei) Data Loading') }}</h2>
                    <p class="text-blue-800 mb-4">
                        {{ __('Our database has :total programs in :field, but the NC status (zulassungsfrei / örtlich / bundesweit) for each has not yet arrived from our partner data provider.', ['total' => number_format($total_in_field, 0, ',', '.'), 'field' => $field->name_tr]) }}
                    </p>
                    <p class="text-blue-800 mb-4">
                        {{ __('For this information you can use Hochschulkompass\'s official Zulassungsmodus filter:') }}
                    </p>
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ hochschulkompass_url($field->name, 'zulassungsfrei') }}"
                           target="_blank" rel="noopener"
                           class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold px-5 py-2.5 rounded-lg transition">
                            🔓 Hochschulkompass: {{ $field->name }} NC Frei →
                        </a>
                        <a href="{{ route('programs.index', ['field' => $field->slug]) }}"
                           class="inline-block bg-white hover:bg-gray-50 border border-gray-300 text-gray-800 font-semibold px-5 py-2.5 rounded-lg transition">
                            {{ __('All :field programs', ['field' => $field->name_tr]) }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @else
        {{-- Data var — programmatic SEO listesi --}}
        <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 mb-6 flex items-center justify-between flex-wrap gap-3">
            <div>
                <p class="text-sm text-emerald-800">
                    <strong class="text-emerald-900 text-lg">{{ $programs->total() }}</strong> {{ __('NC Frei programs') }} ·
                    {{ __('Out of :total :field programs in total.', ['total' => number_format($total_in_field, 0, ',', '.'), 'field' => $field->name_tr]) }}
                </p>
            </div>
            <a href="{{ hochschulkompass_url($field->name, 'zulassungsfrei') }}" target="_blank" rel="noopener"
               class="text-xs text-emerald-700 hover:text-emerald-900 underline">
                {{ __('More on Hochschulkompass ↗') }}
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach ($programs as $p)
                <a href="{{ route('programs.show', $p->slug) }}"
                   class="block bg-white border border-gray-200 hover:border-emerald-400 hover:shadow-md transition rounded-xl p-5">
                    <div class="flex items-start gap-3">
                        @if ($p->university->logo_url)
                            <img src="{{ $p->university->logo_url }}" alt="" class="w-12 h-12 object-contain bg-gray-50 rounded p-1 flex-shrink-0" loading="lazy" decoding="async">
                        @endif
                        <div class="flex-1 min-w-0">
                            <h3 class="font-bold text-gray-900 leading-snug">{{ $p->name }}</h3>
                            <p class="text-sm text-gray-600 mt-1">
                                {{ $p->university->display_name }}
                                @if ($p->university->city) · {{ $p->university->city->name }} @endif
                            </p>
                            <div class="flex flex-wrap gap-2 mt-2 text-xs">
                                <span class="inline-block bg-emerald-100 text-emerald-700 font-semibold px-2 py-0.5 rounded-full">🔓 NC Frei</span>
                                <span class="inline-block text-gray-600">{{ ucfirst($p->degree) }}</span>
                                @if ($p->language)
                                    <span class="inline-block text-gray-600">{{ strtoupper($p->language) }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-8">{{ $programs->links() }}</div>
    @endif

    {{-- SEO için açıklayıcı içerik --}}
    <section class="mt-12 prose max-w-none text-gray-700">
        <h2 class="text-2xl font-bold text-gray-900 mb-3">{{ __('What does NC Frei mean in :field?', ['field' => $field->name_tr]) }}</h2>
        <p class="mb-3">
            {{ __('In Germany, university programs fall into three groups:') }}
        </p>
        <ul class="list-disc list-inside space-y-1 mb-4">
            <li><strong class="text-emerald-700">{{ __('Zulassungsfrei (NC Frei):') }}</strong> {{ __('No quota restriction. Your high school GPA is not checked — once you complete the required documents you are admitted automatically.') }}</li>
            <li><strong class="text-orange-700">{{ __('Örtlich zulassungsbeschränkt:') }}</strong> {{ __('The university has its own cut-off NC (e.g. 2.4).') }}</li>
            <li><strong class="text-red-700">{{ __('Bundesweit zulassungsbeschränkt:') }}</strong> {{ __('Germany-wide central quota (applied via Hochschulstart — medicine, pharmacy etc.).') }}</li>
        </ul>
        <p class="mb-4">
            {{ __('In the :field area, international students often prioritise NC Frei programs because the conversion of high school diplomas into the German grade system (modifizierte bayerische Formel) can yield unpredictable results. NC Frei programs eliminate this risk.', ['field' => $field->name_tr]) }}
        </p>
        <p>
            <a href="{{ route('faqs.topic', 'master') }}" class="text-primary-600 hover:underline">→ {{ __('Germany application process FAQ') }}</a> ·
            <a href="{{ route('tools.grade-converter') }}" class="text-primary-600 hover:underline">→ {{ __('Convert your grade to the German system') }}</a>
        </p>
    </section>
</div>
@endsection
