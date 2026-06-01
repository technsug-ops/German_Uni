@extends('layouts.app')

@section('title', __('About') . ' — ' . brand('name'))

<x-seo
    :title="__('About — Public-Benefit Germany Education Guide')"
    :description="__('AlmanyaUni is a public-benefit Germany education and career platform — built with official data, community experience, and volunteer effort.')"
/>

@section('content')

{{-- ============ HERO ============ --}}
<section class="bg-gradient-to-br from-primary-700 via-primary-600 to-primary-800 text-white">
    <div class="max-w-[1400px] mx-auto px-4 py-16 text-center">
        <span class="inline-flex items-center gap-2 bg-white/10 border border-white/20 backdrop-blur px-4 py-1.5 rounded-full text-sm font-semibold mb-6">
            <x-svg-icon name="globe" class="w-4 h-4" />
            {{ __('Public benefit · Student-supported') }}
        </span>
        <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold leading-tight mb-5">
            {{ __('Studying in Germany,') }} <br>
            <span class="text-accent-400">{{ __('accessible to everyone.') }}</span>
        </h1>
        <p class="text-lg md:text-xl text-primary-100 max-w-3xl mx-auto">
            @if (app()->getLocale() === 'tr')
                AlmanyaUni — Türk öğrenciler için başladık, dünyaya açılıyoruz.
                Resmi kaynaklardan derlediğimiz veriler, topluluğun deneyimi ve gönüllülerin emeği ile büyüyen
                <strong class="text-white">kamu yararına</strong> bir platformuz.
            @else
                {{ brand('name') . ' — ' . __('a public-benefit platform built on data from official sources, community experience, and volunteer effort. We help international students navigate higher education in Germany.') }}
            @endif
        </p>
    </div>
</section>

{{-- ============ STATS ============ --}}
<section class="bg-white border-b border-gray-200">
    <div class="max-w-[1400px] mx-auto px-4 py-12">
        <h2 class="text-center text-sm font-semibold uppercase tracking-wider text-gray-500 mb-8">{{ __('What\'s on AlmanyaUni') }}</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
            <a href="{{ route('universities.index') }}" class="block hover:opacity-80 transition">
                <p class="text-3xl md:text-4xl font-extrabold text-primary-700">{{ number_format($stats['universities'], 0, ',', '.') }}</p>
                <p class="text-xs text-gray-600 mt-1 inline-flex items-center gap-1 justify-center"><x-svg-icon name="academic-cap" class="w-3.5 h-3.5" /> {{ __('Universities') }}</p>
            </a>
            <a href="{{ route('programs.index') }}" class="block hover:opacity-80 transition">
                <p class="text-3xl md:text-4xl font-extrabold text-primary-700">{{ number_format($stats['programs'], 0, ',', '.') }}</p>
                <p class="text-xs text-gray-600 mt-1 inline-flex items-center gap-1 justify-center"><x-svg-icon name="book-open" class="w-3.5 h-3.5" /> {{ __('Programs') }}</p>
            </a>
            <a href="{{ route('professions.index') }}" class="block hover:opacity-80 transition">
                <p class="text-3xl md:text-4xl font-extrabold text-primary-700">{{ number_format($stats['professions'], 0, ',', '.') }}</p>
                <p class="text-xs text-gray-600 mt-1 inline-flex items-center gap-1 justify-center"><x-svg-icon name="briefcase" class="w-3.5 h-3.5" /> {{ __('Professions') }}</p>
            </a>
            <a href="{{ route('scholarships.index') }}" class="block hover:opacity-80 transition">
                <p class="text-3xl md:text-4xl font-extrabold text-accent-600">{{ number_format($stats['scholarships'], 0, ',', '.') }}</p>
                <p class="text-xs text-gray-600 mt-1 inline-flex items-center gap-1 justify-center"><x-svg-icon name="trophy" class="w-3.5 h-3.5" /> {{ __('Scholarships') }}</p>
            </a>
            <a href="{{ route('cities.index') }}" class="block hover:opacity-80 transition">
                <p class="text-3xl md:text-4xl font-extrabold text-primary-700">{{ number_format($stats['cities'], 0, ',', '.') }}</p>
                <p class="text-xs text-gray-600 mt-1 inline-flex items-center gap-1 justify-center"><x-svg-icon name="building-office" class="w-3.5 h-3.5" /> {{ __('Cities') }}</p>
            </a>
            <a href="{{ route('states.index') }}" class="block hover:opacity-80 transition">
                <p class="text-3xl md:text-4xl font-extrabold text-primary-700">{{ $stats['states'] }}</p>
                <p class="text-xs text-gray-600 mt-1 inline-flex items-center gap-1 justify-center"><x-svg-icon name="map" class="w-3.5 h-3.5" /> {{ __('States') }}</p>
            </a>
            <a href="{{ route('blog.index') }}" class="block hover:opacity-80 transition">
                <p class="text-3xl md:text-4xl font-extrabold text-accent-600">{{ $stats['posts'] }}</p>
                <p class="text-xs text-gray-600 mt-1 inline-flex items-center gap-1 justify-center"><x-svg-icon name="document-text" class="w-3.5 h-3.5" /> {{ __('Blog') }}</p>
            </a>
            <a href="{{ route('faqs.index') }}" class="block hover:opacity-80 transition">
                <p class="text-3xl md:text-4xl font-extrabold text-accent-600">{{ $stats['faqs'] }}</p>
                <p class="text-xs text-gray-600 mt-1 inline-flex items-center gap-1 justify-center"><x-svg-icon name="information-circle" class="w-3.5 h-3.5" /> {{ __('FAQ') }}</p>
            </a>
        </div>

        <p class="text-center text-sm text-gray-600 mt-8">
            {{ __('Plus') }} <a href="{{ route('tools.index') }}" class="text-primary-700 font-semibold hover:underline">{{ __('6 interactive tools') }}</a>:
            {{ __('cost of living, budget planner, visa cost, application calendar, grade converter, and university match quiz.') }}
        </p>
    </div>
