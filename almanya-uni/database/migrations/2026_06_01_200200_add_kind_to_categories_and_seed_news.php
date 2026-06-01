<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Kategorilere `kind` ekler ('blog' | 'news') → blog ve haber kategorileri
 * birbirine karışmaz. 7 haber kategorisini seed eder (3 dil).
 */
return new class extends Migration
{
    private const NEWS_CATEGORIES = [
        ['slug' => 'visa-residence', 'color' => '#2563eb', 'tr' => 'Vize & Oturum',        'en' => 'Visa & Residence',      'de' => 'Visum & Aufenthalt'],
        ['slug' => 'law-policy',     'color' => '#7c3aed', 'tr' => 'Yasa & Mevzuat',        'en' => 'Law & Policy',          'de' => 'Gesetz & Politik'],
        ['slug' => 'universities',   'color' => '#0891b2', 'tr' => 'Üniversite & Araştırma','en' => 'Universities & Research','de' => 'Hochschule & Forschung'],
        ['slug' => 'integration',    'color' => '#16a34a', 'tr' => 'Entegrasyon & Başarı',  'en' => 'Integration & Success', 'de' => 'Integration & Erfolg'],
        ['slug' => 'funding',        'color' => '#ca8a04', 'tr' => 'Burs & Finansman',      'en' => 'Scholarships & Funding','de' => 'Stipendien & Finanzierung'],
        ['slug' => 'practical',      'color' => '#dc2626', 'tr' => 'Pratik & Takvim',       'en' => 'Practical & Deadlines', 'de' => 'Praktisch & Fristen'],
        ['slug' => 'student-life',   'color' => '#db2777', 'tr' => 'Öğrenci Yaşamı & Şehir','en' => 'Student Life & Cities', 'de' => 'Studentenleben & Stadt'],
    ];

    public function up(): void
    {
        if (Schema::hasTable('categories') && ! Schema::hasColumn('categories', 'kind')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->string('kind', 12)->default('blog')->index()->after('slug');
            });
        }

        if (! Schema::hasTable('categories')) return;

        $sort = 100;
        foreach (self::NEWS_CATEGORIES as $c) {
            $exists = DB::table('categories')->where('slug', $c['slug'])->exists();
            if ($exists) {
                DB::table('categories')->where('slug', $c['slug'])->update(['kind' => 'news']);
                continue;
            }
            DB::table('categories')->insert([
                'name'       => $c['tr'],
                'name_tr'    => $c['tr'],
                'name_en'    => $c['en'],
                'name_de'    => $c['de'],
                'slug'       => $c['slug'],
                'kind'       => 'news',
                'color'      => $c['color'],
                'sort_order' => $sort,
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $sort += 10;
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('categories')) {
            DB::table('categories')->whereIn('slug', array_column(self::NEWS_CATEGORIES, 'slug'))->where('kind', 'news')->delete();
            if (Schema::hasColumn('categories', 'kind')) {
                Schema::table('categories', fn (Blueprint $t) => $t->dropColumn('kind'));
            }
        }
    }
};
