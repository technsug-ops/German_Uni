@extends('layouts.app')

@section('title', $topic->name . ' ' . __('FAQ') . ' — ' . brand('name'))

<x-seo
    :title="$topic->name . ' — ' . __('Frequently Asked Questions')"
    :description="$topic->description ?: __(':topic — frequently asked questions from students considering Germany.', ['topic' => $topic->name])"
/>

<x-json-ld :data="\App\Support\Seo::breadcrumbs([
    ['name' => __('Home'), 'url' => route('home')],
    ['name' => __('FAQ'), 'url' => route('faqs.index')],
    ['name' => $topic->name, 'url' => route('faqs.topic', $topic->slug)],
])" />

@php
    // Sadece cevaplı sorular FAQPage schema'sına dahil edilir (Google kuralı).
    $answered = $faqs->where('has_answer', true);
@endphp

@if ($answered->isNotEmpty())
    @php
        $faqPayload = [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => $answered->map(fn ($f) => [
                '@type' => 'Question',
                'name' => $f->question,
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => strip_tags($f->answer_html ?? ''),
                    'url' => route('faqs.show', [$topic->slug, $f->slug]),
                ],
            ])->values()->all(),
            'speakable' => [
                '@type' => 'SpeakableSpecification',
                'cssSelector' => ['h1', 'h2'],
            ],
        ];
    @endphp
    <x-json-ld :data="$faqPayload" />
@endif

@section('content')
<div class="relative text-white py-12 overflow-hidden">
    @if ($topic->image_url)
        <img src="{{ $topic->image_url }}" alt="" loading="eager" class="absolute inset-0 w-full h-full object-cover">
        <div class="absolute inset-0" style="background: linear-gradient(to right, {{ $topic->color ?? '#1E40AF' }}E6, {{ $topic->color ?? '#1E40AF' }}B3);"></div>
    @else
        <div class="absolute inset-0" style="background: linear-gradient(to right, {{ $topic->color ?? '#1E40AF' }}, {{ $topic->color ?? '#1E40AF' }}DD);"></div>
    @endif
    <div class="relative max-w-[1400px] mx-auto px-4">
        <nav class="text-sm opacity-80 mb-3">
            <a href="{{ route('faqs.index') }}" class="hover:opacity-100">← {{ __('All FAQ') }}</a>
        </nav>
        <div class="flex items-center gap-4 mb-3">
            <span class="inline-flex items-center justify-center w-12 h-12 rounded-xl shrink-0 bg-white/20 text-white">
                {!! e_icon($topic->icon, 'w-7 h-7') !!}
            </span>
            <h1 class="text-3xl md:text-4xl font-bold">{{ $topic->name }}</h1>
        </div>
        @if ($topic->description)
            <p class="text-lg opacity-90 max-w-3xl">{{ $topic->description }}</p>
        @endif
    </div>
</div>

@php
    // Intent rozet etiketi olan gruplardan filtre chip'leri kur (sıralı + sayılı).
    $byIntent = $faqs->groupBy('intent');
    $intentOrder = ['nasil', 'ne-kadar', 'ne-zaman', 'hangi', 'var-mi', 'neden', 'community'];
    $chips = [];
    foreach ($intentOrder as $slug) {
        if ($byIntent->has($slug) && ($label = $byIntent[$slug]->first()->intentLabel())) {
            $chips[$slug] = ['label' => $label, 'count' => $byIntent[$slug]->count()];
        }
    }
@endphp

