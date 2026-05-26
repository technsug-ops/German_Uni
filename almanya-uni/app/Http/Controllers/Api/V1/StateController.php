<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\CityResource;
use App\Http\Resources\StateResource;
use App\Models\State;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StateController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'q' => 'nullable|string|max:100',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        $query = State::query()
            ->where('is_active', true)
            ->withCount('cities')
            ->orderBy('name_de');

        if (!empty($validated['q'])) {
            $term = '%' . str_replace(['%', '_'], ['\%', '\_'], $validated['q']) . '%';
            $query->where(function ($w) use ($term) {
                $w->where('name_de', 'like', $term)
                    ->orWhere('name_tr', 'like', $term);
            });
        }

        $perPage = (int) ($validated['per_page'] ?? 50);
        return StateResource::collection($query->paginate($perPage)->withQueryString());
    }

    public function show(string $slugOrId): JsonResponse
    {
        $query = State::query()->withCount('cities');

        $state = is_numeric($slugOrId)
            ? $query->findOrFail((int) $slugOrId)
            : $query->where('slug', $slugOrId)->firstOrFail();

        $cities = $state->cities()
            ->withCount('universities')
            ->orderBy('name_de')
            ->get();

        return response()->json([
            'data' => (new StateResource($state))->resolve(),
            'cities' => CityResource::collection($cities)->resolve(),
        ]);
    }
}
