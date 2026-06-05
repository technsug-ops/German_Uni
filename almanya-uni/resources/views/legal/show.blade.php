@extends('layouts.app')

@section('title', $page->getTitle() . ' — ' . brand('name'))

<x-seo
    :title="$page->getTitle() . ' — ' . brand('name')"
    :description="$page->getDescription() ?? __('Legal page')"
/>

<x-json-ld :data="[
    '@context' => 'https://schema.org',
    '@type' => 'WebPage',
    'name' => $page->getTitle(),
    'description' => $page->getDescription(),
    'url' => url()->current(),
    'dateModified' => optional($page->updated_at)->toIso8601String(),
    'datePublished' => optional($page->effective_date)->toDateString(),
]" />

@section('content')

<section class="bg-gradient-to-br from-primary-700 to-primary-900 text-white">
    <div class="max-w-3xl mx-auto px-4 py-12 md:py-16">
        <p class="text-sm uppercase tracking-wide text-primary-200 mb-3">{{ __('Legal') }}</p>
        <h1 class="text-3xl md:text-5xl font-extrabold leading-tight mb-3">{{ $page->getTitle() }}</h1>
        @if ($page->effective_date)
            <p class="text-primary-100">{{ __('Last updated:') }} {{ $page->effective_date->format('d.m.Y') }}</p>
        @endif
    </div>
</section>

{{-- Markdown tablolarının (GDPR veri tablosu) garanti düzgün render'ı: prose'un
     arbitrary th/td modifier'ları derlenmediğinde sütunlar iç içe geçiyordu. --}}
<style>
    .legal-prose table { width: 100%; border-collapse: collapse; margin: 1.5rem 0; font-size: 0.9rem; }
    .legal-prose th, .legal-prose td { border: 1px solid #e5e7eb; padding: 0.5rem 0.75rem; text-align: left; vertical-align: top; }
    .legal-prose thead th { background: #f9fafb; font-weight: 600; }
    .legal-prose td strong { font-weight: 600; }
</style>

<article class="legal-prose max-w-3xl mx-auto px-4 py-12 prose prose-lg max-w-none
                prose-headings:font-extrabold prose-headings:text-gray-900
                prose-h2:text-2xl prose-h2:mt-10 prose-h2:mb-4
                prose-h3:text-lg prose-h3:mt-6 prose-h3:mb-3
                prose-p:text-gray-700 prose-p:leading-relaxed
                prose-li:text-gray-700
                prose-table:text-sm prose-table:my-4
                prose-th:bg-gray-50 prose-th:p-2 prose-td:p-2 prose-th:border prose-td:border prose-th:border-gray-200 prose-td:border-gray-200
                prose-a:text-primary-700 prose-a:no-underline hover:prose-a:underline
                prose-strong:text-gray-900
                prose-code:bg-gray-100 prose-code:px-1 prose-code:py-0.5 prose-code:rounded prose-code:text-sm prose-code:before:hidden prose-code:after:hidden">

    {!! $page->getRenderedBody() !!}

    <hr class="my-12 border-gray-200">

    <div class="not-prose text-sm text-gray-500 flex flex-wrap gap-4 justify-between items-center">
        <span>
            {{ __('Last updated:') }} {{ optional($page->updated_at)->format('d.m.Y H:i') }}
        </span>
        <a href="mailto:info@{{ str_replace('www.', '', request()->getHost()) }}" class="text-primary-600 hover:underline">
            {{ __('Questions about this page?') }}
        </a>
    </div>

</article>

@endsection
