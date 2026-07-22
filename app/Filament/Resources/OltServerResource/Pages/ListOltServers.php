<?php

namespace App\Filament\Resources\OltServerResource\Pages;

use App\Filament\Resources\OltServerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOltServers extends ListRecords
{
    protected static string $resource = OltServerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
