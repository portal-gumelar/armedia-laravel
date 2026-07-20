<?php

namespace App\Filament\Resources\Radius\RadCheckResource\Pages;

use App\Filament\Resources\Radius\RadCheckResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRadCheck extends EditRecord
{
    protected static string $resource = RadCheckResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
