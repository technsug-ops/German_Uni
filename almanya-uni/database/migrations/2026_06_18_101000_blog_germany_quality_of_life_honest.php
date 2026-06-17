<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/** TR blog (geliş-sonrası serisi): Almanya'da yaşam kalitesi — dürüst artılar/eksiler. FK-safe. */
return new class extends Migration
{
    public function up(): void
    {
        $slug = 'quality-of-life-in-germany-honest-pros-and-cons-for-students';
        $body = <<<'MD'
"Almanya'da yaşam kalitesi yüksek" da doğru, "Almanya'da hayat zor" da. Gerçek, ikisinin **arasında** ve büyük ölçüde **uyum sağlamana** bağlı. Bu yazı, internetteki abartılı övgü ya da karalamalar yerine, öğrenci gözünden **dengeli** bir artı/eksi tablosu sunar.

## ✅ Gerçek artılar
- **Ücretsiz/çok ucuz üniversite** eğitimi ([gerçek maliyetler](/tr/blog/is-university-free-in-germany-2026-real-costs)).
- **Sağlık sistemine erişim**: sigortayla kapsamlı tedavi (hizmet anında ücretsiz).
- **Deutschlandticket / Semesterticket** ile ucuz toplu taşıma; yoğun tren ağı.
- **Güçlü kiracı ve işçi hakları**, sağlam tüketici koruması, hastalık izninde koruma.
- **Güvenlik**, temiz içme suyu, 30 gün standart izin.
- **Merkeziyetsiz** ülke: fırsatlar tek bir mega-şehirde toplanmamış; güçlü Mittelstand (KOBİ) → iş çeşitliliği.
- Güzel doğa (Alpler, göller, ormanlar, deniz), güçlü kulüp/spor altyapısı, dünyanın en iyi **çıraklık (Ausbildung)** sistemi.

## ⚠️ Dürüst eksiler (öğrencinin gerçekten karşılaşacağı)
- **Konut**: büyük şehirlerde oda/daire bulmak rekabetçi ve yorucu; özellikle yabancı olarak. "Eigenbedarf" gibi yasal tahliye boşlukları var. (Plan için: [bütçe gerçeği](/tr/blog/real-cost-of-being-a-student-in-germany-budget-truth).)
- **Sağlık "bedava" değil**: gelirinin ~%14,6'sı (işverenle paylaşılır); uzman/terapi randevuları **aylar** sürebilir.
- **Bürokrasi**: kâğıt, mektup, hatta faks; yavaş ve dijitalleşme zayıf ([yeni gelen bürokrasi rehberi](/tr/blog/germany-new-arrival-bureaucracy-guide-first-steps-institutions)).
- **Deutsche Bahn**: ucuz ama sık gecikme/iptal.
- **Hava**: yılın büyük kısmı gri/yağmurlu; kışın güneş erken batar — morali etkileyebilir.
- **Sosyal hayat**: arkadaşlık yavaş kurulur, kendini bir süre "dışarıdan" hissedebilirsin ([yalnızlık & ruh sağlığı](/tr/blog/loneliness-and-mental-health-as-an-international-student-in-germany)).
- **Vergiler** yüksek (üst gelirlerde maaşın büyük kısmı); Pazar günleri çoğu yer kapalı; Kita (kreş) sıraları uzun.

## Öğrenci için tablo aslında güçlü
Eksilerin çoğu (konut, bürokrasi, hava, sosyal) **gerçek** ama **yönetilebilir** — özellikle öğrenciysen artılar (ucuz/ücretsiz eğitim, Semesterticket, sağlık erişimi, güvenlik, ucuz öğrenci hayatı) ağır basar. Anahtar: **beklentini gerçekçi tutmak ve baştan plan yapmak.**

## Sonuç
Almanya **yüksek yaşam kalitesi sunuyor** — ama bu, Alman yaşam tarzına ve zihniyetine uyum sağlamayı gerektiriyor; herkes için değil. Sıcak, anında-samimi bir kültür beklersen zorlanırsın; sistemi ve sınırlarını bilerek gelirsen büyük değer bulursun. Dürüst beklenti = mutlu uyum. Devamı: [geliş-sonrası gerçek hayat rehberi](/tr/blog/germany-life-after-arrival-advice-to-past-self) · [şehir mi üniversite mi](/tr/blog/city-vs-university-which-matters-more-in-germany).

---
*Uluslararası öğrenci/sakin deneyimlerinden, dengeli ve karşılıklı bakışla derlenmiştir. Deneyim şehir/duruma göre değişir.*
MD;
        $excerpt = 'Almanya\'da yaşam kalitesi gerçekten yüksek mi? Öğrenci gözünden dengeli artı/eksi: ücretsiz eğitim, sağlık erişimi, ucuz ulaşım, güvenlik vs konut zorluğu, bürokrasi, DB gecikmeleri, hava, sosyal mesafe ve yüksek vergiler. Dürüst beklenti rehberi.';
        $html = Str::markdown($body, ['html_input' => 'allow', 'allow_unsafe_links' => false]);
        $userId = DB::table('users')->where('id', 6)->exists() ? 6 : DB::table('users')->orderBy('id')->value('id');
        $categoryId = DB::table('categories')->where('id', 9)->exists() ? 9 : DB::table('categories')->where('slug', 'yasam')->value('id');
        $payload = ['locale' => 'tr', 'user_id' => $userId, 'category_id' => $categoryId,
            'title' => 'Almanya\'da Yaşam Kalitesi: Öğrenci Gözünden Dürüst Artılar ve Eksiler',
            'excerpt' => Str::limit($excerpt, 250, '…'), 'content_md' => $body, 'content_html' => $html,
            'meta_title' => 'Almanya\'da Yaşam Kalitesi: Dürüst Artılar ve Eksiler (2026)',
            'meta_description' => Str::limit($excerpt, 158, '…'),
            'reading_minutes' => max(1, (int) round(str_word_count(strip_tags($html)) / 200)),
            'is_published' => true, 'published_at' => now()];
        $existing = Post::where('slug', $slug)->first();
        if ($existing) { $existing->update($payload); } else { Post::create($payload + ['slug' => $slug, 'translation_group_id' => (string) Str::uuid()]); }
    }
    public function down(): void { Post::where('slug', 'quality-of-life-in-germany-honest-pros-and-cons-for-students')->delete(); }
};
