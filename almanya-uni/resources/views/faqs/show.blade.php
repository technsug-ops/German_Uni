@extends('layouts.app')

@section('title', $faq->question . '  — ' . brand('name'))

@php
    $metaDescription = $faq->has_answer
        ? \Illuminate\Support\Str::limit(strip_tags($faq->answer_html ?? ''), 160)
        : ($faq->question . ' — ' . __('Detailed answer for international students who want to study in Germany.'));
@endphp

<x-seo
    :title="$faq->question"
    :description="$metaDescription"
/>

<x-json-ld :data="\App\Support\Seo::breadcrumbs([
    ['name' => __('Home'), 'url' => route('home')],
    ['name' => __('FAQ'), 'url' => route('faqs.index')],
    ['name' => $topic->name, 'url' => route('faqs.topic', $topic->slug)],
    ['name' => $faq->question, 'url' => route('faqs.show', [$topic->slug, $faq->slug])],
])" />

@if ($faq->has_answer)
    @php
        $qaSchema = [
            '@context' => 'https://schema.org',
            '@type' => $faq->intent === 'nasil' ? 'HowTo' : 'QAPage',
            $faq->intent === 'nasil' ? 'name' : 'mainEntity' => $faq->intent === 'nasil'
                ? $faq->question
                : [
                    '@type' => 'Question',
                    'name' => $faq->question,
                    'answerCount' => 1,
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => strip_tags($faq->answer_html ?? ''),
                        'upvoteCount' => 0,
                    ],
                ],
            'speakable' => [
                '@type' => 'SpeakableSpecification',
                'cssSelector' => ['h1', '.blog-content'],
            ],
        ];
    @endphp
    <x-json-ld :data="$qaSchema" />
@endif

@section('content')
<article class="max-w-[1400px] mx-auto px-4 py-10">
    <!-- Breadcrumb -->
    <nav class="text-sm text-gray-500 mb-6">
        <a href="{{ route('faqs.index') }}" class="hover:text-primary-600">{{ __('FAQ') }}</a>
        <span class="mx-2">/</span>
        <a href="{{ route('faqs.topic', $topic->slug) }}" class="hover:text-primary-600">{{ $topic->name }}</a>
    </nav>

    <!-- Topic badge -->
    <a href="{{ route('faqs.topic', $topic->slug) }}"
       class="inline-flex items-center gap-2 text-sm font-semibold uppercase tracking-wide mb-3"
       style="color: {{ $topic->color ?? '#1E40AF' }}">
        @if ($topic->icon)<span>{{ $topic->icon }}</span>@endif
        {{ $topic->name }}
    </a>

    <!-- Question -->
    <h1 class="text-3xl md:text-4xl font-bold leading-tight mb-4 text-gray-900">{{ $faq->question }}</h1>

    <!-- Meta -->
    <div class="flex flex-wrap items-center gap-3 text-sm text-gray-500 pb-6 mb-8 border-b border-gray-200">
        @if ($faq->has_answer)
            <span class="text-green-600 font-semibold">✓ {{ __('Answered') }}</span>
            <span>·</span>
            <span>{{ $faq->answer_minutes }} {{ __('min read') }}</span>
            <span>·</span>
        @endif
        @if ($faq->intentLabel())
            <span class="inline-block bg-primary-50 text-primary-700 px-2 py-0.5 rounded text-xs font-semibold">
                {{ $faq->intentLabel() }}
            </span>
            <span>·</span>
        @endif
        <span>{{ __(':count views', ['count' => number_format($faq->view_count)]) }}</span>
    </div>

    <!-- Answer -->
    @if ($faq->has_answer)
        <div class="blog-content mb-10">
            {!! $faq->answer_html !!}
        </div>
    @else
        <div class="bg-yellow-50 border border-yellow-300 rounded-lg p-6 mb-10">
            <p class="font-semibold text-yellow-900 mb-2">{{ __('No answer has been written for this question yet') }}</p>
            <p class="text-gray-700 text-sm">
                {{ __('This question was extracted from community data and a detailed answer will be added soon. You can browse other answered questions in the same category below.') }}
            </p>
        </div>
    @endif

    <!-- Related -->
    @if ($related->isNotEmpty())
        <section class="mt-12 pt-8 border-t border-gray-200">
            <h2 class="text-2xl font-bold mb-6">{{ __('Related Questions in :topic', ['topic' => $topic->name]) }}</h2>
            <div class="space-y-2">
                @foreach ($related as $r)
                    <a href="{{ route('faqs.show', [$topic->slug, $r->slug]) }}"
                       class="flex items-start justify-between gap-4 bg-white border border-gray-200 hover:border-primary-500 transition rounded-lg p-3">
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-gray-900 leading-snug">{{ $r->question }}</p>
                            @if ($r->has_answer)
                                <span class="inline-block text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded font-semibold mt-1">
                                    ✓ {{ __('Answered') }}
                                </span>
                            @endif
                        </div>
                        <span class="text-primary-500 flex-shrink-0">→</span>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    <!-- CTA -->
    <div class="mt-12 bg-primary-50 border border-primary-200 rounded-lg p-6 text-center">
        <p class="font-semibold text-primary-900 mb-2">{{ __('Explore all FAQs') }}</p>
        <p class="text-gray-700 text-sm mb-4">
            {{ __('200+ questions and answers across 14 topics including :topic.', ['topic' => $topic->name]) }}
        </p>
        <a href="{{ route('faqs.index') }}" class="inline-block bg-primary-500 hover:bg-primary-600 text-white px-6 py-2 rounded font-semibold transition">
            {{ __('FAQ Home →') }}
        </a>
    </div>
</article>
@endsection
