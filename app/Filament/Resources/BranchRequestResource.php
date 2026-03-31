<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BranchRequestResource\Pages;
use App\Models\BranchRequest;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions;

class BranchRequestResource extends Resource
{
    protected static ?string $model = BranchRequest::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-plus-circle';

    protected static string | \UnitEnum | null $navigationGroup = 'Office Management';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                \Filament\Schemas\Components\Section::make('Request Info')
                    ->schema([
                        Forms\Components\TextInput::make('reference_number')
                            ->disabled()
                            ->dehydrated(false),
                        Forms\Components\TextInput::make('branch_name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('location')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('proposed_office')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('estimated_rent')
                            ->numeric()
                            ->prefix('ETB')
                            ->required(),
                        Forms\Components\Hidden::make('requester_id')
                            ->default(fn () => auth()->id()),
                    ])->columns(2),

                \Filament\Schemas\Components\Section::make('Management & Status')
                    ->schema([
                        Forms\Components\Select::make('priority')
                            ->options([
                                'low' => 'Low',
                                'medium' => 'Medium',
                                'high' => 'High',
                                'critical' => 'Critical',
                            ])
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'approved' => 'Approved',
                                'rejected' => 'Rejected',
                                'in_progress' => 'In Progress',
                                'completed' => 'Completed',
                            ])
                            ->required()
                            ->default('pending'),
                        Forms\Components\Textarea::make('landlord_details')
                            ->rows(3),
                        Forms\Components\Textarea::make('remarks')
                            ->rows(3),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reference_number')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('branch_name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('location')
                    ->searchable(),
                Tables\Columns\TextColumn::make('priority')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'critical' => 'danger',
                        'high' => 'warning',
                        'medium' => 'info',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'approved' => 'success',
                        'completed' => 'success',
                        'rejected' => 'danger',
                        'in_progress' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('estimated_rent')
                    ->money('ETB')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status'),
                Tables\Filters\SelectFilter::make('priority'),
            ])
            ->actions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBranchRequests::route('/'),
            'create' => Pages\CreateBranchRequest::route('/create'),
            'edit' => Pages\EditBranchRequest::route('/{record}/edit'),
        ];
    }
}
