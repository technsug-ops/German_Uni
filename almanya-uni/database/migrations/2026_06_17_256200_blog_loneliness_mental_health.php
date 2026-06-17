<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * TR blog (geliş sonrası serisi): Uluslararası öğrenci olarak yalnızlık ve ruh sağlığı.
 * Hassas konu — sorumlu çerçeve + gerçek kriz/destek kaynakları. Çapraz linkli. FK-safe.
 */
return new class extends Migration
{
    public function up(): void
    {
        $slug = 'loneliness-and-mental-health-as-an-international-student-in-germany';

        $body = <<<'MD'
Başvuru, vize ve [Sperrkonto](/tr/blog/sperrkonto-for-a-german-visa-what-is-it-how-much-and) herkesin konuştuğu konular. Ama Almanya'ya gelen uluslararası öğrencilerin **en az bunlar kadar zorlandığı**, neredeyse hiç konuşulmayan bir gerçek var: **yalnızlık ve ruh sağlığı.** Bu yazı, bunu dürüstçe ele alıyor ve **nereden destek alabileceğini** gösteriyor.

> 💙 Bu yazı bilgilendirme amaçlıdır, tıbbi/profesyonel danışmanlık yerine geçmez. Zorlanıyorsan **yardım istemek güçlülüktür** — yalnız değilsin.

## Neden bu kadar zor?
Yeni bir ülke, yeni bir dil, yeni bir kültür — üstüne **akademik yük + iş + ev işleri + kötü ev arkadaşları** eklenince izolasyon ciddi bir soruna dönüşebilir. Birçok öğrenci, "süreç"e o kadar odaklanır ki **geldikten sonraki duygusal yükü** hesaba katmaz. Sonuç: kültür şoku, sıla hasreti, tükenmişlik ve bazen depresif belirtiler.

## Yaygın ama konuşulmayan zorluklar
- **Arkadaşlık kurmak baştan zor.** Almanya'da sosyal ilişkiler yavaş ısınır; dil engeli bunu büyütür.
- **Sıla hasreti & aidiyet eksikliği** — özellikle ilk 6-12 ay.
- **Mali stres** — para sıkıntısı yalnızlığı derinleştirir (WG/şehir değiştirebilmek için tampon önemli).
- **Mükemmeliyetçilik & karşılaştırma** — herkes "harika gidiyor" gibi görünür (özellikle sosyal medyada), sen geride hissedersin.

## Ne yapabilirsin?
- **Dili sosyal bir kapı olarak gör.** Almanca ilerledikçe arkadaşlık ve aidiyet de kolaylaşır. (Bkz. [iş için Almanca gerçeği](/tr/blog/german-language-reality-for-jobs-in-germany-the-honest-truth).)
- **Topluluğa katıl:** üniversite öğrenci kulüpleri (Hochschulgruppen), Buddy/Tandem programları, spor kulüpleri (Hochschulsport), Stammtisch'ler, Türk öğrenci dernekleri ve forumlar.
- **Rutin kur:** uyku, hareket, düzenli yemek ve dışarı çıkmak — ruh halini doğrudan etkiler.
- **Beklentini gerçekçi tut:** ilk yıl zordur; bu *senin başarısızlığın değil*, herkesin yaşadığı bir geçiş.

## Profesyonel destek — ücretsiz/erişilebilir kaynaklar
- **Üniversitenin Psikolojik Danışma Merkezi** (Psychologische Beratungsstelle) — genelde **ücretsiz** ve çoğu üniversitede İngilizce de mevcut.
- **Studierendenwerk** psikolojik danışmanlık hizmetleri.
- **Nightline** — öğrenciden öğrenciye anonim dinleme hattı (birçok şehirde, sık sık İngilizce).
- **Krankenkasse (TK/AOK vb.)** psikoterapiyi (Psychotherapie) karşılar — aile hekimi (Hausarzt) veya doğrudan terapist üzerinden yönlendirme alabilirsin.
- **Telefonseelsorge** — ücretsiz, 7/24, anonim: **0800 111 0 111** / **0800 111 0 222**.
- **Acil/kriz durumunda: 112** (tıbbi acil).

## Sonuç
Yalnızlık ve ruh sağlığı, Almanya öğrenci yolculuğunun **gerçek ve normal** bir parçası — zayıflık değil. Baştan plan yap: dile yatırım yap, topluluk kur, rutinini koru ve gerektiğinde **profesyonel desteğe başvur**. Yardım istemek, bu yolculuğu sürdürülebilir kılmanın en güçlü adımıdır. Devamı: [geliş sonrası gerçek hayat rehberi](/tr/blog/germany-life-after-arrival-advice-to-past-self).

---

*Bu yazı bilgilendirme amaçlıdır. Kriz anında yukarıdaki hatları veya 112'yi ara. Yalnız değilsin.*
MD;

        $excerpt = 'Uluslararası öğrenci olarak Almanya\'da yalnızlık ve ruh sağlığı: neden zor, yaygın ama konuşulmayan zorluklar ve ücretsiz destek kaynakları (üni psikolojik danışma, Nightline, Krankenkasse terapi, Telefonseelsorge). Yardım istemek güçlülüktür.';

        $html = Str::markdown($body, ['html_input' => 'allow', 'allow_unsafe_links' => false]);
        $userId = DB::table('users')->where('id', 6)->exists() ? 6 : DB::table('users')->orderBy('id')->value('id');
        $categoryId = DB::table('categories')->where('id', 9)->exists() ? 9 : DB::table('categories')->where('slug', 'yasam')->value('id');

        $payload = [
            'locale' => 'tr', 'user_id' => $userId, 'category_id' => $categoryId,
            'title' => 'Uluslararası Öğrenci Olarak Yalnızlık ve Ruh Sağlığı: Konuşulmayan Gerçek',
            'excerpt' => Str::limit($excerpt, 250, '…'),
            'content_md' => $body, 'content_html' => $html,
            'meta_title' => 'Almanya\'da Öğrenci Yalnızlığı ve Ruh Sağlığı: Destek Kaynakları',
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
        Post::where('slug', 'loneliness-and-mental-health-as-an-international-student-in-germany')->delete();
    }
};
