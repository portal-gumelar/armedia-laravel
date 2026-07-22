<?php

namespace App\Filament\Resources\CsrDistributionResource\Pages;

use App\Filament\Resources\CsrDistributionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCsrDistributions extends ListRecords
{
    protected static string $resource = CsrDistributionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
