@extends('layouts.app')

@section('title', $page_title . '  — ' . brand('name'))

<x-seo :title="$page_title" :description="$page_description" />

@if (!empty($active_category))
    <x-json-ld :data="\App\Support\Seo::breadcrumbs([
        ['name' => 'Ana Sayfa', 'url' => route('home')],
        ['name' => 'Blog', 'url' => route('blog.index')],
        ['name' => $active_category->name, 'url' => route('blog.category', $active_category->slug)],
    ])" />
@endif

@section('content')
<div class="bg-gradient-to-r from-primary-500 to-primary-700 text-white py-12">
    <div class="max-w-[1400px] mx-auto px-4">
        @if (!empty($active_category))
            <nav class="text-sm text-primary-100 mb-3">
                <a href="{{ route('blog.index') }}" class="hover:text-white">← Blog</a>
            </nav>
        @endif
        <h1 class="text-3xl md:text-4xl font-bold mb-3">{{ $page_title }}</h1>
        <p class="text-lg text-primary-100 max-w-3xl">{{ $page_description }}</p>

        {{-- Blog arama --}}
        <form method="GET"
              action="{{ !empty($active_category) ? route('blog.category', $active_category->slug) : route('blog.index') }}"
              class="mt-5 max-w-2xl">
            <div class="relative">
                <input type="search" name="q" value="{{ $searchQ ?? '' }}"
                       placeholder="{{ !empty($active_category) ? __('Search in :cat...', ['cat' => $active_category->name]) : __('Search blog posts (title or content)...') }}"
                       minlength="2" maxlength="80"
                       class="w-full pl-11 pr-32 py-3 text-gray-900 placeholder-gray-500 bg-white border-2 border-white/40 rounded-lg focus:border-white focus:ring-2 focus:ring-white/40 shadow-md">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">🔍</span>
                <button type="submit" class="absolute right-1.5 top-1/2 -translate-y-1/2 bg-primary-700 hover:bg-primary-800 text-white font-semibold px-4 py-2 rounded text-sm transition">
                    {{ __('Search') }}
                </button>
            </div>
            @if (! empty($searchQ))
                <div class="mt-2 text-sm text-primary-100 flex items-center gap-3 flex-wrap">
                    <span>{{ __('Results for') }} <strong class="text-white">"{{ $searchQ }}"</strong></span>
                    <a href="{{ !empty($active_category) ? route('blog.category', $active_category->slug) : route('blog.index') }}"
                       class="text-white underline hover:no-underline">✕ {{ __('Clear search') }}</a>
                </div>
            @endif
        </form>
    </div>
</div>

<div class="max-w-[1400px] mx-auto px-4 py-10">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Posts -->
        <div class="lg:col-span-3">
            @if ($posts->isEmpty())
                <x-empty-state
                    icon="✍️"
                    :title="__('No posts yet.')"
                    :description="__('New content for this category is coming soon. Browse our FAQ in the meantime.')"
                    :actions="[
                        ['label' => __('FAQ'), 'url' => route('faqs.index'), 'primary' => true, 'icon' => '❓'],
                        ['label' => __('All blog posts'), 'url' => route('blog.index')],
                    ]"
                />
            @else
                <div class="space-y-6">
                    @foreach ($posts as $post)
                        <article class="bg-white border border-gray-200 rounded-lg overflow-hidden hover:shadow-md transition">
                            <div class="p-6">
                                @if ($post->category)
                                    <a href="{{ route('blog.category', $post->category->slug) }}"
                                       class="inline-block text-xs font-semibold uppercase tracking-wide mb-2"
                                       style="color: {{ $post->category->color ?? '#1E40AF' }}">
                                        {{ __($post->category->name) }}
                                    </a>
                                @endif
                                <h2 class="text-2xl font-bold mb-3 leading-tight flex items-start gap-2 flex-wrap">
                                    <a href="{{ route('blog.show', $post->slug) }}" class="text-gray-900 hover:text-primary-600 transition">
                                        {{ $post->title }}
                                    </a>
                                    <x-new-badge :date="$post->published_at" />
                                </h2>
                                @if ($post->excerpt)
                                    <p class="text-gray-700 leading-relaxed mb-4">{{ $post->excerpt }}</p>
                                @endif
                                <div class="flex flex-wrap items-center gap-3 text-sm">
                                    @if ($post->author)
                                        <div class="flex items-center gap-2">
                                            @if ($post->author->avatar_url)
                                                <img src="{{ $post->author->avatar_url }}" alt="{{ $post->author->name }}"
                                                     class="w-7 h-7 rounded-full object-cover" loading="lazy" decoding="async">
                                            @else
                                                <div class="w-7 h-7 rounded-full bg-gradient-to-br from-primary-600 to-primary-800 text-white text-xs font-bold flex items-center justify-center">
                                                    {{ strtoupper(mb_substr($post->author->name, 0, 1)) }}
                                                </div>
                                            @endif
                                            <span class="font-semibold text-gray-700">{{ $post->author->name }}</span>
                                        </div>
                                        <span class="text-gray-300">·</span>
                                    @endif
                                    <div class="text-gray-500 flex items-center gap-2">
                                        @if ($post->published_at)
                                            <time datetime="{{ $post->published_at->toIso8601String() }}">
                                                {{ $post->published_at->translatedFormat('d M Y') }}
                                            </time>
                                            <span>·</span>
                                        @endif
                                        <span>{{ $post->reading_minutes }} dk okuma</span>
                                    </div>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                @if ($posts->hasPages())
                    <div class="mt-8">
                        {{ $posts->links() }}
                    </div>
                @endif
            @endif
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            @include('blog._sidebar')
        </div>
    </div>
</div>
@endsection
