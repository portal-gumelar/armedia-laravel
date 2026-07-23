<?php

namespace App\Filament\Widgets;

use App\Models\Invoice;
use App\Models\MarketingFee;
use App\Models\OperationalExpense;
use App\Enums\InvoiceStatus;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class FinancialOverviewWidget extends BaseWidget
{
    protected static ?string $pollingInterval = null; // No polling needed for finance

    protected function getStats(): array
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        // 1. Pemasukan (Invoices Lunas bulan ini)
        $revenue = Invoice::where('status', InvoiceStatus::LUNAS->value)
            ->whereBetween('paid_at', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        // 2. Pengeluaran Operasional bulan ini
        $operationalExpenses = OperationalExpense::whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->sum('total_harga');

        // 3. Pengeluaran Marketing Fee bulan ini
        $marketingFees = MarketingFee::whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->sum('total_fee');

        $totalExpenses = $operationalExpenses + $marketingFees;

        // 4. Laba Bersih
        $netProfit = $revenue - $totalExpenses;

        return [
            Stat::make('Pemasukan (Bulan Ini)', 'Rp ' . number_format($revenue, 0, ',', '.'))
                ->description('Dari tagihan internet lunas')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            
            Stat::make('Pengeluaran (Bulan Ini)', 'Rp ' . number_format($totalExpenses, 0, ',', '.'))
                ->description('Operasional & Fee Marketing')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger'),
            
            Stat::make('Laba Bersih', 'Rp ' . number_format($netProfit, 0, ',', '.'))
                ->description('Pemasukan - Pengeluaran')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color($netProfit >= 0 ? 'success' : 'danger'),
        ];
    }
}
