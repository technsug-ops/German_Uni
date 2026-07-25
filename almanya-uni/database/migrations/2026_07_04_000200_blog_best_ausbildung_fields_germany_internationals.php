<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN): En çok talep gören Ausbildung alanları (2026). Doğrulandı: darboğaz meslekleri
 * (IT/Fachinformatiker, Mechatroniker, Elektroniker, otelcilik, Anlagenmechaniker SHK) daha kolay
 * sözleşme + vize + Übernahme sağlar; maaşlar 2025 itibarıyla yaklaşık, resmi kaynaktan doğrula.
 * Yazar: Halil Yaprakli. Kategori: almanyada-egitim. FK-safe + slug-bazlı idempotent.
 */
return new class extends Migration
{
    public function up(): void
    {
        $groupId = 'a2b20000-2222-4d5f-9f80-aa02bb07dd02';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'almanyada-egitim')->value('id')
            ?? DB::table('categories')->where('slug', 'universities')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
Almanya'da bir Ausbildung (dual meslek eğitimi) yapmaya karar verdin — peki hangi alanda? Bu karar, çoğu insanın sandığından çok daha önemli. Çünkü Almanya'da bazı meslekler **darboğaz meslekleri** (Engpassberufe / Mangelberufe) sayılıyor: işveren bulamıyorlar, bu yüzden yurtdışından gelen bir aday için hem sözleşme almak hem vize almak hem de eğitim sonunda işe alınmak çok daha kolay. Yanlış alan seçersen, aylarca sözleşme bulamayabilirsin.

Bu yazıda 2026 itibarıyla yabancı öğrenciler için en mantıklı, en çok talep gören Ausbildung alanlarını dürüstçe anlatıyorum. Sistemin kendisini merak ediyorsan önce [Ausbildung nedir yazısına](/tr/blog/what-is-ausbildung-dual-vocational-training-in-germany-for-foreigners) göz at.

## Neden alan seçimi bu kadar önemli?

Basit bir mantık: **darboğaz alanı = kolay sözleşme + kolay vize + kolay iş.** Almanya'da işveren yıllardır çırak (Azubi) bulamayan sektörlerde, yurtdışından başvuran bir aday altın değerinde. Bu alanlarda:

- Şirketler yabancı adaylara daha açık, hatta bazıları dil kursunu destekliyor.
- **§16a Ausbildung vizesi** başvurusu, elinde imzalı sözleşme olduğu için daha sorunsuz ilerliyor (yine de resmi adımları make-it-in-germany.com ve Bundesagentur für Arbeit üzerinden doğrula).
- Eğitim sonunda **Übernahme** (şirketin seni işe alması) çok yaygın → nitelikli işçi oturumu → birkaç yılda kalıcı oturum yolu.

Prestijli ama tıka basa dolu bir alan (örneğin medya, tasarım) yerine, "kimsenin gitmek istemediği ama para ve güvence olan" alanı seçmek, yurtdışından gelen biri için genelde daha akıllıca.

## IT ve yazılım: Fachinformatiker

Almanya'nın en büyük dijital açığı burada. **Fachinformatiker** (bilişim uzmanı) Ausbildung'u, özellikle iki yönde çok talep görüyor: *Fachinformatiker für Anwendungsentwicklung* (yazılım geliştirme) ve *Fachinformatiker für Systemintegration* (sistem/ağ yönetimi). 3 yıllık dual eğitim; şirkette çalışırken maaş alırsın.

Bu alan yurtdışından gelenler için ideal çünkü teknik İngilizce iş ortamında yaygın — yine de Berufsschule Almanca olduğu için **B1–B2 Almanca şart.** Eğitim sonrası IT sektörü çok güçlü; dilersen üniversiteye veya Blue Card yoluna da geçebilirsin. Almanya'da tech sektörünün genel resmini [IT/tech alanında çalışma yazısında](/tr/blog/working-in-it-tech-in-germany-as-a-foreigner-blue-card-salary) bulabilirsin.

## Mekatronik ve elektronik: Mechatroniker ve Elektroniker

Almanya sanayi ülkesi; makineleri kuran, bakımını yapan, otomasyonu yöneten teknisyenlere sürekli ihtiyaç var.

- **Mechatroniker:** Mekanik + elektronik + yazılımın kesişimi. Otomotiv, üretim, otomasyon dev sektörler. Klasik bir darboğaz mesleği.
- **Elektroniker:** Birçok alt dalı var (enerji ve bina tekniği, otomasyon teknolojisi, endüstriyel işletme). Enerji dönüşümü (solar, ısı pompaları, şarj altyapısı) nedeniyle talep uzun yıllar yüksek kalacak.

Bu meslekler el becerisi + teknik zeka ister, iyi maaş ve mükemmel iş güvencesi sunar. Eğitim sonrası **Meister** yapıp kendi işini kurma veya tekniker olma yolu da açık.

## Otelcilik, lojistik ve perakende

Hizmet ve ticaret tarafında da büyük açıklar var — bunlar genelde dil olarak biraz daha zorlayıcı ama sözleşme bulması en kolay alanlar arasında:

- **Hotelfachmann/-frau (otelcilik):** Turizm bölgelerinde ciddi personel açığı. Müşteriyle bol temas → Almancan hızla gelişir. Uluslararası zincirlerde İngilizce de işe yarar.
- **Lojistik (Fachkraft für Lagerlogistik):** E-ticaret patlamasıyla depo/lojistik uzmanına büyük talep.
- **Perakende (Kaufmann/-frau im Einzelhandel):** Yaygın, çok sayıda açık pozisyon; iş dünyasına giriş için sağlam bir temel.

Bu alanlar "prestij" tarafında düşük görünse de, sözleşme bulması kolay ve oturum yolu aynı şekilde işliyor.

## Zanaat meslekleri: sayılarla

El işi (Handwerk) tarafı Almanya'nın en ağır darboğazı. İşte 2026 itibarıyla öne çıkan alanlar (maaşlar **2025 itibarıyla yaklaşık aylık brüt Ausbildungsvergütung**, sektör/Tarif'e göre değişir, her yıl artar — doğrula):

