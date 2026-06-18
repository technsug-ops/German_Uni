<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/** TR blog (üniversite): Hochschule vs Universität vs FH/TH — kurum tipleri, CS/mühendislik örnekleri, doktora yolu. FK-safe. */
return new class extends Migration
{
    public function up(): void
    {
        $slug = 'hochschule-vs-universitaet-vs-fh-differences-in-germany';
        $body = <<<'MD'
Almanya'da üniversite ararken "Hochschule", "Universität", "FH", "TH" gibi terimlerle karşılaşıp kafan karışır. Hepsi yükseköğretim ama **aynı şey değil** — ve doğru tercih, özellikle CS/mühendislikte kariyerini etkiler. İşte sade, net açıklama.

## Terimler: Hochschule bir şemsiye terim
- **Hochschule** = tüm yükseköğretim kurumlarının **genel adı** (şemsiye terim).
- **Universität (Uni)** = bir Hochschule türü; **akademik + araştırma** odaklı, klasik üniversite.
- **Fachhochschule (FH) / HAW** = "uygulamalı bilimler üniversitesi"; **pratik/meslek** odaklı.
- ⚠️ Kafa karıştıran nokta: kendine sadece **"Hochschule"** diyen bir kurum **genellikle FH'dir.**
- **Technische Hochschule (TH)** = teknik odaklı; **bazıları gerçek üniversitedir** (ör. RWTH Aachen, KIT), **bazıları FH'dir** (ör. TH Köln). Ada değil, **kuruma bak**.

## Kısa tarih: FH neden var?
FH'ler **1970'lerde** kuruldu: o dönem üniversiteye Abitur (13 yıl) gerekiyordu; meslek eğitimi almış kişilere yükseköğretim yolu açmak için. Bu yüzden FH **baştan iş/meslek odaklı** (mühendislik, işletme...) ve Bologna reformundan beri Uni-FH çizgisi giderek **bulanıklaştı**.

## Uni vs FH: net karşılaştırma
| | **Universität** | **Fachhochschule / HAW** |
|---|---|---|
| Odak | Teori, araştırma, akademi | Uygulama, proje, meslek |
| Ders tarzı | Büyük amfi, çok teori | Küçük grup, okul-benzeri, lab |
| Staj | Zorunlu değil (çoğu) | Genelde **zorunlu pratik dönem** |
| Doktora | Doğrudan açık | **Doğrudan zor** (üni iş birliği gerekir) |
| Kabul | Daha rekabetçi olabilir | Sıklıkla **daha esnek** |
| Akademi kariyeri | ✅ Uygun başlangıç | ⚠️ İdeal değil |

## CS & Mühendislik özelinde fark (somut)
Aynı dersin işlenişi farklıdır:
- **Lineer cebir:** Uni teoremi **ispatlar**; FH onunla (saf lineer cebirle) bir **spam filtresi yazdırır**.
- **Bilgisayar ağları:** Uni **OSI modelini** derinlemesine işler; FH ilk 2 haftada **lab'da çalışan bir ağ** kurdurur.
- **Matematik:** Uni'de teoremi ispatlarsın, FH'de **uygulamayı** bilmen yeter.

FH'de **küçük sınıf + uygulama** → öğrenciler erken (2. sömestr civarı) **Werkstudent** olarak alanında çalışmaya başlar → mezuniyette çoğu zaten **1-2 yıl tecrübeli** → iş bulmak kolay. (Bkz. [Werkstudent: iş piyasasının anahtarı](/tr/blog/werkstudent-in-germany-the-real-key-to-the-job-market).)

## Doktora / akademi
- Akademik kariyer / doktora hedefin varsa: **Universität'ten başla.**
- FH'de Master mümkün, hatta bazı FH'ler **iş birliğiyle doktora** pozisyonu sunar — ama saf akademi için ideal değil; FH profili sanayi tarafından çok, akademi tarafından daha az değer görür.

## "Sadece mezuniyet sonrası para önemli" diyorsan
Bachelor düzeyinde **CS/mühendislikte iş piyasası açısından Uni-FH farkı azdır.** Maaşı belirleyen kurum tipi değil, **becerin + tecrüben**. FH'nin pratik + erken Werkstudent avantajı, hızlı istihdam ve gelir için genelde **lehine** çalışır. (İlgili: [İngilizce Master: FH vs Uni stratejisi](/tr/blog/english-master-admission-chances-germany-gpa-fh-vs-uni-strategy).)

## Neden FH'ler "sıralanmıyor"?
Devlet üniversiteleri kabaca eşit kabul edilir; uluslararası sıralamalar **araştırmayı** ölçtüğü için FH'ler ya görünmez ya düşük çıkar — bu "kötü" demek **değil**, misyonları farklı. (Detay: [sıralamalar Almanya'da ne ifade eder](/tr/blog/do-university-rankings-matter-in-germany-qs-the-explained).)

