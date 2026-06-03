<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

/**
 * "Almanya'da Yaşam & Kültür" üst (top-level) blog kategorisi.
 * Kültür/yaşam içerikleri (Direktheit, Lüften, Ruhezeit, Pfand, Biergarten...) için
 * tek çatı. parent_id=null → üst başlık; ileride alt kategoriler eklenebilir.
 * Çok dilli (name_tr/en/de), English-canonical slug. Idempotent (slug).
 */
return new class extends Migration
{
    private const SLUG = 'german-life-culture';

    public function up(): void
    {
        if (! Schema::hasTable('categories')) return;

        $data = [
            'name'       => 'Almanya\'da Yaşam & Kültür', // taban (accessor name_tr/en/de ile dile çözülür)
            'name_tr'    => 'Almanya\'da Yaşam & Kültür',
            'name_en'    => 'Life & Culture in Germany',
            'name_de'    => 'Leben & Kultur in Deutschland',
            'kind'       => 'blog',
            'parent_id'  => null,
            'color'      => '#0d9488',
            'sort_order' => 5,
            'is_active'  => true,
            'updated_at' => now(),
        ];

        if (DB::table('categories')->where('slug', self::SLUG)->exists()) {
            DB::table('categories')->where('slug', self::SLUG)->update($data);
            return;
        }

        DB::table('categories')->insert($data + [
            'slug'       => self::SLUG,
            'created_at' => now(),
        ]);
    }

    public function down(): void
    {
        if (Schema::hasTable('categories')) {
            DB::table('categories')->where('slug', self::SLUG)->delete();
        }
    }
};
