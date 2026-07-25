<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN): Almanya'da Uluslararasi Iliskiler / politikada calismak (2026).
 * Dogrulandi: diplomatlik Alman vatandasligi ister; giris maasi ~40-50k (2025, degisir);
 * Almanca kamu / Ingilizce uluslararasi dil ikiligi; staj + network kritik.
 * Yazar: Halil Yaprakli. Kategori: almanyada-egitim. slug-bazli idempotent.
 */
return new class extends Migration
{
    public function up(): void
    {
        $groupId = 'c6d30000-3333-4f7b-9fa0-cc04dd09ff03';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'almanyada-egitim')->value('id')
            ?? DB::table('categories')->where('slug', 'universities')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
"Uluslararası İlişkiler okudum, Almanya'da BM'de ya da bir think-tank'te çalışabilir miyim?" Cevap: evet, mümkün — ama romantik hayalle gerçek arasındaki farkı baştan bilmek gerekir. Bu alan **rekabetçi**, kariyer yolu tek çizgi değil, ve "diplomat olacağım" diyen Türk öğrenci için kritik bir sürpriz var. Bu yazı, Almanya'daki IR/politika iş ekosistemini kuruluş kuruluş, maaş maaş, dürüstçe anlatır.

## Hangi sektörler var: kuruluşlar haritası

IR/siyaset bilimi diploması seni tek bir mesleğe değil, bir **sektör yelpazesine** açar. Almanya bağlamında başlıca oyuncular:

- **Uluslararası kuruluşlar:** BM ajansları (Bonn'da güçlü BM varlığı — UNV, UNFCCC iklim sekretaryası), AB kurumları (Brüksel merkezli ama Almanya'dan erişilir), OECD, IMF, Dünya Bankası. İngilizce-dostu ama son derece rekabetçi.
- **Kalkınma iş birliği:** **GIZ** (Deutsche Gesellschaft für Internationale Zusammenarbeit — merkez Bonn/Eschborn) ve **KfW** kalkınma bankası. Almanya'nın kalkınma politikasının motoru; IR mezunları için en somut büyük işverenler.
- **Think-tank'ler:** **SWP** (Stiftung Wissenschaft und Politik — hükümete danışan devasa think-tank, Berlin), **DGAP** (Alman Dış Politika Konseyi), **ECFR** (European Council on Foreign Relations). Prestijli ama pozisyonlar az.
- **Siyasi vakıflar (Stiftungen):** **Konrad-Adenauer-Stiftung** (CDU'ya yakın), **Friedrich-Ebert-Stiftung** (SPD'ye yakın) ve diğer parti vakıfları. Yurtdışı ofisleri, burslar, politika programları — geniş istihdam alanı.
- **NGO'lar, siyasi danışmanlık, gazetecilik, akademi.** İnsani yardım (Welthungerhilfe, Caritas international), iklim/enerji politikası ve göç alanında çalışan yüzlerce STK; kamu politikası odaklı danışmanlık şirketleri; uluslararası masa gazeteciliği ve doktora üzerinden akademik yol da bu yelpazededir.

Kritik nokta: bu sektörlerin çoğu **tek net "giriş sınavı" ya da standart yol sunmaz.** Mühendis ya da hekim gibi net bir meslek tanımın olmaz; kariyerini kendin, staj ve uzmanlaşma ile inşa edersin. Bu belirsizlik alanın hem özgürlüğü hem zorluğudur.

Bu manzaranın tümüne genel bir bakış için [IR diplomasıyla iş piyasası yazısına](/tr/blog/what-to-do-with-a-political-science-ir-degree-in-germany-job-market) bakabilirsin.

## Berlin / Bonn ekosistemi: konum kritik

Bu alanda **nerede olduğun**, ne okuduğun kadar önemli. İki merkez var:

- **Berlin:** IR kariyerinin kalbi. Auswärtiges Amt (Dışişleri Bakanlığı), think-tank'lerin çoğu (SWP, DGAP, ECFR), NGO'lar, elçilikler, uluslararası basının Alman büroları. Staj ve network için rakipsiz.
- **Bonn:** "Birleşmiş Milletler şehri" — BM ajansları ve GIZ merkezi burada. Kalkınma ve çok taraflı kuruluşlar isteyenler için Bonn, Berlin kadar önemli.

Neden hangi şehirde okuduğun sana staj ağı açtığı için önemli; Almanya'da doğru şehir ve program seçiminin mantığını [üniversite prestiji & seçim yazısında](/tr/blog/how-university-prestige-and-rankings-work-in-germany-choosing-the-right-one) ayrıntılı ele aldım.

## Diplomat olmak: Alman vatandaşlığı gerçeği

Bu, Türk öğrenci için en önemli dürüst not: **Alman dış hizmetine (Auswärtiges Amt diplomatı) girmek Alman vatandaşlığı gerektirir.** Auswärtiger Dienst bir kamu memuriyeti (Beamtenverhältnis) statüsüdür ve kural olarak Alman (ya da bazı hallerde başka bir AB) vatandaşlığı şarttır.

Yani "IR okuyup Almanya'da diplomat olacağım" hedefi, Alman vatandaşlığına geçmedikçe (genelde uzun ikamet + naturalizasyon sonrası) **doğrudan mümkün değildir.** Bu bir engel değil, bir gerçek: alternatif olarak BM, AB, GIZ, think-tank ve NGO yolları vatandaşlık istemez ve uluslararası personele çok daha açıktır. Hedefini buna göre kur.

Bir başka dürüst not: Auswärtiges Amt dışındaki kamu memuriyeti rolleri (bakanlıklar, federal daireler) de çoğunlukla vatandaşlık ya da AB vatandaşlığı arar. Ama uluslararası kuruluşlar ve özel sektör (danışmanlık, NGO) bu şartın dışındadır — bu yüzden Türk öğrenci için en gerçekçi kariyer eksenleri çoğunlukla kamu-dışı ya da çok-taraflı kuruluşlardır. Vatandaşlık bir gün gelirse kamu kapıları da açılır; ama planını "gelmeyebilir" varsayımı üzerine kur.

## Maaş gerçeği (yaklaşık, doğrula)

Rakamlar sektöre göre çok değişir. **2025 itibarıyla, yaklaşık brüt yıllık** (kesin değil, mutlaka güncel ilanlardan doğrula):

| Alan | Giriş (yaklaşık, yıllık brüt) | Not |
|---|---|---|
| Think-tank / NGO | ~35.000–45.000 € | Genelde alt uçta; süreli sözleşme yaygın |
| GIZ / kalkınma | ~40.000–50.000 € | Yurtiçi; saha/uzman rollerinde üstü |
| Kamu / vakıflar | ~40.000–48.000 € | TVöD tarifesine bağlı (kademe/deneyim) |
| Uluslararası kuruluş (BM/AB) | Değişken, sık üstü | Çok rekabetçi; staj/JPO ile giriş yaygın |
| Siyasi/yönetim danışmanlığı | ~45.000–55.000+ € | En üst uç; özel sektör |

Genel giriş bandı kabaca **~40–50k** civarında toplanır; think-tank/NGO altında, uluslararası kuruluş ve danışmanlık üstünde. Erken kariyerde **süreli (befristet) sözleşme** bu alanda çok yaygındır — bunu baştan hesaba kat.

## Dil gerçeği: Almanca kamu, İngilizce uluslararası

Bu alanın en yanlış anlaşılan noktası. Kısa gerçek: **iki dil ikiliği** var.

- **Alman kamu, GIZ merkez, vakıflar, yerel NGO'lar, çoğu Almanya-içi iş:** günlük çalışma dili Almanca. Ciddi rol için **B2/C1 Almanca fiilen şart.**
- **BM/AB/OECD ve uluslararası kuruluşlar:** çalışma dili İngilizce; Almanca olmadan da girilebilir ama rekabet küresel.

Pratik sonuç: sadece İngilizce ile "uluslararası kuruluş" katmanını hedefleyebilirsin ama bu en rekabetçi katman. Almanya-içi istikrarlı kariyer istiyorsan **Almanca'ya yatırım yap** — bu, kapı sayısını katlar. İngilizce master'la başlayıp Almanca'yı ihmal etme tuzağını [İngilizce master yazısında](/tr/blog/english-taught-international-relations-political-science-masters-in-germany) uzun uzun anlattım.

## İş arama: staj, network ve Blue Card

Bu alanda işe girişin **iki gerçek anahtarı** var: staj ve ağ.

- **Praktikum (staj):** IR/politika dünyasında staj neredeyse zorunlu bir giriş kapısı. SWP, DGAP, vakıflar, GIZ, elçilikler düzenli stajyer alır. İlk kalıcı işin çoğu zaman staj yaptığın yerden ya da o ağ üzerinden gelir.
- **Network:** Konferanslar, vakıf burs ağları (Konrad-Adenauer/Friedrich-Ebert bursiyerleri güçlü alumni ağına girer), model BM, üniversite kariyer etkinlikleri. Bu alan "kimi tanıdığın" alanıdır — acı ama gerçek.
- **Vize/Blue Card:** Akademik derecen ve yeterli maaşlı bir iş teklifin varsa Blue Card genelde darboğaz değildir; eşik akademik role uygun profilde makuldür (rakam yıla göre değişir, resmi kaynaktan doğrula). Süreç ve zaman çizelgesi için [iş teklifiyle çalışma vizesi yazısına](/tr/blog/germany-work-visa-with-job-offer-process-timeline-fast-track) bak. Master + iş arama vizesi stratejisini [master vs iş-arama vizesi yazısında](/tr/blog/germany-masters-vs-job-seeker-visa-two-keys-career) karşılaştırdım.

Nereden başlayacağını netleştirmek için önce [Almanya'da IR/siyaset bilimi okumak rehberine](/tr/blog/studying-international-relations-political-science-in-germany-as-a-foreigner) göz atmanı öneririm.

## Sonuç & dürüst tavsiye

Almanya'da IR/politika kariyeri **gerçek ve mümkün** ama romantik değil. Dürüst özet: (1) Diplomatlık Alman vatandaşlığı ister — hedefini BM/AB/GIZ/think-tank/NGO yollarına kur. (2) Giriş maaşı kabaca ~40–50k, süreli sözleşme yaygın; think-tank/NGO altta, danışmanlık/uluslararası kuruluş üstte. (3) Almanca kamu için fiilen şart, İngilizce uluslararası katmanı açar — ikisini de geliştir. (4) Staj + network bu alanda diplomadan daha belirleyici; erkenden başla. Alanı stratejik, gözü açık ve sabırlı seç; bu meslek hızlı zengin etmez ama anlamlı ve uluslararasıdır.

*Bu yazı 2026 başı itibarıyla genel bilgi amaçlıdır; maaşlar, vize eşikleri ve kurum politikaları değişir. Somut kararlar için ilgili kurumun resmi kaynağını (Auswärtiges Amt, GIZ, uni-assist/DAAD, ilgili işveren ilanları) doğrula.*
MD;

        $deBody = <<<'MD'
"Ich habe Internationale Beziehungen studiert — kann ich in Deutschland bei der UNO oder in einem Thinktank arbeiten?" Antwort: Ja, das ist möglich — aber du solltest den Unterschied zwischen Traum und Realität von Anfang an kennen. Dieses Feld ist **kompetitiv**, der Karriereweg ist keine gerade Linie, und für internationale Studierende gibt es eine entscheidende Überraschung, wenn es um "Diplomat werden" geht. Dieser Beitrag erklärt dir das deutsche Berufsökosystem für IB/Politik ehrlich — Institution für Institution, Gehalt für Gehalt.

## Welche Sektoren gibt es: die Landkarte der Organisationen

Ein IB-/Politikwissenschaftsabschluss öffnet dir nicht einen Beruf, sondern ein **Spektrum an Sektoren**. Die wichtigsten Akteure im deutschen Kontext:

- **Internationale Organisationen:** UN-Agenturen (starke UN-Präsenz in Bonn — UNV, UNFCCC-Klimasekretariat), EU-Institutionen, OECD, IWF, Weltbank. Englischfreundlich, aber extrem kompetitiv.
- **Entwicklungszusammenarbeit:** die **GIZ** (Deutsche Gesellschaft für Internationale Zusammenarbeit — Sitz Bonn/Eschborn) und die Entwicklungsbank **KfW**. Der Motor der deutschen Entwicklungspolitik und einer der konkretesten Großarbeitgeber für IB-Absolventen.
- **Thinktanks:** **SWP** (Stiftung Wissenschaft und Politik — großer regierungsnaher Thinktank, Berlin), **DGAP** (Deutsche Gesellschaft für Auswärtige Politik), **ECFR**. Prestigeträchtig, aber wenige Stellen.
- **Politische Stiftungen:** **Konrad-Adenauer-Stiftung** (CDU-nah), **Friedrich-Ebert-Stiftung** (SPD-nah) und weitere Parteistiftungen. Auslandsbüros, Stipendien, Politikprogramme — ein breites Beschäftigungsfeld.
- **NGOs, politische Beratung, Journalismus, Wissenschaft.**

Einen Gesamtüberblick über diese Landschaft findest du im Beitrag zum [Arbeitsmarkt mit einem IB-Abschluss](/de/blog/what-to-do-with-a-political-science-ir-degree-in-germany-job-market-de).

## Ökosystem Berlin / Bonn: der Standort ist entscheidend

In diesem Feld ist **wo du bist** genauso wichtig wie was du studiert hast. Es gibt zwei Zentren:

- **Berlin:** das Herz der IB-Karriere. Auswärtiges Amt, die meisten Thinktanks (SWP, DGAP, ECFR), NGOs, Botschaften, die Deutschland-Büros internationaler Medien. Unschlagbar für Praktika und Netzwerk.
- **Bonn:** die "UN-Stadt" — hier sitzen UN-Agenturen und die GIZ-Zentrale. Für Entwicklung und multilaterale Organisationen ist Bonn genauso wichtig wie Berlin.

Warum die Stadt so wichtig ist: Sie öffnet dir das Praktikumsnetzwerk. Die Logik der richtigen Stadt- und Programmwahl habe ich im Beitrag über [Prestige & die richtige Uni-Wahl](/de/blog/how-university-prestige-and-rankings-work-in-germany-choosing-the-right-one-de) ausführlich behandelt.

## Diplomat werden: die Realität der deutschen Staatsbürgerschaft

Das ist der wichtigste ehrliche Punkt für internationale Studierende: **Der Eintritt in den deutschen Auswärtigen Dienst (Diplomat im Auswärtigen Amt) setzt die deutsche Staatsbürgerschaft voraus.** Der Auswärtige Dienst ist ein Beamtenverhältnis, und in der Regel ist die deutsche (in bestimmten Fällen eine andere EU-)Staatsangehörigkeit Bedingung.

Das Ziel "IB studieren und in Deutschland Diplomat werden" ist also ohne Einbürgerung (meist nach langem Aufenthalt + Naturalisierung) **nicht direkt möglich.** Das ist keine Barriere, sondern eine Tatsache: Als Alternative verlangen die Wege über UN, EU, GIZ, Thinktanks und NGOs keine deutsche Staatsbürgerschaft und stehen internationalem Personal viel offener. Richte dein Ziel entsprechend aus.

## Die Gehaltsrealität (ungefähr, bitte prüfen)

Die Zahlen schwanken stark je nach Sektor. **Stand 2025, ungefähr, brutto pro Jahr** (nicht exakt, unbedingt an aktuellen Stellenanzeigen prüfen):

| Bereich | Einstieg (ca. Jahresbrutto) | Hinweis |
|---|---|---|
| Thinktank / NGO | ~35.000–45.000 € | Oft am unteren Ende; befristete Verträge häufig |
| GIZ / Entwicklung | ~40.000–50.000 € | Inland; in Fach-/Feldrollen höher |
| Öffentlicher Dienst / Stiftungen | ~40.000–48.000 € | Nach TVöD (Stufe/Erfahrung) |
| Internationale Organisation (UN/EU) | Variabel, oft höher | Sehr kompetitiv; Einstieg oft über Praktikum/JPO |
| Politik-/Unternehmensberatung | ~45.000–55.000+ € | Oberes Ende; Privatsektor |

Das allgemeine Einstiegsband liegt grob um **~40–50k**; darunter Thinktank/NGO, darüber internationale Organisationen und Beratung. **Befristete Verträge** sind in diesem Feld am Karrierestart sehr verbreitet — kalkuliere das von Anfang an ein.

## Die Sprachrealität: Deutsch im öffentlichen, Englisch im internationalen Bereich

Der am meisten missverstandene Punkt. Die kurze Wahrheit: Es gibt eine **Sprach-Dualität**.

- **Deutscher öffentlicher Dienst, GIZ-Zentrale, Stiftungen, lokale NGOs, die meisten Jobs in Deutschland:** Arbeitssprache ist Deutsch. Für eine ernsthafte Rolle ist **B2/C1-Deutsch faktisch Pflicht.**
- **UN/EU/OECD und internationale Organisationen:** Arbeitssprache Englisch; ein Einstieg ohne Deutsch ist möglich, aber der Wettbewerb ist global.

Praktische Folge: Nur mit Englisch kannst du die Ebene der "internationalen Organisationen" anpeilen — das ist aber die kompetitivste Ebene. Willst du eine stabile Karriere in Deutschland, **investiere in Deutsch** — das vervielfacht die Zahl der Türen. Die Falle, mit einem englischen Master zu starten und Deutsch zu vernachlässigen, habe ich im [Beitrag über englischsprachige Master](/de/blog/english-taught-international-relations-political-science-masters-in-germany-de) ausführlich beschrieben.

## Jobsuche: Praktikum, Netzwerk und Blaue Karte

Der Einstieg in dieses Feld hat **zwei echte Schlüssel**: Praktikum und Netzwerk.

- **Praktikum:** In der IB-/Politikwelt ist das Praktikum fast eine Pflicht-Eintrittstür. SWP, DGAP, Stiftungen, GIZ und Botschaften nehmen regelmäßig Praktikanten. Die erste feste Stelle kommt oft dort, wo du ein Praktikum gemacht hast, oder über dieses Netzwerk.
- **Netzwerk:** Konferenzen, Stipendiennetzwerke der Stiftungen (Stipendiaten von Konrad-Adenauer/Friedrich-Ebert kommen in starke Alumni-Netzwerke), Model UN, Karriereveranstaltungen. Dieses Feld ist ein "Wen kennst du"-Feld — bitter, aber wahr.
- **Visum/Blaue Karte:** Mit deinem akademischen Abschluss und einem ausreichend bezahlten Jobangebot ist die Blaue Karte meist kein Engpass; die Schwelle ist für ein akademisches Profil in der Regel vertretbar (der Betrag ändert sich jährlich, prüfe die offizielle Quelle). Zu Prozess und Zeitplan siehe den [Beitrag zum Arbeitsvisum mit Jobangebot](/de/blog/germany-work-visa-with-job-offer-process-timeline-fast-track-de). Die Strategie Master vs. Jobsuchvisum habe ich im [Beitrag Master vs. Jobsuchvisum](/de/blog/germany-masters-vs-job-seeker-visa-two-keys-career-de) verglichen.

Um zu klären, wo du anfangen solltest, empfehle ich dir zuerst den [Leitfaden zum IB-/Politikstudium in Deutschland](/de/blog/studying-international-relations-political-science-in-germany-as-a-foreigner-de).

## Fazit & ehrlicher Rat

Eine IB-/Politikkarriere in Deutschland ist **real und möglich**, aber nicht romantisch. Ehrliche Zusammenfassung: (1) Diplomat sein setzt deutsche Staatsbürgerschaft voraus — richte dein Ziel auf UN/EU/GIZ/Thinktank/NGO. (2) Das Einstiegsgehalt liegt grob bei ~40–50k, befristete Verträge sind häufig; Thinktank/NGO unten, Beratung/internationale Organisationen oben. (3) Deutsch ist für den öffentlichen Dienst faktisch Pflicht, Englisch öffnet die internationale Ebene — entwickle beides. (4) Praktikum + Netzwerk sind in diesem Feld entscheidender als der Abschluss; fang früh an. Wähle das Feld strategisch, mit offenen Augen und geduldig; dieser Beruf macht nicht schnell reich, ist aber sinnvoll und international.

*Dieser Beitrag dient dem allgemeinen Überblick, Stand Anfang 2026; Gehälter, Visaschwellen und Institutionsrichtlinien ändern sich. Für konkrete Entscheidungen prüfe die offizielle Quelle der jeweiligen Institution (Auswärtiges Amt, GIZ, uni-assist/DAAD, Stellenanzeigen der Arbeitgeber).*
MD;

        $enBody = <<<'MD'
"I studied International Relations — can I work at the UN or a think tank in Germany?" The answer: yes, it's possible — but you should know the gap between the romantic dream and reality from the start. This field is **competitive**, the career path is not a straight line, and for international students there's a crucial surprise when it comes to "becoming a diplomat." This post walks you honestly through Germany's IR/policy job ecosystem — organization by organization, salary by salary.

## Which sectors exist: the map of organizations

An IR/political science degree opens not one profession but a **spectrum of sectors**. The main players in the German context:

- **International organizations:** UN agencies (a strong UN presence in Bonn — UNV, the UNFCCC climate secretariat), EU institutions, the OECD, IMF, World Bank. English-friendly, but extremely competitive.
- **Development cooperation:** **GIZ** (Deutsche Gesellschaft für Internationale Zusammenarbeit — headquartered in Bonn/Eschborn) and the development bank **KfW**. The engine of German development policy and one of the most concrete large employers for IR graduates.
- **Think tanks:** **SWP** (Stiftung Wissenschaft und Politik — a large government-advising think tank, Berlin), **DGAP** (German Council on Foreign Relations), **ECFR**. Prestigious, but few positions.
- **Political foundations (Stiftungen):** **Konrad-Adenauer-Stiftung** (close to the CDU), **Friedrich-Ebert-Stiftung** (close to the SPD) and other party foundations. Foreign offices, scholarships, policy programs — a broad field of employment.
- **NGOs, political consulting, journalism, academia.**

For an overview of this whole landscape, see the post on the [job market with an IR degree](/en/blog/what-to-do-with-a-political-science-ir-degree-in-germany-job-market-en).

## The Berlin / Bonn ecosystem: location is critical

In this field, **where you are** matters as much as what you studied. There are two hubs:

- **Berlin:** the heart of an IR career. The Auswärtiges Amt (Federal Foreign Office), most think tanks (SWP, DGAP, ECFR), NGOs, embassies, the German bureaus of international media. Unbeatable for internships and networking.
- **Bonn:** the "UN city" — home to UN agencies and GIZ headquarters. For development and multilateral organizations, Bonn is as important as Berlin.

The city matters because it opens your internship network. I covered the logic of choosing the right city and program in detail in the post on [prestige & choosing the right university](/en/blog/how-university-prestige-and-rankings-work-in-germany-choosing-the-right-one-en).

## Becoming a diplomat: the German citizenship reality

This is the single most important honest point for international students: **joining the German foreign service (a diplomat in the Auswärtiges Amt) requires German citizenship.** The Auswärtiger Dienst is a civil-servant status (Beamtenverhältnis), and as a rule German citizenship (or, in certain cases, another EU nationality) is a condition.

So the goal of "study IR and become a diplomat in Germany" is **not directly possible** without naturalization (usually after long residence + citizenship). This is not a barrier but a fact: as an alternative, the UN, EU, GIZ, think tank and NGO paths do not require German citizenship and are far more open to international staff. Set your goal accordingly.

## The salary reality (approximate, verify)

The numbers vary widely by sector. **As of 2025, approximate, gross per year** (not exact — always verify against current job postings):

| Field | Entry (approx. annual gross) | Note |
|---|---|---|
| Think tank / NGO | ~€35,000–45,000 | Often the lower end; fixed-term contracts common |
| GIZ / development | ~€40,000–50,000 | Domestic; higher in specialist/field roles |
| Public service / foundations | ~€40,000–48,000 | Per the TVöD scale (grade/experience) |
| International org (UN/EU) | Variable, often higher | Very competitive; entry often via internship/JPO |
| Political/management consulting | ~€45,000–55,000+ | Upper end; private sector |

The general entry band clusters roughly around **~€40–50k**; below it think tanks/NGOs, above it international organizations and consulting. **Fixed-term (befristet) contracts** are very common at the career start in this field — factor that in from the beginning.

## The language reality: German for public, English for international

The most misunderstood point in this field. The short truth: there is a **language duality**.

- **German public service, GIZ headquarters, foundations, local NGOs, most jobs inside Germany:** the working language is German. For a serious role, **B2/C1 German is effectively mandatory.**
- **UN/EU/OECD and international organizations:** the working language is English; entry without German is possible, but the competition is global.

Practical consequence: with English alone you can target the "international organization" layer — but that's the most competitive layer. If you want a stable career inside Germany, **invest in German** — it multiplies the number of doors. I described the trap of starting with an English master's and neglecting German at length in the [post on English-taught master's programs](/en/blog/english-taught-international-relations-political-science-masters-in-germany-en).

## Job search: internships, networking and the Blue Card

Getting into this field has **two real keys**: internships and network.

- **Internship (Praktikum):** In the IR/policy world an internship is almost a mandatory entry door. SWP, DGAP, the foundations, GIZ and embassies take interns regularly. Your first permanent job often comes from where you interned, or through that network.
- **Network:** Conferences, foundation scholarship networks (Konrad-Adenauer/Friedrich-Ebert scholars enter strong alumni networks), Model UN, university career events. This field is a "who you know" field — bitter, but true.
- **Visa/Blue Card:** With your academic degree and a sufficiently paid job offer, the Blue Card is usually not a bottleneck; the threshold is generally reasonable for an academic profile (the amount changes yearly — verify the official source). For the process and timeline, see the [post on the work visa with a job offer](/en/blog/germany-work-visa-with-job-offer-process-timeline-fast-track-en). I compared the master's vs. job-seeker visa strategy in the [master's vs. job-seeker visa post](/en/blog/germany-masters-vs-job-seeker-visa-two-keys-career-en).

To clarify where to start, I recommend first reading the [guide to studying IR/political science in Germany](/en/blog/studying-international-relations-political-science-in-germany-as-a-foreigner-en).

## Conclusion & honest advice

An IR/policy career in Germany is **real and possible**, but not romantic. Honest summary: (1) Being a diplomat requires German citizenship — aim at the UN/EU/GIZ/think tank/NGO paths instead. (2) The entry salary is roughly ~€40–50k, fixed-term contracts are common; think tanks/NGOs at the bottom, consulting/international organizations at the top. (3) German is effectively mandatory for public service, English opens the international layer — develop both. (4) Internships + network matter more than the degree in this field; start early. Choose the field strategically, with open eyes and patience; this profession won't make you rich fast, but it's meaningful and international.

*This post is general information as of early 2026; salaries, visa thresholds and institutional policies change. For concrete decisions, verify the official source of the relevant institution (Auswärtiges Amt, GIZ, uni-assist/DAAD, employer job postings).*
MD;

        $variants = [
            'tr' => ['slug'=>'working-in-international-relations-policy-organizations-in-germany',    'title'=>'Almanya\'da Uluslararası İlişkiler ve Politikada Çalışmak: Kuruluşlar & Gerçek (2026)', 'excerpt'=>'BM/AB/OECD, GIZ/KfW, SWP/DGAP think-tank ve siyasi vakıflar: Almanya\'da IR/politika kariyerinin kuruluşları, maaşları ve dürüst gerçekleri — diplomatlık için vatandaşlık şartı dahil.', 'meta_title'=>'Almanya\'da Uluslararası İlişkiler / Politikada Çalışmak (2026)', 'meta_description'=>'Almanya\'da IR/politika kariyeri: kuruluşlar (BM/AB/GIZ/think-tank), maaş ~40-50k, diplomatlık için Alman vatandaşlığı şartı, Almanca/İngilizce dil gerçeği, staj & network.', 'body'=>$trBody],
            'de' => ['slug'=>'working-in-international-relations-policy-organizations-in-germany-de', 'title'=>'In Internationalen Beziehungen & Politik in Deutschland arbeiten: Organisationen & Realität (2026)', 'excerpt'=>'UN/EU/OECD, GIZ/KfW, SWP/DGAP-Thinktanks und politische Stiftungen: Organisationen, Gehälter und ehrliche Wahrheiten einer IB-/Politikkarriere in Deutschland — inklusive Staatsbürgerschaftspflicht für Diplomaten.', 'meta_title'=>'In IB & Politik in Deutschland arbeiten: Organisationen (2026)', 'meta_description'=>'IB-/Politikkarriere in Deutschland: Organisationen (UN/EU/GIZ/Thinktanks), Gehalt ~40-50k, deutsche Staatsbürgerschaft für Diplomaten, Sprachrealität Deutsch/Englisch, Praktikum & Netzwerk.', 'body'=>$deBody],
            'en' => ['slug'=>'working-in-international-relations-policy-organizations-in-germany-en', 'title'=>'Working in International Relations & Policy in Germany: Organizations & Reality (2026)', 'excerpt'=>'UN/EU/OECD, GIZ/KfW, SWP/DGAP think tanks and political foundations: the organizations, salaries and honest truths of an IR/policy career in Germany — including the citizenship requirement for diplomats.', 'meta_title'=>'Working in IR & Policy in Germany: Organizations (2026)', 'meta_description'=>'IR/policy career in Germany: organizations (UN/EU/GIZ/think tanks), salary ~€40-50k, German citizenship required for diplomats, German/English language reality, internships & networking.', 'body'=>$enBody],
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
            'working-in-international-relations-policy-organizations-in-germany',
            'working-in-international-relations-policy-organizations-in-germany-de',
            'working-in-international-relations-policy-organizations-in-germany-en',
        ])->delete();
    }
};
