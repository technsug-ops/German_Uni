@extends('layouts.app')

@php
    $description = \App\Support\Seo::descriptionFromBlocks(
        $state->content_blocks,
        __(':n cities, :u universities in :state state of Germany. State guide for international students.', ['n' => $totals['cities_with_unis'], 'u' => $totals['unis'], 'state' => $state->name])
    );
@endphp

<x-seo
    :title="$state->name . ' (' . $state->name_tr . ') — ' . __('State Guide')"
    :description="$description"
    :image="$state->image_url"
/>

<x-json-ld :data="\App\Support\Seo::breadcrumbs([
    ['name' => __('Home'), 'url' => route('home')],
    ['name' => __('States'), 'url' => route('states.index')],
    ['name' => $state->name, 'url' => route('states.show', $state->slug)],
])" />

@php $faqSchema = \App\Support\Seo::faqPageFromBlocks($state->content_blocks); @endphp
@if ($faqSchema)
    <x-json-ld :data="$faqSchema" />
@endif

@section('title', $state->name . ' ' . __('State — For International Students') . '  — ' . brand('name'))
@section('meta_description', $description)

@section('content')

{{-- HERO --}}
<section class="relative bg-gradient-to-br from-primary-700 via-primary-600 to-accent-500 text-white overflow-hidden">
    @if($state->image_url)
        <img src="{{ $state->image_url }}" alt="{{ $state->name }}"
             class="absolute inset-0 w-full h-full object-cover opacity-30" loading="eager" fetchpriority="high"/>
        <div class="absolute inset-0 bg-gradient-to-t from-primary-900/80 via-primary-800/50 to-transparent"></div>
    @endif
    <div class="relative max-w-[1400px] mx-auto px-4 py-10 md:py-14">
        <nav class="text-sm text-primary-100 mb-3">
            <a href="/" class="hover:text-white">{{ __('Home') }}</a>
            <span class="mx-2 opacity-60">›</span>
            <a href="{{ route('states.index') }}" class="hover:text-white">{{ __('States') }}</a>
            <span class="mx-2 opacity-60">›</span>
            <span class="text-white">{{ $state->name }}</span>
        </nav>
        <h1 class="text-4xl md:text-5xl font-extrabold mb-2 leading-tight drop-shadow">{{ $state->name }}</h1>
        @if($state->name !== $state->getAttributes()['name_de'])
            <p class="text-primary-100 text-lg mb-3 opacity-80">{{ $state->getAttributes()['name_de'] }}</p>
        @endif
        <div class="flex flex-wrap gap-2 text-sm mt-3">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white/15 backdrop-blur ring-1 ring-white/25">
                <x-svg-icon name="building-office" class="w-4 h-4" />
                {{ __(':n university cities', ['n' => $totals['cities_with_unis']]) }}
            </span>
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white/15 backdrop-blur ring-1 ring-white/25">
                <x-svg-icon name="academic-cap" class="w-4 h-4" />
                {{ __(':n universities', ['n' => $totals['unis']]) }}
            </span>
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white/15 backdrop-blur ring-1 ring-white/25">
                <x-svg-icon name="check-circle" class="w-4 h-4" />
                {{ __(':p public · :v private', ['p' => $totals['public'], 'v' => $totals['private']]) }}
            </span>
            @if($state->capital)
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white/15 backdrop-blur ring-1 ring-white/25">
                    <x-svg-icon name="map-pin" class="w-4 h-4" />
                    {{ __('Capital:') }} {{ $state->capital }}
                </span>
            @endif
            @if (! empty($regionLabel))
                <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full bg-white/15 backdrop-blur ring-1 ring-white/25">
                    {{ $regionLabel[0] }} {{ $regionLabel[1] }}
                </span>
            @endif
            @if ($state->population)
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white/15 backdrop-blur ring-1 ring-white/25">
                    <x-svg-icon name="users" class="w-4 h-4" />
                    {{ __(':n population', ['n' => number_format($state->population, 0, ',', '.')]) }}
                </span>
            @endif
        </div>
    </div>
</section>

