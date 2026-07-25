<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN): Almanya'da Uluslararası İlişkiler / Siyaset Bilimi okumak (2026).
 * Doğrulandı: Bachelor genelde Almanca (C1), İngilizce master bol; tepe bölümler FU Berlin
 * (Otto-Suhr-Institut), Mannheim (ampirik), Tübingen (IR); Berlin kariyer merkezi (Auswärtiges
 * Amt, SWP/DGAP/ECFR). Kamu ücreti ~150-350€/dönem, BW non-EU ~1.500€. Sayılar yıl-hedge'li.
 * Yazar: Halil Yaprakli. Kategori: almanyada-egitim. slug-bazlı idempotent.
 */
return new class extends Migration
{
    public function up(): void
    {
        $groupId = 'c6d10000-1111-4f7b-9fa0-cc04dd09ff01';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'almanyada-egitim')->value('id')
            ?? DB::table('categories')->where('slug', 'universities')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
Uluslararası İlişkiler ve Siyaset Bilimi, Türk öğrenciler arasında Almanya'da en çok merak edilen sosyal bilim alanlarından. Almanya'nın Avrupa'nın siyasi ağırlık merkezi olması, Berlin'in diplomasi ve think-tank ekosistemi, bu alanı cazip kılıyor. Ama bu, "diploma alınca kolayca büyükelçi olunur" demek değil. Bu yazı, alanı gerçekçi biçimde anlatıyor: ne okunur, hangi dilde, nerede ve dürüst beklenti ne olmalı.

## 1. Alan kapsamı: Uluslararası İlişkiler mi, Siyaset Bilimi mi?

Almanya'da bu iki alan çoğu zaman aynı çatı altında ama farklı vurgularla okutulur. **Politikwissenschaft (Siyaset Bilimi)** daha geniş şemsiye: siyaset teorisi, karşılaştırmalı siyaset, Alman/Avrupa siyasal sistemi ve uluslararası ilişkileri kapsar. **Internationale Beziehungen (Uluslararası İlişkiler / IR)** ise devletler arası ilişkiler, dış politika, güvenlik, uluslararası kuruluşlar ve küresel yönetişime odaklanır.

Pratik fark şu: Almanya'da saf "IR bachelor" nadirdir; genelde bir Politikwissenschaft lisansı okur, sonra uzmanlaşırsın. IR daha çok **master seviyesinde** ayrı bir program olarak karşına çıkar. Mannheim gibi bazı bölümler **ampirik/kantitatif** (anket, istatistik, veri), Tübingen gibi bazıları **teori ve IR ağırlıklı**. Alan seçimini yaparken bu vurgu farkını okumak, isimden daha önemli.

## 2. Bachelor Almanca, master İngilizce: dil gerçeği

En kritik nokta bu. **Bachelor programları büyük çoğunlukla Almancadır ve genelde C1 seviyesi ister.** İngilizce lisans bu alanda nadirdir. Yani Türkiye'den lisans için gelecek bir öğrencinin ciddi Almanca yatırımı yapması gerekir.

**Master seviyesinde tablo tersine döner: İngilizce program boldur.** IR, public policy ve political science alanında İngilizce yüksek lisanslar Almanya'da yaygın. Bu yüzden birçok Türk öğrenci için mantıklı rota: lisansı Türkiye'de (veya Almanca'yla burada) bitir, İngilizce master için Almanya'ya gel. İngilizce master seçenekleri kümedeki [İngilizce IR/kamu politikası master rehberinde](/tr/blog/english-taught-international-relations-political-science-masters-in-germany) ayrıntılı.

Not: İngilizce master'a girsen bile, Almanya'da staj ve kamu işleri için Almanca çoğu zaman şart. Dili "master İngilizce diye" tamamen erteleme.

## 3. Tepe bölümler (2026 itibarıyla, yaklaşık)

Almanya'da "Ivy League" mantığı yoktur; bölüm gücü programa göre değişir. Aşağıdaki tablo, alanda saygın kabul edilen bölümlerin bir özeti (kesin sıralama değil — [Almanya'da prestij ve sıralama nasıl işler yazısına](/tr/blog/how-university-prestige-and-rankings-work-in-germany-choosing-the-right-one) bak):

