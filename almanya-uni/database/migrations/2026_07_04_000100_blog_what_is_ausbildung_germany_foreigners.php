<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN): Almanya'da Ausbildung nedir — dual meslek eğitimi genel rehberi (2026).
 * Doğrulandı: Ausbildung = dual meslek eğitimi (şirket + Berufsschule), duale (maaşlı ~1.000-1.300€/ay)
 * vs schulische (okul-tabanlı); yabancılar için §16a Ausbildung vizesi + B1-B2 Almanca; ~325+ tanınmış meslek.
 * Yazar: Halil Yaprakli. Kategori: almanyada-egitim. FK-safe + slug-bazlı idempotent.
 */
return new class extends Migration
{
    public function up(): void
    {
        $groupId = 'a1b10000-1111-4d5f-9f80-aa02bb07dd01';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'almanyada-egitim')->value('id')
            ?? DB::table('categories')->where('slug', 'universities')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
Almanya'da üniversite tek yol değil. **Ausbildung** — yani dual meslek eğitimi — milyonlarca Alman gencinin ve giderek daha fazla yabancının kariyere girdiği köklü bir sistemdir. Bu yazı, akademik bir bölüm değil, **maaşlı, uygulamalı meslek eğitimi** olan Ausbildung'un ne olduğunu, kimler için uygun olduğunu ve yabancı olarak nasıl bir yol izlediğini net biçimde anlatır. (Sayılar 2025/2026 itibarıyla, yaklaşık; başvurmadan önce resmi kaynaklardan doğrula.)

## Ausbildung nedir: dual sistemin mantığı

Ausbildung, **iki ayağı olan** bir meslek eğitimidir; adı zaten buradan gelir ("dual"). Haftanın çoğunu bir **şirkette** (Betrieb) gerçek işi yaparak, kalan günleri ise **meslek okulunda** (Berufsschule) teoriyi öğrenerek geçirirsin. Yani sınıfta ezber yapıp sonra iş aramaya çıkmazsın; **kazanırken öğrenir**, ilk günden bir işletmenin parçası olursun.

Bu sistem Almanya'nın düşük genç işsizliğinin ve güçlü sanayisinin temel taşlarından biridir. İşverenler, kendi ihtiyaç duydukları nitelikli işçiyi bizzat yetiştirdiği için mezunlar iş piyasasında **hemen kullanılabilir** durumdadır. **~325'ten fazla resmi tanınan Ausbildung mesleği** vardır: IT'den (**Fachinformatiker**) mekatroniğe (**Mechatroniker**), otelcilikten (**Hotelfachmann/frau**) tesisat ve ısıtmaya (**Anlagenmechaniker SHK**) kadar.

## İki tür: duale vs schulische Ausbildung

Ausbildung'un iki ana biçimi vardır ve aradaki fark **maaş** ve **yapı** açısından kritiktir:

| Özellik | Duale Ausbildung | Schulische Ausbildung |
|---|---|---|
| Yapı | Şirket + Berufsschule | Ağırlıklı okul-tabanlı (staj eklerle) |
| Maaş | **Evet** (~1.000–1.300€/ay brüt, yıllık artar) | Genelde **yok**, bazen ücret bile ödenir |
| Tipik alanlar | IT, mekatronik, zanaat, ticaret, lojistik | Bazı sağlık, sosyal ve terapi meslekleri |
| Sözleşme | Şirketle imzalı Ausbildung sözleşmesi | Okul kaydı |
| En yaygın mı? | **Evet**, klasik model | Daha dar bir grup meslek |

**Çoğu yabancı için hedef, maaşlı olan duale Ausbildung'dur** çünkü hem geçim sağlar hem de vize açısından daha nettir. Schulische yolda ücret olmayabileceği için maddi güvence (Sperrkonto) gerekebilir.

## Kimler için: üniversitenin gerçek bir alternatifi

Ausbildung şu profillere çok uygundur: eliyle ve pratikle iş yapmayı sevenler, dört yıl teorik eğitim yerine **erken işe girip para kazanmak** isteyenler, üniversite diploması denklik sorunları yaşayanlar ve Almanya'da **hızlı ve sağlam bir oturum yolu** arayanlar.

Türk kültüründe meslek eğitimi bazen üniversiteden daha az prestijli görülür. Dürüst olalım: Almanya'da durum farklıdır. Kalifiye bir **Elektroniker** veya **Anlagenmechaniker**, işsiz kalma riski neredeyse sıfır olan, saygın ve iyi kazanan bir meslektir. Prestij algısıyla iş güvencesini karıştırma.

Üniversiteyi tamamen elemek istemiyorsan iyi haber: Ausbildung sonrası **Meister** yapabilir ya da üniversiteye geçebilirsin — yollar kapalı değil.

## Süre ve sonunda ne alırsın

Bir Ausbildung tipik olarak **3 ile 3,5 yıl** sürer (bazı mesleklerde performansa göre kısaltılabilir). Sonunda oda sınavını (IHK/HWK) geçersen ülke çapında **tanınan bir meslek diploması** alırsın — zanaat mesleklerinde buna **Gesellenbrief**, diğerlerinde resmi sertifika denir.

Bu belge sadece bir kağıt değildir: Almanya'da **nitelikli işçi (Fachkraft)** statüsünün kapısıdır. İşte bu statü, sonraki tüm oturum ve kariyer adımlarını mümkün kılar.

## Yabancı için §16a vize ana hatları

AB dışından geliyorsan, Ausbildung yapmak için tipik olarak **§16a** (Ausbildung vizesi) yolunu izlersin. Ana hatlarıyla gerekenler:

- **İmzalı bir Ausbildung sözleşmesi** (bir Alman şirketinden) — en zor ve en kritik adım budur.
- **Dil yeterliliği**, genellikle **B1–B2 Almanca** (aşağıda ayrıntı var).
- Maaşsız (schulische) yolda **maddi güvence** kanıtı (Sperrkonto gibi).

Vize adımlarının detayı yıla ve konsolosluğa göre değişir; bu yüzden başvuru öncesi **make-it-in-germany.com** ve **Bundesagentur für Arbeit** gibi resmi kaynaklardan güncel şartları mutlaka doğrula. Buradaki bilgi genel bir çerçevedir, garanti değildir.

Ausbildung'a nasıl uygun bir alan seçeceğini [en çok talep gören Ausbildung alanları](/tr/blog/best-ausbildung-fields-in-germany-for-international-students) yazısında, yeri nasıl bulup başvuracağını ise [yurtdışından Ausbildung başvurusu](/tr/blog/how-to-find-and-apply-for-an-ausbildung-in-germany-from-abroad) yazısında adım adım anlatıyoruz.

## Dil şartı (B1-B2) ve kazanırken öğrenme avantajı

**Dil, yabancılar için en büyük engeldir** — bunu dürüstçe söyleyelim. Berufsschule tamamen Almanca yürür, iş yerinde meslektaşlarınla ve müşterilerle Almanca konuşursun. Çoğu şirket ve vize süreci **B1, çoğu zaman B2 Almanca** bekler. Dilin zayıfsa hem sözleşme almak hem de eğitimi tamamlamak zorlaşır. Bu yüzden gerçek hazırlık, başvurudan önce dil kursuna yatırım yapmaktır.

Buna karşılık büyük avantaj şudur: Ausbildung'da **kazanırken öğrenirsin**. Üniversite öğrencisi para öderken sen maaş alırsın, üstelik mezun olduğunda darboğaz mesleklerinde işe alınma (**Übernahme**) ihtimalin çok yüksektir ve buradan **kalıcı oturuma (Niederlassungserlaubnis)** giden yol birkaç yıla iner. Maaş, hayat ve oturum yolunu [Ausbildung maaşı ve kalıcı oturum](/tr/blog/ausbildung-in-germany-salary-life-and-path-to-permanent-residence) yazısında detaylandırıyoruz. Özellikle sağlık alanına ilgin varsa [Almanya'da yabancılar için hemşirelik Ausbildung](/tr/blog/nursing-ausbildung-in-germany-for-internationals-paid-training) yazısı da tam sana göre.

