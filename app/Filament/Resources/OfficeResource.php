<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OfficeResource\Pages;
use App\Models\Office;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions;

class OfficeResource extends Resource
{
    protected static ?string $model = Office::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-briefcase';

    protected static string | \UnitEnum | null $navigationGroup = 'Office Management';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                \Filament\Schemas\Components\Section::make('Branch Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('address')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('city')
                            ->required()
                            ->maxLength(100),
                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->maxLength(20),
                    ])->columns(2),

                \Filament\Schemas\Components\Section::make('Type & Pricing')
                    ->schema([
                        Forms\Components\Select::make('type')
                            ->options([
                                'branch' => 'Head / Branch Office',
                                'rental_unit' => 'Leasable Asset',
                            ])
                            ->required()
                            ->default('branch'),
                        Forms\Components\TextInput::make('price_per_month')
                            ->numeric()
                            ->prefix('ETB')
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'available' => 'Available',
                                'occupied' => 'Occupied',
                                'maintenance' => 'In Maintenance',
                            ])
                            ->required()
                            ->default('available'),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('city')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'available' => 'success',
                        'occupied' => 'warning',
                        'maintenance' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('price_per_month')
                    ->money('ETB')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'branch' => 'Branch',
                        'rental_unit' => 'Leasable',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'available' => 'Available',
                        'occupied' => 'Occupied',
                    ]),
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
            'index' => Pages\ListOffices::route('/'),
            'create' => Pages\CreateOffice::route('/create'),
            'edit' => Pages\EditOffice::route('/{record}/edit'),
        ];
    }
}