| Alan (Meslek) | Aylık maaş (yaklaşık, brüt) | Talep düzeyi |
|---|---|---|
| IT / Fachinformatiker | ~1.000–1.200€ | Çok yüksek |
| Mechatroniker | ~1.000–1.200€ | Çok yüksek |
| Elektroniker | ~1.000–1.200€ | Çok yüksek |
| Anlagenmechaniker SHK (tesisat/ısıtma) | ~900–1.100€ | Aşırı yüksek (darboğaz) |
| Industriemechaniker | ~1.000–1.200€ | Yüksek |
| Hotelfachmann/-frau (otelcilik) | ~900–1.100€ | Yüksek |

**Anlagenmechaniker SHK** (sıhhi tesisat, ısıtma, klima) özellikle vurgulamaya değer: ısı pompası ve enerji dönüşümü nedeniyle Almanya'nın en çok ustaya ihtiyaç duyduğu meslek. **Industriemechaniker** (endüstriyel mekanik) ise fabrikaların bel kemiği. Bu iki alanda sözleşme bulmak, dil şartını karşılıyorsan görece kolay.

## Bakım ve hemşirelik

Sağlık/bakım (Pflege) belki de Almanya'nın en büyük tek açığı — ama bunun kendine has bir dünyası, ayrı diploma tanıma (Anerkennung) süreçleri ve okul-tabanlı (schulische) yapısı var. Bu yüzden burada kısa kesiyorum: eğer bakım/hemşirelik seni çekiyorsa, ayrıntılı ve dürüst bilgiyi [uluslararasılar için hemşirelik Ausbildung yazısında](/tr/blog/nursing-ausbildung-in-germany-for-internationals-paid-training) bulacaksın. Kısaca: maaşlı, çok talep gören, oturum açısından çok güvenli ama fiziksel ve duygusal olarak yorucu bir yol.

