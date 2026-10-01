<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * KfW-Studienkredit (174) + Bildungskredit (173) faiz güncellemesi — 01.10.2026 (öğrenci kredisi rehberi TR/EN/DE,
 * yalnız content_md).
 *
 * Resmî kaynak (02.10.2026'da kontrol edildi): KfW Konditionenanzeiger "Stand: 01.10.2026" → KfW-Studienkredit 174,
 * Variabler Zins: 6,20 % Sollzins (6,38 % effektiv); Bildungskredit 173, Variabler Zins: 4,14 % (4,09 % effektiv;
 * resmî tabloda efektif < nominal, öyle bırakıldı); ikisi de gültig ab 01.10.2026. KfW Q&A "Zinsen des
 * KfW-Studienkredits per 01.10.2026": 6,53 → 6,38 % eff. p.a., halbjährlich zum 1. April und 1. Oktober.
 * KfW önceki dönemi (01.04.2026: 6,34 % / 6,53 % eff.) metinde yalnız "önceki dönem" olarak etiketli kalır;
 * Bildungskredit'in eski oranı (3,57 % / 3,53 %) tarihsel bağlamda gerekmediği için tamamen kaldırıldı.
 *
 * Kapsam: KfW faiz kutusu, karşılaştırma paragrafı, değişken-faiz riski cümlesi, tablo KfW hücresi, SSS cevabı,
 * kaynak 3/4 etiketleri; Bildungskredit faiz kutusu (+ [4] Konditionenanzeiger atfı) ve tablo hücresi (kullanıcı
 * onayı 02.10.2026). Bilinçli olarak DOKUNULMAYAN: genel "Stand: 26.09.2026" uyarı kutusu, excerpt/meta (faiz oranı
 * içermiyor), slug/başlık, diğer finansman içeriği.
 *
 * Motor: her düzenleme tam alt-metin (old → new). Ön kontrol yazmadan önce üç dilin hepsinde: kayıt slug + locale +
 * translation_group_id ile bulunmalı; her old tam 1 kez ve new 0 kez (bekleyen) ya da old 0 / new 1 kez (uygulanmış)
 * olmalı. Bekleyen + uygulanmış karışık durum, eksik kayıt ya da beklenmeyen sayım → RuntimeException, hiçbir dile
 * yazılmaz. Yazım tek transaction (Post::saving content_html'i yeniden üretir) + transaction içi doğrulama; ikinci
 * çalıştırma no-op. PHPUnit altında (boş test DB) up() atlanır; motor testte run() ile sınanır.
 */
return new class extends Migration
{
    public const GROUP = '054874dc-fc54-481b-ba30-d060d837e5a1';

    public const SLUGS = [
        'tr' => 'student-loans-and-study-financing-in-germany',
        'en' => 'student-loans-and-study-financing-in-germany-en',
        'de' => 'student-loans-and-study-financing-in-germany-de',
    ];

    public const EDITS = [
        'tr' => [
            ['> **KfW Studienkredit faizi — Stand: 26.09.2026, geçerlilik başlangıcı: 01.04.2026**
> Nominal: **%6,34** · Efektif: **%6,53** [\\[3\\]](#content-kaynaklar)[\\[4\\]](#content-kaynaklar)
> 1 Ekim 2026\'dan itibaren geçerli olacak değişken faiz, bu yazının hazırlandığı tarihte henüz doğrulanamadı. Güncel oranı mutlaka KfW\'nin resmi sayfasından kontrol edin.',
             '> **KfW Studienkredit faizi — Stand: 02.10.2026, geçerlilik başlangıcı: 01.10.2026**
> Nominal: **%6,20** · Efektif: **%6,38** [\\[3\\]](#content-kaynaklar)[\\[4\\]](#content-kaynaklar)
> Bu oran değişkendir: KfW faizi her yıl 1 Nisan ve 1 Ekim\'de yeniden belirler; bir sonraki dönemde düşebilir de yükselebilir de. Güncel oranı mutlaka KfW\'nin resmi sayfasından kontrol edin.'],
            ['Karşılaştırma için: 1 Ekim 2025\'ten itibaren geçerli efektif faiz %6,04 idi [\\[3\\]](#content-kaynaklar). Yani oran altı ay içinde yarım puana yakın değişti.',
             'Karşılaştırma için: 1 Nisan 2026\'dan itibaren geçerli önceki oran nominal %6,34, efektif %6,53; 1 Ekim 2025\'ten itibaren geçerli efektif faiz ise %6,04 idi [\\[3\\]](#content-kaynaklar). Yani oran bir yıl içinde önce yükseldi, sonra yeniden düştü.'],
            ['KfW Studienkredit\'in efektif faizi 1 Ekim 2025\'ten itibaren %6,04 iken, 1 Nisan 2026\'dan itibaren %6,53 oldu [\\[3\\]](#content-kaynaklar).',
             'KfW Studienkredit\'in efektif faizi 1 Ekim 2025\'ten itibaren %6,04 iken 1 Nisan 2026\'dan itibaren %6,53\'e çıktı, 1 Ekim 2026\'dan itibaren ise %6,38\'e indi [\\[3\\]](#content-kaynaklar).'],
            ['Değişken; %6,34 nominal / %6,53 efektif (Stand 26.09.2026, 01.04.2026\'dan beri)',
             'Değişken; %6,20 nominal / %6,38 efektif (Stand 02.10.2026, 01.10.2026\'dan beri)'],
            ['26.09.2026 itibarıyla, 01.04.2026\'dan beri geçerli oran nominal %6,34, efektif %6,53\'tür. Faiz değişkendir ve normalde her yıl 1 Nisan ve 1 Ekim\'de yeniden belirlenir. 1 Ekim 2026\'dan itibaren geçerli oranı KfW\'nin resmi sayfasından kontrol edin.',
             '01.10.2026\'dan itibaren geçerli oran nominal %6,20, efektif %6,38\'dir (önceki dönem: nominal %6,34, efektif %6,53). Faiz değişkendir ve normalde her yıl 1 Nisan ve 1 Ekim\'de yeniden belirlenir; bir sonraki dönemde değişebilir. Güncel oranı KfW\'nin resmi sayfasından kontrol edin.'],
            ['3. KfW, Fragen und Antworten zum KfW-Studienkredit (faiz 01.04.2026)',
             '3. KfW, Fragen und Antworten zum KfW-Studienkredit (faiz 01.10.2026)'],
            ['4. KfW, Konditionenanzeiger (26.09.2026\'da kontrol edildi)',
             '4. KfW, Konditionenanzeiger (02.10.2026\'da kontrol edildi)'],
            ['> **Bildungskredit faizi — Stand: 26.09.2026, geçerlilik başlangıcı: 01.04.2026**
> Nominal: **%3,57** · Efektif: **%3,53** [\\[5\\]](#content-kaynaklar)',
             '> **Bildungskredit faizi — Stand: 02.10.2026, geçerlilik başlangıcı: 01.10.2026**
> Nominal: **%4,14** · Efektif: **%4,09** [\\[4\\]](#content-kaynaklar)[\\[5\\]](#content-kaynaklar)'],
            ['Değişken; %3,57 nominal / %3,53 efektif (Stand 26.09.2026, 01.04.2026\'dan beri)',
             'Değişken; %4,14 nominal / %4,09 efektif (Stand 02.10.2026, 01.10.2026\'dan beri)'],
        ],
        'en' => [
            ['> **KfW Studienkredit interest rate: Stand 26.09.2026, valid from 01.04.2026**
> Nominal: **6.34%** · Effective: **6.53%** [\\[3\\]](#content-sources)[\\[4\\]](#content-sources)
> The variable rate applying from 1 October 2026 had not been verified when this guide was written. Always check the current rate on KfW\'s official website.',
             '> **KfW Studienkredit interest rate: Stand 02.10.2026, valid from 01.10.2026**
> Nominal: **6.20%** · Effective: **6.38%** [\\[3\\]](#content-sources)[\\[4\\]](#content-sources)
> This rate is variable: KfW resets it every 1 April and 1 October, and the next rate can be lower or higher. Always check the current rate on KfW\'s official website.'],
            ['For comparison: the effective rate from 1 October 2025 was 6.04% [\\[3\\]](#content-sources). So the rate moved by about half a percentage point within six months.',
             'For comparison: the previous rate, valid from 1 April 2026, was 6.34% nominal and 6.53% effective, and the effective rate from 1 October 2025 was 6.04% [\\[3\\]](#content-sources). So within a year the rate first rose and then fell again.'],
            ['The effective rate for the KfW Studienkredit was 6.04% from 1 October 2025 and 6.53% from 1 April 2026 [\\[3\\]](#content-sources).',
             'The effective rate for the KfW Studienkredit was 6.04% from 1 October 2025, rose to 6.53% from 1 April 2026 and fell to 6.38% from 1 October 2026 [\\[3\\]](#content-sources).'],
            ['Variable; 6.34% nominal / 6.53% effective (Stand 26.09.2026, valid from 01.04.2026)',
             'Variable; 6.20% nominal / 6.38% effective (Stand 02.10.2026, valid from 01.10.2026)'],
            ['As of 26.09.2026, the rate valid from 01.04.2026 is 6.34% nominal and 6.53% effective. The rate is variable and is normally reset every 1 April and 1 October. Check KfW\'s official website for the rate applying from 1 October 2026.',
             'The rate valid from 01.10.2026 is 6.20% nominal and 6.38% effective (previous period: 6.34% nominal, 6.53% effective). The rate is variable and is normally reset every 1 April and 1 October, so it can change at the next reset. Check KfW\'s official website for the current rate.'],
            ['3. KfW, Fragen und Antworten zum KfW-Studienkredit (rate from 01.04.2026)',
             '3. KfW, Fragen und Antworten zum KfW-Studienkredit (rate from 01.10.2026)'],
            ['4. KfW, Konditionenanzeiger (checked 26.09.2026)',
             '4. KfW, Konditionenanzeiger (checked 02.10.2026)'],
            ['> **Bildungskredit interest rate: Stand 26.09.2026, valid from 01.04.2026**
> Nominal: **3.57%** · Effective: **3.53%** [\\[5\\]](#content-sources)',
             '> **Bildungskredit interest rate: Stand 02.10.2026, valid from 01.10.2026**
> Nominal: **4.14%** · Effective: **4.09%** [\\[4\\]](#content-sources)[\\[5\\]](#content-sources)'],
            ['Variable; 3.57% nominal / 3.53% effective (Stand 26.09.2026, valid from 01.04.2026)',
             'Variable; 4.14% nominal / 4.09% effective (Stand 02.10.2026, valid from 01.10.2026)'],
        ],
        'de' => [
            ['> **Zinssatz KfW-Studienkredit – Stand: 26.09.2026, gültig ab 01.04.2026**
> Sollzins: **6,34 %** · Effektivzins: **6,53 %** [\\[3\\]](#content-quellen)[\\[4\\]](#content-quellen)
> Der ab 1. Oktober 2026 geltende variable Zinssatz war bei Redaktionsschluss noch nicht bestätigt. Prüfen Sie den aktuellen Zinssatz immer auf der offiziellen Website der KfW.',
             '> **Zinssatz KfW-Studienkredit – Stand: 02.10.2026, gültig ab 01.10.2026**
> Sollzins: **6,20 %** · Effektivzins: **6,38 %** [\\[3\\]](#content-quellen)[\\[4\\]](#content-quellen)
> Der Zinssatz ist variabel: Die KfW legt ihn jeweils zum 1. April und 1. Oktober neu fest; der nächste Zinssatz kann niedriger oder höher sein. Prüfen Sie den aktuellen Zinssatz immer auf der offiziellen Website der KfW.'],
            ['Zum Vergleich: Ab dem 1. Oktober 2025 lag der Effektivzins bei 6,04 % [\\[3\\]](#content-quellen). Innerhalb von sechs Monaten hat sich der Zins also um rund einen halben Prozentpunkt verändert.',
             'Zum Vergleich: Der vorherige, ab 1. April 2026 gültige Zins lag bei 6,34 % Sollzins und 6,53 % effektiv; ab dem 1. Oktober 2025 lag der Effektivzins bei 6,04 % [\\[3\\]](#content-quellen). Innerhalb eines Jahres ist der Zins also erst gestiegen und dann wieder gesunken.'],
            ['Der Effektivzins des KfW-Studienkredits lag ab 1. Oktober 2025 bei 6,04 % und ab 1. April 2026 bei 6,53 % [\\[3\\]](#content-quellen).',
             'Der Effektivzins des KfW-Studienkredits lag ab 1. Oktober 2025 bei 6,04 %, stieg ab 1. April 2026 auf 6,53 % und sank ab 1. Oktober 2026 auf 6,38 % [\\[3\\]](#content-quellen).'],
            ['Variabel; 6,34 % Sollzins / 6,53 % effektiv (Stand 26.09.2026, gültig ab 01.04.2026)',
             'Variabel; 6,20 % Sollzins / 6,38 % effektiv (Stand 02.10.2026, gültig ab 01.10.2026)'],
            ['Stand 26.09.2026 beträgt der seit 01.04.2026 gültige Zins 6,34 % Sollzins und 6,53 % effektiv. Der Zins ist variabel und wird in der Regel zum 1. April und 1. Oktober angepasst. Den ab 1. Oktober 2026 geltenden Zinssatz finden Sie auf der offiziellen Website der KfW.',
             'Der seit 01.10.2026 gültige Zins beträgt 6,20 % Sollzins und 6,38 % effektiv (vorherige Periode: 6,34 % Sollzins, 6,53 % effektiv). Der Zins ist variabel und wird in der Regel zum 1. April und 1. Oktober angepasst; er kann sich also beim nächsten Termin ändern. Den aktuellen Zinssatz finden Sie auf der offiziellen Website der KfW.'],
            ['3. KfW, Fragen und Antworten zum KfW-Studienkredit (Zins ab 01.04.2026)',
             '3. KfW, Fragen und Antworten zum KfW-Studienkredit (Zins ab 01.10.2026)'],
            ['4. KfW, Konditionenanzeiger (abgerufen am 26.09.2026)',
             '4. KfW, Konditionenanzeiger (abgerufen am 02.10.2026)'],
            ['> **Zinssatz Bildungskredit – Stand: 26.09.2026, gültig ab 01.04.2026**
> Sollzins: **3,57 %** · Effektivzins: **3,53 %** [\\[5\\]](#content-quellen)',
             '> **Zinssatz Bildungskredit – Stand: 02.10.2026, gültig ab 01.10.2026**
> Sollzins: **4,14 %** · Effektivzins: **4,09 %** [\\[4\\]](#content-quellen)[\\[5\\]](#content-quellen)'],
            ['Variabel; 3,57 % Sollzins / 3,53 % effektiv (Stand 26.09.2026, gültig ab 01.04.2026)',
             'Variabel; 4,14 % Sollzins / 4,09 % effektiv (Stand 02.10.2026, gültig ab 01.10.2026)'],
        ],
    ];

    public function up(): void
    {
        if (app()->runningUnitTests()) {
            return;
        }

        Log::info('KfW/Bildungskredit faiz 01.10.2026: '.$this->run());
    }

    /** Ön kontrol + yazım. Dönüş: "applied: …" ya da "noop". Sorun varsa RuntimeException (yazım yok). */
    public function run(array $edits = self::EDITS): string
    {
        $problems = [];
        $plan = [];
        $states = ['pending' => 0, 'applied' => 0];

        foreach (self::SLUGS as $loc => $slug) {
            $posts = Post::where('slug', $slug)->where('locale', $loc)->get();
            if ($posts->count() !== 1) {
                $problems[] = "{$loc}: {$slug} kaydı {$posts->count()} adet (1 bekleniyordu)";
                continue;
            }
            $post = $posts->first();
            if ($post->translation_group_id !== self::GROUP) {
                $problems[] = "{$loc}: çeviri grubu beklenen değil ({$post->translation_group_id})";
                continue;
            }

            $md = (string) $post->content_md;
            foreach ($edits[$loc] ?? [] as $i => [$old, $new]) {
                $o = substr_count($md, $old);
                $n = substr_count($md, $new);
                if ($o === 1 && $n === 0) {
                    $states['pending']++;
                } elseif ($o === 0 && $n === 1) {
                    $states['applied']++;
                } else {
                    $problems[] = "{$loc} #{$i}: beklenmeyen durum (eski {$o}, yeni {$n})";
                }
            }
            $plan[$loc] = $post;
        }

        if (! $problems && $states['pending'] > 0 && $states['applied'] > 0) {
            $problems[] = "kısmen uygulanmış durum ({$states['applied']} uygulanmış, {$states['pending']} bekleyen)";
        }
        if ($problems) {
            throw new RuntimeException('KfW/Bildungskredit faiz 01.10.2026: ön kontrol başarısız, hiçbir şey yazılmadı. '.implode(' | ', $problems));
        }
        if ($states['pending'] === 0) {
            return 'noop';
        }

        DB::transaction(function () use ($plan, $edits) {
            foreach ($plan as $loc => $post) {
                $md = (string) $post->content_md;
                foreach ($edits[$loc] as [$old, $new]) {
                    $md = str_replace($old, $new, $md);
                }
                $post->content_md = $md;   // Post::saving: content_html + reading_minutes
                $post->save();

                $fresh = Post::findOrFail($post->id);
                foreach ($edits[$loc] as $i => [$old, $new]) {
                    if (substr_count((string) $fresh->content_md, $old) !== 0 || substr_count((string) $fresh->content_md, $new) !== 1) {
                        throw new RuntimeException("KfW/Bildungskredit faiz 01.10.2026: yazım sonrası doğrulama başarısız ({$loc} #{$i}), geri alındı.");
                    }
                }
                if (! $fresh->content_html) {
                    throw new RuntimeException("KfW/Bildungskredit faiz 01.10.2026: {$loc} content_html boş, geri alındı.");
                }
            }
        });

        return 'applied: '.implode(', ', array_map(fn ($l) => $l.' '.count($edits[$l]), array_keys($plan)));
    }

    public function down(): void
    {
        // Bilinçli olarak boş: forward-only içerik düzeltmesi.
    }
};
