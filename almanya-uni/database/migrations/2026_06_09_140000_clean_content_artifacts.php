<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * İçerik üretim artıklarını temizler (geri bildirim):
 *  1) "Fazit + CTA" başlık artığı (CTA placeholder doldurulmadan kalmış) → "Fazit".
 *  2) DE/EN postlarda <code> / `...` içine gömülü TÜRKÇE SEO keyword span'leri
 *     (örn "pazar açık market almanya") → kaldır; ardından Almanca parantez varsa onu bırak.
 *
 * GÜVENLİ: sadece Türkçe-sinyali (ş/ğ/ı/İ/ç VEYA pazar|almanya|alışveriş|açık|vize|yurt|
 * sınav|başvuru) içeren code span'leri hedeflenir → legit code (git push, npm run build…)
 * KORUNUR. DB::table kullanılır (Eloquent save() YOK → Scout tetiklenmez).
 */
return new class extends Migration
{
    public function up(): void
    {
        $tbl = 'posts';
        $sig = '(?:[ışğİĞŞçÇ]|\b(?:pazar|almanya|alışveriş|açık|vize|yurt|sınav|başvuru)\b)';

        // 1) "Fazit + CTA" → "Fazit" (tüm diller)
        $rows = DB::table($tbl)
            ->where('content_md', 'like', '%+ CTA%')
            ->orWhere('content_html', 'like', '%+ CTA%')
            ->get(['id', 'content_md', 'content_html']);
        foreach ($rows as $r) {
            DB::table($tbl)->where('id', $r->id)->update([
                'content_md'   => str_replace([' + CTA', '+ CTA'], '', (string) $r->content_md),
                'content_html' => str_replace([' + CTA', '+ CTA'], '', (string) $r->content_html),
                'updated_at'   => now(),
            ]);
        }

        // 2) DE/EN postlarda Türkçe keyword code span'leri
        $rows = DB::table($tbl)
            ->whereIn('locale', ['de', 'en'])
            ->where(function ($q) {
                $q->where('content_html', 'like', '%<code>%')
                  ->orWhere('content_md', 'like', '%`%');
            })
            ->get(['id', 'content_md', 'content_html']);

        foreach ($rows as $r) {
            $html = (string) $r->content_html;
            $md   = (string) $r->content_md;

            // <code>TR</code> (Almanca) → Almanca ; sonra kalan <code>TR</code> → kaldır
            $html = preg_replace("~<code>(?=[^<]*$sig)[^<]*</code>\s*\(([^)]+)\)~ui", '$1', $html);
            $html = preg_replace("~\s*<code>(?=[^<]*$sig)[^<]*</code>~ui", '', $html);
            // content_md hem <code> hem backtick olabilir
            $md = preg_replace("~<code>(?=[^<]*$sig)[^<]*</code>\s*\(([^)]+)\)~ui", '$1', $md);
            $md = preg_replace("~\s*<code>(?=[^<]*$sig)[^<]*</code>~ui", '', $md);
            $md = preg_replace("~`(?=[^`]*$sig)[^`]*`\s*\(([^)]+)\)~ui", '$1', $md);
            $md = preg_replace("~\s*`(?=[^`]*$sig)[^`]*`~ui", '', $md);

            // sadece fazla BOŞLUK (newline değil) topla
            $html = preg_replace('~ {2,}~u', ' ', $html);
            $md   = preg_replace('~ {2,}~u', ' ', $md);

            if ($html !== (string) $r->content_html || $md !== (string) $r->content_md) {
                DB::table($tbl)->where('id', $r->id)->update([
                    'content_html' => $html,
                    'content_md'   => $md,
                    'updated_at'   => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        // Geri alınamaz (içerik temizliği).
    }
};
