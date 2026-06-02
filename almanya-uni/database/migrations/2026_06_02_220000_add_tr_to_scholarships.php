<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Burslara Türkçe isim alanları. DAAD kaynağı sadece de/en veriyor; kaynak-dili
 * lokalizasyon kuralı (feedback-source-language-localization): orijinal ad
 * korunur + /tr'de yerel ad parantezde. introduction_json zaten çok-dilli (tr
 * key eklenir, kolon gerekmez); name/programmname ayrı kolon olduğu için eklenir.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('scholarships', function (Blueprint $t) {
            if (! Schema::hasColumn('scholarships', 'name_tr')) {
                $t->string('name_tr')->nullable()->after('name_en');
            }
            if (! Schema::hasColumn('scholarships', 'programmname_tr')) {
                $t->string('programmname_tr')->nullable()->after('programmname_en');
            }
        });
    }

    public function down(): void
    {
        Schema::table('scholarships', function (Blueprint $t) {
            $t->dropColumn(['name_tr', 'programmname_tr']);
        });
    }
};
