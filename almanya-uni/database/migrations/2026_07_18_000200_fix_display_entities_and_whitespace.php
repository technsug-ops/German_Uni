<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Duz-metin olarak gosterilen alanlardaki (title/excerpt/meta_title/meta_description)
 * cift-encode HTML entity'lerini (&quot; -> ", &amp; -> & vb.) ve kacan literal
 * escape dizilerini (\n \r \t) temizler.
 *
 * Neden: Blade {{ }} bu alanlari zaten escape ediyor; alanda '&quot;' saklaninca
 * '&amp;quot;' cikip ekranda '&quot;' gorunuyordu (ekran goruntusu). content_html'e
 * DOKUNULMAZ (o gercek HTML).
 *
 * Idempotent + ortamdan bagimsiz: temiz degerlerde no-op, bozuklarda duzeltir.
 */
return new class extends Migration
{
    public function up(): void
    {
        $fields = ['title', 'excerpt', 'meta_title', 'meta_description'];
        $changed = 0;

        DB::table('posts')->select(array_merge(['id'], $fields))->orderBy('id')
            ->chunk(200, function ($rows) use ($fields, &$changed) {
                foreach ($rows as $row) {
                    $update = [];
                    foreach ($fields as $f) {
                        $v = $row->$f;
                        if ($v === null || $v === '') { continue; }
                        $new = $this->clean($v);
                        if ($new !== $v) { $update[$f] = $new; }
                    }
                    if ($update) {
                        DB::table('posts')->where('id', $row->id)->update($update);
                        $changed++;
                    }
                }
            });

        echo "  duzeltilen kayit: {$changed}\n";
    }

    private function clean(string $v): string
    {
        // Entity decode (tek/cift-encode icin stabil olana kadar, max 3 gecis)
        $new = $v;
        for ($i = 0; $i < 3; $i++) {
            $d = html_entity_decode($new, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            if ($d === $new) { break; }
            $new = $d;
        }
        // Kacan literal escape dizileri -> bosluk
        $new = str_replace(["\\r\\n", "\\n", "\\r", "\\t"], ' ', $new);
        // Bosluk topla + trim
        $new = preg_replace('/[ ]{2,}/u', ' ', $new);
        return trim($new);
    }

    public function down(): void
    {
        // Geri alinamaz.
    }
};
