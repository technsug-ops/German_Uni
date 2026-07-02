<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN): Almanya'da maaşlı doktora & araştırma kariyeri (2026).
 * Doğrulandı: Almanya'da doktora genelde maaşlı iş (TV-L E13, harç yok), 3-5 yıl.
 * İki yol (IMPRS/yapılandırılmış vs Doktorvater), Max Planck/Helmholtz/Fraunhofer/Leibniz.
 * WissZeitVG süreli sözleşme, sanayi daha iyi öder. Maaş/eşik 2025/2026 hedge'li.
 * Yazar: Halil Yaprakli. Kategori: almanyada-egitim. FK-safe + slug-bazlı idempotent.
 */
return new class extends Migration
{
    public function up(): void
    {
        $groupId = 'e3a30000-3333-4b3d-9f60-ee01ff05bb03';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'almanyada-egitim')->value('id')
            ?? DB::table('categories')->where('slug', 'universities')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
Türkiye'de "doktora yapmak" çoğu zaman bir masraf, bir fedakârlık gibi düşünülür: yıllarca okursun, harç ödersin, belki asistanlıkla ayakta durursun. Almanya'da tablo tersine döner. Burada doktora genellikle **maaşlı bir iştir** — üniversite ya da araştırma enstitüsü sana bir pozisyon verir, TV-L E13 tarifesine göre maaş yatar ve **öğrenim harcı ödemezsin**. Bu yazıda maaşlı doktoranın nasıl işlediğini, iki başvuru yolunu, Max Planck gibi dev enstitüleri ve kimsenin sana söylemediği dürüst gerçekleri anlatıyorum.

## Doktora = maaşlı bir iş (TV-L E13, harç yok)

Almanya'da doktora, çoğu doğa bilimi ve mühendislik alanında bir **öğrenci statüsü değil, bir çalışan statüsüdür**. Tipik olarak bir profesörün ya da enstitünün araştırma projesinde **bilimsel çalışan (wissenschaftlicher Mitarbeiter)** olarak işe alınırsın ve maaşın kamu tarife sistemi **TV-L E13**'e göre belirlenir.

- **Harç yok:** Doktora için "tuition" ödemezsin; sadece dönemlik semester katkısı (~150–350€) olabilir.
- **Maaş (yaklaşık, 2025/2026 itibarıyla; doğrula):** tam pozisyon TV-L E13 brüt yaklaşık **€2.800–4.200/ay** aralığında; doğa bilimlerinde sık sık **%50–75 kısmi pozisyon** verilir, yani eline geçen daha düşük olabilir.
- **Süre:** genelde **3–5 yıl**. Unvan doğa bilimlerinde **"Dr. rer. nat."**
- **Alternatif:** maaşlı pozisyon yerine **burslu** (stipendium) da olabilirsin — bu durumda sosyal sigorta ve vergi durumu farklıdır.

**Kısaca: doktora bir maliyet değil, bir gelir kaynağıdır.** Türk öğrencilerin en çok şaşırdığı gerçek budur.

## İki yol: yapılandırılmış program vs. geleneksel Doktorvater

Almanya'da doktoraya iki farklı kapıdan girilir. İkisi de geçerli, ama havaları çok farklı.

| | Yapılandırılmış program | Geleneksel (Doktorvater) |
|---|---|---|
| Nasıl bulunur | İlana/programa başvuru | Doğrudan profesöre e-posta/teklif |
| Dil | Sık sık **İngilizce** | Bölüme göre değişir |
| Yapı | Ders + seminer + tez | Ağırlıklı tez, serbest |
| Örnek | **IMPRS**, Graduate School, Helmholtz okulları | Bir kürsüde tek asistan |
| Uluslararası | Çok yüksek | Değişken |

- **Yapılandırılmış (structured):** **IMPRS (International Max Planck Research Schools)**, üniversite Graduate School'ları ve Helmholtz araştırma okulları. Başvuru rekabetçi, program organize, kohort halinde ilerlersin, İngilizce-dostudur. Yurt dışından gelen için **en erişilebilir yol** budur.
- **Geleneksel (Doktorvater/Doktormutter):** Bir profesörü bulur, araştırma fikrini pitch'ler, kabul alırsan onun ekibinde çalışırsın. Daha serbest ama daha bireyseldir; **doğru profesörü bulmak** her şeydir.

## Araştırma enstitüleri: Max Planck, Helmholtz, Fraunhofer, Leibniz

Almanya'nın gücü sadece üniversitelerde değil, dünya çapındaki **dört büyük araştırma ağında**dır. Doktora ve postdoc pozisyonlarının büyük kısmı buralarda açılır.

| Ağ | Odak | Örnek alan |
|---|---|---|
| **Max Planck** | Temel/merak-güdümlü araştırma | Fizik, moleküler biyoloji, nörobilim |
| **Helmholtz** | Büyük bilim, altyapı | Parçacık fiziği, enerji, sağlık |
| **Fraunhofer** | Uygulamalı, sanayi-yakın | Malzeme, üretim, uygulamalı fizik |
| **Leibniz** | Karma temel + uygulamalı | Çevre, ekonomi, yaşam bilimleri |

Max Planck **merak** için, Helmholtz **büyük altyapı** için, Fraunhofer **sanayiye yakın uygulama** için, Leibniz ise **ikisinin arası** için düşün. Doğa bilimcisiysen bu enstitülerin iş ilanı sayfalarını (ör. IMPRS portalları) düzenli takip et.

## Başvuru: pozisyon ara, teklif al, vize

Doktora başvurusu bir "üniversite başvurusu" değil, neredeyse bir **iş başvurusudur**.

1. **Pozisyon/ilan ara:** IMPRS portalları, üniversite kürsü sayfaları, Helmholtz/Leibniz iş panoları, `academics.de`, `EURAXESS`.
2. **Doğrudan iletişim:** geleneksel yolda profesöre kısa, kişiye özel bir e-posta + CV + ilgi alanı özeti.
3. **Belgeler:** yüksek lisans diploması + transkript, motivasyon/araştırma önerisi, referanslar, İngilizce (çoğu araştırma İngilizce) ve bazı sanayi/klinik rollerde **Almanca**.
4. **Teklif ve sözleşme:** kabul edilirsen iş sözleşmesi (TV-L E13) ya da burs mektubu alırsın.
5. **Vize:** AB dışından geliyorsan **araştırmacı/çalışma vizesi** ya da öğrenci vizesi ile başlarsın; iş sözleşmesi süreci kolaylaştırır. Detay için [Almanya iş teklifiyle çalışma vizesi süreci](/tr/blog/germany-work-visa-with-job-offer-process-timeline-fast-track) yazısına bak.

## Dürüst gerçek: akademi güvencesiz olabilir

Şimdi kimsenin broşüre yazmadığı kısım. Almanya akademisi parlak ama **kırılgan bir kariyer yolu** sunar.

- **Süreli sözleşmeler:** Almanya'da bir yasa var — **WissZeitVG** — kalıcı olmayan bilimsel personelin ne kadar süre süreli sözleşmeyle çalışabileceğini **sınırlar**. Doktora + postdoc yıllarının toplamı bu süreye sayılır. Yani profesör olamazsan bir noktada **akademik kapı kapanabilir**.
- **Az sayıda kalıcı kadro:** Profesörlük pozisyonları çok azdır ve rekabet serttir; postdoc'tan profesöre giden yol dar ve belirsizdir.
- **Sanayi daha iyi öder:** Doktora sonrası **sanayi genellikle akademiden daha yüksek maaş** ve daha stabil sözleşme verir. Fizikçiler sık sık **veri bilimi, finans, danışmanlık** alanlarına geçer.

Bu bir vazgeçme çağrısı değil — bir **gözü açık gitme** çağrısı. Doktora harika bir yatırımdır; ama "ne olursa olsun profesör olacağım" diye değil, **kapıları açık tutarak** git.

## Doktora sonrası: postdoc mu, sanayi mi?

Doktoranı bitirdin, "Dr. rer. nat." aldın. İki geniş yol var:

- **Akademi (postdoc):** Araştırmayı seviyorsan ve WissZeitVG saatinin farkındaysan, postdoc + yurt dışı deneyim + kendi grup fonu peşinde koşarsın. Ödül yüksek, güvence düşük.
- **Sanayi:** İlaç (Bayer, Boehringer, Merck), kimya (BASF, Evonik), biyoteknoloji (**BioNTech**), tıbbi cihaz ve özellikle **veri bilimi/analitik**. Doktora burada güçlü bir kozdur ve maaş genelde daha iyidir. Veri yoluna ilgi duyuyorsan [Almanya'da veri bilimi & yapay zekâya nasıl girilir](/tr/blog/how-to-break-into-data-science-ai-in-germany) yazısı doğrudan işine yarar.

Blue Card açısından: doğa bilimcileri sık sık **MINT/darboğaz meslek** kategorisine girer ve **düşük maaş eşiğinden** (2025 için ~43.760€ darboğaz meslek, hedge'li — doğrula) Blue Card alabilir.

## Sonuç & dürüst tavsiye

Almanya'da doktora, Türkiye'deki algının aksine bir **fedakârlık değil, maaşlı bir iştir**: harç yok, TV-L E13 maaş var, unvan "Dr. rer. nat.". Yurt dışından gelen için en temiz kapı **yapılandırılmış programlar (IMPRS/Graduate School)**, en esnek kapı ise **doğru Doktorvater**'dır. Max Planck, Helmholtz, Fraunhofer ve Leibniz senin oyun alanın.

Dürüst tavsiyem: **doktorayı yap — ama akademiyi tek plan yapma.** WissZeitVG süreli sözleşmeleri sınırlar, kalıcı kadro azdır, sanayi daha iyi öder. Doktoranı bir bilim insanı olmak kadar **istihdam edilebilirliğini** yükseltmek için de kullan; İngilizce araştırma yaparken bir yandan **Almanca**nı ilerlet, çünkü sanayi rolleri onu ister.

Bu küme sana tüm resmi verir: [Almanya'da doğa bilimleri okumak](/tr/blog/studying-natural-sciences-physics-chemistry-biology-in-germany), [Almancasız İngilizce doğa bilimi master programları](/tr/blog/english-taught-natural-science-masters-in-germany-without-german) ve [bilim diplomasıyla sanayide ne yapılır](/tr/blog/what-to-do-with-a-science-degree-in-germany-industry-careers).

*Bu yazı 2026 başı itibarıyla hazırlanmıştır. Maaş rakamları, TV-L tarifeleri, WissZeitVG kuralları ve Blue Card eşikleri değişebilir; başvurmadan önce enstitünün ve resmi kaynakların güncel bilgilerini doğrula.*
MD;
        $deBody = <<<'MD'
In vielen Ländern gilt eine Promotion als teures Opfer: Du studierst jahrelang, zahlst Gebühren, hältst dich mit Assistenzjobs über Wasser. In Deutschland ist es umgekehrt. Hier ist eine Promotion meist **ein bezahlter Job** — eine Universität oder ein Forschungsinstitut stellt dich an, du wirst nach TV-L E13 bezahlt und du zahlst **keine Studiengebühren**. In diesem Beitrag erkläre ich dir, wie die bezahlte Promotion funktioniert, welche zwei Wege es gibt, welche großen Forschungsinstitute wichtig sind und welche ehrlichen Wahrheiten dir niemand sagt.

## Promotion = ein bezahlter Job (TV-L E13, keine Gebühren)

In Deutschland ist eine Promotion in den meisten Natur- und Ingenieurwissenschaften **kein Studierendenstatus, sondern ein Beschäftigungsverhältnis**. Typischerweise wirst du als **wissenschaftlicher Mitarbeiter** in einem Forschungsprojekt angestellt, und dein Gehalt richtet sich nach dem öffentlichen Tarif **TV-L E13**.

- **Keine Gebühren:** Für die Promotion zahlst du keine "Tuition"; nur der Semesterbeitrag (~150–350€) kann anfallen.
- **Gehalt (ungefähr, Stand 2025/2026; bitte prüfen):** eine volle Stelle nach TV-L E13 liegt brutto etwa bei **€2.800–4.200/Monat**; in den Naturwissenschaften gibt es oft **50–75 %-Teilzeitstellen**, also kann netto weniger ankommen.
- **Dauer:** in der Regel **3–5 Jahre**. Der Titel in den Naturwissenschaften ist **"Dr. rer. nat."**
- **Alternative:** statt einer bezahlten Stelle kannst du auch ein **Stipendium** bekommen — dann sind Sozialversicherung und Steuer anders geregelt.

**Kurz gesagt: Eine Promotion ist keine Ausgabe, sondern eine Einkommensquelle.** Das überrascht internationale Studierende am meisten.

## Zwei Wege: strukturiertes Programm vs. klassischer Doktorvater

In Deutschland gibt es zwei Türen zur Promotion. Beide sind gültig, fühlen sich aber sehr unterschiedlich an.

| | Strukturiertes Programm | Klassisch (Doktorvater) |
|---|---|---|
| Wie man es findet | Bewerbung auf Ausschreibung | Direkte E-Mail an Professor |
| Sprache | Oft **Englisch** | Je nach Lehrstuhl |
| Struktur | Kurse + Seminare + Arbeit | Vor allem die Arbeit, frei |
| Beispiel | **IMPRS**, Graduate School, Helmholtz-Schulen | Einzelstelle an einem Lehrstuhl |
| International | Sehr hoch | Unterschiedlich |

- **Strukturiert:** **IMPRS (International Max Planck Research Schools)**, Graduate Schools der Universitäten und Helmholtz-Forschungsschulen. Die Bewerbung ist kompetitiv, das Programm ist organisiert, du gehst im Jahrgang voran und es ist englischfreundlich. Für Bewerber aus dem Ausland ist das **der zugänglichste Weg**.
- **Klassisch (Doktorvater/Doktormutter):** Du findest eine Professorin oder einen Professor, pitchst deine Forschungsidee, und wenn du angenommen wirst, arbeitest du in ihrem Team. Freier, aber individueller; **den richtigen Betreuer zu finden** ist alles.

## Forschungsinstitute: Max Planck, Helmholtz, Fraunhofer, Leibniz

Die Stärke Deutschlands liegt nicht nur in den Universitäten, sondern in **vier großen Forschungsnetzwerken** von Weltrang. Ein großer Teil der Promotions- und Postdoc-Stellen wird dort ausgeschrieben.

| Netzwerk | Fokus | Beispielbereich |
|---|---|---|
| **Max Planck** | Grundlagenforschung | Physik, Molekularbiologie, Neurowissenschaften |
| **Helmholtz** | Großforschung, Infrastruktur | Teilchenphysik, Energie, Gesundheit |
| **Fraunhofer** | Angewandt, industrienah | Materialien, Produktion, angewandte Physik |
| **Leibniz** | Grundlagen + angewandt gemischt | Umwelt, Wirtschaft, Life Sciences |

Denk an Max Planck für **Neugier**, an Helmholtz für **große Infrastruktur**, an Fraunhofer für **industrienahe Anwendung** und an Leibniz für **etwas dazwischen**. Wenn du Naturwissenschaftler bist, verfolge die Stellenseiten dieser Institute (z. B. die IMPRS-Portale) regelmäßig.

## Bewerbung: Stelle suchen, Angebot bekommen, Visum

Eine Promotionsbewerbung ist keine "Studienbewerbung", sondern fast eine **Jobbewerbung**.

1. **Stelle/Ausschreibung suchen:** IMPRS-Portale, Lehrstuhlseiten, Helmholtz-/Leibniz-Jobbörsen, `academics.de`, `EURAXESS`.
2. **Direkter Kontakt:** beim klassischen Weg eine kurze, persönliche E-Mail an den Professor + CV + Zusammenfassung deiner Interessen.
3. **Unterlagen:** Masterzeugnis + Transcript, Motivations-/Forschungsvorschlag, Referenzen, Englisch (Forschung meist auf Englisch) und in manchen Industrie-/Klinikrollen **Deutsch**.
4. **Angebot und Vertrag:** bei Zusage bekommst du einen Arbeitsvertrag (TV-L E13) oder einen Stipendienbrief.
5. **Visum:** wenn du von außerhalb der EU kommst, startest du mit einem **Forscher-/Arbeitsvisum** oder einem Studierendenvisum; ein Arbeitsvertrag erleichtert den Prozess. Details findest du im Beitrag [Arbeitsvisum mit Jobangebot in Deutschland](/de/blog/germany-work-visa-with-job-offer-process-timeline-fast-track-de).

## Die ehrliche Wahrheit: die Wissenschaft kann unsicher sein

Jetzt der Teil, den keine Broschüre schreibt. Die deutsche Wissenschaft ist glänzend, aber sie bietet einen **fragilen Karriereweg**.

- **Befristete Verträge:** Es gibt ein Gesetz — das **WissZeitVG** — das **begrenzt**, wie lange nicht dauerhaft angestelltes wissenschaftliches Personal befristet arbeiten darf. Deine Promotions- und Postdoc-Jahre zählen dazu. Wenn du also keine Professur bekommst, kann sich irgendwann **die akademische Tür schließen**.
- **Wenige feste Stellen:** Professuren sind sehr selten und hart umkämpft; der Weg vom Postdoc zur Professur ist schmal und ungewiss.
- **Die Industrie zahlt besser:** Nach der Promotion bietet **die Industrie meist ein höheres Gehalt** und stabilere Verträge als die Wissenschaft. Physiker wechseln oft in **Data Science, Finanzen, Beratung**.

Das ist kein Aufruf zum Aufgeben — sondern ein Aufruf, **mit offenen Augen** hinzugehen. Eine Promotion ist eine großartige Investition; aber geh nicht mit "Ich werde um jeden Preis Professor", sondern **halte dir die Türen offen**.

## Nach der Promotion: Postdoc oder Industrie?

Du hast deine Promotion abgeschlossen, den "Dr. rer. nat." bekommen. Es gibt zwei große Wege:

- **Wissenschaft (Postdoc):** Wenn du Forschung liebst und dir das WissZeitVG-Zeitkonto bewusst ist, jagst du Postdocs + Auslandserfahrung + eigene Fördermittel. Hohe Belohnung, wenig Sicherheit.
- **Industrie:** Pharma (Bayer, Boehringer, Merck), Chemie (BASF, Evonik), Biotechnologie (**BioNTech**), Medizintechnik und besonders **Data Science/Analytik**. Die Promotion ist hier ein starker Trumpf und das Gehalt meist besser. Wenn dich der Datenweg reizt, hilft dir der Beitrag [Wie man in Data Science & KI in Deutschland einsteigt](/de/blog/how-to-break-into-data-science-ai-in-germany-de) direkt weiter.

Zur Blue Card: Naturwissenschaftler fallen oft in die **MINT-/Engpassberuf**-Kategorie und können die Blue Card mit einer **niedrigeren Gehaltsschwelle** bekommen (für 2025 etwa 43.760€ für Engpassberufe, mit Vorbehalt — bitte prüfen).

## Fazit & ehrlicher Rat

Eine Promotion in Deutschland ist kein Opfer, sondern **ein bezahlter Job**: keine Gebühren, Gehalt nach TV-L E13, Titel "Dr. rer. nat.". Für Bewerber aus dem Ausland ist die sauberste Tür ein **strukturiertes Programm (IMPRS/Graduate School)**, die flexibelste der **richtige Doktorvater**. Max Planck, Helmholtz, Fraunhofer und Leibniz sind dein Spielfeld.

Mein ehrlicher Rat: **Mach die Promotion — aber mach die Wissenschaft nicht zum einzigen Plan.** Das WissZeitVG begrenzt befristete Verträge, feste Stellen sind selten, die Industrie zahlt besser. Nutze deine Promotion nicht nur, um Wissenschaftler zu werden, sondern auch, um deine **Beschäftigungsfähigkeit** zu erhöhen; verbessere dein **Deutsch**, während du auf Englisch forschst, denn die Industrie verlangt es.

Dieses Cluster gibt dir das ganze Bild: [Naturwissenschaften in Deutschland studieren](/de/blog/studying-natural-sciences-physics-chemistry-biology-in-germany-de), [englischsprachige Naturwissenschafts-Master ohne Deutsch](/de/blog/english-taught-natural-science-masters-in-germany-without-german-de) und [was man mit einem Naturwissenschafts-Abschluss in der Industrie macht](/de/blog/what-to-do-with-a-science-degree-in-germany-industry-careers-de).

*Dieser Beitrag wurde Anfang 2026 erstellt. Gehälter, TV-L-Tarife, WissZeitVG-Regeln und Blue-Card-Schwellen können sich ändern; prüfe vor der Bewerbung die aktuellen Angaben des Instituts und der offiziellen Quellen.*
MD;
        $enBody = <<<'MD'
In many countries, "doing a PhD" is seen as an expensive sacrifice: you study for years, pay fees, and scrape by on teaching assistant work. In Germany, the picture flips. Here a doctorate is usually **a paid job** — a university or research institute hires you, pays you according to the TV-L E13 pay scale, and you pay **no tuition fees**. In this post I explain how the salaried PhD works, the two application routes, the giant research institutes, and the honest truths nobody tells you.

## A PhD = a paid job (TV-L E13, no fees)

In Germany, a PhD in most natural sciences and engineering is **not a student status but an employment relationship**. Typically you are hired as a **research associate (wissenschaftlicher Mitarbeiter)** on a professor's or institute's research project, and your salary follows the public pay scale **TV-L E13**.

- **No fees:** you don't pay "tuition" for the PhD; only the semester contribution (~€150–350) may apply.
- **Salary (approximate, as of 2025/2026; verify):** a full position on TV-L E13 is roughly **€2,800–4,200/month gross**; in the natural sciences you are often given a **50–75% part-time position**, so take-home pay can be lower.
- **Duration:** usually **3–5 years**. The title in the natural sciences is **"Dr. rer. nat."**
- **Alternative:** instead of a salaried position you may hold a **scholarship (Stipendium)** — in which case social insurance and tax are handled differently.

**In short: a PhD is not a cost, it is a source of income.** This is the fact that surprises international students most.

## Two routes: structured programme vs. traditional Doktorvater

There are two doors into a PhD in Germany. Both are valid, but they feel very different.

| | Structured programme | Traditional (Doktorvater) |
|---|---|---|
| How you find it | Apply to an advertised position | Direct email to a professor |
| Language | Often **English** | Depends on the chair |
| Structure | Courses + seminars + thesis | Mostly the thesis, free-form |
| Example | **IMPRS**, Graduate School, Helmholtz schools | A single post at one chair |
| International | Very high | Variable |

- **Structured:** **IMPRS (International Max Planck Research Schools)**, university Graduate Schools, and Helmholtz research schools. Applications are competitive, the programme is organised, you move forward in a cohort, and it is English-friendly. For applicants from abroad this is **the most accessible route**.
- **Traditional (Doktorvater/Doktormutter):** you find a professor, pitch your research idea, and if accepted you work in their team. More flexible but more individual; **finding the right supervisor** is everything.

## Research institutes: Max Planck, Helmholtz, Fraunhofer, Leibniz

Germany's strength is not only in universities but in **four world-class research networks**. A large share of PhD and postdoc positions are advertised there.

| Network | Focus | Example field |
|---|---|---|
| **Max Planck** | Basic, curiosity-driven research | Physics, molecular biology, neuroscience |
| **Helmholtz** | Big science, infrastructure | Particle physics, energy, health |
| **Fraunhofer** | Applied, industry-facing | Materials, manufacturing, applied physics |
| **Leibniz** | Mixed basic + applied | Environment, economics, life sciences |

Think Max Planck for **curiosity**, Helmholtz for **big infrastructure**, Fraunhofer for **industry-facing application**, and Leibniz for **something in between**. If you are a natural scientist, follow these institutes' job pages (e.g. the IMPRS portals) regularly.

## Applying: find a position, get an offer, get a visa

A PhD application is not a "university application" but almost a **job application**.

1. **Search for a position/advert:** IMPRS portals, chair pages, Helmholtz/Leibniz job boards, `academics.de`, `EURAXESS`.
2. **Direct contact:** on the traditional route, a short, personal email to the professor + CV + summary of your interests.
3. **Documents:** master's degree + transcript, motivation/research proposal, references, English (most research is in English) and, in some industry/clinical roles, **German**.
4. **Offer and contract:** if accepted you receive an employment contract (TV-L E13) or a scholarship letter.
5. **Visa:** if you come from outside the EU, you start with a **researcher/work visa** or a student visa; an employment contract makes the process easier. For details, see [Germany work visa with a job offer](/en/blog/germany-work-visa-with-job-offer-process-timeline-fast-track-en).

## The honest truth: academia can be insecure

Now for the part no brochure writes. German academia is brilliant, but it offers a **fragile career path**.

- **Fixed-term contracts:** there is a law — the **WissZeitVG** — that **limits** how long non-permanent academic staff may work on fixed-term contracts. Your PhD and postdoc years count toward this. So if you don't land a professorship, at some point **the academic door can close**.
- **Few permanent posts:** professorships are very rare and fiercely contested; the path from postdoc to professor is narrow and uncertain.
- **Industry pays better:** after a PhD, **industry usually offers higher pay** and more stable contracts than academia. Physicists often move into **data science, finance, consulting**.

This is not a call to give up — it is a call to go in **with your eyes open**. A PhD is a great investment; but go in not with "I will become a professor no matter what," but by **keeping your doors open**.

## After the PhD: postdoc or industry?

You've finished your PhD, earned the "Dr. rer. nat." There are two broad paths:

- **Academia (postdoc):** if you love research and are aware of the WissZeitVG clock, you chase postdocs + international experience + your own grant funding. High reward, low security.
- **Industry:** pharma (Bayer, Boehringer, Merck), chemistry (BASF, Evonik), biotechnology (**BioNTech**), medical devices, and especially **data science/analytics**. A PhD is a strong asset here and pay is usually better. If the data path appeals to you, the post [how to break into data science & AI in Germany](/en/blog/how-to-break-into-data-science-ai-in-germany-en) is directly useful.

On the Blue Card: natural scientists often fall into the **STEM/shortage occupation** category and can obtain a Blue Card with a **lower salary threshold** (around €43,760 for shortage occupations in 2025, hedged — verify).

## Conclusion & honest advice

A PhD in Germany is not a sacrifice but **a paid job**: no fees, a TV-L E13 salary, the title "Dr. rer. nat." For applicants from abroad the cleanest door is a **structured programme (IMPRS/Graduate School)**, the most flexible is the **right Doktorvater**. Max Planck, Helmholtz, Fraunhofer and Leibniz are your playing field.

My honest advice: **do the PhD — but don't make academia your only plan.** The WissZeitVG limits fixed-term contracts, permanent posts are scarce, and industry pays better. Use your PhD not only to become a scientist but also to raise your **employability**; improve your **German** while you research in English, because industry roles will want it.

This cluster gives you the whole picture: [studying natural sciences in Germany](/en/blog/studying-natural-sciences-physics-chemistry-biology-in-germany-en), [English-taught natural science master's without German](/en/blog/english-taught-natural-science-masters-in-germany-without-german-en), and [what to do with a science degree in industry](/en/blog/what-to-do-with-a-science-degree-in-germany-industry-careers-en).

*This post was prepared in early 2026. Salaries, TV-L pay scales, WissZeitVG rules and Blue Card thresholds can change; verify the institute's and official sources' current information before you apply.*
MD;

        $variants = [
            'tr' => ['slug'=>'doing-a-phd-and-research-career-in-germany-as-a-foreigner',    'title'=>'Almanya\'da Doktora ve Araştırma Kariyeri: Maaşlı PhD Rehberi (2026)', 'excerpt'=>'Almanya\'da doktora çoğu zaman maaşlı bir iştir: harç yok, TV-L E13 maaş var. İki yol (IMPRS vs Doktorvater), Max Planck/Helmholtz enstitüleri ve akademinin güvencesiz gerçeği bu rehberde.', 'meta_title'=>'Almanya\'da Maaşlı Doktora ve Araştırma Kariyeri (2026)', 'meta_description'=>'Almanya\'da doktora maaşlı bir iştir (TV-L E13, harç yok). IMPRS vs Doktorvater, Max Planck/Helmholtz/Fraunhofer ve dürüst kariyer gerçeği.', 'body'=>$trBody],
            'de' => ['slug'=>'doing-a-phd-and-research-career-in-germany-as-a-foreigner-de', 'title'=>'Promotion & Forschungskarriere in Deutschland: der bezahlte PhD-Leitfaden (2026)', 'excerpt'=>'Eine Promotion in Deutschland ist meist ein bezahlter Job: keine Gebühren, Gehalt nach TV-L E13. Zwei Wege (IMPRS vs Doktorvater), Max-Planck-/Helmholtz-Institute und die ehrliche Wahrheit über die unsichere Wissenschaft.', 'meta_title'=>'Bezahlte Promotion & Forschungskarriere in Deutschland (2026)', 'meta_description'=>'Eine Promotion in Deutschland ist ein bezahlter Job (TV-L E13, keine Gebühren). IMPRS vs Doktorvater, Max Planck/Helmholtz/Fraunhofer und die ehrliche Wahrheit.', 'body'=>$deBody],
            'en' => ['slug'=>'doing-a-phd-and-research-career-in-germany-as-a-foreigner-en', 'title'=>'Doing a PhD & Research Career in Germany: the Salaried PhD Guide (2026)', 'excerpt'=>'A PhD in Germany is usually a paid job: no tuition, a TV-L E13 salary. Two routes (IMPRS vs Doktorvater), Max Planck/Helmholtz institutes, and the honest truth about insecure academia.', 'meta_title'=>'Salaried PhD & Research Career in Germany (2026)', 'meta_description'=>'A PhD in Germany is a paid job (TV-L E13, no fees). IMPRS vs Doktorvater, Max Planck/Helmholtz/Fraunhofer, and the honest career truth.', 'body'=>$enBody],
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
            'doing-a-phd-and-research-career-in-germany-as-a-foreigner',
            'doing-a-phd-and-research-career-in-germany-as-a-foreigner-de',
            'doing-a-phd-and-research-career-in-germany-as-a-foreigner-en',
        ])->delete();
    }
};
