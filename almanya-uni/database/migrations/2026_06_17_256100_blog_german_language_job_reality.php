<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * TR blog (geliş sonrası serisi): "İngilizce yeterli" miti — iş için Almanca gerçeği.
 * Uluslararası öğrenci deneyimleri + topluluk. Çapraz linkli. FK-safe.
 */
return new class extends Migration
{
    public function up(): void
    {
        $slug = 'german-language-reality-for-jobs-in-germany-the-honest-truth';

        $body = <<<'MD'
"Almanya'da İngilizce yeterli, Almanca şart değil." Bu cümle, gelmeden önce en çok duyduğun ve geldikten sonra en çok pişman olduğun şey olabilir. İş bulma söz konusu olunca gerçek çok daha net: **Almanca, neredeyse pazarlık konusu değil.** Bu yazı, uluslararası öğrenci ve çalışanların deneyimine dayanarak bunu dürüstçe anlatıyor.

## Gerçek: çoğu tam zamanlı iş için B2/C1 şart
Tam zamanlı pozisyonların büyük kısmı için en az **B2, tercihen C1** Almanca beklenir. Sadece İngilizceyle alan "unicorn" firmalar vardır ama bunlar **kural değil, istisnadır.** Üstelik iş tamamen İngilizce yürüse bile, Almanlar takımda **kültürel uyum (cultural fit)** arar — Almanca konuşamaman seni mülakat öncesi eler.

## Müşteri/saha teması varsa C1
Pozisyon üretim sahası, müşteri, hasta veya halkla temas içeriyorsa **en az C1** gerekir. Hatta marketteki kasiyerlik bile Almanca ister. "İngilizce konuşulan iş" beklentisi, çoğu sektörde gerçekçi değil.

## Notlar değil, deneyim ve dil
Firmalar **notlarına bakmaz**; pozisyona uygun **araç/beceri** deneyimine bakar — projeler, tez ve özellikle **alan-içi Werkstudent/Praktikum**. Bir işe alma sürecine katılmış kişilerin ortak görüşü: *"2.3 + 2 yıl Werkstudent, 1.0 + hiç deneyimden iyidir."* Yurt dışı iş deneyimi ise, özel ve talep gören bir uzmanlığın yoksa, genelde fazla değer görmez.

## Mühendisler pahalıdır, hızlı verim beklenir
Özellikle mühendislikte firmalar seni **hızla üretken** görmek ister. Bu yüzden **dil** ve **yerel deneyim eksikliği**, aşılması gereken en büyük iki engeldir. Diploma (prestijli TU bile) tek başına iş garantisi değildir — bu piyasada **Almanlar bile** zorlanıyor.

## Peki ne yapmalı?
- **Almancaya çok erken başla.** Hedef B2, ardından C1. Geldikten sonra sıfırdan dil + Werkstudent + master'ı 4-6 dönemde birden çıkarmak neredeyse imkânsız — mümkünse **dili yanında getir.**
- **Alan-içi Werkstudent/Praktikum** bul; bir dönem uzasa bile yerel deneyim kazandırır. (Bkz. [öğrenci çalışma izni](/tr/blog/student-work-permit-in-germany-2026-20-hour-rule-and-types) · [İngilizce master sonrası kariyer](/tr/blog/after-your-english-masters-german-internships-jobs-and-career-opportunities).)
- **Deneyim biriktir:** projeler, tez, açık kaynak, staj — pozisyonun istediği araçlarla.
- İngilizce master yapacaksan bile, [Almanca planını](/tr/blog/english-masters-in-germany-without-german-your-complete-guide) baştan kur.

## Sonuç
"İngilizce yeterli" bir başlangıç olabilir ama **kariyer için yeterli değil.** Almanca'yı erkenden, ciddi bir hedefle (B2→C1) büyüt; alan-içi deneyim biriktir; notlara değil, beceriye yatırım yap. Bu üçlüyü kuran uluslararası öğrenciler Almanya'da gerçekten güçlü bir kariyer kuruyor. Daha fazlası: [geliş sonrası gerçek hayat rehberi](/tr/blog/germany-life-after-arrival-advice-to-past-self).

---

*Bu yazı, Almanya'da okumuş/çalışan uluslararası öğrencilerin deneyimlerinden ve topluluğumuzdan derlenmiştir.*
MD;

        $excerpt = '"Almanya\'da İngilizce yeterli" miti gerçek değil: tam zamanlı işlerin çoğu B2/C1 Almanca ister, İngilizce pozisyonda bile kültürel uyum eler, notlar değil alan-içi deneyim (Werkstudent) konuşur. İş için Almanca gerçeği ve ne yapmalı.';

        $html = Str::markdown($body, ['html_input' => 'allow', 'allow_unsafe_links' => false]);
        $userId = DB::table('users')->where('id', 7)->exists() ? 7 : DB::table('users')->orderBy('id')->value('id');
        $categoryId = DB::table('categories')->where('id', 9)->exists() ? 9 : DB::table('categories')->where('slug', 'yasam')->value('id');

        $payload = [
            'locale' => 'tr', 'user_id' => $userId, 'category_id' => $categoryId,
            'title' => '"İngilizce Yeterli, Almanca Şart Değil" Miti: İş İçin Gerçek',
            'excerpt' => Str::limit($excerpt, 250, '…'),
            'content_md' => $body, 'content_html' => $html,
            'meta_title' => 'Almanya\'da İş İçin Almanca Şart mı? "İngilizce Yeterli" Miti',
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
        Post::where('slug', 'german-language-reality-for-jobs-in-germany-the-honest-truth')->delete();
    }
};
