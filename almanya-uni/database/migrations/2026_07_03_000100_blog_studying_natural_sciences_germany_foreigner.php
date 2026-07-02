<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN): Almanya'da Doğa Bilimleri okumak — Fizik/Kimya/Biyoloji/Life Sciences (2026).
 * Doğrulandı: Bachelor genelde Almanca (C1), İngilizce master bol; kamu ~150-350€/dönem (BW non-EU ~1.500€);
 * tepe okullar LMU/TU München, Heidelberg (EMBL), Göttingen, KIT; başvuru uni-assist/NC + Studienkolleg; doktora maaşlı.
 * Yazar: Halil Yaprakli. Kategori: almanyada-egitim. FK-safe + slug-bazlı idempotent.
 */
return new class extends Migration
{
    public function up(): void
    {
        $groupId = 'e1a10000-1111-4b3d-9f60-ee01ff05bb01';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'almanyada-egitim')->value('id')
            ?? DB::table('categories')->where('slug', 'universities')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
Almanya, doğa bilimleri okumak isteyen uluslararası öğrenciler için Avrupa'nın en güçlü adreslerinden biri. **Fizik, kimya ve biyoloji** alanlarında hem köklü üniversiteler hem de dünya çapında araştırma enstitüleri (Max Planck, Helmholtz) var. Üstelik kamu üniversiteleri neredeyse **ücretsiz**. Bu yazıda bir yabancı öğrenci olarak Almanya'da doğa bilimleri eğitiminin nasıl işlediğini dürüstçe anlatıyorum.

## Fizik, Kimya, Biyoloji ve Life Sciences kapsamı

"Doğa bilimleri" (Naturwissenschaften) Almanya'da geniş bir şemsiye:

- **Physik (Fizik):** teorik, deneysel, astrofizik, katı hâl, kuantum. Almanya fizik geleneği çok güçlü.
- **Chemie (Kimya):** organik, inorganik, fiziksel kimya; sanayiyle (BASF, Evonik) yakın bağ.
- **Biologie (Biyoloji):** moleküler biyoloji, genetik, ekoloji, mikrobiyoloji.
- **Life Sciences / Biochemie:** biyokimya, biyoteknoloji, nörobilim — Heidelberg ve München'in en güçlü olduğu alanlar.

Bu alanlar lab ağırlıklıdır; teorik fizik ve biyoinformatik gibi dalların dışında zamanının büyük kısmını laboratuvarda ya da veri başında geçirirsin.

## Bachelor (Almanca) vs İngilizce master

Kritik bir ayrım var ve çoğu öğrenci burada yanılıyor:

- **Bachelor genelde Almanca yürür.** *2025/2026 itibarıyla, yaklaşık; doğrula* — İngilizce lisans (bachelor) doğa bilimlerinde **nadirdir**. Lisans için pratikte **Almanca C1** beklenir (TestDaF / DSH).
- **İngilizce master ise bol.** Özellikle **fizik, moleküler biyoloji, life sciences, kimya ve nörobilim** alanlarında İngilizce yüksek lisans (MSc) programları çoktur.

Bu, yol haritanı belirler: Almancan yoksa **master seviyesinde** İngilizce başlaman çok daha gerçekçi. Bunun detayını [Almancasız İngilizce doğa bilimi master programları](/tr/blog/english-taught-natural-science-masters-in-germany-without-german) yazısında ayrı ele alıyorum.

## Laboratuvar ve araştırma odaklı yapı

Alman doğa bilimleri eğitimi **araştırmaya** dönüktür. Beklemen gerekenler:

- Yoğun **Praktikum** (laboratuvar staj/uygulama modülleri) — notunun büyük kısmı buradan gelir.
- **Bachelorarbeit / Masterarbeit** (bitirme tezi): gerçek bir araştırma grubunda aylarca süren deneysel proje.
- Erken dönemde bir çalışma grubuna (**Arbeitsgruppe**) girip **HiWi** (öğrenci asistanı) olarak çalışma imkânı.

Bu yapı seni doğal olarak **doktoraya** hazırlar — ki Almanya'da doktora genelde maaşlı bir iştir. Önizlemesi aşağıda, tamamı [Almanya'da doktora ve araştırma kariyeri](/tr/blog/doing-a-phd-and-research-career-in-germany-as-a-foreigner) yazısında.

