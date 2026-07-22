<?php

namespace App\Filament\Resources\ProrataRateResource\Pages;

use App\Filament\Resources\ProrataRateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProrataRates extends ListRecords
{
    protected static string $resource = ProrataRateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
