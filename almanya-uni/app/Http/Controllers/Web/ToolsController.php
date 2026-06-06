<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\CityCostData;
use App\Models\FieldOfStudy;
use App\Models\Profession;
use App\Models\Program;
use App\Models\Scholarship;
use App\Models\University;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;
use Illuminate\View\View;

class ToolsController extends Controller
{
    public function index(): View
    {
        $tools = [
            [
                'slug'        => 'yasam-maliyeti',
                'title'       => __('Cost of Living Calculator'),
                'description' => __('Calculate your monthly expenses as a student in Germany, city by city.'),
                'icon'        => '💰',
                'route'       => route('tools.cost-of-living'),
                'live'        => true,
            ],
            [
                'slug'        => 'not-donusturucu',
                'title'       => __('Grade Converter'),
                'description' => __('Convert your university grade to the German 1-5 system.'),
                'icon'        => '📊',
                'route'       => route('tools.grade-converter'),
                'live'        => true,
            ],
            [
                'slug'        => 'uni-onerisi',
                'title'       => __('University Match Quiz'),
                'description' => __('8 questions → the German universities that fit you best.'),
                'icon'        => '🎯',
                'route'       => route('tools.recommendation'),
                'live'        => true,
            ],
            [
                'slug'        => 'kariyer-pusulasi',
                'title'       => __('Career Compass'),
                'description' => __('Talent (RIASEC) + value analysis → real professions that match you from 3,500+ data.'),
                'icon'        => '🧭',
                'route'       => route('tools.career-compass'),
                'live'        => true,
            ],
            [
                'slug'        => 'basvuru-takvimi',
                'title'       => __('Application Calendar'),
                'description' => __('See upcoming deadlines, filter, add to calendar. 7,000+ programs.'),
                'icon'        => '📅',
                'route'       => route('tools.deadlines'),
                'live'        => true,
            ],
            [
                'slug'        => 'vize-maliyeti',
                'title'       => __('Visa Cost Calculator'),
                'description' => __('Add up ALL costs of the Germany student visa process step by step.'),
                'icon'        => '💸',
                'route'       => route('tools.visa-cost'),
                'live'        => true,
            ],
            [
                'slug'        => 'butce-planlayici',
                'title'       => __('Budget Planner'),
                'description' => __('Monthly income + expense + savings goal. City-based, with work income.'),
                'icon'        => '📈',
                'route'       => route('tools.budget-planner'),
                'live'        => true,
            ],
            [
                'slug'        => 'bloke-hesap',
                'title'       => __('Blocked Account (Sperrkonto) Finder'),
                'description' => __('Compare blocked account providers for the Germany student visa. Price, speed, insurance combo.'),
                'icon'        => '🏦',
                'route'       => route('tools.blocked-account'),
                'live'        => true,
            ],
            [
                'slug'        => 'visa-appointment',
                'title'       => __('Visa Appointment (iData)'),
                'description' => __('Step-by-step Germany student visa appointment via iData in Turkey: process, fees, offices.'),
                'icon'        => '🛂',
                'route'       => route('tools.visa-appointment'),
                'live'        => true,
                'locales'     => ['tr'], // iData yalnızca Türkiye'den başvuranlar için → sadece /tr
            ],
            [
                'slug'        => 'language-certificates',
                'title'       => __('Language Certificates'),
                'description' => __('TestDaF vs DSH vs telc vs Goethe — which German certificate do you need for university?'),
                'icon'        => '🎓',
                'route'       => route('tools.language-certificates'),
                'live'        => true,
            ],
            [
                'slug'        => 'pathway-finder',
                'title'       => __('Germany Pathway Finder'),
                'description' => __('5 questions → Studienkolleg, Bachelor, Master, PhD, Ausbildung or Sprachkurs. Real durations + costs.'),
                'icon'        => '🧭',
                'route'       => route('tools.pathway-finder'),
                'live'        => true,
            ],
            [
                'slug'        => 'inspire-me',
                'title'       => __('Inspire Me'),
                'description' => __('Stuck choosing? Random university + city + programme + scholarship + profession + field. Each refresh = 6 new picks.'),
                'icon'        => '✨',
                'route'       => route('tools.inspire-me'),
                'live'        => true,
            ],
            [
                'slug'        => 'professional-recognition',
                'title'       => __('Professional Recognition'),
                'description' => __('Is your profession recognised in Germany? 6 popular jobs — authority, timeline, cost.'),
                'icon'        => '🛡️',
                'route'       => route('tools.professional-recognition'),
                'live'        => true,
            ],
        ];

        // Locale-özel araçları gizle (ör. iData yalnızca /tr'de).
        $tools = array_values(array_filter(
            $tools,
            fn ($t) => empty($t['locales']) || in_array(app()->getLocale(), $t['locales'], true)
        ));

        return view('tools.index', compact('tools'));
    }

    public function costOfLiving(Request $request): View
    {
        // Hem manuel costData hem AI cost_of_living block olan şehirler
        $cities = City::query()
            ->where(function ($q) {
                $q->whereHas('costData')
                    ->orWhereNotNull('content_blocks');
            })
            ->with('costData', 'state:id,name_tr,name_en,name_de,slug')
            ->orderBy('name_de')
            ->get(['id', 'slug', 'name_tr', 'name_de', 'state_id', 'content_blocks','name_en','name_de']);

        $selectedCityId = (int) $request->query('city');
        $housing        = $request->query('housing', 'wg');
        $lifestyle      = $request->query('lifestyle', 'normal');

        $housing   = in_array($housing,   ['wg', 'studio', 'apartment'], true)  ? $housing   : 'wg';
        $lifestyle = in_array($lifestyle, ['frugal', 'normal', 'comfortable'], true) ? $lifestyle : 'normal';

        $result = null;

        if ($selectedCityId) {
            $city = $cities->firstWhere('id', $selectedCityId);

            if ($city) {
                if ($city->costData) {
                    // Manuel veri varsa onu kullan (en doğru)
                    $result = $this->buildCostBreakdown($city, $housing, $lifestyle);
                } else {
                    // AI content_blocks fallback
                    $result = $this->buildCostBreakdownFromAi($city, $housing, $lifestyle);
                }
            }
        }

        return view('tools.cost-of-living', [
            'cities'           => $cities,
            'selected_city_id' => $selectedCityId,
            'housing'          => $housing,
            'lifestyle'        => $lifestyle,
            'result'           => $result,
        ]);
    }

    /**
     * AI cost_of_living content block'undan approximate breakdown üret.
     * Range string'lerini ("300-450") ortalama sayıya çevirir, housing/lifestyle çarpanı uygular.
     */
    private function buildCostBreakdownFromAi(City $city, string $housing, string $lifestyle): ?array
    {
        $block = collect($city->content_blocks ?? [])->firstWhere('type', 'cost_of_living');
        if (!$block || empty($block['items'])) return null;

        $parseRange = function (string $amount): float {
            $amount = trim(str_replace([',', ' ', '€', 'EUR'], ['.', '', '', ''], $amount));
            if (preg_match('/(\d+(?:\.\d+)?)\s*[-–]\s*(\d+(?:\.\d+)?)/', $amount, $m)) {
                return ((float) $m[1] + (float) $m[2]) / 2;
            }
            if (preg_match('/(\d+(?:\.\d+)?)/', $amount, $m)) {
                return (float) $m[1];
            }
            return 0;
        };

        // Housing multiplier — WG bazlı (AI default WG kullanır), studio +50%, apartment +120%
        $housingMult = match ($housing) {
            'studio'    => 1.5,
            'apartment' => 2.2,
            default     => 1.0,
        };
        $lifestyleMult = match ($lifestyle) {
            'frugal'      => 0.80,
            'comfortable' => 1.25,
            default       => 1.00,
        };

        $items = [];
        $total = 0;
        foreach ($block['items'] as $item) {
            $label = $item['label'] ?? '';
            $value = $parseRange($item['amount'] ?? '');

            // Kira için housing çarpanı, yemek/eğlence için lifestyle
            $isRent = preg_match('/kira|wohn|miete|wg/i', $label);
            $isLifestyle = preg_match('/yemek|eğlen|kişisel|other|diğer/i', $label);
            if ($isRent) {
                $value = $value * $housingMult;
            } elseif ($isLifestyle) {
                $value = $value * $lifestyleMult;
            }
            $value = (int) round($value);
            $total += $value;
            $items[] = [
                'key' => mb_strtolower($label),
                'label' => $label,
                'value' => $value,
                'fixed' => !$isLifestyle,
            ];
        }

        return [
            'city'              => $city,
            'tier'              => null,
            'items'             => $items,
            'total_month'       => $total,
            'total_year'        => $total * 12,
            'blocked_account'   => max($total * 12, 11904),
            'housing'           => $housing,
            'housing_label'     => $this->housingLabel($housing),
            'lifestyle'         => $lifestyle,
            'lifestyle_label'   => $this->lifestyleLabel($lifestyle),
            'source'            => 'ai',
            'source_note'       => 'AI üretimi yaklaşık veri — kesin için şehir resmi sitesinden doğrula.',
        ];
    }

    public function gradeConverter(Request $request): View
    {
        $system  = $request->query('system', 'tr4');   // tr4 (4'lük) | tr100 (100'lük)
        $grade   = $request->query('grade');

        $system  = in_array($system, ['tr4', 'tr100'], true) ? $system : 'tr4';
        $result  = null;

        if ($grade !== null && $grade !== '') {
            $grade = (float) str_replace(',', '.', $grade);
            $result = $this->convertGrade($system, $grade);
        }

        $table = $this->gradeTable($system);

        return view('tools.grade-converter', [
            'system' => $system,
            'grade'  => $grade,
            'result' => $result,
            'table'  => $table,
        ]);
    }

    /**
     * Bütçe Planlayıcı — aylık gelir vs gider + tasarruf hedefi.
     */
    public function budgetPlanner(Request $request): View
    {
        // Şehir baseline'ı için yine cost-of-living mantığı kullan
        $cities = City::whereHas('costData')
            ->with('costData', 'state:id,name_tr,name_en,name_de')
            ->orderBy('name_de')
            ->get(['id', 'slug', 'name_tr', 'name_de', 'state_id','name_en','name_de']);

        $selectedCityId = (int) $request->query('city', 0);
        $housing        = $request->query('housing', 'wg');
        $lifestyle      = $request->query('lifestyle', 'normal');
        $housing   = in_array($housing,   ['wg', 'studio', 'apartment'], true)   ? $housing   : 'wg';
        $lifestyle = in_array($lifestyle, ['frugal', 'normal', 'comfortable'], true) ? $lifestyle : 'normal';

        // Gider — şehir seçildiyse cost-of-living'den
        $expense = null;
        $city = $cities->firstWhere('id', $selectedCityId);
        if ($city && $city->costData) {
            $expense = $this->buildCostBreakdown($city, $housing, $lifestyle);
        }

        // Gelir kaynakları (kullanıcı tarafından girilir)
        $income = [
            'sperrkonto'  => (int) ($request->query('sperrkonto', 992)),
            'scholarship' => (int) ($request->query('scholarship', 0)),
            'job'         => (int) ($request->query('job', 0)),
            'family'      => (int) ($request->query('family', 0)),
        ];
        $totalIncome = array_sum($income);

        // Tasarruf hedefi
        $savingsGoal = (int) ($request->query('savings_goal', 100));

        // Sonuç analizi
        $expenseTotal = $expense['total_month'] ?? 0;
        $netBalance = $totalIncome - $expenseTotal;
        $coversGoal = $netBalance >= $savingsGoal;

        // Öneriler
        $suggestions = [];
        if ($expenseTotal === 0) {
            $suggestions[] = ['type' => 'info', 'msg' => __('Şehir seç → otomatik gider tahmini gelir.')];
        } elseif ($totalIncome < $expenseTotal) {
            $deficit = $expenseTotal - $totalIncome;
            $suggestions[] = ['type' => 'warning', 'msg' => "❌ Aylık {$deficit}€ açık var. Çalışma geliri ekle (Werkstudent max 538€) ya da WG'ye geç."];
        } elseif (! $coversGoal) {
            $shortage = $savingsGoal - $netBalance;
            $suggestions[] = ['type' => 'warning', 'msg' => "⚠️ Tasarruf hedefin için {$shortage}€ daha gerek. Eğlence/yemek kalemini kıs ya da burs ara."];
        } else {
            $extra = $netBalance - $savingsGoal;
            $suggestions[] = ['type' => 'success', 'msg' => "✅ Tasarruf hedefin karşılanıyor. {$extra}€ ekstra var — biriktir ya da yatır."];
        }

        if ($income['job'] > 538) {
            $suggestions[] = ['type' => 'warning', 'msg' => "⚠️ Werkstudent statüsünde aylık max 538€ kazanılır (vergi+sigorta avantajı için). Üstüne çıkarsan tam çalışan sayılırsın."];
        }

        if ($income['job'] === 0 && $netBalance < 0) {
            $suggestions[] = ['type' => 'info', 'msg' => "💡 Almanya'da öğrenci olarak 140 tam gün/280 yarım gün çalışabilirsin (2024 değişikliği). Asgari ücret 12,82€/saat."];
        }

        return view('tools.budget-planner', [
            'cities'           => $cities,
            'selected_city_id' => $selectedCityId,
            'housing'          => $housing,
            'lifestyle'        => $lifestyle,
            'expense'          => $expense,
            'expense_total'    => $expenseTotal,
            'income'           => $income,
            'total_income'     => $totalIncome,
            'savings_goal'     => $savingsGoal,
            'net_balance'      => $netBalance,
            'covers_goal'      => $coversGoal,
            'suggestions'      => $suggestions,
        ]);
    }

