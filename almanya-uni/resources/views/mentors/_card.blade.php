<a href="{{ route('mentors.show', $mentor->slug) }}"
   class="group block bg-white border border-gray-200 hover:border-emerald-400 hover:shadow-md transition rounded-xl p-5 flex flex-col">
    <div class="flex items-start gap-3 mb-3">
        @if ($mentor->avatar_url)
            <img src="{{ $mentor->avatar_url }}" alt="{{ $mentor->name }}" loading="lazy"
                 class="w-14 h-14 rounded-full object-cover ring-2 ring-emerald-100">
        @else
            <div class="w-14 h-14 rounded-full bg-gradient-to-br from-emerald-500 to-teal-500 text-white font-extrabold flex items-center justify-center text-lg ring-2 ring-emerald-100">
                {{ $mentor->initials }}
            </div>
        @endif
        <div class="flex-1 min-w-0">
            <h3 class="font-bold text-gray-900 group-hover:text-emerald-700 leading-tight">{{ $mentor->name }}</h3>
            @if ($mentor->headline)
                <p class="text-xs text-gray-600 line-clamp-2 mt-0.5">{{ $mentor->headline }}</p>
            @endif
            @if ($mentor->is_featured)
                <span class="inline-block mt-1 text-[10px] font-bold uppercase tracking-wider px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-700">⭐ Öne Çıkan</span>
            @endif
        </div>
    </div>

    @if (! empty($mentor->topics))
        <div class="flex flex-wrap gap-1 mb-3">
            @foreach (array_slice($mentor->topics, 0, 4) as $topic)
                <span class="text-[10px] px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700">{{ $topic }}</span>
            @endforeach
        </div>
    @endif

    <div class="mt-auto pt-3 border-t border-gray-100 flex items-baseline justify-between text-xs text-gray-600">
        @if ($mentor->is_free)
            <span class="font-semibold text-emerald-600">🎁 Ücretsiz</span>
        @else
            <span class="font-semibold text-amber-700">{{ number_format($mentor->rate_eur, 0, ',', '.') }} €/{{ $mentor->session_duration ?: 'seans' }}</span>
        @endif
        @if ($mentor->rating_avg)
            <span>⭐ {{ number_format($mentor->rating_avg, 1) }} ({{ $mentor->rating_count }})</span>
        @elseif ($mentor->sessions_count > 0)
            <span>{{ $mentor->sessions_count }} seans</span>
        @endif
    </div>
</a>