## Sonuç & dürüst tavsiye

Ausbildung, Almanya'da geleceğini kurmak isteyen ama üniversite yolunu zorlu ya da uygun bulmayan yabancılar için **son derece sağlam** bir seçenektir: maaşlı, uygulamalı, iş güvencesi yüksek ve oturuma giden yolu net. Ama iki gerçeği yok sayma: **Almanca** (B1–B2) olmadan bu kapı açılmaz ve en zor kısım **yurtdışından sözleşme almaktır**.

Dürüst tavsiyem: önce Almancana ciddi yatırım yap, darboğaz bir alan seç, sonra sistemli biçimde başvur. Bunu yaparsan Ausbildung, üniversiteden çok daha hızlı bir şekilde seni istihdama ve kalıcı oturuma taşıyabilir.

*Bu yazı 2026 başı itibarıyla genel bilgilendirme amaçlıdır; maaşlar, vize kuralları ve dil şartları yıllara ve eyaletlere göre değişebilir. Başvurmadan önce make-it-in-germany.com, Bundesagentur für Arbeit ve ilgili IHK/HWK gibi resmi kaynaklardan güncel bilgiyi doğrula.*
MD;

        $deBody = <<<'MD'
In Deutschland ist die Universität nicht der einzige Weg. Die **Ausbildung** — die duale Berufsausbildung — ist ein bewährtes System, über das Millionen junger Menschen und immer mehr internationale Bewerber in den Beruf einsteigen. Dieser Artikel erklärt klar, was eine Ausbildung ist, für wen sie passt und wie dein Weg als Ausländer aussieht — es geht nicht um ein akademisches Studium, sondern um eine **bezahlte, praxisnahe Berufsausbildung**. (Zahlen Stand 2025/2026, ungefähr; prüfe sie vor der Bewerbung über offizielle Quellen.)

