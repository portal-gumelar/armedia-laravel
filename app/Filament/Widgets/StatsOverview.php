<?php

namespace App\Filament\Widgets;

use App\Models\AcrMember;
use App\Models\Registration;
use App\Models\AcrPointTransaction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?string $pollingInterval = '15s';

    protected function getStats(): array
    {
        $totalMembers = AcrMember::count();
        $totalRegistrations = Registration::count();
        $totalPoints = AcrMember::sum('total_poin');
        $totalMessages = \App\Models\ContactMessage::count();
        $totalArticles = \App\Models\Article::count();

        return [
            Stat::make('Total Pendaftar Baru', $totalRegistrations)
                ->description('Pendaftar paket internet baru')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart([7, 2, 10, 3, 15, 4, 17]),
                
            Stat::make('Total Member ACR', $totalMembers)
                ->description('Member aktif di sistem')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary'),

            Stat::make('Total Poin Beredar', number_format((float)$totalPoints, 0, ',', '.'))
                ->description('Total poin ACR yang belum ditukar')
                ->descriptionIcon('heroicon-m-gift')
                ->color('warning'),

            Stat::make('Pesan Masuk', $totalMessages)
                ->description('Pesan pelanggan dari website')
                ->descriptionIcon('heroicon-m-envelope')
                ->color('danger'),

            Stat::make('Total Artikel', $totalArticles)
                ->description('Artikel dan berita yang dipublikasikan')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('info'),
        ];
    }
}
