<?php

namespace Database\Seeders;

use App\Models\MenuPage;
use Illuminate\Database\Seeder;

class MenuPageSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            // ========== KEŞFET ==========
            ['key' => 'universities.index', 'label' => 'Üniversiteler', 'icon' => '🎓', 'description' => 'Tüm Alman üniversiteleri', 'group' => 'kesfet', 'sort_order' => 10],
            ['key' => 'programs.index',     'label' => 'Programlar',    'icon' => '📚', 'description' => 'Lisans + yüksek lisans', 'group' => 'kesfet', 'sort_order' => 20],
            ['key' => 'cities.index',       'label' => 'Şehirler',      'icon' => '🏙️', 'description' => 'Öğrenci şehirleri', 'group' => 'kesfet', 'sort_order' => 30],
            ['key' => 'states.index',       'label' => 'Eyaletler',     'icon' => '🗺️', 'description' => '16 federal eyalet', 'group' => 'kesfet', 'sort_order' => 40],
            ['key' => 'fields.index',       'label' => 'Alanlar',       'icon' => '🎯', 'description' => 'Akademik alanlar', 'group' => 'kesfet', 'sort_order' => 50],
            ['key' => 'professions.index',  'label' => 'Meslekler',     'icon' => '💼', 'description' => 'Meslek tanımları', 'group' => 'kesfet', 'sort_order' => 60],
            ['key' => 'map.index',          'label' => 'Harita',        'icon' => '📍', 'description' => 'İnteraktif harita', 'group' => 'kesfet', 'sort_order' => 70],
            ['key' => 'rankings.index',     'label' => 'Sıralamalar',   'icon' => '📊', 'description' => 'Boyut & prestij', 'group' => 'kesfet', 'sort_order' => 80],
            ['key' => 'compare.index',      'label' => 'Karşılaştır',   'icon' => '⚖️', 'description' => '2-4 üniyi yan yana', 'group' => 'kesfet', 'sort_order' => 90],

            // ========== ARAÇLAR ==========
            ['key' => 'tools.recommendation',  'label' => 'Üni Önerisi Quiz\'i', 'icon' => '🎯', 'description' => '8 soruda eşleşme', 'group' => 'araclar', 'sort_order' => 10],
            ['key' => 'tools.career-compass',  'label' => 'Kariyer Pusulası',    'icon' => '🧭', 'description' => 'Yetenek + meslek', 'badge' => 'YENİ', 'group' => 'araclar', 'sort_order' => 20],
            ['key' => 'tools.cost-of-living',  'label' => 'Yaşam Maliyeti',      'icon' => '💰', 'description' => 'Şehir bazlı gider', 'group' => 'araclar', 'sort_order' => 30],
            ['key' => 'tools.budget-planner',  'label' => 'Bütçe Planlayıcı',    'icon' => '📈', 'description' => 'Gelir-gider dengesi', 'group' => 'araclar', 'sort_order' => 40],
            ['key' => 'tools.visa-cost',       'label' => 'Vize Maliyeti',       'icon' => '💸', 'description' => 'Tüm masraf kalemleri', 'group' => 'araclar', 'sort_order' => 50],
            ['key' => 'tools.blocked-account', 'label' => 'Bloke Hesap Bulucu',  'icon' => '🏦', 'description' => 'Sperrkonto karşılaştır', 'badge' => 'YENİ', 'group' => 'araclar', 'sort_order' => 60],
            ['key' => 'tools.health-insurance', 'label' => 'Sağlık Sigortası', 'label_en' => 'Health Insurance', 'label_de' => 'Krankenversicherung', 'icon' => '🩺', 'description' => 'GKV / PKV / expat karşılaştır', 'description_en' => 'GKV / PKV / expat comparison', 'description_de' => 'GKV / PKV / Expat-Vergleich', 'badge' => 'YENİ', 'group' => 'araclar', 'sort_order' => 65],
            ['key' => 'tools.visa-appointment', 'label' => 'Vize Randevusu', 'label_en' => 'Visa Appointment', 'label_de' => 'Visumtermin', 'icon' => '🛂', 'description' => 'iData randevu rehberi', 'description_en' => 'iData appointment guide', 'description_de' => 'iData-Terminleitfaden', 'badge' => 'YENİ', 'group' => 'araclar', 'sort_order' => 52],
            ['key' => 'tools.studienkolleg',   'label' => 'Studienkolleg', 'label_en' => 'Studienkolleg', 'label_de' => 'Studienkolleg', 'icon' => '🏫', 'description' => 'T/M/W/G/S kurs eşleştirme', 'description_en' => 'T/M/W/G/S course matching', 'description_de' => 'T/M/W/G/S-Kurs-Zuordnung', 'group' => 'araclar', 'sort_order' => 67],
            ['key' => 'tools.deadlines',       'label' => 'Başvuru Takvimi',     'icon' => '📅', 'description' => 'Deadline + takvim', 'group' => 'araclar', 'sort_order' => 70],
            ['key' => 'tools.pathway-finder',  'label' => 'Rota Bulucu', 'label_en' => 'Pathway Finder', 'label_de' => 'Weg-Finder', 'icon' => '🗺️', 'description' => 'Sana en uygun yol', 'description_en' => 'Your best route', 'description_de' => 'Dein bester Weg', 'group' => 'araclar', 'sort_order' => 15],
            ['key' => 'tools.inspire-me',      'label' => 'Bana İlham Ver', 'label_en' => 'Inspire Me', 'label_de' => 'Inspirier mich', 'icon' => '💡', 'description' => 'İlgine göre keşfet', 'description_en' => 'Discover by interest', 'description_de' => 'Nach Interesse entdecken', 'group' => 'araclar', 'sort_order' => 17],
            ['key' => 'tools.grade-converter', 'label' => 'Not Dönüştürücü',     'icon' => '📊', 'description' => 'TR → Alman 1-5', 'group' => 'araclar', 'sort_order' => 80],
            ['key' => 'housing.index',         'label' => 'Ev / Yurt Rehberi',   'icon' => '🏠', 'description' => 'Konaklama bulma', 'group' => 'araclar', 'sort_order' => 90],
            ['key' => 'tools.index',           'label' => 'Tüm Araçlar',         'icon' => '🛠️', 'description' => 'Tool listesi', 'group' => 'araclar', 'sort_order' => 999],

            // ========== FIRSATLAR ==========
            ['key' => 'scholarships.index', 'label' => 'Tüm Burslar',   'icon' => '🎓', 'description' => '166 burs programı', 'group' => 'firsatlar', 'sort_order' => 10],
            ['key' => 'scholarships.daad',  'label' => 'DAAD Bursları', 'icon' => '🇩🇪', 'description' => 'Devlet bursları', 'group' => 'firsatlar', 'sort_order' => 20],
            ['key' => 'events.index',       'label' => 'Etkinlikler',   'icon' => '📅', 'description' => 'Webinar · workshop · panel', 'badge' => 'YENİ', 'group' => 'firsatlar', 'sort_order' => 30],
            ['key' => 'mentors.index',      'label' => 'Mentorlar',     'icon' => '🤝', 'description' => 'Mezunlarla 1-on-1', 'badge' => 'YENİ', 'group' => 'firsatlar', 'sort_order' => 40],

            // ========== İÇERİK ==========
            ['key' => 'study.germany', 'label' => 'Almanya\'da Eğitim',   'icon' => '🇩🇪', 'description' => 'Neden Almanya, şehir-endüstri haritası', 'badge' => 'YENİ', 'group' => 'icerik', 'sort_order' => 5],
            ['key' => 'blog.index', 'label' => 'Blog',                   'icon' => '📝', 'description' => 'Rehber yazıları', 'group' => 'icerik', 'sort_order' => 10],
            ['key' => 'faqs.index', 'label' => 'Sıkça Sorulan Sorular',  'icon' => '❓', 'description' => '269 cevaplı soru', 'group' => 'icerik', 'sort_order' => 20],
            ['key' => 'about',      'label' => 'Biz Kimiz',              'icon' => 'ℹ️', 'description' => 'AlmanyaUni hakkında', 'group' => 'icerik', 'sort_order' => 30],
            ['key' => 'team',       'label' => 'Ekip & Yazarlar',        'icon' => '👥', 'description' => 'Kurucu · editör · katkıcı', 'group' => 'icerik', 'sort_order' => 40],

            // ========== STANDALONE (Forum, vb.) ==========
            ['key' => 'forum', 'link_type' => 'url', 'url' => '/forum/', 'label' => 'Forum', 'icon' => '💬', 'description' => 'Topluluk forumu', 'group' => 'standalone', 'protect_route' => false, 'sort_order' => 10],
        ];

        foreach ($items as $data) {
            $data['link_type'] = $data['link_type'] ?? 'route';
            $data['is_enabled'] = $data['is_enabled'] ?? true;
            $data['protect_route'] = $data['protect_route'] ?? true;

            MenuPage::updateOrCreate(
                ['key' => $data['key']],
                $data
            );
        }

        MenuPage::flushCache();
    }
}
