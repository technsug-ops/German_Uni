<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\BlockedAccountProviderResource;
use App\Models\BlockedAccountProvider;
use Illuminate\Http\Request;

class BlockedAccountController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'q' => 'nullable|string|max:100',
            'combo_insurance' => 'nullable|boolean',
            'bafin_licensed' => 'nullable|boolean',
            'sort' => 'nullable|in:setup_fee_eur,monthly_fee_eur,yearly_fee_eur,activation_days_min,sort_order',
            'order' => 'nullable|in:asc,desc',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        $query = BlockedAccountProvider::query()->published();

        if (array_key_exists('combo_insurance', $validated) && $validated['combo_insurance'] !== null) {
            $query->where('combo_insurance', $request->boolean('combo_insurance'));
        }
        if (array_key_exists('bafin_licensed', $validated) && $validated['bafin_licensed'] !== null) {
            $query->where('bafin_licensed', $request->boolean('bafin_licensed'));
        }

        if (! empty($validated['q'])) {
            $term = '%' . str_replace(['%', '_'], ['\%', '\_'], $validated['q']) . '%';
            $query->where('name', 'like', $term);
        }

        if (! empty($validated['sort'])) {
            $query->orderBy($validated['sort'], $validated['order'] ?? 'asc');
        } else {
            $query->orderByDesc('is_featured')->orderBy('sort_order');
        }

        $perPage = (int) ($validated['per_page'] ?? 30);

        return BlockedAccountProviderResource::collection($query->paginate($perPage)->withQueryString());
    }

    public function show(string $slugOrId)
    {
        $query = BlockedAccountProvider::query()->published();
        $provider = is_numeric($slugOrId)
            ? $query->findOrFail((int) $slugOrId)
            : $query->where('slug', $slugOrId)->firstOrFail();

        return new BlockedAccountProviderResource($provider);
    }
}
