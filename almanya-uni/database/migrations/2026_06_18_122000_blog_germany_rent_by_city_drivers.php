<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/** TR blog (öğrenci yaşamı): Almanya kira coğrafyası — en pahalı/ucuz şehirler + kirayı belirleyen 4 faktör. FK-safe. */
return new class extends Migration
{
    public function up(): void
    {
        $slug = 'germany-rent-by-city-cheapest-most-expensive-and-what-drives-rent';
        $body = <<<'MD'
Almanya'da kira **şehre göre dramatik** değişir: en ucuz yer (Borgentreich) ile en pahalı (München) arasında **4 kattan fazla** fark var. Öğrenci için bu, doğrudan **bütçe** demek — nerede okuyacağın, ne kadar para harcayacağını belirler. Bu yazı (ev serisinin tamamlayıcısı) kiranın **nerede pahalı/ucuz** olduğunu ve **neden** öyle olduğunu gösterir.

> ⚠️ **Veri notu:** Rakamlar ZEIT ONLINE / empirica regio'nun **2019** verisinden (yeni kiralama, m² başına Kaltmiete). Bugün **daha yüksekler** — ama buradaki **örüntü ve sıralama** büyük ölçüde hâlâ geçerli. Güncel rakam için yerel **Mietspiegel** ve ilanlara bak. (Kira terimleri: [Kaltmiete/Warmmiete/Kaution](/tr/blog/germany-rental-costs-explained-kaltmiete-warmmiete-nebenkosten-kaution).)

## En pahalı: München ve Bavyera
- **München** açık ara lider: **17,51 €/m²**. Üstelik en pahalı 2-5. sıralar da Münih çevresi (Grünwald 16,67 · Neubiberg 16,36 · Unterföhring 16,03 · Oberschleißheim 16,00). Listenin **27. sırasına kadar neredeyse hep Bavyera** (tek istisna: Sylt adası).
- Diğer metropoller: **Frankfurt 13,86** · **Stuttgart 13,32** · **Hamburg 11,59** · **Köln 11,13** · **Berlin 10,49** (2012'den beri **+%42**).

## En ucuz: Doğu Almanya ve bazı kırsal bölgeler
- **Borgentreich** (NRW, Paderborn-Göttingen arası): **3,93 €/m²** — ülkenin en düşüğü.
- Saksonya: Elsterberg **4,00** · Seifhennersdorf **4,07** · Großschönau **4,15** · Thüringen Treffurt **4,17**.
- Öğrenci dostu büyük doğu şehirleri (2019): **Leipzig ~7,08** · **Dresden ~7,76** · **Magdeburg ~6,01** · **Erfurt ~7,39** · **Rostock ~7,67**.

## Kiranı belirleyen 4 faktör
ZEIT analizi kira artışının arkasında **dört örüntü** buluyor — şehir seçerken bunları bil:

### 1) Metropoller
Herkesin yaşamak istediği yerde konut kıt → kira yüksek. Dört milyonluk şehir (München, Berlin, Hamburg, Köln) kırsala kıyasla çok daha pahalı.

### 2) Speckgürtel (çevre kuşak) — sürpriz tuzak
Metropolde tutunamayanlar **çevreye** taşınır; bu da çevre kasabaların kirasını yukarı çeker. Örnek: Berlin'e 15 km **Teltow 10,61 €** — yani **Berlin'in kendisinden pahalı**! Dahası, kira **otoyol/tren hatları boyunca** yükselir (pendler talebi). Çevreye taşınırken **yol masrafı + zaman** maliyetini de hesapla; "ucuz" sandığın yer toplamda ucuz olmayabilir.

### 3) Kıyılar ve turistik bölgeler
Deniz/Alp manzarası primlidir. Ostsee kıyısı (Kühlungsborn 8,67 · Graal-Müritz 9,02 · Heringsdorf 8,91) iç kesimden (Franzburg-Richtenberg 5,00) neredeyse **iki kat** pahalı.

### 4) Sınırlar (özellikle batıda)
Lüksemburg sınırı: pahalı Lüksemburg yerine Almanya tarafında oturup işe gidenler talebi artırır → Perl (Saarland) **9,52 €** (+%20). Buna karşılık **doğu sınırı** (Polonya/Çek) ucuz kalır (~5 €) — bölge kırsal ve seyrek nüfuslu.

## Öğrenci için çıkarımlar
- **Şehir seçimi = bütçe kararı.** Aynı program, ucuz bir şehirde çok daha rahat yaşatır ([şehir mi üniversite mi](/tr/blog/city-vs-university-which-matters-more-in-germany)).
- **Pahalı şehirde okuyacaksan** WG + iyi toplu taşımalı çevre semt düşün — ama Speckgürtel'in artık ucuz olmayabileceğini ve commute maliyetini unutma.
- **Doğu Almanya şehirleri** (Leipzig, Dresden, Erfurt, Magdeburg, Rostock) öğrenci bütçesine **çok daha dost**.
- **Mietpreisbremse:** Gergin piyasalarda yeni kira, yerel ortalamanın en fazla **%10 üstü** olabilir — hakkını bil, fahiş kirayı sorgula.

## Sonuç
Almanya'da kira "ülke geneli" değil, **şehir ve hatta semt** meselesidir: München zirvede, Doğu en ucuz, metropol çevresi (Speckgürtel) sandığından pahalı, kıyı/sınır bölgeleri primli. Öğrenciysen şehrini **bütçe + commute + program** üçgeninde seç. İlgili: [ev/oda bulma](/tr/blog/finding-accommodation-in-germany-wg-and-housing-search-guide) · [kira maliyetleri](/tr/blog/germany-rental-costs-explained-kaltmiete-warmmiete-nebenkosten-kaution) · [öğrenci bütçe gerçeği](/tr/blog/real-cost-of-being-a-student-in-germany-budget-truth).

---
*Veri kaynağı: ZEIT ONLINE / empirica regio (2019, yeni kiralama Kaltmiete). Güncel kiralar daha yüksektir — yerel Mietspiegel ve ilanlardan teyit et.*
MD;
        $excerpt = 'Almanya\'da kira şehre göre 4 kattan fazla değişir (Borgentreich 3,93€ ↔ München 17,51€). En pahalı München+Bavyera, en ucuz Doğu Almanya. Kirayı belirleyen 4 faktör: metropoller, Speckgürtel (çevre kuşak — Teltow Berlin\'den pahalı!), kıyılar, sınırlar. Öğrenci için şehir seçimi = bütçe. (Veri: ZEIT/empirica regio 2019.)';
        $html = Str::markdown($body, ['html_input' => 'allow', 'allow_unsafe_links' => false]);
        $userId = DB::table('users')->where('id', 4)->exists() ? 4 : DB::table('users')->orderBy('id')->value('id');
        $categoryId = DB::table('categories')->where('id', 16)->exists() ? 16 : DB::table('categories')->where('slug', 'student-life')->value('id');
        $payload = ['locale' => 'tr', 'user_id' => $userId, 'category_id' => $categoryId,
            'title' => 'Almanya\'da Şehir Şehir Kira: En Pahalı/Ucuz Yerler ve Kiranı Belirleyen 4 Faktör',
            'excerpt' => Str::limit($excerpt, 250, '…'), 'content_md' => $body, 'content_html' => $html,
            'meta_title' => 'Almanya\'da Şehir Şehir Kira: En Pahalı/Ucuz Yerler + 4 Faktör',
            'meta_description' => Str::limit($excerpt, 158, '…'),
            'reading_minutes' => max(1, (int) round(str_word_count(strip_tags($html)) / 200)),
            'is_published' => true, 'published_at' => now()];
        $existing = Post::where('slug', $slug)->first();
        if ($existing) { $existing->update($payload); } else { Post::create($payload + ['slug' => $slug, 'translation_group_id' => (string) Str::uuid()]); }
    }
    public function down(): void { Post::where('slug', 'germany-rent-by-city-cheapest-most-expensive-and-what-drives-rent')->delete(); }
};
