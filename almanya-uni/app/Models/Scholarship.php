<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Laravel\Scout\Searchable;

class Scholarship extends Model
{
    use Searchable;

    protected $fillable = [
        'sap_objid',
        'daad_id',
        'sap_progid',
        'sap_target_system',
        'name_de',
        'name_en',
        'langname_de',
        'langname_en',
        'programmname_de',
        'programmname_en',
        'programmtyp_id',
        'slug',
        'introduction_json',
        'q_de_json',
        'q_en_json',
        'is_daad',
        'is_move',
        'sorting',
        'last_seen_at',
        'removed_at',
        'detail_url',
    ];

    protected $casts = [
        'introduction_json' => 'array',
        'q_de_json'         => 'array',
        'q_en_json'         => 'array',
        'is_daad'           => 'boolean',
        'is_move'           => 'boolean',
        'last_seen_at'      => 'datetime',
        'removed_at'        => 'datetime',
    ];

    public function origins(): BelongsToMany
    {
        return $this->belongsToMany(ScholarshipOrigin::class, 'scholarship_origin', 'scholarship_id', 'origin_id');
    }

    public function statuses(): BelongsToMany
    {
        return $this->belongsToMany(ScholarshipStatus::class, 'scholarship_status', 'scholarship_id', 'status_id');
    }

    public function subjects(): BelongsToMany
    {
        // 5th arg = parentKey (Scholarship.id), 6th arg = relatedKey (ScholarshipSubject.code)
        return $this->belongsToMany(ScholarshipSubject::class, 'scholarship_subject', 'scholarship_id', 'subject_code', 'id', 'code');
    }

    public function intentions(): BelongsToMany
    {
        return $this->belongsToMany(ScholarshipIntention::class, 'scholarship_intention', 'scholarship_id', 'intention_id');
    }

    public function deadline(): HasOne
    {
        // DAAD deadline file uses scholarship's sap_progid (NOT sap_objid) as its key,
        // even though the column is named sap_objid in their JSON.
        return $this->hasOne(ScholarshipDeadline::class, 'sap_objid', 'sap_progid');
    }

    public function scopeActive(Builder $q): Builder
    {
        return $q->whereNull('removed_at');
    }

    /**
     * `introduction` / `qDe` / `qEn` can be a plain string OR a {de:..., en:...} dict.
     * Return the text in the requested language, falling back to en → de → first value.
     */
    public function textFor(string $field, string $lang = 'en'): ?string
    {
        $v = $this->{$field};
        if (is_string($v)) return $v;
        if (is_array($v)) {
            return $v[$lang] ?? $v['en'] ?? $v['de'] ?? (reset($v) ?: null);
        }
        return null;
    }

    public function introductionText(string $lang = 'en'): ?string
    {
        return $this->textFor('introduction_json', $lang);
    }

    public function qText(string $lang = 'en'): ?string
    {
        $field = $lang === 'de' ? 'q_de_json' : 'q_en_json';
        return $this->textFor($field, $lang);
    }

    public function displayName(string $lang = 'en'): string
    {
        return $this->textFor('name_' . $lang, $lang)
            ?? $this->name_en
            ?? $this->name_de
            ?? ('DAAD #' . $this->sap_objid);
    }

    public function searchableAs(): string
    {
        return 'scholarships';
    }

    public function toSearchableArray(): array
    {
        $this->loadMissing(['origins:id', 'statuses:id', 'subjects:code', 'intentions:id']);

        return [
            'id'             => $this->id,
            'sap_objid'      => (int) $this->sap_objid,
            'name_en'        => $this->name_en,
            'name_de'        => $this->name_de,
            'programmname_en'=> $this->programmname_en,
            'programmname_de'=> $this->programmname_de,
            'introduction'   => $this->introductionText('en'),
            'slug'           => $this->slug,
            'origin_ids'     => $this->origins->pluck('id')->map(fn ($i) => (int) $i)->all(),
            'status_ids'     => $this->statuses->pluck('id')->map(fn ($i) => (int) $i)->all(),
            'subject_codes'  => $this->subjects->pluck('code')->all(),
            'intention_ids'  => $this->intentions->pluck('id')->map(fn ($i) => (int) $i)->all(),
            'is_daad'        => (bool) $this->is_daad,
        ];
    }

    public function shouldBeSearchable(): bool
    {
        return $this->removed_at === null;
    }
}
