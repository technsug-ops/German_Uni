<?php

use App\Models\State;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Eyalet bayrakları: Wikidata P41 → Wikimedia Commons (kamu malı, ücretsiz; Freepik gibi
 * telifli kaynak DEĞİL). URL'ler lokalde Wikidata'dan çekilip statik gömüldü — CI/prod'da
 * runtime HTTP yok. Commons Special:FilePath SVG'yi 320px PNG'ye rasterize eder.
 */
return new class extends Migration
{
    // Doğrudan upload.wikimedia.org thumb URL'leri (330px PNG; redirect yok, şehir
    // görselleriyle aynı kalıp). Lokalde Special:FilePath çözülerek elde edildi.
    private array $flags = [
        'baden-wurttemberg'       => 'commons/thumb/5/5c/Flag_of_Baden-W%C3%BCrttemberg.svg/330px-Flag_of_Baden-W%C3%BCrttemberg.svg.png',
        'bayern'                  => 'commons/thumb/1/16/Flag_of_Bavaria_%28striped%29.svg/330px-Flag_of_Bavaria_%28striped%29.svg.png',
        'berlin'                  => 'commons/thumb/e/ec/Flag_of_Berlin.svg/330px-Flag_of_Berlin.svg.png',
        'brandenburg'             => 'commons/thumb/0/01/Flag_of_Brandenburg.svg/330px-Flag_of_Brandenburg.svg.png',
        'freie-hansestadt-bremen' => 'commons/thumb/0/0e/Flag_of_Bremen.svg/330px-Flag_of_Bremen.svg.png',
        'hamburg'                 => 'commons/thumb/7/74/Flag_of_Hamburg.svg/330px-Flag_of_Hamburg.svg.png',
        'hessen'                  => 'commons/thumb/f/f7/Flag_of_Hesse.svg/330px-Flag_of_Hesse.svg.png',
        'niedersachsen'           => 'commons/thumb/8/81/Flag_of_Lower_Saxony.svg/330px-Flag_of_Lower_Saxony.svg.png',
        'mecklenburg-vorpommern'  => 'commons/thumb/c/ce/Flag_of_Mecklenburg-Western_Pomerania.svg/330px-Flag_of_Mecklenburg-Western_Pomerania.svg.png',
        'nordrhein-westfalen'     => 'commons/thumb/8/84/Flag_of_North_Rhine-Westphalia.svg/330px-Flag_of_North_Rhine-Westphalia.svg.png',
        'rheinland-pfalz'         => 'commons/thumb/b/b7/Flag_of_Rhineland-Palatinate.svg/330px-Flag_of_Rhineland-Palatinate.svg.png',
        'saarland'                => 'commons/thumb/f/f7/Flag_of_Saarland.svg/330px-Flag_of_Saarland.svg.png',
        'sachsen'                 => 'commons/thumb/f/fd/Flag_of_Saxony.svg/330px-Flag_of_Saxony.svg.png',
        'sachsen-anhalt'          => 'commons/thumb/c/c2/Flag_of_Saxony-Anhalt_%28state%29.svg/330px-Flag_of_Saxony-Anhalt_%28state%29.svg.png',
        'schleswig-holstein'      => 'commons/thumb/b/b4/Flag_of_Schleswig-Holstein.svg/330px-Flag_of_Schleswig-Holstein.svg.png',
        'thuringen'               => 'commons/thumb/b/bd/Flag_of_Thuringia.svg/330px-Flag_of_Thuringia.svg.png',
    ];

    public function up(): void
    {
        if (! Schema::hasColumn('states', 'flag_url')) {
            Schema::table('states', function (Blueprint $table) {
                $table->string('flag_url')->nullable()->after('image_url');
            });
        }

        $base = 'https://upload.wikimedia.org/wikipedia/';
        foreach ($this->flags as $slug => $path) {
            State::where('slug', $slug)->update(['flag_url' => $base . $path]);
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('states', 'flag_url')) {
            Schema::table('states', function (Blueprint $table) {
                $table->dropColumn('flag_url');
            });
        }
    }
};
