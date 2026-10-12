<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * slug_redirects'e dil sütunu: FAQ slug'ları dile özgüdür (unique(locale, slug)). Dil bilinmeden zincir izleme,
 * TR bir eski slug'ı EN slug'ına taşıyabiliyordu (eski EN slug'ı = yeni TR slug'ı olan iki grup). Faz 2 kayıtları
 * hedef slug'ın gerçek diliyle doldurulur; diğer entity'ler dil bağımsız (NULL) kalır.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('slug_redirects') || Schema::hasColumn('slug_redirects', 'locale')) {
            return;
        }
        Schema::table('slug_redirects', function (Blueprint $table) {
            $table->string('locale', 5)->nullable()->after('entity');
            $table->dropUnique(['entity', 'from_slug']);
            $table->unique(['entity', 'from_slug', 'locale']);
        });
        if (! Schema::hasTable('faqs')) {
            return;
        }
        // Backfill: faq yönlendirmelerinde hedef slug'ın dili
        DB::table('slug_redirects')->where('entity', 'faq')->orderBy('id')->chunkById(500, function ($rows) {
            foreach ($rows as $r) {
                $locales = DB::table('faqs')->where('slug', $r->to_slug)->pluck('locale')->unique()->values();
                if ($locales->count() === 1) {
                    DB::table('slug_redirects')->where('id', $r->id)->update(['locale' => $locales->first()]);
                }
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('slug_redirects', 'locale')) {
            return;
        }
        Schema::table('slug_redirects', function (Blueprint $table) {
            $table->dropUnique(['entity', 'from_slug', 'locale']);
            $table->dropColumn('locale');
            $table->unique(['entity', 'from_slug']);
        });
    }
};
