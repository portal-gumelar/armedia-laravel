<?php

namespace App\Filament\Resources\OperationalExpenseResource\Pages;

use App\Filament\Resources\OperationalExpenseResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOperationalExpense extends EditRecord
{
    protected static string $resource = OperationalExpenseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
