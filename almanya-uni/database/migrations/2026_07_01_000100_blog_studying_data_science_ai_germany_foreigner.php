<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN): Almanya'da yabancı olarak Data Science & Yapay Zekâ (AI) okumak (2026).
 *
 * Doğrulandı (genel kabul + üniversite şartları, 2026):
 *  - Almanya AI/ML'de güçlü: DFKI, Max Planck (MPI-IS), Fraunhofer, Cyber Valley (Tübingen/Stuttgart),
 *    ELLIS ağı. Tepe okullar: TUM, Uni Tübingen, Saarland, TU Darmstadt, LMU, RWTH, KIT, TU Berlin.
 *  - Seviye: Data Science/AI çoğunlukla MASTER seviyesi; bachelor'da genelde Informatik/Matematik/İstatistik.
 *  - #1 şok: ağır matematik + istatistik (lineer cebir, olasılık, ML teorisi, optimizasyon) — "araç kursu" değil.
 *  - Dil: İngilizce master bol ve kamu ünide ücretsiz (~150–350€ semester; BW non-EU ~1.500€/dönem).
 *  - Başvuru uni-assist; CS/Math/Stats/Physics/Eng lisans arka planı (yeterli matematik/programlama) beklenir.
 *
 * Yazar: Halil Yaprakli. Kategori: almanyada-egitim. FK-safe + slug-bazlı idempotent.
 * İç-link: küme yazıları 2/3/4 + CS-studying + engineering-studying → her dilde locale-doğru.
 */
