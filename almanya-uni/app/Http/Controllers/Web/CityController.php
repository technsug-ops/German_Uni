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
        // universities_count = BİRİNCİL (city_id) + KAMPÜS (university_campuses) aktif üni.
        // Böylece sadece-kampüs şehirler (ör. Duisburg → Duisburg-Essen) de listede çıkar
        // ve rozet sayısı detay sayfasıyla tutarlı olur.
        $query = City::query()
            ->select('cities.*')
            ->selectRaw('(
                (select count(*) from universities u where u.city_id = cities.id and u.is_active = 1)
                + (select count(*) from university_campuses uc
                   inner join universities u2 on u2.id = uc.university_id
                   where uc.city_id = cities.id and u2.is_active = 1)
            ) as universities_count')
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

        return view('cities.show', [
            'city'           => $city,
            'similarCities'  => $similarCities,
            'topPrograms'    => $topPrograms,
            'topFieldsInCity'=> $topFieldsInCity,
            'relatedPosts'   => $relatedPosts,
            'citySize'       => $citySize,
            'experiences'    => $experiences,
        ]);
    }
}
