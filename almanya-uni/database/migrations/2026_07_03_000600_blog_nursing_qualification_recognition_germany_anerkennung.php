<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN): Yurtdışı hemşirelik diplomasının Almanya'da tanınması (Anerkennung) (2026).
 * Doğrulandı: Anerkennung eyalet Anerkennungsstelle üzerinden yürür; denklikte fark varsa
 * Kenntnisprüfung veya Anpassungslehrgang ile kapatılır; §16d tanınma vizesi + B2/Fachsprachprüfung.
 * Süre/adım eyalete göre değişir; resmi anerkennung-in-deutschland.de'den doğrula.
 * Yazar: Halil Yaprakli. Kategori: almanyada-egitim. FK-safe + slug-bazlı idempotent.
 */
return new class extends Migration
{
    public function up(): void
    {
        $groupId = 'f2a20000-2222-4c4e-9f70-ff01aa06cc02';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'almanyada-egitim')->value('id')
            ?? DB::table('categories')->where('slug', 'universities')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
Türkiye'de (ya da başka bir ülkede) hemşirelik okuyup diploma aldıysan, Almanya'da sıfırdan eğitime başlamana gerek yok. Diplomanı **tanıtma (Anerkennung)** süreciyle mevcut niteliğini Almanya'da geçerli hale getirebilir, tam yetkili **Pflegefachkraft** olarak çalışabilirsin. Ama bu süreç ne kadar cazip olsa da bürokratik ve dile bağlı — bu yazıda adım adım, dürüstçe anlatıyorum.

## Anerkennung nedir, kim için?

**Anerkennung**, yurtdışında aldığın mesleki niteliğin (burada: hemşirelik) Almanya'daki karşılığıyla **denkliğinin resmi olarak tanınması** demektir. Hemşirelik Almanya'da **düzenlenmiş bir meslektir (reglementierter Beruf)** — yani "Pflegefachkraft" unvanıyla çalışmak için devlet tanıması **şart**. Diploma tek başına yetmez.

Bu yol senin için, eğer:
- Ülkende **tamamlanmış bir hemşirelik eğitimin/diploman** varsa,
- Almanya'da sıfırdan 3 yıllık [Ausbildung](/tr/blog/nursing-ausbildung-in-germany-for-internationals-paid-training) yapmak yerine mevcut niteliğini kullanmak istiyorsan,
- Ve dil şartını (genelde B2) karşılamaya hazırsan.

Genel çerçeve ve iki yolun karşılaştırması için [Almanya'da hemşire olmak: yabancılar için rehber](/tr/blog/becoming-a-nurse-in-germany-as-a-foreigner) yazısına da bakabilirsin.

## Süreç: Anerkennungsstelle'ye başvuru → değerlendirme → Bescheid

Süreç kabaca şöyle işler (adımlar ve süre **eyalete göre değişir**, aşağıyı yol haritası olarak oku, resmi kaynaktan doğrula):

1. **Yetkili makamı bul.** Her eyaletin (Bundesland) kendi **Anerkennungsstelle**'si var. Hangi eyalette çalışmak istiyorsan oranın makamına başvurursun.
2. **Başvuru + belgeler.** Diploma, transkript (ders içerikleri/saatleri), kimlik, meslek tecrüben, çeviriler (yeminli) ve genelde apostil/tasdik istenir.
3. **Denklik değerlendirmesi.** Makam, senin eğitiminle Alman Pflegefachkraft eğitimini **içerik ve saat** açısından karşılaştırır.
4. **Karar (Bescheid).** Sonuçta yazılı bir karar alırsın:
   - **Tam denklik (volle Gleichwertigkeit):** doğrudan tanınırsın.
   - **Kısmi denklik (wesentliche Unterschiede):** önemli farklar var → bir **telafi önlemi** ile kapatman gerekir (aşağıdaki tablo).

## Eksik varsa: Kenntnisprüfung mü Anpassungslehrgang mı?

Eğer Bescheid "önemli farklar var" derse, iki telafi yolundan biriyle bunu kapatırsın. Çoğu eyalette **seçim hakkı sende olabilir** ama detay eyalete/duruma göre değişir:

| Kriter | **Kenntnisprüfung** (bilgi sınavı) | **Anpassungslehrgang** (uyum kursu) |
|---|---|---|
| Ne | Teorik + pratik sınav | Denetimli çalışma/eğitim dönemi |
| Süre | Kısa (hazırlık ayları + sınav) | Genelde birkaç ay – ~1 yıla kadar (fark kadar) |
| Avantaj | Hızlı olabilir | Sınav baskısı yok, iş başında öğrenirsin |
| Dezavantaj | Sınav riski, yoğun hazırlık | Daha uzun sürebilir, işveren/kurum bağı gerekir |
| Sonuç | Başarınca tam tanınma | Tamamlayınca tam tanınma |

**Kalın gerçek:** Hangisinin sana uygun olduğu diploma-fark miktarına, Almanca seviyene ve bir işveren/eğitim yeri bulup bulamamana bağlı. Kesin süreyi sana ancak **kendi Bescheid'ın** söyler.

## Dil: B2 + Fachsprachprüfung

Tanınma dosyanı ne kadar iyi hazırlarsan hazırla, **dil olmadan çalışamazsın**. Bakım işi tamamen iletişime dayalı: hasta, aile, doktor, dokümantasyon.

- **Genel Almanca:** çoğu eyalet/işveren **B2** ister (bazıları B1'le başlatıp B2'yi çalışırken tamamlatabilir).
- **Fachsprachprüfung (mesleki dil sınavı):** birçok eyalette sağlık meslekleri için **mesleki Almanca sınavı** ayrıca istenir — tıbbi terminoloji, hasta görüşmesi, devir teslim.

Dil planını erken kur. Yapılandırılmış bir başlangıç için [sıfırdan C1'e Almanca öğrenme yol haritası](/tr/blog/learning-german-from-zero-to-c1-a-roadmap-testdafdsh) işine yarayabilir.

## §16d: tanınma amaçlı vize

Türkiye'den geliyorsan ve tanınman **Almanya'da** bir telafi önlemi (Kenntnisprüfung/Anpassungslehrgang) gerektiriyorsa, bunun için özel bir oturum türü var: **§16d (Aufenthalt zur Anerkennung ausländischer Berufsqualifikationen)**.

- **Amaç:** telafi önlemini Almanya'da tamamlaman için oturma izni.
- **Genelde gerekir:** başlamış bir tanınma süreci (ya da telafi ihtiyacını gösteren Bescheid), yeterli dil (sıkça B1/B2), geçim/finansman kanıtı.
- Alternatif: doğrudan **iş teklifiyle** nitelikli işçi oturumu da mümkün olabilir; Almanya'da **hızlandırılmış nitelikli işçi prosedürü** var. İş teklifiyle vize sürecinin genel mantığı için [iş teklifiyle Almanya çalışma vizesi](/tr/blog/germany-work-visa-with-job-offer-process-timeline-fast-track) yazısına bak.

Öğrenci olarak gelip amaç değiştirmeyi düşünenler için [öğrenci vizesinden çalışma iznine geçiş (Zweckwechsel)](/tr/blog/changing-student-visa-to-work-permit-germany-zweckwechsel) yazısı da faydalı olabilir.

*Vize/oturum kuralları sık değişir — kesin şartı Alman konsolosluğu ve resmi Anerkennungsstelle üzerinden doğrula.*

## Süre & bürokrasi gerçeği

Dürüst olalım: **tanınma bürokrasisi yavaş olabilir.** Belge toplama, yeminli çeviri, apostil, makamın değerlendirmesi ve telafi önlemi derken süreç aylara — bazen daha uzuna — yayılabilir. Süre;
- hangi eyalete başvurduğuna,
- belgelerinin ne kadar eksiksiz olduğuna,
- telafi önlemi gerekip gerekmediğine

göre ciddi değişir. Kimse sana "şu kadar haftada biter" diye **garanti veremez** — verilirse şüphelen. Dosyanı baştan eksiksiz ve düzenli hazırlamak en çok zaman kazandıran şeydir.

## Sonuç & dürüst tavsiye

Yurtdışı diploman Almanya'da **değerli** — Pflegenotstand (bakım açığı) yüzünden tanınmış yabancı hemşireye çok talep var. Ama süreç iki şeye bağlı: **temiz bir tanınma dosyası** ve **dil**. Benim tavsiyem:

1. Erkenden **hedef eyaleti** ve onun **Anerkennungsstelle**'sini belirle.
2. **Belgelerini** (transkript, saatler, çeviriler) baştan derli topla.
3. **Almancaya paralel** başla — B2 + mesleki dil en büyük darboğaz.
4. Telafi önlemi çıkarsa (Kenntnisprüfung/Anpassungslehrgang), Bescheid'ın ne dediğine göre kararını ver.

Çalışma tarafındaki gerçek (maaş, dil, koşullar) için [Almanya'da hemşire olarak çalışmak](/tr/blog/working-as-a-nurse-in-germany-salary-language-and-reality) yazısına da göz at.

*Bu yazı 2026 başı itibarıyla genel bilgilendirme amaçlıdır; süreç, süre, dil eşikleri ve vize şartları eyalete ve zamana göre değişir. Bağlayıcı bilgi için resmi Anerkennungsstelle ve anerkennung-in-deutschland.de kaynaklarını doğrula.*
MD;

        $deBody = <<<'MD'
Wenn du deine Pflegeausbildung im Ausland abgeschlossen hast, musst du in Deutschland nicht bei null anfangen. Mit der **Anerkennung** deiner ausländischen Qualifikation kannst du als vollwertige **Pflegefachkraft** arbeiten. Der Weg ist attraktiv, aber bürokratisch und stark sprachabhängig – hier erkläre ich ihn dir ehrlich, Schritt für Schritt.

## Was ist Anerkennung, und für wen?

**Anerkennung** bedeutet, dass deine im Ausland erworbene Qualifikation offiziell als **gleichwertig** zur deutschen Ausbildung anerkannt wird. Pflege ist in Deutschland ein **reglementierter Beruf** – um als "Pflegefachkraft" zu arbeiten, ist die staatliche Anerkennung **Pflicht**. Ein Diplom allein reicht nicht.

Dieser Weg ist für dich, wenn du:
- eine **abgeschlossene Pflegeausbildung/ein Diplom** aus deinem Heimatland hast,
- deine vorhandene Qualifikation nutzen willst, statt eine neue [Ausbildung](/de/blog/nursing-ausbildung-in-germany-for-internationals-paid-training-de) zu machen,
- und bereit bist, die Sprachanforderung (meist B2) zu erfüllen.

Einen Überblick über beide Wege findest du im Beitrag [Pflegekraft in Deutschland werden](/de/blog/becoming-a-nurse-in-germany-as-a-foreigner-de).

## Der Ablauf: Antrag bei der Anerkennungsstelle → Prüfung → Bescheid

Der Ablauf sieht ungefähr so aus (Schritte und Dauer **hängen vom Bundesland ab** – lies das als Orientierung und prüfe amtlich nach):

1. **Zuständige Stelle finden.** Jedes Bundesland hat seine eigene **Anerkennungsstelle**. Du bewirbst dich dort, wo du arbeiten möchtest.
2. **Antrag + Unterlagen.** Diplom, Transcript (Inhalte/Stunden), Ausweis, Berufserfahrung, beglaubigte Übersetzungen und oft Apostille.
3. **Gleichwertigkeitsprüfung.** Die Stelle vergleicht deine Ausbildung inhaltlich und nach Stundenzahl mit der deutschen Pflegeausbildung.
4. **Bescheid.** Du bekommst eine schriftliche Entscheidung:
   - **Volle Gleichwertigkeit:** direkte Anerkennung.
   - **Wesentliche Unterschiede:** du musst sie mit einer **Ausgleichsmaßnahme** schließen (siehe Tabelle).

## Bei Unterschieden: Kenntnisprüfung oder Anpassungslehrgang?

Wenn der Bescheid "wesentliche Unterschiede" feststellt, schließt du die Lücke über einen von zwei Wegen. In vielen Bundesländern hast du evtl. ein **Wahlrecht**, aber die Details variieren:

| Kriterium | **Kenntnisprüfung** | **Anpassungslehrgang** |
|---|---|---|
| Was | Theoretische + praktische Prüfung | Betreute Praxis-/Lernphase |
| Dauer | Kurz (Vorbereitung + Prüfung) | Meist einige Monate – bis ~1 Jahr |
| Vorteil | Kann schnell gehen | Kein Prüfungsdruck, Lernen in der Praxis |
| Nachteil | Prüfungsrisiko, intensive Vorbereitung | Kann länger dauern, braucht Einrichtung/Arbeitgeber |
| Ergebnis | Bei Bestehen volle Anerkennung | Nach Abschluss volle Anerkennung |

**Klartext:** Was zu dir passt, hängt vom Umfang der Unterschiede, deinem Deutschniveau und davon ab, ob du eine Einrichtung findest. Die genaue Dauer sagt dir nur **dein eigener Bescheid**.

## Sprache: B2 + Fachsprachprüfung

Egal wie gut deine Akte ist – **ohne Sprache kannst du nicht arbeiten**. Pflege ist reine Kommunikation: Patient, Angehörige, Ärzte, Dokumentation.

- **Allgemeines Deutsch:** die meisten Bundesländer/Arbeitgeber verlangen **B2** (manche starten mit B1, B2 wird berufsbegleitend nachgeholt).
- **Fachsprachprüfung:** in vielen Bundesländern zusätzlich Pflicht für Gesundheitsberufe – medizinische Terminologie, Patientengespräch, Übergabe.

Plane die Sprache früh. Für einen strukturierten Start kann dir ein [Fahrplan Deutsch von null auf C1](/de/blog/learning-german-from-zero-to-c1-a-roadmap-testdafdsh-de) helfen.

## §16d: Visum zur Anerkennung

Wenn du aus dem Ausland kommst und deine Anerkennung eine Ausgleichsmaßnahme **in Deutschland** erfordert, gibt es dafür einen eigenen Aufenthaltstitel: **§16d (Aufenthalt zur Anerkennung ausländischer Berufsqualifikationen)**.

- **Zweck:** Aufenthalt, um die Ausgleichsmaßnahme in Deutschland zu absolvieren.
- **Meist nötig:** ein begonnenes Anerkennungsverfahren (oder Bescheid), ausreichende Sprache (oft B1/B2), Nachweis der Finanzierung.
- Alternative: ein Aufenthalt als Fachkraft direkt **mit Jobangebot**; Deutschland hat ein **beschleunigtes Fachkräfteverfahren**. Die Logik des Visums mit Jobangebot erklärt der Beitrag [Arbeitsvisum mit Jobangebot](/de/blog/germany-work-visa-with-job-offer-process-timeline-fast-track-de).

Wer als Student kommt und den Zweck wechseln will, findet Hilfe im Beitrag [vom Studentenvisum zur Arbeitserlaubnis (Zweckwechsel)](/de/blog/changing-student-visa-to-work-permit-germany-zweckwechsel-de).

*Visa-/Aufenthaltsregeln ändern sich häufig – prüfe die genauen Bedingungen beim deutschen Konsulat und bei der zuständigen Anerkennungsstelle.*

## Dauer & Bürokratie – ehrlich

Ehrlich: **die Anerkennungsbürokratie kann langsam sein.** Unterlagen sammeln, beglaubigte Übersetzungen, Apostille, die Prüfung der Stelle und die Ausgleichsmaßnahme – das kann sich über Monate ziehen. Die Dauer hängt ab von:
- welchem Bundesland du beantragst,
- wie vollständig deine Unterlagen sind,
- ob eine Ausgleichsmaßnahme nötig ist.

Niemand kann dir eine feste Frist **garantieren** – sei skeptisch, wenn es doch jemand tut. Eine von Anfang an vollständige, saubere Akte spart am meisten Zeit.

## Fazit & ehrlicher Rat

Dein ausländisches Diplom ist in Deutschland **wertvoll** – wegen des Pflegenotstands ist die Nachfrage nach anerkannten ausländischen Pflegekräften hoch. Der Erfolg hängt an zwei Dingen: einer **sauberen Anerkennungsakte** und der **Sprache**. Mein Rat:

1. Lege früh dein **Ziel-Bundesland** und dessen **Anerkennungsstelle** fest.
2. Sammle deine **Unterlagen** (Transcript, Stunden, Übersetzungen) von Anfang an ordentlich.
3. Starte **parallel mit Deutsch** – B2 + Fachsprache ist der größte Engpass.
4. Kommt eine Ausgleichsmaßnahme, entscheide nach deinem Bescheid.

Die Realität auf der Arbeitsseite (Gehalt, Sprache, Bedingungen) findest du im Beitrag [als Pflegekraft in Deutschland arbeiten](/de/blog/working-as-a-nurse-in-germany-salary-language-and-reality-de).

*Dieser Beitrag ist eine allgemeine Information mit Stand Anfang 2026; Ablauf, Dauer, Sprachschwellen und Visabedingungen ändern sich je nach Bundesland und Zeitpunkt. Verbindliche Auskünfte findest du bei der zuständigen Anerkennungsstelle und unter anerkennung-in-deutschland.de.*
MD;

        $enBody = <<<'MD'
If you finished your nursing education abroad, you don't have to start over in Germany. Through the **recognition process (Anerkennung)** you can have your existing qualification validated and work as a fully licensed **Pflegefachkraft** (nurse). It's an attractive route – but bureaucratic and heavily language-dependent. Here is the honest, step-by-step version.

## What is Anerkennung, and who is it for?

**Anerkennung** means your foreign qualification (here: nursing) is officially recognized as **equivalent** to German training. Nursing in Germany is a **regulated profession (reglementierter Beruf)** – to work as a "Pflegefachkraft" state recognition is **mandatory**. A diploma alone is not enough.

This route is for you if you:
- have a **completed nursing education/diploma** from your home country,
- want to use your existing qualification instead of doing a fresh [Ausbildung](/en/blog/nursing-ausbildung-in-germany-for-internationals-paid-training-en),
- and are ready to meet the language requirement (usually B2).

For an overview of both routes, see [becoming a nurse in Germany as a foreigner](/en/blog/becoming-a-nurse-in-germany-as-a-foreigner-en).

## The process: apply to the Anerkennungsstelle → assessment → Bescheid

The process roughly works like this (steps and timeline **depend on the federal state** – read this as a roadmap and verify officially):

1. **Find the competent authority.** Each federal state (Bundesland) has its own **Anerkennungsstelle**. You apply in the state where you want to work.
2. **Application + documents.** Diploma, transcript (content/hours), ID, work experience, certified translations and often an apostille.
3. **Equivalence assessment.** The authority compares your training with German nursing training by **content and hours**.
4. **Decision (Bescheid).** You receive a written decision:
   - **Full equivalence:** direct recognition.
   - **Substantial differences:** you must close them with a **compensation measure** (see table).

## If there are gaps: Kenntnisprüfung vs Anpassungslehrgang

If the Bescheid finds "substantial differences", you close the gap via one of two paths. In many states you may have a **choice**, but details vary:

| Criterion | **Kenntnisprüfung** (knowledge exam) | **Anpassungslehrgang** (adaptation course) |
|---|---|---|
| What | Theoretical + practical exam | Supervised practice/learning phase |
| Duration | Short (prep + exam) | Usually a few months – up to ~1 year |
| Upside | Can be fast | No exam pressure, learn on the job |
| Downside | Exam risk, intense prep | Can take longer, needs an employer/facility |
| Result | Full recognition on passing | Full recognition on completion |

**Blunt fact:** what suits you depends on the size of the differences, your German level, and whether you can secure a facility/employer. Only **your own Bescheid** tells you the exact requirement.

## Language: B2 + Fachsprachprüfung

No matter how strong your file is, **without language you cannot work**. Care work is all communication: patient, family, doctors, documentation.

- **General German:** most states/employers require **B2** (some start at B1 and let you reach B2 while working).
- **Fachsprachprüfung (professional language exam):** in many states an additional requirement for health professions – medical terminology, patient interviews, handover.

Plan language early. For a structured start, a [German from zero to C1 roadmap](/en/blog/learning-german-from-zero-to-c1-a-roadmap-testdafdsh-en) can help.

## §16d: the recognition visa

If you come from abroad and your recognition requires a compensation measure **in Germany** (Kenntnisprüfung/Anpassungslehrgang), there is a dedicated residence permit: **§16d (residence for the recognition of foreign professional qualifications)**.

- **Purpose:** residence to complete the compensation measure in Germany.
- **Usually required:** a started recognition procedure (or Bescheid), sufficient language (often B1/B2), proof of funding.
- Alternative: a skilled-worker residence directly **with a job offer**; Germany has an **accelerated skilled-worker procedure**. For the logic of the job-offer visa, see [Germany work visa with a job offer](/en/blog/germany-work-visa-with-job-offer-process-timeline-fast-track-en).

If you arrive as a student and want to switch purpose, the post on [changing a student visa to a work permit (Zweckwechsel)](/en/blog/changing-student-visa-to-work-permit-germany-zweckwechsel-en) may help.

*Visa/residence rules change often – verify the exact conditions with the German consulate and the competent Anerkennungsstelle.*

## Duration & bureaucracy – honestly

Let's be honest: **recognition bureaucracy can be slow.** Gathering documents, certified translations, apostilles, the authority's assessment and the compensation measure can stretch over months – sometimes longer. The duration depends on:
- which state you apply in,
- how complete your documents are,
- whether a compensation measure is needed.

Nobody can **guarantee** you a fixed timeline – be skeptical if someone does. A complete, clean file from the start saves the most time.

## Conclusion & honest advice

Your foreign diploma is **valuable** in Germany – because of the care shortage (Pflegenotstand), demand for recognized foreign nurses is high. Success rests on two things: a **clean recognition file** and **language**. My advice:

1. Decide early on your **target state** and its **Anerkennungsstelle**.
2. Gather your **documents** (transcript, hours, translations) neatly from the start.
3. Start **German in parallel** – B2 + professional language is the biggest bottleneck.
4. If a compensation measure is required, decide based on what your Bescheid says.

For the reality on the working side (salary, language, conditions), see [working as a nurse in Germany](/en/blog/working-as-a-nurse-in-germany-salary-language-and-reality-en).

*This post is general information as of early 2026; the process, timelines, language thresholds and visa conditions vary by federal state and over time. For binding information, verify with the competent Anerkennungsstelle and anerkennung-in-deutschland.de.*
MD;

        $variants = [
            'tr' => ['slug'=>'getting-your-foreign-nursing-qualification-recognized-in-germany-anerkennung',    'title'=>'Yurtdışı Hemşirelik Diplomanı Almanya\'da Tanıtmak: Anerkennung Rehberi (2026)', 'excerpt'=>'Yurtdışı hemşirelik diploman Almanya\'da nasıl tanınır? Anerkennungsstelle başvurusu, denklik değerlendirmesi, Bescheid, Kenntnisprüfung vs Anpassungslehrgang, B2/Fachsprachprüfung ve §16d tanınma vizesi — adım adım, dürüst rehber.', 'meta_title'=>'Almanya Hemşirelik Diploma Tanınması (Anerkennung) 2026', 'meta_description'=>'Yurtdışı hemşirelik diplomanı Almanya\'da tanıtma rehberi: Anerkennungsstelle, denklik, Bescheid, Kenntnisprüfung vs Anpassungslehrgang, B2 ve §16d vizesi.', 'body'=>$trBody],
            'de' => ['slug'=>'getting-your-foreign-nursing-qualification-recognized-in-germany-anerkennung-de', 'title'=>'Anerkennung ausländischer Pflegeabschlüsse in Deutschland: der Leitfaden (2026)', 'excerpt'=>'Wie wird dein ausländischer Pflegeabschluss in Deutschland anerkannt? Antrag bei der Anerkennungsstelle, Gleichwertigkeitsprüfung, Bescheid, Kenntnisprüfung vs Anpassungslehrgang, B2/Fachsprachprüfung und §16d – ehrlich und Schritt für Schritt.', 'meta_title'=>'Anerkennung ausländischer Pflegeabschlüsse Deutschland 2026', 'meta_description'=>'Leitfaden zur Anerkennung ausländischer Pflegeabschlüsse: Anerkennungsstelle, Gleichwertigkeit, Bescheid, Kenntnisprüfung vs Anpassungslehrgang, B2 und §16d-Visum.', 'body'=>$deBody],
            'en' => ['slug'=>'getting-your-foreign-nursing-qualification-recognized-in-germany-anerkennung-en', 'title'=>'Getting Your Foreign Nursing Qualification Recognized in Germany: Anerkennung Guide (2026)', 'excerpt'=>'How is a foreign nursing qualification recognized in Germany? Applying to the Anerkennungsstelle, equivalence assessment, Bescheid, Kenntnisprüfung vs Anpassungslehrgang, B2/Fachsprachprüfung and the §16d recognition visa — an honest, step-by-step guide.', 'meta_title'=>'Foreign Nursing Qualification Recognition Germany (Anerkennung) 2026', 'meta_description'=>'Guide to getting a foreign nursing qualification recognized in Germany: Anerkennungsstelle, equivalence, Bescheid, Kenntnisprüfung vs Anpassungslehrgang, B2 and the §16d visa.', 'body'=>$enBody],
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
            'getting-your-foreign-nursing-qualification-recognized-in-germany-anerkennung',
            'getting-your-foreign-nursing-qualification-recognized-in-germany-anerkennung-de',
            'getting-your-foreign-nursing-qualification-recognized-in-germany-anerkennung-en',
        ])->delete();
    }
};
