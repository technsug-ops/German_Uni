@extends('layouts.app')

@php
    $description = \App\Support\Seo::descriptionFromBlocks(
        $field->content_blocks,
        __('In Germany, :field offers :all programs across :unis universities. Bachelor, Master and English-taught program options.', [
            'field' => $field->name,
            'all' => $totals['all'],
            'unis' => $totals['unis'],
        ])
    );
@endphp

<x-seo
    :title="$field->name . ' — ' . __('Studying in Germany')"
    :description="$description"
    :image="$field->image_url ?: route('og.image', ['type' => 'field', 'slug' => $field->slug . '.png'])"
/>

<x-json-ld :data="\App\Support\Seo::breadcrumbs([
    ['name' => __('Home'), 'url' => route('home')],
    ['name' => __('Fields'), 'url' => route('fields.index')],
    ['name' => $field->name, 'url' => route('fields.show', $field->slug)],
])" />

@php $faqSchema = \App\Support\Seo::faqPageFromBlocks($field->content_blocks); @endphp
@if ($faqSchema)
    <x-json-ld :data="$faqSchema" />
@endif

@section('title', $field->name . ' — ' . __('Studying in Germany') . '  — ' . brand('name'))
@section('meta_description', $description)

@section('content')

{{-- HERO --}}
<section class="relative text-white overflow-hidden"
         style="background: linear-gradient(135deg, {{ $field->color }}, {{ $field->color }}cc);">
    @if($field->image_url)
        <img src="{{ $field->image_url }}" alt="{{ __(':field — study programs in Germany', ['field' => $field->name]) }}"
             class="absolute inset-0 w-full h-full object-cover opacity-25" loading="eager" fetchpriority="high"/>
        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/30 to-transparent"></div>
    @endif
    <div class="relative max-w-[1400px] mx-auto px-4 py-10 md:py-14">
        <nav class="text-sm text-white/80 mb-3">
            <a href="/" class="hover:text-white">{{ __('Home') }}</a>
            <span class="mx-2 opacity-60">›</span>
            <a href="{{ route('fields.index') }}" class="hover:text-white">{{ __('Fields') }}</a>
            <span class="mx-2 opacity-60">›</span>
            <span class="text-white">{{ $field->name }}</span>
        </nav>
        <div class="flex items-center gap-3 mb-2">
            <span class="text-5xl">{{ $field->icon }}</span>
            <div>
                <h1 class="text-3xl md:text-5xl font-extrabold leading-tight drop-shadow">{{ $field->name }}</h1>
                <p class="text-white/80 text-sm md:text-base mt-1">{{ $field->name_de }}</p>
            </div>
        </div>
        <div class="flex flex-wrap gap-2 text-sm mt-4">
            <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full bg-white/15 backdrop-blur ring-1 ring-white/25">
                🎓 {{ number_format($totals['all']) }} {{ __('programs') }}
            </span>
            <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full bg-white/15 backdrop-blur ring-1 ring-white/25">
                🇬🇧 {{ $totals['english'] }} {{ __('English') }}
            </span>
            <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full bg-white/15 backdrop-blur ring-1 ring-white/25">
                🏛️ {{ $totals['unis'] }} {{ __('universities') }}
            </span>
            @if($totals['professions'] > 0)
                <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full bg-white/15 backdrop-blur ring-1 ring-white/25">
                    💼 {{ $totals['professions'] }} {{ __('professions') }}
                </span>
            @endif
            <a href="{{ route('programs.index', ['field' => $field->slug]) }}"
               class="inline-flex items-center gap-1 px-4 py-1.5 rounded-full bg-white text-gray-900 font-semibold hover:bg-gray-100 transition">
                {{ __('All programs') }} →
            </a>
        </div>
    </div>
</section>

