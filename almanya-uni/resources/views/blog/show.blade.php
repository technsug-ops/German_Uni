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
    ['name' => __('Blog'), 'url' => route('blog.index')],
    $post->category ? ['name' => $post->category->name, 'url' => route('blog.category', $post->category->slug)] : null,
    ['name' => $post->title, 'url' => route('blog.show', $post->slug)],
]))" />

@php
    $tocData = \App\Support\TocBuilder::process($post->content_html);
    // KÖK FIX: içerikteki iç linkleri render anında bu sayfanın diline çevir
    // (önek-siz linkler default locale=en'e düşmesin). [[ContentLinks]]
    $contentHtml = \App\Support\ContentLinks::localizeHtml($tocData['html'], app()->getLocale());
    $toc = $tocData['toc'];
    // Auto FAQ schema: içerikteki soru-başlıklarından FAQPage üret (AIO/AEO sinyali).
    // Görünür accordion EKLEMEZ — sorular zaten gövdede H2 olarak var; sadece JSON-LD.
    $autoFaqs = \App\Support\FaqExtractor::fromHtml($contentHtml);
@endphp

@if (count($autoFaqs) >= 2)
    <x-json-ld :data="\App\Support\Seo::genericFaqPage($autoFaqs)" />
@endif

@section('content')
{{-- Reading progress bar (sticky top, JS ile genişler) --}}
<div id="reading-progress" class="fixed top-0 left-0 right-0 h-1 bg-transparent z-50 pointer-events-none">
    <div id="reading-progress-bar" class="h-full bg-gradient-to-r from-primary-500 to-accent-500 transition-[width] duration-100" style="width:0%"></div>
</div>

