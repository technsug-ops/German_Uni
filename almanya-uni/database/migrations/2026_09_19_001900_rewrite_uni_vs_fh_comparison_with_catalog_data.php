<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR yeniden yazım + DE/EN İLK KEZ): Universität mı Fachhochschule mi?
 *
 * Mevcut TR yazı 4.141 karakterlik ve TR-only idi; konu sitenin en çok aranan karşılaştırmalarından
 * biri olduğu için kendi katalog verimizle yeniden yazıldı ve iki dile açıldı. Slug korundu.
 *
 * GROUNDING — kendi DB'miz (2026-09-19, aktif kayıtlar):
 *   Kurum: 462 aktif üniversite → 184 Fachhochschule/HAW · 114 Universität · 53 sanat/müzik ·
 *          9 "kendi tipi" · 3 yönetim yüksekokulu (99 kayıtta tip boş).
 *   Taşıyıcılık: 241 kamu (öffentlich-rechtlich) · 96 özel-devlet tanınmış · 26 kilise.
 *   Doktora hakkı: 155 kurum "Ja" · 30 "sınırlı" · 178 "Nein".
 *   Program: FH 8.800 (4.764 lisans / 3.382 master) · Uni 11.802 (4.961 lisans / 5.522 master).
 *   Öğretim dilinde İngilizce içeren: FH 1.576 · Uni 2.432.
 *   admission_mode bilinen programlarda NC'siz oran: FH 3.709/5.620 (~%66) · Uni 3.740/5.934 (~%63).
 *   Ortalama öğrenci sayısı: FH 5.503 · Uni 14.513.
 * Not: NC oranları yalnızca admission_mode'u DOLU programlar üzerinden; katalogda mod bilgisi
 * olmayan programlar hesaba katılmadı — yazıda bu sınır açıkça belirtildi.
 * Yazar: Halil Yaprakli. Kategori korunuyor (Üniversite & Araştırma).
 */
