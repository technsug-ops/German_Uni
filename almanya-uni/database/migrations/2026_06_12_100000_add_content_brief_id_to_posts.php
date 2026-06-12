<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * #12 Faz-1 (on-site storytelling): blog yazısını kaynak ContentBrief'e bağla.
 * Böylece bir yazının sayfasında, brief'ten üretilmiş çok-formatlı asset'leri
 * (infografik, podcast, vb.) post'un locale'inde gösterebiliriz.
 * Migration ile oluşturduğum 15 yazıyı (translation_group → brief slug) linkler.
 */
return new class extends Migration
{
    private array $map = [
        'a1b2c3d4-0001-4000-8000-000000000001' => 'almanca-ogrenme-yol-haritasi-testdaf-dsh',
        'a1b2c3d4-0002-4000-8000-000000000002' => 'anmeldung-adim-adim-sehir-kaydi-burgeramt',
        'a1b2c3d4-0003-4000-8000-000000000003' => 'ogrenci-vizesi-gorusmesi-sorular-hazirlik',
        'a1b2c3d4-0004-4000-8000-000000000004' => 'bafog-nedir-uluslararasi-ogrenci-sartlar-basvuru',
        'a1b2c3d4-0005-4000-8000-000000000005' => 'almanya-randevu-rehberi-konsolosluk-burgeramt',
    ];

    public function up(): void
    {
        if (! Schema::hasColumn('posts', 'content_brief_id')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->foreignId('content_brief_id')->nullable()->after('category_id')
                    ->constrained('content_briefs')->nullOnDelete();
            });
        }

        foreach ($this->map as $group => $briefSlug) {
            $briefId = DB::table('content_briefs')->where('slug', $briefSlug)->value('id');
            if ($briefId) {
                DB::table('posts')->where('translation_group_id', $group)
                    ->update(['content_brief_id' => $briefId]);
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('posts', 'content_brief_id')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->dropConstrainedForeignId('content_brief_id');
            });
        }
    }
};