{{-- CONTENT --}}
<section class="bg-gray-50 py-10">
    <div class="max-w-[1400px] mx-auto px-4">

        @if(!empty($state->content_blocks) && app()->getLocale() === 'tr')
            <x-content-blocks :blocks="$state->content_blocks" />
        @else
            <div class="bg-white rounded-2xl border border-gray-200 p-8 text-center shadow-sm">
                <div class="flex justify-center mb-3 text-gray-400"><x-svg-icon name="document-text" class="w-12 h-12" /></div>
                <h2 class="text-xl font-bold text-gray-900 mb-2">{{ __('Detailed content coming soon') }}</h2>
                <p class="text-gray-600 max-w-lg mx-auto">{{ __('A rich guide for this state is being prepared.') }}</p>
            </div>
        @endif

        {{-- ŞEHİRLER --}}
        @if($cities->isNotEmpty())
            <section class="mt-10">
                <div class="flex items-baseline justify-between mb-5 flex-wrap gap-2">
                    <h2 class="text-2xl font-bold text-gray-900 inline-flex items-center gap-2"><x-svg-icon name="building-office" class="w-6 h-6" /> {{ __('Cities in :state', ['state' => $state->name]) }}</h2>
                    <a href="{{ route('cities.index', ['state' => $state->slug]) }}" class="text-sm text-primary-600 hover:underline">{{ __('All') }} →</a>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                    @foreach ($cities as $c)
                        <a href="{{ route('cities.show', $c->slug) }}"
                           class="group bg-white rounded-xl overflow-hidden border border-gray-200 hover:border-primary-500 hover:shadow-md transition flex flex-col">
                            <div class="aspect-[4/3] overflow-hidden bg-gray-100 relative">
                                @if($c->image_url)
                                    <img src="{{ $c->image_url }}" alt="{{ $c->name_de }}" loading="lazy"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-blue-500 to-cyan-400 flex items-center justify-center">
                                        <span class="text-4xl font-extrabold text-white/90">{{ mb_substr($c->name_de, 0, 1) }}</span>
                                    </div>
                                @endif
                                <span class="absolute bottom-2 right-2 px-2 py-0.5 rounded-full bg-black/60 backdrop-blur text-white text-xs font-semibold">
                                    {{ __(':n unis', ['n' => $c->universities_count]) }}
                                </span>
                            </div>
                            <div class="p-3">
                                <h3 class="font-bold text-gray-900 group-hover:text-primary-600 transition truncate">{{ $c->name_de }}</h3>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- TOP ÜNİLER --}}
        @if($topUnis->isNotEmpty())
            <section class="mt-10">
                <div class="flex items-baseline justify-between mb-5 flex-wrap gap-2">
                    <h2 class="text-2xl font-bold text-gray-900 inline-flex items-center gap-2"><x-svg-icon name="academic-cap" class="w-6 h-6" /> {{ __('Featured universities') }}</h2>
                    <a href="{{ route('universities.index', ['state' => $state->slug]) }}" class="text-sm text-primary-600 hover:underline">{{ __('All') }} →</a>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach ($topUnis as $u)
                        <a href="{{ route('universities.show', $u->slug) }}"
                           class="group flex gap-3 bg-white rounded-lg border border-gray-200 hover:border-primary-500 hover:shadow-md transition p-3">
                            @if($u->image_url)
                                <div class="w-20 h-20 rounded-lg overflow-hidden bg-gray-100 shrink-0">
                                    <img src="{{ $u->image_url }}" alt="" class="w-full h-full object-cover" loading="lazy" decoding="async">
                                </div>
                            @elseif($u->logo_url)
                                <div class="w-20 h-20 bg-white rounded-lg ring-1 ring-gray-200 p-2 flex items-center justify-center shrink-0">
                                    <img src="{{ $u->logo_url }}" alt="" class="max-w-full max-h-full object-contain" loading="lazy" decoding="async"/>
                                </div>
                            @else
                                <div class="w-20 h-20 rounded-lg bg-gradient-to-br from-primary-500 to-accent-500 flex items-center justify-center shrink-0">
                                    <span class="text-2xl font-extrabold text-white">{{ mb_substr($u->name_de, 0, 2) }}</span>
                                </div>
                            @endif
                            <div class="flex-1 min-w-0">
                                <h3 class="font-bold text-gray-900 group-hover:text-primary-600 leading-snug line-clamp-2">{{ $u->display_name }}</h3>
                                <p class="text-xs text-gray-500 mt-1 inline-flex items-center gap-1"><x-svg-icon name="map-pin" class="w-3.5 h-3.5" /> {{ $u->city?->name ?? '—' }}</p>
                                @if($u->student_count)
                                    <p class="text-xs text-accent-600 font-semibold mt-1">{{ __(':n students', ['n' => number_format($u->student_count)]) }}</p>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- TOP ALANLAR --}}
        @if (! empty($topFields) && $topFields->isNotEmpty())
            <section class="mt-10">
                <div class="flex items-baseline justify-between mb-4 flex-wrap gap-2">
                    <h2 class="text-2xl font-bold text-gray-900 inline-flex items-center gap-2"><x-svg-icon name="target" class="w-6 h-6" /> {{ __('Strong fields in :state', ['state' => $state->name]) }}</h2>
                    <a href="{{ route('fields.index') }}" class="text-sm text-primary-600 hover:underline">{{ __('All fields') }} →</a>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                    @foreach ($topFields as $field)
                        <a href="{{ route('fields.show', $field->slug) }}"
                           class="group bg-white rounded-xl border border-gray-200 hover:border-primary-500 hover:shadow-md transition p-4 text-center">
                            <div class="mb-1 flex justify-center" style="color: {{ $field->color ?? '#1e40af' }};">{!! e_icon($field->icon, 'w-10 h-10') !!}</div>
                            <h3 class="font-bold text-sm text-gray-900 group-hover:text-primary-600 leading-tight">{{ $field->name }}</h3>
                            <p class="text-xs text-gray-500 mt-1">{{ __(':n programs', ['n' => $field->programs_count]) }}</p>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- İLGİLİ BLOG YAZILARI --}}
        @if (! empty($relatedPosts) && $relatedPosts->count() > 0)
            <section class="mt-10">
                <div class="flex items-baseline justify-between mb-4 flex-wrap gap-2">
                    <h2 class="text-2xl font-bold text-gray-900 inline-flex items-center gap-2"><x-svg-icon name="document-text" class="w-6 h-6" /> {{ __('Articles related to :state', ['state' => $state->name]) }}</h2>
                    <a href="{{ route('blog.index') }}" class="text-sm text-primary-600 hover:underline">{{ __('Blog') }} →</a>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach ($relatedPosts as $post)
                        <a href="{{ route('blog.show', $post->slug) }}"
                           class="group block bg-white rounded-xl border border-gray-200 hover:border-primary-400 hover:shadow-md transition p-5">
                            @if ($post->category)
                                <p class="text-xs font-semibold uppercase tracking-wide mb-2"
                                   style="color: {{ $post->category->color ?? '#1E40AF' }}">
                                    {{ __($post->category->name) }}
                                </p>
                            @endif
                            <h3 class="font-bold text-gray-900 group-hover:text-primary-600 leading-tight mb-2 line-clamp-2">{{ $post->title }}</h3>
                            @if ($post->excerpt)
                                <p class="text-sm text-gray-600 line-clamp-2 mb-3">{{ \Illuminate\Support\Str::limit($post->excerpt, 100) }}</p>
                            @endif
                            <p class="text-xs text-gray-500">{{ __(':n min read', ['n' => $post->reading_minutes]) }}</p>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

    </div>
