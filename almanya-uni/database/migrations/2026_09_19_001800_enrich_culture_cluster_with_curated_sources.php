<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Str;

/**
 * KÜLTÜR KÜMESİ ZENGİNLEŞTİRME (TR+DE+EN) — kullanıcının ikinci parti küratörlü kaynakları.
 *
 * Kaynaklar: intercultural-success.de (kültür şoklarının ARDINDAKİ mantık: doğrudanlık, iş-özel
 * hayat ayrımı, bireysellik), liveingermany.de (expat şok listesi), easygerman.org bölüm 515
 * "Kulturschock Deutschland" (faks, kişisel alan, sessiz pazar), deutsche-digitale-bibliothek.de
 * (Campus-Report 2019, Çinli öğrencilerin kültür şoku — konunun akademik olarak da çalışıldığının
 * kanıtı), twitterperlen.de (kullanıcı anlatıları).
 *
 * OLGU DÜZELTMESİ: kaynaklardan biri "nakit ödemelerin %51'i" (Bundesbank 2023) diyordu — ESKİMİŞ.
 * Bundesbank "Zahlungsverhalten in Deutschland 2025" (yayım Temmuz 2026): işlem sayısında nakit
 * **%45**, nakitsiz **%55** — nakitsiz ödeme İLK KEZ öne geçti (2023'e göre nakit 6 puan düştü;
 * 6.070 kişiyle Forsa anketi). Kaynak bloglarındaki rakam değil, Bundesbank'ın kendi çalışması
 * esas alındı ve yıl etiketlendi.
 *
 * Üç yazı yamandı: 1) kültür şoku → "şokun ardındaki mantık" bölümü eklendi, 2) yazısız kurallar →
 * nakit paragrafı güncel Bundesbank verisiyle yenilendi, 4) kültürle Almanca → adı konmuş iki
 * ücretsiz kaynak (Easy German, Deutsche Digitale Bibliothek) eklendi; Easy German'ın transkript
 * ve kelime yardımının ÜCRETLİ üyelikte olduğu açıkça yazıldı (yanlış beklenti oluşmasın).
 * Yamalar birebir metin eşleşmesiyle ve idempotent: eşleşme yoksa kayıt olduğu gibi bırakılır.
 */
