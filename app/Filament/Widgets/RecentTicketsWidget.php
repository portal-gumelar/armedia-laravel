<?php

namespace App\Filament\Widgets;

use App\Models\Ticket;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentTicketsWidget extends BaseWidget
{
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $heading = '5 Tiket Pengaduan Terbaru';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Ticket::query()->latest()->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('ticket_number')
                    ->label('No. Tiket')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Pelanggan'),
                Tables\Columns\TextColumn::make('subject')
                    ->label('Keluhan')
                    ->limit(30),
                Tables\Columns\TextColumn::make('status')
                    ->badge(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->paginated(false);
    }
}
