<?php

namespace App\Filament\Resources\Programs\Tables;

use App\Models\FieldOfStudy;
use App\Models\ProgramVerification;
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
            // NOT: ->modifyQueryUsing(...->with()) v4'te tablo özet/filtre sorgusunu
            // bozuyordu (model null → newQueryWithoutRelationships, Filament #17275).
            // İlişki kolonlarını Filament zaten otomatik eager-load eder.
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

                // VERIFIED doğrulama kaydı sayısı (salt okunur; alan bazlı "X/8" özeti program detayında)
                TextColumn::make('verified_records_count')
                    ->label('Doğrulanmış kayıt')
                    ->counts(['verifications as verified_records_count' => fn (Builder $q) => $q->where('status', ProgramVerification::VERIFIED)])
                    ->badge()
                    ->color(fn ($state) => (int) $state > 0 ? 'success' : 'gray')
                    ->toggleable(),
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

                // ── Kaynak ve doğrulama ──
                SelectFilter::make('source')
                    ->label('Import kaynağı')
                    ->options(['partner' => 'Partner API', 'daad' => 'DAAD', 'hochschulkompass' => 'Hochschulkompass']),
                TernaryFilter::make('official_program_url')
                    ->label('Resmî program URL\'si')
                    ->nullable()
                    ->trueLabel('Var')
                    ->falseLabel('Yok')
                    ->queries(
                        true: fn (Builder $q) => $q->whereNotNull('official_program_url')->where('official_program_url', '!=', ''),
                        false: fn (Builder $q) => $q->where(fn ($w) => $w->whereNull('official_program_url')->orWhere('official_program_url', '')),
                    ),
                TernaryFilter::make('has_verifications')
                    ->label('Doğrulama kaydı')
                    ->trueLabel('Var')
                    ->falseLabel('Yok')
                    ->queries(
                        true: fn (Builder $q) => $q->whereHas('verifications'),
                        false: fn (Builder $q) => $q->whereDoesntHave('verifications'),
                    ),
                SelectFilter::make('verification_status')
                    ->label('Doğrulama durumu')
                    ->options([
                        ProgramVerification::NEEDS_REVIEW => 'İnceleme gerekli (NEEDS_REVIEW)',
                        ProgramVerification::CONFLICT => 'Çelişki (CONFLICT)',
                        ProgramVerification::VERIFIED => 'En az bir alan doğrulandı',
                    ])
                    ->query(fn (Builder $q, array $data) => filled($data['value'] ?? null)
                        ? $q->whereHas('verifications', fn ($v) => $v->where('status', $data['value']))
                        : $q),
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
