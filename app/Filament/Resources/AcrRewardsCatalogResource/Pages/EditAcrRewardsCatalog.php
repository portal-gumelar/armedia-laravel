<?php

namespace App\Filament\Resources\AcrRewardsCatalogResource\Pages;

use App\Filament\Resources\AcrRewardsCatalogResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAcrRewardsCatalog extends EditRecord
{
    protected static string $resource = AcrRewardsCatalogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
