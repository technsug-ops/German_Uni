<?php

namespace App\Filament\Resources\Universities\Tables;

use App\Models\University;
use App\Services\Enrichment\UniversityEnrichmentService;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Illuminate\Database\Eloquent\Collection;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UniversitiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            // NOT: ->modifyQueryUsing(...->with()) v4'te tablo özet/filtre sorgusunu
            // bozuyordu (model null → newQueryWithoutRelationships, Filament #17275).
            // İlişki kolonlarını Filament zaten otomatik eager-load eder.
            ->defaultSort('name_de')
            ->columns([
                ImageColumn::make('logo_url')
                    ->label('Logo')
                    ->square()
                    ->defaultImageUrl(fn () => null),

                TextColumn::make('name_de')
                    ->label('İsim (DE)')
                    ->searchable(['name_de', 'name_en', 'name_tr', 'short_name'])
                    ->sortable()
                    ->limit(45)
                    ->weight('semibold')
                    ->description(fn ($record) => $record->short_name),

                TextColumn::make('city.name_de')
                    ->label('Şehir')
                    ->sortable()
                    ->description(fn ($record) => $record->city?->state?->name_de),

                TextColumn::make('type')
                    ->label('Tür')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'public' => 'success',
                        'private' => 'info',
                        'religion' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'public' => 'Devlet',
                        'private' => 'Özel',
                        'religion' => 'Dini',
                        default => $state,
                    }),

                TextColumn::make('student_count')
                    ->label('Öğrenci')
                    ->numeric(thousandsSeparator: '.')
                    ->sortable()
                    ->placeholder('—'),

                TextColumn::make('founded_year')
                    ->label('Kuruluş')
                    ->sortable()
                    ->placeholder('—'),

                TextColumn::make('programs_count')
                    ->label('Program')
                    ->counts('programs')
                    ->sortable()
                    ->numeric(),

                IconColumn::make('is_uni_assist_member')
                    ->label('Uni-Assist')
                    ->boolean()
                    ->toggleable(),

                IconColumn::make('has_content')
                    ->label('İçerik')
                    ->getStateUsing(fn (University $r) => !empty($r->content_blocks))
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-minus-circle')
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->tooltip(fn (University $r) => !empty($r->content_blocks) ? count($r->content_blocks) . ' blok' : 'Üretilmemiş'),

                ImageColumn::make('image_url')
                    ->label('Kapak')
                    ->height(50)
                    ->width(80)
                    ->toggleable(),

                IconColumn::make('has_image')
                    ->label('Görsel')
                    ->getStateUsing(fn (University $r) => !empty($r->image_url))
                    ->boolean()
                    ->trueIcon('heroicon-o-photo')
                    ->falseIcon('heroicon-o-minus-circle')
                    ->trueColor('info')
                    ->falseColor('gray')
                    ->toggleable(),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean()
                    ->toggleable(),

                TextColumn::make('data_source')
                    ->label('Kaynak')
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Tür')
                    ->options([
                        'public'   => 'Devlet',
                        'private'  => 'Özel',
                        'religion' => 'Dini',
                    ]),
                TernaryFilter::make('is_active')->label('Aktif'),
                TernaryFilter::make('is_uni_assist_member')->label('Uni-Assist üyesi'),
                Filter::make('has_logo')
                    ->label('Logosu var')
                    ->query(fn (Builder $q) => $q->whereNotNull('logo_url')),
                Filter::make('has_partner')
                    ->label('Partner ID var')
                    ->query(fn (Builder $q) => $q->whereNotNull('partner_id')),
                Filter::make('has_wikidata')
                    ->label('Wikidata var')
                    ->query(fn (Builder $q) => $q->whereNotNull('wikidata_id')),
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
            ->recordActions([
                Action::make('clearImage')
                    ->label('🗑️ Kapağı sil')
                    ->color('warning')
                    ->size('xs')
                    ->visible(fn (University $r) => ! empty($r->image_url))
                    ->requiresConfirmation()
                    ->modalDescription('Kapak görseli silinir → şehir görseli (varsa) otomatik kullanılır. Logo etkilenmez.')
                    ->action(function (University $record) {
                        $record->update(['image_url' => null]);
                        Notification::make()->title('Kapak silindi — şehir fallback aktif')->success()->send();
                    }),

                Action::make('clearLogo')
                    ->label('🚫 Logo sil')
                    ->color('warning')
                    ->size('xs')
                    ->visible(fn (University $r) => ! empty($r->logo_url))
                    ->requiresConfirmation()
                    ->action(function (University $record) {
                        $record->update(['logo_url' => null]);
                        Notification::make()->title('Logo silindi')->success()->send();
                    }),

                Action::make('enrichContent')
                    ->label('🪄 Sayfa Üret')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalDescription('Wikipedia + DB + Forum/Telegram topluluk + AI ile üni sayfa içeriği üretilir (~10 sn, ~$0.003).')
                    ->action(function (University $record) {
                        $svc = app(UniversityEnrichmentService::class);
                        $r = $svc->enrich($record, true);
                        $comm = $r['sources']['community'] ?? [];
                        Notification::make()
                            ->title($r['success'] ? '🪄 ' . $r['blocks_count'] . ' blok üretildi' : '❌ Hata')
                            ->body($r['success']
                                ? 'Wiki: [' . implode(',', $r['sources']['wikipedia_languages'] ?? []) . '] · '
                                    . "{$comm['tg_questions']}× tg · {$comm['forum_titles']}× forum · "
                                    . ($r['sources']['programs'] ?? 0) . ' program'
                                : ($r['error'] ?? 'Bilinmeyen hata'))
                            ->color($r['success'] ? 'success' : 'danger')
                            ->persistent()->send();
                    }),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('clearImagesBulk')
                        ->label('🗑️ Seçili kapakları sil')
                        ->color('warning')
                        ->icon('heroicon-o-photo')
                        ->requiresConfirmation()
                        ->modalDescription('Seçili üniversiteler için kapak görselleri silinir → şehir görseli (varsa) otomatik kullanılır. Logo etkilenmez.')
                        ->action(function (Collection $records) {
                            $n = 0;
                            foreach ($records as $r) {
                                if ($r->image_url) {
                                    $r->update(['image_url' => null]);
                                    $n++;
                                }
                            }
                            Notification::make()->title("{$n} kapak silindi → şehir fallback aktif")->success()->send();
                        }),

                    BulkAction::make('enrichBulk')
                        ->label('🪄 Toplu Sayfa Üret')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalDescription('Seçili üniversiteler için Wikipedia + Community + AI ile içerik üretilir.')
                        ->action(function (Collection $records) {
                            $svc = app(UniversityEnrichmentService::class);
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