| Üniversite | Öne çıkan yönü | Not |
|---|---|---|
| **FU Berlin (Otto-Suhr-Institut)** | Almanya'nın en büyük ve en saygın siyaset bilimi enstitüsü | Berlin konumu büyük artı |
| Humboldt Berlin | Güçlü poli-sci + teori | FU/Potsdam ile ortak IR master |
| **Mannheim** | Ampirik/kantitatif siyaset biliminin zirvesi | Veri/istatistik ağırlıklı |
| **Tübingen** | Güçlü Uluslararası İlişkiler geleneği | IR odaklı öğrenci için ideal |
| Heidelberg, Frankfurt/Goethe | Teori + siyaset sosyolojisi güçlü | Köklü bölümler |
| **Bremen (BIGSSS)** | İngilizce IR/sosyal bilim doktora-master köprüsü | Uluslararası profil |
| Konstanz, LMU, Bonn, Köln | Dengeli, güçlü bölümler | Geniş yelpaze |

**Konum, çoğu zaman üniversitenin adından daha önemli** — bunu 5. bölümde açıyorum.

## 4. Başvuru: uni-assist, NC ve belgeler

Uluslararası öğrenciler başvuruyu çoğunlukla **uni-assist** üzerinden yapar (bazı üniversitelerin kendi portalı olabilir). Süreç kabaca: denklik kontrolü, dil belgesi (Almanca C1 lisans için / İngilizceyse IELTS-TOEFL master için), transkript ve motivasyon mektubu.

**Numerus Clausus (NC)** siyaset biliminde sık karşılaşılan bir kısıt: kontenjan sınırlıysa, kabul not ortalamasına (Abitur/lisans notu) göre yapılır. NC her yıl ve her üniversitede değişir; sabit bir eşik yoktur. Master'da NC yerine genelde lisans notu + motivasyon + bazen ilgili iş/staj tecrübesi değerlendirilir.

Başvuru tarihleri ve tam belge listesini **her zaman üniversitenin resmi sayfasından ve uni-assist'ten doğrula** — bunlar dönemden döneme değişir.

## 5. Konumun önemi: neden Berlin öne çıkıyor?

IR/siyaset biliminde **konum, kariyer için belki de en belirleyici faktör.** Berlin, Almanya'nın siyaset başkenti: **Auswärtiges Amt (Dışişleri Bakanlığı)**, önde gelen think-tank'ler (**SWP, DGAP, ECFR**), sayısız NGO, uluslararası kuruluş temsilcilikleri ve siyasi vakıflar burada. Staj ve network fırsatları açısından Berlin, alanın kalbi.

Bonn/Eschborn ekseni de önemli: kalkınma iş birliği kuruluşu **GIZ**'in merkezi burada. Yani "hangi şehir" sorusu, "hangi üniversite" sorusundan daha stratejik olabilir. Bir bölüm çok saygın ama küçük bir şehirdeyse, staj ve iş için taşınman gerekebilir. Kariyer tarafının detayı için [Almanya'da IR ve politikada çalışmak yazısına](/tr/blog/working-in-international-relations-policy-organizations-in-germany) bak.

## 6. Ücret ve burs: DAAD ve maliyet

Almanya'da **kamu üniversiteleri esasen ücretsizdir**; sadece dönem katkı payı ödersin (2025/2026 itibarıyla yaklaşık **150–350€/dönem**, semester ticket dahil olabilir). Tek büyük istisna **Baden-Württemberg**: AB-dışı öğrencilerden dönem başına yaklaşık **1.500€** alınır. Hertie School gibi özel okullar ise çok daha pahalı ve ayrı kategoridir.

