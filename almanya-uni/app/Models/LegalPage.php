<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Schema;

class LegalPage extends Model
{
    /** İçerik üretilen diller — bkz. config/locales, "şimdilik 3 dil". */
    public const LOCALES = ['tr', 'en', 'de'];

    /** Rollout guard'ının konteyner anahtarı. */
    private const TABLE_CHECK = 'legal_page_translations_exists';

    protected $fillable = [
        'key',
        'titles',
        'descriptions',
        'bodies',
        'effective_date',
        'is_published',
        'sort_order',
    ];

    protected $casts = [
        'titles' => 'array',
        'descriptions' => 'array',
        'bodies' => 'array',
        'effective_date' => 'date',
        'is_published' => 'boolean',
    ];

    public function translations(): HasMany
    {
        return $this->hasMany(LegalPageTranslation::class);
    }

    /**
     * ROLLOUT GUARD (Aşama A) — Faz B'de legacy kolonlarla birlikte kaldırılacak.
     *
     * Deploy sırası "önce kod, sonra migration" olduğu için, yeni model canlıya
     * çıkmışken `legal_page_translations` henüz yaratılmamış bir pencere var.
     * Guard olmasaydı bu pencerede her hukuki sayfa 500 verirdi (tablo yok →
     * QueryException). Tablo yoksa okumalar doğrudan legacy JSON'a düşer —
     * AMA yalnızca istenen locale'in kendi değerine; cross-locale fallback yok.
     *
     * Sonuç konteynerde tutulur: istek başına tek information_schema sorgusu,
     * testlerde de her testin taze app'iyle sıfırlanır.
     */
    public static function translationsTableAvailable(): bool
    {
        if (! app()->bound(self::TABLE_CHECK)) {
            app()->instance(self::TABLE_CHECK, Schema::hasTable('legal_page_translations'));
        }

        return (bool) app()->make(self::TABLE_CHECK);
    }

    /** Test/komut içinde şema değişince guard'ı tazelemek için. */
    public static function flushTranslationsTableCheck(): void
    {
        app()->forgetInstance(self::TABLE_CHECK);
    }

    public static function findByKey(string $key): ?self
    {
        // Cache deliberately disabled: prod was flapping 500s on /tr/impressum and
        // /tr/cerez-politikasi (same controller, same view, others worked) — telltale
        // sign of a poisoned serialized cache value across shared workers. Legal
        // pages are visited rarely; a single indexed lookup costs <1ms, the cache
        // saved nothing meaningful but cost us availability. Re-enable later if
        // traffic justifies, with a cache key version (e.g. legal_page_v2_$key).
        $query = self::where('key', $key)->where('is_published', true);

        // Tablo henüz yoksa eager-load QueryException fırlatırdı.
        if (self::translationsTableAvailable()) {
            $query->with('translations');
        }

        return $query->first();
    }

    /**
     * İstenen dildeki çeviri — BAŞKA DİLE DÜŞMEZ.
     *
     * Hukuki sayfalarda sessiz cross-locale fallback yasak: /en/privacy isteyen
     * birine Türkçe gizlilik metni göstermek, okuduğunu sandığı şeyle bağlandığı
     * şeyin farklı olması demek. Çeviri yoksa null döner ve çağıran taraf
     * (LegalController) 404 verir.
     */
    public function translation(?string $locale = null): ?LegalPageTranslation
    {
        if (! self::translationsTableAvailable()) {
            return null;
        }

        $locale = $locale ?: app()->getLocale();

        return $this->translations
            ->firstWhere('locale', $locale);
    }

    /** O dilde yayımlanabilir bir gövde var mı? */
    public function hasContentFor(?string $locale = null): bool
    {
        return trim($this->getBody($locale)) !== '';
    }

    public function getTitle(?string $locale = null): string
    {
        $title = $this->localeContent('title', 'titles', $locale);

        return $title !== null && trim($title) !== '' ? $title : ucfirst($this->key);
    }

    public function getDescription(?string $locale = null): ?string
    {
        return $this->localeContent('description', 'descriptions', $locale);
    }

    public function getBody(?string $locale = null): string
    {
        return $this->localeContent('body', 'bodies', $locale) ?? '';
    }

    /**
     * Tek bir dilin içeriğini getirir.
     *
     * Sıra: (1) o dilin çeviri kaydı, (2) çeviri kaydı HİÇ yoksa legacy JSON'daki
     * AYNI DİLİN değeri, (3) null.
     *
     * (2) iki aşamalı rollout'un köprüsü: 000300 backfill'i herhangi bir sebeple
     * koşmazsa 15 hukuki sayfa birden 404 vermesin — Impressum'un erişilebilirliği
     * § 5 DDG gereği. Bu köprü SADECE istenen dilin kendi değerini okur; başka
     * dilin metnine düşmek hâlâ imkânsız. Sayfanın çevirileri var ama istenen dil
     * yoksa bilinçli olarak null döner (o dil gerçekten eksik → 404).
     *
     * Legacy JSON kolonları düşürüldüğünde (Aşama B) bu adım de kalkacak.
     */
    private function localeContent(string $column, string $legacyJsonColumn, ?string $locale): ?string
    {
        $locale = $locale ?: app()->getLocale();

        if (! self::translationsTableAvailable()) {
            return $this->legacyJson($legacyJsonColumn)[$locale] ?? null;
        }

        $row = $this->translation($locale);

        if ($row) {
            return $row->{$column};
        }

        if ($this->translations->isNotEmpty()) {
            return null;
        }

        $legacy = $this->legacyJson($legacyJsonColumn);

        return $legacy[$locale] ?? null;
    }

