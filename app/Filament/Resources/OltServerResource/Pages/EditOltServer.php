<?php

namespace App\Filament\Resources\OltServerResource\Pages;

use App\Filament\Resources\OltServerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOltServer extends EditRecord
{
    protected static string $resource = OltServerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