Burs tarafında en bilinen adres **DAAD**; ayrıca siyasi vakıfların (Konrad-Adenauer, Friedrich-Ebert vb.) bursları alanla doğrudan ilgili öğrenciler için değerli. Yaşam maliyeti şehre göre değişir — Berlin/Münih pahalı, küçük şehirler daha uygun. Rakamlar yıldan yıla değişir; **güncel tutarları resmi kaynaktan doğrula.**

## 7. Sonuç ve dürüst tavsiye

Almanya'da Uluslararası İlişkiler / Siyaset Bilimi okumak, doğru planlanırsa güçlü bir yol. Ama dürüst olalım: bu bir **generalist** diploma. Tek net kariyer yolu yoktur, alan rekabetçidir; diplomanın kendisi değil, **staj + network + diller** seni ayakta tutar.

Bir kaç dürüst not: (1) Alman diplomat kadrosu (Auswärtiges Amt) **Alman vatandaşlığı** ister — vatandaşlığın yoksa bu kapı (şimdilik) kapalı. Uluslararası kuruluşlar (BM/AB) İngilizce-dostu ama çok rekabetçi. (2) Lisansı Almanca okumaya hazır değilsen, İngilizce master rotası daha gerçekçi. (3) Master mı yoksa iş arama vizesiyle mi ilerleyeceğine karar veriyorsan, [master vs iş arama vizesi karşılaştırmasını](/tr/blog/germany-masters-vs-job-seeker-visa-two-keys-career) oku. (4) Diplomanla ne yapabileceğini merak ediyorsan [siyaset bilimi/IR diplomasıyla iş piyasası yazısı](/tr/blog/what-to-do-with-a-political-science-ir-degree-in-germany-job-market) tam sana göre.

*Bu yazı 2026 yılı için genel bir rehberdir ve bireysel danışmanlığın yerini tutmaz. Program içerikleri, ücretler, NC eşikleri, dil şartları ve başvuru tarihleri zamanla değişir; karar vermeden önce her rakamı ve şartı üniversitenin resmi sayfasından, DAAD'dan ve uni-assist'ten doğrula.*
MD;

        $deBody = <<<'MD'
Internationale Beziehungen und Politikwissenschaft gehören zu den beliebtesten sozialwissenschaftlichen Fächern für internationale Studierende in Deutschland. Deutschland ist das politische Schwergewicht Europas, und Berlin ist ein Zentrum für Diplomatie und Think-Tanks. Das macht das Feld attraktiv – aber ein Diplom bedeutet nicht automatisch eine Karriere als Diplomat. Dieser Beitrag zeigt dir das Feld realistisch: was du studierst, in welcher Sprache, wo und mit welchen ehrlichen Erwartungen.

## 1. Was das Feld umfasst: Internationale Beziehungen oder Politikwissenschaft?

In Deutschland liegen beide Bereiche oft unter einem Dach, aber mit unterschiedlichem Schwerpunkt. **Politikwissenschaft** ist das breitere Dach: politische Theorie, vergleichende Politik, das deutsche und europäische politische System sowie internationale Beziehungen. **Internationale Beziehungen (IB)** konzentrieren sich auf zwischenstaatliche Beziehungen, Außenpolitik, Sicherheit, internationale Organisationen und Global Governance.

Der praktische Unterschied: Einen reinen "IB-Bachelor" gibt es in Deutschland selten. Meist studierst du einen Politikwissenschafts-Bachelor und spezialisierst dich später. IB begegnet dir eher auf **Masterebene** als eigenes Programm. Manche Institute wie Mannheim sind stark **empirisch/quantitativ** (Umfragen, Statistik, Daten), andere wie Tübingen eher **theorie- und IB-orientiert**. Diesen Schwerpunkt zu lesen ist wichtiger als der Name.

## 2. Bachelor auf Deutsch, Master auf Englisch: die Sprachrealität

Das ist der entscheidende Punkt. **Bachelorprogramme sind überwiegend auf Deutsch und verlangen meist C1.** Englischsprachige Bachelor sind in diesem Feld selten. Wer für den Bachelor herkommt, muss also ernsthaft in Deutsch investieren.

