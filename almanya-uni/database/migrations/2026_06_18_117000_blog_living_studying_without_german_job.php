<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/** TR blog (entegrasyon): Almanca bilmeden okumak/yaşamak + öğrenci işi gerçeği; İngilizce yeter ama tam-zamanlı iş Almanca ister. FK-safe. */
return new class extends Migration
{
    public function up(): void
    {
        $slug = 'studying-in-germany-without-german-living-and-student-job-reality';
        $body = <<<'MD'
"Almancam yok ama Almanya'da İngilizce okumak istiyorum — yaşayabilir miyim, öğrenci işi bulabilir miyim?" Cevap **evet, ama bir şartla.** İngilizce ile başlangıç tamamen mümkün; fakat işin **gerçeğini** bilmezsen ileride sert bir uyanışla karşılaşabilirsin. Dengeli ve dürüst bir tablo.

## 1) İngilizce ile yaşamak: başlangıçta mümkün
- **Büyük ve üniversite şehirlerinde** İngilizce ile günlük hayatı çevirmek mümkün; geniş bir **uluslararası ortam** var (her ülkeden öğrenci, aralarında doğal olarak İngilizce konuşur).
- Genç nesil ve üniversite çevresi genelde İngilizce konuşur; arkadaş çevresi kurmak zor değil.
- Resmî işler (Anmeldung, sağlık, banka) İngilizcesiz biraz zorlar ama **çözülmez değil** — herkes hallediyor.

## 2) "İngilizce okumak anlamsız mı?" — Hayır
Bir efsane: "Almanya'da İngilizce okumak boş." Gerçek:
- **Maliyet avantajı dev:** Devlet üniversitelerinin çoğu **harçsız** (sadece dönem katkı payı); ücretli İngilizce/özel programlar bile Avustralya/ABD'deki **20.000+ €/yıl**'a kıyasla çok daha ucuz.
- Araştırma ve birçok teknik alan zaten **İngilizce** yürür. (İngilizce programlar: [program araması](/tr/programs?language=en) · detaylı: [Almancasız İngilizce Master rehberi](/tr/blog/english-masters-in-germany-without-german-your-complete-guide).)

## 3) Öğrenci işi: Almancasız bulunur (alana bağlı)
- **Uluslararası alanlarda** (CS, mühendislik, veri, araştırma, startup) İngilizce ile öğrenci işi / [Werkstudent](/tr/blog/werkstudent-in-germany-the-real-key-to-the-job-market) / HiWi bulmak **mümkün** — birçok ülkeden öğrenci bunu yapıyor.
- **Ama alana bağlı:** Müşteriyle birebir temas gerektiren işler, hizmet sektörü, ya da Tarih/Felsefe/Edebiyat gibi bölümler **Almanca ister**. Bu alanlardaysan İngilizce tek başına yetmez.

## 4) Asıl gerçek: tam-zamanlı iş çoğu zaman Almanca ister
Bu, en çok atlanan ve en pahalıya patlayan nokta:
- Öğrenci/araştırma işini İngilizce ile bulan birçok kişi, **"demek ki Almancaya gerek yok"** diye diline çalışmayı bırakıyor.
- Mezuniyette **tam-zamanlı şirket işi** ararken sert bir gerçeklik: Alman iş dünyası çoğunlukla **Almanca** döner; pek çok rol **B2-C1** ister.
- **Sonuç:** İngilizce seni içeri sokar, ama **uzun vadeli kariyer için Almanca şart.** Diline en baştan, paralel çalış.

## 5) Bir yılda nereye gelinir? (gerçekçi)
- **B1** (alt-orta) için kabaca **300-500 saat** gerekir ≈ günde 1 saat ile 10-16 ay; günde 2 saat ile 5-8 ay.
- Bir yıl-bir buçuk yılda, çalışmana göre **A2-B2** arası ulaşılabilir. **Tutarlılık** anahtardır: her gün biraz > haftada bir uzun seans.
- **Ücretsiz kaynaklar:** DW "Learn German" ve **Nicos Weg** (hem dil hem kültür). Düzenli plan sunar.

## 6) Kültür şoku: çoğu küçük şey
- Farklar genelde **küçük** ve alışırsın. En belirgini: **Pazar = dinlenme günü (Ruhezeit)** — Pazar gürültülü iş/DIY yapmak hoş karşılanmaz.
- Açık fikirli ve meraklıysan uyum kolay. (Daha fazlası: [kültürel uyum & nezaket](/tr/blog/cultural-etiquette-in-germany-respect-and-integration-for-students).)

## Sonuç
Almanya'da İngilizce ile **başlamak** tamamen mümkün: yaşarsın, arkadaş edinirsin, uluslararası alanlarda öğrenci işi bulursun, üstelik maliyet Avustralya/ABD'ye kıyasla çok düşük. Ama **kalıcı kariyer için Almanca'yı baştan ihmal etme** — İngilizce kapıyı açar, Almanca kapıyı **açık tutar**. Paralel git: İngilizce oku, Almancayı her gün biraz büyüt. İlgili: [Almancasız İngilizce Master](/tr/blog/english-masters-in-germany-without-german-your-complete-guide) · [Werkstudent: iş piyasasının gerçek anahtarı](/tr/blog/werkstudent-in-germany-the-real-key-to-the-job-market) · [yaşam kalitesi — dürüst bakış](/tr/blog/quality-of-life-in-germany-honest-pros-and-cons-for-students).

---
*Uluslararası öğrenci deneyimleri temel alınarak hazırlanmıştır. Dil koşulları ve iş piyasası alana/şehre göre değişir.*
MD;
        $excerpt = 'Almanca bilmeden Almanya\'da okumak/yaşamak mümkün mü? Evet ama bir şartla: İngilizce ile başlangıç, uluslararası şehir ortamı ve (CS/araştırma gibi alanlarda) öğrenci işi bulunur; ücret Avustralya/ABD\'ye kıyasla çok düşük. Asıl gerçek: tam-zamanlı şirket işi çoğu zaman B2-C1 Almanca ister — dili baştan ihmal etme. Bir yılda A2-B2, ücretsiz kaynaklar (DW, Nicos Weg).';
        $html = Str::markdown($body, ['html_input' => 'allow', 'allow_unsafe_links' => false]);
        $userId = DB::table('users')->where('id', 4)->exists() ? 4 : DB::table('users')->orderBy('id')->value('id');
        $categoryId = DB::table('categories')->where('id', 13)->exists() ? 13 : DB::table('categories')->where('slug', 'integration')->value('id');
        $payload = ['locale' => 'tr', 'user_id' => $userId, 'category_id' => $categoryId,
            'title' => 'Almanca Bilmeden Almanya\'da Okumak ve Yaşamak: Öğrenci İşi ve Dilin Gerçeği',
            'excerpt' => Str::limit($excerpt, 250, '…'), 'content_md' => $body, 'content_html' => $html,
            'meta_title' => 'Almanca Bilmeden Almanya\'da Okumak: Yaşam, Öğrenci İşi, Dil Gerçeği',
            'meta_description' => Str::limit($excerpt, 158, '…'),
            'reading_minutes' => max(1, (int) round(str_word_count(strip_tags($html)) / 200)),
            'is_published' => true, 'published_at' => now()];
        $existing = Post::where('slug', $slug)->first();
        if ($existing) { $existing->update($payload); } else { Post::create($payload + ['slug' => $slug, 'translation_group_id' => (string) Str::uuid()]); }
    }
    public function down(): void { Post::where('slug', 'studying-in-germany-without-german-living-and-student-job-reality')->delete(); }
};
