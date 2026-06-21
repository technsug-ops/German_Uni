<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN): Almanya'da tıp okumanın dürüst duygusal gerçeği (2026).
 *
 * Deneyim-temelli (öğrenci anlatıları): yalnızlık, entegrasyon, ruh sağlığı,
 * "mezuniyetten sonra kalayım mı / gideyim mi?" — empatik ama gerçekçi, cesaret kırmadan.
 *  - Yalnızlık yaygın ve senin suçun değil (Alman kültürü mesafeli; tıp/yüksek öğrenim yapısı izole eder).
 *  - Dil sosyal akıcılığı geciktirir (C1 olsa bile mizah/argo gecikir).
 *  - Tükenmişlik/düşük ruh hali = tembellik değil yorgunluk → üni psikolojik danışma + Hausarzt.
 *  - Topluluğu bilinçli kur (uluslararası öğrenciler, kulüpler/Fachschaft, Hochschulsport, tandem, büyük şehirler).
 *  - Irkçılık/ayrımcılık bölgeye göre değişir; üni şehirleri/büyük batı şehirleri daha uluslararası.
 *  - Mezuniyet sonrası: Approbation taşınabilir — Almanya'da kal / AB içinde taşın / USMLE ile ABD / ülkene dön.
 *  - %80'i bitirmişken bırakma — diplomayı (ve ideal olarak vatandaşlık/uzun ikamet) bitir, kapılar açık kalsın.
 *
 * Yazar: Halil Yaprakli. Kategori: yasam. FK-safe + slug-bazlı idempotent.
 * İç-link: locale-doğru (DE/EN sadece tıp blogu; yalnızlık blogu yalnızca TR'de var).
 */
return new class extends Migration
{
    public function up(): void
    {
        $groupId = '7c1e9a20-5b3d-4e8a-9f12-1a2b3c4d5e74';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'yasam')->value('id')
            ?? DB::table('categories')->where('slug', 'student-life')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
Geceleri kütüphaneden çıkıp boş bir odaya döndüğünde, "burada insanları tanıyorum ama gerçek bir arkadaşım yok" diye hissediyorsan — **yalnız değilsin, ve bu senin hatan değil.** Almanya'da tıp okuyan yabancı öğrencilerin çoğu tam olarak bunu yaşıyor. Süreci dürüstçe konuşalım: ne normal, neyle başa çıkılır, ve "değer mi?" sorusunun gerçek cevabı.

## Yalnızlık neden bu kadar yaygın?
İki sebep üst üste biner. **Birincisi kültür:** Almanlar çoğunlukla mesafeli ve özel hayatına önem verir; derin dostluklar yavaş kurulur. Yüzeyde "kibar ama mesafeli" ilişkiler erken dönemde tamamen normaldir. **İkincisi tıp fakültesinin yapısı:** herkes başını kaldırmadan çalışıyor, sürekli sınav, sürekli koşu. Bu kombinasyon "çok insan tanıyorum ama kimse yakın değil" hissini doğurur. Bu kişisel bir başarısızlık değil; geniş çapta yaşanan bir gerçek.

## Dil, izolasyonu derinleştirir
C1'in olsa bile **sosyal akıcılık geriden gelir** — argo, mizah, kelime oyunları, ince göndermeler. Akademik Almancan sağlam olabilir ama bir akşam yemeğindeki kahkahalara yetişememek yalnızlığı büyütür. Bu da geçici ve normaldir; aylar içinde kapanır.

## Tükenmişlik tembellik değil
"Eskiden hırslıydım, şimdi tembel hissediyorum" — bunu çok duyuyoruz. Gerçek şu: bu **tükenmişlik ve muhtemelen düşük ruh hali / depresyon belirtisi, tembellik değil.** Tıp acımasızca yorucu. Kendini suçlama döngüsüne girme.

Somut adımlar:
- Üniversitenin **ücretsiz psikolojik danışmanlığı** (çoğu üniversitede *psychologische Beratung* / Studienberatung var).
- **Hausarzt'ına** (aile hekimi) git — yorgunluk, uyku, ruh hali hakkında konuş.
- **İzole olma.** En kötü içgüdü geri çekilmek; tam tersi gerekir.

## Topluluğu bilinçli kur
Arkadaşlık kendiliğinden gelmiyorsa, **inşa et:**
- Diğer **uluslararası öğrencileri** ve kendi yaşındaki insanları ara.
- **Fachschaft**, öğrenci kulüpleri, **Hochschulsport** (üni sporu), dil **tandem** buluşmaları.
- Daha çeşitli bir sosyal sahne istiyorsan **büyük şehirleri** düşün.

## Irkçılık ve şehir seçimi
Ayrımcılık bazıları için gerçek; ama **bölgeye göre çok değişir.** Büyük/batı şehirleri ve üniversite kasabaları genelde daha uluslararası ve daha rahattır. Bunu nereye yerleşeceğini seçerken hesaba katmana izin var — bu hassasiyet meşru.

## "Mezuniyetten sonra gideyim mi?"
Dürüst çerçeve: Alman tıp diploması + **Approbation son derece taşınabilir.** Mezuniyet sonrası seçeneklerin var — ve "öteki tarafın çimi daha yeşil" yanılgısına düşmeden **somut adımları araştır** (kaç USMLE sınavı, maliyet, süre).

| Seçenek | Anahtar şart | Takas |
|---|---|---|
| Almanya'da kal | Residency (Facharzt) — **maaşlı** | Dil + uzun yıllar |
| AB içinde taşın | Diploma + AB ikamet/pasaport | Yeni ülke/dil adaptasyonu |
| ABD (USMLE) | Step 1 & 2 + ECFMG → match | Pahalı, uzun, rekabetçi |
| Ülkene dön | Denklik | Genelde göreli yüksek maaş |

## %80'i bitirmişken bırakma
En büyük tuzak: bitişe yakınken pes etmek. **Diplomayı (ideal olarak vatandaşlık/uzun ikametle) bitirmek her kapıyı açık tutar;** sona yakın bırakmak hepsini kapatır. Zor günde verilen "bırakıyorum" kararı, iyi günde çok pahalıya patlar.

## Sonuç
Yalnızlık, yorgunluk ve şüphe — hepsi bu yolun parçası, ama hiçbiri kalıcı değil. Yardım iste, topluluğunu kur, diplomayı bitir; sonra dünyanın her yeri açık. Daha derin oku: [yalnızlık & ruh sağlığı](/tr/blog/loneliness-and-mental-health-as-an-international-student-in-germany) · [yabancı olarak tıp okumak](/tr/blog/study-medicine-in-germany-as-a-foreigner-nc-language-testas).

---
*Bu yazı gerçek öğrenci deneyimlerine dayanan genel bir perspektif ve başa çıkma tavsiyesidir; tıbbi veya psikolojik tedavinin yerini tutmaz. Zorlanıyorsan üniversitenin psikolojik danışmanlığına veya Hausarzt'ına başvur.*
MD;

        $deBody = <<<'MD'
Wenn du nachts aus der Bibliothek in ein leeres Zimmer zurückkommst und denkst: „Ich kenne hier Leute, aber ich habe keine echten Freunde" — **du bist nicht allein, und es ist nicht deine Schuld.** Den meisten internationalen Medizinstudierenden in Deutschland geht es genau so. Reden wir ehrlich darüber: was normal ist, womit man umgehen kann, und die echte Antwort auf „Lohnt es sich?".

## Warum Einsamkeit so verbreitet ist
Zwei Gründe überlagern sich. **Erstens die Kultur:** Deutsche sind oft zurückhaltend und privat; tiefe Freundschaften entstehen langsam. Oberflächlich „freundlich, aber distanziert" ist am Anfang völlig normal. **Zweitens die Struktur des Medizinstudiums:** alle lernen mit gesenktem Kopf, ständig Prüfungen, ständig Druck. Diese Kombination erzeugt das Gefühl „Ich kenne viele, aber niemand ist wirklich nah." Das ist kein persönliches Versagen, sondern eine weit verbreitete Realität.

## Sprache vertieft die Isolation
Selbst mit C1 hinkt die **soziale Sprachgewandtheit hinterher** — Slang, Humor, Wortspiele, feine Anspielungen. Dein akademisches Deutsch kann solide sein, doch beim Lachen am Esstisch nicht mitzukommen verstärkt die Einsamkeit. Auch das ist vorübergehend und normal; es löst sich über die Monate.

## Erschöpfung ist keine Faulheit
„Früher war ich ehrgeizig, jetzt fühle ich mich faul" — das hören wir oft. Die Wahrheit: Das ist ein Zeichen von **Erschöpfung und möglicherweise gedrückter Stimmung / Depression, nicht von Faulheit.** Medizin ist unerbittlich. Verfalle nicht in Selbstvorwürfe.

Konkrete Schritte:
- Die **kostenlose psychologische Beratung** der Uni (an den meisten Hochschulen vorhanden).
- Geh zu deinem **Hausarzt** — sprich über Müdigkeit, Schlaf, Stimmung.
- **Isolier dich nicht.** Der schlimmste Instinkt ist Rückzug; das Gegenteil ist nötig.

## Gemeinschaft bewusst aufbauen
Wenn Freundschaft nicht von selbst kommt, **bau sie auf:**
- Suche andere **internationale Studierende** und Menschen in deinem Alter.
- **Fachschaft**, studentische Clubs, **Hochschulsport**, Sprach-**Tandems**.
- Für eine vielfältigere soziale Szene größere **Großstädte** in Betracht ziehen.

## Rassismus und Stadtwahl
Diskriminierung ist für manche real; aber sie **variiert stark je nach Region.** Größere/westliche Städte und Universitätsstädte sind meist internationaler und entspannter. Du darfst das bei der Wahl deines Wohnorts berücksichtigen — diese Sensibilität ist legitim.

## „Soll ich nach dem Abschluss gehen?"
Ehrlich gesagt: Der deutsche Medizinabschluss + die **Approbation sind hochgradig übertragbar.** Du hast Optionen nach dem Abschluss — und ohne dem „Auf der anderen Seite ist das Gras grüner"-Irrtum zu verfallen, **recherchiere die konkreten Schritte** (wie viele USMLE-Prüfungen, Kosten, Dauer).

| Option | Schlüssel-Voraussetzung | Kompromiss |
|---|---|---|
| In Deutschland bleiben | Facharztweiterbildung — **bezahlt** | Sprache + viele Jahre |
| Innerhalb der EU ziehen | Abschluss + EU-Aufenthalt/Pass | Neue Anpassung |
| USA (USMLE) | Step 1 & 2 + ECFMG → Match | Teuer, lang, kompetitiv |
| Heimkehren | Anerkennung | Oft relativ höheres Gehalt |

## Hör nicht auf, wenn du zu 80 % fertig bist
Die größte Falle: kurz vor dem Ziel aufgeben. **Den Abschluss (idealerweise mit Einbürgerung/langem Aufenthalt) zu beenden hält alle Türen offen;** kurz vor Schluss abzubrechen schließt sie. Eine „Ich höre auf"-Entscheidung an einem schlechten Tag wird an guten Tagen sehr teuer.

## Fazit
Einsamkeit, Erschöpfung und Zweifel gehören zu diesem Weg, aber keines davon ist von Dauer. Hol dir Hilfe, bau deine Gemeinschaft auf, beende den Abschluss — danach steht dir die Welt offen. Mehr dazu: [Medizin als Ausländer studieren](/de/blog/study-medicine-in-germany-as-a-foreigner-nc-language-testas-de).

---
*Dieser Beitrag bietet eine allgemeine Perspektive und Bewältigungstipps auf Basis echter Studierendenerfahrungen; er ersetzt keine medizinische oder psychologische Behandlung. Wenn es dir schlecht geht, wende dich an die psychologische Beratung deiner Uni oder an deinen Hausarzt.*
MD;

        $enBody = <<<'MD'
If you walk back from the library to an empty room at night and think, "I know people here, but I have no real friends" — **you're not alone, and it's not your fault.** Most international medical students in Germany feel exactly this. Let's talk about it honestly: what's normal, what you can do about it, and the real answer to "is it worth it?".

## Why loneliness is so common
Two things stack up. **First, culture:** Germans are often reserved and private; deep friendships form slowly. Surface-level "friendly but not genuine" relationships are completely normal early on. **Second, the structure of med school:** everyone is heads-down studying, constant exams, constant grind. That combination breeds the feeling "I know a lot of people but no one is actually close." This is not a personal failing — it's a widely reported reality.

## Language deepens the isolation
Even with C1, your **social fluency lags** — slang, humor, wordplay, subtle references. Your academic German can be solid, yet not keeping up with the laughter at a dinner table magnifies loneliness. This too is temporary and normal; it closes over the months.

## Burnout is not laziness
"I used to be ambitious, now I feel lazy" — we hear this constantly. The truth: that's a sign of **exhaustion and possibly low mood / depression, not laziness.** Med school is relentless. Don't spiral into self-blame.

Concrete steps:
- Your university's **free psychological counseling** (most have *psychologische Beratung* / Studienberatung).
- See your **Hausarzt** (GP) — talk about fatigue, sleep, mood.
- **Don't isolate.** The worst instinct is to withdraw; you need the opposite.

## Build community deliberately
If friendship doesn't come on its own, **build it:**
- Seek out other **international students** and people your own age.
- **Fachschaft** (student body), clubs, **Hochschulsport** (university sports), language **tandems**.
- For a more diverse social scene, consider **larger cities**.

## Racism and choosing a city
Discrimination is real for some; but it **varies hugely by region.** Bigger/western cities and university towns tend to be more international and more relaxed. You're allowed to factor this in when choosing where to settle — that sensitivity is legitimate.

## "Should I leave after graduation?"
Honest framing: the German medical degree + **Approbation is highly portable.** You have options after graduating — and without falling for the "grass is greener" trap, **research the concrete steps** (how many USMLE exams, costs, timelines) before deciding.

| Option | Key requirement | Tradeoff |
|---|---|---|
| Stay in Germany | Residency (Facharzt) — **paid** | Language + many years |
| Move within the EU | Degree + EU residence/passport | New adaptation |
| USA (USMLE) | Step 1 & 2 + ECFMG → match | Expensive, long, competitive |
| Return home | Recognition | Often relatively higher salary |

## Don't quit when you're 80% done
The biggest trap: giving up near the finish. **Finishing the degree (ideally with citizenship/long residence) keeps every door open;** quitting near the end closes them all. An "I quit" decision made on a bad day costs you dearly on the good ones.

## Bottom line
Loneliness, exhaustion and doubt are all part of this path, but none of them is permanent. Ask for help, build your community, finish the degree — then the whole world is open. Read more: [studying medicine as a foreigner](/en/blog/study-medicine-in-germany-as-a-foreigner-nc-language-testas-en).

---
*This post offers general perspective and coping advice based on real student experiences; it is not a substitute for medical or psychological treatment. If you're struggling, reach out to your university's psychological counseling or your Hausarzt.*
MD;

        $variants = [
            'tr' => [
                'slug'  => 'studying-medicine-in-germany-the-honest-reality-loneliness-worth-it',
                'title' => 'Almanya\'da Tıp Okumanın Dürüst Gerçeği: Yalnızlık, Tükenmişlik ve "Değer mi?" (2026)',
                'excerpt' => 'Almanya\'da yabancı olarak tıp okumanın duygusal gerçeği: yalnızlık neden yaygın (kültür + tıp yapısı), dilin izolasyonu, tükenmişliğin tembellik olmadığı + nereden yardım alınır, topluluğu kurmak, ırkçılık/şehir seçimi ve "mezuniyetten sonra gideyim mi?" — taşınabilir diploma, USMLE/AB/ülkene dönüş. Empatik ama gerçekçi.',
                'meta_title' => 'Almanya\'da Tıp: Yalnızlık, Tükenmişlik ve "Değer mi?" (2026)',
                'meta_description' => 'Almanya\'da yabancı olarak tıp okumanın dürüst duygusal gerçeği: yalnızlık, ruh sağlığı, topluluk kurmak ve "mezuniyetten sonra kalayım mı?" — destekleyici, gerçekçi 2026 rehberi.',
                'body' => $trBody,
            ],
            'de' => [
                'slug'  => 'studying-medicine-in-germany-the-honest-reality-loneliness-worth-it-de',
                'title' => 'Die ehrliche Realität des Medizinstudiums in Deutschland: Einsamkeit, Erschöpfung & „Lohnt es sich?" (2026)',
                'excerpt' => 'Die emotionale Realität des Medizinstudiums als Ausländer in Deutschland: warum Einsamkeit verbreitet ist (Kultur + Studienstruktur), Sprache und Isolation, Erschöpfung ist keine Faulheit + wo es Hilfe gibt, Gemeinschaft aufbauen, Rassismus/Stadtwahl und „Soll ich nach dem Abschluss gehen?" — übertragbarer Abschluss, USMLE/EU/Heimkehr. Empathisch, aber realistisch.',
                'meta_title' => 'Medizin in Deutschland: Einsamkeit, Erschöpfung & „Lohnt es sich?" (2026)',
                'meta_description' => 'Die ehrliche emotionale Realität des Medizinstudiums als Ausländer in Deutschland: Einsamkeit, psychische Gesundheit, Gemeinschaft und „Soll ich nach dem Abschluss bleiben?" — unterstützender, realistischer Leitfaden 2026.',
                'body' => $deBody,
            ],
            'en' => [
                'slug'  => 'studying-medicine-in-germany-the-honest-reality-loneliness-worth-it-en',
                'title' => 'The Honest Reality of Studying Medicine in Germany: Loneliness, Burnout & "Is It Worth It?" (2026)',
                'excerpt' => 'The emotional reality of studying medicine in Germany as a foreigner: why loneliness is common (culture + med-school structure), language and isolation, burnout is not laziness + where to get help, building community, racism/city choice and "should I leave after graduation?" — a portable degree, USMLE/EU/returning home. Empathetic but realistic.',
                'meta_title' => 'Medicine in Germany: Loneliness, Burnout & "Is It Worth It?" (2026)',
                'meta_description' => 'The honest emotional reality of studying medicine in Germany as a foreigner: loneliness, mental health, building community and "should I stay after graduation?" — a supportive, realistic 2026 guide.',
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
            'studying-medicine-in-germany-the-honest-reality-loneliness-worth-it',
            'studying-medicine-in-germany-the-honest-reality-loneliness-worth-it-de',
            'studying-medicine-in-germany-the-honest-reality-loneliness-worth-it-en',
        ])->delete();
    }
};
