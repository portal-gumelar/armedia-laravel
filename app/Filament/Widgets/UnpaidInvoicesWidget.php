<?php

namespace App\Filament\Widgets;

use App\Models\Invoice;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class UnpaidInvoicesWidget extends BaseWidget
{
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $heading = '5 Tagihan Terdekat Belum Dibayar';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Invoice::query()
                    ->where('payment_status', '!=', 'Lunas')
                    ->orderBy('due_date', 'asc')
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('invoice_number')
                    ->label('No. Tagihan')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Pelanggan'),
                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Total')
                    ->money('IDR', true),
                Tables\Columns\TextColumn::make('due_date')
                    ->label('Jatuh Tempo')
                    ->date('d M Y')
                    ->sortable()
                    ->color(fn (Invoice $record): string => $record->due_date < now() ? 'danger' : 'warning'),
                Tables\Columns\TextColumn::make('payment_status')
                    ->label('Status')
                    ->badge()
                    ->color('danger'),
            ])
            ->paginated(false);
    }
}
