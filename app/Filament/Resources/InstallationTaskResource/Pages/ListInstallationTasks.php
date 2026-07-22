<?php

namespace App\Filament\Resources\InstallationTaskResource\Pages;

use App\Filament\Resources\InstallationTaskResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInstallationTasks extends ListRecords
{
    protected static string $resource = InstallationTaskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
