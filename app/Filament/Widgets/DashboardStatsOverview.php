<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
use App\Models\Invoice;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class DashboardStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // 1. Total Active Customers
        $activeCustomers = Customer::where('subscription_status', '!=', 'isolir')->count();

        // 2. Total Isolated Customers
        $isolatedCustomers = Customer::where('subscription_status', 'isolir')->count();

        // 3. Unpaid Invoices Total for current month
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        
        $unpaidTotal = Invoice::where('status', '!=', 'lunas')
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->sum('amount');

        // 4. Monthly Income (Paid Invoices)
        $incomeTotal = Invoice::where('status', 'lunas')
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->sum('amount');

        return [
            Stat::make('Pelanggan Aktif', $activeCustomers)
                ->description('Total pelanggan tidak isolir')
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),
                
            Stat::make('Pelanggan Isolir', $isolatedCustomers)
                ->description('Perlu tindak lanjut')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger'),
                
            Stat::make('Tunggakan Bulan Ini', 'Rp ' . number_format($unpaidTotal, 0, ',', '.'))
                ->description('Tagihan belum lunas')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('warning'),
                
            Stat::make('Pendapatan Bulan Ini', 'Rp ' . number_format($incomeTotal, 0, ',', '.'))
                ->description('Total tagihan lunas')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('primary'),
        ];
    }
}
