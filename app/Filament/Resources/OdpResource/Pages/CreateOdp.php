<?php

namespace App\Filament\Resources\OdpResource\Pages;

use App\Filament\Resources\OdpResource;
use Filament\Resources\Pages\CreateRecord;

class CreateOdp extends CreateRecord
{
    protected static string $resource = OdpResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (isset($data['koordinat']['lat']) && isset($data['koordinat']['lng'])) {
            $data['latitude']  = $data['koordinat']['lat'];
            $data['longitude'] = $data['koordinat']['lng'];
        }
        unset($data['koordinat']);

        return $data;
    }
}
