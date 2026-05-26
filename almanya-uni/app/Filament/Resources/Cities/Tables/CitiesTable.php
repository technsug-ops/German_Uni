<?php

namespace App\Filament\Resources\Cities\Tables;

use App\Models\City;
use App\Services\Enrichment\CityEnrichmentService;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class CitiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image_url')
                    ->label('Görsel')
                    ->square()
                    ->size(48)
                    ->defaultImageUrl(fn () => 'data:image/svg+xml;base64,'.base64_encode('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48"><rect fill="#e5e7eb" width="48" height="48"/><text x="24" y="30" text-anchor="middle" font-size="20" fill="#9ca3af">🏙️</text></svg>')),

                TextColumn::make('name_de')
                    ->label('Ad')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold')
                    ->description(fn (City $r) => $r->name_tr !== $r->name_de ? $r->name_tr : null),

                TextColumn::make('state.name_de')
                    ->label('Eyalet')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('universities_count')
                    ->label('Üni')
                    ->counts(['universities' => fn ($q) => $q->where('is_active', 1)])
                    ->badge()
                    ->color('primary')
                    ->sortable(),

                TextColumn::make('population')
                    ->label('Nüfus')
                    ->numeric(thousandsSeparator: '.')
                    ->sortable()
                    ->toggleable(),

                IconColumn::make('has_content')
                    ->label('İçerik')
                    ->getStateUsing(fn (City $r) => !empty($r->content_blocks))
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-minus-circle')
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->tooltip(fn (City $r) => !empty($r->content_blocks) ? count($r->content_blocks) . ' blok' : 'Üretilmemiş'),

                TextColumn::make('last_enriched_at')
                    ->label('Son üretim')
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean()
                    ->toggleable(),
            ])
            ->filters([
                TernaryFilter::make('has_content')
                    ->label('İçerik üretilmiş')
                    ->placeholder('Tümü')
                    ->trueLabel('İçerik var')
                    ->falseLabel('İçerik yok')
                    ->queries(
                        true: fn ($query) => $query->whereNotNull('content_blocks'),
                        false: fn ($query) => $query->whereNull('content_blocks'),
                    ),
                TernaryFilter::make('has_image')
                    ->label('Kapak görseli')
                    ->placeholder('Tümü')
                    ->trueLabel('Görsel var')
                    ->falseLabel('Görsel yok')
                    ->queries(
                        true: fn ($query) => $query->whereNotNull('image_url'),
                        false: fn ($query) => $query->whereNull('image_url'),
                    ),
            ])
            ->defaultSort('name_de')
            ->recordActions([
                Action::make('enrichContent')
                    ->label('🪄 Sayfa Üret')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalDescription('Wikipedia + DB + Forum/Telegram topluluk verisi + AI ile sayfa içeriği üretilir (~10 sn, ~$0.003).')
                    ->action(function (City $record) {
                        $svc = app(CityEnrichmentService::class);
                        $r = $svc->enrich($record, true);
                        $comm = $r['sources']['community'] ?? [];
                        Notification::make()
                            ->title($r['success'] ? '🪄 ' . $r['blocks_count'] . ' blok üretildi' : '❌ Hata')
                            ->body($r['success']
                                ? "Wiki: [" . implode(',', $r['sources']['wikipedia_languages'] ?? []) . '] · '
                                    . "{$comm['tg_questions']}× tg soru · {$comm['forum_titles']}× forum başlık · "
                                    . ($r['sources']['uni_count'] ?? 0) . ' üni'
                                : ($r['error'] ?? 'Bilinmeyen hata'))
                            ->color($r['success'] ? 'success' : 'danger')
                            ->persistent()->send();
                    }),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('enrichBulk')
                        ->label('🪄 Toplu Sayfa Üret')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalDescription('Seçili tüm şehirler için Wikipedia + Community + AI ile içerik üretilir. Mevcut içerik üzerine yazılır.')
                        ->action(function (Collection $records) {
                            $svc = app(CityEnrichmentService::class);
                            $success = 0; $failed = 0;
                            foreach ($records as $r) {
                                try {
                                    $res = $svc->enrich($r, true);
                                    $res['success'] ? $success++ : $failed++;
                                } catch (\Throwable $e) {
                                    $failed++;
                                }
                            }
                            Notification::make()
                                ->title("Toplu üretim bitti: ✅ {$success} · ❌ {$failed}")
                                ->color($failed === 0 ? 'success' : 'warning')
                                ->send();
                        }),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
