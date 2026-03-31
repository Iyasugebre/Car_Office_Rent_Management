<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceRequestResource\Pages;
use App\Models\ServiceRequest;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions;

class ServiceRequestResource extends Resource
{
    protected static ?string $model = ServiceRequest::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-wrench';

    protected static string | \UnitEnum | null $navigationGroup = 'Fleet Management';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                \Filament\Schemas\Components\Section::make('Vehicle & Issue')
                    ->schema([
                        Forms\Components\Select::make('car_id')
                            ->relationship('car', 'plate_number')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('requester_id')
                            ->relationship('requester', 'name')
                            ->required()
                            ->default(auth()->id()),
                        Forms\Components\Select::make('service_type')
                            ->options([
                                'routine' => 'Routine Maintenance',
                                'repair' => 'Emergency Repair',
                                'inspection' => 'Annual Inspection',
                                'tires' => 'Tire Replacement',
                            ])
                            ->required(),
                        Forms\Components\Select::make('urgency_level')
                            ->options([
                                'low' => 'Low',
                                'medium' => 'Medium',
                                'high' => 'High',
                                'critical' => 'Critical',
                            ])
                            ->required(),
                    ])->columns(2),

                \Filament\Schemas\Components\Section::make('Description & Costs')
                    ->schema([
                        Forms\Components\Textarea::make('problem_description')
                            ->required()
                            ->rows(3)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('service_provider')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('cost')
                            ->numeric()
                            ->prefix('ETB'),
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending Approval',
                                'approved' => 'Approved',
                                'in_progress' => 'In Progress',
                                'completed' => 'Completed',
                                'rejected' => 'Rejected',
                            ])
                            ->required()
                            ->default('pending'),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('car.plate_number')
                    ->label('Vehicle')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('service_type')
                    ->badge(),
                Tables\Columns\TextColumn::make('urgency_level')
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
                        'completed' => 'success',
                        'approved' => 'info',
                        'rejected' => 'danger',
                        'in_progress' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('cost')
                    ->money('ETB')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->label('Requested on')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status'),
                Tables\Filters\SelectFilter::make('urgency_level'),
                Tables\Filters\SelectFilter::make('car_id')
                    ->relationship('car', 'plate_number'),
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
            'index' => Pages\ListServiceRequests::route('/'),
            'create' => Pages\CreateServiceRequest::route('/create'),
            'edit' => Pages\EditServiceRequest::route('/{record}/edit'),
        ];
    }
}
