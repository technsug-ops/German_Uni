<?php

namespace Database\Seeders;

use App\Models\LegalPage;
use Illuminate\Database\Seeder;

class LegalPagesSeeder extends Seeder
{
    /**
     * Seed legal pages with initial content adapted from German legal templates
     * (DSGVO/GDPR + TMG + MStV) + TechNS UG operator info. All content should be
     * reviewed by a lawyer before relying on it for compliance.
     *
     * Idempotent: uses updateOrCreate so re-running won't duplicate.
     */
    public function run(): void
    {
        $pages = [
            $this->impressum(),
            $this->privacy(),
            $this->disclaimer(),
            $this->terms(),
            $this->cookies(),
        ];

        foreach ($pages as $i => $page) {
            LegalPage::updateOrCreate(
                ['key' => $page['key']],
                array_merge($page, ['sort_order' => $i, 'is_published' => true, 'effective_date' => '2026-05-27'])
            );
        }
    }

    private function impressum(): array
    {
        $body = <<<'MD'
## Operatör (§ 5 TMG)

**TechNS UG (haftungsbeschränkt)**
Ludwig-Erhard-Str. 16A
61440 Oberursel (Taunus)
Deutschland

## Yönetim

**Geschäftsführer:** Halil Yaprakli

## İletişim

- **Telefon:** +49 6171 277 51 37
- **E-posta:** admin@applytogerman.com
- **Web:** https://almanyauni.com · https://applytogerman.com

## Ticaret Sicili

- **Sicil mahkemesi:** Amtsgericht Bad Homburg
- **Sicil numarası:** HRB 16236

## Vergi Bilgileri

- **USt-IdNr (§ 27 a UStG):** DE312006599
- **Steuernummer:** 003 246 12171

## İçerikten Sorumlu (§ 18 Abs. 2 MStV)

Halil Yaprakli, Ludwig-Erhard-Str. 16A, 61440 Oberursel (Taunus), Deutschland

## AB Çevrimiçi Uyuşmazlık Çözümü

Avrupa Komisyonu çevrimiçi uyuşmazlık çözümü platformu sağlar: [ec.europa.eu/consumers/odr](https://ec.europa.eu/consumers/odr). Tüketici hakem heyeti önünde uyuşmazlık çözümüne katılma zorunluluğumuz veya isteğimiz bulunmamaktadır.

## İçerik Sorumluluğu

Hizmet sağlayıcı olarak, § 7 Abs. 1 TMG uyarınca bu sayfalardaki kendi içeriğimizden genel yasalara göre sorumluyuz. §§ 8-10 TMG uyarınca, iletilen veya saklanan üçüncü taraf bilgilerini izleme veya yasadışı faaliyete işaret eden koşulları araştırma yükümlülüğümüz yoktur.

## Telif Hakkı

Site operatörleri tarafından oluşturulan içerikler Alman telif hakkı yasasına tabidir. Çoğaltma, işleme, dağıtma ve telif hakkı sınırları dışındaki her türlü kullanım, ilgili yazar veya yaratıcının yazılı izniyle gerçekleştirilir.
MD;

        $bodyEn = str_replace([
            'Operatör (§ 5 TMG)', 'Yönetim', 'İletişim', 'Ticaret Sicili', 'Vergi Bilgileri',
            'İçerikten Sorumlu', 'AB Çevrimiçi Uyuşmazlık Çözümü', 'İçerik Sorumluluğu', 'Telif Hakkı',
            'Telefon:', 'E-posta:', 'Web:', 'Sicil mahkemesi:', 'Sicil numarası:',
            'Geschäftsführer:', 'Hizmet sağlayıcı olarak, § 7 Abs. 1 TMG uyarınca bu sayfalardaki kendi içeriğimizden genel yasalara göre sorumluyuz. §§ 8-10 TMG uyarınca, iletilen veya saklanan üçüncü taraf bilgilerini izleme veya yasadışı faaliyete işaret eden koşulları araştırma yükümlülüğümüz yoktur.',
            'Avrupa Komisyonu çevrimiçi uyuşmazlık çözümü platformu sağlar:', 'Tüketici hakem heyeti önünde uyuşmazlık çözümüne katılma zorunluluğumuz veya isteğimiz bulunmamaktadır.',
            'Site operatörleri tarafından oluşturulan içerikler Alman telif hakkı yasasına tabidir. Çoğaltma, işleme, dağıtma ve telif hakkı sınırları dışındaki her türlü kullanım, ilgili yazar veya yaratıcının yazılı izniyle gerçekleştirilir.',
        ], [
            'Operator (§ 5 TMG)', 'Management', 'Contact', 'Commercial Register', 'Tax Information',
            'Responsible for content', 'EU Online Dispute Resolution', 'Liability for content', 'Copyright',
            'Phone:', 'Email:', 'Web:', 'Registry court:', 'Registration number:',
            'Managing Director:',
            'As a service provider, we are responsible for our own content on these pages pursuant to § 7 (1) TMG. Pursuant to §§ 8-10 TMG, we are not obliged to monitor transmitted or stored third-party information or investigate circumstances indicating illegal activity.',
            'The European Commission provides a platform for online dispute resolution:', 'We are neither obliged nor willing to participate in dispute resolution proceedings before a consumer arbitration board.',
            'Content created by the operator is subject to German copyright law. Duplication, processing, distribution and any kind of exploitation outside the limits of copyright require the written consent of the respective author or creator.',
        ], $body);

        $bodyDe = str_replace([
            'Operatör (§ 5 TMG)', 'Yönetim', 'İletişim', 'Ticaret Sicili', 'Vergi Bilgileri',
            'İçerikten Sorumlu', 'AB Çevrimiçi Uyuşmazlık Çözümü', 'İçerik Sorumluluğu', 'Telif Hakkı',
            'Telefon:', 'E-posta:', 'Web:', 'Sicil mahkemesi:', 'Sicil numarası:',
            'Geschäftsführer:',
            'Hizmet sağlayıcı olarak, § 7 Abs. 1 TMG uyarınca bu sayfalardaki kendi içeriğimizden genel yasalara göre sorumluyuz. §§ 8-10 TMG uyarınca, iletilen veya saklanan üçüncü taraf bilgilerini izleme veya yasadışı faaliyete işaret eden koşulları araştırma yükümlülüğümüz yoktur.',
            'Avrupa Komisyonu çevrimiçi uyuşmazlık çözümü platformu sağlar:', 'Tüketici hakem heyeti önünde uyuşmazlık çözümüne katılma zorunluluğumuz veya isteğimiz bulunmamaktadır.',
            'Site operatörleri tarafından oluşturulan içerikler Alman telif hakkı yasasına tabidir. Çoğaltma, işleme, dağıtma ve telif hakkı sınırları dışındaki her türlü kullanım, ilgili yazar veya yaratıcının yazılı izniyle gerçekleştirilir.',
        ], [
            'Betreiber (§ 5 TMG)', 'Geschäftsführung', 'Kontakt', 'Handelsregister', 'Steuerinformationen',
            'Verantwortlich für den Inhalt', 'EU-Streitbeilegung', 'Haftung für Inhalte', 'Urheberrecht',
            'Telefon:', 'E-Mail:', 'Web:', 'Registergericht:', 'Registernummer:',
            'Geschäftsführer:',
            'Als Diensteanbieter sind wir gemäß § 7 Abs. 1 TMG für eigene Inhalte auf diesen Seiten nach den allgemeinen Gesetzen verantwortlich. Nach §§ 8-10 TMG sind wir jedoch nicht verpflichtet, übermittelte oder gespeicherte fremde Informationen zu überwachen oder nach Umständen zu forschen, die auf eine rechtswidrige Tätigkeit hinweisen.',
            'Die Europäische Kommission stellt eine Plattform zur Online-Streitbeilegung bereit:', 'Wir sind nicht verpflichtet und nicht bereit, an einem Streitbeilegungsverfahren vor einer Verbraucherschlichtungsstelle teilzunehmen.',
            'Die durch die Seitenbetreiber erstellten Inhalte unterliegen dem deutschen Urheberrecht. Vervielfältigung, Bearbeitung, Verbreitung und jede Art der Verwertung außerhalb der Grenzen des Urheberrechtes bedürfen der schriftlichen Zustimmung des jeweiligen Autors bzw. Erstellers.',
        ], $body);

        return [
            'key' => 'impressum',
            'titles' => ['tr' => 'Künye', 'en' => 'Imprint', 'de' => 'Impressum'],
            'descriptions' => [
                'tr' => '§ 5 TMG ve § 18 MStV uyarınca yasal bildirim — TechNS UG operatör, adres, iletişim, ticaret sicili ve vergi bilgileri.',
                'en' => 'Legal disclosure under § 5 TMG and § 18 MStV — TechNS UG operator info, address, contact, register, tax data.',
                'de' => 'Rechtliche Angaben gemäß § 5 TMG und § 18 MStV — Betreiber, Anschrift, Kontakt, Handelsregister, Steuerinformationen der TechNS UG.',
            ],
            'bodies' => ['tr' => $body, 'en' => $bodyEn, 'de' => $bodyDe],
        ];
    }

    private function privacy(): array
    {
        // Privacy policy (DSGVO/GDPR + KVKK compliant template). Multi-locale.
        $tr = <<<'MD'
## 1. Veri Sorumlusu (Verantwortliche Stelle)

GDPR Madde 4(7) ve KVKK Madde 3 uyarınca veri sorumlusu:

**TechNS UG (haftungsbeschränkt)**
Ludwig-Erhard-Str. 16A, 61440 Oberursel (Taunus), Deutschland
E-posta: admin@applytogerman.com — Tel: +49 6171 277 51 37

Veri koruma görevlimiz (Datenschutzbeauftragter) henüz atanmamıştır; sorularınızı doğrudan yukarıdaki adrese yöneltebilirsiniz.

## 2. Toplanan Kişisel Veriler

| Kategori | Veri | Toplama Anı |
|---|---|---|
| **Erişim verileri** | IP (hashed), tarayıcı, referrer, sayfa yolu | Her sayfa ziyareti |
| **Hesap verileri** | Ad, e-posta, şifre (bcrypt) | Üyelik açılışı |
| **Newsletter** | E-posta, ad (opsiyonel) | Abone olma |
| **Yorum verileri** | İsim, e-posta, üniversite yorumu | Yorum gönderme |
| **Çerez verileri** | Oturum, dil, consent durumu | Tarayıcı çerezleri |

## 3. İşleme Amaçları ve Hukuki Dayanak

- **Hizmetin sağlanması** (üniversite arama, karşılaştırma) — GDPR 6(1)(b) sözleşmenin ifası
- **Güvenlik ve abuse önleme** — GDPR 6(1)(f) meşru menfaat (botların engellenmesi, log tutma)
- **Newsletter gönderimi** — GDPR 6(1)(a) açık rıza (çift opt-in)
- **İstatistik (analitik)** — GDPR 6(1)(f) meşru menfaat (self-hosted, sadece anonim sayım, çerez consent'le)
- **Hukuki yükümlülükler** — GDPR 6(1)(c) (örn. vergi belgeleri saklama)

## 4. Saklama Süreleri

- **Erişim logları** — 90 gün sonra anonimleştirme
- **Hesap verileri** — Hesap aktif olduğu sürece + silme talebine kadar
- **Newsletter aboneliği** — Abonelik iptaline kadar
- **Yorumlar** — Yayında kaldığı sürece (yazarın silme hakkı saklı)
- **Yasal zorunluluk** (vergi vb.) — 10 yıl (HGB § 257, AO § 147)

## 5. Veri Aktarımı (Üçüncü Taraflar)

Kişisel verilerinizi yalnızca aşağıdaki kategorilerde paylaşırız:

- **Sunucu sağlayıcı:** All-Inkl (KASSERVER.COM), AB içinde Almanya
- **E-posta gönderimi:** Brevo (eski Sendinblue), AB içinde, DSGVO-uyumlu
- **CDN / yazı tipi:** Yok (self-hosted Inter font, Cloudflare yok)
- **Analitik:** Self-hosted (Google Analytics yok)

ABD'ye veri aktarımı **yapılmaz**.

## 6. Çerezler

Detaylar için [Çerez Politikası](/cookie-policy) sayfamızı inceleyin. Kısaca:

- **Zorunlu:** Oturum çerezi (CSRF, login), dil tercihi
- **Analitik:** Anonim ziyaretçi sayımı (sadece consent verildiyse)
- **Reklam:** Yok

## 7. Haklarınız (GDPR Madde 15-22 + KVKK Madde 11)

- **Erişim** — Hangi verilerinizi sakladığımızı öğrenme
- **Düzeltme** — Yanlış verileri düzelttirme
- **Silme** ("unutulma hakkı") — Hesabınız ve tüm verilerinizi sildirme
- **İşlemeyi kısıtlama** — Belirli işlemlerin durdurulması
- **Veri taşınabilirlik** — Verilerinizi makine-okunabilir formatta alma
- **İtiraz hakkı** — Meşru menfaate dayalı işlemeye itiraz
- **Şikayet hakkı** — Hessischer Datenschutzbeauftragter (HBDI) veya KVKK Kurumu'na şikayet

Talepleriniz için: **admin@applytogerman.com**

## 8. SSL/TLS Şifreleme

Tüm bağlantılar **HTTPS (TLS 1.2+)** ile şifrelidir. URL'in başındaki 🔒 simgesi şifreli iletişimi gösterir.

## 9. Otomatik Karar Verme

GDPR Madde 22 anlamında otomatik karar verme veya profilleme yapılmaz.

## 10. Politika Değişiklikleri

Bu politikayı yasal değişikliklerde veya hizmet güncellemelerinde değiştirebiliriz. Önemli değişiklikleri e-posta veya site banner'ı ile bildiririz. Geçerlilik tarihi yukarıdadır.
MD;

        $en = <<<'MD'
## 1. Data Controller (Verantwortliche Stelle)

In the sense of Art. 4(7) GDPR and Turkey's KVKK Art. 3, the data controller is:

**TechNS UG (haftungsbeschränkt)**
Ludwig-Erhard-Str. 16A, 61440 Oberursel (Taunus), Germany
Email: admin@applytogerman.com — Phone: +49 6171 277 51 37

We have not appointed a Data Protection Officer (Datenschutzbeauftragter); inquiries may be directed to the address above.

## 2. Personal Data Collected

| Category | Data | When |
|---|---|---|
| **Access data** | IP (hashed), browser, referrer, path | Every page view |
| **Account data** | Name, email, password (bcrypt) | On registration |
| **Newsletter** | Email, name (optional) | On subscription |
| **Reviews** | Name, email, university review | On submission |
| **Cookies** | Session, language, consent state | Browser cookies |

## 3. Purposes & Legal Basis

- **Service provision** (university discovery, comparison) — Art. 6(1)(b) GDPR, contract performance
- **Security & abuse prevention** — Art. 6(1)(f) GDPR, legitimate interest
- **Newsletter** — Art. 6(1)(a) GDPR, explicit consent (double opt-in)
- **Analytics** — Art. 6(1)(f) GDPR (self-hosted, anonymized; consent for non-essential)
- **Legal obligations** — Art. 6(1)(c) GDPR (e.g. tax record retention)

## 4. Retention Periods

- **Access logs** — Anonymized after 90 days
- **Account data** — Until deletion request
- **Newsletter** — Until unsubscribe
- **Reviews** — Until removed by author or moderator
- **Legal obligations** (tax) — 10 years (HGB § 257, AO § 147)

## 5. Third-Party Data Transfer

We share data only with:

- **Hosting:** All-Inkl (KASSERVER.COM), EU/Germany
- **Email delivery:** Brevo (formerly Sendinblue), EU, GDPR-compliant
- **CDN/fonts:** None (self-hosted Inter font, no Cloudflare)
- **Analytics:** Self-hosted (no Google Analytics)

**No transfers to the USA.**

## 6. Cookies

See our [Cookie Policy](/en/cookie-policy). Summary:

- **Essential:** Session (CSRF, login), language preference
- **Analytics:** Anonymous visitor counts (only with consent)
- **Advertising:** None

## 7. Your Rights (GDPR Art. 15-22 / KVKK Art. 11)

- **Access** — Know what data we store about you
- **Rectification** — Correct inaccurate data
- **Erasure** ("right to be forgotten") — Delete your account and all data
- **Restriction** — Stop specific processing
- **Portability** — Receive your data in a machine-readable format
- **Objection** — Object to processing based on legitimate interest
- **Complaint** — File with the Hessian DPA (HBDI) or KVKK Authority

Contact: **admin@applytogerman.com**

## 8. SSL/TLS Encryption

All connections are encrypted with **HTTPS (TLS 1.2+)**. The 🔒 icon in your URL bar confirms encrypted communication.

## 9. Automated Decision-Making

No automated decision-making or profiling per Art. 22 GDPR is performed.

## 10. Changes to this Policy

We may update this policy due to legal changes or service updates. Material changes will be notified via email or a site banner. The effective date is shown above.
MD;

        $de = <<<'MD'
## 1. Verantwortliche Stelle

Verantwortlich gemäß Art. 4(7) DSGVO:

**TechNS UG (haftungsbeschränkt)**
Ludwig-Erhard-Str. 16A, 61440 Oberursel (Taunus), Deutschland
E-Mail: admin@applytogerman.com — Telefon: +49 6171 277 51 37

Ein Datenschutzbeauftragter wurde nicht bestellt; Anfragen richten Sie bitte direkt an die obige Adresse.

## 2. Erhobene personenbezogene Daten

| Kategorie | Daten | Zeitpunkt |
|---|---|---|
| **Zugriffsdaten** | IP (gehasht), Browser, Referrer, Pfad | Bei jedem Seitenaufruf |
| **Kontodaten** | Name, E-Mail, Passwort (bcrypt) | Bei Registrierung |
| **Newsletter** | E-Mail, Name (optional) | Bei Anmeldung |
| **Bewertungen** | Name, E-Mail, Bewertungstext | Bei Abgabe |
| **Cookies** | Session, Sprache, Einwilligungsstatus | Browser-Cookies |

## 3. Zwecke & Rechtsgrundlagen

- **Bereitstellung des Dienstes** (Suche, Vergleich) — Art. 6(1)(b) DSGVO Vertragserfüllung
- **Sicherheit & Missbrauchsabwehr** — Art. 6(1)(f) DSGVO berechtigtes Interesse
- **Newsletter** — Art. 6(1)(a) DSGVO ausdrückliche Einwilligung (Double-Opt-In)
- **Statistik** — Art. 6(1)(f) DSGVO (self-hosted, anonymisiert, Einwilligung für nicht-wesentliche)
- **Rechtliche Pflichten** — Art. 6(1)(c) DSGVO (z. B. Steuer-Aufbewahrung)

## 4. Speicherdauern

- **Zugriffsprotokolle** — Nach 90 Tagen anonymisiert
- **Kontodaten** — Bis zur Löschanfrage
- **Newsletter** — Bis zur Abmeldung
- **Bewertungen** — Bis zur Entfernung durch Autor oder Moderator
- **Gesetzliche Pflichten** (Steuer) — 10 Jahre (HGB § 257, AO § 147)

## 5. Datenweitergabe an Dritte

Daten geben wir nur an folgende Kategorien weiter:

- **Hosting:** All-Inkl (KASSERVER.COM), EU/Deutschland
- **E-Mail-Versand:** Brevo (vormals Sendinblue), EU, DSGVO-konform
- **CDN/Fonts:** Keine (self-hosted Inter, kein Cloudflare)
- **Analytik:** Self-hosted (kein Google Analytics)

**Keine Übermittlung in die USA.**

## 6. Cookies

Details siehe unsere [Cookie-Richtlinie](/de/cookie-policy). Kurz:

- **Notwendig:** Session (CSRF, Login), Sprachwahl
- **Statistik:** Anonyme Besucherzählung (nur mit Einwilligung)
- **Werbung:** Keine

## 7. Ihre Rechte (Art. 15-22 DSGVO)

- **Auskunft** — Welche Daten wir über Sie speichern
- **Berichtigung** — Korrektur falscher Daten
- **Löschung** ("Recht auf Vergessen") — Ihres Kontos und aller Daten
- **Einschränkung** — Stoppen bestimmter Verarbeitungen
- **Datenübertragbarkeit** — Erhalt Ihrer Daten in maschinenlesbarem Format
- **Widerspruch** — Gegen Verarbeitung auf Grundlage berechtigten Interesses
- **Beschwerderecht** — Beim Hessischen Beauftragten für Datenschutz (HBDI)

Kontakt: **admin@applytogerman.com**

## 8. SSL/TLS-Verschlüsselung

Alle Verbindungen sind mit **HTTPS (TLS 1.2+)** verschlüsselt. Das 🔒-Symbol in der URL-Zeile bestätigt die verschlüsselte Kommunikation.

## 9. Automatisierte Entscheidungsfindung

Eine automatisierte Entscheidungsfindung oder Profiling im Sinne von Art. 22 DSGVO findet nicht statt.

## 10. Änderungen dieser Richtlinie

Diese Richtlinie kann aufgrund gesetzlicher Änderungen oder Dienstaktualisierungen angepasst werden. Wesentliche Änderungen werden per E-Mail oder Banner mitgeteilt. Das Datum der letzten Aktualisierung steht oben.
MD;

        return [
            'key' => 'privacy',
            'titles' => ['tr' => 'Gizlilik Politikası', 'en' => 'Privacy Policy', 'de' => 'Datenschutzerklärung'],
            'descriptions' => [
                'tr' => 'GDPR + KVKK uyumlu kişisel veri toplama, kullanım ve koruma politikası — veri sorumlusu, haklar, saklama süreleri.',
                'en' => 'GDPR + KVKK compliant data collection, use and protection policy — controller, your rights, retention periods.',
                'de' => 'DSGVO-konforme Datenschutzerklärung — Verantwortliche, Ihre Rechte, Speicherdauern, Drittanbieter.',
            ],
            'bodies' => ['tr' => $tr, 'en' => $en, 'de' => $de],
        ];
    }

    private function disclaimer(): array
    {
        $tr = <<<'MD'
## Genel Bilgilendirme

Bu sayfada sunulan tüm bilgiler, **TechNS UG** tarafından genel bilgilendirme amacıyla derlenmiştir. Almanya'da yüksek öğrenim, vize, sigorta ve barınma konularındaki yasalar ve düzenlemeler **sıkça değişebilir**; içerik son düzenleme tarihinde doğru olsa da bireysel durumunuza tam uygun olmayabilir.

## Hukuki Tavsiye Niteliğinde Değildir

Site içeriği:

- **Yasal danışmanlık değildir** — Vize, oturum, çalışma izni gibi konularda göçmenlik avukatı veya yeminli danışmana başvurun.
- **Mali danışmanlık değildir** — Sperrkonto, vergi, sigorta için lisanslı danışmana danışın.
- **Tıbbi tavsiye değildir** — Sağlık sigortası, aşı gibi konularda doktor veya BMG kaynaklarına başvurun.
- **Akademik garanti içermez** — Üniversite kabul kriterleri yıldan yıla değişir; kesin bilgi için üniversitenin resmi sitesini kontrol edin.

## Veri Kaynakları ve Doğruluk

Verilerimizi şu kaynaklardan derliyoruz:

- **Wikidata, Wikipedia** — açık ve denetlenebilir
- **DAAD** — German Academic Exchange Service resmi API
- **Hochschulkompass.de** — sadece deep link (kazıma yok)
- **Üniversitelerin resmi siteleri** — periyodik manuel doğrulama
- **AI destekli içerik** (Google Gemini) — açıklamalar/özetler için, **insan onayı sonrası** yayımlanır

**Garanti vermemiz:** Sürekli güncel tutmaya çalışırız. **Vermediğimiz:** Yanlış veya eski bilgilerden doğacak kayıplar için sorumluluk üstlenmeyiz. Önemli kararlar (başvuru, vize, taşınma) öncesi **resmi kaynaktan teyit edin**.

## Üniversite Yorumları (UGC)

Site üzerinde yayınlanan **kullanıcı yorumları** kişisel deneyim ve görüşlerdir, **TechNS UG'nin görüşü değildir**. Yorumlar e-posta doğrulamasından geçer ve moderasyon sonrası yayımlanır; ancak yine de yazarın kişisel perspektifini yansıtır. Belirli bir üniversite veya programa dair önemli kararları sadece bu yorumlara dayanarak vermeyiniz.

## Affiliate ve Reklam

Bazı dış sitelere verdiğimiz linkler **affiliate** olabilir (örn. Sperrkonto sağlayıcıları). Bir kullanıcı bu link üzerinden kayıt olursa **komisyon kazanabiliriz**. Bu komisyon sıralamalarımızı **etkilemez**; sıralama sadece veri (fiyat, hız, özellik) bazlı yapılır.

## Harici Linkler (§§ 7-10 TMG)

Sitemizde üçüncü taraf sitelere bağlantılar bulunur. Bu sitelerin içeriğinden **biz sorumlu değiliz**. Link verdiğimiz anda içerikleri kontrol etmiş olmamıza rağmen, ilgili sitelerin sonradan yapacağı değişikliklerden sorumlu tutulamayız. Yasalara aykırı içerik fark ettiğinizde **lütfen bize bildirin**, kaldıracağız.

## Telif Hakları

Site içeriği (metinler, görseller, kod, tasarım) **Alman Telif Hakkı Yasası (UrhG)**'na tabidir. Kişisel olmayan kullanım yazılı izin gerektirir.

**İstisnalar (anlaşmalı kaynaklar):**
- Wikimedia/Wikipedia görselleri — CC BY-SA lisansı (atıf ile)
- DAAD verisi — kamuya açık API
- Üniversite logoları — Wikimedia Commons üzerinden, ilgili lisansla

**İhlal şüphesi varsa:** admin@applytogerman.com adresine yazın, hemen kaldıralım.

## Sorumluluk Sınırlandırması

TechNS UG, kasıt veya ağır ihmal dışında, sitenin **kesintisiz çalışması**, **veri kaybı**, **erişim engelleri** veya **bilgi yanlışlığından** doğan dolaylı zararlar için sorumluluk üstlenmez. Bu sınırlandırma, hayat-vücut-sağlık zararlarını ve Ürün Sorumluluk Yasası (ProdHaftG) kapsamındaki sorumlulukları **etkilemez**.

## Yargı Yeri

Almanya yasaları geçerlidir. İhtilaf halinde yargı yeri Bad Homburg / Hessen'dir (tüketici hakları saklıdır).

## Şikayet ve Düzeltme

İçerikte yanlış, eksik veya güncel olmayan bilgi gördüğünüzde **admin@applytogerman.com**'a yazın. İncelenip 7 iş günü içinde dönüş yaparız.
MD;

        $en = <<<'MD'
## General Information

All information presented on this site is compiled by **TechNS UG** for general informational purposes. Laws and regulations regarding higher education, visas, insurance, and accommodation in Germany **change frequently**; content correct on the date of publication may not fit your individual situation.

## Not Legal Advice

Site content:

- **Is not legal counsel** — Consult a licensed immigration attorney for visa, residence, or work-permit matters.
- **Is not financial advice** — Consult a licensed advisor for Sperrkonto, taxes, or insurance.
- **Is not medical advice** — Consult a doctor or BMG resources for health insurance or vaccinations.
- **Is not an academic guarantee** — University admission criteria change yearly; verify on the institution's official site.

## Data Sources & Accuracy

We compile data from:

- **Wikidata, Wikipedia** — open and verifiable
- **DAAD** — German Academic Exchange Service official API
- **Hochschulkompass.de** — deep links only (no scraping)
- **University official sites** — periodic manual verification
- **AI-assisted content** (Google Gemini) — for descriptions/summaries, published **after human review**

**We commit:** continuous updates. **We do not commit:** liability for losses arising from inaccurate or outdated information. Verify major decisions (application, visa, relocation) with **official sources**.

## University Reviews (UGC)

Reviews published on the site reflect **personal experience and opinions**, not the view of TechNS UG. Reviews undergo email verification and moderation but still represent author perspective. Do not base critical decisions about a university or program solely on these reviews.

## Affiliate & Advertising

Some external links may be **affiliate** (e.g. Sperrkonto providers). If a user signs up via such a link, **we may earn commission**. This commission **does not influence** our rankings; rankings are based purely on data (price, speed, features).

## External Links (§§ 7-10 TMG)

Our site contains links to third-party websites. We are **not responsible** for their content. We check content at the time of linking, but cannot be held liable for subsequent changes by the linked site. **Please notify us** if you spot illegal content and we will remove the link.

## Copyright

Site content (text, images, code, design) is subject to **German Copyright Law (UrhG)**. Non-personal use requires written permission.

**Exceptions (licensed sources):**
- Wikimedia/Wikipedia images — CC BY-SA (with attribution)
- DAAD data — public API
- University logos — via Wikimedia Commons under respective licenses

**Suspected infringement:** Email admin@applytogerman.com and we will remove it promptly.

## Limitation of Liability

TechNS UG accepts no liability for indirect damages arising from **service interruption**, **data loss**, **access denial**, or **inaccurate information** except in cases of intent or gross negligence. This limitation does **not** affect damages to life-body-health or liability under the Product Liability Act (ProdHaftG).

## Jurisdiction

German law applies. Place of jurisdiction in case of dispute is Bad Homburg / Hesse (consumer rights reserved).

## Complaints & Corrections

If you spot inaccurate, incomplete, or outdated information, email **admin@applytogerman.com**. We review and respond within 7 business days.
MD;

        $de = <<<'MD'
## Allgemeine Hinweise

Alle auf dieser Seite bereitgestellten Informationen wurden von **TechNS UG** zu allgemeinen Informationszwecken zusammengestellt. Gesetze und Vorschriften zu Studium, Visum, Versicherung und Unterkunft in Deutschland **ändern sich häufig**; zum Zeitpunkt der Veröffentlichung korrekte Inhalte passen möglicherweise nicht zu Ihrer individuellen Situation.

## Keine Rechtsberatung

Die Inhalte sind:

- **Keine Rechtsberatung** — Konsultieren Sie für Visa-, Aufenthalts- und Arbeitsangelegenheiten einen zugelassenen Migrationsanwalt.
- **Keine Finanzberatung** — Für Sperrkonto, Steuern, Versicherungen konsultieren Sie einen lizenzierten Berater.
- **Keine medizinische Beratung** — Für Krankenversicherung oder Impfungen konsultieren Sie Ärzte oder BMG-Quellen.
- **Keine akademische Garantie** — Zulassungskriterien ändern sich jährlich; verifizieren Sie auf der Hochschul-Website.

## Datenquellen & Genauigkeit

Daten beziehen wir aus:

- **Wikidata, Wikipedia** — offen und überprüfbar
- **DAAD** — offizielle API
- **Hochschulkompass.de** — nur Deep-Links (kein Scraping)
- **Offizielle Hochschulseiten** — periodische manuelle Verifikation
- **KI-gestützte Inhalte** (Google Gemini) — für Beschreibungen, veröffentlicht **nach menschlicher Prüfung**

**Wir verpflichten uns** zur kontinuierlichen Aktualisierung. **Wir übernehmen keine** Haftung für Verluste aus ungenauen oder veralteten Informationen. Verifizieren Sie wichtige Entscheidungen mit **offiziellen Quellen**.

## Hochschul-Bewertungen (UGC)

Veröffentlichte Bewertungen geben persönliche Erfahrungen und Meinungen wieder, **nicht die Auffassung der TechNS UG**. Bewertungen durchlaufen eine E-Mail-Verifikation und Moderation, spiegeln aber dennoch die Sicht des Autors wider. Treffen Sie keine kritischen Entscheidungen ausschließlich auf Grundlage dieser Bewertungen.

## Affiliate & Werbung

Einige externe Links können **Affiliate-Links** sein (z. B. Sperrkonto-Anbieter). Bei einer Anmeldung über solche Links können wir **Provision erhalten**. Diese Provision **beeinflusst nicht** unsere Rankings; diese basieren ausschließlich auf Daten (Preis, Geschwindigkeit, Funktionen).

## Externe Links (§§ 7-10 TMG)

Unsere Seite enthält Links zu Websites Dritter. Für deren Inhalte sind wir **nicht verantwortlich**. Zum Zeitpunkt der Verlinkung haben wir Inhalte geprüft, können aber für nachträgliche Änderungen nicht haften. **Bitte teilen Sie uns mit**, wenn Sie rechtswidrige Inhalte bemerken — wir entfernen den Link.

## Urheberrecht

Inhalte (Texte, Bilder, Code, Design) unterliegen dem **Urheberrechtsgesetz (UrhG)**. Nicht-persönliche Nutzung erfordert schriftliche Genehmigung.

**Ausnahmen (lizenzierte Quellen):**
- Wikimedia/Wikipedia-Bilder — CC BY-SA (mit Nennung)
- DAAD-Daten — öffentliche API
- Hochschullogos — über Wikimedia Commons unter jeweiligen Lizenzen

**Bei Verdacht auf Verletzung:** E-Mail an admin@applytogerman.com — wir entfernen unverzüglich.

## Haftungsbeschränkung

Die TechNS UG übernimmt — außer bei Vorsatz oder grober Fahrlässigkeit — keine Haftung für indirekte Schäden durch **Betriebsunterbrechung**, **Datenverlust**, **Zugriffsbeschränkungen** oder **falsche Informationen**. Diese Beschränkung berührt **nicht** Schäden an Leben, Körper, Gesundheit oder die Haftung nach dem ProdHaftG.

## Gerichtsstand

Es gilt deutsches Recht. Gerichtsstand bei Streitigkeiten ist Bad Homburg / Hessen (Verbraucherrechte bleiben unberührt).

## Beschwerden & Korrekturen

Wenn Sie ungenaue, unvollständige oder veraltete Informationen bemerken, schreiben Sie an **admin@applytogerman.com**. Wir prüfen und antworten innerhalb von 7 Werktagen.
MD;

        return [
            'key' => 'disclaimer',
            'titles' => ['tr' => 'Yasal Uyarı', 'en' => 'Disclaimer', 'de' => 'Haftungsausschluss'],
            'descriptions' => [
                'tr' => 'TechNS UG yasal uyarı — içerik doğruluğu, harici linkler, affiliate, sorumluluk sınırı, telif hakkı.',
                'en' => 'TechNS UG disclaimer — content accuracy, external links, affiliate, liability limitation, copyright.',
                'de' => 'TechNS UG Haftungsausschluss — Inhaltsgenauigkeit, externe Links, Affiliate, Haftungsbeschränkung, Urheberrecht.',
            ],
            'bodies' => ['tr' => $tr, 'en' => $en, 'de' => $de],
        ];
    }

    private function terms(): array
    {
        $tr = <<<'MD'
## 1. Sözleşmenin Tarafları ve Konusu

Bu kullanım koşulları, **TechNS UG (haftungsbeschränkt)** ("Operatör") tarafından işletilen `almanyauni.com` ve `applytogerman.com` web sitelerinin ("Hizmet") kullanımı için sözleşmesel çerçeveyi belirler. Hizmeti kullanarak bu koşulları kabul etmiş sayılırsınız.

## 2. Hizmetin Tanımı

Hizmet, uluslararası öğrencilerin Almanya'da yüksek öğrenim arayışını desteklemeye yönelik:

- Üniversite, program, şehir veri tabanı
- Yaşam maliyeti, vize, not dönüştürücü gibi hesaplayıcılar
- Burs ve barınma rehberi
- Topluluk yorumları ve FAQ

Hizmet **ücretsizdir** (temel özellikler için). Bazı premium özellikler ileride ücretli sunulabilir.

## 3. Hesap Açma ve Sorumluluk

Bazı özellikler hesap gerektirir. Hesap açarken:

- **Doğru bilgi** vermelisiniz
- **Şifrenizi gizli tutmalısınız**
- **Yaşınız 16'dan büyük** olmalıdır (KVKK/DSGVO)
- Hesabınız üzerinden yapılan tüm işlemlerden **siz sorumlusunuz**

Hesabı askıya alma/silme hakkımız ihlal durumlarında saklıdır.

## 4. Kullanıcı İçeriği (Yorumlar, Geri Bildirim)

Yayınladığınız içerik için **TechNS UG'a münhasır olmayan, telifsiz, dünya çapında lisans verirsiniz** — site içinde sergileme, dağıtma, çevirme hakkı dahil. Kendi içeriğinizi her zaman silme hakkınız vardır.

İçerik gönderirken:

- **Telif/marka ihlali yok**
- **Yanlış/yanıltıcı bilgi yok** ("X üniversite çöp" gibi spam yorumlar silinir)
- **Kişisel saldırı, nefret söylemi yok**
- **Reklam/spam yok**

İhlal halinde içerik kaldırılır, hesabınız askıya alınabilir.

## 5. Yasaklı Kullanımlar

- Kazıma (scraping) — `robots.txt` ile düzenlenmiştir; AI bot'larına açığız ama otomatik veri yağması yasaktır.
- Sistem güvenliğini sınamak (penetration testing) — yetkisiz girişim suçtur.
- Sahte hesap, çoklu hesap manipülasyonu
- Reverse engineering, decompile

## 6. Fikri Mülkiyet

Site içeriği (kod, tasarım, derlenmiş veri tabanı, yazılı içerik) **TechNS UG'a aittir** veya lisanslıdır. Ticari kullanım yazılı izin gerektirir. CC BY-SA görseller (Wikimedia kaynakları) için orijinal lisanslar geçerlidir.

## 7. Affiliate ve Reklam Açıklaması

Bazı dış linkler komisyon sağlayabilir (Sperrkonto, sigorta vb.). Sıralamalarımız **veri bazlıdır**, komisyon etkilemez. Reklam blokları kullanıcıya ayırt edici görünür biçimde sunulur.

## 8. Hizmetin Değişimi/Sonlandırılması

Hizmeti veya bölümlerini önceden bildirimle veya bildirimsiz değiştirme/sonlandırma hakkımız saklıdır. Önemli değişiklikleri (örn. ücretli özelliğe geçiş) **önceden bildiririz**.

## 9. Sorumluluk Sınırlandırması

İçerik tavsiye niteliğinde değildir — detaylar için [Yasal Uyarı](/disclaimer) sayfamızı inceleyin. Kasıt veya ağır ihmal dışında, hizmet kesintisi veya bilgi yanlışlığından doğan **dolaylı zararlardan sorumlu değiliz**. Hayat-vücut-sağlık zararları ve ProdHaftG kapsamı saklıdır.

## 10. Uygulanacak Hukuk ve Yargı Yeri

Bu koşullar **Alman hukukuna** tabidir. Anlaşmazlıklarda yargı yeri **Bad Homburg / Hessen**'dir. Tüketici ise kendi ikametgah mahkemesinde de dava açabilir (AB tüketici hakları).

## 11. Bölünebilirlik

Bu koşulların bir maddesi geçersiz sayılırsa, diğer maddeler geçerli kalır.

## 12. İletişim

Sorularınız için: **admin@applytogerman.com**
MD;

        $en = <<<'MD'
## 1. Parties and Subject

These Terms of Use establish the contractual framework between **TechNS UG (haftungsbeschränkt)** ("Operator") and you regarding use of the websites `almanyauni.com` and `applytogerman.com` ("Service"). By using the Service you agree to these terms.

## 2. Description of the Service

The Service supports international students considering higher education in Germany via:

- University, program, and city database
- Calculators (cost of living, visa cost, grade conversion)
- Scholarship and accommodation guides
- Community reviews and FAQ

The Service is **free of charge** for core features. Some premium features may become paid in the future.

## 3. Account and Responsibility

Some features require an account. When registering:

- Provide **accurate information**
- Keep your **password confidential**
- Be at least **16 years old** (KVKK/GDPR)
- You are **responsible** for all actions on your account

We reserve the right to suspend/delete accounts upon violation.

## 4. User Content (Reviews, Feedback)

By posting content, you grant **TechNS UG a non-exclusive, royalty-free, worldwide license** to display, distribute, and translate it within the Service. You may remove your content at any time.

When posting:

- **No copyright/trademark infringement**
- **No false/misleading information** (spam reviews like "University X is trash" will be removed)
- **No personal attacks, hate speech**
- **No advertising/spam**

Violations lead to content removal and possible account suspension.

## 5. Prohibited Uses

- Scraping — regulated by `robots.txt`; AI crawlers are welcomed, but automated bulk data extraction is prohibited.
- Probing system security (penetration testing) — unauthorized attempts are criminal offenses.
- Fake accounts, multi-account manipulation
- Reverse engineering, decompiling

## 6. Intellectual Property

Site content (code, design, compiled database, written content) is **owned or licensed by TechNS UG**. Commercial use requires written permission. CC BY-SA images (Wikimedia sources) retain their original licenses.

## 7. Affiliate & Advertising Disclosure

Some external links may generate commission (Sperrkonto, insurance, etc.). Our rankings are **data-based**; commission does not influence them. Ad blocks are presented with clear visual distinction.

## 8. Service Changes/Termination

We reserve the right to modify or discontinue the Service with or without notice. Material changes (e.g. paid feature transitions) will be **announced in advance**.

## 9. Limitation of Liability

Content is informational, not advisory — see our [Disclaimer](/en/disclaimer). Except for intent or gross negligence, we are not liable for **indirect damages** from service interruptions or inaccurate information. Damages to life-body-health and ProdHaftG remain unaffected.

## 10. Governing Law and Jurisdiction

These terms are governed by **German law**. Place of jurisdiction is **Bad Homburg / Hesse**. Consumers may also file at their place of residence (EU consumer rights).

## 11. Severability

If a clause is held invalid, the remaining clauses remain in effect.

## 12. Contact

Questions: **admin@applytogerman.com**
MD;

        $de = <<<'MD'
## 1. Vertragsparteien und Vertragsgegenstand

Diese Nutzungsbedingungen regeln das Vertragsverhältnis zwischen der **TechNS UG (haftungsbeschränkt)** ("Betreiber") und Ihnen bezüglich der Nutzung der Websites `almanyauni.com` und `applytogerman.com` ("Dienst"). Mit der Nutzung des Dienstes akzeptieren Sie diese Bedingungen.

## 2. Beschreibung des Dienstes

Der Dienst unterstützt internationale Studieninteressierte für Deutschland mit:

- Hochschul-, Programm- und Städtedatenbank
- Rechner (Lebenshaltung, Visumkosten, Notenumrechnung)
- Stipendien- und Unterkunftsleitfäden
- Community-Bewertungen und FAQ

Der Dienst ist für Grundfunktionen **kostenlos**. Premium-Funktionen können künftig kostenpflichtig werden.

## 3. Konto und Verantwortung

Manche Funktionen erfordern ein Konto. Bei der Registrierung:

- **Wahrheitsgemäße Angaben**
- **Passwort geheim halten**
- Mindestalter **16 Jahre** (KVKK/DSGVO)
- Sie sind für alle Aktivitäten Ihres Kontos **verantwortlich**

Bei Verstößen behalten wir uns Sperrung/Löschung vor.

## 4. Nutzerinhalte (Bewertungen, Feedback)

Mit dem Posten gewähren Sie der **TechNS UG eine nicht-exklusive, weltweite, unentgeltliche Lizenz** zur Anzeige, Verbreitung und Übersetzung innerhalb des Dienstes. Eigene Inhalte können Sie jederzeit löschen.

Beim Posten:

- **Keine Urheber-/Markenrechtsverletzung**
- **Keine falschen/irreführenden Angaben** (Spam-Bewertungen wie "Uni X ist Müll" werden entfernt)
- **Keine persönlichen Angriffe, Hassrede**
- **Keine Werbung/Spam**

Verstöße führen zur Entfernung und ggf. Kontosperre.

## 5. Verbotene Nutzungen

- Scraping — geregelt in `robots.txt`; KI-Crawler willkommen, aber automatisierte Massendatenextraktion verboten.
- Sicherheitstests (Penetration Testing) — unerlaubte Versuche sind Straftaten.
- Fake-Konten, Multi-Account-Manipulation
- Reverse Engineering, Decompilierung

## 6. Geistiges Eigentum

Site-Inhalte (Code, Design, zusammengestellte Datenbank, redaktionelle Inhalte) gehören **TechNS UG** oder sind lizenziert. Kommerzielle Nutzung bedarf schriftlicher Genehmigung. CC BY-SA-Bilder (Wikimedia) unter ursprünglichen Lizenzen.

## 7. Affiliate- und Werbe-Hinweis

Manche externen Links können Provision erzeugen (Sperrkonto, Versicherung). Unsere Rankings sind **datenbasiert**; Provision beeinflusst sie nicht. Werbeblöcke sind klar gekennzeichnet.

## 8. Änderungen/Beendigung des Dienstes

Wir behalten uns vor, den Dienst mit oder ohne Ankündigung zu ändern oder einzustellen. Wesentliche Änderungen (z. B. kostenpflichtige Features) **kündigen wir vorab an**.

## 9. Haftungsbeschränkung

Inhalte sind informativ, keine Beratung — siehe [Haftungsausschluss](/de/disclaimer). Außer bei Vorsatz oder grober Fahrlässigkeit haften wir nicht für **indirekte Schäden** durch Betriebsunterbrechungen oder ungenaue Informationen. Schäden an Leben, Körper, Gesundheit und ProdHaftG bleiben unberührt.

## 10. Anwendbares Recht und Gerichtsstand

Es gilt **deutsches Recht**. Gerichtsstand ist **Bad Homburg / Hessen**. Verbraucher können auch an ihrem Wohnsitz klagen (EU-Verbraucherrechte).

## 11. Salvatorische Klausel

Unwirksame Bestimmungen berühren nicht die Wirksamkeit der übrigen Klauseln.

## 12. Kontakt

Fragen: **admin@applytogerman.com**
MD;

        return [
            'key' => 'terms',
            'titles' => ['tr' => 'Kullanım Koşulları', 'en' => 'Terms of Use', 'de' => 'Nutzungsbedingungen'],
            'descriptions' => [
                'tr' => 'AlmanyaUni / ApplyToGerman kullanım koşulları — TechNS UG operatör, hesap, kullanıcı içeriği, yasaklı kullanımlar, hukuk.',
                'en' => 'AlmanyaUni / ApplyToGerman terms of use — TechNS UG operator, accounts, user content, prohibited uses, law.',
                'de' => 'AlmanyaUni / ApplyToGerman Nutzungsbedingungen — TechNS UG, Konto, Nutzerinhalte, verbotene Nutzungen, Recht.',
            ],
            'bodies' => ['tr' => $tr, 'en' => $en, 'de' => $de],
        ];
    }

    private function cookies(): array
    {
        // Cookie policy stub — short; user can expand in admin.
        $tr = <<<'MD'
## Çerez Nedir?

Çerezler tarayıcınızda saklanan küçük metin dosyalarıdır. Hizmetlerin doğru çalışması ve istatistik için kullanılır.

## Hangi Çerezleri Kullanıyoruz?

- **Zorunlu çerezler:** Oturum, CSRF güvenliği, dil tercihi. Onay gerektirmez (GDPR 6(1)(f)).
- **Analitik çerezler:** Anonim ziyaretçi sayımı. **Sadece onayınızla** etkinleştirilir.
- **Pazarlama çerezleri:** Yok. Üçüncü taraf takip kullanmıyoruz.

## Kontrolünüz

Sayfa açılışında çerez banner'ı görürsünüz. **Kabul Et** veya **Reddet** seçeneklerinden birini kullanabilirsiniz. Tercihinizi istediğinizde değiştirebilirsiniz (tarayıcı çerezlerini silerek banner yeniden çıkar).

## Detaylı Liste

| İsim | Amaç | Süre | Tip |
|---|---|---|---|
| `XSRF-TOKEN` | CSRF güvenliği | Oturum | Zorunlu |
| `laravel_session` | Oturum yönetimi | 2 saat | Zorunlu |
| `almanyauni_uid` | Anonim ziyaretçi takip | 1 yıl | Analitik (onay sonrası) |
| `almanyauni_consent` | Onay durumu kaydı | 1 yıl | Zorunlu |

## Üçüncü Taraf

**Yok.** Google Analytics, Facebook Pixel veya benzer kullanmıyoruz. Self-hosted analitik tercih ediyoruz.

## İletişim

Sorularınız için: admin@applytogerman.com
MD;

        $en = str_replace([
            'Çerez Nedir?', 'Hangi Çerezleri Kullanıyoruz?', 'Kontrolünüz', 'Detaylı Liste', 'Üçüncü Taraf', 'İletişim',
            'Zorunlu çerezler:', 'Analitik çerezler:', 'Pazarlama çerezleri:',
            'Çerezler tarayıcınızda saklanan küçük metin dosyalarıdır. Hizmetlerin doğru çalışması ve istatistik için kullanılır.',
            'Sayfa açılışında çerez banner\'ı görürsünüz. **Kabul Et** veya **Reddet** seçeneklerinden birini kullanabilirsiniz. Tercihinizi istediğinizde değiştirebilirsiniz (tarayıcı çerezlerini silerek banner yeniden çıkar).',
            'İsim', 'Amaç', 'Süre', 'Tip', 'Oturum', 'CSRF güvenliği', 'Oturum yönetimi', '2 saat', 'Zorunlu', 'Analitik (onay sonrası)', 'Onay durumu kaydı', 'Anonim ziyaretçi takip', '1 yıl',
            '**Yok.** Google Analytics, Facebook Pixel veya benzer kullanmıyoruz. Self-hosted analitik tercih ediyoruz.',
            'Sorularınız için: admin@applytogerman.com',
        ], [
            'What is a cookie?', 'Which cookies do we use?', 'Your control', 'Detailed list', 'Third parties', 'Contact',
            'Essential cookies:', 'Analytics cookies:', 'Marketing cookies:',
            'Cookies are small text files stored in your browser, used to keep services working and gather statistics.',
            'On first visit you see a cookie banner. Use **Accept** or **Reject**. You can change your preference anytime by clearing cookies.',
            'Name', 'Purpose', 'Duration', 'Type', 'Session', 'CSRF protection', 'Session management', '2 hours', 'Essential', 'Analytics (after consent)', 'Consent record', 'Anonymous visitor tracking', '1 year',
            '**None.** We do not use Google Analytics, Facebook Pixel, or similar. We prefer self-hosted analytics.',
            'Questions: admin@applytogerman.com',
        ], $tr);

        $de = str_replace([
            'Çerez Nedir?', 'Hangi Çerezleri Kullanıyoruz?', 'Kontrolünüz', 'Detaylı Liste', 'Üçüncü Taraf', 'İletişim',
            'Zorunlu çerezler:', 'Analitik çerezler:', 'Pazarlama çerezleri:',
            'Çerezler tarayıcınızda saklanan küçük metin dosyalarıdır. Hizmetlerin doğru çalışması ve istatistik için kullanılır.',
            'Sayfa açılışında çerez banner\'ı görürsünüz. **Kabul Et** veya **Reddet** seçeneklerinden birini kullanabilirsiniz. Tercihinizi istediğinizde değiştirebilirsiniz (tarayıcı çerezlerini silerek banner yeniden çıkar).',
            'İsim', 'Amaç', 'Süre', 'Tip', 'Oturum', 'CSRF güvenliği', 'Oturum yönetimi', '2 saat', 'Zorunlu', 'Analitik (onay sonrası)', 'Onay durumu kaydı', 'Anonim ziyaretçi takip', '1 yıl',
            '**Yok.** Google Analytics, Facebook Pixel veya benzer kullanmıyoruz. Self-hosted analitik tercih ediyoruz.',
            'Sorularınız için: admin@applytogerman.com',
        ], [
            'Was sind Cookies?', 'Welche Cookies nutzen wir?', 'Ihre Kontrolle', 'Detaillierte Liste', 'Drittanbieter', 'Kontakt',
            'Notwendige Cookies:', 'Statistik-Cookies:', 'Marketing-Cookies:',
            'Cookies sind kleine Textdateien, die in Ihrem Browser gespeichert werden, damit Dienste korrekt funktionieren und Statistiken erhoben werden können.',
            'Beim ersten Besuch sehen Sie ein Cookie-Banner. Nutzen Sie **Akzeptieren** oder **Ablehnen**. Sie können Ihre Auswahl jederzeit ändern, indem Sie Cookies löschen.',
            'Name', 'Zweck', 'Dauer', 'Typ', 'Session', 'CSRF-Schutz', 'Session-Verwaltung', '2 Stunden', 'Notwendig', 'Statistik (nach Einwilligung)', 'Einwilligungsstatus', 'Anonyme Besucher-Erkennung', '1 Jahr',
            '**Keine.** Wir nutzen kein Google Analytics, Facebook Pixel o. ä. Wir bevorzugen self-hosted Analytik.',
            'Fragen: admin@applytogerman.com',
        ], $tr);

        return [
            'key' => 'cookies',
            'titles' => ['tr' => 'Çerez Politikası', 'en' => 'Cookie Policy', 'de' => 'Cookie-Richtlinie'],
            'descriptions' => [
                'tr' => 'AlmanyaUni / ApplyToGerman çerez kullanımı — zorunlu, analitik, üçüncü taraf yok, kullanıcı kontrolü.',
                'en' => 'AlmanyaUni / ApplyToGerman cookie usage — essential, analytics, no third parties, user control.',
                'de' => 'AlmanyaUni / ApplyToGerman Cookie-Nutzung — notwendig, Statistik, keine Drittanbieter, Nutzerkontrolle.',
            ],
            'bodies' => ['tr' => $tr, 'en' => $en, 'de' => $de],
        ];
    }
}
