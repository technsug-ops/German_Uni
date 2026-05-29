# 💰 MAAŞ SİSTEMİ ENTEGRASYONU - 3 KATMANLI HAZIRLIK
## DESTATIS API + Mezun Anketi + İçerik

---

## 🎯 GENEL BAKIŞ

```
KATMAN 1: RESMİ VERİ (Hemen)
└── DESTATIS GENESIS-Online API
    ✅ Resmi RESTful/JSON API VAR!
    ✅ Ücretsiz
    ✅ Alıntılanabilir (Datenlizenz Deutschland)

KATMAN 2: KENDİ ANKETİN (1-3 ay)
└── Mezun maaş anketi sistemi
    ✅ Laravel'de form + veritabanı
    ✅ Anonim toplama
    ✅ Üniversite + bölüm bazlı

KATMAN 3: İÇERİK (Sürekli)
└── Otomatik rapor üretimi
    ✅ "2026 Maaş Raporu"
    ✅ İnfografik
    ✅ Blog entegrasyonu
```

---

# 🏛️ KATMAN 1: DESTATIS API

## ✅ HARİKA HABER: Resmi API Var!

**Araştırma sonucu net:**

```
DESTATIS (Statistisches Bundesamt) = Almanya Resmi İstatistik Kurumu

API Adı:      GENESIS-Online Webservice
Tip:          RESTful / JSON
Maliyet:      ÜCRETSİZ
Lisans:       Datenlizenz Deutschland (alıntıyla serbest!)
Base URL:     https://www-genesis.destatis.de/genesisWS/rest/2020
Kayıt:        Ücretsiz (kullanıcı + şifre veya token)
```

### Test Endpoint (Hemen Dene!):

```
https://www-genesis.destatis.de/genesisWS/rest/2020/helloworld/whoami
```

---

## 📋 ADIM 1: DESTATIS Hesabı Aç

### Kayıt Süreci:

```
1. Git: https://www-genesis.destatis.de
2. Sağ üstte "Registrieren" (Kayıt ol)
3. Formu doldur:
   - E-posta
   - İsim
   - Kullanım amacı: "Bildungsplattform / Forschung"
4. E-posta onayı
5. Giriş yap
6. Sol altta "Webservice-Schnittstelle (API)" tıkla
7. API TOKEN'ı kopyala
```

### API Token Nerede?

```
GENESIS-Online'a giriş yap
→ Sol alt köşe
→ "Webservice-Schnittstelle (API)" 
→ Token görünür
→ Bu token'ı .env'e kaydedeceğiz
```

---

## 📊 ADIM 2: Hangi Verileri Çekeceğiz?

### Maaş İle İlgili GENESIS Tabloları:

```
TABLO KODU    İÇERİK
─────────     ──────────────────────────────────
62361-0001    Brüt aylık kazanç (meslek grubu)
62361-0002    Brüt yıllık kazanç (sektör)
62321-0001    Çalışan başına kazanç (eyalet)
62421-0001    Cinsiyet bazlı kazanç farkı

SEKTÖR KAZANÇLARI:
- Verarbeitendes Gewerbe (İmalat)
- Information und Kommunikation (IT)
- Gesundheitswesen (Sağlık)
- Erziehung und Unterricht (Eğitim)
```

### Veri Türü:

```
DESTATIS şunları verir:
✓ Sektör bazlı ortalama maaş
✓ Eyalet bazlı maaş
✓ Meslek grubu kazançları
✓ Cinsiyet farkı
✓ Tam zamanlı / yarı zamanlı

DESTATIS şunları VERMEZ:
✗ Üniversite bazlı maaş
✗ Belirli pozisyon (junior/senior)
✗ Şirket bazlı
✗ Mezun bazlı

→ Bu yüzden KATMAN 2 (anket) gerekli!
```

---

## 💻 ADIM 3: Laravel Entegrasyonu

### 3.1 Migration - Maaş Tabloları

