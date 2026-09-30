<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Content truth fix — kariyer rehberinin Studienkolleg / dil kursu çalışma bölümü (TR/EN/DE, yalnız 3 blok):
 * student-career-guide-in-germany-work-permits-job-types-salary-deductions[-en|-de]. Chatbot bu bölümden
 * "Studienkolleg/Sprachkurs izinlerinde çalışma kısıtlı; çoğu durumda yalnız tatilde; bazen hiç izin yok" ve
 * "Studienkolleg öğrencileri genellikle Werkstudent olamaz" çekiyordu (§ 16b Abs. 3 ve § 16f Abs. 3 ile çelişki;
 * Werkstudent yasağı doğrulanmadı). Yeni metin: § 16b (Studienkolleg kabulü belgelenince; ilk yıl yasağı yok;
 * 140 Arbeitstage; ≤4 saat yarım gün; ≤20 saatlik ders haftası / ders dışı hafta 2,5 gün; lehe hesap; 20 saat genel
 * tavan değil), § 16f (haftada 20 saate kadar), Werkstudent = sosyal sigorta statüsü (ayrı kural). Başlık, slug,
 * diğer bölümler değişmez. Kaynak: gesetze-im-internet.de §§ 16b, 16f (29.09.2026).
 *
 * Yöntem (her kayıt kararlı kimlikle: posts/faqs = slug+locale, varlıklar = slug):
 *  - md satırları: markdown'dan arındırılmış düz metni canlı sayfadaki blokla BİREBİR eşleşen satır değişir
 *    (liste/başlık/alıntı öneki korunur);
 *  - alanlar (title/question): eski değer birebir doğrulanır;
 *  - JSON content_blocks: eski alt dize içeren tüm metin değerlerinde birebir alt dize değişimi.
 * Ön kontrol yazmadan önce tüm kayıtlarda yapılır; kayıt başına durum "tamamı bekleyen" ya da "tamamı uygulanmış"
 * olmalı; eksik kayıt / eşleşmeyen blok / kısmi durum → RuntimeException, hiçbir şey yazılmaz. Tek transaction.
 * İkinci çalıştırma no-op. PHPUnit altında (boş test DB'si) sorun varsa sessizce çıkar.
 */
return new class extends Migration
{
    public function up(): void
    {
        $spec = json_decode(<<<'JSON'
{
 "records": [
  {
   "table": "posts",
   "slug": "student-career-guide-in-germany-work-permits-job-types-salary-deductions",
   "locale": "tr",
   "md": {
    "content_md": [
     {
      "line": "Studienkolleg (Hazırlık Yılı) Öğrencileri için Farklı Kurallar",
      "replace": "**Studienkolleg ve Dil Kursu Öğrencileri: Çalışma Kuralları**"
     },
     {
      "line": "Hazırlık sınıfında (Studienkolleg) okuyan öğrenciler için durum biraz farklıdır. Genellikle, Studienkolleg süresince veya Almanca kursları (Sprachkurs) için alınan oturum izinlerinde çalışma hakkı kısıtlıdır. Çoğu durumda, bu öğrenciler sadece sömestr tatillerinde veya belirli saatlerle sınırlı olarak çalışabilirler. Hatta bazı durumlarda hiç çalışma izni verilmez.",
      "replace": "Studienkolleg'de ya da bir dil kursundayken ne kadar çalışabileceğin **oturum izninin türüne** bağlıdır. Studienkolleg'e kabulün belgelendiğinde, öğrenime hazırlık tedbiri olarak öğrenim amaçlı oturum (§ 16b AufenthG) kapsamındasın. Bu izinde **ilk yıl için genel bir çalışma yasağı yoktur**: yılda en fazla **140 iş günü** çalışabilirsin (Arbeitstagekonto). 4 saate kadar çalışılan gün yarım gün sayılabilir; ders döneminde en fazla 20 saat çalışılan bir hafta ve ders dönemi dışındaki her hafta 2,5 iş günü sayılabilir; her hafta için lehine olan hesap uygulanır. Yani haftalık 20 saat, bu izinde genel bir oturum tavanı değildir. Öğrenimle bağlantısı olmayan bir **dil kursu** için verilen izin (§ 16f AufenthG) ise **haftada 20 saate kadar** çalışmaya izin verir. Sayım örnekleri: [140 günlük çalışma hesabı](/tr/blog/internship-in-germany-with-b1-b2-german)."
     },
     {
      "line": "Topluluktan gelen bir soruyu yanıtlayalım: \"Hazırlıktayken aldığımız studienbescheinigung ile werkstudent yapabiliyor muyuz?\" Cevap: Hayır, maalesef Studienkolleg öğrencileri genellikle Werkstudent olarak çalışamazlar. Werkstudent statüsü, üniversiteye kayıtlı \"düzenli öğrenci\" (ordentlicher Student) olmak şartını arar. Studienkolleg öğrencileri henüz bu statüde sayılmazlar. Bu nedenle, eğer Studienkolleg'deyken çalışmak istiyorsan, çalışma izninin detaylarını ve sınırlarını mutlaka ikamet ettiğin yerdeki Yabancılar Dairesi (Ausländerbehörde) ile görüşerek teyit etmelisin. Genellikle sadece tatil dönemlerinde veya çok kısıtlı saatlerde, minijob tarzı işlerde çalışmaya izin verilir.",
      "replace": "Topluluktan gelen bir soru: \"Hazırlıktayken aldığımız Studienbescheinigung ile Werkstudent olabilir miyiz?\" **Werkstudent**, sosyal sigortaya ilişkin bir statüdür: ders döneminde düzenli olarak haftada en fazla 20 saat çalışan öğrenci sağlık, bakım ve işsizlik sigortasından muaf olur. Bu, oturum hukukundaki çalışma izniyle aynı kural değildir. Werkstudent statüsünün senin durumuna uygulanıp uygulanamayacağını işverenine ve Krankenkasse'ne sor. Hangi sınırlar içinde çalışabileceğini ise oturum izninin metni belirler; emin değilsen Ausländerbehörde'ye danış."
     }
    ]
   }
  },
  {
   "table": "posts",
   "slug": "student-career-guide-in-germany-work-permits-job-types-salary-deductions-en",
   "locale": "en",
   "md": {
    "content_md": [
     {
      "line": "Different Rules for Studienkolleg (Preparatory Year) Students",
      "replace": "**Studienkolleg and Language-Course Students: Work Rules**"
     },
     {
      "line": "Things are a bit different for students attending a preparatory college (Studienkolleg). Generally, residence permits issued for the duration of a Studienkolleg or for German language courses (Sprachkurs) come with restricted work rights. In most cases, these students can only work during semester breaks or for a limited number of hours. In some situations, they might not be allowed to work at all.",
      "replace": "How much you may work while at a Studienkolleg or on a language course depends on **your type of residence permit**. Once your acceptance at a Studienkolleg is documented, you are covered by the residence permit for study purposes (§ 16b AufenthG) as a study-preparatory measure. This permit has **no general work ban in the first year**: you may work up to **140 working days per year** (Arbeitstagekonto). A day of up to 4 hours can count as half a day; a lecture-period week with no more than 20 hours, and every week outside the lecture period, can count as 2.5 working days; for each week the more favourable count applies. So 20 hours a week is not a general residence-law ceiling for this permit. A permit for a **language course** not linked to studies (§ 16f AufenthG) allows employment of **up to 20 hours per week**. Counting examples: [the 140-day work account](/en/blog/internship-in-germany-with-b1-b2-german-en)."
     },
     {
      "line": "Let's answer a community question: \"Can we work as a Werkstudent with the 'Studienbescheinigung' we get during the preparatory year?\" Answer: Unfortunately, no, Studienkolleg students generally can't work as a Werkstudent. The Werkstudent status requires you to be an \"ordinary student\" (ordentlicher Student) enrolled at a university. Studienkolleg students aren't considered to have this status yet. So, if you're in a Studienkolleg and want to work, you absolutely need to confirm the details and limits of your work permit with your local Foreigners' Office (Ausländerbehörde). Typically, you're only allowed to work during holiday periods or for very limited hours, often in Minijob-style positions.",
      "replace": "A community question: \"Can we become Werkstudenten with the Studienbescheinigung we get during the preparatory year?\" **Werkstudent** is a social-insurance status: a student who regularly works no more than 20 hours a week during the lecture period is exempt from health, long-term care and unemployment insurance. It is not the same rule as the residence-law work permission. Ask your employer and your Krankenkasse whether Werkstudent status can apply in your case. The limits within which you may work are set by the wording of your residence permit; if in doubt, check with the Ausländerbehörde."
     }
    ]
   }
  },
  {
   "table": "posts",
   "slug": "student-career-guide-in-germany-work-permits-job-types-salary-deductions-de",
   "locale": "de",
   "md": {
    "content_md": [
     {
      "line": "Besondere Regeln für Studienkolleg-Studierende",
      "replace": "**Studienkolleg und Sprachkurs: Arbeitsregeln**"
     },
     {
      "line": "Für Studierende, die ein Studienkolleg besuchen, ist die Situation etwas anders. In der Regel sind die Arbeitsrechte bei Aufenthaltserlaubnissen, die für die Dauer eines Studienkollegs oder für Sprachkurse erteilt werden, eingeschränkt. In den meisten Fällen dürfen diese Studierenden nur in den Semesterferien oder mit einer sehr begrenzten Stundenzahl arbeiten. In einigen Fällen ist überhaupt keine Arbeitserlaubnis vorgesehen.",
      "replace": "Wie viel du während des Studienkollegs oder eines Sprachkurses arbeiten darfst, hängt von **deinem Aufenthaltstitel** ab. Ist deine Annahme am Studienkolleg nachgewiesen, fällst du als studienvorbereitende Maßnahme unter die Aufenthaltserlaubnis zum Studium (§ 16b AufenthG). Bei diesem Titel gibt es **kein allgemeines Arbeitsverbot im ersten Jahr**: Du darfst bis zu **140 Arbeitstage im Jahr** arbeiten (Arbeitstagekonto). Ein Tag mit bis zu 4 Stunden kann als halber Tag zählen; eine Woche in der Vorlesungszeit mit höchstens 20 Stunden sowie jede Woche außerhalb der Vorlesungszeit kann als 2,5 Arbeitstage zählen; pro Woche gilt die günstigere Berechnung. 20 Stunden pro Woche sind bei diesem Titel also keine allgemeine aufenthaltsrechtliche Obergrenze. Eine Aufenthaltserlaubnis für einen **Sprachkurs** ohne Studienbezug (§ 16f AufenthG) erlaubt eine Beschäftigung von **bis zu 20 Stunden pro Woche**. Rechenbeispiele: [das 140-Tage-Arbeitskonto](/de/blog/internship-in-germany-with-b1-b2-german-de)."
     },
     {
      "line": "Beantworten wir eine Frage aus der Community: \"Können wir mit der Studienbescheinigung, die wir im Studienkolleg bekommen, als Werkstudent arbeiten?\" Antwort: Nein, leider können Studierende des Studienkollegs in der Regel nicht als Werkstudent arbeiten. Der Werkstudentenstatus setzt voraus, dass du ein \"ordentlicher Student\" an einer Universität bist. Studienkolleg-Studierende gelten noch nicht als in diesem Status. Wenn du also während des Studienkollegs arbeiten möchtest, solltest du die Details und Grenzen deiner Arbeitserlaubnis unbedingt bei der für dich zuständigen Ausländerbehörde klären. Meistens ist nur das Arbeiten in den Ferien oder mit sehr begrenzten Stunden in Minijob-ähnlichen Tätigkeiten erlaubt.",
      "replace": "Eine Frage aus der Community: „Können wir mit der Studienbescheinigung aus dem Studienkolleg als Werkstudent arbeiten?“ **Werkstudent** ist ein Status der Sozialversicherung: Wer in der Vorlesungszeit regelmäßig höchstens 20 Stunden pro Woche arbeitet, ist kranken-, pflege- und arbeitslosenversicherungsfrei. Das ist nicht dieselbe Regel wie die aufenthaltsrechtliche Arbeitserlaubnis. Ob der Werkstudentenstatus in deinem Fall in Frage kommt, klärst du mit deinem Arbeitgeber und deiner Krankenkasse. In welchem Rahmen du arbeiten darfst, ergibt sich aus dem Wortlaut deines Aufenthaltstitels; im Zweifel frag die Ausländerbehörde."
     }
    ]
   }
  }
 ]
}
JSON, true, 512, JSON_THROW_ON_ERROR);

        $problems = [];
        $writes = [];
        $done = [];
        foreach ($spec['records'] as $r) {
            $label = "{$r['table']}:{$r['slug']}".(isset($r['locale']) ? "/{$r['locale']}" : '');
            $q = DB::table($r['table'])->where('slug', $r['slug']);
            if (isset($r['locale'])) {
                $q->where('locale', $r['locale']);
            }
            $rows = $q->get();
            if ($rows->count() !== 1) {
                $problems[] = "{$label}: kayıt sayısı {$rows->count()}";
                continue;
            }
            $row = (array) $rows->first();
            if (isset($r['cluster'])) {
                $group = DB::table('faqs')->where('slug', $r['cluster'])->where('locale', 'tr')->value('translation_group_id');
                if (! $group || $row['translation_group_id'] !== $group) {
                    $problems[] = "{$label}: TR kaydıyla aynı çeviri kümesinde değil";
                    continue;
                }
            }
            $pending = 0;
            $applied = 0;
            $upd = [];

            foreach ($r['md'] ?? [] as $col => $edits) {
                $lines = explode("\n", (string) $row[$col]);
                $changed = false;
                $units = null;
                foreach ($edits as $e) {
                    // $e = {line: canlı bloğun düz metni, subs: [{old,new}]} — blok düz metinle bulunur, değişiklik
                    // yalnız ham markdown'daki alt dizede yapılır (link/biçim korunur). Blok = tek satır, art arda
                    // satırlardan oluşan paragraf (yumuşak satır sonu) ya da tablo hücresi.
                    // {line, replace}: bütün blok yeni markdown ile değişir (önek korunur).
                    $whole = isset($e['replace']);
                    $after = $whole ? $e['replace'] : $e['line'];
                    foreach ($whole ? [] : $e['subs'] as $s) {
                        $after = str_replace($s['old'], $s['new'], $after);
                    }
                    $units ??= $this->units($lines);
                    $old = array_values(array_filter($units, fn ($u) => $u[3] === $this->norm($e['line'])));
                    $new = array_values(array_filter($units, fn ($u) => $u[3] === $this->norm($after)));
                    $rawOk = $old !== [] && array_reduce($old, fn ($ok, $u) => $ok && ($whole
                        ? $u[0] !== 'cell'
                        : array_reduce($e['subs'], fn ($o, $s) => $o && $this->unitHas($lines, $u, $s['old']), true)), true);
                    if (count($old) >= 1 && count($new) === 0 && $rawOk) {
                        $pending++;
                        foreach ($old as [$kind, $start, $len]) {
                            if ($whole) {
                                $cr = str_ends_with($lines[$start], "\r") ? "\r" : '';
                                // ^\s* (0–3 değil): girintili alt liste maddesinin ("    *   ") öneki de korunur
                                preg_match('/^\s*(?:#{1,6}\s+|(?:>\s?)+|[-*+]\s+|\d+[.)]\s+)?/u', $lines[$start], $m);
                                $lines[$start] = $m[0].$e['replace'].$cr;
                                for ($i = $start + 1; $i < $start + $len; $i++) {
                                    $lines[$i] = "\0";   // paragrafın diğer satırları (sonda silinir)
                                }
                            } else {
                                for ($i = $start; $i < $start + $len; $i++) {
                                    foreach ($e['subs'] as $s) {
                                        $lines[$i] = str_replace($s['old'], $s['new'], $lines[$i]);
                                    }
                                }
                            }
                        }
                        $changed = true;
                        $units = null;
                    } elseif (count($old) === 0 && count($new) >= 1) {
                        $applied++;
                    } else {
                        $problems[] = "{$label}: {$col} blok eşleşmedi (eski ".count($old).', yeni '.count($new).', ham alt dize '.($rawOk ? 'var' : 'yok').') «'.mb_substr($e['line'], 0, 70).'»';
                    }
                }
                if ($changed) {
                    $upd[$col] = implode("\n", array_values(array_filter($lines, fn ($l) => $l !== "\0")));
                }
            }

            foreach ($r['fieldsub'] ?? [] as $col => $subs) {
                $cur = (string) $row[$col];
                foreach ($subs as $s) {
                    if (substr_count($cur, $s['old']) === 1 && ! str_contains($cur, $s['new'])) {
                        $pending++;
                        $cur = str_replace($s['old'], $s['new'], $cur);
                        $upd[$col] = $cur;
                    } elseif (! str_contains($cur, $s['old']) && str_contains($cur, $s['new'])) {
                        $applied++;
                    } else {
                        $problems[] = "{$label}: {$col} alt dize eşleşmedi «".mb_substr($s['old'], 0, 60).'»';
                    }
                }
            }

            foreach ($r['rewrite'] ?? [] as $col => $rw) {
                // Tamamen eski kurala dayalı SSS cevabı: eski metin parmak izleriyle doğrulanır, yeni metin bütün yazılır.
                $cur = (string) $row[$col];
                $plain = $this->norm(str_replace("\n", ' ', $cur));
                $fp = array_reduce($rw['fingerprints'], fn ($ok, $f) => $ok && str_contains($plain, $this->norm($f)), true);
                if ($cur === $rw['new']) {
                    $applied++;
                } elseif ($fp) {
                    $pending++;
                    $upd[$col] = $rw['new'];
                } else {
                    $problems[] = "{$label}: {$col} eski metin parmak izleri bulunamadı";
                }
            }

            foreach ($r['fields'] ?? [] as $col => $v) {
                $cur = (string) $row[$col];
                if ($cur === $v['new']) {
                    $applied++;
                } elseif ($cur === $v['old']) {
                    $pending++;
                    $upd[$col] = $v['new'];
                } else {
                    $problems[] = "{$label}: {$col} beklenen eski değerde değil";
                }
            }

            foreach ($r['json'] ?? [] as $col => $subs) {
                $data = json_decode((string) $row[$col], true);
                if (! is_array($data)) {
                    $problems[] = "{$label}: {$col} JSON okunamadı";
                    continue;
                }
                $changed = false;
                $orig = $data;   // durum her alt dize için ORİJİNAL veriye göre belirlenir
                foreach ($subs as $s) {
                    $o = $this->countSub($orig, $s['old']);
                    $n = $this->countSub($orig, $s['new']);
                    if ($o >= 1 && $n === 0) {
                        $pending++;
                        $data = $this->replaceSub($data, $s['old'], $s['new']);
                        $changed = true;
                    } elseif ($o === 0 && $n >= 1) {
                        $applied++;
                    } else {
                        $problems[] = "{$label}: {$col} alt dize eşleşmedi (eski {$o}, yeni {$n}) «".mb_substr($s['old'], 0, 70).'»';
                    }
                }
                if ($changed) {
                    $upd[$col] = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                }
            }

            if ($pending > 0 && $applied > 0) {
                $problems[] = "{$label}: kısmen uygulanmış ({$applied} uygulanmış, {$pending} bekleyen)";
            } elseif ($pending > 0) {
                $writes[] = [$r, $row['id'], $upd];
            } else {
                $done[] = $label;
            }
        }
        if (! $problems && $writes && $done) {
            $problems[] = 'kayıtlar arası kısmi durum: '.count($done).' kayıt uygulanmış, '.count($writes).' bekleyen ('.implode(', ', array_slice($done, 0, 5)).')';
        }

        if ($problems) {
            if (app()->runningUnitTests()) {
                return; // Test DB'si sıfırdan kurulur; bu kayıtların çoğu orada yok. Hiçbir şey yazma.
            }
            throw new RuntimeException('Career guide Studienkolleg fix: ön kontrol başarısız, hiçbir şey yazılmadı. '.implode(' | ', array_slice($problems, 0, 40)));
        }
        if (! $writes) {
            return; // zaten uygulanmış — no-op
        }

        DB::transaction(function () use ($writes) {
            foreach ($writes as [$r, $id, $upd]) {
                if ($r['table'] === 'posts') {
                    $m = \App\Models\Post::findOrFail($id);   // booted(): content_html + reading_minutes
                } elseif ($r['table'] === 'faqs') {
                    $m = \App\Models\Faq::findOrFail($id);    // booted(): answer_html
                } else {
                    DB::table($r['table'])->where('id', $id)->update($upd + ['updated_at' => now()]);
                    continue;
                }
                foreach ($upd as $col => $val) {
                    $m->{$col} = $val;
                }
                $m->save();
            }
        });
    }

    private function countSub($v, string $needle): int
    {
        if (is_array($v)) {
            return array_sum(array_map(fn ($x) => $this->countSub($x, $needle), $v));
        }

        return is_string($v) ? substr_count($v, $needle) : 0;
    }

    private function replaceSub($v, string $old, string $new)
    {
        if (is_array($v)) {
            return array_map(fn ($x) => $this->replaceSub($x, $old, $new), $v);
        }

        return is_string($v) ? str_replace($old, $new, $v) : $v;
    }

    /**
     * Markdown satırlarından sayfadaki bloklara karşılık gelen birimler: [tür, başlangıç, satır sayısı, düz metin].
     * tür: line (tek satır), win (boş satırla kesilmeyen art arda satırlar = yumuşak satır sonlu paragraf),
     * cell (tablo satırındaki hücre).
     */
    private function units(array $lines): array
    {
        $u = [];
        $n = count($lines);
        $blank = fn ($l) => $l === "\0" || trim($l) === '';
        for ($i = 0; $i < $n; $i++) {
            if ($blank($lines[$i])) {
                continue;
            }
            $u[] = ['line', $i, 1, $this->norm($lines[$i])];
            if (str_starts_with(ltrim($lines[$i]), '|')) {
                foreach (explode('|', trim(trim($lines[$i]), '|')) as $cell) {
                    $u[] = ['cell', $i, 1, $this->norm($cell)];
                }
            }
            $txt = $lines[$i];
            for ($j = $i + 1; $j < min($n, $i + 15) && ! $blank($lines[$j]); $j++) {
                $txt .= ' '.$lines[$j];
                $u[] = ['win', $i, $j - $i + 1, $this->norm($txt)];
            }
        }

        return $u;
    }

    private function unitHas(array $lines, array $u, string $needle): bool
    {
        for ($i = $u[1]; $i < $u[1] + $u[2]; $i++) {
            if (str_contains($lines[$i], $needle)) {
                return true;
            }
        }

        return false;
    }

    /** Markdown satırının görünen düz metni (canlı sayfadaki blok metniyle karşılaştırmak için). */
    private function norm(string $s): string
    {
        $s = preg_replace('/^\s*(?:#{1,6}\s+|(?:>\s?)+|[-*+]\s+|\d+[.)]\s+)?/u', '', $s);
        $s = preg_replace('/!\[([^\]]*)\]\([^)]*\)/u', '$1', $s);
        for ($i = 0; $i < 3; $i++) {
            $s = preg_replace('/\[([^\]]*)\]\([^)]*\)/u', '$1', $s);
        }
        $s = preg_replace('/<[^>]+>/u', '', $s);
        $s = str_replace(['**', '__', '*', '`'], '', $s);
        $s = preg_replace('/\\\\([\\\\`*_{}\[\]()#+\-.!>|"\'])/u', '$1', $s);
        $s = html_entity_decode($s, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return trim(preg_replace('/\s+/u', ' ', $s));
    }

    public function down(): void
    {
        // Bilinçli olarak boş: forward-only içerik düzeltmesi.
    }
};
