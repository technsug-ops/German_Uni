<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/** TR blog (öğrenci yaşamı): Almanya'da kira maliyetleri #2 — Kaltmiete/Warmmiete/Nebenkosten/Kaution. FK-safe. */
return new class extends Migration
{
    public function up(): void
    {
        $slug = 'germany-rental-costs-explained-kaltmiete-warmmiete-nebenkosten-kaution';
        $body = <<<'MD'
Almanya'da bir ev ilanına bakarken karşına çıkan **Kaltmiete, Warmmiete, Nebenkosten, Kaution** kelimeleri ilk başta kafa karıştırır — ama bütçeni doğru kurman için şart. Bu yazı (ev serisinin **2.'si**) kira maliyetlerini netleştirir. (Önce: [ev bulma rehberi](/tr/blog/finding-accommodation-in-germany-wg-and-housing-search-guide) · sonra: [sözleşme & dolandırıcılık](/tr/blog/german-rental-contract-moving-in-out-and-avoiding-housing-scams).)

## Kaltmiete vs Warmmiete vs Nebenkosten
- **Kaltmiete (soğuk kira):** Sadece dairenin kirası, hiçbir gider dahil değil. İlanlarda **standart budur**.
- **Nebenkosten (yan giderler):** Kaltmiete'nin üzerine eklenir; genelde **%15-30**. Su, ısıtma, ortak alan temizliği/bakımı, çöp vb. içerir.
- **Warmmiete (sıcak kira):** Kaltmiete + Nebenkosten = aylık toplam ödeyeceğin.

> ⚠️ İçinde **olmayanlar** çoğunlukla: **elektrik, internet/telefon, Rundfunkbeitrag ("TV vergisi")** — bunlar için ayrı sözleşme yaparsın. İlanı dikkatle oku.

## Altın kural: gelirin 1/3'ü
Almanya'da yaygın tavsiye: **net gelirinin 1/3'ünden fazlasını Warmmiete'ye verme.** Bunu uygun daireleri filtrelemek için pusula yap. Çok idareli yaşarsan yarısına kadar çıkabilirsin ama **beklenmedik gidere** marjın kalmaz. (Tam tablo: [öğrenci bütçe gerçeği](/tr/blog/real-cost-of-being-a-student-in-germany-budget-truth).)

## Bölgeye göre büyük fark
- Ülke ortalaması kabaca **~8 €/m²** (Kaltmiete).
- Kırsal bazı yerler **4 €/m²**'ye kadar iner.
- En pahalı şehirler (**Münih, Frankfurt, Stuttgart**) **17 €/m² ve üzeri**. Şehir seçimi bütçeyi doğrudan etkiler ([şehir mi üniversite mi](/tr/blog/city-vs-university-which-matters-more-in-germany)).

## Kaution (depozito) — bilmen gerekenler
- **Üst sınır yasal:** En fazla **3 Kaltmiete** kadar olabilir, daha fazlası **yasadışı**.
- **Taksit hakkın var:** Depozitoyu **3 aylık taksitle** ödeme **yasal hakkındır** — tek seferde istenirse hatırlat.
- **Ayrı tutulur:** Ev sahibi depozitoyu kendi parasından **ayrı** bir hesapta (Mietkautionskonto) tutmak zorundadır.
- **Geri ödeme:** Çıkışta, hasar/eksik kira düşülerek kalan iade edilir — çoğu birkaç ay içinde, kalanı ertesi yıl sonuna kadar (yan gider kapanışı için).
- **Alternatif:** Nakit yoksa **Mietkautionsversicherung** (depozito sigortası) sorulabilir — ama ev sahipleri kabul etmek zorunda değildir.
- 💡 **Asla peşin/elden** kaptırma: **imzalı sözleşme (ve tercihen anahtar)** olmadan hiçbir ödeme yapma.

## Kira nasıl artar? (sözleşme tipi)
- **Sabit (fixed):** Bir yıl sabit; sonra ev sahibi "yerel piyasa"ya göre **ölçülü** artırabilir (çoğu zahmet etmez).
- **Staffelmiete:** Sözleşmede **önceden tanımlı** kademeli artış.
- **Indexmiete:** **Enflasyona** endeksli artış.
Sözleşme hangisi olduğunu yazar — imzadan önce oku.

## Mobilyalı mı, boş mu?
- Almanya'da daireler çoğunlukla **boş (unfurnished)** kiralanır — ve bu çoğu zaman **mutfak yok, hatta ampul bile yok** demektir!
- **Mobilyalı** daireler vardır ama genelde **kısa dönem** ve daha pahalıdır.

## Sonuç
Bütçeni kurarken **Warmmiete'yi** (Kaltmiete + Nebenkosten) baz al, **gelirin 1/3'ü** kuralını uygula, **Kaution'un en fazla 3 Kaltmiete** ve **taksit hakkın** olduğunu unutma, "boş daire = mutfak yok" sürprizine hazır ol. Sırada sözleşme imzası, taşınma ve dolandırıcılıktan korunma var → [sözleşme & taşınma & scam](/tr/blog/german-rental-contract-moving-in-out-and-avoiding-housing-scams). İlgili: [Sperrkonto (bloke hesap)](/tr/blog/sperrkonto-2025-complete-guide-blocked-account-for-germany-visa).

---
*Genel rehberdir. Oranlar ve fiyatlar bölgeye/döneme göre değişir — ilanı ve sözleşmeyi dikkatle kontrol et.*
MD;
        $excerpt = 'Almanya\'da kira maliyetleri netleşiyor: Kaltmiete (soğuk kira) vs Nebenkosten (%15-30 yan gider) vs Warmmiete (toplam); elektrik/internet/Rundfunk genelde hariç. Altın kural: gelirin 1/3\'ü. Kaution en fazla 3 Kaltmiete + 3 taksit hakkı + ayrı hesap. Bölge farkı (8€/m² ort., Münih 17€+). Boş daire = mutfak yok sürprizi.';
        $html = Str::markdown($body, ['html_input' => 'allow', 'allow_unsafe_links' => false]);
        $userId = DB::table('users')->where('id', 4)->exists() ? 4 : DB::table('users')->orderBy('id')->value('id');
        $categoryId = DB::table('categories')->where('id', 16)->exists() ? 16 : DB::table('categories')->where('slug', 'student-life')->value('id');
        $payload = ['locale' => 'tr', 'user_id' => $userId, 'category_id' => $categoryId,
            'title' => 'Almanya\'da Kira Maliyetleri: Kaltmiete, Warmmiete, Nebenkosten ve Kaution',
            'excerpt' => Str::limit($excerpt, 250, '…'), 'content_md' => $body, 'content_html' => $html,
            'meta_title' => 'Almanya Kira Maliyetleri: Kaltmiete, Warmmiete, Nebenkosten, Kaution',
            'meta_description' => Str::limit($excerpt, 158, '…'),
            'reading_minutes' => max(1, (int) round(str_word_count(strip_tags($html)) / 200)),
            'is_published' => true, 'published_at' => now()];
        $existing = Post::where('slug', $slug)->first();
        if ($existing) { $existing->update($payload); } else { Post::create($payload + ['slug' => $slug, 'translation_group_id' => (string) Str::uuid()]); }
    }
    public function down(): void { Post::where('slug', 'germany-rental-costs-explained-kaltmiete-warmmiete-nebenkosten-kaution')->delete(); }
};
