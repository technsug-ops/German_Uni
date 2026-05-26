<?php

namespace App\Console\Commands;

use App\Models\University;
use Illuminate\Console\Command;

class ExportProgramsForAdmission extends Command
{
    protected $signature = 'programs:export-for-admission
        {--uni= : Üni slug (boşsa tüm üniler)}
        {--id= : Üni ID (alternatif)}
        {--degree= : Sadece bachelor/master/phd vs.}
        {--only-empty : Sadece admission_mode\'u boş olanları}
        {--output= : Çıktı CSV yolu (varsayılan: data/admission-{uni}.csv)}';

    protected $description = 'Bir üninin programlarını CSV olarak çıkar (admission_mode kolonu boş, manuel doldurmak için)';

    public function handle(): int
    {
        $uniSlug = $this->option('uni');
        $uniId = $this->option('id');
        $degree = $this->option('degree');
        $onlyEmpty = (bool) $this->option('only-empty');

        $q = University::query();
        if ($uniSlug) $q->where('slug', $uniSlug);
        if ($uniId) $q->where('id', (int) $uniId);

        if (! $uniSlug && ! $uniId) {
            $this->error('--uni veya --id ver');
            return self::FAILURE;
        }

        $uni = $q->first();
        if (! $uni) {
            $this->error('Üni bulunamadı');
            return self::FAILURE;
        }

        $progQ = $uni->programs()->where('is_active', true)->orderBy('name_de');
        if ($degree) $progQ->where('degree', $degree);
        if ($onlyEmpty) $progQ->whereNull('admission_mode');

        $programs = $progQ->get(['id', 'slug', 'name_de', 'name_en', 'degree', 'language', 'admission_mode']);

        if ($programs->isEmpty()) {
            $this->warn('Hiç program yok');
            return self::SUCCESS;
        }

        $output = $this->option('output') ?: 'data/admission-' . $uni->slug . '.csv';
        $path = base_path($output);

        $fh = fopen($path, 'w');
        fputcsv($fh, [
            'program_slug',
            'admission_mode',
            'admission_summary',
            'nc_value',
            'program_name_de',
            'program_name_en',
            'degree',
            'language',
            'currently',
        ]);

        foreach ($programs as $p) {
            fputcsv($fh, [
                $p->slug,
                '',
                '',
                '',
                $p->name_de,
                $p->name_en ?? '',
                $p->degree,
                $p->language ?? '',
                $p->admission_mode ?? '',
            ]);
        }
        fclose($fh);

        $this->info("✅ CSV: {$output} ({$programs->count()} program)");
        $this->newLine();
        $this->line("Üni: {$uni->name_de}");
        $this->newLine();
        $this->line("📝 Doldurma kuralları:");
        $this->line("  admission_mode kolonu için 4 değer:");
        $this->line("    zulassungsfrei  → NC yok, herkes alınır");
        $this->line("    oertlich        → Üni'nin kendi NC kotası (yerel)");
        $this->line("    bundesweit      → Hochschulstart üzerinden ulusal");
        $this->line("    auswahl         → Eignungsprüfung/portfolio/test");
        $this->line("  (boş bırakırsan değişiklik yok)");
        $this->newLine();
        $this->line("  admission_summary (opsiyonel): kısa not (örn: 'Kış NC, Yaz açık')");
        $this->line("  nc_value (opsiyonel): ortalama NC değeri (örn: 2.3)");
        $this->newLine();
        $this->line("Doldurduktan sonra:");
        $this->line("  php artisan programs:import-admission {$output} --dry-run");
        $this->line("  php artisan programs:import-admission {$output}");

        return self::SUCCESS;
    }
}
