<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * DAAD programlarını veri dosyasına çıkarır — prod'a taşımanın TEK güvenli yolu.
 *
 * NEDEN DOSYA: prod'da artisan çalıştıramıyoruz ve deploy'un migrate adımında canlı API'den
 * 3.245 kaydı 1 sn aralıkla çekmek ~1 saat sürerdi. Bu yüzden veri LOKALDE `daad:import` ile
 * çekilir, buradan dosyaya yazılır, migration dosyadan uygular (hk-programs-*.json deseniyle
 * aynı). Üniversite SLUG + AD ile taşınır; prod ID'lerine güvenilmez.
 */
class ExportDaadPrograms extends Command
{
    protected $signature = 'daad:export-file {--out=resources/data/daad-programs.json}';

    protected $description = 'source=daad programları veri dosyasına çıkarır (migration ile prod\'a taşınır).';

    public function handle(): int
    {
        $rows = DB::table('programs as p')
            ->leftJoin('universities as u', 'u.id', '=', 'p.university_id')
            ->leftJoin('fields_of_study as f', 'f.id', '=', 'p.field_of_study_id')
            ->where('p.source', 'daad')
            ->get([
                'p.source_id', 'p.name_de', 'p.name_en', 'p.slug', 'p.degree', 'p.language',
                'p.duration_semesters', 'p.start_semester', 'p.tuition_fee_eur', 'p.admission_summary',
                'p.description_en', 'p.image_url', 'p.language_level_de', 'p.language_level_en',
                'p.is_online', 'p.study_form', 'p.financial_support', 'p.support_info',
                'p.source_url', 'p.study_fields_raw',
                'u.slug as university_slug', 'u.name_de as university_name',
                'f.slug as field_slug',
            ]);

        $out = [];
        foreach ($rows as $r) {
            if (! $r->university_slug || ! $r->source_id) {
                continue; // üniversitesi çözülemeyen kayıt prod'a taşınmaz
            }
            $out[] = (array) $r;
        }

        $path = base_path((string) $this->option('out'));
        file_put_contents($path, json_encode([
            'source'       => 'DAAD International Programmes API',
            'generated_at' => now()->toIso8601String(),
            'note'         => 'daad:import ile lokalde cekildi. Migration source_id ile upsert eder.',
            'programs'     => $out,
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        $this->info(count($out) . ' program yazıldı: ' . $path);

        return self::SUCCESS;
    }
}
