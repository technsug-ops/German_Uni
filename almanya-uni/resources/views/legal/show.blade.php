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

{{-- Tailwind Typography (prose) plugin frontend bundle'ında DERLENMİYOR → tüm prose*
     sınıfları no-op. Preflight margin'leri sıfırladığından legal sayfası "yazılar
     birbirine karışmış" görünüyordu (QA #11, Merve 05.06.2026). Blog'un .blog-content
     deseninin aynısı: kendi self-contained tipografimiz (build gerektirmez). --}}
<style>
    .legal-prose { font-size: 1.0625rem; line-height: 1.75; color: #374151; }
    .legal-prose > * + * { margin-top: 1.1em; }
    .legal-prose h2 { font-size: 1.5rem; font-weight: 800; line-height: 1.25; margin-top: 2.25rem; margin-bottom: 0.75rem; color: #111827; }
    .legal-prose h3 { font-size: 1.2rem; font-weight: 700; line-height: 1.3; margin-top: 1.75rem; margin-bottom: 0.5rem; color: #111827; }
    .legal-prose h4 { font-size: 1.05rem; font-weight: 600; margin-top: 1.25rem; margin-bottom: 0.4rem; color: #111827; }
    .legal-prose p { color: #374151; }
    .legal-prose a { color: #1d4ed8; text-decoration: none; }
    .legal-prose a:hover { text-decoration: underline; }
    .legal-prose strong { color: #111827; font-weight: 600; }
    .legal-prose ul { list-style: disc; padding-left: 1.5em; }
    .legal-prose ol { list-style: decimal; padding-left: 1.5em; }
    .legal-prose li { margin-top: 0.4em; }
    .legal-prose li > ul, .legal-prose li > ol { margin-top: 0.4em; }
    .legal-prose blockquote { border-left: 4px solid #1d4ed8; padding-left: 1em; font-style: italic; color: #4b5563; margin: 1.25em 0; }
    .legal-prose hr { margin: 2.5rem 0; border: 0; border-top: 1px solid #e5e7eb; }
    .legal-prose code { background: #f3f4f6; padding: 0.125rem 0.375rem; border-radius: 0.25rem; font-size: 0.875em; color: #be185d; }
    .legal-prose table { width: 100%; border-collapse: collapse; margin: 1.5rem 0; font-size: 0.9rem; }
    .legal-prose th, .legal-prose td { border: 1px solid #e5e7eb; padding: 0.5rem 0.75rem; text-align: left; vertical-align: top; }
    .legal-prose thead th { background: #f9fafb; font-weight: 600; }
    .legal-prose td strong { font-weight: 600; }
</style>

{{-- NOT: prose* sınıfları frontend bundle'ında derlenmiyor (plugin yok) → hepsi no-op.
     Özellikle 'max-w-none' (tablolar için eklenmişti) 'max-w-3xl'i ezip metni tam
     genişliğe yayıyordu (QA: yazı sayfa ölçüleri dışında). Sadece .legal-prose +
     okunur genişlik bırakıldı; tipografi yukarıdaki <style> bloğunda. --}}
<article class="legal-prose max-w-3xl mx-auto px-4 py-12">

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
