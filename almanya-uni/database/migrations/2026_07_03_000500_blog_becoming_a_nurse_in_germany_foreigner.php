<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN): Almanya'da hemşire olmak — yabancılar için genel bakış + iki yol (2026).
 * Doğrulandı: Pflegenotstand (hemşire açığı) + uluslararası alım gerçek; Pflegeberufegesetz 2020 ile
 * generalistische Pflegeausbildung; iki yol = Anerkennung vs Ausbildung; Almanca B2 tipik şart;
 * §16d tanınma / nitelikli işçi oturumu. Sayılar 2025/2026 yaklaşık, resmi kaynaktan doğrulanmalı.
 * Yazar: Halil Yaprakli. Kategori: almanyada-egitim. FK-safe + slug-bazlı idempotent.
 */
return new class extends Migration
{
    public function up(): void
    {
        $groupId = 'f1a10000-1111-4c4e-9f70-ff01aa06cc01';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'almanyada-egitim')->value('id')
            ?? DB::table('categories')->where('slug', 'universities')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
Almanya'da yıllardır büyüyen bir **hemşire açığı (Pflegenotstand)** var ve ülke, dünyanın her yerinden hemşireleri aktif olarak arıyor. Türkiye'den ya da başka bir ülkeden geliyorsan, hemşirelik Almanya'ya yerleşmenin en gerçekçi, en stabil yollarından biri. Ama "gel çalış" kadar basit değil: iki farklı yol, ciddi bir dil şartı ve sabır isteyen bir bürokrasi var. Bu yazı sana resmi tabloyu dürüstçe çiziyor.

## Neden Almanya: hemşire açığı ve talep

Almanya'nın nüfusu yaşlanıyor ve bakıma ihtiyaç duyan insan sayısı her yıl artıyor. Hastaneler, yaşlı bakım evleri (Pflegeheim) ve ayakta bakım hizmetleri kronik olarak personel arıyor. Bu yüzden **uluslararası hemşire alımı devlet politikası** haline geldi; işverenler yurtdışından işe alım yapan ajanslarla çalışıyor.

Senin için bu ne demek? İşin var. Nitelikli bir hemşireysen ya da hemşire olmaya hazırsan, iş bulmak asıl sorun değil. Asıl zorluk **kapıdan içeri girmek**: diplomanı tanıtmak (veya sıfırdan eğitim almak) ve Almancanı yeterli seviyeye çıkarmak.

## İki yol: tanınma mı, Ausbildung mı? (hangisi sana uygun)

Yabancılar için Almanya'da hemşire olmanın iki ana yolu var. Hangisinin sana uygun olduğu, **zaten hemşirelik diploman olup olmadığına** bağlı.

- **Yol 1 — Tanınma (Anerkennung):** Ülkende hemşirelik eğitimi aldıysan, diploman bir eyaletin **Anerkennungsstelle**'sine (tanınma makamı) sunulur; denklik değerlendirilir. Eksik varsa **Kenntnisprüfung** (bilgi sınavı) veya **Anpassungslehrgang** (uyum kursu) ile kapatırsın. Sonunda tam **Pflegefachkraft** statüsü alırsın.
- **Yol 2 — Ausbildung (sıfırdan meslek eğitimi):** Hemşirelik diploman yoksa (veya baştan başlamak istiyorsan), 3 yıllık **generalistische Pflegeausbildung**'a başlarsın. Bu eğitim **maaşlıdır** — teori + hastane/bakım pratiği bir arada. Sonunda Pflegefachfrau/-mann olursun.

| Kriter | Tanınma (Anerkennung) | Ausbildung (sıfırdan) |
|---|---|---|
| Kime uygun | Zaten hemşirelik diploman var | Diploman yok / baştan başlamak istiyorsun |
| Süre | Değişken; eksik varsa +sınav/kurs | ~3 yıl |
| Gelir | Tanınınca tam maaş | Eğitim boyunca maaş (~1.100–1.400€/ay, *2025/2026 yaklaşık, doğrula*) |
| Dil | Genelde **B2** (bazen B1 başlangıç) | Genelde **B2** (bazen B1 başlangıç) |
| Sonuç | Pflegefachkraft | Pflegefachfrau/-mann |
| Vize | §16d (tanınma) / nitelikli işçi | Ausbildung / nitelikli işçi oturumu |

**Kısa kural:** Diploman varsa → tanınma. Diploman yoksa → Ausbildung. İki yolu da ayrıntılı işleyen kardeş yazılarımıza aşağıdan ulaşabilirsin.

## Dil şartı (B2) neden bu kadar kritik

Yeni gelenlerin çoğu için **en büyük engel dil**, tanınma bürokrasisi değil. Bakım işi tamamen iletişim üzerine kurulu: hastayla, aileyle, doktorla, ekiple sürekli konuşursun; hata payı düşük olduğu için dil zayıflığı hem tehlikeli hem de kabul edilemez sayılır.

Pratikte durum şu: çoğu eyalet ve işveren **çalışmak için Almanca B2** ister. Bazıları B1 ile başlatıp süreç içinde B2'ye çıkmanı bekler. Ayrıca bazı yerlerde mesleki dil sınavı (**Fachsprachprüfung**) da istenir. Dürüst tavsiye: **dile herkesten önce başla.** Almancan hazır değilse diğer her şey bekler.

Dil yol haritası için: [Sıfırdan C1'e Almanca öğrenme yol haritası](/tr/blog/learning-german-from-zero-to-c1-a-roadmap-testdafdsh).

## Meslek: generalistische Pflege ne kapsar

2020'de yürürlüğe giren **Pflegeberufegesetz** ile eskiden ayrı olan üç dal — hastane hemşireliği (Krankenpflege), yaşlı bakımı (Altenpflege) ve çocuk hemşireliği (Kinderpflege) — büyük ölçüde **tek bir genelci (generalistische) eğitimde** birleşti.

Bu şu anlama geliyor: **Pflegefachfrau/-mann** unvanı seni hastanede de, yaşlı bakım evinde de, ayakta bakımda da çalışabilir kılar. Alanlar arası geçiş kolaylaşır, kariyer esnekliği artar. İşin özü hasta bakımı, ilaç ve tedavi takibi, dokümantasyon ve ekip içi koordinasyondur.

## Vize ana hatları (§16d, nitelikli işçi)

**Önemli uyarı:** Aşağısı yalnızca genel bir çerçevedir. Vize kuralları eyalete, işverene ve senin durumuna göre değişir; kesin adım için **resmi Anerkennungsstelle ve anerkennung-in-deutschland.de üzerinden doğrula.**

- **§16d — tanınma amaçlı oturum:** Diplomanın tanınması için Almanya'da bulunman gerekiyorsa (örneğin uyum kursu/sınav için), bu oturum tipi kullanılır.
- **Nitelikli işçi oturumu:** Tanınma tamamlanıp iş teklifin varsa, nitelikli işçi olarak çalışma oturumu alırsın.
- **Hızlandırılmış prosedür:** Almanya'da işverenin başlatabileceği **hızlandırılmış nitelikli işçi prosedürü** var; süreci kısaltabilir.

İş teklifiyle çalışma vizesi sürecinin genel işleyişi için: [Almanya iş teklifiyle çalışma vizesi süreci](/tr/blog/germany-work-visa-with-job-offer-process-timeline-fast-track).

## İlk adımlar ve kaynaklar (resmi doğrula)

1. **Yolunu seç:** Diploman var mı? Tanınma. Yok mu? Ausbildung.
2. **Almancaya başla:** Hedef B2. Bunu ertelersen her şey ertelenir.
3. **Diplomanı hazırla (tanınma yolundaysan):** Tercüme + noter; ilgili eyaletin Anerkennungsstelle'sini bul.
4. **İşveren/ajans araştır:** Uluslararası alım yapan hastane ve bakım kuruluşları çok; ama şeffaf, ücret talep etmeyenleri seç.
5. **Resmi kaynaktan teyit et:** **anerkennung-in-deutschland.de** ve **make-it-in-germany.com** başlangıç için doğru adresler.

## Sonuç ve dürüst tavsiye

Almanya'da hemşirelik, yabancılar için gerçekten **kalıcı oturuma (Niederlassung) giden sağlam bir yol.** İş var, meslek talep görüyor, ve süreç doğru işlerse istikrar sunuyor. Ama süslemeyelim: iş **fiziksel ve duygusal olarak zor**, vardiyalı, ve tanınma bürokrasisi **yavaş** ilerleyebilir.

En büyük hatan dili ertelemek olur. İkinci hatan da "hızlı ve garantili" vaat eden aracılara güvenmek olur — garanti yok. Yolunu netleştir, Almancana yüklen, ve her resmi adımı ilgili makamdan teyit et.

Devamı için üç kardeş rehber:
- [Yurtdışı hemşirelik diplomanı tanıtmak: Anerkennung](/tr/blog/getting-your-foreign-nursing-qualification-recognized-in-germany-anerkennung)
- [Almanya'da hemşirelik Ausbildung: maaşlı eğitim](/tr/blog/nursing-ausbildung-in-germany-for-internationals-paid-training)
- [Hemşire olarak çalışmak: maaş, dil ve gerçek](/tr/blog/working-as-a-nurse-in-germany-salary-language-and-reality)

*Bu yazı 2026 başı itibarıyla genel bilgilendirme amaçlıdır; vize, tanınma ve maaş kuralları sık değişir ve eyalete göre farklılık gösterir. Kesin ve güncel bilgi için resmi Anerkennungsstelle, anerkennung-in-deutschland.de ve Almanya konsolosluğunu esas al.*
MD;

        $deBody = <<<'MD'
In Deutschland gibt es seit Jahren einen wachsenden **Pflegenotstand**, und das Land sucht aktiv Pflegekräfte aus aller Welt. Wenn du aus dem Ausland kommst, ist die Pflege einer der realistischsten und stabilsten Wege, um in Deutschland Fuß zu fassen. Aber es ist nicht so einfach wie "komm und arbeite": Es gibt zwei Wege, eine ernsthafte Sprachhürde und eine Bürokratie, die Geduld verlangt. Dieser Artikel zeigt dir das ehrliche Bild.

## Warum Deutschland: Pflegenotstand und Nachfrage

Die deutsche Bevölkerung altert, und die Zahl der pflegebedürftigen Menschen steigt jedes Jahr. Krankenhäuser, Pflegeheime und ambulante Dienste suchen chronisch Personal. Deshalb ist die **internationale Anwerbung von Pflegekräften** faktisch Teil der Politik geworden.

Was heißt das für dich? Es gibt Arbeit. Wenn du eine qualifizierte Pflegekraft bist oder es werden willst, ist der Job nicht das eigentliche Problem. Die echte Hürde ist der **Einstieg**: dein Diplom anerkennen lassen (oder eine Ausbildung machen) und dein Deutsch auf das nötige Niveau bringen.

## Zwei Wege: Anerkennung oder Ausbildung? (was passt zu dir)

Für Ausländer gibt es zwei Hauptwege, in Deutschland als Pflegekraft zu arbeiten. Welcher zu dir passt, hängt davon ab, ob du **bereits eine Pflegeausbildung** hast.

- **Weg 1 — Anerkennung:** Wenn du in deinem Heimatland eine Pflegeausbildung gemacht hast, reichst du dein Diplom bei der **Anerkennungsstelle** eines Bundeslandes ein; die Gleichwertigkeit wird geprüft. Fehlt etwas, schließt du die Lücke mit einer **Kenntnisprüfung** oder einem **Anpassungslehrgang**. Am Ende erhältst du den vollen Status als **Pflegefachkraft**.
- **Weg 2 — Ausbildung (von Grund auf):** Wenn du kein Pflegediplom hast, beginnst du eine 3-jährige **generalistische Pflegeausbildung**. Diese Ausbildung ist **bezahlt** — Theorie plus Praxis im Krankenhaus/in der Pflege. Am Ende bist du Pflegefachfrau/-mann.

| Kriterium | Anerkennung | Ausbildung (von Grund auf) |
|---|---|---|
| Für wen | Du hast bereits ein Pflegediplom | Kein Diplom / du willst neu anfangen |
| Dauer | Variabel; ggf. + Prüfung/Kurs | ~3 Jahre |
| Einkommen | Volles Gehalt nach Anerkennung | Gehalt während der Ausbildung (~1.100–1.400€/Monat, *2025/2026 ungefähr, prüfen*) |
| Sprache | Meist **B2** (manchmal B1 zum Start) | Meist **B2** (manchmal B1 zum Start) |
| Ergebnis | Pflegefachkraft | Pflegefachfrau/-mann |
| Visum | §16d (Anerkennung) / Fachkräfte | Ausbildung / Fachkräfteaufenthalt |

**Kurze Regel:** Diplom vorhanden → Anerkennung. Kein Diplom → Ausbildung. Beide Wege behandeln wir ausführlich in den Schwester-Artikeln unten.

## Warum die Sprachanforderung (B2) so entscheidend ist

Für die meisten Neuankömmlinge ist die **größte Hürde die Sprache**, nicht die Anerkennungsbürokratie. Pflege ist reine Kommunikation: mit Patienten, Angehörigen, Ärzten und dem Team sprichst du ständig; weil die Fehlermarge klein ist, gilt schwaches Deutsch als gefährlich und inakzeptabel.

In der Praxis: Die meisten Bundesländer und Arbeitgeber verlangen **Deutsch B2 zum Arbeiten**. Manche lassen dich mit B1 starten und erwarten, dass du auf B2 kommst. Mancherorts kommt eine **Fachsprachprüfung** dazu. Ehrlicher Rat: **Fang mit der Sprache vor allem anderen an.** Wenn dein Deutsch nicht bereit ist, wartet alles andere.

Für einen Sprachfahrplan: [Deutsch von null auf C1 lernen — Fahrplan](/de/blog/learning-german-from-zero-to-c1-a-roadmap-testdafdsh-de).

## Der Beruf: was die generalistische Pflege umfasst

Mit dem **Pflegeberufegesetz** von 2020 wurden die früher getrennten Bereiche — Krankenpflege, Altenpflege und Kinderkrankenpflege — weitgehend in **einer generalistischen Ausbildung** zusammengeführt.

Das bedeutet: Der Titel **Pflegefachfrau/-mann** macht dich einsetzbar im Krankenhaus, im Pflegeheim und in der ambulanten Pflege. Der Wechsel zwischen Bereichen wird leichter, deine Karriere flexibler. Kern der Arbeit sind Patientenversorgung, Medikamenten- und Behandlungsüberwachung, Dokumentation und Koordination im Team.

## Visum im Überblick (§16d, Fachkräfte)

**Wichtiger Hinweis:** Das Folgende ist nur ein grober Rahmen. Visaregeln hängen vom Bundesland, vom Arbeitgeber und von deiner Situation ab; für die genauen Schritte **prüfe über die offizielle Anerkennungsstelle und anerkennung-in-deutschland.de.**

- **§16d — Aufenthalt zur Anerkennung:** Wenn du für die Anerkennung deines Diploms in Deutschland sein musst (z. B. für Anpassungslehrgang/Prüfung), wird dieser Aufenthaltstyp genutzt.
- **Fachkräfteaufenthalt:** Ist die Anerkennung abgeschlossen und hast du ein Jobangebot, bekommst du einen Aufenthalt als Fachkraft.
- **Beschleunigtes Verfahren:** In Deutschland kann der Arbeitgeber ein **beschleunigtes Fachkräfteverfahren** anstoßen, das den Prozess verkürzen kann.

Zum allgemeinen Ablauf des Arbeitsvisums mit Jobangebot: [Arbeitsvisum mit Jobangebot in Deutschland](/de/blog/germany-work-visa-with-job-offer-process-timeline-fast-track-de).

## Erste Schritte und Quellen (offiziell prüfen)

1. **Wähle deinen Weg:** Diplom vorhanden? Anerkennung. Nein? Ausbildung.
2. **Fang mit Deutsch an:** Ziel B2. Wenn du das aufschiebst, schiebt sich alles auf.
3. **Bereite dein Diplom vor (auf dem Anerkennungsweg):** Übersetzung + Beglaubigung; finde die Anerkennungsstelle des Bundeslandes.
4. **Recherchiere Arbeitgeber/Agenturen:** Es gibt viele Kliniken und Pflegeeinrichtungen, die international anwerben; wähle transparente, die keine Gebühren verlangen.
5. **Bestätige über offizielle Quellen:** **anerkennung-in-deutschland.de** und **make-it-in-germany.com** sind die richtigen Adressen zum Start.

## Fazit und ehrlicher Rat

Pflege in Deutschland ist für Ausländer wirklich ein **solider Weg zur Niederlassung.** Es gibt Arbeit, der Beruf ist gefragt, und wenn der Prozess richtig läuft, bietet er Stabilität. Aber schönreden wollen wir nichts: Die Arbeit ist **körperlich und emotional hart**, im Schichtdienst, und die Anerkennungsbürokratie kann **langsam** sein.

Dein größter Fehler wäre, die Sprache aufzuschieben. Dein zweiter Fehler wäre, Vermittlern zu vertrauen, die "schnell und garantiert" versprechen — Garantien gibt es nicht. Kläre deinen Weg, gib bei Deutsch Vollgas und bestätige jeden offiziellen Schritt bei der zuständigen Stelle.

Zum Weiterlesen die drei Schwester-Artikel:
- [Ausländisches Pflegediplom anerkennen lassen: Anerkennung](/de/blog/getting-your-foreign-nursing-qualification-recognized-in-germany-anerkennung-de)
- [Pflegeausbildung in Deutschland: bezahlte Ausbildung](/de/blog/nursing-ausbildung-in-germany-for-internationals-paid-training-de)
- [Als Pflegekraft arbeiten: Gehalt, Sprache und Realität](/de/blog/working-as-a-nurse-in-germany-salary-language-and-reality-de)

*Dieser Artikel dient der allgemeinen Information (Stand Anfang 2026); Regeln zu Visum, Anerkennung und Gehalt ändern sich häufig und unterscheiden sich je nach Bundesland. Für genaue und aktuelle Informationen halte dich an die offizielle Anerkennungsstelle, anerkennung-in-deutschland.de und das deutsche Konsulat.*
MD;

        $enBody = <<<'MD'
Germany has had a growing **nursing shortage (Pflegenotstand)** for years, and the country is actively recruiting nurses from around the world. If you're coming from abroad, nursing is one of the most realistic and stable ways to settle in Germany. But it's not as simple as "come and work": there are two paths, a serious language barrier, and a bureaucracy that demands patience. This guide gives you the honest picture.

## Why Germany: nursing shortage and demand

Germany's population is ageing, and the number of people needing care rises every year. Hospitals, care homes (Pflegeheim) and outpatient services chronically look for staff. That's why **international recruitment of nurses** has effectively become policy; employers work with agencies that hire from abroad.

What does this mean for you? There is work. If you're a qualified nurse or ready to become one, the job itself isn't the real problem. The real hurdle is **getting through the door**: having your diploma recognised (or training from scratch) and bringing your German up to the required level.

## Two paths: recognition or Ausbildung? (which suits you)

For foreigners, there are two main paths to becoming a nurse in Germany. Which one fits you depends on whether you **already hold a nursing qualification.**

- **Path 1 — Recognition (Anerkennung):** If you trained as a nurse in your home country, you submit your diploma to a state's **Anerkennungsstelle** (recognition authority); equivalence is assessed. If something is missing, you close the gap with a **Kenntnisprüfung** (knowledge exam) or an **Anpassungslehrgang** (adaptation course). In the end you receive full **Pflegefachkraft** status.
- **Path 2 — Ausbildung (from scratch):** If you don't have a nursing diploma (or want to start fresh), you begin the 3-year **generalistische Pflegeausbildung**. This training is **paid** — theory plus hospital/care practice combined. At the end you become a Pflegefachfrau/-mann.

| Criterion | Recognition (Anerkennung) | Ausbildung (from scratch) |
|---|---|---|
| For whom | You already hold a nursing diploma | No diploma / you want to start fresh |
| Duration | Variable; may add exam/course | ~3 years |
| Income | Full salary once recognised | Salary during training (~€1,100–1,400/month, *2025/2026 approx., verify*) |
| Language | Usually **B2** (sometimes B1 to start) | Usually **B2** (sometimes B1 to start) |
| Outcome | Pflegefachkraft | Pflegefachfrau/-mann |
| Visa | §16d (recognition) / skilled worker | Ausbildung / skilled worker permit |

**Short rule:** Diploma in hand → recognition. No diploma → Ausbildung. We cover both paths in detail in the sibling guides below.

## Why the language requirement (B2) is so critical

For most newcomers, the **biggest barrier is language**, not the recognition bureaucracy. Care work is all about communication: you constantly talk to patients, families, doctors and the team; because the margin for error is small, weak German is treated as both dangerous and unacceptable.

In practice: most states and employers require **German B2 to work**. Some let you start at B1 and expect you to reach B2 along the way. In some places a professional language exam (**Fachsprachprüfung**) is also required. Honest advice: **start the language before anything else.** If your German isn't ready, everything else waits.

For a language roadmap: [Learning German from zero to C1](/en/blog/learning-german-from-zero-to-c1-a-roadmap-testdafdsh-en).

## The profession: what generalistische Pflege covers

With the **Pflegeberufegesetz** that took effect in 2020, the previously separate fields — hospital nursing (Krankenpflege), elderly care (Altenpflege) and paediatric nursing (Kinderpflege) — were largely merged into **one generalist (generalistische) training**.

This means the title **Pflegefachfrau/-mann** makes you employable in hospitals, care homes and outpatient care. Moving between fields becomes easier and your career more flexible. The core of the job is patient care, medication and treatment monitoring, documentation and team coordination.

## Visa in outline (§16d, skilled worker)

**Important note:** the following is only a rough framework. Visa rules depend on the state, the employer and your situation; for the exact steps **verify via the official Anerkennungsstelle and anerkennung-in-deutschland.de.**

- **§16d — residence for recognition:** If you need to be in Germany to have your diploma recognised (e.g. for an adaptation course/exam), this residence type is used.
- **Skilled worker residence:** Once recognition is complete and you have a job offer, you get a residence permit as a skilled worker.
- **Fast-track procedure:** In Germany the employer can initiate an **accelerated skilled worker procedure** that can shorten the process.

For how the work visa with a job offer generally works: [Germany work visa with a job offer](/en/blog/germany-work-visa-with-job-offer-process-timeline-fast-track-en).

## First steps and resources (verify officially)

1. **Choose your path:** Do you have a diploma? Recognition. No? Ausbildung.
2. **Start German:** Target B2. If you postpone this, you postpone everything.
3. **Prepare your diploma (if on the recognition path):** translation + certification; find your state's Anerkennungsstelle.
4. **Research employers/agencies:** there are many hospitals and care providers recruiting internationally; choose transparent ones that don't charge fees.
5. **Confirm via official sources:** **anerkennung-in-deutschland.de** and **make-it-in-germany.com** are the right places to start.

## Conclusion and honest advice

Nursing in Germany is genuinely a **solid path to permanent residence (Niederlassung)** for foreigners. There's work, the profession is in demand, and when the process runs right, it offers stability. But let's not sugar-coat it: the work is **physically and emotionally hard**, involves shifts, and the recognition bureaucracy can be **slow**.

Your biggest mistake would be postponing the language. Your second mistake would be trusting intermediaries who promise "fast and guaranteed" — there are no guarantees. Clarify your path, go all in on German, and confirm every official step with the responsible authority.

To read on, the three sibling guides:
- [Getting your foreign nursing qualification recognised: Anerkennung](/en/blog/getting-your-foreign-nursing-qualification-recognized-in-germany-anerkennung-en)
- [Nursing Ausbildung in Germany: paid training](/en/blog/nursing-ausbildung-in-germany-for-internationals-paid-training-en)
- [Working as a nurse: salary, language and reality](/en/blog/working-as-a-nurse-in-germany-salary-language-and-reality-en)

*This article is for general information as of early 2026; rules on visas, recognition and salary change often and differ by state. For accurate and up-to-date information, rely on the official Anerkennungsstelle, anerkennung-in-deutschland.de and the German consulate.*
MD;

        $variants = [
            'tr' => ['slug'=>'becoming-a-nurse-in-germany-as-a-foreigner',    'title'=>'Almanya\'da Hemşire Olmak: Yabancılar İçin Rehber (2026)', 'excerpt'=>'Almanya\'da hemşire olmanın iki yolu: yurtdışı diplomanın tanınması (Anerkennung) mı, yoksa sıfırdan maaşlı Ausbildung mı? Dil şartı (B2), vize ana hatları ve dürüst ilk adımlar.', 'meta_title'=>'Almanya\'da Hemşire Olmak: Yabancılar İçin Rehber (2026)', 'meta_description'=>'Yabancılar için Almanya\'da hemşirelik: tanınma vs Ausbildung, B2 dil şartı, §16d vize ana hatları ve ilk adımlar. Dürüst, güncel rehber (2026).', 'body'=>$trBody],
            'de' => ['slug'=>'becoming-a-nurse-in-germany-as-a-foreigner-de', 'title'=>'Pflegekraft werden in Deutschland: Leitfaden für Ausländer (2026)', 'excerpt'=>'Zwei Wege in die Pflege in Deutschland: Anerkennung eines ausländischen Diploms oder bezahlte Ausbildung von Grund auf. Sprachanforderung (B2), Visum im Überblick und ehrliche erste Schritte.', 'meta_title'=>'Pflegekraft werden in Deutschland: Leitfaden für Ausländer (2026)', 'meta_description'=>'Pflege in Deutschland für Ausländer: Anerkennung vs Ausbildung, Sprachniveau B2, Visum (§16d) und erste Schritte. Ehrlicher, aktueller Leitfaden (2026).', 'body'=>$deBody],
            'en' => ['slug'=>'becoming-a-nurse-in-germany-as-a-foreigner-en', 'title'=>'Becoming a Nurse in Germany: A Guide for Foreigners (2026)', 'excerpt'=>'Two paths to nursing in Germany: getting a foreign diploma recognised (Anerkennung) or paid Ausbildung from scratch. The B2 language requirement, visa outline and honest first steps.', 'meta_title'=>'Becoming a Nurse in Germany: A Guide for Foreigners (2026)', 'meta_description'=>'Nursing in Germany for foreigners: recognition vs Ausbildung, B2 language requirement, §16d visa outline and first steps. Honest, up-to-date guide (2026).', 'body'=>$enBody],
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
        Post::whereIn('slug', ['becoming-a-nurse-in-germany-as-a-foreigner', 'becoming-a-nurse-in-germany-as-a-foreigner-de', 'becoming-a-nurse-in-germany-as-a-foreigner-en'])->delete();
    }
};
