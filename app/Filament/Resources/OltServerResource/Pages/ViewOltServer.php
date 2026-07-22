<?php

namespace App\Filament\Resources\OltServerResource\Pages;

use App\Filament\Resources\OltServerResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewOltServer extends ViewRecord
{
    protected static string $resource = OltServerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
