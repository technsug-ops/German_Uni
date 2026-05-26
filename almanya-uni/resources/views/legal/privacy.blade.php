@extends('layouts.app')

@section('title', __('Privacy Policy') . '  — ' . brand('name'))

<x-seo
    :title="__('Privacy Policy') . '  — ' . brand('name')"
    :description="__('AlmanyaUni\'s personal data collection, use and protection policy. GDPR compliant.')" />

@section('content')

<section class="bg-gradient-to-br from-primary-700 to-primary-900 text-white">
    <div class="max-w-3xl mx-auto px-4 py-12 md:py-16">
        <p class="text-sm uppercase tracking-wide text-primary-200 mb-3">{{ __('Legal Notice') }}</p>
        <h1 class="text-3xl md:text-5xl font-extrabold leading-tight mb-3">{{ __('Privacy Policy') }}</h1>
        <p class="text-primary-100">{{ __('Last updated:') }} {{ $updated_at }}</p>
    </div>
</section>

<article class="max-w-3xl mx-auto px-4 py-12 prose prose-lg max-w-none
                prose-headings:font-extrabold prose-headings:text-gray-900
                prose-h2:text-2xl prose-h2:mt-10 prose-h2:mb-4
                prose-h3:text-lg prose-h3:mt-6 prose-h3:mb-3
                prose-p:text-gray-700 prose-p:leading-relaxed
                prose-li:text-gray-700
                prose-a:text-primary-700 prose-a:no-underline hover:prose-a:underline
                prose-strong:text-gray-900">

