<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Wikidata ile çözülemeyen şehirsiz ünilere ELLE doğrulanmış şehir atar.
 *  - Mevcut şehirlere bağlar (Bremen, Essen, Berlin, Kleve, Lemgo, Iserlohn, Wiesbaden)
 *  - Tabloda olmayan şehirleri oluşturur (Merseburg, Reutlingen, Schmalkalden,
 *    Wilhelmshaven, Ismaning, Heide) — isim + eyalet doğrulandı.
 *  - #575 (Hesse State Univ. for Police — İngilizce shell, 0 program) = #643'ün (6 program)
 *    DUPE'u → pasifleştirilir.
 *
 * University update'leri query-builder (Scout/Meilisearch tetiklemez — prod 405 önlenir).
 */
return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        // 1) #575 dupe shell'i pasifleştir (HöMS = #643)
        DB::table('universities')->where('id', 575)->update(['is_active' => false]);

        // 2) Yeni şehir oluştur + üniyi bağla  [uni_id, şehir adı, state_id]
        $create = [
            [143, 'Merseburg',      15],
            [151, 'Reutlingen',      1],
            [604, 'Schmalkalden',   17],
            [594, 'Wilhelmshaven',  10],
            [216, 'Ismaning',        2],
            [926, 'Heide',          16],
        ];
        foreach ($create as [$uniId, $name, $stateId]) {
            // FK-güvenli: state yoksa (ör. boş test DB) null bırak — FK ihlali olmasın.
            $stateId = DB::table('states')->where('id', $stateId)->exists() ? $stateId : null;
            // Zaten varsa (idempotent) tekrar oluşturma
            $existing = DB::table('cities')->where('name_de', $name)->value('id');
            $cityId = $existing ?: DB::table('cities')->insertGetId([
                'state_id'   => $stateId,
                'name_tr'    => $name,
                'name_de'    => $name,
                'name_en'    => $name,
                'slug'       => $this->uniqueSlug($name),
                'is_active'  => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            DB::table('universities')->where('id', $uniId)->update(['city_id' => $cityId]);
        }

        // 3) Mevcut şehirlere bağla  [uni_id => city_id]
        $link = [
            987 => 75,   // Constructor University → Bremen
            859 => 182,  // Folkwang → Essen
            991 => 8,    // Hertie School → Berlin
            764 => 192,  // Rhine-Waal → Kleve
            883 => 193,  // OWL University → Lemgo
            840 => 19,   // South Westphalia → Iserlohn
            643 => 184,  // HöMS → Wiesbaden
        ];
        foreach ($link as $uniId => $cityId) {
            DB::table('universities')->where('id', $uniId)->update(['city_id' => $cityId]);
        }
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name);
        $slug = $base; $i = 2;
        while (DB::table('cities')->where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }
        return $slug;
    }

    public function down(): void
    {
        // İçerik düzeltmesi — geri alınmaz.
    }
};
