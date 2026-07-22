<?php

namespace App\Filament\Resources\CableRouteResource\Pages;

use App\Filament\Resources\CableRouteResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCableRoutes extends ListRecords
{
    protected static string $resource = CableRouteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