<article class="max-w-[1400px] mx-auto px-4 py-10">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Main content -->
        <div class="lg:col-span-3">
            <!-- Geri + Breadcrumb -->
            <div class="flex items-center gap-3 mb-6">
                <button type="button" onclick="history.length > 1 ? history.back() : (window.location.href='{{ route('blog.index') }}')"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-gray-300 text-sm text-gray-600 hover:bg-gray-50 hover:text-primary-600 transition shrink-0">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                    {{ __('Back') }}
                </button>
                <nav class="text-sm text-gray-500">
                    <a href="{{ route('blog.index') }}" class="hover:text-primary-600">{{ __('Blog') }}</a>
                    @if ($post->category)
                        <span class="mx-2">/</span>
                        <a href="{{ route('blog.category', $post->category->slug) }}" class="hover:text-primary-600">
                            {{ __($post->category->name) }}
                        </a>
                    @endif
                </nav>
            </div>

            <!-- Category badge -->
            @if ($post->category)
                <a href="{{ route('blog.category', $post->category->slug) }}"
                   class="inline-block text-xs font-semibold uppercase tracking-wide mb-3"
                   style="color: {{ $post->category->color ?? '#1E40AF' }}">
                    {{ __($post->category->name) }}
                </a>
            @endif

            <!-- Title -->
            <h1 class="text-3xl md:text-5xl font-bold leading-tight mb-4">{{ $post->title }}</h1>

            <!-- Excerpt -->
            @if ($post->excerpt)
                <p class="text-xl text-gray-700 leading-relaxed mb-6">{{ $post->excerpt }}</p>
            @endif

            <!-- Meta + author -->
            <div class="flex flex-wrap items-center gap-3 text-sm pb-6 mb-8 border-b border-gray-200">
                @if ($post->author)
                    <div class="flex items-center gap-3">
                        @if ($post->author->slug)
                            <a href="{{ route('author.show', $post->author->slug) }}" class="flex items-center gap-3 hover:opacity-90 transition">
                        @endif
                        @if ($post->author->avatar_url)
                            <img src="{{ $post->author->avatar_url }}" alt="{{ $post->author->name }}"
                                 class="w-11 h-11 rounded-full object-cover ring-2 ring-primary-100" loading="lazy" decoding="async">
                        @else
                            <div class="w-11 h-11 rounded-full bg-gradient-to-br from-primary-600 to-primary-800 text-white font-extrabold flex items-center justify-center ring-2 ring-primary-100">
                                {{ strtoupper(mb_substr($post->author->name, 0, 1)) }}
                            </div>
                        @endif
                        <div class="leading-tight">
                            <p class="font-semibold text-gray-900 flex items-center gap-1.5">
                                {{ $post->author->name }}
                                @if ($post->author->is_contributor)
                                    <span class="inline-flex items-center gap-0.5 text-[10px] font-bold px-1.5 py-0.5 rounded-full bg-emerald-100 text-emerald-700" title="{{ __('Community Contributor') }}"><x-svg-icon name="leaf" class="w-3 h-3" /></span>
                                @endif
                            </p>
                            @if ($post->author->role_label)
                                <p class="text-xs text-primary-700">{{ __($post->author->role_label) }}</p>
                            @endif
                        </div>
                        @if ($post->author->slug)
                            </a>
                        @endif
                    </div>

                    @if ($post->coAuthor)
                        <span class="text-gray-400 text-sm">{{ __('with') }}</span>
                        <div class="flex items-center gap-2">
                            @if ($post->coAuthor->slug)
                                <a href="{{ route('author.show', $post->coAuthor->slug) }}" class="flex items-center gap-2 hover:opacity-90 transition">
                            @endif
                            @if ($post->coAuthor->avatar_url)
                                <img src="{{ $post->coAuthor->avatar_url }}" alt="{{ $post->coAuthor->name }}"
                                     class="w-9 h-9 rounded-full object-cover ring-1 ring-gray-200" loading="lazy">
                            @else
                                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-primary-500 to-primary-700 text-white text-sm font-bold flex items-center justify-center">
                                    {{ strtoupper(mb_substr($post->coAuthor->name, 0, 1)) }}
                                </div>
                            @endif
                            <div class="leading-tight">
                                <p class="font-semibold text-gray-800 text-sm">{{ $post->coAuthor->name }}</p>
                                @if ($post->coAuthor->role_label)
                                    <p class="text-[11px] text-gray-500">{{ \Illuminate\Support\Str::limit(__($post->coAuthor->role_label), 32) }}</p>
                                @endif
                            </div>
                            @if ($post->coAuthor->slug)
                                </a>
                            @endif
                        </div>
                    @endif

                    <span class="text-gray-300">·</span>
                @endif
                <div class="text-gray-500 flex flex-wrap items-center gap-2">
                    @if ($post->published_at)
                        <time itemprop="datePublished" datetime="{{ $post->published_at->toIso8601String() }}">
                            {{ $post->published_at->translatedFormat('d M Y') }}
                        </time>
                        @if ($post->updated_at && $post->updated_at->gt($post->published_at->copy()->addDay()))
                            <span class="text-emerald-600 font-medium" title="{{ __('Last updated') }}">
                                · {{ __('Updated') }}
                                <time itemprop="dateModified" datetime="{{ $post->updated_at->toIso8601String() }}">
                                    {{ $post->updated_at->translatedFormat('d M Y') }}
                                </time>
                            </span>
                        @endif
                        <span>·</span>
                    @endif
                    <span>{{ __(':n min read', ['n' => $post->reading_minutes]) }}</span>
                    <span>·</span>
                    <span>{{ __(':n views', ['n' => number_format($post->view_count)]) }}</span>
                </div>
            </div>

            @php
                $featured = $post->featured_image
                    ? (str_starts_with($post->featured_image, 'http') ? $post->featured_image : asset('storage/' . ltrim($post->featured_image, '/')))
                    : null;
                $audio = $post->audio_url
                    ? (str_starts_with($post->audio_url, 'http') ? $post->audio_url : asset('storage/' . ltrim($post->audio_url, '/')))
                    : null;
                // YouTube/Vimeo embed URL üret
                $videoEmbed = null;
                if ($post->video_url) {
                    if (preg_match('~(?:youtu\.be/|youtube\.com/(?:watch\?v=|embed/|v/))([\w-]{11})~', $post->video_url, $m)) {
                        $videoEmbed = 'https://www.youtube-nocookie.com/embed/' . $m[1];
                    } elseif (preg_match('~vimeo\.com/(\d+)~', $post->video_url, $m)) {
                        $videoEmbed = 'https://player.vimeo.com/video/' . $m[1];
                    }
                }
            @endphp

            {{-- Featured görsel — kompakt (max-h), CLS önlemek için boyut attribute'lu --}}
            @if ($featured)
                <figure class="mb-8">
                    <img src="{{ $featured }}" alt="{{ $post->title }}"
                         width="1200" height="630"
                         class="w-full max-h-64 md:max-h-72 object-cover rounded-lg shadow-sm"
                         loading="lazy" decoding="async" fetchpriority="low">
                    @if ($post->featured_image_caption)
                        <figcaption class="text-sm text-gray-500 text-center mt-2 italic">{{ $post->featured_image_caption }}</figcaption>
                    @endif
                </figure>
            @endif

            {{-- Video embed — yoksa görünmez --}}
            {{-- AdSense banner — yazı başı --}}
            <x-ad-slot type="banner" slot="banner_top" />

            @if ($videoEmbed)
                <div class="relative w-full aspect-video mb-8 rounded-lg overflow-hidden shadow-sm bg-black">
                    <iframe src="{{ $videoEmbed }}"
                            class="absolute inset-0 w-full h-full"
                            frameborder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen
                            loading="lazy"></iframe>
                </div>
            @endif

            {{-- Audio / Podcast — yoksa görünmez --}}
            @if ($audio)
                <div class="bg-gradient-to-br from-primary-50 to-accent-50 border border-primary-100 rounded-lg p-5 mb-8">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-11 h-11 bg-primary-600 text-white rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.383 3.076A1 1 0 0110 4v12a1 1 0 01-1.617.786L4.21 13H2a1 1 0 01-1-1V8a1 1 0 011-1h2.21l4.173-3.786a1 1 0 011-.138z"/><path d="M14.657 2.929a1 1 0 011.414 0A9.972 9.972 0 0119 10a9.972 9.972 0 01-2.929 7.071 1 1 0 11-1.414-1.414A7.971 7.971 0 0017 10c0-2.21-.894-4.208-2.343-5.657a1 1 0 010-1.414zM12.95 5.05a1 1 0 011.414 0A5.983 5.983 0 0116 10a5.983 5.983 0 01-1.636 4.95 1 1 0 11-1.414-1.414A3.987 3.987 0 0014 10a3.987 3.987 0 00-1.05-2.536 1 1 0 010-1.414z"/></svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900 inline-flex items-center gap-1.5"><x-svg-icon name="play" class="w-4 h-4 text-primary-600" /> {{ __('Audio Narration (Podcast)') }}</p>
                            <p class="text-xs text-gray-600">{{ __('You can also follow this article by listening') }}
                                @if ($post->audio_duration_seconds)
                                    — <span class="font-mono">{{ $post->formatted_audio_duration }}</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    <audio controls preload="metadata" class="w-full">
                        <source src="{{ $audio }}" type="audio/mpeg">
                        {{ __('Your browser does not support audio playback.') }}
                    </audio>
                </div>
            @endif

            {{-- Server-rendered TOC (visible to AIO/Bing crawlers without JS) — shown when ≥2 H2s --}}
            @if (count($toc) >= 2)
                <nav id="post-toc-ssr" class="bg-gray-50 border border-gray-200 rounded-xl p-5 mb-8" aria-label="{{ __('Table of Contents') }}">
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-3 inline-flex items-center gap-1.5"><x-svg-icon name="list-bullet" class="w-3.5 h-3.5" /> {{ __('In this article') }}</p>
                    <ol class="space-y-1.5 text-sm">
                        @foreach ($toc as $i => $entry)
                            <li class="flex items-start gap-2">
                                <span class="flex-shrink-0 w-5 h-5 rounded-full bg-primary-100 text-primary-700 text-[11px] font-bold flex items-center justify-center mt-0.5">{{ $i + 1 }}</span>
                                <a href="#{{ $entry['id'] }}" class="text-gray-800 hover:text-primary-700 font-medium">{{ $entry['text'] }}</a>
                            </li>
                        @endforeach
                    </ol>
                </nav>
            @endif

            <!-- Body -->
            <div id="post-content" class="blog-content prose-img:rounded-lg prose-img:shadow-sm prose-img:my-6 prose-img:w-full">
                {!! $contentHtml !!}
            </div>

            {{-- İçerik-uyumlu affiliate kart — slug'a göre context seç --}}
            @php
                $context = 'default';
                $slug = $post->slug ?? '';
                if (str_contains($slug, 'vize') || str_contains($slug, 'visa')) $context = 'visa';
                elseif (str_contains($slug, 'sperrkonto') || str_contains($slug, 'ucretsiz')) $context = 'sperrkonto';
                elseif (str_contains($slug, 'sigorta') || str_contains($slug, 'insurance')) $context = 'insurance';
            @endphp
            <x-ad-slot type="affiliate-card" :context="$context" />

            {{-- Galeri — yoksa görünmez --}}
            @if (! empty($post->gallery_images))
                <section class="mt-10 pt-6 border-t border-gray-200">
                    <h2 class="text-sm font-semibold uppercase tracking-wide text-gray-500 mb-4">{{ __('Gallery') }}</h2>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        @foreach ($post->gallery_images as $img)
                            @php
                                $src = str_starts_with($img, 'http') ? $img : asset('storage/' . ltrim($img, '/'));
                            @endphp
                            <a href="{{ $src }}" target="_blank" rel="noopener" class="block group">
                                <img src="{{ $src }}" alt="" loading="lazy"
                                     class="w-full aspect-[4/3] object-cover rounded-lg shadow-sm group-hover:shadow-md group-hover:scale-[1.02] transition">
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif

            {{-- Yardımcı oldu mu? feedback (localStorage ile çift oy engellenir) --}}
            <div x-data="postFeedback({{ $post->id }})" class="mt-10 pt-6 border-t border-gray-200">
                <div x-show="!voted" class="flex items-center gap-4 flex-wrap">
                    <p class="text-sm font-semibold text-gray-700">{{ __('Was this guide helpful?') }}</p>
                    <div class="flex gap-2">
                        <button @click="vote('up')" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg border border-gray-200 hover:border-green-400 hover:bg-green-50 text-gray-700 hover:text-green-700 text-sm font-medium transition">
                            <x-svg-icon name="check-circle" class="w-4 h-4" /> {{ __('Yes') }}
                        </button>
                        <button @click="vote('down')" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg border border-gray-200 hover:border-red-400 hover:bg-red-50 text-gray-700 hover:text-red-700 text-sm font-medium transition">
                            <x-svg-icon name="x-circle" class="w-4 h-4" /> {{ __('No') }}
                        </button>
                    </div>
                </div>
                <div x-show="voted" x-cloak class="bg-gradient-to-br from-primary-50 to-accent-50 border border-primary-100 rounded-lg p-4">
                    <p class="text-sm font-medium text-gray-800" x-text="thanks"></p>
                    <p x-show="voted === 'down'" x-cloak class="text-xs text-gray-600 mt-1">{!! __('Let us know what was missing via the <strong>feedback widget</strong> at the bottom right, and we will update quickly.') !!}</p>
                </div>
            </div>

            <!-- Share -->
            <div class="mt-8 pt-6 border-t border-gray-200">
                <p class="text-sm font-semibold text-gray-600 mb-3">{{ __('Share this article') }}</p>
                <div class="flex gap-2 flex-wrap">
                    <a href="https://twitter.com/intent/tweet?text={{ urlencode($post->title) }}&url={{ urlencode(request()->fullUrl()) }}"
                       target="_blank" rel="noopener"
                       class="inline-flex items-center gap-1.5 bg-gray-100 hover:bg-gray-900 hover:text-white text-gray-700 px-4 py-2 rounded-lg font-semibold text-sm transition">
                        𝕏 Twitter
                    </a>
                    <a href="https://wa.me/?text={{ urlencode($post->title . ' ' . request()->fullUrl()) }}"
                       target="_blank" rel="noopener"
                       class="inline-flex items-center gap-1.5 bg-gray-100 hover:bg-green-500 hover:text-white text-gray-700 px-4 py-2 rounded-lg font-semibold text-sm transition">
                        <x-svg-icon name="chat-bubble" class="w-4 h-4" /> WhatsApp
                    </a>
                    <a href="https://t.me/share/url?url={{ urlencode(request()->fullUrl()) }}&text={{ urlencode($post->title) }}"
                       target="_blank" rel="noopener"
                       class="inline-flex items-center gap-1.5 bg-gray-100 hover:bg-blue-500 hover:text-white text-gray-700 px-4 py-2 rounded-lg font-semibold text-sm transition">
                        <x-svg-icon name="plane" class="w-4 h-4" /> Telegram
                    </a>
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->fullUrl()) }}"
                       target="_blank" rel="noopener"
                       class="inline-flex items-center gap-1.5 bg-gray-100 hover:bg-blue-700 hover:text-white text-gray-700 px-4 py-2 rounded-lg font-semibold text-sm transition">
                        f Facebook
                    </a>
                    <button onclick="copyPostLink(this)"
                       class="inline-flex items-center gap-1.5 bg-gray-100 hover:bg-primary-600 hover:text-white text-gray-700 px-4 py-2 rounded-lg font-semibold text-sm transition">
                        <x-svg-icon name="link" class="w-4 h-4" /> <span data-label>{{ __('Copy link') }}</span>
                    </button>
                </div>
            </div>

            <!-- Yazar hakkında -->
            @if ($post->author && ($post->author->bio || $post->author->role_label))
                <section class="mt-12 pt-8 border-t border-gray-200">
                    <h2 class="text-sm font-semibold uppercase tracking-wide text-gray-500 mb-4">{{ __('About the Author') }}</h2>
                    <div class="bg-gradient-to-br from-primary-50 to-white border border-primary-100 rounded-xl p-6 flex flex-col sm:flex-row gap-5">
                        <div class="flex-shrink-0">
                            @if ($post->author->avatar_url)
                                <img src="{{ $post->author->avatar_url }}" alt="{{ $post->author->name }}"
                                     class="w-20 h-20 rounded-full object-cover ring-4 ring-white shadow" loading="lazy" decoding="async">
                            @else
                                <div class="w-20 h-20 rounded-full bg-gradient-to-br from-primary-600 to-primary-800 text-white text-3xl font-extrabold flex items-center justify-center ring-4 ring-white shadow">
                                    {{ strtoupper(mb_substr($post->author->name, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                        <div class="flex-1">
                            <p class="font-bold text-lg text-gray-900 leading-tight">{{ $post->author->name }}</p>
                            @if ($post->author->role_label)
                                <p class="text-sm text-primary-700 font-semibold mb-2">{{ __($post->author->role_label) }}</p>
                            @endif
                            @if ($post->author->bio)
                                <p class="text-sm text-gray-700 leading-relaxed">{{ $post->author->bio }}</p>
                            @endif
                            @if (! empty($post->author->social_links))
                                <div class="flex flex-wrap gap-2 mt-3">
                                    @foreach ($post->author->social_links as $type => $value)
                                        @php
                                            $url = match ($type) {
                                                'email'    => 'mailto:' . $value,
                                                'twitter'  => 'https://twitter.com/' . ltrim($value, '@'),
                                                'linkedin' => 'https://linkedin.com/in/' . $value,
                                                'github'   => 'https://github.com/' . $value,
                                                default    => $value,
                                            };
                                            $socialIcon = match ($type) {
                                                'email' => 'envelope',
                                                'linkedin' => 'briefcase',
                                                'github' => 'computer',
                                                default => null,
                                            };
                                            $isTwitter = $type === 'twitter';
                                            $label = match ($type) {
                                                'email' => $value,
                                                'twitter' => '@' . ltrim($value, '@'),
                                                'linkedin' => 'LinkedIn',
                                                'github' => 'GitHub',
                                                default => $type,
                                            };
                                        @endphp
                                        <a href="{{ $url }}" target="_blank" rel="noopener"
                                           class="inline-flex items-center gap-1 text-xs bg-white border border-gray-200 hover:border-primary-300 text-gray-700 hover:text-primary-700 px-3 py-1.5 rounded-md transition">
                                            @if ($isTwitter)
                                                <span>𝕏</span>
                                            @elseif ($socialIcon)
                                                <x-svg-icon :name="$socialIcon" class="w-3.5 h-3.5" />
                                            @endif
                                            {{ $label }}
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </section>
            @endif

            <!-- Newsletter (yazı sonu CTA) -->
            <section id="newsletter-signup" class="mt-12 pt-6 border-t border-gray-200 scroll-mt-24">
                <x-newsletter-form
                    source="blog_post_{{ $post->id }}"
                    variant="card"
                    :heading="__('Do not miss this content')"
                    :subheading="__('Weekly similar guides + current deadlines + scholarship announcements. Unsubscribe anytime.')" />
            </section>

            <!-- Related -->
            @if ($related->isNotEmpty())
                <section class="mt-12 pt-8 border-t border-gray-200">
                    <h2 class="text-2xl font-bold mb-6">{{ __('Related Articles') }}</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @foreach ($related as $r)
                            <a href="{{ route('blog.show', $r->slug) }}"
                               class="block bg-white border border-gray-200 hover:border-primary-500 hover:shadow-md transition rounded-lg p-5">
                                <h3 class="font-bold leading-tight mb-2 text-gray-900">{{ $r->title }}</h3>
                                @if ($r->excerpt)
                                    <p class="text-sm text-gray-600 line-clamp-3">{{ \Illuminate\Support\Str::limit($r->excerpt, 100) }}</p>
                                @endif
                                <p class="text-xs text-gray-500 mt-3">{{ __(':n min read', ['n' => $r->reading_minutes]) }}</p>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif
        </div>

        <!-- Sidebar: Kategoriler (üstte) + İçindekiler (altta, collapsible) — tek sticky blok -->
        <div class="lg:col-span-1">
            <div class="lg:sticky lg:top-24 space-y-4">
                {{-- 1) KATEGORİLER --}}
                @include('blog._sidebar', ['active_category' => $post->category])

                {{-- 2) İÇİNDEKİLER — default kapalı, scroll'da otomatik açılır --}}
                <nav id="post-toc" class="hidden lg:block bg-white border border-gray-200 rounded-xl overflow-hidden">
                    <button type="button" id="post-toc-toggle"
                            class="w-full flex items-center justify-between px-4 py-3 hover:bg-gray-50 transition">
                        <span class="text-xs font-semibold uppercase tracking-wider text-gray-500 inline-flex items-center gap-1.5"><x-svg-icon name="list-bullet" class="w-3.5 h-3.5" /> {{ __('Table of Contents') }}</span>
                        <svg id="post-toc-chevron" class="w-4 h-4 text-gray-400 transition-transform" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/>
                        </svg>
                    </button>
                    <ol id="post-toc-list" class="hidden space-y-0.5 text-sm max-h-[calc(100vh-22rem)] overflow-y-auto px-4 pb-4 pt-1"></ol>
                </nav>
            </div>
        </div>
    </div>
</article>

@push('scripts')
<script>
// 1) Reading progress bar — scroll yüzdesine göre genişler
(function() {
    const bar = document.getElementById('reading-progress-bar');
    const content = document.getElementById('post-content');
    if (!bar || !content) return;
    function update() {
        const top = content.offsetTop;
        const height = content.offsetHeight;
        const scrolled = window.scrollY - top;
        const visible = window.innerHeight;
        const pct = Math.max(0, Math.min(100, (scrolled + visible) / height * 100));
        bar.style.width = pct + '%';
    }
    window.addEventListener('scroll', update, { passive: true });
    window.addEventListener('resize', update);
    update();
})();

// 2) TOC — content içindeki H2 ve H3'leri tarar, sidebar'a doldurur, scrollspy ekler
(function() {
    const content = document.getElementById('post-content');
    const tocList = document.getElementById('post-toc-list');
    const tocWrap = document.getElementById('post-toc');
    if (!content || !tocList || !tocWrap) return;

    // Sadece H2 — temiz, kısa içindekiler (H3'ler çok kalabalık yapıyordu)
    const headings = content.querySelectorAll('h2');
    if (headings.length < 2) { tocWrap.classList.add('hidden'); return; }

    headings.forEach((h, i) => {
        if (!h.id) h.id = 'toc-h-' + i;
        const li = document.createElement('li');
        li.className = 'flex items-start gap-2';
        const num = document.createElement('span');
        num.textContent = (i + 1);
        num.className = 'flex-shrink-0 w-5 h-5 rounded-full bg-gray-100 text-gray-500 text-[11px] font-bold flex items-center justify-center mt-1.5';
        const a = document.createElement('a');
        a.href = '#' + h.id;
        a.textContent = h.textContent.trim();
        a.className = 'flex-1 text-sm text-gray-700 hover:text-primary-600 font-medium block py-1 border-l-2 border-transparent pl-2 transition leading-snug';
        a.dataset.target = h.id;
        li.appendChild(num);
        li.appendChild(a);
        tocList.appendChild(li);
    });

    // ScrollSpy — viewport'a giren H2'yi aktif eder (link + numara)
    const links = tocList.querySelectorAll('a');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(e => {
            if (e.isIntersecting) {
                links.forEach(l => {
                    const num = l.previousElementSibling;
                    if (l.dataset.target === e.target.id) {
                        l.classList.add('text-primary-700', 'border-primary-500', 'bg-primary-50');
                        if (num) { num.classList.remove('bg-gray-100', 'text-gray-500'); num.classList.add('bg-primary-600', 'text-white'); }
                    } else {
                        l.classList.remove('text-primary-700', 'border-primary-500', 'bg-primary-50');
                        if (num) { num.classList.add('bg-gray-100', 'text-gray-500'); num.classList.remove('bg-primary-600', 'text-white'); }
                    }
                });
            }
        });
    }, { rootMargin: '-80px 0px -70% 0px' });
    headings.forEach(h => observer.observe(h));

    // Collapse/expand: default kapalı; scroll'da otomatik açılır; manuel toggle
    const toggle = document.getElementById('post-toc-toggle');
    const chevron = document.getElementById('post-toc-chevron');
    let userToggled = false;

    function setOpen(open) {
        tocList.classList.toggle('hidden', !open);
        if (chevron) chevron.classList.toggle('rotate-180', open);
    }
    setOpen(false); // default kapalı

    toggle?.addEventListener('click', () => {
        userToggled = true;
        setOpen(tocList.classList.contains('hidden'));
    });

    // Sayfa içeriğine girince (scroll > 350px) otomatik aç — kullanıcı elle kapatmadıysa
    let autoOpened = false;
    window.addEventListener('scroll', () => {
        if (userToggled) return;
        if (!autoOpened && window.scrollY > 350) { setOpen(true); autoOpened = true; }
        else if (autoOpened && window.scrollY <= 100) { setOpen(false); autoOpened = false; }
    }, { passive: true });
})();

// 3) Copy link
function copyPostLink(btn) {
    const url = window.location.href;
    navigator.clipboard.writeText(url).then(() => {
        const label = btn.querySelector('[data-label]');
        if (label) {
            const old = label.textContent;
            label.textContent = '{{ __('Copied!') }}';
            setTimeout(() => label.textContent = old, 1500);
        }
    });
}

// 4) Feedback widget (Alpine)
document.addEventListener('alpine:init', () => {
    window.Alpine && Alpine.data('postFeedback', (postId) => ({
        voted: localStorage.getItem('post-feedback-' + postId) || null,
        thanks: '',
        vote(type) {
            this.voted = type;
            localStorage.setItem('post-feedback-' + postId, type);
            this.thanks = type === 'up' ? '{{ __('Thanks for your feedback!') }}' : '{{ __('Sorry. We will review your feedback.') }}';

            // POST analytics (varsa endpoint'e)
            try {
                fetch('/api/blog-feedback', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || ''},
                    body: JSON.stringify({ post_id: postId, vote: type })
                });
            } catch(e) {}
        }
    }));
});

// 5) Okuma derinliği (scroll %) + sayfada kalış süresi → beacon ile gönder
(function() {
    const content = document.getElementById('post-content');
    const postId = {{ $post->id }};
    if (!content) return;

    const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
    const start = Date.now();
    let maxScroll = 0;
    let lastSent = { scroll: 0, seconds: 0 };

    function calcScroll() {
        const el = content;
        const top = el.offsetTop;
        const h = el.offsetHeight;
        const scrolled = window.scrollY + window.innerHeight - top;
        const pct = Math.max(0, Math.min(100, Math.round(scrolled / h * 100)));
        if (pct > maxScroll) maxScroll = pct;
    }

    function send() {
        const seconds = Math.round((Date.now() - start) / 1000);
        // Anlamlı değişiklik yoksa gönderme (gereksiz istek önleme)
        if (maxScroll <= lastSent.scroll && seconds - lastSent.seconds < 10) return;
        lastSent = { scroll: maxScroll, seconds: seconds };
        const payload = JSON.stringify({ post_id: postId, scroll: maxScroll, seconds: seconds });
        // sendBeacon: sayfa kapanırken bile gider
        if (navigator.sendBeacon) {
            navigator.sendBeacon('{{ route('blog.engagement') }}', new Blob([payload], { type: 'application/json' }));
        } else {
            fetch('{{ route('blog.engagement') }}', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf }, body: payload, keepalive: true }).catch(() => {});
        }
    }

    window.addEventListener('scroll', calcScroll, { passive: true });
    calcScroll();
    // Periyodik (30sn) + sayfa gizlenince/kapanınca gönder
    const timer = setInterval(send, 30000);
    document.addEventListener('visibilitychange', () => { if (document.visibilityState === 'hidden') send(); });
    window.addEventListener('pagehide', send);
    window.addEventListener('beforeunload', send);
})();
</script>
@endpush

