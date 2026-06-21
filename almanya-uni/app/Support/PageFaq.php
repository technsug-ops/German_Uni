<?php

namespace App\Support;

use App\Models\City;
use App\Models\FieldOfStudy;
use App\Models\Program;
use App\Models\State;
use App\Models\University;

/**
 * Page-type aware FAQ generator. Returns array of ['q','a'] entries
 * already translated to the current locale via __() helpers.
 *
 * Used by Auto FAQ schema + visible FAQ accordion on programs/uni/tools pages
 * to boost AI Overview (Google AIO + Bing Copilot) eligibility.
 */
class PageFaq
{
    public static function forProgram(Program $program): array
    {
        $uniName = $program->university?->display_name ?? $program->university?->name_de ?? '';
        $programName = $program->name_de;
        $degreeLabels = [
            'bachelor' => 'Bachelor', 'master' => 'Master', 'phd' => 'PhD',
            'staatsexamen' => 'Staatsexamen', 'diplom' => 'Diplom',
        ];
        $degreeLabel = $degreeLabels[$program->degree] ?? ucfirst((string) $program->degree);
        $langLabel = match ($program->language) {
            'en' => __('English'),
            'de' => __('German'),
            'both' => __('German + English'),
            default => __('German'),
        };
        $tuitionLabel = $program->tuition_fee_eur
            ? number_format((float) $program->tuition_fee_eur, 0, ',', '.') . ' EUR / ' . __('semester')
            : __('No tuition fee (only semester contribution)');

        $faqs = [
            [
                'q' => __('Is :program at :uni taught in German or English?', [
                    'program' => $programName, 'uni' => $uniName,
                ]),
                'a' => __('This :degree programme is taught in **:lang**. Make sure to check the language requirements (e.g. TestDaF, DSH, IELTS or TOEFL) before applying.', [
                    'degree' => $degreeLabel,
                    'lang' => $langLabel,
                ]),
            ],
            [
                'q' => __('How much does the :program programme cost?', ['program' => $programName]),
                'a' => __(':fee_info. International students should also budget around **800–1000 EUR/month** for living costs in Germany.', [
                    'fee_info' => $tuitionLabel,
                ]),
            ],
            [
                'q' => __('What are the admission requirements for :program at :uni?', [
                    'program' => $programName, 'uni' => $uniName,
                ]),
                'a' => __('Typical requirements include: a recognised secondary/undergraduate degree, proof of language proficiency (:lang), and (for non-EU applicants) a uni-assist application plus financial proof (Sperrkonto ~11.904 EUR/year).', [
                    'lang' => $langLabel,
                ]),
            ],
            [
                'q' => __('When is the application deadline?', []),
                'a' => __('Application deadlines vary: winter semester usually closes on **15 July**, summer semester on **15 January**. Always confirm the exact deadline on the official university website.'),
            ],
            [
                'q' => __('Can I work in Germany while studying :program?', ['program' => $programName]),
                'a' => __('Yes. International students may work up to **140 full days / 280 half days per year** without additional permission. After graduation you can apply for an 18-month job-seeker permit.'),
            ],
            [
                'q' => __('How do I apply to :uni — directly or via uni-assist?', ['uni' => $uniName]),
                'a' => __('Most German universities accept international applications through **uni-assist** for document verification. Some unis accept direct applications — check the programme page on the official site.'),
            ],
        ];

        return $faqs;
    }

    public static function forUniversity(University $university): array
    {
        $uniName = $university->display_name ?? $university->name_de;
        $cityName = $university->city?->name ?? '';
        $typeLabel = match ($university->type) {
            'applied_sciences' => __('University of Applied Sciences (HAW/FH)'),
            'university' => __('research university'),
            'art_music' => __('Art / Music college'),
            'private' => __('private university'),
            default => __('higher-education institution'),
        };

        $faqs = [
            [
                'q' => __('What kind of institution is :uni?', ['uni' => $uniName]),
                'a' => __(':uni is a public :type located in :city, Germany. It is recognised by the German Hochschulkompass and BMBF.', [
                    'uni' => $uniName, 'type' => $typeLabel, 'city' => $cityName,
                ]),
            ],
            [
                'q' => __('Are there tuition fees at :uni?', ['uni' => $uniName]),
                'a' => __('Most public German universities — including :uni — charge **no tuition fees** for international Bachelor/Master students. You only pay a **semester contribution** (~150–350 EUR) which usually includes a public-transport ticket.', [
                    'uni' => $uniName,
                ]),
            ],
            [
                'q' => __('How can I apply to :uni as an international student?', ['uni' => $uniName]),
                'a' => __('Non-EU applicants typically apply via **uni-assist** (document verification + APS certificate for some countries). EU applicants apply directly. Documents needed: secondary school diploma, transcripts, language certificate, CV, motivation letter.', []),
            ],
            [
                'q' => __('What language requirements does :uni have?', ['uni' => $uniName]),
                'a' => __('German-taught programmes require **TestDaF 4×4, DSH-2, or telc C1 Hochschule**. English-taught programmes usually accept **IELTS 6.5 / TOEFL 90**. Some Master programmes accept both languages.'),
            ],
            [
                'q' => __('What is student life like in :city?', ['city' => $cityName]),
                'a' => __('Living in :city typically costs **800–1200 EUR/month** (rent, food, transport, insurance). Most cities have an active **Studentenwerk** that offers affordable dormitories, canteens (Mensa), and counselling.', [
                    'city' => $cityName,
                ]),
            ],
        ];

        return $faqs;
    }