**Auf Masterebene dreht sich das Bild: englischsprachige Programme gibt es reichlich.** In IB, Public Policy und Political Science sind englische Master in Deutschland weit verbreitet. Deshalb ist für viele internationale Studierende die sinnvolle Route: den Bachelor im Heimatland (oder auf Deutsch hier) machen und für einen englischen Master nach Deutschland kommen. Details findest du im [Leitfaden zu englischsprachigen IB-/Public-Policy-Mastern](/de/blog/english-taught-international-relations-political-science-masters-in-germany-de).

Hinweis: Auch mit einem englischen Master brauchst du für Praktika und Jobs im öffentlichen Sektor meist Deutsch. Schieb die Sprache nicht komplett auf.

## 3. Starke Institute (Stand 2026, ungefähr)

In Deutschland gibt es keine "Ivy League"; die Stärke hängt vom Programm ab. Die folgende Tabelle ist eine Übersicht angesehener Institute (keine feste Rangliste – siehe [wie Prestige und Rankings in Deutschland funktionieren](/de/blog/how-university-prestige-and-rankings-work-in-germany-choosing-the-right-one-de)):

| Universität | Stärke | Hinweis |
|---|---|---|
| **FU Berlin (Otto-Suhr-Institut)** | Größtes und angesehenstes politikwissenschaftliches Institut | Standort Berlin ist ein großer Vorteil |
| Humboldt Berlin | Starke Politikwissenschaft + Theorie | Gemeinsamer IB-Master mit FU/Potsdam |
| **Mannheim** | Spitze der empirisch/quantitativen Politikwissenschaft | Daten/Statistik-orientiert |
| **Tübingen** | Starke Tradition in Internationalen Beziehungen | Ideal für IB-Fokus |
| Heidelberg, Frankfurt/Goethe | Theorie + politische Soziologie | Etablierte Institute |
| **Bremen (BIGSSS)** | Englischsprachige IB/Sozialwissenschaft | Internationales Profil |
| Konstanz, LMU, Bonn, Köln | Ausgewogene, starke Institute | Breites Spektrum |

**Der Standort ist oft wichtiger als der Name** – dazu mehr in Abschnitt 5.

## 4. Bewerbung: uni-assist, NC und Unterlagen

Internationale Studierende bewerben sich meist über **uni-assist** (manche Unis haben ein eigenes Portal). Der Ablauf grob: Anerkennungsprüfung, Sprachnachweis (Deutsch C1 für den Bachelor / IELTS-TOEFL für englische Master), Transcript und Motivationsschreiben.

Der **Numerus Clausus (NC)** ist in der Politikwissenschaft häufig: Bei begrenzten Plätzen erfolgt die Zulassung nach dem Notendurchschnitt (Abitur/Bachelornote). Der NC ändert sich jedes Jahr und je Uni; einen festen Schwellenwert gibt es nicht. Im Master zählen statt NC meist Bachelornote, Motivation und manchmal einschlägige Praxis.

Bewerbungsfristen und die genaue Unterlagenliste **immer auf der offiziellen Uni-Seite und bei uni-assist prüfen** – sie ändern sich von Semester zu Semester.

## 5. Warum der Standort zählt: das Beispiel Berlin

In IB/Politikwissenschaft ist der **Standort vielleicht der entscheidendste Karrierefaktor.** Berlin ist die politische Hauptstadt: das **Auswärtige Amt**, führende Think-Tanks (**SWP, DGAP, ECFR**), zahlreiche NGOs, Vertretungen internationaler Organisationen und politische Stiftungen sind hier. Für Praktika und Netzwerke ist Berlin das Herz des Feldes.

Auch die Achse Bonn/Eschborn ist wichtig: Dort sitzt die Entwicklungsorganisation **GIZ**. Die Frage "welche Stadt" kann also strategischer sein als "welche Uni". Ist ein angesehenes Institut in einer kleinen Stadt, musst du für Praktikum und Job vielleicht umziehen. Mehr zur Karriereseite im [Beitrag über Arbeiten in IB und Politik](/de/blog/working-in-international-relations-policy-organizations-in-germany-de).

