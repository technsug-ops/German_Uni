<?php

namespace App\Services\Social\Drivers;

use App\Models\ContentAsset;
use App\Services\Social\Contracts\SocialPublisher;
use App\Services\Social\PlatformMap;
use Illuminate\Support\Facades\Http;

/**
 * Faz 2 — Ayrshare tek-API sürücüsü (IG/X/TikTok/LinkedIn/Pinterest/YouTube...).
 * Anahtar (setting:ayrshare_api_key) girilince aktifleşir; zamanlamayı Ayrshare
 * sunucusu yapar → KAS cron'u gerekmez. Anahtar yokken sessizce devre dışı.
 *
 * Kurulum: ayrshare.com hesabı → sosyal profilleri bağla → API key → /admin
 * Yayın Kokpiti → Yayın Ayarları'na yapıştır + sürücüyü "ayrshare" yap.
 */
class AyrsharePublisher implements SocialPublisher
{
    private const ENDPOINT = 'https://app.ayrshare.com/api/post';

    public function key(): string
    {
        return 'ayrshare';
    }

    public function label(): string
    {
        return 'Ayrshare API (otomatik)';
    }

    public function isAutomatic(): bool
    {
        return true;
    }

    public function isConfigured(): bool
    {
        return ! empty(setting('ayrshare_api_key'));
    }

    public function publish(ContentAsset $asset, ?string $pageUrl = null): array
    {
        if (! $this->isConfigured()) {
            return [
                'success' => false,
                'mode'    => 'api',
                'message' => 'Ayrshare API key yok. /admin → Yayın Kokpiti → Yayın Ayarları\'na ekle.',
            ];
        }

        $platform = PlatformMap::ayrshare($asset->asset_type);
        if (! $platform) {
            return ['success' => false, 'mode' => 'api', 'message' => 'Bu asset türü Ayrshare ile eşlenmemiş.'];
        }

        $media = [];
        foreach ((array) ($asset->media ?? []) as $m) {
            if (is_array($m) && ! empty($m['url'])) {
                $media[] = $m['url'];
            }
        }

        try {
            $resp = Http::withToken(setting('ayrshare_api_key'))
                ->timeout(45)
                ->post(self::ENDPOINT, array_filter([
                    'post'      => trim((string) $asset->body_md),
                    'platforms' => [$platform],
                    'mediaUrls' => $media ?: null,
                ]));

            $json = $resp->json() ?? [];
            if ($resp->successful() && (($json['status'] ?? '') === 'success' || ! empty($json['postIds']))) {
                $url = $json['postIds'][0]['postUrl'] ?? ($json['id'] ?? null);
                return ['success' => true, 'mode' => 'api', 'url' => $url, 'message' => 'Ayrshare ile paylaşıldı.'];
            }

            return [
                'success' => false,
                'mode'    => 'api',
                'message' => 'Ayrshare hatası: ' . ($json['message'] ?? $resp->status()),
            ];
        } catch (\Throwable $e) {
            return ['success' => false, 'mode' => 'api', 'message' => 'Ayrshare istisna: ' . $e->getMessage()];
        }
    }
}
