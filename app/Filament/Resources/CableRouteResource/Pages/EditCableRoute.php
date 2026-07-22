<?php

namespace App\Filament\Resources\CableRouteResource\Pages;

use App\Filament\Resources\CableRouteResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCableRoute extends EditRecord
{
    protected static string $resource = CableRouteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
