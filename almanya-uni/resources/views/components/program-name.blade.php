@props([
    'program',
    'class' => '',            // resmi (primary) isim sınıfı
    'localizedClass' => 'block text-sm font-normal text-gray-500', // küçük localized karşılık
])

@php
    // PRIMARY = resmi isim (name_de canonical; başvuruda kullanılan isim).
    $official = $program->name_de ?: ($program->name_en ?? $program->name_tr ?? '');

    // Sayfa dilindeki karşılık (küçük puntoyla). Resmiyle aynıysa gösterme.
    $loc = app()->getLocale();
    $localized = $program->{'name_' . $loc} ?? null;
    $showLocalized = $localized
        && mb_strtolower(trim($localized)) !== mb_strtolower(trim($official));
@endphp

<span class="{{ $class }}">{{ $official }}</span>@if ($showLocalized)<span class="{{ $localizedClass }}">{{ $localized }}</span>@endif
