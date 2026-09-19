<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * ÜÇÜNCÜ PARTİ: kalan yedi alan — tıp-sağlık, veteriner-spor, tarım, dil-kültür,
 * matematik-doğa, sanat-tasarım, sosyal bilimler.
 *
 * Pilot (000300) ve ikinci parti (000400) sonrası katalog bu partiyle tamamlanıyor.
 * Veri `resources/data/hk-programs-{alan}.json` dosyalarından okunur — prod'da hk_catalog boş.
 *
 * ALAN KURALLARI SİTENİN KENDİ ALIŞKANLIĞINA GÖRE yazıldı (mevcut kayıtların dağılımı ölçüldü):
 * Wirtschaftsingenieur → hukuk-ekonomi, Medizintechnik → tıp-sağlık, Data Science → bilişim,
 * Mechatronik → mühendislik. Böylece yeni kayıtlar mevcutlarla aynı yerde toplanıyor.
 *
 * GÜVENLİK: slug bazlı idempotent · üniversite önce slug sonra ad ile eşleşir (prod slug'ları
 * farklı olabiliyor) · aynı üni+ad varsa atlanır · mevcut hiçbir kayıt değiştirilmez/silinmez.
 * Bu kayıtlar yalnız ad+derece+NC taşıdığı için Program::isThin() ile noindex olur ve
 * sitemap'e girmez (bkz. 000300).
 */
return new class extends Migration
{
    /**
     * SIRA ONEMLI: migration ilk eslesen kaydi yazar, ayni uni+ad tekrar gelirse atlar.
     * Bu yuzden spesifik alanlar (tip, veteriner, tarim) genel alandan (sosyal bilimler) ONCE.
     * Ornek: "Lehramt Mathematik" hem matematik-doga hem sosyal-bilimler dosyasinda var;
     * matematik once geldigi icin oraya yazilir.
     */
    private const FILES = [
        'hk-programs-tip-saglik.json',
        'hk-programs-veteriner-spor.json',
        'hk-programs-tarim-ormancilik.json',
        'hk-programs-dil-kultur.json',
        'hk-programs-matematik-doga.json',
        'hk-programs-sanat-tasarim.json',
        'hk-programs-sosyal-bilimler.json',
    ];

    public function up(): void
    {
        if (! Schema::hasTable('programs') || ! Schema::hasTable('universities')) {
            return;
        }

        $bySlug = [];
        $byName = [];
        foreach (DB::table('universities')->where('is_active', 1)->get(['id', 'slug', 'name_de']) as $u) {
            $bySlug[$u->slug] = $u->id;
            $byName[$this->normUni((string) $u->name_de)] = $u->id;
        }

        $existing = [];
        foreach (DB::table('programs')->where('is_active', 1)->get(['university_id', 'name_de']) as $p) {
            $existing[$p->university_id . '|' . $this->normProg((string) $p->name_de)] = true;
        }
        $existingSlugs = DB::table('programs')->pluck('slug')->flip();

        $now = now();
        $insert = [];

        foreach (self::FILES as $file) {
            $path = resource_path('data/' . $file);
            if (! is_file($path)) {
                continue;
            }

            $data = json_decode((string) file_get_contents($path), true);
            $rows = $data['programs'] ?? [];
            $fieldId = DB::table('fields_of_study')->where('slug', $data['field'] ?? '')->value('id');
            if ($rows === [] || ! $fieldId) {
                continue;
            }

            foreach ($rows as $r) {
                $uniId = $bySlug[$r['university_slug'] ?? ''] ?? null;
                if (! $uniId && ! empty($r['university_name'])) {
                    $uniId = $byName[$this->normUni((string) $r['university_name'])] ?? null;
                }
                if (! $uniId || isset($existingSlugs[$r['slug']])) {
                    continue;
                }

                $key = $uniId . '|' . $this->normProg((string) $r['name_de']);
                if (isset($existing[$key])) {
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
                    'study_form'           => $this->studyForm((string) ($r['study_form'] ?? '')),
                    'source'               => 'hochschulkompass',
                    'is_active'            => 1,
                    'last_synced_at'       => $now,
                    'created_at'           => $now,
                    'updated_at'           => $now,
                ];
            }
        }

        foreach (array_chunk($insert, 200) as $chunk) {
            DB::table('programs')->insert($chunk);
        }
    }

    public function down(): void
    {
        // 000300 ile aynı kaynağı paylaşıyorlar; burada YALNIZ bu partinin slug'ları silinir.
        if (! Schema::hasTable('programs')) {
            return;
        }

        foreach (self::FILES as $file) {
            $path = resource_path('data/' . $file);
            if (! is_file($path)) {
                continue;
            }
            $data = json_decode((string) file_get_contents($path), true);
            $slugs = array_column($data['programs'] ?? [], 'slug');
            foreach (array_chunk($slugs, 500) as $chunk) {
                DB::table('programs')->whereIn('slug', $chunk)->where('source', 'hochschulkompass')->delete();
            }
        }
    }

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
        if (str_contains($f, 'berufsbegleitend') || str_contains($f, 'teilzeit')) {
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