return new class extends Migration
{
    public function up(): void
    {
        $groupId = 'd1a10000-1111-4daa-9f30-aa01bb02dd01';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'almanyada-egitim')->value('id')
            ?? DB::table('categories')->where('slug', 'universities')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
"Almanya'da Data Science / Yapay Zekâ okumak nasıl?" — kısa cevap: **alan çok güçlü, ama beklediğinden çok daha matematiksel ve çoğunlukla master seviyesinde.** Almanya AI/ML araştırmasında Avrupa'nın en ağır sıkletlerinden: **DFKI**, **Max Planck (MPI-IS)**, **Fraunhofer**, Tübingen/Stuttgart'taki **Cyber Valley** ve **ELLIS** ağı burada. Ama "Python kursu alıp Data Scientist olacağım" diye gelirsen sert bir gerçekle tanışırsın. İşin doğrusunu net koyalım.

## 1. Önce yapı: DS/AI çoğunlukla MASTER seviyesidir
En sık yanlış anlaşılan nokta bu. Almanya'da **"Data Science" veya "Artificial Intelligence" adlı programların büyük çoğunluğu master seviyesidir.** Bachelor aşamasında tipik yol **Informatik (bilgisayar bilimi), Matematik veya İstatistik** okumaktır — sonra üstüne DS/AI master'ı gelir. Saf "Data Science" bachelor'ları son yıllarda artıyor ama hâlâ **az ve çoğu yeni.**

Pratik sonuç: eğer lise sonrası doğrudan "yapay zekâ okuyacağım" diyorsan, gerçekçi plan **sağlam bir Informatik/Matematik bachelor + DS/AI master**. Bachelor tarafı büyük ölçüde [Informatik okumakla](/tr/blog/studying-computer-science-informatik-in-germany-as-a-foreigner) örtüşür; asıl uzmanlaşma master'da olur.

## 2. #1 gerçeklik şoku: ağır matematik + istatistik, "araç kursu" değil
En çok hayal kırıklığı yaratan nokta: **Alman DS/AI programları teori-yoğundur.** Karşına çıkacaklar: **lineer cebir, olasılık ve istatistik, optimizasyon, makine öğrenmesi teorisi.** Ders "TensorFlow nasıl çağrılır" değil, "bu modelin arkasındaki matematik nedir" üzerine kuruludur.

"Sadece Python ve hazır araç öğreneceğim" diyerek gelenler en çok zorlanan grup. Eğer matematikten kaçıyorsan bu alan seni yorar; ama teoriyi seviyorsan Almanya tam sana göre. **Beklentini şimdiden ayarla:** DS/AI burada bir mühendislik-matematik disiplinidir, bir araç kursu değil.

## 3. Tepe okullar ve araştırma merkezleri
Almanya'nın AI/ML ekosistemi derindir. Master ve araştırma için öne çıkanlar:

| Kurum / Merkez | Neden önemli |
|---|---|
| **TUM (Münih)** | En güçlü CS/AI programlarından biri, geniş ML/DS master seçeneği |
| **Uni Tübingen + Cyber Valley** | Almanya'nın ML merkezi; **MPI-IS** ve **ELLIS** ile iç içe |
| **Saarland (Saarbrücken)** | CS/AI devi; **DFKI** ve MPI burada |
| **TU Darmstadt** | Güçlü ML/robotik/AI araştırması |
| **LMU Münih, RWTH Aachen, KIT, TU Berlin** | Güçlü DS/AI master ve araştırma grupları |

Ek olarak **Fraunhofer** enstitüleri uygulamalı AI Ar-Ge'sinde çok aktiftir. Kısaca: doğru merkezleri hedeflersen dünya sınıfı gruplarla çalışabilirsin.

## 4. Dil: İngilizce master bol ve (kamuda) ücretsiz
İyi haber: **İngilizce master programları DS/AI'de çok yaygın** ve kamu üniversitelerinde **öğrenim ücreti yoktur** — sadece dönemlik ~**150–350€ katkı** (*2025/2026 itibarıyla, yaklaşık; doğrula*). Tek büyük istisna **Baden-Württemberg**: AB-dışı öğrencilerden ~**1.500€/dönem** alır (*hedge: doğrula*).

Bu, Almancasız gelmenin en gerçekçi kapısıdır. Detay bu kümede: [Almancasız İngilizce DS/AI master programları](/tr/blog/english-taught-data-science-ai-masters-in-germany). Yine de günlük hayat, staj ve iş için Almanca er ya da geç işine yarar — ama derse başlamak için İngilizce yeter.

## 5. Başvuru: uni-assist + lisans arka planı şartı
AB-dışı öğrenciler (ör. Türkiye) master başvurusunu çoğunlukla **uni-assist** üzerinden yapar. DS/AI master'ları neredeyse her zaman **ilgili bir lisans arka planı** ister:

- **CS / Informatik, Matematik, İstatistik, Fizik veya Mühendislik** lisansı,
- yeterli **matematik + programlama** kredisi (transkriptinden bakarlar),
- **İngilizce kanıtı** (IELTS/TOEFL), bazı programlarda ek **GRE** veya küçük bir portfolyo.

Yani DS/AI master'ı "sıfırdan" değil, **niceliksel bir lisansın üzerine** kurulur. Arka planın uymuyorsa önce köprü dersleri veya bir dönüşüm master'ı gerekebilir. (Başvuru mantığı için: [master mi, iş-arama vizesi mi](/tr/blog/germany-masters-vs-job-seeker-visa-two-keys-career).)

## 6. DS vs AI vs ML vs Data Engineering — hangisi sana uygun?
Bu etiketler sık karıştırılır; kariyer için farkları bilmek kritik:

| Alan | Ne yapar | Ağırlık |
|---|---|---|
| **Data Science** | veriden içgörü, istatistik, analiz, iş kararı | istatistik + iletişim |
| **AI / ML Research** | yeni modeller/algoritmalar, teori | matematik + araştırma (genelde doktora) |
| **ML Engineering** | modelleri üretime alma, ölçekleme, MLOps | yazılım mühendisliği + ML |
| **Data Engineering** | veri altyapısı, pipeline, veri ambarı | yazılım + sistemler |

**Not:** Almanya iş piyasasında en yüksek talep **ML Engineer** ve **Data Engineer** tarafındadır — saf "araştırma" rolleri genelde doktora ister. Hangi rolü hedeflediğin, hangi master'ı ve hangi dersleri seçeceğini belirler. (Detay: [Data Scientist / ML Engineer olarak çalışmak — maaş & Mavi Kart](/tr/blog/working-as-a-data-scientist-ml-engineer-in-germany-blue-card-salary) ve [DS/AI'ye nasıl girilir](/tr/blog/how-to-break-into-data-science-ai-in-germany).)

