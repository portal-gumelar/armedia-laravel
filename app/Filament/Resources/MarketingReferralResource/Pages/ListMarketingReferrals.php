<?php

namespace App\Filament\Resources\MarketingReferralResource\Pages;

use App\Filament\Resources\MarketingReferralResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMarketingReferrals extends ListRecords
{
    protected static string $resource = MarketingReferralResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
