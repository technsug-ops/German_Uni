<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1) Hiyerarşi: self-referencing parent_id
        Schema::table('categories', function (Blueprint $table) {
            if (! Schema::hasColumn('categories', 'parent_id')) {
                $table->foreignId('parent_id')->nullable()->after('id')
                    ->constrained('categories')->nullOnDelete();
            }
        });

        // 2) Duplikasyonları birleştir: [kaynak slug => hedef slug]
        $merges = [
            'basvuru-surecleri' => 'basvuru',   // Başvuru Süreçleri → Basvuru
            'dil-sinavlari'     => 'dil',        // Dil Sınavları → Dil
            'ogrenci-hayati'    => 'yasam',      // Öğrenci Hayatı → Yasam
        ];

        foreach ($merges as $fromSlug => $toSlug) {
            $from = DB::table('categories')->where('slug', $fromSlug)->first();
            $to   = DB::table('categories')->where('slug', $toSlug)->first();
            if (! $from || ! $to) continue;

            // Postları hedefe taşı
            DB::table('posts')->where('category_id', $from->id)->update(['category_id' => $to->id]);
            // Kaynağı sil
            DB::table('categories')->where('id', $from->id)->delete();
        }

        // 3) Kalan kategorileri düzgün isim + sıra ile güncelle
        $finals = [
            'basvuru'          => ['name' => 'Başvuru',           'sort_order' => 1, 'color' => '#6366f1', 'description' => 'Üniversite başvuru süreci, uni-assist, APS, belgeler.'],
            'almanyada-egitim' => ['name' => 'Almanya\'da Eğitim', 'sort_order' => 2, 'color' => '#0ea5e9', 'description' => 'Üniversite sistemi, programlar, Studienkolleg, akademik hayat.'],
            'dil'              => ['name' => 'Dil & Sınavlar',     'sort_order' => 3, 'color' => '#8b5cf6', 'description' => 'TestDaF, DSH, telc, Goethe — Almanca/İngilizce dil sınavları.'],
            'vize'             => ['name' => 'Vize',               'sort_order' => 4, 'color' => '#ef4444', 'description' => 'Öğrenci vizesi, randevu, Sperrkonto, konsolosluk süreci.'],
            'finans'           => ['name' => 'Finans',             'sort_order' => 5, 'color' => '#f59e0b', 'description' => 'Bütçe, burslar, çalışma izni, yaşam maliyeti.'],
            'yasam'            => ['name' => 'Öğrenci Hayatı',     'sort_order' => 6, 'color' => '#10b981', 'description' => 'Konaklama, Anmeldung, günlük yaşam, şehir hayatı.'],
        ];

        foreach ($finals as $slug => $data) {
            DB::table('categories')->where('slug', $slug)->update($data + ['updated_at' => now()]);
        }
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            if (Schema::hasColumn('categories', 'parent_id')) {
                $table->dropForeign(['parent_id']);
                $table->dropColumn('parent_id');
            }
        });
        // Birleştirme geri alınamaz (post taşıma kalıcı) — kasıtlı.
    }
};
