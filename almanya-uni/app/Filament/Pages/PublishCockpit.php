<?php

namespace App\Filament\Pages;

use App\Models\ContentAsset;
use App\Models\Setting;
use App\Services\Social\Drivers\ManualPublisher;
use App\Services\Social\PlatformMap;
use App\Services\Social\PublisherManager;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

/**
 * 🚀 Yayın Kokpiti — üretilen sosyal asset'leri (Instagram/X/TikTok/LinkedIn/
 * Pinterest/YouTube) tek yerden paylaşıma sürer. Aktif sürücü manuel-asistan
 * (API'siz, ücretsiz) veya Ayrshare (otomatik); UI ikisinde de aynı.
 */
class PublishCockpit extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-rocket-launch';
    protected static ?string $navigationLabel = '🚀 Yayın Kokpiti';
    protected static ?string $title = 'Yayın Kokpiti — Sosyal Paylaşım';
    protected static ?int $navigationSort = 22;
    protected static string|\UnitEnum|null $navigationGroup = 'İçerik';

    protected string $view = 'filament.pages.publish-cockpit';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                ContentAsset::query()
                    ->whereIn('asset_type', PlatformMap::SOCIAL)
                    ->whereNotNull('body_md')
                    ->with('brief')
            )
            ->defaultSort('updated_at', 'desc')
            ->columns([
                TextColumn::make('asset_type')
                    ->label('Platform')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => PlatformMap::label($state)),
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
                SelectFilter::make('asset_type')->label('Platform')
                    ->options(collect(PlatformMap::SOCIAL)->mapWithKeys(fn ($t) => [$t => PlatformMap::label($t)])->all()),
                SelectFilter::make('status')->label('Durum')->options(ContentAsset::STATUSES),
            ])
            ->recordActions([
                // Elle-asistan: metin + medya + "platformda aç" (her sürücüde kullanılabilir)
                Action::make('share')
                    ->label('📤 Paylaş')
                    ->color('info')
                    ->modalHeading(fn (ContentAsset $record) => PlatformMap::label($record->asset_type) . ' — paylaşıma hazır')
                    ->modalContent(fn (ContentAsset $record) => view('filament.social.share-modal', [
                        'share' => app(ManualPublisher::class)->publish($record, url('/'))['share'] ?? null,
                    ]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Kapat'),

                // Otomatik (yalnız Ayrshare aktif + key varsa görünür)
                Action::make('autoPublish')
                    ->label('🤖 Otomatik Paylaş')
                    ->color('success')
                    ->visible(fn () => app(PublisherManager::class)->isAutomaticActive())
                    ->requiresConfirmation()
                    ->modalDescription('Aktif API sürücüsü (Ayrshare) ile bu asset doğrudan platforma paylaşılır.')
                    ->action(function (ContentAsset $record) {
                        $res = app(PublisherManager::class)->active()->publish($record, url('/'));
                        if ($res['success'] ?? false) {
                            $record->update([
                                'status'        => 'published',
                                'published_url' => $res['url'] ?? null,
                                'published_at'  => now(),
                            ]);
                            Notification::make()->title('✅ Paylaşıldı')->body($res['message'] ?? '')->success()->send();
                        } else {
                            Notification::make()->title('❌ Paylaşılamadı')->body($res['message'] ?? 'Bilinmeyen hata')->danger()->persistent()->send();
                        }
                    }),

                // Manuel akış: paylaştıktan sonra linki kaydet
                Action::make('markPublished')
                    ->label('✓ Paylaşıldı')
                    ->color('gray')
                    ->visible(fn (ContentAsset $record) => $record->status !== 'published')
                    ->schema([
                        TextInput::make('published_url')
                            ->label('Yayın linki (opsiyonel)')
                            ->url()
                            ->placeholder('https://...'),
                    ])
                    ->action(function (ContentAsset $record, array $data) {
                        $record->update([
                            'status'        => 'published',
                            'published_url' => $data['published_url'] ?? null,
                            'published_at'  => now(),
                        ]);
                        Notification::make()->title('✓ Paylaşıldı olarak işaretlendi')->success()->send();
                    }),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('settings')
                ->label('⚙️ Yayın Ayarları')
                ->color('gray')
                ->schema([
                    Select::make('social_publisher_driver')
                        ->label('Paylaşım sürücüsü')
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
