<?php

namespace App\Support;

use App\Models\Post;

/**
 * ApplyToGerman Almanya Öğrenci İstatistikleri Raporu 2026 — tek çeviri grubu (TR/EN/DE) olarak posts tablosunda.
 * Veri: database/data/stats-report-2026 (posts.json + report.{tr,en,de}.md). Rapor yayımlanana kadar her dilde yalnız
 * yayındaki kaydın adresi döner; taslakken sayfalar rapora kırık bağlantı vermez (adı düz metin kalır).
 */
final class StudentStatisticsReport
{
    public const GROUP = '4f0a7c6e-2d3b-4e8a-9c1d-7b5e6a2f9d31';

    public const DIR = 'database/data/stats-report-2026';

    /** İstek içi önbellek: locale → Post|null. */
    private static array $published = [];

    public static function definition(): array
    {
        return json_decode(file_get_contents(base_path(self::DIR . '/posts.json')), true, 512, JSON_THROW_ON_ERROR);
    }

    /** Bu dilde YAYINDAKİ rapor yazısı (taslak → null). */
    public static function published(?string $locale = null): ?Post
    {
        $locale ??= app()->getLocale();
        if (! array_key_exists($locale, self::$published)) {
            self::$published[$locale] = Post::blogType()
                ->where('translation_group_id', self::GROUP)
                ->where('locale', $locale)
                ->where('is_published', true)
                ->whereNotNull('published_at')
                ->where('published_at', '<=', now())
                ->first(['id', 'slug', 'type', 'locale', 'title']);
        }

        return self::$published[$locale];
    }

    public static function url(?string $locale = null): ?string
    {
        $post = self::published($locale);

        return $post ? $post->publicUrl($locale ?? app()->getLocale()) : null;
    }

    /** Çevrilmiş rapor adı; yayındaysa aynı dildeki yazıya bağlantılı, değilse düz (kaçışlı) metin. */
    public static function nameHtml(?string $locale = null): string
    {
        $name = e(__('ApplyToGerman Germany Student Statistics Report 2026'));
        $url = self::url($locale);

        return $url ? '<a href="' . e($url) . '" class="underline hover:text-primary-700">' . $name . '</a>' : $name;
    }

    /** Testler için istek içi önbelleği boşaltır. */
    public static function flush(): void
    {
        self::$published = [];
    }
}
