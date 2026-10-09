<?php

use App\Models\Favorite;
use App\Models\Program;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * DAAD programlarının yanlış üniversiteye bağlanmasını düzeltir (114 kayıt).
 *
 * Kök neden: daad:import fuzzyMatch() adlardan "universität/hochschule/technische/university…" kelimelerini silip
 * benzerlik ölçüyordu; "Ulm University" hem Universität Ulm'a hem TH Ulm'a %100 benzediği için id sırasında İLK gelen
 * (çoğunlukla HAW/TH) seçildi. Örn. University of Hamburg → TU Hamburg (28), University of Bremen → Hochschule Bremen (25).
 *
 * Veri: database/data/program-fixes/daad-university-misattribution-2026-10-09.json — DAAD API'nin `academy` alanıyla
 * birebir karşılaştırıldı (2026-10-09), canlı dump'ta aynı atamalar doğrulandı.
 *
 * Her kayıt için (kaynak + source_id + ad + derece + şu anki üniversite birebir eşleşmeli, yoksa atlanır):
 *  - Doğru üniversitede aynı program zaten aktifse (aynı derece, ad_de/ad_en çapraz eşit) → DAAD kaydı doğru üniversiteye
 *    bağlanıp pasifleştirilir; korunan kayıtta BOŞ olan zengin alanlar DAAD kaydından doldurulur, 'both' dil genişliği
 *    korunur, favoriler taşınır (programs:dedupe deseni).
 *  - Yoksa → kayıt doğru üniversiteye taşınır.
 * Idempotent: düzeltilen kayıt bir sonraki çalıştırmada "şu anki üniversite" koşulunu sağlamaz.
 */
return new class extends Migration
{
    private const FILE = 'database/data/program-fixes/daad-university-misattribution-2026-10-09.json';

    /** programs:dedupe ile aynı alanlar. */
    private const RICH_FIELDS = [
        'application_deadline_summer', 'application_deadline_winter', 'application_fee_eur',
        'tuition_fee_eur', 'cost_per_semester_eur', 'nc_value', 'admission_mode', 'admission_summary',
        'qualification_requirements_tr', 'language_requirements_tr', 'required_documents_tr',
        'description_tr', 'description_en', 'duration_semesters', 'study_form', 'source_url', 'image_url',
        'field_of_study_id',
    ];

    public function up(): void
    {
        if (! Schema::hasTable('programs') || ! is_file(base_path(self::FILE))) {
            return;
        }

        $unis = [];
        foreach (json_decode(file_get_contents(base_path(self::FILE)), true)['programs'] as $row) {
            $from = $unis[$row['from']] ??= $this->university($row['from']);
            $to = $unis[$row['to']] ??= $this->university($row['to']);
            if (! $from || ! $to) {
                continue;
            }

            $hits = Program::where('source', 'daad')->where('source_id', $row['source_id'])->where('is_active', true)->get();
            $p = $hits->count() === 1 ? $hits->first() : null;
            if (! $p || (int) $p->university_id !== $from || $p->name_de !== $row['name_de'] || $p->degree !== $row['degree']) {
                continue;
            }

            DB::transaction(function () use ($p, $to) {
                $keep = $this->duplicateAt($p, $to);
                if (! $keep) {
                    $p->forceFill(['university_id' => $to])->save();

                    return;
                }

                $merge = [];
                foreach (self::RICH_FIELDS as $f) {
                    if (blank($keep->getAttribute($f)) && filled($p->getAttribute($f))) {
                        $merge[$f] = $p->getAttribute($f);
                    }
                }
                if ($p->language === 'both' && $keep->language !== 'both') {
                    $merge['language'] = 'both';
                }
                if ($merge) {
                    $keep->forceFill($merge)->save();
                }

                Favorite::where('favoriteable_type', Program::class)->where('favoriteable_id', $p->id)->each(function (Favorite $f) use ($keep) {
                    $exists = Favorite::where('favoriteable_type', Program::class)->where('favoriteable_id', $keep->id)->where('user_id', $f->user_id)->exists();
                    $exists ? $f->delete() : $f->update(['favoriteable_id' => $keep->id]);
                });
                // Pasif sayfa da açılabildiği için kayıt yine de doğru üniversiteye bağlanır.
                $p->forceFill(['is_active' => false, 'university_id' => $to])->save();
            });
        }
    }

    /** Hedef üniversitede aynı programın aktif kaydı (aynı derece; ad_de/ad_en çapraz birebir). */
    private function duplicateAt(Program $p, int $universityId): ?Program
    {
        $names = array_values(array_filter([$p->name_de, $p->name_en]));

        return Program::where('university_id', $universityId)->where('degree', $p->degree)->where('is_active', true)
            ->whereKeyNot($p->id)
            ->where(fn ($q) => $q->whereIn('name_de', $names)->orWhereIn('name_en', $names))
            ->orderByRaw("source = 'daad'")->orderBy('id')
            ->first();
    }

    private function university(string $name): ?int
    {
        $ids = DB::table('universities')->where('name_de', $name)->pluck('id');

        return $ids->count() === 1 ? (int) $ids->first() : null;
    }

    public function down(): void
    {
        // Veri düzeltmesi; önceki üniversiteler JSON'daki `from` alanında.
    }
};
