@extends('layouts.app')

{{--
    Türkiye'den Almanya öğrenim vizesi rehberi (yalnız /tr; controller diğer dilleri araç listesine yönlendirir).
    OSV-3 (14.10.2026) resmî kaynak düzeltmesi:
      - Öğrenim ve öğrenim yeri arama vizeleri YALNIZCA Konsolosluk Hizmet Portalı (digital.diplo.de/studium) üzerinden
        → https://tuerkei.diplo.de/tr-tr/service/05-visaeinreise/2728622-2728622
      - Diğer ulusal vizeler: harici hizmet sağlayıcılar iDATA ve IOM-FAP; iDATA hizmet bedeli 39,92 €, randevu ücreti 12 €, TL ile
        → https://tuerkei.diplo.de/tr-tr/service/05-visaeinreise/2728742-2728742
      - Ulusal vize harcı 75 € → AufenthV § 46 Abs. 2 Nr. 1
    Eski sürümdeki "iData tek yetkili kanal" iddiası, eski iData ücret tutarı, ofis adresleri ve reşit olmayanlar için
    indirimli harç tutarı doğrulanamadığı veya yanlış olduğu için kaldırıldı.
--}}

@section('title', 'Almanya Öğrenim Vizesi Başvurusu (Türkiye): Konsolosluk Hizmet Portalı ve iData — ' . brand('name'))

<x-seo
    title="Almanya Öğrenim Vizesi Başvurusu (Türkiye): Portal ve iData"
    description="Türkiye'den Almanya öğrenim ve öğrenim yeri arama vizesi başvurusu yalnızca Konsolosluk Hizmet Portalı üzerinden yapılır. iData'nın kapsamı, ulusal vize harcı ve iData bedelleri resmî kaynaklarla."
/>

<x-json-ld :data="\App\Support\Seo::breadcrumbs([
    ['name' => __('Home'), 'url' => route('home')],
    ['name' => __('Tools'), 'url' => route('tools.index')],
    ['name' => __('Visa Appointment'), 'url' => route('tools.visa-appointment')],
])" />

