<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Bir hukuki sayfanın TEK bir dildeki içeriği.
 *
 * (legal_page_id, locale) benzersizdir — bkz.
 * 2026_09_24_000200_create_legal_page_translations_table.
 */
class LegalPageTranslation extends Model
{
    protected $fillable = [
        'legal_page_id',
        'locale',
        'title',
        'description',
        'body',
    ];

    public function legalPage(): BelongsTo
    {
        return $this->belongsTo(LegalPage::class);
    }
}
