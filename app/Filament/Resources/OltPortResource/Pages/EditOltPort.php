<?php

namespace App\Filament\Resources\OltPortResource\Pages;

use App\Filament\Resources\OltPortResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOltPort extends EditRecord
{
    protected static string $resource = OltPortResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
