<?php

namespace App\Console\Commands;

use App\Models\Program;
use App\Services\Content\ProgramFieldClassifier;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Alanı BOŞ olan programlara alan atar (ProgramFieldClassifier).
 *
 * NEDEN: DAAD importer'ı field_of_study_id yazmıyor; partner beslemesinde de eşleşmeyenler var.
 * Alanı boş program, alan sıralamalarında ve alan sayfalarında GÖRÜNMEZ — yani 2.693 program
 * katalogda duruyor ama sitenin en çok kullanılan gezinme eksenine hiç girmiyordu.
 *
 * Emin olunamayan kayda DOKUNMAZ (yanlış alan, boş alandan kötüdür: sıralama puanının
 * %22'si alan derinliğine bakıyor).
 */
class ClassifyProgramFields extends Command
{
    protected $signature = 'programs:classify-fields
        {--dry-run : sadece raporla}
        {--source= : yalnız bu kaynak (daad/partner/hochschulkompass)}';

    protected $description = 'Alanı boş programlara ad/konu metninden alan atar (emin olunamayanı atlar).';

    public function handle(ProgramFieldClassifier $classifier): int
    {
        $dry = (bool) $this->option('dry-run');

        $fieldIds = DB::table('fields_of_study')->pluck('id', 'slug')->all();

        $q = Program::query()->whereNull('field_of_study_id')->where('is_active', 1);
        if ($src = $this->option('source')) {
            $q->where('source', $src);
        }

        $stats = ['taranan' => 0, 'atanan' => 0, 'emin_degil' => 0];
        $byField = [];

        $q->chunkById(500, function ($programs) use ($classifier, $fieldIds, $dry, &$stats, &$byField) {
            foreach ($programs as $p) {
                $stats['taranan']++;

                $raw = $p->study_fields_raw;
                $rawText = is_array($raw) ? implode(' ', $raw) : (string) $raw;

                $slug = $classifier->classify([$p->name_de, $p->name_en, $rawText]);
                if (! $slug || ! isset($fieldIds[$slug])) {
                    $stats['emin_degil']++;
                    continue;
                }

                $stats['atanan']++;
                $byField[$slug] = ($byField[$slug] ?? 0) + 1;

                if (! $dry) {
                    $p->field_of_study_id = $fieldIds[$slug];
                    $p->saveQuietly();
                }
            }
        });

        arsort($byField);
        foreach ($byField as $slug => $n) {
            $this->line('  ' . str_pad($slug, 20) . $n);
        }

        $this->newLine();
        $this->info(sprintf(
            '%d program tarandı · %d atandı · %d emin olunamadı (dokunulmadı)%s',
            $stats['taranan'],
            $stats['atanan'],
            $stats['emin_degil'],
            $dry ? ' [DRY-RUN]' : ''
        ));

        return self::SUCCESS;
    }
}
