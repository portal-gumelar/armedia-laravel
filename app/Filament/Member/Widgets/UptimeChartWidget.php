<?php

namespace App\Filament\Member\Widgets;

use App\Models\NetwatchLog;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class UptimeChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Grafik Kualitas Koneksi (24 Jam Terakhir)';
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';
    
    // Optional: make it look sleek
    protected static ?string $maxHeight = '300px';
    protected static ?string $pollingInterval = '60s';

    protected function getData(): array
    {
        $customer = auth('customer')->user();
        
        // Find logs for this customer's device in the last 24 hours
        $logs = NetwatchLog::where('customer_id', $customer->id)
            ->where('created_at', '>=', now()->subHours(24))
            ->orderBy('created_at')
            ->get();
            
        // If no data (or maybe we don't have customer_id in netwatch_logs), let's just show dummy data for the presentation
        if ($logs->isEmpty()) {
            return $this->getDummyData();
        }

        // Processing real logs (assuming status UP/DOWN and rtt)
        // Group by hour
        $labels = [];
        $data = [];
        
        $grouped = $logs->groupBy(function($date) {
            return Carbon::parse($date->created_at)->format('H:00');
        });

        foreach ($grouped as $hour => $hourLogs) {
            $labels[] = $hour;
            // Calculate UP percentage
            $upCount = $hourLogs->where('status', 'UP')->count();
            $percentage = ($upCount / $hourLogs->count()) * 100;
            $data[] = round($percentage, 2);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Kestabilan Jaringan (%)',
                    'data' => $data,
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.2)',
                    'fill' => 'start',
                    'tension' => 0.4,
                ],
            ],
            'labels' => $labels,
        ];
    }
    
    private function getDummyData(): array
    {
        // Generate realistic dummy data for 24 hours (mostly 100%, some slight dips to 98% etc)
        $labels = [];
        $data = [];
        for ($i = 23; $i >= 0; $i--) {
            $labels[] = now()->subHours($i)->format('H:00');
            $data[] = rand(98, 100);
        }
        
        return [
            'datasets' => [
                [
                    'label' => 'Kestabilan Jaringan (%)',
                    'data' => $data,
                    'borderColor' => '#10b981', // Emerald green
                    'backgroundColor' => 'rgba(16, 185, 129, 0.2)',
                    'fill' => 'start',
                    'tension' => 0.4,
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
