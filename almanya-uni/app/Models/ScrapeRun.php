<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScrapeRun extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'scrape_source_id',
        'started_at',
        'finished_at',
        'duration_ms',
        'status',
        'http_requests',
        'items_found',
        'items_new',
        'items_updated',
        'error',
        'meta',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'meta' => 'array',
    ];

    public function source(): BelongsTo
    {
        return $this->belongsTo(ScrapeSource::class, 'scrape_source_id');
    }
}
