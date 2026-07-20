<?php

namespace App\Filament\Mitra\Resources\CustomerResource\Pages;

use App\Filament\Mitra\Resources\CustomerResource;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;

class CreateCustomer extends CreateRecord
{
    protected static string $resource = CustomerResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['mitra_id'] = Filament::getTenant()?->id;
        return $data;
    }
}
