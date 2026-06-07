@props(['variant' => 'white', 'class' => '', 'height' => 'h-10'])

{{--
    İKİNCİL (endorsed) logo — tagline'lı versiyon. Logo karşılaştırma raporu (2026-06-07):
    V1 (sade) birincil = header/favicon/avatar/küçük her yer; tagline ikincil = footer,
    landing, basılı/sunum gibi BÜYÜK + açıklayıcı bağlamlar (~32px üstü).

    Locale-aware: TR → "ALMANYA'DA EĞİTİM", DE → "STUDIEREN IN DEUTSCHLAND".
    EN (veya tagline'ı olmayan dil) → V1 sade beyaz/renkli logoya düşer (yanlış-dil
    tagline sızmaz). variant: white = koyu zemin (footer), default = açık zemin.
--}}
@php
    $locale = app()->getLocale();
    $tone = $variant === 'white' ? 'dark' : 'light'; // kit: -dark = koyu zemin (beyaz metin)
    $taglineLocales = ['tr', 'de'];
    $src = in_array($locale, $taglineLocales, true)
        ? "img/logos/atg-tagline-{$locale}-{$tone}.svg"
        : null;
@endphp

@if ($src)
    <img src="{{ asset($src) }}" alt="{{ brand('name') }}" width="200" height="35"
         class="{{ trim($height . ' w-auto ' . $class) }}" loading="lazy" decoding="async">
@else
    {{-- Tagline'ı olmayan dil → birincil V1 logo --}}
    <x-brand-logo :variant="$variant" height="h-8" :class="$class" />
@endif