@if (app()->getLocale() === 'tr')
    {{-- ============== TÜRKÇE (KVKK + GDPR detaylı) ============== --}}

    <div class="bg-primary-50 border-l-4 border-primary-500 p-5 rounded-r-lg mb-8 not-prose">
        <p class="text-sm text-gray-700 leading-relaxed">
            <strong class="text-primary-900">Özet:</strong>
            AlmanyaUni; e-postanı sadece <strong>bültene aboneliğin için</strong> kullanır.
            Üçüncü taraflarla paylaşmaz. KVKK Madde 11 ve GDPR Madde 15-22 kapsamında her zaman silme, erişim veya iptal hakkın vardır.
            Sorular için: <a href="mailto:{{ $contact_email }}" class="text-primary-700 font-semibold">{{ $contact_email }}</a>
        </p>
    </div>

    <h2>1. Veri Sorumlusu</h2>
    <p>
        AlmanyaUni (bundan sonra <strong>"Platform"</strong>), Türk öğrencilerin Almanya'da eğitim yolculuğunda
        bilgilendirilmesi amacıyla kurulmuş <strong>kamu yararına</strong> bir bilgi platformudur.
        Platform sahibi ve veri sorumlusu kişisel iletişim: <a href="mailto:{{ $contact_email }}">{{ $contact_email }}</a>.
    </p>

    <h2>2. Toplanan Kişisel Veriler</h2>

    <h3>2.1 Aktif olarak verdiğin bilgiler</h3>
    <ul>
        <li><strong>E-posta adresin</strong> — bülten aboneliğinde, kullanıcı kaydında ve danışmanlık başvurusunda alınır</li>
        <li><strong>Adın</strong> (opsiyonel) — bülten aboneliği ve kullanıcı kaydında alınabilir</li>
        <li><strong>Eğitim hedeflerin</strong> (opsiyonel) — kullanıcı kaydı sırasında girilen lise türü, hedef bölüm, dil seviyesi gibi profil bilgileri</li>
        <li><strong>Forum içeriği</strong> — Forum'da gönderdiğin mesajların metni ve kullanıcı adın</li>
    </ul>

    <h3>2.2 Otomatik olarak toplanan bilgiler</h3>
    <ul>
        <li><strong>IP adresi</strong> — Form gönderiminde KVKK kayıt yükümlülüğü ve spam koruması için</li>
        <li><strong>Tarayıcı bilgisi (User-Agent)</strong> — Site uyumluluğu ve güvenlik için</li>
        <li><strong>Referrer URL</strong> — Hangi sayfadan gelindiğini anlamak için</li>
        <li><strong>Çerezler</strong> — Oturum yönetimi, dil tercihi ve giriş durumunu hatırlamak için. Detay: <a href="{{ route('legal.cookies') }}">Çerez Politikası</a></li>
    </ul>

    <h2>3. Veri Toplama Amaçları</h2>
    <p>Kişisel verilerin yalnızca aşağıdaki sınırlı amaçlar için işlenir:</p>
    <ul>
        <li><strong>Bülten gönderimi:</strong> Haftalık özet, başvuru deadline'ları, burs duyuruları</li>
        <li><strong>Kullanıcı kaydı + giriş:</strong> Hesap güvenliği, favorilerin saklanması, kişiselleştirilmiş üni önerileri</li>
        <li><strong>İletişim:</strong> Sorularına yanıt vermek (e-posta yazarsan)</li>
        <li><strong>Forum:</strong> Topluluk içeriği oluşturma ve moderasyon</li>
        <li><strong>İçerik geliştirme:</strong> Anonimleştirilmiş kullanım istatistikleri (hangi sayfa daha çok okunuyor, vs.)</li>
        <li><strong>Yasal yükümlülük:</strong> Türk veya AB mevzuatı gerektirirse arşivleme</li>
    </ul>

    <p>
        <strong>Hiçbir koşulda</strong> verilerin:
    </p>
    <ul>
        <li>❌ Üçüncü taraflarla ticari amaçla paylaşılmaz</li>
        <li>❌ Reklam şirketlerine satılmaz</li>
        <li>❌ Profil oluşturmak için davranışsal hedefleme yapılmaz</li>
        <li>❌ Sosyal medyada veya forumlarda izinsiz paylaşılmaz</li>
    </ul>

    <h2>4. Veri Saklama Süreleri</h2>
    <table class="not-prose w-full border-collapse mt-3 mb-6 text-sm">
        <thead>
            <tr class="bg-primary-50">
                <th class="border border-gray-200 px-4 py-2 text-left font-semibold">Veri Tipi</th>
                <th class="border border-gray-200 px-4 py-2 text-left font-semibold">Saklama Süresi</th>
            </tr>
        </thead>
        <tbody>
            <tr><td class="border border-gray-200 px-4 py-2">Bülten aboneliği (onaylanmış)</td><td class="border border-gray-200 px-4 py-2">Aktif olduğu süre boyunca</td></tr>
            <tr><td class="border border-gray-200 px-4 py-2">Doğrulanmamış abonelik (pending)</td><td class="border border-gray-200 px-4 py-2">30 gün, sonra otomatik silinir</td></tr>
            <tr><td class="border border-gray-200 px-4 py-2">İptal edilmiş abonelik</td><td class="border border-gray-200 px-4 py-2">2 yıl (yeniden abone olunursa kontrol için)</td></tr>
            <tr><td class="border border-gray-200 px-4 py-2">Kullanıcı hesabı</td><td class="border border-gray-200 px-4 py-2">Hesap aktif olduğu sürece</td></tr>
            <tr><td class="border border-gray-200 px-4 py-2">Silinmiş hesap kalıntıları</td><td class="border border-gray-200 px-4 py-2">90 gün backup'larda (sonra tamamen silinir)</td></tr>
            <tr><td class="border border-gray-200 px-4 py-2">Forum mesajları</td><td class="border border-gray-200 px-4 py-2">Süresiz (sen silebilirsin)</td></tr>
            <tr><td class="border border-gray-200 px-4 py-2">IP + User-Agent kayıtları</td><td class="border border-gray-200 px-4 py-2">12 ay</td></tr>
        </tbody>
    </table>

    <h2>5. Senin Haklarınla (KVKK Madde 11 + GDPR)</h2>
    <p>Aşağıdaki haklarına her zaman sahipsin:</p>
    <ol>
        <li><strong>Erişim hakkı:</strong> Hakkında saklanan tüm verileri talep etme</li>
        <li><strong>Düzeltme hakkı:</strong> Yanlış / eksik verileri güncelletme</li>
        <li><strong>Silme hakkı ("unutulma"):</strong> Tüm kişisel verilerin silinmesini talep etme</li>
        <li><strong>İtiraz hakkı:</strong> Veri işlenmesine karşı çıkma</li>
        <li><strong>Veri taşınabilirlik hakkı:</strong> Verilerinin makine-okunabilir formatta (JSON / CSV) sana iletilmesi</li>
        <li><strong>Onay geri çekme:</strong> Bülten aboneliği gibi onaya dayalı işlemleri istediğin zaman iptal etme</li>
        <li><strong>Otomatik karar verme'ye itiraz:</strong> Algoritmik üni önerisi gibi kararlara karşı çıkma</li>
    </ol>

    <p>
        Bu haklarını kullanmak için <a href="mailto:{{ $contact_email }}?subject=KVKK%20talep">{{ $contact_email }}</a>
        adresine yaz. <strong>30 gün içinde</strong> yanıt veririz.
    </p>

    <h2>6. Bültenden Çıkma (Unsubscribe)</h2>
    <p>
        Her gönderdiğimiz bülten e-postasının altında <strong>"Aboneliği iptal et"</strong> linki vardır.
        Tek tıklamayla anında çıkarsın, doğrulama gerekmez, neden sormak zorunda da değilsin.
    </p>
    <p>
        Alternatif olarak <a href="mailto:{{ $contact_email }}?subject=Bültenden%20çık">{{ $contact_email }}</a>
        adresine yazarak da çıkabilirsin.
    </p>

    <h2>7. Çerezler</h2>
    <p>
        AlmanyaUni minimal çerez kullanır. Detay için: <a href="{{ route('legal.cookies') }}">Çerez Politikası</a>.
    </p>
    <p>Temel olarak:</p>
    <ul>
        <li><strong>Gerekli çerezler</strong> (oturum, CSRF, dil tercihi) — Onayın gerekmez, site çalışsın diye zorunlu</li>
        <li><strong>İstatistik çerezleri</strong> (anonim sayfa görüntüleme) — Aktivleştirilirse onayın istenir</li>
        <li><strong>Reklam çerezleri</strong> (Google AdSense vb.) — Şu an aktif değil; aktive edilirse onayın açıkça istenir</li>
    </ul>

    <h2>8. Üçüncü Taraf Hizmetler</h2>
    <p>Platform'un bazı işlevleri için aşağıdaki dış hizmetleri kullanırız:</p>
    <ul>
        <li><strong>Gemini AI (Google):</strong> Bazı içerik çevirileri için, sadece anonim metin verisi gönderilir (kişisel bilgi değil)</li>
        <li><strong>Wikidata:</strong> Üniversite verisi kaynağımız (CC-0 lisanslı public veri)</li>
        <li><strong>DAAD (German Academic Exchange Service):</strong> Resmi program bilgisi kaynağı</li>
        <li><strong>Hosting:</strong> Sunucu sağlayıcı (Almanya veya AB ülkesinde, GDPR uyumlu)</li>
        <li><strong>E-posta sağlayıcı:</strong> Bülten gönderimi için (örn. Resend, Postmark — AB DPA imzalı)</li>
    </ul>

    <h2>9. Veri Güvenliği</h2>
    <ul>
        <li>Şifreler <strong>bcrypt</strong> ile hash'lenmiş şekilde saklanır</li>
        <li>İletişim <strong>HTTPS (TLS 1.2+)</strong> üzerinden şifrelenir</li>
        <li>Veri tabanı yedekleri şifreli ve erişim kısıtlı</li>
        <li>Admin paneli güçlü şifre + (gelecekte) 2FA ile korunur</li>
    </ul>

    <h2>10. Çocukların Verisi</h2>
    <p>
        AlmanyaUni özellikle çocuklara yönelik bir hizmet değildir. <strong>18 yaş altı</strong> kullanıcılar için
        kişisel veri toplamadan önce ebeveyn onayı alınmalıdır. Bu yaş altında bir kullanıcı kaydını fark edersek
        derhal silinir.
    </p>

    <h2>11. Politika Değişiklikleri</h2>
    <p>
        Bu politika güncellenebilir. Önemli değişiklikler:
    </p>
    <ul>
        <li>Bülten aboneliklerine bilgilendirme e-postası gönderilir</li>
        <li>Site içinde 30 gün boyunca bildirim banner'ı görünür</li>
        <li>"Son güncelleme" tarihi en üstte güncellenir</li>
    </ul>

    <h2>12. İletişim & Şikayet</h2>
    <p>
        Sorular, talepler, şikayetler için:
        <a href="mailto:{{ $contact_email }}" class="text-primary-700 font-semibold">{{ $contact_email }}</a>
    </p>
    <p>
        Eğer cevabımızdan memnun değilsen, Türkiye'de <strong>Kişisel Verileri Koruma Kurumu (KVKK)</strong>
        veya AB üyesi ülkelerde ilgili veri koruma otoritesine başvurabilirsin.
    </p>

    <p class="text-sm text-gray-500 mt-12 pt-6 border-t border-gray-200">
        Bu doküman {{ $updated_at }} tarihinde yayınlanmıştır. Bilgilendirme amaçlıdır; bağlayıcı yasal yorum için
        Türk veya AB veri koruma mevzuatına başvur.
    </p>

