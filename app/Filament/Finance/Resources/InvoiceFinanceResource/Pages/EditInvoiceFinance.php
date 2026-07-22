<?php

namespace App\Filament\Finance\Resources\InvoiceFinanceResource\Pages;

use App\Filament\Finance\Resources\InvoiceFinanceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInvoiceFinance extends EditRecord
{
    protected static string $resource = InvoiceFinanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
