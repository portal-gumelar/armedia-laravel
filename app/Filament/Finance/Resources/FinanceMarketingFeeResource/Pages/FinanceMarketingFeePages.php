<?php

namespace App\Filament\Finance\Resources\FinanceMarketingFeeResource\Pages;

use App\Filament\Finance\Resources\FinanceMarketingFeeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;

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

class CreateMarketingFee extends CreateRecord
{
    protected static string $resource = FinanceMarketingFeeResource::class;
}

class EditMarketingFee extends EditRecord
{
    protected static string $resource = FinanceMarketingFeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
