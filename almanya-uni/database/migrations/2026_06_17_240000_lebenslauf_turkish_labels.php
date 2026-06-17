<?php

use App\Models\DocumentTemplate;
use Illuminate\Database\Migrations\Migration;

/**
 * CV (Lebenslauf) şablonu form etiketleri Almanca-Türkçe karışıktı: gövdedeki 27
 * token'ın yalnızca 7'sinin Türkçe label_tr'si vardı; kalan 20'si etiketsiz olduğu için
 * form token adını "insanlaştırıp" Almanca gösteriyordu (SCHULE→"Schule"). Eksik 20
 * token'a Türkçe etiket ekle. Placeholder token'ları ([SCHULE] vb.) ve Almanca CV
 * gövdesi (body_de) korunur — sadece TR form etiketleri düzelir. Idempotent.
 */
return new class extends Migration
{
    public function up(): void
    {
        $add = [
            ['key' => 'ADRESSE',              'label_tr' => 'Adres (sokak, no)',           'label_en' => 'Address',        'label_de' => 'Adresse'],
            ['key' => 'PLZ',                  'label_tr' => 'Posta kodu',                   'label_en' => 'Postal code',    'label_de' => 'PLZ'],
            ['key' => 'STADT',                'label_tr' => 'Şehir',                        'label_en' => 'City',           'label_de' => 'Stadt'],
            ['key' => 'TELEFON',              'label_tr' => 'Telefon',                      'label_en' => 'Phone',          'label_de' => 'Telefon'],
            ['key' => 'EMAIL',                'label_tr' => 'E-posta',                      'label_en' => 'Email',          'label_de' => 'E-Mail'],
            ['key' => 'GEBURTSORT',           'label_tr' => 'Doğum yeri',                   'label_en' => 'Place of birth', 'label_de' => 'Geburtsort'],
            ['key' => 'STAATSANGEHOERIGKEIT', 'label_tr' => 'Uyruk',                        'label_en' => 'Nationality',    'label_de' => 'Staatsangehörigkeit'],
            ['key' => 'VON',                  'label_tr' => 'Başlangıç (ay/yıl)',           'label_en' => 'From',           'label_de' => 'Von'],
            ['key' => 'BIS',                  'label_tr' => 'Bitiş (ay/yıl veya “halen”)',  'label_en' => 'To',             'label_de' => 'Bis'],
            ['key' => 'SCHWERPUNKT',          'label_tr' => 'Uzmanlık / odak alanı',        'label_en' => 'Focus',          'label_de' => 'Schwerpunkt'],
            ['key' => 'ABSCHLUSS',            'label_tr' => 'Derece / mezuniyet',           'label_en' => 'Degree',         'label_de' => 'Abschluss'],
            ['key' => 'SCHULE',               'label_tr' => 'Okul / lise adı',              'label_en' => 'School',         'label_de' => 'Schule'],
            ['key' => 'SCHULABSCHLUSS',       'label_tr' => 'Lise diploması / derecesi',    'label_en' => 'School qualification', 'label_de' => 'Schulabschluss'],
            ['key' => 'POSITION',             'label_tr' => 'Pozisyon / görev',             'label_en' => 'Position',       'label_de' => 'Position'],
            ['key' => 'UNTERNEHMEN',          'label_tr' => 'Şirket / firma',               'label_en' => 'Company',        'label_de' => 'Unternehmen'],
            ['key' => 'ORT',                  'label_tr' => 'Yer / şehir',                  'label_en' => 'Location',       'label_de' => 'Ort'],
            ['key' => 'TAETIGKEIT_KURZ',      'label_tr' => 'Görev (kısa açıklama)',        'label_en' => 'Brief activity', 'label_de' => 'Tätigkeit (kurz)'],
            ['key' => 'ENGLISCH_NIVEAU',      'label_tr' => 'İngilizce seviyen (örn. C1)',  'label_en' => 'English level',  'label_de' => 'Englisch-Niveau'],
            ['key' => 'EDV_KENNTNISSE',       'label_tr' => 'Bilgisayar / yazılım bilgisi', 'label_en' => 'IT skills',      'label_de' => 'EDV-Kenntnisse'],
            ['key' => 'DATUM',                'label_tr' => 'Tarih',                        'label_en' => 'Date',           'label_de' => 'Datum'],
        ];

        $t = DocumentTemplate::where('slug', 'lebenslauf')->first();
        if (! $t) {
            return;
        }

        $ph = is_array($t->placeholders) ? $t->placeholders : [];
        $have = array_column($ph, 'key');
        foreach ($add as $a) {
            if (! in_array($a['key'], $have, true)) {
                $ph[] = $a;
            }
        }
        $t->placeholders = $ph;
        $t->save();
    }

    public function down(): void
    {
        // Etiket eklemesi — geri alınmaz (Türkçe etiketler kalıcı).
    }
};
