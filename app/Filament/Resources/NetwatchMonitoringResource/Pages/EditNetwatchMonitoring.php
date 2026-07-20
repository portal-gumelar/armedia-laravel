<?php

namespace App\Filament\Resources\NetwatchMonitoringResource\Pages;

use App\Filament\Resources\NetwatchMonitoringResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditNetwatchMonitoring extends EditRecord
{
    protected static string $resource = NetwatchMonitoringResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
