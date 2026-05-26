<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * PİLOT: "Almanca Bilmeden İngilizce Master" (ID 13, 30K karakter) blogunu
 * 3 odaklı bloga böler. Her birine:
 *   - Farklı yazar (Elif G. / Hakan Kutlu / Caner Türkdoğru)
 *   - Farklı yayın tarihi
 *   - Pollinations AI kapak görseli
 *   - İçeriğe gömülü HTML/CSS infografik blokları
 */
class BlogSplitPilot extends Command
{
    protected $signature = 'blog:split-pilot {--source=13 : Bölünecek post ID} {--dry-run}';
    protected $description = 'Pilot: uzun blogu 3 parçaya böl + yazar + kapak + infografik';

    public function handle(): int
    {
        $source = Post::find((int) $this->option('source'));
        if (! $source) {
            $this->error('Kaynak post bulunamadı.');
            return self::FAILURE;
        }

        // 1) Yazarlar (yoksa oluştur)
        $authors = $this->ensureAuthors();

        // 2) Kaynak içeriği H2 section'larına ayır
        $sections = $this->splitSections($source->content_md);
        $this->info('Kaynak section sayısı: ' . count($sections));

        // 3) 3 blog tanımı
        $blogs = $this->blogDefinitions($sections, $authors);

        if ($this->option('dry-run')) {
            foreach ($blogs as $b) {
                $this->line("• {$b['title']} · {$b['author']->name} · {$b['published_at']} · " . number_format(mb_strlen($b['content_md'])) . ' ch');
            }
            return self::SUCCESS;
        }

        // 4) Kategori: "Almanya'da Eğitim"
        $catId = $source->category_id;

        foreach ($blogs as $b) {
            // 0) Kapak görselini Pollinations'tan indir → lokal storage (hızlı + kalıcı)
            $cover = $this->downloadCover($b['cover'], $b['slug']);

            // 1) Post'u kaydet — saving observer content_html'i content_md'den (markdown) üretir.
            $post = Post::updateOrCreate(
                ['slug' => $b['slug']],
                [
                    'user_id'                => $b['author']->id,
                    'category_id'            => $catId,
                    'title'                  => $b['title'],
                    'excerpt'                => $b['excerpt'],
                    'content_md'             => $b['content_md'],
                    'featured_image'         => $cover,
                    'featured_image_caption' => $b['cover_caption'],
                    'meta_title'             => $b['meta_title'],
                    'meta_description'       => $b['meta_description'],
                    'is_published'           => true,
                    'published_at'           => $b['published_at'],
                ]
            );

            // 2) İnfografik HTML'lerini observer'dan SONRA ekle (DB update observer'ı tetiklemez,
            //    böylece CommonMark 'html_input=strip' raw HTML'imizi silmez).
            $finalHtml = $b['infographic_top'] . $post->content_html . $b['infographic_bottom'];
            DB::table('posts')->where('id', $post->id)->update(['content_html' => $finalHtml]);

            $this->info("✅ {$post->title} · {$b['author']->name} · {$b['published_at']}");
        }

        // 5) Orijinali yayından kaldır (3 yeni blog onun yerine geçti)
        $source->update(['is_published' => false]);
        $this->warn("Orijinal #{$source->id} yayından kaldırıldı (3 parçaya bölündü).");

        return self::SUCCESS;
    }

    /** Pollinations görselini indirip storage/app/public/blog/{slug}.jpg olarak kaydeder. */
    private function downloadCover(string $url, string $slug): string
    {
        $path = "blog/{$slug}.jpg";
        try {
            $this->line("  ⬇️  Kapak indiriliyor: {$slug} …");
            $resp = Http::timeout(90)->retry(2, 3000)->get($url);
            if ($resp->ok() && str_contains((string) $resp->header('Content-Type'), 'image') && strlen($resp->body()) > 5000) {
                Storage::disk('public')->put($path, $resp->body());
                $this->info('  ✅ Kapak kaydedildi (' . number_format(strlen($resp->body()) / 1024, 0) . ' KB)');
                return $path; // featured_image: storage path (blog show asset('storage/'.$path) yapar)
            }
            $this->warn('  ⚠️  Görsel alınamadı, URL fallback');
        } catch (\Throwable $e) {
            $this->warn('  ⚠️  İndirme hatası: ' . mb_substr($e->getMessage(), 0, 60));
        }
        return $url; // fallback: doğrudan Pollinations URL
    }