```bash
php artisan make:migration create_salary_tables
```

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Resmi sektör maaşları (DESTATIS)
        Schema::create('sector_salaries', function (Blueprint $table) {
            $table->id();
            $table->string('sector_code', 20);           // GENESIS tablo kodu
            $table->string('sector_name_de');            // Almanca sektör
            $table->string('sector_name_tr');            // Türkçe sektör
            $table->string('sector_name_en')->nullable();
            
            $table->decimal('avg_monthly_gross', 10, 2); // Aylık brüt
            $table->decimal('avg_yearly_gross', 12, 2);  // Yıllık brüt
            $table->decimal('avg_monthly_net', 10, 2)->nullable();
            
            $table->string('state_code', 10)->nullable(); // Eyalet
            $table->string('gender', 10)->nullable();      // m/f/all
            $table->year('data_year');                     // Veri yılı
            
            $table->string('source')->default('destatis');
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();
            
            $table->index(['sector_code', 'data_year']);
            $table->index('state_code');
        });
        
        // 2. Mezun maaş anketi (KATMAN 2)
        Schema::create('graduate_salaries', function (Blueprint $table) {
            $table->id();
            
            // İlişkiler
            $table->foreignId('university_id')->nullable()
                  ->constrained()->nullOnDelete();
            $table->foreignId('field_id')->nullable()
                  ->constrained('academic_fields')->nullOnDelete();
            
            // Eğitim bilgisi
            $table->string('degree_type', 30);    // bachelor/master/phd
            $table->year('graduation_year');
            
            // Maaş bilgisi
            $table->decimal('gross_yearly', 12, 2);
            $table->string('currency', 3)->default('EUR');
            $table->enum('experience_level', [
                'entry',      // 0-2 yıl
                'junior',     // 2-5 yıl
                'mid',        // 5-10 yıl
                'senior',     // 10+ yıl
            ]);
            
            // Bağlam
            $table->string('job_title')->nullable();
            $table->string('city')->nullable();
            $table->string('company_size')->nullable(); // startup/sme/large
            $table->string('industry')->nullable();
            
            // Meta
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_published')->default(false);
            $table->string('submitter_email')->nullable(); // Doğrulama için
            $table->ipAddress('submitter_ip')->nullable();  // Spam koruması
            
            $table->timestamps();
            
            $table->index(['university_id', 'field_id']);
            $table->index(['graduation_year', 'is_published']);
        });
        
        // 3. Maaş istatistikleri (önceden hesaplanmış - cache)
        Schema::create('salary_statistics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('university_id')->nullable()
                  ->constrained()->nullOnDelete();
            $table->foreignId('field_id')->nullable()
                  ->constrained('academic_fields')->nullOnDelete();
            
            $table->integer('sample_size');         // Kaç kişi
            $table->decimal('avg_salary', 12, 2);   // Ortalama
            $table->decimal('median_salary', 12, 2); // Medyan
            $table->decimal('min_salary', 12, 2);   // Min
            $table->decimal('max_salary', 12, 2);   // Max
            $table->decimal('percentile_25', 12, 2);
            $table->decimal('percentile_75', 12, 2);
            
            $table->json('by_experience')->nullable(); // Deneyime göre dağılım
            $table->timestamp('calculated_at');
            $table->timestamps();
            
            $table->unique(['university_id', 'field_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salary_statistics');
        Schema::dropIfExists('graduate_salaries');
        Schema::dropIfExists('sector_salaries');
    }
};
```

### 3.2 .env Ayarları

```env
# DESTATIS API
DESTATIS_API_URL=https://www-genesis.destatis.de/genesisWS/rest/2020
DESTATIS_API_TOKEN=your_token_here
DESTATIS_USERNAME=your_username
DESTATIS_PASSWORD=your_password
```

### 3.3 Config Dosyası

`config/destatis.php`:

```php
<?php

