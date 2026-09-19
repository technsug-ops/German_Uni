<?php

namespace App\Filament\Resources\Tasks;

use App\Filament\Resources\Tasks\Pages\CreateTask;
use App\Filament\Resources\Tasks\Pages\EditTask;
use App\Filament\Resources\Tasks\Pages\ListTasks;
use App\Filament\Resources\Tasks\Schemas\TaskForm;
use App\Filament\Resources\Tasks\Tables\TasksTable;
use App\Models\Task;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

/**
 * Görevler / checklist — doküman hâlindeki oyun kitaplarının panel karşılığı.
 * İlk içerik: doc/BACKLINK-PLAYBOOK.md.
 */
class TaskResource extends Resource
{
    protected static ?string $model = Task::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCheckCircle;
    protected static ?string $navigationLabel = 'Görevler';
    protected static ?string $modelLabel = 'Görev';
    protected static ?string $pluralModelLabel = 'Görevler';
    protected static ?string $recordTitleAttribute = 'title';
    protected static ?int $navigationSort = 1;
    protected static string|\UnitEnum|null $navigationGroup = 'Pazarlama';

    public static function canAccess(): bool
    {
        return auth()->user()?->isFullAdmin() === true;
    }

    /** Rozet: açık iş sayısı (tarihi geçen varsa kırmızı). */
    public static function getNavigationBadge(): ?string
    {
        if (! \Illuminate\Support\Facades\Schema::hasTable('tasks')) {
            return null;
        }

        $open = Task::open()->count();

        return $open > 0 ? (string) $open : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        if (! \Illuminate\Support\Facades\Schema::hasTable('tasks')) {
            return null;
        }

        return Task::overdue()->exists() ? 'danger' : 'warning';
    }

    public static function form(Schema $schema): Schema
    {
        return TaskForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TasksTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListTasks::route('/'),
            'create' => CreateTask::route('/create'),
            'edit'   => EditTask::route('/{record}/edit'),
        ];
    }
}
