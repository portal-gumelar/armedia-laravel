<?php

namespace App\Filament\Widgets;

use App\Models\Ticket;
use App\Enums\TicketStatus;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class TechnicianPerformanceWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $user = auth()->user();
        $isTeknisiOnly = $user->hasRole('teknisi') && !$user->hasRole(['super_admin', 'admin', 'cs']);

        $query = Ticket::query();
        if ($isTeknisiOnly) {
            $query->where('assigned_to', $user->id);
        }

        $totalOpen = (clone $query)->whereIn('status', [TicketStatus::OPEN->value, TicketStatus::IN_PROGRESS->value])->count();
        $totalResolved = (clone $query)->whereIn('status', [TicketStatus::RESOLVED->value, TicketStatus::CLOSED->value])->count();
        
        // SLA calculation: Average hours between created_at and resolved_at for resolved tickets
        $avgSla = (clone $query)
            ->whereNotNull('resolved_at')
            ->select(DB::raw('AVG(TIMESTAMPDIFF(HOUR, created_at, resolved_at)) as avg_hours'))
            ->value('avg_hours');
            
        $avgSlaFormatted = $avgSla ? number_format($avgSla, 1) . ' Jam' : 'N/A';

        return [
            Stat::make('Tiket Aktif (Open)', $totalOpen)
                ->description('Tiket yang menunggu penanganan')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
                
            Stat::make('Tiket Selesai (Resolved)', $totalResolved)
                ->description('Total tiket berhasil diselesaikan')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
                
            Stat::make('Rata-Rata Waktu Selesai (SLA)', $avgSlaFormatted)
                ->description('Durasi penanganan sejak tiket dibuat')
                ->descriptionIcon('heroicon-m-bolt')
                ->color($avgSla && $avgSla <= 24 ? 'success' : 'danger'),
        ];
    }
}