## Sonuç & dürüst tavsiye
Almanya, Data Science & AI için **Avrupa'nın en güçlü adreslerinden biri**: DFKI, Max Planck, Cyber Valley, ELLIS; İngilizce master bol ve kamuda ücretsiz. **Ama iki gerçeği baştan kabul et:** (1) alan çoğunlukla **master seviyesi** — önce sağlam bir Informatik/Matematik/İstatistik zemini, (2) iş **ağır matematik + istatistik**, bir araç kursu değil. Matematiği seviyorsan bu alan sana çok şey verir. Doğru merkezleri (TUM, Tübingen/Cyber Valley, Saarland, Darmstadt) hedefle, arka planını ve İngilizce kanıtını erken hazırla.

Devamı bu kümede: [Almancasız İngilizce DS/AI master programları](/tr/blog/english-taught-data-science-ai-masters-in-germany) · [Data Scientist / ML Engineer olarak çalışmak — maaş & Mavi Kart](/tr/blog/working-as-a-data-scientist-ml-engineer-in-germany-blue-card-salary) · [DS/AI'ye nasıl girilir](/tr/blog/how-to-break-into-data-science-ai-in-germany). İlgili: [Informatik okumak](/tr/blog/studying-computer-science-informatik-in-germany-as-a-foreigner) · [Almanya'da mühendislik okumak](/tr/blog/studying-engineering-in-germany-as-a-foreigner).

---
*2026 itibarıyla geçerli genel duruma dayanır; program seviyesi, dil ve ücret şartları üniversiteye göre değişir. Maaş, Mavi Kart eşiği ve ücret rakamları yaklaşıktır ve her yıl değişir — başvurudan önce ilgili üniversitenin International Office'inden ve resmî kaynaklardan teyit et.*
MD;

        $deBody = <<<'MD'
„Wie ist es, in Deutschland Data Science / Künstliche Intelligenz zu studieren?" — kurz: **das Feld ist stark, aber viel mathematischer als du denkst und meist auf Master-Ebene.** Deutschland gehört in der KI/ML-Forschung zu Europas Schwergewichten: **DFKI**, **Max Planck (MPI-IS)**, **Fraunhofer**, das **Cyber Valley** (Tübingen/Stuttgart) und das **ELLIS**-Netzwerk sind hier. Wer aber denkt „ich mache einen Python-Kurs und werde Data Scientist", erlebt einen Realitätsschock. Hier die Fakten.

## 1. Erst die Struktur: DS/KI ist meist auf Master-Ebene
Der häufigste Irrtum. In Deutschland sind die meisten Programme mit dem Namen **„Data Science" oder „Artificial Intelligence" auf Master-Ebene.** Im Bachelor ist der typische Weg **Informatik, Mathematik oder Statistik** — der DS/KI-Master kommt obendrauf. Reine „Data Science"-Bachelor nehmen zu, sind aber noch **selten und meist neu.**

Praktisch heißt das: Wenn du direkt nach dem Abi „KI studieren" willst, ist der realistische Plan **ein solider Informatik-/Mathe-Bachelor + DS/KI-Master.** Der Bachelor-Teil überschneidet sich stark mit dem [Informatik-Studium](/de/blog/studying-computer-science-informatik-in-germany-as-a-foreigner-de); die eigentliche Spezialisierung passiert im Master.

## 2. Realitäts-Schock Nr. 1: viel Mathe + Statistik, kein „Tool-Kurs"
Der größte Frustpunkt: **deutsche DS/KI-Programme sind theorielastig.** Auf dich warten **lineare Algebra, Wahrscheinlichkeit und Statistik, Optimierung, Machine-Learning-Theorie.** Es geht nicht um „wie rufe ich TensorFlow auf", sondern um „welche Mathematik steckt hinter dem Modell".

Wer sagt „ich lerne nur Python und fertige Tools", tut sich am schwersten. Wenn du Mathe meidest, wird dich dieses Feld erschöpfen; magst du Theorie, ist Deutschland ideal. **Stell deine Erwartung jetzt ein:** DS/KI ist hier eine mathematisch-ingenieurwissenschaftliche Disziplin, kein Tool-Kurs.

## 3. Top-Schulen und Forschungszentren
Deutschlands KI/ML-Ökosystem ist tief. Für Master und Forschung stechen hervor:

