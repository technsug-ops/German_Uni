@props(['variant' => 'default', 'class' => '', 'height' => 'h-8 md:h-9'])

@php
    $logo = $variant === 'white' ? brand('logo_white') : brand('logo');
    $name = brand('name');
@endphp

<img src="{{ asset($logo) }}" alt="{{ $name }}" width="160" height="36" class="{{ trim($height . ' w-auto ' . $class) }}" loading="eager" decoding="async" fetchpriority="high">
