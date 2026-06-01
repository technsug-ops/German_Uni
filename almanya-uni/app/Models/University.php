<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Scout\Searchable;

class University extends Model
{
    use HasFactory;
    use Searchable;
    use \App\Models\Concerns\FulltextSearch;
    use \App\Models\Concerns\LocalizableContent;
    use \App\Models\Concerns\LocalizesContentBlocks;

    protected $fillable = [
        'wikidata_id',
        'hs_nummer',
        'partner_id',
        'name_tr',
        'name_de',
        'name_en',
        'slug',
        'short_name',
        'description_tr',
        'description_en',
        'description_de',
        'city_id',
        'latitude',
        'longitude',
        'website_url',
        'phone',
        'street',
        'postal_code',
        'logo_url',
        'image_url',
        'type',
        'hochschultyp',
        'traegerschaft',
        'promotion_recht',
        'habilitation_recht',
        'hrk_member',
        'is_uni_assist_member',
        'uni_assist_id',
        'founded_year',
        'student_count',
        'qs_world_rank',
        'the_world_rank',
        'arwu_world_rank',
        'rankings_synced_at',
        'community_mention_score',
        'community_mention_updated_at',
        'qs_academic_reputation',
        'qs_employer_reputation',
        'qs_citations_per_faculty',
        'qs_faculty_student_ratio',
        'qs_international_faculty',
        'qs_international_students',
        'qs_international_research',
        'qs_employment_outcomes',
        'qs_sustainability',
        'qs_overall_score',
        'wikipedia_url_tr',
        'wikipedia_url_en',
        'wikipedia_url_de',
        'data_source',
        'last_synced_at',
        'is_active',
        'content_blocks',
        'content_blocks_en',
        'content_blocks_de',
        'last_enriched_at',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'last_synced_at' => 'datetime',
        'is_active' => 'boolean',
        'is_uni_assist_member' => 'boolean',
        'hrk_member' => 'boolean',
        'content_blocks' => 'array',
        'content_blocks_en' => 'array',
        'content_blocks_de' => 'array',
        'last_enriched_at' => 'datetime',
        'community_mention_updated_at' => 'datetime',
        'rankings_synced_at' => 'datetime',
        'qs_academic_reputation' => 'decimal:2',
        'qs_employer_reputation' => 'decimal:2',
        'qs_citations_per_faculty' => 'decimal:2',
        'qs_faculty_student_ratio' => 'decimal:2',
        'qs_international_faculty' => 'decimal:2',
        'qs_international_students' => 'decimal:2',
        'qs_international_research' => 'decimal:2',
        'qs_employment_outcomes' => 'decimal:2',
        'qs_sustainability' => 'decimal:2',
        'qs_overall_score' => 'decimal:2',
    ];

    public const QS_INDICATORS = [
        'qs_academic_reputation'   => ['label' => 'Academic Reputation',  'weight' => 30, 'tooltip' => 'Survey of 130,000+ academics worldwide. Measures how peers rate the university\'s teaching and research excellence.'],
        'qs_employer_reputation'   => ['label' => 'Employer Reputation',  'weight' => 15, 'tooltip' => 'Survey of 75,000+ employers. Measures how graduates perform in the workplace and which universities they prefer to hire from.'],
        'qs_citations_per_faculty' => ['label' => 'Citations per Faculty', 'weight' => 20, 'tooltip' => 'Total research citations divided by number of faculty. Indicates research impact and quality.'],
        'qs_faculty_student_ratio' => ['label' => 'Faculty/Student Ratio', 'weight' => 10, 'tooltip' => 'Number of academic staff per student. Lower student-to-faculty = more individual attention.'],
        'qs_international_faculty' => ['label' => 'International Faculty',  'weight' => 5,  'tooltip' => 'Proportion of international academic staff. Indicates global academic environment.'],
        'qs_international_students'=> ['label' => 'International Students', 'weight' => 5,  'tooltip' => 'Proportion of international students. Indicates how welcoming and diverse the campus is.'],
        'qs_international_research'=> ['label' => 'International Research Network', 'weight' => 5, 'tooltip' => 'Diversity of international research partnerships and collaborations.'],
        'qs_employment_outcomes'   => ['label' => 'Employment Outcomes',   'weight' => 5,  'tooltip' => 'Graduate employability and alumni impact. Includes employment rate and high-profile alumni.'],
        'qs_sustainability'        => ['label' => 'Sustainability',        'weight' => 5,  'tooltip' => 'Environmental and social impact. Measures institution\'s contribution to climate action and ESG.'],
    ];

