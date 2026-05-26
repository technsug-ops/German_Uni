<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ScrapeSource extends Model
{
    protected $fillable = [
        'university_id',
        'name',
        'list_url',
        'adapter',
        'config',
        'throttle_ms',
        'respect_robots',
        'is_enabled',
        'etag',
        'last_modified_header',
        'last_run_at',
        'last_found_count',
        'last_status',
        'last_error',
    ];

    protected $casts = [
        'config' => 'array',
        'respect_robots' => 'boolean',
        'is_enabled' => 'boolean',
        'last_run_at' => 'datetime',
    ];

    public function university(): BelongsTo
    {
        return $this->belongsTo(University::class);
    }

    public function runs(): HasMany
    {
        return $this->hasMany(ScrapeRun::class);
    }

    public function scrapedPrograms(): HasMany
    {
        return $this->hasMany(ScrapedProgram::class);
    }
}
