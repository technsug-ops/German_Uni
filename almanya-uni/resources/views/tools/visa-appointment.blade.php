@extends('layouts.app')

@section('title', __('Germany Student Visa Appointment (iData) — Step-by-Step Guide') . ' — ' . brand('name'))

<x-seo
    :title="__('Germany Student Visa Appointment (iData)')"
    :description="__('How to book your Germany student visa appointment via iData in Turkey: national (Type-D) visa, fees, required documents, Istanbul & Ankara offices — verified, step by step.')"
/>

<x-json-ld :data="\App\Support\Seo::breadcrumbs([
    ['name' => __('Home'), 'url' => route('home')],
    ['name' => __('Tools'), 'url' => route('tools.index')],
    ['name' => __('Visa Appointment'), 'url' => route('tools.visa-appointment')],
])" />

@php
    // Doğrulanmış figürler (2026-06-04, çoklu kaynak: iData + Auswärtiges Amt).
    $nationalFee = '€75';
    $idataFee = '€32.81';
    $isTr = app()->getLocale() === 'tr';
@endphp

@section('content')
{{-- HERO --}}
<section class="bg-gradient-to-br from-rose-700 via-red-600 to-orange-500 text-white">
    <div class="max-w-[1400px] mx-auto px-4 py-10 md:py-14">
        <nav class="text-sm text-rose-100 mb-3">
            <a href="/" class="hover:text-white">{{ __('Home') }}</a>
            <span class="mx-2 opacity-50">›</span>
            <a href="{{ route('tools.index') }}" class="hover:text-white">{{ __('Tools') }}</a>
            <span class="mx-2 opacity-50">›</span>
            <span class="text-white">{{ __('Visa Appointment') }}</span>
        </nav>
        <h1 class="text-3xl md:text-5xl font-extrabold leading-tight drop-shadow mb-3 inline-flex items-center gap-3">
            <x-svg-icon name="calendar" class="w-8 h-8 md:w-10 md:h-10" />
            {{ __('Germany Student Visa Appointment (iData)') }}
        </h1>
        <p class="text-lg md:text-xl text-rose-100 max-w-3xl">
            {{ __('iData is the only center authorized by the German Foreign Office to collect visa applications in Turkey. Here is the verified, step-by-step process for the student (national / Type-D) visa.') }}
        </p>

        {{-- Quick facts --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mt-6">
            <div class="bg-white/15 backdrop-blur rounded-lg p-3 ring-1 ring-white/20">
                <p class="text-2xl font-bold">iData</p>
                <p class="text-xs text-rose-100 mt-0.5">{{ __('Only authorized center') }}</p>
            </div>
            <div class="bg-white/15 backdrop-blur rounded-lg p-3 ring-1 ring-white/20">
                <p class="text-2xl font-bold">{{ __('Type-D') }}</p>
                <p class="text-xs text-rose-100 mt-0.5">{{ __('National visa (>90 days)') }}</p>
            </div>
            <div class="bg-white/15 backdrop-blur rounded-lg p-3 ring-1 ring-white/20">
                <p class="text-2xl font-bold">{{ $nationalFee }}</p>
                <p class="text-xs text-rose-100 mt-0.5">{{ __('Visa fee + :fee iData', ['fee' => $idataFee]) }}</p>
            </div>
            <div class="bg-white/15 backdrop-blur rounded-lg p-3 ring-1 ring-white/20">
                <p class="text-2xl font-bold">{{ __('Online') }}</p>
                <p class="text-xs text-rose-100 mt-0.5">{{ __('Appointment via iData site') }}</p>
            </div>
        </div>
    </div>
</section>

<div class="max-w-[1100px] mx-auto px-4 py-10">

    {{-- Featured snippet (AIO / Google AI Overview hedefi) --}}
    <x-featured-snippet
        :question="__('How do I get a Germany student visa appointment in Turkey?')"
        :answer="__('Germany student visa applications in Turkey are submitted only through iData, the officially authorized center. Register on the waiting list for free on the iData website; once your appointment is confirmed, pay the iData service fee within 48 hours. Submit your documents on the appointment day at the Istanbul or Ankara office. The national (Type-D) visa fee is €75 plus a €32.81 iData service fee.')"
        :steps="[
            ['title' => __('Prepare your documents'), 'description' => __('Admission letter, blocked account (Sperrkonto) proof, academic records, health insurance, passport.')],
            ['title' => __('Register on the iData website'), 'description' => __('Join the national-visa waiting list for free; appointments are made only online since July 2024.')],
            ['title' => __('Confirm + pay the service fee'), 'description' => __('After the appointment is confirmed, pay the €32.81 iData fee within 48 hours.')],
            ['title' => __('Attend the appointment'), 'description' => __('Submit documents and biometrics at the Istanbul or Ankara iData office.')],
            ['title' => __('Wait for processing'), 'description' => __('A national visa takes longer than a Schengen visa — plan for several weeks to months.')],
        ]"
    />

    {{-- ADIM ADIM (locale-split: TR tam rehber / EN-DE özet) --}}
    <section class="mt-10">
        <h2 class="text-2xl font-bold text-gray-900 mb-5 inline-flex items-center gap-2">
            <x-svg-icon name="list-bullet" class="w-6 h-6" /> {{ __('Step-by-step') }}
        </h2>

        @if ($isTr)
        <ol class="space-y-4">
            <li class="bg-white border border-gray-200 rounded-xl p-5 flex gap-4">
                <span class="flex-shrink-0 w-8 h-8 rounded-full bg-rose-100 text-rose-700 font-bold flex items-center justify-center">1</span>
                <div>
                    <h3 class="font-bold text-gray-900 mb-1">Evraklarını hazırla</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">Üniversite kabul belgesi (Zulassung), <a href="{{ route('tools.blocked-account') }}" class="text-rose-600 underline">bloke hesap (Sperrkonto)</a> dökümü, diploma/transkript, Almanca/İngilizce dil belgesi, sağlık sigortası, geçerli pasaport, biyometrik fotoğraf ve online doldurulan başvuru formu (VIDEX). Evrak eksikliği randevunun reddine yol açar.</p>
                </div>
            </li>
            <li class="bg-white border border-gray-200 rounded-xl p-5 flex gap-4">
                <span class="flex-shrink-0 w-8 h-8 rounded-full bg-rose-100 text-rose-700 font-bold flex items-center justify-center">2</span>
                <div>
                    <h3 class="font-bold text-gray-900 mb-1">iData web sitesinden randevu al</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">25 Temmuz 2024'ten beri randevular <strong>yalnızca iData web sitesi</strong> üzerinden yapılır. Ulusal (öğrenci) vize bekleme listesine <strong>ücretsiz</strong> kayıt olabilirsin. Öğrenci/ulusal vize için danışma hattı: 0850 460 8493.</p>
                </div>
            </li>
            <li class="bg-white border border-gray-200 rounded-xl p-5 flex gap-4">
                <span class="flex-shrink-0 w-8 h-8 rounded-full bg-rose-100 text-rose-700 font-bold flex items-center justify-center">3</span>
                <div>
                    <h3 class="font-bold text-gray-900 mb-1">Randevu onayı → 48 saat içinde hizmet ücreti</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">iData'dan randevu onayını aldıktan sonra <strong>48 saat içinde</strong> {{ $idataFee }} hizmet ücretini ödemen gerekir; aksi halde randevu iptal olur.</p>
                </div>
            </li>
            <li class="bg-white border border-gray-200 rounded-xl p-5 flex gap-4">
                <span class="flex-shrink-0 w-8 h-8 rounded-full bg-rose-100 text-rose-700 font-bold flex items-center justify-center">4</span>
                <div>
                    <h3 class="font-bold text-gray-900 mb-1">Başvuru günü (ofiste)</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">Randevu günü İstanbul veya Ankara iData ofisinde evraklarını teslim eder, biyometrik veri (parmak izi + foto) verirsin. Ulusal vize harcı {{ $nationalFee }}'tur (kamu bursu alanlar muaf olabilir).</p>
                </div>
            </li>
            <li class="bg-white border border-gray-200 rounded-xl p-5 flex gap-4">
                <span class="flex-shrink-0 w-8 h-8 rounded-full bg-rose-100 text-rose-700 font-bold flex items-center justify-center">5</span>
                <div>
                    <h3 class="font-bold text-gray-900 mb-1">Değerlendirme ve pasaport teslimi</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">Ulusal vize değerlendirmesi Schengen'den uzundur — <strong>haftalar, bazen aylar</strong> sürebilir. Bu yüzden kabul gelir gelmez randevu almaya çalış. Sonuç hazır olunca pasaportunu iData'dan teslim alırsın.</p>
                </div>
            </li>
        </ol>
        @else
        <ol class="space-y-4">
            <li class="bg-white border border-gray-200 rounded-xl p-5 flex gap-4">
                <span class="flex-shrink-0 w-8 h-8 rounded-full bg-rose-100 text-rose-700 font-bold flex items-center justify-center">1</span>
                <div>
                    <h3 class="font-bold text-gray-900 mb-1">{{ __('Prepare your documents') }}</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">{{ __('Admission letter (Zulassung), blocked account (Sperrkonto) proof, academic records, language certificate, health insurance, valid passport, biometric photo and the online application form (VIDEX). Missing documents lead to rejection.') }}</p>
                </div>
            </li>
            <li class="bg-white border border-gray-200 rounded-xl p-5 flex gap-4">
                <span class="flex-shrink-0 w-8 h-8 rounded-full bg-rose-100 text-rose-700 font-bold flex items-center justify-center">2</span>
                <div>
                    <h3 class="font-bold text-gray-900 mb-1">{{ __('Register on the iData website') }}</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">{{ __('Since 25 July 2024, appointments are made only on the iData website. You can join the national (student) visa waiting list for free.') }}</p>
                </div>
            </li>
            <li class="bg-white border border-gray-200 rounded-xl p-5 flex gap-4">
                <span class="flex-shrink-0 w-8 h-8 rounded-full bg-rose-100 text-rose-700 font-bold flex items-center justify-center">3</span>
                <div>
                    <h3 class="font-bold text-gray-900 mb-1">{{ __('Confirm + pay the service fee') }}</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">{{ __('After your appointment is confirmed, pay the :fee iData service fee within 48 hours, otherwise the appointment is cancelled.', ['fee' => $idataFee]) }}</p>
                </div>
            </li>
            <li class="bg-white border border-gray-200 rounded-xl p-5 flex gap-4">
                <span class="flex-shrink-0 w-8 h-8 rounded-full bg-rose-100 text-rose-700 font-bold flex items-center justify-center">4</span>
                <div>
                    <h3 class="font-bold text-gray-900 mb-1">{{ __('Attend the appointment') }}</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">{{ __('On the day, submit documents and biometrics at the Istanbul or Ankara iData office. The national visa fee is :fee (scholarship holders may be exempt).', ['fee' => $nationalFee]) }}</p>
                </div>
            </li>
            <li class="bg-white border border-gray-200 rounded-xl p-5 flex gap-4">
                <span class="flex-shrink-0 w-8 h-8 rounded-full bg-rose-100 text-rose-700 font-bold flex items-center justify-center">5</span>
                <div>
                    <h3 class="font-bold text-gray-900 mb-1">{{ __('Wait for processing') }}</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">{{ __('A national visa takes longer than a Schengen visa — several weeks to months. Apply as soon as you receive your admission.') }}</p>
                </div>
            </li>
        </ol>
        @endif
    </section>

    {{-- ÜCRETLER --}}
    <section class="mt-10 bg-amber-50 border border-amber-100 rounded-xl p-6">
        <h2 class="text-xl font-bold text-amber-900 mb-3 inline-flex items-center gap-2">
            <x-svg-icon name="currency-euro" class="w-5 h-5" /> {{ __('Fees') }}
        </h2>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <tbody class="divide-y divide-amber-100">
                    <tr>
                        <td class="py-2 pr-4 text-amber-900">{{ __('National (Type-D) visa fee') }}</td>
                        <td class="py-2 font-bold text-amber-900 text-right">{{ $nationalFee }} <span class="font-normal text-amber-700">({{ __('€37.50 for minors') }})</span></td>
                    </tr>
                    <tr>
                        <td class="py-2 pr-4 text-amber-900">{{ __('iData service fee') }}</td>
                        <td class="py-2 font-bold text-amber-900 text-right">{{ $idataFee }}</td>
                    </tr>
                    <tr>
                        <td class="py-2 pr-4 text-amber-900">{{ __('Public-scholarship holders') }}</td>
                        <td class="py-2 font-bold text-amber-900 text-right">{{ __('May be exempt') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <x-source-note
            :sources="[
                ['name' => 'iData', 'url' => 'https://www.idata.com.tr/de/en'],
                ['name' => 'Auswärtiges Amt', 'url' => 'https://tuerkei.diplo.de/'],
            ]"
            updated="2026-06-04"
            :note="__('Fees change — always confirm on the official iData / consulate site.')"
            class="!bg-white/60 !border-amber-100"
        />
    </section>

    {{-- OFİSLER --}}
    <section class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <h3 class="font-bold text-gray-900 mb-1 inline-flex items-center gap-2"><x-svg-icon name="building-office" class="w-5 h-5" /> {{ __('Istanbul office') }}</h3>
            <p class="text-sm text-gray-600">İnönü Caddesi No: 10, Gümüşsuyu — Beyoğlu / İstanbul</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <h3 class="font-bold text-gray-900 mb-1 inline-flex items-center gap-2"><x-svg-icon name="building-office" class="w-5 h-5" /> {{ __('Ankara office') }}</h3>
            <p class="text-sm text-gray-600">Atatürk Bulvarı No: 118, Kavaklıdere — Çankaya / Ankara</p>
        </div>
    </section>

    @if ($isTr)
    {{-- TÜRK ÖĞRENCİLER İÇİN İPUÇLARI (Telegram topluluk sinyalinden) --}}
    <section class="mt-8 bg-emerald-50 border border-emerald-100 rounded-xl p-6">
        <h2 class="text-xl font-bold text-emerald-900 mb-3">🇹🇷 İpuçları (topluluk deneyiminden)</h2>
        <ul class="text-emerald-800 text-sm space-y-2 leading-relaxed">
            <li>• <strong>Randevu kıtlığı</strong> en büyük dert — kabul gelir gelmez bekleme listesine kaydol, beklemeyi sonraya bırakma.</li>
            <li>• Bekleme listesine kayıt <strong>ücretsiz</strong>; ücreti sadece randevu onaylanınca (48 saat içinde) ödüyorsun.</li>
            <li>• Evrakları <strong>eksiksiz ve düzenli</strong> götür — eksik evrak randevu reddi demek, yeni randevu haftalar kaybettirir.</li>
            <li>• <a href="{{ route('tools.blocked-account') }}" class="underline">Sperrkonto</a> dökümü ve kabul belgesi olmadan başvuru kabul edilmez.</li>
            <li>• Sahte "randevu satıcılarına" itibar etme — tek yetkili kanal iData web sitesidir.</li>
        </ul>
    </section>
    @endif

    {{-- Cross-link --}}
    <section class="mt-8 grid grid-cols-1 sm:grid-cols-3 gap-3">
        <a href="{{ route('tools.blocked-account') }}" class="block bg-white border border-gray-200 rounded-xl p-4 hover:border-rose-400 hover:shadow-sm transition">
            <p class="mb-1 text-rose-600"><x-svg-icon name="banknotes" class="w-6 h-6" /></p>
            <p class="font-bold text-gray-900">{{ __('Blocked Account') }}</p>
            <p class="text-xs text-gray-500 mt-0.5">{{ __('Sperrkonto for the visa') }}</p>
        </a>
        <a href="{{ route('tools.visa-cost') }}" class="block bg-white border border-gray-200 rounded-xl p-4 hover:border-rose-400 hover:shadow-sm transition">
            <p class="mb-1 text-rose-600"><x-svg-icon name="currency-euro" class="w-6 h-6" /></p>
            <p class="font-bold text-gray-900">{{ __('Visa Cost') }}</p>
            <p class="text-xs text-gray-500 mt-0.5">{{ __('Total visa budget') }}</p>
        </a>
        <a href="{{ route('tools.deadlines') }}" class="block bg-white border border-gray-200 rounded-xl p-4 hover:border-rose-400 hover:shadow-sm transition">
            <p class="mb-1 text-rose-600"><x-svg-icon name="calendar" class="w-6 h-6" /></p>
            <p class="font-bold text-gray-900">{{ __('Application Calendar') }}</p>
            <p class="text-xs text-gray-500 mt-0.5">{{ __('Deadlines + ICS export') }}</p>
        </a>
    </section>

    {{-- Disclaimer --}}
    <p class="text-xs text-gray-400 mt-8 text-center max-w-3xl mx-auto">
        {{ __('This guide is informational and based on official iData and Federal Foreign Office sources verified on the date shown. The process and fees may change — always confirm on the official iData website and the German consulate before applying.') }}
    </p>
</div>
@endsection
