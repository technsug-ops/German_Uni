<?php

namespace Database\Seeders;

use App\Models\FieldOfStudy;
use Illuminate\Database\Seeder;

class FieldOfStudySeeder extends Seeder
{
    /**
     * Hochschulkompass'ın 9 ana Fächergruppen (alan grubu) sınıflandırması.
     */
    public function run(): void
    {
        $rows = [
            ['muhendislik',           'Mühendislik',                       'Ingenieurwissenschaften',            'Engineering',                       '🔧', '#1E40AF', 1],
            ['bilisim',               'Bilişim / Bilgisayar',              'Informatik',                         'Computer Science',                  '💻', '#0EA5E9', 2],
            ['matematik-doga',        'Matematik &amp; Doğa Bilimleri',    'Mathematik, Naturwissenschaften',    'Mathematics &amp; Natural Sciences','🧪', '#10B981', 3],
            ['tip-saglik',            'Tıp &amp; Sağlık',                  'Medizin, Gesundheitswissenschaften', 'Medicine &amp; Health',             '🏥', '#EF4444', 4],
            ['hukuk-ekonomi',         'Hukuk &amp; Ekonomi',               'Rechts-, Wirtschaftswissenschaften', 'Law &amp; Economics',               '⚖️', '#F59E0B', 5],
            ['sosyal-bilimler',       'Sosyal Bilimler',                   'Sozialwissenschaften',               'Social Sciences',                   '👥', '#8B5CF6', 6],
            ['sanat-tasarim',         'Sanat &amp; Tasarım',               'Kunst, Kunstwissenschaft',           'Art &amp; Design',                  '🎨', '#EC4899', 7],
            ['dil-kultur',            'Dil &amp; Kültür',                  'Sprach- und Kulturwissenschaften',   'Languages &amp; Cultural Studies',   '📚', '#06B6D4', 8],
            ['tarim-ormancilik',      'Tarım, Ormancılık, Beslenme',       'Agrar-, Forst- und Ernährungswiss.', 'Agriculture &amp; Forestry',        '🌾', '#84CC16', 9],
            ['veteriner-spor',        'Veterinerlik &amp; Spor Bilimleri', 'Veterinärmedizin, Sport',            'Veterinary &amp; Sport Sciences',   '🐾', '#A855F7', 10],
        ];

        foreach ($rows as $r) {
            FieldOfStudy::updateOrCreate(
                ['slug' => $r[0]],
                [
                    'name_tr'    => html_entity_decode($r[1]),
                    'name_de'    => html_entity_decode($r[2]),
                    'name_en'    => html_entity_decode($r[3]),
                    'icon'       => $r[4],
                    'color'      => $r[5],
                    'sort_order' => $r[6],
                    'is_active'  => true,
                ]
            );
        }

        $this->command?->info('FieldOfStudy seeded: ' . FieldOfStudy::count() . ' rows.');
    }
}
