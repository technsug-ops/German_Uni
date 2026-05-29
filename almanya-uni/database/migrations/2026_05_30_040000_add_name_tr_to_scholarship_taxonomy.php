<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = ['scholarship_statuses', 'scholarship_subject_groups', 'scholarship_intentions'];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && ! Schema::hasColumn($table, 'name_tr')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->string('name_tr')->nullable()->after('name_en');
                });
            }
        }

        $statuses = [
            1 => 'Lisans öğrencileri',
            2 => 'Doktora sonrası araştırmacılar',
            3 => 'Mezunlar',
            4 => 'Doktora adayları / Doktora öğrencileri',
            5 => 'Öğretim üyeleri',
        ];
        foreach ($statuses as $id => $name) {
            DB::table('scholarship_statuses')->where('id', $id)->update(['name_tr' => $name]);
        }

        $subjects = [
            'A' => 'Dil ve Kültür Bilimleri',
            'B' => 'Hukuk, Ekonomi ve Sosyal Bilimler',
            'C' => 'Matematik ve Doğa Bilimleri',
            'D' => 'Tıp',
            'E' => 'Veterinerlik, Tarım, Orman ve Beslenme Bilimleri, Ekoloji',
            'F' => 'Mühendislik',
            'G' => 'Sanat, Müzik ve Spor',
        ];
        foreach ($subjects as $code => $name) {
            DB::table('scholarship_subject_groups')->where('code', $code)->update(['name_tr' => $name]);
        }

        $intentions = [
            1 => 'Eğitim',
            2 => 'Araştırma / Doktora',
            3 => 'Dil Kursu',
            4 => 'Staj',
            5 => 'Öğretmenlik',
        ];
        foreach ($intentions as $id => $name) {
            DB::table('scholarship_intentions')->where('id', $id)->update(['name_tr' => $name]);
        }
    }

    public function down(): void
    {
        foreach (['scholarship_statuses', 'scholarship_subject_groups', 'scholarship_intentions'] as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'name_tr')) {
                Schema::table($table, fn (Blueprint $t) => $t->dropColumn('name_tr'));
            }
        }
    }
};
