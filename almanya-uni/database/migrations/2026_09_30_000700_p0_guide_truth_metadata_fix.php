<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * P0 guide truth fix — SEO metadata (000600'ün devamı). 000600 gövdeyi düzeltti; eski framing şu alanlarda kaldı
 * (meta/og/twitter description + JSON-LD description = Post::metaDescriptionResolved(), <title> = meta_title):
 *  - Studienkolleg rehberi (TR/EN/DE): meta_description hâlâ "Türk lise mezunlarının çoğu … geçmeli" → yeni (doğrulanmış) excerpt.
 *  - anabin rehberi (TR/EN/DE): title/meta_title "Türk diploması … sınıflandırılır/classified", excerpt(+meta_description)
 *    eski girişten kesit → H kodları = kurum statüsü; Türk diploması HZB açısından ayrı kurallarla değerlendirilir.
 *  - Studienkolleg merkez listesi (TR/EN/DE): excerpt(+meta_description) "lise diploman yeterli değil" → giriş hakkı yolu.
 * Gövde, slug, published_at dokunulmaz (Post::saving yalnız content_md değişince render eder).
 *
 * Koruma: her alanın eski değeri birebir (\r yok sayılır) eşleşmeli; yeni değere eşitse uygulanmış sayılır. null_ok alanlar
 * (meta_title/meta_description) boşsa dokunulmaz: fallback zaten düzeltilen alana düşer. Eşleşmeyen alan, eksik kayıt,
 * kayıt içi ya da kayıtlar arası kısmi durum → RuntimeException, hiçbir şey yazılmaz. Tek transaction. İkinci çalıştırma no-op.
 */
return new class extends Migration
{
    public function up(): void
    {
        $spec = json_decode(<<<'JSON'
{
 "records": [
  {
   "slug": "what-is-anabin-h-h-h-how-is-a-turkish-diploma",
   "locale": "tr",
   "fields": {
    "title": {
     "old": "Anabin H+, H+-, H- Nedir? Türk Diploması Almanya İçin Nasıl Sınıflandırılır?",
     "new": "Anabin H+, H+/- ve H- Nedir? Türk Diploması ve Almanya'da HZB",
     "null_ok": false
    },
    "meta_title": {
     "old": "Anabin H+, H+-, H- Nedir? Türk Diploması Almanya İçin Nasıl Sınıflandırılır?",
     "new": "Anabin H+, H+/- ve H- Nedir? Türk Diploması ve Almanya'da HZB",
     "null_ok": true
    },
    "excerpt": {
     "old": "Anabin H+, H+-, H- Nedir? Türk Lise Diploması Almanya İçin Nasıl Sınıflandırılır?\n\nAlmanya'da üniversite hayalleri kuran bir Türk öğrenci misin? Başvuru sürecinin en kritik adımlarından biri, lise veya üniversite diplomanızın Almanya'daki denklik dur...",
     "new": "Anabin'deki H+, H+/- ve H- yükseköğretim kurumlarının statüsünü gösterir, lise diplomasını değil. Türk diplomanın Almanya üniversite başvurusunda HZB açısından nasıl değerlendirildiğini öğren.",
     "null_ok": false
    },
    "meta_description": {
     "old": "Anabin H+, H+-, H- Nedir? Türk Lise Diploması Almanya İçin Nasıl Sınıflandırılır?\n\nAlmanya'da üniversite hayalleri kuran bir Türk öğrenci misin? Başvuru sürecinin en kritik adımlarından biri, lise veya üniversite diplomanızın Almanya'daki denklik dur...",
     "new": "Anabin'deki H+, H+/- ve H- yükseköğretim kurumlarının statüsünü gösterir, lise diplomasını değil. Türk diplomanın Almanya üniversite başvurusunda HZB açısından nasıl değerlendirildiğini öğren.",
     "null_ok": true
    }
   }
  },
  {
   "slug": "what-is-anabin-h-h-h-how-is-a-turkish-diploma-en",
   "locale": "en",
   "fields": {
    "title": {
     "old": "What is Anabin H+, H+-, H-? How is a Turkish Diploma Classified for Germany?",
     "new": "What Do Anabin H+, H+/- and H- Mean? Turkish Diplomas and HZB",
     "null_ok": false
    },
    "meta_title": {
     "old": "What is Anabin H+, H+-, H-? How is a Turkish Diploma Classified for Germany?",
     "new": "What Do Anabin H+, H+/- and H- Mean? Turkish Diplomas and HZB",
     "null_ok": true
    },
    "excerpt": {
     "old": "Are you a Turkish student dreaming of university in Germany? One of the most critical steps in the application process is understanding the recognition status of your high school or university diploma in Germany.",
     "new": "In anabin, H+, H+/- and H- describe the status of higher-education institutions, not school certificates. Learn how a Turkish diploma is assessed for HZB and university admission in Germany.",
     "null_ok": false
    },
    "meta_description": {
     "old": "Are you a Turkish student dreaming of university in Germany? One of the most critical steps in the application process is understanding the recognition status of your high school or university diploma in Germany.",
     "new": "In anabin, H+, H+/- and H- describe the status of higher-education institutions, not school certificates. Learn how a Turkish diploma is assessed for HZB and university admission in Germany.",
     "null_ok": true
    }
   }
  },
  {
   "slug": "what-is-anabin-h-h-h-how-is-a-turkish-diploma-de",
   "locale": "de",
   "fields": {
    "title": {
     "old": "Was ist Anabin H+, H+-, H-? Wie wird ein türkisches Diplom für Deutschland klassifiziert?",
     "new": "Anabin H+, H+/- und H-: Was bedeuten sie für türkische Abschlüsse?",
     "null_ok": false
    },
    "meta_title": {
     "old": "Was ist Anabin H+, H+-, H-? Wie wird ein türkisches Diplom für Deutschland klassifiziert?",
     "new": "Anabin H+, H+/- und H-: Was bedeuten sie für türkische Abschlüsse?",
     "null_ok": true
    },
    "excerpt": {
     "old": "Träumst du als türkischer Student von einem Studium in Deutschland? Einer der wichtigsten Schritte im Bewerbungsprozess ist es, den Anerkennungsstatus deines Abitur- oder Hochschulabschlusses in Deutschland zu verstehen.",
     "new": "In anabin beschreiben H+, H+/- und H- den Status von Hochschulen, nicht von Schulabschlüssen. So wird dein türkischer Abschluss für Hochschulzugangsberechtigung und Hochschulzulassung bewertet.",
     "null_ok": false
    },
    "meta_description": {
     "old": "Träumst du als türkischer Student von einem Studium in Deutschland? Einer der wichtigsten Schritte im Bewerbungsprozess ist es, den Anerkennungsstatus deines Abitur- oder Hochschulabschlusses in Deutschland zu verstehen.",
     "new": "In anabin beschreiben H+, H+/- und H- den Status von Hochschulen, nicht von Schulabschlüssen. So wird dein türkischer Abschluss für Hochschulzugangsberechtigung und Hochschulzulassung bewertet.",
     "null_ok": true
    }
   }
  },
  {
   "slug": "studienkolleg-center-list-2026-public-private-institutions",
   "locale": "tr",
   "fields": {
    "excerpt": {
     "old": "Almanya Studienkolleg Merkez Listesi 2026: Devlet ve Özel Seçenekler (Türk Öğrenciler İçin Rehber)\n\nAlmanya'da üniversite okuma hayali kuran bir Türk öğrenci misin? Lise diplomanın Almanya'da doğrudan üniversiteye giriş için yeterli olmadığını öğrend...",
     "new": "Almanya'daki devlet ve özel Studienkolleg merkezleri 2026: şehirler, kurslar ve başvuru şartları. Studienkolleg'e ihtiyacın olup olmadığı lise türüne değil; YKS yerleşmesi ya da önceki öğrenim gibi giriş hakkı yoluna bağlıdır.",
     "null_ok": false
    },
    "meta_description": {
     "old": "Almanya Studienkolleg Merkez Listesi 2026: Devlet ve Özel Seçenekler (Türk Öğrenciler İçin Rehber)\n\nAlmanya'da üniversite okuma hayali kuran bir Türk öğrenci misin? Lise diplomanın Almanya'da doğrudan üniversiteye giriş için yeterli olmadığını öğrend...",
     "new": "Almanya'daki devlet ve özel Studienkolleg merkezleri 2026: şehirler, kurslar ve başvuru şartları. Studienkolleg'e ihtiyacın olup olmadığı lise türüne değil; YKS yerleşmesi ya da önceki öğrenim gibi giriş hakkı yoluna bağlıdır.",
     "null_ok": true
    }
   }
  },
  {
   "slug": "studienkolleg-center-list-2026-public-private-institutions-en",
   "locale": "en",
   "fields": {
    "excerpt": {
     "old": "This 2026 list of Studienkolleg centers in Germany offers public and private options for Turkish students. Are you a Turkish student dreaming of studying at a university in Germany and have learned that your high school diploma isn't directly suffici...",
     "new": "Public and private Studienkolleg centers in Germany for 2026: cities, courses and entry requirements. Whether you need a Studienkolleg depends on your access route (such as YKS placement or prior study), not on your type of school.",
     "null_ok": false
    },
    "meta_description": {
     "old": "This 2026 list of Studienkolleg centers in Germany offers public and private options for Turkish students. Are you a Turkish student dreaming of studying at a university in Germany and have learned that your high school diploma isn't directly suffici...",
     "new": "Public and private Studienkolleg centers in Germany for 2026: cities, courses and entry requirements. Whether you need a Studienkolleg depends on your access route (such as YKS placement or prior study), not on your type of school.",
     "null_ok": true
    }
   }
  },
  {
   "slug": "studienkolleg-center-list-2026-public-private-institutions-de",
   "locale": "de",
   "fields": {
    "excerpt": {
     "old": "Diese Liste der Studienkolleg-Zentren 2026 in Deutschland bietet staatliche und private Optionen für türkische Studenten. Träumen Sie als türkischer Student davon, an einer deutschen Universität zu studieren und haben erfahren, dass Ihr Abitur nicht...",
     "new": "Staatliche und private Studienkollegs in Deutschland 2026: Städte, Kurse und Aufnahmevoraussetzungen. Ob du ein Studienkolleg brauchst, hängt von deinem Zugangsweg (etwa YKS-Zuweisung oder Vorstudium) ab, nicht von der Schulart.",
     "null_ok": false
    },
    "meta_description": {
     "old": "Diese Liste der Studienkolleg-Zentren 2026 in Deutschland bietet staatliche und private Optionen für türkische Studenten. Träumen Sie als türkischer Student davon, an einer deutschen Universität zu studieren und haben erfahren, dass Ihr Abitur nicht...",
     "new": "Staatliche und private Studienkollegs in Deutschland 2026: Städte, Kurse und Aufnahmevoraussetzungen. Ob du ein Studienkolleg brauchst, hängt von deinem Zugangsweg (etwa YKS-Zuweisung oder Vorstudium) ab, nicht von der Schulart.",
     "null_ok": true
    }
   }
  },
  {
   "slug": "studienkolleg-guide-2026-who-needs-it-which-course-which-school",
   "locale": "tr",
   "fields": {
    "meta_description": {
     "old": "Türk lise mezunlarının çoğunun Almanya'ya başlamadan önce geçmesi gereken bir yıllık ön hazırlık programı: Studienkolleg. T-Kurs mı M-Kurs mu, nasıl başvurulur, hangi şehirler ücretsiz — bölüm bazlı tam yol haritası.",
     "new": "Doğrudan giriş hakkı olmayan adayların Almanya'da üniversiteye hazırlandığı bir yıllık program: Studienkolleg; gerekip gerekmediği giriş hakkı yoluna bağlı. T-Kurs mı M-Kurs mu, nasıl başvurulur, hangi şehirler ücretsiz — bölüm bazlı tam yol haritası.",
     "null_ok": false
    }
   }
  },
  {
   "slug": "studienkolleg-guide-2026-who-needs-it-which-course-which-school-en",
   "locale": "en",
   "fields": {
    "meta_description": {
     "old": "A one-year preparatory program that most Turkish high school graduates must complete before starting their studies in Germany: Studienkolleg. This guide covers T-Kurs or M-Kurs, application process, and free cities.",
     "new": "A one-year preparatory programme for applicants without direct access to German higher education: Studienkolleg — whether you need it depends on your access route. This guide covers T-Kurs or M-Kurs, application process, and free cities.",
     "null_ok": false
    }
   }
  },
  {
   "slug": "studienkolleg-guide-2026-who-needs-it-which-course-which-school-de",
   "locale": "de",
   "fields": {
    "meta_description": {
     "old": "Ein einjähriges Vorbereitungsprogramm, das die meisten türkischen Abiturienten vor dem Studium in Deutschland absolvieren müssen: das Studienkolleg. Dieser Leitfaden erklärt T-Kurs oder M-Kurs, Bewerbung und kostenlose Städte.",
     "new": "Ein einjähriges Vorbereitungsprogramm für Bewerber ohne direkten Hochschulzugang in Deutschland: das Studienkolleg – ob du es brauchst, hängt von deinem Zugangsweg ab. Dieser Leitfaden erklärt T-Kurs oder M-Kurs, Bewerbung und kostenlose Städte.",
     "null_ok": false
    }
   }
  }
 ]
}
JSON, true, 512, JSON_THROW_ON_ERROR)['records'];

        $norm = fn ($v) => $v === null ? null : str_replace("\r", '', (string) $v);
        $problems = $writes = $done = [];
        $missing = 0;
        foreach ($spec as $r) {
            $label = "{$r['locale']}:{$r['slug']}";
            $rows = DB::table('posts')->where('slug', $r['slug'])->where('locale', $r['locale'])->get();
            if ($rows->count() !== 1) {
                $missing += $rows->isEmpty() ? 1 : 0;
                $problems[] = "{$label}: kayıt sayısı {$rows->count()}";
                continue;
            }
            $row = $rows->first();
            $upd = [];
            $applied = 0;
            foreach ($r['fields'] as $col => $f) {
                $cur = $norm($row->{$col});
                if ($cur === $f['new']) {
                    $applied++;
                } elseif ($cur === $f['old']) {
                    $upd[$col] = $f['new'];
                } elseif ($f['null_ok'] && ($cur === null || trim($cur) === '')) {
                    // boş: fallback (title / excerpt) zaten düzeltiliyor — dokunma
                } else {
                    $problems[] = "{$label}: {$col} eşleşmedi «".mb_substr((string) $cur, 0, 70).'»';
                }
            }
            if ($upd && $applied) {
                $problems[] = "{$label}: kısmen uygulanmış ({$applied} uygulanmış, ".count($upd).' bekleyen)';
            } elseif ($upd) {
                $writes[] = [$row->id, $upd];
            } else {
                $done[] = $label;
            }
        }
        if (! $problems && $writes && $done) {
            $problems[] = 'kayıtlar arası kısmi durum: '.count($done).' kayıt uygulanmış, '.count($writes).' bekleyen ('.implode(', ', array_slice($done, 0, 5)).')';
        }

        if ($problems) {
            if ($missing === count($spec) && app()->runningUnitTests()) {
                return; // Test DB'si sıfırdan kurulur; bu yazılar orada yok.
            }
            throw new RuntimeException('P0 guide metadata fix: ön kontrol başarısız, hiçbir şey yazılmadı. '.implode(' | ', $problems));
        }
        if (! $writes) {
            return; // zaten uygulanmış — no-op
        }

        DB::transaction(function () use ($writes) {
            foreach ($writes as [$id, $upd]) {
                $m = \App\Models\Post::findOrFail($id);
                foreach ($upd as $col => $val) {
                    $m->{$col} = $val;
                }
                $m->save();
            }
        });
    }

    public function down(): void
    {
        // Bilinçli olarak boş: forward-only içerik düzeltmesi.
    }
};
