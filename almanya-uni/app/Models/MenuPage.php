<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class MenuPage extends Model
{
    public const CACHE_KEY = 'menu_pages.all_v1';

    protected $fillable = [
        'key', 'link_type', 'url', 'label', 'icon', 'description', 'badge',
        'group', 'is_enabled', 'protect_route', 'sort_order',
    ];

    protected $casts = [
        'is_enabled'    => 'boolean',
        'protect_route' => 'boolean',
    ];

    public const GROUPS = [
        'kesfet'     => ['label' => 'Keşfet',     'emoji' => '🔍', 'color' => 'primary'],
        'araclar'    => ['label' => 'Araçlar',    'emoji' => '🛠️', 'color' => 'primary'],
        'firsatlar'  => ['label' => 'Fırsatlar',  'emoji' => '🎖️', 'color' => 'emerald'],
        'icerik'     => ['label' => 'İçerik',     'emoji' => '📚', 'color' => 'primary'],
        'standalone' => ['label' => 'Standalone', 'emoji' => '🔗', 'color' => 'gray'],
    ];

    protected static function booted(): void
    {
        $clear = fn () => static::flushCache();
        static::saved($clear);
        static::deleted($clear);
    }

    public function scopeEnabled(Builder $q): Builder
    {
        return $q->where('is_enabled', true);
    }

    public function scopeGroup(Builder $q, string $group): Builder
    {
        return $q->where('group', $group);
    }

    public function getLabelAttribute(?string $value): ?string
    {
        return $value ? __($value) : $value;
    }

    public function getDescriptionAttribute(?string $value): ?string
    {
        return $value ? __($value) : $value;
    }

    public function getResolvedUrlAttribute(): ?string
    {
        if ($this->link_type === 'url') {
            return $this->url;
        }
        if (! \Illuminate\Support\Facades\Route::has($this->key)) {
            return null;
        }
        try {
            return route($this->key);
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Bütün enabled menü öğelerini cache'le (kalıcı, kaydetme/silmede temizlenir).
     * Filament admin sadece kaydet/sil yapar — cache otomatik invalidate olur.
     */
    public static function cached(): Collection
    {
        $rows = Cache::rememberForever(self::CACHE_KEY, function () {
            return static::enabled()
                ->orderBy('group')
                ->orderBy('sort_order')
                ->orderBy('label')
                ->get()
                ->toArray();
        });

        // Array'i tekrar Eloquent Collection'a hydrate et (cache serialization güvenli)
        return new Collection(
            array_map(fn ($row) => (new static())->forceFill($row)->syncOriginal(), $rows)
        );
    }

    /**
     * Belirli bir grup için cache'lenmiş ve sıralı liste.
     */
    public static function forGroup(string $group): Collection
    {
        return static::cached()->where('group', $group)->values();
    }

    /**
     * Route name'i ile MenuPage objesi (cache'ten). Middleware kullanır.
     */
    public static function findByKey(string $key): ?self
    {
        return static::cached()->firstWhere('key', $key);
    }

    /**
     * Belirli bir route name yayında mı? Middleware için hızlı kontrol.
     * Cache'lenmiş enabled listesinde yoksa false (varsayılan kapalı değil — DB'de yoksa ya hiç eklenmedi ya da disabled).
     */
    public static function isKeyEnabled(string $key): bool
    {
        $row = static::statusMap()[$key] ?? null;
        return $row === null ? true : (bool) $row['is_enabled'];
    }

    /**
     * Belirli bir route name için protect_route ayarı (cache'ten).
     */
    public static function isKeyProtected(string $key): bool
    {
        $row = static::statusMap()[$key] ?? null;
        return $row === null ? false : (bool) $row['protect_route'];
    }

    /**
     * Cache'lenmiş key → [is_enabled, protect_route] map'i.
     * Middleware her request'te kullanır, performans kritik.
     */
    public static function statusMap(): array
    {
        return Cache::rememberForever('menu_pages.statusmap_v1', function () {
            return static::query()
                ->get(['key', 'is_enabled', 'protect_route'])
                ->keyBy('key')
                ->map(fn ($r) => ['is_enabled' => (bool) $r->is_enabled, 'protect_route' => (bool) $r->protect_route])
                ->toArray();
        });
    }

    /**
     * Cache invalidation (saved/deleted hook'ları + admin'den manuel).
     */
    public static function flushCache(): void
    {
        Cache::forget(self::CACHE_KEY);
        Cache::forget('menu_pages.statusmap_v1');
    }
}
