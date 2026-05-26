<?php

namespace App\Filament\Resources\ScrapedPrograms;

use App\Filament\Resources\ScrapedPrograms\Pages\ListScrapedPrograms;
use App\Models\Program;
use App\Models\ScrapedProgram;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ScrapedProgramResource extends Resource
{
    /** Hassas kaynak — yalnızca tam admin (editör göremez). */
    public static function canViewAny(): bool
    {
        return auth()->user()?->isFullAdmin() ?? false;
    }

    protected static ?string $model = ScrapedProgram::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQueueList;
    protected static ?string $navigationLabel = 'Scrape Review';
    protected static ?string $modelLabel = 'Scraped Program';
    protected static ?string $pluralModelLabel = 'Scrape Review Queue';
    protected static ?int $navigationSort = 71;
    protected static string|\UnitEnum|null $navigationGroup = 'Entegrasyonlar';

    public static function getNavigationBadge(): ?string
    {
        $count = ScrapedProgram::where('review_status', 'pending')->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('university.name_de')->label('Üni')->limit(28)->searchable(),
                TextColumn::make('name_de')->label('Program')->limit(45)->searchable(),
                TextColumn::make('degree')->badge()->color(fn (?string $state) => match ($state) {
                    'bachelor' => 'info', 'master' => 'success', 'phd' => 'warning', default => 'gray',
                }),
                TextColumn::make('language')->badge(),
                TextColumn::make('duration_semesters')->label('Sem.')->numeric(),
                TextColumn::make('review_status')->badge()->color(fn (string $state) => match ($state) {
                    'pending' => 'warning', 'approved', 'auto_approved' => 'success', 'rejected' => 'danger',
                }),
                TextColumn::make('source_url')
                    ->url(fn (?string $state) => $state ?: null)
                    ->openUrlInNewTab()
                    ->formatStateUsing(fn (?string $state) => $state ? '🔗 ' . Str::limit($state, 30) : '—'),
                TextColumn::make('last_seen_at')->dateTime('d.m H:i')->sortable(),
            ])
            ->filters([
                SelectFilter::make('review_status')->options([
                    'pending' => 'Pending',
                    'approved' => 'Approved',
                    'rejected' => 'Rejected',
                    'auto_approved' => 'Auto-approved',
                ])->default('pending'),
                SelectFilter::make('university_id')->relationship('university', 'name_de')->searchable(),
                SelectFilter::make('degree')->options([
                    'bachelor' => 'Bachelor', 'master' => 'Master', 'phd' => 'PhD',
                ]),
            ])
            ->recordActions([
                Action::make('approve')
                    ->label('✓ Onayla → programs')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (ScrapedProgram $r) => $r->review_status === 'pending')
                    ->action(fn (ScrapedProgram $r) => self::approveOne($r)),
                Action::make('reject')
                    ->label('✗ Reddet')
                    ->color('danger')
                    ->visible(fn (ScrapedProgram $r) => $r->review_status === 'pending')
                    ->action(function (ScrapedProgram $r) {
                        $r->update(['review_status' => 'rejected', 'reviewed_by' => auth()->id(), 'reviewed_at' => now()]);
                        Notification::make()->title('Reddedildi')->success()->send();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('bulkApprove')
                        ->label('✓ Toplu Onayla')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $n = 0;
                            foreach ($records as $r) {
                                if ($r->review_status === 'pending') {
                                    self::approveOne($r, false);
                                    $n++;
                                }
                            }
                            Notification::make()->title("$n program onaylandı")->success()->send();
                        }),
                ]),
            ])
            ->defaultSort('last_seen_at', 'desc');
    }

    public static function getPages(): array
    {
        return ['index' => ListScrapedPrograms::route('/')];
    }

    private static function approveOne(ScrapedProgram $r, bool $notify = true): void
    {
        $slug = Str::slug(($r->name_de ?? 'program') . '-' . ($r->degree ?? '') . '-' . substr(md5((string) $r->id), 0, 8));

        $program = Program::updateOrCreate(
            ['source' => 'scrape', 'source_id' => (string) $r->id],
            [
                'university_id' => $r->university_id,
                'name_de' => $r->name_de,
                'name_en' => $r->name_en,
                'slug' => $slug,
                'degree' => $r->degree,
                'language' => $r->language,
                'duration_semesters' => $r->duration_semesters,
                'admission_mode' => $r->admission_mode,
                'nc_value' => $r->nc_value,
                'tuition_fee_eur' => $r->tuition_fee_eur,
                'description_de' => $r->description_de,
                'source_url' => $r->source_url,
                'is_active' => true,
                'last_synced_at' => now(),
            ]
        );

        $r->update([
            'program_id' => $program->id,
            'review_status' => 'approved',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        if ($notify) {
            Notification::make()->title('✓ Onaylandı, programs\'a yazıldı')->success()
                ->body("Program #{$program->id}")->send();
        }
    }
}
