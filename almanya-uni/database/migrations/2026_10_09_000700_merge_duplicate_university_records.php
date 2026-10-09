<?php

use App\Models\Favorite;
use App\Models\Program;
use App\Models\University;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Aynı kurumun ikinci (partner/DAAD "kabuk") kaydını kanonik (HRK/Wikidata) kayda birleştirir — 14 çift.
 *
 * Sorun: kabuk kayıtların çoğu PASİF (listede yok) ama altında AKTİF programlar duruyordu (ör. "Hochschule Fresenius -
 * University of Applied Sciences" 26, "Anhalt University of Applied Sciences" 14, "Paderborn University" 13); Constructor,
 * DIU ve Jade'de ise iki kayıt birden aktifti. Kurum sayfası programları bölünmüş gösteriyordu.
 *
 * Her çift (slug ile, ikisi de tam 1 kayıt olmalı):
 *  - Kabuğun tüm programları kanonik kayda bağlanır. Aktif program kanonik kayıtta zaten aktifse (aynı derece, ad_de/ad_en
 *    çapraz birebir) kabuk tarafı pasifleşir; boş zengin alanlar korunan kayda aktarılır, favoriler taşınır.
 *  - İş ilanları, üniversite favorileri ve yorumları kanonik kayda geçer; kanonikte boş olan name_en/website/logo/açıklama
 *    kabuktan doldurulur (name_en Almanca adın kopyasıysa kabuğun adı İngilizce ad olur); kabuk pasif kalır (silinmez).
 *  - Eski kabuk URL'si UniversityWebController::MERGED_SLUGS ile kanonik sayfaya 301 yönlenir.
 * Idempotent: ikinci çalıştırmada kabukta taşınacak bir şey kalmaz.
 */
return new class extends Migration
{
    /** kabuk slug => kanonik slug */
    private const PAIRS = [
        'hochschule-fresenius-university-of-applied-sciences-partner-019de9f1' => 'hochschule-fresenius-partner-019ddbba',
        'anhalt-university-of-applied-sciences-partner-019de9f1' => 'hochschule-anhalt-q1622074',
        'paderborn-university-partner-019de9ee' => 'universitat-paderborn-q679134',
        'university-of-applied-sciences-emdenleer-partner-019de9f1' => 'hochschule-emdenleer-q1622096',
        'rheinmain-university-of-applied-sciences-partner-019de9f1' => 'hochschule-rheinmain-q542415',
        'ifs-internationale-filmschule-koln-partner-019de9f2' => 'ifs-internationale-filmschule-partner-019ddbba',
        'accadis-hochschule-bad-homburg-university-of-applied-sciences-partner-019de9f1' => 'accadis-hochschule-bad-homburg-partner-019ddbbb',
        'university-of-applied-sciences-ravensburg-weingarten-partner-019de9f1' => 'hochschule-ravensburg-weingarten-q2718933',
        'reutlingen-university-partner-019de9f1' => 'hochschule-reutlingen-q317125',
        'fachhochschule-wedel-university-of-applied-sciences-partner-019de9f2' => 'fachhochschule-wedel-partner-019ddbba',
        'furtwangen-university-partner-019de9f1' => 'hochschule-furtwangen-partner-019ddbba',
        'constructor-university' => 'constructor-university-bremen-partner-019ddbba',
        'dresden-international-university' => 'diu-dresden-international-university-gmbh-partner-019ddbba',
        'jade-university-of-applied-sciences-wilhelmshavenoldenburgelsfleth-partner-019de9f1' => 'jade-hochschule-partner-019ddbba',
    ];

    /** programs:dedupe ile aynı alanlar. */
    private const RICH_FIELDS = [
        'application_deadline_summer', 'application_deadline_winter', 'application_fee_eur',
        'tuition_fee_eur', 'cost_per_semester_eur', 'nc_value', 'admission_mode', 'admission_summary',
        'qualification_requirements_tr', 'language_requirements_tr', 'required_documents_tr',
        'description_tr', 'description_en', 'duration_semesters', 'study_form', 'source_url', 'image_url',
        'field_of_study_id',
    ];

    private const UNIVERSITY_FILL = ['name_en', 'website_url', 'logo_url', 'image_url', 'description_en'];

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

                if (Schema::hasTable('job_postings')) {
                    DB::table('job_postings')->where('university_id', $shell->id)->update(['university_id' => $canonical->id]);
                }
                if (Schema::hasTable('university_reviews')) {
                    DB::table('university_reviews')->where('university_id', $shell->id)->update(['university_id' => $canonical->id]);
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
                // Kanonik name_en yalnız Almanca adın kopyasıysa kabuğun adı resmî İngilizce addır ("Paderborn University").
                if ($canonical->name_en === $canonical->name_de && $shell->name_de !== $canonical->name_de) {
                    $fill['name_en'] = $shell->name_de;
                }
                if ($fill) {
                    DB::table('universities')->where('id', $canonical->id)->update($fill + ['updated_at' => now()]);
                }
                if ($shell->is_active) {
                    DB::table('universities')->where('id', $shell->id)->update(['is_active' => false, 'updated_at' => now()]);
                }
            });
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
        // Veri birleştirmesi; geri alma elle (kabuk kayıtlar silinmedi, PAIRS listesinde).
    }
};
