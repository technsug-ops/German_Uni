<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Çalışan ama menüde olmayan 4 tool'u mega menüye ekler (kullanıcı onayıyla):
 * Vize Randevusu (iData), Studienkolleg, Rota Bulucu, Bana İlham Ver.
 * (Mesleki Denklik kasıtlı dışarıda — kapsam dışı, ayrı site.)
 * Tek tek idempotent; menü admin-yönetimli olduğu için tam seeder çalıştırmıyoruz.
 * Eloquent yerine DB::table (data migration Scout kuralı).
 */
return new class extends Migration
{
    private array $rows = [
        ['key' => 'tools.visa-appointment', 'label' => 'Vize Randevusu',   'label_en' => 'Visa Appointment', 'label_de' => 'Visumtermin',   'icon' => '🛂', 'description' => 'iData randevu rehberi',     'badge' => 'YENİ', 'sort_order' => 52],
        ['key' => 'tools.studienkolleg',    'label' => 'Studienkolleg',     'label_en' => 'Studienkolleg',    'label_de' => 'Studienkolleg', 'icon' => '🏫', 'description' => 'T/M/W/G/S kurs eşleştirme', 'badge' => null,   'sort_order' => 67],
        ['key' => 'tools.pathway-finder',   'label' => 'Rota Bulucu',       'label_en' => 'Pathway Finder',   'label_de' => 'Weg-Finder',    'icon' => '🗺️', 'description' => 'Sana en uygun yol',         'badge' => null,   'sort_order' => 15],
        ['key' => 'tools.inspire-me',       'label' => 'Bana İlham Ver',    'label_en' => 'Inspire Me',       'label_de' => 'Inspirier mich','icon' => '💡', 'description' => 'İlgine göre keşfet',        'badge' => null,   'sort_order' => 17],
    ];

    public function up(): void
    {
        $now = now();

        foreach ($this->rows as $row) {
            $attrs = [
                'label'         => $row['label'],
                'label_en'      => $row['label_en'],
                'label_de'      => $row['label_de'],
                'icon'          => $row['icon'],
                'description'   => $row['description'],
                'badge'         => $row['badge'],
                'group'         => 'araclar',
                'link_type'     => 'route',
                'is_enabled'    => true,
                'protect_route' => true,
                'sort_order'    => $row['sort_order'],
                'updated_at'    => $now,
            ];

            if (DB::table('menu_pages')->where('key', $row['key'])->exists()) {
                DB::table('menu_pages')->where('key', $row['key'])->update($attrs);
            } else {
                DB::table('menu_pages')->insert($attrs + ['key' => $row['key'], 'created_at' => $now]);
            }
        }

        $this->flushMenuCache();
    }

    public function down(): void
    {
        DB::table('menu_pages')->whereIn('key', array_column($this->rows, 'key'))->delete();
        $this->flushMenuCache();
    }

    private function flushMenuCache(): void
    {
        if (method_exists(\App\Models\MenuPage::class, 'flushCache')) {
            \App\Models\MenuPage::flushCache();
        }
    }
};
