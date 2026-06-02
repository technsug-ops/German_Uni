<?php

namespace App\Console\Commands;

use App\Models\ContentBrief;
use Illuminate\Console\Command;

/**
 * "Almanya'da Yaşam & Kültür" taslak brief'lerini seed eder (status=draft).
 *
 * İLHAM/REFERANS: The Local DE "german-habits" etiketi gibi yabancı-perspektifli
 * kültür içerikleri. BİREBİR KOPYA DEĞİL — yalnızca KONU ilhamı; içerik özgün
 * üretilecek, uluslararası/Türk öğrenci açısıyla zenginleştirilecek, topluluk
 * (Forum + Telegram) gerçek sorularıyla beslenecek.
 *
 * Asset ÜRETMEZ (token tasarrufu) — bunlar backlog fikirleri. Geliştirilince
 * /admin/content-briefs'te "Çalışılıyor → Hazır" yapılıp asset üretilir.
 */
class SeedCultureBriefs extends Command
{
    protected $signature = 'content:seed-culture-briefs {--skip-existing : Mevcut slug\'ı atla}';
    protected $description = 'Almanya yaşam/kültür taslak brief\'lerini seed eder (status=draft, asset üretmez)';

    /**
     * Her biri öğrenci uyumuna (entegrasyon) odaklı, özgün açılı taslak fikir.
     * Kaynak ilham notu her brief'in notes alanında.
     */
    private const REF = 'İlham/referans: The Local DE "german-habits" etiketi (yabancı gözüyle Alman gündelik kültürü). BİREBİR KOPYALAMA — özgün yaz, uluslararası/Türk öğrenci açısıyla zenginleştir. Topluluk havuzundan (Forum + Telegram) gerçek soru ekle. Pratik + "para cezası/utanç yememe" çerçevesi.';

