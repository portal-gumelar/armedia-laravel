<?php

namespace App\Filament\Member\Resources\TicketResource\Widgets;

use App\Models\Ticket;
use App\Enums\TicketStatus;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TicketStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $customerId = auth('customer')->id();

        $openCount = Ticket::where('customer_id', $customerId)
            ->whereIn('status', [TicketStatus::OPEN->value, TicketStatus::PROCESS->value])
            ->count();

        $resolvedCount = Ticket::where('customer_id', $customerId)
            ->where('status', TicketStatus::RESOLVED->value)
            ->count();

        $totalCount = Ticket::where('customer_id', $customerId)->count();

        return [
            Stat::make('Tiket Aktif', $openCount . ' Laporan')
                ->description('Sedang dalam penanganan')
                ->descriptionIcon('heroicon-m-clock')
                ->color($openCount > 0 ? 'warning' : 'success')
                ->chart([1, 2, 4, 3, 2, 4, $openCount]),
                
            Stat::make('Tiket Selesai', $resolvedCount . ' Laporan')
                ->description('Kendala telah teratasi')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success')
                ->chart([2, 5, 3, 8, 4, 7, 10]),
                
            Stat::make('Total Laporan', $totalCount . ' Laporan')
                ->description('Riwayat keseluruhan')
                ->descriptionIcon('heroicon-m-clipboard-document-list')
                ->color('primary'),
        ];
    }
}