return new class extends Migration
{
    public function up(): void
    {
        $patches = [];

        // ── 1) KÜLTÜR ŞOKU: "şokun ardındaki mantık" bölümü ───────────────────────────────────
        $patches['culture-shock-in-germany-first-six-months-for-students'] = [
            "## Kriz evresini kısaltan şeyler" => <<<'MD'
## En sık anlatılan şoklar — ve ardındaki mantık

Kültür şoku listelerinde hep aynı başlıklar döner. Faydalı olan kısım listenin kendisi değil, her birinin **neden** öyle olduğu: sebebi bilince davranış kişisel bir hakaret olmaktan çıkıyor.

| Şok | Ardındaki mantık |
|---|---|
| **Doğrudanlık** | Eleştiri kişiye değil davranışa yöneliktir; "yüz kaybı" kavramı bu kültürde bu şekilde işlemez |
| **İş ve özel hayatın ayrılığı** | İş arkadaşlığı otomatik olarak arkadaşlığa dönüşmez — mesai sonrası ayrı bir alandır |
| **Bireysellik** | Partiden erken ayrılmak için grubun onayı gerekmez; "ben gidiyorum" tek başına yeterli ve kaba sayılmaz |
| **Kişisel alan** | Fiziksel mesafe ve temassız iletişim mesafe koymak değil, saygı biçimidir |
| **Kurala bağlılık** | Kasiyerin, kondüktörün veya memurun kapıyı tam saatinde kapatması kişisel katılık değil, sistem güvenilirliğidir |
| **Analog bürokrasi** | Dijital bir ülkede hâlâ faks, ıslak imza ve posta — veri koruma kültürü ve kurumsal süreklilik bunu besliyor |

Bunu sadece yeni gelenler yaşamıyor: Almanca öğrenenler için yapılan popüler *Easy German* podcast'i konuya tam bir bölüm ayırdı (515: "Kulturschock Deutschland" — faks makineleri, kişisel alan ve doğrudanlık üzerine), üniversiteler de konuyu araştırıyor; Heidelberg Üniversitesi'nin Campus-Report yayınında Çinli öğrencilerin kültür şoku ele alınmıştı. Yani hissettiğin şey ne istisnai ne de kişisel.

## Kriz evresini kısaltan şeyler
MD,
        ];

        $patches['culture-shock-in-germany-first-six-months-for-students-de'] = [
            "## Was die Krisenphase verkürzt" => <<<'MD'
## Die meistgenannten Schocks — und die Logik dahinter

In Listen zum Kulturschock tauchen immer dieselben Punkte auf. Nützlich ist nicht die Liste, sondern das **Warum**: Wer den Grund kennt, nimmt das Verhalten nicht mehr persönlich.

| Schock | Die Logik dahinter |
|---|---|
| **Direktheit** | Kritik richtet sich an das Verhalten, nicht an die Person; das Konzept des Gesichtsverlusts funktioniert hier anders |
| **Trennung von Beruf und Privatem** | Kollegialität wird nicht automatisch zu Freundschaft — nach Feierabend beginnt ein eigener Bereich |
| **Individualismus** | Wer früher von einer Feier geht, braucht keine Zustimmung der Gruppe; „ich gehe jetzt" genügt und gilt nicht als unhöflich |
| **Persönlicher Raum** | Körperliche Distanz ist keine Abgrenzung, sondern eine Form von Respekt |
| **Regeltreue** | Wenn Kasse, Schalter oder Zug pünktlich schließen, ist das keine persönliche Härte, sondern Verlässlichkeit des Systems |
| **Analoge Bürokratie** | In einem digitalen Land weiterhin Fax, Unterschrift und Briefpost — Datenschutzkultur und institutionelle Kontinuität halten das am Leben |

Damit stehst du nicht allein: Der bekannte Podcast *Easy German* hat dem Thema eine ganze Folge gewidmet (515: „Kulturschock Deutschland" — unter anderem Faxgeräte, persönlicher Raum und Direktheit), und auch Hochschulen erforschen es; im Campus-Report der Universität Heidelberg ging es um den Kulturschock chinesischer Studierender. Was du erlebst, ist weder außergewöhnlich noch persönlich.

## Was die Krisenphase verkürzt
MD,
        ];

        $patches['culture-shock-in-germany-first-six-months-for-students-en'] = [
            "## What shortens the crisis phase" => <<<'MD'
## The shocks people report most — and the logic behind them

Lists of culture shocks in Germany always circle the same items. The useful part is not the list but the **why**: once you know the reason, the behaviour stops reading as a personal slight.

| Shock | The logic behind it |
|---|---|
| **Directness** | Criticism targets the behaviour, not the person; the concept of losing face works differently here |
| **Work and private life kept apart** | Being colleagues does not automatically become friendship — after hours is a separate domain |
| **Individualism** | Leaving a party early needs no group consensus; "I am going home now" is complete and not rude |
| **Personal space** | Physical distance is a form of respect rather than rejection |
| **Adherence to rules** | A counter, a train door or an office closing exactly on time is system reliability, not personal rigidity |
| **Analogue bureaucracy** | Fax machines, wet signatures and postal mail persist in a digital country — data-protection culture and institutional continuity keep them alive |

You are not alone in noticing: the popular *Easy German* podcast devoted an entire episode to it (515: "Kulturschock Deutschland", covering fax machines, personal space and directness), and universities study it too — Heidelberg University's Campus-Report examined the culture shock experienced by Chinese students. What you are feeling is neither unusual nor personal.

## What shortens the crisis phase
MD,
        ];

        // ── 2) YAZISIZ KURALLAR: nakit paragrafı güncel Bundesbank verisiyle ───────────────────
        $patches['unwritten-rules-of-daily-life-in-germany'] = [
            'Kart kullanımı hızla yaygınlaşsa da **nakit hâlâ hayatta**: küçük fırınlar, çoğu döner büfesi, bazı barlar ve haftalık pazarlar kart kabul etmeyebilir. Cebinde 20–30 € taşımak pratik bir alışkanlık.'
                => 'Almanya\'nın "nakit ülkesi" ünü hızla değişiyor: Bundesbank\'ın ödeme davranışı araştırmasına göre 2025\'te işlemlerin **%55\'i nakitsiz**, **%45\'i nakit** yapıldı — nakitsiz ödeme ilk kez öne geçti (2023\'e göre nakit 6 puan düşüş). Yine de **nakitsiz kalmak risklidir**: küçük fırınlar, çoğu döner büfesi, bazı barlar ve haftalık pazarlar hâlâ kart kabul etmiyor. Cebinde 20–30 € taşımak pratik bir alışkanlık.',
        ];

        $patches['unwritten-rules-of-daily-life-in-germany-de'] = [
            'Kartenzahlung breitet sich aus, aber **Bargeld lebt**: kleine Bäckereien, viele Imbisse, manche Bars und Wochenmärkte nehmen keine Karte. 20–30 € dabeizuhaben ist praktisch.'
                => 'Der Ruf Deutschlands als Bargeldland verändert sich schnell: Laut der Bundesbank-Studie zum Zahlungsverhalten wurden 2025 **55 % der Transaktionen bargeldlos** und **45 % bar** bezahlt — erstmals liegt die bargeldlose Zahlung vorn (Bargeld sechs Punkte unter 2023). Ganz ohne Bargeld wird es trotzdem unpraktisch: kleine Bäckereien, viele Imbisse, manche Bars und Wochenmärkte nehmen weiterhin keine Karte. 20–30 € dabeizuhaben ist praktisch.',
        ];

        $patches['unwritten-rules-of-daily-life-in-germany-en'] = [
            'Card payment is spreading, but **cash is very much alive**: small bakeries, many takeaways, some bars and weekly markets may not take cards. Carrying €20–30 is a practical habit.'
                => 'Germany\'s reputation as a cash country is changing fast: according to the Bundesbank\'s payment behaviour study, **55% of transactions were cashless** in 2025 and **45% were cash** — the first time cashless payment came out ahead, with cash down six points on 2023. Going entirely without cash is still impractical, though: small bakeries, many takeaways, some bars and weekly markets continue to refuse cards. Carrying €20–30 is a practical habit.',
        ];

        // ── 3) KÜLTÜRLE ALMANCA: adı konmuş iki ücretsiz kaynak daha ───────────────────────────
        $patches['learning-german-through-culture-german-media-and-podcasts'] = [
            "## Ücretsiz iki kaynak daha" => <<<'MD'
## Öğrenen için yapılmış iki kaynak daha

- **Easy German:** Almanca öğrenenler için üretilen podcast ve YouTube kanalı. Sokak röportajları ve serbest sohbetler doğal hızda ama **Almanca altyazılı** ilerliyor; haftada iki bölüm yayınlanıyor. Kültürel uyum da düzenli konularından biri (ör. 515. bölüm doğrudan "Kulturschock Deutschland" üzerine). Not: podcast ve videolar ücretsiz, ancak **transkript ve kelime yardımı ücretli üyelikte** — bu ayrımı bilerek gir.
- **Deutsche Digitale Bibliothek:** Almanya'nın kütüphane, arşiv ve müzelerinin dijitalleştirilmiş koleksiyonlarını tek yerde toplayan ücretsiz platform. Sanal sergiler, konu dosyaları ve tarihsel belgeler; dil pratiğini kültür ve tarih tarafına bağlamak isteyen için iyi bir okuma kaynağı.

## Ücretsiz iki kaynak daha
MD,
        ];

        $patches['learning-german-through-culture-german-media-and-podcasts-de'] = [
            "## Zwei weitere kostenlose Quellen" => <<<'MD'
## Zwei Angebote, die für Lernende gemacht sind

- **Easy German:** Podcast und YouTube-Kanal speziell für Deutschlernende. Straßeninterviews und freie Gespräche laufen im natürlichen Tempo, aber **mit deutschen Untertiteln**; wöchentlich erscheinen zwei Folgen. Kulturelle Anpassung ist ein wiederkehrendes Thema (Folge 515 etwa behandelt direkt „Kulturschock Deutschland"). Hinweis: Podcast und Videos sind kostenlos, **Transkripte und Vokabelhilfe gehören zur kostenpflichtigen Mitgliedschaft** — diesen Unterschied solltest du kennen.
- **Deutsche Digitale Bibliothek:** kostenlose Plattform, die digitalisierte Bestände deutscher Bibliotheken, Archive und Museen bündelt. Virtuelle Ausstellungen, Themendossiers und historische Dokumente — gut geeignet, um Sprachpraxis mit Kultur und Geschichte zu verbinden.

## Zwei weitere kostenlose Quellen
MD,
        ];

        $patches['learning-german-through-culture-german-media-and-podcasts-en'] = [
            "## Two more free resources" => <<<'MD'
## Two resources built for learners

- **Easy German:** a podcast and YouTube channel made specifically for learners. Street interviews and unscripted conversations run at natural speed but come **with German subtitles**, and two episodes appear each week. Cultural adjustment is a recurring subject — episode 515, for instance, is devoted to "Kulturschock Deutschland". Note: the podcast and videos are free, but **transcripts and vocabulary help sit behind a paid membership** — worth knowing before you rely on them.
- **Deutsche Digitale Bibliothek:** a free platform pooling digitised holdings from German libraries, archives and museums. Virtual exhibitions, thematic dossiers and historical documents — a good way to tie language practice to culture and history.

## Two more free resources
MD,
        ];

        foreach ($patches as $slug => $pairs) {
            $post = Post::where('slug', $slug)->first();
            if (! $post) {
                continue;
            }

            $md = $post->content_md;
            foreach ($pairs as $old => $new) {
                if (! str_contains($md, $old)) {
                    continue;
                }
                // Zaten uygulanmışsa (yeni bölüm başlığı gövdede varsa) tekrar ekleme.
                $firstLine = trim(strtok($new, "\n"));
                if ($firstLine !== '' && str_contains($md, $firstLine) && $firstLine !== trim($old)) {
                    continue;
                }
                $md = str_replace($old, $new, $md);
            }

            if ($md === $post->content_md) {
                continue;
            }

            $html = Str::markdown($md, ['html_input' => 'allow', 'allow_unsafe_links' => false]);
            $post->update([
                'content_md' => $md,
                'content_html' => $html,
                'reading_minutes' => max(1, (int) round(str_word_count(strip_tags($html)) / 200)),
            ]);
        }
    }

    public function down(): void
    {
        // Geri alma yok: eklenen bölümler ve güncellenen Bundesbank verisi yazıları yalnızca
        // doğrulaştırıyor; eski nakit cümlesi 2023 öncesi duruma göre yazılmıştı.
    }
};
