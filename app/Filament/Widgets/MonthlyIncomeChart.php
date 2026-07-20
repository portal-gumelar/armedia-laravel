<?php

namespace App\Filament\Widgets;

use App\Models\Invoice;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MonthlyIncomeChart extends ChartWidget
{
    protected static ?string $heading = 'Grafik Pendapatan (6 Bulan Terakhir)';
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $months = collect();
        $incomes = collect();

        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $months->push($month->translatedFormat('M Y'));
            
            $income = Invoice::where('payment_status', 'Lunas')
                ->whereMonth('created_at', $month->month)
                ->whereYear('created_at', $month->year)
                ->sum('total_amount');
                
            $incomes->push($income);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Pendapatan Bersih (Lunas)',
                    'data' => $incomes->toArray(),
                    'fill' => 'start',
                    'borderColor' => '#10b981', // Emerald/Success color
                    'backgroundColor' => 'rgba(16, 185, 129, 0.2)',
                ],
            ],
            'labels' => $months->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
