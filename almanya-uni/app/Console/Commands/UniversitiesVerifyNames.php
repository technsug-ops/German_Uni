<?php

namespace App\Console\Commands;

use App\Models\University;
use App\Services\WikidataService;
use Illuminate\Console\Command;

/**
 * Üniversite adlarını (name_de) Wikidata RESMİ etiketiyle karşılaştırır — uydurma/yanlış
 * adları yakalar. Wikidata rdfs:label resmi kurum adıdır; DB'deki ad ondan ÇOK farklıysa
 * (düşük benzerlik) muhtemelen AI/import artefaktı (ör. "Hochschule Bochum" yerine
 * "Hochschule für Technik, Wirtschaft und Gesundheit Bochum").
 *
 *   php artisan universities:verify-names                 → rapor (benzerliğe göre sıralı)
 *   php artisan universities:verify-names --threshold=80   → sadece <%80 benzerlik göster
 *   php artisan universities:verify-names --apply --threshold=70
 *        → <%70 benzerlikteki adları Wikidata etiketiyle DÜZELT (eski adı short_name'e taşımaz; dikkatli)
 */
class UniversitiesVerifyNames extends Command
{
    protected $signature = 'universities:verify-names
        {--threshold=85 : Bu benzerlik %% altındakileri göster}';

    protected $description = 'Üni adlarını Wikidata resmi etiketiyle doğrular; uydurma/yanlış adları RAPORLAR (read-only).';

    // NOT: Otomatik --apply YOK. Wikidata etiketleri sık sık ESKİ/kısaltma olduğundan
    // (denetimde 48 uyuşmazlığın 36'sı yanlış alarmdı) körü körüne yazmak doğru adları
    // bozar. Bu komut sadece ADAY raporlar; düzeltme tek tek doğrulanıp migration ile yapılır.
    public function handle(WikidataService $wd): int
    {
        $threshold = (int) $this->option('threshold');

        $unis = University::where('is_active', 1)->whereNotNull('wikidata_id')
            ->get(['id', 'slug', 'name_de', 'wikidata_id']);
        $this->line("Wikidata'lı aktif üni: {$unis->count()}  ·  eşik: <%{$threshold}");

        $labels = $wd->getEntityLabels($unis->pluck('wikidata_id')->all());

        $mismatches = [];
        foreach ($unis as $u) {
            $wdName = $labels[$u->wikidata_id]['de'] ?? $labels[$u->wikidata_id]['en'] ?? null;
            if (! $wdName || preg_match('/^Q\d+$/', $wdName)) continue; // etiket yok

            $a = $this->norm($u->name_de);
            $b = $this->norm($wdName);
            if ($a === $b) continue;

            similar_text($a, $b, $pct);
            if ($pct >= $threshold) continue;

            $mismatches[] = ['u' => $u, 'wd' => $wdName, 'pct' => round($pct)];
        }

        usort($mismatches, fn ($x, $y) => $x['pct'] <=> $y['pct']);

        $this->newLine();
        $this->error('Şüpheli ad (DB ≠ Wikidata, <%' . $threshold . '): ' . count($mismatches));
        foreach ($mismatches as $m) {
            $this->line(sprintf('  [%%%2d] #%d', $m['pct'], $m['u']->id));
            $this->line('        DB : ' . $m['u']->name_de);
            $this->line('        WD : ' . $m['wd'] . '   (' . $m['u']->wikidata_id . ')');
        }

        $this->newLine();
        $this->warn('Bu yalnızca ADAY listesidir. Her birini otoriter kaynakla doğrulayıp migration ile düzeltin (Wikidata etiketi sık sık eski/kısaltmadır).');

        return self::SUCCESS;
    }

    private function norm(string $s): string
    {
        $s = mb_strtolower(trim($s));
        $s = preg_replace('/\s+/u', ' ', $s);
        return $s;
    }
}
