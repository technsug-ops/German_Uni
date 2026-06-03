<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Partner menü öğeleri (Dil Kursları + Yeminli Tercüme) EN/DE'de Türkçe sızıyordu —
 * label_en/label_de boştu. Dile özel label + description set edilir + menü cache flush.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('menu_pages')) {
            return;
        }

        $rows = [
            'language-courses.index' => [
                'label_tr' => 'Dil Kursları',   'label_en' => 'Language Courses', 'label_de' => 'Sprachkurse',
                'description_tr' => 'Üniversite + özel + online',
                'description_en' => 'University + private + online',
                'description_de' => 'Universität + privat + online',
            ],
            'translation-offices.index' => [
                'label_tr' => 'Yeminli Tercüme', 'label_en' => 'Sworn Translation', 'label_de' => 'Beeidigte Übersetzung',
                'description_tr' => 'Diploma & belge çevirisi',
                'description_en' => 'Diploma & document translation',
                'description_de' => 'Zeugnis- & Dokumentenübersetzung',
            ],
        ];

        foreach ($rows as $key => $data) {
            DB::table('menu_pages')->where('key', $key)->update($data + ['updated_at' => now()]);
        }

        // Menü cache'ini temizle (MenuPage::CACHE_KEY) → değişiklik hemen görünür.
        Cache::forget(\App\Models\MenuPage::CACHE_KEY);
    }

    public function down(): void
    {
        // Geri alma gereksiz (dil düzeltmesi).
    }
};
