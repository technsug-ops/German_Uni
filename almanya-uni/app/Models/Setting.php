<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Key-value global ayar deposu (settings tablosu).
 *
 * Tüm değerler tek cache anahtarında ('settings.all', forever) tutulur —
 * her sayfada DB sorgusu olmaz. set() / silme cache'i temizler.
 *
 * Kullanım:
 *   Setting::get('google_analytics_id')           → değer | null
 *   Setting::get('foo', 'varsayılan')             → değer | 'varsayılan'
 *   Setting::set('google_analytics_id', 'G-XXX')  → kaydet + cache temizle
 *   setting('google_analytics_id')                → helper (aynısı)
 */
class Setting extends Model
{
    protected $primaryKey = 'key';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['key', 'value', 'group'];

    public const CACHE_KEY = 'settings.all';

    /** Tüm ayarları key=>value map olarak (cache'li) döndür. */
    public static function map(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            // pluck DB sürücüsünden bağımsız çalışır; tablo yoksa (migrate öncesi) boş döner
            try {
                return static::query()->pluck('value', 'key')->all();
            } catch (\Throwable $e) {
                return [];
            }
        });
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $value = static::map()[$key] ?? null;

        return ($value === null || $value === '') ? $default : $value;
    }

    public static function set(string $key, mixed $value, string $group = 'general'): void
    {
        static::query()->updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'group' => $group],
        );

        Cache::forget(self::CACHE_KEY);
    }

    public static function flushCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
