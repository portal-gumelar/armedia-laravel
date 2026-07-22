<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class OltCapacityWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $ports = \App\Models\OltPort::with('server')->withCount('onus')->get();
        $stats = [];

        foreach ($ports as $port) {
            if (!$port->server) continue;
            
            $utilization = $port->max_capacity > 0 ? round(($port->onus_count / $port->max_capacity) * 100, 1) : 0;
            $color = $utilization >= 90 ? 'danger' : ($utilization >= 75 ? 'warning' : 'success');
            
            $stats[] = Stat::make("{$port->server->name} - S{$port->slot}/P{$port->port}", "{$port->onus_count} / {$port->max_capacity} ONU")
                ->description("Utilisasi: {$utilization}%")
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color($color);
        }

        // Jika tidak ada port yang dikonfigurasi, beri stat kosong
        if (empty($stats)) {
            $stats[] = Stat::make('OLT Capacity', 'Belum ada port OLT')
                ->description('Tambahkan Port OLT di Master Data')
                ->color('secondary');
        }

        return $stats;
    }
}