{{-- CONTENT --}}
<section class="bg-gray-50 py-10">
    <div class="max-w-[1400px] mx-auto px-4">

        {{-- content_blocks varsa render (sadece TR locale — content_blocks single-language) --}}
        @if(!empty($field->content_blocks) && app()->getLocale() === 'tr')
            <x-content-blocks :blocks="$field->content_blocks" />
        @else
            <div class="bg-white rounded-2xl border border-gray-200 p-8 text-center shadow-sm mb-8">
                <div class="text-5xl mb-3">📝</div>
                <h2 class="text-xl font-bold text-gray-900 mb-2">{{ __('Detailed content coming soon') }}</h2>
                <p class="text-gray-600 max-w-lg mx-auto">
                    {{ __('Careers, course content and application details for this field are being prepared. In the meantime, browse the program and university lists below.') }}
                </p>
            </div>
        @endif

        {{-- TOP UNİLER --}}
        @if($topUnis->isNotEmpty())
            <section class="mt-10">
                <div class="flex items-baseline justify-between mb-5 flex-wrap gap-2">
                    <h2 class="text-2xl font-bold text-gray-900">🏛️ {{ __('Top universities in :field', ['field' => $field->name]) }}</h2>
                    <a href="{{ route('universities.index') }}" class="text-sm text-primary-600 hover:underline">{{ __('All universities') }} →</a>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    @foreach ($topUnis as $u)
                        <a href="{{ route('universities.show', $u->slug) }}"
                           class="group bg-white rounded-xl overflow-hidden border border-gray-200 hover:border-primary-500 hover:shadow-md transition flex flex-col">
                            <div class="aspect-[16/9] overflow-hidden bg-gray-100 relative">
                                @if($u->image_url)
                                    <img src="{{ $u->image_url }}" alt="{{ $u->display_name }}" loading="lazy"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-primary-500 to-accent-500 flex items-center justify-center">
                                        <span class="text-3xl font-extrabold text-white/90">{{ mb_substr($u->name_de, 0, 2) }}</span>
                                    </div>
                                @endif
                                <span class="absolute bottom-2 right-2 px-2 py-0.5 rounded-full bg-black/60 backdrop-blur text-white text-xs font-semibold">
                                    {{ $u->programs_count }} {{ __('programs') }}
                                </span>
                            </div>
                            <div class="p-3">
                                <h3 class="font-bold text-gray-900 group-hover:text-primary-600 transition leading-tight line-clamp-2 text-sm">{{ $u->display_name }}</h3>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- TOP ŞEHİRLER — kartlar landing page'e yönlendir (city × field) --}}
        @if (! empty($topCities) && $topCities->isNotEmpty())
            <section class="mt-10">
                <div class="flex items-baseline justify-between mb-4 flex-wrap gap-2">
                    <h2 class="text-2xl font-bold text-gray-900">🏙️ {{ __('Best cities for :field', ['field' => $field->name]) }}</h2>
                    <a href="{{ route('cities.index') }}" class="text-sm text-primary-600 hover:underline" title="{{ __('All cities') }}">{{ __('All cities') }} →</a>
                </div>
                <p class="text-sm text-gray-600 mb-4">{!! __('Cities offering the most programs in this field. Compare the Sperrkonto burden, cost of living and student life with the <a href=":url" class="text-primary-600 hover:underline">cost calculator</a>.', ['url' => route('tools.cost-of-living')]) !!}</p>
                <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                    @foreach ($topCities as $c)
                        @php
                            $sizeBadge = match (true) {
                                $c->population > 1_000_000 => ['🌆', __('Metropolis')],
                                $c->population > 200_000   => ['🏙️', __('Mid-size')],
                                $c->population > 0          => ['🏘️', __('Small')],
                                default                     => null,
                            };
                        @endphp
                        <a href="{{ route('programs.city-field', [$c->slug, $field->slug]) }}"
                           title="{{ __(':field in :city', ['field' => $field->name, 'city' => $c->name]) }}"
                           class="group bg-white rounded-xl border border-gray-200 hover:border-primary-500 hover:shadow-md transition p-4 text-center">
                            <h3 class="font-bold text-gray-900 group-hover:text-primary-600 leading-tight">{{ $c->name }}</h3>
                            <p class="text-sm font-semibold text-primary-700 mt-1">{{ $c->program_count }} {{ __('programs') }}</p>
                            @if ($sizeBadge)
                                <p class="text-xs text-gray-500 mt-1">{{ $sizeBadge[0] }} {{ $sizeBadge[1] }}</p>
                            @endif
                        </a>
                    @endforeach
                </div>
            </section>

            {{-- Cross-linking: field × degree landing pages --}}
            <section class="mt-8">
                <h2 class="text-xl font-bold text-gray-900 mb-3">🎓 {{ __(':field by degree', ['field' => $field->name]) }}</h2>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    @foreach (['bachelor', 'master', 'phd'] as $deg)
                        @php
                            $degLabel = match ($deg) {
                                'bachelor' => __('Bachelor'),
                                'master' => __('Master'),
                                'phd' => __('PhD'),
                            };
                            $degIcon = match ($deg) {
                                'bachelor' => '🎓',
                                'master' => '🎯',
                                'phd' => '🔬',
                            };
                        @endphp
                        <a href="{{ route('programs.field-degree', [$field->slug, $deg]) }}"
                           title="{{ $degLabel }} {{ $field->name }} — {{ __('Germany') }}"
                           class="group flex items-center gap-3 bg-white border border-gray-200 hover:border-primary-500 hover:shadow-md rounded-xl p-4 transition">
                            <span class="text-3xl shrink-0">{{ $degIcon }}</span>
                            <div class="min-w-0">
                                <p class="font-bold text-gray-900 group-hover:text-primary-700 leading-tight">{{ $degLabel }} {{ $field->name }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">{{ __('All programs across Germany') }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- BACHELOR PROGRAMLARI --}}
        @if($bachelorPrograms->isNotEmpty())
            <section class="mt-10">
                <div class="flex items-baseline justify-between mb-4 flex-wrap gap-2">
                    <h2 class="text-2xl font-bold text-gray-900">🎓 {{ __('Bachelor Programs') }} <span class="text-base font-normal text-gray-500">({{ $totals['bachelor'] }})</span></h2>
                    <a href="{{ route('programs.index', ['field' => $field->slug, 'degree' => 'bachelor']) }}" class="text-sm text-primary-600 hover:underline">{{ __('All') }} →</a>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach ($bachelorPrograms as $p)
                        <a href="{{ route('programs.show', $p->slug) }}"
                           class="group bg-white rounded-lg border border-gray-200 hover:border-primary-500 hover:shadow-md transition p-4">
                            <h3 class="font-semibold text-gray-900 group-hover:text-primary-600 leading-snug">{{ $p->name_de }}</h3>
                            @if ($p->university)
                                <p class="text-xs text-gray-500 mt-1">@ {{ $p->university->display_name }}</p>
                            @endif
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- MESLEK ÖRNEKLERİ --}}
        @if(!empty($topProfessions) && $topProfessions->isNotEmpty())
            <section class="mt-10">
                <div class="flex items-baseline justify-between mb-4 flex-wrap gap-2">
                    <h2 class="text-2xl font-bold text-gray-900">💼 {{ __('Professions in :field', ['field' => $field->name]) }} <span class="text-base font-normal text-gray-500">({{ $totals['professions'] }})</span></h2>
                    <a href="{{ route('professions.index', ['field' => $field->slug]) }}" class="text-sm text-primary-600 hover:underline">{{ __('All') }} →</a>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach ($topProfessions as $p)
                        <a href="{{ route('professions.show', $p->slug) }}"
                           class="group bg-white rounded-lg border border-gray-200 hover:border-primary-500 hover:shadow-md transition p-4">
                            <h3 class="font-semibold text-gray-900 group-hover:text-primary-600 leading-snug">
                                {{ $p->name }}
                            </h3>
                            @if ($p->name && $p->name !== $p->name_de)
                                <p class="text-xs text-gray-500 italic mt-0.5">{{ $p->name_de }}</p>
                            @endif
                            @if ($p->description)
                                <p class="text-xs text-gray-600 mt-1 line-clamp-2">{{ \Illuminate\Support\Str::limit($p->description, 140) }}</p>
                            @elseif ($p->steckbrief)
                                <p class="text-xs text-gray-600 mt-1 line-clamp-2">{{ \Illuminate\Support\Str::limit($p->steckbrief, 140) }}</p>
                            @endif
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- MASTER PROGRAMLARI --}}
        @if($masterPrograms->isNotEmpty())
            <section class="mt-10">
                <div class="flex items-baseline justify-between mb-4 flex-wrap gap-2">
                    <h2 class="text-2xl font-bold text-gray-900">🎯 {{ __('Master Programs') }} <span class="text-base font-normal text-gray-500">({{ $totals['master'] }})</span></h2>
                    <a href="{{ route('programs.index', ['field' => $field->slug, 'degree' => 'master']) }}" class="text-sm text-primary-600 hover:underline">{{ __('All') }} →</a>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach ($masterPrograms as $p)
                        <a href="{{ route('programs.show', $p->slug) }}"
                           class="group bg-white rounded-lg border border-gray-200 hover:border-primary-500 hover:shadow-md transition p-4">
                            <h3 class="font-semibold text-gray-900 group-hover:text-primary-600 leading-snug">{{ $p->name_de }}</h3>
                            @if ($p->university)
                                <p class="text-xs text-gray-500 mt-1">@ {{ $p->university->display_name }}</p>
                            @endif
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- İLGİLİ BLOG YAZILARI --}}
        @if (! empty($relatedPosts) && $relatedPosts->count() > 0)
            <section class="mt-10">
                <div class="flex items-baseline justify-between mb-4 flex-wrap gap-2">
                    <h2 class="text-2xl font-bold text-gray-900">📝 {{ __('Posts about :field', ['field' => $field->name]) }}</h2>
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
                            <p class="text-xs text-gray-500">{{ $post->reading_minutes }} {{ __('min read') }}</p>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

    </div>
