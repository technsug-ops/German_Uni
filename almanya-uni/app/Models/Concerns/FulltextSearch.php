<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

/**
 * MySQL FULLTEXT arama (boolean mode, prefix). LIKE %...%'e göre hız + alaka.
 * Kısa sorgu (<3 char, fulltext min token) → LIKE fallback.
 *
 * Kullanım:
 *   Model::searchFulltext($q, ['name_de','name_en'])        // filtre
 *        ->orderByRelevance($q, ['name_de','name_en'])      // en alakalı üstte
 *
 * Not: $columns, ilgili tabloda TEK bir FULLTEXT index ile EŞLEŞMELİ
 * (bkz. add_fulltext_search_indexes migration). orderByRelevance ile
 * searchFulltext AYNI $columns'ı almalı.
 */
trait FulltextSearch
{
    public function scopeSearchFulltext(Builder $query, string $term, array $columns): Builder
    {
        $term = trim($term);
        if ($term === '') {
            return $query;
        }

        $boolean = self::fulltextBooleanQuery($term);

        if ($boolean === null) {
            // <3 char ya da tümü temizlendi → LIKE fallback
            $like = '%' . str_replace(['%', '_'], ['\%', '\_'], $term) . '%';
            return $query->where(function ($w) use ($columns, $like) {
                foreach ($columns as $c) {
                    $w->orWhere($c, 'like', $like);
                }
            });
        }

        return $query->whereFullText($columns, $boolean, ['mode' => 'boolean']);
    }

    /**
     * MATCH...AGAINST alaka skoruna göre sırala (en alakalı üstte). Aynı
     * boolean sorguyu kullanır. Kısa sorgu/fallback durumunda no-op (ikincil
     * sıralama devreye girer).
     */
    public function scopeOrderByRelevance(Builder $query, string $term, array $columns): Builder
    {
        $boolean = self::fulltextBooleanQuery(trim($term));
        if ($boolean === null) {
            return $query; // fulltext yok → dokunma
        }

        $cols = implode(',', array_map(fn ($c) => '`' . str_replace('`', '', $c) . '`', $columns));

        return $query->orderByRaw("MATCH({$cols}) AGAINST(? IN BOOLEAN MODE) DESC", [$boolean]);
    }

    /** "+kelime*" boolean sorgusu; <3 char ya da hepsi geçersizse null. */
    protected static function fulltextBooleanQuery(string $term): ?string
    {
        if (mb_strlen($term) < 3) {
            return null;
        }

        $words = preg_split('/\s+/', $term) ?: [];
        $boolean = collect($words)
            ->map(fn ($w) => preg_replace('/[+\-*"()~<>@]/u', '', $w))
            ->filter(fn ($w) => mb_strlen($w) >= 2)
            ->map(fn ($w) => '+' . $w . '*')
            ->implode(' ');

        return $boolean === '' ? null : $boolean;
    }
}
