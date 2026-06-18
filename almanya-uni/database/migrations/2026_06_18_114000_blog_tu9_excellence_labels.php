<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/** TR blog (üniversite): TU9 + Exzellenzuniversität etiketleri ne demek, gerçekten önemli mi? Dengeli. FK-safe. */
return new class extends Migration
{
    public function up(): void
    {
        $slug = 'tu9-excellence-universities-germany-do-elite-labels-matter';
        $body = <<<'MD'
"Almanya'nın Ivy League'i TU9 mu? Exzellenzuniversität etiketi diplomama değer katar mı?" — bu iki kavram uluslararası öğrencileri en çok kafası karıştıran "elit" etiketler. Gerçeği sakince ayıralım: ikisi de **gerçek ama abartılan** kavramlar; "girersem hayatım değişir" türünden bir Ivy League statüsü **değiller**.

## TU9 nedir?
**TU9**, Almanya'nın en köklü 9 **teknik üniversitesinin** (Technische Universitäten) oluşturduğu bir **birliktir**: RWTH Aachen, TU Berlin, TU Braunschweig, TU Darmstadt, TU Dresden, Leibniz Hannover, KIT Karlsruhe, TU München, Uni Stuttgart.

- **Ne işe yarar:** Ortak çıkar temsili, mühendislik/doğa bilimlerinde güçlü gelenek ve sanayi bağları.
- **Ne DEĞİLDİR:** Bir "kalite sıralaması" ya da Ivy League. TU9 üyesi olmak diplomanı otomatik olarak "üstün" yapmaz; üye olmayan birçok güçlü üniversite/FH vardır.
- **Kime hitap eder:** Mühendislik/teknik bilimlerde köklü bir Universität arıyorsan iyi adreslerdir — ama "TU9 değil" diye bir okulu elemek **yanlış** olur.

## Exzellenzuniversität / Exzellenzinitiative nedir?
Bu, federal hükümetin belirli üniversitelere **ekstra araştırma fonu** verdiği bir **finansman programıdır** (Exzellenzstrategie).

- **Gerçek anlamı:** Üniversite, güçlü araştırma projeleriyle bu fona hak kazanmıştır. Yani **araştırma kapasitesi** sinyali.
- **Sık yapılan hata:** Bunu "Almanya'nın Ivy League'i" sanmak. Eğitim esas olarak eyaletlerin yetkisinde; federal fon, devletin okullar arası fark yaratmaması ilkesi etrafında **dolambaçlı** bir araştırma desteğidir.
- **Lisans öğrencisine etkisi:** Çoğunlukla **dolaylı.** Daha çok doktora/araştırma kariyeri hedefleyenler için anlamlı; "Exzellenz" etiketi nedeniyle işveren seni otomatik öne almaz.

## Peki işveren umursar mı?
Genel kural: Alman işveren, **devlet** diplomalarını TU9/Exzellenz diye ayrıştırmaz. Önemli olan dereceN + **pratik deneyim** ([Werkstudent](/tr/blog/werkstudent-in-germany-the-real-key-to-the-job-market), staj). İstisna: belirli sektörlerde (ör. üst düzey araştırma, bazı finans rolleri) belirli **bölümlerin** ünü fark yaratabilir — ama bu "okulun etiketi" değil, **o bölümün** gücüdür.

## Doğru bakış: etiket değil, bölüm + hedef
- Mühendislik/teknik ve **araştırma** istiyorsan: TU9 üniversiteleri köklü tercihlerdir.
- **Pratik/istihdam** odaklıysan: güçlü bir **FH** çoğu zaman daha mantıklı ([prestij miti & Uni vs FH](/tr/blog/prestige-myth-german-universities-uni-vs-fh-practical-path)).
- **Etiket avı yapma:** "TU9/Exzellenz değil" diye eleme; "bu **bölüm** benim hedefime uygun mu" diye sor.

## Sonuç
TU9 ve Exzellenzuniversität gerçek kavramlardır ama **Ivy League değil**: biri köklü teknik üniversiteler birliği, diğeri bir araştırma fonu. Lisans düzeyinde kariyerini belirleyen şey bu etiketler değil, **bölümün + hedefin + pratik deneyimin**. Karşılaştırma için: [Devlet vs Özel vs FH](/tr/blog/public-vs-private-universities-germany-balanced-comparison) · [Sıralamalar (QS/THE) ne ifade eder](/tr/blog/do-university-rankings-matter-in-germany-qs-the-explained) · üniversiteleri [karşılaştır](/tr/universities).

---
*Alman yükseköğretim yapısı temel alınarak hazırlanmıştır. Üye listesi ve fon durumu zamanla değişebilir — resmi kaynaktan teyit et.*
MD;
        $excerpt = 'TU9 ve Exzellenzuniversität nedir, Almanya\'nın Ivy League\'i mi? Sakin bir ayrım: TU9 = 9 köklü teknik üniversitenin birliği (kalite sıralaması değil), Exzellenz = araştırma fonu programı. İşveren devlet diplomalarını bu etiketlerle ayrıştırmaz; önemli olan bölüm + hedef + pratik deneyim. Lisans öğrencisine etkisi çoğunlukla dolaylı.';
        $html = Str::markdown($body, ['html_input' => 'allow', 'allow_unsafe_links' => false]);
        $userId = DB::table('users')->where('id', 4)->exists() ? 4 : DB::table('users')->orderBy('id')->value('id');
        $categoryId = DB::table('categories')->where('id', 12)->exists() ? 12 : DB::table('categories')->where('slug', 'universities')->value('id');
        $payload = ['locale' => 'tr', 'user_id' => $userId, 'category_id' => $categoryId,
            'title' => 'TU9 ve Exzellenzuniversität: Almanya\'nın "Elit" Üniversite Etiketleri Önemli mi?',
            'excerpt' => Str::limit($excerpt, 250, '…'), 'content_md' => $body, 'content_html' => $html,
            'meta_title' => 'TU9 ve Exzellenzuniversität Nedir? Almanya\'da Önemli mi?',
            'meta_description' => Str::limit($excerpt, 158, '…'),
            'reading_minutes' => max(1, (int) round(str_word_count(strip_tags($html)) / 200)),
            'is_published' => true, 'published_at' => now()];
        $existing = Post::where('slug', $slug)->first();
        if ($existing) { $existing->update($payload); } else { Post::create($payload + ['slug' => $slug, 'translation_group_id' => (string) Str::uuid()]); }
    }
    public function down(): void { Post::where('slug', 'tu9-excellence-universities-germany-do-elite-labels-matter')->delete(); }
};
