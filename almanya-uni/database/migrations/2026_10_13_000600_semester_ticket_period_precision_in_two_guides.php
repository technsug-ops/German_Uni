<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Dönem hassasiyeti (takvim yılı ≠ akademik dönem):
 *
 * 1) Deutschlandticket rehberi (TR/EN/DE): bir önceki düzeltme "2026: 37,80 €/ay" diyerek bütün 2026'yı tek fiyata
 *    bağlamıştı. Deutschlandsemesterticket dönem başındaki Deutschlandticket fiyatının %60'ıdır; KVB (Köln) kurumsal
 *    kaynağına göre 2026 yaz dönemi 34,80 €/ay (208,80 €/dönem), 2026/27 kış dönemi 37,80 €/ay (226,80 €/dönem).
 *    Kaynak: https://www.kvb.koeln/abos-und-tickets/alle-abos/deutschlandsemesterticket.html (13 Ekim 2026'da kontrol
 *    edildi). Kurum kapsamı korunur: "KVB Köln örneği; kendi üniversitenin tutarını kontrol et" — tek kurumdan
 *    genelleme yapılmaz. Yalnız önceki düzeltmenin üç yeri (özet, liste satırı, meta/excerpt) değişir.
 *
 * 2) Ücret rehberi (TR/DE) Senaryo A: 2 × 379,06 € yıllık tahmini, 2026/27 kış ücretinin 2027 yaz döneminde de
 *    geçerli olduğu varsayımına dayanır; bu varsayım satırda açıkça belirtilir (yaz 2027 ücreti henüz yayımlanmadı).
 * Her hedef tam bir kez bulunmalı; aksi halde hiçbir şey yazılmaz. Tek transaction; rerun no-op.
 */