## Sonuç & dürüst tavsiye

Alan seçerken üç soruyu sırayla sor: (1) *Bu alan darboğaz mı?* (Evetse vize ve iş çok daha kolay.) (2) *Dil şartını (B1–B2) kaldırabilir miyim?* (Berufsschule Almanca; bu en büyük engel.) (3) *Bu işi yıllarca yapabilir miyim?* (Zanaat meslekleri fiziksel.)

Dürüst olayım: Türk kültüründe zanaat, üniversiteden daha az prestijli görülebilir. Ama IT, mekatronik, elektronik veya SHK gibi bir darboğaz alanında Ausbildung, sana **kazanırken öğrenme**, neredeyse garanti iş, ve birkaç yılda kalıcı oturum yolu sunar — bu, dolu bir bölümde işsiz kalmaktan çok daha sağlam bir hayat planı olabilir.

Bir sonraki adım için: sözleşme bulup başvurmayı [Ausbildung yeri bulma ve başvuru yazısında](/tr/blog/how-to-find-and-apply-for-an-ausbildung-in-germany-from-abroad), maaş ve oturum yolunu ise [maaş, hayat ve kalıcı oturum yazısında](/tr/blog/ausbildung-in-germany-salary-life-and-path-to-permanent-residence) anlattım.

*Bu yazı 2026 başı itibarıyla genel bilgilendirme amaçlıdır; maaşlar, talep düzeyleri ve vize kuralları değişebilir. Kesin ve güncel bilgi için make-it-in-germany.com, Bundesagentur für Arbeit ve ilgili IHK/HWK kaynaklarını doğrula.*
MD;

        $deBody = <<<'MD'
Du hast dich entschieden, in Deutschland eine Ausbildung (duale Berufsausbildung) zu machen — aber in welchem Bereich? Diese Entscheidung ist wichtiger, als die meisten denken. Denn manche Berufe gelten als **Engpass- oder Mangelberufe**: Die Betriebe finden keine Azubis, und deshalb ist es für einen Bewerber aus dem Ausland viel leichter, einen Vertrag zu bekommen, ein Visum zu erhalten und nach der Ausbildung übernommen zu werden. Wählst du den falschen Bereich, suchst du vielleicht monatelang vergeblich einen Ausbildungsplatz.

In diesem Artikel erkläre ich dir ehrlich, welche Ausbildungsbereiche 2026 für internationale Bewerber am sinnvollsten und gefragtesten sind. Wenn du das System selbst noch nicht kennst, lies zuerst den [Artikel darüber, was eine Ausbildung ist](/de/blog/what-is-ausbildung-dual-vocational-training-in-germany-for-foreigners-de).

## Warum die Wahl des Bereichs so wichtig ist

Die Logik ist einfach: **Mangelberuf = leichter Vertrag + leichteres Visum + sicherer Job.** In Branchen, in denen die Betriebe seit Jahren keine Azubis finden, ist ein Bewerber aus dem Ausland Gold wert. In diesen Bereichen gilt:

- Die Betriebe sind offener für internationale Bewerber, manche unterstützen sogar den Sprachkurs.
- Der Antrag auf das **§16a-Ausbildungsvisum** läuft mit einem unterschriebenen Vertrag reibungsloser (prüfe die offiziellen Schritte trotzdem über make-it-in-germany.com und die Bundesagentur für Arbeit).
- Nach der Ausbildung ist die **Übernahme** sehr üblich → Aufenthalt als Fachkraft → in einigen Jahren der Weg zur Niederlassungserlaubnis.

Statt eines prestigeträchtigen, aber überlaufenen Bereichs (etwa Medien oder Design) den Bereich zu wählen, "in den keiner will, in dem es aber Geld und Sicherheit gibt", ist für jemanden aus dem Ausland meist die klügere Entscheidung.

## IT und Software: Fachinformatiker

