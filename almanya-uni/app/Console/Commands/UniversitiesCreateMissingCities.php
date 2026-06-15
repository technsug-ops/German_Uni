<?php

namespace App\Console\Commands;

use App\Models\City;
use App\Models\State;
use App\Models\University;
use App\Services\WikidataService;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

/**
 * Şehirsiz (city_id null) aktif ünilere Wikidata P131 ile gerçek şehri çözer:
 *  1) Şehir tablomuzda wikidata_id veya isimle VARSA → bağlar (uydurmaz).
 *  2) YOKSA → Wikidata'dan şehir kaydı oluşturur (isim + eyalet + koordinat) ve bağlar.
 *
 * Eyalet, şehrin P131* zinciri Bundesland'a (Q1221156) gidilerek bulunur ve bizim
 * states tablomuza wikidata_id ile eşlenir. İdempotent: ikinci çalıştırmada var olanı bağlar.
 *
 *   php artisan universities:create-missing-cities          → DRY-RUN
 *   php artisan universities:create-missing-cities --apply   → uygula
 */
class UniversitiesCreateMissingCities extends Command
{
    protected $signature = 'universities:create-missing-cities {--apply : Değişiklikleri yaz (varsayılan dry-run)}';

    protected $description = 'Şehirsiz ünilere Wikidata P131 ile şehir çözer; tabloda yoksa şehir oluşturur + bağlar.';

    public function handle(WikidataService $wd): int
    {
        $apply = $this->option('apply');
        $this->info($apply ? '🔥 APPLY' : '🔍 DRY-RUN');

        $unis = University::where('is_active', 1)->whereNull('city_id')->whereNotNull('wikidata_id')
            ->get(['id', 'slug', 'name_de', 'wikidata_id']);
        $this->line("Şehirsiz + wikidata'lı üni: {$unis->count()}");

        // 1) Wikidata: üni → şehir (P131)
        $uniCities = $wd->getUniversityCities($unis->pluck('wikidata_id')->all());
        $cityQids  = collect($uniCities)->pluck('city_qid')->unique()->values()->all();

        // 2) Wikidata: şehir → eyalet (Bundesland)
        $cityStateQ = $wd->getCityStateMapping($cityQids);
        // bizim states: wikidata_id → id
        $stateByQid = State::whereNotNull('wikidata_id')->pluck('id', 'wikidata_id');

        $linked = 0; $created = 0; $skipped = 0;
        foreach ($unis as $u) {
            $info = $uniCities[$u->wikidata_id] ?? null;
            if (! $info) { $skipped++; $this->line("  ✗ atla #{$u->id} {$u->name_de} — Wikidata'da P131 yok"); continue; }

            $cityQid = $info['city_qid'];
            $label   = $info['city_label'];

            // (a) wikidata_id ile mevcut şehir?
            $city = City::where('wikidata_id', $cityQid)->first();
            // (b) isimle mevcut şehir?
            if (! $city) {
                $city = City::whereRaw('LOWER(name_de) = ?', [mb_strtolower($label)])->first();
                if ($city && ! $city->wikidata_id && $apply) {
                    $city->update(['wikidata_id' => $cityQid]); // kanonik kimliği tamamla
                }
            }

            if ($city) {
                $this->line("  ↪ bağla #{$u->id} {$u->name_de} → mevcut «{$city->name_de}» (#{$city->id})");
                if ($apply) $u->update(['city_id' => $city->id]);
                $linked++;
                continue;
            }

            // (c) şehir oluştur
            $stateId = $stateByQid[$cityStateQ[$cityQid] ?? null] ?? null;
            $this->line("  ✚ OLUŞTUR «{$label}» (state_id=" . ($stateId ?? '?') . ") + bağla #{$u->id} {$u->name_de}");
            if ($apply) {
                $newCity = City::create([
                    'wikidata_id' => $cityQid,
                    'state_id'    => $stateId,
                    'name_tr'     => $label,
                    'name_de'     => $label,
                    'name_en'     => $label,
                    'slug'        => $this->uniqueSlug($label),
                    'latitude'    => $info['latitude'],
                    'longitude'   => $info['longitude'],
                    'is_active'   => true,
                ]);
                $u->update(['city_id' => $newCity->id]);
            }
            $created++;
        }

        $this->newLine();
        $this->line("Bağlanan (mevcut): {$linked}  ·  Oluşturulan: {$created}  ·  Çözülemeyen: {$skipped}");
        if (! $apply && ($linked || $created)) {
            $this->warn('Uygulamak için --apply ile çalıştırın.');
        }

        return self::SUCCESS;
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i = 2;
        while (City::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }
        return $slug;
    }
}
