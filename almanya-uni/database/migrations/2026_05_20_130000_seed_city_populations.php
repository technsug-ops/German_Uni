<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Wikipedia 2023-2024 güncel rakamları ile en büyük 60 Alman şehrinin nüfusu.
     * Population filter'ı için temel veri seti.
     */
    public function up(): void
    {
        $populations = [
            'Berlin' => 3_755_000,
            'Hamburg' => 1_910_000,
            'München' => 1_512_000,
            'Köln' => 1_085_000,
            'Frankfurt am Main' => 776_000,
            'Stuttgart' => 633_000,
            'Düsseldorf' => 629_000,
            'Leipzig' => 624_000,
            'Dortmund' => 593_000,
            'Essen' => 580_000,
            'Bremen' => 569_000,
            'Dresden' => 563_000,
            'Hannover' => 545_000,
            'Nürnberg' => 524_000,
            'Duisburg' => 495_000,
            'Bochum' => 365_000,
            'Wuppertal' => 357_000,
            'Bielefeld' => 339_000,
            'Bonn' => 336_000,
            'Münster' => 320_000,
            'Mannheim' => 314_000,
            'Karlsruhe' => 308_000,
            'Augsburg' => 304_000,
            'Wiesbaden' => 280_000,
            'Mönchengladbach' => 263_000,
            'Gelsenkirchen' => 260_000,
            'Aachen' => 250_000,
            'Braunschweig' => 250_000,
            'Kiel' => 248_000,
            'Chemnitz' => 246_000,
            'Halle (Saale)' => 240_000,
            'Magdeburg' => 238_000,
            'Freiburg im Breisgau' => 236_000,
            'Krefeld' => 227_000,
            'Mainz' => 220_000,
            'Lübeck' => 219_000,
            'Erfurt' => 215_000,
            'Oberhausen' => 209_000,
            'Rostock' => 208_000,
            'Kassel' => 204_000,
            'Hagen' => 188_000,
            'Saarbrücken' => 180_000,
            'Hamm' => 179_000,
            'Potsdam' => 183_000,
            'Mülheim an der Ruhr' => 173_000,
            'Ludwigshafen am Rhein' => 173_000,
            'Oldenburg' => 172_000,
            'Leverkusen' => 168_000,
            'Osnabrück' => 167_000,
            'Solingen' => 161_000,
            'Heidelberg' => 159_000,
            'Herne' => 156_000,
            'Neuss' => 154_000,
            'Darmstadt' => 159_000,
            'Paderborn' => 151_000,
            'Regensburg' => 153_000,
            'Ingolstadt' => 137_000,
            'Würzburg' => 127_000,
            'Wolfsburg' => 124_000,
            'Ulm' => 127_000,
            'Heilbronn' => 126_000,
            'Pforzheim' => 125_000,
            'Göttingen' => 117_000,
            'Bottrop' => 117_000,
            'Reutlingen' => 116_000,
            'Koblenz' => 114_000,
            'Bremerhaven' => 113_000,
            'Erlangen' => 112_000,
            'Trier' => 111_000,
            'Jena' => 109_000,
            'Salzgitter' => 103_000,
            'Siegen' => 101_000,
            'Hildesheim' => 100_000,
            'Cottbus' => 99_000,
            'Tübingen' => 91_000,
            'Lüneburg' => 78_000,
            'Marburg' => 76_000,
            'Bayreuth' => 75_000,
            'Konstanz' => 84_000,
            'Bamberg' => 77_000,
            'Greifswald' => 60_000,
            'Passau' => 53_000,
            'Eichstätt' => 14_000,
            'Vechta' => 33_000,
        ];

        foreach ($populations as $name => $pop) {
            DB::table('cities')
                ->whereNull('population')
                ->where(function ($q) use ($name) {
                    $q->where('name_de', $name)
                      ->orWhere('name_en', $name);
                })
                ->update(['population' => $pop]);
        }
    }

    public function down(): void
    {
        // Population'ı sadece bu migration'da set ettiklerimiz için sıfırla
        DB::table('cities')
            ->whereIn('population', [3755000, 1910000, 1512000])
            ->update(['population' => null]);
    }
};