    /** Generic FAQ for the Sperrkonto / blocked-account tool page. */
    public static function forBlockedAccount(): array
    {
        return [
            [
                'q' => __('What is a Sperrkonto (blocked account)?'),
                'a' => __('A **Sperrkonto** is a blocked German bank account that proves you can finance your stay in Germany. As of 2026, the required amount is **11.904 EUR** for one year, released monthly (~992 EUR/month).'),
            ],
            [
                'q' => __('Who needs a blocked account?'),
                'a' => __('Almost every **non-EU student** applying for a German student visa must show proof of financial means — usually a Sperrkonto. EU/EEA citizens are exempt. Scholarship holders (DAAD, Erasmus) can sometimes use the scholarship letter instead.'),
            ],
            [
                'q' => __('Which Sperrkonto provider is cheapest?'),
                'a' => __('Setup fees range from **0 EUR to ~150 EUR**, plus optional monthly maintenance (0–9 EUR). FinTech providers like **Expatrio**, **Coracle**, and **Fintiba** typically offer the most competitive total first-year cost. Compare them in the table above.'),
            ],
            [
                'q' => __('How long does it take to open a Sperrkonto?'),
                'a' => __('FinTech providers (Expatrio, Fintiba, Coracle) usually issue confirmation in **2–7 business days**. Traditional banks (Deutsche Bank, Sparkasse) may take **2–6 weeks** including notarised documents.'),
            ],
            [
                'q' => __('Can I open the Sperrkonto from my home country?'),
                'a' => __('Yes — all FinTech providers and most major banks support **fully online** application + video identification (VideoIdent or POSTIdent) from abroad. You do not need to be in Germany to set it up.'),
            ],
            [
                'q' => __('Can I include health insurance with my Sperrkonto?'),
                'a' => __('Yes — providers like **Expatrio Value Package**, **Fintiba Plus**, and **Coracle Pro** bundle blocked account + **TK / Mawista / DR-WALTER health insurance** in one application. This saves time during visa preparation.'),
            ],
        ];
    }

    public static function forCity(City $city): array
    {
        $cityName = $city->name;
        $stateName = $city->state?->name ?? '';
        return [
            [
                'q' => __('How many universities are there in :city?', ['city' => $cityName]),
                'a' => __(':city hosts multiple recognised German universities — see the list above. Each campus has its own admission rules, language requirements, and student services.', ['city' => $cityName]),
            ],
            [
                'q' => __('How much does it cost to live in :city as a student?', ['city' => $cityName]),
                'a' => __('Average student living cost in :city is **800–1.200 EUR/month** (rent + food + transport + insurance). Studentenwerk dorms (250–400 EUR) are the most affordable but waitlists run 1–2 semesters.', ['city' => $cityName]),
            ],
            [
                'q' => __('Is :city a good destination for international students?', ['city' => $cityName]),
                'a' => __('Yes — :city is part of :state and is well-connected by public transport. International student communities are active, and the local Studentenwerk supports housing, counselling, and Mensa cafeterias.', ['city' => $cityName, 'state' => $stateName]),
            ],
            [
                'q' => __('How do I get a Semesterticket in :city?', ['city' => $cityName]),
                'a' => __('Every enrolled student in :city automatically receives a **Semesterticket** included in the semester contribution (~150–350 EUR/semester). It covers regional public transport — no separate purchase needed.', ['city' => $cityName]),
            ],
            [
                'q' => __('Where can I find part-time student jobs in :city?', ['city' => $cityName]),
                'a' => __('Check the university\'s **Studentenwerk job board**, **Jobmensa**, **Studentjob.de**, and city-specific Facebook groups. Most international students may work **140 full days / 280 half days per year** without extra permission.'),
            ],
        ];
    }