    private function ensureAuthors(): array
    {
        $defs = [
            'elif'  => ['name' => 'Elif G.',          'role' => 'İçerik Editörü · Başvuru uzmanı',  'bio' => 'Almanya başvuru süreçleri ve uni-assist konularında içerik üretiyor.'],
            'gamze' => ['name' => 'Gamze E.',         'role' => 'İçerik Editörü · Dil & sınav',     'bio' => 'Almanca dil sınavları ve hazırlık süreçleri üzerine yazıyor.'],
            'hakan' => ['name' => 'Hakan Kutlu',      'role' => 'İçerik Editörü · Vize & yaşam',    'bio' => 'Vize süreçleri ve Almanya\'da öğrenci yaşamı konusunda deneyimli.'],
            'caner' => ['name' => 'Caner Türkdoğru',  'role' => 'İçerik Editörü · Kariyer',         'bio' => 'Almanya\'da kariyer, staj ve iş hayatı üzerine içerik üretiyor.'],
        ];

        $authors = [];
        foreach ($defs as $key => $d) {
            $email = $key . '@almanyauni.com';
            $authors[$key] = User::firstOrCreate(
                ['email' => $email],
                [
                    'name'       => $d['name'],
                    'password'   => bcrypt(Str::random(32)),
                    'is_author'  => true,
                    'is_admin'   => false,
                    'role_label' => $d['role'],
                    'bio'        => $d['bio'],
                    'avatar_url' => 'https://ui-avatars.com/api/?name=' . urlencode($d['name']) . '&background=10b981&color=fff&bold=true',
                ]
            );
        }
        return $authors;
    }

    /** content_md'yi "## Başlık" → gövde şeklinde parse eder. */
    private function splitSections(string $md): array
    {
        $parts = preg_split('/^##\s+(.+)$/m', $md, -1, PREG_SPLIT_DELIM_CAPTURE);
        $sections = [];
        // $parts[0] = ilk başlıktan önceki metin (genelde boş)
        for ($i = 1; $i < count($parts); $i += 2) {
            $title = trim($parts[$i]);
            $body  = trim($parts[$i + 1] ?? '');
            $sections[$title] = $body;
        }
        return $sections;
    }

    private function sectionMd(array $sections, array $titles): string
    {
        $out = [];
        foreach ($titles as $t) {
            // Esnek eşleştirme: başlık başında geçen
            foreach ($sections as $k => $v) {
                if (Str::startsWith(mb_strtolower($k), mb_strtolower($t))) {
                    $out[] = "## {$k}\n\n{$v}";
                    break;
                }
            }
        }
        return implode("\n\n", $out);
    }