</section>

{{-- ============ MISSION ============ --}}
<section class="max-w-[1400px] mx-auto px-4 py-16">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white border border-gray-200 rounded-xl p-6">
            <div class="w-12 h-12 bg-primary-100 text-primary-700 rounded-lg flex items-center justify-center mb-3"><x-svg-icon name="academic-cap" class="w-7 h-7" /></div>
            <h3 class="font-bold text-gray-900 mb-2">{{ __('Educational Access') }}</h3>
            <p class="text-sm text-gray-600">{{ __('Everything you need to study in Germany — universities, programs, visa, language, living — in one place, free and clear.') }}</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-6">
            <div class="w-12 h-12 bg-accent-100 text-accent-700 rounded-lg flex items-center justify-center mb-3"><x-svg-icon name="users" class="w-7 h-7" /></div>
            <h3 class="font-bold text-gray-900 mb-2">{{ __('Community Driven') }}</h3>
            <p class="text-sm text-gray-600">{!! __('Our content is distilled from <strong>1.5+ million</strong> community messages. Share your experience to make the path easier for others.') !!}</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-6">
            <div class="w-12 h-12 bg-green-100 text-green-700 rounded-lg flex items-center justify-center mb-3"><x-svg-icon name="globe" class="w-7 h-7" /></div>
            <h3 class="font-bold text-gray-900 mb-2">{{ __('Multilingual Vision') }}</h3>
            <p class="text-sm text-gray-600">{{ __('English first, then Turkish, Arabic, Farsi, German, French — bringing students from everywhere to Germany.') }}</p>
        </div>
    </div>
</section>

