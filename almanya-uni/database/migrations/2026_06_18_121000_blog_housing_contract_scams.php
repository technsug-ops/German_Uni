<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/** TR blog (öğrenci yaşamı): Almanya'da kira sözleşmesi, taşınma & dolandırıcılık #3 — Übergabeprotokoll, Anmeldung, Makler, scam. FK-safe. */
return new class extends Migration
{
    public function up(): void
    {
        $slug = 'german-rental-contract-moving-in-out-and-avoiding-housing-scams';
        $body = <<<'MD'
Evi buldun, parayı anladın — sıra **sözleşme, taşınma ve dolandırıcılıktan korunmada.** Ev serisinin **3. ve son** yazısı, en çok hata yapılan ama hakların açısından en kritik kısmı anlatır. (Önceki: [ev bulma](/tr/blog/finding-accommodation-in-germany-wg-and-housing-search-guide) · [kira & maliyetler](/tr/blog/germany-rental-costs-explained-kaltmiete-warmmiete-nebenkosten-kaution).)

## Sözleşme: varsayılan süresizdir
- Almanya'da kira sözleşmeleri **varsayılan olarak süresizdir** ve ev sahibi ancak **çok özel** durumlarda fesheder (en yaygını: evi kendi kullanacak olması). Bu, kiracıya **güçlü koruma** sağlar.
- **Süreli sözleşme** mümkün ama ev sahibi sebebini (ör. planlı büyük tadilat) yazmak zorundadır; sebep geçersizse sözleşme **otomatik süresize** döner.
- **Çıkış bildirimi (Kündigung):** Genelde **3 ay** önceden; çoğu sözleşme ayın son günü çıkışa izin verir — kontratını oku.

## Taşınırken: Übergabeprotokoll (devir tutanağı) — ATLAMA
Taşındığın gün ev sahibiyle daireyi gezip bir **Übergabeprotokoll** imzalarsın:
- **Mevcut hasarlar** (boya, çizik, leke) ve **sayaç değerleri** (elektrik, su, gaz) yazılır.
- Bu adımı **atlama**: aksi halde önceki hasarlardan **sen sorumlu** tutulabilirsin.
- Almancan zayıfsa **yanına Almanca bilen biri** al; her şeyin doğru yazıldığından emin ol.

## Anmeldung: 14 gün içinde + Wohnungsgeberbestätigung
- Ev sahibi sana **Wohnungsgeberbestätigung** (oturma teyidi) vermek **zorundadır** — bu belge olmadan adres kaydı (Anmeldung) yapamazsın.
- Taşındıktan sonra **2 hafta içinde** Anmeldung yapman gerekir. (Adım adım: [Anmeldung rehberi](/tr/blog/how-to-do-the-anmeldung-address-registration-guide-in-germany-within).)

## Çıkarken
- Daireyi kabaca **girişteki haline** getirmen beklenir (sözleşmeye bak).
- Çıkışta yeniden **Übergabeprotokoll** imzalanır → ev sahibinin depozitodan ne kesebileceğini **bu belirler**. Mutlaka hazır bulun, yine yanına tanık al.
- Depozito iadesi genelde iki adımda: çoğu **3-6 ay** içinde, kalanı yan gider kapanışıyla ertesi yıl.

## Emlakçı (Makler): kim öder?
- **2015'ten beri** emlakçıyı, onu **işe alan taraf** öder — bu neredeyse her zaman **ev sahibidir** (Bestellerprinzip).
- Yani sen, **kendin özel olarak tutmadıysan** emlakçıya **para ödememelisin**. "Önce sözleşme imzala" diye seni ödemeye zorlayan emlakçılara dikkat.

## Çoklu kiracı (WG / çift)
- Tek sözleşmede birden çok isim varsa, **müşterek sorumlusunuz** (kira, dairenin durumu hepinizden). Biri erken çıkmak isterse herkesin + ev sahibinin onayı gerekir.
- WG'de mümkünse **her odaya ayrı sözleşme** olanı tercih et; değilse masraf paylaşımını **baştan net** konuş.

## 🚨 Dolandırıcılıktan korunma
Yabancıları hedefleyen ev dolandırıcılığı yaygındır. Kurallar:
- **İmzalı sözleşme + anahtar olmadan ASLA ödeme yapma** (ne depozito, ne "yer tutma").
- "Yurt dışındayım, anahtarı kargolarım, önce depozitoyu yatır" → **klasik dolandırıcılık**.
- Var olmayan ya da geçici kiralanmış daireyle seni inandırmaya çalışabilirler.
- **Craigslist** ilanları neredeyse kesin sahte. Şüpheli ucuz + acele eden ilanlardan uzak dur.
- Ödeme **banka havalesiyle** ve **sözleşmeden sonra** olsun; elden ödediysen **makbuz** iste.

## Sonuç
Almanya'da kiracı hakları güçlüdür ama korunmak için **süreçleri bil**: süresiz sözleşme + 3 ay bildirim, **Übergabeprotokoll'u asla atlama**, **Wohnungsgeberbestätigung** alıp 14 günde **Anmeldung** yap, emlakçıya gereksiz ödeme yapma ve **sözleşme+anahtar olmadan tek kuruş verme**. Seriyi baştan oku: [ev bulma](/tr/blog/finding-accommodation-in-germany-wg-and-housing-search-guide) · [kira & maliyetler](/tr/blog/germany-rental-costs-explained-kaltmiete-warmmiete-nebenkosten-kaution).

---
*Genel bilgilendirmedir, hukuki tavsiye değildir. Tereddütte Mieterverein (kiracı derneği) veya resmî kaynaklara danış.*
MD;
        $excerpt = 'Almanya\'da kira sözleşmesi, taşınma ve dolandırıcılıktan korunma: süresiz sözleşme + 3 ay bildirim, Übergabeprotokoll (devir tutanağı) asla atlanmaz, Wohnungsgeberbestätigung + 14 günde Anmeldung, emlakçıyı 2015\'ten beri ev sahibi öder, ve en önemlisi: imzalı sözleşme + anahtar olmadan ASLA ödeme yapma. Ev serisinin 3. yazısı.';
        $html = Str::markdown($body, ['html_input' => 'allow', 'allow_unsafe_links' => false]);
        $userId = DB::table('users')->where('id', 4)->exists() ? 4 : DB::table('users')->orderBy('id')->value('id');
        $categoryId = DB::table('categories')->where('id', 16)->exists() ? 16 : DB::table('categories')->where('slug', 'student-life')->value('id');
        $payload = ['locale' => 'tr', 'user_id' => $userId, 'category_id' => $categoryId,
            'title' => 'Almanya\'da Kira Sözleşmesi, Taşınma ve Dolandırıcılıktan Korunma',
            'excerpt' => Str::limit($excerpt, 250, '…'), 'content_md' => $body, 'content_html' => $html,
            'meta_title' => 'Almanya Kira Sözleşmesi, Taşınma & Ev Dolandırıcılığından Korunma',
            'meta_description' => Str::limit($excerpt, 158, '…'),
            'reading_minutes' => max(1, (int) round(str_word_count(strip_tags($html)) / 200)),
            'is_published' => true, 'published_at' => now()];
        $existing = Post::where('slug', $slug)->first();
        if ($existing) { $existing->update($payload); } else { Post::create($payload + ['slug' => $slug, 'translation_group_id' => (string) Str::uuid()]); }
    }
    public function down(): void { Post::where('slug', 'german-rental-contract-moving-in-out-and-avoiding-housing-scams')->delete(); }
};
