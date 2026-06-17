<?php

use App\Models\State;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Eyalet armaları (Wappen / coat of arms): Wikidata P94 → Wikimedia Commons doğrudan
 * thumb URL'i (kamu malı, ücretsiz). Harita üstünde her eyaletin armasını göstermek için
 * (referans almancaabc haritasındaki gibi). Lokalde çözülüp statik gömüldü; runtime HTTP yok.
 */
return new class extends Migration
{
    private array $coa = [
        'baden-wurttemberg'       => 'commons/thumb/a/a5/Greater_coat_of_arms_of_Baden-W%C3%BCrttemberg.svg/250px-Greater_coat_of_arms_of_Baden-W%C3%BCrttemberg.svg.png',
        'bayern'                  => 'commons/thumb/d/d5/Coat_of_arms_of_Bavaria.svg/250px-Coat_of_arms_of_Bavaria.svg.png',
        'berlin'                  => 'commons/thumb/8/8c/DEU_Berlin_COA.svg/250px-DEU_Berlin_COA.svg.png',
        'brandenburg'             => 'commons/thumb/a/a2/DEU_Brandenburg_COA.svg/250px-DEU_Brandenburg_COA.svg.png',
        'freie-hansestadt-bremen' => 'commons/thumb/1/17/Bremen_greater_coat_of_arms.svg/250px-Bremen_greater_coat_of_arms.svg.png',
        'hamburg'                 => 'commons/thumb/5/5d/DEU_Hamburg_COA.svg/250px-DEU_Hamburg_COA.svg.png',
        'hessen'                  => 'commons/thumb/c/cd/Coat_of_arms_of_Hesse.svg/250px-Coat_of_arms_of_Hesse.svg.png',
        'niedersachsen'           => 'commons/thumb/0/0b/Coat_of_arms_of_Lower_Saxony.svg/250px-Coat_of_arms_of_Lower_Saxony.svg.png',
        'mecklenburg-vorpommern'  => 'commons/thumb/7/74/Coat_of_arms_of_Mecklenburg-Western_Pomerania_%28great%29.svg/250px-Coat_of_arms_of_Mecklenburg-Western_Pomerania_%28great%29.svg.png',
        'nordrhein-westfalen'     => 'commons/thumb/1/1b/Coat_of_arms_of_North_Rhine-Westphalia.svg/250px-Coat_of_arms_of_North_Rhine-Westphalia.svg.png',
        'rheinland-pfalz'         => 'commons/thumb/8/89/Coat_of_arms_of_Rhineland-Palatinate.svg/250px-Coat_of_arms_of_Rhineland-Palatinate.svg.png',
        'saarland'                => 'commons/thumb/8/8e/Wappen_des_Saarlands.svg/250px-Wappen_des_Saarlands.svg.png',
        'sachsen'                 => 'commons/thumb/5/5f/Coat_of_arms_of_Saxony.svg/250px-Coat_of_arms_of_Saxony.svg.png',
        'sachsen-anhalt'          => 'commons/thumb/5/53/Wappen_Sachsen-Anhalt.svg/250px-Wappen_Sachsen-Anhalt.svg.png',
        'schleswig-holstein'      => 'commons/thumb/0/02/DEU_Schleswig-Holstein_COA.svg/250px-DEU_Schleswig-Holstein_COA.svg.png',
        'thuringen'               => 'commons/thumb/0/08/Coat_of_arms_of_Thuringia.svg/250px-Coat_of_arms_of_Thuringia.svg.png',
    ];

    public function up(): void
    {
        if (! Schema::hasColumn('states', 'coat_of_arms_url')) {
            Schema::table('states', function (Blueprint $table) {
                $table->string('coat_of_arms_url')->nullable()->after('flag_url');
            });
        }

        $base = 'https://upload.wikimedia.org/wikipedia/';
        foreach ($this->coa as $slug => $path) {
            State::where('slug', $slug)->update(['coat_of_arms_url' => $base . $path]);
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('states', 'coat_of_arms_url')) {
            Schema::table('states', function (Blueprint $table) {
                $table->dropColumn('coat_of_arms_url');
            });
        }
    }
};
