<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Program;
use App\Models\Scholarship;
use App\Models\University;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;

/**
 * Gömülebilir araç widget'ları — backlink mıknatısı. Bloglar/forumlar iframe ile
 * sitelerine gömer; her widget alt linkte ApplyToGerman'a "powered by" linki taşır,
 * yani her gömme = bir backlink. Sayfa layout'suz, self-contained (inline CSS + vanilla
 * JS), frame-ancestors * ile her origin'de gömülebilir.
 *
 * Locale-agnostic canonical route (/embed/cost-of-living); ?lang=tr|en|de ile dil seçilir.
 */
class EmbedController extends Controller
{
    private const LOCALES = ['tr', 'en', 'de'];

    public function costOfLiving(Request $request): Response
    {
        $lang = $request->query('lang');
        if (in_array($lang, self::LOCALES, true)) {
            App::setLocale($lang);
        }

        // Düz array cache'le (Eloquent Collection değil) — database cache driver'ı
        // deserialize'de bozabiliyor. Key locale-spesifik (name accessor locale-aware).
        $cities = cache()->remember('embed.cost.cities:' . App::getLocale(), now()->addHours(12), function () {
            return City::query()
                ->whereHas('costData')
                ->with('costData')
                ->orderBy('name_de')
                ->get(['id', 'name_tr', 'name_en', 'name_de'])
                ->map(fn (City $c) => [
                    'name'       => $c->name,
                    'wg'         => (int) $c->costData->rent_wg,
                    'studio'     => (int) $c->costData->rent_studio,
                    'apartment'  => (int) $c->costData->rent_apartment,
                    'food'       => (int) $c->costData->food,
                    'transport'  => (int) $c->costData->transport,
                    'utilities'  => (int) $c->costData->utilities,
                    'insurance'  => (int) $c->costData->health_insurance,
                    'fun'        => (int) $c->costData->entertainment,
                    'misc'       => (int) $c->costData->misc,
                ])
                ->values()
                ->all();
        });

        // frame-ancestors * → her origin'de iframe ile gömülebilir (X-Frame-Options yok).
        return response()
            ->view('embed.cost-of-living', ['cities' => $cities])
            ->header('Content-Security-Policy', 'frame-ancestors *');
    }

    /**
     * Canlı istatistik sayacı widget'ı — gömülebilir veri rozeti (464 üni · 14k program …).
     * Sayılar server-rendered (JS gerekmez); 12 saat cache. Veri-gazeteciliği için.
     */
    public function stats(Request $request): Response
    {
        $lang = $request->query('lang');
        if (in_array($lang, self::LOCALES, true)) {
            App::setLocale($lang);
        }

        $stats = cache()->remember('embed.stats.totals', now()->addHours(12), function () {
            $programs   = Program::where('is_active', true)->count();
            $programsEn = Program::where('is_active', true)->whereIn('language', ['en', 'both'])->count();

            return [
                'universities' => University::where('is_active', true)->count(),
                'programs'     => $programs,
                'en_pct'       => $programs ? (int) round($programsEn / $programs * 100) : 0,
                'cities'       => City::whereHas('universities', fn ($q) => $q->where('is_active', 1))->count(),
                'scholarships' => Scholarship::whereNull('removed_at')->count(),
            ];
        });

        return response()
            ->view('embed.stats', ['stats' => $stats])
            ->header('Content-Security-Policy', 'frame-ancestors *');
    }
}
