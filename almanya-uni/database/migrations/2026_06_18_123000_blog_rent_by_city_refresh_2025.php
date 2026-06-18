<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/** TR blog GÜNCELLEME: kira coğrafyası yazısını 2025/2026 rakamlarına çek (ImmoScout Q3 2025 + MLP 2025 + Value Marktdaten 2026Q1). FK-safe. */
return new class extends Migration
{
    public function up(): void
    {
        $slug = 'germany-rent-by-city-cheapest-most-expensive-and-what-drives-rent';
        $post = Post::where('slug', $slug)->first();
        if (! $post) { return; } // orijinal migration yoksa atla

        $body = <<<'MD'
Almanya'da kira **şehre göre dramatik** değişir ve son yıllarda **hızla yükseldi**. Örnek: 2019'da en pahalı şehir München'de yeni kiralama ~17,51 €/m² iken, **2025'te 26 €/m²'yi aştı** — yani fark açılıyor. Öğrenci için bu doğrudan **bütçe** demek: nerede okuyacağın, ne kadar harcayacağını belirler. Bu yazı kiranın **nerede pahalı/ucuz** olduğunu ve **neden** öyle olduğunu güncel veriyle gösterir.

> **Kaynak & güncellik:** Kira verisini **Value Marktdaten** (eski empirica-systeme; 2026 Q1'e kadar) ve **empirica regio** toplar — bunlar **ilan/teklif kirası** (Angebotsmiete). Aşağıdaki güncel rakamlar **ImmoScout24 WohnBarometer (Q3 2025)** ve **MLP Studentenwohnreport 2025**'ten; resmî/yasal referans için yerel **Mietspiegel**'e bak. (Kira terimleri: [Kaltmiete/Warmmiete/Kaution](/tr/blog/germany-rental-costs-explained-kaltmiete-warmmiete-nebenkosten-kaution).)

## En pahalı: München ve metropoller (güncel, 2025)
- **München** açık ara lider: ilan kirası **26 €/m²'nin üzerinde** (ilk kez 26'yı aştı). Münih çevresi (Grünwald, Unterföhring, Oberschleißheim...) de en pahalılar arasında.
- Diğer metropoller (ilan kirası, 2025): **Frankfurt ~18,95** · **Düsseldorf ~18,34** (yıllık +%5,5) · Hamburg, Köln, Berlin yüksek seyirde. En **ucuz metropol** hâlâ **Leipzig ~13,90 €**.
- Ülke geneli ilan kirası (mevcut konut) ort. **~8,80 €/m²** (yıllık +%3), yeni yapı **~13 €/m²**.

## En ucuz: Doğu Almanya ve kırsal bölgeler
- En düşük segment hâlâ **Doğu Almanya** ile bazı kırsal NRW/Saksonya/Thüringen yerleşimleri (ör. Borgentreich, Elsterberg, Treffurt — m² başına ~4 €).
- **Öğrenci dostu büyük doğu şehirleri** öğrenci bütçesine çok daha uygundur: Leipzig, Dresden, Erfurt, Magdeburg, Rostock — gerçi Leipzig **en hızlı artanlardan** (aşağıya bak).

## Kiranı belirleyen 4 faktör
Veri analizi kira farkının arkasında **dört örüntü** buluyor — şehir seçerken bunları bil:

### 1) Metropoller
Herkesin yaşamak istediği yerde konut kıt → kira yüksek. Dört milyonluk şehir (München, Berlin, Hamburg, Köln) kırsala kıyasla çok daha pahalı.

### 2) Speckgürtel (çevre kuşak) — sürpriz tuzak
Metropolde tutunamayanlar çevreye taşınır; bu da çevre kasabaların kirasını yukarı çeker. Berlin örneğinde Teltow gibi yakın çevre, zaman zaman **Berlin'in kendisinden pahalı** olabiliyor. Kira ayrıca **otoyol/tren hatları boyunca** yükselir (pendler talebi). Çevreye taşınırken **yol masrafı + zaman** maliyetini de hesapla.

### 3) Kıyılar ve turistik bölgeler
Deniz/Alp manzarası primlidir. Ostsee kıyısı (Kühlungsborn, Graal-Müritz, Heringsdorf) iç kesime kıyasla neredeyse **iki kat** pahalı olabiliyor.

### 4) Sınırlar (özellikle batıda)
Lüksemburg sınırı: pahalı Lüksemburg yerine Almanya tarafında oturup işe gidenler talebi artırır → Perl gibi sınır kasabalarında kira yükselir. Doğu sınırı (Polonya/Çek) ise ucuz kalır — bölge kırsal ve seyrek nüfuslu.

## Öğrenciye özel: MLP Studentenwohnreport 2025
Öğrenci konutuna odaklı en güncel kaynak (38 üni şehri):
- Öğrenci kiraları kalite-ayarlı ort. **+%2,3**; **küçük daireler (<40 m²) +%4,3** (öğrencinin en çok aradığı tip — en hızlı artan).
- Son 3 yılda **en hızlı artan**: **Leipzig, Freiburg, Konstanz** (>%6/yıl). **En durağan**: Chemnitz, Trier, Stuttgart, Würzburg, Tübingen, Ulm.
- En pahalı = metropoller; **en ucuz segment = Doğu Almanya**.

## Öğrenci için çıkarımlar
- **Şehir seçimi = bütçe kararı.** Aynı program ucuz bir şehirde çok daha rahat yaşatır ([şehir mi üniversite mi](/tr/blog/city-vs-university-which-matters-more-in-germany)).
- **Pahalı şehirde okuyacaksan** WG + iyi toplu taşımalı çevre semt düşün — ama Speckgürtel'in artık ucuz olmayabileceğini ve commute maliyetini unutma.
- **Doğu Almanya şehirleri** öğrenci bütçesine çok daha dost (Leipzig'in hızlı arttığını akılda tut).
- **Mietpreisbremse:** Gergin piyasalarda yeni kira, yerel ortalamanın en fazla **%10 üstü** olabilir — hakkını bil.

## Sonuç
Almanya'da kira "ülke geneli" değil, **şehir ve hatta semt** meselesidir ve **hızla artıyor** (München 2019'da 17,5 € → 2025'te 26+ €). München zirvede, Doğu en ucuz, metropol çevresi sandığından pahalı. Öğrenciysen şehrini **bütçe + commute + program** üçgeninde seç ve **güncel** rakama (Mietspiegel/ilan) bak. İlgili: [ev/oda bulma](/tr/blog/finding-accommodation-in-germany-wg-and-housing-search-guide) · [kira maliyetleri](/tr/blog/germany-rental-costs-explained-kaltmiete-warmmiete-nebenkosten-kaution) · [öğrenci bütçe gerçeği](/tr/blog/real-cost-of-being-a-student-in-germany-budget-truth).

---
*Veri kaynakları: Value Marktdaten / empirica regio (ilan kirası, 2026 Q1'e kadar), ImmoScout24 WohnBarometer (Q3 2025), MLP Studentenwohnreport 2025. İlan kiraları sözleşme kirasından yüksek eğilimlidir; güncel rakamı yerel Mietspiegel ve ilanlardan teyit et.*
MD;
        $excerpt = 'Almanya\'da kira şehre göre dramatik değişir ve hızla arttı: München 2019\'da 17,5€ → 2025\'te 26+€/m². En pahalı metropoller (München, Frankfurt ~19€), en ucuz Doğu Almanya. Kirayı belirleyen 4 faktör (metropol, Speckgürtel, kıyı, sınır) + MLP 2025 öğrenci verisi (küçük daireler +%4,3; Leipzig/Freiburg/Konstanz en hızlı). Öğrenci için şehir = bütçe.';
        $html = Str::markdown($body, ['html_input' => 'allow', 'allow_unsafe_links' => false]);

        $post->update([
            'title' => 'Almanya\'da Şehir Şehir Kira (2025): En Pahalı/Ucuz Yerler ve Kiranı Belirleyen 4 Faktör',
            'excerpt' => Str::limit($excerpt, 250, '…'),
            'content_md' => $body,
            'content_html' => $html,
            'meta_title' => 'Almanya\'da Şehir Şehir Kira 2025: En Pahalı/Ucuz + 4 Faktör',
            'meta_description' => Str::limit($excerpt, 158, '…'),
            'reading_minutes' => max(1, (int) round(str_word_count(strip_tags($html)) / 200)),
        ]);
    }

    public function down(): void { /* içerik güncellemesi — geri alma yok */ }
};
