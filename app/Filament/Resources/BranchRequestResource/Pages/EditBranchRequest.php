<?php

namespace App\Filament\Resources\BranchRequestResource\Pages;

use App\Filament\Resources\BranchRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBranchRequest extends EditRecord
{
    protected static string $resource = BranchRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
