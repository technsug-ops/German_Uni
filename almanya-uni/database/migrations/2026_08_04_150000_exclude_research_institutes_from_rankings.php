<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Üniversite olmayan kurumları sıralamaların dışına çıkarır.
 *
 * SORUN: universities tablosunda Max Planck Enstitüleri, Helmholtz ve Leibniz merkezleri
 * gibi ARAŞTIRMA kurumları var (2026-08-04 taramasında 18'i aktif). Bunlar üniversite
 * değil ama "En İyi X Üniversiteleri" / "En Büyük Üniversiteler" listelerinde çıkıyorlar.
 *
 * NEDEN SİLMİYORUZ: taşıdıkları 2-3'er program gerçek doktora programları (IMPRS gibi);
 * silmek/pasifleştirmek gerçek içerik kaybettirir. Bu yüzden kayıt ve programları KALIYOR,
 * yalnızca sıralamalardan hariç tutuluyorlar (RankingService::baseQuery).
 *
 * AYRI DURUM — Informationszentrum für Fremdsprachenforschung: üniversite değil, programı
 * yok ve 20.532'lik öğrenci sayısı hatalı (büyük olasılıkla Marburg'dan yanlış eşleşmiş).
 * Bu sayı yüzünden "En Büyük Üniversiteler" listesinde görünüyordu → is_active=0.
 *
 * ID kullanılmadı; ID'ler ortamdan ortama değişir, ad kalıbıyla eşleştirildi.
 */
return new class extends Migration
{
    /** Üniversite olmayan araştırma kurumu ad kalıpları. */
    private const RESEARCH_PATTERNS = [
        'Max Planck%', 'Max-Planck%', 'The Max Planck%',
        'Helmholtz%', 'Helmholtz-Zentrum%',
        'Leibniz Institute%', 'Leibniz-Institut%',
        'Fraunhofer%',
    ];

    public function up(): void
    {
        if (!Schema::hasColumn('universities', 'exclude_from_rankings')) {
            Schema::table('universities', function (Blueprint $table) {
                $table->boolean('exclude_from_rankings')
                    ->default(false)
                    ->after('is_active')
                    ->comment('Üniversite değil (araştırma kurumu) — sıralamalarda gösterilmez');
                $table->index('exclude_from_rankings');
            });
        }

        $flagged = 0;
        foreach (self::RESEARCH_PATTERNS as $pattern) {
            $flagged += DB::table('universities')
                ->where(function ($q) use ($pattern) {
                    $q->where('name_de', 'like', $pattern)
                      ->orWhere('name_en', 'like', $pattern);
                })
                ->update(['exclude_from_rankings' => true]);
        }

        $deactivated = DB::table('universities')
            ->where(function ($q) {
                $q->where('name_de', 'like', 'Informationszentrum für Fremdsprachenforschung%')
                  ->orWhere('name_en', 'like', 'Informationszentrum für Fremdsprachenforschung%');
            })
            ->update(['is_active' => false, 'exclude_from_rankings' => true]);

        // Sıralama listesi 6 saat cache'li — yeni filtre hemen görünsün.
        foreach (['tr', 'en', 'de', 'ar'] as $locale) {
            cache()->forget('rankings.all.' . $locale);
        }

        echo "exclude_from_rankings işaretlenen: {$flagged} · pasifleştirilen: {$deactivated}\n";
    }

    public function down(): void
    {
        // is_active geri açılmıyor — hangi kayıtların zaten pasif olduğu bilinmiyor,
        // körlemesine açmak başka kayıtları diriltebilir.
        if (Schema::hasColumn('universities', 'exclude_from_rankings')) {
            Schema::table('universities', function (Blueprint $table) {
                $table->dropIndex(['exclude_from_rankings']);
                $table->dropColumn('exclude_from_rankings');
            });
        }
    }
};