</section>

{{-- ════════ KOMŞU EYALETLER ════════ --}}
@if (!empty($otherStates) && $otherStates->isNotEmpty())
<section class="bg-white border-t border-gray-200 py-10">
    <div class="max-w-[1400px] mx-auto px-4">
        <div class="flex items-baseline justify-between mb-5 flex-wrap gap-2">
            <h2 class="text-2xl font-bold text-gray-900 inline-flex items-center gap-2"><x-svg-icon name="map" class="w-6 h-6" /> {{ $state->latitude ? __('Neighboring states') : __('Other states') }}</h2>
            <a href="{{ route('states.index') }}" class="text-sm text-primary-600 hover:underline font-semibold">{{ __('All') }} →</a>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
            @foreach ($otherStates as $s)
                <a href="{{ route('states.show', $s->slug) }}"
                   class="group bg-white rounded-xl overflow-hidden border border-gray-200 hover:border-primary-500 hover:shadow-md transition flex flex-col">
                    <div class="aspect-[4/3] overflow-hidden bg-gray-100 relative">
                        @if ($s->image_url)
                            <img src="{{ $s->image_url }}" alt="{{ $s->name_de }}" loading="lazy"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-emerald-500 to-teal-400 flex items-center justify-center">
                                <span class="text-3xl font-extrabold text-white/90">{{ mb_substr($s->name_de, 0, 1) }}</span>
                            </div>
                        @endif
                        @if (!empty($s->distance_km))
                            <span class="absolute bottom-2 right-2 px-2 py-0.5 rounded-full bg-black/60 backdrop-blur text-white text-xs font-semibold">
                                ~{{ (int) round($s->distance_km) }} km
                            </span>
                        @endif
                    </div>
                    <div class="p-3">
                        <h3 class="font-bold text-gray-900 group-hover:text-primary-600 transition text-sm truncate">{{ $s->name_de }}</h3>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- Generic FAQ — sadece content_blocks içinde FAQ blok YOKSA göster (çift FAQ önleme) --}}
@php $hasContentBlockFaq = collect($state->content_blocks ?? [])->contains(fn($b) => ($b['type'] ?? null) === 'faq'); @endphp
@if (! $hasContentBlockFaq)
<x-faq-section
    :title="__('Frequently Asked Questions')"
    :subtitle="__('Quick answers about studying in :state', ['state' => $state->name])"
    :faqs="\App\Support\PageFaq::forState($state)"
/>
@endif

@endsection
