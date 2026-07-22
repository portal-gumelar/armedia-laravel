<?php

namespace App\Filament\Resources\AttenuationLogResource\Pages;

use App\Filament\Resources\AttenuationLogResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAttenuationLogs extends ListRecords
{
    protected static string $resource = AttenuationLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
