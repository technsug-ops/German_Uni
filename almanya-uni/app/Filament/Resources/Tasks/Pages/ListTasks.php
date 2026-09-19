<?php

namespace App\Filament\Resources\Tasks\Pages;

use App\Filament\Resources\Tasks\TaskResource;
use App\Models\Task;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTasks extends ListRecords
{
    protected static string $resource = TaskResource::class;

    /** Başlık altında ilerleme: "12 / 38 tamam · 3 tarihi geçti". */
    public function getSubheading(): ?string
    {
        $total = Task::count();
        if ($total === 0) {
            return null;
        }

        $done    = Task::where('status', 'done')->count();
        $overdue = Task::overdue()->count();
        $pct     = (int) round($done / $total * 100);

        $line = "{$done} / {$total} tamam (%{$pct})";

        return $overdue > 0 ? $line . " · {$overdue} tarihi geçti" : $line;
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Yeni görev'),
        ];
    }
}
