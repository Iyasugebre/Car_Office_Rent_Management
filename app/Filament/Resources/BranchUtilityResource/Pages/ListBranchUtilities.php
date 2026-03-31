<?php

namespace App\Filament\Resources\BranchUtilityResource\Pages;

use App\Filament\Resources\BranchUtilityResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBranchUtilities extends ListRecords
{
    protected static string $resource = BranchUtilityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
