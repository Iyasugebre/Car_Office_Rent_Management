<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AgreementResource\Pages;
use App\Models\Agreement;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions;

class AgreementResource extends Resource
{
    protected static ?string $model = Agreement::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-document-text';

    protected static string | \UnitEnum | null $navigationGroup = 'Rent Management';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                \Filament\Schemas\Components\Section::make('Agreement Details')
                    ->schema([
                        Forms\Components\TextInput::make('agreement_id')
                            ->required()
                            ->unique(ignoreRecord: true),
                        Forms\Components\Select::make('branch_request_id')
                            ->relationship('branchRequest', 'branch_name')
                            ->label('Branch Request')
                            ->required()
                            ->searchable(),
                        Forms\Components\TextInput::make('landlord_name')
                            ->required(),
                        Forms\Components\Textarea::make('property_address')
                            ->required()
                            ->columnSpanFull(),
                    ])->columns(2),

                \Filament\Schemas\Components\Section::make('Terms & Pricing')
                    ->schema([
                        Forms\Components\DatePicker::make('start_date')
                            ->required(),
                        Forms\Components\DatePicker::make('end_date')
                            ->required(),
                        Forms\Components\TextInput::make('monthly_rent')
                            ->numeric()
                            ->prefix('ETB')
                            ->required(),
                        Forms\Components\Select::make('payment_schedule')
                            ->options([
                                'Monthly' => 'Monthly',
                                'Quarterly' => 'Quarterly',
                                'Annually' => 'Annually',
                            ])
                            ->required()
                            ->default('Monthly'),
                    ])->columns(2),

                \Filament\Schemas\Components\Section::make('Status & Legal')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'active' => 'Active',
                                'expired' => 'Expired',
                                'terminated' => 'Terminated',
                            ])
                            ->required()
                            ->default('active'),
                        Forms\Components\Select::make('legal_status')
                            ->options([
                                'pending' => 'Pending',
                                'approved' => 'Approved',
                                'rejected' => 'Rejected',
                            ])
                            ->default('pending'),
                        Forms\Components\FileUpload::make('contract_path')
                            ->label('Contract Document')
                            ->directory('contracts')
                            ->acceptedFileTypes(['application/pdf'])
                            ->columnSpanFull(),
                        Forms\Components\DatePicker::make('next_rent_due_at')
                            ->label('Next Rent Payment Due'),
                        Forms\Components\DatePicker::make('last_rent_paid_at')
                            ->label('Last Rent Paid At'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('agreement_id')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('branchRequest.branch_name')
                    ->label('Branch')
                    ->searchable(),
                Tables\Columns\TextColumn::make('landlord_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'draft' => 'gray',
                        'expired' => 'warning',
                        'terminated' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('legal_status')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'approved' => 'success',
                        'pending' => 'warning',
                        'rejected' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('end_date')
                    ->date()
                    ->sortable()
                    ->label('Expiry'),
                Tables\Columns\TextColumn::make('monthly_rent')
                    ->money('ETB')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'active' => 'Active',
                        'expired' => 'Expired',
                        'terminated' => 'Terminated',
                    ]),
                Tables\Filters\SelectFilter::make('legal_status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
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
            'index' => Pages\ListAgreements::route('/'),
            'create' => Pages\CreateAgreement::route('/create'),
            'edit' => Pages\EditAgreement::route('/{record}/edit'),
        ];
    }
}
