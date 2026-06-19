<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * TR blog: Almanya'da uluslararası öğrenciler için yarı zamanlı iş rehberi (2026).
 * Shiksha "Part-Time Work Options for International Students in Germany" makalesinden
 * esinlenip 2026 kurallarıyla (140/280 gün, €13,90 asgari ücret, €603 mini-job) güncellendi.
 * FK-safe + idempotent (slug-bazlı upsert).
 */
return new class extends Migration
{
    public function up(): void
    {
        $slug = 'part-time-jobs-germany-international-students-guide';
        $body = <<<'MD'
Almanya'da okumak yaşam masraflarını karşılamak için iyi bir plan ister. İyi haber: yarı zamanlı çalışmak hem yasal hem yaygın. 2026'da kurallar da öğrenci lehine gevşedi.

Bu rehber; kaç gün çalışabileceğini, hangi iş tiplerinin olduğunu, ne kadar kazanabileceğini ve nelere dikkat etmen gerektiğini sade biçimde özetliyor.

## Kaç saat / kaç gün çalışabilirsin? (2026 kuralı)
AB-dışı (Türkiye dahil) öğrenciler için yıllık çalışma hakkı **2026'da 120 tam günden 140 tam güne** çıkarıldı:

- **140 tam gün** *veya* **280 yarım gün** / yıl.
- Alternatif olarak **haftada 20 saate** kadar.
- **Yarım gün** = günde ≤4 saat; **tam gün** = günde >4 saat.

Ders döneminde haftada **20 saati aşmamak** önemli — aşarsan "öğrenci" statüsünün sigorta avantajını kaybedersin. **Sömestr tatilinde tam zamanlı** çalışabilirsin, ama bu günler yıllık 140/280 hesabına eklenir.

> **AB/AEA öğrencileri** Almanlarla aynı haklara sahiptir; gün limiti yoktur. Yine de öğrenci sağlık sigortası avantajı için ders döneminde 20 saat sınırı geçerlidir.

## Ne kadar kazanırsın?
- **Asgari ücret (1 Ocak 2026):** brüt **€13,90 / saat**.
- Haftada 20 saat ≈ aylık **€1.000–1.200** civarı brüt (işe göre değişir).

Bu gelir güzel bir destek; ama vize için gereken **bloke hesabın (Sperrkonto) yerine geçmez** — finansal kanıtı yine ayrıca göstermen gerekir. Detay: [Sperrkonto (bloke hesap) nedir](/tr/blog/sperrkonto-for-a-german-visa-what-is-it-how-much-and).

## İş tipleri: hangisi sana uygun?

| İş tipi | Ne demek | Kime uygun |
|---|---|---|
| **Mini-job** | Aylık ≤ **€603** (2026); vergi/sigorta avantajlı | Hızlı, esnek ek gelir isteyen |
| **Werkstudent** | Alanında, ders döneminde ≤20 saat; sosyal sigortada büyük muafiyet | CV + alan tecrübesi isteyen |
| **HiWi** (öğrenci/araştırma asistanı) | Üni içinde akademik destek işi | Akademik kariyer / esneklik isteyen |
| **Praktikum** (staj) | Zorunlu veya gönüllü staj | Program gereği / sektör deneyimi |
| **Gastronomi / perakende / lojistik** | Kafe, market, depo, kurye | Almancası yeni başlayan, esnek saat isteyen |

### Werkstudent neden değerli?
Ders döneminde haftada 20 saate kadar **kendi alanında** çalışırsın; sağlık, işsizlik ve bakım sigortasından **muafsın** (sadece ~%9,3 emeklilik primi ödersin). Mezuniyette çoğu öğrenci zaten **1–2 yıl sektör tecrübesiyle** çıkar. Derinlemesine: [Werkstudent: iş piyasasının asıl anahtarı](/tr/blog/werkstudent-in-germany-the-real-key-to-the-job-market).

### HiWi (üni içi iş)
Bölümünde araştırma/ders desteği yaparsın. Esnek saatler, akademik referans ve kampüse yakınlık avantajdır; akademik kariyer düşünüyorsan ideal başlangıçtır.

## Vergi ve sigorta: kısa özet
- **Mini-job (≤€603/ay):** pratikte vergi kesilmez; sigorta yükü minimaldir.
- **Werkstudent:** sağlık/işsizlik/bakım sigortası muaf, emeklilik primi var.
- Yıllık gelirin **temel ödeneğin (~€12.000+)** altındaysa, kesilen gelir vergisini (Lohnsteuer) **Steuererklärung** ile geri alabilirsin.

## Başlamadan önce gerekenler
1. **Vergi numarası (Steuer-ID)** — Anmeldung sonrası posta ile gelir.
2. **Alman banka hesabı** — maaşın yatması için.
3. **Sağlık sigortası** — öğrenci tarifesi.
4. **Oturum izni** — çalışma hakkı ve gün limiti izninin üstünde yazılıdır; kontrol et.

## Nereden iş bulunur?
- Üniversitenin **kariyer portalı / ilan panosu**.
- **Studentenwerk** ve öğrenci iş borsaları (ör. Jobmensa).
- Şirketlerin **kariyer sayfaları** (Werkstudent ilanları).
- Kampüs içi **HiWi** ilanları (bölüm sekreterliği / panolar).

## En önemli uyarı: limiti aşma
140 gün / 20 saat sınırını aşmak küçük bir idari mesele değildir; **oturum izni incelemesine** ve ağır durumlarda **mezuniyet sonrası çalışma izni başvurunun reddine** yol açabilir. Saatlerini kayıt altında tut.

---
*2026 kuralları temel alınarak hazırlanmıştır. Çalışma hakkı, limitler ve eşik tutarları değişebilir — başvurudan önce resmî kaynaktan (yabancılar dairesi / üniversite) teyit et.*
MD;

        $excerpt = 'Almanya\'da uluslararası öğrenciler için yarı zamanlı iş rehberi (2026): yeni 140 tam / 280 yarım gün kuralı, €13,90 asgari ücret, €603 mini-job sınırı, Werkstudent ve HiWi avantajları, vergi & sigorta özeti, iş bulma kanalları ve limiti aşmanın riskleri.';
        $html = Str::markdown($body, ['html_input' => 'allow', 'allow_unsafe_links' => false]);
        $userId = DB::table('users')->where('id', 4)->exists() ? 4 : DB::table('users')->orderBy('id')->value('id');
        $categoryId = DB::table('categories')->where('slug', 'yasam')->value('id')
            ?? DB::table('categories')->where('slug', 'student-life')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $payload = [
            'locale' => 'tr', 'user_id' => $userId, 'category_id' => $categoryId,
            'title' => 'Almanya\'da Yarı Zamanlı İş: Uluslararası Öğrenci Rehberi (2026)',
            'excerpt' => Str::limit($excerpt, 250, '…'),
            'content_md' => $body, 'content_html' => $html,
            'source_url' => 'https://www.shiksha.com/studyabroad/part-time-work-options-for-international-students-in-germany-articlepage-532',
            'source_name' => 'Shiksha',
            'meta_title' => 'Almanya\'da Yarı Zamanlı İş — Öğrenci Çalışma Rehberi 2026',
            'meta_description' => Str::limit($excerpt, 158, '…'),
            'reading_minutes' => max(1, (int) round(str_word_count(strip_tags($html)) / 200)),
            'is_published' => true, 'published_at' => now(),
        ];

        $existing = Post::where('slug', $slug)->first();
        if ($existing) {
            $existing->update($payload);
        } else {
            Post::create($payload + ['slug' => $slug, 'translation_group_id' => (string) Str::uuid()]);
        }
    }

    public function down(): void
    {
        Post::where('slug', 'part-time-jobs-germany-international-students-guide')->delete();
    }
};
