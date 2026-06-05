<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\State;
use App\Models\University;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UniversityWebController extends Controller
{
    private const ALLOWED_TYPES = ['public', 'private', 'applied_sciences', 'art', 'religion'];

    public function index(Request $request): View|\Illuminate\Http\Response
    {
        $query = University::query()->where('is_active', true);

        if ($request->filled('q')) {
            $search = trim((string) $request->input('q'));
            // Kelime-bazlı arama: her kelime AYRI eşleşmeli (AND), ama herhangi bir alanda (OR).
            // Önceki tek-LIKE "TU münchen" gibi kısaltma+şehir aramalarında 0 sonuç veriyordu
            // ("Technische Universität München" tek bir alanda "TU münchen" substring'ini içermez).
            $tokens = preg_split('/\s+/', $search, -1, PREG_SPLIT_NO_EMPTY) ?: [$search];
            foreach ($tokens as $token) {
                $query->where(function ($w) use ($token) {
                    $w->where('name_de', 'like', "%$token%")
                        ->orWhere('name_en', 'like', "%$token%")
                        ->orWhere('name_tr', 'like', "%$token%")
                        ->orWhere('short_name', 'like', "%$token%")
                        ->orWhere('description_de', 'like', "%$token%")
                        ->orWhereHas('city', fn ($c) => $c
                            ->where('name_de', 'like', "%$token%")
                            ->orWhere('name_tr', 'like', "%$token%")
                            ->orWhere('name_en', 'like', "%$token%"));
                });
            }
        }

        $type = $request->input('type');
        if ($type && in_array($type, self::ALLOWED_TYPES, true)) {
            $query->where('type', $type);
        }

        if ($request->filled('city')) {
            $city = $request->input('city');
            $query->whereHas('city', fn ($q) => $q->where('slug', $city)
                ->orWhere('name_de', 'like', "%$city%")
                ->orWhere('name_tr', 'like', "%$city%")
                ->orWhere('name_en', 'like', "%$city%"));
        }

        if ($request->filled('state')) {
            $state = $request->input('state');
            $query->whereHas('city.state', fn ($q) => $q->where('slug', $state)
                ->orWhere('name_de', 'like', "%$state%")
                ->orWhere('name_tr', 'like', "%$state%")
                ->orWhere('name_en', 'like', "%$state%"));
        }

        if ($request->filled('founded_min')) {
            $query->where('founded_year', '>=', $request->input('founded_min'));
        }

        if ($request->filled('founded_max')) {
            $query->where('founded_year', '<=', $request->input('founded_max'));
        }

        // İngilizce program filter — Türk öğrenciler için kritik
        if ($request->filled('english') && $request->input('english') === '1') {
            $query->whereHas('programs', fn ($p) => $p
                ->where('is_active', 1)
                ->whereIn('language', ['en', 'both']));
        }

        // NC-frei (admission_mode = zulassungsfrei) program sunan üniler
        if ($request->filled('nc_free') && $request->input('nc_free') === '1') {
            $query->whereHas('programs', fn ($p) => $p
                ->where('is_active', 1)
                ->where('admission_mode', 'zulassungsfrei'));
        }

        // Uni-assist üye — Türk öğrenciler için başvuru kolaylığı
        if ($request->filled('uni_assist') && $request->input('uni_assist') === '1') {
            $query->where('is_uni_assist_member', true);
        }

        // Üni boyutu (öğrenci sayısı)
        if ($request->filled('size')) {
            match ($request->input('size')) {
                'small'  => $query->where('student_count', '<', 5000),
                'medium' => $query->whereBetween('student_count', [5000, 20000]),
                'large'  => $query->where('student_count', '>', 20000),
                default  => null,
            };
        }

        $total = $query->count();
        $universities = $query->with('city.state')
            ->orderByDesc('student_count')
            ->orderBy('name_de')
            ->paginate(48)
            ->withQueryString()
            ->through(fn ($u) => [
                'id' => $u->id,
                'slug' => $u->slug,
                'name_de' => $u->name_de,
                'logo_url' => $u->logo_url,
                // Raw own-image only. _grid resolves the 3-layer fallback
                // (own → city landmark pool → gradient) via App\Support\CoverImage.
                'image_url' => $u->image_url,
                'city_slug' => $u->city?->slug,
                'city_name' => $u->city?->name,
                'state_name' => $u->city?->state?->name,
                'founded_year' => $u->founded_year,
                'type' => $u->type,
                'student_count' => $u->student_count,
            ]);

        // Dropdown'lar için gerçekten DB'de bulunan türler + tüm eyaletler
        $availableTypes = University::where('is_active', true)
            ->select('type')
            ->distinct()
            ->whereNotNull('type')
            ->pluck('type')
            ->toArray();

        $states = State::orderBy('name_de')->get(['slug', 'name_de', 'name_tr','name_en','name_de']);

        // Form-helper closures used by both index and _grid partials
        $typeLabel = fn ($t) => match ($t) {
            'public' => __('Public'),
            'private' => __('Private'),
            'applied_sciences' => __('Applied Sciences'),
            'art' => __('Art'),
            'religion' => __('Religion'),
            default => $t ? ucfirst($t) : '-',
        };
        $typeBadgeColor = fn ($t) => match ($t) {
            'public' => 'bg-emerald-50 text-emerald-700',
            'private' => 'bg-amber-50 text-amber-700',
            'applied_sciences' => 'bg-blue-50 text-blue-700',
            'art' => 'bg-pink-50 text-pink-700',
            'religion' => 'bg-purple-50 text-purple-700',
            default => 'bg-gray-100 text-gray-700',
        };

        $viewData = [
            'universities' => $universities,
            'total' => $total,
            'available_types' => $availableTypes,
            'states' => $states,
            'typeLabel' => $typeLabel,
            'typeBadgeColor' => $typeBadgeColor,
        ];

        // XHR — return only the grid partial (async filter UX)
        if ($request->ajax() || $request->wantsJson() || $request->boolean('partial')) {
            return response(view('universities._grid', $viewData)->render())
                ->header('Content-Type', 'text/html; charset=utf-8');
        }

        return view('universities.index', $viewData);
    }

    public function show(string $slugOrId): View
    {
        $university = University::where('slug', $slugOrId)
            ->orWhere('id', $slugOrId)
            ->with(['city.state'])
            ->firstOrFail();

        \App\Support\ActivityLogger::log($university, $university->name_de);

        $data = [
            'id' => $university->id,
            'slug' => $university->slug,
            'name_de' => $university->name_de,
            'name_en' => $university->name_en,
            'description_tr' => $university->description_tr,
            'description_de' => $university->description_de,
            'description_en' => $university->description_en,
            'type' => $university->type,
            'founded_year' => $university->founded_year,
            'student_count' => $university->student_count,
            'website_url' => $university->website_url,
            'logo_url' => $university->logo_url,
            'wikipedia_url_de' => $university->wikipedia_url_de,
            'wikipedia_url_en' => $university->wikipedia_url_en,
            'city_name' => $university->city?->name,
            'city_slug' => $university->city?->slug,
            'state_name' => $university->city?->state?->name,
            'coordinates' => $university->coordinates,
            // Locale-aware: TR=content_blocks, EN/DE=content_blocks_{locale} (yoksa null → blade gizler)
            'content_blocks' => $university->localizedBlocks(),
            'image_url' => $university->image_url,
        ];

        // Programs (gruplandırılmış)
        $programs = $university->programs()
            ->where('is_active', true)
            ->orderBy('degree')
            ->orderBy('name_de')
            ->with('field:id,name_tr,name_en,name_de,icon,color,slug')
            ->get([
                'id', 'university_id', 'field_of_study_id', 'name_de', 'slug',
                'degree', 'degree_specification', 'language', 'duration_semesters',
                'location', 'description_tr', 'description_en',
                'application_deadline_summer', 'application_deadline_winter',
                'tuition_fee_eur', 'subjects', 'admission_mode',
            ]);

        $programsByDegree = $programs->groupBy('degree');

        // Benzer üniler — önce aynı şehirde, sonra aynı tipte
        $similarUnis = University::query()
            ->where('id', '!=', $university->id)
            ->where('is_active', 1)
            ->where(function ($q) use ($university) {
                if ($university->city_id) {
                    $q->where('city_id', $university->city_id);
                }
            })
            ->orderByDesc('student_count')
            ->take(6)
            ->get(['id', 'slug', 'name_de', 'logo_url', 'image_url', 'type', 'city_id', 'student_count']);

        if ($similarUnis->count() < 4 && $university->type) {
            $extra = University::query()
                ->where('id', '!=', $university->id)
                ->where('is_active', 1)
                ->where('type', $university->type)
                ->whereNotIn('id', $similarUnis->pluck('id'))
                ->orderByDesc('student_count')
                ->take(6 - $similarUnis->count())
                ->get(['id', 'slug', 'name_de', 'logo_url', 'image_url', 'type', 'city_id', 'student_count']);
            $similarUnis = $similarUnis->merge($extra);
        }

        $similarUnis->load('city:id,name_tr,name_en,name_de,slug');

        // Onaylı öğrenci deneyimleri (Topluluk Katkıcısı)
        $shortName = $university->short_name;
        $experiences = \App\Models\Contribution::approved()
            ->where('target_type', 'university')
            ->where(function ($q) use ($university, $shortName) {
                $q->where('target_label', 'like', '%' . $university->name_de . '%');
                if ($shortName && mb_strlen($shortName) >= 3) {
                    $q->orWhere('target_label', 'like', '%' . $shortName . '%');
                }
            })
            ->with('user:id,name,avatar_url,is_contributor')
            ->latest('approved_at')
            ->take(6)
            ->get();

        $cityUniCount = 0;
        $cityStudents = 0;
        $cityPrograms = 0;
        if ($university->city_id) {
            $cityUniIds = University::where('city_id', $university->city_id)
                ->where('is_active', 1)->pluck('id');
            $cityUniCount = $cityUniIds->count();
            $cityStudents = (int) University::whereIn('id', $cityUniIds)->sum('student_count');
            $cityPrograms = \App\Models\Program::whereIn('university_id', $cityUniIds)
                ->where('is_active', 1)->count();
        }

        // UGC reviews — approved listesi + ortalama rating
        $reviews = \App\Models\UniversityReview::approved()
            ->where('university_id', $university->id)
            ->latest()
            ->limit(10)
            ->get();
        $reviewsStats = \App\Models\UniversityReview::approved()
            ->where('university_id', $university->id)
            ->selectRaw('COUNT(*) as total, AVG(rating) as avg_rating')
            ->selectRaw('SUM(CASE WHEN rating=5 THEN 1 ELSE 0 END) as r5')
            ->selectRaw('SUM(CASE WHEN rating=4 THEN 1 ELSE 0 END) as r4')
            ->selectRaw('SUM(CASE WHEN rating=3 THEN 1 ELSE 0 END) as r3')
            ->selectRaw('SUM(CASE WHEN rating=2 THEN 1 ELSE 0 END) as r2')
            ->selectRaw('SUM(CASE WHEN rating=1 THEN 1 ELSE 0 END) as r1')
            ->first();

        return view('universities.show', [
            'university'         => $data,
            'university_model'   => $university,
            'programs'           => $programs,
            'programs_by_degree' => $programsByDegree,
            'similarUnis'        => $similarUnis,
            'experiences'        => $experiences,
            'cityUniCount'       => $cityUniCount,
            'cityStudents'       => $cityStudents,
            'cityPrograms'       => $cityPrograms,
            'reviews'            => $reviews,
            'reviewsStats'       => $reviewsStats,
        ]);
    }
}
