@extends('layouts.app')

@section('title', __('Team & Contributors') . ' — ' . brand('name'))

<x-seo
    :title="__('Team & Contributors') . ' — ' . brand('name')"
    :description="__('Founder, content editors and mentors building AlmanyaUni — the Germany study guide for international students. Growing together.')"
/>

@section('content')

{{-- HERO --}}
<section class="bg-gradient-to-br from-indigo-700 via-purple-600 to-pink-500 text-white">
    <div class="max-w-[1400px] mx-auto px-4 py-12 md:py-16">
        <nav class="text-sm text-indigo-100 mb-3">
            <a href="/" class="hover:text-white">{{ __('Home') }}</a>
            <span class="mx-2 opacity-60">›</span>
            <span class="text-white">{{ __('Team') }}</span>
        </nav>
        <h1 class="text-3xl md:text-5xl font-extrabold leading-tight drop-shadow mb-3">👥 {{ __('Team & Contributors') }}</h1>
        <p class="text-lg md:text-xl text-indigo-100 max-w-3xl">
            {{ __('We grow AlmanyaUni together for international students — founder, content editors and mentors. So far :count guide articles, thousands of pages of content.', ['count' => $totalPosts]) }}
        </p>
    </div>
</section>

@php
    $colors = ['#4f46e5','#0891b2','#db2777','#059669','#d97706','#7c3aed'];
    $initials = fn($name) => collect(explode(' ', trim($name)))->map(fn($p) => mb_substr($p,0,1))->take(2)->implode('');
    // Yazar verisini modal için JS objesine çevir
    $authorData = fn($p, $color) => [
        'name'        => $p->name,
        'role'        => $p->role_label,
        'bio'         => $p->bio,
        'avatar'      => $p->avatar_url,
        'initials'    => $initials($p->name),
        'color'       => $color,
        'posts_count' => $p->posts_count,
        'posts'       => $p->posts->map(fn($post) => [
            'title' => $post->title,
            'url'   => route('blog.show', $post->slug),
            'date'  => optional($post->published_at)->format('d.m.Y'),
            'min'   => $post->reading_minutes,
        ])->all(),
    ];
@endphp

