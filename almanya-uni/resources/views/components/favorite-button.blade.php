@props([
    'model',
    'type',       // 'university' | 'program' | 'profession'
    'size' => 'md',  // sm | md | lg
    'block' => false,
])

@php
    $isFav = auth()->check() && auth()->user()->hasFavorited($model);
    $sizeClasses = match ($size) {
        'sm' => 'text-sm px-2.5 py-1 gap-1.5',
        'lg' => 'text-base px-5 py-2.5 gap-2',
        default => 'text-sm px-3 py-1.5 gap-2',
    };
    $widthClass = $block ? 'w-full' : 'inline-flex';
@endphp

@auth
    <button type="button"
            data-fav-type="{{ $type }}"
            data-fav-id="{{ $model->getKey() }}"
            data-fav-active="{{ $isFav ? '1' : '0' }}"
            class="fav-btn {{ $widthClass }} flex items-center justify-center rounded-lg border transition font-semibold {{ $sizeClasses }}
                   {{ $isFav
                        ? 'bg-pink-50 border-pink-300 text-pink-700 hover:bg-pink-100'
                        : 'bg-white border-gray-300 text-gray-700 hover:border-pink-400 hover:bg-pink-50' }}"
            aria-label="Favorile">
        <svg class="fav-icon w-4 h-4 {{ $isFav ? 'fill-current' : '' }}" fill="{{ $isFav ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 0 1 6.364 0L12 7.636l1.318-1.318a4.5 4.5 0 1 1 6.364 6.364L12 20 4.318 12.682a4.5 4.5 0 0 1 0-6.364Z"/>
        </svg>
        <span class="fav-label">{{ $isFav ? 'Favoride' : 'Favorile' }}</span>
    </button>
@else
    <a href="{{ route('login') }}"
       class="{{ $widthClass }} flex items-center justify-center rounded-lg border bg-white border-gray-300 text-gray-700 hover:border-pink-400 hover:bg-pink-50 transition font-semibold {{ $sizeClasses }}"
       title="Favorilemek için giriş yap">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 0 1 6.364 0L12 7.636l1.318-1.318a4.5 4.5 0 1 1 6.364 6.364L12 20 4.318 12.682a4.5 4.5 0 0 1 0-6.364Z"/>
        </svg>
        <span>Favorile</span>
    </a>
@endauth
