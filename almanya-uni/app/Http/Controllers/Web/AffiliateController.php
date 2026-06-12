<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AffiliateClick;
use App\Models\BlockedAccountProvider;
use App\Models\HealthInsuranceProvider;
use App\Models\HousingProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Affiliate dış-link redirect + tıklama takibi. /go/{type}/{slug}?ctx=...
 * Tıklamayı affiliate_clicks'e yazar (savunmacı), sonra sağlayıcının affiliate
 * URL'ine (yoksa website'ine) 302. Locale-agnostik (locale grubu DIŞINDA kayıtlı).
 */
class AffiliateController extends Controller
{
    private const TYPES = [
        'sperrkonto' => BlockedAccountProvider::class,
        'insurance'  => HealthInsuranceProvider::class,
        'housing'    => HousingProvider::class,
    ];

    public function go(Request $request, string $type, string $slug): RedirectResponse
    {
        $model = self::TYPES[$type] ?? null;
        abort_unless($model, 404);

        /** @var BlockedAccountProvider|HealthInsuranceProvider|null $provider */
        $provider = $model::where('slug', $slug)->first();
        abort_unless($provider, 404);

        $url = $provider->cta_url;
        abort_unless($url, 404);

        // Tıklamayı kaydet — hata olursa redirect'i ASLA engelleme (gelir > log).
        try {
            AffiliateClick::create([
                'provider_type' => $type,
                'provider_id'   => $provider->id,
                'provider_slug' => $slug,
                'context'       => $this->clean($request->query('ctx'), 32),
                'locale'        => app()->getLocale(),
                'host'          => $this->clean($request->getHost(), 64),
                'ip_hash'       => $request->ip() ? hash('sha256', $request->ip()) : null,
                'user_agent'    => $this->clean($request->userAgent(), 255),
                'referer'       => $this->clean($request->headers->get('referer'), 255),
            ]);
        } catch (\Throwable $e) {
            report($e);
        }

        return redirect()->away($url);
    }

    private function clean(?string $value, int $max): ?string
    {
        $value = trim((string) $value);
        return $value === '' ? null : mb_substr($value, 0, $max);
    }
}
