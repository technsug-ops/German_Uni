<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Halil Yaprakli'yı otorite konumunda persona olarak öne çıkarır:
 *
 * 1) Almanya'da Eğitim (kategori 1) postlarını Halil + Anna arasında karışık dağıt:
 *    - 1/3 Halil tek yazar
 *    - 1/3 Anna tek yazar
 *    - 1/3 Anna primary + Halil co-author (akademik konularda kurucu desteği)
 *
 * 2) Öğrenci Hayatı (kategori 9) içindeki Kariyer-ilgili postlara (IT iş bulma,
 *    Werkstudent, vb.) Halil'i co-author yap (Caner primary kalır)
 *
 * 3) Kurucudan mektup (TR + EN + DE) — Halil tek yazar, kategori 1, featured
 */
return new class extends Migration
{
    public function up(): void
    {
        $halilId  = DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id');
        $annaId   = DB::table('users')->where('email', 'anna@almanyauni.de')->value('id');
        $canerId  = DB::table('users')->where('email', 'caner@almanyauni.com')->value('id');

        if (! $halilId || ! $annaId) {
            // Founder veya Anna yoksa migration skip — başka migration'larla zaten oluşturulmuş olmalı
            return;
        }

        // 1) Almanya'da Eğitim re-distribute
        $eduPosts = DB::table('posts')
            ->where('category_id', 1)
            ->where('locale', 'tr')
            ->orderBy('id')
            ->pluck('id', 'translation_group_id')
            ->all();

        // Translation_group bazlı dağıt — aynı yazı (TR/EN/DE) aynı yazar(lar)a sahip olsun
        $tgs = array_keys($eduPosts);
        $totalGroups = count($tgs);

        foreach ($tgs as $i => $tg) {
            // Pattern: %33 Halil only, %33 Anna only, %33 Anna + Halil co
            $pattern = $i % 3;

            if ($pattern === 0) {
                // Halil tek yazar
                DB::table('posts')->where('translation_group_id', $tg)
                    ->update(['user_id' => $halilId, 'co_author_id' => null]);
            } elseif ($pattern === 1) {
                // Anna tek yazar
                DB::table('posts')->where('translation_group_id', $tg)
                    ->update(['user_id' => $annaId, 'co_author_id' => null]);
            } else {
                // Anna primary + Halil co-author
                DB::table('posts')->where('translation_group_id', $tg)
                    ->update(['user_id' => $annaId, 'co_author_id' => $halilId]);
            }
        }

        // 2) Kariyer-ilgili Öğrenci Hayatı postlarına Halil co-author
        if ($canerId) {
            $careerSlugs = ['almanya-it-is-bulma', 'werkstudent', 'blue-card', 'kariyer'];
            foreach ($careerSlugs as $slugFragment) {
                DB::table('posts')
                    ->where('category_id', 9)
                    ->where('slug', 'like', '%' . $slugFragment . '%')
                    ->update(['co_author_id' => $halilId]);
            }
        }

        // 3) Kurucu yazısı (TR + EN + DE)
        $existing = DB::table('posts')->where('slug', 'kurucudan-mektup-almanyauni-neden-var')->first();
        if (! $existing) {
            $groupId = (string) Str::uuid();
            $now = now();

            $tr = [
                'title' => 'AlmanyaUni Neden Var: Kurucudan Açık Mektup',
                'slug'  => 'kurucudan-mektup-almanyauni-neden-var',
                'excerpt' => 'Türk öğrenci için Almanya yolculuğu bir hayalden bir plana dönüşmeli. Plan açık, kaynaklar resmi, deneyim toplulukla paylaşılmış olmalı. AlmanyaUni bu yüzden var.',
                'content_md' => self::founderLetterTR(),
            ];
            $en = [
                'title' => 'Why AlmanyaUni Exists: An Open Letter from the Founder',
                'slug'  => 'founders-letter-why-almanyauni-exists',
                'excerpt' => 'For Turkish students, the path to Germany should turn from a dream into a plan — with clear steps, official sources, and community-tested experience. That is why AlmanyaUni exists.',
                'content_md' => self::founderLetterEN(),
            ];
            $de = [
                'title' => 'Warum AlmanyaUni existiert: Ein offener Brief des Gründers',
                'slug'  => 'gruenderbrief-warum-almanyauni-existiert',
                'excerpt' => 'Für türkische Studierende sollte der Weg nach Deutschland vom Traum zum Plan werden — mit klaren Schritten, offiziellen Quellen und der Erfahrung der Community. Dafür gibt es AlmanyaUni.',
                'content_md' => self::founderLetterDE(),
            ];

            foreach ([['tr', $tr], ['en', $en], ['de', $de]] as [$locale, $data]) {
                $contentHtml = Str::markdown($data['content_md'], ['html_input' => 'allow']);
                DB::table('posts')->insert([
                    'locale'               => $locale,
                    'translation_group_id' => $groupId,
                    'user_id'              => $halilId,
                    'co_author_id'         => null,
                    'category_id'          => 1, // Almanya'da Eğitim
                    'title'                => $data['title'],
                    'slug'                 => $data['slug'],
                    'excerpt'              => $data['excerpt'],
                    'content_md'           => $data['content_md'],
                    'content_html'         => $contentHtml,
                    'meta_title'           => $data['title'],
                    'meta_description'     => $data['excerpt'],
                    'reading_minutes'      => max(2, (int) round(str_word_count(strip_tags($contentHtml)) / 220)),
                    'is_published'         => true,
                    'published_at'         => $now,
                    'created_at'           => $now,
                    'updated_at'           => $now,
                ]);
            }
        }
    }

    public function down(): void {}

    private static function founderLetterTR(): string
    {
        return <<<'MD'
# AlmanyaUni Neden Var: Kurucudan Açık Mektup

Almanya'da yükseköğretim **600'ü aşkın üniversite** ve **18.000'in üzerinde program** ile devasal bir sistem. Türk öğrenci için ise bu sistemin yalnızca bir kısmı erişilebilir — geri kalanı çoğu zaman karanlıkta kalıyor.

> *"uni-assist VPD'yi neden istiyor?"*
> *"Sperrkonto'da €992 yetmez derler, gerçek ne?"*
> *"Anabin'de diplomam H+ mı yoksa H+- mi?"*
> *"Krankenkasse fiyatları aynıymış, neden seçim yapayım?"*

Bu sorular her gün binlerce Türk öğrencinin Telegram gruplarında, forumlarında, WhatsApp sohbetlerinde dönüyor. Cevap arayan kişi sayısı, doğru cevabı bulan sayısından çok daha fazla.

## Neden Bu Platformu Kurdum

Ben **Halil Yaprakli**. Almanya yolculuğunu kendi yaşamış, sonradan dönüp arkasından gelecek olanlara "bu süreç bu kadar zor olmamalı" diyebilen biri. AlmanyaUni'yi bu inançla kurdum: doğru bilgiye erişim ücretsiz olmalı, resmi kaynaklardan beslenmelidir ve topluluk deneyimiyle zenginleştirilmelidir.

## AlmanyaUni Bugün

Şu anda platformda:

- **600+ aktif Alman üniversitesi** (DAAD ve Hochschulkompass kaynaklı, sürekli güncelleniyor)
- **18.000+ program** (lisans, yüksek lisans, doktora — İngilizce öğretim de filtreli)
- **20+ pratik araç** — Sperrkonto karşılaştırma, yaşam maliyeti hesaplayıcı, vize randevu takvimi, başvuru deadline'ları, not dönüştürücü
- **150'den fazla rehber yazı** — her ay yenisi eklenir
- **130+ şehir profili** — kira aralıkları, ulaşım, öğrenci kapasitesi, kültürel notlar
- **200+ SSS** — topluluk soruları üzerinden derlendi, uzman cevapları eklendi

Hepsi **ücretsiz** ve kayıt zorunluluğu olmadan erişilebilir. Premium versiyon mentor seansları, kişisel başvuru takip dashboard'u gibi gelişmiş özellikler için 2026 sonunda gelecek; ama temel rehberlik her zaman açık kalacak.

## Editör Kadrosu

Almanya'daki öğrenci deneyimini içeriden aktaran **7 editör** içerik üretiyor:

| Editör | Uzmanlık |
|---|---|
| **Anna Schmidt** (Berlin) | Alman akademik sistemi, Studienkolleg, Hochschulzulassung |
| **Ayesha Khan** (Münih, TUM) | Uluslararası öğrenci finansı — Sperrkonto, Krankenkasse, Schufa |
| **Elif G.** | Başvuru süreci, uni-assist, VPD, ret sebepleri |
| **Gamze E.** | Almanca dil sınavları — TestDaF, DSH, Goethe, telc |
| **Hakan Kutlu** | Vize başvurusu, konsolosluk randevuları, Anmeldung |
| **Caner Türkdoğru** | Kariyer, Werkstudent, mezun sonrası iş arama |
| **Halil Yaprakli** *(Kurucu)* | Editörlük, kurucu yazıları, genel stratejik içerik |

Her editör kendi alanındaki içerikleri **resmi kaynaklar** (DAAD, KMK, Anabin, BERUFENET, Auswärtiges Amt) + **topluluk havuzu** (Telegram + Forum, **142.000+ mesaj** analiz edildi) üzerinden hazırlıyor.

## Nereye Gidiyoruz

Önümüzdeki 12 ayın yol haritası:

1. **Application Tracker** — başvurunun 8 adımını (eligibility kontrolünden vize randevusuna) tek dashboard'dan takip et
2. **Mentor ağı** — Almanya'da kariyer yapmış Türk profesyonellerle 1-on-1 görüşme, ücretsiz + ücretli seçenekleri
3. **Forum açılışı** (2027) — anonim soru-cevap, uzman moderasyonu, kategori bazlı arşiv
4. **Native app** — PWA bugün hazır, native iOS/Android 2027'de

## Sen Ne Yapabilirsin

- **Sorduğun soru cevap bulamıyorsa**: Forum açıldığında bekleme listesine kaydol; arada bizim editör e-postalarını da kullanabilirsin
- **Yazar veya mentor olmak istiyorsan**: [/mentors](/mentors) sayfası üzerinden başvur
- **Topluluk olarak destek ver**: yazıları sosyal medyada paylaş, deneyimini blog yorumlarında aktar — her gerçek anekdot bir başkasının kararına yön verir
- **Almanya'ya gelmeden önce**: doğru kaynaklara güven — **DAAD, KMK Anabin, Hochschulkompass, BERUFENET, Auswärtiges Amt**. Bu sitelerin tamamı resmi ve ücretsizdir.

## Son Söz

Almanya yolculuğunda yalnız olmadığını bilmen önemli. AlmanyaUni'nin yaptığı şey aslında basit: **resmi bilgi + topluluk deneyimi + Türkçe açık dil**. Bu üçü yan yana geldiğinde bir öğrencinin Almanya'ya geçiş kararı, hayalden somut bir plana dönüşüyor.

Her açıklanan SSS, her tamamlanan tool, her zenginleştirilen üniversite sayfası bu plana katkıdır. Geri bildirimini bekliyorum.

**— Halil Yaprakli**
*Kurucu, AlmanyaUni*
*Mayıs 2026*
MD;
    }

    private static function founderLetterEN(): string
    {
        return <<<'MD'
# Why AlmanyaUni Exists: An Open Letter from the Founder

German higher education is a massive system — **over 600 universities** and **18,000+ programs**. For Turkish students, however, only a fraction of this system is accessible; the rest stays in the dark.

> *"Why does uni-assist need a VPD?"*
> *"Will the €992 Sperrkonto rate be enough in practice?"*
> *"Is my diploma H+ or H+- in Anabin?"*
> *"Krankenkasse prices look the same — why does the choice matter?"*

These questions circulate every day in thousands of Turkish students' Telegram groups, forums, and WhatsApp chats. The number of people asking far exceeds the number who find a reliable answer.

## Why I Built This

I'm **Halil Yaprakli**. Someone who lived the Germany path and then turned around to say: *this process should not be this hard*. I built AlmanyaUni on that conviction — access to accurate information must be free, sourced from official documents, and enriched by community experience.

## What AlmanyaUni Is Today

Currently on the platform:

- **600+ active German universities** (sourced from DAAD and Hochschulkompass, continuously updated)
- **18,000+ programs** (Bachelor, Master, PhD — English-taught filtered)
- **20+ practical tools** — Sperrkonto comparison, cost-of-living calculator, visa appointment timeline, application deadlines, grade converter
- **150+ how-to guides** — new ones added every month
- **130+ city profiles** — rent ranges, transport, student capacity, cultural notes
- **200+ FAQs** — collected from community questions, answered by expert editors

All **free** and accessible without registration. A Premium tier with mentor sessions and a personal application dashboard is coming late 2026, but the core guides will remain open.

## The Editor Team

**7 editors** produce content based on real student experience in Germany:

| Editor | Specialty |
|---|---|
| **Anna Schmidt** (Berlin) | German academic system, Studienkolleg, Hochschulzulassung |
| **Ayesha Khan** (Munich, TUM) | International student finance — Sperrkonto, Krankenkasse, Schufa |
| **Elif G.** | Application process, uni-assist, VPD, rejection reasons |
| **Gamze E.** | German language exams — TestDaF, DSH, Goethe, telc |
| **Hakan Kutlu** | Visa application, consulate appointments, Anmeldung |
| **Caner Türkdoğru** | Career, Werkstudent, post-graduation job search |
| **Halil Yaprakli** *(Founder)* | Editorial oversight, founder pieces, general strategic content |

Every editor draws on **official sources** (DAAD, KMK, Anabin, BERUFENET, Auswärtiges Amt) + a **community pool** (Telegram + Forum — **142,000+ messages** analysed).

## Where We Are Going

The roadmap for the next 12 months:

1. **Application Tracker** — follow all 8 steps of your application (from eligibility check to visa appointment) on a single dashboard
2. **Mentor network** — 1-on-1 sessions with Turkish professionals who built careers in Germany; free and paid options
3. **Forum launch** (2027) — anonymous Q&A with expert moderation and category-based archive
4. **Native app** — PWA is live today; native iOS/Android in 2027

## What You Can Do

- **If you didn't find the answer**: join the forum waiting list; meanwhile reach out via the editor inbox
- **If you want to write or mentor**: apply through [/mentors](/mentors)
- **Support the community**: share guides on social media, leave honest experience notes in blog comments — every real anecdote shifts someone's decision
- **Before you come to Germany**: trust the official sources — **DAAD, KMK Anabin, Hochschulkompass, BERUFENET, Auswärtiges Amt**. All are official and free.

## Final Words

It matters that you know you are not alone on the path to Germany. What AlmanyaUni does is actually simple: **official information + community experience + plain Turkish**. When those three sit side by side, the decision to come to Germany turns from a dream into a concrete plan.

Every clarified FAQ, every completed tool, every enriched university page contributes to that plan. Your feedback is welcome.

**— Halil Yaprakli**
*Founder, AlmanyaUni*
*May 2026*
MD;
    }

    private static function founderLetterDE(): string
    {
        return <<<'MD'
# Warum AlmanyaUni existiert: Ein offener Brief des Gründers

Das deutsche Hochschulsystem ist riesig — **über 600 Universitäten** und **mehr als 18.000 Studiengänge**. Für türkische Studierende ist davon allerdings nur ein Bruchteil zugänglich; der Rest bleibt im Dunkeln.

> *"Warum verlangt uni-assist eine VPD?"*
> *"Reicht der Sperrkonto-Satz von 992 € in der Praxis aus?"*
> *"Ist mein Abschluss in Anabin H+ oder H+-?"*
> *"Die Krankenkasse-Beiträge sind gleich — warum dann eine Auswahl?"*

Diese Fragen kursieren täglich in tausenden Telegram-Gruppen, Foren und WhatsApp-Chats türkischer Studierender. Die Zahl derer, die fragen, übersteigt bei Weitem die derer, die eine zuverlässige Antwort finden.

## Warum ich diese Plattform aufgebaut habe

Ich bin **Halil Yaprakli**. Jemand, der den Weg nach Deutschland selbst gegangen ist und sich danach umgedreht hat, um zu sagen: *Dieser Prozess sollte nicht so schwer sein*. AlmanyaUni habe ich mit dieser Überzeugung aufgebaut — der Zugang zu verlässlichen Informationen muss kostenlos sein, aus offiziellen Quellen stammen und durch Community-Erfahrung angereichert werden.

## AlmanyaUni heute

Derzeit auf der Plattform:

- **600+ aktive deutsche Hochschulen** (Quellen: DAAD und Hochschulkompass, fortlaufend aktualisiert)
- **18.000+ Studiengänge** (Bachelor, Master, PhD — auch englischsprachige filterbar)
- **20+ praktische Tools** — Sperrkonto-Vergleich, Lebenshaltungs-Rechner, Visumstermin-Kalender, Bewerbungsfristen, Notenumrechnung
- **150+ Leitfäden** — monatlich kommen neue dazu
- **130+ Städteprofile** — Mietspannen, ÖPNV, Studierendenzahl, kulturelle Hinweise
- **200+ FAQ** — aus Community-Fragen zusammengestellt, von Experten beantwortet

Alles **kostenlos** und ohne Registrierung zugänglich. Eine Premium-Variante mit Mentor-Sessions und persönlichem Bewerbungs-Dashboard kommt Ende 2026, der Kernbereich bleibt jedoch immer offen.

## Das Redaktionsteam

**7 Redakteure** erstellen Inhalte auf Basis echter Studienerfahrung in Deutschland:

| Redakteur | Schwerpunkt |
|---|---|
| **Anna Schmidt** (Berlin) | Deutsches Hochschulsystem, Studienkolleg, Hochschulzulassung |
| **Ayesha Khan** (München, TUM) | Internationale Finanzierung — Sperrkonto, Krankenkasse, Schufa |
| **Elif G.** | Bewerbung, uni-assist, VPD, Ablehnungsgründe |
| **Gamze E.** | Deutsche Sprachprüfungen — TestDaF, DSH, Goethe, telc |
| **Hakan Kutlu** | Visumsantrag, Konsulatstermine, Anmeldung |
| **Caner Türkdoğru** | Karriere, Werkstudent, Jobsuche nach dem Abschluss |
| **Halil Yaprakli** *(Gründer)* | Redaktion, Gründerstücke, strategische Inhalte |

Jeder Redakteur stützt sich auf **offizielle Quellen** (DAAD, KMK, Anabin, BERUFENET, Auswärtiges Amt) + einen **Community-Pool** (Telegram + Forum — **142.000+ Nachrichten** ausgewertet).

## Wohin geht die Reise

Die Roadmap für die nächsten 12 Monate:

1. **Application Tracker** — alle 8 Schritte der Bewerbung (von der Zulassungsprüfung bis zum Visumstermin) in einem Dashboard
2. **Mentor-Netzwerk** — 1-zu-1-Gespräche mit türkischen Fachkräften, die in Deutschland Karriere gemacht haben; kostenlose und bezahlte Optionen
3. **Forum-Start** (2027) — anonyme Q&A mit Experten-Moderation und kategorisiertem Archiv
4. **Native App** — PWA ist heute live; native iOS/Android 2027

## Was du tun kannst

- **Wenn deine Frage offen bleibt**: Trag dich auf die Warteliste fürs Forum ein; bis dahin sind die Redakteur-Postfächer offen
- **Wenn du schreiben oder mentorieren möchtest**: bewirb dich über [/mentors](/mentors)
- **Unterstütze die Community**: Teile Leitfäden in sozialen Medien, hinterlasse ehrliche Erfahrungsnotizen in den Blog-Kommentaren — jede echte Anekdote beeinflusst die Entscheidung anderer
- **Bevor du nach Deutschland kommst**: vertraue offiziellen Quellen — **DAAD, KMK Anabin, Hochschulkompass, BERUFENET, Auswärtiges Amt**. Alle sind offiziell und kostenlos.

## Schlussworte

Es ist wichtig zu wissen, dass du auf dem Weg nach Deutschland nicht allein bist. Was AlmanyaUni tut, ist eigentlich einfach: **offizielle Informationen + Community-Erfahrung + klare türkische Sprache**. Sobald diese drei nebeneinander stehen, wird aus dem Traum von Deutschland ein konkreter Plan.

Jede geklärte FAQ, jedes fertige Tool, jede angereicherte Hochschulseite trägt zu diesem Plan bei. Über dein Feedback freue ich mich.

**— Halil Yaprakli**
*Gründer, AlmanyaUni*
*Mai 2026*
MD;
    }
};