{{-- ============================================================
     YORUMLAR — UGC + E-E-A-T sinyali
     ============================================================ --}}
<section id="comments" class="bg-gray-50 border-t border-gray-200 mt-12">
    <div class="max-w-3xl mx-auto px-4 py-12">
        <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2 flex items-center gap-2">
            <x-svg-icon name="chat-bubble" class="w-7 h-7 text-indigo-600" /> {{ __('Comments') }}
            @if ($post->approvedComments->count() > 0)
                <span class="text-base font-normal text-gray-500">({{ $post->approvedComments->count() }})</span>
            @endif
        </h2>
        <p class="text-sm text-gray-600 mb-6">{{ __('Share your experience or ask a question. Comments are reviewed before publishing.') }}</p>

        {{-- Status flash (after submit) --}}
        @if (session('comment_status'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg p-4 mb-6 text-sm">
                ✓ {{ session('comment_status') }}
            </div>
        @endif

        {{-- Comment form --}}
        <form method="POST" action="{{ route('blog.comment.store', $post->slug) }}" class="bg-white border border-gray-200 rounded-xl p-5 mb-8">
            @csrf
            <textarea name="body" required minlength="10" maxlength="3000" rows="4"
                placeholder="{{ __('Your comment...') }}"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-sm">{{ old('body') }}</textarea>

            @guest
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-3">
                    <input type="text" name="author_name" maxlength="80" required value="{{ old('author_name') }}"
                        placeholder="{{ __('Name (required)') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:border-indigo-500 text-sm">
                    <input type="email" name="author_email" maxlength="150" required value="{{ old('author_email') }}"
                        placeholder="{{ __('Email (not published, required)') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:border-indigo-500 text-sm">
                </div>
                <p class="text-[11px] text-gray-400 mt-2">{{ __('Your email is never shown publicly — only used to notify you if your comment receives a reply.') }}</p>
            @endguest

            {{-- Honeypot (gizli, bot dolduracak) --}}
            <input type="text" name="website" tabindex="-1" autocomplete="off"
                style="position:absolute;left:-9999px;opacity:0" aria-hidden="true">

            @error('body')
                <p class="text-xs text-red-600 mt-2">{{ $message }}</p>
            @enderror
            @error('author_name')
                <p class="text-xs text-red-600 mt-2">{{ $message }}</p>
            @enderror
            @error('author_email')
                <p class="text-xs text-red-600 mt-2">{{ $message }}</p>
            @enderror

            <div class="flex items-center justify-between mt-4">
                <p class="text-xs text-gray-500">{{ __('Be respectful — spam/abusive comments are removed.') }}</p>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-5 py-2 rounded-lg text-sm transition">
                    {{ __('Post comment') }} →
                </button>
            </div>
        </form>

        {{-- Comments list --}}
        @if ($post->approvedComments->isNotEmpty())
            <div class="space-y-5">
                @foreach ($post->approvedComments as $comment)
                    <article id="comment-{{ $comment->id }}" class="bg-white border border-gray-200 rounded-xl p-4">
                        <div class="flex items-start gap-3">
                            @if ($comment->display_avatar)
                                <img src="{{ $comment->display_avatar }}" alt="{{ $comment->display_name }}"
                                    class="w-10 h-10 rounded-full object-cover flex-shrink-0">
                            @else
                                <div class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-sm flex-shrink-0">
                                    {{ mb_substr($comment->display_name, 0, 1) }}
                                </div>
                            @endif
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap mb-1">
                                    @if ($comment->user?->slug)
                                        <a href="{{ route('author.show', $comment->user->slug) }}"
                                           class="font-semibold text-sm text-gray-900 hover:text-indigo-600">{{ $comment->display_name }}</a>
                                    @else
                                        <span class="font-semibold text-sm text-gray-900">{{ $comment->display_name }}</span>
                                    @endif
                                    @if ($comment->is_pinned)
                                        <span class="inline-flex items-center gap-1 text-[10px] uppercase font-bold tracking-wider text-amber-600 bg-amber-50 px-2 py-0.5 rounded"><x-svg-icon name="flag" class="w-3 h-3" /> {{ __('Pinned') }}</span>
                                    @endif
                                    <time class="text-xs text-gray-400" datetime="{{ $comment->created_at->toIso8601String() }}">
                                        {{ $comment->created_at->diffForHumans() }}
                                    </time>
                                </div>
                                <div class="prose prose-sm max-w-none text-gray-700 whitespace-pre-line leading-relaxed">{{ $comment->body }}</div>

                                {{-- Replies --}}
                                @if ($comment->replies->isNotEmpty())
                                    <div class="mt-4 pl-4 border-l-2 border-gray-100 space-y-3">
                                        @foreach ($comment->replies as $reply)
                                            <div id="comment-{{ $reply->id }}" class="flex items-start gap-2">
                                                @if ($reply->display_avatar)
                                                    <img src="{{ $reply->display_avatar }}" alt="{{ $reply->display_name }}" class="w-7 h-7 rounded-full object-cover flex-shrink-0">
                                                @else
                                                    <div class="w-7 h-7 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-[11px] flex-shrink-0">
                                                        {{ mb_substr($reply->display_name, 0, 1) }}
                                                    </div>
                                                @endif
                                                <div class="flex-1">
                                                    <div class="flex items-center gap-2 mb-0.5">
                                                        <span class="font-semibold text-xs text-gray-900">{{ $reply->display_name }}</span>
                                                        <time class="text-[11px] text-gray-400">{{ $reply->created_at->diffForHumans() }}</time>
                                                    </div>
                                                    <p class="text-xs text-gray-700 whitespace-pre-line leading-relaxed">{{ $reply->body }}</p>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        @else
            <div class="text-center py-8 text-sm text-gray-500">
                {{ __('No comments yet. Be the first to share your experience!') }}
            </div>
        @endif
    </div>
</section>

{{-- Schema.org Comment + commentCount --}}
@if ($post->approvedComments->count() > 0)
    @push('head')
    <script type="application/ld+json">{!! json_encode([
        '@context'      => 'https://schema.org',
        '@type'         => 'DiscussionForumPosting',
        '@id'           => url()->current() . '#comments',
        'commentCount'  => $post->approvedComments->count(),
        'comment'       => $post->approvedComments->take(20)->map(fn ($c) => array_filter([
            '@type'        => 'Comment',
            'text'         => \Illuminate\Support\Str::limit($c->body, 500),
            'dateCreated'  => $c->created_at->toIso8601String(),
            'author'       => array_filter([
                '@type' => 'Person',
                'name'  => $c->display_name,
                'url'   => $c->user?->slug ? route('author.show', $c->user->slug) : null,
            ]),
        ]))->all(),
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
    @endpush
@endif

@endsection
