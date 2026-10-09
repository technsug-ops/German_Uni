<?php

use App\Models\Favorite;
use App\Models\Program;
use App\Models\ProgramVerification;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Pilot doğrulamasında program KİMLİĞİ resmî kaynakla çelişen 5 kaydı düzeltir (kaynaklar 2026-10-09'da yeniden okundu).
 *
 *  - Bozuk Almanca ad (program aynı): Bonn "Geodetic Engineering", RheinMain "Baukulturerbe" → ad düzeltilir, kimlik VERIFIED.
 *  - Yanlış üniversite: DAAD "MSc Chemistry" TH Ulm'a bağlıydı; program Universität Ulm'un (THU'da kimya master'ı yok)
 *    ve Uni Ulm'da başka MSc Chemistry kaydı yok → kayıt Uni Ulm'a taşınır, kimlik VERIFIED.
 *  - Çift kayıt: DAAD "Master of Public Policy" FH Erfurt'a bağlıydı; aynı program Universität Erfurt'ta zaten var (partner,
 *    Willy Brandt School) → DAAD kaydı pasifleştirilir, favoriler korunan kayda taşınır (programs:dedupe deseni).
 *  - Program değil: RWTH "Master's College European Studies (MAC-ES)" MPA European Studies'e hazırlayan dijital kurs → pasif.
 *
 * Eşleştirme pilot import ile aynı (kaynak + dış kimlik + name_de/derece/üniversite birebir); hedef üniversite/korunan
 * kayıt tam ada göre ve tam 1 sonuçla bulunur. Eşleşmeyen atlanır; düzeltilen kayıt bir sonraki çalıştırmada eşleşmez.
 */
return new class extends Migration
{
    private const CHECKED_AT = '2026-10-09';

    public function up(): void
    {
        if (! Schema::hasTable('programs') || ! Schema::hasTable('program_verifications')) {
            return;
        }

        // Bonn: ad bozuk. Kış tarihi 2026-07-15 hiçbir gruba uymuyordu (AB dışı 31.05.2026, AB 31.08.2026; WS 2027/28 yok).
        $this->fix(
            ['source' => 'partner', 'external_id_field' => 'partner_id', 'external_id' => '019ddbbc-c662-727c-95a1-1cc4267083fa', 'name_de' => 'Science, Single-Subject GEODETIC ENGINEERING', 'degree' => 'master', 'university' => 'Rheinische Friedrich-Wilhelms-Universität Bonn'],
            ['name_de' => 'Geodetic Engineering', 'application_deadline_winter' => null],
            ['source_value' => 'Master Geodetic Engineering — M.Sc.', 'source_url' => 'https://www.gug.uni-bonn.de/en/master-ge/apply', 'evidence' => 'Master Geodetic Engineering (M.Sc.)'],
            [
                'status' => ProgramVerification::NEEDS_REVIEW, 'applicant_group' => 'non_eu', 'term' => '',
                'source_value' => 'WS 2026/27 | non-EU | 2026-05-31 (closed); WS 2026/27 | EU | 2026-08-31 (closed); WS 2027/28 | not yet published',
                'source_url' => 'https://www.gug.uni-bonn.de/en/master-ge/apply',
                'evidence' => 'non-EU/EWR citizens from 3 January 2026 till 31 May 2026; EU/EWR citizens from 3 January 2026 till 31 August 2026',
                'review_reason' => 'Yanlış tarih (2026-07-15) kaldırıldı; WS 2027/28 tarihi yayınlanınca girilmeli',
            ],
        );

        // RheinMain: ad bozuk (resmî Almanca ad Baukulturerbe). Dil 'both' idi; resmî sayfa "Course Language: German" →
        // 'de' (pilotta zaten dil çelişkisi olarak işaretliydi), dil kaydı VERIFIED.
        $this->fix(
            ['source' => 'partner', 'external_id_field' => 'partner_id', 'external_id' => '019ddbbd-bb87-7131-a40e-dbdb31358ff1', 'name_de' => 'Science Architectural Heritage Conservation', 'degree' => 'bachelor', 'university' => 'Hochschule RheinMain'],
            ['name_de' => 'Baukulturerbe', 'language' => 'de'],
            ['source_value' => 'Baukulturerbe B.Sc. (EN: Architectural Heritage Conservation) — Hochschule RheinMain, Wiesbaden', 'source_url' => 'https://www.hs-rm.de/en/architecture-and-civil-engineering/degree-programs/bachelors-degree/architectural-heritage-conservation', 'evidence' => 'Baukulturerbe B.Sc. / Architectural Heritage Conservation B.Sc. · Wiesbaden | Campus Kurt-Schumacher-Ring'],
            null,
            ['language' => ['source_value' => 'de', 'source_url' => 'https://www.hs-rm.de/en/architecture-and-civil-engineering/degree-programs/bachelors-degree/architectural-heritage-conservation', 'evidence' => 'Course Language: German']],
        );

        // Ulm: Universität Ulm'a taşı. Yaz tarihi 2027-05-15 (kış son gününün kopyası) yanlıştı; resmî yaz 15 Eki–15 Kas
        // (yıllık kural, yıl yok) → sıradaki yaz son tarihi 2026-11-15, kayıt NEEDS_REVIEW.
        $ulm = $this->university('Universität Ulm');
        if ($ulm) {
            $this->fix(
                ['source' => 'daad', 'external_id_field' => 'source_id', 'external_id' => '6938', 'name_de' => 'MSc Chemistry', 'degree' => 'master', 'university' => 'Technische Hochschule Ulm'],
                ['university_id' => $ulm, 'application_deadline_summer' => '2026-11-15'],
                ['source_value' => 'Chemistry - Master of Science (MSc) — Universität Ulm', 'source_url' => 'https://www.uni-ulm.de/en/study/application-and-enrolment/masters-programmes/chemistry-master/', 'evidence' => 'Chemistry - Master of Science (MSc) · Ulm University'],
                [
                    'status' => ProgramVerification::NEEDS_REVIEW, 'applicant_group' => '', 'term' => '',
                    'source_value' => 'winter semester (no year stated) | all | 1 April - 15 May; summer semester (no year stated) | all | 15 October - 15 November',
                    'source_url' => 'https://www.uni-ulm.de/en/study/application-and-enrolment/masters-programmes/chemistry-master/',
                    'evidence' => 'Winter semester: 1 April - 15 May · Summer semester: 15 October - 15 November (same for all applicants)',
                    'review_reason' => 'Tarihler yıllık kural (yıl yok): kış 15 May, yaz 15 Kas; yanlış yaz tarihi 2027-05-15 → 2026-11-15',
                ],
            );
        }

        // Erfurt: DAAD kaydı Universität Erfurt'taki partner kaydının çifti → pasif, favoriler taşınır.
        $erfurt = $this->university('Universität Erfurt');
        $keep = $erfurt ? $this->single(Program::where('university_id', $erfurt)->where('degree', 'master')->where('is_active', true)
            ->where('name_de', 'Master of Public Policy (MPP) at the Willy Brandt School of Public Policy')) : null;
        if ($keep) {
            $this->deactivate(
                ['source' => 'daad', 'external_id_field' => 'source_id', 'external_id' => '3726', 'name_de' => 'Master of Public Policy', 'degree' => 'master', 'university' => 'Fachhochschule Erfurt'],
                $keep,
            );
        }

        // RWTH: MAC-ES derece programı değil, hazırlık kursu → pasif.
        $this->deactivate(
            ['source' => 'partner', 'external_id_field' => 'partner_id', 'external_id' => '019de9f2-11de-7309-acc0-36e8f5b7acca', 'name_de' => 'Master’s College European Studies (MAC-ES)', 'degree' => 'master', 'university' => 'RWTH Aachen University'],
            null,
        );
    }

    private function fix(array $match, array $changes, array $identity, ?array $deadline = null, array $verify = []): void
    {
        $p = $this->resolve($match);
        if (! $p) {
            return;
        }

        DB::transaction(function () use ($p, $changes, $identity, $deadline, $verify) {
            $p->forceFill($changes)->save();

            // Program düzeltildikten SONRA doğrulanır → parmak izi yeni değerlere göre hesaplanır.
            foreach (['identity' => $identity] + $verify as $field => $attrs) {
                $v = ProgramVerification::firstOrNew(['program_id' => $p->id, 'field' => $field, 'applicant_group' => '', 'term' => '']);
                $v->fill(array_merge($attrs, ['status' => ProgramVerification::VERIFIED, 'review_reason' => null, 'checked_at' => self::CHECKED_AT, 'verified_via' => 'manual-recheck']))->save();
            }

            if ($deadline) {
                ProgramVerification::where('program_id', $p->id)->where('field', 'deadline')->delete();
                ProgramVerification::create(array_merge($deadline, ['program_id' => $p->id, 'field' => 'deadline', 'checked_at' => self::CHECKED_AT, 'verified_via' => 'manual-recheck']));
            }
        });
    }

    private function deactivate(array $match, ?Program $keep): void
    {
        $p = $this->resolve($match);
        if (! $p || ! $p->is_active) {
            return;
        }

        DB::transaction(function () use ($p, $keep) {
            if ($keep) {
                Favorite::where('favoriteable_type', Program::class)->where('favoriteable_id', $p->id)->each(function (Favorite $f) use ($keep) {
                    $exists = Favorite::where('favoriteable_type', Program::class)->where('favoriteable_id', $keep->id)->where('user_id', $f->user_id)->exists();
                    $exists ? $f->delete() : $f->update(['favoriteable_id' => $keep->id]);
                });
            }
            $p->update(['is_active' => false]);
        });
    }

    private function resolve(array $m): ?Program
    {
        $p = $this->single(Program::with('university:id,name_de')->where('source', $m['source'])->where($m['external_id_field'], $m['external_id']));

        return ($p && $p->name_de === $m['name_de'] && $p->degree === $m['degree'] && $p->university?->name_de === $m['university']) ? $p : null;
    }

    private function university(string $name): ?int
    {
        $ids = DB::table('universities')->where('name_de', $name)->pluck('id');

        return $ids->count() === 1 ? (int) $ids->first() : null;
    }

    private function single($query): ?Program
    {
        $hits = $query->get();

        return $hits->count() === 1 ? $hits->first() : null;
    }

    public function down(): void
    {
        // Veri düzeltmesi; önceki değerler yukarıdaki eşleştirme bloklarında.
    }
};
