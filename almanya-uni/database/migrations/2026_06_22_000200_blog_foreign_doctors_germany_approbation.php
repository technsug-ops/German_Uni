<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN): Almanya'da yabancı doktor olarak çalışmak — denklik/lisans yolu (2026).
 *
 * Doğrulandı (Approbation/Berufserlaubnis, FSP/Kenntnisprüfung, Ärztekammer dil şartları, 2026):
 *  - İki lisans: Approbation = tam/süresiz hekimlik lisansı (bağımsız + Facharzt önkoşulu);
 *    Berufserlaubnis = geçici/sınırlı izin (max ~2 yıl, yer/işveren bağlı), köprü.
 *  - AB/AEA diploması otomatik tanınır → genelde sadece FSP (tıp Almancası), bilgi sınavı yok.
 *  - AB-dışı (Türkiye vb.): Gleichwertigkeit kontrolü; eşdeğer → FSP sonrası Approbation,
 *    değil/değerlendirilemez → ek olarak Kenntnisprüfung (tıp bilgisi sözlü sınavı).
 *  - FSP: Ärztekammer'in yaptığı ~C1 tıp dili sınavı; genel dil sıklıkla B2 + üstüne FSP.
 *    Bazı Bundesländer Berufserlaubnis'ten ÖNCE FSP belgesi ister.
 *  - Bundesland önemli: FSP/KP randevu kuyrukları eyalete göre çok değişir (~1 yıla varan).
 *  - Facharztausbildung: ~5-6 yıl ve ÜCRETLİ (asistan hekim maaşı).
 *
 * Yazar: Halil Yaprakli. Kategori: visa-residence → vize → first. FK-safe + slug-bazlı idempotent.
 * İç-link: anabin + yabancı olarak tıp okumak (her ikisi de tr/de/en mevcut) → her dilde locale-doğru.
 */
