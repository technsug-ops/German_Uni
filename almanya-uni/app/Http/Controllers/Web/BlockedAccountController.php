<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\BlockedAccountProvider;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlockedAccountController extends Controller
{
    public function index(Request $request): View
    {
        $filter = $request->query('filter');

        $providers = BlockedAccountProvider::published()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $filtered = match ($filter) {
            'cheapest'  => $providers->sortBy(fn ($p) => $p->first_year_cost_eur ?? PHP_INT_MAX)->values(),
            'turkish'   => $providers->filter(fn ($p) => is_array($p->supported_languages) && in_array('tr', $p->supported_languages))->values(),
            'insurance' => $providers->where('combo_insurance', true)->values(),
            'fast'      => $providers->filter(fn ($p) => $p->activation_days_max && $p->activation_days_max <= 7)->values(),
            'fintech'   => $providers->where('type', 'fintech')->values(),
            'bank'      => $providers->where('type', 'traditional_bank')->values(),
            default     => $providers,
        };

        $totals = [
            'total'      => $providers->count(),
            'fintech'    => $providers->where('type', 'fintech')->count(),
            'bank'       => $providers->where('type', 'traditional_bank')->count(),
            'with_insurance' => $providers->where('combo_insurance', true)->count(),
        ];

        return view('tools.blocked-account.index', [
            'providers' => $filtered,
            'all_providers' => $providers,
            'filter' => $filter,
            'totals' => $totals,
        ]);
    }

    /**
     * Country-specific landing page (örn /tools/sperrkonto/country/turkey).
     * SEO için programmatic landing — her ülke için provider listesi + ülkeye özel notlar.
     */
    public function country(string $country): View
    {
        $countries = self::countriesData();
        $key = strtolower($country);
        $data = $countries[$key] ?? null;
        abort_unless($data, 404);

        // Sağlayıcı listesi — ülke desteği var mı kontrolü (supported_countries var ise filter, yoksa hepsi)
        $providers = BlockedAccountProvider::published()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->filter(function ($p) use ($key) {
                $sc = $p->supported_countries;
                return empty($sc) || !is_array($sc) || in_array($key, $sc) || in_array('all', $sc);
            })
            ->values();

        $otherCountries = collect($countries)->reject(fn ($v, $k) => $k === $key)->take(8);

        return view('tools.blocked-account.country', [
            'country' => $data + ['key' => $key],
            'providers' => $providers,
            'otherCountries' => $otherCountries,
        ]);
    }

    /**
     * Top 10 ülke — Almanya'ya en çok başvuran uluslararası öğrenci kaynakları.
     */
    public static function countriesData(): array
    {
        return [
            'turkey'   => ['name' => 'Türkiye',  'flag' => '🇹🇷', 'currency' => 'TRY', 'demonym_en' => 'Turkish', 'demonym_de' => 'türkisch', 'demonym_tr' => 'Türk',  'note_focus' => __('Türk öğrenciler için Sperrkonto. Vize randevu süresi ~2-3 ay (İstanbul/Ankara/İzmir).'), 'tier' => 'high'],
            'india'    => ['name' => 'India',    'flag' => '🇮🇳', 'currency' => 'INR', 'demonym_en' => 'Indian', 'demonym_de' => 'indisch',  'demonym_tr' => 'Hint',  'note_focus' => 'Largest international student group in Germany. APS certificate (~3 months) required.', 'tier' => 'high'],
            'pakistan' => ['name' => 'Pakistan', 'flag' => '🇵🇰', 'currency' => 'PKR', 'demonym_en' => 'Pakistani', 'demonym_de' => 'pakistanisch', 'demonym_tr' => 'Pakistanlı', 'note_focus' => 'APS certificate (~3-4 months) required. Tier-1 universities have higher approval rates.', 'tier' => 'high'],
            'nigeria'  => ['name' => 'Nigeria',  'flag' => '🇳🇬', 'currency' => 'NGN', 'demonym_en' => 'Nigerian', 'demonym_de' => 'nigerianisch', 'demonym_tr' => 'Nijeryalı', 'note_focus' => 'Visa interview at Abuja or Lagos embassy. Strong proof of return important.', 'tier' => 'high'],
            'usa'      => ['name' => 'United States', 'flag' => '🇺🇸', 'currency' => 'USD', 'demonym_en' => 'American', 'demonym_de' => 'amerikanisch', 'demonym_tr' => 'Amerikalı', 'note_focus' => 'No visa required for stays under 90 days for student visa application from inside Germany.', 'tier' => 'medium'],
            'uk'       => ['name' => 'United Kingdom', 'flag' => '🇬🇧', 'currency' => 'GBP', 'demonym_en' => 'British', 'demonym_de' => 'britisch', 'demonym_tr' => 'İngiliz', 'note_focus' => 'Post-Brexit visa rules apply. EU student status no longer automatic.', 'tier' => 'medium'],
            'uae'      => ['name' => 'United Arab Emirates', 'flag' => '🇦🇪', 'currency' => 'AED', 'demonym_en' => 'Emirati', 'demonym_de' => 'emiratisch', 'demonym_tr' => 'Emirlikli', 'note_focus' => 'Apply via German embassy in Abu Dhabi or Dubai consulate.', 'tier' => 'medium'],
            'china'    => ['name' => 'China',    'flag' => '🇨🇳', 'currency' => 'CNY', 'demonym_en' => 'Chinese', 'demonym_de' => 'chinesisch', 'demonym_tr' => 'Çinli', 'note_focus' => 'APS certificate required. Second-largest international student group in Germany.', 'tier' => 'high'],
            'brazil'   => ['name' => 'Brazil',   'flag' => '🇧🇷', 'currency' => 'BRL', 'demonym_en' => 'Brazilian', 'demonym_de' => 'brasilianisch', 'demonym_tr' => 'Brezilyalı', 'note_focus' => 'Visa appointments via VFS Global, partnership with German consulates.', 'tier' => 'medium'],
            'iran'     => ['name' => 'Iran',     'flag' => '🇮🇷', 'currency' => 'IRR', 'demonym_en' => 'Iranian', 'demonym_de' => 'iranisch', 'demonym_tr' => 'İranlı', 'note_focus' => 'Sanctions-related limitations on some providers (Wise, some FinTechs may decline). Use major banks.', 'tier' => 'high'],
        ];
    }

    public function show(string $slug): View
    {
        $provider = BlockedAccountProvider::where('slug', $slug)
            ->published()
            ->firstOrFail();

        $similar = BlockedAccountProvider::published()
            ->where('id', '!=', $provider->id)
            ->where('type', $provider->type)
            ->orderBy('sort_order')
            ->limit(3)
            ->get();

        return view('tools.blocked-account.show', [
            'provider' => $provider,
            'similar' => $similar,
        ]);
    }
}
