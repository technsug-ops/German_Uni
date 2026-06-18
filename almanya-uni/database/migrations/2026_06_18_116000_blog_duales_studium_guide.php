<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/** TR blog (eğitim): Duales Studium — maaşlı/ikili eğitim tam rehber. Pratik odaklı yol. FK-safe. */
return new class extends Migration
{
    public function up(): void
    {
        $slug = 'duales-studium-germany-paid-study-complete-guide';
        $body = <<<'MD'
"Almanya'da okurken hem maaş alıp hem iş tecrübesi kazanmak mümkün mü?" Evet — adı **Duales Studium (ikili eğitim).** Üniversite/akademi eğitimini bir şirkette **ücretli çalışmayla** birleştiren, pratik odaklı ve iş garantisine en yakın yollardan biri. Pratik isteyen ([prestij değil pratik](/tr/blog/prestige-myth-german-universities-uni-vs-fh-practical-path)) öğrenciler için güçlü bir seçenek. İşte A'dan Z'ye.

## Duales Studium nedir?
Eğitimin **iki ayağı** vardır ve dönüşümlüdür:
1. **Teori:** Bir Hochschule / Berufsakademie / Duale Hochschule'de (ör. DHBW) dersler.
2. **Pratik:** Bir **şirkette** düzenli, ücretli çalışma dönemleri.

Sonunda **tanınan bir diploma** (genelde Bachelor) + ciddi **iş tecrübesi** + çoğu zaman şirkette **kalma fırsatı** alırsın.

## Türleri
- **Ausbildungsintegrierend:** Diploma + bir **Ausbildung** (meslek sertifikası) birlikte.
- **Praxisintegrierend:** Diploma + yoğun şirket pratiği (ayrı Ausbildung sertifikası yok).
- (Çalışanlara yönelik berufs-/ausbildungsbegleitend varyantlar da vardır.)

## Avantajları
- **Maaş:** Okurken aylık ücret alırsın (alan/şirkete göre değişir) — ailene daha az yük.
- **İş tecrübesi:** Mezun olduğunda zaten **2-3 yıl** gerçek deneyimin olur.
- **İstihdam:** Şirketler genelde dual öğrencilerini **işe alır**; iş bulma kaygısı düşük.
- **Harç yok/çok düşük:** Çoğu programda eğitim ücretini **şirket** üstlenir.

## Zorlukları (dürüst olalım)
- **Yoğun tempo:** "Boş yaz tatili" yok; teori + iş aynı anda. Disiplin ister.
- **Daha az esneklik:** Şirkete bağlısın; bölüm/şehir değiştirmek zordur.
- **Dil:** Çoğu dual program **Almanca** yürür ve şirket Almanca ister → genelde **B2-C1 Almanca** beklenir. (İngilizce dual program azdır.)
- **Rekabet:** İyi şirketlerin dual kontenjanları popülerdir; başvuru erken ve ciddidir.

## Nasıl başvurulur? (önemli fark)
Normal üniversite başvurusundan farklı: **önce şirketi bulursun.**
1. **Şirket ara:** Dual program sunan firmalara (büyük şirketler, Mittelstand) **doğrudan** başvur. Genelde başvuru **1-1.5 yıl önceden** açılır.
2. Şirketle **sözleşme** yaparsın; o da seni partner Hochschule/Akademie'ye yerleştirir.
3. CV + motivasyon + çoğu zaman mülakat/değerlendirme. (CV için: [şablonlar](/tr/templates) faydalı olabilir.)

## Kimler için ideal?
- Pratik/uygulama seven, "teori ağırlıklı klasik üniversite" istemeyenler.
- Okurken **gelir** ve **net iş yolu** isteyenler.
- **Almancası iyi** (en az B2 hedefleyen) ve Almanya'da **uzun vadeli** kalmayı planlayanlar.

> Almancan henüz yeterli değilse: önce dili güçlendir, paralelinde [İngilizce programlar](/tr/programs?language=en) veya klasik [FH](/tr/blog/public-vs-private-universities-germany-balanced-comparison) yolunu değerlendir; dual'a sonra geçebilirsin.

## Duales Studium vs Werkstudent vs klasik staj
- **Duales Studium:** Eğitimin **yapısal parçası** — program şirketle entegre, diploma + tecrübe bir arada.
- **[Werkstudent](/tr/blog/werkstudent-in-germany-the-real-key-to-the-job-market):** Klasik üniversitede okurken **yan iş** (haftada ~20 saat) — programdan bağımsız ama iş piyasasına açılan kapı.
- İkisi de pratik kazandırır; dual daha bağlayıcı ve garantili, Werkstudent daha esnek.

## Sonuç
Duales Studium, Almanya'da **maaşlı + iş garantisine yakın + pratik** okumak isteyenler için en güçlü yollardan biri — karşılığında yoğun tempo ve **iyi Almanca** ister. Pratik odaklıysan ama dual sana fazla bağlayıcı geliyorsa, **FH + Werkstudent** kombinasyonu esnek bir alternatif. İlgili: [Prestij miti & Uni vs FH](/tr/blog/prestige-myth-german-universities-uni-vs-fh-practical-path) · [Devlet vs Özel vs FH](/tr/blog/public-vs-private-universities-germany-balanced-comparison) · programları [karşılaştır](/tr/programs).

---
*Genel rehberdir. Program türleri, ücretler ve dil koşulları şirkete/okula göre değişir — resmi kaynaktan teyit et.*
MD;
        $excerpt = 'Duales Studium (ikili eğitim): Almanya\'da okurken maaş alıp iş tecrübesi kazanmanın yolu. Türleri (ausbildungs-/praxisintegrierend), avantajları (maaş, istihdam, harçsız), zorlukları (yoğun tempo, B2-C1 Almanca şartı), "önce şirketi bul" başvuru mantığı ve Werkstudent ile farkı. Pratik odaklı öğrenci için güçlü seçenek.';
        $html = Str::markdown($body, ['html_input' => 'allow', 'allow_unsafe_links' => false]);
        $userId = DB::table('users')->where('id', 4)->exists() ? 4 : DB::table('users')->orderBy('id')->value('id');
        $categoryId = DB::table('categories')->where('id', 8)->exists() ? 8 : DB::table('categories')->where('slug', 'basvuru')->value('id');
        $payload = ['locale' => 'tr', 'user_id' => $userId, 'category_id' => $categoryId,
            'title' => 'Duales Studium: Almanya\'da Maaşlı Okumak — A\'dan Z\'ye Rehber',
            'excerpt' => Str::limit($excerpt, 250, '…'), 'content_md' => $body, 'content_html' => $html,
            'meta_title' => 'Duales Studium Nedir? Almanya\'da Maaşlı İkili Eğitim Rehberi',
            'meta_description' => Str::limit($excerpt, 158, '…'),
            'reading_minutes' => max(1, (int) round(str_word_count(strip_tags($html)) / 200)),
            'is_published' => true, 'published_at' => now()];
        $existing = Post::where('slug', $slug)->first();
        if ($existing) { $existing->update($payload); } else { Post::create($payload + ['slug' => $slug, 'translation_group_id' => (string) Str::uuid()]); }
    }
    public function down(): void { Post::where('slug', 'duales-studium-germany-paid-study-complete-guide')->delete(); }
};