    public static function forField(FieldOfStudy $field): array
    {
        $fieldName = $field->name;
        return [
            [
                'q' => __('How many :field programmes are available in Germany?', ['field' => $fieldName]),
                'a' => __('Germany offers hundreds of :field programmes across public universities (UnivBers) and Universities of Applied Sciences (HAW/FH). Bachelor, Master and PhD level all available — see the list above for current openings.', ['field' => $fieldName]),
            ],
            [
                'q' => __('Are :field programmes taught in English?', ['field' => $fieldName]),
                'a' => __('Yes — many :field Master programmes are taught entirely in **English** (especially in technical and business fields). Bachelor programmes are mostly in German. Filter the list above by language to find English-taught options.', ['field' => $fieldName]),
            ],
            [
                'q' => __('What admission requirements apply to :field programmes?', ['field' => $fieldName]),
                'a' => __('Typical requirements: recognised high-school / Bachelor degree, language certificate (DSH/TestDaF for German, IELTS/TOEFL for English), motivation letter, sometimes APS certificate or specific subject prerequisites.'),
            ],
            [
                'q' => __('What career prospects does :field have in Germany?', ['field' => $fieldName]),
                'a' => __('After graduation, international students can apply for an **18-month job-seeker visa** to find work in Germany. :field graduates are in demand in Germany\'s skilled-workforce shortage list (Fachkräftemangel) for many specialisations.', ['field' => $fieldName]),
            ],
            [
                'q' => __('Are there scholarships specifically for :field?', ['field' => $fieldName]),
                'a' => __('DAAD, Konrad-Adenauer-Stiftung, Friedrich-Ebert-Stiftung, and Deutschlandstipendium offer scholarships open to :field applicants. Check our scholarships page for current calls and deadlines.', ['field' => $fieldName]),
            ],
        ];
    }

    public static function forState(State $state): array
    {
        $stateName = $state->name;
        return [
            [
                'q' => __('What is :state known for academically?', ['state' => $stateName]),
                'a' => __(':state hosts a mix of public universities and Universities of Applied Sciences. See the city + university list above for the full landscape, including international-friendly campuses.', ['state' => $stateName]),
            ],
            [
                'q' => __('Are there tuition fees in :state?', ['state' => $stateName]),
                'a' => __('Most public universities in :state — like across Germany — charge **no tuition** for Bachelor/Master. Baden-Württemberg is the exception (1.500 EUR/semester for non-EU students). Always confirm on the official university page.', ['state' => $stateName]),
            ],
            [
                'q' => __('How expensive is student life in :state?', ['state' => $stateName]),
                'a' => __('Living costs vary by city in :state: large cities (Munich, Stuttgart, Frankfurt) → 1.000–1.400 EUR/month; mid-size cities → 800–1.100 EUR; smaller university towns → 650–900 EUR.', ['state' => $stateName]),
            ],
            [
                'q' => __('Which cities in :state are best for international students?', ['state' => $stateName]),
                'a' => __('Cities with established international student communities + strong Studentenwerk support are usually the best entry points. Check the city list above with student counts and university density.', ['state' => $stateName]),
            ],
        ];
    }

    public static function forScholarships(): array
    {
        return [
            [
                'q' => __('What are the main scholarships for studying in Germany?'),
                'a' => __('Top sources: **DAAD** (German Academic Exchange Service), **Deutschlandstipendium** (300 EUR/month, merit-based), **Heinrich-Böll-Stiftung**, **Konrad-Adenauer-Stiftung**, **Friedrich-Ebert-Stiftung**, **Erasmus+** (for EU), and individual university scholarships.'),
            ],
            [
                'q' => __('Are DAAD scholarships available for Master programmes?'),
                'a' => __('Yes — DAAD offers Master scholarships (~ 934 EUR/month + tuition + travel + insurance) for development-related fields, EPOS programmes, and specific country quotas. Application deadlines are usually 6–12 months before programme start.'),
            ],
            [
                'q' => __('Can I get a scholarship without high grades?'),
                'a' => __('Yes — many German scholarships use **social engagement + motivation + need-based criteria** rather than only grades. Heinrich-Böll, Hans-Böckler, and political-party foundations consider activism, volunteering, and personal background heavily.'),
            ],
            [
                'q' => __('Do German scholarships cover living costs entirely?'),
                'a' => __('DAAD covers ~934 EUR/month + tuition + travel + insurance — fully sufficient for living costs in most German cities. Deutschlandstipendium adds 300 EUR/month on top of other income. Smaller scholarships (~150–500 EUR) usually need to be combined.'),
            ],
            [
                'q' => __('When should I apply for German scholarships?'),
                'a' => __('Plan **9–15 months ahead**. Most DAAD calls open in **August–October** for the following winter semester. Stiftungen accept applications year-round but review only 2–4 times per year.'),
            ],
        ];
    }

