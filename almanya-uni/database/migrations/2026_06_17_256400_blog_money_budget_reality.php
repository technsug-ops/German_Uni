<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/** TR blog (geliş-sonrası serisi): Para & bütçe gerçeği — tamponla gel. FK-safe. */
return new class extends Migration
{
    public function up(): void
    {
        $slug = 'real-cost-of-being-a-student-in-germany-budget-truth';
        $body = <<<'MD'
[Sperrkonto](/tr/blog/sperrkonto-for-a-german-visa-what-is-it-how-much-and)'na yıllık parayı yatırmak bir şey; **Almanya'da gerçekten geçinmek** başka bir şey. Bütçeyi hafife alan öğrenciler ilk aylarda zorlanıyor. Bu yazı, kimsenin önceden söylemediği **para gerçeklerini** anlatıyor.

## En önemli tavsiye: tamponla gel
Para sıkıntın varsa, gelir gelmez **part-time işe bağımlı** kalmadan geçinebileceğin bir **tampon** ile gel. Çünkü:
- Tam zamanlı okuyup part-time çalışmak **çok yorucu** ve sürdürülmesi zor.
- Werkstudent/part-time iş bulmak da **kolay değil** ([Werkstudent gerçeği](/tr/blog/werkstudent-in-germany-the-real-key-to-the-job-market)).
- Kötü bir WG veya yanlış şehir çıkarsa, **taşınabilmek için** finansal esnekliğe ihtiyacın olur.

## İlk aylar pahalıdır
- **Depozito (Kaution):** genelde 2-3 aylık soğuk kira — peşin.
- **İlk + güncel kira**, bazen komisyon.
- **Mobilya/ev eşyası**, mutfak, yatak (WG'de bazen hazır gelir).
- Anmeldung'a kadar **geçici konaklama** (hostel/Airbnb) — pahalı.
- Bunların hepsi Sperrkonto'dan aylık serbest kalan ~992 €'nun **dışında** ve önce.

## Düzenli giderler — beklenmedikler dahil
- Kira (büyük şehirlerde WG odası 400-700 €+), yemek, ulaşım (Semesterticket çoğu yerde dahil).
- **Sağlık sigortası** (öğrenci ~120 €/ay — [Krankenkasse karşılaştırma](/tr/blog/health-insurance-comparison-2026-which-krankenkasse-is-best-for-students-in)).
- **Rundfunkbeitrag** (yayın katkısı, ~18,36 €/ay — WG'de paylaşılır).
- **Semesterbeitrag** (dönem ücreti, ~150-350 €/dönem).
- Telefon/internet, kişisel harcamalar.

> 💡 ~992 €/ay (Sperrkonto serbest tutarı) **büyük/pahalı şehirlerde** (Münih, Frankfurt, Hamburg) gerçekçi olmayabilir; şehir seçimi bütçeyi doğrudan etkiler ([şehir mi üniversite mi](/tr/blog/city-vs-university-which-matters-more-in-germany)).

## Pratik öneriler
- **Gerçekçi bir aylık bütçe** çıkar (şehir bazında); [üniversite ücretsiz mi & gerçek maliyetler](/tr/blog/is-university-free-in-germany-2026-real-costs) yazısına bak.
- WG odasını erken ara; pahalı geçici konaklamayı kısa tut.
- İlk dönem işe bel bağlama; ders + dil + uyum zaten yoğun.
- [Schufa](/tr/blog/schufa-guide-2026-why-is-credit-score-important-for-turkish-students) ve banka hesabını erken kur.

## Sonuç
Almanya'da öğrencilik **ücretsiz eğitim** demek olsa da **bedava yaşam** demek değil. Tamponlu gel, ilk-kurulum masraflarını ve beklenmedik kalemleri hesaba kat, şehrini bütçene göre seç. Para esnekliği, yalnızca rahatlık değil — **WG/şehir değiştirebilmek ve ruh sağlığın** için de kritik ([yalnızlık & ruh sağlığı](/tr/blog/loneliness-and-mental-health-as-an-international-student-in-germany)).

---
*Uluslararası öğrenci deneyimlerinden ve topluluğumuzdan derlenmiştir. Rakamlar şehir/yıla göre değişir.*
MD;
        $excerpt = 'Almanya\'da öğrenci olmanın gerçek maliyeti: tamponla gel, ilk aylar pahalı (Kaution 2-3 kira, mobilya, geçici konaklama), ~992 € büyük şehirde yetmeyebilir, part-time garanti değil. Beklenmedik giderler ve gerçekçi bütçe.';
        $html = Str::markdown($body, ['html_input' => 'allow', 'allow_unsafe_links' => false]);
        $userId = DB::table('users')->where('id', 6)->exists() ? 6 : DB::table('users')->orderBy('id')->value('id');
        $categoryId = DB::table('categories')->where('id', 9)->exists() ? 9 : DB::table('categories')->where('slug', 'yasam')->value('id');
        $payload = ['locale' => 'tr', 'user_id' => $userId, 'category_id' => $categoryId,
            'title' => 'Almanya\'da Öğrenci Olmanın Gerçek Maliyeti: Bütçe Gerçeği (Tamponla Gel)',
            'excerpt' => Str::limit($excerpt, 250, '…'), 'content_md' => $body, 'content_html' => $html,
            'meta_title' => 'Almanya\'da Öğrenci Bütçesi: Gerçek Maliyetler ve Beklenmedik Giderler',
            'meta_description' => Str::limit($excerpt, 158, '…'),
            'reading_minutes' => max(1, (int) round(str_word_count(strip_tags($html)) / 200)),
            'is_published' => true, 'published_at' => now()];
        $existing = Post::where('slug', $slug)->first();
        if ($existing) { $existing->update($payload); } else { Post::create($payload + ['slug' => $slug, 'translation_group_id' => (string) Str::uuid()]); }
    }
    public function down(): void { Post::where('slug', 'real-cost-of-being-a-student-in-germany-budget-truth')->delete(); }
};
