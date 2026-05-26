<?php

namespace App\Filament\Resources\Programs\Tables;

use App\Models\FieldOfStudy;
use App\Models\University;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class ProgramsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $q) => $q->with('university:id,name_de', 'field:id,name_tr,icon'))
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('name_de')
                    ->label('Program')
                    ->searchable(['name_de', 'name_en', 'name_tr'])
                    ->sortable()
                    ->limit(50)
                    ->weight('semibold')
                    ->description(fn ($record) => $record->degree_specification),

                TextColumn::make('university.name_de')
                    ->label('Üniversite')
                    ->searchable()
                    ->limit(35),

                TextColumn::make('degree')
                    ->label('Derece')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'bachelor' => 'success',
                        'master'   => 'info',
                        'phd'      => 'warning',
                        default    => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('language')
                    ->label('Dil')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'en'   => 'info',
                        'de'   => 'success',
                        'both' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'en'   => 'EN',
                        'de'   => 'DE',
                        'both' => 'DE+EN',
                        default => $state,
                    }),

                TextColumn::make('field.name_tr')
                    ->label('Alan')
                    ->limit(20)
                    ->placeholder('—'),

                // NC durumu — yeni
                TextColumn::make('admission_mode')
                    ->label('NC')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'zulassungsfrei' => 'success',
                        'oertlich'       => 'warning',
                        'bundesweit'     => 'danger',
                        'auswahl'        => 'info',
                        default          => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'zulassungsfrei' => '🔓 NC Frei',
                        'oertlich'       => '⚠️ Yerel NC',
                        'bundesweit'     => '🚦 Ulusal',
                        'auswahl'        => '🎯 Auswahl',
                        default          => '— bilinmiyor',
                    })
                    ->placeholder('— bilinmiyor'),

                TextColumn::make('duration_semesters')
                    ->label('Süre')
                    ->suffix(' sem')
                    ->sortable()
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('description_tr')
                    ->label('TR')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-minus-circle')
                    ->getStateUsing(fn ($record) => filled($record->description_tr))
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('degree')
                    ->label('Derece')
                    ->options([
                        'bachelor' => 'Bachelor',
                        'master'   => 'Master',
                        'phd'      => 'PhD',
                        'staatsexamen' => 'Staatsexamen',
                        'other'    => 'Diğer',
                    ]),
                SelectFilter::make('language')
                    ->label('Dil')
                    ->options([
                        'en' => 'İngilizce',
                        'de' => 'Almanca',
                        'both' => 'İki dilli',
                    ]),
                SelectFilter::make('field_of_study_id')
                    ->label('Alan')
                    ->options(fn () => FieldOfStudy::pluck('name_tr', 'id')->toArray())
                    ->searchable(),
                SelectFilter::make('university_id')
                    ->label('Üniversite')
                    ->options(fn () => University::orderBy('name_de')->pluck('name_de', 'id')->toArray())
                    ->searchable(),
                // NC filter — yeni
                SelectFilter::make('admission_mode')
                    ->label('NC Durumu')
                    ->options([
                        'zulassungsfrei' => '🔓 NC Frei (Zulassungsfrei)',
                        'oertlich'       => '⚠️ Yerel NC',
                        'bundesweit'     => '🚦 Ulusal NC',
                        'auswahl'        => '🎯 Auswahlverfahren',
                    ]),
                Filter::make('admission_unknown')
                    ->label('NC bilinmiyor')
                    ->query(fn (Builder $q) => $q->whereNull('admission_mode')),
                TernaryFilter::make('is_active')->label('Aktif'),
                Filter::make('has_description_tr')
                    ->label('Türkçe açıklama var')
                    ->query(fn (Builder $q) => $q->whereNotNull('description_tr')),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    // NC Frei işaretleme
                    BulkAction::make('markZulassungsfrei')
                        ->label('🔓 NC Frei işaretle')
                        ->color('success')
                        ->icon('heroicon-o-lock-open')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            $count = $records->count();
                            $records->each->update(['admission_mode' => 'zulassungsfrei']);
                            Notification::make()
                                ->title("$count program NC Frei olarak işaretlendi")
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    BulkAction::make('markOertlich')
                        ->label('⚠️ Yerel NC işaretle')
                        ->color('warning')
                        ->icon('heroicon-o-exclamation-triangle')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            $count = $records->count();
                            $records->each->update(['admission_mode' => 'oertlich']);
                            Notification::make()
                                ->title("$count program Yerel NC olarak işaretlendi")
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    BulkAction::make('markBundesweit')
                        ->label('🚦 Ulusal NC işaretle')
                        ->color('danger')
                        ->icon('heroicon-o-flag')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            $count = $records->count();
                            $records->each->update(['admission_mode' => 'bundesweit']);
                            Notification::make()
                                ->title("$count program Ulusal NC olarak işaretlendi")
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    BulkAction::make('clearAdmission')
                        ->label('🧹 NC bilgisini temizle')
                        ->color('gray')
                        ->icon('heroicon-o-x-mark')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            $count = $records->count();
                            $records->each->update(['admission_mode' => null]);
                            Notification::make()
                                ->title("$count program NC bilgisi temizlendi")
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    DeleteBulkAction::make(),
                ]),
            ])
            ->deferLoading();
    }
}
