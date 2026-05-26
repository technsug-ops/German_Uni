<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Models\EventCategory;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

/**
 * Plan 1'deki 7 kategori × ~5 etkinlik = 34 template'i DB'ye taslak olarak ekler.
 * Admin sonra her birini açıp tarih/sponsor doldurup yayına alır.
 */
class EventsSeedTemplates extends Command
{
    protected $signature = 'events:seed-templates {--force : Var olan template\'leri sil yeniden oluştur}';
    protected $description = 'Plan 1\'deki 34 event template\'ini admin için taslak olarak ekle';

    public function handle(): int
    {
        if ($this->option('force')) {
            Event::where('is_active', false)->where('description_md', 'like', '%TASLAK TEMPLATE%')->delete();
            $this->warn('Var olan template taslakları silindi.');
        }

        $templates = $this->templates();
        $created = 0;

        foreach ($templates as $tpl) {
            $catId = EventCategory::where('slug', Event::TYPES[$tpl['type']]['category'] ?? null)->value('id');

            $slug = Str::slug($tpl['title']) . '-template-' . $tpl['type'];

            if (Event::where('slug', $slug)->exists()) {
                continue;
            }

            Event::create([
                'category_id'    => $catId,
                'type'           => $tpl['type'],
                'title_tr'       => $tpl['title'],
                'slug'           => $slug,
                'description_md' => $tpl['desc'] . "\n\n---\n\n**[TASLAK TEMPLATE]** — Admin: bu taslağı düzenleyip tarih + sponsor + kayıt linki ekleyince yayına alabilirsin. `is_active=true` ve `is_featured=true` (öne çıkarmak istiyorsan).",
                'host'           => $tpl['host'] ?? 'AlmanyaUni',
                'target_audience' => $tpl['audience'] ?? 'all',
                'difficulty'     => $tpl['difficulty'] ?? null,
                'duration_minutes' => $tpl['duration'] ?? null,
                'reward'         => $tpl['reward'] ?? null,
                'tags'           => $tpl['tags'] ?? [],
                'starts_at'      => now()->addMonth(),
                'mode'           => $tpl['mode'] ?? 'online',
                'price_eur'      => 0,
                'is_active'      => false,
                'is_featured'    => false,
                'banner_color'   => Event::TYPES[$tpl['type']]['color'] ?? '#1E40AF',
            ]);
            $created++;
        }

        $this->info("✅ {$created} template oluşturuldu (is_active=false, is_featured=false).");
        $this->info('Filament admin → Etkinlikler → templates listesini görebilir, kopyala/edit ile yayına alabilirsin.');
        return self::SUCCESS;
    }

