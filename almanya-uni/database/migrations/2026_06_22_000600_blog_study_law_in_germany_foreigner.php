<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN): Almanya'da yabancı olarak hukuk okumak (2026).
 *
 * Doğrulandı (FU/HU Berlin jura, BRAK Juristenausbildung, jurinsight NC, LLM-guide, 2026):
 *  - Einheitsjurist: Volljurist için Staatsexamen yolu — ~9-10 sömestr → 1. Staatsexamen →
 *    Rechtsreferendariat (~2 yıl, MAAŞLI, istasyonlu) → 2. Staatsexamen. Toplam ~7 yıl.
 *  - LL.B./LL.M. ile Almanya'da lisanslı avukat (Volljurist) OLUNMAZ; uluslararası öğrenci
 *    daha çok burada. Çoğu üni entegre LL.B. veriyor (ilk 6 sömestr+Schwerpunkt) → "kalırsan
 *    diploma yok" riskini yumuşatır.
 *  - Dil: resmî C1 ama anadili gibi değerlendirilir; Fachsprache + Gutachtenstil kalp; natifler
 *    bile zorlanır. NC tıptan rahat (~1,5-3,0; bazı üniler NC'siz). Diploma ülkeye özgü (taşınmaz).
 *  - AB hukuk mezunu: Eignungsprüfung ile Staatsexamen'siz avukat olabilir.
 *
 * Yazar: Halil Yaprakli. Kategori: almanyada-egitim. FK-safe + slug-bazlı idempotent.
 * İç-link: anabin + studienkolleg-myth (tr/de/en); TR'de ek conditional-admission (TR-only).
 */
return new class extends Migration
{
    public function up(): void
    {
        $groupId = '7c1e9a20-5b3d-4e8a-9f12-1a2b3c4d5e75';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'almanyada-egitim')->value('id')
            ?? DB::table('categories')->where('slug', 'law-policy')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
"Almanya'da yabancı olarak hukuk okunur mu?" — Almancası iyi olanların sık sorduğu ama yanıtı en çok şaşırtan sorulardan biri. Kısa cevap: **okunur, ama hukuk yabancılar için Almanya'daki en zor bölümlerden biridir** — çünkü hukukta **dil, bir araç değil, bizzat konunun kendisidir.**

## 1. Önce çatal: iki çok farklı yol
| | **Staatsexamen** | **LL.B. / LL.M.** |
|---|---|---|
| Amaç | Almanya'da **avukat/hâkim/savcı (Volljurist)** olmak | Hukukla *çalışmak*, akademi, kendi ülkende pratik |
| Yapı | Bachelor/Master DEĞİL; ~9–10 sömestr → devlet sınavı | Klasik Bachelor/Master |
| Avukat olur mu? | ✅ Evet (tek yol) | ❌ Hayır (Almanya'da lisanslı avukat olamazsın) |
| Uluslararası öğrenci | Çok az (~500'de 5) | Daha fazla (Erasmus + LL.M.) |

Karar buradan başlar: **Almanya'da avukat mı olacaksın, yoksa "hukukla ilgili" mi çalışacaksın?**

## 2. Avukatlık yolu: Staatsexamen (uzun ve sert)
1. **Hukuk öğrenimi** (~9–10 sömestr) → **1. Staatsexamen** (Erste juristische Prüfung)
2. **Rechtsreferendariat** (staj, ~2 yıl, **maaşlı** — mahkeme/savcılık/idare/avukatlık istasyonları)
3. **2. Staatsexamen** → **Volljurist** (her hukuk mesleğine ehil)

Toplam **~7 yıl**. Eskiden "sınavı geçemezsen elinde diploma kalmaz" korkusu vardı; bugün çoğu üni ilk 6 sömestr + Schwerpunkt sonrası **entegre bir LL.B.** vererek bu riski yumuşatıyor.

## 3. Asıl mesele: hukuk Almancası
Resmî şart genelde **C1**'dir; ama gerçekte **anadili gibi değerlendirilirsin** — gramer hataları bir noktadan sonra puan kaybettirir. Almanca hukuk dili (Fachsprache) ve özellikle **Gutachtenstil** (uzman-rapor yazım üslubu) işin kalbidir; **anadili Almanlar bile zorlanır, hatta kalır.** Kendini test et: [§242 BGB](https://www.gesetze-im-internet.de/bgb/__242.html) ve [§930 BGB](https://www.gesetze-im-internet.de/bgb/__930.html)'yi oku — zorlanmadan anlıyorsan iyi; hiç anlamıyorsan, yabancı dilde ne kadar hızlı öğrenebileceğini dürüstçe değerlendir.

> Dürüst not: C1 zemindir, tavan değil. Avukatlığın asıl aracı dildir; günlük hayatını (okuma/yazma/düşünme) Almanca'ya çevirmeden bu yol çok risklidir. Yine de tam adanmışlıkla **başaran yabancılar var** — imkânsız değil, ama "iki kat çalışma" demektir.

## 4. Kabul: NC ve denklik
Hukukta NC tıbba göre **çok daha rahat** (üniye göre ~1,5–3,0; bazı üniler NC'siz). Lise diploman Abitur'a denk mi → [Anabin](/tr/blog/what-are-anabin-h-h-h-how-is-your-turkish-diploma)'den bak; denk değilse [Studienkolleg](/tr/blog/studienkolleg-is-not-a-language-school-what-it-really-is) gerekebilir. (Şartlı kabul: [bedingte Zulassung rehberi](/tr/blog/germany-conditional-admission-bedingte-zulassung-guide).)

## 5. Diploma taşınır mı? (Kritik)
Alman hukuk diploması **ülkeye özgüdür** — Alman hukuku okursun, başka ülkede doğrudan geçmez. Hareket alanı dar nişlerdir (AB hukuku, uluslararası/tahkim) ya da LL.M. + yurt dışı baro sınavı. Yani Staatsexamen seni büyük ölçüde **Almanya'ya bağlar.** (Not: AB'de hukuk okuduysan, "Eignungsprüfung" ile Staatsexamen'siz avukat olabilirsin.)

## 6. Değer mi? (Dengeli bakış)
Kariyer tarafı genelde **sağlam**: işsizlik düşük, hâkim/savcı açığı var, diploma çok yönlü. Yeni mezunlarda arz fazlası/maaş tartışması var ama tablo iyi. Karar rehberi:

- **Almanya'da avukat olup kalmak istiyorsan** → Staatsexamen, Almanca'ya tam dal.
- **Hukukla çalışmak / kendi ülkende pratik** → LL.B./LL.M. ya da ülkende oku.
- **Almancan C1 ama yazıda anadili seviyesinde değilse** → önce dili native-yakını seviyeye çıkar, sonra karar ver.

## Özet
Almanya'da yabancı olarak hukuk **mümkün ama dil-yoğun ve uzun bir taahhüt.** Önce "avukat mı, hukukla mı?" sorusunu netleştir; sonra hukuk Almancasını dürüstçe test et. Doğru beklenti + tam adanmışlıkla başarılır.

---
*2026 itibarıyla yürürlükteki yapı temel alınmıştır; NC, denklik ve eyalet kuralları değişir — başvurudan önce üniversite / Justizprüfungsamt'tan teyit et.*
MD;

        $deBody = <<<'MD'
„Kann man als Ausländer in Deutschland Jura studieren?" — eine häufige Frage von Menschen mit gutem Deutsch, deren Antwort am meisten überrascht. Kurz: **ja, aber Jura ist für Nicht-Muttersprachler eines der schwersten Fächer in Deutschland** — denn in Jura ist **die Sprache kein Werkzeug, sondern der Gegenstand selbst.**

## 1. Zuerst die Weggabelung: zwei sehr verschiedene Wege
| | **Staatsexamen** | **LL.B. / LL.M.** |
|---|---|---|
| Ziel | **Anwalt/Richter/Staatsanwalt (Volljurist)** in Deutschland | *mit* Recht arbeiten, Wissenschaft, Praxis im Heimatland |
| Aufbau | KEIN Bachelor/Master; ~9–10 Semester → Staatsexamen | klassischer Bachelor/Master |
| Anwalt möglich? | ✅ Ja (einziger Weg) | ❌ Nein (keine Zulassung als Anwalt in DE) |
| Internationale Studierende | sehr wenige (~5 von 500) | mehr (Erasmus + LL.M.) |

Die Entscheidung beginnt hier: **Willst du Anwalt in Deutschland werden – oder „mit Recht" arbeiten?**

## 2. Der Anwaltsweg: Staatsexamen (lang und hart)
1. **Jurastudium** (~9–10 Semester) → **1. Staatsexamen** (Erste juristische Prüfung)
2. **Rechtsreferendariat** (~2 Jahre, **bezahlt** — Stationen: Zivilgericht/Staatsanwaltschaft/Verwaltung/Kanzlei)
3. **2. Staatsexamen** → **Volljurist** (befähigt für jeden juristischen Beruf)

Insgesamt **~7 Jahre**. Früher galt „fällst du durch, hast du keinen Abschluss"; heute vergeben viele Unis nach den ersten 6 Semestern + Schwerpunkt einen **integrierten LL.B.** und mildern dieses Risiko.

## 3. Der Kern: die juristische Sprache
Offiziell reicht oft **C1**; faktisch wirst du aber **wie ein Muttersprachler bewertet** — Grammatikfehler kosten ab einem Punkt Punkte. Die juristische Fachsprache und vor allem der **Gutachtenstil** sind das Herzstück; **selbst deutsche Muttersprachler tun sich schwer oder fallen durch.** Teste dich: lies [§242 BGB](https://www.gesetze-im-internet.de/bgb/__242.html) und [§930 BGB](https://www.gesetze-im-internet.de/bgb/__930.html) — verstehst du es mühelos, gut; verstehst du gar nichts, beurteile ehrlich, wie schnell du in einer Fremdsprache lernst.

> Ehrliche Anmerkung: C1 ist der Boden, nicht die Decke. Das Hauptwerkzeug des Anwalts ist die Sprache; ohne dein ganzes Leben (lesen/schreiben/denken) auf Deutsch umzustellen, ist dieser Weg sehr riskant. Dennoch: **es gibt Ausländer, die es mit vollem Einsatz schaffen** — nicht unmöglich, aber „doppelt so viel Arbeit".

## 4. Zulassung: NC und Anerkennung
Der NC in Jura ist **deutlich entspannter als in Medizin** (je nach Uni ~1,5–3,0; manche Unis NC-frei). Ist dein Schulabschluss dem Abitur gleichwertig → prüfe [Anabin](/de/blog/what-are-anabin-h-h-h-how-is-your-turkish-diploma-de); wenn nicht, ist evtl. das [Studienkolleg](/de/blog/studienkolleg-is-not-a-language-school-what-it-really-is-de) nötig.

## 5. Ist der Abschluss übertragbar? (Kritisch)
Ein deutscher Jura-Abschluss ist **rechtsordnungsspezifisch** — du studierst deutsches Recht, das anderswo nicht direkt gilt. Mobil bist du nur in engen Nischen (EU-Recht, international/Schiedsverfahren) oder über LL.M. + ausländisches Anwaltsexamen. Das Staatsexamen **bindet dich also weitgehend an Deutschland.** (Hinweis: Mit einem EU-Jura-Abschluss kannst du über die „Eignungsprüfung" ohne Staatsexamen Anwalt werden.)

## 6. Lohnt es sich? (Ausgewogen)
Die Karriereseite ist meist **solide**: niedrige Arbeitslosigkeit, Richter-/Staatsanwaltsmangel, vielseitiger Abschluss. Bei Berufseinsteigern gibt es eine Überangebots-/Gehaltsdebatte, aber das Bild ist gut. Entscheidungshilfe:

- **Anwalt in Deutschland werden & bleiben** → Staatsexamen, voll auf Deutsch setzen.
- **Mit Recht arbeiten / im Heimatland praktizieren** → LL.B./LL.M. oder zu Hause studieren.
- **C1, aber schriftlich nicht muttersprachlich** → zuerst die Sprache auf nahezu muttersprachliches Niveau bringen.

## Fazit
Jura als Ausländer in Deutschland ist **möglich, aber eine sprachintensive, lange Verpflichtung.** Kläre zuerst „Anwalt oder mit Recht?"; teste dann ehrlich deine juristische Sprache. Mit der richtigen Erwartung und vollem Einsatz ist es machbar.

---
*Stand 2026; NC, Anerkennung und Länderregeln variieren — vor der Bewerbung bei der Universität / dem Justizprüfungsamt bestätigen.*
MD;

        $enBody = <<<'MD'
"Can you study law in Germany as a foreigner?" — a common question from people with good German, and the answer surprises most. Short: **yes, but law is one of the hardest subjects in Germany for non-native speakers** — because in law, **language isn't a tool, it's the subject itself.**

## 1. The fork first: two very different paths
| | **Staatsexamen** | **LL.B. / LL.M.** |
|---|---|---|
| Goal | become a **lawyer/judge/prosecutor (Volljurist)** in Germany | *work with* law, academia, practise back home |
| Structure | NOT bachelor/master; ~9–10 semesters → state exam | classic bachelor/master |
| Can you be a lawyer? | ✅ Yes (the only route) | ❌ No (no licence to practise in Germany) |
| International students | very few (~5 of 500) | more (Erasmus + LL.M.) |

The decision starts here: **do you want to be a lawyer in Germany — or to "work with" law?**

## 2. The lawyer route: Staatsexamen (long and hard)
1. **Law studies** (~9–10 semesters) → **1st Staatsexamen** (first state exam)
2. **Rechtsreferendariat** (legal traineeship, ~2 years, **paid** — stations: civil court / prosecutor / administration / law firm)
3. **2nd Staatsexamen** → **Volljurist** (qualified for every legal profession)

Total **~7 years**. The old fear was "fail the exam and you have no degree"; today many universities award an **integrated LL.B.** after the first 6 semesters + specialisation, cushioning that risk.

## 3. The crux: legal German
Officially **C1** often suffices; in reality you're **graded like a native** — grammar mistakes cost points beyond a point. Legal German (Fachsprache) and especially the **Gutachtenstil** (legal-opinion writing style) are the heart of it; **even German native speakers struggle or fail.** Test yourself: read [§242 BGB](https://www.gesetze-im-internet.de/bgb/__242.html) and [§930 BGB](https://www.gesetze-im-internet.de/bgb/__930.html) — if you grasp them easily, good; if not at all, judge honestly how fast you learn in a foreign language.

> Honest note: C1 is the floor, not the ceiling. A lawyer's main tool is language; without switching your whole life (reading/writing/thinking) to German, this path is very risky. Still, **some foreigners do succeed with full commitment** — not impossible, but "twice the work".

## 4. Admission: NC and recognition
The NC for law is **much more relaxed than medicine** (~1.5–3.0 depending on the university; some are NC-free). Is your school diploma equivalent to the Abitur → check [Anabin](/en/blog/what-are-anabin-h-h-h-how-is-your-turkish-diploma-en); if not, you may need the [Studienkolleg](/en/blog/studienkolleg-is-not-a-language-school-what-it-really-is-en).

## 5. Is the degree portable? (Critical)
A German law degree is **jurisdiction-specific** — you study German law, which doesn't transfer directly elsewhere. Mobility exists only in narrow niches (EU law, international/arbitration) or via an LL.M. + a foreign bar exam. So the Staatsexamen **largely ties you to Germany.** (Note: with an EU law degree you can become a lawyer via the "Eignungsprüfung" without the Staatsexamen.)

## 6. Is it worth it? (Balanced)
The career side is generally **solid**: low unemployment, a shortage of judges/prosecutors, a versatile degree. There's an oversupply/pay debate for fresh graduates, but the picture is good. Decision guide:

- **Want to be a lawyer in Germany and stay** → Staatsexamen, go all-in on German.
- **Want to work with law / practise at home** → LL.B./LL.M. or study at home.
- **C1 but not native-level in writing** → raise your German to near-native first, then decide.

## Bottom line
Law as a foreigner in Germany is **possible but a language-intensive, long commitment.** First settle "lawyer or work-with-law?"; then honestly test your legal German. With the right expectations and full commitment, it's doable.

---
*Based on the structure in force as of 2026; NC, recognition and state rules vary — confirm with the university / the Justizprüfungsamt before applying.*
MD;

        $variants = [
            'tr' => [
                'slug'  => 'studying-law-in-germany-as-a-foreigner-staatsexamen-vs-llb-llm',
                'title' => 'Almanya\'da Yabancı Olarak Hukuk Okumak (2026): Staatsexamen mı, LL.B./LL.M. mi?',
                'excerpt' => 'Almanya\'da yabancı olarak hukuk okunur ama dil-yoğun en zor yollardan biri: Staatsexamen (avukatlık, ~7 yıl, Referendariat) vs LL.B./LL.M. (avukat yapmaz), hukuk Almancası & Gutachtenstil gerçeği, NC (tıptan rahat), diplomanın ülkeye özgü olması ve "değer mi" karar rehberi.',
                'meta_title' => 'Almanya\'da Yabancı Olarak Hukuk Okumak — Staatsexamen vs LL.M. (2026)',
                'meta_description' => 'Almanya\'da hukuk: Staatsexamen (Volljurist, ~7 yıl) vs LL.B./LL.M., hukuk Almancası gerçeği, NC, diploma taşınabilirliği ve karar rehberi — yabancılar için 2026.',
                'body' => $trBody,
            ],
            'de' => [
                'slug'  => 'studying-law-in-germany-as-a-foreigner-staatsexamen-vs-llb-llm-de',
                'title' => 'Als Ausländer Jura in Deutschland studieren (2026): Staatsexamen vs. LL.B./LL.M.',
                'excerpt' => 'Jura als Ausländer in Deutschland ist möglich, aber einer der sprachintensivsten Wege: Staatsexamen (Volljurist, ~7 Jahre, Referendariat) vs. LL.B./LL.M. (macht keinen Anwalt), die Realität von Fachsprache & Gutachtenstil, NC (entspannter als Medizin), die Rechtsordnungs-Bindung und eine Entscheidungshilfe.',
                'meta_title' => 'Jura in Deutschland als Ausländer — Staatsexamen vs. LL.M. (2026)',
                'meta_description' => 'Jura in Deutschland: Staatsexamen (Volljurist, ~7 J.) vs. LL.B./LL.M., die Sprach-Realität, NC, Übertragbarkeit & Entscheidungshilfe — für Ausländer 2026.',
                'body' => $deBody,
            ],
            'en' => [
                'slug'  => 'studying-law-in-germany-as-a-foreigner-staatsexamen-vs-llb-llm-en',
                'title' => 'Studying Law in Germany as a Foreigner (2026): Staatsexamen vs. LL.B./LL.M.',
                'excerpt' => 'Studying law in Germany as a foreigner is possible but one of the most language-intensive paths: Staatsexamen (Volljurist, ~7 years, Referendariat) vs. LL.B./LL.M. (won\'t make you a lawyer), the reality of legal German & Gutachtenstil, the NC (easier than medicine), jurisdiction-specific degree and a decision guide.',
                'meta_title' => 'Study Law in Germany as a Foreigner — Staatsexamen vs. LL.M. (2026)',
                'meta_description' => 'Law in Germany: Staatsexamen (Volljurist, ~7 yrs) vs. LL.B./LL.M., the legal-German reality, NC, portability & a decision guide — for foreigners 2026.',
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
            'studying-law-in-germany-as-a-foreigner-staatsexamen-vs-llb-llm',
            'studying-law-in-germany-as-a-foreigner-staatsexamen-vs-llb-llm-de',
            'studying-law-in-germany-as-a-foreigner-staatsexamen-vs-llb-llm-en',
        ])->delete();
    }
};
