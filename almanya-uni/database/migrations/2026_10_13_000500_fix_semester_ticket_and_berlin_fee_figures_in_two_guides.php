<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * İki rehberde hedefli ücret düzeltmesi (TR/EN/DE, mevcut kayıtlar; yeni blok üretilmez):
 *
 * 1) Deutschlandticket rehberi: Deutschlandsemesterticket "~34,80 €/ay (208,80 €/dönem)" 2025 fiyatının (58 €) %60'ıydı.
 *    Deutschlandticket 1 Ocak 2026'dan itibaren 63 €; öğrenci bileti %60 = 37,80 €/ay = 226,80 €/dönem.
 *    Kaynak: Verkehrsministerkonferenz kararı (Eylül 2025, 2026 fiyatı 63 €); Deutschlandsemesterticket %60 kuralı;
 *    TU/FU Berlin ve Universität Hamburg 2026/27 kış dönemi ücret tabloları (226,80 €), Ekim 2026'da kontrol edildi.
 *    Yalnız üç yer: özet paragrafı, fiyat listesi satırı, meta açıklaması (+ excerpt aynı metni taşıyorsa).
 *
 * 2) "Almanya'da üniversite ücretsiz mi?" rehberi (TR/DE; EN kardeşi kısa özet, tablo yok → dokunulmaz):
 *    - Semesterbeitrag tablosu: başlık "(2026)" → doğrulanan satırlar 2026/27 kış dönemi, ~ işaretli satırlar önceki
 *      dönemin yaklaşık değerleri olarak işaretlenir (geçmiş bilgi korunur, "güncel ortalama" gibi sunulmaz).
 *    - TU Berlin 379,06 € (226,80 € bilet dahil), Universität Hamburg 402,00 €, RWTH Aachen 354,80 € + öğrenci temsil
 *      payı, LMU München 97 € + ayrı 43 €/ay öğrenci bileti — Batch 4 kaynakları (Ekim 2026).
 *    - Senaryo A (Berlin): 2 × 379,06 € = 758,12 €; toplam ~12.758 € (yaşam 12.000 € + 758 €). Semesterticket dâhil
 *      toplama ulaşım ikinci kez eklenmez.
 * Her hedef satır tam bir kez bulunmalı; aksi halde hiçbir şey yazılmaz. Tek transaction; rerun no-op.
 */
