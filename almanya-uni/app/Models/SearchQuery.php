<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SearchQuery extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'query', 'query_raw', 'results_count', 'breakdown', 'session_id', 'took_ms', 'created_at',
    ];

    protected $casts = [
        'breakdown'  => 'array',
        'created_at' => 'datetime',
    ];
}
