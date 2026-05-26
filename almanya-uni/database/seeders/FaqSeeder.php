<?php

namespace Database\Seeders;

use App\Models\Faq;
use App\Models\FaqTopic;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class FaqSeeder extends Seeder
{
    /**
     * Topic catalog. `pool_size` reflects raw question pool from 1.5M message analysis.
     * Slug used in URL. Ausbildung intentionally omitted (vocational, not academic).
     */
    private array $topics = [
        ['name' => 'Vize', 'slug' => 'vize', 'icon' => '🛂', 'color' => '#1E40AF', 'pool_size' => 1537, 'sort_order' => 1,
         'description' => 'Almanya öğrenci vizesi, dil kursu vizesi, Sperrkonto, sigorta ve başvuru süreciyle ilgili sıkça sorulanlar.'],
        ['name' => 'Dil', 'slug' => 'dil', 'icon' => '🗣️', 'color' => '#F97316', 'pool_size' => 1451, 'sort_order' => 2,
         'description' => 'TestDaF, DSH, Goethe sertifikaları, B1/B2/C1 seviye gereklilikleri ve Almanca öğrenme kaynakları.'],
        ['name' => 'Master & PhD', 'slug' => 'master', 'icon' => '🎓', 'color' => '#0EA5E9', 'pool_size' => 1283, 'sort_order' => 3,
         'description' => 'Yüksek lisans ve doktora başvuruları, DAAD bursu, dil şartları ve mezuniyet sonrası iş arama vizesi.'],
        ['name' => 'Uni-Assist', 'slug' => 'uni-assist', 'icon' => '📋', 'color' => '#10B981', 'pool_size' => 572, 'sort_order' => 4,
         'description' => 'Uni-Assist üzerinden başvuru, VPD, HZB notu, ücretler ve sık karşılaşılan red sebepleri.'],
        ['name' => 'Studienkolleg', 'slug' => 'studienkolleg', 'icon' => '🏫', 'color' => '#8B5CF6', 'pool_size' => 626, 'sort_order' => 5,
         'description' => 'Studienkolleg giriş sınavı, M-Kurs/T-Kurs farkı, Feststellungsprüfung ve hazırlık seçenekleri.'],
        ['name' => 'Yurt & Konaklama', 'slug' => 'yurt', 'icon' => '🏠', 'color' => '#EC4899', 'pool_size' => 667, 'sort_order' => 6,
         'description' => 'Studierendenwerk yurtları, WG arama, kira fiyatları, Kaution ve geçici konaklama seçenekleri.'],
        ['name' => 'Para & Finansman', 'slug' => 'para', 'icon' => '💰', 'color' => '#84CC16', 'pool_size' => 1956, 'sort_order' => 7,
         'description' => 'Sperrkonto (bloke hesap), Fintiba alternatifleri, yaşam maliyeti, TR-DE para transferi ve öğrenci bütçesi.'],
        ['name' => 'Şehir & Hayat', 'slug' => 'sehir', 'icon' => '🏙️', 'color' => '#06B6D4', 'pool_size' => 1474, 'sort_order' => 8,
         'description' => 'Hangi şehir öğrenci için uygun, çevirmen önerileri, yaşam standardı ve öğrenci grupları.'],
        ['name' => 'İş & Werkstudent', 'slug' => 'is', 'icon' => '💼', 'color' => '#F59E0B', 'pool_size' => 1751, 'sort_order' => 9,
         'description' => 'Werkstudent, mini-job, 20 saat çalışma izni, mezuniyet sonrası iş arama vizesi ve LinkedIn DE.'],
        ['name' => 'Sigorta', 'slug' => 'sigorta', 'icon' => '🏥', 'color' => '#EF4444', 'pool_size' => 415, 'sort_order' => 10,
         'description' => 'Öğrenci sağlık sigortası (TK, AOK, Barmer), Fintiba paketleri, 30 yaş üstü öğrenciler ve aile sigortası.'],
        ['name' => 'Randevu', 'slug' => 'randevu', 'icon' => '📅', 'color' => '#6366F1', 'pool_size' => 2147, 'sort_order' => 11,
         'description' => 'Konsolosluk, IDATA, Anmeldung randevuları ve ortalama bekleme süreleri.'],
        ['name' => 'Anmeldung', 'slug' => 'anmeldung', 'icon' => '🏛️', 'color' => '#14B8A6', 'pool_size' => 326, 'sort_order' => 12,
         'description' => 'Bürgeramt randevusu, evrak listesi, Wohnungsgeberbestätigung ve 14 gün şartı.'],
        ['name' => 'Burs', 'slug' => 'burs', 'icon' => '🏆', 'color' => '#A855F7', 'pool_size' => 167, 'sort_order' => 13,
         'description' => 'DAAD, Erasmus, Konrad Adenauer ve diğer Almanya öğrenci bursları için başvuru ve şartlar.'],
        ['name' => 'Diploma Denkliği', 'slug' => 'denklik', 'icon' => '📜', 'color' => '#64748B', 'pool_size' => 1532, 'sort_order' => 14,
         'description' => 'Türk lise/üniversite diplomalarının Almanya\'da tanınması, Anabin ve denklik süreçleri.'],
    ];

    /**
     * Curated student-focused questions per topic slug.
     * Filtered from raw 374-item analysis to remove medical-professional questions
     * (Approbation/FSP/Berufserlaubnis/Hospitation/Krankenpflege etc.) and Ausbildung.
     * Slightly rephrased for clarity where needed.
     */
    private array $questions = [
        'vize' => [
            'Almanya öğrenci vizesi nasıl alınır?',
            'Dil kursu vizesi başvurusu nasıl yapılır?',
            'Üniversite kabul belgesi vize başvurusu için yeterli mi?',
            'Ulusal vize başvurusunda dil sertifikası zorunlu mu?',
            'Vize başvurusu sonrası olumlu/olumsuz dönüş ortalama kaç gün sürer?',
            'Fintiba dışında bloke hesap (Sperrkonto) açabileceğim alternatifler nelerdir?',
            'Bloke hesapta ne kadar para olması gerekiyor?',
            'Dil kursu kaydı en az kaç ay olmak zorunda?',
            'Dil kursu vizesinde çocuğa da vize çıkarılır mı?',
            'Vize için istenen sağlık sigortasının kapsamı ne olmalı?',
            'Fintiba Basic ve Plus arasındaki fark nedir, ulusal vize için hangisi yeterli?',
            'Sperrkonto açtırsam bile dil kursu vizesi alabilir miyim?',
            'Ankara konsolosluğundan vize randevusu ortalama kaç haftada alınıyor?',
            'Vize başvurusunda istenen evraklar nelerdir?',
            'Vize başvurum reddedilirse ne yapabilirim?',
            'Almanya öğrenci vizesi kaç yıl geçerli oluyor?',
            'Master kabul belgemle vize başvurusunu Almanya\'dan yapabilir miyim?',
            'Yeşil pasaportla öğrenci vizesi başvurusu nasıl yapılır?',
        ],
        'dil' => [
            'Goethe online kursu nasıl, tavsiye eder misiniz?',
            'TestDaF ile DSH arasındaki fark nedir?',
            'B2 sertifikası ile master başvurusu yapabilir miyim?',
            'Almanca C1 hangi sınav ile kanıtlanır?',
            'IELTS ile Almanya\'da İngilizce master başvurusu yapılabilir mi?',
            'Online ders veren Almanca öğretmeni önerisi var mı?',
            'Goethe sınav tarihleri ne zaman duyurulur?',
            'TestDaF her bölümden en az kaç almak gerekiyor?',
            'B1 ile dil kursu vizesi başvurusu mümkün mü?',
            'Almanca öğrenmek için en hızlı yol nedir?',
            'Goethe B1 sertifikası büyükelçilik tarafından kabul edilir mi?',
            'DSH hangi seviyede yeterli kabul edilir, DSH-2 mi DSH-3 mü?',
            'Online Almanca kursu mu yüz yüze mi daha verimli?',
            'Türkiye\'de TestDaF\'a hazırlanan kurum önerisi var mı?',
            'Almanca dil sınavı ücretleri ne kadar?',
            'Goethe vs Telc sertifikası — uni başvurusu için fark var mı?',
            'B2 için online birebir hoca tavsiye eder misiniz?',
        ],
        'master' => [
            'Almanya\'da ücretsiz master mümkün mü?',
            'Goethe B2 ile şartsız master kabulü alabilir miyim?',
            'Düşük GPA ile Almanya\'da master/lisans kabulü alınır mı?',
            'NC nedir, master başvurusunu nasıl etkiler?',
            'Bachelor ve master farklı bölümlerde olabilir mi?',
            'Master sonrası iş arama vizesi (Jobsuche) kaç ay geçerli?',
            'Almanya\'da PhD programları nasıl bulunur?',
            'Public Health master için Almanya\'daki seçenekler nelerdir?',
            'Bilgisayar mühendisliği master için DE/AT/CH karşılaştırması?',
            'Online master diploması Almanya\'da geçerli mi?',
            'Master tezi sürecinde üniversite değiştirebilir miyim?',
            'Master kabul belgesi vize başvurusu için yeterli mi?',
            'Diş hekimliği master/PhD programları nasıl?',
            'Sosyal bilimler master programları için Almanca şartı nedir?',
            'Master 3. sınıfta mı 4. sınıf sonrası mı başvurmalı?',
            'İngilizce master programlarında Almanca seviyesi gerekli mi?',
            'Sertifika beklemeden master başvurusu yapılır mı?',
            'Mainz veya Frankfurt\'ta master bursu olanakları nelerdir?',
        ],
        'uni-assist' => [
            'Uni-Assist VPD nedir, ne işe yarar?',
            'VPD başvuru ücreti ve ek başvuru ücreti ne kadar?',
            'Uni-Assist HZB notu nasıl hesaplanır?',
            'Uni-Assist üzerinden başvuru reddedilirse ne yapmalıyım?',
            'APS ile Uni-Assist arasındaki ilişki ve sıralama nedir?',
            'DoSV programlar için Uni-Assist üzerinden başvuru yapılır mı?',
            'VPD aldıktan sonra üniversitelere doğrudan mı başvurmalıyım?',
            'Apostilli evrak Uni-Assist başvurusu için gerekli mi?',
            'Uni-Assist için diploma çevirisi yeminli mi olmalı?',
            'VPD\'nin geçerlilik süresi var mı?',
            'Uni-Assist üzerinden başvuruda transcript çevirisi zorunlu mu?',
            'Uni-Assist red sebepleri en çok hangileridir?',
            'Lisans bitirmeden Uni-Assist VPD başvurusu mümkün mü?',
            'Uni-Assist ödeme hatası durumunda ne yapılır?',
            'Uni-Assist Mein Konto güncellemesi nasıl yapılır?',
            'Uni-Assist evraklarını postayla mı göndermem gerekiyor?',
            'Uni-Assist ile üniversite paralel başvurusu yapılabilir mi?',
            'Uni-Assist VPD 1.0 ve 4.0 not farkı nedir?',
            'Wintersemester için Uni-Assist deadline ne zaman?',
        ],
        'studienkolleg' => [
            'Studienkolleg nedir, kimler için zorunlu?',
            'Studienkolleg Feststellungsprüfung süreci nasıl işler?',
            'Studienkolleg M-Kurs ile T-Kurs arasındaki fark nedir?',
            'Devlet Studienkolleg ücretsiz mi, özel okul ne kadar?',
            'Studienkolleg\'e kabul için Almanca C1 şart mı?',
            'Studienkolleg eğitimi 1 yıl mı 2 yıl mı sürer?',
            'Studienkolleg giriş sınavında neler soruluyor?',
            'Studienkolleg sonrası üniversite kabulü garanti mi?',
            'Studienkolleg için vize başvurusu özel mi?',
            'Studienkolleg için Sperrkonto gerekli mi?',
            'Hazırlık programını atlayıp doğrudan Bachelor başvurusu mümkün mü?',
            'Studienkolleg\'de başarısız olursam ne olur?',
            'Studienkolleg yıl içinde 2 dönem mi alıyor?',
            'Studienkolleg ile direkt Bachelor karşılaştırması — hangisi avantajlı?',
            'Studienkolleg yurdu var mı, başvuru süreci nasıl?',
            'Studienkolleg giriş için lise diploması çevirisi yeminli mi olmalı?',
            'Studienkolleg kabul belgesi vize için yeterli mi?',
        ],
        'yurt' => [
            'WG nedir, nasıl aranır?',
            'Studierendenwerk yurt başvurusu nasıl yapılır?',
            'Berlin, Münih, Hamburg ortalama kira ne kadar?',
            'Kaution genelde kaç aylık kiraya denk gelir?',
            'WG bulmak için Almanca seviyesi ne olmalı?',
            'Yurt başvurusu için deadline ne zaman?',
            'WG-Gesucht gibi platformlar ücretsiz mi?',
            'WG/yurt sözleşmesi olmadan vize görüşmesine girilebilir mi?',
            'Aileler için öğrenci yurdu seçeneği var mı?',
            'Geçici 1 aylık konaklama için Airbnb/yurt karşılaştırması?',
            'Wohnung tutarken garantör (Bürgschaft) zorunlu mu?',
            'Anmeldung için yurt adresi yeterli mi?',
            'WG ilanlarında dolandırıcılık nasıl ayırt edilir?',
            'Frankfurt staj için kısa süreli konaklama nasıl ayarlanır?',
            'Münih\'te öğrenci yurdu bulmak ne kadar zor?',
            'Üniversite yurdu başvurusunda kabul oranı yüzde kaç?',
        ],
        'para' => [
            'Sperrkonto (bloke hesap) için ne kadar para gerekli?',
            'Türkiye\'den Almanya\'ya para transferinde en uygun banka hangisi?',
            'Fintiba\'ya para transferinde SWIFT şube bilgisi sorun yaratır mı?',
            'Bloke hesaptan para çekmek için Türkiye\'de hesap açtırmak gerekiyor mu?',
            'Bloke hesap aile bireyleri için ayrı miktar mı isteniyor?',
            'Wise veya Revolut Türkiye-Almanya transferi için uygun mu?',
            'Almanya öğrenci yıllık yaşam maliyeti ortalama kaç Euro?',
            'Goethe yoğunlaştırılmış kurs 1200€\'ya değer mi?',
            'Vize başvurusu için 75€ ücret yeterli mi, ek harcama var mı?',
            'Bloke hesap kapalı kalırsa aylık kesinti devam eder mi?',
            'Berlin\'e Euro yatırma — en az komisyonlu yöntem hangisi?',
            'Postbank, Deutsche Bank, N26 — öğrenci için en uygun banka?',
            'Almanya\'dan Türkiye\'ye para göndermek için en uygun uygulama hangisi?',
            'Sayfa başı yeminli tercüme ortalama kaç Euro?',
        ],
        'sehir' => [
            'Almanya\'da öğrenci için en ucuz şehirler hangileri?',
            'Berlin, Münih, Hamburg — öğrenci için hangisi tercih edilir?',
            'Hamburg\'da yüz yüze Almanca kursu önerisi var mı?',
            'Berlin\'de uygun fiyatlı çevirmen önerisi?',
            'Frankfurt civarı dil kursu seçenekleri nelerdir?',
            'Bremen-Hamburg-Hannover öğrenci grup linki var mı?',
            'Freiburg\'da kısa süreli yurt veya daire?',
            'Stuttgart\'ta yeminli çevirmen önerisi?',
            'Berlin\'de denklik için evrak listesi nelerdir?',
            'Berlin\'in NRW\'ye göre öğrenci avantajları?',
            'Hangi eyalette öğrenim ücreti var (Baden-Württemberg AB dışı)?',
            'Karlsruhe veya Freiburg öğrenci hayatı nasıl?',
            'Öğrenci için doğu Almanya\'nın avantajları (Leipzig, Dresden)?',
        ],
        'is' => [
            'Werkstudent nedir, kim başvurabilir?',
            'Öğrenci olarak haftada kaç saat çalışabilirim?',
            'Mini-Job 538€ sınırı nedir?',
            'Werkstudent ile bloke hesap kalan parayı birlikte kullanabilir miyim?',
            'Master sırasında part-time çalışmak mümkün mü?',
            'LinkedIn DE üzerinden iş aramak öğrenciler için etkili mi?',
            'Lieferando\'da öğrenci olarak çalışınca ne kadar kazanılır?',
            'Werkstudent başvurusu ne zaman yapmalı?',
            'Mezuniyet sonrası iş arama vizesi (Jobsuche) süresi nedir?',
            'Almanca olmadan Almanya\'da öğrenci işi bulunur mu?',
            'İlk staj veya Werkstudent pozisyonu nasıl bulunur?',
            'Mühendislik öğrencisi için iş arama maaş ortalaması ne kadar?',
            'Öğrenci olarak çalışırken sosyal güvenlik kesintisi var mı?',
            'Master sonrası iş bulamazsam vizem ne olur?',
            'Werkstudent saat sınırı nasıl kontrol ediliyor?',
        ],
        'sigorta' => [
            'TK, AOK, Barmer arasındaki fark — öğrenci için hangisi?',
            'Öğrenci sağlık sigortası (Krankenversicherung) aylık ne kadar?',
            'Vize için 30K Euro teminatlı sigorta yeterli mi?',
            'Fintiba\'nın sigorta paketi vize için kabul ediliyor mu?',
            'Studienkolleg öğrencileri için sigorta zorunluluğu nedir?',
            'Bachelor başlarken 30 yaş üstüysem sigortam değişir mi?',
            'Almanya\'ya gelmeden Türkiye\'den sigorta yaptırabilir miyim?',
            'Diş tedavisi öğrenci sigortasına dahil mi?',
            'Öğrenci sigortası iptal etme süreci nasıl?',
            'AOK Bayern\'a öğrenci olarak nasıl başvurulur?',
            'Werkstudent için sigorta zorunlu mu?',
            'Sigorta belgesi Anmeldung için gerekli mi?',
            'Aile bireyleri öğrenci sigortasına dahil edilebilir mi?',
            'Acil sağlık durumu için seyahat sigortası ayrı mı?',
            'Master öğrencisinin sigortası lisanstan farklı mı?',
        ],
        'randevu' => [
            'Almanya konsolosluğu vize randevusu kaç hafta sürede çıkar?',
            'Ankara konsolosluk öğrenci vizesi başvurusu randevu süreci?',
            'İstanbul başkonsolosluk dil kursu vizesi randevu durumu?',
            'IDATA üzerinden vize başvurusu nasıl yapılır?',
            'Randevu en yakın ekim başı çıkıyor, ne yapmalı?',
            'Randevu bulamayanlar için alternatif yöntemler nelerdir?',
            'İzmir IDATA üzerinden ulusal vize başvuru süresi?',
            'Randevu iptal edilirse yeniden mi alınmalı?',
            'Anmeldung randevu (Bürgeramt) ortalama ne kadar sürede çıkar?',
        ],
        'anmeldung' => [
            'Anmeldung nedir, Almanya\'ya gelir gelmez yapmak zorunda mıyım?',
            'Anmeldung için Bürgeramt\'tan randevu nasıl alınır?',
            'Anmeldung için gereken evraklar nelerdir?',
            'WG sözleşmem var, Anmeldung için yeterli mi?',
            'Anmeldung olmadan banka hesabı açılabilir mi?',
            'Berlin\'de Anmeldung randevusu çok zor — alternatif var mı?',
            'Wohnungsgeberbestätigung nedir, nasıl alınır?',
            'Anmeldung\'u 14 gün içinde yapmazsam ceza yer miyim?',
            'Münih\'te Anmeldung süreci nasıl işler?',
            'Anmeldung sonrası SteuerID ne zaman gelir?',
            'Anmeldung ile Ummeldung arasındaki fark nedir?',
            'Anmeldung\'da pasaport ve vize beraber mi gerekli?',
            'Vize uzatması için Anmeldung şart mı?',
            'Yurt adresi ile Anmeldung yapılabilir mi?',
        ],
        'burs' => [
            'DAAD bursu nasıl alınır, başvuru süreci nedir?',
            'DAAD başvurusu için hangi belgeler gerekli?',
            'Burs için yaş sınırı var mı?',
            'DAAD kısa süreli (3-6 aylık) burs imkanları neler?',
            'Master için DAAD bursu aylık ne kadar?',
            'Burs ile dil kursunu birlikte alabilir miyim?',
            'Erasmus ile DAAD bursu karşılaştırması?',
            'Burs başvurusu için kaç üniversiteye yazılmalı?',
            'Burs için not ortalaması ve dil şartı nedir?',
            'PhD için Almanya bursları nelerdir?',
            'Konrad Adenauer Vakfı bursu nasıl alınır?',
            'Burs için referans mektubu nasıl yazılır?',
            'Türkiye\'den Almanya bursu için TEV gibi yerel kaynaklar var mı?',
            'DAAD burs başvuru tarihleri ne zaman?',
            'Burs sonrasında Türkiye\'ye dönüş zorunluluğu var mı?',
        ],
        'denklik' => [
            'Türk lise diplomasının Almanya\'da geçerliliği var mı?',
            'Anabin nedir, üniversiteler nasıl listeleniyor?',
            'Üniversite diplomam Almanya\'da denk sayılır mı?',
            'Lise denklik için hangi notların önemli?',
            'Bachelor denkliği master başvurusunda nasıl değerlendirilir?',
            'Türkiye\'deki üniversitemin Anabin durumu nasıl öğrenilir?',
            'Anabin H+ ve H- ne anlama gelir?',
        ],
    ];

    public function run(): void
    {
        $topicMap = [];
        foreach ($this->topics as $t) {
            $topic = FaqTopic::updateOrCreate(
                ['slug' => $t['slug']],
                $t
            );
            $topicMap[$t['slug']] = $topic->id;
        }

        $usedSlugs = [];

        foreach ($this->questions as $topicSlug => $questions) {
            $topicId = $topicMap[$topicSlug] ?? null;
            if (!$topicId) {
                continue;
            }

            foreach ($questions as $i => $question) {
                $baseSlug = $this->slugify($question);
                $slug = $baseSlug;
                $n = 1;
                while (in_array($slug, $usedSlugs, true)) {
                    $slug = $baseSlug . '-' . (++$n);
                }
                $usedSlugs[] = $slug;

                Faq::updateOrCreate(
                    ['slug' => $slug],
                    [
                        'faq_topic_id' => $topicId,
                        'question' => $question,
                        'intent' => Faq::detectIntent($question),
                        'sort_order' => $i,
                        'is_published' => true,
                    ]
                );
            }
        }
    }

    private function slugify(string $text): string
    {
        $slug = Str::slug($text, '-', 'tr');
        $slug = preg_replace('/[^a-z0-9-]/', '', $slug);
        return Str::limit($slug, 70, '');
    }
}
