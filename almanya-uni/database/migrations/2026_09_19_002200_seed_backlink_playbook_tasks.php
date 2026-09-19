<?php

use App\Models\Task;
use Illuminate\Database\Migrations\Migration;

/**
 * doc/BACKLINK-PLAYBOOK.md → panelde işaretlenebilir görevler.
 *
 * Kaynak doküman yerinde kalıyor (taktikler, outreach script'leri ve "ne YAPMA"
 * listesi orada); buraya yalnızca TAKİP EDİLECEK İŞLER taşındı.
 *
 * Tarihler: playbook'un 90 günlük planındaki hafta aralıkları, migration'ın
 * çalıştığı güne göre hesaplanır (hafta 1-2 → +14 gün, 3-6 → +42, 7-10 → +70,
 * 11-12 → +84). Prod'da ne zaman koşarsa takvim o gün başlar.
 *
 * Zaten yapılmış işler 'done' olarak işaretlenir — ilk açılışta ilerleme çubuğu
 * gerçeği göstersin diye (link-to-us sayfası, sosyal profiller, OG meta).
 *
 * İdempotent: title+playbook eşleşmesiyle upsert; tekrar çalışırsa kopya üretmez
 * ve kullanıcının işaretlediği durumları EZMEZ (yalnızca eksik alanları doldurur).
 */
