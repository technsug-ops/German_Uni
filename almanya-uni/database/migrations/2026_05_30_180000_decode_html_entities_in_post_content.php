<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Posts have HTML-encoded entities (&quot; &amp; &#039; etc.) in their
 * content/meta fields, not just excerpt. Blade `{{ }}` and content
 * renderers escape them again on display, leaking literal `&quot;` to
 * users. Decode in-place — idempotent (only rows with `&` are touched
 * and decoded once via html_entity_decode).
 *
 * content_html is left alone — by design it's already HTML and any
 * `&quot;` inside there is correct encoding inside an attribute.
 * Only Markdown / plain-text columns are decoded.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('posts')) return;

        $textCols = ['title', 'content_md', 'meta_title', 'meta_description', 'featured_image_caption'];

        foreach ($textCols as $col) {
            if (! Schema::hasColumn('posts', $col)) continue;

            $rows = DB::table('posts')
                ->where(function ($q) use ($col) {
                    $q->where($col, 'like', '%&quot;%')
                      ->orWhere($col, 'like', '%&#039;%')
                      ->orWhere($col, 'like', '%&apos;%')
                      ->orWhere($col, 'like', '%&#x27;%')
                      ->orWhere($col, 'like', '%&lt;%')
                      ->orWhere($col, 'like', '%&gt;%');
                })
                ->get(['id', $col]);

            foreach ($rows as $row) {
                $clean = html_entity_decode((string) $row->{$col}, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                if ($clean !== $row->{$col}) {
                    DB::table('posts')->where('id', $row->id)->update([$col => $clean]);
                }
            }
        }
    }

    public function down(): void {}
};