{{-- ============ VISION ============ --}}
<section class="bg-gradient-to-br from-primary-50 to-white border-y border-primary-100">
    <div class="max-w-[1400px] mx-auto px-4 py-16">
        <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-6 text-center">{{ __('Our Vision') }}</h2>
        <div class="space-y-6 text-lg text-gray-700 leading-relaxed">
            @if (app()->getLocale() === 'tr')
                <p>
                    Almanya, dünyanın en kaliteli ve <strong>ücretsiz</strong> eğitim sistemlerinden birine sahip — ama bu fırsata erişim için gereken bilgi
                    hâlâ dağınık, Almanca ağırlıklı ve karmaşık. Türk öğrenciler her yıl saatlerce forumlarda, eski blog yazılarında, telefonda
                    anlam çıkarmaya çalışıyor. Bu zamanın çoğu israf.
                </p>
                <p>
                    <strong class="text-primary-700">AlmanyaUni'nin amacı net:</strong> Almanya'da öğrencilik kararına dair her sorunun
                    cevabını — vize gereksinimleri, hangi üniversitenin hangi programa nasıl baktığı, yaşam maliyetinin gerçekçi rakamları,
                    Studienkolleg gerekliliği, başvuru deadline'ları, hangi mesleğin hangi diploma ile yapıldığı — tek yerde, Türkçe ve güncel tutmak.
                </p>
                <p>
                    Bunu <strong>kamu yararına</strong> yapıyoruz. İçeriklerin çoğu <strong>ücretsiz ve ücretsiz kalacak.</strong>
                    Site gelirini reklam ve gönüllü destek ile sürdürüyoruz; toplanan kaynağın ana payı içerik üretimi, çeviri ve teknik altyapıya gidiyor.
                    Hedefimiz: <em>Türkiye'den yola çıkıp Almanya'ya değer katan her öğrenci için en güvenilir ilk durak olmak.</em>
                </p>
            @else
                <p>
                    {!! __('Germany has one of the world\'s highest-quality and <strong>tuition-free</strong> education systems — but the information needed to access this opportunity is still scattered, German-heavy, and complex. Prospective students spend hours every year on forums, old blog posts, and calls trying to make sense of it. Most of that time is wasted.') !!}
                </p>
                <p>
                    {!! __('<strong>AlmanyaUni\'s purpose is clear:</strong> to keep every answer about studying in Germany — visa requirements, how each university evaluates which program, realistic cost-of-living numbers, Studienkolleg requirements, application deadlines, which profession needs which diploma — in one place, current and accessible in your language.') !!}
                </p>
                <p>
                    {!! __('We do this <strong>for the public good</strong>. Most content is <strong>free and will stay free.</strong> We sustain the site through ads and volunteer support; the bulk of revenue goes to content production, translation, and technical infrastructure. Our goal: <em>to be the most trusted first stop for every student bringing value from anywhere to Germany.</em>') !!}
                </p>
            @endif
        </div>
    </div>
</section>

{{-- ============ TEAM ============ --}}
<section class="max-w-[1400px] mx-auto px-4 py-16">
    <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-3 text-center">{{ __('Team') }}</h2>
    <p class="text-center text-gray-600 max-w-2xl mx-auto mb-10">
        {{ __('AlmanyaUni is built by a small but passionate team and a growing volunteer community.') }}
    </p>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @foreach ($team as $m)
            @php $color = $m['color'] ?? 'primary'; @endphp
            <div class="bg-white border border-gray-200 rounded-xl p-6">
                <div class="flex items-start gap-4 mb-4">
                    <div class="w-16 h-16 rounded-full flex items-center justify-center text-3xl font-extrabold flex-shrink-0
                        @if ($color === 'accent') bg-accent-500 text-white
                        @else bg-primary-600 text-white @endif">
                        {{ $m['avatar'] }}
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 text-lg leading-tight">{{ $m['name'] }}</h3>
                        <p class="text-sm text-{{ $color }}-700 font-semibold">{!! $m['role'] !!}</p>
                    </div>
                </div>
                <p class="text-sm text-gray-700 leading-relaxed mb-3">{!! $m['bio'] !!}</p>
                @if (! empty($m['social']))
                    <div class="flex flex-wrap gap-2">
                        @foreach ($m['social'] as $s)
                            <a href="{{ $s['url'] }}" class="inline-flex items-center gap-1 text-sm bg-gray-100 hover:bg-gray-200 text-gray-800 px-2.5 py-1 rounded transition">
                                <span>{{ $s['icon'] }}</span> <span>{{ $s['label'] }}</span>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</section>

