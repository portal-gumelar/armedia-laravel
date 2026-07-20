<?php

namespace App\Filament\Widgets;

use App\Enums\CustomerSubscriptionStatus;
use App\Enums\DeviceStatus;
use App\Enums\MonitoringStatus;
use App\Models\Customer;
use App\Models\Device;
use App\Models\Invoice;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class IspStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $activeCount = Customer::where('subscription_status', CustomerSubscriptionStatus::AKTIF->value)->count();

        $deviceTerpasang = Device::where('status', DeviceStatus::TERPASANG->value)->count();
        $deviceStok      = Device::where('status', DeviceStatus::STOK->value)->count();

        $pendapatan = Customer::with('internetPackage')
            ->where('subscription_status', CustomerSubscriptionStatus::AKTIF->value)
            ->get()
            ->sum(fn ($c) => $c->internetPackage?->harga ?? 0);

        $downCount = Customer::where('monitoring_status', MonitoringStatus::DOWN->value)->count();

        return [
            Stat::make('Pelanggan Aktif', number_format($activeCount))
                ->description('Total langganan berjalan')
                ->color('success')
                ->icon('heroicon-o-users'),

            Stat::make('Perangkat ONT', "{$deviceTerpasang} terpasang / {$deviceStok} stok")
                ->description('Inventaris perangkat')
                ->color('info')
                ->icon('heroicon-o-cpu-chip'),

            Stat::make('Est. Pendapatan Bulan Ini', 'Rp ' . number_format($pendapatan))
                ->description('Berdasarkan harga paket pelanggan aktif')
                ->color('warning')
                ->icon('heroicon-o-banknotes'),

            Stat::make('Pelanggan DOWN', number_format($downCount))
                ->description('Status monitoring terakhir: offline')
                ->color($downCount > 0 ? 'danger' : 'success')
                ->icon('heroicon-o-exclamation-circle'),
        ];
    }
}