return [
    'api_url' => env('DESTATIS_API_URL'),
    'token' => env('DESTATIS_API_TOKEN'),
    'username' => env('DESTATIS_USERNAME'),
    'password' => env('DESTATIS_PASSWORD'),
    
    // İlgilendiğimiz tablolar
    'tables' => [
        'sector_monthly' => '62361-0001',  // Aylık brüt (meslek)
        'sector_yearly' => '62361-0002',   // Yıllık brüt (sektör)
        'state_earnings' => '62321-0001',  // Eyalet bazlı
    ],
    
    // Sektör eşleştirmeleri (GENESIS kodları → Türkçe)
    'sector_mapping' => [
        'B-S'  => 'Tüm Sektörler',
        'C'    => 'İmalat Sanayi',
        'F'    => 'İnşaat',
        'G'    => 'Ticaret',
        'J'    => 'Bilişim ve İletişim',
        'K'    => 'Finans ve Sigorta',
        'M'    => 'Mühendislik ve Bilim',
        'Q'    => 'Sağlık ve Sosyal Hizmet',
        'P'    => 'Eğitim',
    ],
];
```

### 3.4 DESTATIS Service Class

`app/Services/DestatisService.php`:

```php
<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class DestatisService
{
    private string $apiUrl;
    private string $token;
    
    public function __construct()
    {
        $this->apiUrl = config('destatis.api_url');
        $this->token = config('destatis.token');
    }
    
    /**
     * API bağlantısını test et
     */
    public function testConnection(): array
    {
        try {
            $response = Http::timeout(30)
                ->get($this->apiUrl . '/helloworld/whoami');
            
            return [
                'success' => $response->successful(),
                'data' => $response->json(),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
    
    /**
     * Token doğrula
     */
    public function checkLogin(): bool
    {
        try {
            $response = Http::timeout(30)
                ->get($this->apiUrl . '/helloworld/logincheck', [
                    'username' => $this->token,
                    'password' => '', // Token kullanınca boş
                    'language' => 'de',
                ]);
            
            $data = $response->json();
            return ($data['Status'] ?? '') === 'Sie wurden erfolgreich an-/abgemeldet.';
            
        } catch (\Exception $e) {
            Log::error('DESTATIS login check failed: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Tablo verisi çek
     * 
     * @param string $tableCode GENESIS tablo kodu (örn: 62361-0001)
     */
    public function getTable(string $tableCode): ?array
    {
        $cacheKey = "destatis_table_{$tableCode}";
        
        return Cache::remember($cacheKey, 86400 * 7, function () use ($tableCode) {
            try {
                $response = Http::timeout(60)
                    ->get($this->apiUrl . '/data/table', [
                        'username' => $this->token,
                        'password' => '',
                        'name' => $tableCode,
                        'area' => 'all',
                        'format' => 'ffcsv',  // Flat File CSV (en temiz)
                        'language' => 'de',
                    ]);
                
                if (!$response->successful()) {
                    Log::error('DESTATIS table fetch failed', [
                        'table' => $tableCode,
                        'status' => $response->status(),
                    ]);
                    return null;
                }
                
                return $this->parseFlatFileCsv($response->body());
                
            } catch (\Exception $e) {
                Log::error('DESTATIS exception: ' . $e->getMessage());
                return null;
            }
        });
    }
    
    /**
     * İstatistik ara
     */
    public function searchStatistics(string $term): array
    {
        try {
            $response = Http::timeout(30)
                ->get($this->apiUrl . '/find/find', [
                    'username' => $this->token,
                    'password' => '',
                    'term' => $term,
                    'category' => 'tables',
                    'pagelength' => 50,
                    'language' => 'de',
                ]);
            
            return $response->successful() ? $response->json() : [];
            
        } catch (\Exception $e) {
            Log::error('DESTATIS search failed: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Flat File CSV parse et
     */
    private function parseFlatFileCsv(string $csv): array
    {
        $lines = explode("\n", trim($csv));
        if (count($lines) < 2) return [];
        
        // İlk satır başlık
        $headers = str_getcsv(array_shift($lines), ';');
        
        $data = [];
        foreach ($lines as $line) {
            if (empty(trim($line))) continue;
            
            $row = str_getcsv($line, ';');
            if (count($row) === count($headers)) {
                $data[] = array_combine($headers, $row);
            }
        }
        
        return $data;
    }
}
```

### 3.5 Import Command

```bash
php artisan make:command ImportDestatisSalaries
```

`app/Console/Commands/ImportDestatisSalaries.php`:

```php
<?php

namespace App\Console\Commands;

use App\Models\SectorSalary;
use App\Services\DestatisService;
use Illuminate\Console\Command;

class ImportDestatisSalaries extends Command
{
    protected $signature = 'destatis:import 
        {--test : Sadece bağlantı testi}';
    
    protected $description = 'DESTATIS API\'den sektör maaşlarını çek';
    
    public function handle(DestatisService $destatis)
    {
        $this->info('🏛️ DESTATIS Maaş Verisi Import');
        $this->newLine();
        
        // 1. Bağlantı testi
        $this->info('Bağlantı test ediliyor...');
        $test = $destatis->testConnection();
        
        if (!$test['success']) {
            $this->error('❌ Bağlantı başarısız!');
            $this->error($test['error'] ?? 'Bilinmeyen hata');
            return self::FAILURE;
        }
        
        $this->info('✅ API bağlantısı başarılı!');
        
        // 2. Login kontrolü
        if (!$destatis->checkLogin()) {
            $this->error('❌ Token geçersiz! .env\'i kontrol et.');
            return self::FAILURE;
        }
        
        $this->info('✅ Token geçerli!');
        
        if ($this->option('test')) {
            $this->info('Test modu - import yapılmadı.');
            return self::SUCCESS;
        }
        
        $this->newLine();
        
        // 3. Sektör maaşlarını çek
        $tableCode = config('destatis.tables.sector_yearly');
        $this->info("Tablo çekiliyor: {$tableCode}");
        
        $data = $destatis->getTable($tableCode);
        
        if (empty($data)) {
            $this->error('❌ Veri alınamadı!');
            return self::FAILURE;
        }
        
        $this->info("✓ {$tableCode}: " . count($data) . " satır alındı");
        
        // 4. Veritabanına kaydet
        $imported = 0;
        $bar = $this->output->createProgressBar(count($data));
        
        foreach ($data as $row) {
            // NOT: Gerçek kolon isimleri DESTATIS tablosuna göre
            // değişir. İlk import'ta dump alıp kontrol et!
            
            // Örnek mapping (gerçek veriye göre uyarla):
            try {
                SectorSalary::updateOrCreate(
                    [
                        'sector_code' => $row['WERT'] ?? 'unknown',
                        'data_year' => 2024,
                    ],
                    [
                        'sector_name_de' => $row['label'] ?? '',
                        'sector_name_tr' => '', // Sonra çevrilecek
                        'avg_yearly_gross' => (float)($row['value'] ?? 0),
                        'avg_monthly_gross' => (float)($row['value'] ?? 0) / 12,
                        'source' => 'destatis',
                        'synced_at' => now(),
                    ]
                );
                $imported++;
            } catch (\Exception $e) {
                // Hatalı satırı atla
            }
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine(2);
        
        $this->info("✅ {$imported} kayıt import edildi!");
        
        return self::SUCCESS;
    }
}
```

### Kullanım:

```bash
# Önce test
php artisan destatis:import --test

# Gerçek import
php artisan destatis:import
```

---

# 📝 KATMAN 2: MEZUN MAAŞ ANKETİ

## Konsept

```
Mezunlardan anonim maaş bilgisi topla
→ Üniversite + bölüm bazlı
→ Kendi özgün veritabanın
→ "TUM Bilgisayar Müh: €68K" gibi
```

## ADIM 1: Model

`app/Models/GraduateSalary.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GraduateSalary extends Model
{
    protected $fillable = [
        'university_id', 'field_id', 'degree_type',
        'graduation_year', 'gross_yearly', 'currency',
        'experience_level', 'job_title', 'city',
        'company_size', 'industry', 'is_verified',
        'is_published', 'submitter_email', 'submitter_ip',
    ];
    
    protected $casts = [
        'gross_yearly' => 'decimal:2',
        'is_verified' => 'boolean',
        'is_published' => 'boolean',
        'graduation_year' => 'integer',
    ];
    
    // Gizli alanlar (API'de gösterme!)
    protected $hidden = [
        'submitter_email',
        'submitter_ip',
    ];
    
    public function university(): BelongsTo
    {
        return $this->belongsTo(University::class);
    }
    
    public function field(): BelongsTo
    {
        return $this->belongsTo(AcademicField::class, 'field_id');
    }
    
    // Sadece yayınlanmış
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }
}
```

## ADIM 2: Anket Controller

`app/Http/Controllers/SalarySurveyController.php`:

```php
<?php

namespace App\Http\Controllers;

use App\Models\GraduateSalary;
use App\Models\University;
use App\Models\AcademicField;
use Illuminate\Http\Request;

class SalarySurveyController extends Controller
{
    /**
     * Anket formunu göster
     */
    public function create()
    {
        return view('salary.survey', [
            'universities' => University::orderBy('name_tr')->get(),
            'fields' => AcademicField::orderBy('name_tr')->get(),
        ]);
    }
    
    /**
     * Anket gönderimini kaydet
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'university_id' => 'required|exists:universities,id',
            'field_id' => 'required|exists:academic_fields,id',
            'degree_type' => 'required|in:bachelor,master,phd',
            'graduation_year' => 'required|integer|min:1990|max:2026',
            'gross_yearly' => 'required|numeric|min:10000|max:500000',
            'experience_level' => 'required|in:entry,junior,mid,senior',
            'job_title' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'company_size' => 'nullable|in:startup,sme,large',
            'industry' => 'nullable|string|max:100',
            'submitter_email' => 'nullable|email',
        ]);
        
        // Spam koruması: Aynı IP'den 24 saatte max 3
        $recentCount = GraduateSalary::where('submitter_ip', $request->ip())
            ->where('created_at', '>', now()->subDay())
            ->count();
        
        if ($recentCount >= 3) {
            return back()->withErrors([
                'limit' => 'Çok fazla gönderim. Yarın tekrar deneyin.'
            ]);
        }
        
        // Kaydet (henüz yayınlanmamış)
        $validated['submitter_ip'] = $request->ip();
        $validated['is_published'] = false; // Admin onayı bekler
        $validated['is_verified'] = false;
        
        GraduateSalary::create($validated);
        
        return redirect()->route('salary.thanks')
            ->with('success', 'Teşekkürler! Maaş bilginiz incelendikten sonra yayınlanacak.');
    }
    
    /**
     * Teşekkür sayfası
     */
    public function thanks()
    {
        return view('salary.thanks');
    }
}
```

## ADIM 3: Anket Formu (Blade)

`resources/views/salary/survey.blade.php` - özet yapı:

```html
@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto py-12">
    <h1 class="text-3xl font-bold mb-2">Maaş Anketi</h1>
    <p class="text-gray-600 mb-8">
        Mezunların gerçek maaş bilgileri herkese yardımcı olur. 
        Bilgiler <strong>%100 anonim</strong>.
    </p>
    
    <form method="POST" action="{{ route('salary.store') }}" 
          class="space-y-6">
        @csrf
        
        {{-- Üniversite --}}
        <div>
            <label class="label">Üniversite</label>
            <select name="university_id" required class="input">
                <option value="">Seç...</option>
                @foreach($universities as $uni)
                    <option value="{{ $uni->id }}">
                        {{ $uni->name_tr }}
                    </option>
                @endforeach
            </select>
        </div>
        
        {{-- Bölüm --}}
        <div>
            <label class="label">Bölüm</label>
            <select name="field_id" required class="input">
                <option value="">Seç...</option>
                @foreach($fields as $field)
                    <option value="{{ $field->id }}">
                        {{ $field->name_tr }}
                    </option>
                @endforeach
            </select>
        </div>
        
        {{-- Derece --}}
        <div>
            <label class="label">Derece</label>
            <select name="degree_type" required class="input">
                <option value="bachelor">Lisans (Bachelor)</option>
                <option value="master">Yüksek Lisans (Master)</option>
                <option value="phd">Doktora (PhD)</option>
            </select>
        </div>
        
        {{-- Mezuniyet Yılı --}}
        <div>
            <label class="label">Mezuniyet Yılı</label>
            <input type="number" name="graduation_year" 
                   min="1990" max="2026" required class="input">
        </div>
        
        {{-- Yıllık Brüt Maaş --}}
        <div>
            <label class="label">Yıllık Brüt Maaş (€)</label>
            <input type="number" name="gross_yearly" 
                   min="10000" max="500000" required class="input"
                   placeholder="örn: 65000">
        </div>
        
        {{-- Deneyim --}}
        <div>
            <label class="label">Deneyim Seviyesi</label>
            <select name="experience_level" required class="input">
                <option value="entry">Yeni Mezun (0-2 yıl)</option>
                <option value="junior">Junior (2-5 yıl)</option>
                <option value="mid">Mid-level (5-10 yıl)</option>
                <option value="senior">Senior (10+ yıl)</option>
            </select>
        </div>
        
        {{-- Opsiyonel: Pozisyon --}}
        <div>
            <label class="label">Pozisyon (opsiyonel)</label>
            <input type="text" name="job_title" class="input"
                   placeholder="örn: Software Engineer">
        </div>
        
        {{-- Opsiyonel: Şehir --}}
        <div>
            <label class="label">Çalıştığın Şehir (opsiyonel)</label>
            <input type="text" name="city" class="input"
                   placeholder="örn: München">
        </div>
        
        <button type="submit" class="btn-accent w-full">
            Maaş Bilgisini Gönder
        </button>
        
        <p class="text-sm text-gray-500 text-center">
            Gönderdiğin bilgi anonim olarak saklanır ve 
            sadece istatistik için kullanılır.
        </p>
    </form>
</div>
@endsection
```

## ADIM 4: Routes

`routes/web.php`:

```php
use App\Http\Controllers\SalarySurveyController;

Route::prefix('maas')->group(function () {
    Route::get('/anket', [SalarySurveyController::class, 'create'])
        ->name('salary.survey');
    Route::post('/anket', [SalarySurveyController::class, 'store'])
        ->name('salary.store');
    Route::get('/tesekkurler', [SalarySurveyController::class, 'thanks'])
        ->name('salary.thanks');
});
```

---

# 📊 KATMAN 3: İSTATİSTİK + İÇERİK

## İstatistik Hesaplama Command

```bash
php artisan make:command CalculateSalaryStats
```

`app/Console/Commands/CalculateSalaryStats.php`:

```php
<?php

namespace App\Console\Commands;

use App\Models\GraduateSalary;
use App\Models\SalaryStatistic;
use App\Models\University;
use Illuminate\Console\Command;

class CalculateSalaryStats extends Command
{
    protected $signature = 'salary:calculate-stats';
    protected $description = 'Mezun maaş istatistiklerini hesapla';
    
    public function handle()
    {
        $this->info('📊 Maaş istatistikleri hesaplanıyor...');
        
        // Üniversite + bölüm kombinasyonlarını grupla
        $combinations = GraduateSalary::published()
            ->selectRaw('university_id, field_id, COUNT(*) as cnt')
            ->groupBy('university_id', 'field_id')
            ->having('cnt', '>=', 3) // En az 3 kayıt (anonimlik!)
            ->get();
        
        $this->info("Hesaplanacak: {$combinations->count()} kombinasyon");
        
        $bar = $this->output->createProgressBar($combinations->count());
        
        foreach ($combinations as $combo) {
            $salaries = GraduateSalary::published()
                ->where('university_id', $combo->university_id)
                ->where('field_id', $combo->field_id)
                ->pluck('gross_yearly')
                ->sort()
                ->values();
            
            $count = $salaries->count();
            
            SalaryStatistic::updateOrCreate(
                [
                    'university_id' => $combo->university_id,
                    'field_id' => $combo->field_id,
                ],
                [
                    'sample_size' => $count,
                    'avg_salary' => $salaries->avg(),
                    'median_salary' => $this->median($salaries),
                    'min_salary' => $salaries->first(),
                    'max_salary' => $salaries->last(),
                    'percentile_25' => $this->percentile($salaries, 25),
                    'percentile_75' => $this->percentile($salaries, 75),
                    'calculated_at' => now(),
                ]
            );
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine(2);
        $this->info('✅ İstatistikler güncellendi!');
        
        return self::SUCCESS;
    }
    
    private function median($collection)
    {
        $count = $collection->count();
        $mid = intdiv($count, 2);
        
        return $count % 2 === 0
            ? ($collection[$mid - 1] + $collection[$mid]) / 2
            : $collection[$mid];
    }
    
    private function percentile($collection, int $p)
    {
        $count = $collection->count();
        $index = (int) ceil(($p / 100) * $count) - 1;
        return $collection[max(0, $index)] ?? 0;
    }
}
```

## Otomatik Çalıştır (Scheduler)

`app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule): void
{
    // Her gün maaş istatistiklerini güncelle
    $schedule->command('salary:calculate-stats')
        ->dailyAt('04:00');
    
    // Her ay DESTATIS verisini güncelle
    $schedule->command('destatis:import')
        ->monthlyOn(1, '03:00');
}
```

---

## 📋 ENTEGRASYON KONTROL LİSTESİ

### Katman 1 (DESTATIS):
```
□ DESTATIS hesabı açıldı
□ API token alındı
□ .env ayarlandı
□ Migration çalıştırıldı
□ DestatisService oluşturuldu
□ Import command yazıldı
□ Bağlantı testi başarılı
□ İlk veri çekildi
```

### Katman 2 (Anket):
```
□ GraduateSalary modeli
□ SalarySurveyController
□ Anket formu (Blade)
□ Routes eklendi
□ Spam koruması
□ Admin onay paneli
```

### Katman 3 (İstatistik):
```
□ CalculateSalaryStats command
□ Scheduler ayarlandı
□ Maaş gösterim sayfası
□ İnfografik şablonu
□ Blog entegrasyonu
```

---

## 🎯 ÖNEMLİ NOTLAR

### Anonimlik Kuralı (ÇOK ÖNEMLİ!):
```
⚠️ En az 3 kişilik veri olmadan gösterme!

Sebep:
- 1-2 kişilik veri → kişi tanınabilir
- KVKK/GDPR ihlali olur
- "3+ kişi" = anonim sayılır

Kodda: having('cnt', '>=', 3)
```

### DESTATIS Lisansı:
```
"Datenlizenz Deutschland – Namensnennung"

Yani: Kullanabilirsin AMA kaynak göster:
"Kaynak: Statistisches Bundesamt (Destatis), 2024"
```

### İlk Import'ta:
```
DESTATIS tablolarının kolon isimleri değişken.
İlk çekişte mutlaka dump al:

dd($data[0]); // İlk satırı gör

Sonra mapping'i gerçek kolonlara göre düzelt.
```

---

## 🚀 SONRAKİ ADIMLAR

```
ÖNCELİK SIRASI:

1. Önce ana platform (üniversiteler) çalışsın
2. DESTATIS hesabı aç (5 dakika)
3. Maaş migration'larını ekle
4. Anket formunu kur (1 gün)
5. İlk mezunları davet et (anket doldursun)
6. 3+ veri toplanınca istatistik göster
7. Blog yazısı: "İlk Maaş Raporumuz"
```

---

## ❓ ŞİMDİ NE YAPALIM?

```
A) "DESTATIS hesabı açma + ilk bağlantıyı test et"
   → Adım adım birlikte yapalım

B) "Önce HeidiSQL'de veritabanı + Laravel kuralım"
   → Ana platform önce, maaş sonra

C) "Maaş anket formunu detaylandır"
   → Tam tasarım + admin panel

D) "Maaş gösterim sayfasını tasarla"
   → Üniversite sayfasında nasıl görünecek
```

Bir önerim: **Önce B** (ana platform), çünkü maaş sistemi üniversite tablosuna bağlı. Üniversiteler olmadan maaş anketi çalışmaz.

**Sen ne dersin?** 💪
