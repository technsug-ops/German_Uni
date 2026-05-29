<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Posts.content_html still leaks literal `&quot;` in body text (e.g. quoted
 * phrases like "Yetersiz finans"). Earlier migrations skipped content_html
 * because naively decoding the whole HTML would break entities used inside
 * attributes (href, title, etc.).
 *
 * This migration walks each row's HTML with DOMDocument and decodes only
 * text nodes — attribute values stay untouched. Idempotent.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('posts') || ! Schema::hasColumn('posts', 'content_html')) return;

        $rows = DB::table('posts')
            ->whereNotNull('content_html')
            ->where(function ($q) {
                $q->where('content_html', 'like', '%&quot;%')
                  ->orWhere('content_html', 'like', '%&#039;%')
                  ->orWhere('content_html', 'like', '%&apos;%')
                  ->orWhere('content_html', 'like', '%&#x27;%');
            })
            ->get(['id', 'content_html']);

        foreach ($rows as $row) {
            $clean = $this->decodeTextNodes((string) $row->content_html);
            if ($clean !== null && $clean !== $row->content_html) {
                DB::table('posts')->where('id', $row->id)->update(['content_html' => $clean]);
            }
        }
    }

    private function decodeTextNodes(string $html): ?string
    {
        if ($html === '') return $html;

        $dom = new \DOMDocument('1.0', 'UTF-8');
        libxml_use_internal_errors(true);

        // Wrap so DOMDocument doesn't inject <html><body>. Force UTF-8.
        $wrapped = '<?xml encoding="UTF-8"?><div id="__wrap__">' . $html . '</div>';
        $ok = $dom->loadHTML($wrapped, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        if (! $ok) return null;

        // DOMDocument decodes entities on load. Re-saving via saveHTML re-encodes
        // only what is structurally required (e.g. `&` in text → `&amp;`, `"` in
        // attributes → `&quot;`). Text-node `"` and `'` are left as raw chars.
        $wrap = $dom->getElementById('__wrap__');
        if (! $wrap) return null;

        $out = '';
        foreach ($wrap->childNodes as $child) {
            $out .= $dom->saveHTML($child);
        }
        return $out;
    }

    public function down(): void {}
};
