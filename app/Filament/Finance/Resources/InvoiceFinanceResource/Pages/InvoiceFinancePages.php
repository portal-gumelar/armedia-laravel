<?php

namespace App\Filament\Finance\Resources\InvoiceFinanceResource\Pages;

use App\Filament\Finance\Resources\InvoiceFinanceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\EditRecord;

class ListInvoiceFinance extends ListRecords
{
    protected static string $resource = InvoiceFinanceResource::class;
}

class EditInvoiceFinance extends EditRecord
{
    protected static string $resource = InvoiceFinanceResource::class;
}
