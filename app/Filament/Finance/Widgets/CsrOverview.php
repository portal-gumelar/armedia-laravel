<?php

namespace App\Filament\Finance\Widgets;

use App\Models\CsrContribution;
use App\Services\CsrCalculatorService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class CsrOverview extends BaseWidget
{
    protected static ?int $sort = 3;

    protected function getStats(): array
    {
        $service    = app(CsrCalculatorService::class);
        $liveData   = $service->getLiveData();
        $byVillage  = $service->getLiveDataByVillage();

        $totalCustomer  = $liveData->sum('customer_count');
        $totalCsr       = $liveData->sum('csr_total');
        $totalDesa      = $liveData->sum('desa_share');
        $totalRt        = $liveData->sum('rt_share');
        $villageCount   = $byVillage->count();

        return [
            Stat::make('CSR Bulan Ini', 'Rp ' . number_format($totalCsr, 0, ',', '.'))
                ->description("{$totalCustomer} pelanggan aktif × Rp 3.000")
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('success'),

            Stat::make('Bagian Desa', 'Rp ' . number_format($totalDesa, 0, ',', '.'))
                ->description("Dibagi ke {$villageCount} desa (@ Rp 1.000/pelanggan)")
                ->descriptionIcon('heroicon-m-map-pin')
                ->color('info'),

            Stat::make('Bagian RT', 'Rp ' . number_format($totalRt, 0, ',', '.'))
                ->description('Dibagi ke tiap RT aktif (@ Rp 2.000/pelanggan)')
                ->descriptionIcon('heroicon-m-users')
                ->color('warning'),
        ];
    }
}
