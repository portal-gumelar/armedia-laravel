<?php

namespace App\Filament\Hrm\Widgets;

use App\Models\Employee;
use App\Models\Leave;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class HrmOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // 1. Total Karyawan Aktif
        $activeEmployees = Employee::where('status', 'aktif')->count();

        // 2. Cuti Menunggu Persetujuan
        $pendingLeaves = Leave::where('status', 'pending')->count();

        // 3. Ulang Tahun Bulan Ini
        $birthdaysThisMonth = Employee::where('status', 'aktif')
            ->whereMonth('birth_date', Carbon::now()->month)
            ->count();

        return [
            Stat::make('Karyawan Aktif', $activeEmployees)
                ->description('Total karyawan dengan status aktif')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary'),

            Stat::make('Cuti Pending', $pendingLeaves)
                ->description('Menunggu persetujuan manajer')
                ->descriptionIcon('heroicon-m-clock')
                ->color($pendingLeaves > 0 ? 'warning' : 'success'),

            Stat::make('Ulang Tahun Bulan Ini', $birthdaysThisMonth)
                ->description('Karyawan yang berulang tahun di bulan ' . Carbon::now()->translatedFormat('F'))
                ->descriptionIcon('heroicon-m-cake')
                ->color('info'),
        ];
    }
}