    private function templates(): array
    {
        return [
            // 🤝 NETWORKING & CAREER
            ['type' => 'founder_dating',      'title' => 'Start-up Founder Speed Dating',          'desc' => 'Girişimcilerle 1-on-1 10 dakikalık sohbet seansları. Network kur, gerçek hikayeler dinle, kendi fikrine geri bildirim al.', 'duration' => 120, 'reward' => 'Mentorship match şansı + LinkedIn bağlantı', 'tags' => ['Networking', 'Startup', 'Founder']],
            ['type' => 'tech_meet',           'title' => 'Tech Giants Meet Students',              'desc' => 'Google, SAP, Zalando mühendisleriyle teknik workshop + Q&A. Şirketin günlük hayatı, açık pozisyonlar, başvuru ipuçları.', 'duration' => 90, 'reward' => 'İş başvuru referral kodu', 'tags' => ['Tech', 'Career', 'Google', 'SAP'], 'sponsor' => 'Google · SAP · Zalando'],
            ['type' => 'internship_showcase', 'title' => 'Internship Showcase 2026',               'desc' => '50+ şirketin staj deneyimi paylaşımı + açık pozisyon ilanları. Staj uygulayan öğrencilerle networking.', 'duration' => 180, 'audience' => 'bachelor', 'reward' => 'Staj başvuru fast-track linki'],
            ['type' => 'case_competition',    'title' => 'Consulting Case Competition',            'desc' => 'McKinsey/BCG tarzı hakemli yarışma. Takım kur, gerçek bir vaka çöz, jüriye sun.', 'duration' => 240, 'difficulty' => 'advanced', 'reward' => '€1.000 ödül + mentoring + sertifika', 'tags' => ['Consulting', 'Strategy', 'Competition']],
            ['type' => 'mentorship_match',    'title' => 'Mentor Eşleşme Etkinliği',               'desc' => 'AlmanyaUni mentor ağıyla canlı eşleşme. Kısa kişisel sohbetler sonrasında en uygun mentörüyle birinci buluşma.', 'duration' => 90, 'reward' => '1 ücretsiz mentoring oturumu'],

            // 🛠️ SKILL DEVELOPMENT
            ['type' => 'bootcamp',            'title' => 'AI & Coding Bootcamp (3 gün)',           'desc' => '3 gün yoğun Python + Machine Learning. Pratik proje, GitHub portfolio, sertifika.', 'duration' => 1440, 'difficulty' => 'intermediate', 'reward' => 'Sertifika + GitHub case study', 'tags' => ['AI', 'Python', 'ML']],
            ['type' => 'sprint',              'title' => 'Product Management Sprint',              'desc' => 'Gerçek bir ürün tasarla, pitch et. Mentörlerle 1-on-1 sprint review.', 'duration' => 480, 'reward' => 'Top 3 pitch için CV referansı', 'tags' => ['Product', 'PM', 'Design']],
            ['type' => 'hackathon',           'title' => 'Design Thinking Hackathon (48 saat)',    'desc' => 'Sosyal bir sorunu 48 saatte çöz. Tasarım + kod + sunum. Sosyal etki + ödül.', 'duration' => 2880, 'reward' => '€2.000 ödül + sponsorlu giriş', 'tags' => ['Design Thinking', 'Social Impact', 'Hackathon']],
            ['type' => 'masterclass',         'title' => 'Public Speaking Master Class',           'desc' => 'CEO veya TEDx konuşmacısı anlatıyor. Topluluk önünde rahat konuşma teknikleri.', 'duration' => 120, 'reward' => 'Konuşma örnek videon eşlik kaydı'],
            ['type' => 'workshop',            'title' => 'Data Science for Beginners',             'desc' => 'Excel\'den Python\'a geçiş. Pandas, görselleştirme, ilk veri seti analizi.', 'duration' => 180, 'difficulty' => 'beginner', 'tags' => ['Data', 'Python', 'Pandas']],

            // 🌍 PEER LEARNING
            ['type' => 'exchange_networking', 'title' => 'Erasmus+ Speed Networking',              'desc' => '200 öğrenci · 15 ülke · açık alan. Hızlı tanışma turları, kahve molası, takip eden parti.', 'duration' => 180, 'mode' => 'offline'],
            ['type' => 'house_party',         'title' => 'International Student House Party',      'desc' => 'Tema gecesi: Latin Night, Asian Festival, Türk Gecesi vb. Live DJ + sosyal alan.', 'duration' => 300, 'mode' => 'offline'],
            ['type' => 'study_group',         'title' => 'Çalışma Grubu Eşleşme Yarışması',        'desc' => 'Aynı dersi alan öğrencileri tanıştıran gamified etkinlik. Toplam çalışma saati toplama yarışı.', 'duration' => 60, 'audience' => 'bachelor'],
            ['type' => 'cultural_night',      'title' => 'Kültür Gecesi + Yemek',                  'desc' => 'Öğrenciler kendi ülkesinden yemek/dans/müzik sunuyor. Açık katılım.', 'duration' => 240, 'mode' => 'offline'],
            ['type' => 'city_exploration',    'title' => 'Şehir Keşif Macerası (Scavenger Hunt)',   'desc' => 'Şehri keşfet, ipuçları çöz, sosyalleş. Takımlar halinde. Sonu pizza partisi.', 'duration' => 240, 'mode' => 'offline'],

            // 🧠 PERSONAL GROWTH
            ['type' => 'entrepreneurship',    'title' => 'Entrepreneurship Bootcamp (1 ay)',       'desc' => 'Kendi işini kur. Idea validation, MVP, pitch, hukuki temeller. Hafta sonu intensive.', 'duration' => 2880, 'difficulty' => 'intermediate', 'reward' => 'En iyi pitch için seed fund teklifi'],
            ['type' => 'wellbeing_retreat',   'title' => 'Productivity & Wellbeing Weekend Retreat', 'desc' => 'Mental health + time management + yoga. Hafta sonu kampı, doğa, mindfulness.', 'duration' => 2880, 'mode' => 'offline'],
            ['type' => 'goal_setting',        'title' => 'Hedef Belirleme Workshop\'u',             'desc' => 'Life coach eşliğinde stratejik planlama. Accountability partner eşleşmesi.', 'duration' => 180],
            ['type' => 'book_club',           'title' => 'Kitap Kulübü + Yazar Q&A',               'desc' => 'Success, motivation, startup hikayeleri. Aylık kitap, canlı yazar konuğu.', 'duration' => 90],
            ['type' => 'finance_workshop',    'title' => 'Personal Finance for Students',          'desc' => 'Tasarruf, basit yatırım, vergi (Steuererklärung), Sperrkonto sonrası bütçe.', 'duration' => 120, 'tags' => ['Finans', 'Vergi', 'Bütçe']],

            // 🏔️ ADVENTURE
            ['type' => 'climbing_trip',       'title' => 'Alp Dağları Hafta Sonu Tırmanışı',       'desc' => 'Grup tırmanış + kamp. Adrenalin + networking. Acemi dostu rota.', 'duration' => 2880, 'mode' => 'offline'],
            ['type' => 'startup_retreat',     'title' => 'Startup Retreat + Doğa',                 'desc' => 'Pitch practice + raftıng/yürüyüş. İş + doğa kombinasyonu.', 'duration' => 1440, 'mode' => 'offline'],
            ['type' => 'conference_roadtrip', 'title' => 'Konferans Road Trip (3 şehir)',          'desc' => '3 şehir · 3 konferans · 1 buddy. Yol arkadaşlarıyla networking + öğrenim.', 'duration' => 4320, 'mode' => 'offline'],
            ['type' => 'esports',             'title' => 'Esports Turnuva — LoL / Valorant',       'desc' => 'Takım turnuvası + ödül havuzu. Online katılım. Hayran ekran paylaşımı.', 'duration' => 360, 'reward' => 'Top 3 takım için para ödülü + ekipman'],
            ['type' => 'film_night',          'title' => 'Belgesel Gecesi + Panel',                'desc' => 'Startup/tech belgeseli izleme + sonrasında uzman panel. Açık tartışma.', 'duration' => 180],

            // 🏭 INDUSTRY IMMERSION
            ['type' => 'factory_tour',        'title' => 'Siemens / Bosch Fabrika Gezisi',         'desc' => 'Üretim hattı + mühendislik workshop. Şirket içi bir gün.', 'duration' => 480, 'mode' => 'offline', 'sponsor' => 'Siemens / Bosch'],
            ['type' => 'banking_day',         'title' => 'Banking & Finance Career Day',           'desc' => 'Deutsche Bank, Commerzbank + 5 fintech sunumları. Açık pozisyon listesi.', 'duration' => 360, 'sponsor' => 'Deutsche Bank · Commerzbank'],
            ['type' => 'studio_visit',        'title' => 'Game Dev Studio Ziyareti (Ubisoft/Crytek)', 'desc' => 'Stüdyo arkasını keşfet + gamedev workshop. Açık pozisyonlar.', 'duration' => 360, 'mode' => 'offline'],
            ['type' => 'architecture_vr',     'title' => 'Mimari + AI + VR Deneyimi',              'desc' => 'Şehri tasarla, VR\'da gez. Mimarlık + teknoloji kesişimi.', 'duration' => 240],
            ['type' => 'media_house',         'title' => 'Medya Evi Arka Plan (ARD/ProSiebenSat.1)', 'desc' => 'TV/podcast prodüksiyon stüdyosu turu + content creation workshop.', 'duration' => 240, 'mode' => 'offline'],

            // 🎤 SPECIAL FORMAT
            ['type' => 'ted_talk',            'title' => 'AlmanyaUni TED-Style Talks',             'desc' => '20-30 yaş genç founder/yatırımcı 5 konuşmacı, 15 dk her biri. Tartışma + Q&A.', 'duration' => 120, 'reward' => 'Video kaydı LinkedIn paylaşımı için'],
            ['type' => 'popup_festival',      'title' => 'AlmanyaUni Pop-up Festival (3 gün)',      'desc' => 'Workshops + müzik + food. 3 gün immersive deneyim. Tomorrowland tarzı organizasyon.', 'duration' => 4320, 'mode' => 'offline'],
            ['type' => 'film_festival',       'title' => 'Öğrenci Film Festivali',                  'desc' => 'Öğrencilerin filmleri, kırmızı halı, networking. Jüri ödülü + halk oylaması.', 'duration' => 360, 'mode' => 'offline', 'reward' => 'En iyi film için €5.000 + festival sertifikası'],
            ['type' => 'pitch_competition',   'title' => 'Pitch Competition Grand Final',           'desc' => 'Ödül havuzu €100.000+. En iyi 10 startup pitch eder, jüri yatırımcı seçer.', 'duration' => 480, 'reward' => '€100.000+ ödül havuzu + yatırımcı tanıtımı'],
            ['type' => 'virtual_summit',      'title' => 'Global Virtual Summit',                   'desc' => 'Remote, 500+ uluslararası öğrenci. Time-zone friendly. Workshop + networking room.', 'duration' => 480, 'mode' => 'online'],
            ['type' => 'ama_session',         'title' => 'AlmanyaUni AMA: Başarılı Mezun',          'desc' => 'Almanya\'da kariyer kuran mezunlarımız soru-cevap. Pratik tavsiyeler, başarı hikayeleri.', 'duration' => 90, 'mode' => 'online'],
        ];
    }
}