<div class="max-w-[1400px] mx-auto px-4 py-10" x-data="faqTopicFilter()">
    <p class="text-gray-600 mb-5">
        {!! __('<strong>:count questions</strong>', ['count' => $faqs->count()]) !!}
        @if ($answered->count() > 0)
            · <span class="text-green-600 font-semibold">{{ __(':count answered', ['count' => $answered->count()]) }}</span>
        @endif
    </p>

    {{-- Sayfa içi anlık arama --}}
    <div class="relative mb-4">
        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
            <x-svg-icon name="search" class="w-5 h-5" />
        </span>
        <input type="text" x-model="q"
               placeholder="{{ __('Search this topic…') }}"
               class="w-full pl-10 pr-10 py-3 border border-gray-300 rounded-lg focus:border-primary-500 focus:ring-1 focus:ring-primary-500 outline-none">
        <button type="button" x-show="q" x-cloak @click="q = ''"
                class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600"
                aria-label="{{ __('Clear') }}">✕</button>
    </div>

    {{-- Intent filtre chip'leri --}}
    @if (count($chips) > 1)
        <div class="flex flex-wrap gap-2 mb-6">
            <button type="button" @click="intent = 'all'"
                    :class="intent === 'all' ? 'bg-primary-600 text-white border-primary-600' : 'bg-white text-gray-700 border-gray-300 hover:border-primary-400'"
                    class="text-sm font-medium px-3 py-1.5 rounded-full border transition">
                {{ __('All') }} <span class="opacity-70">{{ $faqs->count() }}</span>
            </button>
            @foreach ($chips as $slug => $chip)
                <button type="button" @click="intent = @js($slug)"
                        :class="intent === @js($slug) ? 'bg-primary-600 text-white border-primary-600' : 'bg-white text-gray-700 border-gray-300 hover:border-primary-400'"
                        class="text-sm font-medium px-3 py-1.5 rounded-full border transition">
                    {{ $chip['label'] }} <span class="opacity-70">{{ $chip['count'] }}</span>
                </button>
            @endforeach
        </div>
    @endif

    <div class="grid sm:grid-cols-2 gap-3 items-start">
        @foreach ($faqs as $faq)
            <a href="{{ route('faqs.show', [$topic->slug, $faq->slug]) }}"
               data-q="{{ $faq->question }}" data-intent="{{ $faq->intent }}"
               x-show="matches($el)" x-cloak
               class="flex items-start justify-between gap-4 bg-white border border-gray-200 hover:border-primary-500 hover:shadow-sm transition rounded-lg p-4 h-full">
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-gray-900 leading-snug">{{ $faq->question }}</p>
                    <div class="flex gap-2 mt-1.5 text-xs">
                        @if ($faq->has_answer)
                            <span class="inline-block bg-green-100 text-green-700 px-2 py-0.5 rounded font-semibold">
                                ✓ {{ __('Answer available') }} · {{ $faq->answer_minutes }} {{ __('min') }}
                            </span>
                        @else
                            <span class="inline-block bg-gray-100 text-gray-500 px-2 py-0.5 rounded">
                                {{ __('Coming soon') }}
                            </span>
                        @endif
                        @if ($faq->intentLabel())
                            <span class="inline-block bg-primary-50 text-primary-700 px-2 py-0.5 rounded">
                                {{ $faq->intentLabel() }}
                            </span>
                        @endif
                    </div>
                </div>
                <span class="text-primary-500 text-xl flex-shrink-0">→</span>
            </a>
        @endforeach
    </div>

    {{-- Sonuç yok --}}
    <p x-show="!hasResults" x-cloak class="text-center text-gray-500 py-12">
        {{ __('No questions match your search.') }}
    </p>
</div>

@push('scripts')
<script>
function faqTopicFilter() {
    const norm = (s) => (s || '').toLowerCase()
        .replace(/ı/g, 'i').replace(/ş/g, 's').replace(/ğ/g, 'g')
        .replace(/ü/g, 'u').replace(/ö/g, 'o').replace(/ç/g, 'c').replace(/â/g, 'a');
    return {
        q: '',
        intent: 'all',
        hasResults: true,
        matches(el) {
            const okIntent = this.intent === 'all' || el.dataset.intent === this.intent;
            const okText = !this.q.trim() || norm(el.dataset.q).includes(norm(this.q.trim()));
            return okIntent && okText;
        },
        init() {
            this.$watch('q', () => this.recount());
            this.$watch('intent', () => this.recount());
        },
        recount() {
            this.$nextTick(() => {
                this.hasResults = [...this.$el.querySelectorAll('[data-q]')]
                    .some((el) => this.matches(el));
            });
        },
    };
}
</script>
@endpush
@endsection
