<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;

/**
 * Dr. Filiz Özkan yazar OTORİTESİNİ artırır — gerçek (LinkedIn-doğrulanmış) akademik CV
 * ile zengin E-E-A-T profili. Kişinin izniyle. Prod-only kullanıcı → data-migration.
 *
 * Doğrulanmış kariyer (public LinkedIn):
 *  - İktisat/Finansal Ekonometri doktoru
 *  - Doçent · Sakarya Üniversitesi, Finansal Ekonometri (2009–2016)
 *  - Öğretim Görevlisi · Bülent Ecevit Üniversitesi (2001–2009)
 *  - Misafir Araştırmacı · University of Pittsburgh, ABD (2011–2012)
 *  - Data Analyst · WZB Berlin Sosyal Bilimler Merkezi (2019–2022)
 *  - Operations & Education Manager · euroTech Study GmbH, Almanya (2023–)
 * Uydurma iddia YOK. Katkı-sağlayandan tam yazara yükseltir (is_author, is_contributor=false).
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

        $u->role_label    = 'İktisat Dr. · Veri Bilimi & Kariyer Uzmanı';
        $u->role_label_en = 'PhD Economics · Data Science & Career Specialist';
        $u->role_label_de = 'Dr. Wirtschaft · Data-Science- & Karriereexpertin';

        $u->bio = 'Dr. Filiz Özkan, iktisat ve finansal ekonometri doktorasına sahip bir akademisyen ve '
            . 'veri uzmanıdır. Sakarya Üniversitesi\'nde Finansal Ekonometri alanında doçent olarak görev yaptı, '
            . 'Bülent Ecevit Üniversitesi\'nde ders verdi ve University of Pittsburgh\'ta (ABD) misafir araştırmacı '
            . 'olarak bulundu. Berlin Sosyal Bilimler Merkezi\'nde (WZB) veri analisti olarak çalıştıktan sonra, '
            . 'hâlihazırda Almanya merkezli teknoloji eğitim kurumu euroTech Study GmbH\'de Eğitim ve Operasyon '
            . 'Müdürü olarak görev yapıyor. Makine öğrenmesi, Python, SQL ve veri görselleştirme (Tableau) '
            . 'alanlarında uzmanlaşan Özkan, ApplyToGerman için Almanya\'da yüksek lisans, veri bilimi & yapay '
            . 'zekâ kariyeri ve akademik başvuru süreçleri hakkında içerik üretiyor.';

        $u->bio_en = 'Dr. Filiz Özkan is an academic and data specialist with a doctorate in economics and '
            . 'financial econometrics. She served as an Associate Professor of Financial Econometrics at Sakarya '
            . 'University, lectured at Bülent Ecevit University, and was a Visiting Researcher at the University of '
            . 'Pittsburgh (USA). After working as a Data Analyst at the Berlin Social Science Center (WZB), she is '
            . 'currently the Operations and Education Manager at euroTech Study GmbH, a Germany-based technology '
            . 'education institution. Specializing in machine learning, Python, SQL, and data visualization '
            . '(Tableau), she writes for ApplyToGerman on graduate studies, data science & AI careers, and the '
            . 'academic application process.';

        $u->bio_de = 'Dr. Filiz Özkan ist Akademikerin und Datenexpertin mit einer Promotion in Wirtschafts- und '
            . 'Finanzökonometrie. Sie war außerordentliche Professorin für Finanzökonometrie an der Sakarya-'
            . 'Universität, lehrte an der Bülent-Ecevit-Universität und war Gastforscherin an der University of '
            . 'Pittsburgh (USA). Nach ihrer Tätigkeit als Data Analyst am Wissenschaftszentrum Berlin für '
            . 'Sozialforschung (WZB) ist sie derzeit Operations- und Bildungsmanagerin bei der euroTech Study '
            . 'GmbH, einer technologieorientierten Bildungseinrichtung in Deutschland. Spezialisiert auf Machine '
            . 'Learning, Python, SQL und Datenvisualisierung (Tableau), schreibt sie für ApplyToGerman über '
            . 'Masterstudium, Data-Science- & KI-Karrieren und den akademischen Bewerbungsprozess.';

        $u->expertise = [
            'İktisat & finansal ekonometri',
            'Makine öğrenmesi & yapay zekâ',
            'Python, SQL & veri görselleştirme (Tableau)',
            'Yüksek lisans & akademik başvuru',
            'Almanya\'da veri bilimi & kariyer',
        ];

        $social = (array) ($u->social_links ?? []);
        $social['linkedin'] = 'https://de.linkedin.com/in/dr-filiz-%C3%B6zkan-';
        $u->social_links = $social;

        // Katkı-sağlayandan TAM YAZARA yükselt → team sayfasında yazar kartında görünür.
        $u->is_author = true;
        $u->is_contributor = false;
        if (empty($u->slug)) $u->slug = 'filiz-ozkan';

        // avatar_url'e dokunulmuyor — foto ayrıca eklenecek (LinkedIn indirilemedi).

        $u->save();
        echo "enrich_filiz_ozkan: {$u->name} profili zenginleştirildi + tam yazar yapıldı (slug={$u->slug})\n";
    }

    public function down(): void
    {
        // Geri alma yok — profil bilgisi korunur.
    }
};
