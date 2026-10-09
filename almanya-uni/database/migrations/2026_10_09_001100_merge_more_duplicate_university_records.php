<?php

use App\Models\Favorite;
use App\Models\Program;
use App\Models\University;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * İkinci tur çift üniversite birleştirmesi (2026_10_09_000700 ile aynı yöntem) — 9 kabuk → 8 kanonik kayıt.
 *
 * Partner/DAAD kabukları resmî (HRK/Wikidata) kaydın yanında AKTİF duruyor ve programları bölüyordu:
 * Hochschule Bochum (resmî HRK kaydında 0 program, programlar iki kabukta), Macromedia, GISMA, BTU Cottbus-Senftenberg,
 * TH Wildau, TU Hamburg, FAU Erlangen-Nürnberg. Freie Universität Berlin'de ağustostaki HU/TU Berlin deseni: asıl veri
 * (HRK 11, öğrenci sayısı, 231 program) "Freie Universität Berlin, E-Medien" alt-birim kaydında; DOĞRU ADLI kayıt
 * (freie-universitat-berlin, QS 98) kanonik kalır ([[duplicate-university-records]] kuralı).
 *
 * Her çift: programlar kanonik kayda (aktif kopyası varsa pasifleşir, zengin alan + favori aktarılır), iş ilanı/yorum/
 * kampüs/favori taşınır, kanonikte boş alanlar kabuktan dolar (HRK no ve Wikidata benzersiz olduğu için kabuktan
 * alınıp aktarılır), kabuk pasif kalır; eski URL UniversityWebController::MERGED_SLUGS ile 301.
 * Hochschule Bochum'un resmî kaydı eski adını taşıyordu → güncel resmî ad. Idempotent.
 */
return new class extends Migration
{
    /** kabuk slug => kanonik slug */
    private const PAIRS = [
        'hochschule-bochum-partner-019ddbba' => 'hochschule-fur-technik-wirtschaft-und-gesundheit-bochum-q1622078',
        'bochum-university-of-applied-sciences-partner-019de9f1' => 'hochschule-fur-technik-wirtschaft-und-gesundheit-bochum-q1622078',
        'fachhochschule-macromedia-q1346417' => 'macromedia-university-of-applied-sciences-hs366',
        'german-international-school-of-management-and-administration-gisma-partner-019ddbba' => 'gisma-university-of-applied-sciences-hs518',
        'brandenburg-university-of-technology-cottbus-senftenberg-partner-019de9f1' => 'brandenburgische-technische-universitat-cottbus-senftenberg-partner-019ddbba',
        'technical-university-of-applied-sciences-wildau-partner-019de9f1' => 'technische-hochschule-wildau-q686171',
        'hamburg-university-of-technology-partner-019de9f1' => 'technische-universitat-hamburg-q1060',
        'fau-erlangen-nurnberg-partner-019de9f1' => 'friedrich-alexander-universitat-erlangen-nurnberg-q40025',
        'freie-universitat-berlin-e-medien-q130548543' => 'freie-universitat-berlin',
    ];

    /** Kanonik kayıtta düzeltilecek alanlar: slug => [beklenen eski name_de, yeni değerler]. */
    private const CANONICAL_UPDATES = [
        'hochschule-fur-technik-wirtschaft-und-gesundheit-bochum-q1622078' => [
            'Hochschule für Technik, Wirtschaft und Gesundheit Bochum',
            ['name_de' => 'Hochschule Bochum', 'name_en' => 'Bochum University of Applied Sciences'],
        ],
    ];

    /** programs:dedupe ile aynı alanlar. */
    private const RICH_FIELDS = [
        'application_deadline_summer', 'application_deadline_winter', 'application_fee_eur',
        'tuition_fee_eur', 'cost_per_semester_eur', 'nc_value', 'admission_mode', 'admission_summary',
        'qualification_requirements_tr', 'language_requirements_tr', 'required_documents_tr',
        'description_tr', 'description_en', 'duration_semesters', 'study_form', 'source_url', 'image_url',
        'field_of_study_id',
    ];

    private const UNIVERSITY_FILL = [
        'name_en', 'website_url', 'logo_url', 'image_url', 'description_en', 'description_tr', 'description_de', 'city_id',
        'latitude', 'longitude', 'phone', 'street', 'postal_code', 'type', 'founded_year', 'student_count', 'is_uni_assist_member',
    ];

    /** Benzersiz kimlik alanları: kabuktan silinip kanoniğe yazılır. */
    private const UNIQUE_FILL = ['hs_nummer', 'wikidata_id'];

    public function up(): void
    {
        if (! Schema::hasTable('universities') || ! Schema::hasTable('programs')) {
            return;
        }

        foreach (self::PAIRS as $shellSlug => $canonicalSlug) {
            $shell = $this->single($shellSlug);
            $canonical = $this->single($canonicalSlug);
            if (! $shell || ! $canonical || $shell->id === $canonical->id) {
                continue;
            }

            DB::transaction(function () use ($shell, $canonical) {
                foreach (Program::where('university_id', $shell->id)->orderBy('id')->get() as $p) {
                    $this->reattach($p, $canonical->id);
                }

                foreach (['job_postings', 'university_reviews', 'university_campuses'] as $table) {
                    if (Schema::hasTable($table) && Schema::hasColumn($table, 'university_id')) {
                        DB::table($table)->where('university_id', $shell->id)->update(['university_id' => $canonical->id]);
                    }
                }
                Favorite::where('favoriteable_type', University::class)->where('favoriteable_id', $shell->id)->each(function (Favorite $f) use ($canonical) {
                    $exists = Favorite::where('favoriteable_type', University::class)->where('favoriteable_id', $canonical->id)->where('user_id', $f->user_id)->exists();
                    $exists ? $f->delete() : $f->update(['favoriteable_id' => $canonical->id]);
                });

                $fill = [];
                foreach (self::UNIVERSITY_FILL as $f) {
                    if (blank($canonical->getAttribute($f)) && filled($shell->getAttribute($f))) {
                        $fill[$f] = $shell->getAttribute($f);
                    }
                }
                $unique = [];
                foreach (self::UNIQUE_FILL as $f) {
                    if (blank($canonical->getAttribute($f)) && filled($shell->getAttribute($f))) {
                        $unique[$f] = $shell->getAttribute($f);
                    }
                }
                DB::table('universities')->where('id', $shell->id)
                    ->update(array_fill_keys(array_keys($unique), null) + ['is_active' => false, 'updated_at' => now()]);
                if ($fill || $unique) {
                    DB::table('universities')->where('id', $canonical->id)->update($fill + $unique + ['updated_at' => now()]);
                }
            });
        }

        foreach (self::CANONICAL_UPDATES as $slug => [$oldName, $values]) {
            DB::table('universities')->where('slug', $slug)->where('name_de', $oldName)->update($values + ['updated_at' => now()]);
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

    private function single(string $slug): ?University
    {
        $hits = University::where('slug', $slug)->get();

        return $hits->count() === 1 ? $hits->first() : null;
    }

    public function down(): void
    {
        // Veri birleştirmesi; kabuk kayıtlar silinmedi (PAIRS).
    }
};
