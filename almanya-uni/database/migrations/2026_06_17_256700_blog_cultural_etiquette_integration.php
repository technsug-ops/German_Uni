<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * TR blog (geliş-sonrası serisi): Almanya'da kültürel uyum & iletişim nezaketi.
 * Yapıcı, evrensel, iki-yönlü saygı çerçevesi (klişe/suçlama YOK). FK-safe.
 */
return new class extends Migration
{
    public function up(): void
    {
        $slug = 'cultural-etiquette-in-germany-respect-and-integration-for-students';
        $body = <<<'MD'
Almanya'da akademik ve günlük hayatın yarısı **iletişim kültürü.** Doğru bilgiyi getirmek kadar, **nasıl iletişim kurduğun** da seni hocalar, idari personel ve diğer öğrencilerle ilişkinde ya çok kolaylaştırır ya da zorlaştırır. Bu rehber, **yumuşak bir uyum** için bilmen gereken pratik kültürel kodları toplar — kimseyi yargılamak için değil, senin işini kolaylaştırmak için.

> 💡 Kültürel farklar **iki yönlüdür**: Almanlar da kendi tarzlarında bazen fazla direkt/soğuk gelebilir. Amaç "kim haklı" değil, karşılıklı saygıyla daha verimli bir ortam kurmak.

## 1. Soru sormadan önce minimum araştırma yap
Almanya'da, sormadan önce **temel bir araştırma** yapmak (bölüm belgelerini okumak, üniversite sitesini/Google'ı kullanmak) bir **saygı işareti** sayılır. Belgelerde veya kısa bir aramayla cevaplanabilecek onlarca küçük soruyla insanları boğmak, "çaba göstermiyor" izlenimi bırakır. **Önce araştır, sonra net ve odaklı sor.**

## 2. Emir verme — kibarca rica et, "hayır"ı kabul et
"Şunu yap" yerine "Rica etsem yardımcı olabilir misiniz?" Bu fark, bazı kültürlerde önemsiz olsa da Almanya'da (ve birçok yerde) büyük önem taşır. Ayrıca **resmi makamlarla** (memur, profesör, idare) **tartışmaya girmek** genelde işe yaramaz — çoğu sürecin net bir kuralı vardır; kibar, yapıcı ve sabırlı yaklaşım her zaman daha sonuç verir.

## 3. Kimseyi "hizmetçin" gibi görme
Diğer öğrenciler, asistanlar veya çalışanlar **senin kişisel asistanın değil.** "Şunu benim için çevir / ödevini bana uyarla / şu 100 sayfalık PDF'i çevirip yolla" gibi talepler insanları rahatsız eder ve sana kapı kapatır. **Başkalarının zamanı değerli bir kaynaktır.** Yardım aldığında teşekkür et, mümkünse karşılığını ver. (Çeviri için zaten harika ücretsiz AI araçları var.)

## 4. Grup çalışmasında adil ol
Grup ödevlerinde **herkes katkı vermeli.** Tüm işi bir kişiye yıkıp adını listeye yazdırmak, hazır çözüm istemek veya bireysel ödevde başkasının çözümünü kopyalamak; hem **intihal riski** doğurur hem de dürüst, gerçekten öğrenmek isteyen arkadaşlarını cezalandırır ve motivasyonlarını kırar. Adil katkı, hem etik hem de itibarın için en doğrusu.

## 5. Kültürel farkları önceden öğren — özellikle arkadaşlık
Almanya'da **arkadaşlık yavaş gelişir** ve ciddiye alınır; gerçek bir dostluk iki taraftan da zaman ve emek ister. Tanıştığın birine ilk günden "arkadaşım" demek, karşı tarafta beklediğinden farklı bir izlenim bırakabilir. **Sabırlı ol**, yüzeysel/çıkar amaçlı yakınlıktan kaçın — gerçek ilişkiler zamanla kurulur. (Bu, [yalnızlık ve uyum](/tr/blog/loneliness-and-mental-health-as-an-international-student-in-germany) sürecinin de doğal bir parçası.)

## Özet
- Sormadan önce **minimum araştırma** yap, net sor.
- **Emir değil rica**; "hayır"a saygı.
- "Sahip-hizmetçi" zihniyetini bırak; zamana saygı + teşekkür.
- Grup işinde **adil katkı**, kopya/dayatma yok.
- **Kültürel farklar gerçek** — özellikle arkadaşlıkta sabır.

Bu basit kodlar, hocalarla ve arkadaşlarla ilişkini kökten iyileştirir; uyum sürecini çok daha yumuşak yapar. Dil de bu kapının anahtarıdır ([iş & günlük hayatta Almanca](/tr/blog/german-language-reality-for-jobs-in-germany-the-honest-truth)). Tüm seri: [geliş-sonrası gerçek hayat rehberi](/tr/blog/germany-life-after-arrival-advice-to-past-self).

---
*Bu yazı, Almanya'da yaşayan öğrenci ve mezunların paylaştığı deneyimlerden, yapıcı ve karşılıklı-saygı çerçevesinde derlenmiştir.*
MD;
        $excerpt = 'Almanya\'da üniversite ve günlük hayatta yumuşak bir uyum için kültürel kodlar: sormadan önce araştır, emir değil rica et, başkalarının zamanına saygı göster, grup işinde adil ol ve arkadaşlığın zaman aldığını bil. Yapıcı, karşılıklı-saygı rehberi.';
        $html = Str::markdown($body, ['html_input' => 'allow', 'allow_unsafe_links' => false]);
        $userId = DB::table('users')->where('id', 6)->exists() ? 6 : DB::table('users')->orderBy('id')->value('id');
        $categoryId = DB::table('categories')->where('id', 9)->exists() ? 9 : DB::table('categories')->where('slug', 'yasam')->value('id');
        $payload = ['locale' => 'tr', 'user_id' => $userId, 'category_id' => $categoryId,
            'title' => 'Almanya\'da Kültürel Uyum: Üniversite ve Günlük Hayatta Saygı & Nezaket Kodları',
            'excerpt' => Str::limit($excerpt, 250, '…'), 'content_md' => $body, 'content_html' => $html,
            'meta_title' => 'Almanya\'da Kültürel Uyum ve İletişim Nezaketi: Öğrenci Rehberi',
            'meta_description' => Str::limit($excerpt, 158, '…'),
            'reading_minutes' => max(1, (int) round(str_word_count(strip_tags($html)) / 200)),
            'is_published' => true, 'published_at' => now()];
        $existing = Post::where('slug', $slug)->first();
        if ($existing) { $existing->update($payload); } else { Post::create($payload + ['slug' => $slug, 'translation_group_id' => (string) Str::uuid()]); }
    }
    public function down(): void { Post::where('slug', 'cultural-etiquette-in-germany-respect-and-integration-for-students')->delete(); }
};
