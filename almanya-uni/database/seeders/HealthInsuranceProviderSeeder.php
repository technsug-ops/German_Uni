<?php

namespace Database\Seeders;

use App\Models\HealthInsuranceProvider;
use Illuminate\Database\Seeder;

/**
 * Almanya öğrenci sağlık sigortası sağlayıcıları (kıyas aracı verisi).
 *
 * Fiyatlar 2025/26 dönemi için yaklaşık değerlerdir. GKV (gesetzlich) öğrenci
 * tarifesi devletçe düzenlenir → tüm public kasalarda neredeyse aynıdır; fark
 * servis/uygulama/İngilizce destek/ek avantajlardadır. Expat/incoming fiyatları
 * sağlayıcıya göre değişir. last_verified_at ile işaretli; view'da disclaimer var.
 *
 * Idempotent: updateOrCreate(slug) → tekrar çalıştırmak güvenli.
 */
class HealthInsuranceProviderSeeder extends Seeder
{
    public function run(): void
    {
        $verified = '2026-06-10';

        $providers = [
            // ─── PUBLIC / GESETZLICH (GKV) ────────────────────────────────
            [
                'slug' => 'tk-techniker',
                'name' => 'Techniker Krankenkasse (TK)',
                'type' => 'public',
                'website_url' => 'https://www.tk.de/en',
                'monthly_fee_eur' => 136.00,
                'age_limit' => 30,
                'accepted_for_visa' => true,
                'accepted_for_enrollment' => true,
                'covers_dental' => true,
                'covers_pregnancy' => true,
                'covers_mental_health' => true,
                'covers_repatriation' => false,
                'digital_signup' => true,
                'english_support' => true,
                'supported_languages' => ['de', 'en'],
                'best_for' => 'public_standard',
                'description_en' => 'Germany’s largest public health fund and the most popular choice among international students. Full statutory coverage accepted for both the visa and university enrolment.',
                'description_de' => 'Deutschlands größte gesetzliche Krankenkasse und die beliebteste Wahl internationaler Studierender. Voller gesetzlicher Schutz, anerkannt für Visum und Immatrikulation.',
                'description_tr' => 'Almanya’nın en büyük yasal (gesetzlich) sağlık kasası ve uluslararası öğrencilerin en çok tercih ettiği seçenek. Hem vize hem üniversite kaydı için geçerli tam kapsam.',
                'pros' => ['Üniversite kaydı için geçerli (zorunlu sigorta)', 'İngilizce uygulama ve destek hattı', 'Diş + hamilelik + ruh sağlığı kapsamı', 'En geniş anlaşmalı doktor ağı'],
                'cons' => ['30 yaş / 14. dönem üstü için geçerli değil', 'Aylık ücret expat sigortasından yüksek'],
                'features' => ['TK-App (EN)', 'Elektronik sağlık kartı', 'Aile sigortası seçeneği'],
                'is_featured' => true,
                'sort_order' => 1,
            ],
            [
                'slug' => 'aok',
                'name' => 'AOK',
                'type' => 'public',
                'website_url' => 'https://www.aok.de/pk/international-students/',
                'monthly_fee_eur' => 135.00,
                'age_limit' => 30,
                'accepted_for_visa' => true,
                'accepted_for_enrollment' => true,
                'covers_dental' => true,
                'covers_pregnancy' => true,
                'covers_mental_health' => true,
                'digital_signup' => true,
                'english_support' => true,
                'supported_languages' => ['de', 'en'],
                'best_for' => 'public_standard',
                'description_en' => 'A regional network of statutory health funds with strong local branch presence — handy for in-person help in your university city.',
                'description_de' => 'Ein regionales Netz gesetzlicher Krankenkassen mit starker Filialpräsenz vor Ort — praktisch für persönliche Hilfe in deiner Uni-Stadt.',
                'description_tr' => 'Güçlü yerel şube ağına sahip bölgesel yasal sağlık kasaları — üniversite şehrinde yüz yüze destek için pratik.',
                'pros' => ['Üniversite kaydı için geçerli', 'Her şehirde fiziksel şube', 'Diş + hamilelik kapsamı'],
                'cons' => ['Bölgeye göre hizmet değişir', '30 yaş üstü için geçerli değil'],
                'features' => ['Yerel şube ağı', 'Online kayıt'],
                'sort_order' => 2,
            ],
            [
                'slug' => 'barmer',
                'name' => 'BARMER',
                'type' => 'public',
                'website_url' => 'https://www.barmer.de/en',
                'monthly_fee_eur' => 136.00,
                'age_limit' => 30,
                'accepted_for_visa' => true,
                'accepted_for_enrollment' => true,
                'covers_dental' => true,
                'covers_pregnancy' => true,
                'covers_mental_health' => true,
                'digital_signup' => true,
                'english_support' => true,
                'supported_languages' => ['de', 'en'],
                'best_for' => 'public_standard',
                'description_en' => 'One of the largest statutory funds, partner of several student-services bundles (e.g. Fintiba). Full coverage for visa and enrolment.',
                'description_de' => 'Eine der größten gesetzlichen Kassen, Partner mehrerer Studierenden-Pakete (z. B. Fintiba). Voller Schutz für Visum und Immatrikulation.',
                'description_tr' => 'En büyük yasal kasalardan biri; çeşitli öğrenci paketlerinin (ör. Fintiba) sigorta ortağı. Vize ve kayıt için tam kapsam.',
                'pros' => ['Üniversite kaydı için geçerli', 'Fintiba paketiyle entegre', 'İngilizce destek'],
                'cons' => ['30 yaş üstü için geçerli değil'],
                'features' => ['BARMER-App', 'Bonus programı'],
                'sort_order' => 3,
            ],

            // ─── EXPAT / INCOMING ─────────────────────────────────────────
            [
                'slug' => 'dr-walter-provisit',
                'name' => 'DR-WALTER (PROVISIT / EDUCARE24)',
                'type' => 'expat',
                'website_url' => 'https://www.dr-walter.com/en/',
                'monthly_fee_eur' => 33.00,
                'monthly_fee_max_eur' => 79.00,
                'accepted_for_visa' => true,
                'accepted_for_enrollment' => false,
                'covers_dental' => true,
                'covers_pregnancy' => false,
                'covers_mental_health' => false,
                'covers_repatriation' => true,
                'digital_signup' => true,
                'english_support' => true,
                'supported_languages' => ['de', 'en'],
                'best_for' => 'pre_enrollment',
                'description_en' => 'Affordable incoming insurance widely accepted for the visa during the language-course / Studienkolleg / application phase, before you can join a public fund.',
                'description_de' => 'Günstige Incoming-Versicherung, breit anerkannt fürs Visum während der Sprachkurs- / Studienkolleg- / Bewerbungsphase, bevor eine gesetzliche Kasse möglich ist.',
                'description_tr' => 'Yasal kasaya geçmeden önceki dil kursu / Studienkolleg / başvuru aşamasında vize için geniş kabul gören, uygun fiyatlı incoming sigorta.',
                'pros' => ['Aylık ~€33’ten başlar', 'Vize için kabul edilir', 'Dil kursu/Studienkolleg dönemi için ideal', 'Ülke dışına çıkış (repatriation) kapsamı'],
                'cons' => ['Üniversite kaydı için TEK BAŞINA geçmez (public’e geçilir)', 'Hamilelik genelde kapsam dışı', 'Ön mevcut durumlarda kısıtlama'],
                'features' => ['Online anında poliçe', 'Çok dilli'],
                'sort_order' => 10,
            ],
            [
                'slug' => 'mawista',
                'name' => 'MAWISTA',
                'type' => 'expat',
                'website_url' => 'https://www.mawista.com/en/',
                'monthly_fee_eur' => 32.00,
                'monthly_fee_max_eur' => 98.00,
                'accepted_for_visa' => true,
                'accepted_for_enrollment' => false,
                'covers_dental' => true,
                'covers_pregnancy' => true,
                'covers_repatriation' => true,
                'digital_signup' => true,
                'english_support' => true,
                'supported_languages' => ['de', 'en'],
                'best_for' => 'non_eu_incoming',
                'description_en' => 'Flexible incoming/expat plans for newcomers, language students and guest researchers. Monthly cancellation; popular for the pre-enrolment window.',
                'description_de' => 'Flexible Incoming-/Expat-Tarife für Neuankömmlinge, Sprachschüler und Gastforschende. Monatlich kündbar; beliebt für die Phase vor der Immatrikulation.',
                'description_tr' => 'Yeni gelenler, dil öğrencileri ve misafir araştırmacılar için esnek incoming/expat tarifeleri. Aylık iptal; kayıt öncesi dönem için popüler.',
                'pros' => ['Esnek, aylık iptal', 'Hamilelik kapsamı (üst tarife)', 'Uygun fiyat'],
                'cons' => ['Üniversite kaydı için tek başına geçmez', 'Kapsam tarifeye göre değişir'],
                'features' => ['Online başvuru', 'Misafir araştırmacı tarifesi'],
                'sort_order' => 11,
            ],
            [
                'slug' => 'care-concept',
                'name' => 'Care Concept',
                'type' => 'expat',
                'website_url' => 'https://www.care-concept.de/en/',
                'monthly_fee_eur' => 29.00,
                'monthly_fee_max_eur' => 89.00,
                'accepted_for_visa' => true,
                'accepted_for_enrollment' => false,
                'covers_dental' => true,
                'covers_repatriation' => true,
                'digital_signup' => true,
                'english_support' => true,
                'supported_languages' => ['de', 'en'],
                'best_for' => 'pre_enrollment',
                'description_en' => 'Budget incoming cover (Care College / Visum) for the visa and the first months before switching to a statutory fund.',
                'description_de' => 'Preiswerter Incoming-Schutz (Care College / Visum) fürs Visum und die ersten Monate vor dem Wechsel in eine gesetzliche Kasse.',
                'description_tr' => 'Vize ve yasal kasaya geçmeden önceki ilk aylar için ekonomik incoming kapsam (Care College / Visum).',
                'pros' => ['En düşük başlangıç fiyatı (~€29)', 'Vize için kabul edilir', 'Hızlı online poliçe'],
                'cons' => ['Üniversite kaydı için tek başına geçmez', 'Kapsam temel düzeyde'],
                'features' => ['Care College tarifesi', 'Anında belge'],
                'sort_order' => 12,
            ],

            // ─── PRIVATE (PKV) ────────────────────────────────────────────
            [
                'slug' => 'ottonova',
                'name' => 'ottonova',
                'type' => 'private',
                'website_url' => 'https://www.ottonova.de/en',
                'monthly_fee_eur' => 170.00,
                'monthly_fee_max_eur' => 300.00,
                'age_limit' => null,
                'accepted_for_visa' => true,
                'accepted_for_enrollment' => false,
                'covers_dental' => true,
                'covers_pregnancy' => true,
                'covers_mental_health' => true,
                'covers_repatriation' => false,
                'digital_signup' => true,
                'english_support' => true,
                'supported_languages' => ['de', 'en'],
                'best_for' => 'over_30',
                'description_en' => 'Fully digital private health insurer (PKV) in English — a strong fit for students over 30, PhD candidates and scholarship holders who cannot join a public fund.',
                'description_de' => 'Vollständig digitale private Krankenversicherung (PKV) auf Englisch — gut geeignet für Studierende über 30, Promovierende und Stipendiat:innen ohne Zugang zur GKV.',
                'description_tr' => 'Tamamen dijital, İngilizce özel sağlık sigortası (PKV) — yasal kasaya giremeyen 30 yaş üstü öğrenciler, doktora adayları ve burslular için güçlü seçenek.',
                'pros' => ['30 yaş üstü / doktora / burslu için uygun', 'Tamamen dijital, İngilizce', 'Geniş özel kapsam (diş, ruh sağlığı, hamilelik)'],
                'cons' => ['Public’e göre pahalı', 'Üniversite kaydında public muafiyeti gerekir', 'PKV’den GKV’ye geri dönüş zordur'],
                'features' => ['İngilizce dijital uygulama', 'Hızlı geri ödeme', 'Doktora/araştırmacı tarifeleri'],
                'sort_order' => 20,
            ],
        ];

        foreach ($providers as $p) {
            $p['is_published'] = $p['is_published'] ?? true;
            $p['last_verified_at'] = $verified;
            HealthInsuranceProvider::updateOrCreate(['slug' => $p['slug']], $p);
        }
    }
}
