<?php

namespace App\Filament\Resources\OltPortResource\Pages;

use App\Filament\Resources\OltPortResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOltPorts extends ListRecords
{
    protected static string $resource = OltPortResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
