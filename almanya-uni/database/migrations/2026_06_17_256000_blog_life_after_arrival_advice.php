<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * TR blog (geliş sonrası serisi - amiral gemisi): Almanya'ya gelmeden önce keşke
 * birinin söylediği şeyler. Uluslararası öğrenci deneyimleri + topluluk havuzu.
 * Çapraz linkli. EN/DE çevirisi translate-posts ile üretilecek. FK-safe.
 */
return new class extends Migration
{
    public function up(): void
    {
        $slug = 'germany-life-after-arrival-advice-to-past-self';

        $body = <<<'MD'
İnternetteki içeriğin neredeyse tamamı **"Almanya'ya nasıl gidilir"** anlatır: başvuru, vize, [Sperrkonto](/tr/blog/sperrkonto-for-a-german-visa-what-is-it-how-much-and), üniversite sıralamaları. Ama **gittikten sonra hayatın gerçekte nasıl** olduğunu kimse dürüstçe söylemez. Bu yazı, Almanya'da okumuş/çalışan uluslararası öğrencilerin deneyimlerinden ve topluluğumuzdan damıttığımız, **keşke birinin önceden söylediği** şeyleri topluyor — iyi ve zor yanlarıyla.

## 1. Almanca olmadan iş "yok" sayılır
En sık yapılan hata: "İngilizce yeterli." Gerçek: tam zamanlı pozisyonların çoğu için **B2, tercihen C1 Almanca** beklenir. İşin dili İngilizce olsa bile, Almanlar takımda **kültürel uyum** arar ve bu seni eleyebilir. Detay: ["İngilizce yeterli" miti](/tr/blog/german-language-reality-for-jobs-in-germany-the-honest-truth).

## 2. Notlar değil, deneyim konuşur
Firmalar **2.3 + 2 yıl Werkstudent** olan birini, **1.0 + hiç deneyimi olmayandan** önce işe alır. Alan-içi bir **working student (Werkstudent)** pozisyonu, Alman piyasasındaki deneyim eksiğini kapatan **tek fark yaratan** şeydir — bulması zor, bir dönem uzatabilir ama değer.

## 3. "Tam zamanlı okur + part-time çalışırım" gerçekçi değil
İkisini birden sürdürmek **çok yorucu**. Para sıkıntın varsa, gelir gelmez part-time'a bağımlı olmadan geçinebileceğin bir **tamponla gel**. Werkstudent/part-time iş bulmak da kolay değil.

## 4. Arkadaşlık zor, yalnızlık gerçek
Yeni bir ülke, dil, kültür + okul + iş yükü… İzolasyon ciddi bir sorun olabilir. Bunu hafife alma; baştan plan yap. Detay ve kaynaklar: [Yalnızlık ve ruh sağlığı](/tr/blog/loneliness-and-mental-health-as-an-international-student-in-germany).

## 5. Diploma iş garantisi değil
Prestijli bir TU'dan mezun olmak bile seni otomatik işe sokmaz; bu piyasada **Almanlar bile** zorlanıyor. Diploma + Almanca + deneyim üçlüsü gerekir.

## 6. Şehir seçimi bazen üniden önemli
Yaşam maliyeti, iş imkânları, topluluk ve sosyal hayat çoğu zaman **şehre** bağlı. Üni sıralamasına takılıp şehri görmezden gelme — [şehir ve eyalet rehberlerimize](/tr/states) bak.

## 7. Beklenmedik masraflara hazırlık
İlk aylar pahalıdır: depozito (Kaution, genelde 2-3 kira), mobilya, sigorta, Anmeldung'a kadar geçici konaklama. Bütçeni bu "ilk kurulum" maliyetiyle planla.

## 8. WG/oda bulmak baştan zor
Özellikle büyük şehirlerde oda rekabeti yüksek. Gelmeden önce geçici çözüm ayarla, sonra WG'ye geç. Kötü ev arkadaşı çıkarsa **taşınabilmek için** finansal esnekliğin olsun.

## 9. Bürokrasiyi erken öğren
Anmeldung, sağlık sigortası, oturum izni, banka… İlk haftalarda yapman gerekenleri bil: [Yeni gelen öğrenci bürokrasi rehberi](/tr/blog/germany-new-arrival-bureaucracy-guide-first-steps-institutions).

## 10. Tek tavsiye: Almancayı erken başlat
Geri dönüp kendine tek şey söyleyebilseydin, çoğu öğrenci aynı şeyi der: **"Almancaya çok daha erken başla."** Dil; iş, arkadaşlık, günlük hayat ve özgüvenin anahtarı.

## Peki iyi yanı?
Kaliteli ve çoğunlukla ücretsiz eğitim, güçlü iş piyasası (doğru hazırlanırsan), seyahat, bağımsızlık ve seni gerçekten büyüten bir deneyim. Zorlukları **bilerek** gelirsen, bu yolculuk fazlasıyla değer.

## Sonuç
Almanya'ya "süreci" tamamlayıp gelmek yarısı; asıl yolculuk **geldikten sonra** başlıyor. Almancanı erken büyüt, deneyim (Werkstudent) biriktir, tamponlu bütçeyle gel, yalnızlığa karşı baştan plan yap ve şehri de en az üni kadar ciddiye al. İlgili rehberler: [Almanca-iş gerçeği](/tr/blog/german-language-reality-for-jobs-in-germany-the-honest-truth) · [yalnızlık & ruh sağlığı](/tr/blog/loneliness-and-mental-health-as-an-international-student-in-germany) · [öğrenci çalışma izni](/tr/blog/student-work-permit-in-germany-2026-20-hour-rule-and-types).

---

*Bu yazı, uluslararası öğrenci ve mezunların paylaştığı gerçek deneyimlerden ve topluluğumuzdan derlenmiştir. Herkesin yolculuğu farklıdır.*
MD;

        $excerpt = 'Almanya\'ya gelmeden önce keşke birinin söylediği 10 şey: Almanca-iş gerçeği, Werkstudent, para tamponu, yalnızlık, diploma ≠ iş, şehir seçimi ve daha fazlası — uluslararası öğrenci deneyimlerinden dürüst bir geliş-sonrası rehberi.';

        $html = Str::markdown($body, ['html_input' => 'allow', 'allow_unsafe_links' => false]);
        $userId = DB::table('users')->where('id', 6)->exists() ? 6 : DB::table('users')->orderBy('id')->value('id');
        $categoryId = DB::table('categories')->where('id', 9)->exists() ? 9 : DB::table('categories')->where('slug', 'yasam')->value('id');

        $payload = [
            'locale' => 'tr', 'user_id' => $userId, 'category_id' => $categoryId,
            'title' => 'Almanya\'ya Gelmeden Önce Keşke Birinin Söylediği 10 Şey (Geliş Sonrası Gerçek Hayat)',
            'excerpt' => Str::limit($excerpt, 250, '…'),
            'content_md' => $body, 'content_html' => $html,
            'meta_title' => 'Almanya\'da Geliş Sonrası Gerçek Hayat: Keşke Bilseydim Dedikleri',
            'meta_description' => Str::limit($excerpt, 158, '…'),
            'reading_minutes' => max(1, (int) round(str_word_count(strip_tags($html)) / 200)),
            'is_published' => true, 'published_at' => now(),
        ];

        $existing = Post::where('slug', $slug)->first();
        if ($existing) { $existing->update($payload); }
        else { Post::create($payload + ['slug' => $slug, 'translation_group_id' => (string) Str::uuid()]); }
    }

    public function down(): void
    {
        Post::where('slug', 'germany-life-after-arrival-advice-to-past-self')->delete();
    }
};