| Institution / Zentrum | Warum wichtig |
|---|---|
| **TUM (München)** | eines der stärksten CS/KI-Programme, breite ML/DS-Master-Auswahl |
| **Uni Tübingen + Cyber Valley** | Deutschlands ML-Hub; eng mit **MPI-IS** und **ELLIS** |
| **Saarland (Saarbrücken)** | CS/KI-Powerhouse; **DFKI** und MPI sind hier |
| **TU Darmstadt** | starke ML-/Robotik-/KI-Forschung |
| **LMU München, RWTH Aachen, KIT, TU Berlin** | starke DS/KI-Master und Forschungsgruppen |

Dazu sind die **Fraunhofer**-Institute in der angewandten KI-F&E sehr aktiv. Kurz: Wer die richtigen Zentren anpeilt, arbeitet mit Weltklasse-Gruppen.

## 4. Sprache: englischsprachige Master reichlich und (staatlich) gebührenfrei
Gute Nachricht: **Englischsprachige Master in DS/KI gibt es reichlich** und an staatlichen Unis fallen **keine Studiengebühren** an — nur ein Semesterbeitrag von ~**150–350€** (*Stand 2025/2026, ungefähr; bitte prüfen*). Die große Ausnahme ist **Baden-Württemberg**: Von Nicht-EU-Studierenden werden ~**1.500€/Semester** verlangt (*bitte prüfen*).

Das ist der realistischste Weg, ohne Deutsch zu starten. Mehr in diesem Cluster: [englischsprachige DS/KI-Master ohne Deutsch](/de/blog/english-taught-data-science-ai-masters-in-germany-de). Für Alltag, Praktikum und Job hilft dir Deutsch trotzdem früher oder später — aber zum Studienstart reicht Englisch.

## 5. Bewerbung: uni-assist + Vorbildung als Voraussetzung
Nicht-EU-Studierende (z. B. Türkei) bewerben sich für den Master meist über **uni-assist**. DS/KI-Master verlangen fast immer eine **einschlägige Vorbildung:**

- ein Bachelor in **CS / Informatik, Mathematik, Statistik, Physik oder Ingenieurwesen**,
- ausreichend **Mathe- + Programmier**-Credits (dein Transkript wird geprüft),
- **Englischnachweis** (IELTS/TOEFL), manchmal zusätzlich **GRE** oder ein kleines Portfolio.

Ein DS/KI-Master baut also nicht „bei null" auf, sondern auf einem **quantitativen Bachelor.** Passt dein Hintergrund nicht, brauchst du evtl. erst Brückenkurse oder einen Umstiegs-Master. (Zur Bewerbungslogik: [Master oder Job-Seeker-Visum](/de/blog/germany-masters-vs-job-seeker-visa-two-keys-career-de).)

## 6. DS vs KI vs ML vs Data Engineering — was passt zu dir?
Diese Begriffe werden oft verwechselt; für die Karriere ist der Unterschied entscheidend:

| Feld | Was es tut | Schwerpunkt |
|---|---|---|
| **Data Science** | Insights aus Daten, Statistik, Analyse, Geschäftsentscheidung | Statistik + Kommunikation |
| **KI / ML Research** | neue Modelle/Algorithmen, Theorie | Mathe + Forschung (meist Promotion) |
| **ML Engineering** | Modelle in Produktion bringen, skalieren, MLOps | Software-Engineering + ML |
| **Data Engineering** | Dateninfrastruktur, Pipelines, Data Warehouse | Software + Systeme |

**Hinweis:** Am deutschen Arbeitsmarkt ist die Nachfrage bei **ML Engineer** und **Data Engineer** am höchsten — reine „Research"-Rollen setzen meist eine Promotion voraus. Welche Rolle du anpeilst, bestimmt Master und Fächerwahl. (Mehr: [als Data Scientist / ML Engineer arbeiten — Gehalt & Blue Card](/de/blog/working-as-a-data-scientist-ml-engineer-in-germany-blue-card-salary-de) und [wie man in DS/KI einsteigt](/de/blog/how-to-break-into-data-science-ai-in-germany-de).)