    private const BRIEFS = [
        [
            'title' => 'Alman Doğrudanlığı (Direktheit): Türk Öğrenci İçin Kültür Şoku ve Uyum Rehberi',
            'slug' => 'alman-dogrudanligi-kultur-soku-uyum',
            'audience' => 'mevcut_ogrenci',
            'topic' => 'yasam',
            'primary_keyword' => 'alman doğrudanlığı kültür şoku',
            'secondary_keywords' => ['almanlar neden bu kadar direkt', 'german directness', 'almanya kültür farkı', 'iş yerinde alman iletişimi', 'kabalık mı dürüstlük mü'],
            'pain_point' => 'Türk öğrenci Almanya\'da "Bu kötü olmuş" / "Hayır, katılmıyorum" gibi doğrudan geri bildirimi kabalık sanıp inciniyor. Hocayla, ev arkadaşıyla, Werkstudent\'te patronla iletişimde "lafı dolandırmama" kültürünü nasıl okumalı, kendini nasıl ifade etmeli, ne zaman gerçekten kaba ne zaman normal?',
            'notes' => 'Türk dolaylı/yüksek-bağlam iletişim vs Alman düşük-bağlam. Örnekler: akademik feedback, WG anlaşmazlığı, iş yeri. "Du" vs "Sie". Pozitif çerçeve: dürüstlük = saygı. ' . self::REF,
        ],
        [
            'title' => 'Lüften: Almanya\'da Küf ve Kira Cezasından Korunmak İçin Havalandırma Kuralı',
            'slug' => 'luften-havalandirma-kuf-kira-cezasi',
            'audience' => 'mevcut_ogrenci',
            'topic' => 'konut',
            'primary_keyword' => 'lüften nasıl yapılır küf',
            'secondary_keywords' => ['almanya küf kira sorumluluğu', 'stoßlüften', 'pencere havalandırma kışın', 'schimmel kaution', 'ev nemli ne yapmalı'],
            'pain_point' => 'Yeni gelen öğrenci kışın pencereyi hep kapalı tutunca duvarda küf (Schimmel) çıkıyor, ev sahibi depozitten (Kaution) kesinti yapıyor veya tamir faturası çıkarıyor. Günde kaç kez, kaç dakika "Stoßlüften" yapılmalı, kalorifer + pencere dengesi, küf çıkarsa sorumluluk kimde, depozit nasıl korunur?',
            'notes' => 'Stoßlüften günde 2-4 kez 5-10 dk tam açık. Kontamine olunca hukuki sorumluluk (kiracı havalandırmadıysa kiracıda). Kaution kesintisi önleme. Higrometre önerisi. ' . self::REF,
        ],
        [
            'title' => 'Ruhezeit ve Komşuluk Kuralları: Almanya\'da Para Cezası ve Şikayet Yememek',
            'slug' => 'ruhezeit-komsuluk-kurallari-almanya',
            'audience' => 'mevcut_ogrenci',
            'topic' => 'yasam',
            'primary_keyword' => 'ruhezeit nedir komşuluk kuralları',
            'secondary_keywords' => ['almanya gürültü saatleri', 'pazar günü sessizlik', 'hausordnung kurallar', 'komşu şikayeti almanya', 'gece 22 sessizlik'],
            'pain_point' => 'Türk öğrenci akşam 22\'den sonra müzik, gece çamaşır makinesi, pazar günü matkap kullanınca komşu şikayeti / ev sahibi uyarısı / hatta para cezası alıyor. Ruhezeit saatleri (gece + öğle + pazar), Hausordnung neyi yasaklar, komşuyla nasıl iletişim kurulur, haklı/haksız şikayet ayrımı nedir?',
            'notes' => 'Ruhezeit: 22:00-06:00 + çoğu yerde 13:00-15:00 + tüm Pazar/resmi tatil. Hausordnung bağlayıcı. Çamaşır/duş tartışmalı. WG iç dinamiği. Pozitif: erken tanışma şikayeti önler. ' . self::REF,
        ],
        [
            'title' => 'Almanya\'da Nakit Kültürü: Neden Hâlâ "Nur Bargeld" ve Kart Nerede Geçmez?',
            'slug' => 'almanya-nakit-kulturu-bargeld-kart',
            'audience' => 'mevcut_ogrenci',
            'topic' => 'para',
            'primary_keyword' => 'almanya nakit kültürü neden',
            'secondary_keywords' => ['nur bargeld ne demek', 'almanya kart geçmiyor', 'girocard ec kart', 'bäckerei nakit', 'almanya ödeme alışkanlıkları'],
            'pain_point' => 'Türkiye\'de her yerde kart geçerken Almanya\'da fırın, döner, küçük market, bazı barlar "Nur Bargeld" (sadece nakit) diyor; kredi kartı yerine Girocard (EC) isteniyor. Yeni gelen öğrenci neden, nerede nakit gerekir, ne kadar nakit taşımalı, Girocard nasıl alınır, Apple/Google Pay nerede çalışır?',
            'notes' => 'Kültürel: mahremiyet + borç karşıtlığı. Girocard ≠ kredi kartı; çoğu yer kredi kartı değil EC ister. Bäckerei/Imbiss/Wochenmarkt nakit. Çekmemeye karşı her zaman €20-50 cebde. ' . self::REF,
        ],
        [
            'title' => 'Pfand Sistemi: Almanya\'da Şişe-Kutu Depozitosu ve Geri İade Rehberi',
            'slug' => 'pfand-sise-kutu-depozito-iade',
            'audience' => 'mevcut_ogrenci',
            'topic' => 'para',
            'primary_keyword' => 'pfand nedir nasıl iade edilir',
            'secondary_keywords' => ['almanya şişe depozito', 'pfandautomat kullanımı', 'einweg mehrweg pfand', '25 cent şişe', 'pfand para iadesi market'],
            'pain_point' => 'Yeni gelen öğrenci su/bira/kola alırken fiyata eklenen 8-25 cent "Pfand"i bilmiyor, boş şişeleri çöpe atıp para kaybediyor. Hangi şişe-kutu Pfand\'lı (Einweg/Mehrweg farkı), Pfandautomat nasıl kullanılır, fiş nereye bozdurulur, ayda ne kadar geri kazanılır?',
            'notes' => 'Einweg (tek kullanım) €0.25, Mehrweg cam/pet €0.08-0.15. Logo: Pfand işareti. Market girişindeki otomat → fiş → kasada düş. Sosyal boyut: şişe toplama. ' . self::REF,
        ],
        [
            'title' => 'Almanya\'da Bahşiş (Trinkgeld): Ne Kadar, Nasıl Verilir, Kartla mı Nakitle mi?',
            'slug' => 'almanya-bahsis-trinkgeld-rehberi',
            'audience' => 'mevcut_ogrenci',
            'topic' => 'yasam',
            'primary_keyword' => 'almanya bahşiş ne kadar',
            'secondary_keywords' => ['trinkgeld yüzde kaç', 'almanya restoran bahşiş', 'kartla bahşiş nasıl', 'stimmt so ne demek', 'taksi kuaför bahşiş'],
            'pain_point' => 'Türk öğrenci restoran/kafe/taksi/kuaförde ne kadar bahşiş bırakacağını bilmiyor; garson hesabı getirince ne diyeceğini, kartla öderken bahşişi nasıl ekleyeceğini şaşırıyor. Yüzde kaç normal, yuvarlama mantığı, "Stimmt so" nasıl kullanılır, bahşiş bırakmamak ayıp mı?',
            'notes' => 'Tipik %5-10, küçük tutar yuvarlama. Kartla öderken toplamı sözlü söyle ("32 bitte") garson öyle çeker — sonradan ekleme zor. "Stimmt so" = üstü kalsın. Zorunlu değil ama âdet. Tip-button tartışması. ' . self::REF,
        ],
        [
            'title' => 'Alman Bira Bahçesi & Kneipe Kültürü: Öğrenci Sosyal Hayatı İçin Rehber',
            'slug' => 'biergarten-kneipe-ogrenci-sosyal-hayat',
            'audience' => 'mevcut_ogrenci',
            'topic' => 'yasam',
            'primary_keyword' => 'almanya biergarten kültürü',
            'secondary_keywords' => ['kneipe nedir', 'biergarten kendi yemeğini götürme', 'almanya öğrenci sosyalleşme', 'stammtisch', 'alkolsüz biergarten öğrenci'],
            'pain_point' => 'Almanya\'da sosyal hayatın çoğu Biergarten/Kneipe etrafında dönüyor; içmeyen veya bütçesi kısıtlı Türk öğrenci nasıl dahil olur? Biergarten\'a kendi yemeğini götürme kuralı, masaya katılma adabı (Stammtisch), alkolsüz seçenekler, ısmarlama/ödeme bölüşme (getrennt zahlen) nasıl işler?',
            'notes' => 'Biergarten: çoğunda kendi yemeğini götürebilirsin (içecek oradan). "Getrennt" (ayrı) vs "zusammen" ödeme. Alkolsüz: Apfelschorle, alkolfrei Bier normal. İçmeyen dışlanmaz. Networking değeri. ' . self::REF,
        ],
        [
            'title' => 'Freibad, Sauna ve FKK: Almanya\'da Açık Hava + Sauna Kültürü Kuralları',
            'slug' => 'freibad-sauna-fkk-kurallari',
            'audience' => 'mevcut_ogrenci',
            'topic' => 'yasam',
            'primary_keyword' => 'almanya sauna kuralları yabancı',
            'secondary_keywords' => ['freibad etiket', 'almanya fkk nedir', 'sauna çıplaklık kuralı', 'aufguss sauna', 'havuz öğrenci indirimi'],
            'pain_point' => 'Türk öğrenci için Almanya\'da sauna (genelde çıplak + karma) ve FKK kültürü ciddi kültür şoku. Freibad (açık hava havuzu) ve sauna adabı ne, havlu kuralı, karma/ayrı saatler, mahremiyet sınırları, rahat hissetmeyen biri nasıl katılır veya nazikçe geçer?',
            'notes' => 'Sauna: çoğu karma + çıplak + havlu altına şart (hijyen). Aufguss seansı. Freibad ucuz öğrenci eğlencesi. Hassasiyet: kültürel/dini rahatsızlık → kadın-saatleri / mayolu spa alternatifleri. Saygılı, yargısız ton. ' . self::REF,
        ],
        [
            'title' => 'Almanya\'nın Bölgesel Karakterleri: Şehir Seçerken Bilmen Gereken Kültür Farkları',
            'slug' => 'almanya-bolgesel-karakterler-sehir-secimi',
            'audience' => 'aday_ogrenci',
            'topic' => 'sehir',
            'primary_keyword' => 'almanya bölgesel farklar şehir seçimi',
            'secondary_keywords' => ['bavyera vs berlin', 'swabian tutumlu', 'almanya lehçeleri', 'kuzey güney almanya farkı', 'öğrenci için en iyi şehir karakteri'],
            'pain_point' => 'Aday öğrenci sadece üniversiteye değil şehrin karakterine de bakmalı: Bavyera (Münih) muhafazakâr + pahalı + Bayrisch lehçesi, Berlin çok kültürlü + kaotik + ucuz-ama-değişiyor, Swabia (Stuttgart) tutumlu + çalışkan, Ruhr sıcakkanlı + işçi sınıfı. Lehçe ne kadar sorun, hangi bölge yabancıya/öğrenciye daha kolay?',
            'notes' => 'Klişeleri eğlenceli ama dengeli ver (stereotip ≠ gerçek herkes). Lehçe: Hochdeutsch her yerde anlaşılır ama Bayrisch/Sächsisch zor. Maliyet + iş + topluluk + İngilizce-dostluk ekseni. [[housing-providers-system]] şehir verisine bağla. ' . self::REF,
        ],
        [
            'title' => 'Ders Kitabında Olmayan Almanca: Günlük İfadeler, Sesler ve Yerel Gibi Konuşma',
            'slug' => 'gunluk-almanca-ifadeler-yerel-konusma',
            'audience' => 'mevcut_ogrenci',
            'topic' => 'dil',
            'primary_keyword' => 'günlük almanca konuşma ifadeleri',
            'secondary_keywords' => ['almanca argo öğrenci', 'na ne demek', 'alles klar quatsch', 'almanca dolgu sesleri', 'b1 sonrası günlük almanca'],
            'pain_point' => 'B1/B2 sertifikası olan Türk öğrenci derste "Guten Tag" öğrendi ama sokakta "Na?", "Alles klar", "Quatsch", "Ach so", "Genau" gibi ifadeleri ve Almanların çıkardığı sesleri anlamıyor, yapay konuşuyor. Günlük doğal Almanca, selamlaşma, dolgu kelimeleri, kibar reddetme nasıl öğrenilir?',
            'notes' => 'Na (selam+nasılsın), Alles klar, Genau, Doch, Ach so, Quatsch, Mahlzeit (öğle selamı). Bölgesel selam: Moin (kuzey), Servus (güney), Tach. Yapaylıktan kurtulma. Dizi/podcast önerisi. ' . self::REF,
        ],
        [
            'title' => 'Almanya\'da Pazar Günü ve Tatil Kültürü: Kapalı Marketler ve Ladenschluss Hayatta Kalma',
            'slug' => 'almanya-pazar-gunu-ladenschluss-hayatta-kalma',
            'audience' => 'mevcut_ogrenci',
            'topic' => 'yasam',
            'primary_keyword' => 'almanya pazar günü neden kapalı',
            'secondary_keywords' => ['ladenschluss kuralı', 'pazar açık market almanya', 'späti tankstelle pazar', 'feiertag alışveriş', 'sonntagsruhe'],
            'pain_point' => 'Türk öğrenci Pazar günü süpermarketin kapalı olmasına hazırlıksız yakalanıyor, evde yiyecek bitiyor. Neden kapalı (Ladenschlussgesetz + Sonntagsruhe), Pazar açık olan yerler (tren garı marketleri, Späti, benzinlik, fırın), resmi tatil planlaması, hafta içi nasıl stok yapılır?',
            'notes' => 'Pazar + resmi tatil kapalı (eyalet farkı). Açık: Bahnhof Rewe/Edeka, Spätkauf (Berlin), Tankstelle, bazı Bäckerei sabah. Feiertag\'lar eyalete göre değişir. Cuma stok stratejisi. ' . self::REF,
        ],
        [
            'title' => 'Alman Geri Dönüşümü ve Çöp Ayrıştırma: Öğrenci Evinde Doğru Sistem',
            'slug' => 'alman-cop-ayristirma-geri-donusum-ogrenci',
            'audience' => 'mevcut_ogrenci',
            'topic' => 'konut',
            'primary_keyword' => 'almanya çöp ayrıştırma kuralları',
            'secondary_keywords' => ['gelber sack ne', 'biomüll restmüll papier', 'altglas renk ayırma', 'wg çöp anlaşmazlığı', 'çöp cezası almanya'],
            'pain_point' => 'Türk öğrenci Almanya\'nın katı çöp ayrıştırma sistemine (Gelber Sack, Biomüll, Restmüll, Papier, Altglas renk renk) yabancı; yanlış atınca WG arkadaşı/komşu uyarıyor, bina cezası gelebiliyor. Hangi atık hangi kutuya, cam renk ayrımı, Pfand\'la ilişkisi, çöp günleri nasıl takip edilir?',
            'notes' => 'Gelber Sack (plastik/metal ambalaj), Biomüll (organik), Restmüll (genel), Papier (kâğıt), Altglas (cam: beyaz/yeşil/kahve ayrı konteyner, akşam/pazar atma yasak-gürültü). Yanlış ayırma toplu ceza. WG sorumluluk çizelgesi. ' . self::REF,
        ],
    ];

