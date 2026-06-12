@extends('layouts.app')

@section('title', $template->title . ' — ' . __('Template') . ' — ' . brand('name'))

<x-seo
    :title="$template->title . ' — ' . __('German application template')"
    :description="$template->description ?: __('Ready-to-use German application template with a fill-in guide.')"
/>

@push('head')
<style>
    /* Yazdırırken yalnız doldurulmuş belge görünsün */
    @media print {
        body * { visibility: hidden !important; }
        #tpl-print, #tpl-print * { visibility: visible !important; }
        #tpl-print { position: absolute; left: 0; top: 0; width: 100%; padding: 1.5rem; box-shadow: none !important; border: 0 !important; }
        @page { margin: 1.6cm; }
    }
</style>
@endpush

@section('content')
<div class="max-w-[1100px] mx-auto px-4 py-8 md:py-12">
    <nav class="text-sm text-gray-500 mb-4 no-print">
        <a href="/" class="hover:text-violet-600">{{ __('Home') }}</a>
        <span class="mx-2 opacity-60">›</span>
        <a href="{{ route('templates.index') }}" class="hover:text-violet-600">{{ __('Templates') }}</a>
        <span class="mx-2 opacity-60">›</span>
        <span class="text-gray-700">{{ $template->title }}</span>
    </nav>

    {{-- BAŞLIK --}}
    <header class="mb-6 no-print">
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

    {{-- İNTERAKTİF DOLDURUCU --}}
    <div x-data="templateFiller()" class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-10">
        {{-- SOL: Form --}}
        <div class="no-print">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-lg font-bold text-gray-900 inline-flex items-center gap-2">
                    <x-svg-icon name="list-bullet" class="w-5 h-5 text-violet-600" /> {{ __('Fill in the blanks') }}
                </h2>
                <span class="text-xs font-semibold px-2 py-1 rounded-full"
                      :class="blanksLeft === 0 ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500'"
                      x-text="blanksLeft === 0 ? '{{ __('All set') }}' : blanksLeft + ' {{ __('blanks left') }}'"></span>
            </div>

            @if (count($tokens))
                <div class="space-y-3 bg-white border border-gray-200 rounded-2xl p-4">
                    @foreach ($tokens as $tok)
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">
                                {{ $tok['label'] }}
                                <code class="ml-1 text-[10px] text-violet-400 font-mono">[{{ $tok['key'] }}]</code>
                            </label>
                            <textarea rows="1"
                                      x-model="fields['{{ $tok['key'] }}']"
                                      placeholder="…"
                                      class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-violet-400 focus:border-violet-400 resize-y"></textarea>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-500">{{ __('This template has no blanks to fill — just copy it.') }}</p>
            @endif

            <div class="flex flex-wrap gap-2 mt-4">
                <button type="button" @click="print()"
                        class="inline-flex items-center gap-1.5 bg-violet-600 hover:bg-violet-700 text-white font-bold text-sm py-2.5 px-4 rounded-lg transition">
                    <x-svg-icon name="document-text" class="w-4 h-4" /> {{ __('Download as PDF') }}
                </button>
                <button type="button" @click="copyText()"
                        class="inline-flex items-center gap-1.5 font-semibold text-sm py-2.5 px-4 rounded-lg transition"
                        :class="copied ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 hover:bg-gray-200 text-gray-700'">
                    <span x-text="copied ? '{{ __('Copied!') }}' : '{{ __('Copy text') }}'"></span>
                </button>
                <button type="button" @click="reset()"
                        class="inline-flex items-center gap-1.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-600 font-semibold text-sm py-2.5 px-4 rounded-lg transition">
                    {{ __('Reset') }}
                </button>
            </div>
            <p class="text-[11px] text-gray-400 mt-2">{{ __('Everything stays in your browser — we never see what you type.') }}</p>
        </div>

        {{-- SAĞ: Canlı önizleme (yazdırılacak alan) --}}
        <div class="lg:sticky lg:top-20 self-start w-full">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2 no-print">{{ __('Live preview') }}</p>
            <pre id="tpl-print" x-text="rendered"
                 class="bg-white border border-gray-200 rounded-2xl shadow-sm px-5 py-5 text-[13px] text-gray-800 whitespace-pre-wrap font-mono leading-relaxed overflow-x-auto min-h-[300px]"></pre>
        </div>
    </div>

    {{-- NASIL DOLDURULUR (rehber) --}}
    @if ($guideHtml)
        <section class="mb-8 no-print">
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
        <section class="mb-8 no-print">
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
    <section class="bg-violet-50 rounded-2xl p-6 text-center no-print">
        <p class="text-sm text-gray-700">{{ __('These templates are free for now. More premium templates and guides are on the way.') }}</p>
        <a href="{{ route('pricing') }}" class="inline-flex items-center gap-2 mt-3 text-violet-700 font-bold hover:underline">
            {{ __('See Premium') }} →
        </a>
    </section>
</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('templateFiller', () => ({
            body: @json($body),
            tokens: @json($tokens),
            fields: {},
            copied: false,
            init() {
                this.tokens.forEach(t => { if (!(t.key in this.fields)) this.fields[t.key] = ''; });
            },
            get rendered() {
                let out = this.body;
                this.tokens.forEach(t => {
                    const v = (this.fields[t.key] || '').trim();
                    if (v) out = out.split('[' + t.key + ']').join(v);
                });
                return out;
            },
            get blanksLeft() {
                return this.tokens.filter(t => !(this.fields[t.key] || '').trim()).length;
            },
            copyText() {
                navigator.clipboard.writeText(this.rendered).then(() => {
                    this.copied = true;
                    setTimeout(() => (this.copied = false), 2000);
                });
            },
            print() { window.print(); },
            reset() { this.tokens.forEach(t => (this.fields[t.key] = '')); },
        }));
    });
</script>
@endpush
@endsection
