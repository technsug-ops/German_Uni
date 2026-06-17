<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * TR blog: Konsolosluğa gitmeden önce — Almanya vize süreci nasıl işler + dikkat
 * edilecekler. Resmi + iDATA gerçeği + kendi topluluk havuzumuzun gerçek soruları.
 * FK-safe, İngilizce slug.
 */
return new class extends Migration
{
    public function up(): void
    {
        $slug = 'germany-student-visa-consulate-process-before-you-go';

        $body = <<<'MD'
Üniversite kabulün geldi, finansmanını ayarladın — sıra **vize başvurusunda.** Peki konsolosluk süreci tam olarak nasıl işliyor, randevu gününde ne oluyor ve gitmeden önce nelere dikkat etmelisin? Bu rehber, Almanya öğrenci vizesi sürecini adım adım + kendi topluluğumuzun gerçek sorularıyla anlatıyor.

> ⚠️ **Önemli:** Türkiye'de başvurular çoğunlukla **[iDATA](https://www.idata.com.tr) vize merkezi** üzerinden yapılır (randevu, belge teslimi, biyometri); **konsolosluk** başvuruyu değerlendirip karar verir. Kurallar ve süreler değişebilir — başvuracağın temsilciliğin güncel listesini **[tuerkei.diplo.de](https://tuerkei.diplo.de)** ve iDATA'dan teyit et. İletişim bilgileri için [Almanya konsoloslukları iletişim rehberimize](/tr/blog/germany-consulates-turkey-contact-ankara-istanbul-izmir) bak.

## Süreç nasıl işliyor? (Adım adım)

1. **Üniversite kabulü** (kesin veya şartlı) — vize başvurusunun temeli.
2. **Finansman kanıtı** — [Sperrkonto](/tr/blog/sperrkonto-for-a-german-visa-what-is-it-how-much-and) (2026: ~11.904 €) veya [garantör belgesi](/tr/blog/germany-guarantor-declaration-verpflichtungserklarung-guide).
3. **Randevu** — iDATA üzerinden ulusal vize randevusu al. Yoğunluk yüksek; **en az 2-3 ay önceden** planla.
4. **Belge hazırlığı** — istenen tüm belgeleri eksiksiz, **konsolosluğun belirttiği sıraya göre**, orijinal + fotokopi olarak hazırla.
5. **Randevu günü** — belgeleri teslim et, **biyometrik veri (parmak izi + foto)** alınır, ücret ödenir.
6. **Değerlendirme** — konsolosluk başvurunu inceler; gerekirse ek belge veya **görüşme** ister.
7. **Sonuç** — pasaportun vize ile (veya ret/eksik bildirimiyle) döner.

## İşlem süresi ne kadar?

Resmî tatiller hariç genelde **en az 15 iş günü**; yoğunluk ve özel durumlarda **45 güne kadar** uzayabilir. Bu yüzden uçuş/okul tarihinden **çok önce** başvur.

## Gitmeden önce — kontrol listesi

- ✅ **Pasaport**: seyahatten sonra **en az 3 ay geçerli**, **en az 2 boş sayfa**; eski pasaport/vizelerin fotokopileri.
- ✅ **Başvuru formu**: ulusal vize formu (VIDEX) eksiksiz ve imzalı.
- ✅ **Biyometrik fotoğraf** (güncel, standartlara uygun).
- ✅ **Üniversite kabul mektubu** (kesin/şartlı).
- ✅ **Finansman kanıtı**: Sperrkonto onayı veya garantör belgesi.
- ✅ **Sağlık sigortası** (vize için geçici/seyahat sağlık sigortası — ör. Expatrio/DR-WALTER belgesi).
- ✅ **Dil belgesi** (programın istediği seviye; şartlı kabulde planını göster).
- ✅ **Belgeler doğru SIRADA**, orijinal + fotokopi; gerekiyorsa yeminli tercüme/apostil.
- ✅ **Randevu/ödeme** dekontu ve iDATA evrak listesi.

> 💡 En sık ret/eksik sebebi: **eksik veya çelişkili belge**. Listeyi konsolosluğun/iDATA'nın güncel sayfasından kontrol et — şehir/temsilciliğe göre küçük farklar olabilir.

## Görüşme (mülakat) olursa

Konsolosluk gerekirse seni **kısa bir görüşmeye** çağırabilir. Tipik sorular: **neden Almanya/bu bölüm**, **finansmanı nasıl karşılıyorsun**, **mezuniyet sonrası planın**. Cevapların **net, tutarlı ve belgelerinle uyumlu** olsun — abartma, ezbere konuşma; planını gerçekçi anlat.

## Hangi konsolosluğa başvuracaksın?

**İkamet ettiğin ile göre** yetkili temsilcilik belirlenir (Ankara / İstanbul / İzmir). İkametini değiştirip başka konsolosluğa geçmek bazen bekleme/farklı uygulama getirir. İletişim ve adresler: [konsolosluk iletişim rehberi](/tr/blog/germany-consulates-turkey-contact-ankara-istanbul-izmir).

## Topluluğumuzdan en çok sorulanlar (gerçek sorular)

### "Belgem (ör. Telc B1) randevuya yetişmedi. Eksik mi vereyim, randevuyu mu erteleyeyim?"

Eksik/kritik belgeyle gitmek **ret veya gecikme riski** taşır. Mümkünse belge hazır olunca git. Ama randevu bulmak zor olduğundan, iDATA/konsolosluğa durumu yazıp **eksik belgeyi sonradan tamamlama** imkânı olup olmadığını sor; bazı durumlarda "eksik belge" olarak alınıp sonradan tamamlanabilir, bazılarında alınmaz.

### "Vize görüşmesini konsolosluktan mı, iDATA'dan mı yapıyorlar?"

Belge teslimi ve biyometri **iDATA'da** olur; **karar konsolosluğundur** ve görüşmeye **konsolosluk** çağırır. Her başvuruda görüşme olmaz — genelde belge yeterliyse görüşmesiz ilerler.

### "Sağlık sigortası belgesini (Expatrio/indirilebilir) vize randevusunda kullanabilir miyim?"

Genelde evet — vize için **geçici/seyahat sağlık sigortası** belgesi kabul edilir (Expatrio, DR-WALTER vb.). Almanya'ya gidince üniversite kaydı için **yasal sağlık sigortasına** (TK/AOK gibi) geçersin. Belgenin kapsam/tarihlerinin başvuru gününü kapsadığından emin ol.

### "Şartlı kabul için konsolosluk A1 Almanca istiyor, doğru mu?"

Temsilciliğe ve programa göre değişir — bazı konsolosluklar dil-hazırlık/şartlı kabul başvurusunda **temel bir Almanca (ör. A1)** veya net bir dil planı bekleyebilir. Başvuracağın konsolosluğun güncel şartını teyit et. (Bkz. [şartlı kabul rehberi](/tr/blog/germany-conditional-admission-bedingte-zulassung-guide).)

### "Randevumu erteledim, en erken ne zaman yeni randevu çıkıyor?"

Tamamen şehir/temsilcilik yoğunluğuna bağlı; İstanbul/İzmir/Ankara'da dönemsel olarak çok değişir. Erteleme sonrası sistemde açılan ilk uygun tarihi yakalamaya çalış — bu yüzden baştan **erken** başvurmak kritik.

### "SGK hizmet dökümünün Almanca çevirisine gerek var mı?"

İstenen belgeler arasında özel olarak belirtilmemişse genelde gerek olmaz; ama konsolosluğun evrak listesi neyi nasıl istiyorsa ona uy. Barkodlu/resmi belgeler genelde olduğu gibi kabul edilir — yine de listeyi kontrol et.

## Sık yapılan hatalar

- Randevuyu **geç** almak (yoğunlukta tarih bulamamak).
- Belgeleri **yanlış sırada** veya eksik/fotokopisiz götürmek.
- Pasaport geçerliliği / boş sayfa şartını atlamak.
- Finansman kanıtını (Sperrkonto/garantör) **son ana** bırakmak.
- Görüşmede belgelerle **çelişen** beyan vermek.

## Sonuç

Almanya vize süreci, **erken planlama + eksiksiz ve tutarlı belge** ile genelde sorunsuz ilerler. Randevuyu erken al, finansmanı (Sperrkonto/garantör) ve sağlık sigortasını zamanında hazırla, belgeleri konsolosluğun istediği sırada götür. Her zaman başvuracağın temsilciliğin ve iDATA'nın **güncel** listesini teyit et.

---

**Kaynaklar:** Almanya Dışişleri Bakanlığı ([tuerkei.diplo.de](https://tuerkei.diplo.de)), iDATA, ve topluluğumuzun gerçek deneyimleri.
MD;

        $excerpt = 'Almanya öğrenci vizesi konsolosluk süreci adım adım: iDATA randevusu, biyometri, belge hazırlığı, görüşme, işlem süresi ve gitmeden önce kontrol listesi — 2026 rehberi ve topluluğumuzdan gerçek sorular.';

        $html = Str::markdown($body, ['html_input' => 'allow', 'allow_unsafe_links' => false]);

        $userId = DB::table('users')->where('id', 6)->exists() ? 6 : DB::table('users')->orderBy('id')->value('id');
        $categoryId = DB::table('categories')->where('id', 6)->exists() ? 6 : DB::table('categories')->where('slug', 'vize')->value('id');

        $payload = [
            'locale'           => 'tr',
            'user_id'          => $userId,
            'category_id'      => $categoryId,
            'title'            => 'Konsolosluğa Gitmeden Önce: Almanya Vize Süreci Nasıl İşler? (2026 Rehberi)',
            'excerpt'          => Str::limit($excerpt, 250, '…'),
            'content_md'       => $body,
            'content_html'     => $html,
            'meta_title'       => 'Almanya Vize Süreci & Konsolosluk Öncesi Dikkat (2026)',
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
        Post::where('slug', 'germany-student-visa-consulate-process-before-you-go')->delete();
    }
};