    /** Generic FAQ for the Studienkolleg tool page. */
    public static function forStudienkolleg(): array
    {
        return [
            [
                'q' => __('What is a Studienkolleg?'),
                'a' => __('A **Studienkolleg** is a one-year preparatory programme for international students whose high-school diploma is not directly recognised for direct admission to German universities (Anabin H-). It bridges the academic gap and ends with the **Feststellungsprüfung** exam.'),
            ],
            [
                'q' => __('Do I need a Studienkolleg?'),
                'a' => __('You need one if your secondary-school qualification is rated **H-** in the Anabin database (most non-EU diplomas, including Turkish lise, Indian 10+2, Pakistani FSc). EU diplomas usually don\'t need it. Use our [Eligibility Checker](/tools/eligibility-checker) to confirm.'),
            ],
            [
                'q' => __('Which tracks (Kurse) exist?'),
                'a' => __('**T-Kurs** (Engineering, Natural Sciences), **M-Kurs** (Medicine, Biology, Pharmacy), **W-Kurs** (Economics, Social Sciences), **G-Kurs** (Humanities, German studies), **S-Kurs** (Languages, Philology). The track must match your intended Bachelor field.'),
            ],
            [
                'q' => __('How much does Studienkolleg cost?'),
                'a' => __('**Public (staatlich) Studienkollegs are tuition-free** — you only pay the regular semester contribution (~150–350 EUR). **Private Studienkollegs** charge 8,000–15,000 EUR for the year, but offer faster admission + smaller groups.'),
            ],
            [
                'q' => __('How do I get into Studienkolleg?'),
                'a' => __('You usually need: 1) Conditional university admission letter (Zulassungsbescheid mit Auflage), 2) German language B1/B2 certificate, 3) Passed entrance exam (Aufnahmetest) testing German + maths. Apply via uni-assist or directly with the Studienkolleg.'),
            ],
            [
                'q' => __('Can I skip Studienkolleg?'),
                'a' => __('Yes, in two cases: (a) you complete **1–2 successful semesters** at a recognised university in your home country with related subject, or (b) you pass the **TestAS** (Test für Ausländische Studierende) with high score — some unis accept it as alternative.'),
            ],
        ];
    }

    /** Generic FAQ for the housing/providers tool page. */
    public static function forHousing(): array
    {
        return [
            [
                'q' => __('How do I find student housing in Germany?'),
                'a' => __('Main options: **Studentenwerk dormitories** (cheapest, long waitlists 1–2 semesters), **WG-Gesucht / Studenten-WG** (shared flats), **HousingAnywhere / Spotahome** (short-term), private rental sites (ImmoScout24, Wohnungsboerse).'),
            ],
            [
                'q' => __('How much does student housing cost?'),
                'a' => __('Average monthly rent: **Studentenwerk dorm 250–400 EUR**, **WG shared room 350–600 EUR**, **studio apartment 500–900 EUR** depending on city. Munich, Frankfurt, Hamburg are most expensive; Leipzig, Dresden, Magdeburg most affordable.'),
            ],
            [
                'q' => __('When should I start looking for accommodation?'),
                'a' => __('Apply to **Studentenwerk dorms 6–12 months before** semester start. For private WG rooms, start searching **4–8 weeks before** moving — landlords usually don\'t accept applicants too far in advance.'),
            ],
            [
                'q' => __('Do I need to register my address (Anmeldung)?'),
                'a' => __('Yes — every resident in Germany must register at the local **Bürgeramt** within **2 weeks of moving in**. You\'ll need the landlord\'s **Wohnungsgeberbestätigung** form. Without Anmeldung you cannot open a regular bank account or get a tax ID.'),
            ],
            [
                'q' => __('What is a Kaution and how much should I expect?'),
                'a' => __('**Kaution** is the security deposit, legally capped at **3 months\' cold rent (Kaltmiete)**. It\'s held by the landlord and returned at the end of the contract (minus any deductions for damage).'),
            ],
        ];
    }

