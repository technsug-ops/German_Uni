@props(['degree' => null, 'language' => null])

{{-- Spesifik gereklilik verisi yokken degree+dile göre STANDART Alman gerekliliklerini
     gösterir. DB'ye sahte veri YAZILMAZ; bu yalnızca render-zamanı genel rehberdir ve
     "resmi sayfadan doğrula" notuyla açıkça etiketlenir (sıfır halüsinasyon, dürüst). --}}
@php
    $deg = strtolower((string) $degree);
    $lang = strtolower((string) $language);

    // Yeterlilik — dereceye göre
    $qual = match (true) {
        in_array($deg, ['master'], true) => __('A recognised Bachelor\'s degree in a related field; some programs require a minimum grade or specific prerequisites.'),
        in_array($deg, ['phd', 'promotion', 'doctorate'], true) => __('A recognised Master\'s degree in a related field and acceptance by a supervisor (Doktorvater/Betreuer).'),
        default => __('A recognised higher-education entrance qualification (Abitur or equivalent). International applicants may need recognition via Anabin and, if not directly eligible, a Studienkolleg + assessment exam (Feststellungsprüfung).'),
    };

    // Dil — programın diline göre
    $langReq = match (true) {
        $lang === 'de' => __('German at C1 level (DSH-2, TestDaF TDN 4, Goethe-Zertifikat C1, or telc C1 Hochschule).'),
        $lang === 'en' => __('English proficiency (typically IELTS ~6.5 or TOEFL iBT ~90). Basic German is helpful for daily life.'),
        $lang === 'both' => __('Depending on your chosen track: German at C1 (DSH-2/TestDaF) or English (IELTS ~6.5 / TOEFL iBT ~90).'),
        default => null,
    };
@endphp

<section class="bg-gray-50 border border-gray-200 rounded-xl p-6">
    <h2 class="text-2xl font-bold text-gray-900 mb-2 flex items-center gap-2">
        <x-svg-icon name="information-circle" class="w-6 h-6 text-gray-500" /> {{ __('General Requirements') }}
    </h2>
    <p class="text-sm text-gray-500 mb-4">{{ __('Specific requirements for this program are not in our database yet. The following are typical for this degree and language in Germany — always verify the exact requirements on the program\'s official page.') }}</p>

    <div class="space-y-4 text-gray-800">
        <div>
            <h3 class="font-semibold text-gray-900 mb-1">{{ __('Academic qualification') }}</h3>
            <p class="leading-relaxed">{{ $qual }}</p>
        </div>

        @if ($langReq)
            <div>
                <h3 class="font-semibold text-gray-900 mb-1">{{ __('Language') }}</h3>
                <p class="leading-relaxed">{{ $langReq }}</p>
            </div>
        @endif

        <div>
            <h3 class="font-semibold text-gray-900 mb-1">{{ __('Typical documents') }}</h3>
            <ul class="list-disc list-inside leading-relaxed space-y-0.5">
                <li>{{ __('Recognised diploma and transcript (certified translations)') }}</li>
                <li>{{ __('Language certificate') }}</li>
                <li>{{ __('Passport and passport photo') }}</li>
                <li>{{ __('Tabular CV (Lebenslauf)') }}</li>
                <li>{{ __('Letter of motivation (for many programs)') }}</li>
                <li>{{ __('Application via uni-assist / VPD (for most international applicants)') }}</li>
            </ul>
        </div>
    </div>
</section>
