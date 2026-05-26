<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\FieldOfStudyResource;
use App\Models\FieldOfStudy;
use Illuminate\Http\Request;

class FieldOfStudyController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'q' => 'nullable|string|max:80',
            'with_counts' => 'nullable|boolean',
            'per_page' => 'nullable|integer|min:1|max:200',
        ]);

        $query = FieldOfStudy::query()->where('is_active', true);

        if ($request->boolean('with_counts')) {
            $query->withCount(['programs' => fn ($q) => $q->where('is_active', true)]);
        }

        if (!empty($validated['q'])) {
            $term = '%' . str_replace(['%', '_'], ['\%', '\_'], $validated['q']) . '%';
            $query->where(function ($w) use ($term) {
                $w->where('name_de', 'like', $term)
                    ->orWhere('name_tr', 'like', $term)
                    ->orWhere('name_en', 'like', $term);
            });
        }

        $query->orderBy('sort_order')->orderBy('name_de');

        $perPage = (int) ($validated['per_page'] ?? 50);
        $paginator = $query->paginate($perPage)->withQueryString();

        return FieldOfStudyResource::collection($paginator);
    }

    public function show(string $slugOrId)
    {
        $field = is_numeric($slugOrId)
            ? FieldOfStudy::findOrFail((int) $slugOrId)
            : FieldOfStudy::where('slug', $slugOrId)->firstOrFail();

        return new FieldOfStudyResource($field);
    }
}