return new class extends Migration
{
    private const TICKET = [
        'tr' => ['slug' => 'deutschlandticket-2026-price-coverage-and-the-student-semester-ticket', 'md' => [
            "'i (2026: aylık €37.80, standart fiyatın %60'ı) Semesterbeitrag ile alır." => "'i Semesterbeitrag ile alır; fiyat döneme göre değişir: KVB (Köln) örneğinde 2026 yaz dönemi aylık €34.80, 2026/27 kış dönemi aylık €37.80 (dönem başındaki Deutschlandticket fiyatının %60'ı) — kendi üniversitenin tutarını kontrol et.",
            'Standart fiyatın %60\'ı: 2026\'da **aylık €37.80** (≈ dönemlik €226.80; 2025\'te €34.80/ay idi).' => 'Standart fiyatın %60\'ı, dönem başındaki Deutschlandticket fiyatına göre belirlenir — KVB (Köln) örneği: 2026 yaz dönemi **aylık €34.80** (≈ dönemlik €208.80), 2026/27 kış dönemi **aylık €37.80** (≈ dönemlik €226.80). Üniversiteler dönem fiyatını kendi sayfalarında duyurur.',
        ], 'meta' => ['(€37.80/ay, 2026)' => '(2026/27 kış: €37.80/ay; 2026 yaz: €34.80/ay, KVB örneği)']],
        'en' => ['slug' => 'deutschlandticket-2026-price-coverage-and-the-student-semester-ticket-en', 'md' => [
            '(€37.80/month in 2026, 60% of the standard price) via the Semesterbeitrag.' => 'via the Semesterbeitrag; the price is set per semester: at KVB (Cologne) it is €34.80/month for summer semester 2026 and €37.80/month for winter semester 2026/27 (60% of the Deutschlandticket price at the start of the semester) — check your own university\'s amount.',
            '60% of the standard price: **€37.80/month** in 2026 (≈ €226.80 per semester; it was €34.80/month in 2025).' => '60% of the Deutschlandticket price at the start of the semester — KVB (Cologne) example: summer semester 2026 **€34.80/month** (≈ €208.80 per semester), winter semester 2026/27 **€37.80/month** (≈ €226.80 per semester). Universities publish their own semester price.',
        ], 'meta' => ['(€37.80/month in 2026)' => '(winter 2026/27: €37.80/month; summer 2026: €34.80/month, KVB example)']],
        'de' => ['slug' => 'deutschlandticket-2026-price-coverage-and-the-student-semester-ticket-de', 'md' => [
            '(2026: 37,80 €/Monat, 60 % des Standardpreises) über den Semesterbeitrag.' => 'über den Semesterbeitrag; der Preis gilt je Semester: bei der KVB (Köln) 34,80 €/Monat im Sommersemester 2026 und 37,80 €/Monat im Wintersemester 2026/27 (60 % des Deutschlandticket-Preises zu Semesterbeginn) — prüfe den Betrag deiner Hochschule.',
            '60 % des Standardpreises: 2026 **37,80 €/Monat** (≈ 226,80 € pro Semester; 2025 waren es 34,80 €/Monat).' => '60 % des Deutschlandticket-Preises zu Semesterbeginn — Beispiel KVB (Köln): Sommersemester 2026 **34,80 €/Monat** (≈ 208,80 € pro Semester), Wintersemester 2026/27 **37,80 €/Monat** (≈ 226,80 € pro Semester). Die Hochschulen veröffentlichen ihren Semesterpreis selbst.',
        ], 'meta' => ['(37,80 €/Monat, 2026)' => '(WiSe 2026/27: 37,80 €/Monat; SoSe 2026: 34,80 €/Monat, Beispiel KVB)']],
    ];

    private const FEES = [
        'tr' => ['slug' => 'is-university-free-in-germany-2026-real-costs', 'lines' => [
            '| Semesterbeitrag (2 sömestre, TU Berlin 2026/27: 2 × 379,06 €; Deutschlandsemesterticket dahil) | 758,12 € |' => '| Semesterbeitrag (2 dönem; TU Berlin 2026/27 kış ücreti 379,06 € ile hesaplandı ve aynı ücretin 2027 yaz döneminde de geçerli olduğu varsayıldı — yaz 2027 ücreti henüz yayımlanmadı; Deutschlandsemesterticket dahil) | ~758 € |',
        ]],
        'de' => ['slug' => 'is-university-free-in-germany-2026-real-costs-de', 'lines' => [
            '| Semesterbeitrag (2 Semester, TU Berlin WS 2026/27: 2 × 379,06 €; Deutschlandsemesterticket inklusive) | 758,12 € |' => '| Semesterbeitrag (2 Semester; gerechnet mit dem TU-Berlin-Beitrag WS 2026/27 von 379,06 € unter der Annahme, dass er auch im Sommersemester 2027 gilt — der Beitrag für SoSe 2027 ist noch nicht veröffentlicht; Deutschlandsemesterticket inklusive) | ~758 € |',
        ]],
    ];

    public function up(): void
    {
        if (! Schema::hasTable('posts')) {
            return;
        }
        $problems = [];
        $plan = [];
        foreach (self::TICKET as $loc => $spec) {
            $post = Post::where('locale', $loc)->where('slug', $spec['slug'])->first();
            if (! $post) {
                continue; // yerel kopyada yok; prod'da var
            }
            $md = (string) $post->content_md;
            if (collect($spec['md'])->every(fn ($new, $old) => str_contains($md, $new) && ! str_contains($md, $old))) {
                continue;
            }
            foreach ($spec['md'] as $old => $new) {
                if (substr_count($md, $old) !== 1) {
                    $problems[] = "ticket $loc (id {$post->id}): '" . mb_substr($old, 0, 40) . "…' tam bir kez bulunamadı (" . substr_count($md, $old) . ')';
                    continue 2;
                }
            }
            $post->content_md = str_replace(array_keys($spec['md']), array_values($spec['md']), $md);
            foreach ($spec['meta'] as $old => $new) {
                foreach (['meta_description', 'excerpt'] as $field) {
                    if (str_contains((string) $post->{$field}, $old)) {
                        $post->{$field} = str_replace($old, $new, (string) $post->{$field});
                    }
                }
            }
            $plan[] = $post;
        }
        foreach (self::FEES as $loc => $spec) {
            $post = Post::where('locale', $loc)->where('slug', $spec['slug'])->first();
            if (! $post) {
                continue;
            }
            $md = (string) $post->content_md;
            $lines = preg_split('/\r\n|\n|\r/', $md);
            if (collect($spec['lines'])->every(fn ($new) => in_array($new, $lines, true))) {
                continue;
            }
            $changed = $lines;
            foreach ($spec['lines'] as $old => $new) {
                $hits = array_keys($changed, $old, true);
                if (count($hits) !== 1) {
                    $problems[] = "fees $loc (id {$post->id}): '" . mb_substr($old, 0, 40) . "…' satırı tam bir kez bulunamadı (" . count($hits) . ')';
                    continue 2;
                }
                $changed[$hits[0]] = $new;
            }
            $assembled = implode(str_contains($md, "\r\n") ? "\r\n" : "\n", $changed);
            if (! mb_check_encoding($assembled, 'UTF-8')) {
                $problems[] = "fees $loc: UTF-8 değil";
                continue;
            }
            $post->content_md = $assembled;
            $plan[] = $post;
        }
        if ($problems) {
            throw new RuntimeException('Dönem düzeltmesi uygulanmadı, hiçbir şey yazılmadı: ' . implode('; ', $problems));
        }
        if (! $plan) {
            return;
        }
        DB::transaction(function () use ($plan) {
            foreach ($plan as $post) {
                try {
                    $post->save();
                } catch (\Throwable $e) {
                    throw new RuntimeException("Dönem düzeltmesi uygulanmadı: {$post->locale}/{$post->slug} (id {$post->id}) kaydedilemedi: " . $e->getMessage(), 0, $e);
                }
            }
        });
    }

    public function down(): void
    {
        // Geri alma yok — içerik düzeltmesi.
    }
};