return new class extends Migration
{
    public function up(): void
    {
        $base = 'hochschule-vs-universitaet-vs-fh-differences-in-germany';

        $existing = Post::where('slug', $base)->first();
        if (! $existing) {
            return;
        }

        $groupId = $existing->translation_group_id ?: (string) Str::uuid();
        $userId = $existing->user_id;
        $categoryId = $existing->category_id;

        $trBody = <<<'MD'
"Üniversite mi Hochschule mi?" sorusu aslında yanlış kurulmuş bir soru — çünkü **Hochschule bir üst kavram**, üniversitenin karşıtı değil. Almanya'da yükseköğretim kurumlarının tamamına *Hochschule* denir; Universität de bir Hochschule'dir, Fachhochschule de.

Asıl soru şu: **Universität mi, Fachhochschule (FH / HAW) mi?** Bu yazı ikisini kendi katalog verimizle karşılaştırıyor — 462 aktif kurum ve 20.600'den fazla program üzerinden.

## Önce terimler

| Terim | Ne demek |
|---|---|
| **Hochschule** | Şemsiye terim: tüm yükseköğretim kurumları |
| **Universität** | Araştırma ağırlıklı, doktora veren klasik üniversite |
| **Fachhochschule (FH) / HAW** | Uygulama ağırlıklı yüksekokul; birçoğu artık kendini *Hochschule für Angewandte Wissenschaften* diye adlandırıyor |
| **Kunst-/Musikhochschule** | Sanat ve müzik kurumları; kabul yetenek sınavıyla |
| **Duale Hochschule** | İşletmeyle sözleşmeli, dönüşümlü okul–iş modeli |

Kafa karışıklığının kaynağı isim değişikliği: birçok FH, adındaki "Fachhochschule"yi kaldırıp yalnızca "Hochschule" veya "HAW" kullanıyor. Kurum türünü isimden değil, kurumun kendi künyesinden anlarsın.

## Katalogdaki gerçek manzara

Kendi veri tabanımızdaki 462 aktif kurumun dağılımı:

| Tür | Kurum sayısı |
|---|---|
| Fachhochschule / HAW | **184** |
| Universität | **114** |
| Sanat ve müzik yüksekokulları | 53 |
| Kendi tipinde kurumlar + yönetim yüksekokulları | 12 |

Taşıyıcılık tarafında: **241 kamu kurumu**, 96 özel ama devlet tanınmış (*privat, staatlich anerkannt*), 26 kilise kurumu.

Yani Almanya'da FH'ler sayıca üniversitelerden fazla. "Gerçek üniversite azdır, gerisi ikinci sınıftır" algısı buradan doğuyor ve yanlış — iki sistem farklı işler için kurulmuş.

## Somut farklar

| Boyut | Universität | Fachhochschule / HAW |
|---|---|---|
| Ağırlık | Teori, araştırma, yöntem | Uygulama, proje, sektör bağlantısı |
| Tipik büyüklük (katalog ort.) | **14.513 öğrenci** | **5.503 öğrenci** |
| Ders ortamı | Büyük amfiler, daha çok bağımsız çalışma | Küçük gruplar, daha yakın hoca teması |
| Staj | Genelde zorunlu değil | Çoğu programda **Praxissemester** zorunlu |
| Doktora hakkı | Kural olarak var | Kural olarak yok (istisnalar artıyor) |
| Program profili | Master ağırlığı yüksek | Lisans ağırlığı yüksek |

Doktora hakkı katalogda da net görünüyor: **155 kurumun doktora yetkisi var, 30'unda sınırlı (alan/süre bazlı), 178 kurumda yok.** Son yıllarda bazı eyaletler güçlü FH'lere sınırlı doktora yetkisi verdi — ama bu hâlâ kural değil, istisna.

## Program tarafı: sayılar ne diyor?

| | Universität | Fachhochschule / HAW |
|---|---|---|
| Toplam aktif program | **11.802** | **8.800** |
| Lisans | 4.961 | 4.764 |
| Master | **5.522** | 3.382 |
| Öğretim dilinde İngilizce içeren | **2.432** | 1.576 |
| NC'siz oran (mod bilgisi olan programlarda) | ~%63 | **~%66** |

Üç okuma:

1. **Lisansta seçenek neredeyse eşit** (4.961'e 4.764). "FH'de az program var" doğru değil.
2. **Master tarafında üniversite açık ara önde** (5.522'ye 3.382). Akademik derinleşme veya doktora düşünüyorsan bu fark önemli.
3. **NC'siz program oranı FH'de biraz daha yüksek.** Fark büyük değil ama kabul şansı arıyorsan FH tarafı genelde daha geçirgen. (Bu oran yalnızca kabul modu kayıtlı programlar üzerinden; katalogda modu boş olan programlar hesapta yok.)

Kendi alanında bu dağılımın nasıl olduğunu [program arama sayfamızdan](/tr/programs) ve [alan sayfalarından](/tr/fields) doğrudan görebilirsin.

## Başvuruda ne değişir?

- **Kabul kriteri:** Uni tarafında not ortalaması ve ön koşul dersleri daha belirleyici; FH tarafında mesleki deneyim, staj ve portföy daha çok ağırlık taşıyabilir.
- **Vorpraktikum:** bazı FH mühendislik programları başlamadan önce haftalarca süren zorunlu ön staj ister. Kabul mektubunda bunu ara.
- **Master geçişi:** FH lisansıyla üniversitede master yapmak mümkündür; bazı programlar eksik teori dersleri için ek yükümlülük (Auflagen) koyar.
- **Dil:** İngilizce programlar her iki türde de var ama üniversitede sayıca fazla.

## Kariyer tarafı: işveren ne diyor?

Mühendislik, bilişim, işletme ve sağlık alanlarında Alman işverenler FH mezunlarını **uygulamaya hazır** olarak görür; iş ilanlarının çoğu "Uni veya FH" diye yazar. Aradaki fark maaş cetvellerinde değil, yolun şeklinde:

- **Sanayi ve KOBİ tarafı:** FH'nin proje ve staj yoğunluğu ilk işe girişte avantaj sağlar.
- **Araştırma, Ar-Ge, akademi:** üniversite yolu daha doğrudan; doktora gerekiyorsa neredeyse zorunlu.
- **Kamuda üst kariyer basamakları:** bazı pozisyonlar belirli derece/tür kombinasyonları ister — ilanın şartlarını okumadan varsayma.

Mezuniyet sonrası oturum ve iş arama tarafı her iki türde aynı işler: [iş arama vizesi rehberimiz](/tr/blog/germany-job-seeker-visa-2026-complete-guide-for-graduates) ve [çalışma iznine geçiş yazımız](/tr/blog/changing-student-visa-to-work-permit-germany-zweckwechsel) kurum türünden bağımsızdır.

## Kim için hangisi?

Kazananı ilan etmek yerine karar çerçevesi:

**Universität sana uygun olabilir** — doktora veya araştırma düşünüyorsan; alanın teorik temeli ağırsa (matematik, fizik, hukuk, tıp); akademik kariyer ihtimalini açık tutmak istiyorsan; İngilizce master seçeneği bolluğu önemliyse.

**Fachhochschule sana uygun olabilir** — mezuniyette doğrudan sektöre girmek istiyorsan; küçük grup ve yakın danışmanlık senin için fark yaratıyorsa; zorunlu staj dönemini avantaj olarak görüyorsan; kabul şansını artıracak bir yol arıyorsan.

**Belirleyici olan üçüncü bir şey var:** kurum türü değil, **programın içeriği.** Aynı isimli iki program, biri FH'de biri Uni'de, tamamen farklı ders planına sahip olabilir. Modulhandbuch'u (ders kataloğu) okumak, tür tartışmasından daha çok bilgi verir.

## Sıkça Sorulanlar

### FH diploması üniversite diplomasından düşük mü sayılır?
Alman sisteminde Bachelor ve Master dereceleri her iki kurum türünde de aynı derecelerdir ve aynı akademik seviyeye karşılık gelir. Fark prestij hiyerarşisinde değil, eğitimin ağırlık merkezindedir.

### FH'de doktora yapabilir miyim?
Kural olarak doktora yetkisi üniversitelerdedir; katalogumuzda 178 kurumda doktora hakkı yok. Yaygın çözüm **kooperatif doktora**: tez bir üniversitede kayıtlı yürür, danışmanlığın bir kısmı FH'den gelir. Bazı eyaletlerde güçlü FH'lere sınırlı yetki verildi — ama önce kendi kurumunun durumunu doğrula.

### Türkiye'de denklik açısından fark var mı?
Denklik değerlendirmesini YÖK yapar ve kriterlerin başında kurumun Almanya'da **devlet tarafından tanınmış** olması gelir — bu FH'ler için de geçerlidir. Yine de program bazında farklılık olabileceği için, başvurmadan önce YÖK'ün güncel listelerinden kendi kurum ve programını kontrol et.

### FH'ler neden dünya sıralamalarında görünmüyor?
Çünkü sıralamaların büyük kısmı **araştırma çıktısı ve atıf** ölçer; FH'ler araştırma değil öğretim ve uygulama için kurulmuştur. Sıralamada olmamak kalite değil, ölçüm biçimi meselesidir.

### Hangisine girmek daha kolay?
Katalog verisi FH tarafında NC'siz program oranının biraz daha yüksek olduğunu gösteriyor (~%66'ya ~%63). Ama "kolay" yanıltıcı: popüler FH programlarında da yoğun rekabet olur, ve bazı üniversite programları tamamen NC'sizdir.

### Özel üniversiteler bu tabloda nerede?
Katalogda 96 kurum "özel, devlet tanınmış". Tanınmışlık akademik geçerlilik için yeterlidir; ayırt edici soru ücret karşılığında ne aldığın — küçük grup, sektör ağı, hızlandırılmış program. Devlet kurumlarında öğrenim çoğunlukla harçsızdır.

## Sonuç ve dürüst tavsiye

Almanya'da doğru soru "hangisi daha iyi" değil, **"hangi yol benim hedefime çıkıyor"**. Doktora ve araştırma istiyorsan üniversite; sektöre hızlı ve uygulamalı girişi istiyorsan FH güçlü seçenek. İkisi arasında geçiş de mümkün — FH lisansından üniversite masterına geçen çok sayıda öğrenci var.

Karar vermeden önce üç şeyi yap: hedeflediğin iki programın **Modulhandbuch**'unu karşılaştır, kabul modunu (NC var mı) kontrol et, ve mezunların nerede çalıştığına bak. Bu üçü, kurum türü tartışmasından daha isabetli karar verdirir.

*Bu yazıdaki kurum ve program sayıları kendi kataloğumuzun 2026 Eylül durumudur; kabul modu bilgisi tüm programlarda dolu olmadığı için NC oranları yalnızca kaydı bulunan programları kapsar. Kabul koşulları her dönem değişebilir — kendi programının resmî sayfasını esas al.*
MD;

        $deBody = <<<'MD'
„Universität oder Hochschule?" ist eigentlich falsch gestellt — denn **Hochschule ist der Oberbegriff**, nicht das Gegenteil von Universität. In Deutschland heißen alle Einrichtungen der Hochschulbildung so; eine Universität ist eine Hochschule, eine Fachhochschule ebenso.

Die eigentliche Frage lautet: **Universität oder Fachhochschule (FH / HAW)?** Dieser Artikel vergleicht beide mit den Daten unseres eigenen Katalogs — 462 aktive Einrichtungen und über 20.600 Studiengänge.

## Zuerst die Begriffe

| Begriff | Bedeutung |
|---|---|
| **Hochschule** | Oberbegriff für alle Hochschuleinrichtungen |
| **Universität** | Forschungsorientiert, mit Promotionsrecht |
| **Fachhochschule (FH) / HAW** | Anwendungsorientiert; viele nennen sich heute *Hochschule für Angewandte Wissenschaften* |
| **Kunst- und Musikhochschule** | Aufnahme über Eignungsprüfung |
| **Duale Hochschule** | Wechsel zwischen Betrieb und Hochschule, mit Vertrag |

Die Verwirrung kommt von der Namensänderung: Viele FHs haben „Fachhochschule" gestrichen und nennen sich nur noch „Hochschule" oder „HAW". Den Typ erkennst du nicht am Namen, sondern am Profil der Einrichtung.

## Das tatsächliche Bild im Katalog

Verteilung der 462 aktiven Einrichtungen in unserer Datenbank:

| Typ | Anzahl |
|---|---|
| Fachhochschule / HAW | **184** |
| Universität | **114** |
| Kunst- und Musikhochschulen | 53 |
| Hochschulen eigenen Typs und Verwaltungshochschulen | 12 |

Nach Trägerschaft: **241 öffentlich-rechtlich**, 96 privat und staatlich anerkannt, 26 kirchlich.

Es gibt also mehr FHs als Universitäten. Die Vorstellung „nur Universitäten sind echte Hochschulen" entsteht genau hier — und sie ist falsch: Die beiden Systeme wurden für unterschiedliche Aufgaben gebaut.

## Die konkreten Unterschiede

| Dimension | Universität | Fachhochschule / HAW |
|---|---|---|
| Schwerpunkt | Theorie, Forschung, Methodik | Anwendung, Projekte, Branchennähe |
| Typische Größe (Katalogmittel) | **14.513 Studierende** | **5.503 Studierende** |
| Lernumgebung | Große Hörsäle, viel Eigenarbeit | Kleinere Gruppen, engerer Kontakt |
| Praktikum | Meist nicht verpflichtend | In vielen Studiengängen **Praxissemester** |
| Promotionsrecht | In der Regel vorhanden | In der Regel nicht (Ausnahmen nehmen zu) |
| Programmprofil | Schwerpunkt Master | Schwerpunkt Bachelor |

Das Promotionsrecht zeigt sich auch im Katalog deutlich: **155 Einrichtungen mit Promotionsrecht, 30 mit eingeschränktem Recht, 178 ohne.** Einzelne Länder haben starken FHs ein begrenztes Promotionsrecht eingeräumt — die Ausnahme, nicht die Regel.

## Die Studiengänge: was sagen die Zahlen?

| | Universität | Fachhochschule / HAW |
|---|---|---|
| Aktive Studiengänge | **11.802** | **8.800** |
| Bachelor | 4.961 | 4.764 |
| Master | **5.522** | 3.382 |
| Mit Englisch als (Teil-)Unterrichtssprache | **2.432** | 1.576 |
| Anteil zulassungsfrei (bei erfasstem Modus) | ~63 % | **~66 %** |

Drei Lesarten:

1. **Im Bachelor ist die Auswahl fast gleich groß** (4.961 zu 4.764). „An FHs gibt es wenig" stimmt nicht.
2. **Im Master liegt die Universität deutlich vorn** (5.522 zu 3.382). Wer akademisch vertiefen oder promovieren will, sollte das einrechnen.
3. **Der Anteil zulassungsfreier Studiengänge ist an FHs etwas höher.** Der Abstand ist klein, die Richtung aber konsistent. (Die Quote bezieht sich nur auf Studiengänge mit erfasstem Zulassungsmodus.)

Wie sich das in deinem Fach verteilt, siehst du direkt in der [Studiengangssuche](/de/programs) und auf den [Fachseiten](/de/fields).

## Was ändert sich bei der Bewerbung?

- **Zulassungskriterien:** an Universitäten zählen Note und Vorleistungen stärker; an FHs können Praxiserfahrung, Praktika und Portfolio mehr Gewicht haben.
- **Vorpraktikum:** manche FH-Ingenieurstudiengänge verlangen vor Studienbeginn ein mehrwöchiges Pflichtpraktikum. Prüfe den Zulassungsbescheid darauf.
- **Wechsel in den Master:** mit FH-Bachelor an einer Universität zu studieren ist möglich; einzelne Programme erteilen Auflagen für fehlende theoretische Module.
- **Sprache:** Englischsprachige Programme gibt es in beiden Typen, an Universitäten zahlenmäßig mehr.

## Karriere: was sagen Arbeitgeber?

In Ingenieurwesen, Informatik, Wirtschaft und Gesundheit gelten FH-Absolventinnen und -Absolventen als **anwendungsbereit**; die meisten Stellenausschreibungen nennen „Uni oder FH". Der Unterschied liegt nicht in Gehaltstabellen, sondern in der Form des Weges:

- **Industrie und Mittelstand:** die Projekt- und Praktikumsdichte der FH hilft beim Einstieg.
- **Forschung und Entwicklung, Wissenschaft:** der universitäre Weg ist direkter und bei Promotion praktisch notwendig.
- **Höhere Laufbahnen im öffentlichen Dienst:** manche Positionen verlangen bestimmte Abschluss-Kombinationen — lies die Ausschreibung, statt zu vermuten.

Aufenthalts- und Jobsuchefragen nach dem Abschluss laufen bei beiden Typen gleich: unser [Ratgeber zur Arbeitsplatzsuche](/de/blog/germany-job-seeker-visa-2026-complete-guide-for-graduates-de) und der [Zweckwechsel-Artikel](/de/blog/changing-student-visa-to-work-permit-germany-zweckwechsel-de) gelten unabhängig von der Hochschulart.

## Für wen passt was?

Statt eines Siegers ein Entscheidungsrahmen:

**Eine Universität passt eher**, wenn du promovieren oder forschen willst; wenn dein Fach eine schwere theoretische Basis hat (Mathematik, Physik, Jura, Medizin); wenn du die akademische Option offenhalten möchtest; wenn dir die größere Auswahl englischsprachiger Master wichtig ist.

**Eine Fachhochschule passt eher**, wenn du nach dem Abschluss direkt in die Branche willst; wenn kleine Gruppen und enge Betreuung für dich einen Unterschied machen; wenn du das Pflichtpraxissemester als Vorteil siehst; wenn du deine Zulassungschancen verbessern möchtest.

**Entscheidend ist aber ein Drittes:** nicht der Hochschultyp, sondern **der Inhalt des Studiengangs.** Zwei gleichnamige Programme — eines an der FH, eines an der Uni — können völlig verschiedene Modulpläne haben. Das Modulhandbuch zu lesen bringt mehr als jede Typdebatte.

## Häufige Fragen

### Gilt ein FH-Abschluss weniger als ein Uni-Abschluss?
Bachelor und Master sind an beiden Hochschultypen dieselben Abschlüsse auf demselben Niveau. Der Unterschied liegt im Schwerpunkt der Ausbildung, nicht in einer Rangordnung.

### Kann ich an einer FH promovieren?
Das Promotionsrecht liegt im Regelfall bei Universitäten; in unserem Katalog haben 178 Einrichtungen keines. Üblich ist die **kooperative Promotion**: die Arbeit läuft formal an einer Universität, ein Teil der Betreuung kommt von der FH. Einzelne Länder haben starken FHs begrenzte Rechte gegeben — prüfe die Lage deiner Hochschule.

### Spielt der Typ bei der Anerkennung im Ausland eine Rolle?
Entscheidend ist meist, dass die Hochschule in Deutschland **staatlich anerkannt** ist — das gilt für FHs ebenso. Weil die Prüfung aber programmbezogen erfolgt, kläre deinen konkreten Fall vorab mit der zuständigen Anerkennungsstelle deines Heimatlandes.

### Warum tauchen FHs in Weltrankings kaum auf?
Weil Rankings überwiegend **Forschungsleistung und Zitationen** messen; FHs sind für Lehre und Anwendung gebaut. Das Fehlen im Ranking ist eine Frage der Messmethode, nicht der Qualität.

### Wo kommt man leichter rein?
Die Katalogdaten zeigen an FHs einen etwas höheren Anteil zulassungsfreier Studiengänge (~66 % zu ~63 %). „Leicht" führt aber in die Irre: beliebte FH-Programme sind stark umkämpft, und viele Universitätsstudiengänge sind komplett zulassungsfrei.

### Und private Hochschulen?
96 Einrichtungen im Katalog sind privat und staatlich anerkannt. Die Anerkennung sichert die akademische Gültigkeit; die eigentliche Frage ist, was du für die Gebühren bekommst — kleine Gruppen, Branchennetzwerk, verkürzte Studienzeit. An staatlichen Hochschulen ist das Studium meist gebührenfrei.

## Fazit und ehrlicher Rat

Die richtige Frage lautet nicht „was ist besser", sondern **„welcher Weg führt zu meinem Ziel"**. Für Promotion und Forschung die Universität; für den schnellen, anwendungsnahen Einstieg die FH. Und der Wechsel bleibt möglich — sehr viele wechseln mit FH-Bachelor in einen Universitätsmaster.

Mach vor der Entscheidung drei Dinge: vergleiche die **Modulhandbücher** deiner beiden Favoriten, prüfe den Zulassungsmodus, und sieh nach, wo die Absolventinnen und Absolventen heute arbeiten. Das führt zu besseren Entscheidungen als jede Typdiskussion.

*Die Zahlen geben den Stand unseres Katalogs im September 2026 wieder; der Zulassungsmodus ist nicht bei allen Studiengängen erfasst, die Quoten beziehen sich daher nur auf erfasste Einträge. Maßgeblich ist die offizielle Seite deines Studiengangs.*
MD;

        $enBody = <<<'MD'
"University or Hochschule?" is a badly framed question — because **Hochschule is the umbrella term**, not the opposite of a university. In Germany every higher education institution is a Hochschule; a Universität is one, and so is a Fachhochschule.

The real question is: **Universität or Fachhochschule (FH / HAW)?** This article compares the two using our own catalogue — 462 active institutions and more than 20,600 programmes.

## The terms first

| Term | What it means |
|---|---|
| **Hochschule** | Umbrella term for all higher education institutions |
| **Universität** | Research-oriented, with the right to award doctorates |
| **Fachhochschule (FH) / HAW** | Applied-science institution; many now call themselves *Hochschule für Angewandte Wissenschaften* |
| **Kunst-/Musikhochschule** | Art and music institutions; admission through an aptitude test |
| **Duale Hochschule** | Alternating study and company placement, under contract |

The confusion comes from renaming: many FHs dropped "Fachhochschule" and now call themselves simply "Hochschule" or "HAW". You identify the type from the institution's profile, not its name.

## What the catalogue actually shows

The 462 active institutions in our database break down as:

| Type | Count |
|---|---|
| Fachhochschule / HAW | **184** |
| Universität | **114** |
| Art and music institutions | 53 |
| Institutions of their own type and administrative colleges | 12 |

By ownership: **241 public-law institutions**, 96 private but state-recognised, 26 church-run.

So there are more FHs than universities. The belief that "only universities are real institutions" starts precisely here — and it is wrong: the two systems were built for different jobs.

## The concrete differences

| Dimension | Universität | Fachhochschule / HAW |
|---|---|---|
| Emphasis | Theory, research, methodology | Application, projects, industry links |
| Typical size (catalogue average) | **14,513 students** | **5,503 students** |
| Learning environment | Large lecture halls, more independent work | Smaller groups, closer contact with teaching staff |
| Placement | Usually not compulsory | A compulsory **Praxissemester** in many programmes |
| Doctoral rights | As a rule, yes | As a rule, no (exceptions are growing) |
| Programme profile | Weighted towards master's | Weighted towards bachelor's |

Doctoral rights show up clearly in the data: **155 institutions hold them, 30 hold limited rights, 178 have none.** Some states have granted strong FHs limited doctoral rights — still the exception, not the rule.

## The programmes: what the numbers say

| | Universität | Fachhochschule / HAW |
|---|---|---|
| Active programmes | **11,802** | **8,800** |
| Bachelor's | 4,961 | 4,764 |
| Master's | **5,522** | 3,382 |
| With English as a language of instruction | **2,432** | 1,576 |
| Share open-admission (where the mode is recorded) | ~63% | **~66%** |

Three readings:

1. **At bachelor's level the choice is nearly equal** (4,961 versus 4,764). "FHs have little on offer" is simply untrue.
2. **At master's level universities are clearly ahead** (5,522 versus 3,382). If you are thinking about academic depth or a doctorate, factor that in.
3. **The share of open-admission programmes is slightly higher at FHs.** The gap is small but consistent. (The figure covers only programmes whose admission mode is recorded in the catalogue.)

You can see how this splits in your own field directly in our [programme search](/en/programs) and on the [field pages](/en/fields).

## What changes in your application?

- **Admission criteria:** universities weigh grades and prerequisite coursework more heavily; FHs can give more weight to practical experience, internships and portfolios.
- **Vorpraktikum:** some FH engineering programmes require a multi-week placement before you even start. Check your admission letter for it.
- **Moving into a master's:** studying a master's at a university with an FH bachelor's is possible; some programmes impose conditions (Auflagen) for missing theoretical modules.
- **Language:** English-taught programmes exist in both types, with more of them at universities.

## Careers: what employers actually say

In engineering, IT, business and healthcare, German employers regard FH graduates as **ready for practice**; most job adverts say "university or FH". The difference is not in salary tables but in the shape of the route:

- **Industry and mid-sized companies:** the FH's density of projects and placements helps at entry.
- **Research and development, academia:** the university route is more direct, and effectively necessary if a doctorate is involved.
- **Senior public-sector careers:** some positions require specific degree combinations — read the advert rather than assuming.

Residence and job-search questions after graduation work the same way for both: our [job-seeker guide](/en/blog/germany-job-seeker-visa-2026-complete-guide-for-graduates-en) and [Zweckwechsel article](/en/blog/changing-student-visa-to-work-permit-germany-zweckwechsel-en) apply regardless of institution type.

## Which one suits whom?

Rather than declaring a winner, a decision frame:

**A university may suit you** if you are considering a doctorate or research; if your field rests on heavy theory (mathematics, physics, law, medicine); if you want to keep the academic option open; if the wider choice of English-taught master's matters to you.

**A Fachhochschule may suit you** if you want to move straight into industry; if small groups and close supervision make a difference for you; if you see the compulsory placement semester as an advantage; if you are looking to improve your admission chances.

**But a third factor decides more than either:** not the institution type but **the content of the programme.** Two identically named programmes, one at an FH and one at a university, can have entirely different module plans. Reading the Modulhandbuch tells you more than any type debate.

## Frequently asked questions

### Is an FH degree worth less than a university degree?
Bachelor's and master's degrees are the same qualifications at the same level at both types of institution. The difference lies in where the training puts its weight, not in a hierarchy.

### Can I do a doctorate at an FH?
Doctoral rights generally sit with universities; 178 institutions in our catalogue have none. The common route is a **cooperative doctorate**: the thesis is formally registered at a university while part of the supervision comes from the FH. A few states have granted limited rights to strong FHs — check your institution's specific position.

### Does the type affect recognition abroad?
What usually matters is that the institution is **state-recognised in Germany**, which applies to FHs too. Because assessment is programme-specific, clarify your individual case with the recognition authority in your home country before applying.

### Why do FHs barely appear in world rankings?
Because rankings mostly measure **research output and citations**, while FHs exist for teaching and application. Absence from a ranking is a question of measurement method, not quality.

### Which is easier to get into?
Catalogue data shows a slightly higher share of open-admission programmes at FHs (~66% versus ~63%). But "easier" misleads: popular FH programmes are fiercely contested, and plenty of university programmes have no numerus clausus at all.

### Where do private institutions fit in?
Ninety-six institutions in the catalogue are private and state-recognised. Recognition secures academic validity; the real question is what the fees buy you — small groups, an industry network, a shortened programme. At public institutions, study is mostly free of tuition.

## Conclusion and honest advice

The right question is not "which is better" but **"which route reaches my goal"**. For a doctorate and research, the university; for a fast, applied entry into industry, the FH is a strong option. And movement between them is normal — a great many students take an FH bachelor's into a university master's.

Before deciding, do three things: compare the **Modulhandbücher** of your two candidate programmes, check the admission mode, and look at where graduates now work. Those three tell you more than the institution-type argument ever will.

*Institution and programme counts reflect our catalogue as of September 2026; admission mode is not recorded for every programme, so the open-admission shares cover recorded entries only. Admission requirements change each intake — treat your programme's official page as authoritative.*
MD;

        $variants = [
            'tr' => [
                'slug' => $base,
                'title' => 'Universität mi Fachhochschule mi? Almanya\'da Kurum Türleri ve Gerçek Farklar',
                'excerpt' => 'Hochschule bir üst kavram, üniversitenin karşıtı değil. 462 kurum ve 20.600 programlık kendi katalog verimizle: FH mi Uni mi, doktora hakkı, NC\'siz program oranı, İngilizce programlar, kariyer farkı ve karar çerçevesi.',
                'meta_title' => 'Universität mi Fachhochschule mi? Almanya\'da Gerçek Farklar',
                'meta_description' => 'Uni vs FH: 184 FH, 114 üniversite, 20.600 program verisiyle karşılaştırma — doktora hakkı, NC\'siz oran, İngilizce programlar ve kariyer etkisi (2026).',
                'body' => $trBody,
            ],
            'de' => [
                'slug' => $base . '-de',
                'title' => 'Universität oder Fachhochschule? Hochschultypen in Deutschland im Vergleich',
                'excerpt' => 'Hochschule ist der Oberbegriff, nicht das Gegenteil von Universität. Mit den Daten unseres Katalogs aus 462 Einrichtungen und 20.600 Studiengängen: Promotionsrecht, zulassungsfreie Anteile, englischsprachige Programme, Karrierewege und ein Entscheidungsrahmen.',
                'meta_title' => 'Universität oder Fachhochschule? Die echten Unterschiede',
                'meta_description' => 'Uni vs. FH mit Katalogdaten: 184 FHs, 114 Universitäten, 20.600 Studiengänge — Promotionsrecht, zulassungsfreie Anteile, Sprache, Karriere (2026).',
                'body' => $deBody,
            ],
            'en' => [
                'slug' => $base . '-en',
                'title' => 'University or Fachhochschule? German Institution Types and the Real Differences',
                'excerpt' => 'Hochschule is the umbrella term, not the opposite of a university. Using our own catalogue of 462 institutions and 20,600 programmes: doctoral rights, open-admission shares, English-taught options, career routes and a decision frame.',
                'meta_title' => 'University or Fachhochschule? The Real Differences in Germany',
                'meta_description' => 'Uni vs FH with catalogue data: 184 FHs, 114 universities, 20,600 programmes — doctoral rights, open admission, English-taught options, careers (2026).',
                'body' => $enBody,
            ],
        ];

        foreach ($variants as $locale => $v) {
            $html = Str::markdown($v['body'], ['html_input' => 'allow', 'allow_unsafe_links' => false]);
            $payload = [
                'locale' => $locale, 'translation_group_id' => $groupId, 'user_id' => $userId, 'category_id' => $categoryId,
                'title' => $v['title'], 'excerpt' => Str::limit($v['excerpt'], 250, '…'),
                'content_md' => $v['body'], 'content_html' => $html,
                'meta_title' => $v['meta_title'], 'meta_description' => Str::limit($v['meta_description'], 158, '…'),
                'reading_minutes' => max(1, (int) round(str_word_count(strip_tags($html)) / 200)),
                'is_published' => true, 'published_at' => $existing->published_at ?? now(),
            ];
            $post = Post::where('slug', $v['slug'])->first();
            $post ? $post->update($payload) : Post::create($payload + ['slug' => $v['slug']]);
        }
    }

    public function down(): void
    {
        // TR yazı yerinde kalır (yalnızca gövdesi yenilendi); sonradan eklenen çeviriler silinir.
        Post::whereIn('slug', [
            'hochschule-vs-universitaet-vs-fh-differences-in-germany-de',
            'hochschule-vs-universitaet-vs-fh-differences-in-germany-en',
        ])->delete();
    }
};
