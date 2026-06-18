<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/** TR blog (üniversite): Prestij miti + Uni vs FH (teori vs pratik) + pratik odaklı yollar (FH/duales Studium). FK-safe. */
return new class extends Migration
{
    public function up(): void
    {
        $slug = 'prestige-myth-german-universities-uni-vs-fh-practical-path';
        $body = <<<'MD'
"Prestijli bir Alman üniversitesine girmek için ruh sağlığımı feda etmeli miyim? Daha 'sakin' ve pratik odaklı okumak istesem nereye gitmeliyim?" — özellikle bilgisayar mühendisliği (CS) düşünenlerin sık sorduğu, çok haklı bir soru. Kısa cevap **rahatlatıcı**: Almanya'da "prestij" çoğunlukla bir **yanılgı/pazarlama** meselesidir; asıl önemli ayrım prestij değil, **Uni mı FH mı** sorusudur. Bu yazı, ruh sağlığını koruyarak doğru yolu seçmen için pratik bir pusula.

## Yanılgı 1: "Prestijli üniversite" Almanya'da büyük bir şey
Almanya'da ABD/İngiltere tarzı Ivy League **yoktur**. İşveren, hangi **devlet** üniversitesinden mezun olduğunla pek ilgilenmez — diploma + pratik deneyim önemlidir. "TUM en iyisi" algısı büyük ölçüde **güçlü pazarlamadan** doğar; Alman öğrencilerin çoğu "en prestijli" değil, **istediğim bölüm + yaşamak istediğim şehir** diye seçer. Yani prestij peşinde koşup kendini yıpratman **gerekmiyor**. (Detaylı karşılaştırma: [Devlet vs Özel vs FH](/tr/blog/public-vs-private-universities-germany-balanced-comparison).)

## Yanılgı 2: "Yüksek sıralı üniversite = daha zor/daha stresli"
Tüm devlet üniversiteleri **aynı standartlara** göre ders verir ve sınav yapar. "Top-ranked" bir okulun sınavları otomatik olarak daha zor değildir. Zorluk **kuruma değil, bölüme** bağlıdır (ör. tıp, CS, mühendislik her yerde yoğundur). "Prestijli okul = sosyal hayatın biter" varsayımı da gerçeği yansıtmaz — yoğunluk bölümün doğasıyla ilgilidir, okulun "ünüyle" değil. (Bkz. [Alman üniversiteleri zor mu?](/tr/blog/are-german-universities-hard-for-international-students-the-weeding-out-truth))

## Asıl ayrım: Universität (teori) vs Fachhochschule (pratik)
Senin aradığın "pratik odaklı" deneyim aslında bir **kurum tipi** seçimi:

| | **Universität** | **Fachhochschule / HAW** |
|---|---|---|
| Odak | Araştırma, **teori**, akademik derinlik | **Uygulama**, proje, sektör |
| CS gerçeği | Informatik ≈ yarı matematik (teorik) | Daha çok lab, proje, staj |
| Staj | Zorunlu değil (çoğu) | Genelde **zorunlu pratik dönem** |
| Doktora | Doğrudan açık | Sınırlı (genelde Uni ile) |
| Kabul | Daha rekabetçi olabilir | Sıklıkla **daha esnek** |

**Önemli:** "CS pratiktir, neden teori okuyayım?" yaygın bir yanılgı. **Üniversite tanımı gereği teoriktir.** Pratik istiyorsan üç gerçek yol var:

1. **Fachhochschule (FH/HAW)** — üniversite diploması, ama uygulama ağırlıklı. Pratik odaklı öğrenci için **en mantıklı tercih**.
2. **Duales Studium (ikili eğitim)** — üniversite + bir şirkette ücretli çalışma dönüşümlü. Hem maaş, hem iş tecrübesi, hem diploma. İş garantisine en yakın yol.
3. **Ausbildung / Fachinformatiker** — üniversite değil, meslek eğitimi; tamamen pratik ve istihdam odaklı.

## "Chill" gerçekten ne demek? Doğru beklenti
- Hiçbir yerde "sıfır stres" yok — ama stresi **pratik** tarafa kaydırmak mümkün: FH veya duales Studium tam bunu yapar.
- Ruh sağlığını koruyan asıl faktör **prestij değil**: sevdiğin şehir, makul kira, dengeli ders yükü, bir [Werkstudent](/tr/blog/werkstudent-in-germany-the-real-key-to-the-job-market) işiyle ritim. (İlham: [Almanya'da yaşam kalitesi — dürüst bakış](/tr/blog/quality-of-life-in-germany-honest-pros-and-cons-for-students).)
- Notun sınırdaysa, **açık-kabul (zulassungsfrei / NC'siz) programlar** baskıyı azaltır — bunları [program araması](/tr/programs)nda filtreleyebilir, FH/Uni'leri [üniversiteler](/tr/universities) sayfasında karşılaştırabilirsin.

## CS özelinde pratik not
- **Uni'de** CS okuyacaksan teoriye (matematik, algoritma, OS, ağlar) hazır ol — bu, uzun vadede güçlü bir temel.
- **Pratik** istiyorsan: güçlü **FH'lerde Angewandte Informatik / Wirtschaftsinformatik**, ya da bir şirketle **duales Studium**. İş piyasasında bu profiller fazlasıyla rağbet görür.
- "Hangi okul daha iyi" yerine "hangi **program** bana uygun ders/staj dengesi sunuyor" diye bak.

## Sonuç
Almanya'da prestij peşinde koşmak — özellikle ruh sağlığın pahasına — **gereksiz**. İşveren devlet diplomalarını ayrıştırmaz; önemli olan **Uni mı FH mı** ve **hangi program** sorusu. Pratik ve dengeli bir deneyim istiyorsan **FH veya duales Studium** senin yolun; akademik derinlik/araştırma istiyorsan **Universität**. İkisi de "iyi" — mesele senin hedefin. İlgili: [Devlet vs Özel vs FH](/tr/blog/public-vs-private-universities-germany-balanced-comparison) · [Bachelor mı Master mı](/tr/blog/bachelor-vs-master-in-germany-difficulty-language-work-for-internationals) · [İngilizce Master: FH vs Uni stratejisi](/tr/blog/english-master-admission-chances-germany-gpa-fh-vs-uni-strategy).

---
*Uluslararası öğrenci deneyimleri ve Alman yükseköğretim yapısı temel alınarak hazırlanmıştır. Kabul ve program koşulları okula/döneme göre değişir — resmi kaynaktan teyit et.*
MD;
        $excerpt = 'Almanya\'da "prestijli üniversite" çoğunlukla bir yanılgı/pazarlama: işveren devlet diplomalarını ayrıştırmaz, "top-ranked = daha zor" doğru değil. Asıl ayrım prestij değil Uni (teori/araştırma) vs FH (pratik). Pratik ve "chill" odaklı isteyenler için FH, duales Studium ve Ausbildung yolları + CS özelinde net tavsiye.';
        $html = Str::markdown($body, ['html_input' => 'allow', 'allow_unsafe_links' => false]);
        $userId = DB::table('users')->where('id', 4)->exists() ? 4 : DB::table('users')->orderBy('id')->value('id');
        $categoryId = DB::table('categories')->where('id', 12)->exists() ? 12 : DB::table('categories')->where('slug', 'universities')->value('id');
        $payload = ['locale' => 'tr', 'user_id' => $userId, 'category_id' => $categoryId,
            'title' => 'Almanya\'da Prestijli Üniversite Şart mı? Uni vs FH ve Pratik Odaklı Yol',
            'excerpt' => Str::limit($excerpt, 250, '…'), 'content_md' => $body, 'content_html' => $html,
            'meta_title' => 'Almanya Prestijli Üniversite Miti: Uni vs FH ve Pratik Odaklı Okumak',
            'meta_description' => Str::limit($excerpt, 158, '…'),
            'reading_minutes' => max(1, (int) round(str_word_count(strip_tags($html)) / 200)),
            'is_published' => true, 'published_at' => now()];
        $existing = Post::where('slug', $slug)->first();
        if ($existing) { $existing->update($payload); } else { Post::create($payload + ['slug' => $slug, 'translation_group_id' => (string) Str::uuid()]); }
    }
    public function down(): void { Post::where('slug', 'prestige-myth-german-universities-uni-vs-fh-practical-path')->delete(); }
};
