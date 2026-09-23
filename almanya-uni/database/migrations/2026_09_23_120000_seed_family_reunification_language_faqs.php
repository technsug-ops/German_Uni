<?php

use App\Models\Faq;
use App\Models\FaqTopic;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Aile birleşiminde Almanca dil şartı — 4 SSS × 3 dil (TR/EN/DE).
 *
 * Kaynak: topluluktan gelen "Köln'de alınan ÖSD B1 aile birleşimi için yeterli mi?"
 * sorusu. Soru iki yaygın yanlışı barındırıyordu: (1) istenen seviye B1 sanılıyor,
 * gerçekte A1; (2) Türk vatandaşlarının Dogan kararı sayesinde muaf olduğu sanılıyor,
 * bu 01.08.2015'ten beri geçerli değil. Cevaplar Auswärtiges Amt Vize El Kitabı'nın
 * (Stand 21.08.2026) tam metninden doğrulandı.
 *
 * Idempotent: slug zaten varsa atlar. faq_topic_id topic SLUG'ından çözülür.
 * answer_html + answer_minutes Faq saving-hook'u tarafından üretilir.
 */
return new class extends Migration
{
    public function up(): void
    {
        $topicIds = FaqTopic::pluck('id', 'slug')->all();
        $existing = DB::table('faqs')->pluck('slug')->flip();

        $inserted = 0;
        $skipped  = 0;

        Faq::unguard();

        foreach ($this->faqs() as $item) {
            $topicId = $topicIds[$item['topic_slug']] ?? null;

            if (! $topicId) {
                $skipped += count($item['translations']);

                continue;
            }

            $groupId = (string) Str::uuid();

            foreach ($item['translations'] as $locale => $t) {
                $slug = $locale === 'tr' ? $item['slug'] : $item['slug'] . '-' . $locale;

                if (isset($existing[$slug])) {
                    $skipped++;

                    continue;
                }

                $faq = new Faq();
                $faq->forceFill([
                    'faq_topic_id'         => $topicId,
                    'translation_group_id' => $groupId,
                    'locale'               => $locale,
                    'question'             => $t['question'],
                    'slug'                 => $slug,
                    'answer_md'            => trim($t['answer_md']),
                    'intent'               => 'community',
                    'category'             => 'visa',
                    'has_answer'           => true,
                    'is_published'         => true,
                    'sort_order'           => $item['sort_order'],
                ]);
                $faq->save();

                $existing[$slug] = true;
                $inserted++;
            }
        }

        Faq::reguard();

        echo "family-reunification language FAQs: +{$inserted} eklendi, {$skipped} atlandı\n";
    }

    public function down(): void
    {
        DB::table('faqs')->whereIn('slug', $this->allSlugs())->delete();
    }

    /** @return array<string> */
    private function allSlugs(): array
    {
        $slugs = [];
        foreach ($this->faqs() as $item) {
            foreach (array_keys($item['translations']) as $locale) {
                $slugs[] = $locale === 'tr' ? $item['slug'] : $item['slug'] . '-' . $locale;
            }
        }

        return $slugs;
    }

    private function faqs(): array
    {
        // Kaynaklar (cevapların sonunda da linkli veriliyor)
        $srcTurkey = 'https://tuerkei.diplo.de/tr-de/service/05-visaeinreise/2720770-2720770';
        $srcVhb    = 'https://www.auswaertiges-amt.de/resource/blob/207816/4a7c6232702495822cc934ee6a6ba25e/visumhandbuch-data.pdf';
        $srcCerts  = 'https://www.auswaertiges-amt.de/de/service/fragenkatalog-node/2238204-2238204';
        $srcBamf   = 'https://www.bamf.de/SharedDocs/Anlagen/DE/MigrationAufenthalt/Ehegattennachzug/ehegattennachzug.pdf';

        return [
            [
                'slug'       => 'osd-certificate-accepted-family-reunification-germany',
                'topic_slug' => 'dil',
                'sort_order' => 10,
                'translations' => [
                    'tr' => [
                        'question'  => 'Aile birleşimi için ÖSD sertifikası geçerli mi?',
                        'answer_md' => <<<MD
**Evet, ÖSD geçerli.** Auswärtiges Amt'ın Vize El Kitabı, "Dil bilgisinin belgelenmesi" bölümünde ÖSD'yi tanınan sınav sağlayıcıları arasında sayıyor — A1'den C2'ye kadar tüm seviyeler dahil.

Türkiye'den başvuranlar için önemli bir ayrıntı var: Almanya'nın Türkiye'deki temsilcilikleri aile birleşiminde **yalnızca iki kurumun** sertifikasını kabul ediyor:

- **Goethe-Institut** — "Start Deutsch 1"
- **ÖSD** — "Grundstufe Deutsch 1"

Temsilcilik sayfası bunu açıkça yazıyor: *"Türkiye'deki diğer sağlayıcılar Alman temsilciliklerinin vize bölümlerince tanınmaz."* Yani **telc sertifikası Türkiye'de kabul edilmiyor** — Almanya'da veya başka ülkelerde geçerli sayılsa bile. Sınava girmeden önce bu ayrımı bilmek, boşa harcanan aylardan kurtarır.

Sertifikanın sınav tarihi genellikle **1 yıldan eski olmamalı**. Kesin süre için başvuracağın temsilciliğin bilgi notuna bak.

Sertifikayı Almanya'da aldıysan (örneğin Köln'deki bir ÖSD merkezinde), kurum yine ÖSD olduğu için tanınırlığı değişmez.

Kaynaklar: [Almanya'nın Türkiye temsilcilikleri]({$srcTurkey}) · [Auswärtiges Amt — kabul edilen dil sertifikaları]({$srcCerts})

*Bu içerik genel bilgilendirme amaçlıdır, hukuki danışmanlık değildir.*
MD,
                    ],
                    'en' => [
                        'question'  => 'Is an ÖSD certificate accepted for family reunification in Germany?',
                        'answer_md' => <<<MD
**Yes, ÖSD is accepted.** The German Federal Foreign Office's Visa Handbook lists ÖSD among the recognised examination providers in its chapter on proof of language skills, covering every level from A1 to C2.

If you apply from Turkey, one detail matters a great deal: the German missions in Turkey accept certificates from **only two providers** for family reunification:

- **Goethe-Institut** — "Start Deutsch 1"
- **ÖSD** — "Grundstufe Deutsch 1"

The mission's own page states it plainly: *other providers in Turkey are not recognised by the visa sections of the German missions.* This means a **telc certificate is not accepted in Turkey**, even though telc is recognised in Germany and in other countries. Knowing this before you book an exam can save you months.

The exam date usually must be **no more than one year old**. Check the information sheet of the mission handling your application for the exact rule.

If you sat the exam inside Germany (for example at an ÖSD centre in Cologne), the provider is still ÖSD, so recognition is unaffected.

Sources: [German missions in Turkey]({$srcTurkey}) · [Federal Foreign Office — accepted language certificates]({$srcCerts})

*This is general information, not legal advice.*
MD,
                    ],
                    'de' => [
                        'question'  => 'Wird ein ÖSD-Zertifikat beim Familiennachzug anerkannt?',
                        'answer_md' => <<<MD
**Ja, das ÖSD wird anerkannt.** Das Visumhandbuch des Auswärtigen Amts führt das ÖSD im Beitrag „Nachweis von Sprachkenntnissen" unter den anerkannten Prüfungsanbietern auf — mit allen Stufen von A1 bis C2.

Für Anträge aus der Türkei gilt eine wichtige Einschränkung: Die deutschen Auslandsvertretungen in der Türkei erkennen beim Familiennachzug nur **zwei Anbieter** an:

- **Goethe-Institut** — „Start Deutsch 1"
- **ÖSD** — „Grundstufe Deutsch 1"

Auf der Seite der Vertretung heißt es ausdrücklich: *„Weitere Anbieter in der Türkei werden seitens der Visastellen der deutschen Vertretungen nicht anerkannt."* Ein **telc-Zertifikat wird in der Türkei also nicht akzeptiert**, obwohl telc in Deutschland und anderen Ländern anerkannt ist.

Das Prüfungsdatum darf in der Regel **nicht länger als ein Jahr** zurückliegen. Die genaue Frist steht im Merkblatt der zuständigen Auslandsvertretung.

Wurde die Prüfung in Deutschland abgelegt (etwa an einem ÖSD-Zentrum in Köln), bleibt der Anbieter das ÖSD — an der Anerkennung ändert sich nichts.

Quellen: [Deutsche Vertretungen in der Türkei]({$srcTurkey}) · [Auswärtiges Amt — anerkannte Sprachzertifikate]({$srcCerts})

*Allgemeine Information, keine Rechtsberatung.*
MD,
                    ],
                ],
            ],

            [
                'slug'       => 'german-level-required-family-reunification-a1-or-b1',
                'topic_slug' => 'dil',
                'sort_order' => 11,
                'translations' => [
                    'tr' => [
                        'question'  => 'Aile birleşiminde A1 mi B1 mi gerekiyor?',
                        'answer_md' => <<<MD
Eş için aile birleşiminde gereken seviye **A1'dir** — yasada "basit Almanca bilgisi" olarak geçer (§ 30 Abs. 1 S. 1 Nr. 2 AufenthG). Alman vatandaşının eşi için de aynı şart uygulanır (§ 28 Abs. 1 S. 5 AufenthG).

**B1 başka şeyler için gerekiyor:**

- Süresiz oturum izni (Niederlassungserlaubnis)
- Vatandaşlık başvurusu

Aile birleşimi vizesi için B1 istenmez. Bu ayrım sık karıştırılıyor ve insanlar gereğinden iki seviye yüksek bir sınava aylarca hazırlanıyor.

**Elinde zaten B1 varsa:** daha yüksek bir seviye normalde A1'i kapsar. Yine de temsilcilik bilgi notları sınavı adıyla sayıyor ("Start Deutsch 1", "Grundstufe Deutsch 1"), bu yüzden başvurudan önce ilgili temsilciliğe sorup teyit almak en sağlıklısı — özellikle sertifikan bir yıldan eskiyse.

Kaynaklar: [BAMF — aile birleşiminde basit Almanca bilgisi]({$srcBamf}) · [Auswärtiges Amt Vize El Kitabı]({$srcVhb})

*Bu içerik genel bilgilendirme amaçlıdır, hukuki danışmanlık değildir.*
MD,
                    ],
                    'en' => [
                        'question'  => 'Does family reunification require A1 or B1 German?',
                        'answer_md' => <<<MD
Spouse reunification requires **A1** — what the law calls "basic knowledge of German" (§ 30 (1) sentence 1 no. 2 Residence Act). The same requirement applies to the spouse of a German citizen (§ 28 (1) sentence 5).

**B1 is needed for something else:**

- A permanent settlement permit (Niederlassungserlaubnis)
- Naturalisation

B1 is not required for a family reunification visa. People frequently confuse the two and spend months preparing for an exam two levels above what they need.

**If you already hold B1:** a higher level normally covers A1. Even so, the missions' information sheets name specific exams ("Start Deutsch 1", "Grundstufe Deutsch 1"), so confirm with the mission handling your case before you apply — particularly if your certificate is more than a year old.

Sources: [BAMF — basic German skills for spouse reunification]({$srcBamf}) · [Federal Foreign Office Visa Handbook]({$srcVhb})

*This is general information, not legal advice.*
MD,
                    ],
                    'de' => [
                        'question'  => 'Ist beim Familiennachzug A1 oder B1 erforderlich?',
                        'answer_md' => <<<MD
Beim Ehegattennachzug ist **A1** erforderlich — im Gesetz „einfache Kenntnisse der deutschen Sprache" (§ 30 Abs. 1 S. 1 Nr. 2 AufenthG). Für den Nachzug zum deutschen Ehegatten gilt dieselbe Voraussetzung (§ 28 Abs. 1 S. 5 AufenthG).

**B1 wird für anderes verlangt:**

- Niederlassungserlaubnis
- Einbürgerung

Für das Visum zum Familiennachzug ist B1 nicht erforderlich. Diese beiden Anforderungen werden häufig verwechselt.

**Wenn bereits ein B1-Zertifikat vorliegt:** Eine höhere Stufe deckt A1 in der Regel ab. Da die Merkblätter der Auslandsvertretungen die Prüfungen jedoch namentlich aufführen („Start Deutsch 1", „Grundstufe Deutsch 1"), sollte dies vor der Antragstellung bei der zuständigen Vertretung bestätigt werden — besonders wenn das Zertifikat älter als ein Jahr ist.

Quellen: [BAMF — einfache Deutschkenntnisse beim Ehegattennachzug]({$srcBamf}) · [Visumhandbuch des Auswärtigen Amts]({$srcVhb})

*Allgemeine Information, keine Rechtsberatung.*
MD,
                    ],
                ],
            ],

            [
                'slug'       => 'turkish-citizens-language-requirement-family-reunification-dogan',
                'topic_slug' => 'vize',
                'sort_order' => 12,
                'translations' => [
                    'tr' => [
                        'question'  => 'Türk vatandaşları aile birleşiminde Almanca şartından muaf mı? (Dogan kararı)',
                        'answer_md' => <<<MD
**Hayır, otomatik bir muafiyet yok.** Bu, Türk topluluğunda sık dolaşan bir yanlış bilgi.

Kafa karışıklığının kaynağı Avrupa Birliği Adalet Divanı'nın **Dogan kararı** (C-138/13, 10 Temmuz 2014). Karar, Ortaklık Hukuku'nun standstill hükmü nedeniyle o dönemki Alman düzenlemesini hukuka aykırı bulmuştu. Gerekçe şuydu: dil belgesinin bulunmaması başvurunun **otomatik reddine** yol açıyor, kişinin özel durumu hiç değerlendirilmiyordu.

Almanya bu itirazı **1 Ağustos 2015'te** yasaya § 30 Abs. 1 S. 3 Nr. 6 AufenthG'yi ekleyerek giderdi. Auswärtiges Amt'ın güncel Vize El Kitabı bunu açıkça yazıyor: ortaklık hukukundan yararlanan Türk vatandaşının eşinden de dil belgesi **istenebilir**; standstill hükmü artık buna engel değil.

**Geriye kalan hak — zorluk (Härtefall) istisnası:** Kişinin özel koşulları nedeniyle Almanya'ya girmeden önce Almanca öğrenmeye çalışması **mümkün ya da makul değilse**, dil şartı aranmaz. Dogan kararının kalıcı etkisi budur: red otomatik olamaz, her başvuruda bireysel değerlendirme yapılmak zorundadır.

Somut durumunun bu istisnaya girip girmediği bir hukukçuya danışılacak bir sorudur.

Kaynak: [Auswärtiges Amt Vize El Kitabı, bölüm "Ehegattennachzug zum assoziationsberechtigten türkischen Ehepartner"]({$srcVhb})

*Bu içerik genel bilgilendirme amaçlıdır, hukuki danışmanlık değildir.*
MD,
                    ],
                    'en' => [
                        'question'  => 'Are Turkish citizens exempt from the German language requirement for family reunification? (Dogan ruling)',
                        'answer_md' => <<<MD
**No, there is no automatic exemption.** This is a widespread misconception.

It stems from the Court of Justice of the European Union's **Dogan ruling** (C-138/13, 10 July 2014). The Court held that the German rule as it then stood breached the standstill clause of the EEC–Turkey Association Agreement. The reason was specific: missing proof of language skills led to an **automatic refusal**, with no room to consider the applicant's individual circumstances.

Germany addressed that objection on **1 August 2015** by adding § 30 (1) sentence 3 no. 6 to the Residence Act. The Federal Foreign Office's current Visa Handbook now states explicitly that proof of language skills **may be required** also from the spouse of a Turkish national covered by association law, and that the standstill clause no longer stands in the way.

**What does remain — the hardship exception:** if, because of the particular circumstances of the individual case, it is **impossible or unreasonable** for the spouse to make efforts to learn basic German before entry, the requirement does not apply. That is the lasting effect of Dogan: a refusal cannot be automatic, and each case must be assessed individually.

Whether your situation falls under that exception is a question for a lawyer.

Source: [Federal Foreign Office Visa Handbook, section on spouse reunification with an association-privileged Turkish spouse]({$srcVhb})

*This is general information, not legal advice.*
MD,
                    ],
                    'de' => [
                        'question'  => 'Sind türkische Staatsangehörige beim Familiennachzug vom Sprachnachweis befreit? (Dogan-Urteil)',
                        'answer_md' => <<<MD
**Nein, eine automatische Befreiung gibt es nicht.** Das ist ein verbreitetes Missverständnis.

Ausgangspunkt ist das **Dogan-Urteil** des EuGH (C-138/13, 10. Juli 2014). Der Gerichtshof sah die damalige deutsche Regelung als Verstoß gegen die assoziationsrechtliche Stillhalteklausel an. Der Grund war konkret: Der fehlende Sprachnachweis führte **automatisch zur Ablehnung**, ohne dass besondere Umstände des Einzelfalls berücksichtigt werden konnten.

Deutschland hat dieser Beanstandung zum **1. August 2015** durch § 30 Abs. 1 S. 3 Nr. 6 AufenthG Rechnung getragen. Das aktuelle Visumhandbuch des Auswärtigen Amts stellt daher fest: Auch beim Nachzug zum assoziationsberechtigten türkischen Ehepartner **darf** der Sprachnachweis verlangt werden; die Stillhalteklausel des Art. 13 ARB 1/80 steht dem nicht mehr entgegen.

**Was bleibt — die Härtefallregelung:** Ist es dem Ehegatten aufgrund besonderer Umstände des Einzelfalles **nicht möglich oder nicht zumutbar**, vor der Einreise Bemühungen zum Spracherwerb zu unternehmen, entfällt das Erfordernis. Das ist die bleibende Wirkung von Dogan: Eine Ablehnung darf nicht automatisch erfolgen, jeder Fall ist einzeln zu prüfen.

Ob der eigene Fall darunterfällt, sollte anwaltlich geprüft werden.

Quelle: [Visumhandbuch des Auswärtigen Amts, Abschnitt „Ehegattennachzug zum assoziationsberechtigten türkischen Ehepartner"]({$srcVhb})

*Allgemeine Information, keine Rechtsberatung.*
MD,
                    ],
                ],
            ],

            [
                'slug'       => 'who-is-exempt-german-language-requirement-family-reunification',
                'topic_slug' => 'vize',
                'sort_order' => 13,
                'translations' => [
                    'tr' => [
                        'question'  => 'Aile birleşiminde Almanca şartından kimler muaf?',
                        'answer_md' => <<<MD
Dil şartı herkese uygulanmıyor. Başlıca muafiyetler:

- **Mavi Kart (Blue Card), ICT Kart, araştırmacı veya nitelikli işçi** oturumu sahiplerinin eşleri
- **AB/AEA vatandaşının** eşi
- Başvuru sırasında **Almancası zaten görünür şekilde yeterli** olanlar
- **İyi istihdam beklentisi olan üniversite mezunları**
- Evlilik, eşin Almanya'ya **mülteci olarak gelmesinden önce** kurulmuşsa
- **Zorluk (Härtefall):** özel koşullar nedeniyle giriş öncesi Almanca öğrenmenin mümkün ya da makul olmadığı durumlar (§ 30 Abs. 1 S. 3 Nr. 6 AufenthG)
- **Alman vatandaşının eşiyse:** bir yıl boyunca ciddi çaba göstermesine rağmen başaramadıysa şart dayatılamaz

Dikkat edilecek nokta: muafiyetlerin çoğu **Almanya'daki eşin oturum türüne** bağlı, başvuranın kendi durumuna değil. Yani önce "eşim hangi oturum izniyle Almanya'da?" sorusunu yanıtlamak gerekiyor.

Kaynaklar: [Auswärtiges Amt — Ehegattennachzug'da dil şartı ve istisnalar](https://pristina.diplo.de/xk-de/service/visa-einreise/2433520-2433520) · [BAMF]({$srcBamf})

*Bu içerik genel bilgilendirme amaçlıdır, hukuki danışmanlık değildir.*
MD,
                    ],
                    'en' => [
                        'question'  => 'Who is exempt from the German language requirement for family reunification?',
                        'answer_md' => <<<MD
The language requirement does not apply to everyone. The main exemptions:

- Spouses of holders of an **EU Blue Card, ICT Card, researcher or skilled-worker** permit
- Spouses of **EU/EEA citizens**
- Applicants whose **German is already evidently sufficient** at the time of application
- **University graduates with good employment prospects**
- Marriages that existed **before the spouse came to Germany as a refugee**
- **Hardship:** where particular circumstances make it impossible or unreasonable to make efforts to learn basic German before entry (§ 30 (1) sentence 3 no. 6 Residence Act)
- **Spouses of German citizens:** the requirement cannot be enforced if genuine efforts have remained unsuccessful for a year

One thing to note: most exemptions depend on **the residence status of the spouse already in Germany**, not on the applicant's own situation. So the first question to answer is: on which permit is my spouse living in Germany?

Sources: [Federal Foreign Office — language requirement and exceptions](https://pristina.diplo.de/xk-de/service/visa-einreise/2433520-2433520) · [BAMF]({$srcBamf})

*This is general information, not legal advice.*
MD,
                    ],
                    'de' => [
                        'question'  => 'Wer ist beim Familiennachzug vom Sprachnachweis befreit?',
                        'answer_md' => <<<MD
Der Sprachnachweis gilt nicht für alle. Die wichtigsten Ausnahmen:

- Ehegatten von Inhabern einer **Blauen Karte EU, ICT-Karte, Forscher- oder Fachkräfte**-Aufenthaltserlaubnis
- Ehegatten von **EU-/EWR-Bürgern**
- Antragsteller, deren **Deutschkenntnisse bereits erkennbar ausreichen**
- **Hochschulabsolventen mit guter Erwerbsprognose**
- Ehen, die bereits bestanden, **bevor der Ehegatte als Flüchtling nach Deutschland kam**
- **Härtefall:** wenn es aufgrund besonderer Umstände des Einzelfalles nicht möglich oder nicht zumutbar ist, vor der Einreise Bemühungen zum Spracherwerb zu unternehmen (§ 30 Abs. 1 S. 3 Nr. 6 AufenthG)
- **Beim Nachzug zu Deutschen:** Der Nachweis darf nicht verlangt werden, wenn ernsthafte Bemühungen ein Jahr lang erfolglos geblieben sind

Wichtig: Die meisten Ausnahmen hängen vom **Aufenthaltstitel des in Deutschland lebenden Ehegatten** ab, nicht von der Situation des Antragstellers. Die erste zu klärende Frage lautet daher: Mit welchem Aufenthaltstitel lebt mein Ehegatte in Deutschland?

Quellen: [Auswärtiges Amt — Sprachnachweis und Ausnahmen](https://pristina.diplo.de/xk-de/service/visa-einreise/2433520-2433520) · [BAMF]({$srcBamf})

*Allgemeine Information, keine Rechtsberatung.*
MD,
                    ],
                ],
            ],
        ];
    }
};
