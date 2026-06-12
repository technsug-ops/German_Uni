@extends('layouts.app')

@section('title', $template->title . ' — ' . __('Template') . ' — ' . brand('name'))

<x-seo
    :title="$template->title . ' — ' . __('German application template')"
    :description="$template->description ?: __('Ready-to-use German application template with a fill-in guide.')"
/>

@section('content')
<div class="max-w-[900px] mx-auto px-4 py-8 md:py-12">
    <nav class="text-sm text-gray-500 mb-4">
        <a href="/" class="hover:text-violet-600">{{ __('Home') }}</a>
        <span class="mx-2 opacity-60">›</span>
        <a href="{{ route('templates.index') }}" class="hover:text-violet-600">{{ __('Templates') }}</a>
        <span class="mx-2 opacity-60">›</span>
        <span class="text-gray-700">{{ $template->title }}</span>
    </nav>

    {{-- BAŞLIK --}}
    <header class="mb-6">
        <div class="flex items-center gap-2 mb-2">
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-bold rounded-full bg-violet-100 text-violet-700">
                <x-svg-icon name="document-text" class="w-3.5 h-3.5" /> {{ __('Template') }}
            </span>
            @if ($template->is_premium)
                <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-bold rounded-full bg-amber-100 text-amber-800">
                    <x-svg-icon name="star" class="w-3.5 h-3.5" /> {{ __('Premium') }}
                </span>
            @endif
        </div>
        <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900 leading-tight">{{ $template->title }}</h1>
        @if ($template->description)
            <p class="text-gray-600 mt-2">{{ $template->description }}</p>
        @endif
    </header>

    {{-- ŞABLON GÖVDESİ + KOPYALA --}}
    <div x-data="{ copied: false, copy() { navigator.clipboard.writeText(this.$refs.body.innerText).then(() => { this.copied = true; setTimeout(() => this.copied = false, 2000); }); } }"
         class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm mb-8">
        <div class="flex items-center justify-between px-4 py-2.5 bg-gray-50 border-b border-gray-200">
            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('Template') }} · {{ strtoupper($template->doc_type === 'cv' ? 'Lebenslauf' : 'DE') }}</span>
            <button type="button" @click="copy()"
                    class="inline-flex items-center gap-1.5 text-sm font-semibold px-3 py-1.5 rounded-lg transition"
                    :class="copied ? 'bg-emerald-100 text-emerald-700' : 'bg-violet-600 text-white hover:bg-violet-700'">
                <x-svg-icon name="document-text" class="w-4 h-4" />
                <span x-text="copied ? '{{ __('Copied!') }}' : '{{ __('Copy') }}'"></span>
            </button>
        </div>
        <pre x-ref="body" class="px-5 py-5 text-sm text-gray-800 whitespace-pre-wrap font-mono leading-relaxed overflow-x-auto">{{ $body }}</pre>
    </div>

    {{-- PLACEHOLDER AÇIKLAMALARI --}}
    @if (is_array($template->placeholders) && count($template->placeholders))
        <section class="mb-8">
            <h2 class="text-lg font-bold text-gray-900 mb-3 inline-flex items-center gap-2">
                <x-svg-icon name="list-bullet" class="w-5 h-5 text-violet-600" /> {{ __('What to fill in') }}
            </h2>
            <div class="overflow-x-auto rounded-xl border border-gray-200">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600 text-left">
                        <tr><th class="px-4 py-2.5">{{ __('Placeholder') }}</th><th class="px-4 py-2.5">{{ __('Meaning') }}</th></tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($template->placeholders as $ph)
                            @php
                                $label = $ph['label_' . app()->getLocale()] ?? ($ph['label_en'] ?? ($ph['label_de'] ?? ''));
                            @endphp
                            <tr>
                                <td class="px-4 py-2.5"><code class="text-violet-700 font-mono text-xs bg-violet-50 px-1.5 py-0.5 rounded">[{{ $ph['key'] }}]</code></td>
                                <td class="px-4 py-2.5 text-gray-700">{{ $label }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    @endif

    {{-- NASIL DOLDURULUR (rehber) --}}
    @if ($guideHtml)
        <section class="mb-8">
            <h2 class="text-lg font-bold text-gray-900 mb-3 inline-flex items-center gap-2">
                <x-svg-icon name="academic-cap" class="w-5 h-5 text-violet-600" /> {{ __('How to use this template') }}
            </h2>
            <div class="prose prose-sm max-w-none prose-headings:text-gray-900 prose-a:text-violet-600 bg-white border border-gray-200 rounded-xl p-5">
                {!! $guideHtml !!}
            </div>
        </section>
    @endif

    {{-- İLGİLİ ŞABLONLAR --}}
    @if ($related->isNotEmpty())
        <section class="mb-8">
            <h2 class="text-lg font-bold text-gray-900 mb-3">{{ __('Related templates') }}</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @foreach ($related as $r)
                    <a href="{{ route('templates.show', $r->slug) }}"
                       class="group bg-white border border-gray-200 hover:border-violet-400 rounded-xl p-4 flex items-center gap-3 transition">
                        <x-svg-icon :name="$r->icon" class="w-5 h-5 text-violet-500 flex-shrink-0" />
                        <span class="font-semibold text-gray-800 group-hover:text-violet-700 text-sm">{{ $r->title }}</span>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- UPSELL --}}
    <section class="bg-violet-50 rounded-2xl p-6 text-center">
        <p class="text-sm text-gray-700">{{ __('These templates are free for now. More premium templates and guides are on the way.') }}</p>
        <a href="{{ route('pricing') }}" class="inline-flex items-center gap-2 mt-3 text-violet-700 font-bold hover:underline">
            {{ __('See Premium') }} →
        </a>
    </section>
</div>
@endsection
