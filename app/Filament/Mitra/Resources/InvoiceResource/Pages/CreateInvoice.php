<?php

namespace App\Filament\Mitra\Resources\InvoiceResource\Pages;

use App\Filament\Mitra\Resources\InvoiceResource;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;

class CreateInvoice extends CreateRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['mitra_id'] = Filament::getTenant()?->id;
        return $data;
    }
}
