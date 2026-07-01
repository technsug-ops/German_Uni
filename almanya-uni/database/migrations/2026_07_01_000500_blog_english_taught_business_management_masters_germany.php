<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN): İngilizce işletme/management master programları — Almancasız (2026).
 * Doğrulandı: Kamu ünilerinde İngilizce Management/Finance/Marketing/Analytics master bol ve genelde
 * ücretsiz (~150–350€/dönem katkısı, BW non-EU ~1.500€); özel işletme okulları pahalı (~20–40k€).
 * Şartlar: lisans + IELTS/TOEFL, bazı programlarda GMAT/GRE. Almancasız okunur ama kurumsal iş/staj/günlük
 * hayat için Almanca ciddi avantaj — dürüst uyarı. Yazar: Halil Yaprakli. Kategori: almanyada-egitim.
 * FK-safe + slug-bazlı idempotent.
 */
return new class extends Migration
{
    public function up(): void
    {
        $groupId = 'b1a10000-1111-4baa-9f30-aa01bb02ee01';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'almanyada-egitim')->value('id')
            ?? DB::table('categories')->where('slug', 'universities')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
"Almanca bilmeden Almanya'da İşletme okuyabilir miyim?" Lisans için cevap çoğunlukla "zor" olsa da, **master seviyesinde tablo tamamen değişiyor.** Almanya'da İngilizce eğitim veren Management, Finance, Marketing ve Business Analytics master programları **bol** ve kamu üniversitelerinde çoğu zaman **ücretsiz.** Ama işin bir de dürüst yüzü var: diplomayı İngilizce alman, iş piyasasında Almancasız iş bulacağın anlamına gelmiyor. Bu yazı dengeli ve net bir rehber.

## İngilizce Management / Finance / Marketing / Analytics master'ı bol
İşletme, uluslararası öğrenciler arasında **en popüler alanlardan** biri ve Almanya bu talebe İngilizce master programlarıyla cevap veriyor. En yaygın başlıklar:

- **Management / International Management** — genel işletme, strateji, liderlik.
- **Finance / Financial Management** — bankacılık, kurumsal finans, yatırım.
- **Marketing / International Marketing** — marka, dijital pazarlama, tüketici davranışı.
- **Business Analytics / Data Science in Business** — hızla büyüyen, veri odaklı alan.
- **Supply Chain, Accounting & Controlling, Entrepreneurship** gibi uzmanlaşmalar.

Bachelor'da durum farklı: **çoğu işletme lisansı Almanca (C1) ister**, İngilizce bachelor nadir ve genelde **özel okullarda** bulunur. Master'da ise İngilizce programlar hem kamu hem özel tarafta yaygın. Yani Almancasız plan yapan biri için **master, bachelor'dan çok daha gerçekçi bir kapı.**

## Kamu (ücretsiz) vs özel (pahalı) İngilizce programlar
En kritik karar bu: aynı alanı **ücretsiz kamu üniversitesinde** de, **pahalı özel işletme okulunda** da okuyabilirsin. Farkı bilerek seç.

| Boyut | Kamu üniversite | Özel işletme okulu |
|---|---|---|
| Ücret (2025/26, yaklaşık) | Ücretsiz — ~150–350€/dönem katkısı; BW non-EU ~1.500€/dönem | ~20.000–40.000€ (program boyu) |
| Örnekler | Mannheim, Uni Köln, Goethe Frankfurt, LMU München, Münster | WHU – Otto Beisheim, Frankfurt School, ESMT Berlin, HHL Leipzig |
| Kabul | Rekabetçi (tepe ünilerde NC), belge ağırlıklı | Mülakat + bazen GMAT, sanayi bağı güçlü |
| Dil | İngilizce programlar mevcut | Çoğu tamamen İngilizce |
| Güçlü yanı | Maliyet + akademik itibar | Network, kariyer hizmeti, MBA seçeneği |

**Mannheim, Almanya'nın 1 numaralı işletme üniversitesi kabul edilir** ve kamu tarafında ücretsizdir — yani "ücretsiz" her zaman "zayıf" demek değil. Özel okulların asıl vaadi ücretin değdiği **network ve kariyer hızı.** *(Kamu vs özel kararını ayrı yazıda derinleştireceğiz.)*

## Şartlar: lisans + İngilizce + bazen GMAT/GRE
Tipik bir İngilizce işletme master başvurusunda beklenenler:

1. **İlgili lisans diploması** — çoğu program işletme/ekonomi/ilgili alan ister; bazıları farklı alandan geçişe (conversion) açık.
2. **İngilizce yeterliliği** — genelde **IELTS ~6.5–7.0** veya **TOEFL iBT ~90–100** (program değişir, teyit et).
3. **GMAT/GRE** — tepe programlarda ve çoğu özel okulda istenir (ör. GMAT ~600+); birçok kamu programında istenmez.
4. **Not ortalaması / CV / motivasyon mektubu / referans** — özellikle rekabetçi ve özel programlarda belirleyici.

**Not:** Almanca yeterliliği İngilizce programlar için **başvuru şartı değildir** — ama aşağıda göreceğin gibi iş için ayrı bir mesele.

## Ücret gerçeği: kamu ücretsiz, BW ~1.500€, özel ~20–40k
- **Kamu üniversitesi:** Öğrenim ücreti yok; sadece **dönem katkısı ~150–350€** (semester ticket, öğrenci hizmetleri dahil).
- **Baden-Württemberg istisnası:** Bu eyalette **AB-dışı öğrenciler ~1.500€/dönem** öder (Mannheim bu eyalette). Yine de özel okula kıyasla düşük.
- **Özel okullar:** Program boyunca **~20.000–40.000€**; MBA'lerde daha da yüksek olabilir.
- **Yaşam gideri** her iki yolda ortak: vize için **Sperrkonto** (2025'te yıllık ~11.904€, doğrula) + aylık kira/gıda/sigorta.

> *2025/2026 itibarıyla, yaklaşık rakamlar — resmî kaynaktan teyit et.*

## Almancasız tuzağı: diploma İngilizce, iş Almanca
İşte yazının en dürüst kısmı. Programın İngilizce olması, **Almanya iş piyasasının İngilizce olduğu anlamına gelmez.** Tech/CS'in aksine, **kurumsal ve işletme rollerinin çoğu günlük işi Almanca yürütür.**

- **Staj / Werkstudent:** Okurken alacağın işlerin büyük kısmı Almanca ister; bu tecrübe işletmede kariyerin anahtarıdır.
- **Mezuniyet sonrası iş:** Controlling, İK, pazarlama, kurumsal roller genelde **Almanca şart/çok güçlü avantaj.** Danışmanlık ve finansta bazı İngilizce roller olsa da rekabet yüksek.
- **Günlük hayat:** Resmî işler, kira, sağlık — Almanca hayatı ciddi kolaylaştırır.

**Dürüst tavsiye:** İngilizce master'a başla, ama **paralel olarak B2-C1 Almanca'yı ciddi bir hedef yap.** Almancasız master mümkün; Almancasız kariyer çok daha zor.

## Başvuru: uni-assist / doğrudan, DAAD
- **uni-assist:** AB-dışı (ör. Türkiye) adayların çoğu belge/denklik ön-kontrolünü buradan yapar; bazı üniler **doğrudan** başvuru alır — her programın sayfasını oku.
- **Zamanlama:** Kış dönemi için başvurular genelde **kıştan önceki bahar/yaz** kapanır; belge çevirisi + onay zaman alır, erken başla.
- **Burs:** **DAAD** master/PhD seviyesinde en bilinen kaynak; ayrıca üniversite ve vakıf bursları. Bursu garanti sayma, planı finansmanla kur.

## Sonuç & dürüst tavsiye
Almancasız Almanya'da İşletme master'ı **gerçekçi ve güçlü bir plan:** İngilizce Management/Finance/Marketing/Analytics programları bol, kamuda çoğu **ücretsiz** ve dünya çapında itibarlı. Ama tek başına diploma yetmez — **Almanya'da uzun vadeli işletme kariyeri Almanca ile gelir.** O yüzden: İngilizce oku, **B2-C1 Almanca'yı paralel yükselt**, staj/Werkstudent tecrübesi topla ve maliyeti (kamu vs özel) gözünü açarak seç.

İlgili: [Almanya'da İşletme (BWL) okumak — rehber](/tr/blog/studying-business-administration-bwl-in-germany-international-student-guide) · [Master mı, İş Arama Vizesi mi — iki kariyer anahtarı](/tr/blog/germany-masters-vs-job-seeker-visa-two-keys-career) · [Almanya'da IT/tech'te çalışmak — Mavi Kart & maaş (karşılaştırma için)](/tr/blog/working-in-it-tech-in-germany-as-a-foreigner-blue-card-salary).

---
*Genel rehberdir. Ücretler, kabul ve dil koşulları programa/yıla ve eyalete göre değişir; 2026 itibarıyla resmî üniversite ve kurum kaynaklarından teyit et.*
MD;

        $deBody = <<<'MD'
"Kann ich in Deutschland BWL studieren, ohne Deutsch zu können?" Beim Bachelor lautet die ehrliche Antwort meist "schwierig" — beim **Master ändert sich das Bild aber komplett.** Englischsprachige Master in Management, Finance, Marketing und Business Analytics sind in Deutschland **reichlich vorhanden** und an staatlichen Universitäten oft **kostenlos.** Doch es gibt auch eine ehrliche Kehrseite: Ein englischer Abschluss bedeutet nicht, dass du auf dem Arbeitsmarkt ohne Deutsch einen Job findest. Dieser Artikel ist ein ausgewogener, klarer Leitfaden.

## Englische Master in Management / Finance / Marketing / Analytics gibt es reichlich
BWL gehört zu den **beliebtesten Fächern** internationaler Studierender, und Deutschland bedient diese Nachfrage mit englischsprachigen Masterprogrammen. Die häufigsten Richtungen:

- **Management / International Management** — allgemeine BWL, Strategie, Führung.
- **Finance / Financial Management** — Banking, Corporate Finance, Investment.
- **Marketing / International Marketing** — Marke, digitales Marketing, Konsumentenverhalten.
- **Business Analytics / Data Science in Business** — schnell wachsend, datengetrieben.
- Spezialisierungen wie **Supply Chain, Accounting & Controlling, Entrepreneurship**.

Beim Bachelor ist es anders: **Die meisten BWL-Bachelor verlangen Deutsch (C1)**, englische Bachelor sind selten und meist an **privaten Hochschulen**. Beim Master dagegen sind englische Programme sowohl an staatlichen als auch an privaten Häusern verbreitet. Für jemanden ohne Deutsch ist der **Master also eine deutlich realistischere Tür als der Bachelor.**

## Staatlich (kostenlos) vs privat (teuer)
Das ist die zentrale Entscheidung: Dasselbe Fach kannst du **kostenlos an einer staatlichen Uni** oder **teuer an einer privaten Business School** studieren. Wähle mit offenen Augen.

| Aspekt | Staatliche Universität | Private Business School |
|---|---|---|
| Kosten (2025/26, ca.) | Kostenlos — ~150–350€/Semester Beitrag; BW Nicht-EU ~1.500€/Semester | ~20.000–40.000€ (gesamt) |
| Beispiele | Mannheim, Uni Köln, Goethe Frankfurt, LMU München, Münster | WHU – Otto Beisheim, Frankfurt School, ESMT Berlin, HHL Leipzig |
| Zulassung | Kompetitiv (NC an Top-Unis), unterlagenbasiert | Interview + oft GMAT, starker Industriebezug |
| Sprache | Englische Programme vorhanden | Meist komplett Englisch |
| Stärke | Kosten + akademischer Ruf | Netzwerk, Career Service, MBA-Option |

**Mannheim gilt als die Nummer 1 der BWL in Deutschland** — und ist als staatliche Uni kostenlos. "Kostenlos" heißt also nicht "schwach". Das eigentliche Versprechen der Privaten ist das, wofür sich die Gebühr lohnt: **Netzwerk und Karrieregeschwindigkeit.** *(Die Entscheidung staatlich vs privat vertiefen wir in einem eigenen Artikel.)*

## Voraussetzungen: Bachelor + Englisch + manchmal GMAT/GRE
Typischerweise erwartet dich bei einer englischsprachigen BWL-Master-Bewerbung:

1. **Passender Bachelor** — meist BWL/Wirtschaft/verwandt; einige Programme sind für Quereinsteiger (Conversion) offen.
2. **Englischnachweis** — meist **IELTS ~6,5–7,0** oder **TOEFL iBT ~90–100** (variiert, prüfen).
3. **GMAT/GRE** — an Top-Programmen und den meisten Privaten verlangt (z. B. GMAT ~600+); an vielen staatlichen Programmen nicht.
4. **Noten / CV / Motivationsschreiben / Referenzen** — besonders bei kompetitiven und privaten Programmen entscheidend.

**Hinweis:** Ein Deutschnachweis ist für englische Programme **keine Zulassungsvoraussetzung** — für den Job aber, wie du gleich siehst, eine andere Sache.

## Kosten-Realität: staatlich kostenlos, BW ~1.500€, privat ~20–40k
- **Staatliche Uni:** Keine Studiengebühren; nur **Semesterbeitrag ~150–350€** (inkl. Semesterticket, Studierendenwerk).
- **Ausnahme Baden-Württemberg:** Hier zahlen **Nicht-EU-Studierende ~1.500€/Semester** (Mannheim liegt in BW). Trotzdem günstig gegenüber privat.
- **Private Schools:** Über das Programm **~20.000–40.000€**; bei MBAs noch höher.
- **Lebenshaltung** gilt für beide Wege: fürs Visum das **Sperrkonto** (2025 jährlich ~11.904€, prüfen) + monatlich Miete/Essen/Versicherung.

> *Stand 2025/2026, ungefähre Zahlen — aus offiziellen Quellen bestätigen.*

## Die Falle "ohne Deutsch": Abschluss auf Englisch, Job auf Deutsch
Das ist der ehrlichste Teil. Ein englisches Programm bedeutet **nicht, dass der deutsche Arbeitsmarkt englisch ist.** Anders als in Tech/CS läuft der Alltag in **den meisten Unternehmens- und BWL-Rollen auf Deutsch.**

- **Praktikum / Werkstudent:** Ein Großteil dieser Jobs verlangt Deutsch — und genau diese Erfahrung ist in der BWL der Schlüssel zur Karriere.
- **Job nach dem Abschluss:** Controlling, HR, Marketing, Unternehmensrollen setzen meist **Deutsch voraus oder als starken Vorteil**. In Consulting und Finance gibt es einige englische Rollen, aber der Wettbewerb ist hart.
- **Alltag:** Behörden, Miete, Gesundheit — mit Deutsch deutlich leichter.

**Ehrlicher Rat:** Starte mit dem englischen Master, aber mach **parallel B2-C1 Deutsch zu einem ernsthaften Ziel.** Master ohne Deutsch ist möglich; Karriere ohne Deutsch ist viel schwerer.

## Bewerbung: uni-assist / direkt, DAAD
- **uni-assist:** Die meisten Nicht-EU-Bewerber (z. B. aus der Türkei) lassen Unterlagen/Äquivalenz hier vorprüfen; manche Unis nehmen **direkt** an — lies jede Programmseite.
- **Timing:** Fürs Wintersemester schließen Bewerbungen oft **im Frühjahr/Sommer davor**; Übersetzung + Beglaubigung dauern, fang früh an.
- **Stipendium:** Der **DAAD** ist auf Master/PhD-Ebene die bekannteste Quelle; dazu Uni- und Stiftungsstipendien. Betrachte ein Stipendium nicht als sicher — plane die Finanzierung solide.

## Fazit & ehrlicher Rat
Ein BWL-Master ohne Deutsch ist in Deutschland ein **realistischer, starker Plan:** englische Management-/Finance-/Marketing-/Analytics-Programme gibt es reichlich, an staatlichen Unis oft **kostenlos** und weltweit angesehen. Aber der Abschluss allein reicht nicht — **eine langfristige BWL-Karriere in Deutschland kommt mit Deutsch.** Also: studiere auf Englisch, **bau parallel B2-C1 Deutsch auf**, sammle Praktikums-/Werkstudenten-Erfahrung und wähle die Kosten (staatlich vs privat) mit offenen Augen.

Verwandt: [Master vs Job-Seeker-Visum — zwei Karriereschlüssel](/de/blog/germany-masters-vs-job-seeker-visa-two-keys-career-de) · [In der IT/Tech in Deutschland arbeiten — Blue Card & Gehalt (zum Vergleich)](/de/blog/working-in-it-tech-in-germany-as-a-foreigner-blue-card-salary-de).

---
*Allgemeiner Leitfaden. Gebühren, Zulassung und Sprachanforderungen variieren je nach Programm, Jahr und Bundesland; Stand 2026 bitte bei offiziellen Uni- und Behördenquellen bestätigen.*
MD;

        $enBody = <<<'MD'
"Can I study business in Germany without German?" For a bachelor's the honest answer is usually "difficult" — but at **master's level the picture changes completely.** English-taught master's in Management, Finance, Marketing and Business Analytics are **abundant** in Germany, and at public universities they are often **free.** There is also an honest flip side, though: an English-taught degree does not mean you'll land a job on the market without German. This article is a balanced, plain guide.

## English-taught Management / Finance / Marketing / Analytics master's are plentiful
Business is one of the **most popular fields** for international students, and Germany meets that demand with English-taught master's programmes. The most common tracks:

- **Management / International Management** — general business, strategy, leadership.
- **Finance / Financial Management** — banking, corporate finance, investment.
- **Marketing / International Marketing** — brand, digital marketing, consumer behaviour.
- **Business Analytics / Data Science in Business** — fast-growing and data-driven.
- Specialisations such as **Supply Chain, Accounting & Controlling, Entrepreneurship**.

Bachelor's are different: **most business bachelor's require German (C1)**, English-taught bachelor's are rare and usually sit at **private schools**. At master's level, by contrast, English programmes are common on both the public and private side. So for someone without German, the **master's is a far more realistic door than the bachelor's.**

## Public (free) vs private (expensive)
This is the key decision: you can study the same field **free at a public university** or **expensively at a private business school.** Choose with open eyes.

| Aspect | Public university | Private business school |
|---|---|---|
| Cost (2025/26, approx.) | Free — ~€150–350/semester fee; BW non-EU ~€1,500/semester | ~€20,000–40,000 (total) |
| Examples | Mannheim, Uni Köln, Goethe Frankfurt, LMU München, Münster | WHU – Otto Beisheim, Frankfurt School, ESMT Berlin, HHL Leipzig |
| Admission | Competitive (NC at top unis), document-based | Interview + often GMAT, strong industry links |
| Language | English programmes available | Mostly fully English |
| Strength | Cost + academic reputation | Network, career service, MBA option |

**Mannheim is regarded as Germany's number 1 for business** — and as a public university it is free, so "free" does not mean "weak." What the privates really sell, and what the fee is meant to buy, is **network and career speed.** *(We go deeper on the public vs private decision in a separate article.)*

## Requirements: bachelor's + English + sometimes GMAT/GRE
A typical English-taught business master's application expects:

1. **A relevant bachelor's** — usually business/economics/related; some programmes are open to conversion applicants from other fields.
2. **English proof** — typically **IELTS ~6.5–7.0** or **TOEFL iBT ~90–100** (varies, verify).
3. **GMAT/GRE** — required at top programmes and most privates (e.g. GMAT ~600+); not asked at many public programmes.
4. **Grades / CV / motivation letter / references** — decisive especially at competitive and private programmes.

**Note:** A German-language proof is **not an admission requirement** for English programmes — but for the job, as you'll see next, it's a different matter.

## Cost reality: public free, BW ~€1,500, private ~€20–40k
- **Public university:** No tuition; only a **semester fee ~€150–350** (includes semester ticket, student services).
- **Baden-Württemberg exception:** Here **non-EU students pay ~€1,500/semester** (Mannheim is in BW). Still cheap versus private.
- **Private schools:** Over the programme **~€20,000–40,000**; MBAs can be higher.
- **Living costs** apply on both routes: for the visa the **Sperrkonto** (blocked account, ~€11,904/year in 2025, verify) + monthly rent/food/insurance.

> *As of 2025/2026, approximate figures — confirm from official sources.*

## The "without German" trap: degree in English, job in German
This is the most honest part. An English programme does **not mean the German job market is English.** Unlike tech/CS, **most corporate and business roles run day-to-day in German.**

- **Internship / Werkstudent:** A large share of these jobs require German — and this experience is the key to a business career.
- **Job after graduation:** Controlling, HR, marketing and corporate roles usually **require German or treat it as a strong advantage.** Consulting and finance have some English roles, but competition is fierce.
- **Daily life:** Authorities, renting, healthcare — much easier with German.

**Honest advice:** Start the English-taught master's, but make **B2-C1 German a serious parallel goal.** A master's without German is possible; a career without German is much harder.

## Applying: uni-assist / direct, DAAD
- **uni-assist:** Most non-EU applicants (e.g. from Turkey) run document/equivalence pre-checks here; some universities admit **directly** — read each programme page.
- **Timing:** For the winter intake, applications often close **in the spring/summer before**; translation + certification take time, so start early.
- **Scholarships:** **DAAD** is the best-known source at master's/PhD level; add university and foundation scholarships. Don't treat a scholarship as guaranteed — build the plan around funding.

## Conclusion & honest advice
A business master's without German is a **realistic, strong plan** in Germany: English-taught Management/Finance/Marketing/Analytics programmes are plentiful, often **free** at public universities, and well respected worldwide. But the degree alone is not enough — **a long-term business career in Germany comes with German.** So: study in English, **build B2-C1 German in parallel**, gather internship/Werkstudent experience, and choose the cost (public vs private) with open eyes.

Related: [Master's vs the Job-Seeker Visa — two career keys](/en/blog/germany-masters-vs-job-seeker-visa-two-keys-career-en) · [Working in IT/tech in Germany — Blue Card & salary (for comparison)](/en/blog/working-in-it-tech-in-germany-as-a-foreigner-blue-card-salary-en).

---
*General guide. Fees, admission and language requirements vary by programme, year and federal state; as of 2026, confirm with official university and government sources.*
MD;

        $variants = [
            'tr' => ['slug'=>'english-taught-business-management-masters-in-germany-without-german',    'title'=>'Almancasız Almanya\'da İşletme: İngilizce Master Programları (2026)', 'excerpt'=>'Almancasız Almanya\'da İşletme master\'ı gerçekçi: İngilizce Management/Finance/Marketing/Analytics programları bol, kamuda çoğu ücretsiz (~150–350€/dönem, BW ~1.500€), özelde ~20–40k€. Şartlar: lisans + IELTS/TOEFL, bazen GMAT/GRE. Dürüst uyarı: diploma İngilizce olsa da kurumsal iş/staj/günlük hayat için Almanca (B2-C1) ciddi avantaj.', 'meta_title'=>'Almancasız İşletme Master\'ı Almanya (2026)', 'meta_description'=>'Almanya\'da İngilizce işletme master\'ı: Management/Finance/Marketing/Analytics bol, kamuda ücretsiz, özelde ~20–40k€. Şartlar, ücret ve Almanca gerçeği (2026).', 'body'=>$trBody],
            'de' => ['slug'=>'english-taught-business-management-masters-in-germany-without-german-de', 'title'=>'BWL in Deutschland ohne Deutsch: Englischsprachige Master (2026)', 'excerpt'=>'Ein BWL-Master ohne Deutsch ist realistisch: englische Management-/Finance-/Marketing-/Analytics-Programme gibt es reichlich, an staatlichen Unis oft kostenlos (~150–350€/Semester, BW ~1.500€), privat ~20–40k€. Voraussetzungen: Bachelor + IELTS/TOEFL, manchmal GMAT/GRE. Ehrlich: Der Abschluss ist englisch, der Job oft deutsch — B2-C1 planen.', 'meta_title'=>'BWL-Master ohne Deutsch in Deutschland (2026)', 'meta_description'=>'Englischsprachige BWL-Master in Deutschland: Management/Finance/Marketing/Analytics, staatlich kostenlos, privat ~20–40k€. Voraussetzungen, Kosten & Deutsch-Realität (2026).', 'body'=>$deBody],
            'en' => ['slug'=>'english-taught-business-management-masters-in-germany-without-german-en', 'title'=>'Business in Germany Without German: English-Taught Master\'s (2026)', 'excerpt'=>'A business master\'s without German is realistic: English-taught Management/Finance/Marketing/Analytics programmes are plentiful, often free at public universities (~€150–350/semester, BW ~€1,500), private ~€20–40k. Requirements: bachelor\'s + IELTS/TOEFL, sometimes GMAT/GRE. Honest note: the degree is English but the job is often German — plan for B2-C1.', 'meta_title'=>'Business Master\'s in Germany Without German (2026)', 'meta_description'=>'English-taught business master\'s in Germany: Management/Finance/Marketing/Analytics, public free, private ~€20–40k. Requirements, cost & the German-language reality (2026).', 'body'=>$enBody],
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
            'english-taught-business-management-masters-in-germany-without-german',
            'english-taught-business-management-masters-in-germany-without-german-de',
            'english-taught-business-management-masters-in-germany-without-german-en',
        ])->delete();
    }
};
