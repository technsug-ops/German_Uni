<?php

namespace App\Filament\Resources\Subscribers\Pages;

use App\Filament\Resources\Subscribers\SubscriberResource;
use App\Models\Subscriber;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListSubscribers extends ListRecords
{
    protected static string $resource = SubscriberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('exportAll')
                ->label('📤 Tümünü CSV indir')
                ->color('info')
                ->action(function () {
                    $filename = 'all-subscribers-' . now()->format('Y-m-d-His') . '.csv';

                    return response()->streamDownload(function () {
                        $out = fopen('php://output', 'w');
                        fputcsv($out, ['email', 'name', 'language', 'source', 'confirmed_at', 'unsubscribed_at', 'created_at']);
                        Subscriber::query()->orderBy('created_at')->chunk(500, function ($chunk) use ($out) {
                            foreach ($chunk as $r) {
                                fputcsv($out, [
                                    $r->email,
                                    $r->name,
                                    $r->language,
                                    $r->source,
                                    $r->confirmed_at?->toIso8601String(),
                                    $r->unsubscribed_at?->toIso8601String(),
                                    $r->created_at?->toIso8601String(),
                                ]);
                            }
                        });
                        fclose($out);
                    }, $filename);
                }),
        ];
    }
}