    /**
     * Başvuru takvimi — yaklaşan deadline'ları filtreli olarak listele.
     * Quick filter: "next-30d", "next-90d", "next-6mo", "all"
     */
    public function deadlines(Request $request): View
    {
        $filters = [
            'degree'   => $request->query('degree', ''),
            'language' => $request->query('language', ''),
            'field'    => $request->query('field', ''),
            'semester' => $request->query('semester', ''),
            'window'   => $request->query('window', 'next-90d'),
            'q'        => trim((string) $request->query('q', '')),
        ];

        // Pencere → SQL aralığına dönüştür
        [$dateFrom, $dateTo] = match ($filters['window']) {
            'next-30d'  => [now()->startOfDay(), now()->addDays(30)],
            'next-90d'  => [now()->startOfDay(), now()->addDays(90)],
            'next-6mo'  => [now()->startOfDay(), now()->addMonths(6)],
            'next-year' => [now()->startOfDay(), now()->addYear()],
            default     => [now()->startOfDay(), now()->addYears(2)],
        };

        $q = Program::query()
            ->where('is_active', 1)
            ->where(function ($w) use ($dateFrom, $dateTo, $filters) {
                if ($filters['semester'] === 'summer') {
                    $w->whereBetween('application_deadline_summer', [$dateFrom, $dateTo]);
                } elseif ($filters['semester'] === 'winter') {
                    $w->whereBetween('application_deadline_winter', [$dateFrom, $dateTo]);
                } else {
                    // Hem winter hem summer
                    $w->where(function ($w2) use ($dateFrom, $dateTo) {
                        $w2->whereBetween('application_deadline_winter', [$dateFrom, $dateTo])
                           ->orWhereBetween('application_deadline_summer', [$dateFrom, $dateTo]);
                    });
                }
            })
            ->with(['university:id,name_de,slug,logo_url,city_id', 'university.city:id,name_tr,name_en,name_de,slug', 'field:id,name_tr,name_en,name_de,icon,color']);

        if ($filters['degree']) {
            $q->where('degree', $filters['degree']);
        }
        if ($filters['language']) {
            $q->where('language', $filters['language']);
        }
        if ($filters['field']) {
            $q->whereHas('field', fn ($f) => $f->where('slug', $filters['field']));
        }
        if ($filters['q'] !== '') {
            $like = '%' . $filters['q'] . '%';
            $q->where(function ($w) use ($like) {
                $w->where('name_de', 'like', $like)
                  ->orWhereHas('university', fn ($u) => $u->where('name_de', 'like', $like));
            });
        }

        // En yakın GELECEK deadline'a göre sırala. Geçmiş tarihler (veride çok sayıda
        // stale 2025 var) sıralamaya KATILMAZ — yoksa stale tarihliler hep en üste gelip
        // her pencerede aynı ilk sayfa görünüyordu (QA: gün filtresi çalışmıyor sanıldı).
        $programs = $q->orderByRaw('
            LEAST(
                CASE WHEN application_deadline_winter >= CURDATE() THEN application_deadline_winter ELSE "9999-12-31" END,
                CASE WHEN application_deadline_summer >= CURDATE() THEN application_deadline_summer ELSE "9999-12-31" END
            ) ASC
        ')
        ->paginate(40)
        ->withQueryString();

        $fields = FieldOfStudy::active()->orderBy('sort_order')->get(['slug', 'name_tr', 'icon','name_en','name_de']);

        return view('tools.deadlines', [
            'programs' => $programs,
            'filters'  => $filters,
            'fields'   => $fields,
            'today'    => now()->startOfDay(),
        ]);
    }

    /**
     * Belirli programların deadline'larını ICS (takvim) dosyası olarak indir.
     * /tools/deadlines/ics?slugs=a,b,c&semester=winter
     */
    public function deadlinesIcs(Request $request): Response
    {
        $slugs = explode(',', (string) $request->query('slugs', ''));
        $slugs = array_filter(array_map('trim', $slugs));
        if (empty($slugs)) {
            return response('No programs', 400);
        }

        $semester = $request->query('semester', 'winter');
        $deadlineCol = $semester === 'summer' ? 'application_deadline_summer' : 'application_deadline_winter';

        $programs = Program::whereIn('slug', $slugs)
            ->whereNotNull($deadlineCol)
            ->with('university:id,name_de,slug')
            ->get();

        $ics = "BEGIN:VCALENDAR\r\nVERSION:2.0\r\nPRODID:-//AlmanyaUni//Deadlines//TR\r\n";
        foreach ($programs as $p) {
            $deadline = $p->$deadlineCol;
            if (! $deadline) continue;
            $date = $deadline->format('Ymd');
            $uid = md5($p->slug . '-' . $semester) . '@almanyauni.com';
            $title = mb_substr($p->name_de . ' — ' . ($p->university?->name_de ?? ''), 0, 80);
            $title = str_replace(["\n", "\r", ','], [' ', ' ', '\\,'], $title);
            $url = url('/programs/' . $p->slug);

            $ics .= "BEGIN:VEVENT\r\n";
            $ics .= "UID:$uid\r\n";
            $ics .= "DTSTAMP:" . now()->format('Ymd\THis\Z') . "\r\n";
            $ics .= "DTSTART;VALUE=DATE:$date\r\n";
            $ics .= "DTEND;VALUE=DATE:$date\r\n";
            $ics .= "SUMMARY:📅 $title — Başvuru deadline\r\n";
            $ics .= "DESCRIPTION:" . ($semester === 'summer' ? 'Yaz' : 'Kış') . " dönemi başvurusu. Detay: $url\r\n";
            $ics .= "URL:$url\r\n";
            $ics .= "END:VEVENT\r\n";
        }
        $ics .= "END:VCALENDAR\r\n";

        return response($ics, 200, [
            'Content-Type'        => 'text/calendar; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="almanyauni-deadlines.ics"',
        ]);
    }

    /**
     * Vize maliyeti hesaplayıcı — Türk öğrencilere yönelik 2025 güncel masraflar.
     */
    public function visaCost(Request $request): View
    {
        // Seçilebilir maliyet kalemleri (default değerler 2025 güncel)
        $items = [
            'sperrkonto' => [
                'label' => 'Sperrkonto (Bloke hesap)',
                'desc'  => '11.904€ (992€/ay × 12). Bu paranı GERİ ALIRSIN — her ay 992€ çekersin.',
                'default' => 11904,
                'min' => 0, 'max' => 15000,
                'required' => true,
                'recoverable' => true,
                'icon' => '🏦',
            ],
            'visa_fee' => [
                'label' => __('Vize başvuru ücreti'),
                'desc'  => __('Almanya konsolosluğu öğrenci vizesi (Studienvisum). 2025: 75€.'),
                'default' => 75,
                'min' => 50, 'max' => 100,
                'required' => true,
                'recoverable' => false,
                'icon' => '📋',
            ],
            'idata_appointment' => [
                'label' => 'iDATA randevu + kargo',
                'desc'  => 'iDATA üzerinden vize randevu ücreti + belgelerin kuryeyle gönderimi.',
                'default' => 30,
                'min' => 0, 'max' => 100,
                'required' => false,
                'recoverable' => false,
                'icon' => '📦',
            ],
            'uni_assist' => [
                'label' => 'Uni-Assist başvurusu',
                'desc'  => 'İlk uni-assist başvurusu 75€. Her ek üniversite +30€. 3 üni ortalama hesaplandı.',
                'default' => 135,
                'min' => 0, 'max' => 500,
                'required' => false,
                'recoverable' => false,
                'icon' => '📨',
            ],
            'lang_test' => [
                'label' => 'Almanca dil sınavı',
                'desc'  => 'TestDaF 195€, DSH 100-180€, telc Hochschule 145-180€. Default: TestDaF.',
                'default' => 195,
                'min' => 0, 'max' => 250,
                'required' => false,
                'recoverable' => false,
                'icon' => '📚',
            ],
            'translation' => [
                'label' => __('Belge çeviri + onaylama'),
                'desc'  => 'Diploma, transkript, kimlik tercümesi + noter onayı. Yaklaşık 250€.',
                'default' => 250,
                'min' => 100, 'max' => 600,
                'required' => true,
                'recoverable' => false,
                'icon' => '🖋️',
            ],
            'health_insurance_initial' => [
                'label' => 'Seyahat sağlık sigortası (90 gün)',
                'desc'  => __('Vize başvurusu için zorunlu. Anmeldung sonrası öğrenci sigortasına geçersin.'),
                'default' => 30,
                'min' => 20, 'max' => 80,
                'required' => true,
                'recoverable' => false,
                'icon' => '🩺',
            ],
            'flight' => [
                'label' => 'Uçak bileti (tek yön)',
                'desc'  => __('İstanbul/Ankara → Almanya. Sezonluk 200-450€. Default: 300€.'),
                'default' => 300,
                'min' => 100, 'max' => 700,
                'required' => true,
                'recoverable' => false,
                'icon' => '✈️',
            ],
            'first_month' => [
                'label' => __('Almanya\'da ilk ay (Sperrkonto dışı tampon)'),
                'desc'  => 'Anmeldung gecikirse Sperrkonto erişimi açılmaz. Cebinde ekstra bulundurmak iyi.',
                'default' => 500,
                'min' => 0, 'max' => 2000,
                'required' => false,
                'recoverable' => false,
                'icon' => '💶',
            ],
            'aps' => [
                'label' => 'APS (Türkiye için zorunlu DEĞİL, opsiyonel)',
                'desc'  => 'Akademische Prüfstelle — Türkiye için gerekli değil. (Çin/Vietnam/Hindistan için zorunlu.)',
                'default' => 0,
                'min' => 0, 'max' => 300,
                'required' => false,
                'recoverable' => false,
                'icon' => '🎓',
            ],
        ];

        // Kullanıcı değerleri (GET param ile override)
        $values = [];
        foreach ($items as $key => $item) {
            $val = $request->query($key);
            if ($val !== null && is_numeric($val)) {
                $val = max($item['min'], min($item['max'], (int) $val));
            } else {
                $val = $item['default'];
            }
            $values[$key] = $val;
        }

        // Hesaplamalar
        $total       = array_sum($values);
        $recoverable = $values['sperrkonto']; // 11.904€'yu geri alacaksın (her ay 992)
        $nonRecoverable = $total - $recoverable;

        return view('tools.visa-cost', [
            'items'           => $items,
            'values'          => $values,
            'total'           => $total,
            'recoverable'     => $recoverable,
            'non_recoverable' => $nonRecoverable,
        ]);
    }

    /**
     * Almanya öğrenci vizesi randevu rehberi (iData, Türkiye).
     * Statik içerik — tüm figürler doğrulandı + kaynak künyeli (halüsinasyon savunması).
     * TR çekirdek spearhead: 24K Telegram randevu mesajı, rakipsiz boşluk.
     */
    public function visaAppointment(): View|\Illuminate\Http\RedirectResponse
    {
        // iData yalnızca Türkiye'den başvuranları ilgilendirir → sadece Türkçe sayfada göster.
        if (app()->getLocale() !== 'tr') {
            return redirect()->route('tools.index');
        }

        return view('tools.visa-appointment');
    }

    /**
     * Almanca dil sertifikaları karşılaştırma rehberi (TestDaF / DSH / telc / Goethe).
     * TR çekirdek #2 tema (34.6K Telegram "dil" mesajı: "TestDaF mı DSH mı"). Evrensel
     * içerik → düzgün i18n. Tüm figürler doğrulandı + kaynak künyeli.
     */
    public function languageCertificates(): View
    {
        return view('tools.language-certificates');
    }

    public function recommendation(Request $request): View
    {
        $answers = null;
        $result  = null;

        if ($request->isMethod('post')) {
            $answers = $request->validate([
                'budget'    => 'required|in:low,mid,high',
                'city_size' => 'required|in:small,medium,large',
                'lang'      => 'required|in:de,en,both',
                'uni_type'  => 'required|in:public,private,any',
                'field'     => 'required|in:muhendislik,bilisim,matematik-doga,tip-saglik,hukuk-ekonomi,sosyal-bilimler,sanat-tasarim,dil-kultur,tarim-ormancilik,veteriner-spor',
                'region'    => 'required|in:nord,sued,west,ost,any',
                'lifestyle' => 'required|in:quiet,balanced,vibrant',
                'community' => 'required|in:large_intl,medium,local',
            ]);

            $result = $this->runRecommendationV2($answers);

            // Auth ise sonucu kaydet (geçmiş için)
            if ($request->user()) {
                \App\Models\UserQuizResult::create([
                    'user_id'   => $request->user()->id,
                    'quiz_type' => 'recommendation',
                    'answers'   => $answers,
                    'result'    => [
                        'personality'      => $result['personality'],
                        'university_ids'   => collect($result['universities'])->pluck('id')->toArray(),
                        'university_names' => collect($result['universities'])->pluck('name_de')->toArray(),
                        'top_score'        => $result['universities'][0]['score'] ?? null,
                    ],
                ]);
            }
        }

        return view('tools.recommendation', [
            'answers' => $answers,
            'result'  => $result,
        ]);
    }

    // ===============================================================
    // KARİYER PUSULASI — teknik (RIASEC) + duygusal (değer) analizi
    // ===============================================================

    public function careerCompass(Request $request): View
    {
        $result = null;

        if ($request->isMethod('post')) {
            // 7 RIASEC sorusu (her biri R/I/A/S/E/C harfi) + 5 değer sorusu
            $data = $request->validate([
                'q1' => 'required|in:R,I,A,S',
                'q2' => 'required|in:R,I,A,E',
                'q3' => 'required|in:R,I,A,S',
                'q4' => 'required|in:R,I,E,C',
                'q5' => 'required|in:R,I,A,S',
                'q6' => 'required|in:R,I,A,C',
                'q7' => 'required|in:R,I,A,C,S',
                'v_income'   => 'required|in:income,meaning',
                'v_rhythm'   => 'required|in:stable,dynamic',
                'v_place'    => 'required|in:office,field',
                'v_path'     => 'required|in:theory,practice',
                'v_security' => 'required|in:security,freedom',
            ]);

            $result = $this->runCareerCompass($data);

            if ($request->user()) {
                \App\Models\UserQuizResult::create([
                    'user_id'   => $request->user()->id,
                    'quiz_type' => 'career_compass',
                    'answers'   => $data,
                    'result'    => [
                        'holland_code'    => $result['holland_code'],
                        'profile_title'   => $result['profile']['title'],
                        'profession_ids'  => collect($result['professions'])->pluck('id')->toArray(),
                        'profession_names'=> collect($result['professions'])->pluck('name_tr')->toArray(),
                    ],
                ]);
            }
        }

        return view('tools.career-compass', ['result' => $result]);
    }

    private function runCareerCompass(array $a): array
    {
        // 1) RIASEC puanları
        $riasec = ['R' => 0, 'I' => 0, 'A' => 0, 'S' => 0, 'E' => 0, 'C' => 0];
        foreach (['q1','q2','q3','q4','q5','q6','q7'] as $q) {
            if (isset($a[$q]) && isset($riasec[$a[$q]])) {
                $riasec[$a[$q]] += 1;
            }
        }
        arsort($riasec);
        $topTwo = array_slice(array_keys($riasec), 0, 2);
        $hollandCode = implode('', $topTwo);

        // 2) Değer profili
        $values = [
            'income'   => $a['v_income'],
            'rhythm'   => $a['v_rhythm'],
            'place'    => $a['v_place'],
            'path'     => $a['v_path'],
            'security' => $a['v_security'],
        ];

        // 3) Field → RIASEC ağırlıkları (her alanın baskın profili)
        $fieldRiasec = [
            'muhendislik'      => ['R' => 3, 'I' => 2],
            'bilisim'          => ['I' => 3, 'C' => 2, 'R' => 1],
            'matematik-doga'   => ['I' => 3, 'R' => 1],
            'tip-saglik'       => ['S' => 3, 'I' => 2],
            'hukuk-ekonomi'    => ['E' => 3, 'C' => 2],
            'sosyal-bilimler'  => ['S' => 3, 'I' => 1],
            'sanat-tasarim'    => ['A' => 3, 'R' => 1],
            'dil-kultur'       => ['A' => 2, 'S' => 2],
            'tarim-ormancilik' => ['R' => 3, 'I' => 1],
            'veteriner-spor'   => ['R' => 2, 'S' => 2, 'I' => 1],
        ];

        // 4) Field skorla (kullanıcı RIASEC × field ağırlığı dot-product)
        $fieldScores = [];
        foreach ($fieldRiasec as $slug => $weights) {
            $score = 0;
            foreach ($weights as $letter => $w) {
                $score += ($riasec[$letter] ?? 0) * $w;
            }
            $fieldScores[$slug] = $score;
        }
        arsort($fieldScores);
        $topFieldSlugs = array_slice(array_keys($fieldScores), 0, 4);

        $fields = \App\Models\FieldOfStudy::whereIn('slug', $topFieldSlugs)->get()->keyBy('slug');
        $topFieldIds = $fields->pluck('id')->toArray();

        // 5) Bu alanlardan enrich'li meslekleri al
        $pathType = $values['path'] === 'theory' ? 'studienberuf' : 'ausbildung';

        $professions = \App\Models\Profession::query()
            ->whereIn('field_of_study_id', $topFieldIds)
            ->whereNotNull('description_tr')->where('description_tr', '!=', '')
            ->with('field:id,slug,name_tr,name_en,name_de,icon')
            ->get(['id', 'slug', 'name_tr', 'name_de', 'description_tr', 'type', 'field_of_study_id', 'cluster_label','name_en','name_de']);

        // 6) Her mesleğe value ince-skoru + neden eşleşti
        $scored = $professions->map(function ($p) use ($values, $fieldScores, $riasec, $topTwo) {
            $score = 50;
            $reasonsTech = [];
            $reasonsEmo  = [];

            $fieldSlug = $p->field?->slug;
            $fieldScore = $fieldScores[$fieldSlug] ?? 0;
            $maxFieldScore = max($fieldScores) ?: 1;
            $score += (int) round(($fieldScore / $maxFieldScore) * 30); // alan uyumu max +30

            // Teknik gerekçe (RIASEC)
            $letterLabels = [
                'R' => 'uygulamalı/pratik işler', 'I' => 'analiz ve araştırma',
                'A' => 'yaratıcılık', 'S' => 'insanlarla çalışma',
                'E' => 'liderlik/girişimcilik', 'C' => 'düzen ve sistematik iş',
            ];
            $reasonsTech[] = $p->field?->name_tr . ' alanı senin baskın profilinle (' . implode('+', array_map(fn ($l) => $letterLabels[$l] ?? $l, $topTwo)) . ') örtüşüyor';

            // Eğitim yolu (path) eşleşmesi
            if ($values['path'] === 'theory' && $p->type === 'studienberuf') {
                $score += 12;
                $reasonsEmo[] = 'Üniversite eğitimini tercih ediyorsun → bu bir Studienberuf (akademik meslek)';
            } elseif ($values['path'] === 'practice' && $p->type === 'ausbildung') {
                $score += 12;
                $reasonsEmo[] = 'Pratik mesleki eğitim istiyorsun → bu bir Ausbildung mesleği (işbaşı eğitim)';
            } elseif ($p->type === 'weiterbildung') {
                $score += 4;
            }

            // Güvenlik / özgürlük
            if ($values['security'] === 'security' && in_array($p->type, ['ausbildung', 'grundberuf', 'studienberuf'])) {
                $score += 6;
                $reasonsEmo[] = 'İş güvenliği önemli senin için → bu meslek net, tanımlı bir kariyer yolu sunar';
            } elseif ($values['security'] === 'freedom') {
                $score += 4;
                $reasonsEmo[] = 'Esneklik/özgürlük istiyorsun → bu alanda serbest çalışma (Freiberufler) imkânı araştırılabilir';
            }

            // Ofis / saha
            $realisticFields = ['muhendislik', 'tarim-ormancilik', 'veteriner-spor', 'sanat-tasarim'];
            if ($values['place'] === 'field' && in_array($fieldSlug, $realisticFields)) {
                $score += 5;
                $reasonsEmo[] = 'Sahada/hareketli çalışmayı seviyorsun → bu meslek masabaşına bağlı değil';
            } elseif ($values['place'] === 'office' && in_array($fieldSlug, ['hukuk-ekonomi', 'bilisim'])) {
                $score += 5;
                $reasonsEmo[] = 'Düzenli ofis ortamı tercih ediyorsun → bu alan buna uygun';
            }

            // Anlam / gelir
            if ($values['income'] === 'meaning' && in_array($fieldSlug, ['tip-saglik', 'sosyal-bilimler', 'dil-kultur'])) {
                $score += 5;
                $reasonsEmo[] = 'Topluma faydalı, anlamlı iş arıyorsun → bu meslek doğrudan insanlara dokunuyor';
            } elseif ($values['income'] === 'income' && in_array($fieldSlug, ['bilisim', 'muhendislik', 'hukuk-ekonomi'])) {
                $score += 5;
                $reasonsEmo[] = 'Gelir önceliğin → bu alan Almanya\'da yüksek kazanç potansiyeli taşır';
            }

            $score = min(100, max(0, $score));

            return [
                'id'            => $p->id,
                'slug'          => $p->slug,
                'name_tr'       => $p->name_tr ?: $p->name_de,
                'name_de'       => $p->name_de,
                'description'   => \Illuminate\Support\Str::limit(strip_tags($p->description_tr), 180),
                'type'          => $p->type,
                'field'         => $p->field,
                'score'         => $score,
                'reasons_tech'  => array_slice($reasonsTech, 0, 2),
                'reasons_emo'   => array_slice($reasonsEmo, 0, 3),
            ];
        });

        // 7) Çeşitlilik: aynı alandan max 3, toplam 8
        $perFieldCount = [];
        $top = $scored->sortByDesc('score')->filter(function ($p) use (&$perFieldCount) {
            $f = $p['field']?->slug ?? '?';
            $perFieldCount[$f] = ($perFieldCount[$f] ?? 0) + 1;
            return $perFieldCount[$f] <= 3;
        })->take(8)->values()->all();

        // 8) İlgili programlar (top field'lerden, dil tercihi yok)
        $relatedPrograms = \App\Models\Program::whereIn('field_of_study_id', $topFieldIds)
            ->where('is_active', 1)
            ->with('university:id,name_de,slug,city_id', 'university.city:id,name_tr,name_en,name_de,slug')
            ->orderByRaw('CASE WHEN language IN ("en","both") THEN 0 ELSE 1 END')
            ->take(6)
            ->get(['id', 'slug', 'name_tr', 'name_de', 'degree', 'language', 'university_id', 'field_of_study_id','name_en','name_de']);

        return [
            'holland_code' => $hollandCode,
            'riasec'       => $riasec,
            'values'       => $values,
            'profile'      => $this->careerProfile($topTwo, $values),
            'top_fields'   => $topFieldSlugs,
            'fields'       => $fields,
            'professions'  => $top,
            'programs'     => $relatedPrograms,
        ];
    }

    private function careerProfile(array $topTwo, array $values): array
    {
        $code = implode('', $topTwo);
        $map = [
            'RI' => ['emoji' => '🔧', 'title' => 'Analist-Yapıcı', 'desc' => 'Hem elini taşın altına koyan hem de mantıkla çözen tip. Mühendislik ve teknik alanlar senin doğal sahan.'],
            'IR' => ['emoji' => '🔬', 'title' => 'Araştırmacı-Mühendis', 'desc' => 'Önce anlar, sonra üretir. Bilim, mühendislik ve teknoloji seni çeker.'],
            'IA' => ['emoji' => '🧪', 'title' => 'Yaratıcı Düşünür', 'desc' => 'Analitik zekâ + yaratıcılık. Araştırma, tasarım ve inovasyon senin alanın.'],
            'AI' => ['emoji' => '🎨', 'title' => 'Yaratıcı Analist', 'desc' => 'Sanatsal vizyon + mantık. Tasarım, mimari, dijital yaratıcılık sana uygun.'],
            'AS' => ['emoji' => '🎭', 'title' => 'İfadeci-Sosyal', 'desc' => 'Yaratıcı ve insan odaklı. Dil, kültür, eğitim ve iletişim alanları seni besler.'],
            'SA' => ['emoji' => '🤝', 'title' => 'Sosyal-Yaratıcı', 'desc' => __('İnsanlarla bağ kurmayı ve yaratıcı ifadeyi seversin. Sağlık, eğitim, sosyal hizmet.')],
            'SI' => ['emoji' => '🩺', 'title' => 'Şefkatli Çözücü', 'desc' => 'İnsana yardım + analiz. Tıp, sağlık ve sosyal bilimler senin alanın.'],
            'IS' => ['emoji' => '🧠', 'title' => 'Analitik Yardımcı', 'desc' => 'Araştırma + insan odağı. Psikoloji, tıp araştırması, sosyal bilim sana uygun.'],
            'EC' => ['emoji' => '💼', 'title' => 'Lider-Organizatör', 'desc' => 'Girişimcilik + düzen. İşletme, hukuk, ekonomi ve yönetim senin sahan.'],
            'CE' => ['emoji' => '📊', 'title' => 'Sistematik Yönetici', 'desc' => 'Düzen + liderlik. Finans, denetim, idari ve hukuki alanlar sana uygun.'],
            'RE' => ['emoji' => '🏗️', 'title' => 'Pratik Girişimci', 'desc' => 'Uygulama + iş kurma. Teknik girişimcilik, üretim yönetimi senin alanın.'],
            'IC' => ['emoji' => '💻', 'title' => 'Veri-Sistem Tipi', 'desc' => 'Analiz + düzen. Bilişim, veri bilimi, yazılım senin doğal sahan.'],
            'CI' => ['emoji' => '🖥️', 'title' => 'Sistematik Analist', 'desc' => 'Düzen + araştırma. IT, sistem yönetimi, veri analizi sana uygun.'],
        ];

        return $map[$code] ?? [
            'emoji' => '🧭',
            'title' => 'Çok Yönlü Profil',
            'desc' => 'Birden fazla güçlü yönün var — bu da geniş bir meslek yelpazesi demek. Aşağıdaki önerileri keşfet.',
        ];
    }

    // ---------------------------------------------------------------
    // Helpers
    // ---------------------------------------------------------------

    private function buildCostBreakdown(City $city, string $housing, string $lifestyle): array
    {
        $cd = $city->costData;

        $rent = match ($housing) {
            'studio'    => $cd->rent_studio,
            'apartment' => $cd->rent_apartment,
            default     => $cd->rent_wg,
        };

        $multiplier = match ($lifestyle) {
            'frugal'      => 0.75,
            'comfortable' => 1.30,
            default       => 1.00,
        };

        $food          = (int) round($cd->food          * $multiplier);
        $entertainment = (int) round($cd->entertainment * $multiplier);
        $misc          = (int) round($cd->misc          * $multiplier);

        $items = [
            ['key' => 'rent',          'label' => $this->housingLabel($housing), 'value' => $rent,           'fixed' => true],
            ['key' => 'food',          'label' => 'Yemek (market + dışarı)',     'value' => $food,           'fixed' => false],
            ['key' => 'transport',     'label' => 'Ulaşım (Semester / Deutschlandticket)', 'value' => $cd->transport, 'fixed' => true],
            ['key' => 'utilities',     'label' => 'İnternet + Elektrik + Telefon', 'value' => $cd->utilities, 'fixed' => true],
            ['key' => 'insurance',     'label' => __('Sağlık sigortası (öğrenci)'),  'value' => $cd->health_insurance, 'fixed' => true],
            ['key' => 'entertainment', 'label' => 'Eğlence + Spor',              'value' => $entertainment,  'fixed' => false],
            ['key' => 'misc',          'label' => 'Diğer (kıyafet, kırtasiye)',  'value' => $misc,           'fixed' => false],
        ];

        $total      = array_sum(array_column($items, 'value'));
        $totalYear  = $total * 12;
        $blockedAcc = max($total * 12, 11904); // Sperrkonto 2025: 992 EUR/ay = 11904/yıl

        return [
            'city'              => $city,
            'tier'              => $cd->tier,
            'items'             => $items,
            'total_month'       => $total,
            'total_year'        => $totalYear,
            'blocked_account'   => $blockedAcc,
            'housing'           => $housing,
            'housing_label'     => $this->housingLabel($housing),
            'lifestyle'         => $lifestyle,
            'lifestyle_label'   => $this->lifestyleLabel($lifestyle),
        ];
    }

    private function housingLabel(string $housing): string
    {
        return match ($housing) {
            'studio'    => 'Stüdyo daire (1-Zimmer)',
            'apartment' => 'Apartman (2-Zimmer)',
            default     => 'WG / Paylaşımlı oda',
        };
    }

    private function lifestyleLabel(string $lifestyle): string
    {
        return match ($lifestyle) {
            'frugal'      => 'Tutumlu',
            'comfortable' => 'Konforlu',
            default       => 'Normal',
        };
    }

    /**
     * Modifizierte bayerische Formel:
     *   Nd = 1 + 3 * (Nmax - Nx) / (Nmax - Nmin)
     *
     * TR 4'lük: Nmax=4.0, Nmin=2.0 (geçer not)
     * TR 100'lük: Nmax=100, Nmin=60 (geçer not)
     */
    private function convertGrade(string $system, float $grade): array
    {
        if ($system === 'tr100') {
            $nmax = 100.0;
            $nmin = 60.0;
            $valid = $grade >= 0 && $grade <= 100;
        } else {
            $nmax = 4.0;
            $nmin = 2.0;
            $valid = $grade >= 0 && $grade <= 4.0;
        }

        if (! $valid) {
            return ['valid' => false, 'message' => 'Geçerli bir not gir (TR 4\'lük: 0-4, TR 100\'lük: 0-100).'];
        }

        // Geçer notun altında mı?
        if ($grade < $nmin) {
            return [
                'valid'       => true,
                'german'      => 5.0,
                'german_text' => 'nicht ausreichend (5)',
                'note'        => __('Bu not Almanya\'da geçer not değil (5,0 = fail).'),
                'grade'       => $grade,
            ];
        }

        $nd = 1 + 3 * ($nmax - $grade) / ($nmax - $nmin);
        $nd = round($nd, 1);
        $nd = max(1.0, min(4.0, $nd));

        return [
            'valid'       => true,
            'german'      => $nd,
            'german_text' => $this->germanGradeText($nd),
            'note'        => null,
            'grade'       => $grade,
        ];
    }

    private function germanGradeText(float $nd): string
    {
        return match (true) {
            $nd <= 1.5 => 'sehr gut (çok iyi)',
            $nd <= 2.5 => 'gut (iyi)',
            $nd <= 3.5 => 'befriedigend (orta)',
            $nd <= 4.0 => 'ausreichend (geçer)',
            default    => 'nicht ausreichend (kalır)',
        };
    }

    private function gradeTable(string $system): array
    {
        if ($system === 'tr100') {
            $steps = [100, 95, 90, 85, 80, 75, 70, 65, 60];
        } else {
            $steps = [4.0, 3.7, 3.5, 3.3, 3.0, 2.7, 2.5, 2.3, 2.0];
        }

        $rows = [];
        foreach ($steps as $g) {
            $c = $this->convertGrade($system, (float) $g);
            $rows[] = ['tr' => $g, 'de' => $c['german'], 'text' => $c['german_text']];
        }
        return $rows;
    }

    /**
     * V2: Skor tabanlı eşleştirme. Her üni 0-100 puan alır.
     * Veri kaynakları: programs.language, programs.field_of_study_id, cities.population,
     * cities.avg_rent_min, states.region, universities.type, universities.student_count
     */
    private function runRecommendationV2(array $answers): array
    {
        $fieldSlug = $answers['field'];
        $field     = \App\Models\FieldOfStudy::where('slug', $fieldSlug)->first();
        $fieldId   = $field?->id;

        // 1) Şehirleri bütçe + bölge ile ön-filtrele
        $cityIds = $this->shortlistCitiesV2($answers['budget'], $answers['region']);

        if (empty($cityIds)) {
            // Tamamen daralırsa tüm aktif şehirler
            $cityIds = City::where('is_active', 1)->pluck('id')->toArray();
        }

        // 2) Üniversite havuzu: şehir + tip + (alan/dil programı var mı)
        $uniQ = University::query()
            ->where('is_active', true)
            ->whereIn('city_id', $cityIds);

        if ($answers['uni_type'] !== 'any') {
            $uniQ->where('type', $answers['uni_type']);
        }

        // Alan + dil bir bonus puana dönüşür (filtre değil) — boş sonuç ekranını engelle.
        // Önce alan eşleşenleri öne almak için sort.
        if ($fieldId) {
            $uniQ->withCount(['programs as field_programs_count' => fn ($q) => $q
                ->where('field_of_study_id', $fieldId)
                ->where('is_active', 1)]);
            $uniQ->orderByDesc('field_programs_count');
        }

        $universities = $uniQ
            ->with(['city:id,name_tr,name_en,name_de,slug,population,avg_rent_min,avg_rent_max,state_id', 'city.state:id,name_tr,name_en,name_de,region'])
            ->limit(60)
            ->get(['id', 'slug', 'name_de', 'name_en', 'short_name', 'type', 'city_id', 'logo_url', 'student_count', 'founded_year']);

        // 3) Her üniversiteye skor ver + neden eşleşti listesi
        $scored = $universities->map(function ($uni) use ($answers, $fieldId, $fieldSlug) {
            $score   = 0;
            $reasons = [];

            // a) Bütçe (max 20)
            $cityRent = $uni->city?->avg_rent_min ?: 250;
            $budgetScore = match ($answers['budget']) {
                'low'  => $cityRent <= 250 ? 20 : ($cityRent <= 320 ? 12 : 4),
                'mid'  => $cityRent >= 230 && $cityRent <= 320 ? 20 : 12,
                'high' => $cityRent >= 280 ? 20 : 14,
            };
            $score += $budgetScore;
            if ($budgetScore >= 16) {
                $reasons[] = ['icon' => '💰', 'text' => 'Bütçene uygun şehir (€' . $cityRent . '–€' . ($uni->city?->avg_rent_max ?: $cityRent + 80) . '/ay)'];
            }

            // b) Şehir büyüklüğü (max 15)
            $pop = $uni->city?->population ?: 100000;
            $sizeScore = match ($answers['city_size']) {
                'large'  => $pop >= 500000 ? 15 : ($pop >= 250000 ? 8 : 3),
                'medium' => $pop >= 150000 && $pop < 500000 ? 15 : 8,
                'small'  => $pop < 150000 ? 15 : ($pop < 300000 ? 8 : 3),
            };
            $score += $sizeScore;
            if ($sizeScore >= 12) {
                $reasons[] = ['icon' => '🏙️', 'text' => match (true) {
                    $pop >= 500000 => 'Büyük metropol (' . number_format($pop, 0, ',', '.') . ' nüfus)',
                    $pop >= 150000 => 'Orta büyüklükte şehir (' . number_format($pop, 0, ',', '.') . ' nüfus)',
                    default        => 'Sakin öğrenci şehri (' . number_format($pop, 0, ',', '.') . ' nüfus)',
                }];
            }

            // c) Bölge (max 10)
            if ($answers['region'] !== 'any' && $uni->city?->state?->region === $answers['region']) {
                $score += 10;
                $reasons[] = ['icon' => '🧭', 'text' => 'İstediğin bölgede (' . $this->regionLabel($answers['region']) . ' Almanya)'];
            } elseif ($answers['region'] === 'any') {
                $score += 6;
            }

            // d) Üniversite tipi (max 10)
            if ($answers['uni_type'] === 'any') {
                $score += 6;
            } elseif ($uni->type === $answers['uni_type']) {
                $score += 10;
                $reasons[] = ['icon' => '🎓', 'text' => $answers['uni_type'] === 'public' ? 'Devlet üniversitesi (harç yok / düşük)' : 'Özel üniversite (butik / İngilizce odaklı)'];
            }

            // e) Alan + Dil programı sayısı (max 25)
            $programCount = 0;
            $englishProgramCount = 0;
            if ($fieldId) {
                $programCount = \DB::table('programs')
                    ->where('university_id', $uni->id)
                    ->where('field_of_study_id', $fieldId)
                    ->where('is_active', 1)
                    ->count();

                $englishProgramCount = \DB::table('programs')
                    ->where('university_id', $uni->id)
                    ->where('field_of_study_id', $fieldId)
                    ->where('is_active', 1)
                    ->whereIn('language', ['en', 'both'])
                    ->count();

                $fieldScore = match (true) {
                    $programCount >= 20 => 15,
                    $programCount >= 8  => 12,
                    $programCount >= 3  => 8,
                    $programCount >= 1  => 5,
                    default             => 0,
                };
                $score += $fieldScore;
                if ($programCount >= 3) {
                    $reasons[] = ['icon' => '📚', 'text' => $programCount . ' program — alanın güçlü'];
                }

                // dil bonus
                if ($answers['lang'] === 'en' && $englishProgramCount >= 1) {
                    $score += 10;
                    $reasons[] = ['icon' => '🇬🇧', 'text' => $englishProgramCount . ' İngilizce program'];
                } elseif ($answers['lang'] === 'de' && $programCount > $englishProgramCount) {
                    $score += 10;
                    $reasons[] = ['icon' => '🇩🇪', 'text' => 'Almanca programlar mevcut'];
                } elseif ($answers['lang'] === 'both') {
                    $score += 8;
                }
            }

            // f) Lifestyle/topluluk (max 20)
            // student_count'a göre "ne kadar canlı"
            $sc = $uni->student_count ?: 5000;
            $lifestyleScore = match ($answers['lifestyle']) {
                'quiet'    => $sc < 15000 ? 10 : 4,
                'balanced' => $sc >= 8000 && $sc <= 25000 ? 10 : 6,
                'vibrant'  => $sc >= 20000 ? 10 : 5,
            };
            $score += $lifestyleScore;
            if ($lifestyleScore >= 8) {
                $reasons[] = ['icon' => '✨', 'text' => match ($answers['lifestyle']) {
                    'quiet'    => 'Sakin/yoğun olmayan kampüs',
                    'balanced' => 'Dengeli kampüs hayatı',
                    'vibrant'  => 'Hareketli, büyük öğrenci topluluğu',
                }];
            }

            // Topluluk (Türk + uluslararası öğrenci): büyük şehir + büyük üni → büyük topluluk varsayımı
            $communityScore = match ($answers['community']) {
                'large_intl' => ($pop >= 400000 && $sc >= 15000) ? 10 : 5,
                'medium'     => ($pop >= 150000 && $pop < 600000) ? 10 : 6,
                'local'      => ($pop < 200000) ? 10 : 5,
            };
            $score += $communityScore;

            // Skor normalleştir
            $score = min(100, max(0, $score));

            return [
                'id'           => $uni->id,
                'slug'         => $uni->slug,
                'name_de'      => $uni->name_de,
                'short_name'   => $uni->short_name,
                'type'         => $uni->type,
                'logo_url'     => $uni->logo_url,
                'student_count'=> $uni->student_count,
                'founded_year' => $uni->founded_year,
                'city'         => $uni->city,
                'score'        => $score,
                'reasons'      => array_slice($reasons, 0, 5),
                'program_count'=> $programCount,
            ];
        });

        // Skora göre sırala + ilk 6
        $top = $scored->sortByDesc('score')->take(6)->values()->all();

        // Kişilik tipi
        $personality = $this->detectPersonality($answers);

        return [
            'universities' => $top,
            'personality'  => $personality,
            'total_pool'   => $scored->count(),
            'answers'      => $answers,
        ];
    }

    private function shortlistCitiesV2(string $budget, string $region): array
    {
        $q = City::query()->where('is_active', 1);

        if ($region !== 'any') {
            $q->whereHas('state', fn ($s) => $s->where('region', $region));
        }

        // Bütçe filtresi: avg_rent_min'e göre — null olanlar geçer (tahmin yapamayız)
        $q->where(function ($w) use ($budget) {
            $w->whereNull('avg_rent_min');
            match ($budget) {
                'low'  => $w->orWhere('avg_rent_min', '<=', 280),
                'mid'  => $w->orWhereBetween('avg_rent_min', [220, 340]),
                'high' => $w->orWhere('avg_rent_min', '>=', 250),
            };
        });

        return $q->pluck('id')->toArray();
    }

    /**
     * Cevaplara göre "kişilik tipi" + tek satır profil çıkar.
     * Bu sadece eğlence amaçlıdır, skoru etkilemez.
     */
    private function detectPersonality(array $a): array
    {
        // Tip belirleme: lifestyle + community + city_size
        if ($a['city_size'] === 'large' && $a['lifestyle'] === 'vibrant') {
            return ['emoji' => '🌆', 'title' => 'Metropol Tipi', 'description' => 'Berlin/Hamburg/München tipi — büyük şehir, kalabalık, sürekli aksiyon.'];
        }
        if ($a['city_size'] === 'small' && $a['lifestyle'] === 'quiet') {
            return ['emoji' => '🌲', 'title' => __('Klasik Üni-Şehir Tipi'), 'description' => 'Tübingen/Göttingen/Heidelberg tipi — bisiklet + bira bahçesi + akademik odak.'];
        }
        if ($a['region'] === 'sued' && $a['lifestyle'] !== 'vibrant') {
            return ['emoji' => '🏔️', 'title' => 'Bavyera Tipi', 'description' => __('Güney Almanya — dağ + brezel + güçlü teknik üniversite.')];
        }
        if ($a['region'] === 'nord') {
            return ['emoji' => '⚓', 'title' => 'Kuzey Tipi', 'description' => 'Hamburg/Bremen/Kiel — liman + denizci + minimal estetik.'];
        }
        if ($a['region'] === 'ost') {
            return ['emoji' => '🎨', 'title' => __('Doğu Almanya Tipi'), 'description' => 'Leipzig/Dresden — ucuz + sanatçı + alternatif sahne.'];
        }
        if ($a['budget'] === 'low' && $a['lifestyle'] !== 'vibrant') {
            return ['emoji' => '💪', 'title' => 'Pratik Bütçe Tipi', 'description' => 'Akıllı para — düşük kira + güçlü uni = en uygun yatırım.'];
        }
        if ($a['field'] === 'muhendislik' && $a['region'] === 'sued') {
            return ['emoji' => '⚙️', 'title' => 'Bavyera Mühendisi', 'description' => __('TU München/Stuttgart/Karlsruhe — Almanya\'nın endüstri kalbi.')];
        }
        if (in_array($a['field'], ['bilisim', 'matematik-doga'])) {
            return ['emoji' => '💻', 'title' => 'Tech Tipi', 'description' => __('KIT/TU München/Aachen — Almanya\'nın tech merkezleri seni bekliyor.')];
        }
        return ['emoji' => '🎯', 'title' => 'Dengeli Tipi', 'description' => 'Her şeyden biraz — bütçe + akademik + lifestyle dengesi.'];
    }

    private function regionLabel(string $r): string
    {
        return match ($r) {
            'nord' => 'Kuzey',
            'sued' => 'Güney',
            'west' => 'Batı',
            'ost'  => 'Doğu',
            default => $r,
        };
    }

    // ---- ESKİ (eski API call'lar geriye dönük çalışsın diye burada) ----
    private function runRecommendation(array $answers): array
    {
        // 1. Şehir filtresi (bütçe + şehir büyüklüğü)
        $cityIds = $this->shortlistCities($answers['budget'], $answers['city_size']);

        // 2. Üniversite filtresi
        $q = University::query()
            ->where('is_active', true)
            ->whereIn('city_id', $cityIds);

        if ($answers['uni_type'] !== 'any') {
            $q->where('type', $answers['uni_type']);
        }

        $q->orderByRaw('CASE WHEN logo_url IS NOT NULL THEN 0 ELSE 1 END')
          ->orderByDesc('student_count');

        $universities = $q->limit(6)
            ->with(['city:id,name_tr,name_en,name_de,slug', 'city.costData','name_en','name_de'])
            ->get(['id', 'slug', 'name_de', 'short_name', 'type', 'city_id', 'logo_url', 'student_count', 'founded_year']);

        return [
            'cities_count'    => count($cityIds),
            'universities'    => $universities,
            'budget_label'    => $this->budgetLabel($answers['budget']),
            'city_size_label' => $this->citySizeLabel($answers['city_size']),
            'lang_label'      => $this->langLabel($answers['lang']),
            'uni_type_label'  => $this->uniTypeLabel($answers['uni_type']),
            'field_label'     => $this->fieldLabel($answers['field']),
        ];
    }

    private function shortlistCities(string $budget, string $citySize): array
    {
        $costQ = CityCostData::query();

        $costQ->whereIn('tier', match ($budget) {
            'low'  => ['affordable', 'mid'],
            'mid'  => ['mid', 'expensive'],
            'high' => ['expensive', 'very_expensive', 'mid'],
        });

        $cityIds = $costQ->pluck('city_id')->toArray();

        // Eğer city_size filtresi de gerekiyorsa Üni sayısına göre eleme
        $citiesQuery = City::whereIn('id', $cityIds)->withCount('universities');

        $cities = $citiesQuery->get(['id', 'universities_count'])
            ->filter(function ($c) use ($citySize) {
                return match ($citySize) {
                    'large'  => $c->universities_count >= 6,
                    'medium' => $c->universities_count >= 3 && $c->universities_count < 6,
                    'small'  => $c->universities_count < 3,
                };
            });

        // Hiç şehir kalmadıysa filtreyi gevşet
        if ($cities->isEmpty()) {
            return $cityIds;
        }

        return $cities->pluck('id')->toArray();
    }

    private function budgetLabel(string $b): string
    {
        return match ($b) {
            'low'  => '< 800 EUR/ay (uygun fiyatlı)',
            'mid'  => '800-1200 EUR/ay (orta)',
            'high' => '> 1200 EUR/ay (yüksek)',
        };
    }

    private function citySizeLabel(string $s): string
    {
        return match ($s) {
            'small'  => 'Küçük öğrenci şehri',
            'medium' => 'Orta büyüklükte şehir',
            'large'  => 'Büyük metropol',
        };
    }

    private function langLabel(string $l): string
    {
        return match ($l) {
            'de'   => 'Almanca',
            'en'   => 'İngilizce',
            'both' => 'Almanca veya İngilizce',
        };
    }

    private function uniTypeLabel(string $t): string
    {
        return match ($t) {
            'public'  => 'Devlet üniversitesi',
            'private' => 'Özel üniversite',
            'any'     => 'Fark etmez',
        };
    }

    private function fieldLabel(string $f): string
    {
        return match ($f) {
            'engineering' => 'Mühendislik',
            'medicine'    => __('Tıp / Sağlık'),
            'social'      => 'Sosyal Bilimler',
            'economics'   => 'Ekonomi / İşletme',
            'arts'        => 'Sanat / Tasarım',
            'it'          => 'Bilişim / Bilgisayar',
        };
    }

    // ════════════════════════════════════════════════════════════════════
    // PROFESSIONAL RECOGNITION (Mesleki Denklik) — Almanya'da çalışabilmek
    // için Anerkennung gerekli mi? Hangi kurum, ne kadar sürer, maliyet?
    // ════════════════════════════════════════════════════════════════════
    public function professionalRecognition(Request $request): View
    {
        $professions = self::recognitionProfessions();
        $result = null;

        if ($request->isMethod('post')) {
            $data = $request->validate([
                'profession_key' => 'required|string',
                'country_origin' => 'nullable|string|max:8',
                'work_experience_years' => 'nullable|integer|min:0|max:50',
            ]);

            $key = $data['profession_key'];
            if (isset($professions[$key])) {
                $profession = $professions[$key];
                $result = $this->assessProfessionalRecognition($profession, $data);
            }
        }

        return view('tools.professional-recognition', [
            'professions' => $professions,
            'result' => $result,
            'old' => $request->all(),
        ]);
    }

    /**
     * Almanya'da Anerkennung kanonik mesleklerin matrisi.
     * Veri kaynakları: anerkennung-in-deutschland.de + BERUFENET + Bundesärztekammer.
     */
    private static function recognitionProfessions(): array
    {
        return [
            'doctor' => [
                'name' => 'Hekim (Doktor)',
                'name_en' => 'Medical Doctor',
                'icon' => '🩺',
                'regulated' => true,
                'authority' => 'Landesärztekammer (eyalet tabipler odası)',
                'authority_url' => 'https://www.bundesaerztekammer.de/',
                'estimated_months' => '6–18',
                'estimated_cost_eur' => '€400–800',
                'language_required' => 'B2/C1 Almanca + medikal C1 sınav (Fachsprachenprüfung)',
                'process_steps' => [
                    'Diploma + transkript Beglaubigte Übersetzung',
                    'Approbation (lisans) için Anerkennungsbehörde\'ye başvuru',
                    'Eyaletin Gleichwertigkeitsprüfung\'ı (denklik testi) — gerekirse Kenntnisprüfung (8 saatlik bilgi testi)',
                    'Fachsprachenprüfung (medikal Almanca sınavı)',
                    'Approbation belgesi düzenlenir',
                ],
                'pitfalls' => [
                    'Anerkennung olmadan asistan olarak çalışılamaz (Berufserlaubnis ile geçici 2 yıl mümkün, sonra Approbation şart)',
                    'Eyalet bazlı denklik testleri farklı — Bavyera daha katı, Berlin görece esnek',
                    'Türk diploması için klinik staj eksikse Kenntnisprüfung mecburi',
                ],
            ],
            'engineer' => [
                'name' => 'Mühendis',
                'name_en' => 'Engineer',
                'icon' => '⚙️',
                'regulated' => false,
                'authority' => 'IHK FOSA (Foreign Skills Approval) — opsiyonel sertifikasyon',
                'authority_url' => 'https://www.ihk-fosa.de/',
                'estimated_months' => '0–3',
                'estimated_cost_eur' => '€0 (opsiyonel: €100–500 IHK FOSA)',
                'language_required' => 'B1/B2 (işverene göre)',
                'process_steps' => [
                    'Doğrudan iş başvurusu yapabilirsin (regulated meslek değil)',
                    'CV + diploma çevirisi yeterli, Anerkennung ZORUNLU DEĞİL',
                    '(Opsiyonel) IHK FOSA üzerinden "Zeugnisbewertung" — denklik belgesi maaş pazarlığında işe yarar',
                    '"Beratender Ingenieur" (consulting engineer) gibi spesifik ünvan için ayrı kayıt gerek',
                ],
                'pitfalls' => [
                    'Diploma denkliği iş için şart değil ama Blue Card için ZAB Zeugnisbewertung istenir',
                    'Public sector pozisyonlar genelde resmi denklik bekler — özel sektör daha esnek',
                    'Almanca B2 olmadan Junior pozisyon nadir, Senior yabancı dilde mümkün',
                ],
            ],
            'nurse' => [
                'name' => 'Hemşire',
                'name_en' => 'Nurse',
                'icon' => '👩‍⚕️',
                'regulated' => true,
                'authority' => 'Eyalet sağlık otoritesi (örn. Regierungspräsidium Düsseldorf)',
                'authority_url' => 'https://www.anerkennung-in-deutschland.de/',
                'estimated_months' => '4–12',
                'estimated_cost_eur' => '€200–600',
                'language_required' => 'B2 Almanca + bazı eyaletlerde sağlık dili sınavı',
                'process_steps' => [
                    'Diploma + müfredat + 2 yıl Türk hemşirelik tecrübesi belgesi',
                    'Eyalet otoritesine "Berufserlaubnis" başvurusu',
                    'Eğitim farkı tespit edilirse 6 aya kadar Anpassungslehrgang (uyum kursu) veya Kenntnisprüfung',
                    'B2 Almanca + bazı eyaletlerde Pflege-Fachsprachenprüfung',
                    'Anerkennung belgesi → tam yetkili hemşire olarak çalışma',
                ],
                'pitfalls' => [
                    'Türk müfredatı 3 yıllık olduğu için bazı eyaletler doğrudan onayladığı halde NRW gibi eyaletler kursu ister',
                    'Pflegehelfer (yardımcı) olarak işe başlanabilir ama maaş %30 düşük',
                    'Hızlı yol: "Triple Win" programı (GIZ + Bundesagentur) — 6–9 ay tüm süreç paketlenmiş',
                ],
            ],
            'teacher' => [
                'name' => 'Öğretmen',
                'name_en' => 'Teacher',
                'icon' => '🎓',
                'regulated' => true,
                'authority' => 'Eyalet Kultusministerium (eğitim bakanlığı)',
                'authority_url' => 'https://www.anerkennung-in-deutschland.de/',
                'estimated_months' => '12–24',
                'estimated_cost_eur' => '€200–500',
                'language_required' => 'C1/C2 Almanca + Almanca branş bilgisi',
                'process_steps' => [
                    'Hangi okul tipinde öğretmenlik (ilkokul / Gymnasium / meslek okulu) — başvuru otoritesini etkiler',
                    'Diploma + tezler + pedagojik formasyon belgesi çevirisi',
                    'Eyalet bakanlığına resmi "Lehrerlaubnis" başvurusu',
                    'Eğitim eksiği için 6–24 ay "Anpassungsmaßnahme" (uyum eğitimi)',
                    'Quereinstieg programları (yan giriş) yeni öğretmen açığı olan branşlarda hızlı yol',
                ],
                'pitfalls' => [
                    'Branşa göre çok değişken — STEM açıkken sosyal bilim kontenjanı dolu',
                    'Tek branş yetmez, çoğu eyalet 2 branş ister',
                    'Devlet okulu için Beamter (memur) statüsüne C1+ ve 45 yaş altı şart genelde',
                ],
            ],
            'it_specialist' => [
                'name' => 'IT Uzmanı / Yazılım Geliştirici',
                'name_en' => 'IT Specialist / Software Developer',
                'icon' => '💻',
                'regulated' => false,
                'authority' => 'Yok — direkt iş başvurusu serbest',
                'authority_url' => 'https://www.make-it-in-germany.com/en/working-in-germany/it-specialists',
                'estimated_months' => '0',
                'estimated_cost_eur' => '€0',
                'language_required' => 'B1 Almanca veya B2 İngilizce (işverene göre)',
                'process_steps' => [
                    'Diploma + iş tecrübesi + portfolio ile doğrudan başvuru',
                    'Anerkennung GEREKMEZ — IT serbest meslek',
                    'Blue Card için lisans diploması + €45.300 yıllık maaş şart',
                    'Diploma yoksa "Fachkräfteeinwanderungsgesetz" altında 3+ yıl tecrübe + €50.000 maaş ile Blue Card mümkün',
                ],
                'pitfalls' => [
                    'En kolay sektör ama remote-only pozisyonlar Blue Card için yetmiyor — fiziksel iş yeri Almanya\'da olmalı',
                    'Junior pozisyon bulmak yetenek/portfolio gerek, Senior daha kolay',
                    'Self-employed IT için ayrı vize var ama özel: "Freiberufler"',
                ],
            ],
            'lawyer' => [
                'name' => 'Avukat',
                'name_en' => 'Lawyer',
                'icon' => '⚖️',
                'regulated' => true,
                'authority' => 'Eyalet Anwaltskammer',
                'authority_url' => 'https://www.brak.de/',
                'estimated_months' => '24–48',
                'estimated_cost_eur' => '€500–1500',
                'language_required' => 'C1/C2 Almanca + Alman hukuku bilgisi',
                'process_steps' => [
                    'Türk hukuk diploması doğrudan tanınmıyor — Alman hukuku eğitimi gerek',
                    'Universität\'te 2–4 yıl Aufbaustudium (yapı tamamlama eğitimi) — bazıları Master LL.M.',
                    '1. ve 2. Staatsexamen (devlet sınavları) — Alman öğrencileriyle aynı kademe',
                    'Referendariat (2 yıl staj) — devlet hizmetinde maaşlı staj',
                    'Tam Anwalt yetkisi → Anwaltskammer\'a kayıt',
                ],
                'pitfalls' => [
                    'Almanya\'da Türk hukuku üzerine "Beratungsanwalt" (danışman avukat) olabilirsin — Staatsexamen gerekmez ama yetkilerin sınırlı',
                    'AB hukuku odaklı LL.M. sonrası bazı uluslararası firmalar işe alır',
                    'Tam yol uzun + pahalı — 5–7 yıllık taahhüt',
                ],
            ],
        ];
    }

    private function assessProfessionalRecognition(array $profession, array $data): array
    {
        $exp = (int) ($data['work_experience_years'] ?? 0);
        $notes = [];

        if ($exp >= 5 && ! $profession['regulated']) {
            $notes[] = '✅ ' . $exp . ' yıl tecrübe ile Senior pozisyon hedefleyebilirsin — Anerkennung\'a gerek yok.';
        }
        if ($profession['regulated'] && $exp < 2) {
            $notes[] = '⚠️ Regulated meslek + 2 yıldan az tecrübe = denklik testleri daha sıkı olabilir.';
        }
        if (in_array($data['country_origin'] ?? 'TR', ['TR', 'tr'])) {
            $notes[] = 'ℹ️ Türk diplomaları için spesifik: Çevirilerin Beglaubigte Übersetzung olması ve transkriptin notu içermesi şart.';
        }

        return [
            'profession' => $profession,
            'verdict' => $profession['regulated']
                ? 'regulated'
                : 'free',
            'next_step_url' => 'https://www.anerkennung-in-deutschland.de/',
            'notes' => $notes,
        ];
    }

    public function studienkolleg(Request $request): View
    {
        $query = \App\Models\Studienkolleg::active()
            ->with(['city:id,name_de,name_en,name_tr,slug', 'state:id,name_de,name_en,name_tr', 'university:id,name_de,slug']);

        // Filtreler
        if ($type = $request->query('type')) {
            if (in_array($type, ['staatlich', 'privat'])) {
                $query->where('type', $type);
            }
        }
        if ($track = $request->query('track')) {
            if (in_array($track, ['T', 'M', 'W', 'G', 'S'])) {
                $query->whereJsonContains('tracks', $track);
            }
        }

        $studienkollegs = $query->orderBy('sort_order')->orderBy('name')->get();

        $stats = [
            'total'     => \App\Models\Studienkolleg::active()->count(),
            'staatlich' => \App\Models\Studienkolleg::active()->where('type', 'staatlich')->count(),
            'privat'    => \App\Models\Studienkolleg::active()->where('type', 'privat')->count(),
            'cities'    => \App\Models\Studienkolleg::active()->whereNotNull('city_id')->distinct('city_id')->count('city_id'),
        ];

        return view('tools.studienkolleg', [
            'studienkollegs' => $studienkollegs,
            'stats' => $stats,
            'activeFilter' => ['type' => $type, 'track' => $track],
            'trackLabels' => \App\Models\Studienkolleg::trackLabels(),
        ]);
    }

    public function eligibilityChecker(Request $request): View
    {
        $countries = self::eligibilityCountries();
        $result = null;

        if ($request->isMethod('post')) {
            $data = $request->validate([
                'country' => 'required|string',
                'degree'  => 'required|in:high_school,bachelor,master',
                'target'  => 'required|in:bachelor,master,phd',
                'gpa'     => 'nullable|numeric|min:0|max:100',
                'language' => 'required|in:german_c1,english_b2,both,none',
            ]);

            $result = $this->assessEligibility($data, $countries);
        }

        return view('tools.eligibility-checker', [
            'countries' => $countries,
            'result' => $result,
            'old' => $request->old() ?: $request->all(),
        ]);
    }

    /**
     * MVP scoring matrix — 15 ülke × diploma → anabin sınıflandırması.
     * H+ = direkt başvuru, H+- = bazı kısıtlamalar, H- = Studienkolleg gerekli.
     */
    private function assessEligibility(array $data, array $countries): array
    {
        $c = $data['country'];
        $degree = $data['degree'];
        $target = $data['target'];
        $cInfo = $countries[$c] ?? null;

        // Anabin sınıflandırması (manuel, en yaygın 15 ülke)
        $anabin = $cInfo['anabin'][$degree] ?? 'unknown';

        $needsStudienkolleg = false;
        $needsAps = $cInfo['needs_aps'] ?? false;
        $needsTestAs = false;
        $issues = [];
        $okPoints = [];
        $verdict = 'ok'; // ok | conditional | needs_prep | not_eligible

        // Degree mismatch kontrolü
        if ($target === 'bachelor' && $degree !== 'high_school') {
            $issues[] = __('You selected target as Bachelor but already have a higher degree — apply directly to your target.');
        } elseif ($target === 'master' && $degree === 'high_school') {
            $issues[] = __('Master\'s requires a Bachelor\'s degree first.');
            $verdict = 'not_eligible';
        } elseif ($target === 'phd' && $degree !== 'master') {
            $issues[] = __('PhD typically requires a Master\'s degree.');
            $verdict = 'not_eligible';
        }

        // Anabin H+/H+-/H- yorumla (sadece Bachelor hedef için Studienkolleg ilgili)
        if ($target === 'bachelor' && $degree === 'high_school') {
            switch ($anabin) {
                case 'H+':
                    $okPoints[] = __('Your high school diploma is directly recognized (Anabin H+).');
                    break;
                case 'H+-':
                    $verdict = 'conditional';
                    $issues[] = __('Diploma partially recognized — only specific subjects (Anabin H+-). Check with the university.');
                    break;
                case 'H-':
                    $verdict = 'needs_prep';
                    $needsStudienkolleg = true;
                    $issues[] = __('Your diploma is not directly recognized (Anabin H-). You need to complete Studienkolleg (1 year foundation program) OR 1-2 successful semesters at a recognized university in your country.');
                    break;
                default:
                    $verdict = 'conditional';
                    $issues[] = __('Anabin classification unclear for this country. Check manually at anabin.kmk.org');
            }
            $needsTestAs = in_array($c, ['china', 'vietnam', 'india', 'pakistan'], true);
        }

        // APS (China, Vietnam, India, Pakistan, Mongolia)
        if ($needsAps) {
            $issues[] = __('You need an APS certificate (Akademische Prüfstelle) before applying — process takes ~3 months.');
        }

        // Dil şartı
        $lang = $data['language'];
        $needsLang = match (true) {
            $lang === 'german_c1' || $lang === 'both' => null,
            $target === 'bachelor' => __('Most Bachelor\'s programs require German C1 (TestDaF 4×4 or DSH-2). Some English-taught Bachelor programs accept B2 English (IELTS 6.0).'),
            $target === 'master' => $lang === 'english_b2' ? null : __('Master\'s programs need either German C1 OR English B2-C1. You can apply to English-taught Master\'s.'),
            $target === 'phd' => null,
            default => null,
        };
        if ($needsLang) $issues[] = $needsLang;

        // GPA bilgilendirici
        $gpa = (float) ($data['gpa'] ?? 0);
        if ($gpa > 0) {
            if ($gpa >= 80 || ($gpa <= 5 && $gpa <= 2.5)) {
                $okPoints[] = __('Your GPA is competitive for selective programs.');
            } elseif ($gpa < 60 || ($gpa <= 5 && $gpa >= 3.5)) {
                $issues[] = __('Your GPA may be below average — focus on universities with open admission or vocational pathways.');
            }
        }

        if ($verdict === 'ok' && count($okPoints) > 0) $verdict = 'ok';
        if ($verdict === 'ok' && count($issues) > 0) $verdict = 'conditional';

        return [
            'verdict' => $verdict,
            'anabin' => $anabin,
            'needs_studienkolleg' => $needsStudienkolleg,
            'needs_aps' => $needsAps,
            'needs_testas' => $needsTestAs,
            'issues' => $issues,
            'ok_points' => $okPoints,
            'country' => $cInfo,
            'data' => $data,
        ];
    }

    /**
     * 15 ülke için anabin H+/H+-/H- manuel matrix (en sık başvuran ülkeler).
     */
    public static function eligibilityCountries(): array
    {
        return [
            'germany'   => ['name' => 'Deutschland', 'flag' => '🇩🇪', 'needs_aps' => false, 'anabin' => ['high_school' => 'H+',  'bachelor' => 'H+', 'master' => 'H+']],
            'turkey'    => ['name' => 'Türkiye',     'flag' => '🇹🇷', 'needs_aps' => false, 'anabin' => ['high_school' => 'H+',  'bachelor' => 'H+', 'master' => 'H+']],
            'india'     => ['name' => 'India',       'flag' => '🇮🇳', 'needs_aps' => true,  'anabin' => ['high_school' => 'H-',  'bachelor' => 'H+-','master' => 'H+']],
            'pakistan'  => ['name' => 'Pakistan',    'flag' => '🇵🇰', 'needs_aps' => true,  'anabin' => ['high_school' => 'H-',  'bachelor' => 'H+-','master' => 'H+']],
            'china'     => ['name' => 'China',       'flag' => '🇨🇳', 'needs_aps' => true,  'anabin' => ['high_school' => 'H+-', 'bachelor' => 'H+', 'master' => 'H+']],
            'nigeria'   => ['name' => 'Nigeria',     'flag' => '🇳🇬', 'needs_aps' => false, 'anabin' => ['high_school' => 'H-',  'bachelor' => 'H+-','master' => 'H+']],
            'usa'       => ['name' => 'USA',         'flag' => '🇺🇸', 'needs_aps' => false, 'anabin' => ['high_school' => 'H+-', 'bachelor' => 'H+', 'master' => 'H+']],
            'uk'        => ['name' => 'UK',          'flag' => '🇬🇧', 'needs_aps' => false, 'anabin' => ['high_school' => 'H+',  'bachelor' => 'H+', 'master' => 'H+']],
            'uae'       => ['name' => 'UAE',         'flag' => '🇦🇪', 'needs_aps' => false, 'anabin' => ['high_school' => 'H-',  'bachelor' => 'H+-','master' => 'H+']],
            'iran'      => ['name' => 'Iran',        'flag' => '🇮🇷', 'needs_aps' => false, 'anabin' => ['high_school' => 'H-',  'bachelor' => 'H+-','master' => 'H+']],
            'brazil'    => ['name' => 'Brazil',      'flag' => '🇧🇷', 'needs_aps' => false, 'anabin' => ['high_school' => 'H+-', 'bachelor' => 'H+', 'master' => 'H+']],
            'russia'    => ['name' => 'Russia',      'flag' => '🇷🇺', 'needs_aps' => false, 'anabin' => ['high_school' => 'H+-', 'bachelor' => 'H+', 'master' => 'H+']],
            'ukraine'   => ['name' => 'Ukraine',     'flag' => '🇺🇦', 'needs_aps' => false, 'anabin' => ['high_school' => 'H+-', 'bachelor' => 'H+', 'master' => 'H+']],
            'egypt'     => ['name' => 'Egypt',       'flag' => '🇪🇬', 'needs_aps' => false, 'anabin' => ['high_school' => 'H-',  'bachelor' => 'H+-','master' => 'H+']],
            'morocco'   => ['name' => 'Morocco',     'flag' => '🇲🇦', 'needs_aps' => false, 'anabin' => ['high_school' => 'H-',  'bachelor' => 'H+-','master' => 'H+']],
            'syria'     => ['name' => 'Syria',       'flag' => '🇸🇾', 'needs_aps' => false, 'anabin' => ['high_school' => 'H-',  'bachelor' => 'H+-','master' => 'H+']],
        ];
    }

    // ===============================================================
    // PATHWAY FINDER — 5 soruda Almanya rotanı bul (Studienkolleg /
    // Bachelor / Master / PhD / Ausbildung / Sprachkurs)
    // ===============================================================

    public function pathwayFinder(Request $request): View
    {
        $result = null;
        $old = [];

        if ($request->isMethod('post')) {
            $data = $request->validate([
                'education_level' => 'required|in:high_school,bachelor_student,bachelor_grad,master_grad,working',
                'german_level'    => 'required|in:none,a1_a2,b1_b2,c1_plus',
                'age_band'        => 'required|in:17_22,23_28,29_35,35_plus',
                'budget_monthly'  => 'required|in:under_800,800_1100,1100_plus',
                'timeline'        => 'required|in:fast_6m,normal_1y,long_2y',
            ]);
            $old = $data;
            $result = $this->runPathwayFinder($data);
        }

        return view('tools.pathway-finder', [
            'result'   => $result,
            'old'      => $old,
            'pathways' => self::pathwaysCatalog(),
        ]);
    }

    private function runPathwayFinder(array $a): array
    {
        // Score each pathway from a baseline of 0
        $scores = [
            'studienkolleg' => 0,
            'bachelor'      => 0,
            'master'        => 0,
            'phd'           => 0,
            'ausbildung'    => 0,
            'sprachkurs'    => 0,
        ];
        $notes = [];

        // Education level → primary route signal
        switch ($a['education_level']) {
            case 'high_school':
                $scores['studienkolleg'] += 6;
                $scores['bachelor']      += 5;
                $scores['ausbildung']    += 4;
                $scores['sprachkurs']    += 2;
                $notes[] = __('Direct bachelor admission depends on your country\'s Anabin status — many countries require Studienkolleg first.');
                break;
            case 'bachelor_student':
                $scores['bachelor']   += 5;
                $scores['sprachkurs'] += 2;
                $notes[] = __('Transfer credits depend on the German university — apply with full transcript and module descriptions.');
                break;
            case 'bachelor_grad':
                $scores['master']     += 7;
                $scores['ausbildung'] += 2;
                $scores['sprachkurs'] += 2;
                break;
            case 'master_grad':
                $scores['phd']        += 7;
                $scores['master']     += 3;
                $scores['sprachkurs'] += 1;
                break;
            case 'working':
                $scores['ausbildung'] += 5;
                $scores['master']     += 4;
                $scores['sprachkurs'] += 3;
                $notes[] = __('Working professionals with 2+ years experience often qualify for "Berufsbegleitend" (work-study) or Fachhochschule master programmes.');
                break;
        }

        // German level
        switch ($a['german_level']) {
            case 'none':
                $scores['sprachkurs']    += 6;
                $scores['studienkolleg'] += 2;
                $scores['ausbildung']    += 1;
                $scores['bachelor']      += 1;
                $notes[] = __('Without German you need 12–18 months of language study first — every other pathway becomes harder.');
                break;
            case 'a1_a2':
                $scores['sprachkurs']    += 4;
                $scores['studienkolleg'] += 3;
                $scores['ausbildung']    += 2;
                break;
            case 'b1_b2':
                $scores['studienkolleg'] += 4;
                $scores['bachelor']      += 4;
                $scores['ausbildung']    += 3;
                $scores['master']        += 3;
                break;
            case 'c1_plus':
                $scores['bachelor']   += 5;
                $scores['master']     += 5;
                $scores['phd']        += 4;
                $scores['ausbildung'] += 3;
                $notes[] = __('C1+ German opens every door — focus on programme fit instead of language barriers.');
                break;
        }

        // Age band
        switch ($a['age_band']) {
            case '17_22':
                $scores['studienkolleg'] += 3;
                $scores['bachelor']      += 4;
                $scores['ausbildung']    += 3;
                break;
            case '23_28':
                $scores['master']     += 4;
                $scores['bachelor']   += 2;
                $scores['ausbildung'] += 3;
                break;
            case '29_35':
                $scores['master']     += 3;
                $scores['phd']        += 3;
                $scores['ausbildung'] += 4;
                $notes[] = __('After 30, Studienkolleg + Bachelor (8+ years) is rarely the best ROI — Master or Ausbildung pays off faster.');
                break;
            case '35_plus':
                $scores['ausbildung'] += 5;
                $scores['master']     += 2;
                $scores['phd']        += 2;
                $notes[] = __('Ausbildung pays a salary from day 1 (~€1,000–1,300/month) — best path when university entry is harder.');
                break;
        }

        // Budget
        switch ($a['budget_monthly']) {
            case 'under_800':
                $scores['ausbildung']    += 4;  // gets paid
                $scores['phd']           += 3;  // often gets stipend
                $scores['studienkolleg'] += 1;
                $notes[] = __('Under €800/month budget is risky. Realistic only with Ausbildung (paid) or PhD (stipend). State universities + WG + cheaper city helps.');
                break;
            case '800_1100':
                $scores['bachelor']      += 3;
                $scores['master']        += 3;
                $scores['studienkolleg'] += 2;
                $scores['ausbildung']    += 2;
                break;
            case '1100_plus':
                $scores['bachelor'] += 4;
                $scores['master']   += 4;
                $scores['phd']      += 3;
                break;
        }

        // Timeline
        switch ($a['timeline']) {
            case 'fast_6m':
                $scores['ausbildung'] += 4;
                $scores['sprachkurs'] += 3;
                $scores['master']     += 2;
                $notes[] = __('Realistic timelines: Sprachkurs/Ausbildung = 6–12 months. Master = 2 years. Bachelor + Studienkolleg = 4–5 years.');
                break;
            case 'normal_1y':
                $scores['master']        += 3;
                $scores['studienkolleg'] += 2;
                $scores['ausbildung']    += 2;
                break;
            case 'long_2y':
                $scores['bachelor']      += 4;
                $scores['studienkolleg'] += 4;
                $scores['phd']           += 3;
                break;
        }

        arsort($scores);
        $top = array_keys($scores);
        $catalog = self::pathwaysCatalog();

        return [
            'top_pathway'    => $catalog[$top[0]],
            'second_pathway' => $catalog[$top[1]] ?? null,
            'third_pathway'  => $catalog[$top[2]] ?? null,
            'scores'         => $scores,
            'notes'          => $notes,
        ];
    }

    // ===============================================================
    // INSPIRE ME — random discovery widget (MyGuide pattern).
    // Server-side randomisation across 6 entity types.
    // ===============================================================

    public function inspireMe(): View
    {
        // Each pick is server-rendered. Refresh = new picks.
        $seed = random_int(0, PHP_INT_MAX);
        mt_srand($seed);

        $uni = University::where('is_active', true)
            ->whereNotNull('content_blocks')
            ->whereNotNull('image_url')
            ->inRandomOrder()
            ->first(['id', 'slug', 'name_de', 'name_en', 'name_tr', 'short_name', 'logo_url', 'image_url', 'student_count', 'type', 'founded_year', 'city_id', 'content_blocks']);

        $city = City::where('is_active', true)
            ->whereNotNull('content_blocks')
            ->whereNotNull('image_url')
            ->has('universities')
            ->whereNotIn('slug', ['harburg-q1635', 'nordrhein-westfalen-q1198', 'bayern-q980', 'nord-q1997469', 'schleswig-holstein-q1194', 'rheinland-pfalz-q1200'])
            ->withCount(['universities' => fn ($q) => $q->where('is_active', 1)])
            ->inRandomOrder()
            ->first(['id', 'slug', 'name_de', 'name_en', 'name_tr', 'state_id', 'image_url', 'population', 'content_blocks']);

        $program = Program::where('is_active', true)
            ->whereIn('language', ['en', 'both'])
            ->with(['university:id,slug,name_de,name_en,name_tr,short_name', 'field:id,slug,name_tr,name_en,name_de,icon'])
            ->inRandomOrder()
            ->first(['id', 'slug', 'name_en', 'name_de', 'university_id', 'field_of_study_id', 'degree', 'duration_semesters', 'language', 'cost_per_semester_eur']);

        $scholarship = Scholarship::whereNull('removed_at')
            ->inRandomOrder()
            ->first(['id', 'slug', 'name_en', 'name_de', 'programmname_en', 'is_daad']);

        $profession = Profession::where('is_active', true)
            ->inRandomOrder()
            ->first(['id', 'slug', 'name_tr', 'name_de', 'cluster_label', 'description_tr']);

        $field = FieldOfStudy::active()
            ->withCount(['programs' => fn ($q) => $q->where('is_active', 1)])
            ->having('programs_count', '>', 0)
            ->inRandomOrder()
            ->first(['id', 'slug', 'name_tr', 'name_en', 'name_de', 'icon', 'color']);

        return view('tools.inspire-me', [
            'uni'         => $uni,
            'city'        => $city,
            'program'     => $program,
            'scholarship' => $scholarship,
            'profession'  => $profession,
            'field'       => $field,
        ]);
    }

    private static function pathwaysCatalog(): array
    {
        return [
            'studienkolleg' => [
                'key'        => 'studienkolleg',
                'icon'       => '🏫',
                'name'       => 'Studienkolleg',
                'subtitle'   => __('Foundation year — bridge to bachelor'),
                'duration'   => __('1 year + Bachelor 3 years = 4 years total'),
                'language'   => __('B2 German required'),
                'cost'       => __('€100–500/semester (public)'),
                'best_for'   => __('High school grads from Anabin H+- or H- countries who need to bridge to a Bachelor.'),
                'next_url'   => '/tools/studienkolleg',
                'next_label' => __('Find a Studienkolleg'),
                'colors'     => ['from-blue-600', 'to-cyan-600', 'border-blue-300', 'text-blue-900'],
            ],
            'bachelor' => [
                'key'        => 'bachelor',
                'icon'       => '🎓',
                'name'       => __('Bachelor'),
                'subtitle'   => __('Direct undergraduate study (3–4 years)'),
                'duration'   => __('3–4 years'),
                'language'   => __('B2/C1 German OR English (international programmes)'),
                'cost'       => __('€100–500/semester (public) · €5,000+/year (private)'),
                'best_for'   => __('High school grads from H+ Anabin countries or those with completed Studienkolleg.'),
                'next_url'   => '/universities',
                'next_label' => __('Explore universities'),
                'colors'     => ['from-indigo-600', 'to-violet-600', 'border-indigo-300', 'text-indigo-900'],
            ],
            'master' => [
                'key'        => 'master',
                'icon'       => '📚',
                'name'       => __('Master'),
                'subtitle'   => __('Specialised postgraduate (1.5–2 years)'),
                'duration'   => __('1.5–2 years'),
                'language'   => __('B2 German OR IELTS 6.5+/TOEFL 90+ for English programmes'),
                'cost'       => __('€100–500/semester (public)'),
                'best_for'   => __('Bachelor graduates wanting deeper specialisation. Many English-taught options.'),
                'next_url'   => '/programs?type=master',
                'next_label' => __('Browse Master programmes'),
                'colors'     => ['from-emerald-600', 'to-teal-600', 'border-emerald-300', 'text-emerald-900'],
            ],
            'phd' => [
                'key'        => 'phd',
                'icon'       => '🔬',
                'name'       => __('PhD / Doctorate'),
                'subtitle'   => __('Research doctorate (3–5 years)'),
                'duration'   => __('3–5 years'),
                'language'   => __('English usually sufficient'),
                'cost'       => __('Often funded (€1,200–2,500/month stipend)'),
                'best_for'   => __('Master graduates with research interest. Apply directly to a Doktorvater (supervisor).'),
                'next_url'   => '/scholarships',
                'next_label' => __('See PhD scholarships'),
                'colors'     => ['from-purple-600', 'to-fuchsia-600', 'border-purple-300', 'text-purple-900'],
            ],
            'ausbildung' => [
                'key'        => 'ausbildung',
                'icon'       => '🔧',
                'name'       => 'Ausbildung',
                'subtitle'   => __('Paid vocational training (2–3.5 years)'),
                'duration'   => __('2–3.5 years'),
                'language'   => __('B1/B2 German typically required'),
                'cost'       => __('PAID: €900–1,300/month from year 1'),
                'best_for'   => __('Career changers, ages 25+, or those who want a guaranteed job + visa pathway without university.'),
                'next_url'   => '/blog',
                'next_label' => __('Read Ausbildung guides'),
                'colors'     => ['from-amber-600', 'to-orange-600', 'border-amber-300', 'text-amber-900'],
            ],
            'sprachkurs' => [
                'key'        => 'sprachkurs',
                'icon'       => '💬',
                'name'       => __('Sprachkurs (Language School)'),
                'subtitle'   => __('Intensive German course (6–18 months)'),
                'duration'   => __('6–18 months until B2/C1'),
                'language'   => __('Starts from A1'),
                'cost'       => __('€300–800/month (Sprachkurs visa: §16f)'),
                'best_for'   => __('Step 1 for everyone without B2 German. Required before Studienkolleg, Bachelor (German-taught), or Ausbildung.'),
                'next_url'   => '/tools/visa-cost',
                'next_label' => __('Visa cost calculator'),
                'colors'     => ['from-rose-600', 'to-pink-600', 'border-rose-300', 'text-rose-900'],
            ],
        ];
    }
}
