@php
    $record = $getRecord();
    $media = $record?->media ?? [];
    $images = collect($media)->where('type', 'image')->values();
    $audios = collect($media)->where('type', 'audio')->values();
@endphp

@if($audios->isNotEmpty())
    <div class="space-y-3 mb-4">
        <div class="text-sm font-semibold text-gray-700 dark:text-gray-300">
            🎙️ Üretilen ses ({{ $audios->count() }})
        </div>
        @foreach($audios as $audio)
            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3 ring-1 ring-gray-950/5 dark:ring-white/10">
                <audio controls class="w-full" preload="metadata">
                    <source src="{{ $audio['url'] }}" type="audio/mpeg">
                </audio>
                <div class="flex items-center justify-between mt-2 text-xs text-gray-500">
                    <span>{{ $audio['generated_by'] ?? '' }} · {{ $audio['voice_id'] ?? '' }}</span>
                    <span>{{ number_format($audio['character_count'] ?? 0) }} char · {{ round(($audio['size_bytes'] ?? 0)/1024,1) }} KB</span>
                </div>
            </div>
        @endforeach
    </div>
@endif

@if($images->isNotEmpty())
    <div class="space-y-3">
        <div class="text-sm font-semibold text-gray-700 dark:text-gray-300">
            🎨 Üretilen görseller ({{ $images->count() }})
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
            @foreach($images as $img)
                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg overflow-hidden ring-1 ring-gray-950/5 dark:ring-white/10">
                    <a href="{{ $img['url'] }}" target="_blank" class="block aspect-square overflow-hidden bg-gray-100 dark:bg-gray-900">
                        <img src="{{ $img['url'] }}"
                             alt="{{ Str::limit($img['prompt'] ?? '', 60) }}"
                             loading="lazy"
                             class="w-full h-full object-cover hover:scale-105 transition-transform"/>
                    </a>
                    <div class="p-2 text-xs">
                        <div class="text-gray-500 dark:text-gray-400 truncate" title="{{ $img['prompt'] ?? '' }}">
                            {{ Str::limit($img['prompt'] ?? '', 50) }}
                        </div>
                        <div class="flex items-center justify-between mt-1 text-[10px] text-gray-400">
                            <span>{{ $img['width'] ?? '?' }}×{{ $img['height'] ?? '?' }}</span>
                            <span>{{ round(($img['size_bytes'] ?? 0) / 1024, 1) }} KB</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif
