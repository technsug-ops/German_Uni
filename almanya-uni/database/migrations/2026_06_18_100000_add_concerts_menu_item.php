<?php

use App\Models\MenuPage;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

/**
 * "Konserler & Kültür" nav öğesi (Fırsatlar menüsü) → /events/concerts.
 * Dış konserler kendi etkinliklerimizden ayrı sayfaya taşındı.
 * Model üzerinden yazılır → menü cache otomatik invalidate olur. Idempotent (key+group).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('menu_pages')) {
            return;
        }

        MenuPage::updateOrCreate(
            ['key' => 'events.concerts', 'group' => 'firsatlar'],
            [
                'link_type'      => 'route',
                'label'          => 'Konserler & Kültür',
                'label_tr'       => 'Konserler & Kültür',
                'label_en'       => 'Concerts & Culture',
                'label_de'       => 'Konzerte & Kultur',
                'description'    => 'Almanya genelinde konser, tiyatro, komedi',
                'description_tr' => 'Almanya genelinde konser, tiyatro, komedi',
                'description_en' => 'Concerts, theatre & comedy across Germany',
                'description_de' => 'Konzerte, Theater & Comedy in ganz Deutschland',
                'icon'           => '🎵',
                'badge'          => null,
                'is_enabled'     => true,
                'sort_order'     => 35, // Etkinlikler (30) ile Mentorlar (40) arası
            ]
        );
    }

    public function down(): void
    {
        if (Schema::hasTable('menu_pages')) {
            MenuPage::where('key', 'events.concerts')->where('group', 'firsatlar')->delete();
        }
    }
};