## Tepe okullar

Doğa bilimlerinde öne çıkan üniversiteler (alan güçlerine göre, kabaca):

| Üniversite | Öne çıkan alan | Not |
|---|---|---|
| **LMU & TU München** | Fizik, kimya, life sciences | Münih ekosistemi + Max Planck yakınlığı |
| **Heidelberg** | Life sciences, moleküler biyoloji | **EMBL** ve DKFZ ile iç içe |
| **Göttingen** | Fizik, kimya | Köklü bilim geleneği |
| **KIT (Karlsruhe)** | Fizik, kimya | Helmholtz + üniversite birleşik |
| **Bonn / Freiburg / Tübingen** | Fizik, biyoloji | Güçlü araştırma çıktısı |
| **RWTH Aachen / TU Dresden** | Kimya, uygulamalı bilim | Sanayi bağı güçlü |

*2025/2026 itibarıyla, yaklaşık; sıralamalar alt-alana göre değişir, kendi programını doğrula.* Mühendisliğe kayan bir profildeysen [Almanya'da yabancı olarak mühendislik okumak](/tr/blog/studying-engineering-in-germany-as-a-foreigner) yazısı da işine yarar.

## Başvuru: uni-assist / NC ve Studienkolleg

Başvuru süreci kabaca şöyle:

- **uni-assist:** birçok üniversite yabancı başvurularını buradan toplar (belge/diploma denkliği ön-kontrolü).
- **NC (Numerus Clausus):** biyoloji gibi popüler bölümlerde **kontenjan sınırı** olabilir; not ortalaması belirleyici. Fizik ve kimya çoğu yerde **NC-frei** (kontenjansız) olabilir.
- **Studienkolleg:** Türkiye lisesi + üniversite denkliği yetmezse, doğrudan lisansa başlamadan önce bir hazırlık yılı gerekebilir. Dikkat: bu bir dil kursu **değildir** — detay: [Studienkolleg bir dil okulu değildir](/tr/blog/studienkolleg-is-not-a-language-school-what-it-really-is).

Belgeler, dil sınavı (bachelor için C1), motivasyon ve başvuru dönemleri (kış dönemi başvuruları genelde yaz aylarında kapanır) kritik. Erken başla.

## Ücret, burs ve doktora yolu önizlemesi

- **Harç:** kamu üniversitelerinde **öğrenim ücreti yok**; sadece dönemlik katkı ~**150–350€** (semester ticket dâhil olabilir). İstisna: **Baden-Württemberg**, AB dışı öğrencilerden ~**1.500€/dönem** alır. *2025/2026 itibarıyla, yaklaşık; doğrula.*
- **Burs:** **DAAD** en bilinen kaynak; ayrıca Deutschlandstipendium ve vakıf bursları.
- **Doktora önizlemesi:** Almanya'da doktora çoğunlukla **maaşlı bir pozisyondur** (TV-L E13 kadrosu, tam/kısmi ~**€2.800–4.200 brüt/ay**, alana göre) ve **harç yoktur**. Bu, doğa bilimleri okumanın en güçlü kozlarından biri.

Master mi, iş arama vizesi mi ikilemindeysen [Almanya'da master vs iş-arama vizesi](/tr/blog/germany-masters-vs-job-seeker-visa-two-keys-career) yazısı kariyer planını netleştirir.

## Sonuç & dürüst tavsiye

Almanya'da doğa bilimleri okumak **maliyet açısından çok cazip** ve araştırma altyapısı dünya standardında. Dürüst tavsiyem:

1. **Almancan yoksa master hedefle.** İngilizce lisans nadir; İngilizce MSc bol.
2. **Lab ve araştırmayı sevmiyorsan** iki kez düşün — bu alanlar araştırma odaklı.
3. **Erken planla:** Studienkolleg gerekliliği, NC ve başvuru dönemleri seni yıl kaybettirebilir.
4. **Doktora perspektifini baştan düşün:** Almanya'da maaşlı PhD, doğa bilimleri mezunu için gerçek bir kariyer yolu.

Kararını verirken sadece prestije değil, **hangi alt-alanın seni lab veya veri başında mutlu edeceğine** bak.

*Bu yazı 2026 başı itibarıyla hazırlanmıştır. Öğrenim ücretleri, başvuru koşulları, NC kontenjanları ve maaş rakamları eyalete, üniversiteye ve yıla göre değişir. Başvurmadan önce ilgili üniversitenin ve resmî kurumların güncel bilgilerini mutlaka doğrula.*
MD;

        $deBody = <<<'MD'
Deutschland ist eine der stärksten Adressen Europas, wenn du **Naturwissenschaften** studieren willst. In **Physik, Chemie und Biologie** findest du sowohl traditionsreiche Universitäten als auch weltweit führende Forschungsinstitute (Max Planck, Helmholtz). Und die staatlichen Universitäten sind praktisch **kostenlos**. In diesem Artikel erkläre ich dir ehrlich, wie ein Naturwissenschaftsstudium in Deutschland als internationale:r Studierende:r funktioniert.

## Was Physik, Chemie, Biologie und Life Sciences umfassen

"Naturwissenschaften" sind in Deutschland ein breites Dach:

- **Physik:** theoretisch, experimentell, Astrophysik, Festkörper, Quanten. Deutschland hat eine sehr starke Physiktradition.
- **Chemie:** organisch, anorganisch, physikalische Chemie; enge Verbindung zur Industrie (BASF, Evonik).
- **Biologie:** Molekularbiologie, Genetik, Ökologie, Mikrobiologie.
- **Life Sciences / Biochemie:** Biochemie, Biotechnologie, Neurowissenschaften — hier sind Heidelberg und München besonders stark.

Diese Fächer sind laborlastig; außer in Bereichen wie theoretischer Physik oder Bioinformatik verbringst du viel Zeit im Labor oder mit Daten.

## Bachelor (auf Deutsch) vs. englischsprachiger Master

Es gibt eine entscheidende Unterscheidung, bei der sich viele täuschen:

- **Der Bachelor läuft meist auf Deutsch.** *Stand 2025/2026, ungefähr; bitte prüfen* — englischsprachige Bachelorprogramme sind in den Naturwissenschaften **selten**. Für den Bachelor wird praktisch **Deutsch C1** erwartet (TestDaF / DSH).
- **Englischsprachige Master gibt es dagegen reichlich.** Besonders in **Physik, Molekularbiologie, Life Sciences, Chemie und Neurowissenschaften** gibt es viele englischsprachige Masterprogramme (MSc).

Das bestimmt deinen Fahrplan: Ohne Deutsch ist ein Einstieg auf **Masterebene** auf Englisch viel realistischer. Die Details behandle ich im Artikel [Englischsprachige Naturwissenschafts-Master in Deutschland ohne Deutsch](/de/blog/english-taught-natural-science-masters-in-germany-without-german-de).

## Labor- und forschungsorientierter Aufbau

Das naturwissenschaftliche Studium in Deutschland ist **forschungsorientiert**. Was dich erwartet:

- Intensive **Praktika** (Labormodule) — ein großer Teil deiner Note kommt daher.
- **Bachelorarbeit / Masterarbeit**: ein monatelanges experimentelles Projekt in einer echten Forschungsgruppe.
- Früh die Möglichkeit, in einer **Arbeitsgruppe** als **HiWi** (studentische Hilfskraft) zu arbeiten.

Diese Struktur bereitet dich natürlich auf eine **Promotion** vor — die in Deutschland meist ein bezahlter Job ist. Eine Vorschau unten, alles Weitere im Artikel [Promotion und Forschungskarriere in Deutschland](/de/blog/doing-a-phd-and-research-career-in-germany-as-a-foreigner-de).

## Top-Universitäten

Herausragende Universitäten in den Naturwissenschaften (grob nach Stärken):

| Universität | Stärke | Hinweis |
|---|---|---|
| **LMU & TU München** | Physik, Chemie, Life Sciences | Münchner Ökosystem + Nähe zu Max Planck |
| **Heidelberg** | Life Sciences, Molekularbiologie | eng mit **EMBL** und DKFZ verzahnt |
| **Göttingen** | Physik, Chemie | traditionsreiche Wissenschaftskultur |
| **KIT (Karlsruhe)** | Physik, Chemie | Helmholtz + Universität vereint |
| **Bonn / Freiburg / Tübingen** | Physik, Biologie | starke Forschungsleistung |
| **RWTH Aachen / TU Dresden** | Chemie, angewandte Wissenschaft | starke Industrienähe |

*Stand 2025/2026, ungefähr; Rankings variieren je nach Teilgebiet, prüfe dein konkretes Programm.* Wenn dein Profil eher zur Ingenieurwissenschaft tendiert, hilft dir auch [Ingenieurwesen in Deutschland als Ausländer:in studieren](/de/blog/studying-engineering-in-germany-as-a-foreigner-de).

## Bewerbung: uni-assist / NC und Studienkolleg

Der Bewerbungsprozess grob:

- **uni-assist:** viele Universitäten bündeln hier internationale Bewerbungen (Vorprüfung von Zeugnissen/Abschlüssen).
- **NC (Numerus Clausus):** in beliebten Fächern wie Biologie kann es eine **Zulassungsbeschränkung** geben; der Notendurchschnitt entscheidet. Physik und Chemie sind vielerorts **NC-frei**.
- **Studienkolleg:** Reicht dein Schulabschluss + Hochschulzugang nicht aus, brauchst du ggf. ein Vorbereitungsjahr, bevor du direkt ins Bachelorstudium startest. Achtung: Das ist **kein** Sprachkurs — Details: [Das Studienkolleg ist keine Sprachschule](/de/blog/studienkolleg-is-not-a-language-school-what-it-really-is-de).

Unterlagen, Sprachnachweis (C1 für den Bachelor), Motivation und Fristen (Bewerbungen fürs Wintersemester schließen oft im Sommer) sind entscheidend. Fang früh an.

## Kosten, Stipendien und Vorschau auf den Promotionsweg

- **Gebühren:** an staatlichen Universitäten gibt es **keine Studiengebühren**; nur ein Semesterbeitrag von ~**150–350€** (ggf. inkl. Semesterticket). Ausnahme: **Baden-Württemberg** verlangt von Nicht-EU-Studierenden ~**1.500€/Semester**. *Stand 2025/2026, ungefähr; bitte prüfen.*
- **Stipendien:** **DAAD** ist die bekannteste Quelle; außerdem das Deutschlandstipendium und Stiftungsstipendien.
- **Promotions-Vorschau:** Eine Promotion in Deutschland ist meist eine **bezahlte Stelle** (TV-L E13, volle/anteilige Stelle ~**2.800–4.200€ brutto/Monat**, je nach Fach) und **ohne Gebühren**. Das ist einer der größten Trümpfe eines Naturwissenschaftsstudiums.

Wenn du zwischen Master und Jobsuchevisum schwankst, klärt [Master vs. Jobsuchevisum in Deutschland](/de/blog/germany-masters-vs-job-seeker-visa-two-keys-career-de) deine Karriereplanung.

## Fazit & ehrlicher Rat

Naturwissenschaften in Deutschland zu studieren ist **kostenmäßig sehr attraktiv** und die Forschungsinfrastruktur ist auf Weltniveau. Mein ehrlicher Rat:

1. **Ohne Deutsch: ziele auf den Master.** Englische Bachelor sind selten, englische MSc reichlich.
2. **Wenn du Labor und Forschung nicht magst**, überlege es dir zweimal — diese Fächer sind forschungsorientiert.
3. **Plane früh:** Studienkolleg-Pflicht, NC und Fristen können dich ein Jahr kosten.
4. **Denk die Promotion von Anfang an mit:** eine bezahlte Promotion in Deutschland ist ein echter Karriereweg für Naturwissenschaftler:innen.

Schau bei deiner Entscheidung nicht nur auf Prestige, sondern darauf, **welches Teilgebiet dich im Labor oder mit Daten glücklich macht**.

*Dieser Artikel wurde Anfang 2026 erstellt. Studiengebühren, Bewerbungsbedingungen, NC-Grenzen und Gehälter variieren je nach Bundesland, Universität und Jahr. Prüfe vor der Bewerbung unbedingt die aktuellen Angaben der jeweiligen Universität und offizieller Stellen.*
MD;

        $enBody = <<<'MD'
Germany is one of Europe's strongest destinations if you want to study **natural sciences**. In **physics, chemistry and biology** you'll find both long-established universities and world-leading research institutes (Max Planck, Helmholtz). And public universities are practically **free**. In this article I explain honestly how studying natural sciences in Germany works as an international student.

## What physics, chemistry, biology and life sciences cover

"Natural sciences" (Naturwissenschaften) is a broad umbrella in Germany:

- **Physics:** theoretical, experimental, astrophysics, solid-state, quantum. Germany has a very strong physics tradition.
- **Chemistry:** organic, inorganic, physical chemistry; closely tied to industry (BASF, Evonik).
- **Biology:** molecular biology, genetics, ecology, microbiology.
- **Life sciences / biochemistry:** biochemistry, biotechnology, neuroscience — areas where Heidelberg and Munich are especially strong.

These fields are lab-heavy; apart from areas like theoretical physics or bioinformatics, you'll spend a lot of time in the lab or working with data.

## Bachelor's (in German) vs English-taught master's

There's a crucial distinction where many students get it wrong:

- **The bachelor's is mostly taught in German.** *As of 2025/2026, approximate; verify* — English-taught bachelor's programs in natural sciences are **rare**. For a bachelor's you're effectively expected to have **German C1** (TestDaF / DSH).
- **English-taught master's, on the other hand, are plentiful.** Especially in **physics, molecular biology, life sciences, chemistry and neuroscience**, there are many English-taught master's (MSc) programs.

This shapes your roadmap: without German, starting at **master's level** in English is far more realistic. I cover the details in [English-taught natural science master's in Germany without German](/en/blog/english-taught-natural-science-masters-in-germany-without-german-en).

## Lab- and research-oriented structure

Natural science education in Germany is **research-oriented**. What to expect:

- Intensive **Praktika** (lab modules) — a large part of your grade comes from these.
- **Bachelor's / master's thesis**: a months-long experimental project in a real research group.
- Early opportunity to join a research group (**Arbeitsgruppe**) and work as a **HiWi** (student assistant).

This structure naturally prepares you for a **PhD** — which in Germany is usually a paid job. A preview below; the full story is in [Doing a PhD and research career in Germany](/en/blog/doing-a-phd-and-research-career-in-germany-as-a-foreigner-en).

## Top universities

Standout universities in natural sciences (roughly by their strengths):

| University | Strength | Note |
|---|---|---|
| **LMU & TU Munich** | Physics, chemistry, life sciences | Munich ecosystem + Max Planck proximity |
| **Heidelberg** | Life sciences, molecular biology | closely linked with **EMBL** and DKFZ |
| **Göttingen** | Physics, chemistry | deep-rooted science culture |
| **KIT (Karlsruhe)** | Physics, chemistry | Helmholtz + university combined |
| **Bonn / Freiburg / Tübingen** | Physics, biology | strong research output |
| **RWTH Aachen / TU Dresden** | Chemistry, applied science | strong industry ties |

*As of 2025/2026, approximate; rankings vary by sub-field, verify your specific program.* If your profile leans toward engineering, [Studying engineering in Germany as a foreigner](/en/blog/studying-engineering-in-germany-as-a-foreigner-en) is also useful.

## Applying: uni-assist / NC and Studienkolleg

The application process, roughly:

- **uni-assist:** many universities collect international applications here (pre-checking certificates/degrees).
- **NC (Numerus Clausus):** popular subjects like biology may have an **admission cap**; your grade average is decisive. Physics and chemistry are **NC-free** in many places.
- **Studienkolleg:** if your school leaving certificate + university access isn't sufficient, you may need a preparatory year before starting a bachelor's directly. Note: this is **not** a language course — details: [Studienkolleg is not a language school](/en/blog/studienkolleg-is-not-a-language-school-what-it-really-is-en).

Documents, language proof (C1 for the bachelor's), motivation and deadlines (winter-semester applications often close in summer) are critical. Start early.

## Fees, scholarships and a preview of the PhD path

- **Fees:** public universities charge **no tuition**; only a semester contribution of ~**€150–350** (may include a semester ticket). Exception: **Baden-Württemberg** charges non-EU students ~**€1,500/semester**. *As of 2025/2026, approximate; verify.*
- **Scholarships:** **DAAD** is the best-known source; also the Deutschlandstipendium and foundation scholarships.
- **PhD preview:** a PhD in Germany is usually a **paid position** (TV-L E13, full/partial position ~**€2,800–4,200 gross/month**, depending on the field) and **fee-free**. This is one of the biggest advantages of studying natural sciences.

If you're torn between a master's and a job-seeker visa, [Germany master's vs job-seeker visa](/en/blog/germany-masters-vs-job-seeker-visa-two-keys-career-en) will clarify your career plan.

## Conclusion & honest advice

Studying natural sciences in Germany is **very attractive cost-wise** and the research infrastructure is world-class. My honest advice:

1. **Without German, target the master's.** English bachelor's are rare; English MSc are plentiful.
2. **If you don't enjoy the lab and research**, think twice — these fields are research-oriented.
3. **Plan early:** Studienkolleg requirements, NC and deadlines can cost you a year.
4. **Factor in the PhD perspective from the start:** a paid PhD in Germany is a real career path for natural science graduates.

When you decide, look not just at prestige but at **which sub-field will make you happy in the lab or with data**.

*This article was prepared in early 2026. Tuition fees, application conditions, NC caps and salary figures vary by state, university and year. Always verify the current information from the relevant university and official bodies before applying.*
MD;

        $variants = [
            'tr' => ['slug'=>'studying-natural-sciences-physics-chemistry-biology-in-germany',    'title'=>'Almanya\'da Doğa Bilimleri Okumak: Fizik, Kimya, Biyoloji (2026)', 'excerpt'=>'Almanya\'da fizik, kimya, biyoloji ve life sciences okumak: Almanca bachelor vs İngilizce master, lab odaklı yapı, tepe okullar, uni-assist/NC ve Studienkolleg, ücret & burs ve maaşlı doktora yolu.', 'meta_title'=>'Almanya\'da Doğa Bilimleri Okumak: Fizik/Kimya/Biyoloji (2026)', 'meta_description'=>'Almanya\'da doğa bilimleri (fizik/kimya/biyoloji) okumak: Almanca bachelor vs İngilizce master, tepe okullar, başvuru, ücret ve maaşlı doktora yolu.', 'body'=>$trBody],
            'de' => ['slug'=>'studying-natural-sciences-physics-chemistry-biology-in-germany-de', 'title'=>'Naturwissenschaften in Deutschland studieren: Physik, Chemie, Biologie (2026)',        'excerpt'=>'Physik, Chemie, Biologie und Life Sciences in Deutschland studieren: deutscher Bachelor vs. englischer Master, laborlastiger Aufbau, Top-Unis, uni-assist/NC und Studienkolleg, Kosten & Stipendien und der bezahlte Promotionsweg.',   'meta_title'=>'Naturwissenschaften in Deutschland studieren (2026)',  'meta_description'=>'Naturwissenschaften (Physik/Chemie/Biologie) in Deutschland studieren: Bachelor vs. Master, Top-Unis, Bewerbung, Kosten und bezahlte Promotion.',   'body'=>$deBody],
            'en' => ['slug'=>'studying-natural-sciences-physics-chemistry-biology-in-germany-en', 'title'=>'Studying Natural Sciences in Germany: Physics, Chemistry, Biology (2026)',        'excerpt'=>'Studying physics, chemistry, biology and life sciences in Germany: German bachelor vs English master, lab-oriented structure, top universities, uni-assist/NC and Studienkolleg, fees & scholarships and the paid PhD path.',   'meta_title'=>'Studying Natural Sciences in Germany: Physics/Chemistry/Biology (2026)',  'meta_description'=>'Studying natural sciences (physics/chemistry/biology) in Germany: bachelor vs master, top universities, applying, fees and the paid PhD path.',   'body'=>$enBody],
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
            'studying-natural-sciences-physics-chemistry-biology-in-germany',
            'studying-natural-sciences-physics-chemistry-biology-in-germany-de',
            'studying-natural-sciences-physics-chemistry-biology-in-germany-en',
        ])->delete();
    }
};
