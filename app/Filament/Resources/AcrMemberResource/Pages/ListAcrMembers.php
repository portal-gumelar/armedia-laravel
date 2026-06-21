<?php

namespace App\Filament\Resources\AcrMemberResource\Pages;

use App\Filament\Resources\AcrMemberResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAcrMembers extends ListRecords
{
    protected static string $resource = AcrMemberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
