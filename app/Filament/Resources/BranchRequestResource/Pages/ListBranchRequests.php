<?php

namespace App\Filament\Resources\BranchRequestResource\Pages;

use App\Filament\Resources\BranchRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBranchRequests extends ListRecords
{
    protected static string $resource = BranchRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
