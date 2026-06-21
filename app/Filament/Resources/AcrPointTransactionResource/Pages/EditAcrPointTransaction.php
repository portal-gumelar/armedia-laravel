<?php

namespace App\Filament\Resources\AcrPointTransactionResource\Pages;

use App\Filament\Resources\AcrPointTransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAcrPointTransaction extends EditRecord
{
    protected static string $resource = AcrPointTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
