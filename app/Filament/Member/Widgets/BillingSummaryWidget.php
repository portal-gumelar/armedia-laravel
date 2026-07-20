<?php

namespace App\Filament\Member\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Invoice;
use App\Models\Ticket;
use App\Models\AcrMember;

class BillingSummaryWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $customer = auth('customer')->user();
        
        // 1. Tagihan Belum Dibayar
        $unpaidInvoice = Invoice::where('customer_id', $customer->id)
            ->where('status', 'unpaid')
            ->orderBy('due_date', 'asc')
            ->first();
            
        $tagihanLabel = 'Tagihan Bulan Ini';
        $tagihanValue = $unpaidInvoice ? 'Rp ' . number_format($unpaidInvoice->amount, 0, ',', '.') : 'Lunas ✅';
        $tagihanDesc  = $unpaidInvoice ? 'Jatuh tempo: ' . $unpaidInvoice->due_date->format('d M Y') : 'Tidak ada tagihan tertunggak';
        $tagihanColor = $unpaidInvoice ? 'danger' : 'success';

        // 2. Tiket Aktif
        $activeTicketsCount = Ticket::where('customer_id', $customer->id)
            ->whereIn('status', ['open', 'in_progress'])
            ->count();
            
        // 3. Poin ACR
        $acrMember = AcrMember::where('customer_id', $customer->id)->first();
        $poinValue = $acrMember ? number_format($acrMember->total_poin, 0, ',', '.') : 'Belum Daftar';
        
        return [
            Stat::make($tagihanLabel, $tagihanValue)
                ->description($tagihanDesc)
                ->descriptionIcon($unpaidInvoice ? 'heroicon-m-exclamation-circle' : 'heroicon-m-check-badge')
                ->color($tagihanColor),
                
            Stat::make('Poin Reward ACR', $poinValue)
                ->description('Kumpulkan & Tukar Hadiah')
                ->descriptionIcon('heroicon-m-gift')
                ->color('warning'),
                
            Stat::make('Tiket Pengaduan Aktif', $activeTicketsCount)
                ->description('Dalam proses perbaikan')
                ->descriptionIcon('heroicon-m-wrench-screwdriver')
                ->color('info'),
        ];
    }
}
