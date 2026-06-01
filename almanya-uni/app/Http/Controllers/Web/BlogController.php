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
        $filters = $this->parseFilters($request);

        $query = Post::published()->blogType()
            ->with(['author:id,name,slug,avatar_url,role_label,bio,social_links', 'coAuthor:id,name,slug,avatar_url,role_label', 'category']);

        $this->applyFilters($query, $filters);

        $paginator = $this->applySort($query, $filters['sort'])
            ->paginate(self::PER_PAGE)
            ->withQueryString();

        return view('blog.index', [
            'posts' => $paginator,
            'categories' => $this->sidebarCategories(),
            'authorsList' => $this->authorsForFilter(),
            'filters' => $filters,
            'searchQ' => $filters['q'],
            'page_title' => $filters['q'] !== '' ? __('Search:') . ' ' . $filters['q'] : __('Blog'),
            'page_description' => $filters['q'] !== ''
                ? __('Blog posts matching ":q"', ['q' => $filters['q']])
                : __('Guides on studying in Germany — university applications, language tests, student life and more.'),
        ]);
    }

    /**
     * @return array{q:string,author:?string,sort:string,length:?string}
     */
    private function parseFilters(Request $request): array
    {
        $sort = (string) $request->query('sort', 'newest');
        if (! in_array($sort, ['newest', 'oldest', 'popular'], true)) {
            $sort = 'newest';
        }

        $length = $request->query('length');
        if (! in_array($length, ['short', 'medium', 'long'], true)) {
            $length = null;
        }

        return [
            'q'      => trim((string) $request->query('q', '')),
            'author' => $request->query('author') ?: null,   // user.slug
            'sort'   => $sort,
            'length' => $length,
        ];
    }

    private function applyFilters($query, array $filters): void
    {
        if ($filters['q'] !== '' && mb_strlen($filters['q']) >= 2) {
            $q = $filters['q'];
            $query->where(function ($w) use ($q) {
                $w->where('title', 'like', '%' . $q . '%')
                  ->orWhere('excerpt', 'like', '%' . $q . '%')
                  ->orWhere('content_md', 'like', '%' . $q . '%');
            });
        }

        if ($filters['author']) {
            $authorId = \App\Models\User::where('slug', $filters['author'])->value('id');
            if ($authorId) {
                $query->where(function ($w) use ($authorId) {
                    $w->where('user_id', $authorId)
                      ->orWhere('co_author_id', $authorId);
                });
            }
        }

        if ($filters['length'] === 'short') {
            $query->where('reading_minutes', '<=', 5);
        } elseif ($filters['length'] === 'medium') {
            $query->whereBetween('reading_minutes', [6, 15]);
        } elseif ($filters['length'] === 'long') {
            $query->where('reading_minutes', '>', 15);
        }
    }

    private function applySort($query, string $sort)
    {
        return match ($sort) {
            'oldest'  => $query->orderBy('published_at'),
            'popular' => $query->orderByDesc('view_count')->orderByDesc('published_at'),
            default   => $query->orderByDesc('published_at'),
        };
    }

    private function authorsForFilter()
    {
        return \App\Models\User::where(function ($q) {
                $q->where('is_author', true)->orWhere('is_editor', true)->orWhere('is_admin', true);
            })
            ->whereNotNull('slug')
            ->withCount(['posts' => fn ($q) => $q->where('is_published', true)->where('locale', app()->getLocale())])
            ->having('posts_count', '>', 0)
            ->orderByDesc('posts_count')
            ->get(['id', 'name', 'slug', 'avatar_url', 'role_label']);
    }

    public function show(string $slug): View
    {
        $post = Post::published()
            ->with(['author:id,name,slug,avatar_url,role_label,bio,social_links', 'category', 'approvedComments'])
            ->where('slug', $slug)
            ->firstOrFail();

        Post::where('id', $post->id)->increment('view_count');

        // Dil değiştirici + hreflang: her dilin GERÇEK slug'ına URL (slug locale'e göre
        // farklı: tr=base, en=base-en, de=base-de → naif prefix-swap 404 verirdi).
        if ($post->translation_group_id) {
            $localeUrls = Post::blogType()
                ->where('translation_group_id', $post->translation_group_id)
                ->where('is_published', true)->whereNotNull('published_at')->where('published_at', '<=', now())
                ->get(['locale', 'slug'])
                ->mapWithKeys(fn ($s) => [$s->locale => url($s->locale . '/blog/' . $s->slug)])
                ->all();
            view()->share('localeUrls', $localeUrls);
        }

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

    /**
     * Yorum gönder — anonim veya login. pending status, admin onayından sonra public.
     */
    public function storeComment(\Illuminate\Http\Request $request, string $slug): \Illuminate\Http\RedirectResponse
    {
        $post = Post::published()->where('slug', $slug)->firstOrFail();

        $data = $request->validate([
            'body'         => 'required|string|min:10|max:3000',
            'parent_id'    => 'nullable|integer|exists:post_comments,id',
            'author_name'  => 'nullable|string|max:80',
            'author_email' => 'nullable|email|max:150',
            'website'      => 'nullable|string|max:200', // honeypot — boş olmalı
        ]);

        // Honeypot: bot dolduruysa sessizce yut
        if (! empty($data['website'])) {
            return back()->with('comment_status', __('Thanks for your comment — it\'s pending review.'));
        }

        $user = $request->user();
        if (! $user) {
            $request->validate([
                'author_name'  => 'required|string|min:2|max:80',
                'author_email' => 'required|email|max:150',
            ]);
        }

        \App\Models\PostComment::create([
            'post_id'      => $post->id,
            'user_id'      => $user?->id,
            'parent_id'    => $data['parent_id'] ?? null,
            'author_name'  => $user ? null : ($data['author_name'] ?? null),
            'author_email' => $user ? null : ($data['author_email'] ?? null),
            'body'         => $data['body'],
            'status'       => 'pending',
            'ip_address'   => $request->ip(),
            'user_agent'   => substr((string) $request->userAgent(), 0, 255),
        ]);

        return back()
            ->with('comment_status', __('Thanks for your comment — it\'s pending review.'))
            ->withFragment('comments');
    }

    public function category(string $slug, Request $request): View
    {
        $category = Category::active()->where('slug', $slug)
            ->where(fn ($w) => $w->where('kind', 'blog')->orWhereNull('kind'))
            ->firstOrFail();
        $filters = $this->parseFilters($request);

        $query = Post::published()->blogType()
            ->where('category_id', $category->id)
            ->with(['author:id,name,slug,avatar_url,role_label,bio,social_links', 'coAuthor:id,name,slug,avatar_url,role_label', 'category']);

        $this->applyFilters($query, $filters);

        $paginator = $this->applySort($query, $filters['sort'])
            ->paginate(self::PER_PAGE)
            ->withQueryString();

        return view('blog.index', [
            'posts' => $paginator,
            'categories' => $this->sidebarCategories(),
            'active_category' => $category,
            'authorsList' => $this->authorsForFilter(),
            'filters' => $filters,
            'searchQ' => $filters['q'],
            'page_title' => $filters['q'] !== '' ? __('Search in :cat:', ['cat' => $category->name]) . ' ' . $filters['q'] : $category->name,
            'page_description' => $category->description
                ?: ($category->name . ' kategorisindeki tüm yazılar.'),
        ]);
    }

    private function sidebarCategories()
    {
        $publishedCount = fn ($q) => $q->where('is_published', true)->whereNotNull('published_at');

        return Category::active()
            ->where(fn ($w) => $w->where('kind', 'blog')->orWhereNull('kind'))
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
