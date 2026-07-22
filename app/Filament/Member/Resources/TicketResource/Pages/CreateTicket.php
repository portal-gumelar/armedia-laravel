<?php

namespace App\Filament\Member\Resources\TicketResource\Pages;

use App\Filament\Member\Resources\TicketResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTicket extends CreateRecord
{
    protected static string $resource = TicketResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['customer_id'] = auth('customer')->id();
        $data['status'] = \App\Enums\TicketStatus::OPEN->value;
        return $data;
    }
}
