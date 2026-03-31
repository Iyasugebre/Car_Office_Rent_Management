<?php

namespace App\Filament\Resources\BranchRequestResource\Pages;

use App\Filament\Resources\BranchRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateBranchRequest extends CreateRecord
{
    protected static string $resource = BranchRequestResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['requester_id'] = auth()->id();
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
