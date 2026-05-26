<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PremiumInterest extends Model
{
    protected $fillable = [
        'email', 'name', 'tier_interest', 'source_page', 'locale', 'country', 'note', 'contacted', 'contacted_at',
    ];

    protected $casts = [
        'contacted'    => 'boolean',
        'contacted_at' => 'datetime',
    ];
}
