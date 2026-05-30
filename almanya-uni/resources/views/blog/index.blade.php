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
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"><x-svg-icon name="search" class="w-4 h-4" /></span>
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
    {{-- Filtre toolbar --}}
    @php
        $hasActiveFilter = ! empty($filters['author']) || ! empty($filters['length']) || ($filters['sort'] ?? 'newest') !== 'newest' || ! empty($searchQ);
        $baseUrl = ! empty($active_category) ? route('blog.category', $active_category->slug) : route('blog.index');
        $buildUrl = function (array $override) use ($baseUrl, $filters, $searchQ) {
            $params = array_filter(array_merge([
                'q'      => $searchQ,
                'author' => $filters['author'] ?? null,
                'sort'   => ($filters['sort'] ?? 'newest') !== 'newest' ? $filters['sort'] : null,
                'length' => $filters['length'] ?? null,
            ], $override));
            return $baseUrl . (empty($params) ? '' : '?' . http_build_query($params));
        };
    @endphp
    <div class="bg-white border border-gray-200 rounded-xl p-4 mb-6 flex flex-wrap items-center gap-3 text-sm">
        {{-- Sıralama --}}
        <div class="flex items-center gap-2">
            <span class="text-xs text-gray-500 font-semibold uppercase tracking-wide">{{ __('Sort:') }}</span>
            @foreach ([
                'newest'  => ['label' => __('Newest'),  'icon' => 'sparkles'],
                'oldest'  => ['label' => __('Oldest'),  'icon' => 'calendar'],
                'popular' => ['label' => __('Popular'), 'icon' => 'fire'],
            ] as $key => $meta)
                @php $isActive = ($filters['sort'] ?? 'newest') === $key; @endphp
                <a href="{{ $buildUrl(['sort' => $key === 'newest' ? null : $key]) }}"
                   class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full text-xs font-semibold transition {{ $isActive ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    <x-svg-icon :name="$meta['icon']" class="w-3.5 h-3.5" /> {{ $meta['label'] }}
                </a>
            @endforeach
        </div>

        <span class="text-gray-300 hidden md:inline">|</span>

        {{-- Okuma süresi --}}
        <div class="flex items-center gap-2">
            <span class="text-xs text-gray-500 font-semibold uppercase tracking-wide">{{ __('Length:') }}</span>
            @foreach ([
                'short'  => ['label' => __('Short (≤5 min)'), 'icon' => 'rocket-launch'],
                'medium' => ['label' => __('Medium (5–15 min)'), 'icon' => 'book-open'],
                'long'   => ['label' => __('Long (>15 min)'),  'icon' => 'compass'],
            ] as $key => $meta)
                @php $isActive = ($filters['length'] ?? null) === $key; @endphp
                <a href="{{ $buildUrl(['length' => $isActive ? null : $key]) }}"
                   class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full text-xs font-semibold transition {{ $isActive ? 'bg-amber-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    <x-svg-icon :name="$meta['icon']" class="w-3.5 h-3.5" /> {{ $meta['label'] }}
                </a>
            @endforeach
        </div>

        @if ($hasActiveFilter)
            <span class="text-gray-300 hidden md:inline">|</span>
            <a href="{{ $baseUrl }}" class="inline-flex items-center gap-1 text-xs text-rose-600 hover:text-rose-700 font-semibold">
                ✕ {{ __('Clear filters') }}
            </a>
            <span class="text-xs text-gray-500 ml-auto">
                {{ __(':n results', ['n' => $posts->total()]) }}
            </span>
        @endif
    </div>

    {{-- Yazar filtresi - chip row --}}
    @if (! empty($authorsList) && $authorsList->isNotEmpty())
        <div class="bg-white border border-gray-200 rounded-xl p-4 mb-6">
            <div class="flex items-center gap-2 mb-3">
                <span class="text-xs text-gray-500 font-semibold uppercase tracking-wide inline-flex items-center gap-1.5"><x-svg-icon name="pencil" class="w-3.5 h-3.5" /> {{ __('Filter by author:') }}</span>
                @if (! empty($filters['author']))
                    <a href="{{ $buildUrl(['author' => null]) }}" class="text-[11px] text-rose-600 hover:underline">✕ {{ __('All authors') }}</a>
                @endif
            </div>
            <div class="flex flex-wrap gap-2">
                @foreach ($authorsList as $a)
                    @php $isActive = ($filters['author'] ?? null) === $a->slug; @endphp
                    <a href="{{ $buildUrl(['author' => $isActive ? null : $a->slug]) }}"
                       class="inline-flex items-center gap-2 pl-1 pr-3 py-1 rounded-full border-2 transition {{ $isActive ? 'border-primary-600 bg-primary-50' : 'border-gray-200 hover:border-primary-300 bg-white' }}">
                        @if ($a->avatar_url)
                            <img src="{{ $a->avatar_url }}" alt="{{ $a->name }}" class="w-7 h-7 rounded-full object-cover">
                        @else
                            <div class="w-7 h-7 rounded-full bg-gradient-to-br from-primary-600 to-primary-800 text-white text-xs font-bold flex items-center justify-center">
                                {{ strtoupper(mb_substr($a->name, 0, 1)) }}
                            </div>
                        @endif
                        <span class="text-xs font-semibold {{ $isActive ? 'text-primary-900' : 'text-gray-700' }}">{{ $a->name }}</span>
                        <span class="text-[10px] {{ $isActive ? 'text-primary-700' : 'text-gray-400' }}">({{ $a->posts_count }})</span>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Posts -->
        <div class="lg:col-span-3">
            @if ($posts->isEmpty())
                <x-empty-state
                    icon="✍️"
                    :title="__('No posts yet.')"
                    :description="__('New content for this category is coming soon. Browse our FAQ in the meantime.')"
                    :actions="[
                        ['label' => __('FAQ'), 'url' => route('faqs.index'), 'primary' => true],
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
                                        @if ($post->coAuthor)
                                            <div class="flex items-center gap-1.5 text-gray-500 text-xs">
                                                <span>{{ __('with') }}</span>
                                                @if ($post->coAuthor->avatar_url)
                                                    <img src="{{ $post->coAuthor->avatar_url }}" alt="{{ $post->coAuthor->name }}"
                                                         class="w-5 h-5 rounded-full object-cover" loading="lazy">
                                                @else
                                                    <div class="w-5 h-5 rounded-full bg-gradient-to-br from-primary-500 to-primary-700 text-white text-[10px] font-bold flex items-center justify-center">
                                                        {{ strtoupper(mb_substr($post->coAuthor->name, 0, 1)) }}
                                                    </div>
                                                @endif
                                                <span class="font-semibold text-gray-700">{{ $post->coAuthor->name }}</span>
                                            </div>
                                        @endif
                                        <span class="text-gray-300">·</span>
                                    @endif
                                    <div class="text-gray-500 flex items-center gap-2">
                                        @if ($post->published_at)
                                            <time datetime="{{ $post->published_at->toIso8601String() }}">
                                                {{ $post->published_at->translatedFormat('d M Y') }}
                                            </time>
                                            <span>·</span>
                                        @endif
                                        <span>{{ __(':n min read', ['n' => $post->reading_minutes]) }}</span>
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
