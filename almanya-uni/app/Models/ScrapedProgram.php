<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScrapedProgram extends Model
{
    protected $fillable = [
        'scrape_source_id',
        'university_id',
        'program_id',
        'external_key',
        'source_url',
        'name_de',
        'name_en',
        'degree',
        'language',
        'duration_semesters',
        'admission_mode',
        'study_form',
        'deadline_raw',
        'tuition_raw',
        'semester_fee_raw',
        'ects_credits',
        'nc_value',
        'tuition_fee_eur',
        'description_de',
        'raw',
        'content_hash',
        'review_status',
        'reviewed_by',
        'reviewed_at',
        'review_notes',
        'first_seen_at',
        'last_seen_at',
    ];

    protected $casts = [
        'raw' => 'array',
        'first_seen_at' => 'datetime',
        'last_seen_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'nc_value' => 'decimal:2',
    ];

    public function source(): BelongsTo
    {
        return $this->belongsTo(ScrapeSource::class, 'scrape_source_id');
    }

    public function university(): BelongsTo
    {
        return $this->belongsTo(University::class);
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