Hier ist die größte digitale Lücke Deutschlands. Die Ausbildung zum **Fachinformatiker** ist besonders in zwei Richtungen sehr gefragt: *Fachinformatiker für Anwendungsentwicklung* (Softwareentwicklung) und *Fachinformatiker für Systemintegration* (System- und Netzwerkverwaltung). Drei Jahre duale Ausbildung; du bekommst Gehalt, während du im Betrieb arbeitest.

Dieser Bereich ist für Menschen aus dem Ausland ideal, weil technisches Englisch im Arbeitsumfeld verbreitet ist — trotzdem ist **B1–B2-Deutsch Pflicht**, da die Berufsschule auf Deutsch läuft. Nach der Ausbildung ist die IT-Branche sehr stark; du kannst auch an die Hochschule wechseln oder den Blue-Card-Weg gehen. Das Gesamtbild der Tech-Branche findest du im [Artikel über das Arbeiten in IT und Tech](/de/blog/working-in-it-tech-in-germany-as-a-foreigner-blue-card-salary-de).

## Mechatronik und Elektronik: Mechatroniker und Elektroniker

Deutschland ist ein Industrieland; Techniker, die Maschinen aufbauen, warten und die Automatisierung steuern, werden ständig gebraucht.

- **Mechatroniker:** Schnittstelle aus Mechanik, Elektronik und Software. Automobil, Produktion und Automatisierung sind riesige Branchen. Ein klassischer Mangelberuf.
- **Elektroniker:** Es gibt viele Fachrichtungen (Energie- und Gebäudetechnik, Automatisierungstechnik, Betriebstechnik). Wegen der Energiewende (Solar, Wärmepumpen, Ladeinfrastruktur) bleibt die Nachfrage über viele Jahre hoch.

Diese Berufe verlangen handwerkliches Geschick und technisches Verständnis, bieten gutes Gehalt und ausgezeichnete Jobsicherheit. Nach der Ausbildung steht dir auch der Weg zum **Meister** (eigener Betrieb) oder zum Techniker offen.

## Hotellerie, Logistik und Einzelhandel

Auch im Service- und Handelsbereich gibt es große Lücken — sprachlich oft etwas anspruchsvoller, aber unter den Bereichen, in denen ein Vertrag am leichtesten zu finden ist:

- **Hotelfachmann/-frau:** In Tourismusregionen herrscht ernster Personalmangel. Viel Kundenkontakt → dein Deutsch entwickelt sich schnell. In internationalen Ketten hilft auch Englisch.
- **Logistik (Fachkraft für Lagerlogistik):** Durch den E-Commerce-Boom ist die Nachfrage nach Lager- und Logistikfachkräften groß.
- **Einzelhandel (Kaufmann/-frau im Einzelhandel):** Weit verbreitet, viele offene Stellen; eine solide Grundlage für den Einstieg in die Arbeitswelt.

Diese Bereiche gelten beim Prestige als niedriger, aber ein Vertrag ist leicht zu finden, und der Aufenthaltsweg funktioniert genauso.

## Handwerksberufe: mit Zahlen

Das Handwerk ist Deutschlands schwerster Engpass. Hier die 2026 herausragenden Bereiche (Gehälter sind die **ungefähre monatliche Brutto-Ausbildungsvergütung Stand 2025**, variieren je nach Branche/Tarif und steigen jedes Jahr — bitte prüfen):

| Bereich (Beruf) | Monatsgehalt (ca., brutto) | Nachfrage |
|---|---|---|
| IT / Fachinformatiker | ~1.000–1.200€ | Sehr hoch |
| Mechatroniker | ~1.000–1.200€ | Sehr hoch |
| Elektroniker | ~1.000–1.200€ | Sehr hoch |
| Anlagenmechaniker SHK (Sanitär/Heizung) | ~900–1.100€ | Extrem hoch (Engpass) |
| Industriemechaniker | ~1.000–1.200€ | Hoch |
| Hotelfachmann/-frau | ~900–1.100€ | Hoch |

