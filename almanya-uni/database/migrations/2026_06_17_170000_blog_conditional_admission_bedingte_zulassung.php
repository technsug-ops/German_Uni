<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR): Almanya'da Şartlı Kabul (Bedingte Zulassung) — Bachelor + Master 2026 rehberi.
 *
 * Kaynak: resmi üniversite sayfaları (TU Dortmund, TU Clausthal, Uni Marburg/Heidelberg/Jena,
 * TUM) + §16b AufenthG dil-hazırlık süre kuralı + kendi topluluk havuzumuzdaki (Telegram +
 * DeutschStudent) gerçek Türk öğrenci soruları.
 * Slug İngilizce (kural). FK-safe (CI fresh-DB seed'siz → user/category yoksa null'a düşer).
 */
return new class extends Migration
{
    public function up(): void
    {
        $slug = 'germany-conditional-admission-bedingte-zulassung-guide';

        $body = <<<'MD'
Almanya'da bir bölüme akademik olarak uygunsun ama **Almanca (veya İngilizce) dil belgen henüz hazır değil** mi? İşte tam burada **şartlı kabul (bedingte Zulassung)** devreye girer. Bu rehber, şartlı kabulü **hem Bachelor hem Master** için A'dan Z'ye anlatıyor: nasıl alınır, dil şartını ne kadar sürede tamamlaman gerekir, **süre uzatılır mı**, ve **aynı anda başka üniversitelere de başvurabilir misin** — kendi topluluğumuzun gerçek sorularıyla.

> ⚠️ **Önemli:** Şartlı kabul kuralları üniversiteden üniversiteye ve konsolosluğa göre değişir. Her zaman başvuracağın **üniversitenin uluslararası ofisi** ve **Alman temsilciliği** ile güncel şartları teyit et. Bu yazı bilgilendirme amaçlıdır.

## Şartlı kabul (bedingte Zulassung) nedir?

Şartlı kabul, üniversitenin sana şu mesajı verdiği kabuldür: **"Akademik olarak uygunsun, ama önce bir koşulu (genellikle dil yeterliliğini) tamamlaman gerekiyor."** Yani not ortalaman, diploman ve bölüm uygunluğun yeterli; eksik olan tek şey resmi dil belgesi (çoğunlukla **C1 Almanca**).

Üniversite sana bir **şartlı kabul mektubu (bedingter Zulassungsbescheid)** verir; sen dil şartını tamamlayıp belgeleyince bu kabul **kesin kayda (Immatrikulation)** dönüşür.

## Kimler şartlı kabul alır?

- Lise mezunları (Bachelor için — denklik/Studienkolleg durumuna göre)
- Lisans mezunları veya **son dönem** öğrencileri (Master için)
- Akademik şartları karşılayan ama **dil belgesi eksik** adaylar

## Bachelor için şartlı kabul

Bachelor programları genellikle **C1 seviyesinde Almanca** ister (DSH-2, TestDaF 4×4, Telc C1 Hochschule veya Goethe C1/C2). Akademik olarak uygunsan ama bu belgen yoksa:

- Üniversite **şartlı kabul** verir; Almanya'da bir dil kursuna katılıp C1'i tamamlarsın.
- **Studienkolleg ilişkisi:** Türk lise diploman doğrudan denk sayılmıyorsa, üniversite kabulü **Studienkolleg + Feststellungsprüfung** koşuluna da bağlayabilir. Bu durumda "şart" hem dil hem Studienkolleg olur. (Lisans bitirmiş veya bazı diploma türlerinde Studienkolleg gerekmeyebilir — üniversiteye sor.)
- C1'i (ve gerekiyorsa Studienkolleg'i) tamamlayınca bölüme kesin kayıt yaparsın.

## Master için şartlı kabul

Master'da şartlı kabul iki tipik durumda çıkar:

1. **Dil belgesi eksik:** Almanca programlarda genellikle **B2 veya C1** istenir (programa göre değişir). Belgen hazır değilse üniversite şartlı kabul verip dil şartını **en geç ikinci dönem sonuna kadar** tamamlamanı isteyebilir (ör. TUM bu modeli uygular).
2. **Lisans henüz bitmemiş:** Son dönem öğrencisiysen, üniversite "lisans diplomanı şu tarihe kadar getir" koşuluyla şartlı kabul verebilir.

> 💡 **İngilizce master notu:** Programın tamamen İngilizceyse Almanca **gerekmez**; bu durumda "dil şartı" İngilizce belgesidir (IELTS/TOEFL) ve şartlı kabul İngilizce belgesinin tamamlanmasına bağlı olabilir. Lisansın İngilizceyse bazı üniversiteler ayrı belge istemeyebilir — yine de teyit et.

## Dil şartı: hangi sınavlar geçerli?

Almanca programlar için kabul edilen başlıca C1 belgeleri:

- **DSH-2** (Deutsche Sprachprüfung für den Hochschulzugang — sadece Almanya'daki üniversitelerde)
- **TestDaF** (genelde her bölümden 4 — "TDN 4×4")
- **Telc C1 Hochschule**
- **Goethe-Zertifikat C1 / C2**

Hangi belgenin ve hangi seviyenin kabul edildiği **programa göre** değişir; bölümün sayfasından doğrula.

## Süre ve C1 uzatması — 1 yılda tamamlanamazsa ne olur?

Bu en kritik konulardan biri. Dil hazırlık süresi yasal olarak sınırlıdır:

- **§ 16b Abs. 5 AufenthG** uyarınca, dil hazırlık süresi **kural olarak bir yıldır ve iki yılı geçmemelidir.**
- Yani **bir yıl içinde C1'i tamamlayamazsan, süre ikinci yıl için uzatılabilir.** İlk yılında C1'e ulaşamayan öğrencilerin hazırlığı, ikinci yıla (toplam ~2 yıla kadar) uzatılır.
- Pratikte: DSH hazırlık dönemlerinin **3. ve 4. döneme** uzaması için ek belge (ilerleme kanıtı) istenir; **5. döneme** kayıt artık mümkün değildir. Yani tampon var ama sınırsız değil.
- Zamanlama örneği: Başvuruda **A2** seviyesindeysen, C1'e ulaşman genelde en az ~6 ay sürer; bu yüzden kabulün çoğu zaman **bir sonraki dönem** için düzenlenir.

**Özet:** Bir yılda C1 olmazsa panik yok — süre ikinci yıl için uzatılır (yasal üst sınır ~2 yıl). Ama bu hakkı sınırsız sanma; düzenli ilerleme göstermen ve dönem uzatmalarını belgelemen gerekir.

## Birden fazla üniversiteye başvurmak ve geçiş yapmak

Çok merak edilen ve **çok önemli** bir nokta: **Şartlı kabul seni tek bir üniversiteye bağlamaz.**

- **Aynı anda birden fazla üniversiteye başvurabilirsin.** Bazı üniversiteler hiç Almanca olmadan da şartlı kabul verirken, bazıları en az B1 ister — bu yüzden birden çok başvuru mantıklıdır.
- Şartlı kabul **bağlayıcı bir taahhüt değildir.** Sadece **kesin kayıt (Immatrikulation)** yaptığın **tek** üniversiteye bağlanırsın.
- Yani bir üniversiteden (ör. FU Berlin) şartlı kabul alıp Almanya'da dilini C1'e getirdikten sonra, **başka bir üniversiteye başvurup oraya geçiş yapabilirsin.** Dil şartını sağladığın belgeyle yeni üniversiteye başvurman önünde engel yoktur.
- Dikkat: Üniversitelerin uygulaması farklıdır. Bazı şehirler/üniversiteler (zaman zaman bazı Berlin üniversiteleri gibi) **şartlı kabul vermeyebilir** — "önce dili bitir, sonra başvur" der. Bu yüzden hedef listende şartlı kabul veren üniversiteleri ayrı işaretle.

## Vize ve finansman

- Şartlı kabul, bir **öğrenci adayı / dil kursu vizesi** (§ 16b kapsamında) almanı destekleyebilir. Vize alt-kodları (ör. konsolosluğa göre değişen tipler) temsilciliğe özeldir — başvuracağın konsolosluğa sor.
- **Sperrkonto (bloke hesap):** Vize için genelde **bir yıllık** finansman kanıtı gerekir (2026: yıllık 11.904 €). Bloke hesabı açarken gidiş sebebini üniversitenin/danışmanın yönlendirmesine göre seç (master / dil hazırlığı).
- Sıfır Almanca ile dahi şartlı kabul + dil kursu üzerinden öğrenci vizesi alan çok sayıda öğrenci var; ama bu, üniversitenin şartlı kabul vermesine ve konsolosluğun ikna olmasına bağlıdır.

## Kesin kayıt (Immatrikulation)

Dil şartını (ve varsa Studienkolleg/ön koşulları) tamamlayıp belgeleyince:

1. Belgeyi üniversitenin uluslararası ofisine/öğrenci işlerine sunarsın.
2. Şartlı kabul **kesin kayda (Immatrikulation)** dönüşür.
3. Bölümüne (Bachelor/Master) resmen başlarsın.

## Başvuru takvimi (genel)

- **Kış dönemi:** genelde **15 Temmuz**'a kadar (bazı üniversiteler 15 Mayıs/erken)
- **Yaz dönemi:** genelde **15 Ocak**'a kadar
- Bazı üniversiteler/özel programlar yıl boyu başvuru alır. Her programın kendi son tarihini doğrula.

## Topluluğumuzdan en çok sorulanlar (gerçek sorular)

Aşağıdaki sorular, Telegram ve forum topluluğumuzda Türk öğrencilerin **gerçekten sorduğu** sorulardan derlendi.

### "Sıfır Almanca ile şartlı kabul alıp dil okuluna gitmek için öğrenci vizesi alabilir miyim?"

Evet, mümkün — çok sayıda öğrenci sıfır/başlangıç Almancayla şartlı kabul + dil kursu üzerinden vize aldı. Şartı: bir üniversitenin sana şartlı kabul vermesi ve konsolosluğun planını (dil + finansman) ikna edici bulması. Bloke hesap (1 yıl) ve net bir dil planı şart.

### "Master için şartlı kabul aldım, şu an B2'deyim. C1'e ne kadar sürede ulaşmam gerekir?"

Genel kural: dil hazırlığı **bir yıl**, en fazla **iki yıl**. Üniversiten çoğu zaman dil şartını **ikinci dönem sonuna kadar** tamamlamanı ister. B2'den C1'e geçiş kişiye göre birkaç ay sürer; sınav takvimini (Telc/TestDaF/DSH) buna göre planla.

### "Bir üniversiteden şartlı kabul alıp C1 yapınca başka bir üniversiteye geçebilir miyim?"

Evet. Şartlı kabul seni bağlamaz; sadece **kesin kayıt** yaptığın üniversiteye bağlanırsın. C1 belgeni aldıktan sonra başka bir üniversiteye başvurup oraya geçiş yapabilirsin. Hatta birden fazla üniversiteye **aynı anda** başvurman, şartlı kabul şansını artırır.

### "1 yılda C1'e ulaşamazsam ne olur?"

Süre **ikinci yıl için uzatılır** (yasal üst sınır ~2 yıl, § 16b AufenthG). İlerleme göstermen ve dönem uzatmalarını belgelemen gerekir; DSH hazırlığında 3. ve 4. dönem ek belgeyle uzar, 5. dönem mümkün değildir. Yani tampon var ama düzenli çalışman şart.

### "Berlin üniversiteleri şartlı kabul vermiyor mu?"

Bazı üniversiteler (zaman zaman bazı Berlin üniversiteleri dahil) şartlı kabul **vermez** — "önce dili bitir, C1 ile başvur" der. Bu üniversiteye özeldir; hedef listende şartlı kabul veren üniversiteleri ayrıca araştır.

### "Goethe B2 ile şartlı kabul olmadan direkt master'a başlayabilir miyim?"

Programın istediği seviyeye bağlı. Bazı master programları **B2** ile direkt kabul verirken, çoğu **C1** ister. Programın dil şartını kontrol et; B2 yeterliyse şartlı kabule gerek kalmadan direkt başlayabilirsin.

### "Bloke hesap açarken gidiş sebebini master mı, dil okulu mu seçmeliyim?"

Şartlı kabulle gidip önce dil yapacaksan, çoğu durumda **dil hazırlığı/öğrenci adayı** olarak ilerlersin; ama bu konsolosluğun ve bankanın (Expatrio vb.) yönlendirmesine göre değişir. Emin değilsen üniversitenin uluslararası ofisine ve konsolosluğa sor — yanlış seçim vize sürecini yavaşlatabilir.

## Sık yapılan hatalar

- Sadece **tek** üniversiteye şartlı kabul için başvurmak (birden fazla başvur — şansını artırır).
- Şartlı kabulü **direkt kabul** sanıp dil şartını ertelemek.
- Dil hazırlığının **süre sınırını** (1-2 yıl) göz ardı etmek.
- Berlin gibi şartlı kabul vermeyen üniversitelere şartlı kabul beklentisiyle yüklenmek.
- İngilizce master'da gereksiz yere Almanca belgesi peşinde koşmak (ya da tersi).

## Sonuç

Şartlı kabul, **dil belgen henüz hazır değilken** Almanya yolculuğuna başlamanın en yaygın ve geçerli yoludur — hem Bachelor hem Master için. Aklında tut: **bir yılda C1 olmazsan süre ikinci yıla uzar** (yasal üst sınır ~2 yıl) ve **şartlı kabul seni tek üniversiteye bağlamaz** — birden fazla başvurabilir, şartı sağlayınca başka üniversiteye geçebilirsin. Her zaman üniversitenin uluslararası ofisi ve Alman temsilciliğiyle güncel şartları teyit et.

---

**Resmi kaynaklar:** § 16b AufenthG (dil hazırlık süresi), üniversitelerin uluslararası ofisleri (ör. TU Dortmund, TU Clausthal, Uni Marburg/Heidelberg/Jena, TUM), DAAD ve uni-assist.
MD;

        $excerpt = 'Almanya\'da şartlı kabul (bedingte Zulassung) nedir, Bachelor ve Master için nasıl alınır, dil şartını ne kadar sürede tamamlarsın, 1 yılda C1 olmazsa süre nasıl uzar ve sonra başka üniversiteye nasıl geçersin — 2026 rehberi.';

        $html = Str::markdown($body, ['html_input' => 'allow', 'allow_unsafe_links' => false]);

        // FK-safe: CI fresh test DB seed'siz olabilir → user/category yoksa null'a düş.
        $userId = DB::table('users')->where('id', 4)->exists()
            ? 4 // Elif G. — başvuru / uni-assist personası
            : DB::table('users')->orderBy('id')->value('id');
        $categoryId = DB::table('categories')->where('id', 8)->exists()
            ? 8 // Başvuru
            : DB::table('categories')->where('slug', 'basvuru')->value('id');

        $payload = [
            'locale'           => 'tr',
            'user_id'          => $userId,
            'category_id'      => $categoryId,
            'title'            => "Almanya'da Şartlı Kabul (Bedingte Zulassung): Bachelor ve Master İçin 2026 Tam Rehber",
            'excerpt'          => Str::limit($excerpt, 250, '…'), // excerpt kolonu VARCHAR(255)
            'content_md'       => $body,
            'content_html'     => $html,
            'meta_title'       => "Almanya Şartlı Kabul (Bedingte Zulassung) 2026 — Bachelor & Master Rehberi",
            'meta_description' => Str::limit($excerpt, 158, '…'),
            'reading_minutes'  => max(1, (int) round(str_word_count(strip_tags($html)) / 200)),
            'is_published'     => true,
            'published_at'     => now(),
        ];

        $existing = Post::where('slug', $slug)->first();
        if ($existing) {
            $existing->update($payload);
        } else {
            Post::create($payload + [
                'slug'                 => $slug,
                'translation_group_id' => (string) Str::uuid(),
            ]);
        }
    }

    public function down(): void
    {
        Post::where('slug', 'germany-conditional-admission-bedingte-zulassung-guide')->delete();
    }
};
