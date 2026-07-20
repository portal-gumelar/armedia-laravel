<?php

namespace App\Filament\Resources\MarketingFeeResource\Pages;

use App\Filament\Resources\MarketingFeeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMarketingFee extends EditRecord
{
    protected static string $resource = MarketingFeeResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
