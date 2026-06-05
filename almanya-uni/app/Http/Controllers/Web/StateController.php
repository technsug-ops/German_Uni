<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\FieldOfStudy;
use App\Models\Post;
use App\Models\State;
use App\Models\University;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StateController extends Controller
{
    public function index(Request $request): View
    {
        $q = State::query()->withCount('cities');

        // Bölge filtresi (kuzey/güney/batı/doğu)
        if ($request->filled('region') && in_array($request->input('region'), ['nord', 'sued', 'west', 'ost'], true)) {
            $q->where('region', $request->input('region'));
        }

        // Sıralama
        $sort = $request->input('sort', 'name');
        $q = match ($sort) {
            'population' => $q->orderByDesc('population'),
            'uni_count'  => $q,  // sonra elle sıralanır
            default      => $q->orderBy('name_de'),
        };

        $states = $q->get();

        // Eyalete göre üni sayıları
        $uniCountsByState = University::where('universities.is_active', 1)
            ->join('cities', 'universities.city_id', '=', 'cities.id')
            ->selectRaw('cities.state_id, COUNT(*) as cnt')
            ->groupBy('cities.state_id')
            ->pluck('cnt', 'state_id');

        $states->each(function ($s) use ($uniCountsByState) {
            $s->setAttribute('uni_count', (int) ($uniCountsByState[$s->id] ?? 0));
        });

        // uni_count sıralaması Collection üzerinde
        if ($sort === 'uni_count') {
            $states = $states->sortByDesc('uni_count')->values();
        }

        return view('states.index', [
            'states'  => $states,
            'filters' => $request->only(['region', 'sort']),
        ]);
    }

    public function show(string $slug): View
    {
        $state = State::where('slug', $slug)->firstOrFail();

        $cities = City::where('state_id', $state->id)
            ->withCount(['universities' => fn ($q) => $q->where('is_active', 1)])
            ->having('universities_count', '>', 0)
            ->orderByDesc('universities_count')
            ->take(20)
            ->get();

        $topUnis = University::whereHas('city', fn ($q) => $q->where('state_id', $state->id))
            ->where('is_active', 1)
            ->orderByDesc('student_count')
            ->take(12)
            ->get();

        $totals = [
            'cities' => City::where('state_id', $state->id)->count(),
            'cities_with_unis' => City::where('state_id', $state->id)
                ->whereHas('universities', fn ($q) => $q->where('is_active', 1))->count(),
            'unis' => University::whereHas('city', fn ($q) => $q->where('state_id', $state->id))
                ->where('is_active', 1)->count(),
            'public' => University::whereHas('city', fn ($q) => $q->where('state_id', $state->id))
                ->where('is_active', 1)->where('type', 'public')->count(),
            'private' => University::whereHas('city', fn ($q) => $q->where('state_id', $state->id))
                ->where('is_active', 1)->where('type', 'private')->count(),
        ];

        // ⚠️ Bayat istatistik düzeltmesi: content_blocks içindeki 'universities_in_city'
        // bloğu enrichment ANINDA hesaplanmış sayıları gömüyor. Sonradan ~üni pasifleşti
        // (986→645 temizliği) → kart canlı sayı (52) gösterirken blok bayat (72) kalıyordu.
        // Render anında CANLI sayıyla override et → kart ile detay her zaman uyumlu.
        $liveTopUniNames = $topUnis->take(8)->pluck('name_de')->filter()->values()->all();
        $stateBlocks = collect($state->localizedBlocks() ?? [])->map(function ($block) use ($totals, $liveTopUniNames) {
            if (($block['type'] ?? null) === 'universities_in_city') {
                $block['total']   = $totals['unis'];
                $block['public']  = $totals['public'];
                $block['private'] = $totals['private'];
                if (! empty($liveTopUniNames)) {
                    $block['top_unis'] = $liveTopUniNames;
                }
            }
            return $block;
        })->all();

        // Komşu eyaletler — coğrafi mesafeye göre (haversine)
        $otherStates = State::query()
            ->where('id', '!=', $state->id);
        if ($state->latitude && $state->longitude) {
            // Haversine formula — yaklaşık km mesafe
            $otherStates->selectRaw("*,
                (6371 * acos(
                    cos(radians(?)) * cos(radians(latitude)) *
                    cos(radians(longitude) - radians(?)) +
                    sin(radians(?)) * sin(radians(latitude))
                )) AS distance_km",
                [(float) $state->latitude, (float) $state->longitude, (float) $state->latitude])
                ->orderBy('distance_km');
        } else {
            $otherStates->orderBy('name_de');
        }
        $otherStates = $otherStates->take(6)->get();

        // Top 5 alan — bu eyalette en çok program sunan alanlar
        $stateUniIds = University::whereHas('city', fn ($q) => $q->where('state_id', $state->id))
            ->where('is_active', 1)
            ->pluck('id');

        $topFields = FieldOfStudy::active()
            ->whereHas('programs', fn ($q) => $q->whereIn('university_id', $stateUniIds)->where('is_active', 1))
            ->withCount(['programs' => fn ($q) => $q->whereIn('university_id', $stateUniIds)->where('is_active', 1)])
            ->orderByDesc('programs_count')
            ->take(5)
            ->get(['id', 'slug', 'name_tr', 'icon', 'color','name_en','name_de']);

        // İlgili blog yazıları — eyalet adı geçen
        $relatedPosts = Post::where('is_published', 1)->where('locale', app()->getLocale())
            ->whereNotNull('published_at')
            ->where(function ($q) use ($state) {
                $q->where('title', 'like', '%' . $state->name_de . '%')
                  ->orWhere('content_md', 'like', '%' . $state->name_de . '%');
            })
            ->with('category')
            ->orderByDesc('published_at')
            ->take(3)
            ->get(['id', 'slug', 'title', 'excerpt', 'reading_minutes', 'published_at', 'category_id']);

        // Bölge etiketi (locale-aware)
        $regionLabel = match ($state->region) {
            'nord'  => ['🌊', __('Northern Germany')],
            'sued'  => ['⛰️', __('Southern Germany')],
            'west'  => ['🏭', __('Western Germany')],
            'ost'   => ['🌅', __('Eastern Germany')],
            default => null,
        };

        return view('states.show', compact('state', 'cities', 'topUnis', 'totals', 'otherStates', 'topFields', 'relatedPosts', 'regionLabel', 'stateBlocks'));
    }
}
