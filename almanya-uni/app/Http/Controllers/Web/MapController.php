<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\State;
use App\Models\University;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MapController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total'    => University::whereNotNull('latitude')->whereNotNull('longitude')->count(),
            'public'   => University::whereNotNull('latitude')->where('type', 'public')->count(),
            'private'  => University::whereNotNull('latitude')->where('type', 'private')->count(),
            'religion' => University::whereNotNull('latitude')->where('type', 'religion')->count(),
        ];

        $states = State::orderBy('name_de')->get(['slug', 'name_tr', 'name_de','name_en','name_de']);

        return view('map.index', compact('stats', 'states'));
    }

    public function universitiesJson(Request $request): JsonResponse
    {
        $query = University::query()
            ->where('is_active', true)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude');

        if ($request->filled('type')) {
            $type = $request->input('type');
            if (in_array($type, ['public', 'private', 'religion', 'applied_sciences', 'art'], true)) {
                $query->where('type', $type);
            }
        }

        if ($request->filled('state')) {
            $stateSlug = $request->input('state');
            $query->whereHas('city.state', fn ($q) => $q->where('slug', $stateSlug));
        }

        // İngilizce program filter
        if ($request->input('english') === '1') {
            $query->whereHas('programs', fn ($p) => $p->where('is_active', 1)->whereIn('language', ['en', 'both']));
        }

        // Boyut filter
        if ($request->filled('size')) {
            match ($request->input('size')) {
                'small'  => $query->where('student_count', '<', 5000),
                'medium' => $query->whereBetween('student_count', [5000, 20000]),
                'large'  => $query->where('student_count', '>', 20000),
                default  => null,
            };
        }

        // Search
        if ($request->filled('q')) {
            $q = $request->input('q');
            $like = '%' . $q . '%';
            $query->where(fn ($w) => $w->where('name_de', 'like', $like)
                ->orWhere('name_en', 'like', $like)
                ->orWhere('short_name', 'like', $like));
        }

        $rows = $query->with('city:id,name_tr,name_en,name_de,slug')
            ->get([
                'id', 'slug', 'name_de', 'short_name', 'type',
                'latitude', 'longitude', 'city_id', 'student_count', 'logo_url',
            ])
            ->map(fn ($u) => [
                'id'    => $u->id,
                'slug'  => $u->slug,
                'name'  => $u->name_de,
                'short' => $u->short_name,
                'type'  => $u->type,
                'lat'   => (float) $u->latitude,
                'lng'   => (float) $u->longitude,
                'city'  => $u->city?->name,
                'students' => $u->student_count,
                'logo'  => $u->logo_url,
            ])
            ->values();

        return response()->json([
            'count' => $rows->count(),
            'items' => $rows,
        ]);
    }
}
