@props([
    'title' => null,
    'description' => null,
    'canonical' => null,
    'image' => null,
    'imageAlt' => null,
    'type' => 'website',
    'noindex' => false,
    'publishedAt' => null,
    'updatedAt' => null,
    'author' => null,
])

@php
    // Domain-aware brand: almanyauni.com=AlmanyaUni, applytogerman.com=ApplyToGerman
    $siteName = brand('name');
    $brandTagline = brand('tagline');
    // Title fallback: TR locale'de + AlmanyaUni brand'de TR-spesifik title; diğer kombinasyonlarda brand-aware
    $finalTitle = $title
        ?? (app()->getLocale() === 'tr' && brand_key() === 'almanyauni'
            ? config('seo.default.title')
            : ($siteName . ($brandTagline ? ' — ' . $brandTagline : ' — ' . __('Study in Germany Guide'))));
    $finalDescription = $description
        ?? (app()->getLocale() === 'tr' && brand_key() === 'almanyauni'
            ? config('seo.default.description')
            : __('University, visa, cost of living and scholarship guide for studying in Germany.'));
    $canonicalUrl = $canonical ?? url(request()->path());
    // OG image: explicit > brand-specific > config fallback
    $ogImage = $image ?? brand('og_image') ?? config('seo.default.image');
    $ogImageAbs = str_starts_with($ogImage, 'http') ? $ogImage : url($ogImage);
    $ogImageAlt = $imageAlt ?? ($title ?? $siteName);
    $twitterCard = config('seo.twitter.card');
    // Twitter handle brand-specific
    $twitterHandle = brand('twitter') ?: config('seo.twitter.handle');
    // og:locale current locale'den türet (BCP47 → og format)
    $localeMap = ['tr' => 'tr_TR', 'de' => 'de_DE', 'en' => 'en_US'];
    $locale = $localeMap[app()->getLocale()] ?? config('seo.locale');
    // Multi-language alternate locale'ler (mevcut hariç)
    $altLocales = collect(config('locale.active', ['tr','de','en']))
        ->map(fn ($l) => $localeMap[$l] ?? null)
        ->filter(fn ($l) => $l && $l !== $locale)
        ->values();
@endphp

@push('meta')
    <meta name="description" content="{{ $finalDescription }}">
    {{-- canonical artık layout'ta (her sayfa için tek, çift olmasın) --}}
    @if ($noindex)
        {{-- İnce/boş iç sayfa: index'e girme ama linkleri TAKİP ET (değer iyi sayfalara aksın). --}}
        <meta name="robots" content="noindex, follow">
    @else
        <meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large">
    @endif

    {{-- Open Graph --}}
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:type" content="{{ $type }}">
    <meta property="og:title" content="{{ $finalTitle }}">
    <meta property="og:description" content="{{ $finalDescription }}">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    <meta property="og:image" content="{{ $ogImageAbs }}">
    <meta property="og:image:width" content="{{ config('seo.default.image_width', 1200) }}">
    <meta property="og:image:height" content="{{ config('seo.default.image_height', 630) }}">
    <meta property="og:image:alt" content="{{ $ogImageAlt }}">
    <meta property="og:image:type" content="{{ str_ends_with($ogImageAbs, '.png') ? 'image/png' : 'image/jpeg' }}">
    <meta property="og:locale" content="{{ $locale }}">
    @foreach ($altLocales as $alt)
        <meta property="og:locale:alternate" content="{{ $alt }}">
    @endforeach

    @if ($type === 'article' && $publishedAt)
        <meta property="article:published_time" content="{{ $publishedAt instanceof \DateTimeInterface ? $publishedAt->format('c') : $publishedAt }}">
        @if ($updatedAt)
            <meta property="article:modified_time" content="{{ $updatedAt instanceof \DateTimeInterface ? $updatedAt->format('c') : $updatedAt }}">
        @endif
        @if ($author)
            <meta property="article:author" content="{{ $author }}">
        @endif
    @endif

    {{-- Twitter / X --}}
    <meta name="twitter:card" content="{{ $twitterCard }}">
    <meta name="twitter:site" content="{{ $twitterHandle }}">
    @if ($type === 'article' && $author)
        <meta name="twitter:creator" content="{{ $twitterHandle }}">
    @endif
    <meta name="twitter:title" content="{{ $finalTitle }}">
    <meta name="twitter:description" content="{{ $finalDescription }}">
    <meta name="twitter:image" content="{{ $ogImageAbs }}">
    <meta name="twitter:image:alt" content="{{ $ogImageAlt }}">
@endpush
