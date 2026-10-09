<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Program Source & Verification Layer V1.
 *
 * IMPORT kaynağı (programın sisteme nereden geldiği) mevcut alanlarda kalır: programs.source, source_url,
 * last_synced_at (= veri çekildiği an). Bu migration DOĞRULAMA katmanını ekler:
 *  - programs: official_program_url (üniversitenin kendi program sayfası), application_method, application_url,
 *    uni_assist_required / vpd_required ('yes' | 'no' | NULL = bilinmiyor — NULL asla "hayır" değildir).
 *  - program_verifications: alan bazında doğrulama kaydı (alan + aday grubu + dönem başına bir satır).
 * Mevcut programlara hiçbir değer yazılmaz; hiçbir kayıt otomatik VERIFIED olmaz. Additive + idempotent.
 */
return new class extends Migration
{
    public function up(): void
    {
        // programs büyük (FULLTEXT'li) bir tablo: her sütun ayrı ALTER olursa MySQL tabloyu her seferinde kopyalar.
        // Eksik sütunlar TEK ifadeyle eklenir (tek kopya) — deploy sırasındaki HTTP migrate'te zaman aşımı riski düşer.
        $cols = [
            'official_program_url' => 'VARCHAR(500) NULL',
            'application_method' => 'VARCHAR(32) NULL',
            'application_url' => 'VARCHAR(500) NULL',
            'uni_assist_required' => 'VARCHAR(8) NULL',
            'vpd_required' => 'VARCHAR(8) NULL',
        ];
        $missing = array_filter($cols, fn ($c) => ! Schema::hasColumn('programs', $c), ARRAY_FILTER_USE_KEY);
        if ($missing) {
            if (DB::getDriverName() === 'mysql') {
                DB::statement('ALTER TABLE `programs` '.implode(', ', array_map(fn ($c, $def) => "ADD COLUMN `{$c}` {$def}", array_keys($missing), $missing)));
            } else {
                Schema::table('programs', function (Blueprint $t) use ($missing) {
                    foreach ($missing as $c => $def) {
                        $t->string($c, (int) filter_var($def, FILTER_SANITIZE_NUMBER_INT))->nullable();
                    }
                });
            }
        }

        if (! Schema::hasTable('program_verifications')) {
            Schema::create('program_verifications', function (Blueprint $t) {
                $t->id();
                $t->foreignId('program_id')->constrained('programs')->cascadeOnDelete();
                $t->string('field', 32);                       // identity|language|application_method|uni_assist|vpd|deadline|tuition|requirements
                $t->string('applicant_group', 64)->default(''); // '' = tüm adaylar / belirtilmemiş
                $t->string('term', 32)->default('');            // örn. 'WS 2027/28'; '' = döneme bağlı değil
                $t->string('status', 16)->default('unverified'); // unverified|verified|needs_review|conflict
                $t->text('source_value')->nullable();           // kaynağın söylediği değer (insan-okur)
                $t->string('program_value_fingerprint', 40)->nullable(); // doğrulama anında programdaki değerin parmak izi
                $t->string('source_url', 500)->nullable();
                $t->text('evidence')->nullable();               // kaynaktaki ilgili kısa bölüm
                $t->date('checked_at')->nullable();             // kaynağın kontrol edildiği gün
                $t->timestamp('verified_at')->nullable();       // yalnız VERIFIED olduğunda; sync yenilemez
                $t->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
                $t->string('verified_via', 32)->nullable();     // admin | pilot
                $t->string('review_reason', 255)->nullable();
                $t->timestamps();

                $t->unique(['program_id', 'field', 'applicant_group', 'term'], 'program_verifications_scope_unique');
                $t->index(['status']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('program_verifications');
        Schema::table('programs', function (Blueprint $t) {
            foreach (['vpd_required', 'uni_assist_required', 'application_url', 'application_method', 'official_program_url'] as $c) {
                if (Schema::hasColumn('programs', $c)) {
                    $t->dropColumn($c);
                }
            }
        });
    }
};
