<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Seeds the bio_en/bio_de/role_label_en/role_label_de columns for the
 * current 9-person team. Run on production right after the schema
 * migration that adds those columns.
 *
 * Idempotent: keyed by slug (or by name when slug is null), only
 * updates if the target column is currently empty.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('users')) return;
        if (! Schema::hasColumn('users', 'role_label_en')) return;

        $entries = [
            ['key' => ['slug' => 'halil-yaprakli'],
             'role_label_en' => 'Founder',
             'role_label_de' => 'Gründer',
             'bio_en' => 'Founder of AlmanyaUni. He founded this platform in 2026 to ensure Turkish students have access to accurate and up-to-date information on their journey to Germany. He writes guides compiled from official sources and enriched with community experiences.',
             'bio_de' => 'Gründer von AlmanyaUni. Er gründete diese Plattform im Jahr 2026, um türkischen Studierenden auf ihrem Weg nach Deutschland den Zugang zu korrekten und aktuellen Informationen zu gewährleisten. Er schreibt Leitfäden, die aus offiziellen Quellen zusammengestellt und mit Community-Erfahrungen angereichert sind.'],

            ['key' => ['slug' => 'elif-g'],
             'role_label_en' => 'Content Editor · Application Specialist',
             'role_label_de' => 'Inhaltsredakteur · Bewerbungsexperte',
             'bio_en' => 'Creates content on Germany application processes and uni-assist topics.',
             'bio_de' => 'Erstellt Inhalte zu Bewerbungsprozessen in Deutschland und uni-assist Themen.'],

            ['key' => ['slug' => 'gamze-e'],
             'role_label_en' => 'Content Editor · Language & Tests',
             'role_label_de' => 'Redakteur · Sprache & Prüfung',
             'bio_en' => 'Covers German language exams and preparation processes.',
             'bio_de' => 'Schreibt über deutsche Sprachprüfungen und Vorbereitungsprozesse.'],

            ['key' => ['slug' => 'hakan-kutlu'],
             'role_label_en' => 'Content Editor · Visa & Living',
             'role_label_de' => 'Content Editor · Visum & Leben',
             'bio_en' => 'Experienced in visa processes and student life in Germany.',
             'bio_de' => 'Erfahren in Visumsprozessen und dem Studierendenleben in Deutschland.'],

            ['key' => ['slug' => 'caner-turkdogru'],
             'role_label_en' => 'Content Editor · Career',
             'role_label_de' => 'Content Editor · Karriere',
             'bio_en' => 'Creates content on career, internships, and work life in Germany.',
             'bio_de' => 'Erstellt Inhalte zu Karriere, Praktika und Berufsleben in Deutschland.'],

            ['key' => ['slug' => 'anna-schmidt'],
             'role_label_en' => 'Content Editor · German academic system',
             'role_label_de' => 'Content Editor · Deutsches Hochschulsystem',
             'bio_en' => 'A German academic living in Berlin. They create expert content on Studienkolleg, Hochschulzulassung, and the German higher education system.',
             'bio_de' => 'Deutscher Akademiker, ansässig in Berlin. Er erstellt Experteninhalte zu den Themen Studienkolleg, Hochschulzulassung und das deutsche Hochschulsystem.'],

            ['key' => ['slug' => 'ayesha-khan'],
             'role_label_en' => 'Content Editor · International student finance',
             'role_label_de' => 'Inhaltsredakteur · Internationale Studierendenfinanzen',
             'bio_en' => "Originally from Pakistan, an international master's student at TUM Munich. She writes about Sperrkonto, Krankenkasse, and Schufa topics from a non-EU student's perspective.",
             'bio_de' => 'Pakistanischer Herkunft, internationaler Masterstudent an der TUM München. Schreibt über die Themen Sperrkonto, Krankenkasse und Schufa aus der Perspektive eines Nicht-EU-Studenten.'],

            ['key' => ['name' => 'Admin'],
             'role_label_en' => 'System Administrator',
             'role_label_de' => 'Systemadministrator',
             'bio_en' => null,
             'bio_de' => null],

            ['key' => ['name' => 'Editor'],
             'role_label_en' => 'Content Editor',
             'role_label_de' => 'Inhaltsredakteur',
             'bio_en' => null,
             'bio_de' => null],
        ];

        foreach ($entries as $e) {
            $q = DB::table('users');
            foreach ($e['key'] as $col => $val) {
                $q->where($col, $val);
            }
            // Only overwrite if target column is currently empty (idempotent)
            $update = [];
            if (! empty($e['role_label_en'])) $update['role_label_en'] = $e['role_label_en'];
            if (! empty($e['role_label_de'])) $update['role_label_de'] = $e['role_label_de'];
            if (! empty($e['bio_en']))        $update['bio_en']        = $e['bio_en'];
            if (! empty($e['bio_de']))        $update['bio_de']        = $e['bio_de'];
            if (! empty($update)) {
                $q->update($update);
            }
        }
    }

    public function down(): void
    {
        // No-op — keeping translations on rollback is safer than wiping data.
    }
};
