<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $cfg['direction'] ?? 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>
        @switch($locale)
            @case('en') Coming Soon — AlmanyaUni @break
            @case('de') Bald verfügbar — AlmanyaUni @break
            @default Yakında — AlmanyaUni
        @endswitch
    </title>

    @php
        $strings = match ($locale) {
            'en' => [
                'badge' => 'Coming Soon',
                'title' => 'English version is on its way',
                'subtitle' => 'AlmanyaUni currently runs in Turkish. The English version is under active development.',
                'cta_back' => 'Continue in Turkish',
                'cta_notify' => 'Notify me at launch',
                'features_title' => 'What you\'ll find here',
                'features' => [
                    ['📚', 'Comprehensive guides for international students in Germany'],
                    ['🎓', '645 official Hochschulen + 15,782 programs — searchable database'],
                    ['💬', 'Active community forum (Turkish + English categories)'],
                    ['🛠️', 'Cost-of-living calculator, grade converter, application timeline'],
                ],
                'footer' => 'We\'re translating university descriptions, blog posts, and FAQs. Expected launch: Q3 2026.',
                'switch_back' => '🇹🇷 Switch back to Turkish',
            ],
            'de' => [
                'badge' => 'Bald verfügbar',
                'title' => 'Die deutsche Version ist in Arbeit',
                'subtitle' => 'AlmanyaUni läuft derzeit auf Türkisch. Die deutsche Version wird gerade entwickelt.',
                'cta_back' => 'Auf Türkisch fortfahren',
                'cta_notify' => 'Bei Veröffentlichung benachrichtigen',
                'features_title' => 'Was du hier finden wirst',
                'features' => [
                    ['📚', 'Umfassende Ratgeber für internationale Studierende in Deutschland'],
                    ['🎓', '645 offizielle Hochschulen + 15.782 Studiengänge — durchsuchbare Datenbank'],
                    ['💬', 'Aktives Community-Forum (Türkische + Englische Kategorien)'],
                    ['🛠️', 'Lebenshaltungskosten-Rechner, Notenumrechner, Bewerbungstimeline'],
                ],
                'footer' => 'Wir übersetzen Hochschulbeschreibungen, Blogbeiträge und FAQs. Geplanter Start: Q3 2026.',
                'switch_back' => '🇹🇷 Zurück zu Türkisch',
            ],
            default => [
                'badge' => 'Yakında',
                'title' => 'Bu dil yakında yayında',
                'subtitle' => 'AlmanyaUni şu an Türkçe yayında. Bu dilin geliştirmesi devam ediyor.',
                'cta_back' => 'Türkçe devam et',
                'cta_notify' => 'Yayınlandığında haber ver',
                'features_title' => 'Burada bulacağın',
                'features' => [],
                'footer' => 'Üni açıklamaları, blog yazıları ve SSS çevirileri sürüyor. Beklenen yayın: 2026 Q3.',
                'switch_back' => '🇹🇷 Türkçe\'ye geç',
            ],
        };
    @endphp

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: { 50:'#EFF6FF',100:'#DBEAFE',500:'#3B82F6',600:'#2563EB',700:'#1D4ED8',800:'#1E40AF',900:'#1E3A8A' },
                        accent:  { 50:'#FFF7ED',400:'#FB923C',500:'#F97316',600:'#EA580C' },
                    },
                    fontFamily: { sans: ['Inter','system-ui','sans-serif'] },
                },
            },
        };
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body class="min-h-screen bg-gradient-to-br from-primary-900 via-primary-800 to-primary-900 font-sans text-white antialiased flex flex-col">

    <header class="border-b border-white/10">
        <div class="max-w-5xl mx-auto px-6 py-5 flex items-center justify-between">
            <a href="/" class="flex items-center gap-2 font-extrabold text-xl">
                <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-accent-500">🎓</span>
                <span>AlmanyaUni</span>
            </a>
            <a href="/?_l=tr"
               class="bg-accent-500 hover:bg-accent-600 transition px-4 py-2 rounded-lg font-semibold text-sm">
                {{ $strings['switch_back'] }}
            </a>
        </div>
    </header>

    <main class="flex-1 flex items-center">
        <div class="max-w-5xl mx-auto px-6 py-16 w-full">

            <div class="text-center mb-12">
                <span class="inline-flex items-center gap-2 bg-accent-500/20 border border-accent-500/40 text-accent-400 px-4 py-1.5 rounded-full text-sm font-semibold mb-6">
                    ⏳ {{ $strings['badge'] }}
                </span>
                <h1 class="text-4xl md:text-6xl font-extrabold leading-tight mb-5">
                    {{ $strings['title'] }}
                </h1>
                <p class="text-lg md:text-xl text-primary-100 max-w-2xl mx-auto">
                    {{ $strings['subtitle'] }}
                </p>
            </div>

            @if (! empty($strings['features']))
                <div class="bg-white/5 border border-white/10 backdrop-blur rounded-2xl p-6 md:p-8 max-w-3xl mx-auto mb-10">
                    <h2 class="text-sm font-semibold uppercase tracking-wide text-primary-200 mb-5">
                        {{ $strings['features_title'] }}
                    </h2>
                    <ul class="space-y-3">
                        @foreach ($strings['features'] as [$icon, $text])
                            <li class="flex items-start gap-3 text-primary-50">
                                <span class="text-2xl flex-shrink-0">{{ $icon }}</span>
                                <span class="leading-relaxed pt-0.5">{{ $text }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="flex flex-col sm:flex-row gap-3 justify-center max-w-md mx-auto">
                <a href="/?_l=tr"
                   class="flex-1 bg-accent-500 hover:bg-accent-600 transition text-center px-6 py-3.5 rounded-lg font-bold shadow-lg">
                    {{ $strings['cta_back'] }} →
                </a>
                <a href="mailto:technsug@gmail.com?subject={{ urlencode($strings['cta_notify']) }}&body={{ urlencode('Locale: ' . $locale) }}"
                   class="flex-1 bg-white/10 hover:bg-white/20 border border-white/20 transition text-center px-6 py-3.5 rounded-lg font-semibold">
                    ✉️ {{ $strings['cta_notify'] }}
                </a>
            </div>

            <p class="text-center text-sm text-primary-300 mt-10 max-w-2xl mx-auto">
                {{ $strings['footer'] }}
            </p>
        </div>
    </main>

    <footer class="border-t border-white/10 py-6">
        <p class="text-center text-xs text-primary-300">
            &copy; {{ date('Y') }} AlmanyaUni · technsug@gmail.com
        </p>
    </footer>

</body>
</html>