    /** Cost of Living calculator FAQ — DAAD official data, monthly minimum, city differences. */
    public static function forCostOfLiving(): array
    {
        return [
            ['q' => __('How much does it cost to live in Germany as a student?'),
             'a' => __('Officially, **DAAD estimates around €992/month** for students in Germany (rent + food + transport + insurance + leisure). Sperrkonto requires exactly this amount × 12 = **€11,904/year** as financial proof for the visa. Real cost varies €850–€1,500 by city.')],
            ['q' => __('Which German cities are cheapest for students?'),
             'a' => __('Most affordable: **Leipzig, Dresden, Magdeburg, Halle, Chemnitz** (€700–900/month). Most expensive: **Munich, Frankfurt, Hamburg, Stuttgart, Düsseldorf** (€1,100–1,400/month). University tuition fees are zero at most public unis regardless of city.')],
            ['q' => __('Is the €992/month Sperrkonto amount fixed?'),
             'a' => __('Yes — for the **2025/26 academic year**, the German embassy requires the standard DAAD rate. It updates roughly annually. Check the embassy of your country of residence for current figures before applying.')],
            ['q' => __('Are there hidden semester fees on top of living costs?'),
             'a' => __('Yes: **Semesterbeitrag (semester contribution)** of **€100–430** depending on university. This typically includes the **Semesterticket** (free public transport across the federal state) and Studentenwerk services. Tuition itself is free at most public unis.')],
            ['q' => __('Can I work as a student to reduce costs?'),
             'a' => __('International students can work **140 full days / 280 half days per year** without an additional permit. As a **Werkstudent** with a related field job, you can earn up to **€603/month tax-free** and gain experience aligned with your studies.')],
        ];
    }

    /** Visa Cost calculator FAQ — total costs, breakdown, refunds. */
    public static function forVisaCost(): array
    {
        return [
            ['q' => __('How much does a German student visa cost in total?'),
             'a' => __('The **visa fee itself is €75**. But total costs including required documents reach **€12,200–€13,000**: Sperrkonto setup (€89–€150) + Sperrkonto block amount (€11,904) + insurance (€110–€140) + uni-assist fee (€75) + translations (€80–€200) + APS (~€250 in some countries) + flight (€300–€600).')],
            ['q' => __('Is the visa fee refundable if rejected?'),
             'a' => __('No — the **€75 visa fee is non-refundable** even if your application is rejected. You can appeal within one month if rejection seems unjustified. Sperrkonto block amount IS refundable if visa fails (refund process 2–4 weeks).')],
            ['q' => __('Can I avoid the Sperrkonto requirement?'),
             'a' => __('Three alternatives: (1) **Scholarship letter** showing equivalent monthly support, (2) **Parent guarantee (Verpflichtungserklärung)** from someone in Germany, (3) Some unis accept a **bank guarantee** from a German bank. Sperrkonto is the most common path for self-funded students.')],
            ['q' => __('Do I need health insurance before applying for the visa?'),
             'a' => __('Yes — **travel insurance covering minimum €30,000 for the first 90 days** is required at visa interview. After arrival, you switch to **mandatory student health insurance (€110–€140/month)** like TK, AOK, or Barmer for the rest of your stay.')],
            ['q' => __('How long does the German student visa process take?'),
             'a' => __('Appointment booking: **4–12 weeks wait** at consulates. After interview: **6–12 weeks processing**. Plan to start **at least 4 months before** your intended program start. Some consulates (Istanbul, Tehran, Ankara) have longer queues — book as early as possible.')],
        ];
    }

    /** Budget Planner tool FAQ — Werkstudent rules, savings goal, Bafög. */
    public static function forBudgetPlanner(): array
    {
        return [
            ['q' => __('How much can I earn as a student in Germany?'),
             'a' => __('International students: **140 full days or 280 half days per year**. As a **Werkstudent** (working student in your field) you can work **up to 20 hours/week during semester, full-time during semester break**. Maximum tax-free monthly income via mini-job: **€603**.')],
            ['q' => __('What is Bafög and am I eligible?'),
             'a' => __('**Bafög** is German federal student aid (loan + grant, 50/50). International students CAN qualify if they have **permanent residency in Germany** or specific status (refugee, EU citizen residing in Germany 5+ years). New international students from outside EU are generally **not eligible**.')],
            ['q' => __('How much should I save before coming to Germany?'),
             'a' => __('Recommended: **first 2–3 months expenses on top of Sperrkonto** = **€2,500–€4,500** extra. Reason: Sperrkonto releases €992/month, but you need cash for first-week costs (deposit, Anmeldung, initial groceries, etc.) before the first release arrives.')],
            ['q' => __('Do I need to pay German tax on my student income?'),
             'a' => __('If you earn **under €12,096/year** (2025 limit), you owe **zero income tax**. Above that: progressive rates from 14%. You always file annual tax declaration as Werkstudent — you usually get back most withheld tax in the refund.')],
            ['q' => __('Are scholarships counted as income in budget planning?'),
             'a' => __('For visa: yes — scholarships count toward the €992/month financial proof. For income tax: most scholarships (DAAD, Erasmus+, Deutschlandstipendium) are **tax-free**. Always include them in monthly income estimates when planning.')],
        ];
    }

