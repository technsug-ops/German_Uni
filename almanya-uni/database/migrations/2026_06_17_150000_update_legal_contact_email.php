<?php

use App\Models\LegalPage;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Prod'da legal sayfalar DB'de (seeder prod'da çalışmaz). Görünür iletişim
     * e-postasını tek adrese taşı: admin@applytogerman.com. Cerrahi string-replace —
     * tüm gövde/açıklama metnini ezmez, sadece eski e-postaları değiştirir, böylece
     * admin'den yapılmış olası elle düzenlemeler korunur.
     */
    public function up(): void
    {
        $from = ['info@almanyauni.com', 'info@applytogerman.com'];
        $to = 'admin@applytogerman.com';

        foreach (LegalPage::all() as $page) {
            $dirty = false;

            foreach (['bodies', 'descriptions'] as $field) {
                $values = $page->$field;
                if (! is_array($values)) {
                    continue;
                }
                foreach ($values as $locale => $text) {
                    if (! is_string($text)) {
                        continue;
                    }
                    $new = str_replace($from, $to, $text);
                    if ($new !== $text) {
                        $values[$locale] = $new;
                        $dirty = true;
                    }
                }
                if ($dirty) {
                    $page->$field = $values;
                }
            }

            if ($dirty) {
                $page->save(); // saved() event clears the legal_page cache
            }
        }
    }

    public function down(): void
    {
        // İletişim e-postası birleştirme — geri alınmaz (eski karışık adresler bilinçli terk edildi).
    }
};
