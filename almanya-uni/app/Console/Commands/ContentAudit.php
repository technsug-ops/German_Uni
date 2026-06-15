<?php

namespace App\Console\Commands;

use App\Models\Program;
use App\Models\University;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * İçerik bütünlük denetimi — üni/şehir/program verisindeki OBJEKTİF hataları,
 * şüpheli kayıtları ve içerik boşluklarını raporlar. Read-only (hiçbir şey yazmaz).
 *
 * Başvuru takip sistemi program deadline'larına bağlı olduğundan bu denetim
 * "hiçbir yanlışa mahal verilmemeli" disiplininin kalıcı kapısıdır. CI/pre-deploy'da
 * çalıştırılabilir: ERROR sınıfı doluysa exit 1 döner.
 *
 *   php artisan content:audit                  → özet rapor
 *   php artisan content:audit --samples=10     → her sınıf için örnek ID/slug
 *   php artisan content:audit --only=deadlines → tek grup
 */
class ContentAudit extends Command
{
    protected $signature = 'content:audit
        {--samples=5 : Her bulgu sınıfı için gösterilecek örnek kayıt sayısı}
        {--only= : Sadece bir grup: deadlines|values|integrity|classification|completeness}';

    protected $description = 'İçerik denetimi: program/üni veri hatalarını, boşluklarını ve tutarsızlıklarını raporlar (read-only).';

    private const LANGUAGES     = ['de', 'en', 'both', 'other'];
    private const DEGREES       = ['bachelor', 'master', 'phd', 'sprachkurs', 'studienkolleg', 'staatsexamen', 'diplom', 'other', 'unknown'];
    private const ADMISSION     = ['zulassungsfrei', 'oertlich', 'bundesweit', 'auswahl'];

    private int $samples;
    private int $errorCount = 0;

    public function handle(): int
    {
        $this->samples = max(0, (int) $this->option('samples'));
        $only = $this->option('only');

        $today = now()->toDateString();
        $this->line(str_repeat('═', 72));
        $this->info('  İÇERİK DENETİMİ — ' . now()->format('Y-m-d H:i'));
        $this->line(str_repeat('═', 72));
        $this->line('Aktif program: ' . Program::where('is_active', 1)->count()
            . '  ·  Aktif üni: ' . University::where('is_active', 1)->count());

        if (! $only || $only === 'deadlines')      $this->groupDeadlines($today);
        if (! $only || $only === 'values')         $this->groupValues();
        if (! $only || $only === 'integrity')      $this->groupIntegrity();
        if (! $only || $only === 'classification') $this->groupClassification();
        if (! $only || $only === 'completeness')   $this->groupCompleteness();

        $this->newLine();
        $this->line(str_repeat('═', 72));
        if ($this->errorCount > 0) {
            $this->error("  ✖ {$this->errorCount} OBJEKTİF HATA sınıfı dolu — düzeltme gerekiyor.");
            return self::FAILURE;
        }
        $this->info('  ✓ Objektif hata yok.');
        return self::SUCCESS;
    }

    // ───────────────────────── Gruplar ─────────────────────────

    private function groupDeadlines(string $today): void
    {
        $this->section('DEADLINE (başvuru takibinin kalbi)');
        $ap = fn () => Program::where('is_active', 1);

        $this->error_(
            'Geçmiş deadline (aktif) — takvimde yanlış "süresi doldu"',
            (clone $ap())->where(function ($q) use ($today) {
                $q->whereDate('application_deadline_summer', '<', $today)
                  ->orWhereDate('application_deadline_winter', '<', $today);
            })->get(['id', 'slug', 'application_deadline_summer', 'application_deadline_winter'])
        );

        $this->warn_(
            'Her iki deadline da null (aktif) — deadline takviminde görünmez',
            (clone $ap())->whereNull('application_deadline_summer')->whereNull('application_deadline_winter')
                ->get(['id', 'slug'])
        );

        $this->warn_(
            'Yaz == kış deadline (şüpheli parse/partner artefaktı)',
            (clone $ap())->whereColumn('application_deadline_summer', 'application_deadline_winter')
                ->get(['id', 'slug', 'application_deadline_summer'])
        );
    }

