<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * 🛠️ Operasyonlar — sık kullanılan bakım/içerik işlemleri tek tık. URL yazmaya gerek yok.
 * Her buton ilgili artisan komutunu çalıştırır, çıktıyı bildirimde gösterir. Sadece admin.
 */
class OpsCenter extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-wrench-screwdriver';
    protected static ?string $navigationLabel = '🛠️ Operasyonlar';
    protected static ?string $title = 'Operasyonlar';
    protected static ?int $navigationSort = 1;
    protected static string|\UnitEnum|null $navigationGroup = 'Sistem';

    protected string $view = 'filament.pages.ops-center';

    public static function canAccess(): bool
    {
        return auth()->user()?->is_admin === true;
    }

    /** Dashboard'da gösterilecek operasyonlar: [key, emoji, başlık, açıklama, onay-metni]. */
    public const GROUPS = [
        'İçerik & Çeviri' => [
            ['news-fetch', '📰', 'Haber Çek', 'Aktif RSS kaynaklardan yeni haber adayı çeker (gelen kutusuna düşer).', 'Tüm aktif kaynaklardan haber çekilsin mi?'],
            ['resolve-links', '🔗', 'İç Linkleri Çöz', 'Yazılardaki iç linkleri gerçek yazıya bağlar; hedefi yoksa düz metne indirir (404 biter).', 'Tüm yazıların iç linkleri çözümlensin mi?'],
            ['translate-missing', '🌐', 'Eksik Çevirileri Tamamla', 'EN+DE çevirisi eksik TR yazıları Gemini ile çevirir.', 'Eksik çeviriler tamamlansın mı? (Gemini token harcar)'],
            ['seed-culture', '🎨', 'Kültür Brief Seed', '"Almanya\'da Yaşam & Kültür" taslak briefleri ekler (mevcut atlanır).', 'Kültür briefleri seed edilsin mi?'],
        ],
        'Bakım & Sistem' => [
            ['cache-clear', '🧹', 'Cache Temizle', 'config / route / view / uygulama cache\'ini temizler.', 'Tüm cache temizlensin mi?'],
            ['og-cache', '🖼️', 'OG Cache Temizle', 'Sosyal paylaşım görsellerini siler; sonraki istekte yeni fontla üretilir.', 'OG görsel cache\'i silinsin mi?'],
            ['migrate', '🗃️', 'Migration Çalıştır', 'Bekleyen DB migration\'larını uygular (deploy sonrası).', 'Bekleyen migration\'lar çalıştırılsın mı?'],
        ],
    ];

    public function runOp(string $key): void
    {
        abort_unless(auth()->user()?->is_admin, 403);
        @set_time_limit(180);

        try {
            $out = match ($key) {
                'news-fetch'        => $this->artisan('news:fetch', ['--max-seconds' => 35]),
                'resolve-links'     => $this->artisan('content:resolve-post-links', []),
                'translate-missing' => $this->artisan('content:translate-posts', ['--all-untranslated' => true, '--sleep' => 0]),
                'seed-culture'      => $this->artisan('content:seed-culture-briefs', ['--skip-existing' => true]),
                'migrate'           => $this->artisan('migrate', ['--force' => true, '--no-interaction' => true]),
                'cache-clear'       => $this->clearCaches(),
                'og-cache'          => $this->clearOg(),
                default             => throw new \InvalidArgumentException('Bilinmeyen işlem: ' . $key),
            };

            Notification::make()
                ->title('✅ Çalıştı')
                ->body(Str::limit(trim($out) !== '' ? trim($out) : 'Tamamlandı.', 800))
                ->success()->persistent()->send();
        } catch (\Throwable $e) {
            Notification::make()->title('❌ Hata')->body(Str::limit($e->getMessage(), 400))->danger()->persistent()->send();
        }
    }

    private function artisan(string $cmd, array $args): string
    {
        Artisan::call($cmd, $args);
        return Artisan::output();
    }

    private function clearCaches(): string
    {
        $out = '';
        foreach (['config:clear', 'route:clear', 'view:clear', 'cache:clear'] as $c) {
            Artisan::call($c);
            $out .= $c . ': ' . trim(Artisan::output()) . "\n";
        }
        return $out;
    }

    private function clearOg(): string
    {
        $dir = storage_path('app/public/og');
        $files = is_dir($dir) ? File::allFiles($dir) : [];
        foreach ($files as $f) {
            @unlink($f->getPathname());
        }
        return count($files) . ' OG PNG silindi (yeniden üretilecekler).';
    }
}
