<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CarResource\Pages;
use App\Models\Car;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CarResource extends Resource
{
    protected static ?string $model = Car::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-truck';

    protected static string | \UnitEnum | null $navigationGroup = 'Fleet Management';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                \Filament\Schemas\Components\Section::make('Basic Information')
                    ->schema([
                        Forms\Components\Select::make('office_id')
                            ->relationship('office', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('make')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('model')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('year')
                            ->numeric()
                            ->required(),
                        Forms\Components\TextInput::make('plate_number')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50),
                        Forms\Components\TextInput::make('registration_number')
                            ->maxLength(100),
                    ])->columns(2),

                \Filament\Schemas\Components\Section::make('Pricing & Usage')
                    ->schema([
                        Forms\Components\TextInput::make('price_per_day')
                            ->numeric()
                            ->prefix('ETB')
                            ->required(),
                        Forms\Components\TextInput::make('mileage')
                            ->numeric()
                            ->label('Current Mileage (km)')
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'available' => 'Available',
                                'rented' => 'Rented',
                                'maintenance' => 'In Maintenance',
                                'reserved' => 'Reserved',
                            ])
                            ->required()
                            ->default('available'),
                    ])->columns(3),

                \Filament\Schemas\Components\Section::make('Legal Compliance')
                    ->schema([
                        Forms\Components\DatePicker::make('bolo_expiry_date')
                            ->label('Bolo Expiry Date'),
                        Forms\Components\DatePicker::make('inspection_expiry_date')
                            ->label('Inspection Expiry Date'),
                        Forms\Components\FileUpload::make('bolo_certificate_path')
                            ->label('Bolo Certificate')
                            ->directory('legal/bolo'),
                        Forms\Components\FileUpload::make('inspection_certificate_path')
                            ->label('Inspection Certificate')
                            ->directory('legal/inspection'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('plate_number')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('make')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('model')
                    ->searchable(),
                Tables\Columns\TextColumn::make('office.name')
                    ->label('Branch')
                    ->sortable(),
                Tables\Columns\TextColumn::make('price_per_day')
                    ->money('ETB')
                    ->sortable(),
                Tables\Columns\TextColumn::make('mileage')
                    ->numeric()
                    ->label('Mileage (km)')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'available' => 'success',
                        'rented' => 'warning',
                        'maintenance' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('overall_status')
                    ->label('Compliance')
                    ->badge()
                    ->color(fn ($record): string => $record->overall_status),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('office_id')
                    ->relationship('office', 'name')
                    ->label('Filter by Branch'),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'available' => 'Available',
                        'rented' => 'Rented',
                        'maintenance' => 'In Maintenance',
                    ]),
            ])
            ->actions([
                Actions\EditAction::make(),
                Actions\ViewAction::make(),
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
            'index' => Pages\ListCars::route('/'),
            'create' => Pages\CreateCar::route('/create'),
            'edit' => Pages\EditCar::route('/{record}/edit'),
        ];
    }
}
