<?php

namespace App\Filament\Resources\WebSettingResource\Pages;

use App\Filament\Resources\WebSettingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWebSetting extends EditRecord
{
    protected static string $resource = WebSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
