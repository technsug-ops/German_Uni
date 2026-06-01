<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\HousingProviderResource;
use App\Models\HousingProvider;
use Illuminate\Http\Request;

class HousingProviderController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'q' => 'nullable|string|max:100',
            'type' => 'nullable|string|max:30',
            'city' => 'nullable|string|max:80',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        $query = HousingProvider::query()->active();

        if (! empty($validated['type'])) {
            $query->where('type', $validated['type']);
        }

        if (! empty($validated['q'])) {
            $term = '%' . str_replace(['%', '_'], ['\%', '\_'], $validated['q']) . '%';
            $query->where('name', 'like', $term);
        }

        if (! empty($validated['city'])) {
            $query->where('cities', 'like', '%' . str_replace(['%', '_'], ['\%', '\_'], $validated['city']) . '%');
        }

        $query->orderByDesc('is_featured')->orderBy('sort_order')->orderBy('name');

        $perPage = (int) ($validated['per_page'] ?? 30);

        return HousingProviderResource::collection($query->paginate($perPage)->withQueryString());
    }

    public function show(string $slugOrId)
    {
        $query = HousingProvider::query()->active();
        $provider = is_numeric($slugOrId)
            ? $query->findOrFail((int) $slugOrId)
            : $query->where('slug', $slugOrId)->firstOrFail();

        return new HousingProviderResource($provider);
    }
}
