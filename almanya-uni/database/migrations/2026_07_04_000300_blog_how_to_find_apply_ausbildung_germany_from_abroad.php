<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN): Almanya'da Ausbildung yeri bulma ve yurtdışından başvuru (2026).
 * Doğrulandı: Ausbildungsplatz kaynakları = IHK/HWK Lehrstellenbörse + Bundesagentur für Arbeit + işveren siteleri.
 * En zor kısım yurtdışından sözleşme almak; §16a vize sözleşme + B1-B2 Almanca ister. Vize adımları hedge'li, resmi doğrula.
 * Yazar: Halil Yaprakli. Kategori: almanyada-egitim. FK-safe + slug-bazlı idempotent.
 */
return new class extends Migration
{
    public function up(): void
    {
        $groupId = 'a3b30000-3333-4d5f-9f80-aa02bb07dd03';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'almanyada-egitim')->value('id')
            ?? DB::table('categories')->where('slug', 'universities')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
Almanya'da bir Ausbildung (dual meslek eğitimi) fikrini sevdin, alanını da seçtin. Peki asıl soru: **yurtdışından, henüz Almanya'ya gelmeden Ausbildung yeri (Ausbildungsplatz) nasıl bulunur?** Dürüst olalım — bu, tüm sürecin en zor kısmı. Ama imkânsız değil; her yıl binlerce yabancı bunu başarıyor. Bu yazı, nerede arayacağını, nasıl başvuracağını ve sözleşme sonrası vize adımlarını gerçekçi biçimde anlatıyor.

Temel kavramlar için önce [Ausbildung nedir yazısına](/tr/blog/what-is-ausbildung-dual-vocational-training-in-germany-for-foreigners) ve hangi alanların daha kolay olduğunu görmek için [en çok talep gören Ausbildung alanları yazısına](/tr/blog/best-ausbildung-fields-in-germany-for-international-students) göz atmanı öneririm.

## En zor kısım: sözleşmeyi almak

Ausbildung sürecinde vize, taşınma, dil derdi var ama gerçek darboğaz şu: **imzalı bir Ausbildung sözleşmesi (Ausbildungsvertrag) olmadan hiçbir şey başlamaz.** §16a vizesi de, taşınma da, her şey bu sözleşmeye bağlı.

Neden zor? Çünkü sen yurtdışındasın. İşveren seni yüz yüze göremiyor, Almanca seviyeni test edemiyor, "acaba gelir mi, uyum sağlar mı" diye tereddüt ediyor. **İyi haber:** darboğaz mesleklerde (IT, zanaat, otelcilik, bakım) işverenler yer dolduramıyor ve giderek daha çok uluslararası aday alıyor. Yani doğru alan + iyi Almanca + sabırlı başvuru = gerçek şans.

## Nerede aranır: kaynaklar

Ausbildung yeri ararken tek bir yere bağlı kalma — birden çok kanalı paralel kullan. En güvenilir resmi kaynaklar:

| Kaynak | Ne işe yarar | Not |
|---|---|---|
| **Bundesagentur für Arbeit** (arbeitsagentur.de) | Devletin resmi iş/çıraklık portalı, en büyük ilan havuzu | Ausbildung filtresi var; ücretsiz |
| **IHK Lehrstellenbörse** | Ticaret/sanayi odalarının çıraklık borsası (IT, ticaret, otelcilik, endüstri) | Bölgesel oda siteleri |
| **HWK Lehrstellenbörse** | Zanaat odası çıraklık borsası (tesisat, elektrik, mekatronik) | El işi meslekleri için |
| **make-it-in-germany.com** | Devletin resmi uluslararası portalı, vize + iş rehberi | İngilizce; güncel resmi bilgi |
| İşveren siteleri (Kariere/Ausbildung sayfaları) | Doğrudan başvuru; büyük şirketler kendi portalından alır | En yüksek dönüş oranı |

Strateji: büyük şirketlerin (Bosch, Deutsche Bahn, Lufthansa, oteller zincirleri, IT firmaları) "Ausbildung" veya "Karriere" sayfalarına **doğrudan** başvur. Portallar tarama için iyi, ama doğrudan başvuru çoğu zaman daha etkili.

## Başvuru: CV, Anschreiben ve Almanca

Alman başvuru kültürü kendine özgüdür. Tipik bir başvuru paketi:

- **Lebenslauf (CV):** kısa, kronolojik (tersten), tek-iki sayfa, fotoğraflı olması yaygın. Abartısız ve net.
- **Anschreiben (ön yazı / motivasyon mektubu):** "neden bu meslek, neden bu şirket, neden Almanya" — kişisel ve şirkete özel. Kopyala-yapıştır belli olur.
- **Diploma/transkript:** çevirili; lise diploman genelde yeterli.
- **Dil sertifikası:** Goethe/telc/ÖSD Almanca sertifikan varsa mutlaka ekle — en güçlü kozun.

Her şeyi mümkünse **Almanca** hazırla. İngilizce başvuru bazı IT/uluslararası şirketlerde kabul görür ama Ausbildung çoğunlukla Almanca yürür ve Almanca başvuru ciddiyetini gösterir.

## Dil şartı: önce Almanca

Bunu yeterince vurgulayamam: **dil, tüm sürecin belkemiği.** Çoğu işveren ve vize için genelde **B1–B2 Almanca** beklenir; Berufsschule (meslek okulu) tamamen Almanca yürür. Almancasız ne sözleşme ne vize gelir.

Pratik plan: Almanya'ya gelmeden **en az B1**, tercihen B2'ye ulaş. Sertifikanı başvurudan önce al — hem işverene güven verir hem vize dosyanı güçlendirir. Dil, "sonra hallederim" denecek bir şey değil; **ilk yatırımın** bu olmalı.

## §16a vize: sözleşme sonrası adımlar

Sözleşmeyi aldıysan sıra vizede. Non-EU vatandaşlar için **§16a (Ausbildung/meslek eğitimi vizesi)** yolu şöyle işler (**genel hatlar; kesin ve güncel adımları mutlaka resmi kaynaktan doğrula**):

1. **İmzalı Ausbildungsvertrag** — vizenin temeli.
2. **Dil kanıtı** — genelde B1–B2 Almanca sertifikası.
3. **Maddi güvence** — maaşlı *duale* Ausbildung'da maaş çoğu zaman yeterli sayılır; **schulische (okul-tabanlı, ücretsiz)** eğitimde genelde **Sperrkonto (bloke hesap)** istenir.
4. **Ülkendeki Alman konsolosluğunda** ulusal vize (D tipi) başvurusu; randevu + evraklar.
5. Almanya'ya varınca yerel **Ausländerbehörde**'de oturum iznine çevirme.

Bu adımlar ülkeye, konsolosluğa ve yıla göre değişir. **Garanti veremem** — güncel şartlar, ücretler ve evrak listesi için **make-it-in-germany.com** ve başvuracağın Alman konsolosluğunun resmi sayfasını esas al. İş teklifiyle vize sürecinin genel mantığını [iş teklifiyle çalışma vizesi yazısında](/tr/blog/germany-work-visa-with-job-offer-process-timeline-fast-track) da bulabilirsin (Ausbildung §16a ayrı bir yol, ama süreç mantığı benzer).

## Yaygın hatalar ve strateji

Yurtdışından başvuranların en sık düştüğü tuzaklar:

- **Az başvurmak.** 5 başvuruyla olmaz; onlarca, hatta yüzlerce başvuru normaldir. Sayı oyunu.
- **Dili son ana bırakmak.** B1/B2'yi baştan halletmeyen çoğu aday elenir.
- **Genel/kopyala Anschreiben.** Her şirkete özel yaz.
- **Tek kanala güvenmek.** Portal + doğrudan başvuru + oda borsaları birlikte kullan.
- **Darboğaz-dışı, aşırı rekabetçi alan seçmek.** IT/zanaat/bakım gibi darboğaz alanlar yurtdışı adaya çok daha açık.
- **Erken pes etmek.** İlk "red" mailleri normaldir; süreç aylar sürebilir.

Akıllı strateji: erkenden (bir yıl öncesinden) başla, dili öne al, darboğaz alan seç, çok sayıda ve kişiselleştirilmiş başvur, resmi kaynakları takip et.

## Sonuç & dürüst tavsiye

Yurtdışından Ausbildung yeri bulmak zor ama sistematik bir iştir — şans işi değil. Formül net: **iyi Almanca (B1–B2) + darboğaz alan + çok sayıda kişiselleştirilmiş başvuru + resmi kaynakları doğru kullanmak.** Sözleşmeyi aldığında §16a yolu büyük ölçüde açılır; sonrasında maaş, hayat ve kalıcı oturum yolunu [Ausbildung maaşı ve oturum yazısında](/tr/blog/ausbildung-in-germany-salary-life-and-path-to-permanent-residence) anlatıyoruz.

Dürüst tavsiyem: dilini bugün ciddiye al, bir yıl önceden planla, çok başvur ve reddedilmelerden yılma. Bu yol emek ister ama sonunda kazanırken öğrenilen, iş güvenceli ve oturuma çıkan gerçek bir meslek var.

*Bu yazı 2026 başı itibarıyla genel bilgilendirme amaçlıdır; vize şartları, kaynaklar, maaşlar ve prosedürler değişebilir. Bağlayıcı ve güncel bilgi için make-it-in-germany.com, Bundesagentur für Arbeit ve başvuracağın Alman konsolosluğunun resmi sayfalarını esas al. Bu bir hukuki danışmanlık değildir.*
MD;
        $deBody = <<<'MD'
Du hast die Idee einer Ausbildung in Deutschland lieben gelernt und sogar dein Berufsfeld ausgewählt. Die eigentliche Frage ist nun: **Wie findest du vom Ausland aus, bevor du überhaupt in Deutschland bist, einen Ausbildungsplatz?** Sei ehrlich – das ist der schwerste Teil des ganzen Weges. Aber unmöglich ist es nicht; jedes Jahr schaffen es Tausende internationale Bewerber. Dieser Artikel zeigt dir realistisch, wo du suchst, wie du dich bewirbst und welche Visumsschritte nach dem Vertrag folgen.

Für die Grundlagen empfehle ich dir zuerst den Artikel [Was ist eine Ausbildung](/de/blog/what-is-ausbildung-dual-vocational-training-in-germany-for-foreigners-de) und, um die einfacheren Felder zu sehen, den Artikel [die gefragtesten Ausbildungsfelder](/de/blog/best-ausbildung-fields-in-germany-for-international-students-de).

## Der schwerste Teil: den Vertrag bekommen

Bei der Ausbildung gibt es Visum, Umzug und die Sprache – aber der wahre Engpass ist: **Ohne einen unterschriebenen Ausbildungsvertrag beginnt nichts.** Das §16a-Visum, der Umzug, alles hängt an diesem Vertrag.

Warum ist das schwer? Weil du im Ausland bist. Der Arbeitgeber kann dich nicht persönlich sehen, dein Deutschniveau nicht testen und zögert: „Kommt er wirklich, passt er sich an?" **Die gute Nachricht:** In Engpassberufen (IT, Handwerk, Hotellerie, Pflege) können Arbeitgeber die Plätze nicht füllen und nehmen immer mehr internationale Bewerber. Also: das richtige Feld + gutes Deutsch + geduldige Bewerbung = eine echte Chance.

## Wo du suchst: die Quellen

Verlass dich bei der Suche nach einem Ausbildungsplatz nicht auf eine einzige Stelle – nutze mehrere Kanäle parallel. Die verlässlichsten offiziellen Quellen:

| Quelle | Wofür | Hinweis |
|---|---|---|
| **Bundesagentur für Arbeit** (arbeitsagentur.de) | Offizielles staatliches Job-/Ausbildungsportal, größter Anzeigenpool | Ausbildungsfilter vorhanden; kostenlos |
| **IHK-Lehrstellenbörse** | Lehrstellenbörse der Industrie- und Handelskammern (IT, Handel, Hotellerie, Industrie) | Regionale Kammer-Websites |
| **HWK-Lehrstellenbörse** | Lehrstellenbörse der Handwerkskammer (Anlagenmechanik, Elektro, Mechatronik) | Für Handwerksberufe |
| **make-it-in-germany.com** | Offizielles staatliches Portal für Internationale, Visum + Job-Ratgeber | Auf Englisch; aktuelle offizielle Infos |
| Arbeitgeber-Websites (Karriere-/Ausbildungsseiten) | Direktbewerbung; große Firmen rekrutieren über eigenes Portal | Höchste Rücklaufquote |

Strategie: Bewirb dich **direkt** auf den „Ausbildung"- oder „Karriere"-Seiten großer Firmen (Bosch, Deutsche Bahn, Lufthansa, Hotelketten, IT-Firmen). Portale sind gut zum Durchsuchen, aber die Direktbewerbung ist oft wirksamer.

## Die Bewerbung: Lebenslauf, Anschreiben und Deutsch

Die deutsche Bewerbungskultur ist eigen. Ein typisches Bewerbungspaket:

- **Lebenslauf:** kurz, chronologisch (rückwärts), ein bis zwei Seiten, ein Foto ist üblich. Sachlich und klar.
- **Anschreiben (Motivationsschreiben):** „warum dieser Beruf, warum diese Firma, warum Deutschland" – persönlich und firmenspezifisch. Copy-paste fällt auf.
- **Zeugnis/Abschluss:** übersetzt; dein Schulabschluss reicht meist.
- **Sprachzertifikat:** Falls du ein Goethe-/telc-/ÖSD-Zertifikat hast, füge es unbedingt bei – dein stärkster Trumpf.

Bereite möglichst alles **auf Deutsch** vor. Eine englische Bewerbung wird bei manchen IT-/internationalen Firmen akzeptiert, aber die Ausbildung läuft meist auf Deutsch, und eine deutsche Bewerbung zeigt deine Ernsthaftigkeit.

## Sprachanforderung: zuerst Deutsch

Das kann ich nicht genug betonen: **Die Sprache ist das Rückgrat des ganzen Wegs.** Die meisten Arbeitgeber und das Visum erwarten in der Regel **B1–B2 Deutsch**; die Berufsschule läuft komplett auf Deutsch. Ohne Deutsch kommt weder Vertrag noch Visum.

Praktischer Plan: Erreiche vor deiner Ankunft in Deutschland **mindestens B1**, besser B2. Hol dein Zertifikat vor der Bewerbung – das gibt dem Arbeitgeber Sicherheit und stärkt deine Visumsakte. Die Sprache ist nichts, was man „später erledigt"; sie sollte **deine erste Investition** sein.

## §16a-Visum: Schritte nach dem Vertrag

Hast du den Vertrag, kommt das Visum. Für Nicht-EU-Bürger läuft der Weg über das **§16a-Visum (Visum zur Berufsausbildung)** so ab (**allgemeine Umrisse; die genauen und aktuellen Schritte bestätige unbedingt bei der offiziellen Quelle**):

1. **Unterschriebener Ausbildungsvertrag** – die Basis des Visums.
2. **Sprachnachweis** – meist ein B1–B2-Deutschzertifikat.
3. **Finanzierung** – bei einer bezahlten *dualen* Ausbildung reicht die Vergütung oft aus; bei **schulischer (schulbasierter, unbezahlter)** Ausbildung wird meist ein **Sperrkonto** verlangt.
4. **Nationales Visum (Typ D)** beim deutschen Konsulat in deinem Land; Termin + Unterlagen.
5. Nach der Ankunft in Deutschland Umwandlung in einen Aufenthaltstitel bei der örtlichen **Ausländerbehörde**.

Diese Schritte variieren je nach Land, Konsulat und Jahr. **Ich kann nichts garantieren** – für aktuelle Bedingungen, Gebühren und Unterlagenlisten halte dich an **make-it-in-germany.com** und die offizielle Seite des deutschen Konsulats, bei dem du dich bewirbst. Die allgemeine Logik des Visumsprozesses mit einem Jobangebot findest du auch im Artikel [Arbeitsvisum mit Jobangebot](/de/blog/germany-work-visa-with-job-offer-process-timeline-fast-track-de) (die Ausbildung nach §16a ist ein eigener Weg, aber die Prozesslogik ähnelt sich).

## Häufige Fehler und Strategie

Die häufigsten Fallen für Bewerber aus dem Ausland:

- **Zu wenig bewerben.** Mit 5 Bewerbungen geht es nicht; Dutzende, sogar Hunderte Bewerbungen sind normal. Ein Zahlenspiel.
- **Die Sprache bis zuletzt aufschieben.** Wer B1/B2 nicht früh erledigt, fällt meist raus.
- **Allgemeines/kopiertes Anschreiben.** Schreib für jede Firma individuell.
- **Auf einen einzigen Kanal vertrauen.** Nutze Portal + Direktbewerbung + Kammerbörsen zusammen.
- **Ein überkompetitives Feld außerhalb der Engpässe wählen.** Engpassfelder wie IT/Handwerk/Pflege sind für Auslandsbewerber viel offener.
- **Zu früh aufgeben.** Erste Absagen sind normal; der Prozess kann Monate dauern.

Kluge Strategie: früh beginnen (ein Jahr vorher), die Sprache nach vorn ziehen, ein Engpassfeld wählen, zahlreich und personalisiert bewerben, offizielle Quellen verfolgen.

## Fazit & ehrlicher Rat

Vom Ausland aus einen Ausbildungsplatz zu finden, ist schwer, aber eine systematische Aufgabe – kein Glücksspiel. Die Formel ist klar: **gutes Deutsch (B1–B2) + Engpassfeld + zahlreiche personalisierte Bewerbungen + richtiger Umgang mit offiziellen Quellen.** Hast du den Vertrag, öffnet sich der §16a-Weg weitgehend; Gehalt, Leben und Weg zur Niederlassung erklären wir im Artikel [Ausbildung: Gehalt und Aufenthalt](/de/blog/ausbildung-in-germany-salary-life-and-path-to-permanent-residence-de).

Mein ehrlicher Rat: Nimm deine Sprache heute ernst, plan ein Jahr im Voraus, bewirb dich viel und lass dich von Absagen nicht entmutigen. Dieser Weg kostet Mühe, aber am Ende steht ein echter Beruf – lernen bei Bezahlung, Jobsicherheit und ein Weg zum Aufenthalt.

*Dieser Artikel dient der allgemeinen Information mit Stand Anfang 2026; Visumsbedingungen, Quellen, Gehälter und Verfahren können sich ändern. Für verbindliche und aktuelle Informationen halte dich an make-it-in-germany.com, die Bundesagentur für Arbeit und die offiziellen Seiten des deutschen Konsulats, bei dem du dich bewirbst. Dies ist keine Rechtsberatung.*
MD;
        $enBody = <<<'MD'
You've come to love the idea of an Ausbildung (dual vocational training) in Germany, and you've even chosen your field. Now the real question: **how do you find an Ausbildung placement (Ausbildungsplatz) from abroad, before you're even in Germany?** Let's be honest — this is the hardest part of the whole journey. But it's not impossible; thousands of international applicants pull it off every year. This article realistically walks you through where to look, how to apply, and the visa steps that follow the contract.

For the basics I'd suggest first reading the [what is an Ausbildung article](/en/blog/what-is-ausbildung-dual-vocational-training-in-germany-for-foreigners-en) and, to see the easier fields, the [most in-demand Ausbildung fields article](/en/blog/best-ausbildung-fields-in-germany-for-international-students-en).

## The hardest part: getting the contract

In the Ausbildung journey there's the visa, the move, and the language — but the real bottleneck is this: **nothing starts without a signed Ausbildung contract (Ausbildungsvertrag).** The §16a visa, the move, everything hangs on that contract.

Why is it hard? Because you're abroad. The employer can't meet you in person, can't test your German level, and hesitates: "will they really come, will they fit in?" **The good news:** in bottleneck professions (IT, trades, hospitality, care) employers can't fill their placements and are taking on more and more international candidates. So the right field + good German + patient applications = a real chance.

## Where to look: the sources

When hunting for an Ausbildung placement, don't rely on a single place — use several channels in parallel. The most reliable official sources:

| Source | What it's for | Note |
|---|---|---|
| **Bundesagentur für Arbeit** (arbeitsagentur.de) | The government's official job/apprenticeship portal, the largest listing pool | Has an Ausbildung filter; free |
| **IHK Lehrstellenbörse** | Apprenticeship board of the Chambers of Industry and Commerce (IT, trade, hospitality, industry) | Regional chamber websites |
| **HWK Lehrstellenbörse** | Chamber of Crafts apprenticeship board (plumbing, electrical, mechatronics) | For skilled-craft professions |
| **make-it-in-germany.com** | The government's official portal for internationals, visa + job guide | In English; up-to-date official info |
| Employer sites (Careers/Ausbildung pages) | Direct application; big firms recruit via their own portal | Highest response rate |

Strategy: apply **directly** on the "Ausbildung" or "Karriere" pages of large firms (Bosch, Deutsche Bahn, Lufthansa, hotel chains, IT companies). Portals are good for browsing, but a direct application is often more effective.

## The application: CV, Anschreiben and German

German application culture is its own thing. A typical application package:

- **Lebenslauf (CV):** short, chronological (reverse order), one to two pages, a photo is common. Factual and clear.
- **Anschreiben (cover / motivation letter):** "why this profession, why this company, why Germany" — personal and company-specific. Copy-paste is obvious.
- **Diploma/transcript:** translated; your school diploma is usually enough.
- **Language certificate:** if you have a Goethe/telc/ÖSD German certificate, include it by all means — your strongest card.

Prepare everything in **German** if you can. An English application is accepted at some IT/international companies, but the Ausbildung mostly runs in German, and a German application shows you're serious.

## Language requirement: German first

I can't stress this enough: **language is the backbone of the whole journey.** Most employers and the visa generally expect **B1–B2 German**; the Berufsschule (vocational school) runs entirely in German. Without German, neither the contract nor the visa arrives.

Practical plan: reach **at least B1**, ideally B2, before arriving in Germany. Get your certificate before applying — it reassures the employer and strengthens your visa file. Language is not a "I'll sort it out later" thing; it should be **your first investment.**

## §16a visa: steps after the contract

Once you have the contract, the visa is next. For non-EU citizens the path via the **§16a visa (vocational training visa)** works like this (**general outline; be sure to confirm the exact and current steps with the official source**):

1. **Signed Ausbildungsvertrag** — the basis of the visa.
2. **Proof of language** — usually a B1–B2 German certificate.
3. **Financial security** — in a paid *duale* Ausbildung the wage is often deemed sufficient; in **schulische (school-based, unpaid)** training a **Sperrkonto (blocked account)** is usually required.
4. **National visa (type D)** at the German consulate in your country; appointment + documents.
5. After arriving in Germany, conversion into a residence permit at the local **Ausländerbehörde**.

These steps vary by country, consulate and year. **I can't guarantee anything** — for current conditions, fees and document lists, rely on **make-it-in-germany.com** and the official page of the German consulate you'll apply to. You can also find the general logic of the visa process with a job offer in the [work visa with a job offer article](/en/blog/germany-work-visa-with-job-offer-process-timeline-fast-track-en) (the §16a Ausbildung route is a separate path, but the process logic is similar).

## Common mistakes and strategy

The traps applicants from abroad fall into most often:

- **Applying too little.** Five applications won't do it; dozens, even hundreds, are normal. A numbers game.
- **Leaving the language to the last minute.** Most candidates who don't handle B1/B2 early get filtered out.
- **A generic/copied Anschreiben.** Write for each company individually.
- **Relying on a single channel.** Use portal + direct application + chamber boards together.
- **Choosing an over-competitive field outside the bottleneck.** Bottleneck fields like IT/trades/care are far more open to overseas candidates.
- **Giving up too early.** The first rejection emails are normal; the process can take months.

Smart strategy: start early (a year ahead), put the language first, choose a bottleneck field, apply in large numbers and personalized, and follow the official sources.

## Conclusion & honest advice

Finding an Ausbildung placement from abroad is hard, but it's a systematic task — not a matter of luck. The formula is clear: **good German (B1–B2) + bottleneck field + many personalized applications + using official sources correctly.** Once you have the contract, the §16a path largely opens up; we cover salary, life and the path to permanent residence in the [Ausbildung salary and residence article](/en/blog/ausbildung-in-germany-salary-life-and-path-to-permanent-residence-en).

My honest advice: take your language seriously today, plan a year in advance, apply widely, and don't be discouraged by rejections. This path takes effort, but at the end there's a real profession — learning while earning, job security, and a route to residence.

*This article is for general information as of early 2026; visa conditions, sources, salaries and procedures can change. For binding and current information, rely on make-it-in-germany.com, the Bundesagentur für Arbeit, and the official pages of the German consulate you'll apply to. This is not legal advice.*
MD;

        $variants = [
            'tr' => ['slug'=>'how-to-find-and-apply-for-an-ausbildung-in-germany-from-abroad',    'title'=>'Almanya\'da Ausbildung Yeri Bulma ve Yurtdışından Başvuru (2026)', 'excerpt'=>'Yurtdışından Almanya\'da Ausbildung yeri (Ausbildungsplatz) nasıl bulunur? Nerede aranır (IHK/HWK Lehrstellenbörse, Bundesagentur), nasıl başvurulur ve §16a vize adımları — gerçekçi ve dürüst bir rehber.', 'meta_title'=>'Yurtdışından Almanya Ausbildung Yeri Bulma (2026)', 'meta_description'=>'Yurtdışından Almanya\'da Ausbildung yeri bulma: kaynaklar (Bundesagentur, IHK/HWK), başvuru, B1-B2 Almanca ve §16a vize adımları. Dürüst, gerçekçi 2026 rehberi.', 'body'=>$trBody],
            'de' => ['slug'=>'how-to-find-and-apply-for-an-ausbildung-in-germany-from-abroad-de', 'title'=>'Ausbildungsplatz in Deutschland finden und aus dem Ausland bewerben (2026)', 'excerpt'=>'Wie findest du vom Ausland aus einen Ausbildungsplatz in Deutschland? Wo du suchst (IHK/HWK-Lehrstellenbörse, Bundesagentur), wie du dich bewirbst und die §16a-Visumsschritte – ein realistischer, ehrlicher Ratgeber.', 'meta_title'=>'Ausbildungsplatz aus dem Ausland finden (2026)', 'meta_description'=>'Ausbildungsplatz in Deutschland vom Ausland aus finden: Quellen (Bundesagentur, IHK/HWK), Bewerbung, B1-B2 Deutsch und §16a-Visumsschritte. Ehrlicher 2026-Ratgeber.', 'body'=>$deBody],
            'en' => ['slug'=>'how-to-find-and-apply-for-an-ausbildung-in-germany-from-abroad-en', 'title'=>'How to Find and Apply for an Ausbildung in Germany From Abroad (2026)', 'excerpt'=>'How do you find an Ausbildung placement in Germany from abroad? Where to look (IHK/HWK Lehrstellenbörse, Bundesagentur), how to apply, and the §16a visa steps — a realistic, honest guide.', 'meta_title'=>'Find an Ausbildung in Germany From Abroad (2026)', 'meta_description'=>'Finding an Ausbildung placement in Germany from abroad: sources (Bundesagentur, IHK/HWK), applications, B1-B2 German and §16a visa steps. Honest 2026 guide.', 'body'=>$enBody],
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
            'how-to-find-and-apply-for-an-ausbildung-in-germany-from-abroad',
            'how-to-find-and-apply-for-an-ausbildung-in-germany-from-abroad-de',
            'how-to-find-and-apply-for-an-ausbildung-in-germany-from-abroad-en',
        ])->delete();
    }
};
