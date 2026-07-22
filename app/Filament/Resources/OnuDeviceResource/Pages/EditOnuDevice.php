<?php

namespace App\Filament\Resources\OnuDeviceResource\Pages;

use App\Filament\Resources\OnuDeviceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOnuDevice extends EditRecord
{
    protected static string $resource = OnuDeviceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
