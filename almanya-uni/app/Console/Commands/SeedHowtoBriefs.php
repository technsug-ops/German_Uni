<?php

namespace App\Console\Commands;

use App\Models\ContentAsset;
use App\Models\ContentBrief;
use App\Services\Content\ContentGenerationService;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

/**
 * 4 yeni "nasıl yapılır" briefini seed eder, Gemini ile blog asset üretir, status=ready yapar.
 *
 * Sonraki adım: php artisan content:publish-blog-assets → Post'a sync.
 *
 * Source questions: storage/app/community/telegram_by_topic.json (gerçek topluluk soruları).
 */
class SeedHowtoBriefs extends Command
{
    /**
     * 4 yeni brief tanımı. Her biri Türk öğrencinin pain point'lerine odaklı.
     */
    private const BRIEFS = [
        [
            'title' => 'APS Sertifikası Türk Öğrenci Rehberi 2026 — Akademische Prüfstelle Adım Adım',
            'slug' => 'aps-sertifikasi-turk-ogrenci-rehberi-2026',
            'audience' => 'aday_ogrenci',
            'topic' => 'denklik',
            'primary_keyword' => 'aps sertifikası türkiye',
            'secondary_keywords' => ['akademische prüfstelle', 'aps türkiye 2026', 'aps başvuru', 'transkript çevirisi aps', 'aps mülakat'],
            'pain_point' => 'Türk öğrenciler APS\'i hangi durumda almak zorunda, transkript + mülakat akışı, üniversite başvurusu öncesi ne kadar sürede halletmeli? Hangi konsolosluk yapar, ücret 2026 itibariyle ne kadar, online vs yüz yüze mülakat farkı?',
            'topic_filter' => 'denklik',
            'notes' => 'APS Türkiye için yalnızca Çin/Hindistan/Vietnam zorunlu — Türk öğrenci için OPSIYONEL ama bazı üniler ister. Hangi durumda gerekli, hangi durumda gereksiz net açıkla. Pekin/İstanbul APS yapma — Türk konsolosluğu yok.',
        ],
        [
            'title' => 'Anmeldung Rehberi 2026 — Almanya\'da İlk Hafta Şehir Kayıt Adımları',
            'slug' => 'anmeldung-ilk-hafta-rehberi-2026',
            'audience' => 'mevcut_ogrenci',
            'topic' => 'anmeldung',
            'primary_keyword' => 'anmeldung nasıl yapılır',
            'secondary_keywords' => ['bürgeramt randevu', 'wohnungsgeberbestätigung', 'meldebescheinigung', 'ilk hafta almanya', '14 gün anmeldung'],
            'pain_point' => 'Almanya\'ya yeni gelen öğrenci 14 gün içinde Anmeldung yapmak zorunda ama Bürgeramt randevuları çoğu şehirde 4-6 hafta ileriye. Wohnungsgeberbestätigung\'u kiracıdan kim alır, walk-in randevu mümkün mü, geç kayıt cezası nedir?',
            'topic_filter' => 'anmeldung',
            'notes' => 'Berlin/Münih/Frankfurt randevu beklemeleri farklı. Walk-in saatleri şehir bazlı. Geç kayıt için resmi bir ceza yok ama vize uzatma için kanıt gerek. Yurt müdürü Wohnungsgeberbestätigung verir.',
        ],
        [
            'title' => 'Rundfunkbeitrag (GEZ) Öğrenci Rehberi 2026 — Muafiyet Var Mı, Nasıl Başvurulur?',
            'slug' => 'rundfunkbeitrag-gez-ogrenci-muafiyet-2026',
            'audience' => 'mevcut_ogrenci',
            'topic' => 'anmeldung',
            'primary_keyword' => 'rundfunkbeitrag öğrenci',
            'secondary_keywords' => ['gez muafiyet', '18.36 euro ayda', 'rundfunkbeitrag bafög', 'wg ortak ödeme', 'gez iptali'],
            'pain_point' => 'Anmeldung\'dan 2-4 hafta sonra otomatik gelen €18.36/ay GEZ faturası öğrencileri şaşırtıyor. BAföG alanlar muaf — ama Türk öğrenci BAföG\'a hak kazanmıyor. WG\'de ortak ödenebilir mi, geri ödememe ne olur, "Befreiung" başvurusu mümkün mü?',
            'topic_filter' => 'anmeldung',
            'notes' => '€18.36/ay = €220/yıl. BAföG sahipleri muaf. Konut WG ise tek bir kişi öder + bölüştürülür. Ödememe → mahkeme + iceberg ücret. Resmi başvuru formu rundfunkbeitrag.de\'de.',
        ],
        [
            'title' => 'TestAS Rehberi 2026 — Türk Lise Mezunu İçin Gerekli Mi, Nasıl Hazırlanılır?',
            'slug' => 'testas-turk-ogrenci-rehberi-2026',
            'audience' => 'aday_ogrenci',
            'topic' => 'denklik',
            'primary_keyword' => 'testas türk öğrenci',
            'secondary_keywords' => ['testas online', 'testas core test', 'testas matematik fen', 'testas studienkolleg alternatif', 'testas puanı yeterli'],
            'pain_point' => 'TestAS Türk lise mezunu için ZORUNLU değil ama bazı üniler istiyor ve yüksek puan = bonus puan. Hangi modül seçmeli (Mühendislik/Tıp/İktisat/Sosyal), Türkçe seçeneği var mı, online mı yüz yüze, ne kadara mal olur, kaç puan iyi?',
            'topic_filter' => 'denklik',
            'notes' => 'TestAS online 2025\'ten beri var. Türkçe dil seçeneği YAR. Core Test + 1 modül. Free Türk öğrenci için. 100/110/120 ortalama. 130+ üst düzey üni için bonus.',
        ],
        [
            'title' => 'Bürgeramt Randevusu 2026 — Berlin/Münih/Frankfurt Hızlı Yöntemler',
            'slug' => 'burgeramt-randevu-hizli-yontemler-2026',
            'audience' => 'mevcut_ogrenci',
            'topic' => 'anmeldung',
            'primary_keyword' => 'bürgeramt randevu nasıl alınır',
            'secondary_keywords' => ['bürgeramt termin trick', 'berlin meldebescheinigung', 'walk-in anmeldung', 'termin-bot ahlaki mi', 'spontantermin'],
            'pain_point' => 'Berlin\'de Bürgeramt randevuları 6-12 hafta ileride. Erken yakalamak için saat 06:00 portal kontrolü, walk-in saatleri (bazı ofisler 07:30 sırada), Termin-Bot etiği (yasak değil ama tartışmalı), küçük şehirlere gitme stratejisi. Anmeldung\'u 14 gün içinde nasıl tamamlarsın?',
            'topic_filter' => 'anmeldung',
            'notes' => 'Berlin 12 Bezirk her birinin ayrı portal — Lichtenberg, Mitte, Neukölln en zor; Spandau, Reinickendorf görece kolay. Walk-in: Pankow, Tempelhof. Termin-Bot servisleri €30-60 — yasal ama portala yük binmesi sorun.',
        ],
        [
            'title' => 'Schufa Rehberi 2026 — Türk Öğrenci İçin Kredi Notu Neden Önemli?',
            'slug' => 'schufa-turk-ogrenci-rehberi-2026',
            'audience' => 'mevcut_ogrenci',
            'topic' => 'para',
            'primary_keyword' => 'schufa nedir öğrenci',
            'secondary_keywords' => ['schufa auskunft ücretsiz', 'kein schufa wohnung', 'schufa bonitätscheck', 'yabancı schufa kayıt', 'schufa türk öğrenci'],
            'pain_point' => 'Almanya\'da yeni gelen öğrenci Schufa\'sız → ev kiralama zor, telefon kontratı reddediliyor. "Daha 1 ay oldu, Schufa nereden olacak?" Ev sahipleri "Schufa-Auskunft" ister, yoksa yüksek depozit veya kefil. İlk Schufa nasıl başlatılır, ücretsiz versiyon nedir, yabancılar için "kein Schufa" alternatifleri?',
            'topic_filter' => 'para',
            'notes' => 'Schufa BasisScore otomatik oluşur Anmeldung + banka açılışı sonrası. Ücretsiz "Datenkopie nach §15 DSGVO" yılda 1 kez. Yabancılar için ev: Wunderflats (Schufa-free), 3-6 ay depozit, garantörlü konutlar.',
        ],
        [
            'title' => 'Vize Reddi Sonrası Remonstration 2026 — Konsolosluk İtiraz Mektubu Rehberi',
            'slug' => 'vize-reddi-remonstration-itiraz-2026',
            'audience' => 'aday_ogrenci',
            'topic' => 'vize',
            'primary_keyword' => 'vize reddi sonrası ne yapmalı',
            'secondary_keywords' => ['remonstration mektubu örnek', 'vize itiraz süresi 1 ay', '36F vize red sebepleri', 'vize tekrar başvuru', 'idata vize reddi'],
            'pain_point' => 'Vize reddi mektubu geldi — "yetersiz finans", "şüpheli niyet", "evrak eksikliği" gibi belirsiz gerekçeler. 1 ay içinde Remonstration (itiraz) hakkı var ama nasıl yazılır? Yeniden başvurmak mı, itiraz etmek mi? Türk konsolosluklarındaki red oranı + itiraz başarı şansı.',
            'topic_filter' => 'vize',
            'notes' => 'Remonstration 1 ay zorunlu, ücretsiz, posta ile. 60% itiraz başarısı (red sebebine göre). Yeniden başvuru daha hızlı ama §75 ücreti yeniden. Avukat €200-500 — sadece kompleks vakalar.',
        ],
        [
            'title' => 'ZAB Diploma Denkliği 2026 — Master/PhD İçin Resmi Tanıma Süreci',
            'slug' => 'zab-diploma-denklik-master-phd-2026',
            'audience' => 'aday_ogrenci',
            'topic' => 'denklik',
            'primary_keyword' => 'zab diploma denklik',
            'secondary_keywords' => ['zentralstelle für ausländisches bildungswesen', 'statement of comparability', 'zab gutachten', 'türk lisans denklik almanya', 'phd başvuru denklik'],
            'pain_point' => 'Türk lisans/master mezunu Almanya\'da master/PhD\'ye başvurmak için diploma denkliği gerek. Bazı üniler Anabin yetiyor diyor, bazıları ZAB Zeugnisbewertung istiyor. ZAB başvurusu €200, 2-3 ay sürer. Hangi durumda zorunlu, hangi durumda opsiyonel?',
            'topic_filter' => 'denklik',
            'notes' => 'ZAB resmi Bund kuruluşu, Anabin\'den ayrı ama bağlantılı. Zeugnisbewertung 200€. Çok unili başvurularda 1 belge yetiyor. Lisans → master için genelde Anabin yeter; master → PhD için ZAB daha güvenli.',
        ],
        [
            'title' => 'Blue Card 2026 — Almanya Mezunları İçin AB Mavi Kart Rehberi',
            'slug' => 'blue-card-almanya-mezun-2026',
            'audience' => 'mevcut_ogrenci',
            'topic' => 'is',
            'primary_keyword' => 'blue card almanya başvuru',
            'secondary_keywords' => ['eu blue card mezun', 'mavi kart maaş limiti 2026', 'fachkräfteeinwanderungsgesetz', 'oturma izni mavi kart', 'aile birleşimi blue card'],
            'pain_point' => 'Almanya\'da mezun olan Türk öğrenci için iş arama 18 ay var, ama daimi kalmak için Blue Card en güçlü yol. 2026 maaş eşiği €45.300 (mark mesleklerde €41.041). Lisans yetiyor mu, yoksa master mi? Aile birleşimi avantajları, oturma süresi (33 ay), tatil bürokrasisi.',
            'topic_filter' => 'is',
            'notes' => '2024 Fachkräfteeinwanderungsgesetz reform: Blue Card 33 ay daimi oturma (Almanca B1) veya 21 ay (B1+pozisyon). Lisans veya tanınan dengi. STEM/IT/sağlık "Mangelberufe" daha düşük maaş eşiği.',
        ],
        [
            'title' => 'BAföG Alternatifleri 2026 — Türk Öğrenciye Burs ve Finansman Seçenekleri',
            'slug' => 'bafog-alternatifleri-turk-ogrenci-2026',
            'audience' => 'aday_ogrenci',
            'topic' => 'burs',
            'primary_keyword' => 'bafög alternatifleri yabancı öğrenci',
            'secondary_keywords' => ['daad burs türk öğrenci', 'deutschlandstipendium', 'erasmus+ master', 'parteistiftung burs', 'türk öğrenci finansman'],
            'pain_point' => 'BAföG Türk öğrenciye uygun değil (sadece daimi oturma + 5 yıl şartı). Peki diğer yollar? DAAD master/PhD bursları, Deutschlandstipendium €300/ay (uniden bağımsız), Erasmus+ exchange, Konrad-Adenauer/Heinrich-Böll/Friedrich-Ebert parti vakıfları. Hangisine başvurmalı, ne zaman, ne kadar şans?',
            'topic_filter' => 'burs',
            'notes' => 'DAAD master/PhD: Türk için en güçlü, €850-1.300/ay. Deutschlandstipendium: %50 not + sosyal angajman, üni başvuru. Parti vakıfları: sosyal/akademik aktif olmak şart, 2 yıllık süreç. Erasmus+: hâlâ Türk üniyle başlamış olmak gerek.',
        ],
        [
            'title' => 'Yeminli Tercüme Rehberi 2026 — Diploma ve Resmi Belgeleri Almanya Başvurusu İçin',
            'slug' => 'yeminli-tercume-rehberi-diploma-almanya-2026',
            'audience' => 'aday_ogrenci',
            'topic' => 'uni_assist',
            'primary_keyword' => 'yeminli tercüme almanya başvuru',
            'secondary_keywords' => ['noter onaylı tercüme', 'apostil türkiye', 'beglaubigte übersetzung', 'diploma tercümesi maliyet', 'transkript çevirisi', 'lise diploması tercüme'],
            'pain_point' => 'Türk başvuru sahipleri diploma + transkript + nüfus belgesini Almanya için çevirmesi gerek ama hangisi yeminli (Türk noter onaylı) hangisi apostil, hangisi Beglaubigte Übersetzung? Türkiye\'de mi Almanya\'da mı yaptırmalı, ücret ne kadar (€10-50 sayfa başına), uni-assist hangi formatı kabul eder, sahte tercüman riski nasıl bertaraf edilir?',
            'topic_filter' => 'uni_assist',
            'notes' => 'Türkiye\'de noter onaylı tercüman ucuz (€10-20/sayfa) + apostil ekstra. Almanya\'da Beglaubigte Übersetzung €30-50/sayfa, hizli ama pahalı. uni-assist hem Türk noter onaylı + apostili hem Alman Beglaubigte\'yi kabul eder. Diploma + transkript + doğum belgesi (bazı eyaletlerde) gerekli. Adalet Bakanlığı yeminli tercüman listesi: https://www.adalet.gov.tr/. Almanya BDÜ resmi liste. AFFILIATE FRIENDLY: gelecekte tercüme bürosu önerebilir.',
        ],
        [
            'title' => 'Krankenkasse Karşılaştırma 2026 — TK vs AOK vs Barmer vs BKK Öğrenci',
            'slug' => 'krankenkasse-karsilastirma-tk-aok-barmer-2026',
            'audience' => 'aday_ogrenci',
            'topic' => 'sigorta',
            'primary_keyword' => 'krankenkasse karşılaştırma öğrenci',
            'secondary_keywords' => ['tk öğrenci sigortası', 'aok barmer fark', 'krankenkasse türkçe destek', 'student gesetzliche krankenkasse', 'krankenkasse hangi iyi'],
            'pain_point' => 'Almanya\'ya gelen öğrenci €110-140/ay zorunlu sağlık sigortası seçmek zorunda. TK / AOK / Barmer / BKK arasında fiyat aynı (yasa), peki neden seçim? Bonus programları, Türkçe destek, ek sigorta paketleri, dijital uygulama, geri ödeme hızı — hangi krankenkasse Türk öğrenci için en iyi?',
            'topic_filter' => 'sigorta',
            'notes' => 'Yasal krankenkasse fiyatı sabit (~€129.85/ay 2026, %14.6 + bonus %1.5). Fark: bonus programları (TK\'da Apple Watch + 100€/yıl, AOK\'da fitness aboneliği), Türkçe destek (TK Türkçe broşür var, Barmer az), dijital app (TK önde), ek tedavi paketleri (alternatif tıp, diş, gözlük). AFFILIATE FRIENDLY: Krankenkasse direkt komisyonu yok ama affiliate\'e açık tıbbi servis önerileri (Doctolib, diş sigortası eklenti) yapılabilir.',
        ],
        [
            'title' => 'Steuer-ID + IBAN + N26/DKB 2026 — Almanya\'da İlk Hafta Bürokrasi',
            'slug' => 'steuer-id-iban-banka-ilk-hafta-2026',
            'audience' => 'mevcut_ogrenci',
            'topic' => 'anmeldung',
            'primary_keyword' => 'steuer-id nasıl alınır öğrenci',
            'secondary_keywords' => ['n26 dkb öğrenci hesap', 'iban almanya açma', 'tax id otomatik gelir mi', 'steuerklasse öğrenci', 'ilk hafta banka'],
            'pain_point' => 'Anmeldung sonrası Steuer-ID (vergi kimlik) 2-6 hafta içinde otomatik postayla gelir ama Werkstudent başlamak için lazım. IBAN olmadan ne kira ödenir ne sigorta. N26 vs DKB vs Sparkasse — anonim öğrenci hesabı en kolay nerede açılır? Steuer-ID gelmeden iş başvurusu mümkün mü?',
            'topic_filter' => 'anmeldung',
            'notes' => 'Steuer-ID: Anmeldung sonrası 2-6 hafta posta, Finanzamt\'tan elle de istenebilir. N26 ve DKB tam online açılır (10 dk), Sparkasse şubeye gitmek gerek. Steuerklasse 1 (bekar öğrenci) default. Werkstudent için Steuer-ID + IBAN şart, sigorta sertifikası 2-3 hafta sonra eklenir.',
        ],
        [
            'title' => 'Anabin H+, H+-, H- Ayrımı 2026 — Diploma Sınıflandırması Adım Adım',
            'slug' => 'anabin-h-plus-h-eksi-ayrimi-2026',
            'audience' => 'aday_ogrenci',
            'topic' => 'denklik',
            'primary_keyword' => 'anabin h+ h+- h- nedir',
            'secondary_keywords' => ['anabin sınıflandırma', 'anabin türkiye lise', 'h+ direkt başvuru', 'h+- bazı kısıtlama', 'h- studienkolleg'],
            'pain_point' => 'Anabin Türk diplomasını üç sınıfa ayırır: H+ (direkt başvuru), H+- (bazı kısıtlamalar), H- (Studienkolleg gerekli). Ama hangi durumda Türkiye lisesi H+ olur? Anatomi LİSE, fen lise, IB diploma, açık öğretim — her birinin sınıflandırması farklı. Hangi şartlarda H+- veya H- olur, çözüm yolu ne?',
            'topic_filter' => 'denklik',
            'notes' => 'Türk lise → Anabin sınıflandırması bağlam: not ortalaması (4.0 veya 5.0 sistemi), branş (fen, sosyal, eşit ağırlık), AYT-TYT puanı, IB veya AP varsa direkt H+, açık öğretim H- veya H+-. Anabin manuel kontrol şart, otomatik araç yok. Hatalı sınıflandırma uni-assist üzerinden itiraz edilebilir.',
        ],
        [
            'title' => 'Almanya IT İş Bulma 2026 — Türk Mezun ve Werkstudent İçin Kapsamlı Rehber',
            'slug' => 'almanya-it-is-bulma-turk-mezun-2026',
            'audience' => 'phd_adayi',
            'topic' => 'is',
            'primary_keyword' => 'almanya it iş bulma türk',
            'secondary_keywords' => ['werkstudent it pozisyon', 'praktikum yazılım almanya', 'sap siemens iş', 'almanca olmadan iş', 'it cv anschreiben'],
            'pain_point' => 'Türk yazılım/IT mezunu Almanya\'da iş arıyor ama nereden başlamalı? SAP, Siemens, Mercedes-Benz Tech, Zalando, N26, Celonis gibi büyük şirketler vs startup\'lar. Almanca olmadan iş bulunur mu? Werkstudent vs Praktikum vs Junior pozisyon farkı? LinkedIn vs Xing vs StepStone — hangi platform?',
            'topic_filter' => 'is',
            'notes' => 'Almanca olmadan IT iş bulma mümkün ama %30-40 daha az pozisyon. Berlin/Hamburg/Münih English-friendly. CV: 1-2 sayfa, lebenslauf formatı. Anschreiben (motivasyon mektubu) ZORUNLU, kişiselleştirilmiş olmalı. Maaş: junior €45-55K, mid €55-75K, senior €75-100K+. Werkstudent €15-20/saat, en fazla 20 saat/hafta dönem içi.',
        ],
        [
            'title' => 'Almanya\'da Türk Dernekleri Rehberi 2026 — Şehir Bazlı Topluluk + Mentor Listesi',
            'slug' => 'almanya-turk-dernekleri-mentor-rehberi-2026',
            'audience' => 'mevcut_ogrenci',
            'topic' => 'sehir',
            'primary_keyword' => 'almanya türk dernekleri öğrenci',
            'secondary_keywords' => ['türk öğrenci derneği berlin', 'tdf türk doktorlar federasyonu', 'türk akademisyenler', 'türk kahvesi münih', 'türk mentor almanya'],
            'pain_point' => 'Almanya\'ya yeni gelen Türk öğrenci hemşehri/derneklik arıyor: pratik soru cevap, networking, sosyal etkinlikler. Berlin\'de hangi dernekler aktif, Münih\'te öğrenci grubu var mı, Frankfurt\'ta TDF (Türk Doktorlar Federasyonu)? WhatsApp grupları, Telegram, Discord — hangisi gerçekten faydalı? Sahte dernek tuzakları nasıl ayırt edilir?',
            'topic_filter' => 'sehir',
            'notes' => 'Berlin: Türk Toplumu (TBB), Türkische Gemeinde Berlin-Brandenburg. Münih: BTÖD (Bayern Türk Öğrenci Derneği). Frankfurt: TGD Hessen. NRW: TGD NRW. Bursa Akademisyenler Derneği (TAVAK). Sahte/MLM tuzakları: yıllık kayıt €300+ isteyen, "iş garantisi" vaad edenler. Resmi dernek listesi: Türk Konsoloslukları sitesi.',
        ],
        [
            'title' => 'Almanca A1 → C1 Strateji Rehberi 2026 — Türk Öğrenci İçin 6-12 Aylık Plan',
            'slug' => 'almanca-a1-c1-strateji-6-12-ay-2026',
            'audience' => 'aday_ogrenci',
            'topic' => 'dil',
            'primary_keyword' => 'almanca a1 c1 öğrenme stratejisi',
            'secondary_keywords' => ['goethe testdaf hazırlık', 'almanca hızlı öğrenme', 'a1 a2 b1 süre', 'almanca kitap önerisi', 'duolingo babbel italki'],
            'pain_point' => 'Türk öğrenci Almanya başvurusu için B1/B2 lazım ama 6-12 ay içinde A1\'den C1\'e nasıl çıkılır? Goethe-Institut kursu vs ucuz online (Italki + Babbel), Türkiye\'de mi Almanya\'da mı öğrenmek daha hızlı? Hangi kitap, hangi dizi, hangi telegram grubu? Test sırasında stres yönetimi.',
            'topic_filter' => 'dil',
            'notes' => 'A1→B1 ortalama 6 ay (yoğun 4 saat/gün) veya 12 ay (rahat 1-2 saat/gün). Goethe-Institut Türkiye €150-300/kur, Almanya €400-600. Ucuz alternatif: Volkshochschule (VHS) Almanya\'da €100-200, 8-10 hafta. Babbel + Italki ucuz ama disiplin gerek. Diziler: "Dark", "Babylon Berlin", "Türkisch für Anfänger". YouTube: Easy German, Deutsch mit Marija.',
        ],
        [
            'title' => 'B1 Dil Sınavı Kıyaslama 2026 — Goethe vs telc vs TestDaF vs DSH Türk Öğrenci',
            'slug' => 'b1-dil-sinavi-goethe-telc-testdaf-dsh-2026',
            'audience' => 'aday_ogrenci',
            'topic' => 'sinav',
            'primary_keyword' => 'goethe telc testdaf dsh fark',
            'secondary_keywords' => ['hangi almanca sınavı', 'goethe testdaf hangi kabul', 'telc vize geçerli', 'testdaf 4444 puan', 'dsh türkiye merkezi'],
            'pain_point' => 'Türk öğrenci hangi Almanca sınavına girmeli? Goethe-Zertifikat (vize için yeterli mi), telc (uni-assist kabul ediyor mu), TestDaF (online var mı), DSH (sadece Almanya\'da). Her birinin ücreti, sınav merkezi sayısı, geçerlilik süresi, üniversite kabulü ayrı. Yanlış sınav = yeniden başlamak.',
            'topic_filter' => 'denklik',
            'notes' => 'Goethe: tüm seviyeler (A1-C2), €150-280 Türkiye, vize + uni kabul. telc: A1-C2, €100-200, uni-assist kabul ama bazı üniler "Goethe önerir". TestDaF: sadece B2-C1, €195, akademik odaklı, online 2025\'ten beri var. DSH: sadece Almanya\'da, ücretsiz/€100, sadece o üniye geçer. Türkiye sınav merkezleri: Goethe (5 şehir), telc (2-3), TestDaF (sadece İstanbul + Ankara), DSH yok.',
        ],
        [
            'title' => 'DAAD Burs Detay Rehberi 2026 — Master + PhD Başvurusu Adım Adım',
            'slug' => 'daad-burs-detay-master-phd-2026',
            'audience' => 'aday_ogrenci',
            'topic' => 'burs',
            'primary_keyword' => 'daad bursu nasıl başvurulur master',
            'secondary_keywords' => ['daad master programı', 'daad phd başvurusu', 'daad motivasyon mektubu', 'daad referans mektubu', 'daad mülakat', 'daad sonuçları ne zaman'],
            'pain_point' => 'DAAD Türkiye için en güçlü burs ama başvuru süreci kompleks: hangi program (Study Scholarship vs Research Grant), 6-8 sayfa motivasyon mektubu, 2 akademik referans, dil sertifikası, transkript çevirisi. Mart-Ekim arası deadline\'lar, kabul oranı %15-20. Aylık €861 (Master) / €1.200 (PhD) + sigorta + ulaşım. Türk başvuru tipik hataları neler?',
            'topic_filter' => 'burs',
            'notes' => 'DAAD AA Türkiye en güçlü ülke. Study Scholarship master için, Research Grant PhD için. Deadline Ekim (Master) Mart-Mayıs (PhD). Motivasyon mektubu: kişisel + akademik amaç + Almanya neden. Referans: 1 tez danışmanı + 1 ders hocası. Tipik hata: cookie-cutter motivasyon, transkript apostili eksik, gereksiz uzun CV. Sonuçlar Mayıs-Temmuz.',
        ],
        [
            'title' => 'Studienkolleg Merkez Listesi 2026 — Devlet vs Özel, Şehir Bazlı 25+ Kurum',
            'slug' => 'studienkolleg-merkez-listesi-2026',
            'audience' => 'aday_ogrenci',
            'topic' => 'studienkolleg',
            'primary_keyword' => 'studienkolleg merkez listesi türk',
            'secondary_keywords' => ['studienkolleg devlet özel fark', 'studienkolleg berlin hamburg', 'studienkolleg kabul testi', 'studienkolleg kurs t m w g', 'studienkolleg yurt'],
            'pain_point' => 'H+- veya H- olan Türk öğrenci Studienkolleg\'e gitmek zorunda. Hangi şehirde devlet okulu var, hangisinde özel? Berlin, Hamburg, Heidelberg, Köln, Münih, Darmstadt... her birinin kabul testi (Aufnahmetest) farklı, kontenjan farklı. Devlet ücretsiz ama 6-12 ay bekleme listesi. Özel €5.000-9.000/yıl ama hızlı kabul.',
            'topic_filter' => 'studienkolleg',
            'notes' => 'Devlet SK\'lar: ~22 kuruluş. En büyükler: Studienkolleg Berlin (FU+TU), Hamburg, Heidelberg, Frankfurt, Münih, Darmstadt, Köthen, Hannover, Karlsruhe. Kabul testi: Almanca + matematik (T/M), Almanca + ekonomi (W). Özel SK: ColumbusKolleg (Köln/Berlin/Frankfurt €5.500), FIM College Berlin €7.900, Studienkolleg ASK (€8.500). Devletin avantajı: ücretsiz + diğer üniler de kabul eder. Özel\'in avantajı: hızlı, küçük sınıf, başvuru garantili.',
        ],
        [
            'title' => 'Almanya Bachelor Başvuru Detay 2026 — Türk Lise Mezunu İçin Tam Akış',
            'slug' => 'almanya-bachelor-basvuru-detay-2026',
            'audience' => 'aday_ogrenci',
            'topic' => 'uni_assist',
            'primary_keyword' => 'almanya bachelor başvuru türk',
            'secondary_keywords' => ['lisans başvuru almanya', 'bachelor uni assist', 'bachelor not ortalaması alman', 'bachelor numerus clausus', 'bachelor ingilizce program'],
            'pain_point' => 'Türk lise mezunu Almanya\'da Bachelor başvurusu yapacak: hangi notları çevirebilirim (modifizierte bayerische Formel), Numerus Clausus nedir, hangi programlarda kontenjan kapanır, İngilizce Bachelor programları gerçekten var mı (sınırlı), Studienkolleg gerek mi (Anabin sınıflandırması), uni-assist tek başına yeter mi yoksa direkt başvuru daha iyi mi?',
            'topic_filter' => 'uni_assist',
            'notes' => 'Türk lise mezunu Bachelor için modifizierte bayerische Formel ile not dönüştürülür. NC tıp 1.0-1.2, hukuk 2.0-2.5, mühendislik 2.5-3.0, sosyal bilim 1.8-2.8. İngilizce Bachelor: nadir, çoğu Berlin/Frankfurt private + Jacobs Bremen. Anabin H+ direkt başvuru, H+- 1 yıl uni + tekrar başvuru veya Studienkolleg, H- Studienkolleg zorunlu. uni-assist genelde gerekli (özel devlet üniversiteleri direkt). Deadline 15 Temmuz (kış) / 15 Ocak (yaz).',
        ],
    ];

