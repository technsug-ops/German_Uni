<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Otomatik haber çekimi (news:fetch) için RSS/Atom kaynak. Admin "Haber Kaynakları"
 * panelinden yönetilir. keywords boşsa filtre uygulanmaz (kaynaktaki her haber gelir).
 */
class NewsSource extends Model
{
    protected $fillable = [
        'name', 'url', 'default_category', 'keywords',
        'max_per_source', 'enabled', 'sort_order',
        'last_fetched_at', 'last_result',
    ];

    protected $casts = [
        'keywords'        => 'array',
        'enabled'         => 'boolean',
        'max_per_source'  => 'integer',
        'sort_order'      => 'integer',
        'last_fetched_at' => 'datetime',
    ];
}
