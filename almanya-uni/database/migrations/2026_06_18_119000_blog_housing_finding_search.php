<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/** TR blog (öğrenci yaşamı): Almanya'da ev/oda bulma rehberi #1 — WG, siteler, kısa-dönem stratejisi, başvuru. FK-safe. */
return new class extends Migration
{
    public function up(): void
    {
        $slug = 'finding-accommodation-in-germany-wg-and-housing-search-guide';
        $body = <<<'MD'
Almanya'da öğrenciyi bekleyen ilk büyük sınav ders değil — **ev bulmak.** Özellikle büyük şehirlerde talep yüksek, arz az; bir kontrat imzalamak haftalar sürebilir. Bu rehber (3 yazılık ev serisinin **1.'si**) konutu **nasıl bulacağını** anlatır. (Devamı: [kira & maliyetler](/tr/blog/germany-rental-costs-explained-kaltmiete-warmmiete-nebenkosten-kaution) · [sözleşme & dolandırıcılık](/tr/blog/german-rental-contract-moving-in-out-and-avoiding-housing-scams).)

## Konut tipleri
1. **Uzun dönem:** Normal daire/ev veya paylaşımlı ev (**WG = Wohngemeinschaft**). Sözleşme varsayılan olarak **süresiz**.
2. **Kısa dönem:** Zwischenmiete (devren/sublet), Wohnen auf Zeit (mobilyalı, süreli), Monteurzimmer, Jugendherberge (hostel) — birkaç hafta-birkaç ay.
3. **Ticari:** Otel/hostel.

## Altın strateji: önce kısa dönem, sonra yerinde ara
Yurt dışından **uzaktan** kalıcı daire bulmak neredeyse imkânsız — ev sahipleri kiracıyı **yüz yüze** görmek ister. Doğru plan:
1. Gelmeden önce **1-2 aylık kısa dönem** (Wohnen auf Zeit / WG Zwischenmiete / hostel) ayarla.
2. **Yerinde** ol, görüntülemelere (Besichtigung) bizzat katıl, kalıcı yeri öyle bul.
> Büyük şehirlerde onlarca daire gezip haftalarca aramak normaldir — buna göre planla.

## Öğrenci için en mantıklısı: WG
**WG (paylaşımlı ev)** öğrenciler için genelde **en ucuz ve en kolay** yol. Artısı: bazı WG'ler (özellikle uluslararası öğrenciye açık olanlar) **Skype/online mülakat** kabul eder — yani gelmeden bulma şansın daha yüksek. WG görüşmesinde odak **ev arkadaşlarıdır**: yaşam tarzın, programın, ortak hayata katkın (yemek, temizlik) sorulur.

## Nerede aranır? (siteler)
- **wg-gesucht** — WG/oda için bir numara.
- **ImmobilienScout24**, **Immonet** — büyük daire/ev portalları.
- **Kleinanzeigen** — ilan sitesi; mobilyalı/kısa dönem de çıkar.
- **Yerel gazete ilanları** — özellikle geleneksel ev sahipleri; **çok daha az rekabet**.
- ⚠️ **Craigslist KULLANMA** — Almanya'da kullanılmaz; oradaki ilanlar büyük olasılıkla **dolandırıcılık**. (Korunma: [dolandırıcılık bölümü](/tr/blog/german-rental-contract-moving-in-out-and-avoiding-housing-scams).)

## Kısa dönem seçenekleri (ilk inişe)
- **Wohnen auf Zeit** — mobilyalı, süreli daire; pahalı ama bulması kolay, yeni gelenler için ideal.
- **Zwischenmiete** — birinin (ör. Erasmus'a giden öğrenci) dairesini/odasını geçici devretmesi.
- **Monteurzimmer** — gezici işçiler için ucuz odalar; herkes kullanabilir, uzun kalışta pazarlık edilebilir.
- **Jugendherberge / hostel**, **apart-hotel**, **Wunderflats / Mr Lodge** (mobilyalı, pahalı).

## Başvuru: kendini "güvenilir kiracı" olarak göster
- **Selbstauskunft** (kendini tanıtma formu): ev sahibi kirayı **düzenli ödeyebileceğini** görmek ister. Banka dökümü/maaş bordrosu varsa ekle.
- **Schufa** (kredi notu belgesi) istenebilir (yeni gelende olmayabilir; alternatif belgelerle telafi et).
- Görüntülemede **temiz/derli toplu** ol; üniversiteye gider gibi normal kıyafet yeterli (takım elbise abartı durur).
- **Almanca büyük avantaj:** Ev sahipleri "sorunsuz" kiracı seçer; Almanca bilmemek seni listeden eler. (Bkz. [Almanca bilmeden yaşamak gerçeği](/tr/blog/studying-in-germany-without-german-living-and-student-job-reality).)

## Sonuç
Almanya'da ev bulmanın anahtarı: **önce kısa dönemle gel**, **yerinde ara**, öğrenciysen **WG'ye yönel**, doğru sitelerde (wg-gesucht, ImmoScout) **çok başvur** ve kendini güvenilir göster. Sırada para tarafı var → [kira, Nebenkosten ve Kaution nasıl işler](/tr/blog/germany-rental-costs-explained-kaltmiete-warmmiete-nebenkosten-kaution). İlgili: [öğrenci bütçe gerçeği](/tr/blog/real-cost-of-being-a-student-in-germany-budget-truth) · [şehir mi üniversite mi](/tr/blog/city-vs-university-which-matters-more-in-germany).

---
*Genel rehberdir. Site ve fiyatlar şehre/döneme göre değişir.*
MD;
        $excerpt = 'Almanya\'da ev/oda bulma rehberi: konut tipleri (WG, kısa dönem, ticari), "önce kısa dönemle gel-yerinde ara" stratejisi, öğrenci için en mantıklısı WG, doğru siteler (wg-gesucht, ImmoScout, Immonet — Craigslist YOK) ve başvuru (Selbstauskunft, Schufa). 3 yazılık ev serisinin 1.\'si.';
        $html = Str::markdown($body, ['html_input' => 'allow', 'allow_unsafe_links' => false]);
        $userId = DB::table('users')->where('id', 4)->exists() ? 4 : DB::table('users')->orderBy('id')->value('id');
        $categoryId = DB::table('categories')->where('id', 16)->exists() ? 16 : DB::table('categories')->where('slug', 'student-life')->value('id');
        $payload = ['locale' => 'tr', 'user_id' => $userId, 'category_id' => $categoryId,
            'title' => 'Almanya\'da Ev/Oda Bulma Rehberi: WG, Siteler ve Akıllı Arama Stratejisi',
            'excerpt' => Str::limit($excerpt, 250, '…'), 'content_md' => $body, 'content_html' => $html,
            'meta_title' => 'Almanya\'da Ev/Oda Bulma: WG, Siteler ve Arama Stratejisi',
            'meta_description' => Str::limit($excerpt, 158, '…'),
            'reading_minutes' => max(1, (int) round(str_word_count(strip_tags($html)) / 200)),
            'is_published' => true, 'published_at' => now()];
        $existing = Post::where('slug', $slug)->first();
        if ($existing) { $existing->update($payload); } else { Post::create($payload + ['slug' => $slug, 'translation_group_id' => (string) Str::uuid()]); }
    }
    public function down(): void { Post::where('slug', 'finding-accommodation-in-germany-wg-and-housing-search-guide')->delete(); }
};