    protected $signature = 'content:seed-howto-briefs
        {--generate-asset : Brief\'leri oluşturduktan sonra her biri için Gemini\'den blog asset üret}
        {--sleep=2 : Gemini API\'leri arası bekleme}
        {--skip-existing : Mevcut brief\'i (slug) atla}';

    protected $description = '4 nasıl-yapılır blog briefini seed eder + opsiyonel asset üretir';

    public function handle(ContentGenerationService $svc): int
    {
        $tg = json_decode(@file_get_contents(storage_path('app/community/telegram_by_topic.json')), true);
        $msgs = $tg['topics'] ?? [];

        $created = 0; $skipped = 0; $assetSuccess = 0; $assetFail = 0;

        foreach (self::BRIEFS as $def) {
            $existing = ContentBrief::where('slug', $def['slug'])->first();

            if ($existing && $this->option('skip-existing')) {
                $this->line('⏭️ ' . $def['title'] . ' — zaten var');
                $skipped++;
                continue;
            }

            // Topic'e göre telegram cache'ten 6-8 soru al
            $sourceQs = [];
            $topicMsgs = $msgs[$def['topic_filter']] ?? [];
            foreach (array_slice($topicMsgs, 0, 30) as $m) {
                $text = is_array($m) ? ($m['text'] ?? '') : (string) $m;
                $text = trim(html_entity_decode($text));
                if (str_contains(mb_strtolower($text), '?') && mb_strlen($text) > 20 && mb_strlen($text) < 250) {
                    $sourceQs[] = $text;
                }
                if (count($sourceQs) >= 7) break;
            }

            $payload = [
                'title' => $def['title'],
                'slug' => $def['slug'],
                'audience' => $def['audience'],
                'topic' => $def['topic'],
                'primary_keyword' => $def['primary_keyword'],
                'secondary_keywords' => $def['secondary_keywords'],
                'pain_point' => $def['pain_point'],
                'source_questions' => $sourceQs,
                'target_word_count' => 1500,
                'brand_tone' => 'instructive',
                'status' => 'ready',
                'notes' => $def['notes'],
            ];

            $brief = ContentBrief::updateOrCreate(['slug' => $def['slug']], $payload);
            $verb = $brief->wasRecentlyCreated ? '✅ Created' : '🔄 Updated';
            $this->info($verb . ' #' . $brief->id . ' ' . mb_substr($brief->title, 0, 55) . ' · ' . count($sourceQs) . ' Q\'s');
            $created++;

            if ($this->option('generate-asset')) {
                $this->line('   🤖 Generating blog asset...');
                $result = $svc->generateAsset($brief, 'blog');
                if ($result['success']) {
                    $result['asset']->update(['status' => 'ready']);
                    $this->info('   ✅ Asset #' . $result['asset']->id . ' (' . mb_strlen($result['asset']->body_md) . ' chars, ' . ($result['tokens']['output'] ?? '?') . ' tokens)');
                    $assetSuccess++;
                } else {
                    $this->error('   ❌ ' . ($result['error'] ?? 'unknown'));
                    $assetFail++;
                }
                sleep((int) $this->option('sleep'));
            }
        }

        $this->newLine();
        $this->info("━━━━━━━━━━━━━━━━━━━━━━");
        $this->info("Briefs: {$created} processed, {$skipped} skipped");
        if ($this->option('generate-asset')) {
            $this->info("Assets: {$assetSuccess} success, {$assetFail} fail");
        }

        return self::SUCCESS;
    }
}
