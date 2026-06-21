<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\AcrPointTransaction;
use Carbon\Carbon;

class PointTransactionChart extends ChartWidget
{
    protected static ?string $heading = 'Aktivitas Transaksi Poin per Bulan';
    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $dataPenambahan = [];
        $dataPengurangan = [];
        $labels = [];

        // Ambil data 6 bulan terakhir
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $labels[] = $month->translatedFormat('M Y');
            
            $penambahan = AcrPointTransaction::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->where('transaction_type', 'penambahan')
                ->sum('points');
                
            $pengurangan = AcrPointTransaction::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->where('transaction_type', 'pengurangan')
                ->sum('points');
                
            $dataPenambahan[] = $penambahan;
            $dataPengurangan[] = $pengurangan;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Penambahan Poin',
                    'data' => $dataPenambahan,
                    'borderColor' => '#3b82f6', // Tailwind blue
                    'backgroundColor' => 'rgba(59, 130, 246, 0.2)',
                ],
                [
                    'label' => 'Pengurangan Poin',
                    'data' => $dataPengurangan,
                    'borderColor' => '#ef4444', // Tailwind red
                    'backgroundColor' => 'rgba(239, 68, 68, 0.2)',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
