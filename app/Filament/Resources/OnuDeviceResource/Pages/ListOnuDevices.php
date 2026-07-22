<?php

namespace App\Filament\Resources\OnuDeviceResource\Pages;

use App\Filament\Resources\OnuDeviceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOnuDevices extends ListRecords
{
    protected static string $resource = OnuDeviceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
