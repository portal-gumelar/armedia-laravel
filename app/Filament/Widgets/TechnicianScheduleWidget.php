<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\Ticket;
use Illuminate\Database\Eloquent\Builder;

class TechnicianScheduleWidget extends BaseWidget
{
    protected static ?string $heading = 'Jadwal Teknisi Hari Ini';
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Ticket::query()
                    ->whereNotNull('assigned_to')
                    ->whereDate('scheduled_at', today())
            )
            ->columns([
                Tables\Columns\TextColumn::make('assignedTo.name')
                    ->label('Teknisi')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul Tiket')
                    ->searchable(),
                Tables\Columns\TextColumn::make('customer.nama')
                    ->label('Pelanggan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('scheduled_at')
                    ->label('Waktu')
                    ->time('H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'open' => 'danger',
                        'in_progress' => 'warning',
                        'resolved' => 'success',
                        'closed' => 'gray',
                        default => 'primary',
                    }),
            ])
            ->defaultSort('scheduled_at', 'asc');
    }
}