    private function groupValues(): void
    {
        $this->section('DEĞER MANTIĞI');
        $ap = fn () => Program::where('is_active', 1);

        $this->error_(
            'Geçersiz süre (duration_semesters ≤0 veya >20)',
            (clone $ap())->whereNotNull('duration_semesters')
                ->where(fn ($q) => $q->where('duration_semesters', '<=', 0)->orWhere('duration_semesters', '>', 20))
                ->get(['id', 'slug', 'duration_semesters'])
        );

        $this->error_(
            'Absürt başvuru harcı (>€500; Almanya tipik €0–75)',
            (clone $ap())->where('application_fee_eur', '>', 500)
                ->get(['id', 'slug', 'application_fee_eur'])
        );

        $this->error_(
            'Absürt/negatif öğrenim ücreti (<0 veya >€50.000)',
            (clone $ap())->where(fn ($q) => $q->where('tuition_fee_eur', '<', 0)->orWhere('tuition_fee_eur', '>', 50000))
                ->get(['id', 'slug', 'tuition_fee_eur'])
        );

        $this->error_(
            'NC değeri aralık dışı (1.0–4.0 dışı)',
            (clone $ap())->whereNotNull('nc_value')
                ->where(fn ($q) => $q->where('nc_value', '<', 1.0)->orWhere('nc_value', '>', 4.0))
                ->get(['id', 'slug', 'nc_value'])
        );

        $this->error_(
            'Geçersiz language değeri (de/en/both/other dışı)',
            (clone $ap())->whereNotNull('language')->where('language', '!=', '')
                ->whereNotIn('language', self::LANGUAGES)
                ->get(['id', 'slug', 'language'])
        );

        $this->error_(
            'Geçersiz admission_mode değeri',
            (clone $ap())->whereNotNull('admission_mode')->where('admission_mode', '!=', '')
                ->whereNotIn('admission_mode', self::ADMISSION)
                ->get(['id', 'slug', 'admission_mode'])
        );
    }

    private function groupIntegrity(): void
    {
        $this->section('YAPISAL BÜTÜNLÜK');

        $orphans = DB::table('programs')->where('programs.is_active', 1)
            ->leftJoin('universities', 'universities.id', '=', 'programs.university_id')
            ->whereNull('universities.id')
            ->select('programs.id', 'programs.slug')->get();
        $this->error_('Sahipsiz program (üni yok)', $orphans);

        $this->error_(
            'Boş slug (aktif program)',
            Program::where('is_active', 1)->where(fn ($q) => $q->whereNull('slug')->orWhere('slug', ''))->get(['id'])
        );

        $inactiveUni = DB::table('programs')->where('programs.is_active', 1)
            ->join('universities', 'universities.id', '=', 'programs.university_id')
            ->where('universities.is_active', 0)
            ->select('programs.id', 'programs.slug')->get();
        $this->error_('Pasif üni üzerinde aktif program', $inactiveUni);

        $dupes = DB::table('programs')->where('is_active', 1)
            ->select('university_id', 'name_de', 'degree', DB::raw('count(*) c'), DB::raw('min(id) sample_id'))
            ->groupBy('university_id', 'name_de', 'degree')->having('c', '>', 1)->get();
        $this->error_('Dupe program (aynı üni+ad+derece)', $dupes->map(fn ($r) => (object) ['id' => $r->sample_id, 'slug' => $r->name_de . " (×{$r->c})"]));

        $this->error_(
            'Üni şehirsiz (city_id null, aktif)',
            University::where('is_active', 1)->whereNull('city_id')->get(['id', 'slug'])
        );
    }

