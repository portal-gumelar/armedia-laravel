<?php

namespace App\Filament\Finance\Resources\FinanceMarketingFeeResource\Pages;

use App\Filament\Finance\Resources\FinanceMarketingFeeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMarketingFees extends ListRecords
{
    protected static string $resource = FinanceMarketingFeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
