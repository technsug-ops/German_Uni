<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/** TR blog (geliş-sonrası serisi): Şehir mi üniversite mi? FK-safe. */
return new class extends Migration
{
    public function up(): void
    {
        $slug = 'city-vs-university-which-matters-more-in-germany';
        $body = <<<'MD'
Çoğu öğrenci üniversiteyi **sıralamaya** göre seçer ve şehri ikinci plana atar. Ama Almanya'da okuyanların sık tekrarladığı bir gerçek var: **yaşadığın şehir, çoğu zaman üniversitenin kendisinden daha çok belirleyici.** Bu yazı ikisini dürüstçe tartıyor.

## Şehir neyi belirler?
- **Yaşam maliyeti:** Münih/Frankfurt ile Leipzig/Aachen arasında kira uçurumu var — bütçeni doğrudan etkiler ([bütçe gerçeği](/tr/blog/real-cost-of-being-a-student-in-germany-budget-truth)).
- **İş & Werkstudent imkânı:** Sanayi/teknoloji yoğun bölgeler (Stuttgart, Münih, Ren-Main) daha çok [Werkstudent/staj](/tr/blog/werkstudent-in-germany-the-real-key-to-the-job-market) ve mezuniyet işi sunar.
- **Topluluk & sosyal hayat:** Büyük/uluslararası şehirler arkadaşlık ve aidiyeti kolaylaştırır — [yalnızlığa](/tr/blog/loneliness-and-mental-health-as-an-international-student-in-germany) karşı önemli.
- **Dil ortamı & staj ağı**, ulaşım, kültür.

## Üniversite neyi belirler?
- **Program kalitesi**, müfredat, belirli laboratuvar/profesörler, araştırma imkânı.
- **İtibar** — ama Alman iş piyasasında çoğu pozisyonda diploma prestiji, sandığından **daha az** belirleyici; deneyim/beceri önce gelir ([iş piyasası gerçeği](/tr/blog/germany-job-market-reality-after-graduation-for-international-students)).
- TU9 gibi güçlü teknik üniversiteler bazı alanlarda fark yaratır, ama tek başına iş garantisi değildir.

## Peki nasıl karar vermeli?
İkisini **birlikte** değerlendir:
1. **Programın senin alanına uygunluğu** (en önemli akademik kriter — bölüm/içerik).
2. **Şehrin iş/staj piyasası** (alanında firma yoğunluğu var mı?).
3. **Yaşam maliyeti ↔ bütçen.**
4. **Sosyal/topluluk** uyumu.

İdeal: alanına uygun, makul maliyetli, iş imkânı güçlü bir **şehir + program** kombinasyonu. Sırf sıralama için pahalı/izole bir tercihe sıkışma.

## Sonuç
Üniversite önemli, ama Almanya'da **deneyimini, bütçeni, iş imkânını ve sosyal hayatını** günlük olarak şekillendiren şey **şehir**. Karar verirken şehri en az üniversite kadar ciddiye al. Araştırmak için: [eyalet ve şehir rehberlerimiz](/tr/states). Devamı: [geliş-sonrası gerçek hayat rehberi](/tr/blog/germany-life-after-arrival-advice-to-past-self).

---
*Uluslararası öğrenci deneyimlerinden ve topluluğumuzdan derlenmiştir.*
MD;
        $excerpt = 'Almanya\'da şehir mi üniversite mi daha önemli? Şehir; yaşam maliyetini, Werkstudent/iş imkânını, topluluğu ve sosyal hayatı belirler — çoğu zaman üni sıralamasından çok. İkisini birlikte değerlendirme rehberi.';
        $html = Str::markdown($body, ['html_input' => 'allow', 'allow_unsafe_links' => false]);
        $userId = DB::table('users')->where('id', 6)->exists() ? 6 : DB::table('users')->orderBy('id')->value('id');
        $categoryId = DB::table('categories')->where('id', 9)->exists() ? 9 : DB::table('categories')->where('slug', 'yasam')->value('id');
        $payload = ['locale' => 'tr', 'user_id' => $userId, 'category_id' => $categoryId,
            'title' => 'Şehir mi Üniversite mi? Almanya\'da Hangisi Daha Önemli',
            'excerpt' => Str::limit($excerpt, 250, '…'), 'content_md' => $body, 'content_html' => $html,
            'meta_title' => 'Almanya\'da Şehir mi Üniversite mi Daha Önemli? Karar Rehberi',
            'meta_description' => Str::limit($excerpt, 158, '…'),
            'reading_minutes' => max(1, (int) round(str_word_count(strip_tags($html)) / 200)),
            'is_published' => true, 'published_at' => now()];
        $existing = Post::where('slug', $slug)->first();
        if ($existing) { $existing->update($payload); } else { Post::create($payload + ['slug' => $slug, 'translation_group_id' => (string) Str::uuid()]); }
    }
    public function down(): void { Post::where('slug', 'city-vs-university-which-matters-more-in-germany')->delete(); }
};
