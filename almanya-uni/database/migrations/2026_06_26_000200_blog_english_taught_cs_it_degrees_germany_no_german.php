<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN): English-taught CS / IT degrees in Germany without German.
 * Verified: English bachelors are scarce + mostly private (CODE, Constructor, IU, SRH, GISMA, €10k–20k/yr);
 * English masters are abundant + tuition-free at public unis (semester fee ~€150–350; BW ~€1,500/sem non-EU).
 * Realistic no-German route = bachelor abroad → English master in Germany. German still needed for daily life/jobs.
 * Yazar: Halil Yaprakli. Kategori: almanyada-egitim. FK-safe + slug-bazlı idempotent.
 */
return new class extends Migration
{
    public function up(): void
    {
        $groupId = 'b7d1e2a0-2222-4c8a-9f30-aa01bb02cc02';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'almanyada-egitim')->value('id')
            ?? DB::table('categories')->where('slug', 'universities')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
"Almancam yok ama Almanya'da bilgisayar mühendisliği okumak istiyorum, İngilizce bir program bulurum nasılsa" — bu cümleyi çok duyuyoruz. Kısmen doğru, kısmen tuzak. Bu yazıda İngilizce CS/IT eğitiminin Almanya'daki gerçek haritasını, ücretsiz devlet ile pahalı özel ayrımını ve "Almancasız" hayalin nerede çatladığını dürüstçe anlatıyoruz.

## Dürüst tablo: İngilizce lisans nadir, İngilizce yüksek lisans bol

Burada en kritik ayrımı baştan netleştirelim, çünkü çoğu kişi bunu karıştırıyor:

- **İngilizce LİSANS (Bachelor):** Devlet üniversitelerinde **çok nadir**. İngilizce okutulan bilgisayar bilimi lisansları ağırlıkla **özel üniversitelerde**: CODE University Berlin, Constructor University Bremen, IU International, SRH, GISMA. Bunlar **ücretli** — kabaca **yılda €10.000–20.000** bandında.
- **İngilizce YÜKSEK LİSANS (Master):** Devlet üniversitelerinde **bol** ve genellikle **harçsız** — sadece dönemlik katkı payı ödersin (~€150–350/dönem). İstisna: **Baden-Württemberg** eyaleti AB-dışı öğrencilerden ~**€1.500/dönem** alıyor.

Bu yüzden gerçekçi "Almancasız" rota genelde şudur: **lisansı kendi ülkende (ya da İngilizce başka bir yerde) bitir → Almanya'da İngilizce bir master yap.** Master kabul stratejisini ayrı yazılarımızda işliyoruz.

> Detaylar için: [CS/Informatik'i yabancı olarak okumak](/tr/blog/studying-computer-science-informatik-in-germany-as-a-foreigner) ve [İngilizce master kabul şansı ve strateji](/tr/blog/english-master-admission-chances-germany-gpa-fh-vs-uni-strategy).

## Almanca tuzağı: program İngilizce olsa bile hayat Almanca

Programın dili İngilizce olabilir — ama **günlük hayat İngilizce değil.** Yabancı öğrencilerin en sık şikayeti tam burada:

- **Bürokrasi Almanca:** Anmeldung (ikamet kaydı), Ausländerbehörde (yabancılar dairesi), banka, sağlık sigortası yazışmaları — çoğu Almanca.
- **Werkstudent (öğrenci işçi) ve stajlar:** İngilizce-yalnız teknik işler Berlin/startup balonu dışında azdır. Çoğu Werkstudent ilanı en az **B1–B2 Almanca** ister.
- **Sosyal entegrasyon:** Almanca olmadan yerel arkadaş çevresi kurmak zordur; "expat balonunda" kalırsın.

Yani İngilizce program seni dersten geçirir ama **işe ve hayata geçirmez.** Almancayı paralel öğren.

> İlgili: [Almancasız okumak ve öğrenci işi gerçeği](/tr/blog/studying-in-germany-without-german-living-and-student-job-reality) ve [İş için Almanca gerçeği](/tr/blog/german-language-reality-for-jobs-in-germany-the-honest-truth).

## Devlet (ücretsiz) vs özel (ücretli) İngilizce CS

| | Devlet üniversite | Özel üniversite |
|---|---|---|
| Ücret | Harçsız + dönem katkı payı (~€150–350); BW'de AB-dışı ~€1.500/dönem | ~€10.000–20.000/yıl |
| İngilizce lisans | Çok nadir | Asıl burada (CODE, Constructor, IU, SRH, GISMA) |
| İngilizce master | Bol | Var ama gerek yok (devlette bedava) |
| İtibar/akreditasyon | Köklü, tanınır | **Değişken — mutlaka kontrol et** |
| Kabul | Daha rekabetçi | Genelde daha kolay |

**Özel üniversite uyarısı:** Bazıları gerçekten kaliteli ve uluslararası (Constructor Bremen güçlü bir bilim geçmişine sahiptir), bazıları ise pahalı ama itibarı tartışmalıdır. Para vermeden önce **akreditasyonu (örn. devlet tanıması, programmatik akreditasyon), mezun istihdamını ve diplomanın Türkiye/AB'de tanınırlığını** araştır.

## Harç ve maliyet gerçeği

Net ayrım:

- **Devlet master:** Eğitim "bedava" ama her dönem **katkı payı (~€150–350)** ödersin — bu genelde toplu taşıma bileti (Semesterticket) içerir.
- **Baden-Württemberg:** AB-dışı öğrenci ek **~€1.500/dönem** öğrenim ücreti öder (eyalete özel).
- **Özel üniversite:** Yıllık **€10.000–20.000** öğrenim ücreti.

Bunlara ek olarak vize için **bloke hesap (Sperrkonto)** ve geçim masrafları gelir; bu rakamlar yıldan yıla güncellenir, başvuru öncesi resmi kaynaktan doğrula. Burada uydurma rakam vermiyoruz; güncel geçim/bloke hesap tutarını her zaman güncel resmi listeden teyit et.

## Diploman doğrudan tutmuyorsa: Studienkolleg / anabin

İngilizce program da olsa **denklik kuralları değişmez.** Türk lise diploması Almanya'da çoğu zaman **doğrudan üniversite girişine yetmez**; ya **Studienkolleg** (hazırlık yılı + Feststellungsprüfung) ya da **Türkiye'de 1+ yıl üniversite** ile Abitur denkliği sağlanır. Programın İngilizce olması bu adımı atlatmaz.

> Önce şunları oku: [Anabin ve Türk diploması denkliği](/tr/blog/what-are-anabin-h-h-h-how-is-your-turkish-diploma) ve [Studienkolleg bir dil okulu değildir](/tr/blog/studienkolleg-is-not-a-language-school-what-it-really-is).

## Almancasız iş bulma: mümkün ama dar

İngilizce-yalnız teknik işler Almanya'da **Berlin/startup ve büyük teknoloji** şirketlerinde yoğunlaşır (SAP, Zalando, N26, Delivery Hero, Trade Republic gibi). Berlin dışında, özellikle Mittelstand (orta ölçekli sanayi) ve otomotivde **Almanca devasa avantaj.** Gerçekçi tavsiye: işi İngilizceyle kapabilirsin ama **B1–B2 Almancayı yine de öğren** — kariyer kapılarını ikiye katlar.

> Ayrıntılar: [Almanya'da yabancı olarak IT/tech çalışmak: Mavi Kart ve maaş](/tr/blog/working-in-it-tech-in-germany-as-a-foreigner-blue-card-salary).

## Sonuç ve dürüst tavsiye

- **İngilizce lisans** istiyorsan büyük olasılıkla **özel + ücretli** bir yola bakıyorsun — akreditasyonu didik didik araştır.
- **En verimli rota:** lisansı uygun fiyatla bitir → Almanya'da **harçsız İngilizce master** yap.
- Program İngilizce olsa da **Almanca öğrenmeyi ertelemen**, staj/Werkstudent ve Berlin-dışı iş kapılarını kapatır.
- Diploma denkliğini (anabin/Studienkolleg) baştan çöz; İngilizce program bu adımı silmez.

> Devamı: [IT'de çalışmak: Mavi Kart ve maaş](/tr/blog/working-in-it-tech-in-germany-as-a-foreigner-blue-card-salary) ve [CS diplomasıyla ne yapılır: iş piyasası ve maaş](/tr/blog/what-to-do-with-a-computer-science-degree-in-germany-job-market-salary).

*Bu yazıdaki ücret, harç, eşik ve vize bilgileri 2026 başı itibarıyladır ve yıllık güncellenir; başvurudan önce resmi kaynaklardan doğrula.*
MD;

        $deBody = <<<'MD'
"Ich kann kein Deutsch, will aber Informatik in Deutschland studieren — ich finde schon ein englischsprachiges Programm." Diesen Satz hören wir oft. Teils stimmt er, teils ist er eine Falle. Hier bekommst du die ehrliche Landkarte des englischsprachigen CS/IT-Studiums in Deutschland: kostenlose staatliche vs. teure private Wege — und wo der "Ohne-Deutsch"-Traum bricht.

## Die ehrliche Aufteilung: englische Bachelor selten, englische Master reichlich

Diese Unterscheidung musst du von Anfang an verstehen, denn die meisten verwechseln sie:

- **Englischer BACHELOR:** An staatlichen Unis **sehr selten**. Englischsprachige Informatik-Bachelor gibt es vor allem an **privaten Hochschulen**: CODE University Berlin, Constructor University Bremen, IU International, SRH, GISMA. Diese sind **kostenpflichtig** — grob **€10.000–20.000 pro Jahr**.
- **Englischer MASTER:** An staatlichen Unis **reichlich** vorhanden und meist **studiengebührenfrei** — du zahlst nur den Semesterbeitrag (~€150–350/Semester). Ausnahme: **Baden-Württemberg** verlangt von Nicht-EU-Studierenden ~**€1.500/Semester**.

Deshalb lautet die realistische "Ohne-Deutsch"-Route meist: **Bachelor im Ausland (oder anderswo auf Englisch) abschließen → in Deutschland einen englischen Master machen.** Die Master-Zulassungsstrategie behandeln wir separat.

> Mehr dazu: [Informatik als Ausländer studieren](/de/blog/studying-computer-science-informatik-in-germany-as-a-foreigner-de) und [Master-Zulassungschancen & Strategie auf Englisch](/de/blog/english-master-admission-chances-germany-gpa-fh-vs-uni-strategy-de).

## Die Deutsch-Falle: selbst im englischen Programm läuft das Leben auf Deutsch

Die Unterrichtssprache kann Englisch sein — aber **der Alltag ist es nicht.** Genau hier liegt die häufigste Klage internationaler Studierender:

- **Bürokratie auf Deutsch:** Anmeldung, Ausländerbehörde, Bank, Krankenkasse — meist Deutsch.
- **Werkstudentenjobs und Praktika:** Englisch-only-Techjobs sind außerhalb der Berliner Startup-Blase selten. Die meisten Werkstudenten-Stellen verlangen mindestens **B1–B2 Deutsch**.
- **Soziale Integration:** Ohne Deutsch ist ein lokaler Freundeskreis schwer; du bleibst in der "Expat-Blase".

Ein englisches Programm bringt dich durch die Klausuren, aber **nicht automatisch in Job und Alltag.** Lerne Deutsch parallel.

> Verwandt: [Ohne Deutsch studieren & Studentenjob-Realität](/de/blog/studying-in-germany-without-german-living-and-student-job-reality-de) und [Deutsch für den Job — die ehrliche Wahrheit](/de/blog/german-language-reality-for-jobs-in-germany-the-honest-truth-de).

## Staatlich (kostenlos) vs. privat (kostenpflichtig) auf Englisch

| | Staatliche Uni | Private Hochschule |
|---|---|---|
| Kosten | Gebührenfrei + Semesterbeitrag (~€150–350); in BW Nicht-EU ~€1.500/Sem. | ~€10.000–20.000/Jahr |
| Englischer Bachelor | Sehr selten | Hier zu Hause (CODE, Constructor, IU, SRH, GISMA) |
| Englischer Master | Reichlich | Vorhanden, aber unnötig (staatlich gratis) |
| Ruf/Akkreditierung | Etabliert, anerkannt | **Variabel — unbedingt prüfen** |
| Zulassung | Kompetitiver | Meist einfacher |

**Warnung zu Privathochschulen:** Manche sind exzellent und international (Constructor Bremen hat eine starke Forschungstradition), andere sind teuer mit fragwürdigem Ruf. Bevor du zahlst, prüfe **Akkreditierung (z. B. staatliche Anerkennung, Programmakkreditierung), Absolventen-Vermittlung und die Anerkennung des Abschlusses in deinem Land/der EU**.

## Studiengebühren & Kostenrealität

Klare Trennung:

- **Staatlicher Master:** Das Studium ist "gratis", aber du zahlst jedes Semester einen **Semesterbeitrag (~€150–350)** — meist inklusive Semesterticket.
- **Baden-Württemberg:** Nicht-EU-Studierende zahlen zusätzlich **~€1.500/Semester** Studiengebühr (landesspezifisch).
- **Private Hochschule:** **€10.000–20.000** Studiengebühren pro Jahr.

Hinzu kommen für das Visum das **Sperrkonto** und Lebenshaltungskosten; diese Beträge ändern sich jährlich — vor der Bewerbung an der offiziellen Quelle verifizieren. Wir nennen hier keine erfundenen Zahlen; prüfe den aktuellen Sperrkonto-/Lebenshaltungsbetrag stets in der aktuellen offiziellen Liste.

## Wenn dein Abschluss nicht direkt passt: Studienkolleg / anabin

Auch bei einem englischen Programm gelten **die Anerkennungsregeln unverändert.** Ein türkisches Abiturzeugnis reicht in Deutschland oft **nicht direkt** für die Uni-Zulassung; entweder über **Studienkolleg** (Vorbereitungsjahr + Feststellungsprüfung) oder **1+ Jahr Uni in der Türkei** für die Abitur-Gleichwertigkeit. Englisch als Unterrichtssprache überspringt diesen Schritt nicht.

> Lies zuerst: [Anabin & türkische Zeugnis-Anerkennung](/de/blog/what-are-anabin-h-h-h-how-is-your-turkish-diploma-de) und [Studienkolleg ist keine Sprachschule](/de/blog/studienkolleg-is-not-a-language-school-what-it-really-is-de).

## Job ohne Deutsch: möglich, aber eng

Englisch-only-Techjobs konzentrieren sich in Deutschland auf **Berlin/Startups und Big Tech** (SAP, Zalando, N26, Delivery Hero, Trade Republic). Außerhalb Berlins — besonders im Mittelstand und in der Automobilbranche — ist **Deutsch ein riesiger Vorteil.** Ehrlicher Rat: Du kannst den Job auf Englisch bekommen, aber **lerne trotzdem B1–B2 Deutsch** — das verdoppelt deine Karrieretüren.

> Details: [Als Ausländer in IT/Tech in Deutschland arbeiten: Blue Card & Gehalt](/de/blog/working-in-it-tech-in-germany-as-a-foreigner-blue-card-salary-de).

## Fazit & ehrlicher Rat

- Willst du einen **englischen Bachelor**, landest du wahrscheinlich auf einem **privaten, kostenpflichtigen** Weg — prüfe die Akkreditierung gründlich.
- **Effizienteste Route:** Bachelor günstig abschließen → in Deutschland einen **gebührenfreien englischen Master** machen.
- Auch wenn das Programm Englisch ist: **Deutsch aufzuschieben** schließt Praktika/Werkstudenten- und Nicht-Berlin-Jobtüren.
- Kläre die Abschlussanerkennung (anabin/Studienkolleg) von Anfang an; das englische Programm löscht diesen Schritt nicht.

> Weiter: [In IT arbeiten: Blue Card & Gehalt](/de/blog/working-in-it-tech-in-germany-as-a-foreigner-blue-card-salary-de) und [Was tun mit einem CS-Abschluss: Arbeitsmarkt & Gehalt](/de/blog/what-to-do-with-a-computer-science-degree-in-germany-job-market-salary-de).

*Die Gebühren-, Beitrags-, Schwellen- und Visa-Angaben in diesem Artikel gelten Anfang 2026 und werden jährlich aktualisiert; vor der Bewerbung an offiziellen Quellen verifizieren.*
MD;

        $enBody = <<<'MD'
"I don't speak German but I want to study computer science in Germany — I'll just find an English program." We hear this a lot. Partly true, partly a trap. This post gives you the honest map of English-taught CS/IT in Germany: free public vs. expensive private routes — and exactly where the "no German" dream cracks.

## The honest split: English bachelors are scarce, English masters are abundant

Get this distinction straight first, because most people mix it up:

- **English BACHELOR:** **Very scarce** at public universities. English-taught computer science bachelors are mostly at **private universities**: CODE University Berlin, Constructor University Bremen, IU International, SRH, GISMA. These are **paid** — roughly **€10,000–20,000 per year**.
- **English MASTER:** **Abundant** at public universities and usually **tuition-free** — you only pay a semester fee (~€150–350/semester). Exception: **Baden-Württemberg** charges non-EU students ~**€1,500/semester**.

That's why the realistic "no German" route is usually: **finish your bachelor abroad (or in English elsewhere) → do an English master in Germany.** We cover master admission strategy separately.

> More: [Studying CS/Informatik as a foreigner](/en/blog/studying-computer-science-informatik-in-germany-as-a-foreigner-en) and [English master admission chances & strategy](/en/blog/english-master-admission-chances-germany-gpa-fh-vs-uni-strategy-en).

## The German-language trap: even in an English program, life runs in German

The language of instruction can be English — but **daily life isn't.** This is the single most common complaint from international students:

- **Bureaucracy is in German:** Anmeldung (residence registration), Ausländerbehörde (foreigners' office), bank, health insurance — mostly German.
- **Werkstudent jobs and internships:** English-only tech jobs are rare outside the Berlin startup bubble. Most working-student listings want at least **B1–B2 German**.
- **Social integration:** Without German, building a local circle is hard; you stay in the "expat bubble".

An English program gets you through exams but **not automatically into a job and a life.** Learn German in parallel.

> Related: [Studying without German & student-job reality](/en/blog/studying-in-germany-without-german-living-and-student-job-reality-en) and [German for jobs — the honest truth](/en/blog/german-language-reality-for-jobs-in-germany-the-honest-truth-en).

## Public (free) vs. private (paid) English CS

| | Public university | Private university |
|---|---|---|
| Cost | Tuition-free + semester fee (~€150–350); BW non-EU ~€1,500/sem. | ~€10,000–20,000/year |
| English bachelor | Very scarce | This is where they live (CODE, Constructor, IU, SRH, GISMA) |
| English master | Abundant | Exists, but unnecessary (free at public) |
| Reputation/accreditation | Established, recognized | **Variable — verify carefully** |
| Admission | More competitive | Usually easier |

**Private-university caution:** Some are genuinely strong and international (Constructor Bremen has a solid research pedigree), others are expensive with a questionable reputation. Before you pay, research **accreditation (e.g. state recognition, programmatic accreditation), graduate placement, and whether the degree is recognized back home / in the EU**.

## Tuition & cost reality

Clear breakdown:

- **Public master:** Tuition is "free", but you pay a **semester fee (~€150–350)** every term — usually including the Semesterticket (transit pass).
- **Baden-Württemberg:** Non-EU students pay an additional **~€1,500/semester** tuition (state-specific).
- **Private university:** **€10,000–20,000** tuition per year.

On top of that, the visa requires a **blocked account (Sperrkonto)** plus living costs; these amounts change yearly — verify at the official source before applying. We don't invent numbers here; always confirm the current blocked-account/living-cost figure from the up-to-date official list.

## If your diploma isn't a direct fit: Studienkolleg / anabin

Even for an English program, **recognition rules don't change.** A Turkish high-school diploma often **doesn't directly qualify** you for German university; you either go through **Studienkolleg** (a preparatory year + Feststellungsprüfung) or **1+ year of university in Turkey** for Abitur equivalence. English instruction does not skip this step.

> Read first: [Anabin & Turkish diploma recognition](/en/blog/what-are-anabin-h-h-h-how-is-your-turkish-diploma-en) and [Studienkolleg is not a language school](/en/blog/studienkolleg-is-not-a-language-school-what-it-really-is-en).

## Employability without German: possible, but narrow

English-only tech jobs in Germany concentrate in **Berlin/startups and big tech** (SAP, Zalando, N26, Delivery Hero, Trade Republic). Outside Berlin — especially in the Mittelstand and automotive — **German is a massive advantage.** Honest advice: you can land the job in English, but **learn B1–B2 German anyway** — it doubles your career doors.

> Details: [Working in IT/tech in Germany as a foreigner: Blue Card & salary](/en/blog/working-in-it-tech-in-germany-as-a-foreigner-blue-card-salary-en).

## Bottom line & honest advice

- If you want an **English bachelor**, you're likely looking at a **private, paid** route — research accreditation thoroughly.
- **Most efficient route:** finish the bachelor cheaply → do a **tuition-free English master** in Germany.
- Even if the program is English, **postponing German** closes internship/Werkstudent and non-Berlin job doors.
- Sort out diploma recognition (anabin/Studienkolleg) from the start; the English program doesn't erase that step.

> Next: [Working in IT: Blue Card & salary](/en/blog/working-in-it-tech-in-germany-as-a-foreigner-blue-card-salary-en) and [What to do with a CS degree: job market & salary](/en/blog/what-to-do-with-a-computer-science-degree-in-germany-job-market-salary-en).

*The fees, contributions, thresholds and visa figures in this article are as of early 2026 and are updated yearly; verify at official sources before applying.*
MD;

        $variants = [
            'tr' => [
                'slug'  => 'english-taught-computer-science-it-degrees-in-germany-without-german',
                'title' => 'Almanya\'da İngilizce CS/IT Eğitimi: Almancasız Mümkün mü?',
                'excerpt' => 'İngilizce bilgisayar mühendisliği lisansı Almanya\'da nadir ve çoğunlukla pahalı özel üniversitelerde; İngilizce master ise devlette bol ve harçsız. Almancasız rotanın gerçeği ve tuzakları.',
                'meta_title' => 'Almanya\'da İngilizce CS/IT: Almancasız Okumak',
                'meta_description' => 'İngilizce CS lisansı nadir + özel (€10–20k/yıl); İngilizce master devlette harçsız. Almancasız rota, maliyet, denklik ve iş gerçeği.',
                'body' => $trBody,
            ],
            'de' => [
                'slug'  => 'english-taught-computer-science-it-degrees-in-germany-without-german-de',
                'title' => 'Englischsprachiges CS/IT-Studium in Deutschland ohne Deutsch',
                'excerpt' => 'Englische Informatik-Bachelor sind in Deutschland selten und meist an teuren Privathochschulen; englische Master sind staatlich reichlich und gebührenfrei. Die ehrliche Ohne-Deutsch-Route.',
                'meta_title' => 'CS/IT auf Englisch in Deutschland — ohne Deutsch',
                'meta_description' => 'Englische CS-Bachelor selten + privat (€10–20k/Jahr); englische Master staatlich gebührenfrei. Route, Kosten, Anerkennung & Job-Realität.',
                'body' => $deBody,
            ],
            'en' => [
                'slug'  => 'english-taught-computer-science-it-degrees-in-germany-without-german-en',
                'title' => 'English-Taught CS / IT Degrees in Germany Without German',
                'excerpt' => 'English CS bachelors are scarce in Germany and mostly at expensive private universities; English masters are abundant and tuition-free at public unis. The honest no-German route and its traps.',
                'meta_title' => 'English-Taught CS/IT in Germany Without German',
                'meta_description' => 'English CS bachelors scarce + private (€10–20k/yr); English masters tuition-free at public unis. The no-German route, costs, recognition & job reality.',
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
            'english-taught-computer-science-it-degrees-in-germany-without-german',
            'english-taught-computer-science-it-degrees-in-germany-without-german-de',
            'english-taught-computer-science-it-degrees-in-germany-without-german-en',
        ])->delete();
    }
};