## 6. Kosten und Stipendien: DAAD und Ausgaben

**Staatliche Universitäten sind grundsätzlich kostenlos**; du zahlst nur den Semesterbeitrag (Stand 2025/2026 etwa **150–350 €/Semester**, oft inkl. Semesterticket). Die große Ausnahme ist **Baden-Württemberg**: Nicht-EU-Studierende zahlen rund **1.500 €/Semester**. Private Schulen wie die Hertie School sind deutlich teurer und eine eigene Kategorie.

Bei Stipendien ist der **DAAD** die bekannteste Adresse; auch die Stipendien politischer Stiftungen (Konrad-Adenauer, Friedrich-Ebert usw.) passen gut zum Feld. Die Lebenshaltungskosten hängen von der Stadt ab – Berlin/München teuer, kleinere Städte günstiger. Die Zahlen ändern sich jährlich; **prüfe aktuelle Beträge bei der offiziellen Quelle.**

## 7. Fazit und ehrlicher Rat

In Deutschland IB / Politikwissenschaft zu studieren ist bei guter Planung ein starker Weg. Aber ehrlich: Es ist ein **generalistisches** Diplom. Es gibt keinen einzigen klaren Karriereweg, das Feld ist umkämpft; nicht das Diplom selbst, sondern **Praktika + Netzwerk + Sprachen** tragen dich.

Ein paar ehrliche Hinweise: (1) Der deutsche diplomatische Dienst (Auswärtiges Amt) verlangt die **deutsche Staatsbürgerschaft** – ohne sie ist diese Tür (vorerst) zu. Internationale Organisationen (UN/EU) sind englischfreundlich, aber sehr umkämpft. (2) Bist du nicht bereit, den Bachelor auf Deutsch zu machen, ist die Route über einen englischen Master realistischer. (3) Entscheidest du zwischen Master und Job-Suche, lies den [Vergleich Master vs. Jobsuchvisum](/de/blog/germany-masters-vs-job-seeker-visa-two-keys-career-de). (4) Willst du wissen, was du mit dem Diplom machen kannst, ist der [Beitrag zum Arbeitsmarkt für Politik-/IB-Absolventen](/de/blog/what-to-do-with-a-political-science-ir-degree-in-germany-job-market-de) genau richtig.

*Dieser Beitrag ist ein allgemeiner Leitfaden für das Jahr 2026 und ersetzt keine individuelle Beratung. Programminhalte, Gebühren, NC-Werte, Sprachanforderungen und Bewerbungsfristen ändern sich mit der Zeit; prüfe vor jeder Entscheidung jede Zahl und Anforderung auf der offiziellen Uni-Seite, beim DAAD und bei uni-assist.*
MD;

        $enBody = <<<'MD'
International Relations and Political Science are among the most popular social-science fields for international students in Germany. Germany is Europe's political heavyweight, and Berlin is a hub for diplomacy and think-tanks, which makes the field attractive. But a degree does not automatically turn into a career as a diplomat. This post gives you a realistic picture: what you study, in which language, where, and what your honest expectations should be.

## 1. What the field covers: International Relations or Political Science?

In Germany the two areas often sit under one roof but with different emphases. **Politikwissenschaft (Political Science)** is the broader umbrella: political theory, comparative politics, the German and European political system, and international relations. **Internationale Beziehungen (International Relations / IR)** focuses on inter-state relations, foreign policy, security, international organisations and global governance.

The practical difference: a pure "IR bachelor" is rare in Germany. Usually you take a Political Science bachelor and specialise later. IR appears more often as a separate programme at **master's level**. Some institutes such as Mannheim are strongly **empirical/quantitative** (surveys, statistics, data), while others such as Tübingen lean toward **theory and IR**. Reading this emphasis matters more than the name on the door.

## 2. Bachelor in German, master's in English: the language reality

