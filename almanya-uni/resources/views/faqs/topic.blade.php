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
<div class="bg-gradient-to-r text-white py-10"
     style="background: linear-gradient(to right, {{ $topic->color ?? '#1E40AF' }}, {{ $topic->color ?? '#1E40AF' }}DD);">
    <div class="max-w-[1400px] mx-auto px-4">
        <nav class="text-sm opacity-80 mb-3">
            <a href="{{ route('faqs.index') }}" class="hover:opacity-100">← {{ __('All FAQ') }}</a>
        </nav>
        <div class="flex items-center gap-4 mb-3">
            <h1 class="text-3xl md:text-4xl font-bold">{{ $topic->name }}</h1>
        </div>
        @if ($topic->description)
            <p class="text-lg opacity-90 max-w-3xl">{{ $topic->description }}</p>
        @endif
    </div>
</div>

<div class="max-w-[1400px] mx-auto px-4 py-10">
    <p class="text-gray-600 mb-6">
        {!! __('<strong>:count questions</strong>', ['count' => $faqs->count()]) !!}
        @if ($answered->count() > 0)
            · <span class="text-green-600 font-semibold">{{ __(':count answered', ['count' => $answered->count()]) }}</span>
        @endif
    </p>

    <div class="space-y-2">
        @foreach ($faqs as $faq)
            <a href="{{ route('faqs.show', [$topic->slug, $faq->slug]) }}"
               class="flex items-start justify-between gap-4 bg-white border border-gray-200 hover:border-primary-500 hover:shadow-sm transition rounded-lg p-4">
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
</div>
@endsection
