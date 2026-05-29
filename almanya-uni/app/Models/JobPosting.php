<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class JobPosting extends Model
{
    protected $fillable = [
        'slug', 'title', 'university_id', 'city_id', 'field_of_study_id',
        'position_type', 'employment_type', 'language',
        'salary_band', 'salary_min_eur', 'salary_max_eur',
        'excerpt', 'description', 'requirements',
        'posted_at', 'deadline_at',
        'application_url', 'source_url', 'source_name',
        'is_remote', 'is_featured', 'is_active', 'view_count',
    ];

    protected $casts = [
        'posted_at'   => 'date',
        'deadline_at' => 'date',
        'is_remote'   => 'boolean',
        'is_featured' => 'boolean',
        'is_active'   => 'boolean',
    ];

    public const POSITION_TYPES = [
        'phd'        => ['icon' => '🔬', 'label_tr' => 'PhD / Doktora pozisyonu', 'label_en' => 'PhD position',   'label_de' => 'Promotionsstelle'],
        'postdoc'    => ['icon' => '🧪', 'label_tr' => 'Postdoc',                 'label_en' => 'Postdoc',         'label_de' => 'Postdoc'],
        'lecturer'   => ['icon' => '📖', 'label_tr' => 'Öğretim görevlisi',       'label_en' => 'Lecturer',        'label_de' => 'Lehrkraft'],
        'professor'  => ['icon' => '🎓', 'label_tr' => 'Profesörlük',             'label_en' => 'Professorship',   'label_de' => 'Professur'],
        'researcher' => ['icon' => '🔍', 'label_tr' => 'Araştırmacı',             'label_en' => 'Researcher',      'label_de' => 'Wissenschaftliche Stelle'],
        'admin'      => ['icon' => '🗂️', 'label_tr' => 'İdari pozisyon',          'label_en' => 'Administrative',  'label_de' => 'Verwaltung'],
        'industry'   => ['icon' => '🏢', 'label_tr' => 'Endüstri / işbirliği',    'label_en' => 'Industry',        'label_de' => 'Industrie'],
    ];

    public function university(): BelongsTo
    {
        return $this->belongsTo(University::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function field(): BelongsTo
    {
        return $this->belongsTo(FieldOfStudy::class, 'field_of_study_id');
    }

    public function scopeActive(Builder $q): Builder
    {
        return $q->where('is_active', true)
            ->where(function ($qq) {
                $qq->whereNull('deadline_at')->orWhere('deadline_at', '>=', now()->toDateString());
            });
    }

    public function scopeExpiringSoon(Builder $q, int $days = 14): Builder
    {
        return $q->whereNotNull('deadline_at')
            ->whereBetween('deadline_at', [now()->toDateString(), now()->addDays($days)->toDateString()]);
    }

    public function getPositionLabelAttribute(): string
    {
        $locale = app()->getLocale();
        $key = self::POSITION_TYPES[$this->position_type] ?? null;
        if (! $key) return ucfirst($this->position_type);
        return $key['label_' . $locale] ?? $key['label_en'];
    }

    public function getPositionIconAttribute(): string
    {
        return self::POSITION_TYPES[$this->position_type]['icon'] ?? '💼';
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->deadline_at && $this->deadline_at->isPast();
    }

    public function getDaysUntilDeadlineAttribute(): ?int
    {
        if (! $this->deadline_at) return null;
        return (int) Carbon::now()->startOfDay()->diffInDays($this->deadline_at->startOfDay(), false);
    }

    public function getSalaryDisplayAttribute(): ?string
    {
        if ($this->salary_band) return $this->salary_band;
        if ($this->salary_min_eur && $this->salary_max_eur) {
            return '€' . number_format($this->salary_min_eur) . '–' . number_format($this->salary_max_eur) . '/yr';
        }
        if ($this->salary_min_eur) return 'From €' . number_format($this->salary_min_eur);
        return null;
    }

    protected static function booted(): void
    {
        static::creating(function (JobPosting $j) {
            if (empty($j->slug)) {
                $base = Str::slug($j->title);
                $slug = $base;
                $n = 1;
                while (static::where('slug', $slug)->exists()) {
                    $slug = $base . '-' . (++$n);
                }
                $j->slug = $slug;
            }
            if (empty($j->posted_at)) {
                $j->posted_at = now()->toDateString();
            }
        });
    }
}
