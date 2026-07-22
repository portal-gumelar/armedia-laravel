<?php

namespace App\Filament\Member\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Invoice;
use App\Models\Ticket;
use App\Models\AcrMember;
use App\Enums\InvoiceStatus;
use App\Enums\TicketStatus;

class BillingSummaryWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $customer = auth('customer')->user();
        
        // 1. Tagihan Belum Dibayar
        $unpaidInvoice = Invoice::where('customer_id', $customer->id)
            ->where('status', InvoiceStatus::BELUM->value)
            ->orderBy('due_date', 'asc')
            ->first();
            
        $tagihanLabel = 'Tagihan Bulan Ini';
        $tagihanValue = $unpaidInvoice ? 'Rp ' . number_format($unpaidInvoice->amount, 0, ',', '.') : 'Lunas ✅';
        $tagihanDesc  = $unpaidInvoice ? 'Jatuh tempo: ' . $unpaidInvoice->due_date->format('d M Y') : 'Tidak ada tagihan tertunggak';
        $tagihanColor = $unpaidInvoice ? 'danger' : 'success';

        // 2. Tiket Aktif
        $activeTicketsCount = Ticket::where('customer_id', $customer->id)
            ->whereIn('status', [TicketStatus::OPEN->value, TicketStatus::PROCESS->value])
            ->count();
            
        // 3. Poin ACR
        $acrMember = AcrMember::where('customer_id', $customer->id)->first();
        $poinValue = $acrMember ? number_format($acrMember->total_poin, 0, ',', '.') : 'Belum Daftar';
        
        return [
            Stat::make($tagihanLabel, $tagihanValue)
                ->description($tagihanDesc)
                ->descriptionIcon($unpaidInvoice ? 'heroicon-m-exclamation-circle' : 'heroicon-m-check-badge')
                ->color($tagihanColor)
                ->url('/member/invoices')
                ->extraAttributes([
                    'class' => 'hover:shadow-lg hover:-translate-y-1 transition-all duration-300 border-t-4 border-t-emerald-500 cursor-pointer',
                ]),
                
            Stat::make('Poin Reward ACR', $poinValue)
                ->description($acrMember ? 'Kumpulkan & Tukar Hadiah' : 'Daftar di dashboard utama')
                ->descriptionIcon('heroicon-m-gift')
                ->color('warning')
                ->extraAttributes([
                    'class' => 'hover:shadow-lg hover:-translate-y-1 transition-all duration-300 border-t-4 border-t-amber-500',
                ]),
                
            Stat::make('Tiket Pengaduan Aktif', $activeTicketsCount)
                ->description('Dalam proses perbaikan')
                ->descriptionIcon('heroicon-m-wrench-screwdriver')
                ->color('info')
                ->url('/member/tickets')
                ->extraAttributes([
                    'class' => 'hover:shadow-lg hover:-translate-y-1 transition-all duration-300 border-t-4 border-t-blue-500 cursor-pointer',
                ]),
        ];
    }
}
