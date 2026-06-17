<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/** TR blog (geliş-sonrası serisi): Werkstudent gerçeği — alan-içi deneyim > not. FK-safe. */
return new class extends Migration
{
    public function up(): void
    {
        $slug = 'werkstudent-in-germany-the-real-key-to-the-job-market';
        $body = <<<'MD'
Almanya'da uluslararası öğrencilerin kariyerini belirleyen tek faktör soruluyorsa, cevap çoğu zaman **Werkstudent (working student) deneyimi** — diploma notların değil. Bu yazı, neden bu kadar kritik olduğunu ve nasıl yararlanacağını anlatıyor.

## Werkstudent nedir?
Üniversiteye kayıtlı öğrencilerin, **dönem içinde haftada en fazla 20 saat** (tatilde tam zamanlı) bir firmada çalıştığı statüdür. (Çalışma kuralları için: [öğrenci çalışma izni](/tr/blog/student-work-permit-in-germany-2026-20-hour-rule-and-types).) Önemli olan **alan-içi** bir Werkstudent pozisyonu olması — kasiyerlik değil, okuduğun alanda iş.

## Neden notlardan önemli?
Bir işe alma sürecine katılanların ortak görüşü çarpıcı: *"2.3 + 2 yıl alan-içi Werkstudent olan birini, 1.0 + hiç deneyimi olmayandan önce işe alırım."* Firmalar **notuna değil**, pozisyon için gerekli **araç/beceri** deneyimine bakar — bunu projeler, tez ve özellikle Werkstudent/Praktikum ile kazanırsın. Werkstudent, Alman piyasasındaki **yerel deneyim eksiğini** kapatan en güçlü farktır.

## Gerçek: bulması zor, ama değer
- Alan-içi Werkstudent bulmak **kolay değil** ve rekabetlidir; çoğu için **B2/C1 Almanca** gerekir ([iş için Almanca gerçeği](/tr/blog/german-language-reality-for-jobs-in-germany-the-honest-truth)).
- Okul + iş **yorucudur** ve bir dönem uzatabilir — ama mezuniyette seni öne çıkaran şey budur.
- 20'lerinde, bakman gereken bir ailen yokken bu enerjiyi harcamanın tam zamanı.

## Nasıl bulunur?
- Firma kariyer sayfaları (büyük şirketler düzenli Werkstudent ilanı verir), **LinkedIn**, StepStone, Indeed, Glassdoor.
- Üniversitenin **kariyer merkezi (Career Service)** ve şirket sunumları/kariyer fuarları.
- **Praktikum → Werkstudent**: zorunlu/gönüllü stajı bir köprü olarak kullan.
- Profesör/araştırma grubu üzerinden **HiWi** (öğrenci asistanlığı) — akademik deneyim için.
- Başvurularını erken yap, CV'ni Alman formatına uydur, Almancanı geliştir.

## Sonuç
Almanya'da mezuniyetten sonra iş bulmanın anahtarı, **okurken alan-içi deneyim biriktirmektir** — ve bunun en iyi yolu Werkstudent. Zor ve yorucu olabilir, bir dönem uzatabilir; ama notların değil, **deneyimin** seni işe sokar. Erken başla, Almancanı büyüt, sabırlı ol. Devamı: [geliş-sonrası gerçek hayat rehberi](/tr/blog/germany-life-after-arrival-advice-to-past-self) · [mezuniyet sonrası iş piyasası](/tr/blog/germany-job-market-reality-after-graduation-for-international-students).

---
*Uluslararası öğrenci/mezun deneyimlerinden ve topluluğumuzdan derlenmiştir.*
MD;
        $excerpt = 'Werkstudent (working student) gerçeği: Almanya iş piyasasında notlar değil, alan-içi deneyim konuşur — "2.3 + 2 yıl Werkstudent, 1.0 + hiç deneyimden iyidir." Neden kritik, nasıl bulunur, dengesi nasıl kurulur.';
        $html = Str::markdown($body, ['html_input' => 'allow', 'allow_unsafe_links' => false]);
        $userId = DB::table('users')->where('id', 7)->exists() ? 7 : DB::table('users')->orderBy('id')->value('id');
        $categoryId = DB::table('categories')->where('id', 9)->exists() ? 9 : DB::table('categories')->where('slug', 'yasam')->value('id');
        $payload = ['locale' => 'tr', 'user_id' => $userId, 'category_id' => $categoryId,
            'title' => 'Werkstudent Gerçeği: Almanya İş Piyasasının Asıl Anahtarı (Not Değil Deneyim)',
            'excerpt' => Str::limit($excerpt, 250, '…'), 'content_md' => $body, 'content_html' => $html,
            'meta_title' => 'Werkstudent Almanya: Notlar Değil Alan-İçi Deneyim Konuşur',
            'meta_description' => Str::limit($excerpt, 158, '…'),
            'reading_minutes' => max(1, (int) round(str_word_count(strip_tags($html)) / 200)),
            'is_published' => true, 'published_at' => now()];
        $existing = Post::where('slug', $slug)->first();
        if ($existing) { $existing->update($payload); } else { Post::create($payload + ['slug' => $slug, 'translation_group_id' => (string) Str::uuid()]); }
    }
    public function down(): void { Post::where('slug', 'werkstudent-in-germany-the-real-key-to-the-job-market')->delete(); }
};
