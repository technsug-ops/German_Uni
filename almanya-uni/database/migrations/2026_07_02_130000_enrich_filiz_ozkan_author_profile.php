<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;

/**
 * Dr. Filiz Özkan yazar profilini gerçek (LinkedIn-doğrulanmış) bilgilerle zenginleştirir
 * (E-E-A-T). Kişinin izniyle. Prod-only kullanıcı → data-migration ile uygulanır.
 *
 * Kaynak (public LinkedIn): Operations & Education Manager @ euroTech Study GmbH;
 * ML/Python/SQL/Tableau; University of Pittsburgh; Berlin. Uydurma kimlik bilgisi YOK.
 */
return new class extends Migration
{
    public function up(): void
    {
        $u = User::where('slug', 'filiz-ozkan')->first()
            ?? User::where('name', 'like', '%Filiz Özkan%')->first();

        if (! $u) {
            echo "enrich_filiz_ozkan: kullanıcı bulunamadı (prod'da çalıştır)\n";
            return;
        }

        $u->role_label    = 'Eğitim & Operasyon Müdürü, euroTech Study';
        $u->role_label_en = 'Operations & Education Manager, euroTech Study';
        $u->role_label_de = 'Operations- & Bildungsmanagerin, euroTech Study';

        $u->bio = 'Dr. Filiz Özkan, Almanya merkezli teknoloji eğitim kurumu euroTech Study GmbH\'de '
            . 'Eğitim ve Operasyon Müdürü olarak görev yapıyor. Makine öğrenmesi, Python, SQL ve veri '
            . 'görselleştirme (Tableau) alanlarında uzmanlaşan Özkan, University of Pittsburgh mezunudur ve '
            . 'Berlin bölgesinde yaşamaktadır. ApplyToGerman için Almanya\'da eğitim, veri bilimi kariyeri ve '
            . 'başvuru süreçleri hakkında içerik üretiyor.';
        $u->bio_en = 'Dr. Filiz Özkan is the Operations and Education Manager at euroTech Study GmbH, a '
            . 'Germany-based technology education institution. Specializing in machine learning, Python, SQL, '
            . 'and data visualization (Tableau), she is a University of Pittsburgh graduate based in the Berlin '
            . 'area. She writes for ApplyToGerman on studying in Germany, data science careers, and the '
            . 'application process.';
        $u->bio_de = 'Dr. Filiz Özkan ist Operations- und Bildungsmanagerin bei der euroTech Study GmbH, einer '
            . 'technologieorientierten Bildungseinrichtung in Deutschland. Spezialisiert auf Machine Learning, '
            . 'Python, SQL und Datenvisualisierung (Tableau), ist sie Absolventin der University of Pittsburgh '
            . 'und lebt im Raum Berlin. Für ApplyToGerman schreibt sie über das Studium in Deutschland, '
            . 'Data-Science-Karrieren und Bewerbungsprozesse.';

        $u->expertise = [
            'Machine Learning & AI',
            'Python & SQL',
            'Data Visualization (Tableau)',
            'IT & Data Science education in Germany',
            'Career planning',
        ];

        $social = (array) ($u->social_links ?? []);
        $social['linkedin'] = 'https://de.linkedin.com/in/dr-filiz-%C3%B6zkan-';
        $u->social_links = $social;

        // avatar_url'e dokunulmuyor — foto admin panelden yüklenecek (LinkedIn indirilemedi).

        $u->save();
        echo "enrich_filiz_ozkan: {$u->name} profili güncellendi (slug={$u->slug})\n";
    }

    public function down(): void
    {
        // Geri alma yok — profil bilgisi korunur.
    }
};
