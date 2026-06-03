<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Lead — herkese açık dil kursu / tercüme detay sayfasındaki "İlgileniyorum" formu
 * ve tık takibinden gelen kayıtlar. İleride affiliate partnerlere aktarılır.
 */
class Lead extends Model
{
    protected $fillable = [
        'source_type', 'source_id', 'source_name',
        'name', 'email', 'phone', 'message',
        'locale', 'status', 'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public const SOURCES = [
        'language_course'    => 'Dil Kursu',
        'translation_office' => 'Tercüme Bürosu',
    ];

    public const STATUSES = [
        'new'       => 'Yeni',
        'contacted' => 'İletişime geçildi',
        'converted' => 'Dönüştü',
        'archived'  => 'Arşiv',
    ];
}
