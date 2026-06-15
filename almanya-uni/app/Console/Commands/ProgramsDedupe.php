<?php

namespace App\Console\Commands;

use App\Models\Favorite;
use App\Models\Program;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Aynı üni + ad + derece için birden çok aktif program kaydını birleştirir.
 * Tipik kaynak: aynı program hem `partner` hem `daad` import'undan iki kez yaratılır.
 *
 * Strateji: en ÇOK alanı dolu (en zengin) kaydı KORU; diğer(ler)ini pasifleştir
 * (is_active=0 — silmez, geri alınabilir). Favoriler korunan kayda taşınır.
 * Eşitlikte source_id olanı (daad resmi kimliği) tercih eder, o da yoksa düşük id.
 *
 *   php artisan programs:dedupe           → DRY-RUN
 *   php artisan programs:dedupe --apply    → uygula
 */
class ProgramsDedupe extends Command
{
    protected $signature = 'programs:dedupe {--apply : Değişiklikleri yaz (varsayılan dry-run)}';

    protected $description = 'Aynı üni+ad+derece dupe programları birleştirir (zengin kaydı tut, diğerini pasifleştir).';

    /** Zenginlik skoru için sayılan alanlar. */
    private const RICH_FIELDS = [
        'application_deadline_summer', 'application_deadline_winter', 'application_fee_eur',
        'tuition_fee_eur', 'cost_per_semester_eur', 'nc_value', 'admission_mode', 'admission_summary',
        'qualification_requirements_tr', 'language_requirements_tr', 'required_documents_tr',
        'description_tr', 'description_en', 'duration_semesters', 'study_form', 'source_url', 'image_url',
        'field_of_study_id',
    ];

    public function handle(): int
    {
        $apply = $this->option('apply');
        $this->info($apply ? '🔥 APPLY' : '🔍 DRY-RUN');

        $groups = DB::table('programs')->where('is_active', 1)
            ->select('university_id', 'name_de', 'degree', DB::raw('count(*) c'))
            ->groupBy('university_id', 'name_de', 'degree')->having('c', '>', 1)->get();

        $deactivated = 0;
        foreach ($groups as $g) {
            $rows = Program::where('is_active', 1)
                ->where('university_id', $g->university_id)
                ->where('name_de', $g->name_de)
                ->where('degree', $g->degree)
                ->get();

            // En zengin kaydı seç (skor → source_id varlığı → düşük id)
            $keep = $rows->sort(function ($a, $b) {
                $sa = $this->score($a); $sb = $this->score($b);
                if ($sa !== $sb) return $sb <=> $sa;
                $ia = $a->source_id ? 1 : 0; $ib = $b->source_id ? 1 : 0;
                if ($ia !== $ib) return $ib <=> $ia;
                return $a->id <=> $b->id;
            })->first();

            $losers = $rows->where('id', '!=', $keep->id);
            $this->line("  «{$g->name_de}» [{$g->degree}] → KORU #{$keep->id} ({$keep->source}, skor {$this->score($keep)})");
            foreach ($losers as $l) {
                $this->line("      pasifleştir #{$l->id} ({$l->source}, skor {$this->score($l)})");
                if ($apply) {
                    // Favorileri korunan kayda taşı (yinelenenleri at)
                    Favorite::where('favoriteable_type', Program::class)->where('favoriteable_id', $l->id)
                        ->each(function (Favorite $f) use ($keep) {
                            $exists = Favorite::where('favoriteable_type', Program::class)
                                ->where('favoriteable_id', $keep->id)
                                ->where('user_id', $f->user_id)->exists();
                            $exists ? $f->delete() : $f->update(['favoriteable_id' => $keep->id]);
                        });
                    $l->update(['is_active' => false]);
                }
                $deactivated++;
            }
        }

        $this->newLine();
        $this->line("Dupe grubu: {$groups->count()}  ·  Pasifleştirilen: {$deactivated}");
        if (! $apply && $deactivated) {
            $this->warn('Uygulamak için --apply ile çalıştırın.');
        }

        return self::SUCCESS;
    }

    private function score(Program $p): int
    {
        $n = 0;
        foreach (self::RICH_FIELDS as $f) {
            $v = $p->getAttribute($f);
            if ($v !== null && $v !== '') $n++;
        }
        return $n;
    }
}