    private function blogDefinitions(array $s, array $authors): array
    {
        $poll = fn (string $p, int $seed) => 'https://image.pollinations.ai/prompt/' . rawurlencode($p) . "?width=1200&height=630&nologo=true&seed={$seed}";

        return [
            // ---- BLOG A: Başvuru ----
            [
                'title' => 'Almanca Bilmeden Almanya\'da İngilizce Master: Program Bulma ve Başvuru (2026)',
                'slug'  => 'almanca-bilmeden-ingilizce-master-basvuru-rehberi',
                'author' => $authors['elif'],
                'published_at' => '2026-04-12 09:30:00',
                'excerpt' => 'Almanya\'da 2.000+ İngilizce master programı var. Almanca bilmeden nasıl program bulunur, hangi belgeler gerekir, IELTS/TOEFL kaç puan ister? Adım adım başvuru rehberi.',
                'meta_title' => 'Almanca Bilmeden İngilizce Master Almanya — Başvuru Rehberi 2026',
                'meta_description' => 'Almanya İngilizce master başvurusu: program bulma (DAAD), IELTS/TOEFL eşikleri, APS, motivasyon mektubu ve kabul şartları. Türk öğrenciler için 2026 rehberi.',
                'content_md' => $this->sectionMd($s, ['Giriş', 'Almanca Bilmeden Almanya', 'İngilizce Master Programları Nasıl', 'Başvuru Süreci']),
                'cover' => $poll('modern university library Germany, international students studying with laptops, bright academic atmosphere, photorealistic', 101),
                'cover_caption' => 'Almanya\'da İngilizce master programları her yıl artıyor.',
                'infographic_top' => $this->infoEnglishThresholds(),
                'infographic_bottom' => $this->infoApplicationChecklist(),
            ],

            // ---- BLOG B: Vize + Yaşam ----
            [
                'title' => 'İngilizce Master İçin Vize Süreci ve Almanca Bilmeden Almanya\'da Yaşam',
                'slug'  => 'ingilizce-master-vize-sureci-almanca-bilmeden-yasam',
                'author' => $authors['hakan'],
                'published_at' => '2026-04-26 11:15:00',
                'excerpt' => 'İngilizce master kabulü aldın — peki vize görüşmesinde Almanca isterler mi? 40f/36f/17f farkı ne? Almanca bilmeden büyük ve küçük şehirlerde yaşam nasıl? Gerçek deneyimler.',
                'meta_title' => 'İngilizce Master Vizesi + Almanca Bilmeden Almanya\'da Yaşam',
                'meta_description' => 'Almanya İngilizce master vize süreci: 40f/36f/17f vize türleri, konsolosluk dil beklentisi, yeşil pasaport. Almanca olmadan büyük/küçük şehirde yaşam rehberi.',
                'content_md' => $this->sectionMd($s, ['Vize Süreci', 'Almanya\'da Yaşam']),
                'cover' => $poll('German consulate visa appointment, passport documents on desk, professional office setting, photorealistic', 102),
                'cover_caption' => 'İngilizce programlar için vize görüşmesi genellikle İngilizce yapılır.',
                'infographic_top' => $this->infoVisaTypes(),
                'infographic_bottom' => $this->infoCityComparison(),
            ],

            // ---- BLOG C: Kariyer ----
            [
                'title' => 'İngilizce Master Sonrası: Almanca, Staj, İş ve Kariyer Fırsatları',
                'slug'  => 'ingilizce-master-sonrasi-almanca-staj-is-kariyer',
                'author' => $authors['caner'],
                'published_at' => '2026-05-09 10:00:00',
                'excerpt' => 'İngilizce master bitti — iş ararken Almanca şart mı? Master mı Ausbildung mu? Almanca seviyesi iş fırsatlarını nasıl etkiler? Telegram\'dan gerçek sorular + net cevaplar.',
                'meta_title' => 'İngilizce Master Sonrası Kariyer — Almanca, Staj, İş Rehberi',
                'meta_description' => 'Almanya\'da İngilizce master sonrası iş: Almancanın kariyere etkisi, master vs Ausbildung karşılaştırması, staj ve iş başvurusunda dil şartı. 2026 kariyer rehberi.',
                'content_md' => $this->sectionMd($s, ['Staj ve İş', 'Ausbildung mu', 'İngilizce Master Yaparken Almanca', 'İngilizce Master Sonrası', 'Sonuç']),
                'cover' => $poll('young professional in modern Berlin tech office, diverse international team, career success, photorealistic', 103),
                'cover_caption' => 'Almanca bilmek, kariyer fırsatlarını ciddi şekilde genişletir.',
                'infographic_top' => $this->infoMasterVsAusbildung(),
                'infographic_bottom' => $this->infoGermanCareerImpact(),
            ],
        ];
    }

    // ===================== İNFOGRAFİK HTML BLOKLARI =====================

    private function box(string $inner): string
    {
        return '<div style="margin:28px 0;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden;font-family:inherit;">' . $inner . '</div>';
    }

