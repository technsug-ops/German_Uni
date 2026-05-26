<?php

namespace App\Console\Commands;

use App\Models\University;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UniversitiesAudit extends Command
{
    protected $signature = 'universities:audit
        {--execute : Gerçekten uygula (varsayılan dry-run)}
        {--with-fuzzy : Name+City fuzzy match raporla (default OFF — Uni/FH farklı kurumları yanlış eşleştiriyor)}
        {--similar-threshold=92 : İsim benzerlik yüzdesi (sadece --with-fuzzy ile)}';

    protected $description = 'Üni temizliği: dupe tespit + HRK eşleşmesi olmayan şüpheli kayıt raporu. Default DRY-RUN.';

    private bool $dryRun;

    public function handle(): int
    {
        $this->dryRun = !$this->option('execute');

        $this->info($this->dryRun ? '🔍 DRY-RUN — hiçbir şey silinmeyecek' : '🔥 EXECUTE — değişiklikler kalıcı');
        $this->newLine();

        $this->reportOverall();
        $dupesByWikidata = $this->findWikidataIdDupes();
        $dupesByHsNummer = $this->findHsNummerDupes();
        $dupesByNameCity = $this->option('with-fuzzy') ? $this->findNameCityDupes() : [];
        $suspicious = $this->findSuspiciousWikidataOnly();

        $this->newLine();
        $this->line(str_repeat('═', 70));
        $this->info('📋 ÖZET');
        $this->line(str_repeat('═', 70));
        $this->line("Toplam üni:                        " . University::count());
        $this->line("Wikidata_id dupe grubu:            " . count($dupesByWikidata));
        $this->line("Name+City dupe grubu (fuzzy):      " . count($dupesByNameCity));
        $this->line("HS-Nummer dupe grubu (HRK içi):    " . count($dupesByHsNummer));
        $this->line("HRK eşi olmayan şüpheli:           " . count($suspicious));
        $this->newLine();

        if ($this->dryRun) {
            $this->warn('🚫 Hiçbir değişiklik yapılmadı. --execute ile uygulayın.');
            $this->line('Önerilen sıra:');
            $this->line('  1. php artisan universities:audit --execute (önce raporu kullanıcıya onaylat)');
            return self::SUCCESS;
        }

        $this->newLine();
        $this->warn('⚠️  EXECUTE MODE — silme/birleştirme uygulanıyor...');

        // Meilisearch çalışmıyor olabilir; Scout sync'i kapat (memory: storage/meilisearch).
        University::disableSearchSyncing();

        $merged = 0;
        // Sadece kesin sinyaller — fuzzy execute'a girmiyor (Uni/FH karışıklığı riski)
        foreach (array_merge($dupesByWikidata, $dupesByHsNummer) as $group) {
            $merged += $this->mergeGroup($group);
        }

        $deactivated = 0;
        $deleted = 0;
        foreach ($suspicious as $row) {
            [$d1, $d2] = $this->cleanupSuspicious($row);
            $deleted += $d1;
            $deactivated += $d2;
        }

        University::enableSearchSyncing();

        $this->newLine();
        $this->info('✅ Tamamlandı');
        $this->line("Birleştirilen dupe:    $merged");
        $this->line("Tamamen silinen:       $deleted");
        $this->line("Pasifleştirilen:       $deactivated");
        $this->line("Kalan toplam üni:      " . University::count());
        $this->line("Aktif üni:             " . University::where('is_active', true)->count());
        $this->newLine();
        $this->warn('💡 Meilisearch ileride başlatılırsa: php artisan scout:import "App\\Models\\University"');

        return self::SUCCESS;
    }

    private function reportOverall(): void
    {
        $this->line(str_repeat('─', 70));
        $this->info('🗂  Kaynak kırılımı');
        $rows = DB::table('universities')
            ->selectRaw('data_source, COUNT(*) c, SUM(CASE WHEN hrk_member=1 THEN 1 ELSE 0 END) hrk')
            ->groupBy('data_source')
            ->get();
        foreach ($rows as $r) {
            $this->line(sprintf('  %-20s %5d  (HRK üye: %d)', $r->data_source ?? 'NULL', $r->c, $r->hrk));
        }
        $this->newLine();
    }

    private function findWikidataIdDupes(): array
    {
        $rows = DB::table('universities')
            ->selectRaw('wikidata_id, COUNT(*) c')
            ->whereNotNull('wikidata_id')
            ->where('wikidata_id', '!=', '')
            ->groupBy('wikidata_id')
            ->having('c', '>', 1)
            ->get();

        if ($rows->isEmpty()) {
            $this->info('✓ Wikidata_id dupe YOK');
            return [];
        }

        $this->warn('⚠ Wikidata_id dupe:');
        $groups = [];
        foreach ($rows as $r) {
            $ids = University::where('wikidata_id', $r->wikidata_id)->pluck('id')->all();
            $groups[] = ['type' => 'wikidata_id', 'key' => $r->wikidata_id, 'ids' => $ids];
            $this->line("  {$r->wikidata_id} → IDs: " . implode(',', $ids));
        }
        return $groups;
    }

    private function findHsNummerDupes(): array
    {
        $rows = DB::table('universities')
            ->selectRaw('hs_nummer, COUNT(*) c')
            ->whereNotNull('hs_nummer')
            ->where('hs_nummer', '!=', '')
            ->groupBy('hs_nummer')
            ->having('c', '>', 1)
            ->get();

        if ($rows->isEmpty()) {
            $this->info('✓ HS-Nummer dupe YOK');
            return [];
        }

        $this->warn('⚠ HS-Nummer (HRK) dupe:');
        $groups = [];
        foreach ($rows as $r) {
            $ids = University::where('hs_nummer', $r->hs_nummer)->pluck('id')->all();
            $groups[] = ['type' => 'hs_nummer', 'key' => $r->hs_nummer, 'ids' => $ids];
            $this->line("  HS#{$r->hs_nummer} → IDs: " . implode(',', $ids));
        }
        return $groups;
    }

    private function findNameCityDupes(): array
    {
        $threshold = (int) $this->option('similar-threshold');
        $unis = University::query()
            ->select('id', 'name_de', 'short_name', 'city_id', 'hrk_member', 'hs_nummer', 'wikidata_id', 'data_source')
            ->orderBy('id')
            ->get();

        $byCity = $unis->groupBy('city_id');
        $groups = [];

        foreach ($byCity as $cityId => $list) {
            if (!$cityId || $list->count() < 2) {
                continue;
            }
            $arr = $list->all();
            $n = count($arr);
            for ($i = 0; $i < $n; $i++) {
                for ($j = $i + 1; $j < $n; $j++) {
                    $a = $this->normalizeName($arr[$i]->name_de ?? '');
                    $b = $this->normalizeName($arr[$j]->name_de ?? '');
                    if ($a === '' || $b === '') continue;

                    similar_text($a, $b, $pct);
                    if ($pct >= $threshold) {
                        $groups[] = [
                            'type' => 'name_city',
                            'key' => substr($a, 0, 40) . " @city=$cityId",
                            'ids' => [$arr[$i]->id, $arr[$j]->id],
                            'similarity' => round($pct, 1),
                        ];
                    }
                }
            }
        }

        if (empty($groups)) {
            $this->info('✓ Name+City fuzzy dupe YOK');
            return [];
        }

        $this->warn('⚠ Name+City fuzzy dupe (eşik %' . $threshold . '):');
        foreach ($groups as $g) {
            $this->line("  [%{$g['similarity']}] {$g['key']} → IDs: " . implode(',', $g['ids']));
        }
        return $groups;
    }

    private function findSuspiciousWikidataOnly(): array
    {
        $candidates = University::query()
            ->where('data_source', 'wikidata')
            ->whereNull('hrk_member')
            ->whereNull('hs_nummer')
            ->whereNull('partner_id')
            ->select('id', 'name_de', 'wikidata_id', 'city_id', 'website_url', 'student_count')
            ->withCount(['programs'])
            ->get();

        if ($candidates->isEmpty()) {
            $this->info('✓ HRK eşi olmayan Wikidata-only yok');
            return [];
        }

        $this->warn('⚠ HRK eşi YOK, Wikidata-only şüpheli (' . $candidates->count() . ' kayıt):');
        $sample = $candidates->take(15);
        foreach ($sample as $c) {
            $this->line(sprintf(
                '  #%d  %-45s  programs=%d  site=%s',
                $c->id,
                substr($c->name_de ?? '???', 0, 45),
                $c->programs_count,
                $c->website_url ? '✓' : '✗'
            ));
        }
        if ($candidates->count() > 15) {
            $this->line('  ... ve ' . ($candidates->count() - 15) . ' tane daha');
        }

        $withPrograms = $candidates->where('programs_count', '>', 0)->count();
        $withoutPrograms = $candidates->count() - $withPrograms;
        $this->newLine();
        $this->line("  → Programı olan (pasifleştirilecek):  $withPrograms");
        $this->line("  → Programsız (tamamen silinecek):     $withoutPrograms");

        return $candidates->map(fn ($c) => [
            'id' => $c->id,
            'programs_count' => $c->programs_count,
            'name' => $c->name_de,
        ])->all();
    }

    private function normalizeName(string $s): string
    {
        $s = mb_strtolower($s);
        $s = preg_replace('/[^a-z0-9äöüß ]/u', ' ', $s);
        $s = preg_replace('/\b(universität|university|hochschule|fachhochschule|fh|hs|der|für|und|von|zu)\b/u', '', $s);
        $s = preg_replace('/\s+/', ' ', $s);
        return trim($s);
    }

    private function mergeGroup(array $group): int
    {
        $unis = University::whereIn('id', $group['ids'])->get()
            ->sortByDesc(fn ($u) => ($u->hrk_member ? 100 : 0) + ($u->hs_nummer ? 50 : 0) + ($u->website_url ? 10 : 0))
            ->values();

        if ($unis->count() < 2) return 0;

        $primary = $unis->first();
        $merged = 0;

        foreach ($unis->skip(1) as $dupe) {
            DB::transaction(function () use ($primary, $dupe) {
                DB::table('programs')->where('university_id', $dupe->id)->update(['university_id' => $primary->id]);
                DB::table('favorites')->where(['favoriteable_id' => $dupe->id, 'favoriteable_type' => University::class])
                    ->update(['favoriteable_id' => $primary->id]);

                foreach (['wikidata_id', 'hs_nummer', 'website_url', 'phone', 'street', 'postal_code', 'logo_url',
                          'founded_year', 'student_count', 'hochschultyp', 'traegerschaft', 'description_de'] as $f) {
                    if (blank($primary->{$f}) && filled($dupe->{$f})) {
                        $primary->{$f} = $dupe->{$f};
                    }
                }
                if (!$primary->hrk_member && $dupe->hrk_member) {
                    $primary->hrk_member = true;
                }
                $primary->save();

                $dupe->delete();
            });
            $merged++;
            $this->line("  ↪ #{$dupe->id} → #{$primary->id} merge ({$dupe->name_de})");
        }
        return $merged;
    }

    private function cleanupSuspicious(array $row): array
    {
        $u = University::find($row['id']);
        if (!$u) return [0, 0];

        if ($row['programs_count'] > 0) {
            $u->update(['is_active' => false]);
            return [0, 1];
        }

        DB::table('favorites')->where(['favoriteable_id' => $u->id, 'favoriteable_type' => University::class])->delete();
        $u->delete();
        return [1, 0];
    }
}