    /**
     * Locale-aware display name. Üni adları orijinal Almanca kalır,
     * parantezde mevcut locale'in çevirisi gözükür (varsa ve farklıysa).
     *
     *   TR: "Technische Universität München (Münih Teknik Üniversitesi)"
     *   EN: "Technische Universität München (Munich Technical University)"
     *   DE: "Technische Universität München"
     */
    /**
     * Alias so polymorphic relations (e.g. Favorite.favoriteable) can call
     * $item->name uniformly across Uni / Program / Profession / City.
     */
    public function getNameAttribute(): string
    {
        return $this->display_name;
    }

    public function getDisplayNameAttribute(): string
    {
        $de = (string) ($this->name_de ?? '');
        if ($de === '') {
            return (string) ($this->name_en ?? $this->name_tr ?? '');
        }

        $locale = app()->getLocale();
        if ($locale === 'de') return $de;

        $key = 'name_' . $locale;          // name_tr, name_en
        $tr  = (string) ($this->{$key} ?? '');

        if ($tr === '' || trim($tr) === trim($de)) {
            return $de;
        }
        return "{$de} ({$tr})";
    }

    public function getCoordinatesAttribute(): ?array
    {
        if (! $this->latitude || ! $this->longitude) {
            return null;
        }

        return [
            'latitude'  => (float) $this->latitude,
            'longitude' => (float) $this->longitude,
        ];
    }

    public function programs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Program::class);
    }

    public function favorites(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Favorite::class, 'favoriteable');
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function searchableAs(): string
    {
        return 'universities';
    }

    public function toSearchableArray(): array
    {
        $this->loadMissing('city.state');

        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name_de' => $this->name_de,
            'name_en' => $this->name_en,
            'name_tr' => $this->name_tr,
            'short_name' => $this->short_name,
            'city_name' => $this->city?->name_de,
            'city_slug' => $this->city?->slug,
            'state_name' => $this->city?->state?->name_de,
            'state_slug' => $this->city?->state?->slug,
            'type' => $this->type,
            'founded_year' => $this->founded_year,
            'student_count' => $this->student_count,
            'logo_url' => $this->logo_url,
            'has_logo' => $this->logo_url !== null,
        ];
    }

    public function shouldBeSearchable(): bool
    {
        return (bool) $this->is_active;
    }

    // ── Local WebP cache → HTTPS-force → Wikimedia thumb fallback ──
    public function getImageUrlAttribute(?string $value): ?string
    {
        if ($this->slug) {
            $localFile = public_path("img/cache/unis/{$this->slug}.webp");
            if (file_exists($localFile)) {
                return asset("img/cache/unis/{$this->slug}.webp");
            }
        }

        if (! $value) return null;
        $value = preg_replace('#^http://#i', 'https://', $value);
        return wikimedia_thumb($value, 600); // Uni building photos: 600px (cards display ~290-400px)
    }

    public function getLogoUrlAttribute(?string $value): ?string
    {
        if ($this->slug) {
            $localFile = public_path("img/cache/uni-logos/{$this->slug}.webp");
            if (file_exists($localFile)) {
                return asset("img/cache/uni-logos/{$this->slug}.webp");
            }
        }

        if (! $value) return null;
        $value = preg_replace('#^http://#i', 'https://', $value);
        return wikimedia_thumb($value, 100); // Logos display at 36px — 100px gives 2x retina headroom
    }
}
