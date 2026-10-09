<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Program extends Model
{
    use HasFactory;
    use \App\Models\Concerns\FulltextSearch;
    use \App\Models\Concerns\LocalizableContent;

    protected $fillable = [
        'university_id',
        'field_of_study_id',
        'partner_id',
        'partner_university_name',
        'name_de',
        'name_en',
        'name_tr',
        'slug',
        'degree',
        'degree_specification',
        'language',
        'duration_semesters',
        'study_form',
        'location',
        'admission_mode',
        'admission_summary',
        'nc_value',
        'subjects',
        'study_fields_raw',
        'tuition_fee_eur',
        'application_fee_eur',
        'cost_per_semester_eur',
        'application_deadline_summer',
        'application_deadline_winter',
        'source_url',
        'source',
        'source_id',
        'description_tr',
        'description_en',
        'qualification_requirements_tr',
        'language_requirements_tr',
        'required_documents_tr',
        'qualification_requirements_en',
        'language_requirements_en',
        'required_documents_en',
        'last_synced_at',
        'is_active',
        'image_url',
        'language_level_de',
        'language_level_en',
        'is_online',
        'financial_support',
        'support_info',
        'start_semester',
        // Doğrulama katmanı (V1) — import kaynağı source/source_url/last_synced_at'te kalır
        'official_program_url',
        'application_method',
        'application_url',
        'uni_assist_required',
        'vpd_required',
    ];

    protected $casts = [
        'subjects'                    => 'array',
        'study_fields_raw'            => 'array',
        'application_deadline_summer' => 'date',
        'application_deadline_winter' => 'date',
        'last_synced_at'              => 'datetime',
        'is_active'                   => 'boolean',
        'is_online'                   => 'boolean',
        'nc_value'                    => 'decimal:2',
    ];

    public function university(): BelongsTo
    {
        return $this->belongsTo(University::class);
    }

    public function field(): BelongsTo
    {
        return $this->belongsTo(FieldOfStudy::class, 'field_of_study_id');
    }

    public function favorites(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Favorite::class, 'favoriteable');
    }

    public function verifications(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ProgramVerification::class);
    }

    protected static function booted(): void
    {
        // Sync/düzenleme sonrası: doğrulanmış alanın değeri değiştiyse kayıt NEEDS_REVIEW olur (verified_at korunur).
        // Query-builder ile yazan akışlar için ayrıca programs:verification-reconcile komutu vardır.
        static::saved(function (self $p) {
            if ($p->wasChanged() && ProgramVerification::where('program_id', $p->id)->where('status', ProgramVerification::VERIFIED)->exists()) {
                ProgramVerification::reconcile($p);
            }
        });
    }

    private function verificationRows(): \Illuminate\Support\Collection
    {
        return $this->relationLoaded('verifications') ? $this->verifications : $this->verifications()->get();
    }

    /** Program kimliği resmî kaynakla çelişiyor mu (yanlış üniversite/program/derece/ad)? */
    public function hasIdentityConflict(): bool
    {
        return $this->verificationRows()->where('field', 'identity')->where('status', ProgramVerification::CONFLICT)->isNotEmpty();
    }

    /** Alanda resmî kaynakla çelişki kaydı var mı (sayfada değerin yanında uyarı gösterilir)? */
    public function hasFieldConflict(string $field): bool
    {
        return $this->verificationRows()->where('field', $field)->where('status', ProgramVerification::CONFLICT)->isNotEmpty();
    }

    /**
     * SAYFADA gösterilebilecek doğrulanmış kayıtlar. Üç koruma:
     *  1) durum VERIFIED (NEEDS_REVIEW/CONFLICT asla doğrulanmış gibi sunulmaz);
     *  2) program kimliğinde çelişki yok (yanlış programın kaynağından gelen bilgi doğru programa doğrulanmış sayılmaz);
     *  3) programdaki değer doğrulama anındaki parmak iziyle hâlâ AYNI — query-builder güncellemesinden sonra,
     *     reconcile henüz çalışmamış olsa bile değişmiş değer eski doğrulama etiketiyle gösterilmez.
     */
    public function verifiedRecords(string $field): \Illuminate\Support\Collection
    {
        if ($field !== 'identity' && $this->hasIdentityConflict()) {
            return collect();
        }
        $fp = ProgramVerification::fingerprint($this, $field);

        return $this->verificationRows()
            ->where('field', $field)
            ->where('status', ProgramVerification::VERIFIED)
            ->filter(fn ($v) => $v->program_value_fingerprint === $fp)
            ->values();
    }

    /** Alanın sayfada gösterilebilir en yeni doğrulaması (bkz. verifiedRecords korumaları). */
    public function verifiedRecord(string $field): ?ProgramVerification
    {
        return $this->verifiedRecords($field)->sortByDesc('verified_at')->first();
    }

    /** Resmî program bağlantısı yalnız URL VAR ve program kimliği o kaynakta (hâlâ geçerli biçimde) doğrulandıysa. */
    public function hasVerifiedOfficialUrl(): bool
    {
        return filled($this->official_program_url) && ! $this->hasIdentityConflict() && $this->verifiedRecord('identity') !== null;
    }

    /** Admin özeti: ['verified' => n, 'total' => 8, 'needs_review' => n, 'conflict' => n] — alan başına en iyi durum. */
    public function verificationSummary(): array
    {
        $rows = $this->relationLoaded('verifications') ? $this->verifications : $this->verifications()->get();
        $byField = $rows->groupBy('field');
        $count = fn ($status) => $byField->filter(fn ($g) => $g->contains('status', $status))->count();

        return [
            'verified' => $byField->filter(fn ($g) => $g->contains('status', ProgramVerification::VERIFIED)
                && ! $g->contains(fn ($v) => in_array($v->status, [ProgramVerification::CONFLICT, ProgramVerification::NEEDS_REVIEW], true)))->count(),
            'total' => count(ProgramVerification::FIELDS),
            'needs_review' => $count(ProgramVerification::NEEDS_REVIEW),
            'conflict' => $count(ProgramVerification::CONFLICT),
        ];
    }

    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }

    /**
     * Pasifleştirilmiş (dedupe/merge) bir programın yerine geçen AKTİF kayıt: aynı üniversite + derece, ad_de/ad_en
     * çapraz birebir (programs:dedupe ve 2026-10-09 merge migration'larıyla aynı kural). Yoksa null.
     */
    public function activeCounterpart(): ?self
    {
        $names = array_values(array_filter([$this->name_de, $this->name_en]));
        if ($names === []) {
            return null;
        }

        return self::where('university_id', $this->university_id)->where('degree', $this->degree)->where('is_active', true)
            ->whereKeyNot($this->getKey())
            ->where(fn ($q) => $q->whereIn('name_de', $names)->orWhereIn('name_en', $names))
            ->orderByRaw("source IN ('daad', 'partner')")->orderBy('id')
            ->first(['id', 'slug']);
    }

    public function scopeOfDegree($q, string $degree)
    {
        return $q->where('degree', $degree);
    }

    public function getLanguagesArrayAttribute(): array
    {
        if (! $this->language) {
            return [];
        }
        return array_map('trim', explode(',', $this->language));
    }

    /**
     * Programlarda GÖRÜNEN birincil isim DAİMA resmi isimdir (name_de) —
     * başvuru doğruluğu + SEO için (title/meta/h1/breadcrumb/arama/JSON-LD).
     * name_tr/name_en sadece <x-program-name> içinde küçük punto YARDIMCI
     * olarak gösterilir; asıl ismi EZMEZ. Bu, LocalizableContent'in
     * locale-aware getNameAttribute'unu programlar için bilinçli ezer
     * (name_tr dolunca her yerin Türkçeye dönmesini engeller).
     */
    public function getNameAttribute(): ?string
    {
        foreach (['name_de', 'name_en', 'name_tr'] as $c) {
            if (! empty($this->attributes[$c] ?? null)) {
                return $this->attributes[$c];
            }
        }
        return null;
    }

    /**
     * Sayfa dilindeki isim karşılığı — resmi isimden farklıysa döner, yoksa null.
     * <x-program-name> bunu küçük punto yardımcı satır olarak gösterir.
     */
    public function getLocalizedNameAttribute(): ?string
    {
        $val = $this->attributes['name_' . app()->getLocale()] ?? null;
        return ($val && $val !== $this->name) ? $val : null;
    }
    /** Anlamlı sayılmak için ad/derece/kalıp temizliğinden sonra gereken en az FARKLI kelime sayısı. */
    public const MIN_MEANINGFUL_WORDS = 6;

    /** Programa özgü bilgi taşımayan derece/kalıp kelimeleri (TR/EN/DE), küçük harf. */
    private const BOILERPLATE_WORDS = [
        'master', 'masters', 'bachelor', 'bachelors', 'msc', 'mba', 'meng', 'mres', 'llm', 'bsc', 'beng', 'phd',
        'doctoral', 'doctorate', 'science', 'sciences', 'arts', 'and', 'the', 'for', 'und', 'der', 'die', 'das', 'für',
        'programme', 'program', 'programm', 'programs', 'studiengang', 'studiengänge', 'degree', 'course', 'courses',
        'studies', 'study', 'studium', 'yüksek', 'lisans', 'programı', 'bir', 'full', 'time', 'part', 'tam', 'zamanlı',
    ];

    /**
     * Metinde programa özgü kaç farklı anlamlı kelime var? HTML, entity, noktalama, sayılar, programın kendi adları
     * (de/en/tr + derece tanımı), ≤2 harfli kelimeler ve derece/kalıp kelimeleri çıkarılır. "Physics (MSc)" → 0.
     */
    public function meaningfulWordCount(?string $text): int
    {
        if ($text === null || trim($text) === '') {
            return 0;
        }
        $t = mb_strtolower(html_entity_decode(strip_tags($text), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        foreach (['name_de', 'name_en', 'name_tr', 'degree_specification'] as $c) {
            $n = mb_strtolower(trim((string) ($this->attributes[$c] ?? '')));
            if (mb_strlen($n) > 2) {
                $t = preg_replace('/(?<![\p{L}\p{N}])'.preg_quote($n, '/').'(?![\p{L}\p{N}])/u', ' ', $t) ?? $t;
            }
        }
        preg_match_all('/[^\W\d_]+/u', $t, $m);
        $words = array_filter($m[0], fn ($w) => mb_strlen($w) > 2 && ! in_array($w, self::BOILERPLATE_WORDS, true));

        return count(array_unique($words));
    }

    public function isMeaningfulText(?string $text): bool
    {
        return $this->meaningfulWordCount($text) >= self::MIN_MEANINGFUL_WORDS;
    }

    public function hasMeaningfulDescription(): bool
    {
        return $this->isMeaningfulText($this->attributes['description_tr'] ?? null)
            || $this->isMeaningfulText($this->attributes['description_en'] ?? null);
    }

    public function hasMeaningfulRequirements(): bool
    {
        foreach (['qualification_requirements_tr', 'qualification_requirements_en', 'language_requirements_tr', 'language_requirements_en'] as $c) {
            if ($this->isMeaningfulText($this->attributes[$c] ?? null)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Sayfası ARAMA MOTORUNA sunulacak kadar programa özgü içerik taşıyor mu?
     *
     * İnce = anlamlı açıklama YOK ve anlamlı başvuru/dil şartı metni YOK. Yalnız ad + derece (Hochschulkompass),
     * açıklaması program adının tekrarı olan kayıtlar (DAAD "Physics (MSc)") ve yalnız süre/ücret/tarih taşıyan
     * kayıtlar incedir. İnce sayfa sitede görünür (200), noindex,follow alır ve sitemap'e girmez; veri dolunca
     * kendiliğinden indekslenir. Kaynak URL'si eksikliği tek başına ince yapmaz.
     */
    public function isThin(): bool
    {
        return ! $this->hasMeaningfulDescription() && ! $this->hasMeaningfulRequirements();
    }

    /**
     * Sorgu tarafı ÖN filtre: isThin() olmayan her kaydı kapsayan üst küme (açıklama ya da şart metni dolu).
     * Kesin karar PHP'de isThin() ile verilir (sitemap bunu satır satır uygular).
     */
    public function scopeIndexable($q)
    {
        return $q->where(function ($w) {
            foreach (['description_tr', 'description_en', 'qualification_requirements_tr', 'qualification_requirements_en',
                'language_requirements_tr', 'language_requirements_en'] as $c) {
                $w->orWhere(fn ($x) => $x->whereNotNull($c)->where($c, '!=', ''));
            }
        });
    }

    /**
     * Sayfa dilinde gösterilecek açıklama: ['text' => …, 'fallback' => bool]. Kendi dilinde anlamlı açıklama varsa o;
     * yoksa TR/DE sayfada İngilizce kaynak metin (fallback, etiketli); EN sayfada TR'ye ASLA düşülmez.
     * description_de sütunu yok → DE sayfada İngilizce metin her zaman fallback olarak etiketlenir.
     */
    public function displayDescription(?string $locale = null): ?array
    {
        $locale ??= app()->getLocale();
        $own = in_array($locale, ['tr', 'en'], true) ? ($this->attributes["description_{$locale}"] ?? null) : null;
        if ($this->isMeaningfulText($own)) {
            return ['text' => $own, 'fallback' => false];
        }
        $en = $this->attributes['description_en'] ?? null;
        if ($locale !== 'en' && $this->isMeaningfulText($en)) {
            return ['text' => $en, 'fallback' => true];
        }

        return null;
    }

    /**
     * Şart metni (qualification|language|required_documents) sayfa dilinde: ['text' => …, 'fallback' => bool].
     * TR: _tr, yoksa _en (fallback). EN: yalnız _en. DE: _en (fallback, etiketli) — TR metne ASLA düşülmez.
     */
    public function displayRequirement(string $kind, ?string $locale = null): ?array
    {
        $locale ??= app()->getLocale();
        $tr = trim((string) ($this->attributes["{$kind}_tr"] ?? ''));
        $en = trim((string) ($this->attributes["{$kind}_en"] ?? ''));
        if ($locale === 'tr' && $tr !== '') {
            return ['text' => $tr, 'fallback' => false];
        }
        if ($en !== '') {
            return ['text' => $en, 'fallback' => $locale !== 'en'];
        }

        return null;
    }

    /** Başvuru tarihi durumu: 'current' (bugün ve sonrası), 'past' (geçmiş, "son bilinen"), null (tarih yok). */
    public static function deadlineState($date): ?string
    {
        if (! $date) {
            return null;
        }

        return \Illuminate\Support\Carbon::parse($date)->startOfDay()->lt(now()->startOfDay()) ? 'past' : 'current';
    }
}
