<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HousingTemplate extends Model
{
    use Concerns\LocalizableContent;

    protected $fillable = [
        'slug', 'category',
        'title_tr', 'title_en', 'title_de',
        'description_tr', 'description_en', 'description_de',
        'subject_de', 'body_de', 'body_tr_explanation',
        'placeholders', 'sort_order', 'is_active',
    ];

    protected $casts = [
        'placeholders' => 'array',
        'is_active'    => 'boolean',
    ];
}
