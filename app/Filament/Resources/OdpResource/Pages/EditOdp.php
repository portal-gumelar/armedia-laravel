<?php

namespace App\Filament\Resources\OdpResource\Pages;

use App\Filament\Resources\OdpResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOdp extends EditRecord
{
    protected static string $resource = OdpResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
