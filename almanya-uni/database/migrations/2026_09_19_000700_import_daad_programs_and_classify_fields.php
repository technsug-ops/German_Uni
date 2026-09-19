<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * DAAD programları (resmî API) + alanı boş programlara alan ataması.
 *
 * VERİ: `resources/data/daad-programs.json` — lokalde `daad:import` ile DAAD'ın resmî
 * International Programmes API'sinden çekildi, `daad:export-file` ile dosyaya yazıldı.
 * Prod'da artisan yok ve deploy sırasında 3.245 kaydı 1 sn aralıkla çekmek ~1 saat sürerdi;
 * bu yüzden veri repoda taşınıyor (hk-programs-*.json ile aynı desen).
 *
 * NE GETİRİYOR: başvuru tarihi (2.495 kayıt), süre (2.499), resmî program linki (2.810),
 * dil seviyesi, ücret, finansal destek. Bu alanlar dolu olduğu için bu sayfalar
 * Program::isThin() ölçütünden geçer — yani noindex DEĞİL, indekslenebilir içerik.
 *
 * ALAN ATAMASI: DAAD importer'ı field_of_study_id yazmıyordu → 2.693 aktif program alan
 * sıralamalarında ve alan sayfalarında görünmüyordu. `programs:classify-fields` ad/konu
 * metninden atar, EMİN OLAMADIĞINI ATLAR (yanlış alan, boş alandan kötüdür: sıralama
 * puanının %22'si alan derinliğine bakıyor). Lokal ölçüm: 2.310 atandı, 383 atlandı.
 *
 * GÜVENLİK: source_id ile upsert (idempotent) · üniversite önce slug sonra ad ile eşleşir ·
 * eşleşmeyen kayıt atlanır · mevcut programlar SİLİNMEZ.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('programs') || ! Schema::hasTable('universities')) {
            return;
        }

        $path = resource_path('data/daad-programs.json');
        if (is_file($path)) {
            $this->importDaad($path);
        }

        // Alan ataması: DAAD kayıtları + eşleşmemiş partner kayıtları için.
        if (DB::table('programs')->count() >= 100) {
            try {
                Artisan::call('programs:classify-fields');
            } catch (\Throwable $e) {
                report($e);
            }

            // DAAD ile partner beslemesi AYNI programı iki kez yaratıyor (lokalde 1.001 grup
            // ölçüldü). Dedupe zengin kaydı tutar, digerini pasifleştirir ve artık eksik
            // alanları BIRLESTIRIR — özellikle `language`: DAAD "en" derken partner "both"
            // diyebiliyor (83 grup); sadece kazananı bırakmak iki dilli programı
            // İngilizce-only yapar ve Almanca listelerinden düşürürdü.
            try {
                Artisan::call('programs:dedupe', ['--apply' => true]);
            } catch (\Throwable $e) {
                report($e);
            }
        }
    }

    private function importDaad(string $path): void
    {
        $data = json_decode((string) file_get_contents($path), true);
        $rows = $data['programs'] ?? [];
        if ($rows === []) {
            return;
        }

        $bySlug = [];
        $byName = [];
        foreach (DB::table('universities')->get(['id', 'slug', 'name_de']) as $u) {
            $bySlug[$u->slug] = $u->id;
            $byName[$this->normUni((string) $u->name_de)] = $u->id;
        }
        $fieldIds = DB::table('fields_of_study')->pluck('id', 'slug')->all();

        $now = now();
        $insert = [];

        foreach ($rows as $r) {
            $uniId = $bySlug[$r['university_slug'] ?? ''] ?? null;
            if (! $uniId && ! empty($r['university_name'])) {
                $uniId = $byName[$this->normUni((string) $r['university_name'])] ?? null;
            }
            if (! $uniId) {
                continue;
            }

            $payload = [
                'university_id'      => $uniId,
                'field_of_study_id'  => $fieldIds[$r['field_slug'] ?? ''] ?? null,
                'name_de'            => $r['name_de'] ?? null,
                'name_en'            => $r['name_en'] ?? null,
                'degree'             => $r['degree'] ?? 'other',
                'language'           => $r['language'] ?? null,
                'duration_semesters' => $r['duration_semesters'] ?? null,
                'start_semester'     => $r['start_semester'] ?? null,
                'tuition_fee_eur'    => $r['tuition_fee_eur'] ?? null,
                'admission_summary'  => $r['admission_summary'] ?? null,
                'description_en'     => $r['description_en'] ?? null,
                'image_url'          => $r['image_url'] ?? null,
                'language_level_de'  => $r['language_level_de'] ?? null,
                'language_level_en'  => $r['language_level_en'] ?? null,
                'is_online'          => (bool) ($r['is_online'] ?? false),
                'study_form'         => $r['study_form'] ?? null,
                'financial_support'  => $r['financial_support'] ?? null,
                'support_info'       => $r['support_info'] ?? null,
                'source_url'         => $r['source_url'] ?? null,
                'study_fields_raw'   => $r['study_fields_raw'] ?? null,
                'source'             => 'daad',
                'source_id'          => (string) $r['source_id'],
                'is_active'          => 1,
                'last_synced_at'     => $now,
                'updated_at'         => $now,
            ];

            $existing = DB::table('programs')->where('source', 'daad')
                ->where('source_id', (string) $r['source_id'])->first(['id']);

            if ($existing) {
                DB::table('programs')->where('id', $existing->id)->update($payload);
                continue;
            }

            // Slug çakışması olursa (başka kaynaktan aynı ad) kaydı atla — slug global unique.
            if (DB::table('programs')->where('slug', $r['slug'])->exists()) {
                continue;
            }

            $insert[] = $payload + ['slug' => $r['slug'], 'created_at' => $now];
        }

        foreach (array_chunk($insert, 200) as $chunk) {
            DB::table('programs')->insert($chunk);
        }
    }

    public function down(): void
    {
        // Alan ataması geri alınmaz (zaten boştu). DAAD kayıtları korunur —
        // bu migration'dan ÖNCE de 678 DAAD programı vardı, ayırt etmek güvenilmez.
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
};
