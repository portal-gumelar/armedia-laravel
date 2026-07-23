<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Filament\Widgets\FinancialOverviewWidget;
use App\Filament\Widgets\FinancialChartWidget;

class FinancialDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-pie';
    protected static ?string $navigationGroup = 'Laporan & Keuangan';
    protected static ?string $navigationLabel = 'Dashboard Laba/Rugi';
    protected static ?string $title = 'Laporan Laba & Rugi';
    protected static ?int $navigationSort = 0;

    protected static string $view = 'filament.pages.financial-dashboard';

    protected function getHeaderWidgets(): array
    {
        return [
            FinancialOverviewWidget::class,
            FinancialChartWidget::class,
        ];
    }
}
