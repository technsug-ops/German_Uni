<?php

namespace App\Services\Social\Drivers;

use App\Models\ContentAsset;
use App\Services\Social\Contracts\SocialPublisher;
use App\Services\Social\PlatformMap;

/**
 * Faz 1 — API'siz elle-asistan sürücü. Gerçek post atmaz; paylaşıma hazır
 * payload üretir (metin + medya URL'leri + hazır-metinli intent linki). Kullanıcı
 * platforma basar, sonra kokpitte "Paylaşıldı" ile published_url'i kaydeder.
 * KAS-uyumlu, ücretsiz, onay gerektirmez.
 */
class ManualPublisher implements SocialPublisher
{
    public function key(): string
    {
        return 'manual';
    }

    public function label(): string
    {
        return 'Manuel-asistan (API yok)';
    }

    public function isAutomatic(): bool
    {
        return false;
    }

    public function isConfigured(): bool
    {
        return true;
    }

    public function publish(ContentAsset $asset, ?string $pageUrl = null): array
    {
        $type = $asset->asset_type;
        $text = trim((string) $asset->body_md);
        $media = $this->mediaUrls($asset);
        $firstMedia = $media[0] ?? null;

        return [
            'success' => true,
            'mode'    => 'manual',
            'message' => 'Paylaşıma hazır — metni kopyala, platformu aç, paylaştıktan sonra "Paylaşıldı" ile linki kaydet.',
            'share'   => [
                'platform'        => $type,
                'label'           => PlatformMap::label($type),
                'text'            => $text,
                'media'           => $media,
                'intent_url'      => PlatformMap::intentUrl($type, $text, $pageUrl, $firstMedia),
                'open_url'        => PlatformMap::openUrl($type),
                'supports_intent' => PlatformMap::supportsIntent($type),
            ],
        ];
    }

    /** Asset media kolonundan public URL listesi (ImageGenerationService: {url,...}). */
    private function mediaUrls(ContentAsset $asset): array
    {
        $out = [];
        foreach ((array) ($asset->media ?? []) as $m) {
            if (is_array($m) && ! empty($m['url'])) {
                $out[] = $m['url'];
            } elseif (is_string($m) && $m !== '') {
                $out[] = str_starts_with($m, 'http') ? $m : asset($m);
            }
        }
        return $out;
    }
}