    /**
     * Render markdown body to HTML.
     * Uses Laravel's built-in Str::markdown when available; falls back to raw HTML.
     */
    public function getRenderedBody(?string $locale = null): string
    {
        $body = $this->getBody($locale);
        if (! $body) return '';

        // If it already looks like HTML (starts with a tag), return as-is.
        if (preg_match('/^\s*</', $body)) {
            return $body;
        }

        // Otherwise render Markdown.
        return \Illuminate\Support\Str::markdown($body, [
            'html_input' => 'allow',
            'allow_unsafe_links' => false,
        ]);
    }

    /* ---------------------------------------------------------------------
     | Panel / seeder uyumluluğu
     |
     | Form ve seeder hâlâ titles[tr] / bodies[en] gibi dizilerle konuşuyor.
     | Okurken bu diziler ÇEVİRİ KAYITLARINDAN türetilir (legacy JSON'dan
     | değil), yazarken de çeviri kayıtlarına bölünür. Böylece panelde her dil
     | ayrı satır olarak düzenlenir ve EN'i kaydetmek TR/DE satırına dokunmaz.
     --------------------------------------------------------------------- */

    /** @return array<string, string> */
    public function getTitlesAttribute(): array
    {
        return $this->fromTranslations('title', 'titles');
    }

    /** @return array<string, string> */
    public function getDescriptionsAttribute(): array
    {
        return $this->fromTranslations('description', 'descriptions');
    }

    /** @return array<string, string> */
    public function getBodiesAttribute(): array
    {
        return $this->fromTranslations('body', 'bodies');
    }

    /**
     * Çeviri satırları varsa onlardan oku; henüz yoksa (000300 backfill'inden
     * önceki pencere, ya da yeni oluşturulup daha kaydedilmemiş model) legacy
     * JSON kolonuna düş. Bu, dil-içi bir yedek — cross-locale fallback DEĞİL.
     *
     * @return array<string, string>
     */
    private function fromTranslations(string $column, string $legacyJsonColumn): array
    {
        if (! $this->exists || ! self::translationsTableAvailable()) {
            return $this->legacyJson($legacyJsonColumn);
        }

        $rows = $this->relationLoaded('translations')
            ? $this->translations
            : $this->translations()->get();

        if ($rows->isEmpty()) {
            return $this->legacyJson($legacyJsonColumn);
        }

        return $rows
            ->mapWithKeys(fn ($t) => [$t->locale => (string) ($t->{$column} ?? '')])
            ->all();
    }

    /** @return array<string, string> */
    private function legacyJson(string $column): array
    {
        $raw = $this->attributes[$column] ?? null;

        if (is_array($raw)) {
            return $raw;
        }

        $value = json_decode((string) $raw, true);

        return is_array($value) ? $value : [];
    }

    /**
     * Diziyi locale kayıtlarına böler. Yalnızca gelen dillere dokunur:
     * payload'da 'en' varsa yalnız EN satırı yazılır, TR/DE satırları olduğu
     * gibi kalır.
     *
     * @param  array<string, string|null>  $titles
     * @param  array<string, string|null>  $descriptions
     * @param  array<string, string|null>  $bodies
     */
    public function syncTranslations(array $titles, array $descriptions, array $bodies): void
    {
        if (! self::translationsTableAvailable()) {
            return;
        }

        $locales = array_unique(array_merge(
            array_keys($titles),
            array_keys($descriptions),
            array_keys($bodies),
        ));

        foreach ($locales as $locale) {
            $body = $bodies[$locale] ?? null;

            // Gövdesi olmayan dil için kayıt açma — sahte/boş hukuki metin
            // yayımlamaktansa o dilde içerik olmadığını kabul et.
            if (! is_string($body) || trim($body) === '') {
                continue;
            }

            $title = $titles[$locale] ?? null;

            $this->translations()->updateOrCreate(
                ['locale' => $locale],
                [
                    'title'       => is_string($title) && trim($title) !== '' ? $title : ucfirst($this->key),
                    'description' => $descriptions[$locale] ?? null,
                    'body'        => $body,
                ]
            );
        }

        $this->unsetRelation('translations');
    }

    protected static function booted(): void
    {
        // Panel/seeder JSON dizileriyle kaydediyor → çeviri satırlarına böl.
        // Legacy JSON kolonları bilerek yazılmaya devam ediyor: okumalar artık
        // oraya bakmıyor, ama iki aşamalı rollout'un (A: taşı, B: kolonu düşür)
        // B adımına kadar geri dönüş güvencesi olarak ayna tutuluyor. Kolonlar
        // ayrı bir migration'la düşürüldüğünde burası da sadeleşecek.
        static::saved(function (self $page) {
            // Yalnızca JSON alanları gerçekten değiştiyse senkronla. Aksi hâlde
            // (örn. sadece is_published toggle'ı) bayat aynadan yazıp çeviri
            // satırında yapılmış bir düzenlemeyi geri alma riski doğardı.
            if (! $page->wasRecentlyCreated && ! $page->wasChanged(['titles', 'descriptions', 'bodies'])) {
                return;
            }

            $titles = $page->legacyJson('titles');
            $descriptions = $page->legacyJson('descriptions');
            $bodies = $page->legacyJson('bodies');

            if ($bodies !== []) {
                $page->syncTranslations($titles, $descriptions, $bodies);
            }
        });
    }
}
