<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PremiumInterest extends Model
{
    protected $fillable = [
        'email', 'name', 'tier_interest', 'source_page', 'locale', 'country', 'note',
        'contacted', 'contacted_at',
        'wants_beta', 'beta_invited_at', 'confirmation_sent_at',
    ];

    protected $casts = [
        'contacted'             => 'boolean',
        'wants_beta'            => 'boolean',
        'contacted_at'          => 'datetime',
        'beta_invited_at'       => 'datetime',
        'confirmation_sent_at'  => 'datetime',
    ];

    public function scopeBetaCandidates($q)
    {
        return $q->where('wants_beta', true)->whereNull('beta_invited_at');
    }
}
