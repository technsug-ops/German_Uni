<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Sağlık Sigortası tool'u (tools.health-insurance) mega menüye eklenmemişti
 * (route + sayfa vardı ama menu_pages'te kaydı yoktu → kullanıcı bulamıyordu).
 * Tek satır idempotent ekleme; menü admin-yönetimli olduğu için tam seeder
 * çalıştırmıyoruz (admin'in düzenlediği etiketleri ezmesin). Eloquent yerine
 * DB::table (data migration Scout kuralı).
 */
return new class extends Migration
{
    private string $key = 'tools.health-insurance';

    public function up(): void
    {
        $now = now();
        $attrs = [
            'label'         => 'Sağlık Sigortası',
            'label_en'      => 'Health Insurance',
            'label_de'      => 'Krankenversicherung',
            'icon'          => '🩺',
            'description'   => 'GKV / PKV / expat karşılaştır',
            'badge'         => 'YENİ',
            'group'         => 'araclar',
            'link_type'     => 'route',
            'is_enabled'    => true,
            'protect_route' => true,
            'sort_order'    => 65,
            'updated_at'    => $now,
        ];

        if (DB::table('menu_pages')->where('key', $this->key)->exists()) {
            DB::table('menu_pages')->where('key', $this->key)->update($attrs);
        } else {
            DB::table('menu_pages')->insert($attrs + ['key' => $this->key, 'created_at' => $now]);
        }

        $this->flushMenuCache();
    }

    public function down(): void
    {
        DB::table('menu_pages')->where('key', $this->key)->delete();
        $this->flushMenuCache();
    }

    private function flushMenuCache(): void
    {
        if (method_exists(\App\Models\MenuPage::class, 'flushCache')) {
            \App\Models\MenuPage::flushCache();
        }
    }
};
