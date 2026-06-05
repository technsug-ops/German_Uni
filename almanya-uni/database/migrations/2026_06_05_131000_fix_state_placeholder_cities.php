<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * QA (Duisburg sayfası "0 üniversite"): şehri çözülemeyen üniler, içe aktarımda
 * EYALET adıyla açılmış sahte "şehir" kayıtlarına (ör. "Nordrhein-Westfalen")
 * atılmıştı → gerçek şehir sayfaları boş, üniler kayıp.
 *
 * Her üniyi DOĞRU şehrine bağlar (birincil city_id + ek kampüs pivot), eksik
 * şehirleri doğru eyaletle oluşturur, sahte eyalet-şehirleri pasifleştirir
 * (Berlin/Hamburg/Bremen GERÇEK → hariç).
 *
 * SAF DB::table kullanır — Eloquent model event/observer/Scout tetiklemez
 * (prod'da $model->save() bir search-index sync'ine takılıp FAIL veriyordu).
 * İsimle eşleşir (id'ler ortamlar arası farklı). Idempotent.
 */
return new class extends Migration
{
    /** uni name_de => [birincil şehir, [ek kampüsler...]] */
    private array $map = [
        'Hochschule für Musik Detmold'                              => ['Detmold', []],
        'Hochschule Niederrhein'                                    => ['Krefeld', ['Mönchengladbach']],
        'Hochschule Rhein-Waal'                                     => ['Kleve', []],
        'Technische Hochschule Ostwestfalen-Lippe'                  => ['Lemgo', []],
        'Universität Duisburg-Essen'                                => ['Essen', ['Duisburg']],
        'Universität Paderborn'                                     => ['Paderborn', []],
        'Westfälische Hochschule Gelsenkirchen Bocholt Recklinghausen' => ['Gelsenkirchen', ['Bocholt', 'Recklinghausen']],
        'Hochschule für Polizei Baden-Württemberg'                  => ['Villingen-Schwenningen', []],
        'Popakademie Baden-Württemberg GmbH'                        => ['Mannheim', []],
        'Friedrich-Alexander-Universität Erlangen-Nürnberg'         => ['Erlangen', ['Nürnberg']],
        'Hochschule für den öffentlichen Dienst in Bayern'          => ['Hof', []],
        'Hochschule Emden/Leer'                                     => ['Emden', ['Leer']],
        'Kommunale Hochschule für Verwaltung in Niedersachsen'      => ['Hannover', []],
        'Brandenburg University of Technology Cottbus-Senftenberg'  => ['Cottbus', ['Senftenberg']],
        'Medizinische Hochschule Brandenburg Theodor Fontane'       => ['Neuruppin', []],
        'Technische Hochschule Brandenburg'                         => ['Brandenburg an der Havel', []],
        'Technische Hochschule Wildau'                              => ['Wildau', []],
    ];

    /** şehir adı => state_id (eksikse oluşturmak için) */
    private array $cityState = [
        'Detmold' => 11, 'Krefeld' => 11, 'Mönchengladbach' => 11, 'Kleve' => 11,
        'Lemgo' => 11, 'Essen' => 11, 'Duisburg' => 11, 'Paderborn' => 11,
        'Gelsenkirchen' => 11, 'Bocholt' => 11, 'Recklinghausen' => 11,
        'Villingen-Schwenningen' => 1, 'Mannheim' => 1,
        'Erlangen' => 2, 'Nürnberg' => 2, 'Hof' => 2,
        'Emden' => 10, 'Leer' => 10, 'Hannover' => 10,
        'Cottbus' => 4, 'Senftenberg' => 4, 'Neuruppin' => 4,
        'Brandenburg an der Havel' => 4, 'Wildau' => 4,
    ];

    public function up(): void
    {
        $now = now();

        $resolve = function (string $name) use ($now): ?int {
            $id = DB::table('cities')->where('name_de', $name)->value('id');
            if ($id) {
                return (int) $id;
            }
            $stateId = $this->cityState[$name] ?? null;
            if (! $stateId) {
                return null;
            }
            $slug = Str::slug($name);
            if (DB::table('cities')->where('slug', $slug)->exists()) {
                $slug .= '-' . $stateId;
            }
            return (int) DB::table('cities')->insertGetId([
                'name_de'    => $name,
                'name_tr'    => $name,
                'name_en'    => $name,
                'slug'       => $slug,
                'state_id'   => $stateId,
                'is_active'  => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        };

        foreach ($this->map as $uniName => [$primaryName, $campusNames]) {
            $uid = DB::table('universities')->where('name_de', $uniName)->value('id');
            if (! $uid) {
                continue;
            }

            $primaryId = $resolve($primaryName);
            if ($primaryId) {
                DB::table('universities')->where('id', $uid)
                    ->update(['city_id' => $primaryId, 'updated_at' => $now]);
            }

            // Ek kampüsler (birincil hariç) → pivot (tam senkron, idempotent)
            DB::table('university_campuses')->where('university_id', $uid)->delete();
            foreach ($campusNames as $cn) {
                $cid = $resolve($cn);
                if ($cid && $cid !== $primaryId) {
                    DB::table('university_campuses')->insert([
                        'university_id' => $uid,
                        'city_id'       => $cid,
                        'created_at'    => $now,
                        'updated_at'    => $now,
                    ]);
                }
            }
        }

        // Sahte eyalet-adlı "şehir"leri pasifleştir (Berlin/Hamburg/Bremen GERÇEK → hariç).
        // Güvenlik: yalnızca AKTİF ÜNİSİ KALMAYANLAR (eşlenmemiş üni sahipsiz kalmasın).
        $areaStateNames = DB::table('states')->pluck('name_de')
            ->reject(fn ($n) => in_array($n, ['Berlin', 'Hamburg', 'Freie Hansestadt Bremen'], true))
            ->values()->all();

        DB::table('cities')
            ->whereIn('name_de', $areaStateNames)
            ->whereNotExists(function ($q) {
                $q->select(DB::raw(1))->from('universities')
                    ->whereColumn('universities.city_id', 'cities.id')
                    ->where('universities.is_active', 1);
            })
            ->update(['is_active' => 0, 'updated_at' => $now]);
    }

    public function down(): void
    {
        // Geri alınamaz — no-op.
    }
};
