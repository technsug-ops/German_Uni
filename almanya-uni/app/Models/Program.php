<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Program extends Model
{
    use HasFactory;
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
        'last_synced_at',
        'is_active',
        'image_url',
        'language_level_de',
        'language_level_en',
        'is_online',
        'financial_support',
        'support_info',
        'start_semester',
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

    public function scopeActive($q)
    {
        return $q->where('is_active', true);
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
}
