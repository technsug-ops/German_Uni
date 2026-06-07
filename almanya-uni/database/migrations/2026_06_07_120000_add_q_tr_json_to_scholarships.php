<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * DAAD burs uygunluk (q) içeriğinin TR çevirisi için kolon.
 * q_de_json / q_en_json vardı ama TR yoktu → /tr'de uygunluk bloğu İngilizce sızıyordu
 * (kaynak-dili lokalizasyon ihlali). scholarships:localize bunu q_tr_json'a doldurur.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('scholarships', function (Blueprint $table) {
            if (! Schema::hasColumn('scholarships', 'q_tr_json')) {
                $table->json('q_tr_json')->nullable()->after('q_en_json');
            }
        });
    }

    public function down(): void
    {
        Schema::table('scholarships', function (Blueprint $table) {
            if (Schema::hasColumn('scholarships', 'q_tr_json')) {
                $table->dropColumn('q_tr_json');
            }
        });
    }
};
