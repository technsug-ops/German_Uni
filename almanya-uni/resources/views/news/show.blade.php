@extends('layouts.app')

@section('title', $post->metaTitleResolved())

<x-seo
    :title="$post->title"
    :description="$post->metaDescriptionResolved()"
    :image="$post->featured_image ?: route('og.image', ['type' => 'post', 'slug' => $post->slug . '.png'])"
    type="article"
    :publishedAt="$post->published_at"
    :updatedAt="$post->updated_at"
    :author="$post->author?->name"
/>

<x-json-ld :data="\App\Support\Seo::clean(\App\Support\Seo::article($post))" />
<x-json-ld :data="\App\Support\Seo::breadcrumbs(array_filter([
    ['name' => __('Home'), 'url' => route('home')],
    ['name' => __('News from Germany'), 'url' => route('news.index')],
    $post->category ? ['name' => $post->category->name, 'url' => route('news.category', $post->category->slug)] : null,
    ['name' => $post->title, 'url' => route('news.show', $post->slug)],
]))" />

@section('content')
<article class="max-w-[760px] mx-auto px-4 py-8">
    <nav class="text-sm text-gray-500 mb-4">
        <a href="{{ route('news.index') }}" class="hover:text-primary-600">{{ __('News from Germany') }}</a>
        @if ($post->category)
            <span class="mx-1">/</span>
            <a href="{{ route('news.category', $post->category->slug) }}" class="hover:text-primary-600">{{ $post->category->name }}</a>
        @endif
    </nav>

    <div class="flex items-center gap-2 mb-3 text-xs">
        @if ($post->category)
            <span class="px-2 py-0.5 rounded-full text-white font-medium" style="background-color: {{ $post->category->color }}">{{ $post->category->name }}</span>
        @endif
        <span class="text-gray-500">{{ ($post->event_date ?? $post->published_at)?->translatedFormat('d F Y') }}</span>
        <span class="text-gray-400">· {{ $post->reading_minutes }} {{ __('min read') }}</span>
    </div>

    <h1 class="text-3xl md:text-4xl font-extrabold leading-tight text-gray-900 mb-4">{{ $post->title }}</h1>

    @if ($post->excerpt)
        <p class="text-lg text-gray-600 mb-6">{{ $post->excerpt }}</p>
    @endif

    @if ($post->featured_image)
        <img src="{{ $post->featured_image }}" alt="{{ $post->title }}"
             class="w-full rounded-xl mb-6 object-cover">
        @if ($post->featured_image_caption)
            <p class="text-xs text-gray-400 -mt-4 mb-6">{{ $post->featured_image_caption }}</p>
        @endif
    @endif

    <div class="blog-content prose-img:rounded-lg prose-img:shadow-sm prose-img:my-6 prose-img:w-full">
        {!! $post->content_html !!}
    </div>

    {{-- Kaynak atfı (telif: özgün içerik + atıf + deep-link) --}}
    @if ($post->source_url || $post->source_name)
        <div class="mt-8 p-4 rounded-lg bg-gray-50 border border-gray-200 text-sm text-gray-600">
            <span class="font-semibold">{{ __('Source') }}:</span>
            @if ($post->source_url)
                <a href="{{ $post->source_url }}" target="_blank" rel="nofollow noopener"
                   class="text-primary-600 hover:underline">{{ $post->source_name ?: $post->source_url }}</a>
            @else
                {{ $post->source_name }}
            @endif
        </div>
    @endif
</article>

@if ($related->isNotEmpty())
<section class="max-w-[760px] mx-auto px-4 pb-12">
    <h2 class="text-xl font-bold text-gray-900 mb-4">{{ __('Related news') }}</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        @foreach ($related as $r)
            <a href="{{ route('news.show', $r->slug) }}" class="group block p-4 rounded-lg border border-gray-200 hover:border-primary-400 hover:shadow transition">
                <p class="text-xs text-gray-400 mb-1">{{ ($r->event_date ?? $r->published_at)?->translatedFormat('d F Y') }}</p>
                <h3 class="font-semibold text-gray-900 group-hover:text-primary-700 leading-snug">{{ $r->title }}</h3>
            </a>
        @endforeach
    </div>
</section>
@endif
@endsection
