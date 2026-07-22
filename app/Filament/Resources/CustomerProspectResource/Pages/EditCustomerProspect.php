<?php

namespace App\Filament\Resources\CustomerProspectResource\Pages;

use App\Filament\Resources\CustomerProspectResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCustomerProspect extends EditRecord
{
    protected static string $resource = CustomerProspectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
