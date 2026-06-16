<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Advisor extends Model
{
    protected $fillable = [
        'name', 'slug', 'role_title', 'affiliation', 'photo_url',
        'bio', 'linkedin_url', 'profile_url', 'sort_order', 'is_active',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'sort_order' => 'integer',
    ];

    protected static function booted(): void
    {
        static::saving(function (Advisor $a) {
            if (empty($a->slug) && $a->name) {
                $a->slug = Str::slug($a->name);
            }
        });
    }

    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }
}