Der **Anlagenmechaniker SHK** (Sanitär, Heizung, Klima) verdient besondere Erwähnung: Wegen Wärmepumpen und Energiewende ist das der Beruf, in dem Deutschland am dringendsten Fachkräfte braucht. Der **Industriemechaniker** wiederum ist das Rückgrat der Fabriken. In diesen beiden Bereichen einen Vertrag zu finden ist vergleichsweise leicht, wenn du die Sprachanforderung erfüllst.

## Pflege und Krankenpflege

Die Pflege ist vielleicht die größte Einzellücke Deutschlands — aber sie hat ihre eigene Welt, eigene Verfahren zur Anerkennung des Abschlusses und eine schulische Struktur. Deshalb halte ich mich hier kurz: Wenn dich die Pflege reizt, findest du ausführliche und ehrliche Informationen im [Artikel über die Pflege-Ausbildung für Internationale](/de/blog/nursing-ausbildung-in-germany-for-internationals-paid-training-de). Kurz gesagt: bezahlt, sehr gefragt, aufenthaltsrechtlich sehr sicher, aber körperlich und emotional anstrengend.

## Fazit & ehrlicher Rat

Stelle dir bei der Wahl des Bereichs drei Fragen in dieser Reihenfolge: (1) *Ist das ein Mangelberuf?* (Wenn ja, sind Visum und Job viel leichter.) (2) *Schaffe ich die Sprachanforderung (B1–B2)?* (Die Berufsschule ist auf Deutsch; das ist die größte Hürde.) (3) *Kann ich diesen Beruf über Jahre ausüben?* (Handwerksberufe sind körperlich.)

Sei ehrlich zu dir: In manchen Kulturen gilt das Handwerk als weniger angesehen als ein Studium. Aber eine Ausbildung in einem Mangelberuf wie IT, Mechatronik, Elektronik oder SHK bietet dir **Lernen bei vollem Gehalt**, einen nahezu sicheren Job und in wenigen Jahren den Weg zur Niederlassungserlaubnis — das kann ein weit solideres Lebensmodell sein als Arbeitslosigkeit nach einem überfüllten Studiengang.

Für den nächsten Schritt: Wie du einen Vertrag findest und dich bewirbst, steht im [Artikel zum Finden und Bewerben um eine Ausbildung](/de/blog/how-to-find-and-apply-for-an-ausbildung-in-germany-from-abroad-de), und Gehalt sowie Aufenthaltsweg erkläre ich im [Artikel über Gehalt, Leben und Weg zur Niederlassungserlaubnis](/de/blog/ausbildung-in-germany-salary-life-and-path-to-permanent-residence-de).

*Dieser Artikel dient der allgemeinen Information mit Stand Anfang 2026; Gehälter, Nachfrage und Visaregeln können sich ändern. Für genaue und aktuelle Angaben prüfe make-it-in-germany.com, die Bundesagentur für Arbeit und die zuständigen IHK/HWK-Quellen.*
MD;

        $enBody = <<<'MD'
You have decided to do an Ausbildung (dual vocational training) in Germany — but in which field? This decision matters far more than most people think. That is because some occupations count as **shortage occupations** (Engpass- or Mangelberufe): employers cannot find apprentices, so for a candidate coming from abroad it is much easier to get a contract, obtain a visa, and be hired after training. Pick the wrong field, and you may search for a training place for months.

In this article I honestly walk you through the fields that make the most sense and are most in demand for international students in 2026. If you are not yet familiar with the system itself, first read the [article on what an Ausbildung is](/en/blog/what-is-ausbildung-dual-vocational-training-in-germany-for-foreigners-en).

## Why choosing the field matters so much

The logic is simple: **shortage field = easier contract + easier visa + secure job.** In sectors where employers have struggled to find apprentices (Azubis) for years, a candidate applying from abroad is worth their weight in gold. In these fields:

- Employers are more open to international candidates, and some even support your language course.
- The **§16a Ausbildung visa** application runs more smoothly with a signed contract in hand (still verify the official steps via make-it-in-germany.com and the Bundesagentur für Arbeit).
- After training, **Übernahme** (the company hiring you) is very common → skilled-worker residence → in a few years, the path to permanent residence.

Instead of a prestigious but overcrowded field (media or design, say), choosing the field that "nobody wants to enter but where the money and security are" is usually the smarter move for someone coming from abroad.

## IT and software: Fachinformatiker

This is Germany's largest digital gap. The **Fachinformatiker** (IT specialist) Ausbildung is especially in demand in two directions: *Fachinformatiker für Anwendungsentwicklung* (software development) and *Fachinformatiker für Systemintegration* (systems and network administration). Three years of dual training; you earn a salary while working at the company.

This field is ideal for people coming from abroad because technical English is common in the workplace — but **B1–B2 German is mandatory** because the Berufsschule runs in German. After training the IT sector is very strong; you can also move on to university or the Blue Card route. You will find the bigger picture of the tech sector in the [article on working in IT and tech](/en/blog/working-in-it-tech-in-germany-as-a-foreigner-blue-card-salary-en).

## Mechatronics and electronics: Mechatroniker and Elektroniker

Germany is an industrial country; technicians who build machines, maintain them, and manage automation are needed constantly.

- **Mechatroniker:** The intersection of mechanics, electronics and software. Automotive, manufacturing and automation are huge sectors. A classic shortage occupation.
- **Elektroniker:** There are many specialisations (energy and building technology, automation technology, industrial operations). Because of the energy transition (solar, heat pumps, charging infrastructure), demand will stay high for many years.

These trades require manual skill plus technical thinking, and offer good pay and excellent job security. After training, the path to becoming a **Meister** (running your own business) or a technician is also open.

## Hospitality, logistics and retail

The service and trade side has big gaps too — often a bit more demanding on language, but among the fields where a contract is easiest to find:

- **Hotelfachmann/-frau (hospitality):** Serious staff shortages in tourism regions. Lots of customer contact → your German improves fast. In international chains, English helps too.
- **Logistics (Fachkraft für Lagerlogistik):** The e-commerce boom has created strong demand for warehouse and logistics specialists.
- **Retail (Kaufmann/-frau im Einzelhandel):** Widespread, with many open positions; a solid foundation for entering the working world.

These fields look lower on "prestige," but a contract is easy to find and the residence path works exactly the same way.

## Skilled trades: with numbers

The skilled trades (Handwerk) are Germany's most severe bottleneck. Here are the fields standing out in 2026 (salaries are the **approximate monthly gross Ausbildungsvergütung as of 2025**, vary by sector/Tarif, and rise every year — please verify):

| Field (occupation) | Monthly salary (approx., gross) | Demand level |
|---|---|---|
| IT / Fachinformatiker | ~€1,000–1,200 | Very high |
| Mechatroniker | ~€1,000–1,200 | Very high |
| Elektroniker | ~€1,000–1,200 | Very high |
| Anlagenmechaniker SHK (plumbing/heating) | ~€900–1,100 | Extremely high (shortage) |
| Industriemechaniker | ~€1,000–1,200 | High |
| Hotelfachmann/-frau (hospitality) | ~€900–1,100 | High |

The **Anlagenmechaniker SHK** (sanitation, heating, air conditioning) deserves special mention: because of heat pumps and the energy transition, it is the occupation in which Germany most urgently needs skilled workers. The **Industriemechaniker** (industrial mechanic), in turn, is the backbone of the factories. Finding a contract in these two fields is comparatively easy if you meet the language requirement.

## Care and nursing

Care and nursing (Pflege) is perhaps Germany's single biggest gap — but it has its own world, its own qualification-recognition (Anerkennung) processes and a school-based structure. So I will keep it short here: if care or nursing appeals to you, you will find detailed and honest information in the [article on nursing Ausbildung for internationals](/en/blog/nursing-ausbildung-in-germany-for-internationals-paid-training-en). In short: paid, in very high demand, very secure for residence, but physically and emotionally demanding.