    private function groupClassification(): void
    {
        $this->section('SINIFLANDIRMA');
        $ap = fn () => Program::where('is_active', 1);

        $this->warn_(
            'field_of_study_id NULL — alan sayfası/filtrede görünmez',
            (clone $ap())->whereNull('field_of_study_id')->get(['id', 'slug'])
        );

        $this->warn_(
            'Belirsiz degree (other/unknown)',
            (clone $ap())->whereIn('degree', ['other', 'unknown'])->get(['id', 'slug', 'degree'])
        );

        $this->warn_(
            'Bilinmeyen degree değeri (enum dışı)',
            (clone $ap())->whereNotNull('degree')->where('degree', '!=', '')
                ->whereNotIn('degree', self::DEGREES)->get(['id', 'slug', 'degree'])
        );
    }

    private function groupCompleteness(): void
    {
        $this->section('İÇERİK BOŞLUĞU (eksik — yanlış değil)');
        $ap = fn () => Program::where('is_active', 1);
        $empty = fn ($col) => (clone $ap())->where(fn ($q) => $q->whereNull($col)->orWhere($col, ''))->count();

        $this->info_('Boş qualification_requirements_tr', $empty('qualification_requirements_tr'));
        $this->info_('Boş language_requirements_tr',      $empty('language_requirements_tr'));
        $this->info_('Boş required_documents_tr',         $empty('required_documents_tr'));
        $this->info_('Boş description_tr',                $empty('description_tr'));
        $this->info_('nc_value yok',                      (clone $ap())->whereNull('nc_value')->count());
        $this->info_('admission_mode yok',                (clone $ap())->where(fn ($q) => $q->whereNull('admission_mode')->orWhere('admission_mode', ''))->count());

        // Dil ↔ gereklilik çelişkisi (dolu olanlar arasında)
        $mismatch = (clone $ap())->where('language', 'en')
            ->where(fn ($q) => $q->where('language_requirements_tr', 'like', '%DSH%')->orWhere('language_requirements_tr', 'like', '%TestDaF%'))
            ->get(['id', 'slug']);
        $this->warn_('lang=en ama gereklilikte DSH/TestDaF (çelişki)', $mismatch);
    }

    // ───────────────────────── Yardımcılar ─────────────────────────

    private function section(string $title): void
    {
        $this->newLine();
        $this->line('── ' . $title . ' ' . str_repeat('─', max(0, 66 - mb_strlen($title))));
    }

    private function error_(string $label, $rows): void
    {
        $n = is_int($rows) ? $rows : $rows->count();
        if ($n > 0) $this->errorCount++;
        $icon = $n > 0 ? '✖' : '✓';
        $this->line(sprintf('  %s  %-58s %6d', $icon, $label, $n));
        $this->printSamples($rows);
    }

    private function warn_(string $label, $rows): void
    {
        $n = is_int($rows) ? $rows : $rows->count();
        $icon = $n > 0 ? '▲' : '✓';
        $this->line(sprintf('  %s  %-58s %6d', $icon, $label, $n));
        $this->printSamples($rows);
    }

    private function info_(string $label, int $n): void
    {
        $this->line(sprintf('  ·  %-58s %6d', $label, $n));
    }

    private function printSamples($rows): void
    {
        if (is_int($rows) || $this->samples === 0 || $rows->isEmpty()) return;
        foreach ($rows->take($this->samples) as $r) {
            $arr = $r instanceof \Illuminate\Database\Eloquent\Model ? $r->getAttributes() : (array) $r;
            $extra = collect($arr)->except(['id', 'slug'])
                ->filter(fn ($v) => $v !== null && $v !== '' && ! is_array($v))
                ->map(fn ($v, $k) => "$k=$v")->implode(', ');
            $this->line(sprintf('        #%-7s %s %s', $r->id ?? '?', $r->slug ?? '', $extra ? "[$extra]" : ''));
        }
    }
}
