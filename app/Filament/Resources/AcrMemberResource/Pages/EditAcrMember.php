<?php

namespace App\Filament\Resources\AcrMemberResource\Pages;

use App\Filament\Resources\AcrMemberResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAcrMember extends EditRecord
{
    protected static string $resource = AcrMemberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
