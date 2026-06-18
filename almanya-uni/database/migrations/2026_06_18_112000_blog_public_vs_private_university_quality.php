<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/** TR blog (üniversite): Devlet vs Özel vs FH — pozitif, dengeli karşılaştırma. "İyi/kötü" yerine "sana uygun mu". FK-safe. */
return new class extends Migration
{
    public function up(): void
    {
        $slug = 'public-vs-private-universities-germany-balanced-comparison';
        $body = <<<'MD'
"Almanya'da özel üniversiteler kötü mü, devlet mi seçmeliyim?" — forumlarda en çok dönen sorulardan biri ve cevap çoğu zaman keskin: *"özel = kötü."* Gerçek bundan daha **nüanslı** ve aslında daha **rahatlatıcı**: Almanya'da "kötü üniversite" listesi kovalamana gerek yok; çünkü sistem, **doğru soruyu** sorduğunda herkes için işleyen bir mantık üzerine kurulu. Bu yazıda kimseyi kötülemeden, üç modeli **güçlü yanlarıyla** karşılaştırıyoruz.

## Önce zihniyet: Almanya'da "genel sıralama" yoktur — ve bu iyi haber
ABD/İngiltere'deki Ivy League kültürünün aksine, Almanya'da üniversiteler topluca sıralanmaz. İki sağlam sebep:

1. **Devlet üniversiteleri vergiyle finanse edilir** → standartları bilinçli olarak **eşit** tutulur. "Hangisi daha üstün" yarışı yerine "hepsi yeterince iyi" hedeflenir.
2. **Üniversiteye değil, bölüme başvurursun.** "X Üniversitesi'ne girdim" değil, "X'te Makine Mühendisliği'ne girdim" denir. Yani kalite **kurum** değil, **bölüm + hoca + araştırma** düzeyinde anlam taşır.

Sonuç: Almanya'da diploman üzerindeki üniversite adı, kariyerinde **tek başına belirleyici değildir.** İşveren çoğunlukla dereceye + **pratik deneyime** (staj, [Werkstudent](/tr/blog/werkstudent-in-germany-the-real-key-to-the-job-market)) bakar. Bu, "prestijli okula giremezsem biterim" baskısını ortadan kaldırır.

## Üç model, üç güçlü yan

### 1) Devlet Üniversitesi (Universität) — akademik omurga
- **Güçlü yanı:** Ücretsiz (sadece dönem katkı payı), güçlü araştırma, doktora hakkı, geniş tanınırlık, köklü sanayi/hastane iş birlikleri.
- **Kimin için:** Akademik derinlik, araştırma, doktora veya teori-ağırlıklı kariyer hedefleyenler.
- **Not:** Belirli alanlarda gerçekten parlayan adresler vardır (ör. mühendislikte RWTH/TUM/KIT, tıp fakülteleri). Bu "genel üstünlük" değil, **alana özel** güçtür — senin bölümün için doğru adresi seçmek önemli.

### 2) Fachhochschule / HAW (Uygulamalı Bilimler) — istihdamın pratik yolu
- **Güçlü yanı:** Uygulamalı, sektör odaklı müfredat; zorunlu stajlar; küçük gruplar; **daha esnek kabul** koşulları. İş piyasasında pratik beceri çok değerlidir.
- **Kimin için:** Hızlı istihdam, somut beceri ve sektöre yakın eğitim isteyenler; orta not ortalamasıyla **güvenli kabul** arayan uluslararası öğrenciler.
- **Not:** "FH = düşük kalite" değil, **farklı misyon**. Pek çok mühendis ve bilişimci kariyerine FH'den başlar. (Bkz. [İngilizce Master: FH vs Uni stratejisi](/tr/blog/english-master-admission-chances-germany-gpa-fh-vs-uni-strategy).)

### 3) Özel Üniversite — network ve esneklik
- **Güçlü yanı:** Küçük sınıflar, yoğun mentorluk, esnek/çalışırken okunabilen programlar ve **iş/finans/hukukta güçlü mezun ağı**.
- **Gerçekten saygın olanlar (özellikle işletme/finans/hukuk):** WHU, Frankfurt School of Finance & Management, ESMT Berlin, HHL Leipzig, EBS, **Bucerius Law School**, **Hertie School**. Bunlar **seçicidir** — paranın değil, **başarının** kapı açtığı yerlerdir.
- **Pratik pusula:** *Para ile değil, devlet ünisinden bile zor sınavla girilen* özel üniler genelde değerlidir. Bunun dışındaki, "yüksek ücret + kolay kabul + yoğun uluslararası pazarlama" profili olanlarda ise **ödediğin paranın karşılığını net sorgula**: aynı bölümü ücretsiz devlet/FH'de okuyabiliyor musun?

## "İyi/kötü" değil — doğru soru: *Bu program hedefime uygun mu?*
Üniversiteyi yargılamak yerine **şu üç ekseni** tart:

| Eksen | Sor kendine |
|---|---|
| **Alan** | Bölümüm hangi modelde güçlü? (Mühendislik/tıp → genelde devlet; işletme/finans → seçici özel de güçlü) |
| **Hedef** | Doktora/araştırma mı, hızlı istihdam mı, network mü? |
| **Bütçe & tanınırlık** | Ücretli okumanın getirdiği ekstra ne? Diploma Türkiye/AB'de nasıl tanınıyor? |

## Kabul tarafı: NC, zulassungsfrei ve akıllı başvuru
Almanya'da kabul "izlenim" değil **kural** işidir. Bazı bölümler **NC (kontenjan sınırı)** ile, çoğu ise **zulassungsfrei (açık kabul)** ile çalışır. Notun sınırdaysa, **açık-kabul (NC'siz) programlar** seçeneklerini genişletir — bunları doğrudan [program araması](/tr/programs)nda filtreleyebilir, üniversiteleri [üniversiteler](/tr/universities) sayfasından karşılaştırabilirsin. Genel kabul zorluğu için: [Alman üniversiteleri zor mu?](/tr/blog/are-german-universities-hard-for-international-students-the-weeding-out-truth)

## Sonuç
Almanya'da "kötü üniversiteden kaç" mantığı yerine **"hedefime hangi model uyuyor"** mantığı geçerli:
- **Devlet** → akademik derinlik, araştırma, ücretsiz, geniş tanınırlık.
- **FH** → uygulamalı, istihdam odaklı, esnek kabul.
- **Özel** → küçük sınıf + network; iş/finans/hukukta seçici olanlar gerçekten güçlü, gerisinde fiyat/değer dengesini sorgula.

Üçü de doğru öğrenci için **doğru tercih** olabilir. Önemli olan kurumun etiketi değil, **senin bölümün + hedefin** ile örtüşmesi. İlgili rehberler: [Bachelor mı Master mı](/tr/blog/bachelor-vs-master-in-germany-difficulty-language-work-for-internationals) · [İngilizce Master tam rehber](/tr/blog/english-masters-in-germany-without-german-your-complete-guide) · [uni-assist A-Z](/tr/blog/uni-assist-application-guide-a-z-your-step-by-step-path).

---
*Uluslararası öğrenci deneyimleri ve Alman yükseköğretim yapısı temel alınarak hazırlanmıştır. Tanınırlık ve kabul koşulları programa/döneme göre değişir — resmi kaynaktan teyit et.*
MD;
        $excerpt = 'Almanya\'da "özel üniversite kötü mü, devlet mi?" sorusuna dengeli, kötülemeyen bir cevap: genel sıralama neden yok, devlet/FH/özel üç modelin güçlü yanları, gerçekten saygın özel üniler (WHU, Frankfurt School, Bucerius…) ve "iyi/kötü" yerine "hedefime uygun mu" pusulası + NC/açık-kabul başvuru ipuçları.';
        $html = Str::markdown($body, ['html_input' => 'allow', 'allow_unsafe_links' => false]);
        $userId = DB::table('users')->where('id', 4)->exists() ? 4 : DB::table('users')->orderBy('id')->value('id');
        $categoryId = DB::table('categories')->where('id', 12)->exists() ? 12 : DB::table('categories')->where('slug', 'universities')->value('id');
        $payload = ['locale' => 'tr', 'user_id' => $userId, 'category_id' => $categoryId,
            'title' => 'Almanya\'da Devlet, Özel ve FH Üniversiteleri: Kötülemeyen Dengeli Karşılaştırma',
            'excerpt' => Str::limit($excerpt, 250, '…'), 'content_md' => $body, 'content_html' => $html,
            'meta_title' => 'Almanya Devlet vs Özel Üniversite: Dengeli Karşılaştırma (FH Dahil)',
            'meta_description' => Str::limit($excerpt, 158, '…'),
            'reading_minutes' => max(1, (int) round(str_word_count(strip_tags($html)) / 200)),
            'is_published' => true, 'published_at' => now()];
        $existing = Post::where('slug', $slug)->first();
        if ($existing) { $existing->update($payload); } else { Post::create($payload + ['slug' => $slug, 'translation_group_id' => (string) Str::uuid()]); }
    }
    public function down(): void { Post::where('slug', 'public-vs-private-universities-germany-balanced-comparison')->delete(); }
};
