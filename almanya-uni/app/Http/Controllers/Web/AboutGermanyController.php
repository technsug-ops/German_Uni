<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Post;
use App\Models\Program;
use App\Models\Scholarship;
use App\Models\University;
use Illuminate\View\View;

class AboutGermanyController extends Controller
{
    public function index(): View
    {
        $isTr = app()->getLocale() === 'tr';

        // 10 reasons — default English, __() handles locale, 6th reason is locale-aware
        $reasons = [
            ['icon' => '💰', 'title' => __('Free Public Education'),
             'desc' => __('Public universities charge €150-350 per semester (campus services only). No tuition fee — a tenth of foundation universities in many countries.')],
            ['icon' => '🇪🇺', 'title' => __('EU Work Authorization'),
             'desc' => __('After graduation, finding a job in Germany unlocks the right to work in all 27 EU countries. One of the strongest cards a non-EU passport can earn.')],
            ['icon' => '🔍', 'title' => __('18-Month Job Seeker Visa'),
             'desc' => __('You don\'t have to leave after graduation. You get 18 months (1.5 years) to stay in Germany and search for a job (Aufenthaltserlaubnis zur Arbeitsplatzsuche).')],
            ['icon' => '🏭', 'title' => __('Europe\'s Largest Economy'),
             'desc' => __('Germany produces 25% of the EU\'s GDP. Engineering, automotive, chemistry, pharma, finance — global leaders in every sector.')],
            ['icon' => '🌍', 'title' => __('2,000+ English-Taught Programs'),
             'desc' => __('Study a master\'s or PhD without German. Bachelor\'s options are growing too. Thousands of English-taught programs in the DAAD database.')],

            // 6th reason — locale-aware (TR-specific in Turkish, generic elsewhere)
            $isTr
                ? ['icon' => '🤝', 'title' => '3M+ Türk Topluluğu',
                   'desc' => 'Almanya\'da 3 milyondan fazla Türk vatandaşı yaşıyor — yalnızlık değil, kültürel köprü. Türk marketleri, restoranları, dernekleri her şehirde.']
                : ['icon' => '🌐', 'title' => __('Multicultural Society'),
                   'desc' => __('25% of Germany\'s population has a migration background — over 12 million international individuals. Every major city has international markets, restaurants, and cultural centers.')],

            ['icon' => '🏥', 'title' => __('World-Class Healthcare'),
             'desc' => __('Insurance covers all doctor + hospital costs. Student rate is ~€125/month — thousands of euros of treatment covered annually.')],
            ['icon' => '🚆', 'title' => __('Heart of Europe'),
             'desc' => __('Reach Paris in 4 hours, Amsterdam in 6, Rome by overnight train. Semester Ticket gives free city public transport for students.')],
            ['icon' => '🎓', 'title' => __('40+ Nobel-Winning Institutions'),
             'desc' => __('Max Planck institutes, Fraunhofer, Helmholtz — Germany leads European research. The right choice for an academic career.')],
            ['icon' => '💼', 'title' => __('High Post-Grad Salaries'),
             'desc' => __('Engineers start at €60,000+/year. IT €55-70K, doctors €70-90K. Stable euro income with strong purchasing power.')],
        ];

        // City-industry map — city names stay as-is (proper nouns), descriptions translated
        $cityIndustries = [
            ['slug' => 'berlin', 'city' => 'Berlin', 'industries' => [__('Start-up'), 'AI / Tech', __('Media'), __('Arts')],
             'desc' => __('Europe\'s start-up capital. SoundCloud, Zalando, N26 were born here. Largest student city with 200K+ students.')],
            ['slug' => 'munchen', 'city' => 'München', 'industries' => [__('Automotive (BMW)'), __('Aerospace (Airbus)'), __('Software (Siemens)'), __('Insurance')],
             'desc' => __('Capital of Bavaria. Germany\'s #1 destination for engineering students. LMU + TUM duo ranks in world top 50.')],
            ['slug' => 'hamburg', 'city' => 'Hamburg', 'industries' => [__('Maritime Trade'), __('Media'), __('Aerospace'), __('Logistics')],
             'desc' => __('Germany\'s #1 port, Europe\'s #2. Home to Spiegel, ZEIT. Airbus plant — critical for aerospace engineering.')],
            ['slug' => 'frankfurt-am-main', 'city' => 'Frankfurt am Main', 'industries' => [__('Finance'), __('Banking'), __('Stock Exchange'), __('ECB')],
             'desc' => __('Germany\'s financial capital. Home to the European Central Bank. Internship paradise for business/finance/economics students.')],
            ['slug' => 'stuttgart', 'city' => 'Stuttgart', 'industries' => [__('Automotive (Mercedes/Porsche)'), __('Engineering'), 'Bosch'],
             'desc' => __('Germany\'s "automotive heart". Mercedes-Benz, Porsche, Bosch HQ. Uni Stuttgart is elite for engineering.')],
            ['slug' => 'koln', 'city' => 'Köln', 'industries' => [__('Media (RTL/WDR)'), __('Insurance'), __('Chemistry')],
             'desc' => __('Germany\'s media capital. Home to RTL, WDR, Deutsche Welle. Carnival culture makes it Germany\'s "warmest" city.')],
            ['slug' => 'dusseldorf', 'city' => 'Düsseldorf', 'industries' => [__('Fashion'), __('Advertising'), __('Telecom (Vodafone DE)')],
             'desc' => __('Hub for fashion + advertising agencies. Vodafone Germany HQ, Henkel, Metro AG. Capital of NRW state.')],
            ['slug' => 'heidelberg', 'city' => 'Heidelberg', 'industries' => [__('Research (Max Planck)'), __('Medicine'), __('Biotech'), __('Software (SAP)')],
             'desc' => __('Germany\'s oldest university (1386). Max Planck + EMBL + DKFZ → world hub for medical/biotech research.')],
            ['slug' => 'dresden', 'city' => 'Dresden', 'industries' => [__('Semiconductors (Infineon)'), __('Microelectronics'), __('Design')],
             'desc' => __('"Silicon Saxony" — Europe\'s #1 microchip manufacturing region. Infineon, GlobalFoundries, Bosch chip fab. TU Dresden Cluster of Excellence.')],
            ['slug' => 'leipzig', 'city' => 'Leipzig', 'industries' => [__('Logistics (DHL)'), __('E-commerce (Amazon DE)'), __('Music'), __('Automotive')],
             'desc' => __('DHL\'s European hub. Porsche + BMW factories. City of Bach, Mendelssohn — world-class music conservatory.')],
        ];

        // Pull image_url + real slug from DB (slug pattern: "berlin-q64")
        foreach ($cityIndustries as &$ci) {
            $row = City::where('slug', 'LIKE', $ci['slug'] . '-q%')
                ->orWhere('slug', $ci['slug'])
                ->first(['slug', 'image_url']);
            if ($row) {
                $ci['slug']      = $row->slug;
                $ci['image_url'] = $row->image_url;
            } else {
                $ci['image_url'] = null;
            }
        }
        unset($ci);

        // Live stats from DB
        $stats = [
            'universities'  => University::where('is_active', true)->count(),
            'programs'      => Program::where('is_active', true)->count(),
            'programs_en'   => Program::where('is_active', true)->whereIn('language', ['en', 'both'])->count(),
            'cities'        => City::where('is_active', true)->has('universities')->count(),
            'states'        => 16,
            'scholarships'  => Scholarship::whereNull('removed_at')->count(),
        ];

        // Latest 3 blog posts
        $latestPosts = Post::published()
            ->orderByDesc('published_at')
            ->limit(3)
            ->get(['id', 'slug', 'title', 'excerpt', 'reading_minutes', 'published_at']);

        return view('about-germany', [
            'reasons' => $reasons,
            'cityIndustries' => $cityIndustries,
            'stats' => $stats,
            'latestPosts' => $latestPosts,
        ]);
    }
}
