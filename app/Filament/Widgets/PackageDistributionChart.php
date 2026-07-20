<?php

namespace App\Filament\Widgets;

use App\Enums\CustomerSubscriptionStatus;
use App\Models\Customer;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class PackageDistributionChart extends ChartWidget
{
    protected static ?string $heading = 'Distribusi Pelanggan per Paket';
    protected static ?int    $sort    = 2;

    protected function getData(): array
    {
        $data = Customer::with('internetPackage')
            ->where('subscription_status', CustomerSubscriptionStatus::AKTIF->value)
            ->select('internet_package_id', DB::raw('COUNT(*) as count'))
            ->groupBy('internet_package_id')
            ->get();

        $labels = [];
        $counts = [];
        $colors = [];

        $palette = [
            '#ef4444', '#f97316', '#eab308', '#22c55e',
            '#06b6d4', '#3b82f6', '#8b5cf6', '#ec4899',
        ];

        foreach ($data as $i => $row) {
            $labels[] = $row->internetPackage?->nama_paket ?? '(Tanpa Paket)';
            $counts[] = $row->count;
            $colors[] = $palette[$i % count($palette)];
        }

        return [
            'datasets' => [
                [
                    'label'           => 'Pelanggan',
                    'data'            => $counts,
                    'backgroundColor' => $colors,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
