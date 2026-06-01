<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\BlogPostResource;
use App\Http\Resources\BlogPostSummaryResource;
use App\Models\Post;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    private function locale(Request $request): string
    {
        return in_array($request->query('lang'), ['tr', 'en', 'de'], true) ? $request->query('lang') : 'en';
    }

    public function index(Request $request)
    {
        $validated = $request->validate([
            'q' => 'nullable|string|max:100',
            'lang' => 'nullable|in:tr,en,de',
            'category' => 'nullable|string|max:80',
            'sort' => 'nullable|in:published_at,view_count',
            'order' => 'nullable|in:asc,desc',
            'per_page' => 'nullable|integer|min:1|max:50',
        ]);

        $locale = $this->locale($request);

        $query = Post::query()
            ->where('is_published', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->where('locale', $locale)
            ->with('category:id,slug,name_tr,name_en,name_de');

        if (! empty($validated['q'])) {
            $term = '%' . str_replace(['%', '_'], ['\%', '\_'], $validated['q']) . '%';
            $query->where(function ($w) use ($term) {
                $w->where('title', 'like', $term)->orWhere('excerpt', 'like', $term);
            });
        }

        if (! empty($validated['category'])) {
            $query->whereHas('category', fn ($c) => $c->where('slug', $validated['category']));
        }

        $sort = $validated['sort'] ?? 'published_at';
        $order = $validated['order'] ?? 'desc';
        $query->orderBy($sort, $order);

        $perPage = (int) ($validated['per_page'] ?? 15);

        return BlogPostSummaryResource::collection($query->paginate($perPage)->withQueryString());
    }

    public function show(Request $request, string $slugOrId)
    {
        $query = Post::query()
            ->where('is_published', true)
            ->with(['category:id,slug,name_tr,name_en,name_de', 'author:id,name', 'translations:id,translation_group_id,locale']);

        $post = is_numeric($slugOrId)
            ? $query->findOrFail((int) $slugOrId)
            : $query->where('slug', $slugOrId)->firstOrFail();

        return new BlogPostResource($post);
    }
}
