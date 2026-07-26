<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN): Turizm/otelcilik diplomasıyla Almanya iş piyasası (2026).
 * Doğrulandı: uygulamalı FH/HAW diploması + Praxissemester istihdama dönük; operasyonda maaş mütevazı,
 * uzmanlaşma (yönetim/havacılık/MICE/kurumsal) getiriyi artırır; mezuniyet sonrası 18 ay iş-arama oturumu;
 * Blue Card 2026 ~50.700€ / darboğaz ~45.934€ (yaklaşık; doğrula).
 * Yazar: Halil Yaprakli. Kategori: almanyada-egitim. slug-bazlı idempotent.
 */
return new class extends Migration
{
    public function up(): void
    {
        $groupId = 'f6a40000-4444-4d2f-9f30-ff0daa13dd04';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'almanyada-egitim')->value('id')
            ?? DB::table('categories')->where('slug', 'universities')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
Turizm ya da otelcilik yönetimi diploması elinde. Peki bu kağıt Almanya iş piyasasında **gerçekte nereye götürür**? Kısa cevap: uygulamalı bir diploma, doğru uzmanlaşmayla operasyondan yönetime kadar geniş bir yelpaze açar — ama yolu bilinçli seçmek gerekir. Bu yazı diplomanın karşılığını, dürüst maaş gerçeğini ve mezuniyet sonrası vize oturumunu netleştiriyor.

## Uygulamalı diploma: seni nereye götürür?
Almanya'da turizm/otelcilik programları ağırlıkla **FH/HAW (uygulamalı bilimler)** ve özel okullarda okutulur. Bunun anlamı: müfredat teoriden çok **uygulamaya**, **Praxissemester'e** ve sektör projelerine dayanır. İşverenler bu diplomayı "sahaya hazır" olarak okur.

Diploma tek başına bir garanti değil; bir **başlangıç noktası**. Seni götürdüğü yer, mezuniyette hangi alanı seçtiğine bağlı: resepsiyon/operasyondan mı ilerliyorsun, yoksa **revenue management, etkinlik/MICE, havacılık yönetimi veya kurumsal seyahat** gibi bir dikeyde mi uzmanlaşıyorsun? İkisi çok farklı gelir eğrileri çizer.

İyi haber şu: Almanya turizmi yapısal olarak sağlam. İç turizm güçlü, fuar/kongre ekonomisi (MICE) Avrupa'nın en büyüğü, havacılık ve otel zincirleri sürekli nitelikli eleman arıyor. Yani talep var; asıl soru, o talebin **iyi ödeyen** kısmına nasıl konumlanacağın. Diploman kapıyı açar, ama içeride hangi odaya gireceğini senin stratejin belirler.

## Kariyer yolları: diploma nereye açılıyor?
Aşağıdaki tablo tipik yolları ve yaklaşık giriş/orta seviye brüt yıllık aralıkları özetliyor (2025/2026, **yaklaşık; mutlaka doğrula**):

| Kariyer yolu | Tipik işveren | Giriş brüt/yıl (yaklaşık) | Getiri potansiyeli |
|---|---|---|---|
| Otel operasyon/resepsiyon | Marriott, Accor, Hilton, bağımsız oteller | ~30–38k | Düşük–orta |
| Revenue/Hotel management | Zincir oteller, yönetim şirketleri | ~40–55k | Orta–yüksek |
| Etkinlik & MICE yönetimi | Ajanslar, fuar/kongre merkezleri | ~38–50k | Orta–yüksek |
| Havacılık/havayolu yönetimi | Lufthansa Group, havalimanları | ~42–58k | Yüksek |
| Kurumsal seyahat & TMC | Şirket seyahat yönetimi, danışmanlık | ~40–55k | Orta–yüksek |
| Destinasyon/tur operatörü | TUI, DER, DMO'lar | ~35–48k | Orta |

Rakamlar bölge, işveren büyüklüğü ve dil seviyesine göre değişir. **Kalın gerçek:** aynı diplomayla operasyonda ~32k, havacılık yönetiminde ~50k başlayabilirsin — fark, seçtiğin dikeydedir.

## Uzmanlaşma neden getiriyi artırır?
Operasyonel otelcilik **tutku sektörü** ama giriş maaşları mütevazı; bu, sektörün bilinen bir gerçeği. Getiriyi büyüten şey, **uzmanlaşma**: operasyonu bilen ama üstüne **analitik/yönetsel bir katman** ekleyen mezunlar (revenue management, veri, bütçe, tedarik, dijital pazarlama) daha hızlı yönetime çıkar.

- **Havacılık & MICE** genelde daha iyi öder çünkü ölçek büyük, marjlar ve bütçeler yüksektir.
- **Kurumsal/danışmanlık** tarafı ofis-merkezli, daha öngörülebilir ve iyi ücretlidir.
- **Dijital + dil** kombinasyonu (revenue tools + Almanca + İngilizce) seni her kapıda öne çıkarır.

Yani diploma sabit; asıl kaldıraç, mezuniyette **operasyondan yönetime doğru** bilinçli konumlanmaktır. Pratik bir örnek: iki mezun aynı okuldan çıkar; biri resepsiyon vardiyalarında kalır, diğeri revenue/yield tarafına geçip fiyatlama ve doluluk verisini öğrenir. Üç yıl sonra ikincisi genelde departman yöneticisi ya da bölge revenue analistidir — ve maaş farkı belirginleşir. Uzmanlaşma, "yıllar geçtikçe kendiliğinden yükselme" beklentisinden çok daha hızlı sonuç verir.

## Mezuniyet sonrası: 18 aylık iş-arama oturumu
Almanya'da bir devlet/eyalet üniversitesini veya tanınan bir yüksekokulu bitiren AB-dışı mezunlar, iş aramak için **18 aya kadar oturum izni** alabilir (yaklaşık; güncel kuralları doğrula). Bu süre altın değerinde: hâlâ Almanya'dayken staj ağını işe çevireb, tam zamanlı teklif toplayabilirsin.

Teklifi alınca çalışma iznine/**Blue Card**'a geçiş gelir. 2026 için Blue Card genel maaş eşiği **~50.700€/yıl**, darboğaz meslekler ve yeni mezunlar için **~45.934€/yıl** civarındadır (**yaklaşık; doğrula**). Turizm/otelcilikte bazı operasyon rolleri bu eşiğin altında kalabilir — bu yüzden **yönetim/havacılık/kurumsal** rolleri Blue Card'a giden yolu kolaylaştırır. Vize sürecinin adım adım işleyişi için [iş teklifiyle çalışma vizesi rehberimize](/tr/blog/germany-work-visa-with-job-offer-process-timeline-fast-track) bakabilirsin.

## Almanca, network ve staj gerçeği
Üç dürüst gerçek:

1. **Almanca:** Uluslararası zincirler ve kurumsal roller İngilizce-dostudur, ama **Alman iç pazarında** (bölgesel oteller, DMO'lar, kamu turizmi) Almanca çoğu zaman şarttır. B2+ Almanca, ulaşabildiğin iş havuzunu ikiye katlar.
2. **Network:** Bu sektör ilişkiyle döner. **Praxissemester ve staj**, mezuniyette işe dönüşen en güçlü kanaldır — stajını ciddiye al.
3. **Kanıtlanmış deneyim:** İşverenler CV'de "yapabilirim" değil "yaptım" ister. Etkinlik, revenue veya operasyon projelerini somut sonuçla anlat.

## Uluslararası öğrenci için gerçekçi yol
Sırayla düşün: **doğru program → güçlü staj → uzmanlaşma → dil → hedefli başvuru → 18 ay içinde teklif → Blue Card**. Master gerekip gerekmediğine ya da doğrudan iş aramaya mı yöneleceğine karar verirken [master mı yoksa iş-arama vizesi mi karşılaştırmamız](/tr/blog/germany-masters-vs-job-seeker-visa-two-keys-career) net bir çerçeve sunuyor.

Bu diplomanın mantığını en baştan kurmak istersen [Almanya'da turizm & otelcilik okuma rehberi](/tr/blog/studying-tourism-and-hospitality-management-in-germany-as-a-foreigner), Almancasız yol için [İngilizce turizm & otelcilik master programları](/tr/blog/english-taught-tourism-and-hospitality-masters-in-germany), sektörün maaş ve kariyer detayları için [Almanya'da turizm & otelcilikte çalışmak](/tr/blog/working-in-tourism-and-hospitality-in-germany-careers-and-salary) yazılarını oku. Yönetim tarafına komşu bir alan olan işletme için [BWL diplomasıyla iş piyasası](/tr/blog/what-to-do-with-a-business-bwl-degree-in-germany-job-market) da faydalı bir kıyas verir.

## Sonuç & dürüst tavsiye
Turizm/otelcilik diploması Almanya'da **istihdama dönük** bir uygulamalı derecedir — ama getiri, seçtiğin dikeye bağlıdır. Sadece operasyonda kalırsan maaş mütevazı olur; **yönetim, havacılık, MICE veya kurumsal** tarafına uzmanlaşırsan hem gelir hem Blue Card yolu açılır. Stajını, dilini ve mezuniyet sonrası 18 ayı stratejik kullan. Tutkuyu koru ama kariyeri sayılarla planla.

*Bu yazı 2026 başındaki kurallara ve yaklaşık rakamlara dayanır; maaş eşikleri, vize süreleri ve program koşulları değişebilir. Başvurudan önce resmi kaynaklardan (Make it in Germany, ilgili yüksekokul ve yabancılar dairesi) güncel bilgiyi doğrula.*
MD;

        $deBody = <<<'MD'
Du hast einen Abschluss in Tourismus- oder Hospitality-Management in der Hand. Aber wohin führt dieses Papier auf dem deutschen Arbeitsmarkt **wirklich**? Die kurze Antwort: Ein praxisorientierter Abschluss öffnet mit der richtigen Spezialisierung ein breites Feld – vom Betrieb bis ins Management. Aber du musst deinen Weg bewusst wählen. Dieser Beitrag klärt den Gegenwert deines Abschlusses, die ehrliche Gehaltsrealität und deinen Aufenthalt nach dem Studium.

## Ein praxisnaher Abschluss: Wohin bringt er dich?
In Deutschland werden Tourismus-/Hospitality-Programme überwiegend an **FH/HAW (Hochschulen für angewandte Wissenschaften)** und Privatschulen gelehrt. Das heißt: Der Lehrplan setzt weniger auf Theorie und mehr auf **Praxis**, das **Praxissemester** und Branchenprojekte. Arbeitgeber lesen diesen Abschluss als „einsatzbereit".

Der Abschluss allein ist keine Garantie, sondern ein **Startpunkt**. Wohin er dich bringt, hängt davon ab, welchen Bereich du beim Abschluss wählst: Gehst du über Rezeption/Betrieb weiter oder spezialisierst du dich auf eine Vertikale wie **Revenue Management, Event/MICE, Aviation Management oder Corporate Travel**? Beide zeichnen sehr unterschiedliche Gehaltskurven.

Die gute Nachricht: Der deutsche Tourismus ist strukturell solide. Der Binnentourismus ist stark, die Messe-/Kongresswirtschaft (MICE) gehört zu den größten Europas, und Aviation sowie Hotelketten suchen laufend qualifiziertes Personal. Die Nachfrage ist also da; die eigentliche Frage ist, wie du dich in den **gut bezahlten** Teil dieser Nachfrage positionierst. Dein Abschluss öffnet die Tür, aber deine Strategie entscheidet, welchen Raum dahinter du betrittst.

## Karrierewege: Wohin öffnet sich der Abschluss?
Die folgende Tabelle fasst typische Wege und ungefähre Einstiegs-/Mittelbrutto pro Jahr zusammen (2025/2026, **ungefähr; unbedingt prüfen**):

| Karriereweg | Typische Arbeitgeber | Einstiegsbrutto/Jahr (ca.) | Ertragspotenzial |
|---|---|---|---|
| Hotelbetrieb/Rezeption | Marriott, Accor, Hilton, Privathotels | ~30–38k | Niedrig–mittel |
| Revenue/Hotel Management | Kettenhotels, Managementgesellschaften | ~40–55k | Mittel–hoch |
| Event- & MICE-Management | Agenturen, Messe-/Kongresszentren | ~38–50k | Mittel–hoch |
| Aviation/Airline-Management | Lufthansa Group, Flughäfen | ~42–58k | Hoch |
| Corporate Travel & TMC | Firmenreise-Management, Beratung | ~40–55k | Mittel–hoch |
| Destination/Reiseveranstalter | TUI, DER, DMOs | ~35–48k | Mittel |

Die Zahlen variieren je nach Region, Arbeitgebergröße und Sprachniveau. **Harte Wahrheit:** Mit demselben Abschluss startest du im Betrieb bei ~32k, im Aviation-Management bei ~50k – der Unterschied liegt in der gewählten Vertikale.

## Warum Spezialisierung den Ertrag steigert
Die operative Hotellerie ist eine **Leidenschaftsbranche**, aber die Einstiegsgehälter sind bescheiden; das ist ein bekanntes Branchenphänomen. Was den Ertrag steigert, ist **Spezialisierung**: Absolventen, die den Betrieb kennen und eine **analytische/Management-Ebene** darauflegen (Revenue Management, Daten, Budget, Einkauf, digitales Marketing), steigen schneller ins Management auf.

- **Aviation & MICE** zahlen meist besser, weil die Skalierung groß und die Budgets hoch sind.
- Die Seite **Corporate/Beratung** ist bürozentriert, planbarer und gut bezahlt.
- Die Kombination **Digital + Sprache** (Revenue-Tools + Deutsch + Englisch) hebt dich an jeder Tür hervor.

Der Abschluss ist also fix; der eigentliche Hebel ist die bewusste Positionierung **vom Betrieb Richtung Management** beim Abschluss. Ein praktisches Beispiel: Zwei Absolventen verlassen dieselbe Hochschule; einer bleibt in Rezeptionsschichten, der andere wechselt auf die Revenue-/Yield-Seite und lernt Pricing- und Auslastungsdaten. Drei Jahre später ist Letzterer meist Abteilungsleiter oder regionaler Revenue-Analyst – und der Gehaltsunterschied wird deutlich. Spezialisierung liefert viel schneller Ergebnisse als die Hoffnung auf einen automatischen Aufstieg mit den Jahren.

## Nach dem Abschluss: 18 Monate zur Jobsuche
Nicht-EU-Absolventen einer deutschen staatlichen Hochschule können nach dem Studium eine Aufenthaltserlaubnis von **bis zu 18 Monaten** zur Jobsuche erhalten (ungefähr; aktuelle Regeln prüfen). Diese Zeit ist Gold wert: Du kannst dein Praktikumsnetzwerk noch in Deutschland in ein Angebot verwandeln und Vollzeitangebote sammeln.

Mit dem Angebot folgt der Wechsel zur Arbeitserlaubnis/**Blue Card**. Für 2026 liegt die allgemeine Blue-Card-Gehaltsschwelle bei **~50.700€/Jahr**, für Engpassberufe und Berufseinsteiger bei **~45.934€/Jahr** (**ungefähr; prüfen**). Einige Betriebsrollen im Tourismus können unter dieser Schwelle bleiben – deshalb erleichtern **Management-/Aviation-/Corporate**-Rollen den Weg zur Blue Card. Zum Schritt-für-Schritt-Ablauf des Visums siehe unseren [Leitfaden zum Arbeitsvisum mit Jobangebot](/de/blog/germany-work-visa-with-job-offer-process-timeline-fast-track-de).

## Deutsch, Netzwerk und die Praktikumsrealität
Drei ehrliche Wahrheiten:

1. **Deutsch:** Internationale Ketten und Corporate-Rollen sind englischfreundlich, aber im **deutschen Binnenmarkt** (regionale Hotels, DMOs, öffentlicher Tourismus) ist Deutsch oft Pflicht. Deutsch auf B2+ verdoppelt deinen erreichbaren Jobpool.
2. **Netzwerk:** Diese Branche lebt von Beziehungen. Das **Praxissemester und Praktika** sind der stärkste Kanal, der beim Abschluss zu einem Job wird – nimm dein Praktikum ernst.
3. **Nachgewiesene Erfahrung:** Arbeitgeber wollen im Lebenslauf kein „ich kann", sondern „ich habe getan". Beschreibe Event-, Revenue- oder Betriebsprojekte mit konkreten Ergebnissen.

## Ein realistischer Weg für internationale Studierende
Denke in Reihenfolge: **richtiges Programm → starkes Praktikum → Spezialisierung → Sprache → gezielte Bewerbung → Angebot innerhalb von 18 Monaten → Blue Card**. Wenn du entscheidest, ob du einen Master brauchst oder direkt zur Jobsuche gehst, bietet unser [Vergleich Master vs. Job-Seeker-Visum](/de/blog/germany-masters-vs-job-seeker-visa-two-keys-career-de) einen klaren Rahmen.

Wenn du die Logik dieses Abschlusses von Anfang an aufbauen willst, lies den [Leitfaden zum Studium von Tourismus & Hospitality in Deutschland](/de/blog/studying-tourism-and-hospitality-management-in-germany-as-a-foreigner-de), für den Weg ohne Deutsch die [englischsprachigen Master in Tourismus & Hospitality](/de/blog/english-taught-tourism-and-hospitality-masters-in-germany-de) und für Gehalts- und Karrieredetails [Arbeiten in Tourismus & Hospitality in Deutschland](/de/blog/working-in-tourism-and-hospitality-in-germany-careers-and-salary-de). Als benachbartes Managementfeld gibt der [Arbeitsmarkt mit BWL-Abschluss](/de/blog/what-to-do-with-a-business-bwl-degree-in-germany-job-market-de) einen nützlichen Vergleich.

## Fazit & ehrlicher Rat
Ein Tourismus-/Hospitality-Abschluss ist in Deutschland ein **praxisnaher, arbeitsmarktorientierter** Abschluss – aber der Ertrag hängt von deiner gewählten Vertikale ab. Bleibst du nur im Betrieb, ist das Gehalt bescheiden; spezialisierst du dich auf **Management, Aviation, MICE oder Corporate**, öffnen sich Einkommen und Blue-Card-Weg. Nutze dein Praktikum, deine Sprache und die 18 Monate nach dem Abschluss strategisch. Bewahre die Leidenschaft, aber plane die Karriere mit Zahlen.

*Dieser Beitrag basiert auf den Regeln und Näherungswerten von Anfang 2026; Gehaltsschwellen, Visumsfristen und Programmbedingungen können sich ändern. Prüfe vor der Bewerbung aktuelle Angaben bei offiziellen Quellen (Make it in Germany, der jeweiligen Hochschule und der Ausländerbehörde).*
MD;

        $enBody = <<<'MD'
You're holding a degree in tourism or hospitality management. But where does that paper **actually** take you in the German job market? The short answer: a practice-oriented degree opens a wide field with the right specialization — from operations to management. But you have to choose your path deliberately. This post clarifies the value of your degree, the honest salary reality, and your post-graduation residence.

## A hands-on degree: where does it take you?
In Germany, tourism/hospitality programs are mostly taught at **FH/HAW (universities of applied sciences)** and private schools. That means the curriculum leans less on theory and more on **practice**, the **Praxissemester** (mandatory internship semester), and industry projects. Employers read this degree as "ready for the field."

The degree alone is not a guarantee; it's a **starting point**. Where it takes you depends on which area you choose at graduation: do you advance through front-desk/operations, or do you specialize in a vertical such as **revenue management, event/MICE, aviation management, or corporate travel**? The two trace very different salary curves.

The good news: German tourism is structurally solid. Domestic tourism is strong, the trade-fair/congress economy (MICE) is among the largest in Europe, and aviation and hotel chains are constantly looking for qualified staff. So demand exists; the real question is how you position yourself in the **well-paid** part of that demand. Your degree opens the door, but your strategy decides which room behind it you walk into.

## Career paths: where does the degree open up?
The table below summarizes typical paths and approximate entry/mid-level annual gross ranges (2025/2026, **approximate; always verify**):

| Career path | Typical employers | Entry gross/year (approx.) | Earning potential |
|---|---|---|---|
| Hotel operations/front desk | Marriott, Accor, Hilton, independent hotels | ~30–38k | Low–medium |
| Revenue/hotel management | Chain hotels, management companies | ~40–55k | Medium–high |
| Event & MICE management | Agencies, trade fair/congress centers | ~38–50k | Medium–high |
| Aviation/airline management | Lufthansa Group, airports | ~42–58k | High |
| Corporate travel & TMC | Corporate travel management, consulting | ~40–55k | Medium–high |
| Destination/tour operator | TUI, DER, DMOs | ~35–48k | Medium |

Figures vary by region, employer size, and language level. **Hard truth:** with the same degree you might start at ~32k in operations and ~50k in aviation management — the difference is the vertical you choose.

## Why specialization boosts your return
Operational hospitality is a **passion industry**, but entry salaries are modest; that's a well-known feature of the sector. What grows the return is **specialization**: graduates who know operations and add an **analytical/management layer** on top (revenue management, data, budgeting, procurement, digital marketing) climb into management faster.

- **Aviation & MICE** usually pay better because scale is large and budgets are high.
- The **corporate/consulting** side is office-centric, more predictable, and well paid.
- The **digital + language** combination (revenue tools + German + English) makes you stand out at every door.

So the degree is fixed; the real lever is deliberately positioning yourself **from operations toward management** at graduation. A practical example: two graduates leave the same school; one stays in front-desk shifts, the other moves to the revenue/yield side and learns pricing and occupancy data. Three years later the second is usually a department head or regional revenue analyst — and the salary gap becomes clear. Specialization delivers results far faster than hoping for an automatic rise as the years pass.

## After graduation: an 18-month job-search residence
Non-EU graduates of a German state university can obtain a residence permit of **up to 18 months** to look for a job after graduation (approximate; verify current rules). This period is worth gold: while still in Germany, you can turn your internship network into an offer and collect full-time offers.

Once you have an offer, the switch to a work permit/**Blue Card** follows. For 2026, the general Blue Card salary threshold is around **~€50,700/year**, and for shortage occupations and new graduates around **~€45,934/year** (**approximate; verify**). Some operational roles in tourism may fall below this threshold — which is why **management/aviation/corporate** roles ease the path to a Blue Card. For the step-by-step visa process, see our [work visa with a job offer guide](/en/blog/germany-work-visa-with-job-offer-process-timeline-fast-track-en).

## German, network, and the internship reality
Three honest truths:

1. **German:** International chains and corporate roles are English-friendly, but in the **German domestic market** (regional hotels, DMOs, public tourism) German is often mandatory. German at B2+ doubles the job pool you can reach.
2. **Network:** This industry runs on relationships. The **Praxissemester and internships** are the strongest channel that converts into a job at graduation — take your internship seriously.
3. **Proven experience:** Employers want "I did," not "I can," on your CV. Describe event, revenue, or operations projects with concrete results.

## A realistic path for international students
Think in sequence: **right program → strong internship → specialization → language → targeted applications → offer within 18 months → Blue Card**. When deciding whether you need a master's or should go straight to the job hunt, our [master's vs. job-seeker visa comparison](/en/blog/germany-masters-vs-job-seeker-visa-two-keys-career-en) offers a clear framework.

If you want to build this degree's logic from the start, read the [guide to studying tourism & hospitality in Germany](/en/blog/studying-tourism-and-hospitality-management-in-germany-as-a-foreigner-en); for the no-German route, the [English-taught tourism & hospitality master's programs](/en/blog/english-taught-tourism-and-hospitality-masters-in-germany-en); and for salary and career details, [working in tourism & hospitality in Germany](/en/blog/working-in-tourism-and-hospitality-in-germany-careers-and-salary-en). As a neighboring management field, [what to do with a BWL degree](/en/blog/what-to-do-with-a-business-bwl-degree-in-germany-job-market-en) gives a useful comparison.

## Conclusion & honest advice
A tourism/hospitality degree is a **practice-oriented, job-market-focused** degree in Germany — but the return depends on your chosen vertical. If you stay only in operations, the salary stays modest; if you specialize in **management, aviation, MICE, or corporate**, both income and the Blue Card path open up. Use your internship, your language skills, and the 18 months after graduation strategically. Keep the passion, but plan the career with numbers.

*This post is based on rules and approximate figures as of early 2026; salary thresholds, visa durations, and program conditions may change. Before applying, verify current information from official sources (Make it in Germany, the relevant university, and the immigration office).*
MD;

        $variants = [
            'tr' => ['slug'=>'what-to-do-with-a-tourism-hospitality-degree-in-germany-job-market',    'title'=>'Almanya\'da Turizm/Otelcilik Diplomasıyla Ne Yapılır? İş Piyasası (2026)', 'excerpt'=>'Turizm/otelcilik diploması Almanya iş piyasasında nereye götürür? Kariyer yolları, dürüst maaş gerçeği, uzmanlaşmanın getirisi ve mezuniyet sonrası 18 aylık iş-arama oturumu.', 'meta_title'=>'Turizm/Otelcilik Diplomasıyla İş Piyasası — Almanya (2026)', 'meta_description'=>'Almanya\'da turizm/otelcilik diplomasıyla kariyer yolları, maaş gerçeği, uzmanlaşma ve 18 ay iş-arama oturumu. Dürüst 2026 rehberi.', 'body'=>$trBody],
            'de' => ['slug'=>'what-to-do-with-a-tourism-hospitality-degree-in-germany-job-market-de', 'title'=>'Was tun mit einem Tourismus-/Hospitality-Abschluss in Deutschland? Arbeitsmarkt (2026)', 'excerpt'=>'Wohin führt ein Tourismus-/Hospitality-Abschluss auf dem deutschen Arbeitsmarkt? Karrierewege, ehrliche Gehaltsrealität, Spezialisierung und 18 Monate Jobsuche nach dem Studium.', 'meta_title'=>'Tourismus-/Hospitality-Abschluss: Arbeitsmarkt Deutschland (2026)', 'meta_description'=>'Karrierewege, Gehaltsrealität, Spezialisierung und 18 Monate Jobsuche mit einem Tourismus-/Hospitality-Abschluss in Deutschland. Ehrlicher 2026-Leitfaden.', 'body'=>$deBody],
            'en' => ['slug'=>'what-to-do-with-a-tourism-hospitality-degree-in-germany-job-market-en', 'title'=>'What to Do with a Tourism/Hospitality Degree in Germany? Job Market (2026)', 'excerpt'=>'Where does a tourism/hospitality degree take you in Germany\'s job market? Career paths, the honest salary reality, the payoff of specialization, and the 18-month post-graduation job search.', 'meta_title'=>'Tourism/Hospitality Degree: Germany Job Market (2026)', 'meta_description'=>'Career paths, salary reality, specialization and the 18-month job-search residence with a tourism/hospitality degree in Germany. Honest 2026 guide.', 'body'=>$enBody],
        ];

        foreach ($variants as $locale => $v) {
            $html = Str::markdown($v['body'], ['html_input' => 'allow', 'allow_unsafe_links' => false]);
            $payload = [
                'locale'=>$locale, 'translation_group_id'=>$groupId, 'user_id'=>$userId, 'category_id'=>$categoryId,
                'title'=>$v['title'], 'excerpt'=>Str::limit($v['excerpt'],250,'…'),
                'content_md'=>$v['body'], 'content_html'=>$html,
                'meta_title'=>$v['meta_title'], 'meta_description'=>Str::limit($v['meta_description'],158,'…'),
                'reading_minutes'=>max(1,(int)round(str_word_count(strip_tags($html))/200)),
                'is_published'=>true, 'published_at'=>now(),
            ];
            $existing = Post::where('slug', $v['slug'])->first();
            $existing ? $existing->update($payload) : Post::create($payload + ['slug'=>$v['slug']]);
        }
    }

    public function down(): void
    {
        Post::whereIn('slug', [
            'what-to-do-with-a-tourism-hospitality-degree-in-germany-job-market',
            'what-to-do-with-a-tourism-hospitality-degree-in-germany-job-market-de',
            'what-to-do-with-a-tourism-hospitality-degree-in-germany-job-market-en',
        ])->delete();
    }
};
