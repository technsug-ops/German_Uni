<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\MenuPage;
use App\Models\Post;
use Illuminate\View\View;

/**
 * "Almanya'dan" haber akışı — type='news' Post'lar.
 * Sıralama: admin önceliği (varsa) → en yeni en önde (Post::scopeNewsOrder).
 *
 * MODÜL TOGGLE: Admin → Menü Sayfa Yönetimi → "Haberler" kapatılırsa modül
 * tamamen kapanır — menüden gizlenir (otomatik) + tüm /haberler URL'leri 404.
 */
class NewsController extends Controller
{
    private const PER_PAGE = 12;

    /** Modül admin'den kapatıldıysa tüm haber sayfaları 404. */
    private function ensureModuleEnabled(): void
    {
        abort_unless(MenuPage::isKeyEnabled('news.index'), 404);
    }

    public function index(): View
    {
        $this->ensureModuleEnabled();

        $posts = Post::published()->news()->newsOrder()
            ->with(['category', 'author:id,name,slug,avatar_url'])
            ->paginate(self::PER_PAGE);

        return view('news.index', [
            'posts'      => $posts,
            'categories' => $this->newsCategories(),
            'activeCategory' => null,
            'page_title' => __('News from Germany'),
            'page_description' => __('Visa, education law, universities and student life in Germany — what matters for international applicants.'),
        ]);
    }

    public function category(string $slug): View
    {
        $this->ensureModuleEnabled();
        $category = Category::where('kind', 'news')->where('slug', $slug)->firstOrFail();

        $posts = Post::published()->news()->newsOrder()
            ->where('category_id', $category->id)
            ->with(['category', 'author:id,name,slug,avatar_url'])
            ->paginate(self::PER_PAGE);

        return view('news.index', [
            'posts'      => $posts,
            'categories' => $this->newsCategories(),
            'activeCategory' => $category,
            'page_title' => $category->name,
            'page_description' => $category->description ?: $category->name,
        ]);
    }

    public function show(string $slug): View
    {
        $this->ensureModuleEnabled();
        $post = Post::published()->news()
            ->with(['category', 'author:id,name,slug,avatar_url,role_label,bio,social_links'])
            ->where('slug', $slug)
            ->firstOrFail();

        Post::where('id', $post->id)->increment('view_count');

        $related = Post::published()->news()->newsOrder()
            ->when($post->category_id, fn ($q) => $q->where('category_id', $post->category_id))
            ->where('id', '!=', $post->id)
            ->limit(4)
            ->get(['id', 'slug', 'title', 'excerpt', 'featured_image', 'published_at', 'category_id']);

        return view('news.show', [
            'post'       => $post,
            'related'    => $related,
            'categories' => $this->newsCategories(),
        ]);
    }

    private function newsCategories()
    {
        return Category::active()->where('kind', 'news')
            ->withCount(['posts' => fn ($q) => $q->where('type', 'news')->where('is_published', true)->where('locale', app()->getLocale())])
            ->orderBy('sort_order')
            ->get(['id', 'name', 'name_tr', 'name_en', 'name_de', 'slug', 'color']);
    }
}
