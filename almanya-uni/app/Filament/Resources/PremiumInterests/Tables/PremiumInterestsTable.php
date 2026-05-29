<?php

namespace App\Filament\Resources\PremiumInterests\Tables;

use App\Models\PremiumInterest;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class PremiumInterestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('email')
                    ->label('E-posta')
                    ->searchable()
                    ->copyable()
                    ->weight('semibold')
                    ->limit(40),

                TextColumn::make('name')
                    ->label('Ad')
                    ->placeholder('—')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('tier_interest')
                    ->label('Tier')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'premium'   => 'success',
                        'pro'       => 'warning',
                        'undecided' => 'gray',
                        default     => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'premium'   => 'Premium €14',
                        'pro'       => 'Pro €49',
                        'undecided' => 'Emin değil',
                        default     => $state,
                    }),

                IconColumn::make('wants_beta')
                    ->label('🚀 Beta')
                    ->boolean()
                    ->trueColor('purple')
                    ->falseColor('gray'),

                TextColumn::make('beta_invited_at')
                    ->label('Beta davet')
                    ->dateTime('d.m.Y')
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('locale')
                    ->label('Dil')
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn ($state) => strtoupper((string) $state))
                    ->toggleable(),

                TextColumn::make('country')
                    ->label('Ülke')
                    ->placeholder('—')
                    ->toggleable(),

                IconColumn::make('contacted')
                    ->label('İletişim')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-clock')
                    ->trueColor('success')
                    ->falseColor('warning'),

                TextColumn::make('contacted_at')
                    ->label('İletişim tarihi')
                    ->dateTime('d.m.Y H:i')
                    ->placeholder('—')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('note')
                    ->label('Not')
                    ->limit(40)
                    ->placeholder('—')
                    ->tooltip(fn ($record) => $record->note)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('source_page')
                    ->label('Sayfa')
                    ->limit(40)
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Kayıt')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Filter::make('pending')
                    ->label('Sadece bekleyenler (henüz iletişim yok)')
                    ->query(fn (Builder $q) => $q->where('contacted', false))
                    ->default(),

                Filter::make('contacted')
                    ->label('İletişime geçilenler')
                    ->query(fn (Builder $q) => $q->where('contacted', true)),

                Filter::make('beta_candidates')
                    ->label('🚀 Beta adayları (henüz davet edilmedi)')
                    ->query(fn (Builder $q) => $q->where('wants_beta', true)->whereNull('beta_invited_at')),

                Filter::make('beta_invited')
                    ->label('Beta\'ya davet edildi')
                    ->query(fn (Builder $q) => $q->whereNotNull('beta_invited_at')),

                SelectFilter::make('tier_interest')
                    ->label('Tier ilgisi')
                    ->options([
                        'premium'   => 'Premium (€14/ay)',
                        'pro'       => 'Pro (€49 tek)',
                        'undecided' => 'Henüz emin değil',
                    ]),

                SelectFilter::make('locale')
                    ->label('Dil')
                    ->options(['tr' => 'TR', 'en' => 'EN', 'de' => 'DE']),
            ])
            ->recordActions([
                Action::make('markContacted')
                    ->label('✓ İletişime geçildi')
                    ->color('success')
                    ->visible(fn ($record) => ! $record->contacted)
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update(['contacted' => true, 'contacted_at' => now()]);
                        Notification::make()->title('İletişim kaydı işaretlendi')->success()->send();
                    }),

                Action::make('mailto')
                    ->label('✉ E-posta gönder')
                    ->color('info')
                    ->url(fn ($record) => 'mailto:' . $record->email . '?subject=' . rawurlencode('AlmanyaUni Premium hakkında'))
                    ->openUrlInNewTab(),

                Action::make('inviteToBeta')
                    ->label('🚀 Beta\'ya davet et')
                    ->color('purple')
                    ->visible(fn ($record) => $record->wants_beta && ! $record->beta_invited_at)
                    ->requiresConfirmation()
                    ->modalDescription('Beta tester davet maili gönderilecek + beta_invited_at zaman damgalanacak.')
                    ->action(function ($record) {
                        $record->update(['beta_invited_at' => now()]);
                        Notification::make()->title('Beta daveti işaretlendi')->body('Manuel davet mailini şimdi mailto ile gönderebilirsin.')->success()->send();
                    }),

                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('markContactedBulk')
                        ->label('✓ İletişim olarak işaretle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            $n = 0;
                            foreach ($records as $r) {
                                if (! $r->contacted) {
                                    $r->update(['contacted' => true, 'contacted_at' => now()]);
                                    $n++;
                                }
                            }
                            Notification::make()->title("{$n} lead işaretlendi")->success()->send();
                        }),

                    BulkAction::make('exportCsv')
                        ->label('📤 CSV indir')
                        ->color('info')
                        ->action(function (Collection $records) {
                            $filename = 'premium-leads-' . now()->format('Y-m-d-His') . '.csv';

                            return response()->streamDownload(function () use ($records) {
                                $out = fopen('php://output', 'w');
                                fputcsv($out, ['email', 'name', 'tier_interest', 'locale', 'country', 'note', 'source_page', 'contacted', 'contacted_at', 'created_at']);
                                foreach ($records as $r) {
                                    fputcsv($out, [
                                        $r->email,
                                        $r->name,
                                        $r->tier_interest,
                                        $r->locale,
                                        $r->country,
                                        $r->note,
                                        $r->source_page,
                                        $r->contacted ? '1' : '0',
                                        $r->contacted_at?->toIso8601String(),
                                        $r->created_at?->toIso8601String(),
                                    ]);
                                }
                                fclose($out);
                            }, $filename);
                        }),

                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Henüz Premium lead yok')
            ->emptyStateDescription('Pricing sayfasındaki form dolduruldukça burada görünecek.');
    }
}
