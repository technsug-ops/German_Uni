<?php

use App\Models\MenuPage;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * /tr menüsünde Almanca sızıntı (journey.show "Meine Bewerbungsreise",
 * study.germany "Studium in Deutschland"). Sebep: label_tr boş → __() fallback
 * prod'da Almanca'ya düşüyordu. Çözüm: 3 dilin de label/description'unu AÇIKÇA
 * yaz → accessor json'a bağımlı kalmaz, her dilde doğru.
 */
return new class extends Migration
{
    private const ROWS = [
        'journey.show' => [
            'label_tr' => 'Başvuru Yolculuğum', 'label_en' => 'My Application Journey', 'label_de' => 'Meine Bewerbungsreise',
            'description_tr' => '8 adımda Almanya', 'description_en' => '8 steps to Germany', 'description_de' => '8 Schritte nach Deutschland',
        ],
        'study.germany' => [
            'label_tr' => 'Almanya\'da Eğitim', 'label_en' => 'Studying in Germany', 'label_de' => 'Studium in Deutschland',
            'description_tr' => 'Neden Almanya, şehir-endüstri haritası', 'description_en' => 'Why Germany, city-industry map', 'description_de' => 'Warum Deutschland, Stadt-Industrie-Karte',
        ],
    ];

    public function up(): void
    {
        if (! Schema::hasTable('menu_pages') || ! Schema::hasColumn('menu_pages', 'label_tr')) {
            return;
        }
        foreach (self::ROWS as $key => $vals) {
            DB::table('menu_pages')->where('key', $key)->update($vals);
        }
        if (class_exists(MenuPage::class)) {
            MenuPage::flushCache();
        }
    }

    public function down(): void
    {
        // geri alma: açık çevirileri temizle (json fallback'e döner)
        if (Schema::hasTable('menu_pages') && Schema::hasColumn('menu_pages', 'label_tr')) {
            foreach (array_keys(self::ROWS) as $key) {
                DB::table('menu_pages')->where('key', $key)->update([
                    'label_tr' => null, 'label_en' => null, 'label_de' => null,
                    'description_tr' => null, 'description_en' => null, 'description_de' => null,
                ]);
            }
            if (class_exists(MenuPage::class)) MenuPage::flushCache();
        }
    }
};
