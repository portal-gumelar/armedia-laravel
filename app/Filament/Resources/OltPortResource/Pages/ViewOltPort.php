<?php

namespace App\Filament\Resources\OltPortResource\Pages;

use App\Filament\Resources\OltPortResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewOltPort extends ViewRecord
{
    protected static string $resource = OltPortResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
