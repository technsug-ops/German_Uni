<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\HousingProvider;
use App\Models\HousingTemplate;
use App\Models\HousingTip;
use App\Models\StudentDorm;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class HousingController extends Controller
{
    public function index(): View
    {
        $dorms     = StudentDorm::with('city')->where('is_active', true)->orderBy('sort_order')->get();
        $templates = HousingTemplate::where('is_active', true)->orderBy('sort_order')->get();
        $tips      = HousingTip::approved()
            ->with('user:id,name', 'city:id,name_tr,name_en,name_de,slug')
            ->latest()->limit(6)->get();

        $stats = [
            'dorms'     => $dorms->count(),
            'templates' => $templates->count(),
            'tips'      => HousingTip::approved()->count(),
            'cities'    => $dorms->pluck('city_id')->unique()->count(),
        ];

        // Top 8 öğrenci şehri için kira aralığı (CityCostData)
        $rentRanges = City::query()
            ->whereHas('costData')
            ->whereHas('universities', fn ($q) => $q->where('is_active', 1))
            ->with('costData')
            ->withCount(['universities' => fn ($q) => $q->where('is_active', 1)])
            ->orderByDesc('universities_count')
            ->take(10)
            ->get(['id', 'slug', 'name_de'])
            ->map(fn ($c) => [
                'slug'      => $c->slug,
                'name'      => $c->name_de,
                'wg'        => $c->costData?->rent_wg,
                'studio'    => $c->costData?->rent_studio,
                'apartment' => $c->costData?->rent_apartment,
                'uni_count' => $c->universities_count,
            ])
            ->filter(fn ($r) => $r['wg'] !== null)
            ->take(8)
            ->values();

        return view('housing.index', compact('dorms', 'templates', 'tips', 'stats', 'rentRanges'));
    }

    public function providers(Request $request): View
    {
        $type = $request->query('type');

        $query = HousingProvider::active()->orderBy('sort_order');
        if ($type && array_key_exists($type, HousingProvider::TYPES)) {
            $query->where('type', $type);
        }

        $providers = $query->get();
        $grouped   = $providers->groupBy('type');

        $stats = [
            'total'             => HousingProvider::active()->count(),
            'studierendenwerk'  => HousingProvider::active()->where('type', 'studierendenwerk')->count(),
            'private_chain'     => HousingProvider::active()->where('type', 'private_chain')->count(),
            'platform'          => HousingProvider::active()->where('type', 'platform')->count(),
            'total_capacity'    => HousingProvider::active()->where('type', 'studierendenwerk')->sum('total_capacity'),
        ];

        return view('housing.providers', compact('providers', 'grouped', 'stats', 'type'));
    }

    public function providerShow(string $slug): View
    {
        $provider = HousingProvider::active()->where('slug', $slug)->firstOrFail();

        // Bu provider'ın listelendiği şehirler — cities.private_chain_slugs JSON içinde slug aranır
        $citiesWithProvider = collect();
        if ($provider->type === 'private_chain') {
            $citiesWithProvider = City::whereJsonContains('private_chain_slugs', $provider->slug)
                ->orderByDesc('population')
                ->get(['id', 'slug', 'name_de', 'avg_rent_min', 'avg_rent_max', 'population']);
        } elseif ($provider->type === 'studierendenwerk') {
            $citiesWithProvider = City::where('stw_name', $provider->name)
                ->orderByDesc('population')
                ->get(['id', 'slug', 'name_de', 'avg_rent_min', 'avg_rent_max', 'population']);
        }

        // Benzer sağlayıcılar
        $related = HousingProvider::active()
            ->where('type', $provider->type)
            ->where('id', '!=', $provider->id)
            ->orderBy('sort_order')
            ->take(4)
            ->get();

        return view('housing.provider-show', compact('provider', 'citiesWithProvider', 'related'));
    }

    public function template(string $slug): View
    {
        $template = HousingTemplate::where('slug', $slug)->where('is_active', true)->firstOrFail();
        $others   = HousingTemplate::where('is_active', true)
            ->where('id', '!=', $template->id)
            ->orderBy('sort_order')->get();

        return view('housing.template', compact('template', 'others'));
    }

    public function tips(Request $request): View
    {
        $category = $request->query('category');
        $city     = $request->query('city');

        $query = HousingTip::approved()
            ->with('user:id,name', 'city:id,name_tr,name_en,name_de,slug')
            ->latest();

        if ($category) $query->where('category', $category);
        if ($city)     $query->whereHas('city', fn ($q) => $q->where('slug', $city));

        $tips = $query->paginate(20)->withQueryString();

        return view('housing.tips', [
            'tips'     => $tips,
            'category' => $category,
            'city'     => $city,
        ]);
    }

    public function createTip(): View
    {
        $cities = City::has('universities')->orderBy('name_de')->get(['id', 'slug', 'name_de']);
        return view('housing.create-tip', compact('cities'));
    }

    public function storeTip(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'city_id'  => 'nullable|integer|exists:cities,id',
            'title'    => 'required|string|max:255',
            'category' => 'required|in:wg,private,dorm,scam-warning,landlord-talk,other',
            'content'  => 'required|string|min:50|max:5000',
        ]);

        $cityName = null;
        if (! empty($data['city_id'])) {
            $cityName = City::find($data['city_id'])?->name;
        }

        HousingTip::create(array_merge($data, [
            'user_id'     => $request->user()->id,
            'city_name'   => $cityName,
            'is_approved' => false,   // Moderasyon için bekler
        ]));

        return redirect()->route('housing.index')
            ->with('status', 'Deneyimin kaydedildi! Editör onayı sonrası yayına alınacak.');
    }
}