## Was ist eine Ausbildung: das Prinzip des dualen Systems

Eine Ausbildung ist eine Berufsausbildung mit **zwei Säulen** — daher der Name "dual". Du verbringst den größten Teil der Woche in einem **Betrieb** und erledigst echte Arbeit, die restlichen Tage lernst du die Theorie in der **Berufsschule**. Du paukst also nicht erst im Klassenzimmer und suchst danach einen Job — du **lernst, während du verdienst**, und bist vom ersten Tag an Teil eines Unternehmens.

Dieses System ist eine der Grundlagen der niedrigen Jugendarbeitslosigkeit und der starken Industrie Deutschlands. Weil Betriebe genau die Fachkräfte ausbilden, die sie brauchen, sind Absolventen sofort einsetzbar. Es gibt **über 325 anerkannte Ausbildungsberufe**: von IT (**Fachinformatiker**) über Mechatronik (**Mechatroniker**) und Hotellerie (**Hotelfachmann/-frau**) bis zu Sanitär und Heizung (**Anlagenmechaniker SHK**).

## Zwei Formen: duale vs. schulische Ausbildung

Die Ausbildung gibt es in zwei Hauptformen, und der Unterschied bei **Gehalt** und **Struktur** ist entscheidend:

| Merkmal | Duale Ausbildung | Schulische Ausbildung |
|---|---|---|
| Struktur | Betrieb + Berufsschule | Überwiegend schulisch (mit Praktika) |
| Vergütung | **Ja** (~1.000–1.300€/Monat brutto, jährlich steigend) | Meist **keine**, teils sogar Schulgeld |
| Typische Bereiche | IT, Mechatronik, Handwerk, Handel, Logistik | Manche Gesundheits-, Sozial- und Therapieberufe |
| Vertrag | Ausbildungsvertrag mit dem Betrieb | Schulanmeldung |
| Am verbreitetsten? | **Ja**, das klassische Modell | Kleinere Gruppe von Berufen |

**Für die meisten Ausländer ist die bezahlte duale Ausbildung das Ziel**, weil sie den Lebensunterhalt sichert und beim Visum klarer ist. Beim schulischen Weg kann es sein, dass keine Vergütung fließt, sodass ein finanzieller Nachweis (Sperrkonto) nötig wird.

## Für wen: eine echte Alternative zum Studium

Eine Ausbildung passt besonders zu diesen Profilen: Menschen, die gern praktisch und mit den Händen arbeiten, die statt vier Jahren Theorie lieber **früh in den Beruf einsteigen und Geld verdienen** wollen, die Probleme mit der Anerkennung eines ausländischen Abschlusses haben und die einen **schnellen, soliden Weg zum Aufenthalt** in Deutschland suchen.

In der türkischen Kultur gilt eine Berufsausbildung manchmal als weniger prestigeträchtig als ein Studium. Sei ehrlich: In Deutschland ist das anders. Ein qualifizierter **Elektroniker** oder **Anlagenmechaniker** übt einen angesehenen, gut bezahlten Beruf mit fast null Arbeitslosigkeitsrisiko aus. Verwechsle das Prestige-Image nicht mit der Jobsicherheit.

Und wenn du das Studium nicht ganz ausschließen willst — gute Nachricht: Nach der Ausbildung kannst du den **Meister** machen oder an eine Hochschule wechseln. Die Wege bleiben offen.

## Dauer und was du am Ende bekommst

