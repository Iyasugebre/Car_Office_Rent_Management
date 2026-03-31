<?php

namespace App\Filament\Widgets;

use App\Models\Agreement;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class ExpiringAgreementsWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Agreement::query()
                    ->where('status', 'active')
                    ->where('legal_status', 'approved')
                    ->whereDate('end_date', '<=', Carbon::today()->addDays(90))
                    ->orderBy('end_date')
            )
            ->columns([
                Tables\Columns\TextColumn::make('agreement_id')
                    ->label('Agreement #')
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('branchRequest.branch_name')
                    ->label('Branch')
                    ->placeholder('N/A'),
                Tables\Columns\TextColumn::make('end_date')
                    ->date()
                    ->sortable()
                    ->description(fn ($record) => Carbon::today()->diffInDays(Carbon::parse($record->end_date), false) . ' days remaining')
                    ->color(fn ($record) => Carbon::today()->diffInDays(Carbon::parse($record->end_date), false) <= 30 ? 'danger' : 'warning'),
                Tables\Columns\TextColumn::make('progress')
                    ->label('Progress')
                    ->getStateUsing(function ($record) {
                        $start = Carbon::parse($record->start_date);
                        $end = Carbon::parse($record->end_date);
                        $now = Carbon::today();
                        
                        $total = $start->diffInDays($end);
                        if ($total <= 0) return 100;
                        
                        $elapsed = $start->diffInDays($now);
                        return min(100, round(($elapsed / $total) * 100));
                    })
                    ->formatStateUsing(fn ($state) => "
                        <div class=\"w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700 mb-1\">
                          <div class=\"bg-indigo-600 h-2.5 rounded-full\" style=\"width: {$state}%\"></div>
                        </div>
                        <span class=\"text-xs text-gray-500 font-medium\">{$state}% Complete</span>
                    ")
                    ->html(),
            ])
            ->actions([
                Actions\ViewAction::make()
                    ->url(fn (Agreement $record): string => route('filament.admin.resources.agreements.view', $record)),
            ])
            ->emptyStateHeading('All contracts on track')
            ->emptyStateDescription('No agreements expiring in the next 90 days.');
    }
}