    /** University Match quiz FAQ. */
    public static function forRecommendation(): array
    {
        return [
            ['q' => __('How does the university match quiz work?'),
             'a' => __('The quiz collects **5 simple inputs**: budget, field of interest, language preference (German/English/both), city size, and academic level. Behind the scenes it scores all 600+ active German universities and returns the top matches with a percentage score + reason explanation.')],
            ['q' => __('How accurate are the quiz recommendations?'),
             'a' => __('The quiz is a **starting point, not final advice** — designed to surface 5–10 unis you might not have considered. Always verify by visiting each university\'s official page for specific admission requirements, exact tuition, and current deadlines before committing.')],
            ['q' => __('Can I retake the quiz?'),
             'a' => __('Yes — there are no limits. Each retake recalculates from scratch. If your circumstances change (different budget, language ability, etc.), just rerun the quiz. No login required.')],
            ['q' => __('Does the quiz consider scholarship eligibility?'),
             'a' => __('Not directly. The quiz focuses on academic + language fit. After getting your matches, use our **/scholarships** page to filter scholarships by your nationality, field, and program level separately.')],
            ['q' => __('Why didn\'t my dream university appear in results?'),
             'a' => __('Likely reason: **language mismatch or budget filter**. Many top unis (TUM, LMU, Heidelberg) primarily teach in German at Bachelor level — if you selected English-only, they may rank lower. Try adjusting filters to "Both" or German.')],
        ];
    }

    /** Career Compass (RIASEC) FAQ. */
    public static function forCareerCompass(): array
    {
        return [
            ['q' => __('What is RIASEC and how does this tool use it?'),
             'a' => __('**RIASEC** (Holland Codes) is a validated career-interest model with 6 dimensions: Realistic, Investigative, Artistic, Social, Enterprising, Conventional. Our quiz scores your top 3 codes and matches them to real professions in Germany from our 3,500+ profession database.')],
            ['q' => __('Where does the profession data come from?'),
             'a' => __('Primary source: **BERUFENET** (Bundesagentur für Arbeit) — Germany\'s official labour market database. Salary ranges, training paths, and demand indicators are sourced from BERUFENET + DESTATIS national statistics.')],
            ['q' => __('How is this different from a regular career quiz?'),
             'a' => __('Most career quizzes give vague answers like "you should be a manager". We tie your profile to **specific German labour market data** — exact profession titles, training paths (Studium vs Ausbildung), salary expectations in Germany, and matching university programs.')],
            ['q' => __('Is the quiz scientifically validated?'),
             'a' => __('The **RIASEC framework itself** is validated (Holland, 1959; updated continually). Our specific 12-question implementation is calibrated against Holland\'s reference set but is a **starter quiz**, not a clinical assessment. For high-stakes career decisions, consult a vocational psychologist.')],
            ['q' => __('Can I save my career compass results?'),
             'a' => __('With a free account, your top 3 RIASEC profile and matched professions are saved to your dashboard. Anonymous users can copy the result URL — it embeds your scores and reconstructs the page when visited.')],
        ];
    }

    /** Application Calendar FAQ — deadlines, intakes. */
    public static function forDeadlines(): array
    {
        return [
            ['q' => __('When are German university application deadlines?'),
             'a' => __('Two main intakes: **Winter semester** (Oct start) deadline typically **15 July**; **Summer semester** (Apr start) deadline **15 January**. Many programs accept applications earlier — some private unis are rolling. Always verify on the official program page.')],
            ['q' => __('Are deadlines different for international students?'),
             'a' => __('Yes — international applicants typically have **earlier deadlines** (often **30 May for winter / 30 November for summer**) to allow visa processing time. uni-assist applications generally close **6 weeks before** the official faculty deadline.')],
            ['q' => __('Can I add these deadlines to my calendar?'),
             'a' => __('Yes — every deadline on our /tools/deadlines page has an **iCal (.ics) export link**. Click it and your reminder is added directly to Apple Calendar, Google Calendar, or Outlook with a 2-week-before reminder pre-set.')],
            ['q' => __('What if I miss a deadline?'),
             'a' => __('Some programs offer a **"late application" (Nachrückverfahren)** for unfilled spots — usually open 2–6 weeks after the main deadline. Open programs (NC-frei) often have **rolling admissions**. Check the university\'s own portal for exact policy.')],
            ['q' => __('Should I apply for both intakes to be safe?'),
             'a' => __('If your program offers both, yes — applying for both **doubles your chances** and the winter intake usually has more program options. Some students start with summer (less competition) and switch to winter rhythm after the first year.')],
        ];
    }