{{-- ============ CONTRIBUTE ============ --}}
<section class="bg-gradient-to-br from-accent-50 to-white border-t border-accent-100">
    <div class="max-w-[1400px] mx-auto px-4 py-16">
        <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-3 text-center">{{ __('How You Can Contribute') }}</h2>
        <p class="text-center text-gray-600 max-w-2xl mx-auto mb-10">
            {{ __('This platform grows with student support. Here are some ways to help:') }}
        </p>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            <a href="{{ route('contribute') }}" title="{{ __('Write Content') }}"
               class="block bg-white border border-gray-200 rounded-xl p-6 hover:border-primary-400 hover:shadow-md transition">
                <div class="text-primary-600 mb-2"><x-svg-icon name="pencil" class="w-8 h-8" /></div>
                <h3 class="font-bold text-gray-900 mb-2">{{ __('Write Content') }}</h3>
                <p class="text-sm text-gray-600">{{ __('Share your own experience in Germany, application process, or city impression as a blog post.') }}</p>
            </a>
            <a href="{{ route('contact', ['type' => 'partnership', 'subject' => 'Translate']) }}" title="{{ __('Translate') }}"
               class="block bg-white border border-gray-200 rounded-xl p-6 hover:border-primary-400 hover:shadow-md transition">
                <div class="text-primary-600 mb-2"><x-svg-icon name="globe" class="w-8 h-8" /></div>
                <h3 class="font-bold text-gray-900 mb-2">{{ __('Translate') }}</h3>
                <p class="text-sm text-gray-600">{{ __('Help translate German university descriptions or program details into other languages.') }}</p>
            </a>
            <a href="{{ route('contact', ['type' => 'partnership', 'subject' => 'Develop']) }}" title="{{ __('Develop') }}"
               class="block bg-white border border-gray-200 rounded-xl p-6 hover:border-primary-400 hover:shadow-md transition">
                <div class="text-primary-600 mb-2"><x-svg-icon name="cog" class="w-8 h-8" /></div>
                <h3 class="font-bold text-gray-900 mb-2">{{ __('Develop') }}</h3>
                <p class="text-sm text-gray-600">{{ __('Contribute code if you know Laravel, Tailwind, or frontend. (Open-source plan coming soon.)') }}</p>
            </a>
            <a href="{{ route('faqs.index') }}" title="{{ __('Answer Questions') }}"
               class="block bg-white border border-gray-200 rounded-xl p-6 hover:border-primary-400 hover:shadow-md transition">
                <div class="text-primary-600 mb-2"><x-svg-icon name="chat" class="w-8 h-8" /></div>
                <h3 class="font-bold text-gray-900 mb-2">{{ __('Answer Questions') }}</h3>
                <p class="text-sm text-gray-600">{{ __('Help us update or expand missing FAQ entries.') }}</p>
            </a>
            <a href="https://t.me/almanyauni" target="_blank" rel="noopener" title="{{ __('Share') }}"
               class="block bg-white border border-gray-200 rounded-xl p-6 hover:border-primary-400 hover:shadow-md transition">
                <div class="text-primary-600 mb-2"><x-svg-icon name="bell" class="w-8 h-8" /></div>
                <h3 class="font-bold text-gray-900 mb-2">{{ __('Share') }}</h3>
                <p class="text-sm text-gray-600">{{ __('Recommend AlmanyaUni to friends planning to study in Germany. Reach is the most valuable help.') }}</p>
            </a>
            <button type="button" onclick="document.getElementById('feedbackToggle')?.click()" title="{{ __('Feedback') }}"
                    class="text-left block bg-white border border-gray-200 rounded-xl p-6 hover:border-primary-400 hover:shadow-md transition w-full">
                <div class="text-primary-600 mb-2"><x-svg-icon name="star" class="w-8 h-8" /></div>
                <h3 class="font-bold text-gray-900 mb-2">{{ __('Feedback') }}</h3>
                <p class="text-sm text-gray-600">{{ __('Tell us about anything missing, wrong, or improvable — we\'ll fix it together.') }}</p>
            </button>
        </div>

        <div class="text-center mt-10">
            <a href="{{ route('contact', ['type' => 'partnership']) }}" class="inline-flex items-center gap-2 bg-accent-500 hover:bg-accent-600 text-white font-bold px-8 py-3.5 rounded-lg shadow-lg transition">
                <x-svg-icon name="envelope" class="w-5 h-5" />
                {{ __('Contact Us — Contribute') }}
            </a>
        </div>
    </div>
</section>