return new class extends Migration
{
    /** slug → [eski => yeni] (tam alt dize; her biri tam bir kez) + meta/excerpt değişimi */
    private const TICKET = [
        'tr' => ['slug' => 'deutschlandticket-2026-price-coverage-and-the-student-semester-ticket', 'md' => [
            "'i (~aylık €34.80) Semesterbeitrag ile alır." => "'i (2026: aylık €37.80, standart fiyatın %60'ı) Semesterbeitrag ile alır.",
            'Standart fiyatın %60\'ı, yaklaşık **aylık €34.80** (≈ dönemlik €208.80).' => 'Standart fiyatın %60\'ı: 2026\'da **aylık €37.80** (≈ dönemlik €226.80; 2025\'te €34.80/ay idi).',
        ], 'meta' => ['(~€34.80/ay)' => '(€37.80/ay, 2026)']],
        'en' => ['slug' => 'deutschlandticket-2026-price-coverage-and-the-student-semester-ticket-en', 'md' => [
            '(~€34.80/month) via the Semesterbeitrag.' => '(€37.80/month in 2026, 60% of the standard price) via the Semesterbeitrag.',
            '60% of the standard price, about **€34.80/month** (≈ €208.80 per semester).' => '60% of the standard price: **€37.80/month** in 2026 (≈ €226.80 per semester; it was €34.80/month in 2025).',
        ], 'meta' => ['(~€34.80/month)' => '(€37.80/month in 2026)']],
        'de' => ['slug' => 'deutschlandticket-2026-price-coverage-and-the-student-semester-ticket-de', 'md' => [
            '(~34,80 €/Monat) über den Semesterbeitrag.' => '(2026: 37,80 €/Monat, 60 % des Standardpreises) über den Semesterbeitrag.',
            '60 % des Standardpreises, etwa **34,80 €/Monat** (≈ 208,80 € pro Semester).' => '60 % des Standardpreises: 2026 **37,80 €/Monat** (≈ 226,80 € pro Semester; 2025 waren es 34,80 €/Monat).',
        ], 'meta' => ['(~34,80 €/Monat)' => '(37,80 €/Monat, 2026)']],
    ];

    private const FEES = [
        'tr' => ['slug' => 'is-university-free-in-germany-2026-real-costs', 'lines' => [
            '### Eyalet ve üni bazında ortalama Semesterbeitrag (2026)' => '### Örnek Semesterbeitrag tutarları (doğrulanan satırlar 2026/27 kış dönemi; ~ işaretli satırlar önceki dönemin yaklaşık değerleri)',
            '| TU Berlin | ~330 € |' => '| TU Berlin | 379,06 € (2026/27 kış, resmî; 226,80 € Deutschlandsemesterticket dahil) |',
            '| LMU München | ~138 € |' => '| LMU München | 97 € Studierendenwerk katkısı (2026 yaz döneminden itibaren); Semesterticket yok, öğrenci Deutschlandticket\'i ayrı 43 €/ay |',
            '| Universität Hamburg | ~340 € |' => '| Universität Hamburg | 402,00 € (2026/27 kış, resmî; 226,80 € Deutschlandsemesterticket dahil) |',
            '| RWTH Aachen | ~310 € |' => '| RWTH Aachen | 354,80 € + öğrenci temsil payı (2026/27 kış; 226,80 € bilet + 128 € sosyal katkı, temsil payı henüz yayımlanmadı) |',
            '| Semesterbeitrag (2 sömestre) | 660 € |' => '| Semesterbeitrag (2 sömestre, TU Berlin 2026/27: 2 × 379,06 €; Deutschlandsemesterticket dahil) | 758,12 € |',
            '| **TOPLAM** | **~12.660 €** |' => '| **TOPLAM** | **~12.758 €** |',
        ]],
        'de' => ['slug' => 'is-university-free-in-germany-2026-real-costs-de', 'lines' => [
            '### Durchschnittlicher Semesterbeitrag nach Bundesland und Uni (2026)' => '### Beispielhafte Semesterbeiträge (geprüfte Zeilen: Wintersemester 2026/27; mit ~ markierte Zeilen: Näherungswerte aus dem Vorjahr)',
            '| TU Berlin | ~330 € |' => '| TU Berlin | 379,06 € (WS 2026/27, amtlich; inkl. 226,80 € Deutschlandsemesterticket) |',
            '| LMU München | ~138 € |' => '| LMU München | 97 € Studierendenwerksbeitrag (ab Sommersemester 2026); kein Semesterticket, ermäßigtes Deutschlandticket separat 43 €/Monat |',
            '| Universität Hamburg | ~340 € |' => '| Universität Hamburg | 402,00 € (WS 2026/27, amtlich; inkl. 226,80 € Deutschlandsemesterticket) |',
            '| RWTH Aachen | ~310 € |' => '| RWTH Aachen | 354,80 € + Studierendenschaftsanteil (WS 2026/27; 226,80 € Ticket + 128 € Sozialbeitrag, Anteil noch nicht veröffentlicht) |',
            '| Semesterbeitrag (2 Semester) | 660 € |' => '| Semesterbeitrag (2 Semester, TU Berlin WS 2026/27: 2 × 379,06 €; Deutschlandsemesterticket inklusive) | 758,12 € |',
            '| **GESAMT** | **~12.660 €** |' => '| **GESAMT** | **~12.758 €** |',
        ]],
    ];

    public function up(): void
    {
        if (! Schema::hasTable('posts')) {
            return;
        }
        $problems = [];
        $notes = [];
        $plan = [];

        foreach (self::TICKET as $loc => $spec) {
            $post = Post::where('locale', $loc)->where('slug', $spec['slug'])->first();
            if (! $post) {
                $notes[] = "ticket $loc: kayıt yok";
                continue; // yerel kopyada/CI'da bulunmayabilir; prod'da var (28 Oca 2026)
            }
            $md = (string) $post->content_md;
            $meta = (string) $post->meta_description;
            $excerpt = (string) $post->excerpt;
            $allNew = collect($spec['md'])->every(fn ($new, $old) => str_contains($md, $new) && ! str_contains($md, $old));
            if ($allNew) {
                continue; // zaten uygulanmış
            }
            foreach ($spec['md'] as $old => $new) {
                if (substr_count($md, $old) !== 1) {
                    $problems[] = "ticket $loc/{$spec['slug']} (id {$post->id}): '" . mb_substr($old, 0, 40) . "…' tam bir kez bulunamadı (" . substr_count($md, $old) . ')';
                    continue 2;
                }
            }
            $post->content_md = str_replace(array_keys($spec['md']), array_values($spec['md']), $md);
            foreach ($spec['meta'] as $old => $new) {
                if (str_contains($meta, $old)) {
                    $post->meta_description = str_replace($old, $new, $meta);
                }
                if (str_contains($excerpt, $old)) {
                    $post->excerpt = str_replace($old, $new, $excerpt);
                }
            }
            $plan[] = $post;
        }

        foreach (self::FEES as $loc => $spec) {
            $post = Post::where('locale', $loc)->where('slug', $spec['slug'])->first();
            if (! $post) {
                $notes[] = "fees $loc: kayıt yok";
                continue;
            }
            $md = (string) $post->content_md;
            $lines = preg_split('/\r\n|\n|\r/', $md); // \R kullanılmaz (0x85 devam baytı)
            if (collect($spec['lines'])->every(fn ($new) => in_array($new, $lines, true))) {
                continue; // zaten uygulanmış
            }
            $changed = $lines;
            foreach ($spec['lines'] as $old => $new) {
                $hits = array_keys($changed, $old, true);
                if (count($hits) !== 1) {
                    $problems[] = "fees $loc/{$spec['slug']} (id {$post->id}): '" . mb_substr($old, 0, 40) . "…' satırı tam bir kez bulunamadı (" . count($hits) . ')';
                    continue 2;
                }
                $changed[$hits[0]] = $new;
            }
            $eol = str_contains($md, "\r\n") ? "\r\n" : "\n";
            $assembled = implode($eol, $changed);
            if (! mb_check_encoding($assembled, 'UTF-8')) {
                $problems[] = "fees $loc: birleştirilen metin UTF-8 değil";
                continue;
            }
            $post->content_md = $assembled;
            $plan[] = $post;
        }

        if ($problems) {
            throw new RuntimeException('Ücret düzeltmesi uygulanmadı, hiçbir şey yazılmadı: ' . implode('; ', $problems));
        }
        if (! $plan) {
            return;
        }
        DB::transaction(function () use ($plan) {
            foreach ($plan as $post) {
                try {
                    $post->save(); // content_html model hook'unda yeniden üretilir
                } catch (\Throwable $e) {
                    throw new RuntimeException("Ücret düzeltmesi uygulanmadı, hiçbir şey yazılmadı: {$post->locale}/{$post->slug} (id {$post->id}) kaydedilemedi: " . $e->getMessage(), 0, $e);
                }
            }
        });
    }

    public function down(): void
    {
        // Geri alma yok — içerik düzeltmesi.
    }
};
