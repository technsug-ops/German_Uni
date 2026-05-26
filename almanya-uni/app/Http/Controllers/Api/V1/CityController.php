<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\CityResource;
use App\Http\Resources\UniversitySummaryResource;
use App\Models\City;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CityController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'q' => 'nullable|string|max:100',
            'state_id' => 'nullable|integer|exists:states,id',
            'with_unis_only' => 'nullable|boolean',
            'per_page' => 'nullable|integer|min:1|max:200',
        ]);

        $query = City::query()
            ->where('is_active', true)
            ->withCount('universities')
            ->with('state:id,slug,name_de')
            ->orderBy('name_de');

        if (!empty($validated['q'])) {
            $term = '%' . str_replace(['%', '_'], ['\%', '\_'], $validated['q']) . '%';
            $query->where(function ($w) use ($term) {
                $w->where('name_de', 'like', $term)
                    ->orWhere('name_tr', 'like', $term);
            });
        }

        if (!empty($validated['state_id'])) {
            $query->where('state_id', $validated['state_id']);
        }

        if (!empty($validated['with_unis_only'])) {
            $query->has('universities');
        }

        $perPage = (int) ($validated['per_page'] ?? 50);
        return CityResource::collection($query->paginate($perPage)->withQueryString());
    }

    public function show(string $slugOrId): JsonResponse
    {
        $query = City::query()
            ->with('state')
            ->withCount('universities');

        $city = is_numeric($slugOrId)
            ? $query->findOrFail((int) $slugOrId)
            : $query->where('slug', $slugOrId)->firstOrFail();

        $universities = $city->universities()
            ->where('is_active', true)
            ->orderBy('name_de')
            ->get();

        return response()->json([
            'data' => (new CityResource($city))->resolve(),
            'universities' => UniversitySummaryResource::collection($universities)->resolve(),
        ]);
    }

    /**
     * Dedicated content_blocks endpoint — 3rd party platforms için zengin sayfa içeriği.
     * Yanıt: blok listesi + meta (sayı, son güncelleme).
     */
    public function content(string $slugOrId): JsonResponse
    {
        $query = City::query()->with('state');
        $city = is_numeric($slugOrId)
            ? $query->findOrFail((int) $slugOrId)
            : $query->where('slug', $slugOrId)->firstOrFail();

        $blocks = $city->content_blocks ?? [];

        return response()->json([
            'data' => [
                'id' => $city->id,
                'slug' => $city->slug,
                'name' => $city->name_de,
                'image_url' => $city->image_url,
                'state' => $city->state?->name_de,
                'last_enriched_at' => $city->last_enriched_at?->toIso8601String(),
                'blocks_count' => count($blocks),
                'block_types' => array_values(array_unique(array_column($blocks, 'type'))),
                'content_blocks' => $blocks,
            ],
            'links' => [
                'web' => url('/cities/' . $city->slug),
                'api' => url('/api/v1/cities/' . $city->slug),
            ],
        ]);
    }
}