## Fazit & ehrlicher Rat
Deutschland ist für Data Science & KI **eine der stärksten Adressen Europas**: DFKI, Max Planck, Cyber Valley, ELLIS; englischsprachige Master reichlich und staatlich gebührenfrei. **Aber akzeptiere zwei Wahrheiten von Anfang an:** (1) das Feld ist meist **Master-Ebene** — erst ein solides Fundament in Informatik/Mathe/Statistik, (2) die Arbeit ist **viel Mathe + Statistik**, kein Tool-Kurs. Magst du Mathe, gibt dir das Feld viel zurück. Peile die richtigen Zentren an (TUM, Tübingen/Cyber Valley, Saarland, Darmstadt) und bereite Vorbildung und Englischnachweis früh vor.

Weiter in diesem Cluster: [englischsprachige DS/KI-Master ohne Deutsch](/de/blog/english-taught-data-science-ai-masters-in-germany-de) · [als Data Scientist / ML Engineer arbeiten — Gehalt & Blue Card](/de/blog/working-as-a-data-scientist-ml-engineer-in-germany-blue-card-salary-de) · [wie man in DS/KI einsteigt](/de/blog/how-to-break-into-data-science-ai-in-germany-de). Verwandt: [Informatik studieren](/de/blog/studying-computer-science-informatik-in-germany-as-a-foreigner-de) · [Ingenieurwesen in Deutschland studieren](/de/blog/studying-engineering-in-germany-as-a-foreigner-de).

---
*Stand 2026; Programmniveau, Sprache und Gebühren variieren je nach Uni. Gehalt, Blue-Card-Schwelle und Gebührenzahlen sind ungefähr und ändern sich jährlich — vor der Bewerbung beim International Office der jeweiligen Uni und bei offiziellen Quellen bestätigen.*
MD;

        $enBody = <<<'MD'
"What is it like to study Data Science / Artificial Intelligence in Germany?" — short answer: **the field is strong, but far more mathematical than you expect and mostly at master's level.** Germany is one of Europe's AI/ML heavyweights: **DFKI**, **Max Planck (MPI-IS)**, **Fraunhofer**, the **Cyber Valley** (Tübingen/Stuttgart) and the **ELLIS** network are all here. But come thinking "I'll take a Python course and become a Data Scientist" and you'll hit a hard reality check. Here are the facts.

## 1. Structure first: DS/AI is mostly at master's level
This is the most common misunderstanding. In Germany, the vast majority of programmes literally named **"Data Science" or "Artificial Intelligence" are at master's level.** At bachelor level the typical path is **Informatik (computer science), Mathematics or Statistics** — then the DS/AI master's on top. Pure "Data Science" bachelors are increasing but still **rare and mostly new.**

Practically: if you want to "study AI" straight out of school, the realistic plan is **a solid Informatik/Maths bachelor + a DS/AI master's.** The bachelor part overlaps heavily with [studying Informatik](/en/blog/studying-computer-science-informatik-in-germany-as-a-foreigner-en); the real specialisation happens at master's level.

## 2. Reality shock #1: heavy maths + statistics, not a "tool course"
The biggest source of disappointment: **German DS/AI programmes are theory-heavy.** Expect **linear algebra, probability and statistics, optimisation, machine-learning theory.** The course isn't "how do I call TensorFlow" — it's "what is the maths behind this model".

Those who say "I'll just learn Python and ready-made tools" struggle the most. If you avoid maths, this field will exhaust you; if you enjoy theory, Germany is ideal. **Set your expectation now:** here DS/AI is a mathematical, engineering discipline, not a tool course.

## 3. Top schools and research centres
Germany's AI/ML ecosystem is deep. For master's and research, these stand out:

| Institution / Centre | Why it matters |
|---|---|
| **TUM (Munich)** | one of the strongest CS/AI programmes, wide ML/DS master's choice |
| **Uni Tübingen + Cyber Valley** | Germany's ML hub; tightly linked with **MPI-IS** and **ELLIS** |
| **Saarland (Saarbrücken)** | a CS/AI powerhouse; **DFKI** and MPI are here |
| **TU Darmstadt** | strong ML/robotics/AI research |
| **LMU Munich, RWTH Aachen, KIT, TU Berlin** | strong DS/AI master's and research groups |

