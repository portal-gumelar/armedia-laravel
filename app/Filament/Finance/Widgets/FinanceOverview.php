<?php

namespace App\Filament\Finance\Widgets;

use App\Enums\InvoiceStatus;
use App\Models\Expense;
use App\Models\Invoice;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class FinanceOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth   = Carbon::now()->endOfMonth();

        // 1. Pemasukan Bulan Ini (Lunas)
        $revenueThisMonth = Invoice::where('status', InvoiceStatus::LUNAS->value)
            ->whereBetween('paid_at', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        // 2. Tagihan Belum Dibayar
        $unpaidCount = Invoice::where('status', InvoiceStatus::BELUM->value)->count();
        $unpaidTotal = Invoice::where('status', InvoiceStatus::BELUM->value)->sum('amount');

        // 3. Pengeluaran Bulan Ini
        $expenseThisMonth = Expense::whereBetween('expense_date', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        return [
            Stat::make('Pemasukan (Bulan Ini)', 'Rp ' . number_format($revenueThisMonth, 0, ',', '.'))
                ->description('Total invoice terbayar')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('Tagihan Menunggak', 'Rp ' . number_format($unpaidTotal, 0, ',', '.'))
                ->description($unpaidCount . ' invoice belum dibayar')
                ->descriptionIcon('heroicon-m-exclamation-circle')
                ->color('warning'),

            Stat::make('Pengeluaran (Bulan Ini)', 'Rp ' . number_format($expenseThisMonth, 0, ',', '.'))
                ->description('Total biaya operasional & lainnya')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger'),
        ];
    }
}
