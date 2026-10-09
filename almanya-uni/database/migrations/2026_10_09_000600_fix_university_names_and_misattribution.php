<?php

use App\Models\Favorite;
use App\Models\Program;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * İkinci tur üniversite düzeltmesi (veri: database/data/program-fixes/university-names-and-misattribution-2026-10-09.json).
 *
 * 1) Bozuk İngilizce üniversite adları: Wikidata'dan yanlış çekilmiş name_en'ler ("Funeral doom", "Peter Kurz",
 *    ISS Hamburg = "Technical University of Munich", HMU Potsdam = "University of Potsdam" …) sayfaların JSON-LD
 *    alternateName'inde Google'a gidiyor ve import eşleştirmesini bozuyordu → NULL (sayfa Almanca adı kullanır).
 *    Universität Potsdam'ın name_en'i "Universität Potsdam" → "University of Potsdam". Yalnız değer hâlâ beklenen bozuk
 *    değerse yazılır.
 * 2) HMU Potsdam'ın bozuk name_en'i yüzünden daad:import "University of Potsdam" programlarını (37) HMU'ya bağlamıştı;
 *    partner kaydında da HAW Kiel ← "Kiel University" (14), Neubrandenburg ← "Brandenburg UAS" (1), HNU Neu-Ulm ←
 *    "Ulm UAS" (1). Kurum adı DAAD API `academy` alanından (partner source_url = DAAD detay sayfası).
 *
 * Program kuralları 2026_10_09_000500 ile aynı: kaynak + dış kimlik + ad + derece + şu anki üniversite birebir eşleşmeli;
 * hedefte aynı program aktifse kayıt hedefe bağlanıp pasifleştirilir (boş zengin alanlar korunan kayda aktarılır,
 * favoriler taşınır), yoksa taşınır. Idempotent.
 */
return new class extends Migration
{
    private const FILE = 'database/data/program-fixes/university-names-and-misattribution-2026-10-09.json';

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
        if (! Schema::hasTable('programs') || ! Schema::hasTable('universities') || ! is_file(base_path(self::FILE))) {
            return;
        }
        $data = json_decode(file_get_contents(base_path(self::FILE)), true);

        foreach ($data['university_name_en'] as $n) {
            if ($this->university($n['name_de'])) {
                DB::table('universities')->where('name_de', $n['name_de'])->where('name_en', $n['wrong_name_en'])
                    ->update(['name_en' => $n['name_en'], 'updated_at' => now()]);
            }
        }

        $unis = [];
        foreach ($data['programs'] as $row) {
            $from = $unis[$row['from']] ??= $this->university($row['from']);
            $to = $unis[$row['to']] ??= $this->university($row['to']);
            if (! $from || ! $to) {
                continue;
            }

            $hits = Program::where('source', $row['source'])->where($row['external_id_field'], $row['external_id'])->where('is_active', true)->get();
            $p = $hits->count() === 1 ? $hits->first() : null;
            if (! $p || (int) $p->university_id !== $from || $p->name_de !== $row['name_de'] || $p->degree !== $row['degree']) {
                continue;
            }

            DB::transaction(fn () => $this->reattach($p, $to));
        }
    }

    private function reattach(Program $p, int $to): void
    {
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
    }

    /** Hedef üniversitede aynı programın aktif kaydı (aynı derece; ad_de/ad_en çapraz birebir). */
    private function duplicateAt(Program $p, int $universityId): ?Program
    {
        $names = array_values(array_filter([$p->name_de, $p->name_en]));

        return Program::where('university_id', $universityId)->where('degree', $p->degree)->where('is_active', true)
            ->whereKeyNot($p->id)
            ->where(fn ($q) => $q->whereIn('name_de', $names)->orWhereIn('name_en', $names))
            ->orderByRaw("source IN ('daad', 'partner')")->orderBy('id')
            ->first();
    }

    private function university(string $name): ?int
    {
        $ids = DB::table('universities')->where('name_de', $name)->pluck('id');

        return $ids->count() === 1 ? (int) $ids->first() : null;
    }

    public function down(): void
    {
        // Veri düzeltmesi; önceki değerler JSON'da (wrong_name_en, from).
    }
};