    /** Eligibility Checker tool FAQ. */
    public static function forEligibility(): array
    {
        return [
            ['q' => __('How does the eligibility checker work?'),
             'a' => __('You enter your country and high-school diploma type. The tool maps it against the **Anabin database** (Germany\'s official foreign-credentials catalogue) and tells you: (a) **direct university entry**, (b) **Studienkolleg required first**, or (c) **2 years of home-country university** before German entry.')],
            ['q' => __('What is Anabin?'),
             'a' => __('**Anabin** is the official Standing Conference of Education Ministers (KMK) database evaluating foreign academic qualifications. German universities **must** check Anabin when admitting international students — it\'s the legal reference for diploma equivalence.')],
            ['q' => __('My diploma isn\'t in Anabin — what do I do?'),
             'a' => __('Three options: (1) Apply via **uni-assist** which has its own equivalence experts, (2) Contact the **target university directly** (some accept individual assessments), (3) Take the **TestAS** (Test für Ausländische Studierende) which is universally accepted.')],
            ['q' => __('How accurate is this checker?'),
             'a' => __('We use the **official Anabin classification** so the eligibility outcome is the same the university would see. But the **final admission decision** rests with each university — some have stricter requirements (high GPA cutoff, specific subjects, language). Use this checker as starting point.')],
            ['q' => __('What is Studienkolleg and how long does it take?'),
             'a' => __('**Studienkolleg** is a 1-year (2-semester) foundation program at a German university that prepares students whose diploma is not directly equivalent. Five tracks exist: **T-Kurs** (engineering/science), **M-Kurs** (medical), **W-Kurs** (economics), **G-Kurs** (humanities), **S-Kurs** (language/social).')],
        ];
    }

    /**
     * Per-profession FAQ — type-aware, locale-aware, BERUFENET-grounded.
     *
     * Pulls real translated info_fields data when available (info_fields_tr / _en / _fields).
     * Falls back to type-based template language when a specific field is empty.
     */
    public static function forProfession(\App\Models\Profession $p): array
    {
        // Locale-aware: $p->name follows the LocalizableContent fallback chain
        // (tr → en → de for tr locale, en → de → tr for en, de → en → tr for de).
        $name   = $p->name ?: $p->name_de;
        $nameDe = $p->name_de;
        $type = $p->type ?: 'other';

        $locale = app()->getLocale();
        $bucket = match ($locale) {
            'tr'    => $p->info_fields_tr ?? [],
            'en'    => $p->info_fields_en ?? [],
            'de'    => self::mapGermanBuckets($p->info_fields ?? []),
            default => $p->info_fields_en ?? [],
        };

        $pathLine = match ($type) {
            'ausbildung'    => __('a **3-year dual Ausbildung** — combining a vocational school (Berufsschule) with paid on-the-job training at a company. No university degree required.'),
            'studienberuf'  => __('a **Studienberuf** — meaning you need a **university Bachelor\'s or Master\'s degree** from a German or recognised foreign university.'),
            'weiterbildung' => __('a **Weiterbildung** path — advanced specialisation that builds on an existing Ausbildung or degree (e.g. Meister, Fachwirt, certified course).'),
            'grundberuf'    => __('a **Grundberuf** — an entry-level occupation that requires no formal vocational training or specific degree.'),
            default         => __('a standard regulated occupation. Specific qualifications depend on the role.'),
        };

        // Q1 — Tasks: prefer description, then BERUFENET tasks field, then steckbrief
        $tasksAnswer = $p->description
            ?: ($bucket['tasks'] ?? null)
            ?: ($p->clean_steckbrief ? \Illuminate\Support\Str::limit($p->clean_steckbrief, 320) : null)
            ?: __('A :name is :path Day-to-day tasks vary by employer; check BERUFENET for the precise role profile.', ['name' => $nameDe, 'path' => $pathLine]);

        // Q3 — Access route: prefer BERUFENET access field, else type-based template
        $accessAnswer = ($bucket['access'] ?? null)
            ?: __('In Germany, ":name" follows :path Foreign applicants should additionally verify diploma recognition via [anabin.kmk.org](https://anabin.kmk.org/) before applying.', ['name' => $nameDe, 'path' => $pathLine]);

        // Q4 — Workplace: from BERUFENET
        $workplaceAnswer = ($bucket['workplace'] ?? null)
            ?: ($bucket['sectors'] ?? null)
            ?: __('Workplace varies by employer. Check the official BERUFENET listing for the current breakdown of typical work environments for :name.', ['name' => $nameDe]);

        // Q5 — Salary: from BERUFENET (often "Salary data varies, check BERUFENET")
        $salaryAnswer = ($bucket['salary'] ?? null)
            ?: __('Salaries vary by region, employer size, and experience. Consult **BERUFENET** for current figures, or salary aggregators like **gehalt.de** and **stepstone.de Gehaltsreport**.');

        return [
            [
                'q' => __('What does a :name do in Germany?', ['name' => $name]),
                'a' => $tasksAnswer,
            ],
            [
                'q' => __('Is :name an Ausbildung or a degree path?', ['name' => $name]),
                'a' => __('In Germany, ":name" follows :path', ['name' => $nameDe, 'path' => $pathLine]),
            ],
            [
                'q' => __('How can I qualify as :name in Germany?', ['name' => $name]),
                'a' => $accessAnswer,
            ],
            [
                'q' => __('Where do :name typically work in Germany?', ['name' => $name]),
                'a' => $workplaceAnswer,
            ],
            [
                'q' => __('What is the typical salary for :name in Germany?', ['name' => $name]),
                'a' => $salaryAnswer,
            ],
        ];
    }