Eine Ausbildung dauert typischerweise **3 bis 3,5 Jahre** (bei guter Leistung ist in manchen Berufen eine Verkürzung möglich). Wenn du die Kammerprüfung (IHK/HWK) bestehst, erhältst du am Ende einen **bundesweit anerkannten Berufsabschluss** — im Handwerk **Gesellenbrief** genannt, in anderen Bereichen ein offizielles Zeugnis.

Dieses Dokument ist nicht nur ein Stück Papier: Es ist die Tür zum **Fachkraft-Status** in Deutschland. Und genau dieser Status ermöglicht alle weiteren Schritte bei Aufenthalt und Karriere.

## §16a-Visum im Überblick für Ausländer

Wenn du aus einem Nicht-EU-Land kommst, gehst du für eine Ausbildung typischerweise den Weg über **§16a** (Ausbildungsvisum). Grob brauchst du:

- Einen **unterschriebenen Ausbildungsvertrag** (von einem deutschen Betrieb) — das ist der schwierigste und wichtigste Schritt.
- Sprachkenntnisse, in der Regel **B1–B2 Deutsch** (dazu unten mehr).
- Beim unbezahlten (schulischen) Weg einen **finanziellen Nachweis** (etwa ein Sperrkonto).

Die Details der Visumschritte hängen vom Jahr und vom Konsulat ab; prüfe daher vor der Bewerbung die aktuellen Bedingungen unbedingt über offizielle Quellen wie **make-it-in-germany.com** und die **Bundesagentur für Arbeit**. Die Angaben hier sind ein allgemeiner Rahmen, keine Garantie.

Welchen Bereich du wählen solltest, erklären wir im Artikel [gefragteste Ausbildungsbereiche](/de/blog/best-ausbildung-fields-in-germany-for-international-students-de), und wie du einen Platz findest und dich bewirbst, zeigen wir Schritt für Schritt in [Ausbildung aus dem Ausland finden und bewerben](/de/blog/how-to-find-and-apply-for-an-ausbildung-in-germany-from-abroad-de).

## Sprachvoraussetzung (B1-B2) und der Vorteil des Lernens beim Verdienen

**Die Sprache ist für Ausländer die größte Hürde** — sagen wir es ehrlich. Die Berufsschule läuft komplett auf Deutsch, im Betrieb sprichst du mit Kollegen und Kunden Deutsch. Die meisten Betriebe und Visumsverfahren erwarten **B1, oft B2 Deutsch**. Ist dein Deutsch schwach, wird sowohl der Vertragsabschluss als auch das Bestehen der Ausbildung schwer. Die echte Vorbereitung besteht also darin, vor der Bewerbung in einen Sprachkurs zu investieren.

Der große Vorteil dagegen: In der Ausbildung **lernst du, während du verdienst**. Während Studierende zahlen, bekommst du ein Gehalt — und nach dem Abschluss ist die Übernahme in Engpassberufen sehr wahrscheinlich, sodass der Weg zur **Niederlassungserlaubnis** auf wenige Jahre schrumpft. Gehalt, Leben und Aufenthaltsweg vertiefen wir in [Ausbildungsgehalt und Niederlassungserlaubnis](/de/blog/ausbildung-in-germany-salary-life-and-path-to-permanent-residence-de). Wenn dich besonders der Gesundheitsbereich interessiert, ist auch [Pflege-Ausbildung in Deutschland für Internationale](/de/blog/nursing-ausbildung-in-germany-for-internationals-paid-training-de) genau richtig für dich.

## Fazit & ehrlicher Rat

Die Ausbildung ist für Ausländer, die sich in Deutschland eine Zukunft aufbauen wollen, den Uni-Weg aber schwierig oder unpassend finden, eine **äußerst solide** Option: bezahlt, praxisnah, jobsicher und mit klarem Weg zum Aufenthalt. Ignoriere aber zwei Wahrheiten nicht: Ohne **Deutsch** (B1–B2) öffnet sich diese Tür nicht, und der schwierigste Teil ist, **aus dem Ausland einen Vertrag zu bekommen**.

Mein ehrlicher Rat: Investiere zuerst ernsthaft in dein Deutsch, wähle einen Engpassberuf und bewirb dich dann systematisch. Wenn du das tust, kann dich die Ausbildung viel schneller als ein Studium in Beschäftigung und dauerhaften Aufenthalt bringen.

