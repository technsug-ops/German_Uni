<?php

namespace App\Filament\Resources\MenuPages\Tables;

use App\Models\MenuPage;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class MenuPagesTable
{
    public static function configure(Table $table): Table
    {
        $groupOptions = [];
        foreach (MenuPage::GROUPS as $key => $meta) {
            $groupOptions[$key] = $meta['emoji'] . ' ' . $meta['label'];
        }

        return $table
            ->columns([
                TextColumn::make('group')->label('Grup')->badge()
                    ->formatStateUsing(fn (?string $state) => isset(MenuPage::GROUPS[$state])
                        ? (MenuPage::GROUPS[$state]['emoji'] . ' ' . MenuPage::GROUPS[$state]['label'])
                        : ($state ?? '—'))
                    ->color(fn (?string $state) => match ($state) {
                        'kesfet'     => 'info',
                        'araclar'    => 'primary',
                        'firsatlar'  => 'success',
                        'icerik'     => 'warning',
                        'standalone' => 'gray',
                        default      => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('icon')->label('')->size('lg'),

                TextColumn::make('label')->label('Görünen ad')->searchable()->sortable()->weight('bold'),

                TextColumn::make('key')->label('Route / anahtar')->searchable()->color('gray')->size('xs'),

                TextColumn::make('badge')->label('Rozet')->badge()->color('warning')->toggleable(),

                ToggleColumn::make('is_enabled')->label('Yayında')
                    ->onColor('success')
                    ->offColor('danger'),

                IconColumn::make('protect_route')->label('URL kilit')->boolean()->toggleable(),

                TextColumn::make('sort_order')->label('Sıra')->numeric()->sortable()->toggleable(),
            ])
            ->filters([
                SelectFilter::make('group')->label('Grup')->options($groupOptions),
                TernaryFilter::make('is_enabled')->label('Yayında'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->defaultSort('group', 'asc')
            ->groups([
                \Filament\Tables\Grouping\Group::make('group')
                    ->label('Grup')
                    ->getTitleFromRecordUsing(fn ($r) => ($r && isset(MenuPage::GROUPS[$r->group]))
                        ? (MenuPage::GROUPS[$r->group]['emoji'] . ' ' . MenuPage::GROUPS[$r->group]['label'])
                        : ($r->group ?? '—'))
                    ->collapsible(),
            ])
            ->defaultGroup('group')
            ->paginated([25, 50, 100, 'all']);
    }
}
