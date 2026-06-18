<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Post;
use App\Models\Program;
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CityController extends Controller
{
    public function index(Request $request): View|\Illuminate\Http\Response
    {
        // universities_count = KANONİK campus-farkındalı sayım (City::scopeWithCampusAwareUniCount).
        // Birincil (city_id) + kampüs (university_campuses) aktif ünilerin DISTINCT birleşimi
        // → rozet, detay sayfasındaki $city->universities->count() ile BİREBİR eşit.
        $query = City::query()
            ->select('cities.*')
            ->withCampusAwareUniCount()
            ->with('state:id,slug,name_de,name_tr,name_en')
            ->having('universities_count', '>', 0);

        // Katmanlı arama: şehir adlarında FULLTEXT + isim-eşleşmesi/alaka önde.
        if ($request->filled('q')) {
            $s = $request->input('q');
            $like = '%' . str_replace(['%', '_'], ['\%', '\_'], $s) . '%';
            $query->searchFulltext($s, ['name_de', 'name_tr', 'name_en'])
                  ->orderByRaw('CASE WHEN name_de LIKE ? OR name_tr LIKE ? THEN 0 ELSE 1 END', [$like, $like])
                  ->orderByRelevance($s, ['name_de', 'name_tr', 'name_en']);
        }

        if ($request->filled('state')) {
            $query->whereHas('state', fn ($q) => $q->where('slug', $request->input('state')));
        }

        // Şehir boyutu (nüfus aralığı)
        if ($request->filled('size')) {
            match ($request->input('size')) {
                'small'  => $query->where('population', '<', 200000),
                'medium' => $query->whereBetween('population', [200000, 1000000]),
                'large'  => $query->where('population', '>', 1000000),
                default  => null,
            };
        }

        // Üni yoğunluğu (HAVING kullanılır, withCount sonucu)
        if ($request->filled('uni_count')) {
            match ($request->input('uni_count')) {
                'few'    => $query->having('universities_count', '<=', 2),
                'mid'    => $query->havingRaw('universities_count BETWEEN 3 AND 5'),
                'many'   => $query->having('universities_count', '>=', 6),
                default  => null,
            };
        }

        // Sıralama: varsayılan üni sayısı, alternatif nüfus veya alfabetik
        $sort = $request->input('sort', 'uni_count');
        $query = match ($sort) {
            'population' => $query->orderByDesc('population'),
            'name'       => $query->orderBy('name_de'),
            default      => $query->orderByDesc('universities_count'),
        };

        $cities = $query->paginate(48)->withQueryString();

        // States are shown as upper chip-row filter on the cities index. We count
        // only cities that have at least one active university so the badge total
        // matches the actual filtered listing (zero-uni cities are hidden anyway).
        $states = State::orderBy('name_de')
            ->withCount(['cities' => fn ($q) => $q->whereHas('universities', fn ($u) => $u->where('is_active', 1))])
            ->get(['id', 'slug', 'name_tr', 'name_de', 'name_en']);

        $viewData = [
            'cities' => $cities,
            'states' => $states,
            'filters' => $request->only(['q', 'state', 'size', 'uni_count', 'sort']),
        ];

        if ($request->ajax() || $request->wantsJson() || $request->boolean('partial')) {
            return response(view('cities._grid', $viewData)->render())
                ->header('Content-Type', 'text/html; charset=utf-8');
        }

        return view('cities.index', $viewData);
    }

    public function show(string $slug): View
    {
        $city = City::where('slug', $slug)
            ->orWhere('id', $slug)
            ->with(['state', 'universities' => fn ($q) => $q->where('is_active', 1)->orderByDesc('student_count')])
            ->firstOrFail();

        // Kullanıcı aktivitesi — onboarding "Şehirleri keşfet" adımı (3+ şehir) bunu sayar.
        // (Üni/Program/Profession controller'larında vardı, şehirde eksikti → adım hiç dolmuyordu.)
        \App\Support\ActivityLogger::log($city, $city->name_de);

        // Çok-kampüslü üniler: birincil şehri başka ama burada da fakültesi olanları ekle
        // (ör. Duisburg sayfasında Universität Duisburg-Essen). Birincil + kampüs birleşir.
        $campusUnis = $city->campusUniversities()->where('is_active', 1)->orderByDesc('student_count')->get();
        if ($campusUnis->isNotEmpty()) {
            $merged = $city->universities->concat($campusUnis)->unique('id')->sortByDesc('student_count')->values();
            $city->setRelation('universities', $merged);
        }

        // Benzer şehirler — aynı eyaletten + uni sayısı yakın
        $similarCities = City::query()
            ->where('id', '!=', $city->id)
            ->where(function ($q) use ($city) {
                if ($city->state_id) {
                    $q->where('state_id', $city->state_id);
                }
            })
            ->whereHas('universities', fn ($q) => $q->where('is_active', 1))
            ->withCount(['universities' => fn ($q) => $q->where('is_active', 1)])
            ->orderByDesc('universities_count')
            ->take(6)
            ->get();

        // Yeterli yoksa eyalet dışından da popüler şehir ekle
        if ($similarCities->count() < 4) {
            $extra = City::query()
                ->where('id', '!=', $city->id)
                ->whereNotIn('id', $similarCities->pluck('id'))
                ->whereHas('universities', fn ($q) => $q->where('is_active', 1))
                ->withCount(['universities' => fn ($q) => $q->where('is_active', 1)])
                ->orderByDesc('universities_count')
                ->take(6 - $similarCities->count())
                ->get();
            $similarCities = $similarCities->merge($extra);
        }

        // Bu şehirde popüler programlar (öğrenci sayısı yüksek üni'lerden + Türkçe açıklamalılar öne)
        $cityUniIds = $city->universities->pluck('id');
        $topPrograms = Program::whereIn('university_id', $cityUniIds)
            ->where('is_active', 1)
            ->with('university:id,name_de,slug,logo_url', 'field:id,icon')
            ->orderByRaw('CASE WHEN description_tr IS NOT NULL THEN 0 ELSE 1 END')
            ->orderBy('name_de')
            ->take(8)
            ->get(['id', 'slug', 'name_de', 'degree', 'language', 'university_id', 'field_of_study_id']);

        // Top 5 alan — bu şehirde program count'a göre
        $topFieldsInCity = \App\Models\FieldOfStudy::active()
            ->whereHas('programs', fn ($q) => $q->whereIn('university_id', $cityUniIds)->where('is_active', 1))
            ->withCount(['programs' => fn ($q) => $q->whereIn('university_id', $cityUniIds)->where('is_active', 1)])
            ->orderByDesc('programs_count')
            ->take(5)
            ->get(['id', 'slug', 'name_tr', 'icon', 'color','name_en','name_de']);

        // İlgili blog yazıları — şehir adı geçen
        $relatedPosts = Post::where('is_published', 1)
            ->whereNotNull('published_at')
            ->where('locale', app()->getLocale()) // sadece current locale postları
            ->where(function ($q) use ($city) {
                $q->where('title', 'like', '%' . $city->name_de . '%')
                  ->orWhere('content_md', 'like', '%' . $city->name_de . '%');
            })
            ->with('category')
            ->orderByDesc('published_at')
            ->take(3)
            ->get(['id', 'slug', 'title', 'excerpt', 'reading_minutes', 'published_at', 'category_id']);

        // Şehir boyut badge'i için kategori
        $citySize = match (true) {
            $city->population > 1_000_000 => ['🌆', 'Metropol', 'rose'],
            $city->population > 200_000   => ['🏙️', 'Orta', 'blue'],
            $city->population > 0          => ['🏘️', 'Küçük', 'amber'],
            default                        => null,
        };

        // Onaylı öğrenci deneyimleri (Topluluk Katkıcısı)
        $experiences = \App\Models\Contribution::approved()
            ->where('target_type', 'city')
            ->where(fn ($q) => $q->where('target_label', 'like', '%' . $city->name_de . '%')
                ->orWhere('target_label', 'like', '%' . $city->name_tr . '%'))
            ->with('user:id,name,avatar_url,is_contributor')
            ->latest('approved_at')
            ->take(6)
            ->get();

        // Bu şehirle ilgili barınma sağlayıcıları (özel zincir + platform) — cities
        // dizisinde şehri listeleyen aktif providers. Statik private_chain_slugs yerine
        // dinamik: yeni eklenen sağlayıcılar (Wunderflats, Staytoo, vb.) otomatik gelir.
        $cityNameVariants = array_values(array_unique(array_filter([
            $city->name_de,
            $city->name_tr,
            $city->name_en,
            \Illuminate\Support\Str::before($city->name_de, ' '), // "Frankfurt am Main" → "Frankfurt"
        ])));

        $cityChains = \App\Models\HousingProvider::active()
            ->whereIn('type', ['private_chain', 'platform'])
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->get()
            ->filter(function ($p) use ($cityNameVariants) {
                $pc = collect($p->cities ?? [])->map(fn ($c) => trim((string) $c));
                if ($pc->contains('Tüm Almanya')) {
                    return true; // ülke geneli platform
                }

                return $pc->contains(function ($c) use ($cityNameVariants) {
                    return in_array($c, $cityNameVariants, true)
                        || in_array(\Illuminate\Support\Str::before($c, ' '), $cityNameVariants, true);
                });
            })
            ->values();

        return view('cities.show', [
            'city'           => $city,
            'similarCities'  => $similarCities,
            'topPrograms'    => $topPrograms,
            'topFieldsInCity'=> $topFieldsInCity,
            'relatedPosts'   => $relatedPosts,
            'citySize'       => $citySize,
            'experiences'    => $experiences,
            'cityChains'     => $cityChains,
        ]);
    }
}