## Sonuç
- **Universität:** teori, araştırma, doktora, akademi.
- **FH/HAW:** uygulama, staj, erken iş tecrübesi, hızlı istihdam.
- **TH:** teknik odak — üni mi FH mi, kuruma bak.
CS/mühendislikte para+hızlı iş önceliğinse **FH güçlü bir seçim**; akademi/doktora istiyorsan **Uni**. İkisi de Bachelor'da iş için yeterli — kararı **hedef + şehir + kabul** üçgeninde ver. İlgili: [prestij miti & Uni vs FH](/tr/blog/prestige-myth-german-universities-uni-vs-fh-practical-path) · [Devlet vs Özel vs FH](/tr/blog/public-vs-private-universities-germany-balanced-comparison) · [Duales Studium](/tr/blog/duales-studium-germany-paid-study-complete-guide).

---
*Alman yükseköğretim yapısı temel alınarak hazırlanmıştır. Program ve kabul koşulları kuruma göre değişir — resmî kaynaktan teyit et.*
MD;
        $excerpt = 'Hochschule, Universität, FH, TH farkı net: Hochschule şemsiye terim; Universität = araştırma/akademi, FH = uygulama/meslek (sadece "Hochschule" diyen genelde FH), TH = teknik (kuruma göre üni veya FH). CS/mühendislik somut örnekleri (teoremi ispatla vs spam filtre yaz), doktora yolu, Werkstudent avantajı ve "para odaklıysan hangisi" — dengeli rehber.';
        $html = Str::markdown($body, ['html_input' => 'allow', 'allow_unsafe_links' => false]);
        $userId = DB::table('users')->where('id', 4)->exists() ? 4 : DB::table('users')->orderBy('id')->value('id');
        $categoryId = DB::table('categories')->where('id', 12)->exists() ? 12 : DB::table('categories')->where('slug', 'universities')->value('id');
        $payload = ['locale' => 'tr', 'user_id' => $userId, 'category_id' => $categoryId,
            'title' => 'Hochschule, Universität ve FH Farkı: Almanya\'da Kurum Tipleri (CS & Mühendislik)',
            'excerpt' => Str::limit($excerpt, 250, '…'), 'content_md' => $body, 'content_html' => $html,
            'meta_title' => 'Hochschule vs Universität vs FH: Almanya\'da Fark Nedir?',
            'meta_description' => Str::limit($excerpt, 158, '…'),
            'reading_minutes' => max(1, (int) round(str_word_count(strip_tags($html)) / 200)),
            'is_published' => true, 'published_at' => now()];
        $existing = Post::where('slug', $slug)->first();
        if ($existing) { $existing->update($payload); } else { Post::create($payload + ['slug' => $slug, 'translation_group_id' => (string) Str::uuid()]); }
    }
    public function down(): void { Post::where('slug', 'hochschule-vs-universitaet-vs-fh-differences-in-germany')->delete(); }
};
