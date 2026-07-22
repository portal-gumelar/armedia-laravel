<?php

namespace App\Filament\Resources\OnuDeviceResource\Pages;

use App\Filament\Resources\OnuDeviceResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewOnuDevice extends ViewRecord
{
    protected static string $resource = OnuDeviceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
