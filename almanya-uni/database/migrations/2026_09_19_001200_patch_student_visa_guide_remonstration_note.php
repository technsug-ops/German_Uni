<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Str;

/**
 * İÇERİK DÜZELTMESİ (TR+DE+EN): öğrenci vizesi rehberindeki Remonstration bölümü.
 *
 * Yazının kendisi doğru; sadece "ret sonrası" kısmı Remonstration'ı yürürlükteymiş gibi anlatıyordu.
 * Remonstration 1 Temmuz 2025'te Auswärtiges Amt tarafından dünya çapında ve tüm vize türlerinde
 * kaldırıldı (auswaertiges-amt.de/de/newsroom/2724844-2724844). Bu migration yazının tamamını
 * değiştirmez; üç noktayı cerrahi olarak düzeltir:
 *   1. Girişteki "kısa cevap" cümlesi
 *   2. "### Remonstration" bölümünün tamamı → kalan iki yol (yeni başvuru / VG Berlin davası)
 *   3. Harç iadesiyle ilgili SSS cümlesindeki Remonstration atfı
 *
 * Bölüm değişimi REGEX ile yapılıyor (başlıktan kapanış blockquote'una kadar): lokal ile prod
 * gövdesi arasında küçük farklar olsa bile tutar ve ikinci kez çalıştırıldığında eşleşme kalmadığı
 * için tekrar değiştirmez (idempotent). Eşleşme bulunamazsa o kayıt olduğu gibi bırakılır.
 */
return new class extends Migration
{
    public function up(): void
    {
        $trNew = <<<'MD'
### Ret sonrası: Remonstration kaldırıldı

Uzun yıllar boyunca ret kararına karşı **Remonstration** (ücretsiz ikinci değerlendirme) yazılabiliyordu. Bu usul **1 Temmuz 2025 itibarıyla dünya çapında ve tüm vize türlerinde kaldırıldı** — kanuni bir hak değil, Dışişleri Bakanlığı'nın gönüllü sunduğu bir incelemeydi. Geriye iki yol kalıyor:

- **Yeni başvuru:** bekleme süresi yoktur, ertesi gün bile başvurabilirsin. Ama ret gerekçesini kapatmadan yapılan başvuru büyük olasılıkla aynı sonucu verir. Yeni randevu ve yeni harç gerekir.
- **Berlin İdare Mahkemesi'nde dava:** tebliğin ertesi gününden itibaren **1 ay** içinde açılır ve hangi ülkede başvurduğundan bağımsız olarak yalnızca **Verwaltungsgericht Berlin** yetkilidir. Aylar sürer, dönem başlangıcına nadiren yetişir; kararın açıkça hatalı olduğu dosyalar için mantıklıdır.

**Pratik tavsiye:** Öğrenci dosyalarının büyük çoğunluğunda doğru hamle yeni başvurudur. Gerekçeyi nasıl kapatacağın, dönemi kurtarmak için üniversiteyle ne konuşman gerektiği ve dava yolunun pratiği: [vize reddi sonrası ne yapmalı](/tr/blog/what-to-do-after-german-student-visa-refusal-remonstration-appeal-guide).
MD;

        $deNew = <<<'MD'
### Nach der Ablehnung: die Remonstration ist abgeschafft

Jahrelang konnte gegen einen Ablehnungsbescheid eine **Remonstration** (kostenlose zweite Prüfung) eingelegt werden. Dieses Verfahren wurde **zum 1. Juli 2025 weltweit und für alle Visumarten abgeschafft** — es war ohnehin kein gesetzlicher Rechtsbehelf, sondern eine freiwillige Prüfung des Auswärtigen Amts. Es bleiben zwei Wege:

- **Neuer Antrag:** Es gibt keine Sperrfrist, du kannst sofort wieder beantragen. Ohne den Ablehnungsgrund auszuräumen, führt das aber meist zum selben Ergebnis. Neuer Termin und neue Gebühr fallen an.
- **Klage beim Verwaltungsgericht Berlin:** innerhalb **eines Monats** ab dem Tag nach der Bekanntgabe; zuständig ist unabhängig vom Antragsland ausschließlich das **VG Berlin**. Das Verfahren dauert Monate und erreicht den Semesterstart selten — sinnvoll bei erkennbar fehlerhaften Bescheiden.

**Praktischer Tipp:** In den meisten Studienfällen ist der neue Antrag der richtige Weg. Wie du den Ablehnungsgrund schließt, was du mit der Hochschule klären solltest und wie der Klageweg praktisch abläuft: [Was tun nach einer Ablehnung](/de/blog/what-to-do-after-german-student-visa-refusal-remonstration-appeal-guide-de).
MD;

        $patches = [
            // TR
            'germany-student-visa-2026-application-steps-documents-rejection' => [
                'section' => ['/### Remonstration \(Dilekçe\).*?Bekleme süresi yok\./su', $trNew],
                'strings' => [
                    'Reddedilirsen dilekçe (Remonstration) hakkın var.'
                        => 'Remonstration (itiraz) usulü 1 Temmuz 2025\'te kaldırıldı; reddedilirsen yeni başvuru veya Berlin İdare Mahkemesi\'nde dava yolu kaldı.',
                    'Remonstration için de ek ücret yok.'
                        => 'Yeni başvuru yaparsan harcı yeniden ödersin.',
                ],
            ],
            // DE
            'germany-student-visa-2026-application-steps-documents-rejection-de' => [
                'section' => ['/### Remonstration \(Einspruch\).*?Es gibt keine Wartezeit\./su', $deNew],
                'strings' => [
                    'Bei Ablehnung hast du das Recht auf Remonstration.'
                        => 'Die Remonstration wurde zum 1. Juli 2025 abgeschafft; bei Ablehnung bleiben ein neuer Antrag oder die Klage beim VG Berlin.',
                    'Für die Remonstration fallen keine zusätzlichen Gebühren an.'
                        => 'Bei einem neuen Antrag fällt die Gebühr erneut an.',
                ],
            ],
            // EN (yalnızca giriş cümlesi)
            'germany-student-visa-2026-application-steps-documents-rejection-en' => [
                'section' => null,
                'strings' => [
                    'If rejected, you have the right to appeal (Remonstration).'
                        => 'Remonstration was abolished on 1 July 2025; if you are rejected, the remaining routes are a new application or a claim before the Berlin Administrative Court.',
                ],
            ],
        ];

        foreach ($patches as $slug => $patch) {
            $post = Post::where('slug', $slug)->first();
            if (! $post) {
                continue;
            }

            $md = $post->content_md;
            $before = $md;

            if ($patch['section']) {
                [$pattern, $replacement] = $patch['section'];
                $md = preg_replace($pattern, $replacement, $md, 1);
            }

            foreach ($patch['strings'] as $old => $new) {
                $md = str_replace($old, $new, $md);
            }

            if ($md === $before) {
                continue;
            }

            $html = Str::markdown($md, ['html_input' => 'allow', 'allow_unsafe_links' => false]);
            $post->update([
                'content_md' => $md,
                'content_html' => $html,
                'reading_minutes' => max(1, (int) round(str_word_count(strip_tags($html)) / 200)),
            ]);
        }
    }

    public function down(): void
    {
        // Geri alma yok: eski metin kaldırılmış bir usulü yürürlükteymiş gibi anlatıyordu.
    }
};