{{-- ============ ROADMAP ============ --}}
<section class="max-w-[1400px] mx-auto px-4 py-16">
    <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-3 text-center">{{ __('Where We\'re Going') }}</h2>
    <p class="text-center text-gray-600 max-w-2xl mx-auto mb-10">{{ __('Some things we want to build in the near future:') }}</p>
    <div class="space-y-3">
        <div class="bg-white border-l-4 border-primary-500 border-y border-r border-gray-200 rounded-r-lg p-4">
            <p class="font-semibold text-gray-900 inline-flex items-center gap-1.5"><x-svg-icon name="globe" class="w-4 h-4" /> {{ __('Multi-language support') }}</p>
            <p class="text-sm text-gray-600 mt-1">{{ __('After English & Turkish: Arabic, Farsi, German, French — to reach more students.') }}</p>
        </div>
        <div class="bg-white border-l-4 border-primary-500 border-y border-r border-gray-200 rounded-r-lg p-4">
            <p class="font-semibold text-gray-900 inline-flex items-center gap-1.5"><x-svg-icon name="briefcase" class="w-4 h-4" /> {{ __('Career / Job integration') }}</p>
            <p class="text-sm text-gray-600 mt-1">{{ __('Bundesagentur für Arbeit Jobbörse + Entgeltatlas APIs for salaries and job listings.') }}</p>
        </div>
        <div class="bg-white border-l-4 border-primary-500 border-y border-r border-gray-200 rounded-r-lg p-4">
            <p class="font-semibold text-gray-900 inline-flex items-center gap-1.5"><x-svg-icon name="calendar" class="w-4 h-4" /> {{ __('Application calendar & notifications') }}</p>
            <p class="text-sm text-gray-600 mt-1">{{ __('Personalized deadline reminders based on saved programs.') }}</p>
        </div>
        <div class="bg-white border-l-4 border-primary-500 border-y border-r border-gray-200 rounded-r-lg p-4">
            <p class="font-semibold text-gray-900 inline-flex items-center gap-1.5"><x-svg-icon name="academic-cap" class="w-4 h-4" /> {{ __('Community & mentors') }}</p>
            <p class="text-sm text-gray-600 mt-1">{{ __('Mentor matching between students already in Germany and new applicants.') }}</p>
        </div>
        <div class="bg-white border-l-4 border-primary-500 border-y border-r border-gray-200 rounded-r-lg p-4">
            <p class="font-semibold text-gray-900 inline-flex items-center gap-1.5"><x-svg-icon name="cog" class="w-4 h-4" /> {{ __('Open source') }}</p>
            <p class="text-sm text-gray-600 mt-1">{{ __('Eventually open the platform code to grow with more developers.') }}</p>
        </div>
    </div>
</section>

{{-- ============ RECENT POSTS ============ --}}
@if (! empty($recentPosts) && $recentPosts->count() > 0)
    <section class="bg-gradient-to-br from-gray-50 to-white border-t border-gray-200">
        <div class="max-w-[1400px] mx-auto px-4 py-16">
            <div class="flex items-baseline justify-between mb-6">
                <h2 class="text-2xl md:text-3xl font-extrabold text-gray-900 inline-flex items-center gap-2"><x-svg-icon name="document-text" class="w-7 h-7" /> {{ __('Latest Posts') }}</h2>
                <a href="{{ route('blog.index') }}" class="text-sm text-primary-600 hover:underline font-semibold">{{ __('All blog') }} →</a>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach ($recentPosts as $post)
                    <a href="{{ route('blog.show', $post->slug) }}"
                       class="group block bg-white rounded-xl border border-gray-200 hover:border-primary-400 hover:shadow-md transition p-5">
                        @if ($post->category)
                            <p class="text-xs font-semibold uppercase tracking-wide mb-2"
                               style="color: {{ $post->category->color ?? '#1E40AF' }}">
                                {{ __($post->category->name) }}
                            </p>
                        @endif
                        <h3 class="font-bold text-gray-900 group-hover:text-primary-600 leading-tight mb-2 line-clamp-2">{{ $post->title }}</h3>
                        @if ($post->excerpt)
                            <p class="text-sm text-gray-600 line-clamp-3 mb-3">{{ \Illuminate\Support\Str::limit($post->excerpt, 110) }}</p>
                        @endif
                        <p class="text-xs text-gray-500 mt-auto">
                            {{ $post->published_at->translatedFormat('d M Y') }} · {{ $post->reading_minutes }} {{ __('min') }}
                        </p>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
@endif

{{-- ============ CONTACT CTA ============ --}}
<section class="bg-gradient-to-br from-primary-600 to-accent-500 text-white">
    <div class="max-w-3xl mx-auto px-4 py-16 text-center">
        <h2 class="text-2xl md:text-3xl font-extrabold mb-3 inline-flex items-center gap-2"><x-svg-icon name="envelope" class="w-7 h-7" /> {{ __('Still got a question?') }}</h2>
        <p class="text-primary-100 mb-6">
            {{ __('Couldn\'t find an answer in FAQ, want to suggest a missing blog topic, or share your own experience? Email us.') }}
        </p>
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ route('contact', ['type' => 'general']) }}"
               class="inline-flex items-center gap-2 bg-white text-primary-700 hover:bg-gray-100 font-bold px-8 py-3 rounded-lg shadow-lg transition">
                <x-svg-icon name="envelope" class="w-5 h-5" />
                {{ __('Contact us') }}
            </a>
            <a href="{{ route('faqs.index') }}"
               class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/20 border border-white/30 text-white font-bold px-8 py-3 rounded-lg transition">
                <x-svg-icon name="information-circle" class="w-5 h-5" />
                {{ __('Browse FAQ first') }}
            </a>
        </div>
    </div>
</section>

@endsection