This is the key point. **Bachelor programmes are overwhelmingly in German and usually require C1.** English-taught bachelors are rare in this field. So if you come for a bachelor, you must invest seriously in German.

**At master's level the picture flips: English-taught programmes are plentiful.** In IR, public policy and political science, English master's degrees are widespread in Germany. That is why a sensible route for many international students is: do the bachelor at home (or in German here) and come to Germany for an English master's. See the details in the [guide to English-taught IR / public policy master's](/en/blog/english-taught-international-relations-political-science-masters-in-germany-en).

Note: even with an English master's, you will usually need German for internships and public-sector jobs. Do not postpone the language entirely just because the master's is in English.

## 3. Strong departments (as of 2026, approximate)

Germany has no "Ivy League"; strength depends on the programme. The table below summarises respected departments (not a fixed ranking — see [how prestige and rankings work in Germany](/en/blog/how-university-prestige-and-rankings-work-in-germany-choosing-the-right-one-en)):

| University | Strength | Note |
|---|---|---|
| **FU Berlin (Otto-Suhr-Institut)** | Largest and most respected political-science institute | Berlin location is a big plus |
| Humboldt Berlin | Strong poli-sci + theory | Joint IR master's with FU/Potsdam |
| **Mannheim** | Peak of empirical/quantitative political science | Data/statistics-heavy |
| **Tübingen** | Strong International Relations tradition | Ideal for an IR focus |
| Heidelberg, Frankfurt/Goethe | Theory + political sociology | Established departments |
| **Bremen (BIGSSS)** | English-taught IR/social science | International profile |
| Konstanz, LMU, Bonn, Köln | Balanced, strong departments | Broad range |

**Location is often more important than the name** — more on that in section 5.

## 4. Applying: uni-assist, NC and documents