On top of that, the **Fraunhofer** institutes are very active in applied AI R&D. In short: target the right centres and you can work with world-class groups.

## 4. Language: English-taught master's are plentiful and (publicly) tuition-free
Good news: **English-taught DS/AI master's are common**, and at public universities there is **no tuition fee** — only a semester contribution of ~**€150–350** (*as of 2025/2026, approximate; verify*). The one big exception is **Baden-Württemberg**: non-EU students are charged ~**€1,500/semester** (*hedge: verify*).

This is the most realistic door into Germany without German. More in this cluster: [English-taught DS/AI master's without German](/en/blog/english-taught-data-science-ai-masters-in-germany-en). Even so, German will help you sooner or later for daily life, internships and jobs — but to start the degree, English is enough.

## 5. Applying: uni-assist + a required academic background
Non-EU students (e.g. Turkey) usually apply for the master's via **uni-assist**. DS/AI master's almost always require a **relevant academic background:**

- a bachelor in **CS / Informatik, Mathematics, Statistics, Physics or Engineering**,
- enough **maths + programming** credits (they check your transcript),
- **proof of English** (IELTS/TOEFL), and sometimes an additional **GRE** or a small portfolio.

So a DS/AI master's is not built "from zero" — it's built on top of a **quantitative bachelor.** If your background doesn't fit, you may first need bridging courses or a conversion master's. (For the application logic: [master's vs job-seeker visa](/en/blog/germany-masters-vs-job-seeker-visa-two-keys-career-en).)

## 6. DS vs AI vs ML vs Data Engineering — which fits you?
These labels get confused constantly; knowing the difference matters for your career:

| Field | What it does | Emphasis |
|---|---|---|
| **Data Science** | insight from data, statistics, analysis, business decisions | statistics + communication |
| **AI / ML Research** | new models/algorithms, theory | maths + research (usually a PhD) |
| **ML Engineering** | shipping models to production, scaling, MLOps | software engineering + ML |
| **Data Engineering** | data infrastructure, pipelines, data warehousing | software + systems |

**Note:** in the German job market, demand is highest for **ML Engineer** and **Data Engineer** — pure "research" roles usually require a PhD. Which role you target decides which master's and which courses you pick. (More: [working as a Data Scientist / ML Engineer — salary & Blue Card](/en/blog/working-as-a-data-scientist-ml-engineer-in-germany-blue-card-salary-en) and [how to break into DS/AI](/en/blog/how-to-break-into-data-science-ai-in-germany-en).)

## Bottom line & honest advice
Germany is **one of Europe's strongest places** for Data Science & AI: DFKI, Max Planck, Cyber Valley, ELLIS; English-taught master's plentiful and tuition-free at public universities. **But accept two truths up front:** (1) the field is mostly **master's level** — build a solid Informatik/Maths/Statistics foundation first, and (2) the work is **heavy maths + statistics**, not a tool course. If you enjoy maths, this field gives a lot back. Target the right centres (TUM, Tübingen/Cyber Valley, Saarland, Darmstadt) and prepare your background and English proof early.

Continue in this cluster: [English-taught DS/AI master's without German](/en/blog/english-taught-data-science-ai-masters-in-germany-en) · [working as a Data Scientist / ML Engineer — salary & Blue Card](/en/blog/working-as-a-data-scientist-ml-engineer-in-germany-blue-card-salary-en) · [how to break into DS/AI](/en/blog/how-to-break-into-data-science-ai-in-germany-en). Related: [studying Informatik](/en/blog/studying-computer-science-informatik-in-germany-as-a-foreigner-en) · [studying engineering in Germany](/en/blog/studying-engineering-in-germany-as-a-foreigner-en).

---
*Based on the general situation as of 2026; programme level, language and fee requirements vary by university. Salary, Blue Card threshold and fee figures are approximate and change every year — confirm with the relevant university's International Office and official sources before applying.*
MD;

        $variants = [
            'tr' => [
                'slug'  => 'studying-data-science-ai-in-germany-as-a-foreigner',
                'title' => 'Almanya\'da Yabancı Olarak Data Science & Yapay Zekâ Okumak (2026)',
                'excerpt' => 'Almanya\'da Data Science & AI: alan çok güçlü (DFKI, Max Planck, Cyber Valley, ELLIS) ama çoğunlukla MASTER seviyesi ve #1 şok ağır matematik + istatistik — "araç kursu" değil. İngilizce master bol ve kamuda ücretsiz; bachelor genelde Informatik/Matematik. Yabancılar için dürüst 2026 rehberi.',
                'meta_title' => 'Almanya\'da Data Science & AI Okumak — Yabancı Rehberi (2026)',
                'meta_description' => 'Almanya\'da Data Science & AI: çoğunlukla master seviyesi, ağır matematik + istatistik (araç kursu değil), İngilizce master bol ve kamuda ücretsiz, TUM/Tübingen/Saarland — yabancılar için dürüst 2026 rehberi.',
                'body' => $trBody,
            ],
            'de' => [
                'slug'  => 'studying-data-science-ai-in-germany-as-a-foreigner-de',
                'title' => 'Als Ausländer Data Science & KI in Deutschland studieren (2026): Master, Mathe-Realität & Sprache',
                'excerpt' => 'Data Science & KI in Deutschland: ein starkes Feld (DFKI, Max Planck, Cyber Valley, ELLIS), aber meist auf Master-Ebene und Schock Nr. 1: viel Mathe + Statistik — kein Tool-Kurs. Englischsprachige Master reichlich und staatlich gebührenfrei; Bachelor meist Informatik/Mathe. Ehrlicher Leitfaden für Ausländer.',
                'meta_title' => 'Data Science & KI in Deutschland studieren — Ausländer (2026)',
                'meta_description' => 'Data Science & KI in Deutschland: meist Master-Ebene, viel Mathe + Statistik (kein Tool-Kurs), englische Master reichlich und staatlich gebührenfrei, TUM/Tübingen/Saarland — ehrlicher Leitfaden für Ausländer 2026.',
                'body' => $deBody,
            ],
            'en' => [
                'slug'  => 'studying-data-science-ai-in-germany-as-a-foreigner-en',
                'title' => 'Studying Data Science & AI in Germany as a Foreigner (2026)',
                'excerpt' => 'Data Science & AI in Germany: a strong field (DFKI, Max Planck, Cyber Valley, ELLIS) but mostly at master\'s level, and reality shock #1 is heavy maths + statistics — not a tool course. English-taught master\'s are plentiful and tuition-free at public unis; bachelors are usually Informatik/Maths. An honest 2026 guide for foreigners.',
                'meta_title' => 'Studying Data Science & AI in Germany as a Foreigner (2026)',
                'meta_description' => 'Data Science & AI in Germany: mostly master\'s level, heavy maths + statistics (not a tool course), English master\'s plentiful and tuition-free at public unis, TUM/Tübingen/Saarland — an honest 2026 guide for foreigners.',
                'body' => $enBody,
            ],
        ];

        foreach ($variants as $locale => $v) {
            $html = Str::markdown($v['body'], ['html_input' => 'allow', 'allow_unsafe_links' => false]);
            $payload = [
                'locale'           => $locale,
                'translation_group_id' => $groupId,
                'user_id'          => $userId,
                'category_id'      => $categoryId,
                'title'            => $v['title'],
                'excerpt'          => Str::limit($v['excerpt'], 250, '…'),
                'content_md'       => $v['body'],
                'content_html'     => $html,
                'meta_title'       => $v['meta_title'],
                'meta_description' => Str::limit($v['meta_description'], 158, '…'),
                'reading_minutes'  => max(1, (int) round(str_word_count(strip_tags($html)) / 200)),
                'is_published'     => true,
                'published_at'     => now(),
            ];

            $existing = Post::where('slug', $v['slug'])->first();
            if ($existing) {
                $existing->update($payload);
            } else {
                Post::create($payload + ['slug' => $v['slug']]);
            }
        }
    }

    public function down(): void
    {
        Post::whereIn('slug', [
            'studying-data-science-ai-in-germany-as-a-foreigner',
            'studying-data-science-ai-in-germany-as-a-foreigner-de',
            'studying-data-science-ai-in-germany-as-a-foreigner-en',
        ])->delete();
    }
};
