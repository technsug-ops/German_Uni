<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * TR blog: Öğrenciler Türk ehliyetini Almanya'da kullanabilir mi? 6 ay kuralı +
 * Umschreibung (dönüşüm) süreci, belgeler, maliyetler, hukuki gereklilikler.
 * Kullanıcı verisi + genel web araştırması + topluluk sorusu. FK-safe, İngilizce slug.
 */
return new class extends Migration
{
    public function up(): void
    {
        $slug = 'turkish-driving-license-in-germany-conversion-guide';

        $body = <<<'MD'
Almanya'ya öğrenci olarak geldin ve aracını kullanmak istiyorsun — peki **Türk ehliyetin Almanya'da geçerli mi?** Kısa cevap: **ilk 6 ay evet**, sonrasında **Alman ehliyetine çevirmen (Umschreibung) gerekir** — ve Türkiye karşılıklı-tanıma listesinde olmadığı için **teorik + pratik sınava** girmen gerekir. Bu rehber süreci, belgeleri, maliyetleri ve hukuki incelikleri anlatıyor.

> ⚠️ Kurallar eyalete (Bundesland) ve şehrin **Führerscheinstelle/Fahrerlaubnisbehörde**'sine göre küçük farklar gösterir. Başvurmadan önce kendi şehrinin sayfasından teyit et. Bu yazı bilgilendirme amaçlıdır, hukuki danışmanlık değildir.

## Kısa cevap: 6 ay kuralı

- Türk ehliyetin, Almanya'da **ikamet kaydı (Anmeldung) yaptığın tarihten itibaren 6 ay** geçerlidir; bu sürede araç kullanabilirsin.
- **6 ayın sonunda** Almanya'da kullanmaya devam etmek için ehliyetini **Alman ehliyetine çevirmen (Umschreibung)** zorunludur.
- **Uzatma:** Almanya'da **en fazla 12 ay** kalacağını kanıtlarsan (ör. kısa değişim programı), Führerscheinstelle'den izinle bu süre **12 aya** uzatılabilir.

## İlk 6 ayda ne yapmalısın?

Bu dönemde araç kullanırken yanında **ehliyetinin Almanca tercümesi** (ADAC veya yeminli tercüman) **veya uluslararası sürücü belgesi** bulundur. Trafik kontrolünde sadece Türkçe ehliyet sorun çıkarabilir.

## Öğrenci olarak senin durumun

- **Kısa kalış (değişim/Erasmus, ≤6 ay; uzatmayla ≤12 ay):** Genelde çevirmene gerek kalmadan Türk ehliyeti + tercüme/uluslararası belge ile sürebilirsin.
- **Uzun kalış (lisans/master, >6 ay):** 6 ay dolmadan **Umschreibung** sürecini başlatman gerekir. Süreç haftalar/aylar sürebildiği için **erken başla**.

## Umschreibung süreci — adım adım

1. **Sürücü kursuna kayıt** (Fahrschule) — tüm zorunlu dersleri almak gerekmez, ama **pratik sınav için birkaç ders** (genelde 5+) çok faydalı; Almanya'nın trafik kurallarını öğrenirsin.
2. **İlk yardım kursu** (Erste-Hilfe-Kurs) — bir günlük (genelde ~9 ders saati), Almanca verilir; sertifika şart.
3. **Göz testi** (Sehtest) — optikçide yaptırılır (birkaç dakika).
4. **Belediyeye/Führerscheinstelle'ye başvuru** — "Umschreibung einer ausländischen Fahrerlaubnis aus einem Nicht-EU/EWR-Land". Randevu al, belgeleri ver.
5. **Teorik sınav** — başvuru sonrası sınav daveti/ödeme bilgisi gelir (~4 hafta sürebilir). Hafife alma; sürücü kursu hazırlık sağlar.
6. **Pratik (direksiyon) sınavı** — teoriyi geçince. Alman kurallarına dikkat (ör. döner kavşakta sinyal).
7. **Ehliyetini al** — her iki sınavı geçince Alman ehliyetin hazırlanır (yaklaşık 1 ay).

> 💡 Sürücü kursuna kayıt olunca genelde ilk yardım, göz testi, belediye/TÜV randevuları ve sınavlar için **seni yönlendirir / adına randevu alır.**

## Gerekli belgeler

- **Pasaport** (Reisepass)
- **İkamet kaydı** (Anmeldung / Meldebescheinigung)
- **Türk ehliyetinin aslı + fotokopisi** (üstünde İngilizce/uluslararası açıklama yoksa **Almanca çeviri** — ADAC veya yeminli tercüman)
- **Göz testi belgesi** (Sehtest)
- **İlk yardım kursu sertifikası** (Erste-Hilfe-Kurs)
- **Biyometrik fotoğraf**
- **Ehliyetinin hâlâ geçerli olduğunu gösteren belge** (ör. e-Devlet "Sürücü Belgesi Bilgileri Sorgulama")
- **Sürücü kursu kayıt sözleşmesi** (Anmeldebescheinigung einer Fahrschule)

(Belediyeden belediyeye küçük farklar olabilir — kendi şehrinin listesini kontrol et.)

## Maliyetler (yaklaşık, 2026)

| Kalem | Tahmini tutar |
|---|---|
| Göz testi (Sehtest) | ~7 € |
| İlk yardım kursu | ~30-50 € |
| Ehliyet Almanca çeviri (ADAC/yeminli) | ~30-60 € |
| Biyometrik fotoğraf | ~10-15 € |
| Teorik sınav ücreti | ~25 € |
| Pratik sınav ücreti | ~120-140 € |
| Umschreibung (daire) ücreti | ~35-50 € |
| Sürücü dersi (opsiyonel, önerilir) | 45 dk ~70-75 € · 90 dk blok ~140-150 € |

**Toplam:** kaç ders aldığına göre genelde **~800-1.300 €**; yoğun pratik dersle daha yükseğe (bazıları ~2.200 € ödedi) çıkabilir. Yine de Umschreibung, sıfırdan ehliyete göre **~1.500-2.000 € tasarruf** ettirir (zorunlu tüm dersleri almazsın).

## Hukuki gereklilikler ve dikkat

- **6 ay kuralı bağlayıcıdır.** 6 ay dolduktan sonra çevirmeden araç kullanmak, **geçersiz ehliyetle sürmek** sayılır — ceza, sigorta sorunları ve kaza halinde ağır sonuçlar doğurabilir.
- Süre **Anmeldung tarihinden** işler; ertelersen sınav hazırlığına vaktin daralır.
- **Kasko/trafik sigortası**, geçerli (çevrilmiş veya 6 ay içindeki) ehliyet varsayar — geçersiz ehliyette tazminat reddedilebilir.
- Çeviri **resmi** olmalı (ADAC veya yeminli tercüman); rastgele çeviri kabul edilmez.

## Topluluğumuzdan: "İlk 6 ay ehliyeti kullanmak için bir işlem gerekiyor mu?"

Hayır, **ayrı bir izin/işlem gerekmez** — ilk 6 ay (uzatmayla 12 ay) Türk ehliyetinle sürebilirsin. Tek pratik öneri: yanında **Almanca tercüme veya uluslararası sürücü belgesi** bulundur. Ama 6 ay dolmadan **Umschreibung'u başlat** — sınav randevuları haftalar alır.

## Sonuç

Öğrenciysen ve Almanya'da **6 aydan uzun** kalacaksan, Türk ehliyetini **6 ay dolmadan** Alman ehliyetine çevirmeye başla: ilk yardım + göz testi + çeviri + sürücü kursu kaydı + teorik ve pratik sınav. Maliyet ders sayına göre ~800-1.300 €. Kısa süre (≤6-12 ay) kalacaksan tercüme/uluslararası belgeyle sürebilirsin. Her zaman kendi şehrinin Führerscheinstelle sayfasını teyit et.

---

**Kaynaklar:** § 29 FeV (yabancı ehliyetle sürüş süresi), ADAC, eyalet Führerscheinstelle sayfaları ve güncel rehberler.
MD;

        $excerpt = 'Türk ehliyeti Almanya\'da geçerli mi? İlk 6 ay evet; sonra Umschreibung (dönüşüm) + teorik/pratik sınav zorunlu. Öğrenciler için süreç, belgeler, hukuki gereklilikler ve ~800-1.300 € maliyet — 2026 rehberi.';

        $html = Str::markdown($body, ['html_input' => 'allow', 'allow_unsafe_links' => false]);

        $userId = DB::table('users')->where('id', 6)->exists() ? 6 : DB::table('users')->orderBy('id')->value('id');
        $categoryId = DB::table('categories')->where('id', 9)->exists() ? 9 : DB::table('categories')->where('slug', 'yasam')->value('id');

        $payload = [
            'locale'           => 'tr',
            'user_id'          => $userId,
            'category_id'      => $categoryId,
            'title'            => 'Türk Ehliyeti Almanya\'da Geçerli mi? Öğrenciler İçin Dönüşüm (Umschreibung) Rehberi 2026',
            'excerpt'          => Str::limit($excerpt, 250, '…'),
            'content_md'       => $body,
            'content_html'     => $html,
            'meta_title'       => 'Türk Ehliyeti Almanya\'da Geçerli mi? Umschreibung Rehberi 2026',
            'meta_description' => Str::limit($excerpt, 158, '…'),
            'reading_minutes'  => max(1, (int) round(str_word_count(strip_tags($html)) / 200)),
            'is_published'     => true,
            'published_at'     => now(),
        ];

        $existing = Post::where('slug', $slug)->first();
        if ($existing) {
            $existing->update($payload);
        } else {
            Post::create($payload + ['slug' => $slug, 'translation_group_id' => (string) Str::uuid()]);
        }
    }

    public function down(): void
    {
        Post::where('slug', 'turkish-driving-license-in-germany-conversion-guide')->delete();
    }
};
