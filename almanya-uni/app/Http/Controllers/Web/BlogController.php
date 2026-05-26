<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlogController extends Controller
{
    private const PER_PAGE = 10;

    public function index(Request $request): View
    {
        $paginator = Post::published()
            ->with(['author:id,name,avatar_url,role_label,bio,social_links', 'category:id,name,slug,color'])
            ->orderByDesc('published_at')
            ->paginate(self::PER_PAGE)
            ->withQueryString();

        return view('blog.index', [
            'posts' => $paginator,
            'categories' => $this->sidebarCategories(),
            'page_title' => __('Blog'),
            'page_description' => __('Guides on studying in Germany — university applications, language tests, student life and more.'),
        ]);
    }

    public function show(string $slug): View
    {
        $post = Post::published()
            ->with(['author:id,name,avatar_url,role_label,bio,social_links', 'category:id,name,slug,color'])
            ->where('slug', $slug)
            ->firstOrFail();

        Post::where('id', $post->id)->increment('view_count');

        $related = $post->category_id
            ? Post::published()
                ->where('category_id', $post->category_id)
                ->where('id', '!=', $post->id)
                ->orderByDesc('published_at')
                ->limit(3)
                ->get(['id', 'slug', 'title', 'excerpt', 'reading_minutes', 'published_at'])
            : collect();

        return view('blog.show', [
            'post' => $post,
            'related' => $related,
            'categories' => $this->sidebarCategories(),
        ]);
    }

    public function category(string $slug): View
    {
        $category = Category::active()->where('slug', $slug)->firstOrFail();

        $paginator = Post::published()
            ->where('category_id', $category->id)
            ->with(['author:id,name,avatar_url,role_label,bio,social_links', 'category:id,name,slug,color'])
            ->orderByDesc('published_at')
            ->paginate(self::PER_PAGE)
            ->withQueryString();

        return view('blog.index', [
            'posts' => $paginator,
            'categories' => $this->sidebarCategories(),
            'active_category' => $category,
            'page_title' => $category->name,
            'page_description' => $category->description
                ?: ($category->name . ' kategorisindeki tüm yazılar.'),
        ]);
    }

    private function sidebarCategories()
    {
        $publishedCount = fn ($q) => $q->where('is_published', true)->whereNotNull('published_at');

        return Category::active()
            ->topLevel()
            ->withCount(['posts' => $publishedCount])
            ->with(['children' => fn ($q) => $q->active()
                ->withCount(['posts' => $publishedCount])
                ->orderBy('sort_order')])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'parent_id', 'name', 'slug', 'color']);
    }

    /**
     * Post helpful/unhelpful oy — bir cookie/localStorage anahtarı çift oy'u engeller.
     * Counter atomik artırılır.
     */
    public function feedback(Request $request)
    {
        $data = $request->validate([
            'post_id' => ['required', 'integer', 'exists:posts,id'],
            'vote'    => ['required', 'in:up,down'],
        ]);

        $column = $data['vote'] === 'up' ? 'helpful_count' : 'unhelpful_count';
        Post::where('id', $data['post_id'])->increment($column);

        return response()->json(['ok' => true]);
    }

    /**
     * Okuma derinliği + süre takibi. sendBeacon ile gelir (sayfa kapanırken bile).
     * Session başına tek kayıt — scroll max'a, süre toplama yükselir.
     */
    public function engagement(Request $request)
    {
        $data = $request->validate([
            'post_id' => ['required', 'integer', 'exists:posts,id'],
            'scroll'  => ['required', 'integer', 'min:0', 'max:100'],
            'seconds' => ['required', 'integer', 'min:0', 'max:7200'],
        ]);

        // Bot'ları atla (page_views ile aynı mantık — basit UA kontrolü)
        $ua = (string) $request->userAgent();
        if (preg_match('/bot|crawl|spider|slurp|bing|google/i', $ua)) {
            return response()->json(['ok' => true]);
        }

        $sessionId = substr(hash('sha256', $request->session()->getId() ?: $request->ip()), 0, 64);

        $eng = \App\Models\PostEngagement::firstOrNew([
            'post_id'    => $data['post_id'],
            'session_id' => $sessionId,
        ]);
        // Scroll ve süre yalnızca artar (max) — aynı oturumdaki birden fazla beacon birikmesin
        $eng->scroll_depth = max($eng->scroll_depth ?? 0, $data['scroll']);
        $eng->seconds      = max($eng->seconds ?? 0, $data['seconds']);
        $eng->completed    = $eng->scroll_depth >= 90;
        $eng->save();

        return response()->json(['ok' => true]);
    }
}