@php
    // Kontrol: 14.10.2026 (kaynaklar yukarıda).
    $nationalFee = '75 €';
    $idataFee = '39,92 €';
    $idataBookingFee = '12 €';
    $studyUrl = 'https://tuerkei.diplo.de/tr-tr/service/05-visaeinreise/2728622-2728622';
    $nationalUrl = 'https://tuerkei.diplo.de/tr-tr/service/05-visaeinreise/2728742-2728742';
    $portalUrl = 'https://digital.diplo.de/studium';
    $feeLawUrl = 'https://www.gesetze-im-internet.de/aufenthv/__46.html';
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
            Almanya Öğrenim Vizesi Başvurusu (Türkiye)
        </h1>
        <p class="text-lg md:text-xl text-rose-100 max-w-3xl">
            Türkiye'de <strong>öğrenim ve öğrenim yeri arama</strong> vizesi başvuruları artık yalnızca <strong>Konsolosluk Hizmet Portalı</strong> üzerinden yürütülüyor. iData bu iki vize türü için kanal değil; diğer ulusal vizelerde temsilcilikler iData ve IOM-FAP ile çalışıyor.
        </p>

        {{-- Quick facts --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mt-6">
            <div class="bg-white/15 backdrop-blur rounded-lg p-3 ring-1 ring-white/20">
                <p class="text-2xl font-bold">Portal</p>
                <p class="text-xs text-rose-100 mt-0.5">Öğrenim vizesi başvuru kanalı</p>
            </div>
            <div class="bg-white/15 backdrop-blur rounded-lg p-3 ring-1 ring-white/20">
                <p class="text-2xl font-bold">Tip D</p>
                <p class="text-xs text-rose-100 mt-0.5">Ulusal vize (90 günden uzun)</p>
            </div>
            <div class="bg-white/15 backdrop-blur rounded-lg p-3 ring-1 ring-white/20">
                <p class="text-2xl font-bold">{{ $nationalFee }}</p>
                <p class="text-xs text-rose-100 mt-0.5">Ulusal vize harcı</p>
            </div>
            <div class="bg-white/15 backdrop-blur rounded-lg p-3 ring-1 ring-white/20">
                <p class="text-2xl font-bold">{{ $idataFee }}</p>
                <p class="text-xs text-rose-100 mt-0.5">iData hizmet bedeli (öğrenim dışı ulusal vizeler)</p>
            </div>
        </div>
    </div>
</section>

<div class="max-w-[1100px] mx-auto px-4 py-10">

    {{-- Featured snippet (AIO / Google AI Overview hedefi) --}}
    <x-featured-snippet
        question="Türkiye'den Almanya öğrenci vizesi başvurusu nasıl yapılır?"
        answer="Türkiye'de öğrenim ve öğrenim yeri arama vizesi başvuruları yalnızca Konsolosluk Hizmet Portalı (digital.diplo.de/studium) üzerinden yapılır; iData bu vize türleri için kanal değildir. Portalda hesap açar, vize amacını seçer, formu doldurur ve belgelerini yüklersin; sonraki adımlarda temsilciliğin yönlendirmesini izlersin. Ulusal vize harcı 75 €'dur (AufenthV § 46)."
        :steps="[
            ['title' => 'Belgelerini hazırla', 'description' => 'Kabul belgesi, bloke hesap (Sperrkonto) dökümü, diploma ve transkript, dil belgesi, sağlık sigortası, geçerli pasaport.'],
            ['title' => 'Portal hesabı aç', 'description' => 'digital.diplo.de/studium adresinden Konsolosluk Hizmet Portalı\'na gir ve hesap oluştur.'],
            ['title' => 'Vize amacını seç ve formu doldur', 'description' => 'Öğrenim veya öğrenim yeri arama amacını seç, formu doldur ve belgelerini yükle.'],
            ['title' => 'Temsilciliğin yönlendirmesini izle', 'description' => 'Sonraki adımlar (ör. kimlik ve biyometri işlemleri) için portaldaki ve temsilcilikten gelen bilgileri takip et.'],
            ['title' => 'Kararı bekle', 'description' => 'Ulusal vize değerlendirmesi Schengen vizesinden uzun sürer; kabul gelir gelmez başvur.'],
        ]"
    />

    {{-- ADIM ADIM --}}
    <section class="mt-10">
        <h2 class="text-2xl font-bold text-gray-900 mb-5 inline-flex items-center gap-2">
            <x-svg-icon name="list-bullet" class="w-6 h-6" /> {{ __('Step-by-step') }}
        </h2>

        <ol class="space-y-4">
            <li class="bg-white border border-gray-200 rounded-xl p-5 flex gap-4">
                <span class="flex-shrink-0 w-8 h-8 rounded-full bg-rose-100 text-rose-700 font-bold flex items-center justify-center">1</span>
                <div>
                    <h3 class="font-bold text-gray-900 mb-1">Belgelerini hazırla</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">Üniversite kabul belgesi (Zulassung), <a href="{{ route('tools.blocked-account') }}" class="text-rose-600 underline">bloke hesap (Sperrkonto)</a> dökümü, diploma ve transkript, Almanca veya İngilizce dil belgesi, sağlık sigortası ve geçerli pasaport. Güncel belge listesini portalda ve temsilciliğin sayfasında kontrol et.</p>
                </div>
            </li>
            <li class="bg-white border border-gray-200 rounded-xl p-5 flex gap-4">
                <span class="flex-shrink-0 w-8 h-8 rounded-full bg-rose-100 text-rose-700 font-bold flex items-center justify-center">2</span>
                <div>
                    <h3 class="font-bold text-gray-900 mb-1">Konsolosluk Hizmet Portalı'nda hesap aç</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">Öğrenim ve öğrenim yeri arama vizesi başvuruları <strong>yalnızca</strong> <a href="{{ $portalUrl }}" target="_blank" rel="noopener" class="text-rose-600 underline">digital.diplo.de/studium</a> üzerinden yürütülüyor (<a href="{{ $studyUrl }}" target="_blank" rel="noopener" class="underline">Türkiye'deki Alman temsilcilikleri</a>). Bu iki vize türü için iData'dan randevu alınmaz.</p>
                </div>
            </li>
            <li class="bg-white border border-gray-200 rounded-xl p-5 flex gap-4">
                <span class="flex-shrink-0 w-8 h-8 rounded-full bg-rose-100 text-rose-700 font-bold flex items-center justify-center">3</span>
                <div>
                    <h3 class="font-bold text-gray-900 mb-1">Vize amacını seç, formu doldur, belgeleri yükle</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">Kabul belgen varsa öğrenim, henüz yoksa öğrenim yeri arama amacını seç. Formu eksiksiz doldur ve belgelerini portal üzerinden yükle.</p>
                </div>
            </li>
            <li class="bg-white border border-gray-200 rounded-xl p-5 flex gap-4">
                <span class="flex-shrink-0 w-8 h-8 rounded-full bg-rose-100 text-rose-700 font-bold flex items-center justify-center">4</span>
                <div>
                    <h3 class="font-bold text-gray-900 mb-1">Temsilciliğin yönlendirmesini izle</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">Sonraki adımlar için portaldaki ve temsilcilikten gelen bilgileri takip et. Ulusal vize harcı {{ $nationalFee }}'dur (<a href="{{ $feeLawUrl }}" target="_blank" rel="noopener" class="underline">AufenthV § 46</a>); bazı başvuru grupları için muafiyet veya indirim olabilir.</p>
                </div>
            </li>
            <li class="bg-white border border-gray-200 rounded-xl p-5 flex gap-4">
                <span class="flex-shrink-0 w-8 h-8 rounded-full bg-rose-100 text-rose-700 font-bold flex items-center justify-center">5</span>
                <div>
                    <h3 class="font-bold text-gray-900 mb-1">Değerlendirme</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">Ulusal vize değerlendirmesi Schengen vizesinden uzundur; <strong>haftalar, bazen aylar</strong> sürebilir. Bu yüzden kabul gelir gelmez başvur.</p>
                </div>
            </li>
        </ol>
    </section>

    {{-- iData'nın kapsamı --}}
    <section class="mt-10 bg-sky-50 border border-sky-100 rounded-xl p-6">
        <h2 class="text-xl font-bold text-sky-900 mb-3">iData ne zaman devreye girer?</h2>
        <p class="text-sm text-sky-900 leading-relaxed">Öğrenim ve öğrenim yeri arama dışındaki ulusal vizelerde (ör. aile birleşimi, çalışma) Türkiye'deki Alman temsilcilikleri harici hizmet sağlayıcılar <strong>iData ve IOM-FAP</strong> ile çalışır; başvurular ilgili büyükelçilik veya başkonsolosluğun vize bölümünde sonuçlandırılır (<a href="{{ $nationalUrl }}" target="_blank" rel="noopener" class="underline">kaynak</a>). Kendi vize türünün kanalını her zaman temsilciliğin sayfasından doğrula.</p>
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
                        <td class="py-2 pr-4 text-amber-900">Ulusal (Tip D) vize harcı <span class="text-amber-700">— AufenthV § 46 Abs. 2 Nr. 1; bazı gruplar için muafiyet veya indirim olabilir</span></td>
                        <td class="py-2 font-bold text-amber-900 text-right">{{ $nationalFee }}</td>
                    </tr>
                    <tr>
                        <td class="py-2 pr-4 text-amber-900">iData hizmet bedeli <span class="text-amber-700">— yalnız iData üzerinden yürüyen ulusal vizelerde; TL ile ödenir</span></td>
                        <td class="py-2 font-bold text-amber-900 text-right">{{ $idataFee }}</td>
                    </tr>
                    <tr>
                        <td class="py-2 pr-4 text-amber-900">iData randevu ücreti <span class="text-amber-700">— TL ile ödenir</span></td>
                        <td class="py-2 font-bold text-amber-900 text-right">{{ $idataBookingFee }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <p class="text-xs text-amber-800 mt-3">Öğrenim ve öğrenim yeri arama vizeleri portal üzerinden yürüdüğü için bu başvurularda iData bedeli ödenmez.</p>
        <x-source-note
            :sources="[
                ['name' => 'Türkiye\'deki Alman temsilcilikleri – öğrenim vizesi', 'url' => $studyUrl],
                ['name' => 'Türkiye\'deki Alman temsilcilikleri – ulusal vize ve iData', 'url' => $nationalUrl],
                ['name' => 'AufenthV § 46', 'url' => $feeLawUrl],
            ]"
            updated="2026-10-14"
            note="Ücretler ve kanallar değişebilir; başvurmadan önce temsilciliğin güncel sayfasını kontrol et."
            class="!bg-white/60 !border-amber-100"
        />
    </section>

    {{-- İPUÇLARI --}}
    <section class="mt-8 bg-emerald-50 border border-emerald-100 rounded-xl p-6">
        <h2 class="text-xl font-bold text-emerald-900 mb-3">🇹🇷 İpuçları</h2>
        <ul class="text-emerald-800 text-sm space-y-2 leading-relaxed">
            <li>• Kabul gelir gelmez portal başvurusunu başlat; ulusal vize değerlendirmesi uzun sürer.</li>
            <li>• Belgeleri <strong>eksiksiz ve düzenli</strong> yükle; eksik belge süreci uzatır.</li>
            <li>• <a href="{{ route('tools.blocked-account') }}" class="underline">Sperrkonto</a>, vizede maddi durum kanıtı (<strong>Finanzierungsnachweis</strong>) olarak resmi kabul görür (<a href="https://tuerkei.diplo.de/tr-de/service/05-visaeinreise/2621556-2621556" target="_blank" rel="noopener nofollow" class="underline">Almanya Dışişleri · Merkblatt</a>).</li>
            <li>• "Hızlı randevu" satan üçüncü taraflara itibar etme; öğrenim vizesi için resmî kanal Konsolosluk Hizmet Portalı'dır.</li>
        </ul>
    </section>

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
        Bu rehber bilgilendirme amaçlıdır ve 14.10.2026'da kontrol edilen resmî kaynaklara dayanır. Süreç ve ücretler değişebilir; başvurmadan önce Türkiye'deki Alman temsilciliklerinin güncel sayfasını kontrol et.
    </p>
</div>
@endsection
