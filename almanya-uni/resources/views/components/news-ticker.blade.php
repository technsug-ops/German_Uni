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
<section aria-label="{{ __('News from Germany') }}" class="py-3">
    <div class="max-w-[1400px] mx-auto px-4">
        <div class="flex items-stretch rounded-2xl overflow-hidden bg-gradient-to-r from-primary-900 via-primary-900 to-primary-800 text-white shadow-lg shadow-primary-900/25 ring-1 ring-white/10">
        {{-- Sabit etiket — "canlı" nabız noktası + uppercase tracking (premium haber hissi) --}}
        <a href="{{ route('news.index') }}"
           class="flex items-center gap-2 bg-accent-500 hover:bg-accent-400 text-white px-4 py-2.5 font-bold text-[11px] md:text-xs uppercase tracking-wider whitespace-nowrap transition-colors flex-shrink-0">
            <span class="relative flex h-2 w-2" aria-hidden="true">
                <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-white/80 opacity-75"></span>
                <span class="relative inline-flex h-2 w-2 rounded-full bg-white"></span>
            </span>
            <span class="hidden sm:inline">{{ __('News from Germany') }}</span>
            <span class="sm:hidden">{{ __('News') }}</span>
        </a>

        {{-- Kayan band (soldan sağa) — içerik 2x kopyalanır, kesintisiz döngü.
             Süre haber SAYISINA orantılı (her haber ~7s, min 50s) → haberlerin ÇOKLUĞU
             hızı ARTTIRMAZ; sayı ne olursa olsun aynı sakin hızda akar. (Eski sabit 28s
             yüzünden 12 haberde çok hızlıydı.) Saf CSS — JS yok. --}}
        <div class="news-ticker-viewport relative flex-1 overflow-hidden">
            <div class="news-ticker-track flex items-center gap-10 py-2.5 pl-6"
                 style="animation-duration: {{ max(50, count($tickerItems) * 7) }}s">
                @foreach (array_merge($tickerItems, $tickerItems) as $it)
                    <a href="{{ $it['url'] }}"
                       class="inline-flex items-center gap-2.5 text-sm font-medium text-primary-100/85 hover:text-white whitespace-nowrap transition-colors">
                        <span class="w-1.5 h-1.5 rounded-full flex-shrink-0 ring-2 ring-white/10" style="background-color: {{ $it['color'] }}"></span>
                        {{ $it['title'] }}
                    </a>
                @endforeach
            </div>
        </div>
        </div>
    </div>
</section>

@once
<style>
    @keyframes news-ticker-scroll {
        from { transform: translateX(0); }
        to   { transform: translateX(-50%); }
    }
    .news-ticker-track {
        width: max-content;
        animation: news-ticker-scroll 28s linear infinite;
        will-change: transform;
    }
    /* Kenarlarda yumuşak fade — haberler sert kesilmek yerine zarifçe belirir/kaybolur (premium his).
       Destekleyen tarayıcı yoksa düz görünür (graceful degradation). */
    .news-ticker-viewport {
        -webkit-mask-image: linear-gradient(to right, transparent 0, #000 5%, #000 95%, transparent 100%);
                mask-image: linear-gradient(to right, transparent 0, #000 5%, #000 95%, transparent 100%);
    }
    .news-ticker-viewport:hover .news-ticker-track { animation-play-state: paused; }
    @media (prefers-reduced-motion: reduce) {
        .news-ticker-track { animation: none; }
    }
</style>
@endonce
@endif
