<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProfessionResource;
use App\Models\Profession;
use Illuminate\Http\Request;

class ProfessionController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'q' => 'nullable|string|max:120',
            'cluster' => 'nullable|string|max:80',
            'field_id' => 'nullable|integer|exists:fields_of_study,id',
            'field_slug' => 'nullable|string|max:120',
            'type' => 'nullable|string|max:40',
            'sort' => 'nullable|in:name_de,name_tr,kldb_code,created_at',
            'order' => 'nullable|in:asc,desc',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        $query = Profession::query()
            ->where('is_active', true)
            ->with('field:id,slug,name_de');

        if (!empty($validated['q'])) {
            $term = '%' . str_replace(['%', '_'], ['\%', '\_'], $validated['q']) . '%';
            $query->where(function ($w) use ($term) {
                $w->where('name_de', 'like', $term)
                    ->orWhere('name_tr', 'like', $term)
                    ->orWhere('short_name', 'like', $term);
            });
        }

        if (!empty($validated['cluster'])) {
            $query->where('cluster', $validated['cluster']);
        }
        if (!empty($validated['field_id'])) {
            $query->where('field_of_study_id', $validated['field_id']);
        }
        if (!empty($validated['field_slug'])) {
            $query->whereHas('field', fn ($q) => $q->where('slug', $validated['field_slug']));
        }
        if (!empty($validated['type'])) {
            $query->where('type', $validated['type']);
        }

        $sort = $validated['sort'] ?? 'name_de';
        $order = $validated['order'] ?? 'asc';
        $query->orderBy($sort, $order);

        $perPage = (int) ($validated['per_page'] ?? 30);
        $paginator = $query->paginate($perPage)->withQueryString();

        return ProfessionResource::collection($paginator);
    }

    public function show(string $slugOrId, Request $request)
    {
        $query = Profession::query()->with('field');

        $profession = is_numeric($slugOrId)
            ? $query->findOrFail((int) $slugOrId)
            : $query->where('slug', $slugOrId)->firstOrFail();

        $request->merge(['full' => true]);

        return new ProfessionResource($profession);
    }
}
