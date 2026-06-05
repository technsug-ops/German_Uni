<?php

use App\Models\City;
use App\Models\State;
use App\Models\University;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Str;

/**
 * QA (Duisburg sayfası "0 üniversite"): şehri çözülemeyen üniler, içe aktarımda
 * EYALET adıyla açılmış sahte "şehir" kayıtlarına (ör. "Nordrhein-Westfalen")
 * atılmıştı → gerçek şehir sayfaları boş, üniler kayıp.
 *
 * Bu migration her üniyi DOĞRU şehrine bağlar:
 *  - birincil şehir (city_id) = ana kampüs
 *  - ek kampüsler (university_campuses pivot) = çok-şehirli üniler (Duisburg-Essen,
 *    Erlangen-Nürnberg, Cottbus-Senftenberg, Gelsenkirchen-Bocholt-Recklinghausen...)
 *  - eksik şehirler doğru eyalet_id ile oluşturulur
 *  - sahte eyalet-adlı "şehir"ler pasifleştirilir (Berlin/Hamburg/Bremen GERÇEK → dokunulmaz)
 *
 * İSİMLE eşleşir (id'ler ortamlar arası farklı). Idempotent.
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
        // Nordrhein-Westfalen
        'Detmold' => 11, 'Krefeld' => 11, 'Mönchengladbach' => 11, 'Kleve' => 11,
        'Lemgo' => 11, 'Essen' => 11, 'Duisburg' => 11, 'Paderborn' => 11,
        'Gelsenkirchen' => 11, 'Bocholt' => 11, 'Recklinghausen' => 11,
        // Baden-Württemberg
        'Villingen-Schwenningen' => 1, 'Mannheim' => 1,
        // Bayern
        'Erlangen' => 2, 'Nürnberg' => 2, 'Hof' => 2,
        // Niedersachsen
        'Emden' => 10, 'Leer' => 10, 'Hannover' => 10,
        // Brandenburg
        'Cottbus' => 4, 'Senftenberg' => 4, 'Neuruppin' => 4,
        'Brandenburg an der Havel' => 4, 'Wildau' => 4,
    ];

    public function up(): void
    {
        $resolve = function (string $name): ?int {
            $city = City::where('name_de', $name)->first();
            if ($city) {
                return $city->id;
            }
            $stateId = $this->cityState[$name] ?? null;
            if (! $stateId) {
                return null; // bilinmeyen şehir → atla (veri bütünlüğü)
            }
            $slug = Str::slug($name);
            if (City::where('slug', $slug)->exists()) {
                $slug .= '-' . $stateId;
            }
            return City::create([
                'name_de'   => $name,
                'name_tr'   => $name,
                'name_en'   => $name,
                'slug'      => $slug,
                'state_id'  => $stateId,
                'is_active' => true,
            ])->id;
        };

        foreach ($this->map as $uniName => [$primaryName, $campusNames]) {
            $uni = University::where('name_de', $uniName)->first();
            if (! $uni) {
                continue;
            }

            $primaryId = $resolve($primaryName);
            if ($primaryId) {
                $uni->city_id = $primaryId;
                $uni->save();
            }

            // Ek kampüsler (birincil hariç) → pivot (tam senkron, idempotent)
            $campusIds = [];
            foreach ($campusNames as $cn) {
                $cid = $resolve($cn);
                if ($cid && $cid !== $primaryId) {
                    $campusIds[] = $cid;
                }
            }
            $uni->campusCities()->sync($campusIds);
        }

        // Sahte eyalet-adlı "şehir"leri pasifleştir (Berlin/Hamburg/Bremen GERÇEK → hariç)
        $areaStateNames = State::pluck('name_de')
            ->reject(fn ($n) => in_array($n, ['Berlin', 'Hamburg', 'Freie Hansestadt Bremen'], true))
            ->values()->all();
        City::whereIn('name_de', $areaStateNames)->update(['is_active' => false]);
    }

    public function down(): void
    {
        // Geri alınamaz (yanlış atamaya dönmek istemeyiz) — no-op.
    }
};
