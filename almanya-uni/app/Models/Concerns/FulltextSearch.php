<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

/**
 * MySQL FULLTEXT arama (boolean mode, prefix). LIKE %...%'e göre hız + alaka.
 * Kısa sorgu (<3 char, fulltext min token) → LIKE fallback.
 *
 * Kullanım: Model::searchFulltext($q, ['name_de','name_en'])
 * Not: $columns, ilgili tabloda TEK bir FULLTEXT index ile EŞLEŞMELİ
 * (bkz. add_fulltext_search_indexes migration).
 */
trait FulltextSearch
{
    public function scopeSearchFulltext(Builder $query, string $term, array $columns): Builder
    {
        $term = trim($term);
        if ($term === '') {
            return $query;
        }

        // Kısa sorgu: fulltext min token (varsayılan 3) altı → LIKE fallback
        if (mb_strlen($term) < 3) {
            $like = '%' . str_replace(['%', '_'], ['\%', '\_'], $term) . '%';
            return $query->where(function ($w) use ($columns, $like) {
                foreach ($columns as $c) {
                    $w->orWhere($c, 'like', $like);
                }
            });
        }

        // Boolean mode: her kelime "+kelime*" (zorunlu + prefix). Özel
        // operatörleri temizle ki kullanıcı girdisi sorguyu bozmasın.
        $words = preg_split('/\s+/', $term) ?: [];
        $boolean = collect($words)
            ->map(fn ($w) => preg_replace('/[+\-*"()~<>@]/u', '', $w))
            ->filter(fn ($w) => mb_strlen($w) >= 2)
            ->map(fn ($w) => '+' . $w . '*')
            ->implode(' ');

        if ($boolean === '') {
            // Tümü temizlendi (sadece operatör/kısa) → LIKE fallback
            $like = '%' . str_replace(['%', '_'], ['\%', '\_'], $term) . '%';
            return $query->where(function ($w) use ($columns, $like) {
                foreach ($columns as $c) {
                    $w->orWhere($c, 'like', $like);
                }
            });
        }

        return $query->whereFullText($columns, $boolean, ['mode' => 'boolean']);
    }
}
