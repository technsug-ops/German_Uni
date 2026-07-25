<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN): Almanya'da Fizyoterapist Olmak — yabancılar için genel rehber (2026).
 * Doğrulandı: Physiotherapeut düzenlenmiş sağlık mesleğidir (staatliche Anerkennung / Erlaubnis
 * zum Führen der Berufsbezeichnung gerekir); iki yol — yurtdışı diploma tanınması (Anerkennung)
 * ve sıfırdan eğitim (3-yıl Ausbildung veya Bachelor). B2 Almanca pratikte şart, İngilizce program yok.
 * Yazar: Halil Yaprakli. Kategori: almanyada-egitim. slug-bazlı idempotent.
 */
return new class extends Migration
{
    public function up(): void
    {
        $groupId = 'c3d10000-1111-4faf-9f00-cc0add10aa01';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'almanyada-egitim')->value('id')
            ?? DB::table('categories')->where('slug', 'universities')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
Almanya'da fizyoterapist (Physiotherapeut) olmak, sağlık alanında kariyer düşünen yabancılar için en gerçekçi ve talep gören yollardan biri. Ama internetteki basit "gel, çalış" anlatılarının aksine, bu meslek **düzenlenmiş bir sağlık mesleği** ve kendine has kuralları var. Bu rehber, sıfırdan mı başlayacağını yoksa mevcut diplomanı mı tanıtacağını netleştirmen için yazıldı.

## Neden Almanya: talep ve darboğaz
Almanya'nın nüfusu yaşlanıyor ve bu, fizyoterapiye olan talebi sürekli yukarı çekiyor. Yaşlı bakımı, rehabilitasyon klinikleri, ortopedik cerrahi sonrası tedaviler ve spor yaralanmaları — hepsi fizyoterapiste ihtiyaç duyuyor. 2025/2026 itibarıyla birçok eyalette fizyoterapistler **darboğaz meslekleri (Mangelberufe)** arasında sayılıyor; yani iş bulmak, çoğu meslekle kıyaslandığında **belirgin şekilde kolay**. Bu, hem iş güvencesi hem de kalıcı oturuma giden yol açısından yabancılar için büyük bir avantaj.

Çalışma alanları da geniş: özel fizyoterapi muayenehaneleri (Physiopraxis), hastaneler, **rehabilitasyon klinikleri**, yaşlı bakım kurumları, spor kulüpleri ve — tanınman tamamlandıktan sonra — **kendi muayeneni açma** imkânı. Yani sadece iş bulmak değil, hangi ortamda çalışacağını seçme lüksün de var. Büyük şehirlerde de, küçük kasabalarda da ihtiyaç var; bu esneklik yabancılar için nadir bir avantaj.

Ama dürüst olalım: talebin yüksek olması maaşın yüksek olduğu anlamına gelmiyor. Bu gerçeğe [maaş, dil ve çalışma gerçeği yazısında](/tr/blog/working-as-a-physiotherapist-in-germany-salary-language-and-reality) ayrıntılı değiniyoruz.

## İki yol: mevcut diplomayı tanıtmak mı, sıfırdan eğitim mi?
Buraya nasıl geldiğin, izleyeceğin yolu tamamen belirliyor. **İki temel yol** var ve bunları karıştırmamak çok önemli.

| Kriter | Yol 1: Tanınma (Anerkennung) | Yol 2: Sıfırdan eğitim |
|---|---|---|
| Kime uygun? | Ülkesinde **zaten fizyoterapist** olanlar | Bu mesleğe **sıfırdan** başlayanlar |
| Ne yapılır? | Yurtdışı diploma denkliği (Gleichwertigkeit) | 3-yıl Ausbildung **veya** Bachelor |
| Süre | Genelde daha kısa (eksik varsa uzayabilir) | 3–4 yıl civarı |
| Eksik varsa | Kenntnisprüfung veya Anpassungslehrgang | — |
| Dil şartı | B2 Almanca (bazen + Fachsprachprüfung) | B2 Almanca |
| Sonuç | Erlaubnis (meslek unvanı izni) | Erlaubnis (meslek unvanı izni) |

Zaten fizyoterapistsen, doğru adres **tanınma yolu**: adımları [Anerkennung rehberimizde](/tr/blog/getting-your-foreign-physiotherapy-qualification-recognized-in-germany-anerkennung) anlattık. Sıfırdan başlıyorsan, [eğitim yolları yazısı](/tr/blog/physiotherapy-training-and-study-in-germany-for-internationals) senin başlangıç noktan.

## B2 Almanca neden pazarlık konusu değil?
Bu mesleğin en çok yanlış anlaşılan yönü şu: **İngilizce ile fizyoterapist olamazsın.** Almanya'da İngilizce fizyoterapi eğitimi pratikte yok denecek kadar nadir ve mesleği icra ederken sürekli hastalarla konuşuyorsun — ağrı tarif etmek, egzersiz anlatmak, yaşlı bir hastayı yönlendirmek Almanca yürür.

Bu yüzden hem tanınma hem de eğitim yolunda **en az B2 seviyesi Almanca** kural. Bazı tanınma süreçlerinde ayrıca sağlık alanına özel dil sınavı (Fachsprachprüfung) istenebilir. Pratik tavsiye: **dil öğrenmeyi başlangıç noktan yap**, çünkü diğer her adım buna bağlı.

## Meslek düzenlenmiş: staatliche Anerkennung gerçeği
Almanya'da "fizyoterapist" unvanını sadece istediğin için kullanamazsın. Bu **korumalı bir meslek unvanı** ve yasal olarak çalışabilmen için devletten **"Erlaubnis zum Führen der Berufsbezeichnung"** (meslek unvanını taşıma izni) almalısın. Bu izin, staatliche Anerkennung sürecinin sonunda verilir.

Yani ister sıfırdan eğit, ister diplomanı tanıt — her iki yol da aynı kapıya çıkar: **eyaletin yetkili makamından resmi izin.** Bu bürokrasi can sıkıcı görünebilir ama aslında mesleğin değerini ve iş güvenceni koruyan şey de bu.

Önemli bir ayrıntı: tanınma ve denklik kuralları **eyaletten eyalete (Bundesland) küçük farklılıklar** gösterebilir. Başvuracağın makam, yaşayacağın ya da çalışacağın eyalete göre belirlenir. Bu yüzden genel blog yazılarını yol gösterici olarak kullan, ama nihai kararını her zaman ilgili eyaletin resmi tanınma makamının güncel bilgisiyle ver.

## Vize ana hatları (resmi kaynağı doğrula)
Vize durumun, hangi yolda olduğuna bağlı. Genel hatlarıyla, 2026 itibarıyla:

- **Tanınma amaçlı vize:** Diploman denklik değerlendirmesindeyken veya eksikleri (Anpassungslehrgang) tamamlamak için gelmen gerekiyorsa, tanınma amaçlı bir oturum yolu mümkün olabilir (§16d benzeri düzenlemeler).
- **İş teklifiyle nitelikli işçi vizesi:** Tanınman tamamlandıysa ve iş teklifin varsa, nitelikli/vasıflı işçi olarak gelebilirsin; bazı durumlarda **hızlandırılmış prosedür** işletilebilir. Sürecin genel işleyişini [iş teklifiyle çalışma vizesi yazımızda](/tr/blog/germany-work-visa-with-job-offer-process-timeline-fast-track) bulabilirsin.
- **Eğitim vizesi:** Ausbildung veya Bachelor için geliyorsan farklı bir oturum türü söz konusu.

Bu adımlar kişisel duruma ve eyalete göre değişir; **kesin bilgi için mutlaka Almanya konsolosluğu ve eyaletin tanınma makamı** ile teyit et.

## İlk adımlar
1. **Durumunu belirle:** Zaten fizyoterapist misin, yoksa sıfırdan mı başlıyorsun? Yolun bu cevaba bağlı.
2. **Almancaya başla:** B2 uzun bir yol; bugün başlaman en akıllıcası.
3. **Resmi tanınma bilgisi al:** `anerkennung-in-deutschland.de` ve ilgili eyaletin tanınma makamı senin resmi referansın.
4. **Küme yazılarını oku:** [Anerkennung](/tr/blog/getting-your-foreign-physiotherapy-qualification-recognized-in-germany-anerkennung), [eğitim yolları](/tr/blog/physiotherapy-training-and-study-in-germany-for-internationals) ve [maaş/gerçek](/tr/blog/working-as-a-physiotherapist-in-germany-salary-language-and-reality).
5. **Benzer meslekleri kıyasla:** Sağlık alanında karar veriyorsan [hemşirelik rehberi](/tr/blog/becoming-a-nurse-in-germany-as-a-foreigner) de faydalı bir karşılaştırma noktası.

## Sonuç ve dürüst tavsiye
Almanya'da fizyoterapist olmak, **güçlü talebi ve iş güvencesi** olan gerçekçi bir hedef. Yabancılar için en büyük avantaj: iş bulmak zor değil ve kalıcı oturuma açılan net bir yol var. Zaten fizyoterapistsen, tanınma yolu genellikle en hızlı seçenek. Sıfırdan başlıyorsan, 3–4 yıllık bir yatırıma hazır olmalısın.

Ama dürüst gerçek şu: **maaş, harcanan emeğe göre mütevazı** ve **B2 Almanca gerçek bir engel** — bunu küçümseme. Eğer bu iki gerçeği kabullenip yola çıkarsan, Almanya fizyoterapi kariyeri için ödüllendirici ve istikrarlı bir ülke.

*Bu yazı 2026 başı itibarıyla genel bilgilendirme amaçlıdır; vize, tanınma ve maaş kuralları sık değişir. Sayılar yaklaşıktır. Kişisel kararından önce eyaletin tanınma makamı, anerkennung-in-deutschland.de ve Almanya konsolosluğu gibi resmi kaynaklardan güncel bilgiyi doğrula.*
MD;

        $deBody = <<<'MD'
Physiotherapeut in Deutschland zu werden ist für Ausländer einer der realistischsten und gefragtesten Wege in eine Gesundheitskarriere. Doch anders als es einfache "Komm und arbeite"-Geschichten im Internet suggerieren, ist dies ein **reglementierter Gesundheitsberuf** mit eigenen Regeln. Dieser Leitfaden hilft dir zu klären, ob du von vorne anfängst oder dein bestehendes Diplom anerkennen lässt.

## Warum Deutschland: Nachfrage und Engpass
Deutschlands Bevölkerung altert, und das treibt die Nachfrage nach Physiotherapie stetig nach oben. Altenpflege, Rehakliniken, Behandlungen nach orthopädischen Operationen und Sportverletzungen — überall werden Physiotherapeuten gebraucht. Stand 2025/2026 zählen Physiotherapeuten in vielen Bundesländern zu den **Mangelberufen**; einen Job zu finden ist also im Vergleich zu vielen anderen Berufen **deutlich leichter**. Für Ausländer ist das ein großer Vorteil — sowohl für die Jobsicherheit als auch für den Weg zur dauerhaften Aufenthaltserlaubnis.

Aber sei ehrlich zu dir: Hohe Nachfrage bedeutet nicht hohes Gehalt. Diese Realität behandeln wir ausführlich im [Beitrag zu Gehalt, Sprache und Alltag](/de/blog/working-as-a-physiotherapist-in-germany-salary-language-and-reality-de).

## Zwei Wege: bestehendes Diplom anerkennen oder von vorne lernen?
Wie du hierher kommst, bestimmt deinen Weg vollständig. Es gibt **zwei grundlegende Wege**, und du solltest sie nicht verwechseln.

| Kriterium | Weg 1: Anerkennung | Weg 2: Ausbildung von vorne |
|---|---|---|
| Für wen? | Wer im Herkunftsland **schon Physiotherapeut** ist | Wer **bei null** anfängt |
| Was passiert? | Gleichwertigkeitsprüfung des Auslandsdiploms | 3-jährige Ausbildung **oder** Bachelor |
| Dauer | Meist kürzer (länger, wenn etwas fehlt) | Etwa 3–4 Jahre |
| Bei Defiziten | Kenntnisprüfung oder Anpassungslehrgang | — |
| Sprache | B2 Deutsch (manchmal + Fachsprachprüfung) | B2 Deutsch |
| Ergebnis | Erlaubnis (Berufsbezeichnung) | Erlaubnis (Berufsbezeichnung) |

Wenn du schon Physiotherapeut bist, ist der **Anerkennungsweg** deine Adresse: Die Schritte erklären wir im [Anerkennungs-Leitfaden](/de/blog/getting-your-foreign-physiotherapy-qualification-recognized-in-germany-anerkennung-de). Wenn du bei null anfängst, ist der [Beitrag zu den Ausbildungswegen](/de/blog/physiotherapy-training-and-study-in-germany-for-internationals-de) dein Startpunkt.

## Warum B2 Deutsch nicht verhandelbar ist
Der am häufigsten missverstandene Punkt dieses Berufs: **Mit Englisch wirst du kein Physiotherapeut.** Eine englischsprachige Physiotherapieausbildung gibt es in Deutschland praktisch nicht, und im Berufsalltag sprichst du ständig mit Patienten — Schmerzen beschreiben, Übungen erklären, eine ältere Person anleiten läuft alles auf Deutsch.

Deshalb gilt sowohl im Anerkennungs- als auch im Ausbildungsweg **mindestens B2 Deutsch** als Regel. In manchen Anerkennungsverfahren wird zusätzlich eine Fachsprachprüfung für das Gesundheitswesen verlangt. Praktischer Rat: **Mach das Deutschlernen zu deinem Ausgangspunkt**, denn jeder weitere Schritt hängt davon ab.

## Reglementierter Beruf: die Realität der staatlichen Anerkennung
In Deutschland darfst du die Bezeichnung "Physiotherapeut" nicht einfach so führen, weil du willst. Es ist eine **geschützte Berufsbezeichnung**, und um legal arbeiten zu dürfen, brauchst du vom Staat die **"Erlaubnis zum Führen der Berufsbezeichnung"**. Diese Erlaubnis wird am Ende der staatlichen Anerkennung erteilt.

Ob du also von vorne lernst oder dein Diplom anerkennen lässt — beide Wege führen zur selben Tür: **die offizielle Erlaubnis von der zuständigen Landesbehörde.** Diese Bürokratie mag lästig wirken, aber genau sie schützt den Wert des Berufs und deine Jobsicherheit.

## Visum im Überblick (offizielle Quelle prüfen)
Deine Visumssituation hängt davon ab, auf welchem Weg du bist. Grob gesagt, Stand 2026:

- **Visum zur Anerkennung:** Wenn dein Diplom in der Gleichwertigkeitsprüfung ist oder du zum Ausgleich von Defiziten (Anpassungslehrgang) einreisen musst, kann ein Aufenthalt zur Anerkennung möglich sein (Regelungen ähnlich § 16d).
- **Fachkräftevisum mit Jobangebot:** Ist deine Anerkennung abgeschlossen und hast du ein Jobangebot, kannst du als Fachkraft einreisen; in manchen Fällen greift ein **beschleunigtes Verfahren**. Den allgemeinen Ablauf findest du im [Beitrag zum Arbeitsvisum mit Jobangebot](/de/blog/germany-work-visa-with-job-offer-process-timeline-fast-track).
- **Ausbildungsvisum:** Kommst du für Ausbildung oder Bachelor, gilt eine andere Aufenthaltsart.

Diese Schritte variieren je nach persönlicher Lage und Bundesland; **für verbindliche Informationen bestätige unbedingt beim deutschen Konsulat und der Anerkennungsbehörde des Landes.**

## Erste Schritte
1. **Bestimme deine Lage:** Bist du schon Physiotherapeut oder fängst du von vorne an? Der Weg hängt von dieser Antwort ab.
2. **Fang mit Deutsch an:** B2 ist ein langer Weg; heute zu beginnen ist am klügsten.
3. **Hol offizielle Anerkennungsinfos:** `anerkennung-in-deutschland.de` und die Anerkennungsbehörde deines Landes sind deine offiziellen Referenzen.
4. **Lies die Cluster-Beiträge:** [Anerkennung](/de/blog/getting-your-foreign-physiotherapy-qualification-recognized-in-germany-anerkennung-de), [Ausbildungswege](/de/blog/physiotherapy-training-and-study-in-germany-for-internationals-de) und [Gehalt/Realität](/de/blog/working-as-a-physiotherapist-in-germany-salary-language-and-reality-de).
5. **Vergleiche ähnliche Berufe:** Wenn du im Gesundheitsbereich entscheidest, ist auch der [Leitfaden zur Pflege](/de/blog/becoming-a-nurse-in-germany-as-a-foreigner-de) ein nützlicher Vergleichspunkt.

## Fazit und ehrlicher Rat
Physiotherapeut in Deutschland zu werden ist ein realistisches Ziel mit **starker Nachfrage und Jobsicherheit**. Der größte Vorteil für Ausländer: Einen Job zu finden ist nicht schwer, und es gibt einen klaren Weg zur dauerhaften Aufenthaltserlaubnis. Bist du schon Physiotherapeut, ist der Anerkennungsweg meist die schnellste Option. Fängst du von vorne an, musst du auf eine Investition von 3–4 Jahren vorbereitet sein.

Die ehrliche Wahrheit aber: **Das Gehalt ist gemessen am Einsatz bescheiden**, und **B2 Deutsch ist eine echte Hürde** — unterschätze das nicht. Wenn du diese zwei Realitäten akzeptierst und losgehst, ist Deutschland ein lohnendes und stabiles Land für eine Physiotherapie-Karriere.

*Dieser Beitrag dient der allgemeinen Information mit Stand Anfang 2026; Regeln zu Visum, Anerkennung und Gehalt ändern sich häufig. Zahlen sind ungefähr. Prüfe vor deiner persönlichen Entscheidung aktuelle Angaben bei offiziellen Quellen wie der Anerkennungsbehörde des Landes, anerkennung-in-deutschland.de und dem deutschen Konsulat.*
MD;

        $enBody = <<<'MD'
Becoming a physiotherapist (Physiotherapeut) in Germany is one of the most realistic and in-demand paths into a healthcare career for foreigners. But unlike the simple "just come and work" stories online, this is a **regulated health profession** with its own rules. This guide helps you clarify whether you will start from scratch or get your existing qualification recognized.

## Why Germany: demand and shortage
Germany's population is ageing, and that keeps pushing demand for physiotherapy steadily upward. Elderly care, rehabilitation clinics, treatment after orthopedic surgery, and sports injuries — all of them need physiotherapists. As of 2025/2026, physiotherapists count among the **shortage occupations (Mangelberufe)** in many federal states, so finding a job is **noticeably easier** than in most professions. For foreigners this is a major advantage, both for job security and for the road to permanent residence.

But be honest with yourself: high demand does not mean high pay. We cover that reality in detail in the [salary, language and reality article](/en/blog/working-as-a-physiotherapist-in-germany-salary-language-and-reality-en).

## Two paths: recognize an existing qualification or train from scratch?
How you arrive here fully determines your path. There are **two fundamental routes**, and you should not confuse them.

| Criterion | Path 1: Recognition (Anerkennung) | Path 2: Training from scratch |
|---|---|---|
| For whom? | Those who are **already a physiotherapist** abroad | Those starting **from zero** |
| What happens? | Equivalence check (Gleichwertigkeit) of the foreign diploma | 3-year Ausbildung **or** a Bachelor |
| Duration | Usually shorter (longer if something is missing) | Around 3–4 years |
| If there are gaps | Kenntnisprüfung or Anpassungslehrgang | — |
| Language | B2 German (sometimes + Fachsprachprüfung) | B2 German |
| Result | Erlaubnis (permission to use the title) | Erlaubnis (permission to use the title) |

If you are already a physiotherapist, the **recognition path** is your address: we lay out the steps in the [Anerkennung guide](/en/blog/getting-your-foreign-physiotherapy-qualification-recognized-in-germany-anerkennung-en). If you are starting from scratch, the [training routes article](/en/blog/physiotherapy-training-and-study-in-germany-for-internationals-en) is your starting point.

## Why B2 German is non-negotiable
The most misunderstood aspect of this profession: **you will not become a physiotherapist in English.** English-language physiotherapy training barely exists in Germany, and on the job you talk to patients constantly — describing pain, explaining exercises, guiding an elderly patient all happen in German.

That is why **at least B2 German** is the rule on both the recognition and the training path. Some recognition procedures also require a professional language exam (Fachsprachprüfung) for the health sector. Practical advice: **make learning German your starting point**, because every other step depends on it.

## A regulated profession: the staatliche Anerkennung reality
In Germany you cannot use the title "physiotherapist" just because you want to. It is a **protected job title**, and to work legally you must obtain from the state the **"Erlaubnis zum Führen der Berufsbezeichnung"** (permission to use the professional title). This permission is granted at the end of the staatliche Anerkennung process.

So whether you train from scratch or get your diploma recognized, both paths lead to the same door: **official permission from the responsible state authority.** This bureaucracy may look annoying, but it is exactly what protects the value of the profession and your job security.

## Visa in outline (verify the official source)
Your visa situation depends on which path you are on. Broadly, as of 2026:

- **Visa for recognition:** If your diploma is in the equivalence assessment, or you need to enter to close gaps (Anpassungslehrgang), a residence permit for recognition purposes may be possible (rules similar to § 16d).
- **Skilled worker visa with a job offer:** If your recognition is complete and you have a job offer, you can enter as a skilled worker; in some cases a **fast-track procedure** applies. You can find the general process in our [work visa with a job offer article](/en/blog/germany-work-visa-with-job-offer-process-timeline-fast-track).
- **Training visa:** If you come for an Ausbildung or Bachelor, a different residence type applies.

These steps vary by personal situation and federal state; **for binding information, always confirm with the German consulate and the state's recognition authority.**

## First steps
1. **Determine your situation:** Are you already a physiotherapist, or starting from scratch? The path depends on this answer.
2. **Start German now:** B2 is a long road; beginning today is the smartest move.
3. **Get official recognition information:** `anerkennung-in-deutschland.de` and your state's recognition authority are your official references.
4. **Read the cluster articles:** [Anerkennung](/en/blog/getting-your-foreign-physiotherapy-qualification-recognized-in-germany-anerkennung-en), [training routes](/en/blog/physiotherapy-training-and-study-in-germany-for-internationals-en), and [salary/reality](/en/blog/working-as-a-physiotherapist-in-germany-salary-language-and-reality-en).
5. **Compare similar professions:** If you are deciding within healthcare, the [nursing guide](/en/blog/becoming-a-nurse-in-germany-as-a-foreigner-en) is also a useful comparison point.

## Conclusion and honest advice
Becoming a physiotherapist in Germany is a realistic goal with **strong demand and job security**. The biggest advantage for foreigners: finding a job is not hard, and there is a clear road to permanent residence. If you are already a physiotherapist, the recognition path is usually the fastest option. If you start from scratch, you must be ready for a 3–4 year investment.

But the honest truth is this: **the salary is modest relative to the effort**, and **B2 German is a real barrier** — do not underestimate it. If you accept these two realities and set out anyway, Germany is a rewarding and stable country for a physiotherapy career.

*This article is general information as of early 2026; rules on visas, recognition, and salaries change frequently. Figures are approximate. Before making a personal decision, verify current details with official sources such as your state's recognition authority, anerkennung-in-deutschland.de, and the German consulate.*
MD;

        $variants = [
            'tr' => ['slug'=>'becoming-a-physiotherapist-in-germany-as-a-foreigner',    'title'=>"Almanya'da Fizyoterapist Olmak: Yabancılar İçin Rehber (2026)", 'excerpt'=>"Almanya'da fizyoterapist olmanın iki yolu: yurtdışı diplomanı tanıtmak (Anerkennung) mı yoksa sıfırdan Ausbildung/Bachelor mı? Talep, düzenlenmiş meslek, B2 Almanca şartı ve vize ana hatları — yabancılar için dürüst bir başlangıç rehberi.", 'meta_title'=>"Almanya'da Fizyoterapist Olmak (2026) — Yabancı Rehberi", 'meta_description'=>"Almanya'da fizyoterapist olmak: tanınma vs sıfırdan eğitim, B2 Almanca şartı, staatliche Anerkennung ve vize. Yabancılar için dürüst rehber.", 'body'=>$trBody],
            'de' => ['slug'=>'becoming-a-physiotherapist-in-germany-as-a-foreigner-de', 'title'=>"Physiotherapeut in Deutschland werden: Leitfaden für Ausländer (2026)", 'excerpt'=>"Zwei Wege, um Physiotherapeut in Deutschland zu werden: dein Auslandsdiplom anerkennen lassen (Anerkennung) oder eine Ausbildung/Bachelor von vorne. Nachfrage, reglementierter Beruf, B2-Deutsch und Visum im Überblick — ein ehrlicher Einstieg für Ausländer.", 'meta_title'=>"Physiotherapeut in Deutschland werden (2026) — für Ausländer", 'meta_description'=>"Physiotherapeut in Deutschland werden: Anerkennung vs. Ausbildung, B2-Deutsch, staatliche Anerkennung und Visum. Ehrlicher Leitfaden für Ausländer.", 'body'=>$deBody],
            'en' => ['slug'=>'becoming-a-physiotherapist-in-germany-as-a-foreigner-en', 'title'=>"Becoming a Physiotherapist in Germany: A Guide for Foreigners (2026)", 'excerpt'=>"Two paths to becoming a physiotherapist in Germany: get your foreign qualification recognized (Anerkennung) or train from scratch via Ausbildung/Bachelor. Demand, the regulated profession, the B2 German requirement, and a visa outline — an honest starter guide for foreigners.", 'meta_title'=>"Becoming a Physiotherapist in Germany (2026) — Foreigner Guide", 'meta_description'=>"Becoming a physiotherapist in Germany: recognition vs. training from scratch, B2 German, staatliche Anerkennung, and visas. An honest guide for foreigners.", 'body'=>$enBody],
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
            'becoming-a-physiotherapist-in-germany-as-a-foreigner',
            'becoming-a-physiotherapist-in-germany-as-a-foreigner-de',
            'becoming-a-physiotherapist-in-germany-as-a-foreigner-en',
        ])->delete();
    }
};
