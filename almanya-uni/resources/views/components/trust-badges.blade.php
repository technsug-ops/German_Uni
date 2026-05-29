@props([
    'slot' => 'footer',
    'heading' => null,
])

@php
    try {
        $badges = \App\Models\TrustBadge::active()->forSlot($slot)->get();
    } catch (\Throwable $e) {
        $badges = collect();
    }
@endphp

@if ($badges->isNotEmpty())
<div class="trust-badges-{{ $slot }}">
    @if ($heading)
        <p class="text-xs uppercase tracking-wider font-bold text-gray-500 mb-3 text-center">{{ $heading }}</p>
    @endif
    <div class="flex flex-wrap items-center justify-center gap-4 md:gap-6">
        @foreach ($badges as $badge)
            @if ($badge->badge_html)
                {{-- Trustpilot / G2 embed snippet — admin-provided HTML --}}
                <div class="trust-badge-embed">{!! $badge->badge_html !!}</div>
            @else
                <a href="{{ $badge->profile_url ?: '#' }}"
                   @if ($badge->profile_url) target="_blank" rel="noopener noreferrer" @endif
                   class="group inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-white/5 hover:bg-white/10 border border-white/10 transition"
                   title="{{ $badge->display_name }}">
                    @if ($badge->logo_url)
                        <img src="{{ $badge->logo_url }}"
                             alt="{{ $badge->display_name }}"
                             loading="lazy"
                             class="h-5 md:h-6 w-auto object-contain opacity-80 group-hover:opacity-100 transition">
                    @else
                        <span class="text-sm font-semibold text-gray-300 group-hover:text-white">{{ $badge->display_name }}</span>
                    @endif
                    @if ($badge->rating)
                        <span class="text-xs font-bold text-amber-300">
                            ⭐ {{ rtrim(rtrim(number_format((float) $badge->rating, 1), '0'), '.') }}
                            @if ($badge->review_count)
                                <span class="text-gray-400 font-normal">({{ number_format($badge->review_count) }})</span>
                            @endif
                        </span>
                    @endif
                </a>
            @endif
        @endforeach
    </div>
</div>
@endif
