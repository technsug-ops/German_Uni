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
                'a' => __(':tuition. International students should also budget around **800–1000 EUR/month** for living costs in Germany.', [
                    'tuition' => $tuitionLabel,
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
}
