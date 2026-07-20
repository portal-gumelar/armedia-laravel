<?php

namespace App\Filament\Finance\Resources;

use App\Filament\Resources\MarketingFeeResource;

class FinanceMarketingFeeResource extends MarketingFeeResource
{
    protected static ?string $navigationGroup = 'Keuangan';
    protected static ?int $navigationSort = 3;

    // Use the same pages as the original resource
    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Finance\Resources\FinanceMarketingFeeResource\Pages\ListMarketingFees::route('/'),
            'create' => \App\Filament\Finance\Resources\FinanceMarketingFeeResource\Pages\CreateMarketingFee::route('/create'),
            'edit' => \App\Filament\Finance\Resources\FinanceMarketingFeeResource\Pages\EditMarketingFee::route('/{record}/edit'),
        ];
    }
}
