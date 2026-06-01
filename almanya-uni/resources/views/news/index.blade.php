@extends('layouts.app')

@section('title', $page_title . ' — ' . brand('name'))

<x-seo :title="$page_title" :description="$page_description" />

@if (!empty($activeCategory))
    <x-json-ld :data="\App\Support\Seo::breadcrumbs([
        ['name' => __('Home'), 'url' => route('home')],
        ['name' => __('News from Germany'), 'url' => route('news.index')],
        ['name' => $activeCategory->name, 'url' => route('news.category', $activeCategory->slug)],
    ])" />
@endif

@section('content')
<div class="bg-gradient-to-r from-primary-600 to-primary-800 text-white py-12">
    <div class="max-w-[1100px] mx-auto px-4">
        @if (!empty($activeCategory))
            <nav class="text-sm text-primary-100 mb-3">
                <a href="{{ route('news.index') }}" class="hover:text-white">← {{ __('News from Germany') }}</a>
            </nav>
        @endif
        <h1 class="text-3xl md:text-4xl font-bold mb-3">{{ $page_title }}</h1>
        <p class="text-lg text-primary-100 max-w-3xl">{{ $page_description }}</p>
    </div>
</div>

<div class="max-w-[1100px] mx-auto px-4 py-8">
    {{-- Kategori filtreleri --}}
    <div class="flex flex-wrap gap-2 mb-8">
        <a href="{{ route('news.index') }}"
           class="px-3 py-1.5 rounded-full text-sm font-medium transition {{ empty($activeCategory) ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            {{ __('All') }}
        </a>
        @foreach ($categories as $cat)
            @continue(($cat->posts_count ?? 0) < 1 && empty($activeCategory))
            <a href="{{ route('news.category', $cat->slug) }}"
               class="px-3 py-1.5 rounded-full text-sm font-medium transition {{ !empty($activeCategory) && $activeCategory->id === $cat->id ? 'text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
               @if(!empty($activeCategory) && $activeCategory->id === $cat->id) style="background-color: {{ $cat->color }}" @endif>
                {{ $cat->name }}
            </a>
        @endforeach
    </div>

    @if ($posts->isEmpty())
        <div class="text-center py-16 text-gray-500">
            <p class="text-lg">{{ __('No news yet — check back soon.') }}</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($posts as $p)
                <a href="{{ route('news.show', $p->slug) }}"
                   class="group bg-white rounded-xl border border-gray-200 hover:border-primary-400 hover:shadow-lg transition overflow-hidden flex flex-col">
                    @if ($p->featured_image)
                        <div class="aspect-[16/9] overflow-hidden bg-gray-100">
                            <img src="{{ $p->featured_image }}" alt="{{ $p->title }}" loading="lazy"
                                 class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                        </div>
                    @endif
                    <div class="p-5 flex flex-col flex-1">
                        <div class="flex items-center gap-2 mb-2 text-xs">
                            @if ($p->category)
                                <span class="px-2 py-0.5 rounded-full text-white font-medium" style="background-color: {{ $p->category->color }}">
                                    {{ $p->category->name }}
                                </span>
                            @endif
                            @if ($p->news_priority > 0)
                                <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-700 font-medium">📌 {{ __('Featured') }}</span>
                            @endif
                            <span class="text-gray-400 ml-auto">{{ ($p->event_date ?? $p->published_at)?->translatedFormat('d F Y') }}</span>
                        </div>
                        <h2 class="font-bold text-gray-900 leading-snug group-hover:text-primary-700 transition mb-2">{{ $p->title }}</h2>
                        <p class="text-sm text-gray-600 line-clamp-3 flex-1">{{ $p->excerpt }}</p>
                        @if ($p->source_name)
                            <p class="text-xs text-gray-400 mt-3">{{ __('Source') }}: {{ $p->source_name }}</p>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-10">{{ $posts->links() }}</div>
    @endif
</div>
@endsection
