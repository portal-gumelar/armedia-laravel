<?php

namespace App\Filament\Resources\CustomerProspectResource\Pages;

use App\Filament\Resources\CustomerProspectResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCustomerProspects extends ListRecords
{
    protected static string $resource = CustomerProspectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
