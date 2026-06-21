<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Registration;
use Carbon\Carbon;

class RegistrationChart extends ChartWidget
{
    protected static ?string $heading = 'Pendaftar Baru per Bulan';
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $data = [];
        $labels = [];

        // Ambil data 6 bulan terakhir
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $labels[] = $month->translatedFormat('M Y');
            
            $count = Registration::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
                
            $data[] = $count;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Pendaftar Baru',
                    'data' => $data,
                    'borderColor' => '#10b981', // Tailwind success color
                    'backgroundColor' => 'rgba(16, 185, 129, 0.2)',
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
