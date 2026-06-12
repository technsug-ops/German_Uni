<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\FieldOfStudy;
use App\Models\Profession;
use App\Models\Program;
use App\Models\University;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Linkable-asset / veri-gazeteciliği sayfası — Almanya'da eğitim hakkında
 * ALINTILANABİLİR istatistikler (backlink mıknatısı). Blog/gazeteci kaynak
 * gösterip linkler. Dataset schema + cache (stabil veri, 12 saat).
 */
class StatisticsController extends Controller
{
    public function index(): View
    {
        // DÜZ ARRAY cache'le (Collection/Eloquent değil): database cache driver'ı
        // deserialize'de stdClass property'lerini bozabiliyor (slug kayboluyor →
        // route() 500). Düz array her driver'da kusursuz. Field adı locale-aware
        // olduğundan key locale-spesifik.
        $data = cache()->remember('stats.page:' . app()->getLocale(), now()->addHours(12), function () {
            $programs   = Program::where('is_active', 1)->count();
            $programsEn = Program::where('is_active', 1)->whereIn('language', ['en', 'both'])->count();

            return [
                'totals' => [
                    'universities' => University::where('is_active', 1)->count(),
                    'programs'     => $programs,
                    'programs_en'  => $programsEn,
                    'en_pct'       => $programs ? (int) round($programsEn / $programs * 100) : 0,
                    'cities'       => City::where('is_active', 1)->count(),
                    'professions'  => Profession::where('is_active', 1)->count(),
                ],
                'top_uni_en' => DB::table('programs')
                    ->join('universities', 'universities.id', '=', 'programs.university_id')
                    ->where('programs.is_active', 1)
                    ->whereIn('programs.language', ['en', 'both'])
                    ->where('universities.is_active', 1)
                    ->groupBy('universities.id', 'universities.name_de', 'universities.slug')
                    ->select('universities.name_de', 'universities.slug', DB::raw('count(*) as c'))
                    ->orderByDesc('c')->limit(10)->get()
                    ->map(fn ($u) => ['slug' => $u->slug, 'name' => $u->name_de, 'c' => (int) $u->c])->all(),
                'cheapest_cities' => DB::table('city_cost_data')
                    ->join('cities', 'cities.id', '=', 'city_cost_data.city_id')
                    ->where('cities.is_active', 1)
                    ->where('city_cost_data.rent_wg', '>', 0)
                    ->select('cities.name_de', 'cities.slug', DB::raw('(city_cost_data.rent_wg + city_cost_data.food + city_cost_data.transport + city_cost_data.utilities + city_cost_data.misc + city_cost_data.entertainment) as total'))
                    ->orderBy('total')->limit(10)->get()
                    ->map(fn ($c) => ['slug' => $c->slug, 'name' => $c->name_de, 'total' => (int) $c->total])->all(),
                'top_fields' => FieldOfStudy::active()
                    ->withCount(['programs' => fn ($q) => $q->where('is_active', 1)])
                    ->orderByDesc('programs_count')
                    ->limit(8)
                    ->get(['id', 'slug', 'name_tr', 'name_en', 'name_de', 'icon'])
                    ->map(fn ($f) => ['slug' => $f->slug, 'name' => $f->name, 'icon' => $f->icon, 'count' => $f->programs_count])->all(),
            ];
        });

        return view('statistics.index', $data);
    }
}
