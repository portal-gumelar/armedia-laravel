<?php

namespace App\Filament\Finance\Resources\FinanceMarketingFeeResource\Pages;

use App\Filament\Finance\Resources\FinanceMarketingFeeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

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
