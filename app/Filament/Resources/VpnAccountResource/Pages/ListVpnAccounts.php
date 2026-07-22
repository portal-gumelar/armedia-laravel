<?php

namespace App\Filament\Resources\VpnAccountResource\Pages;

use App\Filament\Resources\VpnAccountResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVpnAccounts extends ListRecords
{
    protected static string $resource = VpnAccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
