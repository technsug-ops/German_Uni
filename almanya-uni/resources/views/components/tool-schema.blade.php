@props(['tool' => null])

@php
    $cfg = config('tools_schema.' . $tool);
    $locale = app()->getLocale();
    $fallbackLocale = 'en';

    $name = null;
    $description = null;
    $featureList = [];
    $url = null;

    if (is_array($cfg)) {
        $name = $cfg['name'][$locale] ?? $cfg['name'][$fallbackLocale] ?? null;
        $description = $cfg['description'][$locale] ?? $cfg['description'][$fallbackLocale] ?? null;
        $featureList = $cfg['featureList'][$locale] ?? $cfg['featureList'][$fallbackLocale] ?? [];
        $url = isset($cfg['route']) ? route($cfg['route']) : null;
    }
@endphp

@if ($cfg && $name && $url)
    <x-json-ld :data="[
        '@context' => 'https://schema.org',
        '@type' => 'WebApplication',
        'name' => $name,
        'description' => $description,
        'url' => $url,
        'applicationCategory' => 'EducationalApplication',
        'operatingSystem' => 'Any',
        'browserRequirements' => 'Requires JavaScript and modern browser',
        'inLanguage' => [$locale, 'tr', 'en', 'de'],
        'isAccessibleForFree' => true,
        'offers' => [
            '@type' => 'Offer',
            'price' => '0',
            'priceCurrency' => 'EUR',
        ],
        'featureList' => $featureList,
        'publisher' => [
            '@type' => 'Organization',
            'name' => brand('name'),
            'url' => $url ? (parse_url($url, PHP_URL_SCHEME) . '://' . parse_url($url, PHP_URL_HOST)) : null,
        ],
    ]" />
@endif
