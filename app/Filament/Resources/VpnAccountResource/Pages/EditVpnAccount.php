<?php

namespace App\Filament\Resources\VpnAccountResource\Pages;

use App\Filament\Resources\VpnAccountResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVpnAccount extends EditRecord
{
    protected static string $resource = VpnAccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
