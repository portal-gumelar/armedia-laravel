<?php

namespace App\Filament\Resources\MarketingReferralResource\Pages;

use App\Filament\Resources\MarketingReferralResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMarketingReferral extends EditRecord
{
    protected static string $resource = MarketingReferralResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
