<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

/**
 * Yazar dengeleme + uzmanlik alanlari + tarih cesitlendirme.
 *
 * A) Filiz Ozkan yeni yazar olarak eklenir — Halil'in YARDIMCISI (genel/surec).
 * B) Her yazara UZMANLIK ALANI (role_label + bio, TR/EN/DE) atanir.
 * C) Blog yazilari konularina gore uzmanlara dagitilir. Halil LEAD kalir
 *    (net lider, "2 adim onde"); eslesmeyen genel havuz Halil ile Filiz
 *    arasinda bolusulur (Halil cogunluk). published_at/created_at
 *    2026-01-12..2026-07-20 araligina ceviri-grubu bazinda yayilir.
 *
 * Idempotent.
 */
return new class extends Migration
{
    public function up(): void
    {
        $uid = fn (string $slug) => DB::table('users')->where('slug', $slug)->value('id');

        $halil  = $uid('halil-yaprakli') ?? DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id') ?? DB::table('users')->orderBy('id')->value('id');
        $elif   = $uid('elif-g')          ?? $halil;
        $gamze  = $uid('gamze-e')         ?? $halil;
        $hakan  = $uid('hakan-kutlu')     ?? $halil;
        $caner  = $uid('caner-turkdogru') ?? $halil;
        $anna   = $uid('anna-schmidt')    ?? $halil;
        $ayesha = $uid('ayesha-khan')     ?? $halil;
        // Saglik & akademik yazar: Dr. Hasan Kaya (kullanici belirledi; placeholder kullanilmaz)
        $hasan = $uid('hasan-kaya');
        if (! $hasan) {
            $hasan = DB::table('users')->insertGetId([
                'name'          => 'Dr. Hasan Kaya',
                'slug'          => 'hasan-kaya',
                'email'         => 'hasan.kaya@almanyauni.com',
                'avatar_url'    => 'https://ui-avatars.com/api/?name=Hasan+Kaya&background=b91c1c&color=fff&bold=true',
                'password'      => Hash::make(Str::random(40)),
                'is_author'     => 1,
                'email_verified_at' => now(),
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
        }

        // --- A) Filiz Ozkan (Halil'in yardimcisi) ---
        $filiz = $uid('filiz-ozkan');
        if (! $filiz) {
            $filiz = DB::table('users')->insertGetId([
                'name'          => 'Filiz Özkan',
                'slug'          => 'filiz-ozkan',
                'email'         => 'filiz@almanyauni.com',
                'avatar_url'    => 'https://ui-avatars.com/api/?name=Filiz+Ozkan&background=6d28d9&color=fff&bold=true',
                'password'      => Hash::make(Str::random(40)),
                'is_author'     => 1,
                'email_verified_at' => now(),
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
        }

        // --- B) Uzmanlik alanlari (role_label + bio, TR/EN/DE) ---
        $profiles = [
            $halil => [
                'İçerik & Vize Süreçleri', 'Founder · Visa & Application Process', 'Gründer · Visum & Bewerbungsprozess',
                'Almanya vize, başvuru süreci ve genel öğrenci rehberliği üzerine yazıyor; içeriğin genel yönünü belirliyor.',
                'Writes on German visa, application process and general student guidance; sets the overall editorial direction.',
                'Schreibt über Visum, Bewerbungsprozess und allgemeine Studienberatung; bestimmt die redaktionelle Ausrichtung.',
            ],
            $filiz => [
                'Master & Kariyer Editörü · Halil’in yardımcısı', 'Master’s & Careers Editor · Deputy to Halil', 'Master- & Karriere-Redakteurin · Stellvertreterin von Halil',
                'Almanya’da yüksek lisans programları ve üniversite sonrası kariyer konularında içerik üretir; Halil’e destek verir.',
                'Creates content on master’s programs in Germany and post-university careers; supports Halil.',
                'Erstellt Inhalte zu Masterprogrammen in Deutschland und Karriere nach dem Studium; unterstützt Halil.',
            ],
            $hasan => [
                'Sağlık & Akademik Editör', 'Health & Academic Editor', 'Redakteur Gesundheit & Akademik',
                'Tıp, diş, eczacılık, veterinerlik, sağlık meslekleri ve akademik denklik/prestij konularında içerik üretir.',
                'Produces content on medicine, dentistry, pharmacy, veterinary, health professions and academic recognition/prestige.',
                'Erstellt Inhalte zu Medizin, Zahn-, Pharmazie-, Veterinär- und Gesundheitsberufen sowie akademischer Anerkennung.',
            ],
            $hakan => [
                'Mühendislik & Sanayi Editörü', 'Engineering & Industry Editor', 'Redakteur Ingenieurwesen & Industrie',
                'Mühendislik, otomotiv, matematik, çevre, tarım ve lojistik alanlarında içerik üretir.',
                'Covers engineering, automotive, mathematics, environment, agriculture and logistics.',
                'Erstellt Inhalte zu Ingenieurwesen, Automobil, Mathematik, Umwelt, Landwirtschaft und Logistik.',
            ],
            $caner => [
                'Bilişim & Teknoloji Editörü', 'IT & Technology Editor', 'Redakteur IT & Technologie',
                'Bilgisayar bilimi, veri bilimi/yapay zekâ, bilişim sistemleri ve IT kariyeri konularında uzman.',
                'Expert in computer science, data science/AI, business informatics and IT careers.',
                'Experte für Informatik, Data Science/KI, Wirtschaftsinformatik und IT-Karrieren.',
            ],
            $elif => [
                'İşletme, Ekonomi & Hukuk Editörü', 'Business, Economics & Law Editor', 'Redakteurin Wirtschaft & Recht',
                'İşletme, ekonomi ve hukuk programları ile ilgili başvuru ve kariyer içerikleri üretir.',
                'Produces application and career content on business, economics and law programs.',
                'Erstellt Bewerbungs- und Karriereinhalte zu Wirtschafts- und Rechtsstudiengängen.',
            ],
            $gamze => [
                'Sosyal Bilimler & Yaratıcı Alanlar Editörü', 'Social Sciences & Creative Fields Editor', 'Redakteurin Sozial- & Kreativwissenschaften',
                'Psikoloji, sosyoloji, sosyal hizmet, sanat/tasarım, müzik, mimarlık, iletişim ve turizm alanlarında yazar.',
                'Writes on psychology, sociology, social work, art/design, music, architecture, communication and tourism.',
                'Schreibt über Psychologie, Soziologie, Soziale Arbeit, Kunst/Design, Musik, Architektur, Kommunikation und Tourismus.',
            ],
            $anna => [
                'Almanca Dil & Bürokrasi Editörü', 'German Language & Bureaucracy Editor', 'Redakteurin Deutsch & Behörden',
                'Almanca sınavları, Anmeldung, Studienkolleg, konut ve Almanya’da günlük bürokrasi konularında uzman.',
                'Expert in German exams, Anmeldung, Studienkolleg, housing and everyday bureaucracy in Germany.',
                'Expertin für Deutschprüfungen, Anmeldung, Studienkolleg, Wohnen und Alltagsbürokratie in Deutschland.',
            ],
            $ayesha => [
                'Uluslararası Öğrenci Deneyimi · Burs & İngilizce programlar', 'International Student Experience · Scholarships & English Programs', 'Internationale Studienerfahrung · Stipendien & englische Programme',
                'Burslar, sigorta, İngilizce programlar ve uluslararası öğrenci hayatı üzerine içerik üretir.',
                'Creates content on scholarships, insurance, English-taught programs and international student life.',
                'Erstellt Inhalte zu Stipendien, Versicherung, englischsprachigen Programmen und internationalem Studienleben.',
            ],
        ];

        foreach ($profiles as $id => $p) {
            if (! $id) { continue; }
            DB::table('users')->where('id', $id)->update([
                'role_label'    => $p[0], 'role_label_en' => $p[1], 'role_label_de' => $p[2],
                'bio'           => $p[3], 'bio_en'        => $p[4], 'bio_de'        => $p[5],
                'is_author'     => 1,
                'updated_at'    => now(),
            ]);
        }

        // --- C) Slug -> yazar kurallari (ILK eslesme kazanir) ---
        $rules = [
            ['sperrkonto', $halil], // finans/vize Halil'de kalsin
            // Burs/DAAD Ayesha'da (asagidaki 'phd'/'master' bunlari kapmasin)
            ['scholarship', $ayesha], ['daad', $ayesha], ['bafog', $ayesha], ['stipend', $ayesha],

            // Bilisim & IT (Caner)
            ['business-informatics', $caner], ['informatik', $caner], ['computer-science', $caner],
            ['data-scien', $caner], ['ml-engineer', $caner], ['it-tech', $caner], ['it-job-search', $caner],

            // Saglik & akademik (Cagan)
            ['medicine', $hasan], ['medizin', $hasan], ['doctor', $hasan], ['dentist', $hasan], ['zahn', $hasan],
            ['pharmac', $hasan], ['pharmaz', $hasan], ['veterinar', $hasan], ['tiermedizin', $hasan],
            ['nurs', $hasan], ['physiotherap', $hasan], ['natural-science', $hasan], ['with-a-science-degree', $hasan],
            ['prestige', $hasan], ['ranking', $hasan], ['tu9', $hasan], ['anabin', $hasan], ['zab-', $hasan],
            ['phd', $hasan], ['are-german-universities-hard', $hasan], ['hochschule-vs', $hasan],

            // Muhendislik & sanayi (Hakan)
            ['engineer', $hakan], ['automotive', $hakan], ['fahrzeug', $hakan], ['mathematics', $hakan],
            ['environmental', $hakan], ['sustainab', $hakan], ['renewable', $hakan], ['green-careers', $hakan],
            ['agricultur', $hakan], ['logistics', $hakan], ['supply-chain', $hakan],

            // Isletme & ekonomi & hukuk (Elif)
            ['business-administration', $elif], ['bwl', $elif], ['business-management', $elif],
            ['business-consulting', $elif], ['business-schools', $elif], ['economi', $elif], ['vwl', $elif],
            ['law', $elif], ['jura', $elif], ['llm', $elif], ['llb', $elif],

            // Sosyal & yaratici (Gamze)
            ['psycholog', $gamze], ['psychotherap', $gamze], ['sociolog', $gamze], ['social-work', $gamze],
            ['soziale-arbeit', $gamze], ['social-worker', $gamze], ['art-and-design', $gamze], ['design', $gamze],
            ['music', $gamze], ['performing', $gamze], ['audition', $gamze], ['architect', $gamze],
            ['communication', $gamze], ['media', $gamze], ['tourism', $gamze], ['hospitality', $gamze],
            ['sports-science', $gamze], ['sportwissenschaft', $gamze], ['international-relations', $gamze], ['political-science', $gamze],

            // Almanca dil & burokrasi & yasam (Anna)
            ['testdaf', $anna], ['dsh', $anna], ['telc', $anna], ['goethe', $anna], ['deutschpr', $anna],
            ['learning-german', $anna], ['german-a1-to-c1', $anna], ['zero-to-c1', $anna], ['german-language-reality', $anna],
            ['language-exam', $anna], ['anmeldung', $anna], ['studienkolleg', $anna], ['burgeramt', $anna],
            ['sworn-translation', $anna], ['driving-license', $anna], ['rental', $anna], ['rent-by-city', $anna],
            ['accommodation', $anna], ['finding-a-wg', $anna], ['housing', $anna], ['rundfunk', $anna],
            ['steuer-id', $anna], ['new-arrival', $anna],

            // Master programlari & universite sonrasi kariyer (Filiz — Halil'in yardimcisi)
            ['doing-a-masters', $filiz], ['bachelor-vs-master', $filiz], ['english-master', $filiz],
            ['masters-vs-job-seeker', $filiz], ['job-market-reality', $filiz], ['job-seeker-visa', $filiz],
            ['searching-for-jobs', $filiz], ['after-your-english', $filiz],
            ['changing-student-visa-to-work-permit', $filiz], ['zweckwechsel', $filiz],

            // Uluslararasi ogrenci & sigorta & yasam (Ayesha) — burs/daad yukarida
            ['health-insurance', $ayesha], ['krankenkasse', $ayesha], ['insurance', $ayesha], ['loneliness', $ayesha],
            ['mental-health', $ayesha], ['part-time', $ayesha], ['werkstudent', $ayesha], ['student-associations', $ayesha],
            ['cultural-etiquette', $ayesha], ['quality-of-life', $ayesha], ['without-german-living', $ayesha],
        ];

        $posts = DB::table('posts')->select('id', 'slug', 'translation_group_id')->get();
        $counts = [];

        foreach ($posts as $p) {
            $slug = (string) $p->slug;
            $seed = $p->translation_group_id ?: $slug;
            $h = crc32($seed) & 0x7fffffff;

            $author = $halil; // eslesme yoksa LEAD (genel/vize/surec)
            foreach ($rules as [$needle, $who]) {
                if (strpos($slug, $needle) !== false) { $author = $who; break; }
            }

            $date = Carbon::create(2026, 1, 12, 0, 0, 0)
                ->addDays($h % 189)
                ->addHours(8 + ($h % 11))
                ->addMinutes(intdiv($h, 89) % 60);

            DB::table('posts')->where('id', $p->id)->update([
                'user_id'      => $author,
                'published_at' => $date,
                'created_at'   => $date,
            ]);
            $counts[$author] = ($counts[$author] ?? 0) + 1;
        }

        arsort($counts);
        foreach ($counts as $u => $c) {
            $nm = DB::table('users')->where('id', $u)->value('name');
            echo "  {$nm} (id {$u}): {$c} yazi\n";
        }
    }

    public function down(): void
    {
        // Geri alinamaz (orijinal yazar/tarih saklanmadi).
    }
};
