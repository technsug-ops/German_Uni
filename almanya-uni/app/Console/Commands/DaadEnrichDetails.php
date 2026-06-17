<?php

namespace App\Console\Commands;

use App\Models\Program;
use App\Services\DeadlineParser;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

/**
 * DAAD International Programmes detay sayfalarından YAPISAL, program-spesifik gereklilik
 * verisi çeker (İngilizce). DAAD'ın tutarlı şablonu → güvenilir parse.
 *  - "Academic admission requirements" → qualification_requirements_en
 *  - "Language requirements"           → language_requirements_en
 *  - "Application deadline"             → DeadlineParser ile summer/winter (boşsa)
 *
 * source=daad programlar (source_url zaten detay sayfası). Rate-limit'li. Idempotent:
 * varsayılan sadece _en BOŞ olanları çeker.
 *
 *   php artisan daad:enrich-details --dry-run --limit=10
 *   php artisan daad:enrich-details --apply
 *   php artisan daad:enrich-details --apply --all   (dolu olanları da yenile)
 */
class DaadEnrichDetails extends Command
{
    protected $signature = 'daad:enrich-details
        {--apply : Yaz (varsayılan dry-run)}
        {--all : Dolu _en alanları da yeniden çek}
        {--limit= : Max program (test)}
        {--sleep=500 : İstekler arası ms}';

    protected $description = 'DAAD detay sayfalarından program-spesifik gereklilik (EN) + deadline çeker.';

    private const UA = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0 Safari/537.36';

    public function handle(DeadlineParser $parser): int
    {
        $apply = $this->option('apply');
        $sleep = ((int) $this->option('sleep')) * 1000;

        $q = Program::where('is_active', 1)->where('source', 'daad')
            ->whereNotNull('source_url')->where('source_url', 'like', '%international-programmes%');
        if (! $this->option('all')) {
            // Hem qual hem lang boş olanlar (biri doluysa "işlendi" say — boşuna yeniden çekme).
            $q->where(function ($x) {
                $x->whereNull('qualification_requirements_en')->orWhere('qualification_requirements_en', '');
            })->where(function ($x) {
                $x->whereNull('language_requirements_en')->orWhere('language_requirements_en', '');
            });
        }
        if ($lim = $this->option('limit')) $q->limit((int) $lim);

        $total = (clone $q)->count();
        $this->info(($apply ? '▶ APPLY' : '🔍 DRY-RUN') . " — {$total} DAAD programı");

        $stats = ['qual' => 0, 'lang' => 0, 'deadline' => 0, 'fetched' => 0, 'fail' => 0]; $shown = 0;

        foreach ($q->cursor() as $p) {
            try {
                $res = Http::withHeaders(['User-Agent' => self::UA])->timeout(20)->get($p->source_url);
                if (! $res->ok()) { $stats['fail']++; continue; }
                $html = $res->body();
                $stats['fetched']++;
            } catch (\Throwable $e) { $stats['fail']++; continue; }

            $qual = $this->section($html, 'Academic admission requirements');
            $lang = $this->section($html, 'Language requirements');
            $dlText = $this->section($html, 'Application deadline');

            $updates = [];
            if ($qual && ($this->option('all') || empty($p->qualification_requirements_en))) { $updates['qualification_requirements_en'] = $qual; $stats['qual']++; }
            if ($lang && ($this->option('all') || empty($p->language_requirements_en))) { $updates['language_requirements_en'] = $lang; $stats['lang']++; }

            // Deadline — sadece boşsa, detay metninden
            if ($dlText && $p->application_deadline_summer === null && $p->application_deadline_winter === null) {
                $r = $parser->parse($dlText);
                if ($r['winter']) { $updates['application_deadline_winter'] = $r['winter']; }
                if ($r['summer']) { $updates['application_deadline_summer'] = $r['summer']; }
                if ($r['winter'] || $r['summer']) $stats['deadline']++;
            }

            if ($shown < 6 && $updates) {
                $this->line("  #{$p->id} " . implode(', ', array_keys($updates)));
                if ($qual) $this->line('     qual: ' . mb_substr($qual, 0, 90));
                $shown++;
            }
            if ($apply && $updates) {
                Program::whereKey($p->id)->update($updates);
            }
            if ($sleep) usleep($sleep);
        }

        $this->newLine();
        $this->line("Çekilen: {$stats['fetched']}  ·  qual: {$stats['qual']}  ·  lang: {$stats['lang']}  ·  deadline: {$stats['deadline']}  ·  hata: {$stats['fail']}");
        if (! $apply) $this->warn('Uygulamak için --apply.');
        return self::SUCCESS;
    }

    /** DAAD detay HTML'inden bir bölümün metnini çıkarır (tag temizler). */
    private function section(string $html, string $label): ?string
    {
        $lq = preg_quote($label, '#');
        if (! preg_match('#' . $lq . '\s*</[^>]+>(.{0,1500}?)(?:<h[1-4]|<dt[ >]|<dl[ >])#is', $html, $m)
            && ! preg_match('#' . $lq . '(.{0,1500}?)(?:<h[1-4]|<dt[ >])#is', $html, $m)) {
            return null;
        }
        $t = preg_replace('#<[^>]+>#', ' ', $m[1]);
        $t = html_entity_decode($t, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $t = trim(preg_replace('/\s+/u', ' ', $t));
        // Gürültü/boş değerleri ele
        if ($t === '' || mb_strlen($t) < 4 || preg_match('/^(none|n\/a|-)$/i', $t)) return null;
        return mb_substr($t, 0, 3000);
    }
}
