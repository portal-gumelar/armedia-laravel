<?php

namespace App\Filament\Member\Resources\InvoiceResource\Widgets;

use App\Models\Invoice;
use App\Enums\InvoiceStatus;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class InvoiceStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $customerId = auth('customer')->id();

        $unpaidCount = Invoice::where('customer_id', $customerId)
            ->where('status', InvoiceStatus::BELUM->value)
            ->count();

        $unpaidTotal = Invoice::where('customer_id', $customerId)
            ->where('status', InvoiceStatus::BELUM->value)
            ->sum('amount');

        $paidThisMonth = Invoice::where('customer_id', $customerId)
            ->where('status', InvoiceStatus::LUNAS->value)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');

        return [
            Stat::make('Tagihan Belum Lunas', $unpaidCount . ' Tagihan')
                ->description('Total Tunggakan: Rp ' . number_format($unpaidTotal, 0, ',', '.'))
                ->descriptionIcon($unpaidCount > 0 ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check-badge')
                ->color($unpaidCount > 0 ? 'danger' : 'success')
                ->chart($unpaidCount > 0 ? [7, 2, 10, 3, 15, 4, 17] : [1, 2, 3, 2, 1, 2, 1]),
                
            Stat::make('Pembayaran Bulan Ini', 'Rp ' . number_format($paidThisMonth, 0, ',', '.'))
                ->description('Berhasil Dibayar')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success')
                ->chart([1, 4, 3, 8, 5, 10, 15]),
        ];
    }
}
