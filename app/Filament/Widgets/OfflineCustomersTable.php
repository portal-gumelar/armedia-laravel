<?php

namespace App\Filament\Widgets;

use App\Enums\MonitoringStatus;
use App\Models\Customer;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class OfflineCustomersTable extends BaseWidget
{
    protected static ?int $sort    = 3;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $heading = 'Pelanggan Offline (DOWN)';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Customer::query()
                    ->where('monitoring_status', MonitoringStatus::DOWN->value)
                    ->with(['village', 'internetPackage'])
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable(),
                TextColumn::make('village.name')
                    ->label('Desa')
                    ->placeholder('-'),
                TextColumn::make('ip_address')
                    ->label('IP Address')
                    ->placeholder('-'),
                TextColumn::make('internetPackage.nama_paket')
                    ->label('Paket')
                    ->placeholder('-'),
                TextColumn::make('whatsapp')
                    ->label('WhatsApp')
                    ->url(fn ($record) => $record->whatsapp
                        ? 'https://wa.me/' . preg_replace('/[^0-9]/', '', $record->whatsapp)
                        : null
                    )
                    ->openUrlInNewTab()
                    ->placeholder('-'),
                TextColumn::make('monitoring_checked_at')
                    ->label('Terakhir Dicek')
                    ->dateTime('d M Y H:i')
                    ->placeholder('-'),
            ]);
    }
}
