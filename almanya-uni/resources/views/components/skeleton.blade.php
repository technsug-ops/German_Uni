@props([
    'variant' => 'card',  // card | list | text | image
    'count' => 1,         // how many copies to render
    'aspect' => '16/9',   // for card image area
])

{{--
    Skeleton placeholders for loading states. Use during async filter changes,
    SSR-disabled regions, or paginated infinite-scroll loads.

    Usage:
        <x-skeleton variant="card" :count="6" />
        <x-skeleton variant="list" :count="4" />
        <x-skeleton variant="text" />

    Animation respects prefers-reduced-motion (handled in app.css).
--}}

@for ($i = 0; $i < $count; $i++)
    @switch($variant)
        @case('card')
            <div class="bg-white rounded-xl overflow-hidden border border-gray-200 animate-pulse">
                <div class="bg-gray-200 aspect-[{{ $aspect }}]"></div>
                <div class="p-4 space-y-2">
                    <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                    <div class="h-3 bg-gray-100 rounded w-1/2"></div>
                </div>
            </div>
            @break

        @case('list')
            <div class="flex items-center gap-3 p-3 bg-white border border-gray-200 rounded-lg animate-pulse">
                <div class="w-10 h-10 bg-gray-200 rounded-full"></div>
                <div class="flex-1 space-y-1.5">
                    <div class="h-3 bg-gray-200 rounded w-3/4"></div>
                    <div class="h-2 bg-gray-100 rounded w-1/2"></div>
                </div>
            </div>
            @break

        @case('text')
            <div class="space-y-2 animate-pulse">
                <div class="h-3 bg-gray-200 rounded w-full"></div>
                <div class="h-3 bg-gray-200 rounded w-5/6"></div>
                <div class="h-3 bg-gray-200 rounded w-3/4"></div>
            </div>
            @break

        @case('image')
            <div class="bg-gray-200 rounded-xl aspect-[{{ $aspect }}] animate-pulse"></div>
            @break
    @endswitch
@endfor
