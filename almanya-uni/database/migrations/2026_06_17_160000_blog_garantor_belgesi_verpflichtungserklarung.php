<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR): Almanya'da Garantör Belgesi (Verpflichtungserklärung) — 2026 tam rehber.
 *
 * Kaynak: resmi (Auswärtiges Amt, Handbook Germany, Make-it-in-Germany, §§66-68/§2 AufenthG)
 * + kendi topluluk havuzumuzdaki (Telegram + DeutschStudent) GERÇEK Türk öğrenci soruları.
 * Posts prod'da DB'de durur, seeder prod'da koşmaz → içerik buradan idempotent gelir.
 * slug ile updateOrCreate: tekrar koşmak çoğaltmaz, elle düzenleme yapılmadıysa tazeler.
 */
return new class extends Migration
{
    public function up(): void
    {
        $slug = 'germany-guarantor-declaration-verpflichtungserklarung-guide';

        $body = <<<'MD'
Almanya öğrenci vizesinde en kritik adımlardan biri **finansmanın kanıtlanması** (Finanzierungsnachweis). Bunun iki resmi yolu var: ya bir **bloke hesap (Sperrkonto)** açarsın, ya da Almanya'da yaşayan bir yakının senin için **garantör belgesi (Verpflichtungserklärung)** verir. Bu rehber, garantör belgesini A'dan Z'ye — şartlar, gelir, belgeler, maliyet, hukuki sorumluluk ve topluluğumuzda en çok sorulan gerçek sorularla — anlatıyor.

> ⚠️ **Önemli:** Tüm rakamlar ve kurallar yıllık güncellenir ve şehre göre değişir. Başvurudan önce ilgili **Yabancılar Dairesi (Ausländerbehörde)** ve başvuru yapacağın **Alman Konsolosluğu/Büyükelçiliği** ile mutlaka teyit et. Bu yazı bilgilendirme amaçlıdır, hukuki danışmanlık değildir.

## Garantör belgesi (Verpflichtungserklärung) nedir?

Verpflichtungserklärung, Almanya'da yasal olarak ikamet eden bir kişinin (garantörün), bir yabancının Almanya'daki **tüm masraflarını üstlendiğini** resmen taahhüt ettiği belgedir. Yasal dayanağı Alman İkamet Yasası'nın **§§ 66-68 AufenthG** maddeleridir.

Türkçe'de "garantör belgesi", "taahhütname" veya "taahhüt beyanı" olarak geçer. Vize başvurusunda, senin Almanya'da geçimini sağlayacak paranın garanti altında olduğunu gösteren **finansman kanıtı** işlevi görür.

## Öğrenci için neden önemli: finansman kanıtının iki yolu

Almanya, öğrenci vizesi verirken **§ 2 Abs. 3 AufenthG** uyarınca "güvence altına alınmış geçim" (gesicherter Lebensunterhalt) ister. 2026 için aylık ihtiyaç **992 €** olarak hesaplanır. Bunu iki şekilde kanıtlayabilirsin:

| | **Sperrkonto (Bloke Hesap)** | **Verpflichtungserklärung (Garantör)** |
|---|---|---|
| Nasıl çalışır | Yıllık tutarı bloke hesaba yatırırsın | Almanya'daki bir yakının taahhüt verir |
| 2026 tutarı | **11.904 €/yıl** (aylık 992 € serbest) | Garantörün gelirine bağlı (yatırılan para yok) |
| Kime bağımlı | Kimseye — tamamen sende | Almanya'da gelir sahibi bir garantöre |
| Hız | Hızlı, standart | Garantörün randevusuna + dairenin onayına bağlı |
| Maliyet | Banka açılış + aylık ücret | 29 € (yetişkin) |
| Risk | Düşük (parayı sen kontrol edersin) | Garantöre ciddi hukuki yük (§68) |

**Özet:** Paran varsa Sperrkonto daha basit ve garantili. Yeterli paran yoksa ama Almanya'da güvenilir ve **yüksek gelirli** bir yakının varsa, garantör belgesi geçerli bir alternatiftir.

## Kim garantör olabilir? Şartlar

Garantör olacak kişinin şunlara sahip olması gerekir:

- **Almanya'da yasal ikamet:** Alman vatandaşı, AB vatandaşı veya geçerli bir oturum izni (Aufenthaltstitel).
- **Düzenli ve haczedilebilir net gelir:** Maaşlı çalışan, memur veya gelirini belgeleyebilen serbest meslek sahibi.
- **Bonität (kredi/ödeme güvenilirliği):** Bazı daireler SCHUFA benzeri bir güvenilirlik kontrolü yapar; icra/borç kaydı sorun çıkarabilir.
- **Sosyal yardım almıyor olmak:** Bürgergeld (eski Hartz IV) gibi sosyal yardım alan biri garantör **olamaz**.

Türkiye'de yaşayan bir akraba garantör olamaz — garantörün **Almanya'da** ikamet etmesi şarttır.

## Gelir şartı tam olarak ne kadar?

En çok karıştırılan konu bu. Tek bir sabit rakam yoktur; mantık şudur:

- Eğitim/öğrenim amaçlı bir Verpflichtungserklärung için garantörün **aylık net gelirinin genelde 2.700 € ve üzeri** olması beklenir (Handbook Germany). Bazı şehirler 1.800-2.000 € aralığını da kabul edebilir — **şehre göre değişir.**
- Asıl kriter: garantörün kendi **haciz sınırının (Pfändungsfreigrenze)** ve mevcut yükümlülüklerinin (kira, kendi ailesinin geçimi) ardından, senin **aylık 992 €** ihtiyacını karşılayacak kadar **artan, haczedilebilir geliri kalması.**
- Garantörün bakmakla yükümlü olduğu kişi sayısı arttıkça gerekli gelir de yükselir.
- Ziyaret amaçlı (turist) taahhütlerde haczedilebilir asgari tutar kişi başı **281,50 € (yetişkin)**, **140,75 € (reşit olmayan)** olarak aranır; öğrenim taahhütlerinde eşik daha yüksektir.

> 💡 Pratik kural: Garantörün net geliri ne kadar yüksekse, belge o kadar sorunsuz kabul edilir. Sınırda bir gelirle başvurmak ret riskini artırır.

## Gerekli belgeler

Garantör, kendi şehrindeki Yabancılar Dairesi'ne genelde şunlarla başvurur:

- Doldurulmuş **"Angaben zur Verpflichtungserklärung"** formu (her davet edilen kişi için ayrı).
- **Son 3 maaş bordrosu** (Gehaltsabrechnung). Her ay bordro almıyorsa: son bordro + son 3 ayın **banka hesap ekstresi**.
- **Geçerli kimlik / pasaport** ve (Alman değilse) **oturum izni**.
- **Meldebescheinigung** (adres kaydı) — bazı dairelerde.
- Senin (davet edilenin) **pasaport bilgilerin** ve başvuru amacın (öğrenci vizesi, üniversite kabulü vb.).
- Serbest meslek sahipleri için: vergi danışmanı yazısı, gelir vergisi beyanı (Steuerbescheid).

Belge listesi şehre göre değişir; garantör, randevu almadan önce dairenin sitesinden güncel listeyi kontrol etmelidir.

## Nereden ve nasıl alınır? Adım adım

1. **Garantör, kendi ikamet ettiği şehrin Ausländerbehörde'sine başvurur** (senin değil, garantörün şehri).
2. Çoğu şehirde **randevu (Termin)** gerekir. Bazı büyük şehirlerde online ya da posta/e-posta ile başvuru mümkündür.
3. Garantör belgeleri sunar, formu **dairede, görevli önünde imzalar** (imza orada atılır).
4. Daire geliri ve Bonität'ı kontrol eder, uygunsa belgeyi düzenler.
5. Orijinal belge garantöre verilir; garantör bunu sana (genelde kargoyla) gönderir.
6. Sen bu **orijinal Verpflichtungserklärung'u** vize başvurunda finansman kanıtı olarak sunarsın.

Şehirlerin yoğunluğu çok farklıdır: bazı dairelerde randevu birkaç güne çıkarken, yoğun şehirlerde haftalar sürebilir. **Randevuyu erkenden almak** en kritik ipucudur.

## Maliyet ve geçerlilik süresi

- **Ücret:** 29 € (yetişkin), 14,50 € (18 yaş altı).
- **Geçerlilik:** Belge, vize başvurusunda **6 ay** boyunca finansman kanıtı olarak kullanılabilir. Yani belgenin düzenlenmesi ile vizenin verilmesi arasında 6 aydan fazla olmamalı. Süre dolarsa yeni belge gerekir.

## Garantörün hukuki sorumluluğu — hafife alınmamalı

Verpflichtungserklärung imzalamak **ciddi ve bağlayıcı** bir taahhüttür. Garantör, **§ 68 AufenthG** uyarınca davet edilen kişinin:

- **geçim masraflarını** (barınma, yemek, geçim),
- **hastalık ve bakım masraflarını** (sağlık sigortasının karşılamadığı kısımlar dahil),
- ve gerekirse **sınır dışı (Abschiebung) masraflarını**

karşılamayı taahhüt eder. Bu sorumluluk, kişinin Almanya'daki **tüm kalış süresi** boyunca geçerlidir ve oturum amacı değişse bile (örn. öğrencilikten başka statüye geçiş) **5 yıla kadar** devam edebilir. Yani garantör, "sadece formalite" diye düşünmemeli — devlet, ödediği masrafları garantörden **yasal olarak geri talep edebilir.**

## Topluluğumuzdan en çok sorulanlar (gerçek sorular)

Aşağıdaki sorular, Telegram ve forum topluluğumuzda Türk öğrencilerin **gerçekten sorduğu** sorulardan derlendi.

### "Garantör belgem var. Yine de hesabımda para / Sperrkonto göstermem gerekir mi?"

Bu, **en sık** sorulan soru. Kural olarak: Verpflichtungserklärung **finansman kanıtının kendisidir** — kabul edilirse ayrıca Sperrkonto açman gerekmez. Ancak öğrenci (ulusal) vizesinde konsolosluk, garantörün gelirini **yetersiz** görürse ek kanıt veya Sperrkonto isteyebilir. Garantörün net geliri yüksekse tek başına yeterli olur; sınırdaysa, ek olarak bir miktar para göstermek başvurunu güçlendirir.

### "Almanya'da her yıl ~11.904 € göstermek zorunda mıyım, garantör varsa?"

Verpflichtungserklärung **tüm kalış süresini** kapsadığı için, geçerli bir garantör belgesiyle her yıl ayrıca yeni bir Sperrkonto açman teorik olarak gerekmez. Ancak **oturum izni uzatmalarında** Ausländerbehörde güncel finansman durumunu yeniden sorabilir; garantörün durumu değiştiyse yeni kanıt istenebilir.

### "Eşlerden biri için garantör belgesi, diğeri için Sperrkonto olur mu?"

Evet, mümkündür — her başvuran için **ayrı finansman kanıtı** sunulabilir. Biri garantör belgesiyle, diğeri bloke hesapla başvurabilir. Tek bir garantör iki kişiyi birden üstlenecekse, gereken gelir eşiği o oranda yükselir.

### "Garantörün yanında kalacağım. Yine de yurt/kira göstermem gerekir mi?"

Konaklama, finansmandan **ayrı** bir kanıttır. Garantörün evinde kalacaksan, onun **kira sözleşmesi/adres kaydı** ve sana yetecek alanın olduğuna dair beyanı genelde işe yarar; ayrıca yurt tutman **şart değildir.** Yine de konsolosluk, nerede kalacağının **net** olmasını ister — belirsiz bırakma.

### "Belge vize randevuma yetişmedi. Randevuyu erteleyeyim mi?"

Belge **6 ay geçerli** olduğundan, hazır olunca kullanman en güvenlisidir. Eksik finansman kanıtıyla randevuya gitmek **ret riski** taşır. Ancak randevu bulmak zor olduğundan, ilgili konsolosluğa durumu yazıp **eksik belgeyi sonradan tamamlama** imkânı olup olmadığını sormak mantıklıdır.

### "Dil kursu / Studienkolleg vizesi için garantör kabul edilir mi?"

Dil kursu ve Studienkolleg vizelerinde de finansman kanıtı zorunludur ve Verpflichtungserklärung **kabul edilebilir.** Yine de bazı konsolosluklar dil vizesinde **Sperrkonto'yu tercih eder**; başvuru yapacağın temsilciliğin güncel uygulamasını teyit et.

### "Şehre göre belge ne kadar sürede çıkıyor (ör. Stuttgart yoğun mu)?"

Süre tamamen ilgili **Ausländerbehörde'nin yoğunluğuna** bağlıdır ve şehirden şehre çok değişir. Büyük/yoğun şehirlerde randevu ve işlem haftalar alabilir. Tek çözüm: garantörün **mümkün olan en erken** randevuyu almasıdır.

### "Garantör belgesiyle gerçekten vize çıkıyor mu?"

Evet — Verpflichtungserklärung **yasal ve yaygın kabul gören** bir finansman yoludur. Retlerin çoğu garantörün **gelir yetersizliğinden** veya **eksik/çelişkili belgeden** kaynaklanır, belgenin türünden değil.

## Sperrkonto mu, garantör belgesi mi? Hangisi sana uygun

- **Paran varsa → Sperrkonto.** Kimseye bağımlı değilsin, süreç standart ve öngörülebilir. Çoğu öğrenci için en güvenli yol budur.
- **Paran yok ama Almanya'da yüksek gelirli, güvenilir bir yakının varsa → Verpflichtungserklärung.** Maliyeti düşük (29 €), ama garantöre ciddi hukuki yük bindirir.
- **Sınırda bir durum varsa → ikisini birleştir.** Garantör belgesi + bir miktar bloke para, başvurunu en güçlü hale getirir.

## Sık yapılan hatalar

- Garantörün gelirini **net** yerine **brüt** sanıp eşiği yanlış hesaplamak.
- Belgeyi aldıktan sonra **6 aylık geçerlilik penceresini** kaçırmak.
- Garantörün şehri yerine **öğrencinin** şehrindeki daireye başvurmaya çalışmak (yanlış — garantörün şehri esastır).
- Konaklama kanıtını finansman kanıtıyla **karıştırmak** — ikisi ayrıdır.
- Garantöre §68 sorumluluğunu **anlatmadan** imza attırmak.

## Sonuç

Garantör belgesi (Verpflichtungserklärung), Sperrkonto'ya geçerli ve yasal bir alternatiftir — ama garantöre **uzun vadeli, ciddi bir mali sorumluluk** yükler ve kabulü garantörün gelirine sıkı sıkıya bağlıdır. Paran varsa Sperrkonto genelde daha sorunsuzdur; garantör yolunu seçeceksen, garantörün gelirinin eşiği **rahatça** aştığından ve tüm belgelerin eksiksiz olduğundan emin ol. Her durumda, başvurudan önce ilgili **Ausländerbehörde** ve **Alman temsilciliğiyle** güncel şartları teyit et.

---

**Resmi kaynaklar:** Auswärtiges Amt (Sperrkonto & finansman), Handbook Germany (Verpflichtungserklärung), Make-it-in-Germany, §§ 66-68 ve § 2 Abs. 3 AufenthG.
MD;

        $excerpt = 'Almanya öğrenci vizesinde garantör belgesi (Verpflichtungserklärung) nedir, kim verebilir, gelir şartı ne kadar, hangi belgeler gerekir, maliyeti ve garantörün hukuki sorumluluğu — 2026 güncel rehber ve topluluğumuzdan gerçek sorular.';

        $html = Str::markdown($body, ['html_input' => 'allow', 'allow_unsafe_links' => false]);

        // FK-safe: CI fresh test DB seed'siz olabilir → user 6 / category 6 yoksa
        // null'a (ya da uygun fallback'e) düş ki migrate --force FK ihlaliyle patlamasın
        // (deploy'u gate'lemesin). Kolonlar nullable + nullOnDelete.
        $userId = DB::table('users')->where('id', 6)->exists()
            ? 6
            : DB::table('users')->orderBy('id')->value('id');
        $categoryId = DB::table('categories')->where('id', 6)->exists()
            ? 6
            : DB::table('categories')->where('slug', 'vize')->value('id');

        $payload = [
            'locale'           => 'tr',
            'user_id'          => $userId,     // Hakan Kutlu (6) — vize uzmanı persona
            'category_id'      => $categoryId, // Vize (6)
            'title'            => "Almanya'da Garantör Belgesi (Verpflichtungserklärung): 2026 Tam Rehber",
            'excerpt'          => $excerpt,
            'content_md'       => $body,
            'content_html'     => $html,
            'meta_title'       => "Almanya Garantör Belgesi (Verpflichtungserklärung) 2026 — Tam Rehber",
            'meta_description' => Str::limit($excerpt, 158, '…'),
            'reading_minutes'  => max(1, (int) round(str_word_count(strip_tags($html)) / 200)),
            'is_published'     => true,
            'published_at'     => now(),
        ];

        $existing = Post::where('slug', $slug)->first();
        if ($existing) {
            // translation_group_id'yi koru; yeniden koşmada içeriği tazele.
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
        Post::where('slug', 'germany-guarantor-declaration-verpflichtungserklarung-guide')->delete();
    }
};