## Conclusion & honest advice

When choosing a field, ask yourself three questions in this order: (1) *Is this a shortage occupation?* (If so, visa and job are much easier.) (2) *Can I meet the language requirement (B1–B2)?* (The Berufsschule is in German; this is the biggest hurdle.) (3) *Can I do this job for years?* (Skilled trades are physical.)

Let me be honest: in some cultures, a skilled trade may be seen as less prestigious than a university degree. But an Ausbildung in a shortage field such as IT, mechatronics, electronics or SHK gives you **learning while earning a full salary**, a near-guaranteed job, and within a few years the path to permanent residence — which can be a far more solid life plan than being unemployed after an overcrowded degree.

For your next step: how to find a contract and apply is covered in the [article on finding and applying for an Ausbildung](/en/blog/how-to-find-and-apply-for-an-ausbildung-in-germany-from-abroad-en), and salary and the residence path are covered in the [article on salary, life and the path to permanent residence](/en/blog/ausbildung-in-germany-salary-life-and-path-to-permanent-residence-en).

*This article is for general information as of early 2026; salaries, demand levels and visa rules can change. For accurate and current details, verify via make-it-in-germany.com, the Bundesagentur für Arbeit and the relevant IHK/HWK sources.*
MD;

        $variants = [
            'tr' => ['slug'=>'best-ausbildung-fields-in-germany-for-international-students',    'title'=>'Almanya\'da En Çok Talep Gören Ausbildung Alanları (2026)', 'excerpt'=>'Almanya\'da yabancılar için en mantıklı Ausbildung alanları: IT/Fachinformatiker, mekatronik, elektronik, otelcilik ve SHK gibi darboğaz meslekleri — maaş tablosu ve dürüst tavsiye (2026).', 'meta_title'=>'En Çok Talep Gören Ausbildung Alanları (2026)', 'meta_description'=>'Yabancılar için Almanya\'da en çok talep gören Ausbildung alanları: IT, mekatronik, elektronik, otelcilik, SHK. Darboğaz = kolay vize+iş. Maaş tablosu, 2026.', 'body'=>$trBody],
            'de' => ['slug'=>'best-ausbildung-fields-in-germany-for-international-students-de', 'title'=>'Die gefragtesten Ausbildungsbereiche in Deutschland (2026)', 'excerpt'=>'Die sinnvollsten Ausbildungsbereiche für Internationale in Deutschland: IT/Fachinformatiker, Mechatronik, Elektronik, Hotellerie und SHK als Mangelberufe — Gehaltstabelle und ehrlicher Rat (2026).', 'meta_title'=>'Gefragteste Ausbildungsbereiche in Deutschland (2026)', 'meta_description'=>'Die gefragtesten Ausbildungsbereiche für Internationale: IT, Mechatronik, Elektronik, Hotellerie, SHK. Mangelberuf = leichteres Visum+Job. Gehaltstabelle, 2026.', 'body'=>$deBody],
            'en' => ['slug'=>'best-ausbildung-fields-in-germany-for-international-students-en', 'title'=>'The Most In-Demand Ausbildung Fields in Germany (2026)', 'excerpt'=>'The most sensible Ausbildung fields for internationals in Germany: IT/Fachinformatiker, mechatronics, electronics, hospitality and SHK as shortage occupations — salary table and honest advice (2026).', 'meta_title'=>'Most In-Demand Ausbildung Fields in Germany (2026)', 'meta_description'=>'The most in-demand Ausbildung fields for internationals: IT, mechatronics, electronics, hospitality, SHK. Shortage = easier visa+job. Salary table, 2026.', 'body'=>$enBody],
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
            'best-ausbildung-fields-in-germany-for-international-students',
            'best-ausbildung-fields-in-germany-for-international-students-de',
            'best-ausbildung-fields-in-germany-for-international-students-en',
        ])->delete();
    }
};
