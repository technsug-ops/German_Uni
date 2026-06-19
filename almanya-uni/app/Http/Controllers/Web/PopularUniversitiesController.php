<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\FieldOfStudy;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * "Almanya'da En Çok Tercih Edilen Bölümler ve Üniversiteler" rehberi
 * + etkileşimli "Alman Üniversiteleri Gezgini" (gerçek DB'den: QS dünya
 * sıralaması, şehir, öne çıkan alanlar, tahmini aylık yaşam maliyeti).
 *
 * Editoryal metinler İngilizce __() anahtarı (TR/DE lang dosyalarında).
 * Gezgin veri seti per-locale 6 saat cache'li (rankings ile aynı desen).
 */
class PopularUniversitiesController extends Controller
{
    /** Aylık yaşam maliyeti tahmini = oda kirası + sabit yaşam gideri (yemek/ulaşım/sigorta vb.). */
    private const LIVING_BASE_EUR = 520;

    public function index(): View
    {
        $explorer = Cache::remember(
            'popular-unis.explorer.' . app()->getLocale(),
            now()->addHours(6),
            fn () => $this->buildExplorer()
        );

        return view('universities.popular', [
            'explorer'   => $explorer,
            'categories' => collect($explorer)->pluck('type_label')->unique()->sort()->values()->all(),
        ]);
    }

    /**
     * QS dünya sıralaması olan aktif üniversiteler — gezgin tablosunun veri seti.
     * Şehir + tahmini aylık maliyet + en çok programlı 2 alan ("öne çıkan alanlar").
     */
    private function buildExplorer(): array
    {
        $locale = app()->getLocale();
        $nameCol = in_array($locale, ['tr', 'en', 'de'], true) ? "name_{$locale}" : 'name_de';
        $fieldTable = (new FieldOfStudy)->getTable();

        $typeLabels = [
            'public'           => 'Public',
            'applied_sciences' => 'Applied Sciences (FH)',
            'private'          => 'Private',
            'art'              => 'Art / Music',
            'religion'         => 'Theology',
        ];

        $unis = DB::table('universities as u')
            ->leftJoin('cities as c', 'c.id', '=', 'u.city_id')
            ->whereNotNull('u.qs_world_rank')->where('u.qs_world_rank', '>', 0)
            ->where('u.is_active', 1)
            ->orderBy('u.qs_world_rank')
            ->get([
                'u.id', 'u.slug', 'u.name_de', 'u.short_name', 'u.type', 'u.qs_world_rank',
                "c.{$nameCol} as city_name", 'c.slug as city_slug',
                'c.student_rent_wg_warm', 'c.student_rent_warm30', 'c.avg_rent_min',
            ]);

        // Öne çıkan alanlar — tek sorguda tüm üniler için (N+1 değil).
        $topFields = DB::table('programs as p')
            ->join("{$fieldTable} as f", 'f.id', '=', 'p.field_of_study_id')
            ->whereIn('p.university_id', $unis->pluck('id'))
            ->where('p.is_active', 1)
            ->select('p.university_id', "f.{$nameCol} as field_name", DB::raw('count(*) as c'))
            ->groupBy('p.university_id', 'field_name')
            ->orderByDesc('c')
            ->get()
            ->groupBy('university_id')
            ->map(fn ($rows) => $rows->take(2)->pluck('field_name')->filter()->values()->all());

        return $unis->map(function ($u) use ($typeLabels, $topFields) {
            $rent = $u->student_rent_wg_warm ?: $u->student_rent_warm30 ?: $u->avg_rent_min;
            $cost = $rent ? (int) (round(($rent + self::LIVING_BASE_EUR) / 10) * 10) : null;

            return [
                'name'        => $u->short_name ?: $u->name_de,
                'full_name'   => $u->name_de,
                'slug'        => $u->slug,
                'url'         => route('universities.show', $u->slug),
                'city'        => $u->city_name,
                'city_slug'   => $u->city_slug,
                'type'        => $u->type,
                'type_label'  => $typeLabels[$u->type] ?? 'Public',
                'rank'        => (int) $u->qs_world_rank,
                'cost'        => $cost,
                'fields'      => $topFields[$u->id] ?? [],
            ];
        })->all();
    }
}
