<?php

namespace App\Filament\Pages;

use App\Models\ContentAsset;
use App\Models\Setting;
use App\Models\User;
use App\Services\Content\BlogPublisher;
use App\Services\Social\Drivers\ManualPublisher;
use App\Services\Social\PlatformMap;
use App\Services\Social\PublisherManager;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

/**
 * 📡 Yayın Merkezi — üretilen TÜM asset'leri (blog + sosyal) tek yerden yayına sürer.
 * Blog: tek tık TR(+EN+DE) yayın (BlogPublisher). Sosyal: manuel-asistan veya Ayrshare.
 * Toplu aksiyonlar: blog toplu yayınla, sosyal toplu işaretle/otomatik paylaş.
 */
class PublishCockpit extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-rocket-launch';
    protected static ?string $navigationLabel = '📡 Yayın Merkezi';
    protected static ?string $title = 'Yayın Merkezi — Blog + Sosyal';
    protected static ?int $navigationSort = 22;
    protected static string|\UnitEnum|null $navigationGroup = 'İçerik';

    protected string $view = 'filament.pages.publish-cockpit';

    /** Bu sayfada yönetilen tüm asset türleri (blog + sosyal). */
    private static function managedTypes(): array
    {
        return array_merge(['blog'], PlatformMap::SOCIAL);
    }

    private static function typeLabel(string $type): string
    {
        return $type === 'blog' ? '📝 Blog' : PlatformMap::label($type);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                ContentAsset::query()
                    ->whereIn('asset_type', self::managedTypes())
                    ->whereNotNull('body_md')
                    ->with('brief')
            )
            ->defaultSort('updated_at', 'desc')
            ->columns([
                TextColumn::make('asset_type')
                    ->label('Tür / Platform')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => self::typeLabel($state)),
                TextColumn::make('brief.title')
                    ->label('İçerik')
                    ->limit(45)
                    ->wrap()
                    ->tooltip(fn (ContentAsset $record) => $record->brief?->title),
                TextColumn::make('language')
                    ->label('Dil')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => ContentAsset::LANGUAGES[$state] ?? $state),
                TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => ContentAsset::STATUSES[$state] ?? $state)
                    ->color(fn (string $state) => match ($state) {
                        'ready' => 'info', 'scheduled' => 'warning', 'published' => 'success', default => 'gray',
                    }),
                TextColumn::make('media')
                    ->label('Görsel')
                    ->formatStateUsing(fn ($state) => is_array($state) && count($state) ? count($state) . ' görsel' : '—'),
                TextColumn::make('published_url')
                    ->label('Link')
                    ->placeholder('—')
                    ->url(fn (ContentAsset $record) => $record->published_url, true)
                    ->limit(30)
                    ->color('primary'),
                TextColumn::make('updated_at')->label('Güncellendi')->dateTime('d.m H:i')->sortable(),
            ])
            ->filters([
                SelectFilter::make('kind')
                    ->label('Tür')
                    ->options(['blog' => '📝 Blog', 'social' => '📱 Sosyal'])
                    ->query(fn (Builder $query, array $data) => match ($data['value'] ?? null) {
                        'blog'   => $query->where('asset_type', 'blog'),
                        'social' => $query->whereIn('asset_type', PlatformMap::SOCIAL),
                        default  => $query,
                    }),
                SelectFilter::make('asset_type')->label('Platform')
                    ->options(collect(self::managedTypes())->mapWithKeys(fn ($t) => [$t => self::typeLabel($t)])->all()),
                SelectFilter::make('status')->label('Durum')->options(ContentAsset::STATUSES),
            ])
            ->recordActions([
                // ── BLOG: tek tık TR(+EN+DE) yayın ──
                Action::make('publishBlog')
                    ->label('📤 Blog\'a Aktar')
                    ->color('success')
                    ->visible(fn (ContentAsset $record) => $record->asset_type === 'blog')
                    ->schema(self::blogPublishForm())
                    ->modalHeading('Blog yazısına aktar')
                    ->action(function (ContentAsset $record, array $data) {
                        @set_time_limit(180);
                        $res = app(BlogPublisher::class)->publish($record, [
                            'go_live'   => (bool) ($data['go_live'] ?? true),
                            'author_id' => $data['author_id'] ?? null,
                            'translate' => (bool) ($data['translate'] ?? true),
                        ]);
                        self::notifyBlogResult($res, (bool) ($data['go_live'] ?? true));
                    }),

                // ── SOSYAL: paylaşıma hazırla (asistan modal) ──
                Action::make('share')
                    ->label('📤 Paylaş')
                    ->color('info')
                    ->visible(fn (ContentAsset $record) => PlatformMap::isSocial($record->asset_type))
                    ->modalHeading(fn (ContentAsset $record) => self::typeLabel($record->asset_type) . ' — paylaşıma hazır')
                    ->modalContent(fn (ContentAsset $record) => view('filament.social.share-modal', [
                        'share' => app(ManualPublisher::class)->publish($record, url('/'))['share'] ?? null,
                    ]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Kapat'),

                // ── SOSYAL: otomatik (Ayrshare aktifse) ──
                Action::make('autoPublish')
                    ->label('🤖 Otomatik Paylaş')
                    ->color('success')
                    ->visible(fn (ContentAsset $record) => PlatformMap::isSocial($record->asset_type) && app(PublisherManager::class)->isAutomaticActive())
                    ->requiresConfirmation()
                    ->action(function (ContentAsset $record) {
                        $res = app(PublisherManager::class)->active()->publish($record, url('/'));
                        if ($res['success'] ?? false) {
                            $record->update(['status' => 'published', 'published_url' => $res['url'] ?? null, 'published_at' => now()]);
                            Notification::make()->title('✅ Paylaşıldı')->body($res['message'] ?? '')->success()->send();
                        } else {
                            Notification::make()->title('❌ Paylaşılamadı')->body($res['message'] ?? '')->danger()->persistent()->send();
                        }
                    }),

                // ── SOSYAL: paylaştıktan sonra linki kaydet ──
                Action::make('markPublished')
                    ->label('✓ Paylaşıldı')
                    ->color('gray')
                    ->visible(fn (ContentAsset $record) => PlatformMap::isSocial($record->asset_type) && $record->status !== 'published')
                    ->schema([
                        TextInput::make('published_url')->label('Yayın linki (opsiyonel)')->url()->placeholder('https://...'),
                    ])
                    ->action(function (ContentAsset $record, array $data) {
                        $record->update(['status' => 'published', 'published_url' => $data['published_url'] ?? null, 'published_at' => now()]);
                        Notification::make()->title('✓ Paylaşıldı olarak işaretlendi')->success()->send();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    // ── BLOG TOPLU YAYINLA (zaman bütçeli çeviri) ──
                    BulkAction::make('bulkPublishBlog')
                        ->label('📤 Blog Toplu Yayınla')
                        ->color('success')
                        ->icon('heroicon-o-paper-airplane')
                        ->schema([
                            Toggle::make('go_live')->label('Hemen yayınla (canlı)')->default(true),
                            Toggle::make('translate')->label('EN + DE\'ye çevir (zaman bütçeli)')->default(true)
                                ->helperText('Çeviri ağır; ~35 sn bütçe dolunca kalanlar çevrilmeden yayınlanır — sonra "Eksik çevirileri tamamla".'),
                        ])
                        ->action(function (Collection $records, array $data) {
                            @set_time_limit(180);
                            $blog = $records->where('asset_type', 'blog');
                            $started = microtime(true);
                            $pub = 0; $tr = 0; $fail = 0;
                            foreach ($blog as $asset) {
                                $canTranslate = (bool) ($data['translate'] ?? false) && (microtime(true) - $started < 35);
                                $res = app(BlogPublisher::class)->publish($asset, [
                                    'go_live'   => (bool) ($data['go_live'] ?? true),
                                    'translate' => $canTranslate,
                                ]);
                                if ($res['ok'] ?? false) { $pub++; $tr += count($res['translated'] ?? []); }
                                else { $fail++; }
                            }
                            Notification::make()
                                ->title("📤 {$pub} blog yayınlandı · {$tr} çeviri" . ($fail ? " · {$fail} hata" : ''))
                                ->body($blog->count() < $records->count() ? 'Yalnız blog asset\'leri işlendi.' : '')
                                ->success()->persistent()->send();
                        }),

                    // ── SOSYAL: TOPLU "PAYLAŞILDI" İŞARETLE ──
                    BulkAction::make('bulkMarkPublished')
                        ->label('✓ Sosyal: Paylaşıldı işaretle')
                        ->color('gray')
                        ->icon('heroicon-o-check')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            $n = 0;
                            foreach ($records->filter(fn ($a) => PlatformMap::isSocial($a->asset_type)) as $a) {
                                $a->update(['status' => 'published', 'published_at' => now()]);
                                $n++;
                            }
                            Notification::make()->title("✓ {$n} sosyal asset paylaşıldı işaretlendi")->success()->send();
                        }),

                    // ── SOSYAL: TOPLU OTOMATİK PAYLAŞ (Ayrshare) ──
                    BulkAction::make('bulkAutoPublish')
                        ->label('🤖 Sosyal: Toplu Otomatik Paylaş')
                        ->color('info')
                        ->icon('heroicon-o-bolt')
                        ->visible(fn () => app(PublisherManager::class)->isAutomaticActive())
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            @set_time_limit(180);
                            $mgr = app(PublisherManager::class);
                            $started = microtime(true); $ok = 0; $fail = 0; $left = 0;
                            foreach ($records->filter(fn ($a) => PlatformMap::isSocial($a->asset_type)) as $a) {
                                if (microtime(true) - $started >= 35) { $left++; continue; }
                                $res = $mgr->active()->publish($a, url('/'));
                                if ($res['success'] ?? false) {
                                    $a->update(['status' => 'published', 'published_url' => $res['url'] ?? null, 'published_at' => now()]);
                                    $ok++;
                                } else { $fail++; }
                            }
                            Notification::make()
                                ->title("🤖 {$ok} paylaşıldı" . ($fail ? " · {$fail} hata" : '') . ($left ? " · {$left} kaldı (tekrar bas)" : ''))
                                ->color($fail ? 'warning' : 'success')->persistent()->send();
                        }),
                ]),
            ]);
    }

    /** Blog "Aktar" formu — yazar + yayın + çeviri. */
    private static function blogPublishForm(): array
    {
        return [
            Select::make('author_id')
                ->label('Yazar')
                ->options(fn () => User::orderBy('name')->pluck('name', 'id'))
                ->default(fn (ContentAsset $record) => $record->brief?->author_id ?? auth()->id())
                ->searchable()
                ->required(),
            Toggle::make('go_live')->label('Hemen yayınla (canlı)')->default(true),
            Toggle::make('translate')->label('EN + DE\'ye çevir & yayınla')->default(true),
        ];
    }

    private static function notifyBlogResult(array $res, bool $goLive): void
    {
        if (! ($res['ok'] ?? false)) {
            Notification::make()->title('❌ Aktarılamadı')->body($res['message'] ?? '')->danger()->persistent()->send();
            return;
        }
        if (! empty($res['warn'])) {
            Notification::make()->title('⚠️ TR yayında ama çeviri eksik')->body($res['warn'])->warning()->persistent()->send();
        }
        $langNote = ! empty($res['translated']) ? ' + ' . implode(' & ', $res['translated']) . ' çevrildi' : '';
        Notification::make()
            ->title($goLive ? '✅ Yayında!' . $langNote : '📝 Taslak Post oluşturuldu')
            ->body(($res['created'] ?? false ? 'Oluşturuldu' : 'Güncellendi') . ': ' . mb_substr($res['post']->title ?? '', 0, 50))
            ->success()->persistent()->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('settings')
                ->label('⚙️ Yayın Ayarları')
                ->color('gray')
                ->schema([
                    Select::make('social_publisher_driver')
                        ->label('Sosyal paylaşım sürücüsü')
                        ->options(PublisherManager::options())
                        ->default('manual')
                        ->required()
                        ->helperText('Manuel: API yok, sen basarsın. Ayrshare: key girilince otomatik paylaşır.'),
                    TextInput::make('ayrshare_api_key')
                        ->label('Ayrshare API Key')
                        ->password()
                        ->revealable()
                        ->helperText('ayrshare.com → hesap aç → sosyal profilleri bağla → API key. Boşsa manuel modda kalır.'),
                ])
                ->fillForm(fn () => [
                    'social_publisher_driver' => setting('social_publisher_driver', 'manual'),
                    'ayrshare_api_key'        => setting('ayrshare_api_key'),
                ])
                ->action(function (array $data) {
                    Setting::set('social_publisher_driver', $data['social_publisher_driver'] ?? 'manual', 'social');
                    Setting::set('ayrshare_api_key', ($data['ayrshare_api_key'] ?? null) ?: null, 'social');
                    Notification::make()->title('✅ Yayın ayarları kaydedildi')->success()->send();
                }),
        ];
    }
}