International students mostly apply through **uni-assist** (some universities have their own portal). The process, roughly: credential check, language proof (German C1 for the bachelor / IELTS-TOEFL for English master's), transcript and a motivation letter.

The **Numerus Clausus (NC)** is common in political science: when places are limited, admission is based on grade average (Abitur/bachelor grade). The NC changes every year and by university; there is no fixed threshold. At master's level, instead of an NC, admission usually weighs the bachelor grade, motivation and sometimes relevant work/internship experience.

Always **verify application deadlines and the exact document list on the university's official page and with uni-assist** — they change from term to term.

## 5. Why location matters: the Berlin example

In IR/political science, **location is perhaps the single most decisive career factor.** Berlin is Germany's political capital: the **Auswärtiges Amt (Federal Foreign Office)**, leading think-tanks (**SWP, DGAP, ECFR**), countless NGOs, representations of international organisations and political foundations are all here. For internships and networking, Berlin is the heart of the field.

The Bonn/Eschborn axis matters too: the development agency **GIZ** is headquartered there. So "which city" can be more strategic than "which university". If a respected department sits in a small town, you may need to relocate for internships and jobs. For the career side, see the [post on working in IR and policy](/en/blog/working-in-international-relations-policy-organizations-in-germany-en).

## 6. Fees and scholarships: DAAD and cost

**Public universities are essentially free**; you only pay the semester contribution (as of 2025/2026 roughly **€150–350/semester**, often including a semester ticket). The one big exception is **Baden-Württemberg**: non-EU students pay about **€1,500/semester**. Private schools such as the Hertie School are far more expensive and a separate category.

For scholarships, the best-known address is the **DAAD**; the scholarships of political foundations (Konrad-Adenauer, Friedrich-Ebert, etc.) are also a strong fit for this field. Living costs depend on the city — Berlin/Munich expensive, smaller towns cheaper. The figures change year to year; **verify current amounts with the official source.**

## 7. Conclusion and honest advice

Studying IR / Political Science in Germany can be a strong path if you plan it well. But let's be honest: it is a **generalist** degree. There is no single clear career path, the field is competitive; not the degree itself but **internships + network + languages** are what carry you.

A few honest notes: (1) The German diplomatic service (Auswärtiges Amt) requires **German citizenship** — without it, that door is (for now) closed. International organisations (UN/EU) are English-friendly but highly competitive. (2) If you are not ready to do the bachelor in German, the English-master's route is more realistic. (3) If you are deciding between a master's and a job search, read the [master's vs. job-seeker visa comparison](/en/blog/germany-masters-vs-job-seeker-visa-two-keys-career-en). (4) If you want to know what you can do with the degree, the [post on the job market for political-science/IR graduates](/en/blog/what-to-do-with-a-political-science-ir-degree-in-germany-job-market-en) is exactly for you.

*This post is a general guide for the year 2026 and does not replace individual advice. Programme content, fees, NC thresholds, language requirements and application deadlines change over time; before making any decision, verify every figure and requirement on the university's official page, with the DAAD, and with uni-assist.*
MD;

        $variants = [
            'tr' => ['slug'=>'studying-international-relations-political-science-in-germany-as-a-foreigner',    'title'=>'Almanya\'da Uluslararası İlişkiler / Siyaset Bilimi Okumak: Rehber (2026)', 'excerpt'=>'Almanya\'da Uluslararası İlişkiler ve Siyaset Bilimi (Politikwissenschaft) okumak: IR vs poli-sci farkı, Almanca bachelor vs bol İngilizce master, tepe bölümler (FU Berlin/Otto-Suhr, Mannheim, Tübingen), uni-assist & NC başvurusu, neden Berlin, ücret & DAAD ve dürüst kariyer tavsiyesi (2026).', 'meta_title'=>'Almanya\'da Uluslararası İlişkiler / Siyaset Bilimi Okumak 2026', 'meta_description'=>'Almanya\'da IR / Siyaset Bilimi: Almanca bachelor vs İngilizce master, FU Berlin/Mannheim/Tübingen, uni-assist & NC, Berlin\'in önemi, ücret & DAAD, dürüst tavsiye (2026).', 'body'=>$trBody],
            'de' => ['slug'=>'studying-international-relations-political-science-in-germany-as-a-foreigner-de', 'title'=>'Internationale Beziehungen / Politikwissenschaft in Deutschland studieren (2026)', 'excerpt'=>'Internationale Beziehungen und Politikwissenschaft in Deutschland studieren: Unterschied IB vs. Poli-Sci, deutscher Bachelor vs. viele englische Master, starke Institute (FU Berlin/Otto-Suhr, Mannheim, Tübingen), Bewerbung über uni-assist & NC, warum Berlin, Kosten & DAAD und ehrlicher Rat (2026).', 'meta_title'=>'IB / Politikwissenschaft in Deutschland studieren 2026', 'meta_description'=>'IB / Politikwissenschaft in Deutschland: deutscher Bachelor vs. englischer Master, FU Berlin/Mannheim/Tübingen, uni-assist & NC, warum Berlin, Kosten & DAAD (2026).', 'body'=>$deBody],
            'en' => ['slug'=>'studying-international-relations-political-science-in-germany-as-a-foreigner-en', 'title'=>'Studying International Relations / Political Science in Germany (2026)', 'excerpt'=>'Studying International Relations and Political Science in Germany: IR vs poli-sci, German bachelor vs plentiful English master\'s, strong departments (FU Berlin/Otto-Suhr, Mannheim, Tübingen), applying via uni-assist & NC, why Berlin matters, fees & DAAD, and honest career advice (2026).', 'meta_title'=>'Studying International Relations / Political Science in Germany 2026', 'meta_description'=>'IR / Political Science in Germany: German bachelor vs English master\'s, FU Berlin/Mannheim/Tübingen, uni-assist & NC, why Berlin matters, fees & DAAD, honest advice (2026).', 'body'=>$enBody],
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
            'studying-international-relations-political-science-in-germany-as-a-foreigner',
            'studying-international-relations-political-science-in-germany-as-a-foreigner-de',
            'studying-international-relations-political-science-in-germany-as-a-foreigner-en',
        ])->delete();
    }
};
