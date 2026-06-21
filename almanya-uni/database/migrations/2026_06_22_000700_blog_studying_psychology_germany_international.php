<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN): Almanya'da yabancı olarak psikoloji (Bachelor) okumak (2026).
 *
 * Doğrulandı (genel kabul + üniversite şartları, 2026):
 *  - Psikoloji Almanya'nın EN rekabetçi bölümlerinden; çoğu kamu üniversitesinde NC ≈ 1,0 (tıpa yakın).
 *  - Örtlich zulassungsbeschränkt (yerel NC) → üniversite-bazlı başvuru; AB-dışı uni-assist üzerinden.
 *    Tıbbın aksine hochschulstart MERKEZÎ DEĞİL.
 *  - Dil: bachelor'ların büyük çoğunluğu Almanca → C1 (DSH-2 / TestDaF 4). İngilizce psikoloji nadir,
 *    çoğunlukla ÖZEL üniversitelerde (Touro Berlin, Constructor University) veya Master düzeyinde, ücretli.
 *  - İstatistik-ağırlıklı: çok metot/istatistik, sadece "duygulardan konuşmak" değil.
 *  - Polyvalenter Bachelor (WS 2020/21'den): psikoterapist olmak için bachelor'da klinik profil şart.
 *  - Master darboğazı: klinik/psikoterapi yüksek lisans kontenjanı dar ve çok rekabetçi (sık sık ~1,0);
 *    tek başına bachelor sınırlı değer → fiilen "Master gerektiren" alan. Bazı özel üniler NC-siz ama ücretli.
 *
 * Yazar: Halil Yaprakli. Kategori: almanyada-egitim. FK-safe + slug-bazlı idempotent.
 * İç-link: anabin + studienkolleg + tıp (her üçü de tr/de/en mevcut) → her dilde locale-doğru.
 */
return new class extends Migration
{
    public function up(): void
    {
        $groupId = '7c1e9a20-5b3d-4e8a-9f12-1a2b3c4d5e76';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'almanyada-egitim')->value('id')
            ?? DB::table('categories')->where('slug', 'universities')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
"Almanya'da psikoloji okuyabilir miyim?" — yabancı öğrencilerin gözünde cazip ama gerçeği çok az bilinen bir hedef. Kısa cevap: **programlar mükemmel, ama giriş acımasızca rekabetçi.** Hayal kırıklığı yaşamamak için işin gerçeklerini net koyalım.

## 1. NC gerçeği: neredeyse 1,0
Psikoloji Almanya'nın **en rekabetçi bölümlerinden biri.** Çoğu kamu üniversitesinde NC **≈ 1,0** — yani neredeyse düz pekiyi (Abitur tam not). Bu, **tıbba çok yakın** bir sertlik. Karşılaştırma için: hukuk psikolojiye göre çok daha kolay girilir. Yani not ortalaman zayıfsa, kamu üniversitesinde psikoloji bachelor'ı gerçekçi bir hedef olmayabilir.

## 2. Başvuru yolu: yerel NC, üniversite-bazlı
Tıbbın aksine psikoloji **örtlich zulassungsbeschränkt** — yani **yerel NC**. Kontenjan merkezî dağıtılmaz; **her üniversiteye ayrı ayrı** başvurursun. AB-dışı öğrenciler (ör. Türkiye) bunu **uni-assist** üzerinden yapar. **Hochschulstart üzerinden merkezî başvuru YOKTUR** — bu çok yaygın bir yanılgı. (Diploma denkliği için [Anabin & denklik](/tr/blog/what-are-anabin-h-h-h-how-is-your-turkish-diploma).)

## 3. Dil: Almanca C1, İngilizce nadir
Bachelor programlarının **büyük çoğunluğu Almanca**. Asgari **C1 (DSH-2 / TestDaF 4).** Bazı üniler B2 ile başvuruya izin verse de psikoloji **yoğun okuma ve dil** üzerine kurulu; gerçekçi olan **C1 ve üstü.** İngilizce psikoloji **nadir** ve çoğunlukla **özel üniversitelerde** (ör. Touro Berlin, Constructor University) ya da **Master düzeyinde** — ve genelde **ücretli.**

> **Gerçek uyarı:** Psikoloji "duygulardan konuşma" değildir. Almanca akademik metin okuma, sözlü sınavlar ve sunumlar dil hâkimiyeti ister. Pas tutmuş bir C1'in varsa, başlamadan tazele.

## 4. Sürpriz: istatistik-ağırlıklı bir bölüm
Birçok yabancı öğrencinin en büyük şaşkınlığı: psikoloji bachelor'ı **bol istatistik ve araştırma yöntemi** içerir. İlk yıllar metot, deney tasarımı ve veri analiziyle geçer. Matematik/istatistikten hoşlanmıyorsan, bu seni zorlar.

## 5. Polyvalenter Bachelor + Master darboğazı
**WS 2020/21'den** itibaren, ileride **psikoterapist** olmak istiyorsan bachelor'ında bir **klinik profil (klinischer Schwerpunkt)** seçmen şart — buna "polyvalenter Bachelor" denir. **Ama asıl darboğaz Master'da:** klinik/psikoterapi yüksek lisans kontenjanları **dar ve çok rekabetçi** (sık sık **~1,0** gerekir). Tek başına bir psikoloji bachelor'ının kariyer değeri sınırlıdır — psikoloji fiilen **"Master gerektiren" bir alandır.** Bazı **özel üniversiteler NC-siz** ama harç alır.

## 6. Karşılaştırma: psikoloji vs tıp vs hukuk
| Bölüm | NC | Başvuru yolu | Dil |
|---|---|---|---|
| Psikoloji | ≈ 1,0 | üniversite-bazlı (yerel) | C1 |
| Tıp | ~1,0–1,2 | hochschulstart (merkezî) | C1 / DSH-3 |
| Hukuk | ~1,5–3,0 | üniversite-bazlı (yerel) | C1 |

## Özet ve dürüst tavsiye
Almanya'da psikoloji **harika programlar sunar ama girişi acımasızdır**: NC ≈ 1,0, yerel başvuru, Almanca C1, istatistik-ağırlığı ve dar bir Master darboğazı. Notların çok güçlüyse hedefle — değilse, **özel üniversite (ücretli, NC-siz)** ya da yakın alanlar (Kognitionswissenschaft, sosyal hizmet) bir plan B olabilir. Karar vermeden önce başvuracağın üniversitenin International Office'inden NC ve dil şartını **mutlaka teyit et.**

İlgili: [Studienkolleg dil okulu değil](/tr/blog/studienkolleg-is-not-a-language-school-what-it-really-is) · [yabancı olarak tıp okumak](/tr/blog/study-medicine-in-germany-as-a-foreigner-nc-language-testas).

---
*2026 itibarıyla geçerli genel duruma dayanır; NC, dil ve kontenjan şartları üniversiteye göre değişir — başvurudan önce ilgili üniversitenin International Office'inden teyit et.*
MD;

        $deBody = <<<'MD'
„Kann ich in Deutschland Psychologie studieren?" — für internationale Studierende ein verlockendes, aber kaum richtig verstandenes Ziel. Kurz: **die Programme sind exzellent, aber der Zugang ist brutal kompetitiv.** Damit es keine Enttäuschung gibt, hier die Fakten.

## 1. Die NC-Realität: fast 1,0
Psychologie ist eines der **kompetitivsten Fächer Deutschlands.** An den meisten staatlichen Unis liegt der NC bei **≈ 1,0** — also nahezu ein perfekter Abiturschnitt. Das ist **ganz nah an der Medizin.** Zum Vergleich: Jura ist deutlich leichter zugänglich. Mit einem schwachen Notenschnitt ist ein Psychologie-Bachelor an einer staatlichen Uni oft kein realistisches Ziel.

## 2. Bewerbungsweg: lokaler NC, pro Uni
Anders als Medizin ist Psychologie **örtlich zulassungsbeschränkt** — also **lokaler NC**. Die Plätze werden nicht zentral vergeben; du bewirbst dich **pro Universität einzeln**. Nicht-EU-Studierende (z. B. Türkei) tun dies über **uni-assist**. Eine zentrale Bewerbung über **Hochschulstart gibt es NICHT** — ein häufiges Missverständnis. (Zur Anerkennung: [Was ist Anabin](/de/blog/what-are-anabin-h-h-h-how-is-your-turkish-diploma-de).)

## 3. Sprache: Deutsch C1, Englisch selten
Die große Mehrheit der Bachelorprogramme ist **auf Deutsch**. Minimum **C1 (DSH-2 / TestDaF 4).** Manche Unis lassen eine Bewerbung mit B2 zu, doch Psychologie beruht auf **viel Lesen und Sprache**; realistisch ist **C1 und mehr.** Englischsprachige Psychologie ist **selten** und meist an **privaten Unis** (z. B. Touro Berlin, Constructor University) oder auf **Masterniveau** — und in der Regel **gebührenpflichtig.**

> **Realitäts-Check:** Psychologie ist nicht „über Gefühle reden". Akademische Texte auf Deutsch, mündliche Prüfungen und Präsentationen verlangen Sprachbeherrschung. Ein eingerostetes C1 vor dem Start auffrischen.

## 4. Überraschung: ein statistiklastiges Fach
Die größte Überraschung für viele internationale Studierende: Der Psychologie-Bachelor enthält **viel Statistik und Forschungsmethoden.** Die ersten Jahre drehen sich um Methodik, Versuchsdesign und Datenanalyse. Wer Mathe/Statistik nicht mag, hat es schwer.

## 5. Polyvalenter Bachelor + Master-Engpass
**Seit WS 2020/21** musst du, wenn du später **Psychotherapeut** werden willst, im Bachelor einen **klinischen Schwerpunkt** wählen — den „polyvalenten Bachelor". **Der eigentliche Engpass liegt aber im Master:** klinische/psychotherapeutische Masterplätze sind **knapp und sehr kompetitiv** (oft braucht es **~1,0**). Ein Psychologie-Bachelor allein hat begrenzten Karrierewert — Psychologie ist faktisch ein **„Master-Pflicht"-Fach.** Einige **private Unis sind NC-frei**, verlangen aber Gebühren.

## 6. Vergleich: Psychologie vs Medizin vs Jura
| Fach | NC | Bewerbungsweg | Sprache |
|---|---|---|---|
| Psychologie | ≈ 1,0 | pro Uni (lokal) | C1 |
| Medizin | ~1,0–1,2 | Hochschulstart (zentral) | C1 / DSH-3 |
| Jura | ~1,5–3,0 | pro Uni (lokal) | C1 |

## Fazit & ehrlicher Rat
Psychologie in Deutschland bietet **exzellente Programme, aber der Zugang ist brutal**: NC ≈ 1,0, lokale Bewerbung, Deutsch C1, viel Statistik und ein enger Master-Engpass. Mit starken Noten geh dafür — sonst können eine **private Uni (gebührenpflichtig, NC-frei)** oder verwandte Fächer (Kognitionswissenschaft, Soziale Arbeit) ein Plan B sein. Vor der Entscheidung NC und Sprachanforderung beim International Office der Ziel-Uni **unbedingt bestätigen.**

Verwandt: [Studienkolleg ist keine Sprachschule](/de/blog/studienkolleg-is-not-a-language-school-what-it-really-is-de) · [als Ausländer Medizin studieren](/de/blog/study-medicine-in-germany-as-a-foreigner-nc-language-testas-de).

---
*Stand 2026; NC, Sprache und Quote variieren je nach Uni — vor der Bewerbung beim International Office der jeweiligen Uni bestätigen.*
MD;

        $enBody = <<<'MD'
"Can I study psychology in Germany?" — an appealing goal for international students, yet one whose reality is poorly understood. Short answer: **the programmes are excellent, but admission is brutally competitive.** To avoid disappointment, here are the facts.

## 1. The NC reality: almost 1.0
Psychology is one of the **most competitive subjects in Germany.** At most public universities the NC is **≈ 1.0** — essentially a straight-A Abitur average. That is **very close to medicine.** For comparison, law is far easier to get into. So if your grade average is weak, a psychology bachelor at a public university may not be a realistic target.

## 2. Application route: local NC, per university
Unlike medicine, psychology is **örtlich zulassungsbeschränkt** — a **local NC**. Seats are not allocated centrally; you apply **per university, individually**. Non-EU students (e.g. Turkey) do this via **uni-assist**. There is **NO central application via Hochschulstart** — a very common misconception. (For recognition: [Anabin & equivalence](/en/blog/what-are-anabin-h-h-h-how-is-your-turkish-diploma-en).)

## 3. Language: German C1, English rare
The vast majority of bachelor programmes are **in German**. Minimum **C1 (DSH-2 / TestDaF 4).** Some universities allow you to apply at B2, but psychology hinges on **heavy reading and language**; realistically you need **C1 and above.** English-taught psychology is **rare** and mostly at **private universities** (e.g. Touro Berlin, Constructor University) or at **Master level** — and usually **fee-paying.**

> **Reality check:** Psychology is not "talking about feelings". Academic reading in German, oral exams and presentations demand command of the language. Refresh a rusty C1 before you start.

## 4. Surprise: it's a statistics-heavy subject
The biggest surprise for many international students: the psychology bachelor involves **a lot of statistics and research methods.** The early years revolve around methodology, experimental design and data analysis. If you dislike maths/statistics, you'll struggle.

## 5. Polyvalenter Bachelor + the Master's bottleneck
**Since WS 2020/21**, if you want to become a **psychotherapist** later, you must pick a **clinical profile (klinischer Schwerpunkt)** in your bachelor — the "polyvalenter Bachelor". **But the real bottleneck is the Master:** clinical/psychotherapy Master places are **limited and very competitive** (often needing **~1.0**). A psychology bachelor alone has limited career value — psychology is effectively a **"Master-required" field.** Some **private universities are NC-free** but charge tuition.

## 6. Comparison: psychology vs medicine vs law
| Subject | NC | Application route | Language |
|---|---|---|---|
| Psychology | ≈ 1.0 | per university (local) | C1 |
| Medicine | ~1.0–1.2 | Hochschulstart (central) | C1 / DSH-3 |
| Law | ~1.5–3.0 | per university (local) | C1 |

## Bottom line & honest advice
Psychology in Germany offers **excellent programmes, but admission is brutal**: NC ≈ 1.0, local application, German C1, a statistics-heavy curriculum and a narrow Master's bottleneck. With strong grades, go for it — otherwise a **private university (fee-paying, NC-free)** or related fields (cognitive science, social work) can be a plan B. Before deciding, **always confirm** the NC and language requirement with the target university's International Office.

Related: [Studienkolleg is not a language school](/en/blog/studienkolleg-is-not-a-language-school-what-it-really-is-en) · [studying medicine as a foreigner](/en/blog/study-medicine-in-germany-as-a-foreigner-nc-language-testas-en).

---
*Based on the general situation as of 2026; NC, language and quota requirements vary by university — confirm with the relevant university's International Office before applying.*
MD;

        $variants = [
            'tr' => [
                'slug'  => 'studying-psychology-in-germany-international-students-nc-language',
                'title' => 'Almanya\'da Yabancı Olarak Psikoloji Okumak (2026): NC, Dil ve Master Darboğazı',
                'excerpt' => 'Almanya\'da yabancı olarak psikoloji bachelor\'ı: programlar harika ama giriş acımasız — NC ≈ 1,0 (tıba yakın), yerel NC + üniversite-bazlı uni-assist başvuru (hochschulstart değil), Almanca C1 (İngilizce nadir/özel), istatistik-ağırlığı, polyvalenter Bachelor ve dar Master darboğazı — dürüst rehber.',
                'meta_title' => 'Almanya\'da Yabancı Psikoloji Okumak — NC, Dil (2026)',
                'meta_description' => 'Almanya\'da psikoloji: NC ≈ 1,0, yerel NC + üniversite-bazlı başvuru (hochschulstart değil), Almanca C1, istatistik-ağırlığı, polyvalenter Bachelor ve Master darboğazı — yabancılar için dürüst 2026 rehberi.',
                'body' => $trBody,
            ],
            'de' => [
                'slug'  => 'studying-psychology-in-germany-international-students-nc-language-de',
                'title' => 'Als Ausländer Psychologie in Deutschland studieren (2026): NC, Sprache & der Master-Engpass',
                'excerpt' => 'Psychologie-Bachelor als Ausländer in Deutschland: exzellente Programme, aber brutaler Zugang — NC ≈ 1,0 (nahe Medizin), lokaler NC + Bewerbung pro Uni via uni-assist (nicht Hochschulstart), Deutsch C1 (Englisch selten/privat), statistiklastig, polyvalenter Bachelor und enger Master-Engpass — ehrlicher Leitfaden.',
                'meta_title' => 'Psychologie in Deutschland als Ausländer — NC, Sprache (2026)',
                'meta_description' => 'Psychologie in Deutschland: NC ≈ 1,0, lokaler NC + Bewerbung pro Uni (nicht Hochschulstart), Deutsch C1, statistiklastig, polyvalenter Bachelor & Master-Engpass — ehrlicher Leitfaden für Ausländer 2026.',
                'body' => $deBody,
            ],
            'en' => [
                'slug'  => 'studying-psychology-in-germany-international-students-nc-language-en',
                'title' => 'Studying Psychology in Germany as a Foreigner (2026): NC, Language & the Master\'s Bottleneck',
                'excerpt' => 'A psychology bachelor in Germany as a foreigner: excellent programmes but brutal admission — NC ≈ 1.0 (close to medicine), local NC + per-university application via uni-assist (not Hochschulstart), German C1 (English rare/private), statistics-heavy, the polyvalenter Bachelor and a narrow Master\'s bottleneck — an honest guide.',
                'meta_title' => 'Study Psychology in Germany as a Foreigner — NC, Language (2026)',
                'meta_description' => 'Psychology in Germany: NC ≈ 1.0, local NC + per-university application (not Hochschulstart), German C1, statistics-heavy, the polyvalenter Bachelor & Master\'s bottleneck — an honest 2026 guide for foreigners.',
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
            'studying-psychology-in-germany-international-students-nc-language',
            'studying-psychology-in-germany-international-students-nc-language-de',
            'studying-psychology-in-germany-international-students-nc-language-en',
        ])->delete();
    }
};
