<?php

namespace App\Filament\Widgets;

use App\Models\Invoice;
use App\Models\MarketingFee;
use App\Models\OperationalExpense;
use App\Enums\InvoiceStatus;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class FinancialChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Grafik Keuangan (6 Bulan Terakhir)';
    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $months = [];
        $revenueData = [];
        $expenseData = [];

        // Get data for the last 6 months
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $start = $month->copy()->startOfMonth();
            $end = $month->copy()->endOfMonth();

            $months[] = $month->translatedFormat('M Y');

            // Revenue
            $revenue = Invoice::where('status', InvoiceStatus::LUNAS->value)
                ->whereBetween('paid_at', [$start, $end])
                ->sum('amount');
            $revenueData[] = $revenue;

            // Expenses
            $opExpense = OperationalExpense::whereBetween('created_at', [$start, $end])->sum('total_harga');
            $marketing = MarketingFee::whereBetween('created_at', [$start, $end])->sum('total_fee');
            $expenseData[] = $opExpense + $marketing;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Pendapatan',
                    'data' => $revenueData,
                    'backgroundColor' => 'rgba(16, 185, 129, 0.8)', // Emerald / Success
                    'borderColor' => 'rgb(16, 185, 129)',
                ],
                [
                    'label' => 'Pengeluaran',
                    'data' => $expenseData,
                    'backgroundColor' => 'rgba(239, 68, 68, 0.8)', // Rose / Danger
                    'borderColor' => 'rgb(239, 68, 68)',
                ],
            ],
            'labels' => $months,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
