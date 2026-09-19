<?php

namespace App\Filament\Resources\OutreachContacts\Tables;

use App\Filament\Pages\OutreachCompose;
use App\Models\OutreachContact;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class OutreachContactsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('updated_at', 'desc')
            ->columns([
                TextColumn::make('organization')
                    ->label('Firma / Kurum')
                    ->searchable()
                    ->weight('bold')
                    ->description(fn (OutreachContact $r) => $r->contact_name),
                TextColumn::make('category')
                    ->label('Kategori')
                    ->badge()
                    ->formatStateUsing(fn ($state) => OutreachContact::CATEGORIES[$state] ?? $state)
                    ->color('gray'),
                TextColumn::make('email')
                    ->label('E-posta')
                    ->searchable()
                    ->copyable()
                    ->placeholder('—'),
                TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->formatStateUsing(fn ($state) => OutreachContact::STATUSES[$state] ?? $state)
                    ->color(fn ($state) => match ($state) {
                        'new'         => 'gray',
                        'contacted'   => 'info',
                        'replied'     => 'warning',
                        'negotiating' => 'warning',
                        'partner'     => 'success',
                        'declined'    => 'danger',
                        default       => 'gray',
                    }),
                TextColumn::make('messages_count')
                    ->label('Yazışma')
                    ->counts('messages')
                    ->alignCenter()
                    ->badge()
                    ->color('gray'),
                TextColumn::make('last_contacted_at')
                    ->label('Son temas')
                    ->dateTime('d.m.Y')
                    ->sortable()
                    ->placeholder('—'),
                TextColumn::make('next_followup_at')
                    ->label('Sonraki takip')
                    ->date('d.m.Y')
                    ->sortable()
                    ->placeholder('—')
                    ->color(fn (OutreachContact $r) => $r->isFollowupDue() ? 'danger' : null)
                    ->weight(fn (OutreachContact $r) => $r->isFollowupDue() ? 'bold' : null),
                TextColumn::make('priority')
                    ->label('Öncelik')
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->formatStateUsing(fn ($state) => OutreachContact::PRIORITIES[$state] ?? $state)
                    ->color(fn ($state) => $state === 'high' ? 'danger' : 'gray'),
            ])
            ->filters([
                SelectFilter::make('status')->label('Durum')->options(OutreachContact::STATUSES),
                SelectFilter::make('category')->label('Kategori')->options(OutreachContact::CATEGORIES),
                Filter::make('followup_due')
                    ->label('Takip zamanı gelmiş')
                    ->query(fn ($query) => $query
                        ->whereNotNull('next_followup_at')
                        ->whereDate('next_followup_at', '<=', now())
                        ->whereNotIn('status', ['partner', 'declined'])),
            ])
            ->recordActions([
                Action::make('compose')
                    ->label('Mail Gönder')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('success')
                    ->url(fn (OutreachContact $r) => OutreachCompose::getUrl() . '?contact=' . $r->id)
                    ->visible(fn (OutreachContact $r) => filled($r->email)),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ])
            ->emptyStateHeading('Henüz kontak yok')
            ->emptyStateDescription('Görüştüğün firmaları buraya ekle; panelden atılan mailler ve gelen yanıtlar otomatik olarak kontağın altında birikir.');
    }
}
