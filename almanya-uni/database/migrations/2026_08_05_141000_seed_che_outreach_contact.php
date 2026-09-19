<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * CHE veri talebi: mektup şablon olarak panele girer (Mail Şablonları) ve CHE
 * kurumsal kontak defterine eklenir. Metnin kaynağı doc/CHE-DATENANFRAGE.md.
 */
return new class extends Migration
{
    private const TEMPLATE_KEY = 'data-request-che-de';

    public function up(): void
    {
        $now = Carbon::now();

        $body = <<<'TXT'
Sehr geehrte Damen und Herren,

wir betreiben ApplyToGerman (applytogerman.com), eine mehrsprachige
Studienorientierungsplattform, die internationale Studieninteressierte — mit einem
Schwerpunkt auf Bewerberinnen und Bewerbern aus der Türkei — bei der Auswahl eines
Studienstandorts in Deutschland unterstützt. Unsere Datenbank umfasst derzeit rund
480 Hochschulen und etwa 7.000 Studiengänge; die Inhalte werden auf Türkisch, Englisch
und Deutsch bereitgestellt.

Wir stellen unseren Nutzerinnen und Nutzern fachbezogene Hochschulübersichten zur
Verfügung. Bislang stützen sich diese auf die Gesamtplatzierung der Hochschule in den
internationalen Rankings (QS, THE, ARWU). Dieses Vorgehen benachteiligt systematisch die
Hochschulen für angewandte Wissenschaften: Da sie in den globalen Rankings praktisch
nicht vertreten sind, erhalten sie in unserer Bewertung keinen Qualitätswert — obwohl
sie gerade in den ingenieurwissenschaftlichen Fächern für unsere Zielgruppe besonders
relevant sind.

Das CHE Hochschulranking ist unseres Wissens die einzige Quelle, die fachbezogen sowohl
Universitäten als auch Fachhochschulen abbildet. Wir möchten daher anfragen, unter
welchen Bedingungen eine Lizenzierung der Ranking-Daten für unsere Plattform möglich ist.

Konkret wären für uns folgende Angaben relevant:

  - Hochschulkennung (bevorzugt die HRK-Nummer)
  - Fach sowie Hochschultyp (Universität / Fachhochschule) und Abschlussart
  - Zuordnung zur Spitzen-, Mittel- oder Schlussgruppe je Indikator
  - Erhebungsjahr

Selbstverständlich würden wir die Daten ausschließlich mit klarer Quellenangabe und
Verlinkung auf das CHE Hochschulranking bzw. HeyStudium verwenden. Eine vollständige
Wiedergabe der Ranking-Tabellen ist ausdrücklich nicht beabsichtigt; die Angaben sollen
als ein Kriterium unter mehreren in unsere fachbezogenen Übersichten einfließen, jeweils
mit Hinweis auf die Methodik des CHE.

Gerne würden wir erfahren:

  1. ob eine Lizenzierung für diesen Anwendungsfall grundsätzlich möglich ist,
  2. in welchem Format die Daten bereitgestellt werden können (Datei, Schnittstelle),
  3. welche Konditionen und Aktualisierungsintervalle vorgesehen sind.

Für Rückfragen stehen wir jederzeit zur Verfügung und würden uns über eine Rückmeldung
sehr freuen.

Mit freundlichen Grüßen

{{sender_name}}
ApplyToGerman
partnerships@applytogerman.com
https://applytogerman.com
TXT;

        if (Schema::hasTable('email_templates')) {
            DB::table('email_templates')->updateOrInsert(
                ['key' => self::TEMPLATE_KEY],
                [
                    'name'       => 'Veri Talebi — CHE Hochschulranking (DE)',
                    'category'   => 'partnership',
                    'locale'     => 'de',
                    'subject'    => 'Anfrage zur Lizenzierung von CHE-Ranking-Daten für eine Studienorientierungsplattform',
                    'body'       => $body,
                    'is_active'  => true,
                    'sort_order' => 10,
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
        }

        if (Schema::hasTable('outreach_contacts')) {
            DB::table('outreach_contacts')->updateOrInsert(
                ['email' => 'info@che.de'],
                [
                    'organization'     => 'CHE Centrum für Hochschulentwicklung gGmbH',
                    'contact_name'     => null,
                    'website'          => 'https://www.che.de',
                    'category'         => 'data_provider',
                    'status'           => 'new',
                    'priority'         => 'high',
                    'next_followup_at' => $now->copy()->addDays(14)->toDateString(),
                    'notes'            => "Konu bazlı üniversite sıralaması verisi (CHE Hochschulranking) lisans talebi.\n"
                        . "Mektup: Mail Şablonları > \"Veri Talebi — CHE Hochschulranking (DE)\"; kaynak doc/CHE-DATENANFRAGE.md.\n"
                        . "Neden önemli: alan sıralamalarımızın kalite bileşeni genel dünya sırası → Fachhochschule'ler sıfır alıyor.\n"
                        . "İstenen alanlar: HRK-Nummer (bizdeki hs_nummer ile birebir), fach, hochschultyp, abschlussart, Spitzen-/Mittel-/Schlussgruppe, yıl.\n"
                        . "Alternatif adresler: ranking@che.de, methodik.che-ranking.de iletişim formu.\n"
                        . "Cevap gelirse veri şeması hazır: university_subject_ranks (tier + rank_low/rank_high).",
                    'updated_at'       => $now,
                    'created_at'       => $now,
                ]
            );
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('email_templates')) {
            DB::table('email_templates')->where('key', self::TEMPLATE_KEY)->delete();
        }

        if (Schema::hasTable('outreach_contacts')) {
            DB::table('outreach_contacts')->where('email', 'info@che.de')->delete();
        }
    }
};
