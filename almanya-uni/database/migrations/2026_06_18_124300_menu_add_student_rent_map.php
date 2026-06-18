<?php

use App\Models\MenuPage;
use Illuminate\Database\Migrations\Migration;

/** Nav "Araçlar" menüsüne Öğrenci Kira Haritası girişi. */
return new class extends Migration
{
    public function up(): void
    {
        MenuPage::updateOrCreate(
            ['key' => 'map.rents'],
            [
                'link_type' => 'route',
                'url' => null,
                'label' => 'Kira Haritası',
                'label_tr' => 'Kira Haritası',
                'label_en' => 'Student Rent Map',
                'label_de' => 'Mietkarte',
                'icon' => '💶',
                'description' => 'Şehir şehir öğrenci kirası',
                'description_tr' => 'Şehir şehir öğrenci kirası',
                'description_en' => 'Student rent by city',
                'description_de' => 'Studentenmiete nach Stadt',
                'group' => 'araclar',
                'is_enabled' => true,
                'sort_order' => 35,
            ]
        );
    }

    public function down(): void
    {
        MenuPage::where('key', 'map.rents')->delete();
    }
};