    public function handle(): int
    {
        $created = 0; $updated = 0; $skipped = 0;

        foreach (self::BRIEFS as $def) {
            $existing = ContentBrief::where('slug', $def['slug'])->first();
            if ($existing && $this->option('skip-existing')) {
                $this->line('⏭️  ' . $def['title'] . ' — zaten var');
                $skipped++;
                continue;
            }

            $brief = ContentBrief::updateOrCreate(['slug' => $def['slug']], [
                'title'              => $def['title'],
                'slug'               => $def['slug'],
                'audience'           => $def['audience'],
                'topic'              => $def['topic'],
                'primary_keyword'    => $def['primary_keyword'],
                'secondary_keywords' => $def['secondary_keywords'],
                'pain_point'         => $def['pain_point'],
                'source_questions'   => [],
                'target_word_count'  => 1400,
                'brand_tone'         => 'casual',
                'status'             => 'draft',
                'notes'              => $def['notes'],
            ]);

            if ($brief->wasRecentlyCreated) {
                $this->info('✅ ' . mb_substr($brief->title, 0, 60));
                $created++;
            } else {
                $this->line('🔄 ' . mb_substr($brief->title, 0, 60));
                $updated++;
            }
        }

        $this->newLine();
        $this->info("━━━━━━━━━━━━━━━━━━━━━━");
        $this->info("Kültür brief'leri: {$created} yeni, {$updated} güncellendi, {$skipped} atlandı (status=draft).");
        $this->line('Sonraki: /admin/content-briefs → fikri geliştir → Hazır → asset üret.');

        return self::SUCCESS;
    }
}