*Dieser Artikel dient der allgemeinen Information Stand Anfang 2026; Gehälter, Visumsregeln und Sprachanforderungen können sich je nach Jahr und Bundesland ändern. Prüfe vor der Bewerbung die aktuellen Informationen über offizielle Quellen wie make-it-in-germany.com, die Bundesagentur für Arbeit und die zuständige IHK/HWK.*
MD;

        $enBody = <<<'MD'
In Germany, university is not the only path. The **Ausbildung** — the dual vocational training system — is a well-established route through which millions of young Germans, and a growing number of internationals, enter their careers. This article explains clearly what an Ausbildung is, who it suits, and what your path looks like as a foreigner. It is not an academic degree — it is **paid, hands-on vocational training**. (Figures are approximate as of 2025/2026; verify them through official sources before applying.)

## What an Ausbildung is: the logic of the dual system

An Ausbildung is vocational training with **two pillars** — which is where "dual" comes from. You spend most of the week at a **company** (Betrieb) doing real work, and the remaining days learning theory at a **vocational school** (Berufsschule). So you don't cram in a classroom and then go job-hunting — you **learn while you earn** and become part of a business from day one.

This system is one of the foundations of Germany's low youth unemployment and strong industry. Because employers train exactly the skilled workers they need, graduates are immediately employable. There are **more than 325 officially recognized Ausbildung professions**: from IT (**Fachinformatiker**) to mechatronics (**Mechatroniker**), from hospitality (**Hotelfachmann/-frau**) to plumbing and heating (**Anlagenmechaniker SHK**).

## Two forms: dual vs. school-based Ausbildung

The Ausbildung comes in two main forms, and the difference in **pay** and **structure** is critical:

| Feature | Dual Ausbildung (duale) | School-based Ausbildung (schulische) |
|---|---|---|
| Structure | Company + Berufsschule | Mostly school-based (with internships) |
| Pay | **Yes** (~€1,000–1,300/month gross, rising yearly) | Usually **none**, sometimes even tuition |
| Typical fields | IT, mechatronics, trades, retail, logistics | Some health, social and therapy professions |
| Contract | Ausbildung contract with the company | School enrollment |
| Most common? | **Yes**, the classic model | A smaller group of professions |

**For most foreigners, the paid dual Ausbildung is the goal**, because it covers your living costs and is clearer for the visa. On the school-based path there may be no pay, so you may need proof of funds (a Sperrkonto, or blocked account).

## Who it's for: a genuine alternative to university

An Ausbildung suits these profiles especially well: people who enjoy practical, hands-on work; those who would rather **start earning early** than spend four years on theory; those facing recognition problems with a foreign degree; and those looking for a **fast, solid route to residence** in Germany.

In Turkish culture, vocational training is sometimes seen as less prestigious than university. Let's be honest: in Germany it's different. A qualified **Elektroniker** or **Anlagenmechaniker** holds a respected, well-paid job with almost zero risk of unemployment. Don't confuse the prestige image with job security.

And if you don't want to rule out university entirely — good news: after an Ausbildung you can do the **Meister** qualification or move on to a university. The paths stay open.

## Duration and what you earn at the end

An Ausbildung typically takes **3 to 3.5 years** (in some professions it can be shortened for strong performance). If you pass the chamber exam (IHK/HWK), you receive a **nationally recognized vocational qualification** — in the trades it's called the **Gesellenbrief**, in other fields an official certificate.

This document is more than a piece of paper: it's the door to **skilled worker (Fachkraft) status** in Germany. And it is precisely that status that unlocks every later step in residence and career.

## The §16a visa in outline for foreigners

If you come from a non-EU country, you typically take the **§16a** route (the Ausbildung visa) to train in Germany. In broad terms you need:

- A **signed Ausbildung contract** (from a German company) — this is the hardest and most important step.
- Language proficiency, usually **B1–B2 German** (more on this below).
- On the unpaid (school-based) path, **proof of funds** (such as a blocked account).

The details of the visa steps vary by year and consulate, so before applying be sure to verify the current requirements through official sources such as **make-it-in-germany.com** and the **Bundesagentur für Arbeit**. The information here is a general framework, not a guarantee.

Which field to choose is covered in [the most in-demand Ausbildung fields](/en/blog/best-ausbildung-fields-in-germany-for-international-students-en), and how to find a place and apply is walked through step by step in [finding and applying for an Ausbildung from abroad](/en/blog/how-to-find-and-apply-for-an-ausbildung-in-germany-from-abroad-en).

## Language requirement (B1-B2) and the earn-while-you-learn advantage

