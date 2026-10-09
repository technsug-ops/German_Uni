<?php

use App\Models\Program;
use App\Models\ProgramVerification;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Pilot doğrulamasında son başvuru tarihi resmî kaynakla çelişen/şüpheli 5 programın tarihlerini düzeltir
 * (kaynaklar 2026-10-09'da yeniden okundu) ve o programların tarih doğrulama kaydını yeniler.
 *
 * İlke: yıl kaynakta yazmıyorsa yıl TAHMİN edilmez — yanlış tarih silinir (NULL), tarih yıllık kural ise
 * kayıt NEEDS_REVIEW kalır. Tek alan tek tarih tuttuğu için AB dışı (sitenin kitlesi) son tarih seçilir.
 * Kimlik çelişkili pilot programlarına (chemistry, geodetic, Willy Brandt) dokunulmaz: önce kimlik düzelmeli.
 *
 * Eşleştirme pilot import ile aynı: kaynak + dış kimlik tam 1 kayıt + name_de/derece/üniversite birebir;
 * eşleşmeyen atlanır. Idempotent.
 */
return new class extends Migration
{
    private const CHECKED_AT = '2026-10-09';

    public function up(): void
    {
        if (! Schema::hasTable('programs') || ! Schema::hasTable('program_verifications')) {
            return;
        }

        foreach ($this->fixes() as $fix) {
            $p = $this->resolve($fix['match']);
            if (! $p) {
                continue;
            }

            DB::transaction(function () use ($p, $fix) {
                $p->forceFill($fix['dates']);
                if ($p->isDirty()) {
                    $p->save();
                }

                ProgramVerification::where('program_id', $p->id)->where('field', 'deadline')->delete();
                $v = ProgramVerification::firstOrNew([
                    'program_id' => $p->id, 'field' => 'deadline',
                    'applicant_group' => $fix['verification']['applicant_group'], 'term' => $fix['verification']['term'],
                ]);
                $v->fill(array_merge($fix['verification'], ['checked_at' => self::CHECKED_AT, 'verified_via' => 'manual-recheck']))->save();
            });
        }
    }

    private function resolve(array $m): ?Program
    {
        $hits = Program::with('university:id,name_de')->where('source', $m['source'])->where($m['external_id_field'], $m['external_id'])->get();
        if ($hits->count() !== 1) {
            return null;
        }
        $p = $hits->first();

        return ($p->name_de === $m['name_de'] && $p->degree === $m['degree'] && $p->university?->name_de === $m['university']) ? $p : null;
    }

    private function fixes(): array
    {
        return [
            // Kış 1 Ara–30 Nis yıllık kural (mevcut 2027-04-30 gün/ay doğru); yaz için başvuru dönemi yok → yaz tarihi 30 Nisan'ın kopyasıydı.
            [
                'match' => ['source' => 'partner', 'external_id_field' => 'partner_id', 'external_id' => '019de9f2-2280-7095-9c7b-01a881016409', 'name_de' => 'Renewable Energy Systems', 'degree' => 'master', 'university' => 'Nordhausen University of Applied Sciences'],
                'dates' => ['application_deadline_summer' => null],
                'verification' => [
                    'status' => ProgramVerification::NEEDS_REVIEW, 'applicant_group' => '', 'term' => '',
                    'source_value' => 'winter semester (no year stated) | all | 1 December - 30 April; summer semester | all | no application period listed',
                    'source_url' => 'https://www.hs-nordhausen.de/en/study-programmes/renewable-energy-systems/',
                    'evidence' => 'Application period start of the winter semester: 1 December - 30 April; no summer semester application period on the page',
                    'review_reason' => 'Kış son tarihi yıllık kural (yıl yok); yaz tarihi kaldırıldı (kaynakta yaz başvuru dönemi yok)',
                ],
            ],
            // Mevcut 2027-04-15 başvurunun AÇILIŞ tarihiydi; AB dışı son tarih 15 Temmuz (yıllık kural, yaz başlangıcı yok).
            [
                'match' => ['source' => 'daad', 'external_id_field' => 'source_id', 'external_id' => '8312', 'name_de' => 'English and American Studies', 'degree' => 'bachelor', 'university' => 'Universität Bayreuth'],
                'dates' => ['application_deadline_winter' => '2027-07-15', 'application_deadline_summer' => null],
                'verification' => [
                    'status' => ProgramVerification::NEEDS_REVIEW, 'applicant_group' => 'non_eu', 'term' => '',
                    'source_value' => 'winter semester (no year stated) | non-EU | 15. April – 15. Juli; summer semester | start not possible',
                    'source_url' => 'https://www.international-office.uni-bayreuth.de/de/studiengangsseiten/bachelor/anglistik-amerikanistik-ba/index.html',
                    'evidence' => 'Wintersemester: 15. April – 15. Juli · Sommersemester: Studienbeginn zum Sommersemester nicht möglich',
                    'review_reason' => 'Son tarih yıllık kural (yıl yok): 15 Temmuz; WS 2027/28 için resmî duyuru çıkınca doğrulanmalı',
                ],
            ],
            // Önceki: kış ve yaz 2027-03-15. Resmî "aktuelle Bewerbungstermine": SS 2027 AB dışı 15.01.2027 (AB 15.03.2027);
            // WS 2027/28 henüz yayınlanmadı.
            [
                'match' => ['source' => 'daad', 'external_id_field' => 'source_id', 'external_id' => '7708', 'name_de' => 'MSc Natural Language Processing', 'degree' => 'master', 'university' => 'Universität Trier'],
                'dates' => ['application_deadline_winter' => null, 'application_deadline_summer' => '2027-01-15'],
                'verification' => [
                    'status' => ProgramVerification::VERIFIED, 'applicant_group' => 'non_eu', 'term' => 'SS 2027',
                    'source_value' => 'SS 2027 | non-EU | 2027-01-15; SS 2027 | German/EU | 2027-03-15; WS 2027/28 | not yet published',
                    'source_url' => 'https://www.uni-trier.de/index.php?L=2&id=62798',
                    'evidence' => 'Summer Semester 2027 — Non-EU applicants: Dec 15th 2026 - Jan 15th 2027; German and EU applicants: Dec 15th 2026 - Mar 15th 2027',
                    'review_reason' => null,
                ],
            ],
            // Mevcut 2027-05-02 hiçbir kaynakta yok; WS 2026/27 14.06.2026'da bitti, WS 2027/28 henüz yayınlanmadı.
            [
                'match' => ['source' => 'daad', 'external_id_field' => 'source_id', 'external_id' => '10507', 'name_de' => 'Master of Science in Statistics', 'degree' => 'master', 'university' => 'Humboldt-Universität zu Berlin'],
                'dates' => ['application_deadline_winter' => null],
                'verification' => [
                    'status' => ProgramVerification::NEEDS_REVIEW, 'applicant_group' => '', 'term' => '',
                    'source_value' => 'WS 2026/27 | all | 2026-06-14 (closed); WS 2027/28 | not yet published',
                    'source_url' => 'https://www.stat.de/admission',
                    'evidence' => 'The application phase for Winter Semester 2026/27 starts on 18 May 2026 and ends on 14 June 2026.',
                    'review_reason' => 'Yanlış tarih (2027-05-02) kaldırıldı; WS 2027/28 tarihi yayınlanınca girilmeli',
                ],
            ],
            // Fall 2027 rolling admissions (vizeli/vizesiz) 31.07.2027'de kapanır (pilotta 15.07 idi; sayfa güncellenmiş).
            [
                'match' => ['source' => 'daad', 'external_id_field' => 'source_id', 'external_id' => '3663', 'name_de' => 'BSc in Computer Science', 'degree' => 'bachelor', 'university' => 'Constructor University'],
                'dates' => ['application_deadline_winter' => '2027-07-31'],
                'verification' => [
                    'status' => ProgramVerification::VERIFIED, 'applicant_group' => '', 'term' => 'Fall 2027',
                    'source_value' => 'Fall 2027 | Early Action | 2027-02-01; Fall 2027 | Rolling Admissions (visa and no visa) | 2027-07-31',
                    'source_url' => 'https://constructor.university/admission-aid/application-information-undergraduate',
                    'evidence' => 'Early Action: October 1, 2026 – February 1, 2027 · Rolling Admissions (Visa and No Visa): February 2, 2027 – July 31, 2027',
                    'review_reason' => null,
                ],
            ],
        ];
    }

    public function down(): void
    {
        // Veri düzeltmesi; geri alma elle (önceki değerler migration yorumlarında).
    }
};
