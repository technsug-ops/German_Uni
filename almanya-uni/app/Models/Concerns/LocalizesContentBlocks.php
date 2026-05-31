<?php

namespace App\Models\Concerns;

/**
 * Locale-aware enrichment blocks for entities with a TR `content_blocks` JSON
 * column + translated content_blocks_en / content_blocks_de.
 *
 * TR → source content_blocks; EN/DE → content_blocks_{locale} (null until
 * translated, so blades hide them instead of leaking Turkish). Enrichment-B,
 * see doc/MULTILANG-PLAN.
 *
 * The model must cast content_blocks(/_en/_de) to array.
 */
trait LocalizesContentBlocks
{
    public function localizedBlocks(?string $locale = null): ?array
    {
        $locale ??= app()->getLocale();
        if ($locale === 'tr') {
            return $this->content_blocks;
        }
        return $this->{'content_blocks_' . $locale} ?: null;
    }
}