**Language is the biggest hurdle for foreigners** — let's say it plainly. The Berufsschule runs entirely in German, and at work you speak German with colleagues and customers. Most companies and visa processes expect **B1, often B2 German**. If your German is weak, both landing a contract and completing the training become hard. So the real preparation is investing in a language course before you apply.

The big upside, by contrast: in an Ausbildung you **learn while you earn**. While university students pay, you receive a salary — and after graduating, being taken on (Übernahme) in bottleneck professions is very likely, shrinking the path to a **permanent residence (Niederlassungserlaubnis)** to just a few years. We go deeper into pay, life and the residence route in [Ausbildung salary and permanent residence](/en/blog/ausbildung-in-germany-salary-life-and-path-to-permanent-residence-en). And if you're especially drawn to healthcare, [nursing Ausbildung in Germany for internationals](/en/blog/nursing-ausbildung-in-germany-for-internationals-paid-training-en) is a perfect fit.

## Conclusion & honest advice

The Ausbildung is an **extremely solid** option for foreigners who want to build a future in Germany but find the university route difficult or unsuitable: paid, hands-on, job-secure, and with a clear path to residence. But don't ignore two truths: without **German** (B1–B2) this door won't open, and the hardest part is **getting a contract from abroad**.

My honest advice: invest seriously in your German first, choose a bottleneck field, and then apply systematically. Do that, and an Ausbildung can carry you into employment and permanent residence far faster than a university degree.

*This article is for general information as of early 2026; salaries, visa rules and language requirements can change by year and by federal state. Before applying, verify current information through official sources such as make-it-in-germany.com, the Bundesagentur für Arbeit, and the relevant IHK/HWK.*
MD;

        $variants = [
            'tr' => ['slug'=>'what-is-ausbildung-dual-vocational-training-in-germany-for-foreigners',    'title'=>'Almanya\'da Ausbildung Nedir? Dual Meslek Eğitimi Rehberi (2026)', 'excerpt'=>'Ausbildung nedir, duale vs schulische farkı, kimler için uygun, süre ve Gesellenbrief, yabancılar için §16a vizesi ve B1-B2 dil şartı — maaşlı dual meslek eğitiminin dürüst rehberi.', 'meta_title'=>'Almanya\'da Ausbildung Nedir? Dual Meslek Eğitimi (2026)', 'meta_description'=>'Almanya\'da Ausbildung (dual meslek eğitimi) nedir? Duale vs schulische, maaş, §16a vize ve B1-B2 dil şartı — yabancılar için dürüst rehber.', 'body'=>$trBody],
            'de' => ['slug'=>'what-is-ausbildung-dual-vocational-training-in-germany-for-foreigners-de', 'title'=>'Was ist eine Ausbildung in Deutschland? Der Leitfaden (2026)', 'excerpt'=>'Was eine Ausbildung ist, duale vs. schulische Form, für wen sie passt, Dauer und Gesellenbrief, das §16a-Visum für Ausländer und die B1-B2-Sprachanforderung — der ehrliche Leitfaden zur bezahlten dualen Berufsausbildung.', 'meta_title'=>'Was ist eine Ausbildung in Deutschland? Leitfaden 2026', 'meta_description'=>'Was ist eine Ausbildung (duale Berufsausbildung) in Deutschland? Duale vs. schulische, Gehalt, §16a-Visum und B1-B2 Deutsch — der ehrliche Leitfaden.', 'body'=>$deBody],
            'en' => ['slug'=>'what-is-ausbildung-dual-vocational-training-in-germany-for-foreigners-en', 'title'=>'What Is an Ausbildung in Germany? Dual Training Guide (2026)', 'excerpt'=>'What an Ausbildung is, dual vs. school-based, who it suits, duration and the Gesellenbrief, the §16a visa for foreigners and the B1-B2 language requirement — an honest guide to paid dual vocational training.', 'meta_title'=>'What Is an Ausbildung in Germany? Dual Training Guide 2026', 'meta_description'=>'What is an Ausbildung (dual vocational training) in Germany? Dual vs. school-based, pay, the §16a visa and B1-B2 German — an honest guide for foreigners.', 'body'=>$enBody],
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
            'what-is-ausbildung-dual-vocational-training-in-germany-for-foreigners',
            'what-is-ausbildung-dual-vocational-training-in-germany-for-foreigners-de',
            'what-is-ausbildung-dual-vocational-training-in-germany-for-foreigners-en',
        ])->delete();
    }
};
