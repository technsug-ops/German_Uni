<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * FAQ alt-konu kategorisi için elle override alanı. Boşsa FaqCategorizer
 * anahtar-kelime heuristiğine düşer; doluysa admin'in seçtiği kazanır
 * (sınır soruları düzeltmek için). Değer = FaqCategorizer kategori anahtarı.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('faqs', function (Blueprint $table) {
            if (! Schema::hasColumn('faqs', 'category')) {
                $table->string('category')->nullable()->after('intent');
            }
        });
    }

    public function down(): void
    {
        Schema::table('faqs', function (Blueprint $table) {
            if (Schema::hasColumn('faqs', 'category')) {
                $table->dropColumn('category');
            }
        });
    }
};
