<?php

namespace App\Filament\Resources\OntInventoryResource\Pages;

use App\Filament\Resources\OntInventoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOntInventories extends ListRecords
{
    protected static string $resource = OntInventoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
