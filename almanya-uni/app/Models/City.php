<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class City extends Model
{
    use HasFactory;
    use \App\Models\Concerns\FulltextSearch;

    protected $fillable = [
        'wikidata_id', 'state_id',
        'name_tr', 'name_de', 'name_en', 'slug',
        'latitude', 'longitude', 'population',
        'is_active', 'image_url', 'gallery_images', 'gallery_image_urls', 'video_url',
        'content_blocks', 'content_blocks_en', 'content_blocks_de', 'last_enriched_at',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'is_active' => 'boolean',
        'gallery_images' => 'array',
        'gallery_image_urls' => 'array',
        'content_blocks' => 'array',
        'content_blocks_en' => 'array',
        'content_blocks_de' => 'array',
        'private_chain_slugs' => 'array',
        'last_enriched_at' => 'datetime',
    ];

    /**
     * Galeri görsel URL'leri: önce yüklenen fotoğraflar, sonra elle girilen dış URL'ler.
     * Yüklenenler storage path → public URL'e çevrilir; URL'ler olduğu gibi kalır.
     */
    public function galleryUrls(): array
    {
        $uploads = collect($this->gallery_images ?? []);
        $external = collect($this->gallery_image_urls ?? [])
            ->map(fn ($i) => is_array($i) ? ($i['url'] ?? null) : $i);

        return $uploads->merge($external)
            ->filter()
            ->map(fn ($p) => \Illuminate\Support\Str::startsWith($p, ['http://', 'https://'])
                ? $p
                : \Illuminate\Support\Facades\Storage::disk('public')->url($p))
            ->values()
            ->all();
    }

    /** Hero arka planı için ilk galeri görseli (güvenilir self-host); yoksa null → gradient. */
    public function getHeroImageUrlAttribute(): ?string
    {
        return $this->galleryUrls()[0] ?? null;
    }

    /** YouTube video → embed URL (watch?v= / youtu.be → /embed/). */
    public function getVideoEmbedUrlAttribute(): ?string
    {
        if (! $this->video_url) {
            return null;
        }
        if (preg_match('~(?:youtube\.com/watch\?v=|youtu\.be/|youtube\.com/embed/)([\w-]{11})~', $this->video_url, $m)) {
            return 'https://www.youtube.com/embed/' . $m[1];
        }

        return null;
    }

    /**
     * Locale-aware enrichment blocks. TR → source content_blocks; EN/DE →
     * translated content_blocks_{locale} (null until translated, so the blade
     * hides them instead of leaking Turkish). See doc/MULTILANG-PLAN (Enrichment-B).
     */
    public function localizedBlocks(?string $locale = null): ?array
    {
        $locale ??= app()->getLocale();
        if ($locale === 'tr') {
            return $this->content_blocks;
        }
        return $this->{'content_blocks_' . $locale} ?: null;
    }

    public function getNameAttribute(): string
    {
        $locale = app()->getLocale();
        $key = 'name_' . $locale; // name_tr, name_en, name_de
        $value = $this->attributes[$key] ?? null;
        if (empty($value)) {
            // fallback: en → de → tr
            foreach (['name_en', 'name_de', 'name_tr'] as $fb) {
                if (! empty($this->attributes[$fb] ?? null)) {
                    $value = $this->attributes[$fb];
                    break;
                }
            }
        }

        // TR locale'de Almanca disambiguation kuyruğunu gizle:
        //   "Frankfurt am Main" → "Frankfurt"
        //   "Neustadt an der Weinstraße" → "Neustadt"
        // "Frankfurt (Oder)" gibi parantezli ayrımlar etkilenmez (TR'de gerekli).
        if ($locale === 'tr' && $value) {
            $value = preg_replace('/\s+am\s+\S+$/u', '', $value);
            $value = preg_replace('/\s+an\s+der\s+\S+$/u', '', $value);
        }

        return $value ?: '';
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    public function universities(): HasMany
    {
        return $this->hasMany(University::class);
    }

    /**
     * Bu şehirde EK kampüsü olan üniler (birincil şehri başka ama burada da fakültesi var).
     * Çok-kampüslü üniler için (ör. Duisburg sayfasında Universität Duisburg-Essen).
     */
    public function campusUniversities(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(University::class, 'university_campuses');
    }

    /**
     * KANONİK aktif üni sayısı — "dışarıdan" (liste/kart/eyalet) sayım için TEK kaynak.
     *
     * Birincil (city_id) + kampüs (university_campuses) aktif ünilerin DISTINCT birleşimi.
     * `UNION` kullanır → hem birincil/kampüs örtüşmesini hem MÜKERRER kampüs satırlarını
     * otomatik tekilleştirir. Böylece `universities_count`, detay sayfasındaki
     * `$city->universities->count()` (concat+unique merge) ile BİREBİR eşittir.
     *
     * Geçmiş bug: eski `count(*)` toplamı mükerrer kampüs satırını şişiriyordu (Cottbus:
     * liste 3 / sayfa 2) ve kampüs-farkında olmayan `withCount` eksik sayıyordu
     * (Berlin: liste 47 / sayfa 57). Tek kaynak → kart ile detay her zaman uyumlu.
     */
    public function scopeWithCampusAwareUniCount($query)
    {
        // İki korelasyonlu SCALAR subquery toplamı — DERIVED TABLE (FROM-subquery) KULLANMA:
        // korelasyonlu derived table (içinde `cities.id` referansı) MariaDB'de (KAS prod)
        // desteklenmez → "Unknown column 'cities.id'" → 500. Lokal MySQL 8.0.14+ destekler,
        // o yüzden lokal'de fark edilmemişti. Bu additive form her MySQL/MariaDB'de çalışır.
        //
        // university_campuses'te (university_id, city_id) UNIQUE kısıt var → mükerrer satır
        // imkânsız; `u2.city_id <> cities.id` ile birincil/kampüs örtüşmesi dışlanır → sonuç
        // distinct birleşimle BİREBİR aynı = detay sayfasındaki unique-merge count.
        return $query->selectRaw('(
            (select count(*) from universities u
                where u.city_id = cities.id and u.is_active = 1)
            + (select count(*) from university_campuses uc
                inner join universities u2 on u2.id = uc.university_id
                where uc.city_id = cities.id and u2.is_active = 1
                  and (u2.city_id is null or u2.city_id <> cities.id))
        ) as universities_count');
    }

    public function costData(): HasOne
    {
        return $this->hasOne(CityCostData::class);
    }

    // HTTPS-force + local WebP cache (Wikimedia rate-limit immunity) + thumbnail size accessor
    public function getImageUrlAttribute(?string $value): ?string
    {
        // Local WebP cache (populated by `php artisan images:cache-hot`)
        if ($this->slug) {
            $localFile = public_path("img/cache/cities/{$this->slug}.webp");
            if (file_exists($localFile)) {
                return asset("img/cache/cities/{$this->slug}.webp");
            }
        }

        if (! $value) return null;
        $value = preg_replace('#^http://#i', 'https://', $value);
        return wikimedia_thumb($value, 500); // Cards display 186-290px — 500px covers retina
    }
}
