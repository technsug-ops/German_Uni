<?php

use App\Models\Favorite;
use App\Models\Program;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Hochschulkompass (HK) programlarının yanlış üniversiteye bağlanmasını düzeltir — 1.029 program
 * (veri: database/data/program-fixes/hk-university-misattribution-2026-10-09.json).
 *
 * Kök neden: programs:export-hk normUni() kurum türü kelimelerini ve virgülden sonrasını silip ad→slug haritasında
 * SONUNCUYU tutuyordu; "Universität Hamburg" ve "Technische Universität Hamburg" #834'e, "Universität Münster" FH Münster'e,
 * "Technische Universität Dresden" FH Dresden'e, "Hochschule … Offenburg" HTWK Leipzig'e ("fuertechnik") düştü.
 * Gerçek kurum hk_catalog.hochschule'den alındı (program slug'ı buildSlug ile yeniden hesaplanarak satıra bağlandı);
 * canlı dump'ta aynı atamalar doğrulandı. Aynı kurumun çift kayıtları arasındaki kaymalar (Hochschule Bochum) kapsam dışı.
 *
 * Ayrıca #834 "Hamburg University of Applied Sciences" kaydı aslında MSH Medical School Hamburg (HRK 421,
 * medicalschool-hamburg.de, Am Kaiserkai 1) → adı düzeltilir; altındaki partner/DAAD kayıtları HAW Hamburg'a gider.
 *
 * Program kuralları 2026_10_09_000500/000600 ile aynı (slug + kaynak + ad + derece + şu anki üniversite birebir; hedefte
 * aktif kopya varsa pasifleştir + zengin alan/favori aktar, yoksa taşı). Idempotent.
 */
return new class extends Migration
{
    private const FILE = 'database/data/program-fixes/hk-university-misattribution-2026-10-09.json';

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

        // Programlar ÖNCE: "from" eşleşmesi yeniden adlandırmadan önceki adla yapılır.
        $unis = [];
        foreach ($data['programs'] as $row) {
            $from = $unis[$row['from']] ??= $this->university($row['from']);
            $to = $unis[$row['to']] ??= $this->university($row['to']);
            if (! $from || ! $to) {
                continue;
            }

            $hits = Program::where('slug', $row['key'])->where('source', $row['source'])->get();
            $p = $hits->count() === 1 ? $hits->first() : null;
            if (! $p || (int) $p->university_id !== $from || $p->name_de !== $row['name_de'] || $p->degree !== $row['degree']) {
                continue;
            }

            DB::transaction(fn () => $this->reattach($p, $to));
        }

        foreach ($data['rename_university'] as $r) {
            DB::table('universities')->where('slug', $r['slug'])->where('name_de', $r['wrong_name_de'])
                ->update(['name_de' => $r['name_de'], 'name_en' => $r['name_en'], 'short_name' => $r['short_name'], 'updated_at' => now()]);
        }
    }

    private function reattach(Program $p, int $to): void
    {
        $keep = $p->is_active ? $this->duplicateAt($p, $to) : null;
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
        // Veri düzeltmesi; önceki üniversiteler JSON'daki `from` alanında.
    }
};
