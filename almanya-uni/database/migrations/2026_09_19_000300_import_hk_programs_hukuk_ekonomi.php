<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * PİLOT: Hochschulkompass kataloğunda olup bizde olmayan hukuk-ekonomi programlarını ekler.
 *
 * NEDEN: `programs` tablosu ağırlıklı olarak partner/DAAD beslemesinden geliyor (uluslararası
 * odaklı). Ölçüm: HK lisanslarının %86'sı, master'ların %79'u bizde yok. Somut sonuç —
 * "Almanya'da Almanca ekonomi nerede okunur" sorusunda site, Mannheim/Köln/LMU'nun Almanca
 * VWL lisanslarını hiç göstermiyordu; /tools/deadlines ekonomi aramasında 2 program dönüyordu.
 *
 * KAPSAM: yalnızca hukuk-ekonomi alanı (pilot). Veri `resources/data/hk-programs-hukuk-ekonomi.json`
 * dosyasından okunur — prod'da hk_catalog boş olduğu için veri repoda taşınır (hk-admission.json
 * ile aynı desen).
 *
 * GÜVENLİK:
 *  - slug bazlı idempotent: aynı slug varsa dokunulmaz, tekrar çalışsa kopya üretmez
 *  - üniversite önce slug, bulunamazsa ADI ile eşleştirilir (prod slug'ları farklı olabiliyor)
 *  - aynı üniversitede aynı ada sahip aktif program varsa ATLANIR (duplikasyon koruması)
 *  - mevcut hiçbir programı değiştirmez/silmez
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('programs') || ! Schema::hasTable('universities')) {
            return;
        }

        $path = resource_path('data/hk-programs-hukuk-ekonomi.json');
        if (! is_file($path)) {
            return;
        }

        $data = json_decode((string) file_get_contents($path), true);
        $rows = $data['programs'] ?? [];
        if ($rows === []) {
            return;
        }

        $fieldId = DB::table('fields_of_study')->where('slug', $data['field'] ?? 'hukuk-ekonomi')->value('id');

        // Üniversite indeksleri: slug + normalize ad
        $bySlug = [];
        $byName = [];
        foreach (DB::table('universities')->where('is_active', 1)->get(['id', 'slug', 'name_de']) as $u) {
            $bySlug[$u->slug] = $u->id;
            $byName[$this->normUni((string) $u->name_de)] = $u->id;
        }

        // Mevcut aktif programlar: üni + normalize ad (aynı programı ikinci kez yaratma)
        $existing = [];
        foreach (DB::table('programs')->where('is_active', 1)->get(['university_id', 'name_de']) as $p) {
            $existing[$p->university_id . '|' . $this->normProg((string) $p->name_de)] = true;
        }
        $existingSlugs = DB::table('programs')->pluck('slug')->flip();

        $now = now();
        $insert = [];
        $skipped = ['uni' => 0, 'dupe' => 0, 'slug' => 0];

        foreach ($rows as $r) {
            $uniId = $bySlug[$r['university_slug'] ?? ''] ?? null;
            if (! $uniId && ! empty($r['university_name'])) {
                $uniId = $byName[$this->normUni((string) $r['university_name'])] ?? null;
            }
            if (! $uniId) {
                $skipped['uni']++;
                continue;
            }

            if (isset($existingSlugs[$r['slug']])) {
                $skipped['slug']++;
                continue;
            }

            $key = $uniId . '|' . $this->normProg((string) $r['name_de']);
            if (isset($existing[$key])) {
                $skipped['dupe']++;
                continue;
            }
            $existing[$key] = true;

            $insert[] = [
                'university_id'        => $uniId,
                'field_of_study_id'    => $fieldId,
                'name_de'              => mb_substr((string) $r['name_de'], 0, 250),
                'slug'                 => $r['slug'],
                'degree'               => $r['degree'] ?? 'other',
                'degree_specification' => mb_substr((string) ($r['degree_spec'] ?? ''), 0, 80) ?: null,
                'language'             => $r['language'] ?? null,
                'admission_mode'       => $r['admission_mode'] ?? null,
                'location'             => mb_substr((string) ($r['location'] ?? ''), 0, 100) ?: null,
                // study_form varchar(30) ve sitede 'online' gibi TEK değer tutuluyor; HK ise
                // virgüllü liste veriyor ("Berufsbegleitendes Studium, Fernstudium, …").
                // Listeyi kanonik tek değere indiriyoruz, sığmayanı yazmıyoruz.
                'study_form'           => $this->studyForm((string) ($r['study_form'] ?? '')),
                'source'               => 'hochschulkompass',
                'is_active'            => 1,
                'last_synced_at'       => $now,
                'created_at'           => $now,
                'updated_at'           => $now,
            ];
        }

        foreach (array_chunk($insert, 200) as $chunk) {
            DB::table('programs')->insert($chunk);
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('programs')) {
            DB::table('programs')->where('source', 'hochschulkompass')->delete();
        }
    }

    /** HK'nın virgüllü "form" listesini sitenin tek-değerli study_form alanına indirger. */
    private function studyForm(string $form): ?string
    {
        $f = mb_strtolower($form, 'UTF-8');
        if ($f === '') {
            return null;
        }
        if (str_contains($f, 'fernstudium')) {
            return 'online';
        }
        if (str_contains($f, 'duales')) {
            return 'dual';
        }
        if (str_contains($f, 'berufsbegleitend')) {
            return 'part-time';
        }
        if (str_contains($f, 'teilzeit')) {
            return 'part-time';
        }
        if (str_contains($f, 'vollzeit')) {
            return 'full-time';
        }

        return null;
    }

    private function normUni(string $s): string
    {
        $s = mb_strtolower($s, 'UTF-8');
        $s = strtr($s, ['ä' => 'ae', 'ö' => 'oe', 'ü' => 'ue', 'ß' => 'ss']);
        $s = preg_replace('/\s[-–:].*$/u', ' ', $s);
        $s = preg_replace('/,.*$/u', ' ', $s);
        $s = preg_replace('/\(.*?\)/', ' ', $s);
        $s = preg_replace('/\b(university of applied sciences|university|universitaet|hochschule|fachhochschule|technische|fh|uni)\b/u', ' ', $s);

        return preg_replace('/[^a-z0-9]+/', '', $s);
    }

    private function normProg(string $s): string
    {
        $s = mb_strtolower($s, 'UTF-8');
        $s = strtr($s, ['ä' => 'ae', 'ö' => 'oe', 'ü' => 'ue', 'ß' => 'ss']);
        $s = preg_replace('/\b(b\.?\s?sc|m\.?\s?sc|b\.?\s?a|m\.?\s?a|bachelor|master|of science|of arts)\b\.?/u', ' ', $s);
        $s = preg_replace('/\(.*?\)/', ' ', $s);

        return preg_replace('/[^a-z0-9]+/', '', $s);
    }
};
