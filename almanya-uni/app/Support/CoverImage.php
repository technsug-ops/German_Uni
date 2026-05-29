<?php

namespace App\Support;

/**
 * Resolves a cover image for a university card with 3-layer fallback:
 *   1. Admin-curated image_url on the row itself (truth source)
 *   2. City-landmark pool from config/city_landmarks.php (deterministic per uni)
 *   3. null — caller renders gradient + initials
 *
 * Used by universities/_grid.blade.php (and any other surface that lists unis).
 */
class CoverImage
{
    /**
     * @param array{id?:int,image_url?:?string,city_slug?:?string,name_de?:?string} $uni
     * @return array{url:?string, source:string}  source ∈ {own, pool, none}
     */
    public static function forUniversity(array $uni): array
    {
        if (! empty($uni['image_url'])) {
            return ['url' => $uni['image_url'], 'source' => 'own'];
        }

        $pool = self::cityPool($uni['city_slug'] ?? null);
        if ($pool && ! empty($uni['id'])) {
            $idx = abs(crc32((string) $uni['id'])) % count($pool);
            return ['url' => $pool[$idx], 'source' => 'pool'];
        }

        return ['url' => null, 'source' => 'none'];
    }

    /** @return list<string>|null */
    public static function cityPool(?string $citySlug): ?array
    {
        if (! $citySlug) return null;
        $all = config('city_landmarks', []);
        return $all[$citySlug] ?? null;
    }
}
