<?php

namespace App\Filament\Resources\OdpResource\Pages;

use App\Filament\Resources\OdpResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOdp extends EditRecord
{
    protected static string $resource = OdpResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Sync koordinat dari MapPicker ke kolom latitude/longitude di tabel
        if (isset($data['koordinat']['lat']) && isset($data['koordinat']['lng'])) {
            $data['latitude']  = $data['koordinat']['lat'];
            $data['longitude'] = $data['koordinat']['lng'];
        }
        unset($data['koordinat']);

        return $data;
    }
}
