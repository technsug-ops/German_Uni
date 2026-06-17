<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * TR blog: Almanya'ya yeni gelen öğrenci için bürokrasi rehberi — ilk resmi işlemler
 * + önemli kamu kurumları + acil numaralar. Kullanıcı verisi + genel bilgi. FK-safe.
 */
return new class extends Migration
{
    public function up(): void
    {
        $slug = 'germany-new-arrival-bureaucracy-guide-first-steps-institutions';

        $body = <<<'MD'
Almanya'ya öğrenci olarak indin — tebrikler! Şimdi sırada birkaç **resmi işlem** ve tanıman gereken **kamu kurumları** var. Bu rehber, ilk haftalarda yapman gerekenleri ve hangi kurumun ne işe yaradığını sade bir şekilde anlatıyor. (Bazı adımlar aileyle gelenler/uzun kalanlar için; öğrenci olarak en kritikleri işaretledim.)

> ⚠️ Süreçler eyalet ve şehre göre küçük farklar gösterir. Randevu ve belge listelerini kendi şehrinin resmi sayfasından teyit et.

## İlk yapılması gereken resmi işlemler

### 1. İkamet kaydı — Anmeldung ⭐ (en kritik)
3 aydan uzun kalacak herkesin, taşındıktan sonra **en geç 2 hafta içinde** adresini **Bürgeramt / Einwohnermeldeamt**'a bildirip **Anmeldung** belgesini alması gerekir. Bu belge neredeyse her şeyin (banka, sigorta, oturum, telefon) ön koşuludur. Şehir içinde taşınsan bile yeni adresi bildir. Adım adım için: [Anmeldung rehberi](/tr/blog/anmeldung-guide-2026-your-first-week-in-germany-city-registration-steps).

### 2. Vergi numarası — Steuer-ID
Anmeldung'dan sonra **otomatik olarak postayla** adresine gelir; ayrıca başvuru yapmana gerek yoktur. Çalışırsan (Werkstudent/mini-job) işveren bunu ister.

### 3. Sağlık sigortası ⭐
Almanya'da sağlık sigortası **zorunludur** ve üniversite kaydı için şarttır. Öğrenciler genelde **yasal sigortaya (TK, AOK, Barmer…)** kaydolur; bazı durumlarda özel sigorta. Vize için kullandığın geçici/seyahat sigortasından (Expatrio/DR-WALTER) ülkeye gelince yasal sigortaya geçersin.

### 4. Oturum izni — Aufenthaltstitel ⭐
Vizenle geldikten sonra, oturum kartı için bağlı olduğun **Ausländerbehörde (Yabancılar Şubesi)**'nden randevu alman gerekir. Randevular geç çıkabildiği için **vize süren bitmeden 1-2 ay önce** randevu al.

### 5. Banka hesabı
Kira, sigorta ve maaş için bir Alman banka hesabı (Girokonto) aç. Öğrenciye ücretsiz hesap veren bankalar/neobankalar var. Sperrkonto sağlayıcın (Expatrio/Coracle vb.) bazen cari hesaba da dönüştürülebilir.

### 6. Telefon hattı / internet
Sana uygun bir operatör ve tarife seç (prepaid ile başlamak pratik). Anmeldung bazı sözleşmeler için istenebilir.

### 7. Entegrasyon / dil kursu
Yabancılar şubesi duruma göre zorunlu tutabilir; tutmasa bile dil kursu **senin yararına**. Dil seviyen üniversite ve günlük hayat için kritik.

### Aileyle gelenler / uzun kalanlar için
- **Okul kaydı:** Çocukların için ikametten sonra okul kaydı (Schulamt).
- **Kindergeld (çocuk parası):** 18 yaş altı çocuklar için Familienkasse'ye başvuru (sonuç 3-6 ay; geçmişe dönük toplu ödenir).
- **Bürgergeld / kira yardımı:** Düşük gelir/işsizlik durumunda Jobcenter'e başvuru (uygunluk şarta bağlı).

## Almanya'daki önemli kamu kurumları

| Kurum | Ne işe yarar |
|---|---|
| **Bürgeramt / Einwohnermeldeamt** | İkamet kaydı (Anmeldung) ve kayıt silme (Abmeldung) |
| **Ausländerbehörde** | Oturum izni / vize uzatma (öğrenci için kritik) |
| **Finanzamt** | Vergi numarası, vergi beyannamesi, vergi sınıfı |
| **Standesamt** | Doğum, evlilik, resmi nüfus belgeleri |
| **Führerscheinstelle** | Ehliyet işlemleri / Türk ehliyetini çevirme ([detay](/tr/blog/turkish-driving-license-in-germany-conversion-guide)) |
| **Zulassungsstelle** | Araç kaydı/plaka |
| **Familienkasse** | Kindergeld (çocuk parası) başvurusu |
| **Wohngeldstelle** | Konut yardımı (Wohngeld) — düşük gelirli için |
| **Schulamt** | Çocukların Alman eğitim sistemine kaydı |
| **Agentur für Arbeit (Arbeitsamt)** | İş arama, işsizlik kaydı, danışmanlık |
| **Zollamt (Gümrük)** | Çalışma hakları ihlali (ödenmeyen mesai, sözleşmesiz çalıştırma) şikayeti |
| **Arbeitsgericht (İş Mahkemesi)** | Haksız işten çıkarma / ödenmeyen maaş — ücretsiz danışma merkezi var |

## Acil durum numaraları

- **110** — Polis
- **112** — İtfaiye ve ambulans (tüm AB'de geçerli)

Bu numaralar **ücretsiz** ve telefonun kilitliyken/kontörsüz bile aranabilir.

## Öğrenci olarak öncelik sıran

1. **Anmeldung** (2 hafta içinde) → 2. **Sağlık sigortası** → 3. **Banka hesabı** → 4. **Üniversite kaydı (Immatrikulation)** → 5. **Oturum izni randevusu** (vize bitmeden). Telefon hattı ve dil kursunu aralara serpiştir.

## Sık yapılan hatalar

- Anmeldung'u **2 haftalık süreyi** kaçıracak kadar ertelemek (randevu da geç çıkabilir).
- Oturum izni randevusunu **son ana** bırakmak (Ausländerbehörde yoğun).
- Sağlık sigortasını üniversite kaydından önce ayarlamamak.
- Adres değişikliğinde yeni Anmeldung yapmayı unutmak.

## Sonuç

İlk haftalarda işin özü: **Anmeldung → sağlık sigortası → banka → üniversite kaydı → oturum izni.** Hangi kurumun ne işe yaradığını bilmek bürokrasiyi çok kolaylaştırır. Randevuları erken al, belge listelerini şehrinin resmi sayfasından teyit et. Finansman ([Sperrkonto](/tr/blog/sperrkonto-for-a-german-visa-what-is-it-how-much-and)) ve ehliyet ([Türk ehliyeti çevirme](/tr/blog/turkish-driving-license-in-germany-conversion-guide)) için ilgili rehberlerimize de bak.

---

**Not:** Bilgiler bilgilendirme amaçlıdır; işlemler eyalet/şehre göre değişebilir — yetkili makamın güncel sayfasından teyit et.
MD;

        $excerpt = 'Almanya\'ya yeni gelen öğrenci için bürokrasi rehberi: ilk resmi işlemler (Anmeldung, vergi no, sağlık sigortası, oturum izni, banka) + önemli kamu kurumları (Bürgeramt, Finanzamt, Ausländerbehörde…) ve acil numaralar (110/112).';

        $html = Str::markdown($body, ['html_input' => 'allow', 'allow_unsafe_links' => false]);

        $userId = DB::table('users')->where('id', 6)->exists() ? 6 : DB::table('users')->orderBy('id')->value('id');
        $categoryId = DB::table('categories')->where('id', 9)->exists() ? 9 : DB::table('categories')->where('slug', 'yasam')->value('id');

        $payload = [
            'locale'           => 'tr',
            'user_id'          => $userId,
            'category_id'      => $categoryId,
            'title'            => 'Almanya\'ya Yeni Gelen Öğrenci İçin Bürokrasi Rehberi: İlk İşlemler ve Önemli Kurumlar',
            'excerpt'          => Str::limit($excerpt, 250, '…'),
            'content_md'       => $body,
            'content_html'     => $html,
            'meta_title'       => 'Almanya\'da İlk İşlemler ve Önemli Kurumlar — Yeni Gelen Öğrenci Rehberi',
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
        Post::where('slug', 'germany-new-arrival-bureaucracy-guide-first-steps-institutions')->delete();
    }
};
