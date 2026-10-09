<?php

namespace App\Filament\Resources\Programs\RelationManagers;

use App\Models\ProgramVerification;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

/**
 * Program detayında "Kaynak ve Doğrulama" — alan bazında doğrulama kayıtları. Yazma yalnız tam admin
 * (ProgramVerificationPolicy). Durum açıkça seçilir; kaynak URL'si eklemek VERIFIED yapmaz. Basit alanlarda
 * kaynak değeri programdakinden farklıysa model kaydı CONFLICT'e çeker; sonradan değişen değer NEEDS_REVIEW olur.
 */
class VerificationsRelationManager extends RelationManager
{
    protected static string $relationship = 'verifications';

    protected static ?string $title = 'Kaynak ve Doğrulama — alan bazında';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('field')->label('Doğrulanan alan')->options(ProgramVerification::FIELDS)->required()->native(false),
            Select::make('status')->label('Durum')->options(ProgramVerification::STATUSES)->default(ProgramVerification::UNVERIFIED)->required()->native(false)
                ->helperText('VERIFIED: kaynak URL + kanıt zorunlu. Basit alanlarda kaynak değeri programdakinden farklıysa otomatik CONFLICT olur.'),
            Select::make('applicant_group')->label('Aday grubu')->options(ProgramVerification::APPLICANT_GROUPS)->default('')->selectablePlaceholder(false)->native(false),
            TextInput::make('term')->label('Dönem')->placeholder('örn. WS 2027/28 — boş = döneme bağlı değil')->maxLength(32),
            TextInput::make('source_value')->label('Kaynaktaki değer')
                ->helperText('Dil: en/de/both · uni-assist/VPD: yes/no · başvuru: ' . implode('/', array_keys(ProgramVerification::APPLICATION_METHODS)))
                ->columnSpanFull(),
            TextInput::make('source_url')->label('Kaynak URL (resmî sayfa)')->url()->maxLength(500)->columnSpanFull(),
            Textarea::make('evidence')->label('Kanıt (kaynaktaki ilgili kısa bölüm)')->rows(3)->columnSpanFull(),
            DatePicker::make('checked_at')->label('Kaynak kontrol tarihi')->default(now()),
        ])->columns(2);
    }

    public function table(Table $table): Table
    {
        $owner = $this->getOwnerRecord();

        return $table
            ->defaultSort('field')
            ->columns([
                TextColumn::make('field')->label('Alan')->formatStateUsing(fn ($state) => ProgramVerification::FIELDS[$state] ?? $state)->wrap(),
                TextColumn::make('status')->label('Durum')->badge()
                    ->formatStateUsing(fn ($state) => ProgramVerification::STATUSES[$state] ?? $state)
                    ->color(fn ($state) => match ($state) {
                        ProgramVerification::VERIFIED => 'success',
                        ProgramVerification::NEEDS_REVIEW => 'warning',
                        ProgramVerification::CONFLICT => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('applicant_group')->label('Aday grubu')
                    ->formatStateUsing(fn ($state) => ProgramVerification::APPLICANT_GROUPS[$state] ?? $state)->placeholder('All applicants'),
                TextColumn::make('term')->label('Dönem')->placeholder('—'),
                TextColumn::make('source_value')->label('Kaynaktaki değer')->limit(60)->wrap()->placeholder('—'),
                TextColumn::make('current_value')->label('Programdaki değer')
                    ->state(fn (ProgramVerification $r) => implode(' · ', array_map(fn ($x) => $x === null || $x === '' ? '∅' : (string) $x,
                        $r->field === 'requirements' ? ['(metin)'] : ProgramVerification::currentValue($owner, $r->field))))
                    ->wrap(),
                TextColumn::make('checked_at')->label('Kontrol')->date('d.m.Y')->placeholder('—'),
                TextColumn::make('verified_at')->label('Doğrulandı')->dateTime('d.m.Y')->placeholder('—'),
                TextColumn::make('verifier.name')->label('Doğrulayan')->placeholder(fn (ProgramVerification $r) => $r->verified_via ?: '—'),
                TextColumn::make('review_reason')->label('Not')->limit(50)->wrap()->placeholder(''),
                TextColumn::make('source_url')->label('Kaynak')->url(fn ($state) => $state, true)->limit(30)->placeholder('—'),
            ])
            ->filters([
                SelectFilter::make('status')->label('Durum')->options(ProgramVerification::STATUSES),
                SelectFilter::make('field')->label('Alan')->options(ProgramVerification::FIELDS),
            ])
            ->headerActions([
                CreateAction::make()->label('Doğrulama ekle')
                    ->mutateDataUsing(fn (array $data) => $data + ['verified_by' => auth()->id(), 'verified_via' => 'admin']),
            ])
            ->recordActions([
                EditAction::make()->mutateDataUsing(fn (array $data) => array_merge($data, ['verified_by' => auth()->id(), 'verified_via' => 'admin'])),
                DeleteAction::make(),
            ]);
    }
}
