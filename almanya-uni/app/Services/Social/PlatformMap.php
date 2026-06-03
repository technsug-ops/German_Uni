<?php

namespace App\Services\Social;

/**
 * asset_type (ContentAsset) → sosyal platform meta verisi.
 *
 * - SOCIAL: yayın kokpitinde gösterilen kanallar (blog hariç — onun kendi akışı var).
 * - intentUrl(): destekleyen platformlarda "hazır metinle aç" web-intent linki.
 * - openUrl(): intent yoksa elle paylaşım için platformun paylaşım/yükleme sayfası.
 * - ayrshare(): Faz 2 (AyrsharePublisher) için platform API adı.
 */
class PlatformMap
{
    /** Kokpitte yönetilen sosyal asset türleri. */
    public const SOCIAL = [
        'instagram', 'twitter', 'tiktok', 'linkedin',
        'pinterest', 'youtube_long', 'youtube_short', 'social_carousel',
    ];

    /**
     * type => [label, open(elle paylaşım URL'i), ayrshare platform adı]
     */
    private const MAP = [
        'instagram'       => ['📸 Instagram',        'https://www.instagram.com/',       'instagram'],
        'social_carousel' => ['🎠 Instagram Carousel','https://www.instagram.com/',       'instagram'],
        'twitter'         => ['🐦 X (Twitter)',       'https://twitter.com/compose/tweet','twitter'],
        'tiktok'          => ['🎵 TikTok',            'https://www.tiktok.com/upload',    'tiktok'],
        'linkedin'        => ['💼 LinkedIn',          'https://www.linkedin.com/feed/',   'linkedin'],
        'pinterest'       => ['📌 Pinterest',         'https://www.pinterest.com/pin-builder/', 'pinterest'],
        'youtube_long'    => ['🎬 YouTube',           'https://studio.youtube.com/',      'youtube'],
        'youtube_short'   => ['⏱️ YouTube Shorts',    'https://studio.youtube.com/',      'youtube'],
    ];

    public static function isSocial(string $type): bool
    {
        return in_array($type, self::SOCIAL, true);
    }

    public static function label(string $type): string
    {
        return self::MAP[$type][0] ?? $type;
    }

    /** Elle paylaşım için platformun paylaşım/yükleme sayfası. */
    public static function openUrl(string $type): string
    {
        return self::MAP[$type][1] ?? 'https://www.google.com/';
    }

    /** Ayrshare API platform adı (Faz 2). */
    public static function ayrshare(string $type): ?string
    {
        return self::MAP[$type][2] ?? null;
    }

    /**
     * Destekleyen platformlarda hazır-metinli web-intent linki döner; yoksa null.
     * Desteklemeyenler (Instagram/TikTok/YouTube) elle paylaşılır → openUrl().
     */
    public static function intentUrl(string $type, string $text, ?string $pageUrl = null, ?string $mediaUrl = null): ?string
    {
        $t = rawurlencode(mb_substr(trim($text), 0, 270));
        $u = $pageUrl ? rawurlencode($pageUrl) : '';

        return match ($type) {
            'twitter'   => 'https://twitter.com/intent/tweet?text=' . $t . ($u ? '&url=' . $u : ''),
            'linkedin'  => 'https://www.linkedin.com/feed/?shareActive=true&text=' . $t,
            'pinterest' => 'https://www.pinterest.com/pin/create/button/?'
                . ($u ? 'url=' . $u . '&' : '')
                . ($mediaUrl ? 'media=' . rawurlencode($mediaUrl) . '&' : '')
                . 'description=' . $t,
            default     => null, // instagram, tiktok, youtube_*, social_carousel → elle
        };
    }

    /** Bu platform hazır-metinli intent destekliyor mu? */
    public static function supportsIntent(string $type): bool
    {
        return in_array($type, ['twitter', 'linkedin', 'pinterest'], true);
    }
}
