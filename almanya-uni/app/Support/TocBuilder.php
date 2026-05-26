<?php

namespace App\Support;

use Illuminate\Support\Str;

/**
 * Parses post content HTML, injects ids on <h2> tags, and returns a TOC array.
 * Server-side rendered TOC = crawler-visible (Google AIO + Bing Copilot read it
 * without executing JS) while still being scrollspy-friendly for the JS sidebar.
 */
class TocBuilder
{
    /**
     * @return array{html: string, toc: array<int,array{id:string,text:string,level:int}>}
     */
    public static function process(?string $html): array
    {
        if (! $html) return ['html' => '', 'toc' => []];

        $toc = [];
        $usedIds = [];
        $counter = 0;

        // Match <h2> and <h3> with optional attributes
        $html = preg_replace_callback(
            '/<h([23])([^>]*)>(.*?)<\/h\1>/is',
            function ($m) use (&$toc, &$usedIds, &$counter) {
                $level = (int) $m[1];
                $attrs = $m[2];
                $inner = $m[3];

                // Extract existing id="..." if present, else build slug
                $id = null;
                if (preg_match('/\bid\s*=\s*"([^"]+)"/i', $attrs, $idMatch)) {
                    $id = $idMatch[1];
                } else {
                    $textPlain = trim(strip_tags($inner));
                    $slug = Str::slug($textPlain, '-', null);
                    if ($slug === '' || mb_strlen($slug) > 80) {
                        $slug = 'toc-h' . (++$counter);
                    }
                    $base = $slug;
                    $i = 2;
                    while (isset($usedIds[$slug])) {
                        $slug = $base . '-' . $i++;
                    }
                    $id = $slug;
                    $attrs = ' id="' . $id . '"' . $attrs;
                }
                $usedIds[$id] = true;

                $toc[] = [
                    'id' => $id,
                    'text' => trim(strip_tags($inner)),
                    'level' => $level,
                ];

                return "<h{$level}{$attrs}>{$inner}</h{$level}>";
            },
            $html
        );

        // Only h2 entries for sidebar list (h3 stays for in-content anchoring)
        $toc = array_values(array_filter($toc, fn ($t) => $t['level'] === 2));

        return ['html' => $html, 'toc' => $toc];
    }
}
