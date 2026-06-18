<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/** TR blog (üniversite): QS/THE sıralamaları Almanya için ne ifade eder, nasıl doğru kullanılır. Dengeli. FK-safe. */
return new class extends Migration
{
    public function up(): void
    {
        $slug = 'do-university-rankings-matter-in-germany-qs-the-explained';
        $body = <<<'MD'
"QS'te 50., THE'de 30. — bu üniversite Almanya'da gerçekten daha mı iyi?" Uluslararası sıralamalar (QS, THE, ARWU/Shanghai) cazip ve düzenli görünür; ama Almanya bağlamında **ne ifade ettiklerini** ve **ne ifade etmediklerini** bilmek, doğru karar için şart. Kısa cevap: **faydalı bir ipucu, ama tek başına yanıltıcı.**

## Sıralamalar neyi ölçer (ve neyi ölçmez)?
- **Çoğu sıralama kurumu bir bütün olarak ölçer** — araştırma yayınları, atıflar, akademik itibar anketleri, uluslararası oran. Bunlar **araştırma ağırlıklı** metriklerdir.
- **Senin için kritik olanları genelde ölçmez:** ders kalitesi, staj imkânı, sınıf büyüklüğü, mezuniyet sonrası **istihdam** kolaylığı, hocanın iyi anlatıp anlatmadığı.
- Almanya'da **kurum bazlı sıralama yanıltıcıdır** çünkü kalite **bölüm bazında** değişir: bir üni dilbilimde zirve, ama senin okuyacağın matematik bölümünde sıradan olabilir.

## Almanya'ya özgü gerçek
- **Devlet üniversiteleri kabaca eşit** kabul edilir; iç pazarda sıralama kültürü zayıftır. İşveren "QS 40 vs 120" diye ayrıştırmaz.
- **Uluslararası** düzeyde ise birkaç ad öne çıkar (TUM, RWTH, LMU, Heidelberg, KIT...) — ama bu **alana özel** bir görünürlüktür, "her bölümde üstün" demek değil.
- **FH'ler** çoğu küresel sıralamada ya hiç yer almaz ya da düşük görünür — çünkü ölçüt **araştırma**. Bu, FH'nin "kötü" olduğu anlamına **gelmez**; FH'nin misyonu uygulama/istihdam ([prestij miti & Uni vs FH](/tr/blog/prestige-myth-german-universities-uni-vs-fh-practical-path)).

## Sıralamayı doğru kullanmanın yolu: kurumu değil, bölümü oku
1. **Subject (konu) sıralamalarına bak**, genel sıralamaya değil. QS/THE her ikisinde de **alan bazlı** listeler var — okuyacağın bölüm için olanı incele.
2. **Birden çok kaynağı karşılaştır** (QS + THE + ARWU). Tek sıralamaya güvenme; metodolojileri farklıdır.
3. **Senin metriklerini ekle:** staj/Werkstudent imkânı, şehir/kira, dil, kabul koşulu (NC mı, [açık-kabul mu](/tr/programs)). Bunlar günlük hayatını sıralamadan daha çok etkiler.
4. **Tanınırlığı kontrol et:** diploman Türkiye/AB'de nasıl tanınıyor (devlet + akredite mi). Bu, sıralamadan **daha önemli**.

## Pratik öneri
- Sıralamayı bir **filtre/ipucu** olarak kullan, **karar verici** olarak değil.
- Asıl kararı **bölüm + hedef + yaşam koşulları** üçgenine göre ver. Üniversiteleri ve programları [üniversiteler](/tr/universities) ve [program araması](/tr/programs)nda karşılaştırabilirsin.
- "Etiket" merakı için: [TU9 & Exzellenz gerçekten önemli mi](/tr/blog/tu9-excellence-universities-germany-do-elite-labels-matter).

## Sonuç
Uluslararası sıralamalar yararlı bir başlangıç ipucudur ama Almanya'da **kurum bazlı** okunduğunda yanıltır: kalite **bölüme** göre değişir, sıralamalar **araştırmayı** ölçer, senin hayatını etkileyen şeyleri (ders, staj, istihdam, şehir) ölçmez. Doğru yaklaşım: **konu sıralaması + kendi metriklerin + tanınırlık**. İlgili: [Devlet vs Özel vs FH](/tr/blog/public-vs-private-universities-germany-balanced-comparison) · [Alman üniversiteleri zor mu](/tr/blog/are-german-universities-hard-for-international-students-the-weeding-out-truth).

---
*Genel rehber niteliğindedir. Sıralama metodolojileri ve sonuçları yıldan yıla değişir — güncel listeleri resmi kaynaklardan teyit et.*
MD;
        $excerpt = 'QS/THE/ARWU sıralamaları Almanya için ne ifade eder? Faydalı ipucu ama tek başına yanıltıcı: sıralamalar araştırmayı ölçer, ders/staj/istihdam/şehir gibi seni etkileyen şeyleri değil; Almanya\'da kalite kurum değil bölüm bazında değişir. Doğru kullanım: konu sıralaması + kendi metriklerin + tanınırlık.';
        $html = Str::markdown($body, ['html_input' => 'allow', 'allow_unsafe_links' => false]);
        $userId = DB::table('users')->where('id', 4)->exists() ? 4 : DB::table('users')->orderBy('id')->value('id');
        $categoryId = DB::table('categories')->where('id', 12)->exists() ? 12 : DB::table('categories')->where('slug', 'universities')->value('id');
        $payload = ['locale' => 'tr', 'user_id' => $userId, 'category_id' => $categoryId,
            'title' => 'Almanya\'da Üniversite Sıralamaları (QS/THE) İşe Yarar mı? Doğru Kullanım',
            'excerpt' => Str::limit($excerpt, 250, '…'), 'content_md' => $body, 'content_html' => $html,
            'meta_title' => 'Almanya\'da Üniversite Sıralamaları (QS/THE) Önemli mi?',
            'meta_description' => Str::limit($excerpt, 158, '…'),
            'reading_minutes' => max(1, (int) round(str_word_count(strip_tags($html)) / 200)),
            'is_published' => true, 'published_at' => now()];
        $existing = Post::where('slug', $slug)->first();
        if ($existing) { $existing->update($payload); } else { Post::create($payload + ['slug' => $slug, 'translation_group_id' => (string) Str::uuid()]); }
    }
    public function down(): void { Post::where('slug', 'do-university-rankings-matter-in-germany-qs-the-explained')->delete(); }
};
