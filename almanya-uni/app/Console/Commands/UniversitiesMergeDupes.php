<?php

namespace App\Console\Commands;

use App\Models\Favorite;
use App\Models\Program;
use App\Models\University;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * DAAD shell üniler (data_source='daad') için HRK eşli muadil ara, merge et.
 *
 * Strateji:
 * - Şehir aynı (city_id veya city name matching)
 * - Normalize edilmiş core name benzer (Uni/Hochschule farkı korunur)
 * - HRK eşli üni primary, DAAD shell merged in
 */
class UniversitiesMergeDupes extends Command
{
    protected $signature = 'universities:merge-dupes
        {--execute : Uygula (default dry-run)}
        {--threshold=85 : Minimum benzerlik %}';

    protected $description = 'DAAD shell üni dupe\'lerini HRK eşli muadiliyle merge eder.';

    public function handle(): int
    {
        $dryRun = !$this->option('execute');
        $threshold = (int) $this->option('threshold');

        $this->info($dryRun ? '🔍 DRY-RUN' : '▶ EXECUTE');

        if (!$dryRun && method_exists(University::class, 'disableSearchSyncing')) {
            University::disableSearchSyncing();
        }

        // DAAD shell veya partner-only kayıtlar (HRK eşi yok)
        $candidates = University::query()
            ->whereNull('hs_nummer')
            ->where(function ($q) {
                $q->where('data_source', 'daad')
                  ->orWhere(function ($q2) {
                      $q2->whereNotNull('partner_id')->whereNull('wikidata_id');
                  });
            })
            ->get();

        $hrkPool = University::query()
            ->whereNotNull('hs_nummer')
            ->select('id', 'name_de', 'name_en', 'short_name', 'city_id')
            ->get();

        $this->line(sprintf('DAAD/partner shell: %d  ·  HRK pool: %d', $candidates->count(), $hrkPool->count()));

        $merges = [];
        foreach ($candidates as $shell) {
            $shellNorm = $this->normalize($shell->name_de ?: $shell->name_en);
            if (!$shellNorm) continue;

            $best = null;
            $bestScore = 0;
            foreach ($hrkPool as $hrk) {
                // Şehir aynı olmalı (gevşek)
                if ($shell->city_id && $hrk->city_id && $shell->city_id !== $hrk->city_id) continue;

                foreach (array_filter([$hrk->name_de, $hrk->name_en, $hrk->short_name]) as $candName) {
                    $candNorm = $this->normalize($candName);
                    if (!$candNorm) continue;

                    similar_text($shellNorm, $candNorm, $pct);
                    if ($pct > $bestScore) {
                        $bestScore = $pct;
                        $best = $hrk;
                    }
                }
            }

            if ($best && $bestScore >= $threshold) {
                $merges[] = [
                    'shell' => $shell,
                    'primary' => $best,
                    'score' => round($bestScore, 1),
                ];
            }
        }

        $this->newLine();
        $this->info('═══ EŞLEŞMELER ═══');
        $this->line('Toplam: ' . count($merges));

        $rows = array_slice(array_map(function ($m) {
            return [
                'score' => $m['score'] . '%',
                'shell_id' => $m['shell']->id,
                'shell' => mb_substr($m['shell']->name_de ?? $m['shell']->name_en ?? '?', 0, 40),
                'primary_id' => $m['primary']->id,
                'primary' => mb_substr($m['primary']->name_de ?? '', 0, 40),
            ];
        }, $merges), 0, 15);
        if ($rows) {
            $this->table(['%', 'Shell ID', 'Shell ad', 'Primary ID', 'Primary ad'], $rows);
        }

        if ($dryRun) {
            $this->warn('DRY-RUN — değişiklik YOK. --execute ile uygula.');
            return self::SUCCESS;
        }

        $merged = 0;
        $progMoved = 0;
        $favMoved = 0;
        foreach ($merges as $m) {
            DB::transaction(function () use ($m, &$progMoved, &$favMoved) {
                $shell = $m['shell'];
                $primary = $m['primary'];

                // Programs taşı
                $progs = Program::where('university_id', $shell->id)->pluck('id')->all();
                if ($progs) {
                    Program::whereIn('id', $progs)->update(['university_id' => $primary->id]);
                    $progMoved += count($progs);
                }

                // Favorites taşı
                $favCount = DB::table('favorites')
                    ->where(['favoriteable_id' => $shell->id, 'favoriteable_type' => University::class])
                    ->update(['favoriteable_id' => $primary->id]);
                $favMoved += $favCount;

                // Primary'de boş kalan alanları shell'den doldur
                foreach (['name_en', 'description_en', 'website_url', 'logo_url', 'phone', 'street'] as $f) {
                    if (blank($primary->{$f}) && filled($shell->{$f})) {
                        $primary->{$f} = $shell->{$f};
                    }
                }
                $primary->save();

                $shell->delete();
            });
            $merged++;
        }

        $this->newLine();
        $this->info('═══ SONUÇ ═══');
        $this->line("Merge edilen üni:    $merged");
        $this->line("Taşınan program:     $progMoved");
        $this->line("Taşınan favorite:    $favMoved");
        $this->line("Kalan toplam üni:    " . University::count());

        return self::SUCCESS;
    }

    private function normalize(?string $s): string
    {
        if (!$s) return '';
        $s = mb_strtolower($s);

        // KRİTİK: "Uni" / "Hochschule" / "Fachhochschule" / "Technische" KORUNMALI
        // (farklı kurum tipleri, eşit değiller). Sadece dil eşdeğerlerini birleştir.
        $s = str_replace(['university', 'universität'], 'uni', $s);
        $s = str_replace(['fachhochschule'], 'fh', $s);
        $s = str_replace(['technical', 'technische'], 'tech', $s);
        $s = str_replace(['hochschule'], 'hs', $s);

        // Şehir dil varyantları
        $s = str_replace(['munich', 'münchen', 'munchen'], 'münchen', $s);
        $s = str_replace(['cologne', 'köln', 'koln'], 'köln', $s);
        $s = str_replace(['vienna', 'wien'], 'wien', $s);

        // Genuine stop words
        $s = preg_replace('/\b(of|the|for|f[üu]r|und|and|am|von|zu|der|in|de|st\.|staatliche?|stiftung)\b/u', ' ', $s);

        $s = preg_replace('/[^a-z0-9äöüß ]/u', ' ', $s);
        return trim(preg_replace('/\s+/', ' ', $s));
    }
}
