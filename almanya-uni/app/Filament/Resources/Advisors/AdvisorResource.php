<?php

namespace App\Filament\Resources\Advisors;

use App\Filament\Resources\Advisors\Pages\CreateAdvisor;
use App\Filament\Resources\Advisors\Pages\EditAdvisor;
use App\Filament\Resources\Advisors\Pages\ListAdvisors;
use App\Models\Advisor;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class AdvisorResource extends Resource
{
    protected static ?string $model = Advisor::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAcademicCap;
    protected static ?string $navigationLabel = 'Danışma Kurulu';
    protected static ?string $modelLabel = 'Danışman';
    protected static ?string $pluralModelLabel = 'Danışma Kurulu';
    protected static ?int $navigationSort = 40;
    protected static string|\UnitEnum|null $navigationGroup = 'Pazarlama';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Kişi')->schema([
                TextInput::make('name')->label('Ad Soyad')->required()->maxLength(150),
                TextInput::make('role_title')->label('Unvan / Rol')
                    ->placeholder('ör. Bilgisayar Bilimleri Profesörü')->maxLength(200),
                TextInput::make('affiliation')->label('Kurum')
                    ->placeholder('ör. TU München')->maxLength(200),
            ])->columns(1),

            Section::make('Görsel + Doğrulanabilir Link')->schema([
                FileUpload::make('photo_url')->label('Fotoğraf')
                    ->image()->directory('advisors')->imageEditor()->maxSize(2048),
                TextInput::make('linkedin_url')->label('LinkedIn (doğrulama için önerilir)')
                    ->url()->placeholder('https://www.linkedin.com/in/...')->maxLength(500),
                TextInput::make('profile_url')->label('Diğer profil/site (opsiyonel)')
                    ->url()->maxLength(500),
            ])->columns(1),

            Section::make('Biyografi + Yerleşim')->schema([
                Textarea::make('bio')->label('Kısa biyografi')->rows(4)->columnSpanFull()
                    ->helperText('Gerçek, doğrulanabilir bilgi. Uydurma unvan/itibar koyma.'),
                TextInput::make('sort_order')->label('Sıralama (düşük = önce)')->numeric()->default(10),
                Toggle::make('is_active')->label('Aktif (siteye yansıt)')->default(false)
                    ->helperText('Kapalıyken bu kişi sitede görünmez. Aktif danışman yoksa bölüm hiç çıkmaz.'),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('photo_url')->label('Foto')->circular()->size(44),
                TextColumn::make('name')->label('Ad Soyad')->searchable(),
                TextColumn::make('role_title')->label('Unvan')->limit(40),
                TextColumn::make('affiliation')->label('Kurum')->limit(30),
                IconColumn::make('is_active')->label('Aktif')->boolean(),
                TextColumn::make('sort_order')->label('Sıra')->sortable(),
            ])
            ->defaultSort('sort_order', 'asc')
            ->filters([
                TernaryFilter::make('is_active')->label('Aktif'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListAdvisors::route('/'),
            'create' => CreateAdvisor::route('/create'),
            'edit'   => EditAdvisor::route('/{record}/edit'),
        ];
    }
}