    private function infoEnglishThresholds(): string
    {
        return $this->box('
            <div style="background:linear-gradient(90deg,#4f46e5,#7c3aed);color:#fff;padding:14px 18px;font-weight:700;font-size:15px;">📊 İngilizce Yeterlilik Eşikleri (master başvurusu)</div>
            <div style="padding:18px;display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:12px;">
                <div style="background:#eef2ff;border-radius:10px;padding:14px;text-align:center;"><div style="font-size:24px;font-weight:800;color:#4338ca;">6.0–7.5</div><div style="font-size:13px;color:#475569;margin-top:4px;">IELTS</div></div>
                <div style="background:#eef2ff;border-radius:10px;padding:14px;text-align:center;"><div style="font-size:24px;font-weight:800;color:#4338ca;">80–100</div><div style="font-size:13px;color:#475569;margin-top:4px;">TOEFL iBT</div></div>
                <div style="background:#ecfdf5;border-radius:10px;padding:14px;text-align:center;"><div style="font-size:24px;font-weight:800;color:#047857;">FCE+</div><div style="font-size:13px;color:#475569;margin-top:4px;">Cambridge</div></div>
                <div style="background:#fffbeb;border-radius:10px;padding:14px;text-align:center;"><div style="font-size:18px;font-weight:800;color:#b45309;">Muafiyet</div><div style="font-size:12px;color:#475569;margin-top:4px;">Lisans İng. ise bazı üniler belge istemez</div></div>
            </div>
            <div style="padding:0 18px 16px;font-size:12px;color:#64748b;">⚠️ Eşikler programa göre değişir — her üniversitenin kendi sayfasından doğrula.</div>
        ');
    }

    private function infoApplicationChecklist(): string
    {
        $items = [
            ['🎓', 'Lisans diploması + transkript', 'Denklik/GPA dönüşümü gerekebilir'],
            ['🇬🇧', 'İngilizce yeterlilik (IELTS/TOEFL)', 'Lisans İng. ise muafiyet mümkün'],
            ['✍️', 'Motivasyon mektubu', 'Neden bu program + kariyer hedefi'],
            ['📄', 'Akademik CV', 'Akademik format'],
            ['📨', 'Referans mektupları', '1-2 akademik referans'],
            ['🔖', 'APS belgesi', 'Türkiye\'den başvuranlar için ZORUNLU'],
        ];
        $rows = '';
        foreach ($items as $i => [$ic, $t, $d]) {
            $rows .= '<div style="display:flex;gap:12px;align-items:flex-start;padding:10px 0;' . ($i < 5 ? 'border-bottom:1px solid #f1f5f9;' : '') . '">
                <span style="font-size:20px;">' . $ic . '</span>
                <div><div style="font-weight:600;color:#0f172a;font-size:14px;">' . $t . '</div><div style="font-size:12px;color:#64748b;">' . $d . '</div></div></div>';
        }
        return $this->box('
            <div style="background:linear-gradient(90deg,#059669,#10b981);color:#fff;padding:14px 18px;font-weight:700;font-size:15px;">✅ Başvuru Belgeleri Kontrol Listesi</div>
            <div style="padding:8px 18px 16px;">' . $rows . '</div>
        ');
    }

    private function infoVisaTypes(): string
    {
        return $this->box('
            <div style="background:linear-gradient(90deg,#dc2626,#f59e0b);color:#fff;padding:14px 18px;font-weight:700;font-size:15px;">🛂 Hangi Vize? (İngilizce master için)</div>
            <table style="width:100%;border-collapse:collapse;font-size:13px;">
                <tr style="background:#f8fafc;"><th style="text-align:left;padding:10px 18px;color:#475569;">Vize</th><th style="text-align:left;padding:10px;color:#475569;">Ne zaman?</th></tr>
                <tr style="border-top:1px solid #f1f5f9;"><td style="padding:10px 18px;font-weight:700;color:#dc2626;">40f</td><td style="padding:10px;color:#334155;">Kesin kabul (Zulassung) aldıysan — öğrenci vizesi. İngilizce master için en yaygın.</td></tr>
                <tr style="border-top:1px solid #f1f5f9;background:#fafafa;"><td style="padding:10px 18px;font-weight:700;color:#b45309;">36f</td><td style="padding:10px;color:#334155;">Almanca şartlı kabul → dil kursu + üniversite başvurusu.</td></tr>
                <tr style="border-top:1px solid #f1f5f9;"><td style="padding:10px 18px;font-weight:700;color:#7c3aed;">17f</td><td style="padding:10px;color:#334155;">Sadece dil kursu için.</td></tr>
            </table>
            <div style="padding:14px 18px;font-size:12px;color:#64748b;">💬 Topluluktan: "40f mi 36f mi?" — kesin kabulün varsa 40f. Şartlı (dil) kabulün varsa 36f. Kabul mektubunu + konsolosluk sayfasını doğrula.</div>
        ');
    }

    private function infoCityComparison(): string
    {
        return $this->box('
            <div style="background:linear-gradient(90deg,#0891b2,#0d9488);color:#fff;padding:14px 18px;font-weight:700;font-size:15px;">🏙️ Almanca Bilmeden Yaşam: Büyük vs Küçük Şehir</div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:0;">
                <div style="padding:16px 18px;border-right:1px solid #f1f5f9;">
                    <div style="font-weight:700;color:#0e7490;margin-bottom:8px;">🌆 Büyük şehir</div>
                    <div style="font-size:13px;color:#334155;line-height:1.7;">Berlin, Münih, Hamburg, Köln<br>✓ İngilizce yaygın<br>✓ Uluslararası topluluk<br>✓ İngilizce hizmet kolay</div>
                </div>
                <div style="padding:16px 18px;">
                    <div style="font-weight:700;color:#b45309;margin-bottom:8px;">🏘️ Küçük şehir</div>
                    <div style="font-size:13px;color:#334155;line-height:1.7;">Kasaba/taşra<br>⚠️ İngilizce sınırlı<br>⚠️ Bürokraside Almanca<br>✓ Daha ucuz yaşam</div>
                </div>
            </div>
            <div style="padding:0 18px 16px;font-size:12px;color:#64748b;">📌 Anmeldung, banka, sigorta işlemleri Almanca — uni International Office destek verir.</div>
        ');
    }

    private function infoMasterVsAusbildung(): string
    {
        return $this->box('
            <div style="background:linear-gradient(90deg,#7c3aed,#db2777);color:#fff;padding:14px 18px;font-weight:700;font-size:15px;">⚖️ İngilizce Master vs Ausbildung</div>
            <table style="width:100%;border-collapse:collapse;font-size:13px;">
                <tr style="background:#f8fafc;"><th style="text-align:left;padding:10px 18px;color:#475569;"> </th><th style="padding:10px;color:#4338ca;">🎓 İngilizce Master</th><th style="padding:10px;color:#047857;">🔨 Ausbildung</th></tr>
                <tr style="border-top:1px solid #f1f5f9;"><td style="padding:10px 18px;font-weight:600;color:#334155;">Almanca şartı</td><td style="padding:10px;text-align:center;color:#16a34a;">Genelde yok</td><td style="padding:10px;text-align:center;color:#dc2626;">B2 zorunlu</td></tr>
                <tr style="border-top:1px solid #f1f5f9;background:#fafafa;"><td style="padding:10px 18px;font-weight:600;color:#334155;">Maaş (eğitimde)</td><td style="padding:10px;text-align:center;color:#64748b;">Yok</td><td style="padding:10px;text-align:center;color:#16a34a;">Var</td></tr>
                <tr style="border-top:1px solid #f1f5f9;"><td style="padding:10px 18px;font-weight:600;color:#334155;">Süre</td><td style="padding:10px;text-align:center;color:#334155;">1.5–2 yıl</td><td style="padding:10px;text-align:center;color:#334155;">2–3.5 yıl</td></tr>
                <tr style="border-top:1px solid #f1f5f9;background:#fafafa;"><td style="padding:10px 18px;font-weight:600;color:#334155;">Kariyer tipi</td><td style="padding:10px;text-align:center;color:#334155;">Akademik/uzman</td><td style="padding:10px;text-align:center;color:#334155;">Pratik/meslek</td></tr>
            </table>
        ');
    }

    private function infoGermanCareerImpact(): string
    {
        $levels = [
            ['Almanca yok', 18, '#dc2626', 'Sadece İngilizce çalışan uluslararası şirket/startup'],
            ['B1–B2', 55, '#f59e0b', 'Çoğu sektörde kapı açılır, günlük iş iletişimi'],
            ['C1+', 92, '#16a34a', 'Alman şirketleri + kamu + müşteri rolleri tamamen açık'],
        ];
        $bars = '';
        foreach ($levels as [$label, $pct, $color, $desc]) {
            $bars .= '<div style="margin-bottom:14px;">
                <div style="display:flex;justify-content:space-between;font-size:13px;margin-bottom:4px;"><span style="font-weight:600;color:#0f172a;">' . $label . '</span><span style="color:' . $color . ';font-weight:700;">İş erişimi ~%' . $pct . '</span></div>
                <div style="width:100%;height:10px;background:#f1f5f9;border-radius:9999px;overflow:hidden;"><div style="height:100%;width:' . $pct . '%;background:' . $color . ';"></div></div>
                <div style="font-size:11px;color:#64748b;margin-top:3px;">' . $desc . '</div></div>';
        }
        return $this->box('
            <div style="background:linear-gradient(90deg,#059669,#0d9488);color:#fff;padding:14px 18px;font-weight:700;font-size:15px;">📈 Almanca Seviyesi → İş Fırsatı Etkisi</div>
            <div style="padding:18px;">' . $bars . '<div style="font-size:11px;color:#94a3b8;margin-top:4px;">* Temsili oranlar — sektöre/şehre göre değişir.</div></div>
        ');
    }
}
