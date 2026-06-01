<?php

namespace App\Http\Controllers\Api\FlatReklam;

use App\Http\Controllers\Controller;
use App\Services\FlatReklam\SeoResourceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * FlatReklam "Özel Site SEO API" (v1) — sağlayıcı uçları.
 * Sözleşme: doc FlatReklam v1 (ping / seo-resources / {id} / PATCH).
 * Auth: flatreklam.auth middleware (Bearer = setting('flatreklam_api_token')).
 */
class SeoController extends Controller
{
    public function __construct(private SeoResourceService $service)
    {
    }

    public function ping(): JsonResponse
    {
        return response()->json($this->service->siteInfo());
    }

    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => 'nullable|string|max:40',
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:100',
            'lang' => 'nullable|in:tr,en,de',
            'status' => 'nullable|in:published,draft,all',
        ]);

        $result = $this->service->list(
            $validated['type'] ?? null,
            (int) ($validated['page'] ?? 1),
            (int) ($validated['per_page'] ?? 50),
            $validated['lang'] ?? null,
            $validated['status'] ?? 'published',
        );

        return response()->json($result);
    }

    public function show(Request $request, string $id): JsonResponse
    {
        $lang = $request->query('lang');
        $resource = $this->service->find($id, in_array($lang, ['tr', 'en', 'de'], true) ? $lang : null);

        if (! $resource) {
            return response()->json(['error' => 'Kaynak bulunamadı', 'code' => 'NOT_FOUND'], 404);
        }

        return response()->json($resource);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $data = $request->validate([
            'seoTitle' => 'sometimes|nullable|string|max:255',
            'seoDescription' => 'sometimes|nullable|string|max:500',
            'focusKeyword' => 'sometimes|nullable|string|max:120',
            'lang' => 'sometimes|nullable|in:tr,en,de',
        ]);

        [$resource, $error] = $this->service->update($id, $data);

        if ($error) {
            return response()->json($error, $error['code'] === 'NOT_FOUND' ? 404 : 422);
        }

        return response()->json($resource);
    }
}
