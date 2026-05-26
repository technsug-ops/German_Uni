<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\UniversityResource;
use App\Http\Resources\UniversitySummaryResource;
use App\Models\University;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UniversityController extends Controller
{
    private const ALLOWED_TYPES = ['public', 'private', 'applied_sciences', 'art', 'religion'];
    private const ALLOWED_SORTS = ['name_de', 'name_tr', 'founded_year', 'student_count', 'created_at','name_en','name_de'];

    public function index(Request $request)
    {
        $validated = $request->validate([
            'q' => 'nullable|string|max:100',
            'type' => 'nullable|in:' . implode(',', self::ALLOWED_TYPES),
            'city_id' => 'nullable|integer|exists:cities,id',
            'city' => 'nullable|string|max:80',
            'state' => 'nullable|string|max:80',
            'founded_min' => 'nullable|integer|min:800|max:2100',
            'founded_max' => 'nullable|integer|min:800|max:2100',
            'has_logo' => 'nullable|boolean',
            'has_website' => 'nullable|boolean',
            'has_coordinates' => 'nullable|boolean',
            'official_only' => 'nullable|boolean',
            'sort' => 'nullable|in:' . implode(',', self::ALLOWED_SORTS),
            'order' => 'nullable|in:asc,desc',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        $query = University::query()
            ->where('is_active', true)
            ->with('city:id,slug,name_de,name_tr,name_en,state_id');

        if ($request->boolean('official_only')) {
            $query->where('is_official', true);
        }

        if (!empty($validated['q'])) {
            $term = '%' . str_replace(['%', '_'], ['\%', '\_'], $validated['q']) . '%';
            $query->where(function ($w) use ($term) {
                $w->where('name_de', 'like', $term)
                    ->orWhere('name_tr', 'like', $term)
                    ->orWhere('name_en', 'like', $term)
                    ->orWhere('short_name', 'like', $term);
            });
        }

        if (!empty($validated['type'])) {
            $query->where('type', $validated['type']);
        }

        if (!empty($validated['city_id'])) {
            $query->where('city_id', $validated['city_id']);
        }

        if (!empty($validated['city'])) {
            $query->whereHas('city', fn ($q) => $q->where('slug', $validated['city']));
        }

        if (!empty($validated['founded_min'])) {
            $query->where('founded_year', '>=', $validated['founded_min']);
        }
        if (!empty($validated['founded_max'])) {
            $query->where('founded_year', '<=', $validated['founded_max']);
        }

        if (array_key_exists('has_logo', $validated) && $validated['has_logo'] !== null) {
            $validated['has_logo']
                ? $query->whereNotNull('logo_url')
                : $query->whereNull('logo_url');
        }
        if (array_key_exists('has_website', $validated) && $validated['has_website'] !== null) {
            $validated['has_website']
                ? $query->whereNotNull('website_url')
                : $query->whereNull('website_url');
        }
        if (array_key_exists('has_coordinates', $validated) && $validated['has_coordinates'] !== null) {
            $validated['has_coordinates']
                ? $query->whereNotNull('latitude')
                : $query->whereNull('latitude');
        }

        $sort = $validated['sort'] ?? 'name_de';
        $order = $validated['order'] ?? 'asc';
        $query->orderBy($sort, $order);

        $perPage = (int) ($validated['per_page'] ?? 20);
        $paginator = $query->paginate($perPage)->withQueryString();

        return UniversitySummaryResource::collection($paginator);
    }

    public function show(string $slugOrId)
    {
        $query = University::query()->with('city.state');

        $university = is_numeric($slugOrId)
            ? $query->findOrFail((int) $slugOrId)
            : $query->where('slug', $slugOrId)->firstOrFail();

        return new UniversityResource($university);
    }

    /**
     * Dedicated content_blocks endpoint — 3rd party platforms için zengin sayfa içeriği.
     */
    public function content(string $slugOrId): JsonResponse
    {
        $query = University::query()->with('city.state');
        $uni = is_numeric($slugOrId)
            ? $query->findOrFail((int) $slugOrId)
            : $query->where('slug', $slugOrId)->firstOrFail();

        $blocks = $uni->content_blocks ?? [];

        return response()->json([
            'data' => [
                'id' => $uni->id,
                'slug' => $uni->slug,
                'name' => $uni->name_de,
                'short_name' => $uni->short_name,
                'image_url' => $uni->image_url,
                'logo_url' => $uni->logo_url,
                'type' => $uni->type,
                'city' => $uni->city?->name_de,
                'state' => $uni->city?->state?->name_de,
                'last_enriched_at' => $uni->last_enriched_at?->toIso8601String(),
                'blocks_count' => count($blocks),
                'block_types' => array_values(array_unique(array_column($blocks, 'type'))),
                'content_blocks' => $blocks,
            ],
            'links' => [
                'web' => url('/universities/' . $uni->slug),
                'api' => url('/api/v1/universities/' . $uni->slug),
            ],
        ]);
    }

    public function top(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'metric' => 'nullable|in:students,age,founded_year',
            'limit' => 'nullable|integer|min:1|max:100',
            'type' => 'nullable|in:' . implode(',', self::ALLOWED_TYPES),
        ]);

        $metric = $validated['metric'] ?? 'students';
        $limit = (int) ($validated['limit'] ?? 10);

        $query = University::query()
            ->where('is_active', true)
            ->with('city:id,slug,name_de,name_tr,name_en');

        if (!empty($validated['type'])) {
            $query->where('type', $validated['type']);
        }

        match ($metric) {
            'students' => $query->whereNotNull('student_count')->orderByDesc('student_count'),
            'age', 'founded_year' => $query->whereNotNull('founded_year')->orderBy('founded_year'),
        };

        $results = $query->limit($limit)->get();

        return response()->json([
            'metric' => $metric,
            'count' => $results->count(),
            'data' => UniversitySummaryResource::collection($results),
        ]);
    }

    public function compare(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ids' => 'required|string',
        ]);

        $ids = collect(explode(',', $validated['ids']))
            ->map(fn ($v) => (int) trim($v))
            ->filter(fn ($v) => $v > 0)
            ->take(5)
            ->values()
            ->all();

        if (count($ids) < 2) {
            return response()->json([
                'message' => 'Karşılaştırma için en az 2 üniversite ID gerekli.',
            ], 422);
        }

        $universities = University::query()
            ->with('city.state')
            ->whereIn('id', $ids)
            ->get();

        return response()->json([
            'requested_ids' => $ids,
            'count' => $universities->count(),
            'data' => UniversityResource::collection($universities),
        ]);
    }
}