@else
    {{-- ============== ENGLISH (GDPR-style generic) ============== --}}

    <div class="bg-primary-50 border-l-4 border-primary-500 p-5 rounded-r-lg mb-8 not-prose">
        <p class="text-sm text-gray-700 leading-relaxed">
            <strong class="text-primary-900">Summary:</strong>
            AlmanyaUni only uses your email <strong>for your newsletter subscription</strong>.
            We do not share it with third parties. Under GDPR Articles 15-22 you always have the right to access, delete, or revoke consent.
            Questions: <a href="mailto:{{ $contact_email }}" class="text-primary-700 font-semibold">{{ $contact_email }}</a>
        </p>
    </div>

    <h2>1. Data Controller</h2>
    <p>
        AlmanyaUni (hereinafter <strong>"the Platform"</strong>) is a <strong>public-benefit</strong>
        information platform created to inform international students about studying in Germany.
        Data controller contact: <a href="mailto:{{ $contact_email }}">{{ $contact_email }}</a>.
    </p>

    <h2>2. Personal Data We Collect</h2>

    <h3>2.1 Information you actively provide</h3>
    <ul>
        <li><strong>Your email address</strong> — collected for newsletter subscription, user registration, and consulting requests</li>
        <li><strong>Your name</strong> (optional) — may be collected during newsletter subscription or user registration</li>
        <li><strong>Your study goals</strong> (optional) — profile information entered at registration such as high school type, target program, language level</li>
        <li><strong>Forum content</strong> — the text of messages you post in the Forum and your username</li>
    </ul>

    <h3>2.2 Automatically collected information</h3>
    <ul>
        <li><strong>IP address</strong> — for legal record keeping on form submissions and spam protection</li>
        <li><strong>Browser info (User-Agent)</strong> — for site compatibility and security</li>
        <li><strong>Referrer URL</strong> — to understand which page you came from</li>
        <li><strong>Cookies</strong> — for session management, language preference, and login state. Details: <a href="{{ route('legal.cookies') }}">Cookie Policy</a></li>
    </ul>

    <h2>3. Purposes of Data Collection</h2>
    <p>Your personal data is processed only for the following limited purposes:</p>
    <ul>
        <li><strong>Newsletter:</strong> weekly digest, application deadlines, scholarship announcements</li>
        <li><strong>User registration & login:</strong> account security, saving your favourites, personalised university recommendations</li>
        <li><strong>Contact:</strong> responding to your questions (if you email us)</li>
        <li><strong>Forum:</strong> community content creation and moderation</li>
        <li><strong>Content improvement:</strong> anonymised usage statistics (which pages are read most, etc.)</li>
        <li><strong>Legal obligation:</strong> archiving where required by EU regulation</li>
    </ul>

    <p>
        <strong>Under no circumstances</strong> is your data:
    </p>
    <ul>
        <li>Shared with third parties for commercial purposes</li>
        <li>Sold to advertising companies</li>
        <li>Used for behavioural profiling</li>
        <li>Shared on social media or forums without permission</li>
    </ul>

    <h2>4. Data Retention Periods</h2>
    <table class="not-prose w-full border-collapse mt-3 mb-6 text-sm">
        <thead>
            <tr class="bg-primary-50">
                <th class="border border-gray-200 px-4 py-2 text-left font-semibold">Data Type</th>
                <th class="border border-gray-200 px-4 py-2 text-left font-semibold">Retention Period</th>
            </tr>
        </thead>
        <tbody>
            <tr><td class="border border-gray-200 px-4 py-2">Confirmed newsletter subscription</td><td class="border border-gray-200 px-4 py-2">As long as it remains active</td></tr>
            <tr><td class="border border-gray-200 px-4 py-2">Unverified subscription (pending)</td><td class="border border-gray-200 px-4 py-2">30 days, then auto-deleted</td></tr>
            <tr><td class="border border-gray-200 px-4 py-2">Cancelled subscription</td><td class="border border-gray-200 px-4 py-2">2 years (for resubscribe checks)</td></tr>
            <tr><td class="border border-gray-200 px-4 py-2">User account</td><td class="border border-gray-200 px-4 py-2">As long as the account is active</td></tr>
            <tr><td class="border border-gray-200 px-4 py-2">Deleted account residue</td><td class="border border-gray-200 px-4 py-2">90 days in backups (then fully erased)</td></tr>
            <tr><td class="border border-gray-200 px-4 py-2">Forum messages</td><td class="border border-gray-200 px-4 py-2">Indefinitely (you can delete them)</td></tr>
            <tr><td class="border border-gray-200 px-4 py-2">IP + User-Agent logs</td><td class="border border-gray-200 px-4 py-2">12 months</td></tr>
        </tbody>
    </table>

    <h2>5. Your Rights (GDPR Articles 15-22)</h2>
    <p>You always have the following rights:</p>
    <ol>
        <li><strong>Right of access:</strong> request all data stored about you</li>
        <li><strong>Right to rectification:</strong> have incorrect / incomplete data updated</li>
        <li><strong>Right to erasure ("right to be forgotten"):</strong> request deletion of all personal data</li>
        <li><strong>Right to object:</strong> object to processing</li>
        <li><strong>Right to data portability:</strong> receive your data in machine-readable form (JSON / CSV)</li>
        <li><strong>Withdraw consent:</strong> cancel consent-based processing such as newsletter subscription at any time</li>
        <li><strong>Object to automated decision-making:</strong> object to decisions such as algorithmic university recommendations</li>
    </ol>

    <p>
        To exercise these rights write to <a href="mailto:{{ $contact_email }}?subject=GDPR%20request">{{ $contact_email }}</a>.
        We will respond <strong>within 30 days</strong>.
    </p>

    <h2>6. Unsubscribing from the Newsletter</h2>
    <p>
        Every newsletter we send contains an <strong>"Unsubscribe"</strong> link at the bottom.
        One click is enough — no confirmation, no reason required.
    </p>
    <p>
        You can also write to <a href="mailto:{{ $contact_email }}?subject=Unsubscribe">{{ $contact_email }}</a> to opt out.
    </p>

    <h2>7. Cookies</h2>
    <p>
        AlmanyaUni uses minimal cookies. Details: <a href="{{ route('legal.cookies') }}">Cookie Policy</a>.
    </p>
    <p>In essence:</p>
    <ul>
        <li><strong>Essential cookies</strong> (session, CSRF, language preference) — no consent needed, required for the site to work</li>
        <li><strong>Statistics cookies</strong> (anonymous page views) — consent requested when enabled</li>
        <li><strong>Advertising cookies</strong> (e.g. Google AdSense) — not active at the moment; explicit consent will be requested if enabled</li>
    </ul>

    <h2>8. Third-Party Services</h2>
    <p>We use the following external services for some of the Platform's functionality:</p>
    <ul>
        <li><strong>Gemini AI (Google):</strong> for some content translations; only anonymous text data is sent (no personal information)</li>
        <li><strong>Wikidata:</strong> our university data source (CC-0 licensed public data)</li>
        <li><strong>DAAD (German Academic Exchange Service):</strong> official program information source</li>
        <li><strong>Hosting:</strong> server provider (located in Germany or another EU country, GDPR-compliant)</li>
        <li><strong>Email provider:</strong> for newsletter delivery (e.g. Resend, Postmark — EU DPA signed)</li>
    </ul>

    <h2>9. Data Security</h2>
    <ul>
        <li>Passwords are stored hashed using <strong>bcrypt</strong></li>
        <li>Communication is encrypted via <strong>HTTPS (TLS 1.2+)</strong></li>
        <li>Database backups are encrypted and access-restricted</li>
        <li>The admin panel is protected with a strong password and (in the future) 2FA</li>
    </ul>

    <h2>10. Children's Data</h2>
    <p>
        AlmanyaUni is not specifically targeted at children. For users <strong>under 18</strong>,
        parental consent must be obtained before personal data is collected.
        If we identify a registration below this age, it is deleted immediately.
    </p>

    <h2>11. Changes to This Policy</h2>
    <p>
        This policy may be updated. For important changes:
    </p>
    <ul>
        <li>An informational email is sent to newsletter subscribers</li>
        <li>A notification banner appears on the site for 30 days</li>
        <li>The "Last updated" date at the top is refreshed</li>
    </ul>

    <h2>12. Contact & Complaints</h2>
    <p>
        For questions, requests or complaints:
        <a href="mailto:{{ $contact_email }}" class="text-primary-700 font-semibold">{{ $contact_email }}</a>
    </p>
    <p>
        If you are not satisfied with our response, you can contact your relevant data protection authority in the EU country where you reside.
    </p>

    <p class="text-sm text-gray-500 mt-12 pt-6 border-t border-gray-200">
        This document was published on {{ $updated_at }}. It is for informational purposes; for binding legal interpretation,
        please refer to applicable EU data protection law.
    </p>
@endif

</article>

@endsection
