<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Localizes blog category + FAQ topic names (were TR-only → leaked Turkish on
 * EN/DE pages) and fills EN/DE for the pricing MenuPage label/description.
 *
 * Adds name_tr/name_en/name_de to categories + faq_topics; name_tr = existing
 * `name` so the LocalizableContent accessor resolves TR correctly. Idempotent.
 */
return new class extends Migration
{
    public function up(): void
    {
        foreach (['categories', 'faq_topics'] as $table) {
            if (! Schema::hasTable($table)) continue;
            Schema::table($table, function (Blueprint $t) use ($table) {
                foreach (['name_tr', 'name_en', 'name_de'] as $col) {
                    if (! Schema::hasColumn($table, $col)) $t->string($col)->nullable()->after('name');
                }
            });
            // name_tr = existing TR name (only where empty)
            DB::statement("UPDATE `$table` SET name_tr = name WHERE (name_tr IS NULL OR name_tr = '')");
        }

        $categories = [
            "Almanya'da Eğitim" => ['en' => 'Studying in Germany', 'de' => 'Studium in Deutschland'],
            'Finans'            => ['en' => 'Finance',             'de' => 'Finanzen'],
            'Vize'              => ['en' => 'Visa',                'de' => 'Visum'],
            'Dil & Sınavlar'    => ['en' => 'Language & Exams',    'de' => 'Sprache & Prüfungen'],
            'Başvuru'           => ['en' => 'Application',         'de' => 'Bewerbung'],
            'Öğrenci Hayatı'    => ['en' => 'Student Life',        'de' => 'Studierendenleben'],
        ];
        // Schema column adds above are the critical part (guarded); data fills below
        // are wrapped so a failed UPDATE never aborts the migrate chain.
        try {
        foreach ($categories as $tr => $v) {
            DB::table('categories')->where('name', $tr)->update(['name_en' => $v['en'], 'name_de' => $v['de']]);
        }

        $topics = [
            'Vize'              => ['en' => 'Visa',                    'de' => 'Visum'],
            'Dil'               => ['en' => 'Language',                'de' => 'Sprache'],
            'Master & PhD'      => ['en' => 'Master & PhD',            'de' => 'Master & Promotion'],
            'Uni-Assist'        => ['en' => 'Uni-Assist',              'de' => 'Uni-Assist'],
            'Studienkolleg'     => ['en' => 'Studienkolleg',           'de' => 'Studienkolleg'],
            'Yurt & Konaklama'  => ['en' => 'Housing & Accommodation', 'de' => 'Wohnen & Unterkunft'],
            'Para & Finansman'  => ['en' => 'Money & Finance',         'de' => 'Geld & Finanzen'],
            'Şehir & Hayat'     => ['en' => 'City & Life',             'de' => 'Stadt & Leben'],
            'İş & Werkstudent'  => ['en' => 'Work & Werkstudent',      'de' => 'Job & Werkstudent'],
            'Sigorta'           => ['en' => 'Insurance',               'de' => 'Versicherung'],
            'Randevu'           => ['en' => 'Appointment',             'de' => 'Termin'],
            'Anmeldung'         => ['en' => 'Registration (Anmeldung)', 'de' => 'Anmeldung'],
            'Burs'              => ['en' => 'Scholarship',             'de' => 'Stipendium'],
            'Diploma Denkliği'  => ['en' => 'Diploma Recognition',     'de' => 'Diplomanerkennung'],
        ];
        foreach ($topics as $tr => $v) {
            DB::table('faq_topics')->where('name', $tr)->update(['name_en' => $v['en'], 'name_de' => $v['de']]);
        }

        // Pricing MenuPage: fill EN/DE label + description (accessor reads label_{locale}).
        if (Schema::hasTable('menu_pages')) {
            DB::table('menu_pages')->where('key', 'pricing')->update([
                'label_en'       => 'Premium',
                'label_de'       => 'Premium',
                'description_en' => 'Free + Premium memberships',
                'description_de' => 'Kostenlose + Premium-Mitgliedschaften',
            ]);
        }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('localize_taxonomy_names data fill: ' . $e->getMessage());
        }
    }

    public function down(): void
    {
        // No-op (localized columns/values are additive and safe to keep).
    }
};
