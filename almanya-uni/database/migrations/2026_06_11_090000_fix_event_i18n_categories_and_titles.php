<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Events i18n temizliği:
 * 1) event_categories.name_de kolonu + 7 kategoriye EN/DE ad (name_en boştu →
 *    chip'ler EN/DE'de bile TR görünüyordu).
 * 2) AI düşünce sızıntılı title_de'ler ("Gedanken: Der Nutzer möchte...",
 *    "THINK: The user wants...") düzeltildi (Gemini thinking truncation artığı).
 */
return new class extends Migration
{
    private array $cats = [
        'networking'         => ['en' => 'Networking & Career',  'de' => 'Networking & Karriere'],
        'skill'              => ['en' => 'Skill Building',        'de' => 'Kompetenzaufbau'],
        'peer-learning'      => ['en' => 'Community & Meetup',    'de' => 'Community & Kennenlernen'],
        'personal-growth'    => ['en' => 'Personal Growth',       'de' => 'Persönliche Entwicklung'],
        'adventure'          => ['en' => 'Adventure & Social',    'de' => 'Abenteuer & Soziales'],
        'industry-immersion' => ['en' => 'Industry Insights',     'de' => 'Brancheneinblicke'],
        'special-format'     => ['en' => 'Special Format',        'de' => 'Spezialformat'],
    ];

    private array $titleFix = [
        "Sperrkonto + Vize Başvuru Workshop'u" => 'Sperrkonto + Visumantrag-Workshop',
        'Public Speaking Master Class'         => 'Public Speaking Masterclass',
    ];

    public function up(): void
    {
        if (! Schema::hasColumn('event_categories', 'name_de')) {
            Schema::table('event_categories', function (Blueprint $t) {
                $t->string('name_de')->nullable()->after('name_en');
            });
        }

        $now = now();
        foreach ($this->cats as $slug => $n) {
            DB::table('event_categories')->where('slug', $slug)->update([
                'name_en'    => $n['en'],
                'name_de'    => $n['de'],
                'updated_at' => $now,
            ]);
        }

        // Bilinen bozuk başlıklar → doğru Almanca (kaynak içerik env'ler arası aynı → title_tr ile eşle).
        foreach ($this->titleFix as $tr => $de) {
            DB::table('events')->where('title_tr', $tr)->update(['title_de' => $de, 'updated_at' => $now]);
        }

        // Güvenlik ağı: kalan AI-düşünce sızıntılı title_de'leri null'la → accessor title_en/tr'ye düşer.
        foreach (['Gedanken:%', 'THINK:%', '%Der Nutzer möchte%', '%The user wants%'] as $pat) {
            DB::table('events')->where('title_de', 'like', $pat)->update(['title_de' => null, 'updated_at' => $now]);
        }
    }

    public function down(): void
    {
        // name_de kolonu + veri geri alınmıyor (veri düzeltmesi); kolon kalsın zararsız.
    }
};