    /**
     * Map raw German info_fields (full BERUFENET keys) to our short-key bucket used by
     * the locale-aware fallback chain. Used when locale=de — TR/EN have their own buckets.
     */
    private static function mapGermanBuckets(array $info): array
    {
        $map = [
            'tasks'       => ['Aufgaben und Tätigkeiten kompakt', 'Studieninhalte', 'Weiterbildungsinhalte', 'Mögliche Tätigkeitsfelder'],
            'access'      => ['Zugang zur Tätigkeit', 'Zugangsvoraussetzungen für das Studium', 'Zugangsvoraussetzungen für die Weiterbildung'],
            'salary'      => ['Verdienst/Einkommen', 'Vergütung während des Studiums', 'Weiterbildungsvergütung'],
            'workplace'   => ['Arbeitsorte', 'Lernorte'],
            'sectors'     => ['Arbeitsbereiche/Branchen', 'Mögliche Tätigkeitsfelder', 'Abschluss-/Berufsbezeichnungen'],
            'progression' => ['Weiterbildung (beruflicher Aufstieg)', 'Mögliche weiterführende Studienfächer', 'Perspektiven nach der Weiterbildung'],
        ];
        $out = [];
        foreach ($map as $short => $candidates) {
            foreach ($candidates as $key) {
                if (! empty($info[$key])) {
                    $out[$short] = $info[$key];
                    break;
                }
            }
        }
        return $out;
    }

    /** Grade Converter (modified Bavarian formula) FAQ. */
    public static function forGradeConverter(): array
    {
        return [
            ['q' => __('What is the modifizierte bayerische Formel?'),
             'a' => __('The **modified Bavarian formula** is the official equation used by DAAD and uni-assist to convert foreign grades to the German 1.0–4.0 scale. Formula: **x = 1 + 3 × (Nmax − Nd) / (Nmax − Nmin)** where Nmax is your country\'s top grade, Nmin is the passing grade, and Nd is your earned grade.')],
            ['q' => __('Will my converted grade be accepted by every university?'),
             'a' => __('Most German universities accept the modified Bavarian formula because it\'s the **uni-assist standard**. Some universities have their own conversion table that may differ slightly — check the target university\'s "Anerkennung" or "Anrechnung von Studienleistungen" page.')],
            ['q' => __('I have multiple grading systems on my transcript — which do I use?'),
             'a' => __('Most often **the final cumulative GPA** (e.g. 3.45/4.0 or 75/100). If your transcript has both percentage AND letter grades, percentage is preferred for the formula because it gives finer granularity.')],
            ['q' => __('How does the German 1.0–4.0 scale work?'),
             'a' => __('**1.0 = sehr gut (excellent)**, **2.0 = gut (good)**, **3.0 = befriedigend (satisfactory)**, **4.0 = ausreichend (sufficient = pass)**, **5.0 = nicht ausreichend (fail)**. Lower is better. A 1.0 average is very rare — typical good students average 1.5–2.5.')],
            ['q' => __('My GPA is on a 4.0 scale (USA). How does that map?'),
             'a' => __('Approximate mapping: **3.7–4.0 (A) ≈ 1.0–1.5 German**, **3.0–3.6 (B) ≈ 1.6–2.5 German**, **2.0–2.9 (C) ≈ 2.6–3.5 German**, **1.0–1.9 (D) ≈ 3.6–4.0 German**. Use the calculator for exact value based on your specific institution.')],
        ];
    }
}
