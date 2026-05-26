<?php

namespace App\Filament\Resources\PremiumInterests\Pages;

use App\Filament\Resources\PremiumInterests\PremiumInterestResource;
use App\Models\PremiumInterest;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListPremiumInterests extends ListRecords
{
    protected static string $resource = PremiumInterestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('exportAll')
                ->label('📤 Tümünü CSV indir')
                ->color('info')
                ->action(function () {
                    $filename = 'all-premium-leads-' . now()->format('Y-m-d-His') . '.csv';

                    return response()->streamDownload(function () {
                        $out = fopen('php://output', 'w');
                        fputcsv($out, ['email', 'name', 'tier_interest', 'locale', 'country', 'note', 'source_page', 'contacted', 'contacted_at', 'created_at']);
                        PremiumInterest::query()->orderBy('created_at')->chunk(500, function ($chunk) use ($out) {
                            foreach ($chunk as $r) {
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
                        });
                        fclose($out);
                    }, $filename);
                }),
        ];
    }
}
