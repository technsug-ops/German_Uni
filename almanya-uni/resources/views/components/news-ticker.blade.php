@php
    use App\Models\MenuPage;
    use App\Models\Post;

    // Modül kapalıysa veya haber yoksa → hiç render etme.
    $tickerItems = MenuPage::isKeyEnabled('news.index')
        ? \Illuminate\Support\Facades\Cache::remember(
            'news_ticker:v1:' . app()->getLocale(),
            300,
            function () {
                return Post::published()->news()->newsOrder()
                    ->with('category:id,name_tr,name_en,name_de,color,slug')
                    ->limit(12)
                    ->get(['id', 'slug', 'title', 'category_id', 'published_at', 'event_date'])
                    ->map(fn ($p) => [
                        'title' => $p->title,
                        'url'   => route('news.show', $p->slug),
                        'color' => $p->category?->color ?? '#2563eb',
                    ])->all();
            }
        )
        : [];
@endphp

@if (count($tickerItems) >= 1)
<section aria-label="{{ __('News from Germany') }}"
         class="bg-gray-900 text-white border-y border-gray-800 overflow-hidden">
    <div class="max-w-[1400px] mx-auto flex items-stretch">
        {{-- Sabit etiket --}}
        <a href="{{ route('news.index') }}"
           class="flex items-center gap-1.5 bg-accent-500 hover:bg-accent-400 text-gray-900 px-4 py-2.5 font-bold text-xs md:text-sm whitespace-nowrap transition flex-shrink-0">
            <x-svg-icon name="newspaper" class="w-4 h-4" />
            <span class="hidden sm:inline">{{ __('News from Germany') }}</span>
            <span class="sm:hidden">{{ __('News') }}</span>
        </a>

        {{-- Kayan band (soldan sağa) — içerik 2x kopyalanır, kesintisiz döngü --}}
        <div class="news-ticker-viewport relative flex-1 overflow-hidden">
            <div class="news-ticker-track flex items-center gap-10 py-2.5 pl-6">
                @foreach (array_merge($tickerItems, $tickerItems) as $it)
                    <a href="{{ $it['url'] }}"
                       class="inline-flex items-center gap-2 text-sm text-gray-200 hover:text-white whitespace-nowrap">
                        <span class="w-2 h-2 rounded-full flex-shrink-0" style="background-color: {{ $it['color'] }}"></span>
                        {{ $it['title'] }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</section>

@once
<style>
    @keyframes news-ticker-scroll {
        from { transform: translateX(-50%); }
        to   { transform: translateX(0); }
    }
    .news-ticker-track {
        width: max-content;
        animation: news-ticker-scroll 45s linear infinite;
        will-change: transform;
    }
    .news-ticker-viewport:hover .news-ticker-track { animation-play-state: paused; }
    @media (prefers-reduced-motion: reduce) {
        .news-ticker-track { animation: none; }
    }
</style>
@endonce
@endif
