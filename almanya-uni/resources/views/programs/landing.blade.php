@extends('layouts.app')

@section('title', $h1 . ' — ' . brand('name'))

@php
    // OG image: city image varsa onu, yoksa field OG, yoksa city dynamic OG, yoksa default brand
    $ogImage = null;
    if (isset($city) && $city->image_url) {
        $ogImage = $city->image_url;
    } elseif (isset($field) && $field->image_url) {
        $ogImage = $field->image_url;
    } elseif (isset($city)) {
        $ogImage = route('og.image', ['type' => 'city', 'slug' => $city->slug . '.png']);
    } elseif (isset($field)) {
        $ogImage = route('og.image', ['type' => 'field', 'slug' => $field->slug . '.png']);
    }
@endphp

<x-seo
    :title="$h1"
    :description="$metaDescription"
    :image="$ogImage"
    :imageAlt="$h1"
/>

@push('head')
{{-- Schema.org ItemList — Google'a "bu sayfa program listesi" diye söyler --}}
@if ($programs->count() > 0)
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'ItemList',
    'name' => $h1,
    'description' => $metaDescription,
    'numberOfItems' => $totalCount,
    'itemListElement' => $programs->map(fn ($p, $i) => [
        '@type' => 'ListItem',
        'position' => $i + 1,
        'url' => route('programs.show', $p->slug),
        'name' => $p->name,
    ])->values()->all(),
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
</script>
@endif

{{-- BreadcrumbList — SERP'te ana sayfa > programs > [city]/[field] gösterimi --}}
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => array_filter([
        ['@type' => 'ListItem', 'position' => 1, 'name' => __('Home'), 'item' => url('/')],
        ['@type' => 'ListItem', 'position' => 2, 'name' => __('Programs'), 'item' => route('programs.index')],
        isset($city) ? ['@type' => 'ListItem', 'position' => 3, 'name' => $city->name, 'item' => route('cities.show', $city->slug)] : null,
        isset($field) ? ['@type' => 'ListItem', 'position' => isset($city) ? 4 : 3, 'name' => $field->name, 'item' => route('fields.show', $field->slug)] : null,
    ]),
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
</script>
@endpush

@section('content')

{{-- HERO --}}
<section class="bg-gradient-to-br from-primary-700 via-primary-600 to-primary-800 text-white">
    <div class="max-w-[1400px] mx-auto px-4 py-10 md:py-14">
        {{-- Breadcrumb --}}
        <nav class="text-sm text-primary-100 mb-3 flex items-center gap-2 flex-wrap">
            <a href="{{ route('home') }}" class="hover:text-white" title="{{ __('Home') }}">{{ __('Home') }}</a>
            <span class="opacity-60">›</span>
            <a href="{{ route('programs.index') }}" class="hover:text-white" title="{{ __('All Programs') }}">{{ __('Programs') }}</a>
            @if (isset($city))
                <span class="opacity-60">›</span>
                <a href="{{ route('cities.show', $city->slug) }}" class="hover:text-white" title="{{ $city->name }}">{{ $city->name }}</a>
            @endif
            @if (isset($field))
                <span class="opacity-60">›</span>
                <a href="{{ route('fields.show', $field->slug) }}" class="hover:text-white" title="{{ $field->name }}">{{ $field->name }}</a>
            @endif
        </nav>

        <h1 class="text-3xl md:text-5xl font-extrabold leading-tight drop-shadow mb-3 flex items-center gap-3 flex-wrap">
            @if (isset($field) && $field->icon)
                <span class="inline-flex">{!! e_icon($field->icon, 'w-8 h-8 md:w-10 md:h-10') !!}</span>
            @endif
            <span>{{ $h1 }}</span>
        </h1>
        <p class="text-lg md:text-xl text-primary-100 max-w-3xl">
            <strong class="text-white">{{ number_format($totalCount, 0, ',', '.') }}</strong>
            @if (isset($city) && isset($field))
                {{ __(':field programs at universities in :city.', ['field' => $field->name, 'city' => $city->name]) }}
            @elseif (isset($city) && isset($language))
                {{ __(':lang-taught programs at universities in :city.', ['lang' => $language === 'en' ? __('English-taught') : __('German-taught'), 'city' => $city->name]) }}
            @elseif (isset($field) && isset($degree))
                {{ __(':degree :field programs across Germany.', ['degree' => $degree === 'master' ? __('Master') : ($degree === 'phd' ? __('PhD') : __('Bachelor')), 'field' => $field->name]) }}
            @elseif (isset($context) && $context === 'city-nc-free')
                {{ __('NC-free (zulassungsfrei) programs at universities in :city — open admission.', ['city' => $city->name]) }}
            @endif
        </p>

        {{-- Quick filters / cross-links --}}
        @if (isset($context) && $context === 'city-field')
            <div class="flex flex-wrap gap-2 mt-5 text-sm">
                <span class="text-primary-200 mr-1">{{ __('By degree:') }}</span>
                <a href="{{ route('programs.city-field', [$city->slug, $field->slug]) }}?degree=bachelor"
                   class="bg-white/10 hover:bg-white/20 border border-white/15 px-3 py-1 rounded-full transition"
                   title="{{ __('Bachelor') }} — {{ $field->name }} — {{ $city->name }}">{{ __('Bachelor') }}</a>
                <a href="{{ route('programs.city-field', [$city->slug, $field->slug]) }}?degree=master"
                   class="bg-white/10 hover:bg-white/20 border border-white/15 px-3 py-1 rounded-full transition"
                   title="{{ __('Master') }} — {{ $field->name }} — {{ $city->name }}">{{ __('Master') }}</a>
                <a href="{{ route('programs.city-field', [$city->slug, $field->slug]) }}?degree=phd"
                   class="bg-white/10 hover:bg-white/20 border border-white/15 px-3 py-1 rounded-full transition"
                   title="{{ __('PhD') }} — {{ $field->name }} — {{ $city->name }}">{{ __('PhD') }}</a>
                <span class="text-primary-300 mx-2">·</span>
                <a href="{{ route('programs.city-language', [$city->slug, 'en']) }}"
                   class="bg-accent-500/20 hover:bg-accent-500/30 border border-accent-400/30 px-3 py-1 rounded-full transition"
                   title="{{ __('English-taught programs in :city', ['city' => $city->name]) }}">🇬🇧 {{ __('English programs') }}</a>
            </div>
        @endif
    </div>
</section>

<div class="max-w-[1400px] mx-auto px-4 py-10 grid grid-cols-1 lg:grid-cols-4 gap-8">

    {{-- Main: Program list --}}
    <div class="lg:col-span-3">
        @if ($programs->count() === 0)
            <div class="bg-amber-50 border border-amber-200 rounded-2xl p-8 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 mx-auto mb-3 rounded-full bg-amber-100 text-amber-600"><x-svg-icon name="x-circle" class="w-9 h-9" /></div>
                <h2 class="text-xl font-bold text-amber-900 mb-2">{{ __('No programs match this filter') }}</h2>
                <p class="text-amber-800 mb-5">{{ __('Try a different city, field or remove some filters.') }}</p>
                <a href="{{ route('programs.index') }}" class="inline-block bg-amber-600 hover:bg-amber-700 text-white font-semibold px-5 py-2.5 rounded-lg transition" title="{{ __('All Programs') }}">{{ __('See all programs') }} →</a>
            </div>
        @else
            <div class="space-y-3">
                @foreach ($programs as $program)
                    <a href="{{ route('programs.show', $program->slug) }}"
                       title="{{ $program->name }} — {{ $program->university->name ?? '' }}"
                       class="group block bg-white border border-gray-200 hover:border-primary-400 hover:shadow-md rounded-xl p-5 transition">
                        <div class="flex items-start gap-4">
                            @if (! empty($program->university->logo_url))
                                <img src="{{ $program->university->logo_url }}" alt="{{ $program->university->name }}"
                                     class="w-12 h-12 rounded-lg object-contain bg-gray-50 ring-1 ring-gray-200 shrink-0"
                                     loading="lazy" decoding="async">
                            @else
                                <div class="w-12 h-12 rounded-lg bg-primary-50 text-primary-600 flex items-center justify-center shrink-0"><x-svg-icon name="academic-cap" class="w-6 h-6" /></div>
                            @endif
                            <div class="flex-1 min-w-0">
                                <h2 class="text-base md:text-lg font-bold text-gray-900 group-hover:text-primary-700 leading-tight mb-1"><x-program-name :program="$program" /></h2>
                                <p class="text-sm text-gray-600 mb-2">{{ $program->university->name ?? '' }} · {{ $program->university->city->name ?? '' }}</p>
                                <div class="flex flex-wrap gap-1.5 text-xs">
                                    <span class="px-2 py-0.5 rounded bg-primary-50 text-primary-700 font-semibold">{{ __(ucfirst($program->degree)) }}</span>
                                    @if ($program->language === 'en')
                                        <span class="px-2 py-0.5 rounded bg-blue-50 text-blue-700 font-semibold">🇬🇧 {{ __('English') }}</span>
                                    @elseif ($program->language === 'de')
                                        <span class="px-2 py-0.5 rounded bg-gray-100 text-gray-700 font-semibold">🇩🇪 {{ __('German') }}</span>
                                    @elseif ($program->language === 'both')
                                        <span class="px-2 py-0.5 rounded bg-amber-50 text-amber-700 font-semibold">🇬🇧/🇩🇪 {{ __('Both') }}</span>
                                    @endif
                                    @if ($program->duration_semesters)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-gray-100 text-gray-700"><x-svg-icon name="clock" class="w-3 h-3" /> {{ $program->duration_semesters }} {{ __('sem') }}</span>
                                    @endif
                                    @if (! $program->tuition_fee_eur)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 font-semibold"><x-svg-icon name="banknotes" class="w-3 h-3" /> {{ __('Free') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $programs->links() }}
            </div>
        @endif
    </div>

    {{-- Sidebar: related landing pages (internal linking) --}}
    <aside class="lg:col-span-1 space-y-6">
        {{-- Related fields in same city --}}
        @if (isset($otherFields) && $otherFields->count() > 0)
            <section class="bg-white border border-gray-200 rounded-2xl p-5">
                <h2 class="text-sm font-bold uppercase tracking-wider text-gray-600 mb-3 inline-flex items-center gap-1.5">
                    <x-svg-icon name="target" class="w-3.5 h-3.5" /> {{ __('Other fields in :city', ['city' => $city->name]) }}
                </h2>
                <ul class="space-y-2 text-sm">
                    @foreach ($otherFields as $f)
                        <li>
                            <a href="{{ route('programs.city-field', [$city->slug, $f->slug]) }}"
                               title="{{ $f->name }} — {{ $city->name }}"
                               class="flex items-center gap-2 text-gray-700 hover:text-primary-700">
                                <span class="inline-flex w-4 h-4 text-primary-600">{!! $f->icon ? e_icon($f->icon, 'w-4 h-4') : view('components.svg-icon', ['name' => 'book-open', 'class' => 'w-4 h-4'])->render() !!}</span>
                                <span>{{ $f->name }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </section>
        @endif

        {{-- Related cities for same field/language --}}
        @if (isset($otherCities) && $otherCities->count() > 0)
            <section class="bg-white border border-gray-200 rounded-2xl p-5">
                <h2 class="text-sm font-bold uppercase tracking-wider text-gray-600 mb-3 inline-flex items-center gap-1.5">
                    <x-svg-icon name="building-office" class="w-3.5 h-3.5" />
                    @if (isset($field))
                        {{ __(':field in other cities', ['field' => $field->name]) }}
                    @elseif (isset($language))
                        {{ __(':lang-taught in other cities', ['lang' => $language === 'en' ? __('English-taught') : __('German-taught')]) }}
                    @endif
                </h2>
                <ul class="space-y-2 text-sm">
                    @foreach ($otherCities as $c)
                        <li>
                            @if (isset($field))
                                <a href="{{ route('programs.city-field', [$c->slug, $field->slug]) }}"
                                   title="{{ $field->name }} — {{ $c->name }}"
                                   class="flex items-center justify-between gap-2 text-gray-700 hover:text-primary-700">
                                    <span>{{ $c->name }}</span>
                                    @if (isset($c->programs_count))
                                        <span class="text-xs text-gray-400">{{ $c->programs_count }}</span>
                                    @endif
                                </a>
                            @elseif (isset($language))
                                <a href="{{ route('programs.city-language', [$c->slug, $language]) }}"
                                   title="{{ ($language === 'en' ? __('English-taught') : __('German-taught')) }} — {{ $c->name }}"
                                   class="flex items-center gap-2 text-gray-700 hover:text-primary-700">{{ $c->name }}</a>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </section>
        @endif

        {{-- Tools CTA --}}
        <section class="bg-gradient-to-br from-primary-50 to-accent-50 border border-primary-200 rounded-2xl p-5">
            <h2 class="text-sm font-bold uppercase tracking-wider text-primary-700 mb-3 inline-flex items-center gap-1.5"><x-svg-icon name="wrench-screwdriver" class="w-3.5 h-3.5" /> {{ __('Helpful tools') }}</h2>
            <ul class="space-y-2 text-sm">
                <li><a href="{{ route('tools.cost-of-living') }}" class="inline-flex items-center gap-1.5 text-primary-700 hover:text-primary-900 font-medium" title="{{ __('Cost of Living') }}"><x-svg-icon name="banknotes" class="w-4 h-4" /> {{ __('Cost of Living') }}</a></li>
                <li><a href="{{ route('tools.deadlines') }}" class="inline-flex items-center gap-1.5 text-primary-700 hover:text-primary-900 font-medium" title="{{ __('Application Calendar') }}"><x-svg-icon name="calendar" class="w-4 h-4" /> {{ __('Application Calendar') }}</a></li>
                <li><a href="{{ route('tools.eligibility-checker') }}" class="inline-flex items-center gap-1.5 text-primary-700 hover:text-primary-900 font-medium" title="{{ __('Eligibility Checker') }}"><x-svg-icon name="academic-cap" class="w-4 h-4" /> {{ __('Eligibility Checker') }}</a></li>
                <li><a href="{{ route('glossary.index') }}" class="inline-flex items-center gap-1.5 text-primary-700 hover:text-primary-900 font-medium" title="{{ __('Glossary') }}"><x-svg-icon name="book-open" class="w-4 h-4" /> {{ __('Glossary') }}</a></li>
            </ul>
        </section>
    </aside>
</div>

@endsection