return new class extends Migration
{
    public function up(): void
    {
        $w2  = now()->addDays(14)->toDateString();
        $w6  = now()->addDays(42)->toDateString();
        $w10 = now()->addDays(70)->toDateString();
        $w12 = now()->addDays(84)->toDateString();

        $tasks = [
            // ── Hazırlık ────────────────────────────────────────────────────────
            ['Hazırlık', 'Hedef listesini çıkar: 50 dernek/topluluk + 30 blog + 10 üni ofisi', 'Tek bir tabloda topla: kurum adı, iletişim kişisi/e-posta, hangi sayfaya link istiyoruz. Firma Kontakları defterine de girilebilir.', 'high', $w2],
            ['Hazırlık', 'Mevcut ref domain sayısını kaydet (başlangıç ölçümü)', 'GSC → Bağlantılar ekranındaki sayıyı bugünün tarihiyle not et; 3 ay sonra karşılaştırmak için başlangıç noktası.', 'high', $w2],

            // ── A. Topluluk & dernek ────────────────────────────────────────────
            ['A · Topluluk & dernek', 'Almanya\'daki Türk öğrenci derneklerini listele ve iletişime geç', 'Üniversite Türk toplulukları, ADÜTDF, şehir bazlı gruplar. Taktik: önce gerçek yardım, sonra "faydalı bağlantılar" sayfasına eklenme talebi.', 'high', $w6],
            ['A · Topluluk & dernek', 'Üniversite uluslararası ofislerinin "useful links" sayfalarına başvur', 'Akademisches Auslandsamt / International Office sayfaları. Ücretsiz araç olduğumuzu vurgula — reklam değil.', 'high', $w6],
            ['A · Topluluk & dernek', 'Reddit / Quora / Ekşi\'de değer katan cevaplar yaz', 'r/germany ve ülke-spesifik subreddit\'ler. Kural: soruyu gerçekten cevapla, link destekleyici olsun. Link spam\'i ters teper.', 'normal', $w6],
            ['A · Topluluk & dernek', 'Facebook/Telegram öğrenci gruplarında düzenli katkı', 'Haftada birkaç soru cevapla; grup kurallarını ihlal etme.', 'low', $w6],

            // ── B. Blog & danışmanlık ───────────────────────────────────────────
            ['B · Blog & danışmanlık', '30 eğitim/danışmanlık bloguna outreach (TR + EN)', 'Yurt dışı eğitim blogları ve study-abroad siteleri. Haftada 15-20 mail hedefi.', 'high', $w10],
            ['B · Blog & danışmanlık', '2-3 misafir yazı yaz ve yayınlat', 'Uzman olduğun konulardan seç: uni-assist/VPD, NC sistemi, Sperrkonto. Karşı tarafın içerik boşluğunu hedefle.', 'normal', $w10],
            ['B · Blog & danışmanlık', '"Almanya için en iyi araçlar" türü kaynak listelerine eklenme talebi', 'Mevcut listeleri ara, eksik olduğumuz yerlere kısa ve somut bir öneri gönder.', 'normal', $w10],

            // ── C. Linkable asset ───────────────────────────────────────────────
            ['C · Linkable asset', '1. linkable asset: "En ucuz 20 öğrenci şehri" verisi yayınla', 'Kendi şehir/kira verimizden; grafik + tablo + Dataset schema. Alıntılanabilir olması için rakamları net ve kaynaklı ver.', 'high', $w2],
            ['C · Linkable asset', '2. linkable asset: "En çok İngilizce program sunan üniversiteler"', 'Katalogdan üretilebilir (İngilizce içeren program sayıları elimizde).', 'normal', $w12],
            ['C · Linkable asset', '3. linkable asset: "Bölümlere göre NC-frei oranı"', 'admission_mode verisinden; hangi alanda kabul şansı yüksek sorusuna veriyle cevap.', 'normal', $w12],
            ['C · Linkable asset', 'Gazeteci/blog yazarlarına veri pitch\'i gönder', 'Playbook\'taki veri pitch metnini kullan: kaç üni, kaç program, kaç şehir — kaynak gösterimi karşılığı veri.', 'normal', $w12],

            // ── D. Partnerlik ───────────────────────────────────────────────────
            ['D · Partnerlik', 'Sperrkonto ve sigorta sağlayıcılarından karşılıklı link iste', 'Affiliate görüşmesiyle birlikte yürüt — aynı muhatap.', 'normal', $w10],
            ['D · Partnerlik', 'Dil okulu, yurt ve çeviri bürolarıyla karşılıklı link', 'Sitede zaten listeleniyorlar; listelenmeyi karşılıklı bağlantıya çevir.', 'normal', $w10],
            ['D · Partnerlik', 'DAAD partner ağı ve üniversite iş birliklerini araştır', 'Uzun vadeli; önce hangi programın başvuru kabul ettiğini netleştir.', 'low', null],

            // ── E. Dizin & profil ───────────────────────────────────────────────
            ['E · Dizin & profil', 'Eğitim dizinlerine kayıt (alakalı olanlar)', 'Sadece konuyla ilgili dizinler. Toplu/alakasız dizin kaydı ceza riski — playbook\'un "ne YAPMA" listesine bak.', 'low', $w12],
            ['E · Dizin & profil', 'Product Hunt lansmanı (araçlar için)', 'Maliyet hesaplayıcı ve üni eşleştirme aracı lansmana uygun.', 'low', $w12],
            ['E · Dizin & profil', 'Sosyal profilleri tutarlı ve aktif tut', 'IG / X / YouTube / Facebook / Telegram bağlantıları panelde tanımlı; düzenli paylaşım tarafı açık.', 'low', null],

            // ── Ölçüm ───────────────────────────────────────────────────────────
            ['Ölçüm', 'Aylık: GSC ref domain + indexlenen sayfa sayısını kaydet', 'Bu kampanyanın asıl çıktısı indexlenen sayfa sayısı; tek başına link sayısı yanıltır.', 'normal', $w6],
            ['Ölçüm', '3 ayda bir: DR/DA trendini not et', 'Ahrefs/Ubersuggest ücretsiz sürümü yeterli.', 'low', $w12],
            ['Ölçüm', 'Yıl-1 hedefi: +30 alakalı ref domain', 'Playbook hedefi. Ay ay takip et, tek seferde değil.', 'normal', null],

            // ── Kod tarafı ──────────────────────────────────────────────────────
            ['Kod tarafı', 'Embed/paylaş widget: araç sonuçları bloga gömülebilsin', 'Her embed bir backlink üretir. En yüksek getirili kod işi.', 'normal', null],
            ['Kod tarafı', 'Linkable-asset istatistik sayfası altyapısı', 'Dataset schema + grafik; C bölümündeki içerikleri besleyecek şablon.', 'normal', null],
        ];

        $done = [
            ['Kod tarafı', '"Bizi linkleyin / basın" sayfası', '/link-to-us yayında: hazır link metinleri, rozetler, basın kiti ve alıntılanabilir istatistikler.', 'normal'],
            ['Kod tarafı', 'Sosyal paylaşım meta etiketleri (OG / Twitter)', 'Tüm sayfalarda tam; paylaşım sinyali için gereken altyapı hazır.', 'low'],
            ['E · Dizin & profil', 'Sosyal profilleri oluştur (IG / X / YouTube / Facebook / Telegram)', 'Hesaplar açıldı ve panelde tanımlı.', 'low'],
        ];

        $order = 0;

        foreach ($tasks as [$group, $title, $details, $priority, $due]) {
            $this->upsert($title, [
                'playbook'   => 'backlink',
                'group'      => $group,
                'details'    => $details,
                'priority'   => $priority,
                'due_date'   => $due,
                'sort_order' => $order += 10,
            ]);
        }

        foreach ($done as [$group, $title, $details, $priority]) {
            $this->upsert($title, [
                'playbook'     => 'backlink',
                'group'        => $group,
                'details'      => $details,
                'priority'     => $priority,
                'sort_order'   => $order += 10,
                'status'       => 'done',
                'completed_at' => now(),
            ]);
        }
    }

    /**
     * Varsa DOKUNMA (kullanıcının durumu korunur), yoksa oluştur.
     */
    private function upsert(string $title, array $attributes): void
    {
        $existing = Task::where('playbook', $attributes['playbook'])->where('title', $title)->first();

        if ($existing) {
            return;
        }

        Task::create($attributes + ['title' => $title]);
    }

    public function down(): void
    {
        Task::where('playbook', 'backlink')->delete();
    }
};
