<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/** TR blog (geliş-sonrası serisi): Bachelor mı Master mı — zorluk, dil, iş + 3-hak sınav kuralı. FK-safe. */
return new class extends Migration
{
    public function up(): void
    {
        $slug = 'bachelor-vs-master-in-germany-difficulty-language-work-for-internationals';
        $body = <<<'MD'
"Almanya'da okumak (AB dışı bir öğrenci olarak) ne kadar zor?" ve "Bachelor mı Master mı daha kolay?" — en çok sorulan iki soru. Bloke hesap, yan iş ve dil baskısı işin içine girince cevap önemli. Bu yazı, uluslararası öğrenci deneyimlerine dayanarak ikisini karşılaştırıyor ve kimsenin yeterince anlatmadığı **kritik kuralları** veriyor.

## Kısa cevap: Bachelor (Almanca) vs Master (İngilizce)
- **Bachelor** çoğunlukla **Almanca**dır → akıcı Almanca **beklenir**. İlk dönemler "hunger games" gibi filtre derslerle doludur; ayrıca yerel öğrencilerle entegrasyon zordur (çoğu uluslararası öğrenci "paralel" yaşar). Yan işle birlikte yürütmek **çok yorucu**.
- **Master** çoğu zaman **İngilizce** seçenekler sunar → uluslararası bir ortam, kimse senden Almanca beklemez, **entegrasyon daha kolay** ve genelde **daha rahat** geçer.

OP gibi planın "lisansı ülkende bitir, master için Almanya'ya gel + bu arada Almanca öğren" ise — bu **en dengeli** yollardan biri.

> ⚠️ Ama dikkat: Master İngilizce olsa bile, **kalıcı olmak istiyorsan Almanca derslerin %90'ından önemlidir.** İş için B2, iş seviyesi için C1 neredeyse şart ([iş için Almanca gerçeği](/tr/blog/german-language-reality-for-jobs-in-germany-the-honest-truth)). İngilizce balonuna sıkışma.

## ‼️ Kritik kural 1: 3-hak (Drittversuch)
Almanya'da bir sınava genelde **3 hakkın** vardır (ilk + 2 tekrar). Aynı dersi **üç kez de geçemezsen**, o bölümden **exmatrikulation** (kaydın silinir) ve çoğu durumda **Almanya genelinde o bölümü bir daha okuyamazsın** — bu da **oturum iznini** doğrudan riske atar. Yani "bakalım sınav nasılmış" diye girilmez; **her deneme değerlidir.** (Kurallar üniye/eyalete göre değişir, bazı yerlerde Härtefall/istisna olabilir — kendi sınav yönetmeliğini oku.)

## ‼️ Kritik kural 2: Freelancing öğrenci vizesinde (genelde) YASAK
Öğrenci oturumu, **çalışan** olarak yılda 120 tam / 240 yarım gün (veya dönem içi ~20 saat/hafta) izin verir; ama **serbest meslek/freelancing (selbstständige Tätigkeit)** genelde **kapsam dışıdır** ve ayrı izin gerektirir, nadiren verilir. "Online freelance işim var" diye güvenme — vizenin izin verdiğini teyit et ([öğrenci çalışma izni](/tr/blog/student-work-permit-in-germany-2026-20-hour-rule-and-types)).

## Para ve yan iş gerçeği
- **Bloke hesap minimumu yeterli değil.** ~11.904 €/yıl (≈992 €/ay) çıplak asgaridir; pahalı şehirlerde yetmez. **3-5 bin € (veya daha fazla) tampon** ile gel.
- Tipik aylık gider tablosu (örnek): sigorta ~146 €, kira ~320 €, telefon ~30 €, market ~200 €+ → **~700-750 €/ay**.
- **Minijob** ~556 €/ay (2025). Haftada 9-10 saat asgari ücretle çalışmak, giderin 710 €'ysa **her ay açık** verdirir. Bahşişli (vergisiz) bir **garsonluk** işi hayatı kolaylaştırır — ama fiziksel ve yorucudur.
- **İlk 4 dönem**de haftada 10 saatten fazla çalışıp aynı zamanda zamanında mezun olmak çoğu öğrenci için **gerçekçi değil**. (Detay: [bütçe gerçeği](/tr/blog/real-cost-of-being-a-student-in-germany-budget-truth).)

## Dürüst tavsiye
Tüm masraflarını karşılayacak paran yoksa ve **okurken iş bulup geçinmek zorundaysan**, Almanya riskli olabilir: yeterince çalışamaz, sınavlarda zorlanır ve sosyal hayatından olabilirsin. En sağlam kurulum: **tamponlu bütçe + İngilizce Master + paralelde Almanca (B2→C1)**.

## Sonuç
"Ne kadar zor?" sorusunun cevabı **hazırlığına** bağlı. Bachelor-Almanca yolu en zoru; Master-İngilizce + iyi Almanca + para tamponu en yönetilebiliri. 3-hak kuralını ve freelancing yasağını **baştan bil**. İlgili: [Alman üniversiteleri zor mu](/tr/blog/are-german-universities-hard-for-international-students-the-weeding-out-truth) · [Werkstudent gerçeği](/tr/blog/werkstudent-in-germany-the-real-key-to-the-job-market) · [geliş-sonrası rehber](/tr/blog/germany-life-after-arrival-advice-to-past-self).

---
*Uluslararası öğrenci/mezun deneyimlerinden derlenmiştir. Kurallar üniversite/eyalete göre değişir — resmi yönetmeliği teyit et.*
MD;
        $excerpt = 'Almanya\'da AB dışı öğrenci olarak okumak ne kadar zor, Bachelor mı Master mı daha kolay? Bachelor-Almanca "hunger games" vs Master-İngilizce daha rahat; + 3-hak sınav kuralı (Almanya geneli bölüm yasağı), freelancing yasağı ve bloke hesap/yan iş gerçeği.';
        $html = Str::markdown($body, ['html_input' => 'allow', 'allow_unsafe_links' => false]);
        $userId = DB::table('users')->where('id', 6)->exists() ? 6 : DB::table('users')->orderBy('id')->value('id');
        $categoryId = DB::table('categories')->where('id', 1)->exists() ? 1 : DB::table('categories')->where('slug', 'almanyada-egitim')->value('id');
        $payload = ['locale' => 'tr', 'user_id' => $userId, 'category_id' => $categoryId,
            'title' => 'Almanya\'da Bachelor mı Master mı? Zorluk, Dil, İş ve 3-Hak Sınav Kuralı',
            'excerpt' => Str::limit($excerpt, 250, '…'), 'content_md' => $body, 'content_html' => $html,
            'meta_title' => 'Almanya Bachelor vs Master: Zorluk, Dil, İş + 3-Hak Kuralı',
            'meta_description' => Str::limit($excerpt, 158, '…'),
            'reading_minutes' => max(1, (int) round(str_word_count(strip_tags($html)) / 200)),
            'is_published' => true, 'published_at' => now()];
        $existing = Post::where('slug', $slug)->first();
        if ($existing) { $existing->update($payload); } else { Post::create($payload + ['slug' => $slug, 'translation_group_id' => (string) Str::uuid()]); }
    }
    public function down(): void { Post::where('slug', 'bachelor-vs-master-in-germany-difficulty-language-work-for-internationals')->delete(); }
};