</section>

{{-- ════════ DİĞER ALANLAR ════════ --}}
@if (!empty($otherFields) && $otherFields->isNotEmpty())
<section class="bg-white border-t border-gray-200 py-10">
    <div class="max-w-[1400px] mx-auto px-4">
        <div class="flex items-baseline justify-between mb-5 flex-wrap gap-2">
            <h2 class="text-2xl font-bold text-gray-900">🎯 {{ __('Other study fields') }}</h2>
            <a href="{{ route('fields.index') }}" class="text-sm text-primary-600 hover:underline font-semibold">{{ __('All') }} →</a>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            @foreach ($otherFields as $f)
                <a href="{{ route('fields.show', $f->slug) }}"
                   class="group bg-white rounded-xl overflow-hidden border border-gray-200 hover:border-primary-500 hover:shadow-md transition flex flex-col">
                    <div class="aspect-[4/3] overflow-hidden bg-gray-100 relative">
                        @if ($f->image_url)
                            <img src="{{ $f->image_url }}" alt="" loading="lazy"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                        @else
                            <div class="w-full h-full flex items-center justify-center text-5xl"
                                 style="background: linear-gradient(135deg, {{ $f->color }}33, {{ $f->color }}66);">
                                <span>{{ $f->icon }}</span>
                            </div>
                        @endif
                        <span class="absolute bottom-2 right-2 px-2 py-0.5 rounded-full bg-black/60 backdrop-blur text-white text-xs font-semibold">
                            {{ number_format($f->programs_count) }}
                        </span>
                    </div>
                    <div class="p-3">
                        <div class="flex items-center gap-1 leading-tight">
                            <span class="text-lg">{{ $f->icon }}</span>
                            <h3 class="font-bold text-gray-900 group-hover:text-primary-600 transition text-sm truncate">{{ $f->name }}</h3>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- Generic FAQ — sadece content_blocks içinde FAQ blok YOKSA göster (çift FAQ önleme) --}}
@php $hasContentBlockFaq = collect($field->content_blocks ?? [])->contains(fn($b) => ($b['type'] ?? null) === 'faq'); @endphp
@if (! $hasContentBlockFaq)
<x-faq-section
    :title="__('Frequently Asked Questions')"
    :subtitle="__('Quick answers about :field programmes in Germany', ['field' => $field->name])"
    :faqs="\App\Support\PageFaq::forField($field)"
/>
@endif

@endsection
