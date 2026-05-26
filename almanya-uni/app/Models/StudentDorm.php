<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentDorm extends Model
{
    use Concerns\LocalizableContent;

    protected $fillable = [
        'city_id', 'city_name', 'organization',
        'website_url', 'application_url', 'waitlist_avg',
        'rent_min_eur', 'rent_max_eur', 'amenities',
        'notes_tr', 'notes_en', 'notes_de',
        'sort_order', 'is_active',
    ];

    protected $casts = [
        'amenities' => 'array',
        'is_active' => 'boolean',
    ];

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }
}
