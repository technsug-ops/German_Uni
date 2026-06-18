<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Hochschulkompass ham katalog yedeği. Kazınan 22.314 satır burada referans/yedek
 * olarak durur (büyük veri kaybolmasın). Mevcut programları düzeltmek için kaynak;
 * bulk import YAPMAZ. `programs:load-hk-backup` ile doldurulur.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('hk_catalog')) {
            return;
        }

        Schema::create('hk_catalog', function (Blueprint $table) {
            $table->id();
            $table->string('mode', 32)->index();           // zulassungsfrei | oertlich | bundesweit | auswahl
            $table->string('hochschule', 400)->index();     // HK üni adı (ham)
            $table->string('fach', 400);                     // program adı
            $table->string('ort', 200)->nullable();          // şehir
            $table->string('abschluss', 400)->nullable();    // derece
            $table->string('typ', 120)->nullable();          // grundständig | weiterführend
            $table->string('studtyp', 120)->nullable();
            $table->text('form')->nullable();                // Vollzeit/Teilzeit/… (uzun olabilir)
            $table->string('zulassung', 64)->nullable();     // kabul türü (admission_mode)
            $table->json('raw')->nullable();                 // tam ham satır
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hk_catalog');
    }
};
