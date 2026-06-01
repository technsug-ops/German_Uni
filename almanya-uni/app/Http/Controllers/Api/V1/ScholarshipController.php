<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ScholarshipResource;
use App\Http\Resources\ScholarshipSummaryResource;
use App\Models\Scholarship;
use Illuminate\Http\Request;

class ScholarshipController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'q' => 'nullable|string|max:100',
            'daad_only' => 'nullable|boolean',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        $query = Scholarship::query()->active();

        if ($request->boolean('daad_only')) {
            $query->where('is_daad', true);
        }

        if (! empty($validated['q'])) {
            $term = '%' . str_replace(['%', '_'], ['\%', '\_'], $validated['q']) . '%';
            $query->where(function ($w) use ($term) {
                $w->where('name_de', 'like', $term)
                  ->orWhere('name_en', 'like', $term);
            });
        }

        $query->orderBy('sorting')->orderBy('name_en');

        $perPage = (int) ($validated['per_page'] ?? 20);

        return ScholarshipSummaryResource::collection($query->paginate($perPage)->withQueryString());
    }

    public function show(string $slugOrId)
    {
        $query = Scholarship::query()->active();
        $scholarship = is_numeric($slugOrId)
            ? $query->findOrFail((int) $slugOrId)
            : $query->where('slug', $slugOrId)->firstOrFail();

        return new ScholarshipResource($scholarship);
    }
}