return new class extends Migration
{
    public function up(): void
    {
        $groupId = '7c1e9a20-5b3d-4e8a-9f12-1a2b3c4d5e71';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'visa-residence')->value('id')
            ?? DB::table('categories')->where('slug', 'vize')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
Almanya'nın doktora ihtiyacı var — hem de ciddi. Yurt dışında hekim olmuş ya da olmak üzere olan biriysen, Almanya'da çalışman tamamen mümkün; ama bu öğrenci başvurusu değil, bir **tanıma (denklik) sürecidir.** Bürokratik, sabır ister, ama yapılabilir. İşte yol haritası.

## Approbation mı, Berufserlaubnis mi?
İki ayrı lisans var, karıştırma:
- **Approbation:** Tam, **süresiz hekimlik lisansı.** Bağımsız çalışabilir, kendi muayeneni açabilir, her yerde hekimlik yapabilirsin. **Facharztausbildung (uzmanlık eğitimi) için de önkoşuldur.** Asıl hedef budur.
- **Berufserlaubnis:** **Geçici ve sınırlı** çalışma izni — en fazla **~2 yıl**, belirli bir yere/işverene bağlı. Denklik sürecini tamamlarken kullanılan bir **köprüdür.** Approbation'ı beklerken klinikte çalışmaya başlamanı sağlar.

## AB içi mi, AB dışı mı? (en kritik ayrım)
Diplomanın nereden geldiği her şeyi belirler:

| Durum | AB/AEA'da eğitim | AB dışı (Türkiye, Meksika, Orta Doğu…) |
|---|---|---|
| Diploma tanınması | **Otomatik** tanınır | **Gleichwertigkeit** (eşdeğerlik) kontrol edilir |
| Sınav | **Sadece FSP** | **FSP + (gerekirse) Kenntnisprüfung** |
| Sonuç | Approbation | Approbation |

AB-dışı diplomanda yetkili makam eşdeğerlik incelemesi yapar. **Eşdeğerse** → FSP sonrası doğrudan Approbation. **Eşdeğer değilse veya tam değerlendirilemiyorsa** → ayrıca **Kenntnisprüfung'u** geçmen gerekir.

## FSP ve Kenntnisprüfung nedir?
- **Fachsprachprüfung (FSP):** **Tıp Almancası** sınavı (~C1 tıp seviyesi). Bölge **Ärztekammer'i** (tabip odası) düzenler: hasta görüşmesi, doktor-doktor iletişimi ve belge yazımı test edilir.
- **Kenntnisprüfung (KP):** **Tıbbi bilgi** sınavı — sözlü, Almanya devlet sınavı (Staatsexamen) seviyesinde. Sadece diploman eşdeğer bulunmazsa devreye girer.

## Dil: genel B2 + tıp C1 (FSP)
Genelde önce **genel Almanca B2**, üzerine de **FSP (tıp C1)** istenir. TELC B2·C1 Medizin veya Goethe sertifikaları **genel tıp-Almancası** belgeleridir; **FSP'nin kendisi ise tabip odasının yaptığı ayrı bir sınavdır** — sertifikayla karıştırma. Dikkat: bazı eyaletler artık **Berufserlaubnis'i vermeden önce FSP belgesini** şart koşuyor.

## Bundesland seçimi zaman kazandırır
FSP/KP randevu kuyrukları **eyalete göre çok değişir.** Bazı eyaletlerde (ör. Rheinland-Pfalz, Bayern) randevu için **~1 yıla varan** beklemeler olabilir. Başvurunu daha hızlı bir eyalette yapmak, süreci aylarca kısaltabilir — esnek olabiliyorsan eyaleti stratejik seç.

## Uzmanlık eğitimi (Facharzt) — üstelik maaşlı
Approbation'dan sonra **Facharztausbildung** ~**5-6 yıl** sürer ve birçok ülkenin aksine **ücretlidir** (asistan hekim maaşı alırsın). Cerrahi en ağır mesai temposuna sahiptir; radyoloji, dermatoloji, psikiyatri daha sakin dallar arasında.

## Dürüst sonuç
Süreç bürokratik ve sabır ister — ama açık, kurallı ve sonu Approbation'la biten gerçek bir yoldur. Anahtar: **erkenden tıp Almancasına yüklen**, diploma denkliğini önceden araştır, randevu için hızlı bir eyalet seç. Türk diploman söz konusuysa denklik mantığını şuradan başla: [Anabin & denklik](/tr/blog/what-are-anabin-h-h-h-how-is-your-turkish-diploma). Henüz hekim değilsen ve sıfırdan okuyacaksan: [yabancı olarak tıp okumak](/tr/blog/study-medicine-in-germany-as-a-foreigner-nc-language-testas).

---
*2026 itibarıyla yürürlükteki kurallar temel alınmıştır; denklik, FSP/Kenntnisprüfung ve dil şartları eyalete (Bundesland) ve tabip odasına göre değişir — başvurudan önce ilgili Ärztekammer / Approbationsbehörde'den teyit et.*
MD;

        $deBody = <<<'MD'
Deutschland braucht Ärzte — und zwar dringend. Wenn du im Ausland Arzt geworden bist oder es gerade wirst, ist eine Tätigkeit in Deutschland absolut möglich; aber das ist keine Studienbewerbung, sondern ein **Anerkennungsverfahren.** Bürokratisch, geduldfordernd, aber machbar. Hier der Fahrplan.

## Approbation oder Berufserlaubnis?
Zwei verschiedene Lizenzen, nicht verwechseln:
- **Approbation:** die volle, **unbefristete ärztliche Lizenz.** Du darfst selbstständig arbeiten, eine eigene Praxis eröffnen, überall ärztlich tätig sein. Sie ist **Voraussetzung für die Facharztausbildung.** Das ist das eigentliche Ziel.
- **Berufserlaubnis:** eine **befristete, eingeschränkte** Erlaubnis — höchstens **~2 Jahre**, an Ort/Arbeitgeber gebunden. Sie ist eine **Brücke**, während du die Anerkennung abschließt, und lässt dich schon in der Klinik arbeiten.

## EU oder Nicht-EU? (die entscheidende Unterscheidung)
Woher dein Diplom stammt, bestimmt alles:

| Punkt | Ausbildung in EU/EWR | Nicht-EU (Türkei, Mexiko, Naher Osten…) |
|---|---|---|
| Diplom-Anerkennung | **Automatisch** anerkannt | **Gleichwertigkeit** wird geprüft |
| Prüfung | **Nur FSP** | **FSP + (ggf.) Kenntnisprüfung** |
| Ergebnis | Approbation | Approbation |

Bei einem Nicht-EU-Diplom prüft die Behörde die Gleichwertigkeit. **Ist es gleichwertig** → Approbation nach der FSP. **Ist es nicht gleichwertig oder nicht voll bewertbar** → musst du zusätzlich die **Kenntnisprüfung** bestehen.

## Was sind FSP und Kenntnisprüfung?
- **Fachsprachprüfung (FSP):** Prüfung im **medizinischen Deutsch** (~C1-Niveau Medizin). Sie wird von der regionalen **Ärztekammer** abgenommen: Arzt-Patienten-Gespräch, Arzt-Arzt-Kommunikation und Dokumentation.
- **Kenntnisprüfung (KP):** Prüfung des **medizinischen Fachwissens** — mündlich, auf dem Niveau des deutschen Staatsexamens. Nur nötig, wenn das Diplom nicht als gleichwertig gilt.

## Sprache: allgemein B2 + Medizin C1 (FSP)
Meist wird zuerst **allgemeines Deutsch B2** verlangt, darauf die **FSP (Medizin C1)**. TELC B2·C1 Medizin oder Goethe sind **allgemeine Medizin-Deutsch-Zertifikate**; die **FSP selbst ist eine separate Prüfung der Ärztekammer** — nicht verwechseln. Achtung: Manche Bundesländer verlangen das **FSP-Zertifikat schon vor der Berufserlaubnis.**

## Bundesland-Wahl spart Zeit
Die Wartezeiten auf FSP-/KP-Termine **variieren stark je nach Bundesland.** In einigen (z. B. Rheinland-Pfalz, Bayern) kann man **bis zu ~1 Jahr** auf einen Termin warten. Die Bewerbung in einem schnelleren Bundesland kann Monate sparen — bist du flexibel, wähle das Land strategisch.

## Facharztausbildung — und das bezahlt
Nach der Approbation dauert die **Facharztausbildung** ~**5-6 Jahre** und ist, anders als in vielen Ländern, **bezahlt** (Assistenzarzt-Gehalt). Die Chirurgie hat die härtesten Arbeitszeiten; Radiologie, Dermatologie und Psychiatrie gelten als ruhiger.

## Ehrliches Fazit
Das Verfahren ist bürokratisch und braucht Geduld — aber es ist ein klarer, geregelter Weg, der mit der Approbation endet. Schlüssel: **früh in medizinisches Deutsch investieren**, die Gleichwertigkeit vorab klären, ein schnelles Bundesland für den Termin wählen. Bei einem türkischen Diplom starte mit der Anerkennungslogik hier: [Anabin & Anerkennung](/de/blog/what-are-anabin-h-h-h-how-is-your-turkish-diploma-de). Falls du noch kein Arzt bist und neu studierst: [als Ausländer Medizin studieren](/de/blog/study-medicine-in-germany-as-a-foreigner-nc-language-testas-de).

---
*Stand 2026; Gleichwertigkeit, FSP/Kenntnisprüfung und Sprachanforderungen variieren je nach Bundesland und Ärztekammer — vor der Bewerbung bei der zuständigen Ärztekammer / Approbationsbehörde bestätigen.*
MD;

        $enBody = <<<'MD'
Germany needs doctors — badly. If you've already qualified as a physician abroad, or are about to, working in Germany is entirely possible; but this is not a student application, it's a **recognition process.** Bureaucratic, patience-testing, but doable. Here's the roadmap.

## Approbation or Berufserlaubnis?
Two distinct licences — don't confuse them:
- **Approbation:** the full, **unlimited medical licence.** You can practise independently, open your own practice, work anywhere as a doctor. It is also the **prerequisite for the Facharztausbildung (specialty training).** This is the real goal.
- **Berufserlaubnis:** a **temporary, limited** permit — at most **~2 years**, tied to a location/employer. It's a **bridge** while you complete recognition, letting you start working in the clinic sooner.

## EU or non-EU? (the decisive split)
Where your diploma comes from determines everything:

| Aspect | EU/EEA-trained | Non-EU (Turkey, Mexico, Middle East…) |
|---|---|---|
| Diploma recognition | Recognised **automatically** | **Gleichwertigkeit** (equivalence) checked |
| Exams | **FSP only** | **FSP + (if needed) Kenntnisprüfung** |
| Result | Approbation | Approbation |

With a non-EU diploma the authority checks equivalence. **If equivalent** → Approbation after the FSP. **If not equivalent or not fully assessable** → you must also pass the **Kenntnisprüfung.**

## What are the FSP and Kenntnisprüfung?
- **Fachsprachprüfung (FSP):** an exam in **medical German** (~C1 medical level). It's set by the regional **Ärztekammer** (medical chamber): doctor-patient interview, doctor-to-doctor communication and documentation.
- **Kenntnisprüfung (KP):** an exam of **medical knowledge** — oral, at the level of the German state exam (Staatsexamen). Only required if your diploma isn't deemed equivalent.

## Language: general B2 + medical C1 (FSP)
Usually **general German B2** is required first, with the **FSP (medical C1)** on top. TELC B2·C1 Medizin or Goethe are **general medical-German certificates**; the **FSP itself is a separate exam run by the medical chamber** — don't conflate the two. Note: some Bundesländer now require the **FSP certificate before issuing the Berufserlaubnis.**

## Choosing the Bundesland saves time
Waiting times for FSP/KP appointments **vary a lot by state.** In some (e.g. Rhineland-Palatinate, Bavaria) you may wait **up to ~1 year** for a slot. Applying in a faster state can shave months off the timeline — if you're flexible, choose the state strategically.

## Specialty training (Facharzt) — and it's paid
After Approbation, the **Facharztausbildung** takes ~**5-6 years** and, unlike in many countries, it is **paid** (an assistant-doctor salary). Surgery has the toughest hours; radiology, dermatology and psychiatry are among the calmer fields.

## Honest bottom line
The process is bureaucratic and takes patience — but it's a clear, rule-based path that ends in Approbation. The keys: **invest in medical German early**, check equivalence in advance, and pick a fast state for your appointment. If you hold a Turkish diploma, start with the recognition logic here: [Anabin & recognition](/en/blog/what-are-anabin-h-h-h-how-is-your-turkish-diploma-en). If you're not a doctor yet and studying from scratch: [studying medicine as a foreigner](/en/blog/study-medicine-in-germany-as-a-foreigner-nc-language-testas-en).

---
*Based on rules in force as of 2026; equivalence, FSP/Kenntnisprüfung and language requirements vary by Bundesland and medical chamber — confirm with the relevant Ärztekammer / Approbationsbehörde before applying.*
MD;

        $variants = [
            'tr' => [
                'slug'  => 'foreign-doctors-germany-approbation-fsp-kenntnispruefung',
                'title' => 'Almanya\'da Yabancı Doktor Olarak Çalışmak (2026): Approbation, FSP ve Kenntnisprüfung',
                'excerpt' => 'Yurt dışında hekim olduysan Almanya\'da çalışmak mümkün — ama bu denklik (tanıma) sürecidir: Approbation vs Berufserlaubnis, AB vs AB-dışı diploma tanınması, FSP (tıp C1) ve gerekirse Kenntnisprüfung, B2+FSP dil şartı, Bundesland randevu kuyrukları ve maaşlı Facharzt eğitimi.',
                'meta_title' => 'Almanya\'da Yabancı Doktor: Approbation, FSP, KP (2026)',
                'meta_description' => 'Almanya\'da yabancı doktor olarak çalışma rehberi: Approbation vs Berufserlaubnis, AB/AB-dışı denklik, FSP + Kenntnisprüfung, B2+C1 dil, Bundesland kuyruğu ve maaşlı Facharzt.',
                'body' => $trBody,
            ],
            'de' => [
                'slug'  => 'foreign-doctors-germany-approbation-fsp-kenntnispruefung-de',
                'title' => 'Als ausländischer Arzt in Deutschland arbeiten (2026): Approbation, FSP & Kenntnisprüfung',
                'excerpt' => 'Wer im Ausland Arzt wurde, kann in Deutschland arbeiten — aber über ein Anerkennungsverfahren: Approbation vs Berufserlaubnis, EU vs Nicht-EU-Anerkennung, FSP (Medizin C1) und ggf. Kenntnisprüfung, B2+FSP-Sprache, Wartezeiten je Bundesland und die bezahlte Facharztausbildung.',
                'meta_title' => 'Ausländischer Arzt in Deutschland: Approbation, FSP, KP',
                'meta_description' => 'Leitfaden für ausländische Ärzte in Deutschland: Approbation vs Berufserlaubnis, EU/Nicht-EU-Anerkennung, FSP + Kenntnisprüfung, B2+C1, Bundesland-Wartezeiten, bezahlter Facharzt.',
                'body' => $deBody,
            ],
            'en' => [
                'slug'  => 'foreign-doctors-germany-approbation-fsp-kenntnispruefung-en',
                'title' => 'Working as a Foreign Doctor in Germany (2026): Approbation, FSP & Kenntnisprüfung',
                'excerpt' => 'Qualified as a doctor abroad? You can work in Germany — but via a recognition process: Approbation vs Berufserlaubnis, EU vs non-EU diploma recognition, the FSP (medical C1) and possibly the Kenntnisprüfung, B2+FSP language, Bundesland appointment queues and the paid Facharzt training.',
                'meta_title' => 'Foreign Doctor in Germany: Approbation, FSP & KP (2026)',
                'meta_description' => 'Guide for foreign doctors in Germany: Approbation vs Berufserlaubnis, EU/non-EU recognition, FSP + Kenntnisprüfung, B2+C1 language, Bundesland queues and paid Facharzt training.',
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
            'foreign-doctors-germany-approbation-fsp-kenntnispruefung',
            'foreign-doctors-germany-approbation-fsp-kenntnispruefung-de',
            'foreign-doctors-germany-approbation-fsp-kenntnispruefung-en',
        ])->delete();
    }
};