<div x-data="{ open: false, author: {} }" class="max-w-[1400px] mx-auto px-4 py-12 space-y-14">

    {{-- KURUCU --}}
    @if ($founders->isNotEmpty())
        <section>
            <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-2">⭐ {{ __('Founder') }}</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                @foreach ($founders as $i => $p)
                    <article @click="author = @js($authorData($p, $colors[$i % count($colors)])); open = true"
                             class="bg-white border-2 border-indigo-200 rounded-2xl p-6 flex gap-5 shadow-sm cursor-pointer hover:border-indigo-400 hover:shadow-md transition">
                        @if ($p->avatar_url)
                            <img src="{{ $p->avatar_url }}" alt="{{ $p->name }}" class="w-20 h-20 rounded-full object-cover ring-4 ring-indigo-100 flex-shrink-0">
                        @else
                            <div class="w-20 h-20 rounded-full flex items-center justify-center text-white text-2xl font-extrabold flex-shrink-0" style="background: {{ $colors[$i % count($colors)] }};">{{ $initials($p->name) }}</div>
                        @endif
                        <div class="flex-1 min-w-0">
                            <h3 class="text-xl font-bold text-gray-900">{{ $p->name }}</h3>
                            <p class="text-sm font-semibold text-indigo-600 mb-2">{{ $p->role_label }}</p>
                            <p class="text-sm text-gray-600 leading-relaxed">{{ $p->bio }}</p>
                            @if ($p->posts_count > 0)
                                <p class="text-xs text-gray-400 mt-2">✍️ {{ __(':count posts', ['count' => $p->posts_count]) }}</p>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    @endif

    {{-- İÇERİK EDİTÖRLERİ --}}
    @if ($editors->isNotEmpty())
        <section>
            <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-2">✍️ {{ __('Content Editors') }}</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach ($editors as $i => $p)
                    <article @click="author = @js($authorData($p, $colors[$i % count($colors)])); open = true"
                             class="bg-white border border-gray-200 rounded-2xl p-5 text-center hover:shadow-md hover:border-indigo-300 transition cursor-pointer">
                        @if ($p->avatar_url)
                            <img src="{{ $p->avatar_url }}" alt="{{ $p->name }}" class="w-20 h-20 rounded-full object-cover ring-2 ring-gray-100 mx-auto mb-3">
                        @else
                            <div class="w-20 h-20 rounded-full flex items-center justify-center text-white text-xl font-extrabold mx-auto mb-3" style="background: {{ $colors[$i % count($colors)] }};">{{ $initials($p->name) }}</div>
                        @endif
                        <h3 class="font-bold text-gray-900">{{ $p->name }}</h3>
                        <p class="text-xs font-semibold text-indigo-600 mb-2">{{ $p->role_label }}</p>
                        @if ($p->bio)
                            <p class="text-xs text-gray-500 leading-relaxed line-clamp-3">{{ $p->bio }}</p>
                        @endif
                        @if ($p->posts_count > 0)
                            <span class="inline-block mt-3 text-[11px] font-semibold px-2.5 py-1 rounded-full bg-indigo-50 text-indigo-700">{{ __(':count posts', ['count' => $p->posts_count]) }}</span>
                        @endif
                    </article>
                @endforeach
            </div>
        </section>
    @endif

    {{-- MENTORLAR --}}
    @if ($mentors->isNotEmpty())
        <section>
            <div class="flex items-baseline justify-between mb-6 flex-wrap gap-2">
                <h2 class="text-2xl font-bold text-gray-900 flex items-center gap-2">🤝 {{ __('Mentors') }}</h2>
                <a href="{{ route('mentors.index') }}" class="text-sm text-indigo-600 hover:underline">{{ __('All mentors →') }}</a>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($mentors as $m)
                    <a href="{{ route('mentors.show', $m->slug) }}" class="flex items-center gap-3 bg-white border border-gray-200 rounded-xl p-4 hover:border-emerald-300 hover:shadow-md transition">
                        @if ($m->avatar_url)
                            <img src="{{ $m->avatar_url }}" alt="{{ $m->name }}" class="w-12 h-12 rounded-full object-cover">
                        @else
                            <div class="w-12 h-12 rounded-full bg-gradient-to-br from-emerald-500 to-teal-500 text-white font-bold flex items-center justify-center">{{ $initials($m->name) }}</div>
                        @endif
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-gray-900 truncate">{{ $m->name }}</p>
                            <p class="text-xs text-gray-500 truncate">{{ $m->headline ?: $m->current_company }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- DİĞER KATKI SAĞLAYANLAR --}}
    @if ($others->isNotEmpty())
        <section>
            <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-2">🌱 {{ __('Contributors') }}</h2>
            <div class="flex flex-wrap gap-4">
                @foreach ($others as $i => $p)
                    <div class="flex items-center gap-3 bg-white border border-gray-200 rounded-xl px-4 py-3">
                        @if ($p->avatar_url)
                            <img src="{{ $p->avatar_url }}" alt="{{ $p->name }}" class="w-10 h-10 rounded-full object-cover">
                        @else
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-white text-sm font-bold" style="background: {{ $colors[$i % count($colors)] }};">{{ $initials($p->name) }}</div>
                        @endif
                        <div>
                            <p class="font-semibold text-sm text-gray-900">{{ $p->name }}</p>
                            <p class="text-xs text-gray-500">{{ $p->role_label ?: __('Contributor') }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    {{-- TOPLULUK KATKICILARI --}}
    @if (! empty($contributors) && $contributors->isNotEmpty())
        <section>
            <div class="flex items-baseline justify-between mb-6 flex-wrap gap-2">
                <h2 class="text-2xl font-bold text-gray-900 flex items-center gap-2">🌱 {{ __('Community Contributors') }}</h2>
                <span class="text-sm text-gray-500">{{ __(':contributions approved contributions · :people+ people', ['contributions' => $totalContributions, 'people' => $contributors->count()]) }}</span>
            </div>
            <p class="text-sm text-gray-600 mb-5 max-w-2xl">{{ __('Community members who guide other international students by sharing their experience. Share yours and you will appear here too.') }}</p>
            <div class="flex flex-wrap gap-3">
                @foreach ($contributors as $i => $c)
                    <div class="flex items-center gap-2.5 bg-white border border-gray-200 rounded-full pl-1.5 pr-4 py-1.5" title="{{ __('Community Contributor') }}">
                        @if ($c->avatar_url)
                            <img src="{{ $c->avatar_url }}" alt="{{ $c->name }}" class="w-8 h-8 rounded-full object-cover">
                        @else
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold" style="background: {{ $colors[$i % count($colors)] }};">{{ $initials($c->name) }}</div>
                        @endif
                        <span class="text-sm font-medium text-gray-800">{{ $c->name }}</span>
                        <span class="text-[10px]">🌱</span>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    {{-- ════ YAZAR PROFİL MODAL ════ --}}
    <div x-show="open" x-cloak
         @keydown.escape.window="open = false"
         class="fixed inset-0 z-[70] flex items-center justify-center p-4">
        {{-- backdrop --}}
        <div @click="open = false" class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>
        {{-- panel --}}
        <div x-show="open" x-transition
             class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[85vh] overflow-y-auto">
            {{-- kapat --}}
            <button @click="open = false" class="absolute top-4 right-4 w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-gray-500 text-lg z-10">×</button>

            {{-- profil başlık --}}
            <div class="p-6 pb-4 border-b border-gray-100">
                <div class="flex items-center gap-4">
                    <template x-if="author.avatar">
                        <img :src="author.avatar" :alt="author.name" class="w-20 h-20 rounded-full object-cover ring-4 ring-indigo-100">
                    </template>
                    <template x-if="!author.avatar">
                        <div class="w-20 h-20 rounded-full flex items-center justify-center text-white text-2xl font-extrabold" :style="`background: ${author.color}`" x-text="author.initials"></div>
                    </template>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-xl font-bold text-gray-900" x-text="author.name"></h3>
                        <p class="text-sm font-semibold text-indigo-600" x-text="author.role"></p>
                        <p class="text-xs text-gray-400 mt-1"><span x-text="author.posts_count"></span> {{ __('posts') }}</p>
                    </div>
                </div>
                <p class="text-sm text-gray-600 leading-relaxed mt-4" x-text="author.bio"></p>
            </div>

            {{-- yazıları --}}
            <div class="p-6 pt-4">
                <p class="text-xs font-bold uppercase tracking-wider text-gray-500 mb-3">✍️ {{ __('Posts') }}</p>
                <template x-if="!author.posts || author.posts.length === 0">
                    <p class="text-sm text-gray-400">{{ __('No posts published yet.') }}</p>
                </template>
                <div class="space-y-2">
                    <template x-for="post in author.posts" :key="post.url">
                        <a :href="post.url" class="block p-3 rounded-lg border border-gray-100 hover:bg-indigo-50 hover:border-indigo-200 transition">
                            <p class="font-semibold text-sm text-gray-900 leading-snug" x-text="post.title"></p>
                            <p class="text-xs text-gray-400 mt-1">
                                <span x-text="post.date"></span> · <span x-text="post.min"></span> {{ __('min read') }}
                            </p>
                        </a>
                    </template>
                </div>
            </div>
        </div>
    </div>

    {{-- KATIL CTA --}}
    <section class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-2xl p-8 text-center">
        <h2 class="text-2xl font-extrabold mb-2">🌟 {{ __('Contribute too') }}</h2>
        <p class="text-indigo-100 mb-5 max-w-2xl mx-auto">
            {{ __('Content writing, German translation, mentoring or sharing your student experience — AlmanyaUni grows with community support.') }}
        </p>
        <div class="flex flex-wrap gap-3 justify-center">
            <a href="{{ route('contribute') }}" class="bg-white text-indigo-700 hover:bg-gray-100 px-6 py-3 rounded-lg font-bold shadow-lg transition">🌱 {{ __('Share your experience') }}</a>
            <a href="mailto:technsug@gmail.com?subject=AlmanyaUni%20Editor" class="bg-white/15 backdrop-blur hover:bg-white/25 px-6 py-3 rounded-lg font-bold transition">✍️ {{ __('Become a content editor') }}</a>
            <a href="mailto:technsug@gmail.com?subject=AlmanyaUni%20Mentor" class="bg-white/15 backdrop-blur hover:bg-white/25 px-6 py-3 rounded-lg font-bold transition">🤝 {{ __('Become a mentor') }}</a>
        </div>
    </section>
</div>
@endsection
